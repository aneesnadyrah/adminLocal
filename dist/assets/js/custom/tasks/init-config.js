"use strict";

// Class definition
var initConfig = (function () {
  // Shared variables
  var flatpickr;
  var flatpickrSingleDate;
  var flatpickrStartTime;
  var minDate, maxDate;

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

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector('[data-table-filter="search"]');
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  var handleStatusFilter = () => {
    const filterStatus = document.querySelector('[data-table-filter="status"]');
    let initialValue = filterStatus.value;
    if (initialValue === "all") {
      initialValue = "";
    }

    $(filterStatus).on("change", (e) => {
      let value = e.target.value;
      if (value === "all") {
        value = "";
      }
      // console.log("Selected value:", value);
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

      initFlatpickr();
      initFlatpickrSingleDate();
      initFlatpickrStartTime();
      initFlatpickrEndTime();
      handleSearchDatatable();
      handleFlatpickrModal();
      handleStatusFilter();
      handleClearFlatpickr();
    },
    handleFlatpickrModal
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  initConfig.init();
});