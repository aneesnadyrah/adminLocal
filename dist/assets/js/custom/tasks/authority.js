"use strict";

var authorityTable = (function () {
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

  var handleDropzone = () => {
    var elements = [].slice.call(document.querySelectorAll('[data-upload]'));
      elements.map(function (element) {
        var secret = element.getAttribute("data-upload");
        var type = element.getAttribute("data-type");
        var chunks = secret.split('-');
        var statusId = chunks[2];
        var folder = chunks[0];
        var systemId = chunks[1];
        var authorityId = chunks[3];
        var submitId = 'submit-' + systemId + '-' + statusId + '-' + authorityId;
        var submitButton = document.querySelector('[data-submit="' + submitId + '"]');

        dropzone = new Dropzone(element, {
          url: `${apps}/api/tasks/${statusId}/upload`,
          paramName: "file",
          maxFiles: 1,
          maxFilesize: 1024,
          acceptedFiles: type,
          autoProcessQueue: true,
          addRemoveLinks: true,
          sending: function (file, xhr, formData) {
            formData.append("systemId", systemId );
            formData.append("folder", folder );
            formData.append("authorityId", authorityId );

          },
          accept: function (file, done) {
            done();
          },
        });

        dropzone.on("success", function () {
          submitButton.classList.remove("d-none");
        });

        dropzone.on("removedfile", function () {
          submitButton.classList.add("d-none");
        });
      });
  }

  var handleValidation = () => {
    var elements = [].slice.call(document.querySelectorAll('[data-form]'));
    elements.map(function (element) {
      // Get all required fields inside the current form
      var requiredFields = [].slice.call(element.querySelectorAll('[required]'));

      var fields = {};
      requiredFields.forEach(function (field) {
          // Use the name attribute as the key for each field
          fields[field.getAttribute('name')] = {
              validators: {
                  notEmpty: {
                      message: "Sila isi ruangan ini.",
                  },
              },
          };
      });

      validator = FormValidation.formValidation(element, {
        fields: fields,
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
                rowSelector: ".fv-row",
                eleInvalidClass: "", // comment to enable invalid state icons
                eleValidClass: "", // comment to enable valid state icons
            }),
        },
    });

      // Store the validator on the element for later use
      element.validator = validator;
    });
  }


  var handleSubmitTask = () => {
    var submitButtons = [].slice.call(document.querySelectorAll('[data-submit]'));

    submitButtons.map(function (submitButton) {
      var secret = submitButton.getAttribute("data-submit");
      var chunks = secret.split('-');
      var statusId = chunks[2];
        var systemId = chunks[1];
        var authorityId = chunks[3];
      var formId = 'form-' + systemId + '-' + statusId + '-' + authorityId;

      submitButton.addEventListener("click", async (e) => {
        e.preventDefault();

        var element = submitButton.closest('[data-form="' + formId + '"]');
        var validator = element.validator;

        validator.validate().then(function (status){
          if (status === "Valid") {
            submitButton.setAttribute("data-kt-indicator", "on");
            submitButton.disabled = true;
            const serializedArray = $('[data-form="'+ formId +'"]').serializeArray();
            const formData = {};

            serializedArray.forEach((item) => {
              formData[item.name] = item.value;
              formData["systemId"] = systemId;
              formData["authorityId"] = authorityId;
            });
            const json = JSON.stringify(formData);

            api.post(`tasks/${statusId}/submit`, json).then(response => {
              if (response.status === 200) {
                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false;
                toastr.success(response.message);
                setTimeout(function () {
                  if (statusId == 8) {
                    location.href = "geospatial/PCL/extract/" + systemId;
                  } else {
                    window.location.reload();
                  }
                }, 2500);

              } else {
                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false;
                toastr.error('Tugasan anda tidak berjaya dikemaskini');
              }

            }).catch(error => {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;
              toastr.error(`Maaf, nampaknya terdapat ralat dikesan, ${error} . Sila cuba sekali lagi.`);
            });

          } else {
            toastr.error('Sila semak butiran yang diisi.');
          }
        })
      })
    })
  }
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
        handleDropzone();
        handleValidation();
        handleSubmitTask();
      });
    },
  };
})();


var gisAssign = (function () {

  var getEastimated = () => {

  }

  return {
    init: function () {
      getEastimated();

    },
  };

})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  authorityTable.init();
  roleId === 6 || 7 ? gisAssign.init() : null;
});
