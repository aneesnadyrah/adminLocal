"use strict";

// Class Definition
var authResetPassword = (function () {
  // Elements
  var form;
  var submitButton;
  var validator;
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

  var handleForm = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        "ID-card-no": {
          validators: {
            notEmpty: {
              message: "Sila masukkan No Kad Pengenalan Anda",
            },
            regexp: {
              regexp: /\d{2}(0[1-9]|1[0-2])(0[1-9]|[12][0-9]|3[01])\d{5}/,
              message: "Sila semak kembali No Kad Pengenalan Anda",
            },
            stringLength: {
              min: 12,
              max: 12,
              message: "Digit No Kad Pengenalan tidak mencukupi",
            },
          },
        },
        "phone-no": {
          validators: {
            notEmpty: {
              message: "Sila masukkan No Telefon yang Berdaftar Anda",
            },
            regexp: {
              regexp:
                /^(01[0-46-9]-*\d{7,8}|011-1\d{6}|011-3\d{6}|011-7\d{6}|013-\d{7}|014-\d{7}|016-\d{7}|017-\d{7}|018-\d{7}|019-\d{7})$/,
              message: "Sila semak kembali No Telefon Anda",
            },
            stringLength: {
              min: 10,
              max: 11,
              message: "Digit No Telefon anda terlebih/tidak mencukupi",
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

    submitButton.addEventListener("click", function (e) {
      e.preventDefault();

      // Validate form
      validator.validate().then(function (status) {
        if (status == "Valid") {
          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable button to avoid multiple click
          submitButton.disabled = true;

          var formData = $(form).serializeArray();
          var data = {};

          formData.forEach((item) => {
            data[item.name] = item.value;
          });
          const json = JSON.stringify(data);

          api
            .post("auth/recovery", json)
            .then((response) => {
              if (response.message === "success") {
                submitButton.removeAttribute("data-kt-indicator");
                submitButton.disabled = false;
                //   console.log(formData);
                localStorage.setItem("masked", response.phone_no);
                toastr.success(
                  "Kod Keselamatan anda telah di hantar kepada telegram anda!"
                );

                var jsonData = {
                  id: response.telegram_id,
                  code: response.code,
                  type: 2,
                };

                // Stringify the JSON object
                var jsonString = JSON.stringify(jsonData);

                setTimeout(function () {
                  api
                    .post("notification/code", jsonString, {
                      "Content-Type": "multipart/form-data",
                    })
                    .then((response) => {
                      if (response.message == "success") {
                        location.href = "/auth/recovery";
                        form.reset();
                      }
                    });
                }, 1500);
              } else if (response.message == "failed") {
                toastr.error(
                  "Kami tidak menjumpai sebarang maklumat akaun anda. Sila Cuba Lagi!"
                );
              }
            })
            .catch((error) => {
              // Show loading indication
              submitButton.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              submitButton.disabled = true;
              console.log(error);
            });
        } else {
          // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
          Swal.fire({
            text: "Maaf, Sila isi butiran yang diminta. Terima Kasih",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, maklum!",
            customClass: {
              confirmButton: "btn btn-danger",
            },
          });
        }
      });
    });
  };

  // Public Functions
  return {
    // public functions
    init: function () {
      form = document.querySelector("#form-password-reset");
      submitButton = document.querySelector("#submit-password-reset");

      handleForm();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  authResetPassword.init();
});
