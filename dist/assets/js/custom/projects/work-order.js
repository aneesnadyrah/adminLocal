"use strict";

// Class definition
var DataTasking = (function () {
  // Shared variables
  var table;
  var datatable;
  var flatpickr;
  var minDate, maxDate;

  // Private functions
  var initDatatable = function () {
    // Init datatable --- more info on datatables: https://datatables.net/manual/
    datatable = $(table).DataTable({
      ajax: "https://"+hostApps+"/v1/lists/work-order.php",
      columns: [
        { data: "RefNo" },
        { data: "Provider" },
        { data: "District" },
        { data: "Length" },
        { data: "Status" },
        { data: "WOSubmitDate" },
        { data: null },
      ],
      info: false,
      language: {
        loadingRecords: "Sila Tunggu...",
        zeroRecords: "Tiada Rekod Dijumpai",
      },
      pageLength: 10,
      columnDefs: [
        {
          target: 1,
          render: function (index, type, key, meta) {
            var $provider_img = key["ProviderID"];

            if ($provider_img) {
              // For Avatar image
              var $output =
                `<img src="assets/media/provider/` +
                key["ProviderID"] +
                `.webp" alt="` +
                key["Provider"] +
                `" />`;
            } else {
              // For Avatar badge
              var stateNum = Math.floor(Math.random() * 6);
              var states = [
                "success",
                "danger",
                "warning",
                "info",
                "dark",
                "primary",
                "secondary",
              ];
              var $named;
              var $initials;
              var $state = states[stateNum],
                $name = key["Provider"];
              if ($name === null) {
                $named = "Tidak Diketahui";
              } else {
                $named = $name;
              }
              $initials = $named.match(/\b\w/g) || [];
              $initials = (
                ($initials.shift() || "") + ($initials.pop() || "")
              ).toUpperCase();
              $output =
                `<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                  <div class="symbol-label fs-3 bg-light-` + $state + ` text-` + $state + `">` + $initials + `</div>
                </div>`;
            }
            return (
              `<div class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-50px me-2">` + $output + `</div>
                <!--end::Avatar-->
                <!--begin::Title-->
                <a href="javascript:void()" class="text-gray-700 text-hover-primary">` + key["Provider"] + `</a>
                <!--end::Title-->
              </div>`
            );
          },
        },
        {
          target: 3,
          render: function (index, type, key, meta) {
            return (
              parseInt(key["Length"]).toLocaleString("ms-MY", {
                useGrouping: true,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
              }) + " m"
            );
          },
        },
        {
          target: 4,
          render: function (index, type, key, meta) {
            return (
              `<!--begin::Badges-->
              <div class="badge badge-light-` + key["StatusColor"] + `">` + key["Status"] + `</div>
              <div class="text-center">` + key["SysID"] + `</div>
              <!--end::Badges-->`
            );
          },
        },
        {
            target: 5,
            render: function (index, type, key, meta) {
                let timestamp = key["WOSubmitDate"]; // replace with your timestamp
                let date = new Date(timestamp);
                let year = date.getFullYear();
                let month = date.getMonth() + 1;
                let day = date.getDate();
                let formattedDate = year + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
                // output: "2021-02-27"

                if (key["WOSubmitDate"] === null) {
                    date = " - ";
                } else {
                    date = formattedDate;
                }

              return (
                `<div class="text-center">` + date + `</div>`
              );
            },
          },
        {
          orderable: false,
          targets: 6,
          render: function (index, type, key, meta) {
            return (
              `<div class="text-center">
                <button type="button" class="btn btn-icon btn-light-` + key["StatusColor"] + `"  
                data-bs-toggle="modal" data-bs-target="#action-`+ key["SysID"] +`")">
                  <i class="fad fa-file-arrow-down fs-2"></i>
                </button>
              </div>`
            );
          },
        },
      ],

    });
  }




  // Init flatpickr --- more info :https://flatpickr.js.org/getting-started/
  var initFlatpickr = () => {
    const element = document.querySelector("#table-date-range");
    flatpickr = $(element).flatpickr({
      altInput: true,
      altFormat: "d/m/Y",
      dateFormat: "Y-m-d",
      mode: "range",
      onChange: function (selectedDates, dateStr, instance) {
        handleFlatpickr(selectedDates, dateStr, instance);
      },
    });
  };

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector(
      '[data-table-filter="search"]'
    );
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  // Handle status filter dropdown
  var handleStatusFilter = () => {
    const filterStatus = document.querySelector(
      '[data-table-filter="status"]'
    );
    $(filterStatus).on("change", (e) => {
      let value = e.target.value;
      if (value === "all") {
        value = "";
      }
      datatable.column(4).search(value).draw();
    });
  };

  // Handle flatpickr --- more info: https://flatpickr.js.org/events/
  var handleFlatpickr = (selectedDates, dateStr, instance) => {
    minDate = selectedDates[0] ? new Date(selectedDates[0]) : null;
    maxDate = selectedDates[1] ? new Date(selectedDates[1]) : null;

    // Datatable date filter --- more info: https://datatables.net/extensions/datetime/examples/integration/datatables.html
    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      var min = minDate;
      var max = maxDate;
      var dateAdded = new Date(moment(data[5], "YYYY-MM-DD"));

      if (
        (min === null && max === null) ||
        (min === null && dateAdded <= max) ||
        (max === null && dateAdded >= min) ||
        (min <= dateAdded && max >= dateAdded)
      ) {
        return true;
      }
      return false;
    });
    datatable.draw();
  };

  // Handle clear flatpickr
  var handleClearFlatpickr = () => {
    const clearButton = document.querySelector(
      "#date-range-clear"
    );
    clearButton.addEventListener("click", (e) => {
      flatpickr.clear();
    });
  };

  // Public methods
  return {
    init: function () {
      table = document.querySelector("#work-order");

      if (!table) {
        return;
      }

      initDatatable();
      initFlatpickr();
      handleSearchDatatable();
      handleStatusFilter();
      handleClearFlatpickr();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  DataTasking.init();
});
