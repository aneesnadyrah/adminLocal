var trackerNewTable = (function () {
    var dataTable, filterSearch;

    var initTable = () => {
        return new Promise((resolve) => {
            dataTable = new DataTable(table, {
                info: true,
                infoEmpty: true,
                language: {
                    loadingRecords: "Sila Tunggu...",
                    zeroRecords: "Tiada Rekod Dijumpai",
                    info: "Papar _START_ hingga _END_ dari _TOTAL_ rekod",
                    infoEmpty: "Papar 0 hingga 0 dari 0 rekod", // Optional message for empty table
                    infoFiltered: "(carian dari _MAX_ jumlah rekod)",
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
                drawCallback: function () {
                    resolve();
                },
            });
        });
    };

    var handleSearchDatatable = () => {
        filterSearch.addEventListener("keyup", function (e) {
            dataTable.search(e.target.value).draw();
        });
    };

    var handleStatusFilter = () => {
        $(filterStatus).on('change', e => {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            dataTable.column(7).search(value).draw();
        });
    };

    var handleAuthorityFilter = () => {
        $(filterAuthority).on('change', e => {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            dataTable.column(5).search(value).draw();
        });
    };

    // Hook export buttons
    var exportButtons = () => {
        const documentTitle = 'Rumusan Penjejak';
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [{
                extend: 'copyHtml5',
                title: documentTitle,
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'excelHtml5',
                title: documentTitle,
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                title: documentTitle,
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                title: documentTitle,
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                },
                // exportOptions: {
                //     columns: ':not(.no-export)' // Export only visible columns
                // },
                customize: function (doc) {
                    // Set landscape orientation
                    doc.pageOrientation = 'landscape';
                }
            }
            ]
        }).container().appendTo($('#jana_rumusan'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#jana_rumusan_menu [data-kt-export]');
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

    // Ensure that the DOM is ready before executing the code
    $(document).ready(function () {
        // Your Select2 initialization code
        var selectId = "#kt_data_authority";

        var optionFormat = function (item) {
            if (!item.id) {
                return item.text;
            }

            var span = document.createElement("span");
            var imgUrl = item.element.getAttribute("data-authority");
            var template = "";

            template +=
                '<img src="' +
                imgUrl +
                '" class="rounded-circle h-30px me-2" alt="image"/>';
            template += item.text;

            span.innerHTML = template;

            return $(span);
        };

        // Initialize Select2
        $(selectId).select2({
            templateSelection: optionFormat,
            templateResult: optionFormat,
        });
    });


    return {
        init: function () {
            filterSearch = document.querySelector('[data-table-filter="search"]');
            filterStatus = document.querySelector('[data-table-filter="status"]');
            filterAuthority = document.querySelector('[data-table-filter="authority"]');

            initTable().then(() => {
                handleSearchDatatable();
                handleStatusFilter();
                handleAuthorityFilter();
                exportButtons();
            });
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    trackerNewTable.init();
});
