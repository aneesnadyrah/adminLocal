"use strict";

$("#masked").text("+6" + localStorage.getItem("masked"));

if (localStorage.getItem("masked") !== null) {
} else {
  location.href = "/authentication/sign-in.php";
}

localStorage.removeItem("masked");

// Class Definition
var KTSigninTwoSteps = (function () {
  // Elements
  var form;
  var submitButton;

  // Handle form
  var handleForm = function (e) {
    // Handle form submit
    submitButton.addEventListener("click", function (e) {
      e.preventDefault();

      var validated = true;

      var inputs = [].slice.call(form.querySelectorAll('input[maxlength="1"]'));
      inputs.map(function (input) {
        if (input.value === "" || input.value.length === 0) {
          validated = false;
        }
      });

      if (validated === true) {
        // Show loading indication
        submitButton.setAttribute("data-kt-indicator", "on");

        // Disable button to avoid multiple click
        submitButton.disabled = true;

        const inputs = form.querySelectorAll("input[type=text]");

        const inputValues = Array.from(inputs).reduce((values, input) => {
          return values + input.value;
        }, "");

        // Simulate ajax request
        $.ajax({
          url: "https://"+hostApps+"/v1/authentication/verify.php",
          type: "POST",
          data: {
            "active-code": inputValues,
          },
          success: function (r) {
            // Hide loading indication
            submitButton.removeAttribute("data-kt-indicator");

            // Enable button
            submitButton.disabled = false;

            if (r.data.message === "activated") {
              // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
              Swal.fire({
                text: "Akaun anda telah berjaya disahkan!",
                icon: "success",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                  confirmButton: "btn btn-primary",
                },
              }).then(function (result) {
                if (result.isConfirmed) {
                  location.href = "/dashboard";
                }
              });
            } else {
              Swal.fire({
                text: "Kod Pengesahan anda salah! Sila log masuk semula.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                  confirmButton: "btn btn-danger",
                },
              }).then(function (result) {
                if (result.isConfirmed) {
                  location.href = "/authentication/sign-in.php";
                }
              });
            }
          },
        });
      } else {
        swal
          .fire({
            text: "Sila Masukkan Kod Pengesahan yang sah!",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, maklum!",
            customClass: {
              confirmButton: "btn fw-bold btn-light-danger",
            },
          })
          .then(function () {
            KTUtil.scrollTop();
          });
      }
    });
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

  // Public functions
  return {
    // Initialization
    init: function () {
      form = document.querySelector("#kt_sing_in_two_steps_form");
      submitButton = document.querySelector("#kt_sing_in_two_steps_submit");

      handleForm();
      handleType();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTSigninTwoSteps.init();
});
