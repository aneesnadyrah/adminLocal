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
    const urlParams = new URLSearchParams(window.location.search);
    const ref = urlParams.get('ref');

    // Get the current URL
    let currentURL = new URL(window.location.href);
    let urlWithOutParams = currentURL.origin + currentURL.pathname;

    // Get the values of the parameters
    let htaccessUrlParams = urlWithOutParams.replace(apps + '/reports/site/task', '').split("/");
    // urlParams[0] is ignore as it handle the first / in the trimmed url
    let sid = htaccessUrlParams[1];

    datatable = $(table).DataTable({
      ajax: apps + "/api/reports/"+sid+"?report-id=" + ref,
      columns: [
        { data: "ReportNo" },
        { data: "Authority" },
        { data: "SVDate" },
        { data: "Length" },
        { data: "ReportType" },
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
          orderable: false,
          targets: 0,
          render: function (index, type, key, meta) {
            let render = key["RefNo"] + ' - ' + key["ReportNo"];
            return (render);
          },
        },
        {
          target: 1,
          render: function (index, type, key, meta) {
            var $img = key["AuthLogo"];
            var $output;
            if ($img) {
              // For Avatar image
              if (key["AuthLogo"] == "22" || key["AuthLogo"] == "33" || key["AuthLogo"] == "44" ||
                key["AuthLogo"] == "55" || key["AuthLogo"] == "66" || key["AuthLogo"] == "77" ||
                key["AuthLogo"] == "88" || key["AuthLogo"] == "99") {
                $output = `<img src="assets/media/authorities/` + key["AuthLogo"] + `.png" alt="` + key["Authority"] + `" />`;
              } else {
                $output = `<img src="assets/media/authorities/` + key["AuthLogo"] +
                  `.png" alt="` +
                  key["Authority"] +
                  `" />`;
              }

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
              var $state = states[stateNum],
                $named,
                $name = key["Authority"];
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
                <a href="javascript:void()" class="text-gray-700 text-hover-primary">` + key["Authority"] + `</a>
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
            // let colors;
            // let status;

            // if (key["ReportType"] == "1") {
            //   colors = "primary";
            //   status = "Laporan Pengesahan Laluan";
            // }
            // else if (key["ReportType"] == "2") {
            //   colors = "warning";
            //   status = "Laporan Lawatan Tapak Bersama";
            // } else {
            //   colors = "success";
            //   status = "Laporan Lawatan Tapak Berkala";
            // }

            return (
              `<!--begin::Badges-->
              <div class="badge badge-light-`+ `primary` + `">` + key["ReportType"] + `</div>
              <!--end::Badges-->`
            );
          },
        },
        {
          orderable: false,
          targets: -1,
          render: function (index, type, key, meta) {
            let submitStatus = key['ReportStat'];
            let render;

            if (submitStatus == 0) {
              render = `<div class="text-center">
              <a href="/reports/site/${key['SysID']}?ref=${key['ReportNo']}&auth=${key['AuthID']}" class="btn btn-icon btn-light-` + `primary` + `">
                <i class="fad fa-`+ `map-location-dot` + ` fs-2"></i>
              </a>
            </div>`
            } else if (submitStatus == 1) {
              render = `<div class="text-center">
              <a href="/reports/site/view/${key['SysID']}?r=${key['ReportNo']}&a=${key['AuthID']}" class="btn btn-icon btn-light-` + `primary` + `">
                <i class="fad fa-`+ `eye` + ` fs-2"></i>
              </a>
            </div>`
            }
            return (render);
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
      table = document.querySelector("#site-report");

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
