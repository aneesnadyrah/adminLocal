"use strict";

// Class definition
var KTSurveyList = function () {
    // Shared variables
    var datatable;
    var table;

    // Init add schedule modal
    var initSurveyList = () => {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[2].innerHTML, "DD MMM YYYY, LT").format(); // select date from 2nd column in table
            dateRow[2].setAttribute('data-order', realDate);
        });

         // Init datatable --- more info on datatables: https://datatables.net/manual/
         datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'columnDefs': [
                { orderable: false, targets: 1 }, // Disable ordering on column 1 (assigned)
                { orderable: false, targets: 3 }, // Disable ordering on column 3 (actions)
            ]
        });        
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-survey-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    return {
        // Public functions
        init: function () {
            table = document.querySelector('#kt_survey_table');
            
            if (!table) {
                return;
            }

            initSurveyList();
            handleSearchDatatable();
            handleDeleteRows();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTSurveyList.init();
});