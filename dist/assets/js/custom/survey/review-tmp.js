"use strict";

// Class definition
var ReviewTMP = (function () {
  var initializeReviewTMP = function () {
    var elements = [].slice.call(
      document.querySelectorAll("[data-review-tmp]")
    );

    elements.map(function (items) {
      var systemId = items.getAttribute("data-system-id");
      var Id = items.getAttribute("data-id");
      let submitId = "#submit-confirm-review-tmp-" + Id;
      let submitButton = items.querySelector(submitId);
      let submitId2 = "#submit-no-review-tmp-" + Id;
      let submitButton2 = items.querySelector(submitId2);

      const form = items.querySelector("#form-review-tmp-" + Id);
      //   console.log(systemId);
      //   console.log(Id);
      //   console.log(submitId);
      //   console.log(submitButton);
      //   console.log(submitId2);
      //     console.log(submitButton2);
      //     console.log(form);

      let validator;
      validator = FormValidation.formValidation(form, {
        fields: {
          "review-tmp-remark": {
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
                name: "confirm",
                value: "1",
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
                toastr.success(
                  "Semakan Pelan Kawalan Trafik Terima Berjaya Disimpan! 🎉"
                );

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
              html: "Maaf, Sila Isi <strong>Catatan</strong> untuk semakan ini.",
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

      //   submitButton2.classList.remove("d-none");
      // Handle form submit
      submitButton2.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        validator.validate().then(function (status) {
          if (status == "Valid") {
            // Show loading indication
            submitButton2.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton2.disabled = true;

            const serializedArray = $(form).serializeArray().concat(
              {
                name: "confirm",
                value: "2",
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
                toastr.success(
                  "Semakan Pelan Kawalan Trafik Berjaya Disimpan! 🎉"
                );

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
              html: "Maaf, Sila Isi <strong>Catatan</strong> untuk semakan ini.",
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
    });
  };

  // Public methods
  return {
    init: function () {
      // ...

      initializeReviewTMP();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  ReviewTMP.init();
});
