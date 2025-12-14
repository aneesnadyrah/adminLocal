"use strict";

// Class Definition
var KTAuthNewPassword = (function () {
  // Elements
  var form;
  var submitStepper;
  var submitButton;
  var validator;
  var passwordMeter;
  var validateStepper;
  var validateButton;
  var empId;
  var username;

  $("#masked-number").text("+6" + localStorage.getItem("masked"));
  localStorage.getItem("masked") === null
    ? (location.href = "/auth/signin")
    : "";

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

  var validateForm = function (e) {
    // Handle form submit
    validateButton.addEventListener("click", function (e) {
      e.preventDefault();
      var validated = true;

      var inputs = [].slice.call(form.querySelectorAll('input[name^="code_"]'));
      inputs.map(function (input) {
        if (input.value === "" || input.value.length === 0) {
          validated = false;
        }
      });

      if (validated === true) {
        // Show loading indication
        validateButton.setAttribute("data-kt-indicator", "on");

        // Disable button to avoid multiple click
        validateButton.disabled = true;

        var codes = "";
        // Loop through the input fields and concatenate their values
        form.querySelectorAll('input[name^="code_"]').forEach(function (input) {
          codes += input.value;
        });

        // var formData = new FormData();
        var formData = $(form).serializeArray();

        var data = {};
        formData.forEach((item) => {
          data[item.name] = item.value;
        });

        var json = {
          "reset-code": codes,
        };

        data = Object.assign(data, json);

        api
          .post("auth/verify", JSON.stringify(data), {
            "Content-Type": "multipart/form-data",
          })
          .then((response) => {
            // Hide loading indication
            validateButton.removeAttribute("data-kt-indicator");
            // Enable button
            validateButton.disabled = false;
            if (response.message === "success") {
              submitStepper.classList.remove("d-none");
              submitButton.classList.remove("d-none");
              validateStepper.classList.add("d-none");
              validateButton.classList.add("d-none");

              form.querySelector("#masked-number").classList.add("d-none");
              form.querySelector("#masked-title").classList.add("d-none");
              form.querySelector("#masked-subtitle").classList.add("d-none");

              empId = response.id;
              username = response.username;
            } else {
              toastr.error("Kod Pengesahan anda masukkan tidak sah.");
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    });
  };

  var handleForm = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        password: {
          validators: {
            notEmpty: {
              message: "Kata Laluan diperlukan",
            },
            callback: {
              message:
                "Sila Masukkan Kata Laluan dengan gabungan huruf, nombor & simbol.",
              callback: function (input) {
                if (input.value.length > 0) {
                  return validatePassword();
                }
              },
            },
          },
        },
        "confirm-password": {
          validators: {
            notEmpty: {
              message: "Pengesahan Kata Laluan diperlukan",
            },
            identical: {
              compare: function () {
                return form.querySelector('[name="password"]').value;
              },
              message: "Kata Laluan yang dimasukkan tidak sama",
            },
          },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger({
          event: {
            password: false,
          },
        }),
        bootstrap: new FormValidation.plugins.Bootstrap5({
          rowSelector: ".fv-row",
          eleInvalidClass: "", // comment to enable invalid state icons
          eleValidClass: "", // comment to enable valid state icons
        }),
      },
    });

    submitButton.addEventListener("click", function (e) {
      e.preventDefault();

      validator.revalidateField("password");

      validator.validate().then(function (status) {
        if (status == "Valid") {
          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable button to avoid multiple click
          submitButton.disabled = true;

          // var formData = new FormData(form);
          var formData = $(form).serializeArray();

          var data = {};
          formData.forEach((item) => {
            data[item.name] = item.value;
          });

          var jsonData = {
            "emp-id": empId,
            username: username,
          };

          data = Object.assign(data, jsonData);
          // Stringify the JSON object
          var jsonString = JSON.stringify(data);

          // Simulate ajax request
          api
            .post("auth/verify", jsonString, {
              "Content-Type": "multipart/form-data",
            })
            .then((response) => {
              if (response.message === "success") {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");
                // Enable button
                submitButton.disabled = false;

                toastr.success("Kata Laluan anda berjaya diset semula! 🎉");

                setTimeout(function () {
                  form.reset();
                  passwordMeter.reset(); // reset password meter
                  localStorage.removeItem("masked");
                  location.href = "/auth/signin";
                }, 1500);
              } else {
                toastr.error(
                  "Maaf Terdapat Ralat di bahagian dalaman sistem. Sila Cuba sekali lagi."
                );
              }
            })
            .catch((error) => {
              toastr.error(
                "Maaf Terdapat Ralat di bahagian dalaman sistem. Sila Cuba sekali lagi."
              );
            });
        } else {
          toastr.warning("Maaf, sila masukkan kata laluan dengan betul.");
        }
      });
    });

    form
      .querySelector('input[name="password"]')
      .addEventListener("input", function () {
        if (this.value.length > 0) {
          validator.updateFieldStatus("password", "NotValidated");
        }
      });
  };

  var validatePassword = function () {
    return passwordMeter.getScore() === 100;
  };

  var handleType = function () {
    const inputs = form.querySelectorAll("input[type=text]");

    inputs.forEach((input) => {
      input.addEventListener("input", function (e) {
        if (this.value.length === 1) {
          const nextInput = inputs[Array.from(inputs).indexOf(input) + 1];
          if (nextInput) {
            nextInput.focus();
          }
        } else if (this.value.length === 0) {
          const prevInput = inputs[Array.from(inputs).indexOf(input) - 1];
          if (prevInput) {
            prevInput.focus();
          }
        }
      });

      input.addEventListener("paste", function (e) {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData("text");
        for (let i = 0; i < inputs.length; i++) {
          inputs[i].value = paste[i] || "";
        }
        inputs[0].focus();
      });
    });
    inputs[0].focus();
  };

  // Public Functions
  return {
    // public functions
    init: function () {
      form = document.querySelector("#form-recovery-password");
      validateButton = document.querySelector("#validate-recovery-password");
      validateStepper = document.querySelector('[data-stepper="validate"]');
      submitStepper = document.querySelector('[data-stepper="submit"]');
      submitButton = document.querySelector("#submit-recovery-password");
      passwordMeter = KTPasswordMeter.getInstance(
        form.querySelector('[data-kt-password-meter="true"]')
      );

      validateForm();
      handleForm();
      handleType();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTAuthNewPassword.init();
});
