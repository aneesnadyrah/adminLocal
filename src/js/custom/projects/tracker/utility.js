"use strict";

// Class definition
var KTDatatablesExample = function () {
  // Shared variables
  var table;
  var datatable;
  var minDate, maxDate;
  var columnData = {
    commonColumns: function () {
      return [{
          data: "reference_no",
        },
        {
          data: "provider_name",
        },
        {
          data: "district_name",
        },
        {
          data: "application_length",
        },
        {
          data: "status_name",
        },
        {
          data: "created_at",
        },
        {
          data: null,
        },
        {
          data: "id",
        },
      ];
    },
    customRender: function (index, type, key, meta) {
      var $provider_img = key["utility_provider"];

      if ($provider_img) {
        // For Avatar image
        var $output =
          `<img src="assets/media/provider/` +
          key["utility_provider"] +
          `.webp" alt="` +
          key["provider_name"] +
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
          $name = key["provider_name"];
        if ($name === null) {
          $named = "Tidak Diketahui";
        } else {
          $named = $name;
        }
        var $initials = $named.match(/\b\w/g) || [];
        $initials = (
          ($initials.shift() || "") + ($initials.pop() || "")
        ).toUpperCase();
        $output =
          `<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
            <div class="symbol-label fs-3 bg-light-` + $state + ` text-` + $state + `">` +
              $initials +
            `</div>
          </div>`;
      }
      return (
        `<div class="d-flex align-items-center">
          <div class="symbol symbol-50px me-2">` +
            $output +
          `</div>
          <a href="javascript:void()" class="text-gray-700 text-hover-primary">` +
            key["provider_name"] +
          `</a>
        </div>`
      );
    },
  };

  // Private functions
  var initDatatable = function () {
    // Init datatable --- more info on datatables: https://datatables.net/manual/
    datatable = $(table).DataTable({
      ajax: apps + "/api/tracker",
      columns: columnData.commonColumns(),
      order: [
        [7, 'desc']
      ],
      info: false,
      language: {
        loadingRecords: "Sila Tunggu...",
        zeroRecords: "Tiada Rekod Dijumpai",
      },
      pageLength: 10,
      columnDefs: [{
          target: 1,
          render: columnData.customRender,
        },
        {
          target: 3,
          render: function (index, type, key, meta) {
            if (key["application_length"] === null) {
              return "-";
            } else {
              return (
                parseInt(key["application_length"]).toLocaleString("ms-MY", {
                  useGrouping: true,
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0,
                }) + " m"
              );
            }
          },
        },
        {
          target: 4,
          render: function (index, type, key, meta) {
            return `<div class="badge badge-light-` + key["status_color"] + `">` + key["status_name"] + `</div>`

          },
        },
        {
          target: 5,
          render: function (index, type, key, meta) {
            if (key["created_at"] == null) {
              return "-";
            } else {
              return '<div>' + (key["created_at"] ? new Date(key["created_at"]).toLocaleDateString('en-GB').replace(/\//g, '-') : '-') + '</div>';
            }
          },
        },
        {
          orderable: false,
          targets: 6,
          render: function (index, type, key, meta) {
            let render;
            render = `<div class="text-center">
                                <a href="projects/record/check/${key["system_id"]}" class="btn btn-icon btn-light-dark">
                                    <i class="fad fa-eye fs-3"></i>
                                </a>
                            </div>`;

            return render;
          },
        },
        {
          target: 7,
          visible: false,
          render: function (index, type, key, meta) {
            return `<div">` + key["id"] + `</div>`

          },
        },
      ],
    });
  }


  // Hook export buttons
  var exportButtons = () => {
    const documentTitle = 'Rumusan Penjejak';
    var buttons = new $.fn.dataTable.Buttons(table, {
      buttons: [{
          extend: 'copyHtml5',
          title: documentTitle
        },
        {
          extend: 'excelHtml5',
          title: documentTitle
        },
        {
          extend: 'csvHtml5',
          title: documentTitle
        },
        {
          extend: 'pdfHtml5',
          title: documentTitle,
          exportOptions: {
            columns: ':not(.no-export)' // Export only visible columns
          },
          customize: function (doc) {
            // Set landscape orientation
            doc.pageOrientation = 'landscape';
          }
        }
      ]
    }).container().appendTo($('#kt_datatable_example_buttons'));

    // Hook dropdown menu click event to datatable export buttons
    const exportButtons = document.querySelectorAll('#kt_datatable_example_export_menu [data-kt-export]');
    exportButtons.forEach(exportButton => {
      exportButton.addEventListener('click', e => {
        e.preventDefault();

        // Get clicked export value
        const exportValue = e.target.getAttribute('data-kt-export');
        const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

        // Trigger click event on hidden datatable export buttons
        target.click();
      });
    });
  }

  // Handle clear flatpickr
  var handleClearFlatpickr = () => {
    const clearButton = document.querySelector("#date-range-clear");
    clearButton.addEventListener("click", (e) => {
      flatpickr.clear();
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

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector(
      '[data-table-filter="search"]'
    );
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  var handleFilterDatatable = () => {
    const filterPenyedia = document.getElementById("filter-penyedia");
    filterPenyedia.addEventListener("change", function () {
        var selectedValue = this.value;
        if (selectedValue === "") {
            datatable.column(3).search("").draw();
        } else {
            datatable.column(3).search(selectedValue).draw();
        }
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
      var dateAdded = new Date(moment(data[3], "YYYY-MM-DD"));

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

  // Public methods
  return {
    init: function () {
      table = document.querySelector('#tracker-entry');

      if (!table) {
        return;
      }

      initDatatable();
      exportButtons();
      initFlatpickr();
      handleSearchDatatable();
      handleClearFlatpickr();
      handleFilterDatatable();
    }
  };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTDatatablesExample.init();
});