"use strict";

// Class definition
var PPKDAddTeam = (function () {
  // Shared variables
  // const element = document.getElementById('form-ppkd-add-team');

  // Init add task modal
  var initAddTeam = () => {
    let submitId = "#submit-ppkd-add-team";
    // let formId = "#ppkd-add-team";
    let selectId = "#selection-ppkd-add-team";
    let submitButton = document.querySelector(submitId);

    // let form = document.querySelector(formId);
    const form = document.getElementById("form-ppkd-add-team");
    let validator;

    validator = FormValidation.formValidation(form, {
      fields: {
        "ppkd-team-name": {
          validators: {
            notEmpty: {
              message: "Sila Masukkan Nama Kumpulan",
            },
          },
        },
        "ppkd-add-zone": {
          validators: {
            notEmpty: {
              message: "Sila Pilih Zon",
            },
          },
        },
        "ppkd-add-team": {
          validators: {
            notEmpty: {
              message: "Sila Pilih Ahli Kumpulan",
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

    submitButton.classList.remove("d-none");
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

          console.log(jsonData);

          api
            .post(`projects/team/add`, jsonData)
            .then((response) => {
              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

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

              toastr.success("Lantikan Kumpulan PPKD Berjaya! 🎉");

              setTimeout(function () {
                location.href = "projects/team";
              }, 2500);
            })
            .catch((error) => {
              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

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
            html: "Maaf, Sila pilih <strong>Ahli Kumpulan PPKD</strong> untuk permohonan ini.",
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
    });
  };

  return {
    // Public functions
    init: function () {
      initAddTeam();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  PPKDAddTeam.init();
});
