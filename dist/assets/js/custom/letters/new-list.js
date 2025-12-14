"use strict";

// Class definition
var LetterList = (function () {
  // Shared variables
  var table;
  var datatable;
  var flatpickr;
  var flatpickrModal;
  var flatpickrSingleDate;
  var flatpickrStartTime;
  var flatpickrEndTime;
  var minDate, maxDate;

  // Private functions
  var initDatatable = function () {
    // Init datatable --- more info on datatables: https://datatables.net/manual/
    datatable = $(table).DataTable({
      ajax: apps + "/api/letters/list",
      columns: [
        { data: "LetterNo" },
        { data: "Provider" },
        { data: "District" },
        { data: "Length" },
        { data: "LetterStatus" },
        { data: "LetterDate" },
        { data: null },
      ],
      info: false,
      language: {
        loadingRecords: "Sila Tunggu...",
        zeroRecords: "Tiada Rekod Dijumpai",
      },
      order: [[5, 'desc']],
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
              var $state = states[stateNum],
              $named,
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
                  <div class="symbol-label fs-3 bg-light-` +
                $state +
                ` text-` +
                $state +
                `">` +
                $initials +
                `</div>
                </div>`;
            }
            return (
              `<div class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-50px me-2">` +
              $output +
              `</div>
                <!--end::Avatar-->
                <!--begin::Title-->
                <a href="javascript:void()" class="text-gray-700 text-hover-primary">` +
              key["Provider"] +
              `</a>
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
              <div class="badge badge-light-` +
              key["LetterStatusColor"] +
              `">` +
              key["LetterStatus"] +
              `</div>
              <!--end::Badges-->`
            );
          },
        },
        {
          orderable: false,
          targets: 6,
          render: function (index, type, key, meta) {
            let render;

              render = `<div class="text-center">
              <a href="letters/PIL/`+ key['SysID'] +`/` + key["flw_generated_letter_id"] + `" class="btn btn-icon btn-light-` + key["LetterStatusColor"] + `">
                <i class="fad fa-` + key["LetterStatusIcon"] + ` fs-2"></i>
              </a>
            </div>`

            return render;
          },
        },
      ],
    });


    // Initialize Dropzone for each modal
    datatable.on('click', 'button', function () {
      let modalId = $(this).data('bs-target');
      let submitId = "#submit-" + modalId.slice(8);
      let formId = "#form-" + modalId.slice(8);
      let sId = "#sid-" + modalId.slice(8);
      let fId = "#f-" + modalId.slice(8);
      let selectId = "#selection-" + modalId.slice(8);
      let submitButton = document.querySelector(submitId);

    });
  };

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

  var initFlatpickrSingleDate = () => {
    const element = document.querySelector("#single-date-picker");
    flatpickrSingleDate = $(element).flatpickr({
      altInput: true,
      altFormat: "d/m/Y",
      dateFormat: "Y-m-d",
      onChange: function (selectedDates, dateStr, instance) {
        handleFlatpickrSingleDate(selectedDates, dateStr, instance);
      },
    });
  };

  var initFlatpickrStartTime = () => {
    const element = document.querySelector("#start-time-picker");
    flatpickrStartTime = $(element).flatpickr({
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true,
      onClose: function (selectedDates, dateStr, instance) {
        const selectedTime = selectedDates[0].toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
        instance.element.value = selectedTime;
      },
      onValueUpdate: function (selectedDates, dateStr, instance) {
        const selectedTime = selectedDates[0].toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
        instance.element.value = selectedTime;
      },
    });
  };

  var initFlatpickrEndTime = () => {
    const element = document.querySelector("#end-time-picker");
    flatpickrStartTime = $(element).flatpickr({
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
      time_24hr: true,
      onClose: function (selectedDates, dateStr, instance) {
        const selectedTime = selectedDates[0].toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
        instance.element.value = selectedTime;
      },
      onValueUpdate: function (selectedDates, dateStr, instance) {
        const selectedTime = selectedDates[0].toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
        instance.element.value = selectedTime;
      },
    });
  };

  var initFlatpickrModal = () => {
    const modalElement = document.querySelector("#modal-date-range");

    flatpickrModal = $(modalElement).flatpickr({
      altInput: true,
      altFormat: "d/m/Y",
      dateFormat: "Y-m-d",
      mode: "range",
      onChange: function (selectedDates, dateStr, instance) {
        handleFlatpickrModal(selectedDates, dateStr, instance);
      },
    });
  };

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector('[data-table-filter="search"]');
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  // Handle status filter dropdown
  var handleStatusFilter = () => {
    const filterStatus = document.querySelector('[data-table-filter="status"]');
    $(filterStatus).on("change", (e) => {
      let value = e.target.value;
      if (value === "all") {
        value = "";
      }
      datatable.column(4).search(value).draw();
    });

    // datatable.column(4).search(initialValue).draw();
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

  var handleFlatpickrModal = (selectedDates, dateStr, instance) => {
    var minDate, maxDate; // declare variables to store selected dates

    // Update minDate and maxDate based on selected dates
    if (selectedDates && selectedDates.length === 2) {
      minDate = new Date(selectedDates[0]);
      maxDate = new Date(selectedDates[1]);
    } else {
      minDate = null;
      maxDate = null;
    }
  };

  var handleFlatpickrSingleDate = (selectedDates, dateStr, instance) => {
    // Access the selected date using selectedDates[0]
    const selectedDate = selectedDates[0];

    // Format the date string for display in the input field
    const formattedDate = instance.formatDate(selectedDate, "d/m/Y");

    // Update the input field value with the formatted date string
    instance.altInput.value = formattedDate;

    // Do any additional processing or validation here...
  };

  // Handle clear flatpickr
  var handleClearFlatpickr = () => {
    const clearButton = document.querySelector("#date-range-clear");
    clearButton.addEventListener("click", (e) => {
      flatpickr.clear();
    });
  };

  // Public methods
  return {
    init: function () {
      table = document.querySelector("#list-letter");

      if (!table) {
        return;
      }

      initDatatable();
      initFlatpickr();
      initFlatpickrSingleDate();
      initFlatpickrStartTime();
      initFlatpickrEndTime();
      initFlatpickrModal();
      handleSearchDatatable();
      handleStatusFilter();
      handleClearFlatpickr();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  LetterList.init();
});
