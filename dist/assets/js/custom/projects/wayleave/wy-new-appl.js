
"use strict";

// Class definition
var WayleaveFeedback = (function () {
    // Shared variables
    var table;
    var datatable;
    var flatpickr;
    var flatpickrSingleDate;
    var minDate, maxDate;

    // Private functions
    var initWayleaveFeedback = function () {
        datatable = $(table).DataTable({
            // ajax: apps + "/api/projects/wayleaves/fbList",
            ajax: apps + "/api/projects/wayleaves/newApplList",
            columns: [
            { data: "Provider" },
            { data: "District" },
            { data: "Length" },
            { data: "StatusID" },
            { data: "SubmitDate" },
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
                target: 0,
                render: function (index, type, key, meta) {
                var $provider_img = key["ProviderLogo"];

                if ($provider_img) {
                    // For Avatar image
                    var $output =
                    `<img src="assets/media/provider/` +
                    key["ProviderLogo"] +
                    `" alt="` +
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
                    var $initials = $named.match(/\b\w/g) || [];
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
                target: 2,
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
                target: 3,
                render: function (index, type, key, meta) {
                    let render;
                    if (key["StatusID"] == 1) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-dark">Semakan Permohonan Baru</div>
                        <!--end::Badges-->`
                    } else {
                        render =
                        `<div>-</div>`
                    }
                    return render;
                },
            },
            {
                orderable: false,
                targets: 5,
                render: function (index, type, key, meta) {
                    let render;
                    if (key["StatusID"] == 1) {
                        render =
                        `<div class="text-center">
                            <a href="projects/record/check/${key["SysID"]}" class="btn btn-icon btn-light-dark">
                            <i class="fad fa-envelope-open-text fs-2"></i>
                            </a>
                        </div>`;

                    } else {
                        render =
                        `<div class="text-center">
                            <i class="fad fa-circle-check text-success fs-2 "></i>
                        </div>`;
                    }

                    return render;
                },
            },
            ],
            order: [[4, 'desc']], // sort by the 6th column in ascending order
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
        var elements = document.querySelectorAll('[data-datepicker-id]');
        elements.forEach(function(element) {
            // var datepickerId = element.getAttribute('data-datepicker-id');
            flatpickrSingleDate = $(element).flatpickr({
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr, instance) {
                  handleFlatpickrSingleDate(selectedDates, dateStr, instance);
                },
              });
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

    // console.log("handleFlatpickrSingleDate called");
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

    // Create a <style> element
    var printStyle = document.createElement('style');
    printStyle.setAttribute('media', 'print');

    // Define the print styles
    printStyle.textContent = `
        @media print {
            @page {
                /* Print-specific styles for the page */
                size: A4;
                margin-top: 0.5cm;
                margin-left: 2cm;
                margin-right: 2cm;
                margin-bottom: 0.2cm;
            }
            .page-break {
                page-break-inside: avoid;
            }
            .print-end {
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                align-items: flex-end;
            }
            .print-start {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
            }
            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                text-align: center;
                font-size: 12px;
            }
            .kutt_print {
                size: A4;
                margin-top: 3cm;
                margin-bottom: 2cm;
            }
        }
    `;

    // Append the <style> element to the document head
    document.head.appendChild(printStyle);

    document.addEventListener('DOMContentLoaded', function() {
        // Handle print button click
        var printButton = document.getElementById('print_feedback');
        printButton.addEventListener('click', function() {
            // Create a new div to hold the print-only content
            var printSection = document.createElement("div");
            // Copy the content of the specified element to the print-only section
            var printContent = document.getElementById("print_content").innerHTML;
            printSection.innerHTML = printContent;

            // Append the print-only section to the document body
            document.body.appendChild(printSection);

            // Trigger the browser's print functionality
            window.print();

            // Remove the print-only section from the document body
            document.body.removeChild(printSection);
        });
    });

    // Public methods
    return {
        init: function () {
        table = document.querySelector("#wayleave-feedback");

        if (!table) {
            return;
        }

        initWayleaveFeedback();
        initFlatpickr();
        initFlatpickrSingleDate();
        handleSearchDatatable();
        handleClearFlatpickr();

        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    WayleaveFeedback.init();
});