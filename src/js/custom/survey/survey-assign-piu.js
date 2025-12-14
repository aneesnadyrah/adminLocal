"use strict";

// Class definition
var assignPIU = (function () {
  // Private variables
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
  var initModals = function () {
    // Get modal elements
    let piuModal = [].slice.call(
      document.querySelectorAll('[data-modal="piu-modal"]')
    );

    piuModal.forEach(function (e) {
      let modal1;
      let modal2;
      // let modal3;
      let modal4;

      let modal1Button = e.querySelector('[data-modal-action="continue-piu"]');
      let nextModalId = modal1Button.getAttribute("data-next-modal");

      modal1 = new bootstrap.Modal(e);
      modal2 = new bootstrap.Modal(document.getElementById(nextModalId + "-2"));
      // modal3 = new bootstrap.Modal(document.getElementById(nextModalId + "-3"));
      modal4 = new bootstrap.Modal(document.getElementById(nextModalId + "-4"));

      modal1Button.addEventListener("click", function (b) {
        b.preventDefault();
        let selectedOption = e.querySelector(
          'select[name="survey-provider"]'
        ).value;
        console.log(selectedOption);
        // if (selectedOption === "inhouse") {
        //   modal1.hide();
        //   modal2.show();
        // } else if (selectedOption === "outsource") {
        //   modal1.hide();
        //   modal3.show();
        // }
        if (selectedOption === "inhouse") {
          modal1.hide();
          modal2.show();
          // modal3.hide();
          modal4.hide();
        } else if (selectedOption === "outsource") {
          modal1.hide();
          modal2.hide();
          // modal3.hide();
          modal4.show();
        }
      });

      // Close modal when "X" button is clicked for each modal
      let closeButton1 = e.querySelector('.btn[data-bs-dismiss="modal"]');
      let closeButton2 = document
        .getElementById(nextModalId + "-2")
        .querySelector('.btn[data-bs-dismiss="modal"]');
      // let closeButton3 = document
      //   .getElementById(nextModalId + "-3")
      //   .querySelector('.btn[data-bs-dismiss="modal"]');
      let closeButton4 = document
        .getElementById(nextModalId + "-4")
        .querySelector('.btn[data-bs-dismiss="modal"]');

      closeButton1.addEventListener("click", function () {
        modal1.hide();
      });

      closeButton2.addEventListener("click", function () {
        modal2.hide();
      });

      // closeButton3.addEventListener("click", function () {
      //   modal3.hide();
      // });

      closeButton4.addEventListener("click", function () {
        modal4.hide();
      });

      // Clear selection when "X" button is clicked
      let clearButton = e.querySelector('.btn[data-bs-dismiss="modal"]');
      let selectElement = e.querySelector('select[name="category"]');

      clearButton.addEventListener("click", function () {
        selectElement.selectedIndex = -1;
      });
    });
  };

  var initializeInhouseOutsource = function () {
    var elements = [].slice.call(
      document.querySelectorAll("[data-inhouse],[data-outsource]")
    );
    elements.forEach(function (items) {
      var systemId = items.getAttribute("data-system-id");
      var Id = items.getAttribute("data-id");
      console.log(systemId);
      if (items.getAttribute("data-inhouse")) {
        let submitId = "#submit-confirm-inhouse-" + Id;
        let submitButton = items.querySelector(submitId);

        const form = items.querySelector("#form-inhouse-" + Id);

        let validator;
        validator = FormValidation.formValidation(form, {
          fields: {
            "survey-group": {
              validators: {
                notEmpty: {
                  message: "Sila Pilih Kumpulan Ukur",
                },
              },
            },
            "modal-date-range": {
              validators: {
                notEmpty: {
                  message: "Sila Pilih Tarikh Jangkaan Mula & Tamat",
                },
              },
            },
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
              rowSelector: ".fv-row",
              eleInvalidClass: "", // comment to enable invalid state icons
              eleValidClass: "", // comment to enable valid state icons
            }),
          },
        });

        //   submitButton.classList.remove("d-none");
        // Handle form submit
        submitButton.addEventListener("click", function (e) {
          // Prevent button default action
          e.preventDefault();

          // Validate form
          validator.validate().then(function (status) {
            if (status == "Valid") {
              // Show loading indication
              submitButton.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              submitButton.disabled = true;

              const serializedArray = $(form).serializeArray().concat(
                {
                  name: "survey-provider",
                  value: "Inhouse",
                },
                {
                  name: "system-id",
                  value: systemId,
                }
              );

              const formData = {};

              serializedArray.forEach((item) => {
                formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);

              console.log(jsonData);

              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  console.log(response);
                  toastr.options = {
                    closeButton: false,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toastr-bottom-right",
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

                  // toastr.success(response.message);
                  toastr.success("Kumpulan Ukur Berjaya Dilantik! 🎉");

                  // Navigate to the new page after a successful API call
                  setTimeout(function () {
                    location.reload();
                  }, 2500);
                })
                .catch((error) => {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
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
                });
            } else {
              // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
              Swal.fire({
                html: "Maaf, Sila Isi <strong>Maklumat</strong> untuk lantikan ini.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                  confirmButton: "btn btn-primary",
                },
                allowOutsideClick: false,
              });
            }
          });
          // });
        });
      } else if (items.getAttribute("data-outsource")) {
        let submitId2 = "#submit-confirm-outsource-" + Id;
        let submitButton2 = items.querySelector(submitId2);
        const form2 = items.querySelector("#form-outsource-" + Id);

        let validator2;
        validator2 = FormValidation.formValidation(form2, {
          fields: {
            // "survey-outsource": {
            //   validators: {
            //     notEmpty: {
            //       message: "Sila Pilih Juru Ukur",
            //     },
            //   },
            // },
            // "modal-date-range2": {
            //   validators: {
            //     notEmpty: {
            //       message: "Sila Pilih Tarikh Jangkaan Mula & Tamat",
            //     },
            //   },
            // },
            "notes": {
              validators: {
                notEmpty: {
                  message: "Sila Isi Catatan",
                },
              },
            },
          },
          plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap: new FormValidation.plugins.Bootstrap5({
              rowSelector: ".fv-row",
              eleInvalidClass: "", // comment to enable invalid state icons
              eleValidClass: "", // comment to enable valid state icons
            }),
          },
        });

        //   submitButton2.classList.remove("d-none");
        // Handle form submit
        submitButton2.addEventListener("click", function (e) {
          // Prevent button default action
          e.preventDefault();

          // Validate form
          validator2.validate().then(function (status) {
            if (status == "Valid") {
              // Show loading indication
              submitButton2.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              submitButton2.disabled = true;

              const serializedArray = $(form2).serializeArray().concat(
                {
                  name: "survey-provider",
                  value: "Outsource",
                },
                {
                  name: "system-id",
                  value: systemId,
                }
              );

              const formData = {};

              serializedArray.forEach((item) => {
                formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);

              // console.log(jsonData);

              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  toastr.options = {
                    closeButton: false,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toastr-bottom-right",
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

                  // toastr.success(response.message);
                  toastr.success("Berjaya Dihantar 🎉");

                  // Navigate to the new page after a successful API call
                  setTimeout(function () {
                    location.reload();
                  }, 2500);
                })
                .catch((error) => {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    text: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, maklum.",
                    customClass: {
                      confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                  });
                });
            } else {
              // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
              Swal.fire({
                html: "Maaf, Sila Isi <strong>Catatan</strong> untuk teruskan.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                  confirmButton: "btn btn-primary",
                },
                allowOutsideClick: false,
              });
            }
          });
          // });
        });
      }
    });
  };

  return {
    // Public function to initialize the module
    init: function () {
      initFlatpickr();
      initModals();
      initializeInhouseOutsource();
    },
  };
})();

// On document ready
document.addEventListener("DOMContentLoaded", function () {
  // Ensure that both modals are initialized before adding event listeners
  assignPIU.init();
});
