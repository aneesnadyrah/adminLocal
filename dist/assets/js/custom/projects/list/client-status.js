"use strict";

var generalTable = (function () {
    var dataTable, flatpickr, filterSearch, filterStatus, minDate, maxDate, clearButton, validator, dropzone;
    var Malaysian = {
        weekdays: {
            shorthand: ["Aha", "Isn", "Sel", "Rab", "Kha", "Jum", "Sab"],
            longhand: ["Ahad", "Isnin", "Selasa", "Rabu", "Khamis", "Jumaat", "Sabtu"],
        },
        months: {
            shorthand: [
                "Jan",
                "Feb",
                "Mac",
                "Apr",
                "Mei",
                "Jun",
                "Jul",
                "Ogo",
                "Sep",
                "Okt",
                "Nov",
                "Dis",
            ],
            longhand: [
                "Januari",
                "Februari",
                "Mac",
                "April",
                "Mei",
                "Jun",
                "Julai",
                "Ogos",
                "September",
                "Oktober",
                "November",
                "Disember",
            ],
        },
        firstDayOfWeek: 1,
        ordinal: function () {
            return "";
        },
        rangeSeparator: " hingga ",
        weekAbbreviation: "Minggu",
        scrollTitle: "Tatal untuk Menaik",
        toggleTitle: "Klik untuk togol",
        amPM: ["PG", "PTG"],
        yearAriaLabel: "Tahun",
        monthAriaLabel: "Bulan",
        hourAriaLabel: "Jam",
        minuteAriaLabel: "Minit",
        time_24hr: false,
    };
    toastr.options = {
        closeButton: false,
        debug: false,
        newestOnTop: false,
        progressBar: false,
        positionClass: "toastr-top-center",
        preventDuplicates: true,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "2000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

  var initTable = () => {
    return new Promise((resolve) => {
      dataTable = new DataTable(table, {
        info: false,
        language: {
          loadingRecords: "Sila Tunggu...",
          zeroRecords: "Tiada Rekod Dijumpai",
        },
        pageLength: 5,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
        order: [[5, "desc"]],
        columnDefs: [
          { type: 'date-dd-mm-yyyy', targets: 1 }
        ],
        drawCallback: function () {
          resolve();
        },
      });
    });
  };

  var initFlatpickr = () => {
    const element = document.querySelector("#table-date-range");
    flatpickr = $(element).flatpickr({
      altInput: true,
      altFormat: "d/m/Y",
      dateFormat: "Y-m-d",
      locale: Malaysian,
      mode: "range",
      onChange: function (selectedDates, dateStr, instance) {
        handleFlatpickr(selectedDates, dateStr, instance);
      },
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
        dataTable.column(5).search(value).draw();
    });
  };

  var handleFlatpickr = (selectedDates, dateStr, instance) => {
    minDate = selectedDates[0] ? new Date(selectedDates[0]) : null;
    maxDate = selectedDates[1] ? new Date(selectedDates[1]) : null;

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var min = minDate;
        var max = maxDate;
        var dateAdded = new Date(moment(data[1], "DD/MM/YYYY").format("YYYY-MM-DD"));

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
    dataTable.draw();
  };

  var handleClearFlatpickr = () => {
    clearButton.addEventListener("click", (e) => {
      flatpickr.clear();
    });
  };

  return {
    init: function () {
      filterSearch = document.querySelector('[data-table-filter="search"]');
      filterStatus = document.querySelector('[data-table-filter="status"]');
      clearButton = document.querySelector("[date-range-clear]");

      initTable().then(() => {
        initFlatpickr();
        handleSearchDatatable();
        handleStatusFilter();
        handleClearFlatpickr();
      });
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  generalTable.init();
});
