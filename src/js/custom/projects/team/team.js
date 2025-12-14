"use strict";

var teamPPKDTable = (function () {
  var dataTable,
    flatpickr,
    filterSearch,
    filterStatus,
    minDate,
    maxDate,
    clearButton,
    validator,
    dropzone;

  var Malaysian = {
    weekdays: {
      shorthand: ["Aha", "Isn", "Sel", "Rab", "Kha", "Jum", "Sab"],
      longhand: [
        "Ahad",
        "Isnin",
        "Selasa",
        "Rabu",
        "Khamis",
        "Jumaat",
        "Sabtu",
      ],
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
        lengthMenu: [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 50, "Semua"],
        ],
        order: [[3, "desc"]],
        drawCallback: function () {
          resolve();
        },
      });

      // Main function to handle button click
      dataTable.on("click", "button", function () {
        var rowId = $(this).data("row-id");

        // Check if the button is for form submission
        if ($(this).data("bs-target")) {
          console.log("11");
          console.log(rowId);
          handleFormSubmit("#form-ppkd-team-" + rowId, rowId);
        } else if ($(this).attr("id") === "ppkd_delete_button") {
          console.log("22");
          handleDeleteButtonClick(this, rowId);
        }
      });
    });
  };

  // Function to handle the form submission
  var handleFormSubmit = (formId, rowId) => {
    console.log("12");
    var form = document.querySelector(formId);
    var validator;

    validator = FormValidation.formValidation(form, {
      fields: {
        "ppkd-team-edit": {
          validators: {
            notEmpty: {
              message: "Sila Masukkan Nama Kumpulan",
            },
          },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap: new FormValidation.plugins.Bootstrap5({
          rowSelector: ".fv-row",
          eleInvalidClass: "",
          eleValidClass: "",
        }),
      },
    });

    console.log("13");

    // Handle form submit
    var submitButton = document.querySelector("#submit-ppkd-team-" + rowId);
    submitButton.addEventListener("click", function (e) {
      console.log("14");
      e.preventDefault();

      // Validate form
      validator.validate().then(function (status) {
        if (status === "Valid") {
          console.log("15");
          submitForm(rowId, form);
        } else {
          // Show error popup
          showValidationError();
        }
      });
    });
  };

  // Function to submit the form data
  var submitForm = (rowId, form) => {
    var submitButton = document.querySelector("#submit-ppkd-team-" + rowId);

    submitButton.setAttribute("data-kt-indicator", "on");
    submitButton.disabled = true;

    const formData = new FormData(form);

    const data = {};

    formData.forEach((value, name) => {
      if (data.hasOwnProperty(name)) {
        if (!Array.isArray(data[name])) {
          data[name] = [data[name]];
        }
        data[name].push(value);
      } else {
        data[name] = value;
      }
    });

    const jsonData = JSON.stringify(data);

    api
      .post(`projects/team/edit`, jsonData)
      .then((response) => {
        handleFormSubmissionSuccess(submitButton);
      })
      .catch((error) => {
        handleFormSubmissionError(submitButton);
      });
  };

  // Function to handle form submission success
  var handleFormSubmissionSuccess = (submitButton) => {
    submitButton.removeAttribute("data-kt-indicator");
    submitButton.disabled = false;

    // Show success message
    toastr.success("Kumpulan PPKD Berjaya Di Kemaskini! 🎉");

    setTimeout(function () {
      location.href = "projects/team";
    }, 2500);
  };

  // Function to handle form submission error
  var handleFormSubmissionError = (submitButton) => {
    submitButton.removeAttribute("data-kt-indicator");
    submitButton.disabled = false;

    // Show error popup
    Swal.fire({
      text: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, maklum!",
      customClass: {
        confirmButton: "btn btn-primary",
      },
      allowOutsideClick: false,
    });
  };

  // Function to show validation error
  var showValidationError = () => {
    Swal.fire({
      html: "Maaf, Sila pilih <strong>Ahli Kumpulan PPKD</strong> untuk permohonan ini.",
      icon: "warning",
      buttonsStyling: false,
      confirmButtonText: "Ok, maklum!",
      customClass: {
        confirmButton: "btn btn-primary",
      },
      allowOutsideClick: false,
    });
  };

  // Function to handle delete button click
  var handleDeleteButtonClick = (button, rowId) => {
    // Show confirmation message using SweetAlert
    Swal.fire({
      title: "Adakah anda pasti mahu memadamkan kumpulan ini?",
      text: "Anda tidak akan dapat mengembalikan kumpulan ini!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya !",
      cancelButtonText: "Batal",
      customClass: {
        confirmButton: "btn fw-bold btn-danger",
        cancelButton: "btn fw-bold btn-light btn-active-light-primary",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        handleDeleteConfirmation(rowId);
      }
    });
  };

  // Function to handle delete confirmation
  var handleDeleteConfirmation = (rowId) => {
    console.log(rowId);

    api
      .delete(`projects/team/delete?rowId=` + rowId)
      .then((response) => {
        handleDeleteSuccess();
      })
      .catch((error) => {
        handleDeleteError();
      });
  };

  // Function to handle delete success
  var handleDeleteSuccess = () => {
    toastr.success("Kumpulan berjaya dipadamkan! 🎉");

    // Navigate to the new page after a successful API call
    setTimeout(function () {
      location.href = "projects/team";
    }, 2500);
  };

  // Function to handle delete error
  var handleDeleteError = () => {
    // Show error popup
    Swal.fire({
      text: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
      icon: "error",
      buttonsStyling: false,
      confirmButtonText: "Ok, maklum!",
      customClass: {
        confirmButton: "btn btn-primary",
      },
      allowOutsideClick: false,
    });
  };

  var handleSearchDatatable = () => {
    filterSearch.addEventListener("keyup", function (e) {
      dataTable.search(e.target.value).draw();
    });
  };

  var handleStatusFilter = () => {
    $(filterStatus).on("change", (e) => {
      let value = e.target.value;
      if (value === "all") {
        value = "";
      }
      dataTable.column(5).search(value).draw();
    });
  };

  var handleValidation = () => {
    var elements = [].slice.call(document.querySelectorAll("[data-form]"));
    elements.map(function (element) {
      // Get all required fields inside the current form
      var requiredFields = [].slice.call(
        element.querySelectorAll("[required]")
      );

      var fields = {};
      requiredFields.forEach(function (field) {
        // Use the name attribute as the key for each field
        fields[field.getAttribute("name")] = {
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
  };

  var handleSubmitTask = () => {
    var submitButtons = [].slice.call(
      document.querySelectorAll("[data-submit]")
    );

    submitButtons.map(function (submitButton) {
      var secret = submitButton.getAttribute("data-submit");
      var chunks = secret.split("-");
      var statusId = chunks[2];
      var rowId = chunks[1];
      var formId = "form-" + rowId + "-" + statusId;

      submitButton.addEventListener("click", async (e) => {
        e.preventDefault();

        var element = submitButton.closest('[data-form="' + formId + '"]');
        var validator = element.validator;

        validator.validate().then(function (status) {
          if (status === "Valid") {
            submitButton.setAttribute("data-kt-indicator", "on");
            submitButton.disabled = true;
            const serializedArray = $(
              '[data-form="' + formId + '"]'
            ).serializeArray();
            const formData = {};

            serializedArray.forEach((item) => {
              formData[item.name] = item.value;
              formData["rowId"] = rowId;
            });
            const json = JSON.stringify(formData);

            api
              .post(`tasks/${statusId}/submit`, json)
              .then((response) => {
                if (response.status === 200) {
                  submitButton.removeAttribute("data-kt-indicator");
                  submitButton.disabled = false;
                  toastr.success(response.message);
                  setTimeout(function () {
                    window.location.reload();
                  }, 2500);
                } else {
                  submitButton.removeAttribute("data-kt-indicator");
                  submitButton.disabled = false;
                  toastr.error("Tugasan anda tidak berjaya dikemaskini");
                }
              })
              .catch((error) => {
                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false;
                toastr.error(
                  `Maaf, nampaknya terdapat ralat dikesan, ${error} . Sila cuba sekali lagi.`
                );
              });
          } else {
            toastr.error("Sila semak butiran yang diisi.");
          }
        });
      });
    });
  };
  return {
    init: function () {
      filterSearch = document.querySelector('[data-table-filter="search"]');
      filterStatus = document.querySelector('[data-table-filter="status"]');
      clearButton = document.querySelector("[date-range-clear]");

      initTable().then(() => {
        handleSearchDatatable();
        handleStatusFilter();
      });
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  teamPPKDTable.init();
});
