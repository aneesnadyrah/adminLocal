var trackerTable = (function () {
    var dataTable, filterSearch;

    var initTable = () => {
        return new Promise((resolve) => {
            let dateColumns = [];
            for (var i = 18; i <= 75; i++) {
                if (i !== 41 && i !== 61 && i !== 72) {
                    dateColumns.push(i);
                }
            }
            dataTable = new DataTable(table, {
                info: true,
                language: {
                    loadingRecords: "Sila Tunggu...",
                    zeroRecords: "Tiada Rekod Dijumpai",
                    info: "Papar _START_ hingga _END_ dari _TOTAL_ rekod",
                    infoEmpty: "Papar 0 hingga 0 dari 0 rekod", // Optional message for empty table
                    infoFiltered: "(carian dari _MAX_ jumlah rekod)",
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
                columnDefs: [
                  { type: 'date-dd-mm-yyyy', targets: dateColumns }
                ],
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
            dataTable.column(9).search(value).draw();
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

    return {
        init: function () {
            filterSearch = document.querySelector('[data-table-filter="search"]');
            filterStatus = document.querySelector('[data-table-filter="status"]');

            initTable().then(() => {
                handleSearchDatatable();
                handleStatusFilter();
                exportButtons();
            });
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    trackerTable.init();
});
