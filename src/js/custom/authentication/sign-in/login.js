"use strict";

// Class definition
var KTSigninGeneral = (function () {
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

  // Handle form
  var handleValidation = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        username: {
          validators: {
            notEmpty: {
              message: "Nama pengguna diperlukan",
            },
            stringLength: {
              min: 6,
              max: 30,
              message: "Nama pengguna mestilah lebih daripada 6 aksara",
            },
            regexp: {
              regexp: /^[a-zA-Z0-9_\.]+$/,
              message:
                "Nama pengguna hanya boleh terdiri daripada abjad, nombor, titik dan garis bawah",
            },
          },
        },
        password: {
          validators: {
            notEmpty: {
              message: "Kata Laluan Diperlukan",
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
  };

  var handleSubmit = function (e) {
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

          var formData = $(form).serializeArray();
          var data = {};

          $(formData).each(function(index, obj) {
            data[obj.name] = obj.value;
          });

          api.post('auth/login', JSON.stringify(data)).then(response => {

              console.log(response);
              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

              if(response.message == "success") {

                toastr.success("Selamat Datang, " + response.first_name + "!");

                setTimeout(function () {
                  form.querySelector('[name="username"]').value = "";
                  form.querySelector('[name="password"]').value = "";

                  //form.submit(); // submit form
                  location.href = "/dashboard";
                }, 2500);
              } else if (response.message == "false") {
                toastr.warning("Maaf, Akaun anda tidak diaktifkan lagi. Sila isi 6 digit nombor verifikasi.");

                setTimeout(function () {
                  form.querySelector('[name="username"]').value = "";
                  form.querySelector('[name="password"]').value = "";

                  localStorage.setItem("masked", response.phone_no);

                  const serializedArray = {
                    id: response.telegram_id,
                    code: response.code,
                    type: '1',
                  };

                  const data = serializedArray;

                  api.post('notification/code', JSON.stringify(data))
                  .then(response => {
                    if(response.message == "success") { 
                      location.href = "/auth/verify";
                    }
                    
                  }).catch(error => {
                    console.log(error);
                  })
                }, 2500);
              } else if (response.message == "failed"){
                toastr.error("Maaf, Sila Semak Nama Pengguna Atau Kata Laluan Anda.");
              }
          }).catch(error => {
            console.log(error);
            // Hide loading indication
            submitButton.removeAttribute("data-kt-indicator");

            // Enable button
            submitButton.disabled = false;

            toastr.error("Maaf, Terdapat Ralat di dalam sistem. Sila cuba sekali lagi.");
          })
        } else {
          toastr.error("Maaf, Terdapat Ralat di dalam sistem. Sila cuba sekali lagi.");
        }
      });
    });
  };

  // Public functions
  return {
    // Initialization
    init: function () {
      form = document.querySelector("#sign_in_form");
      submitButton = document.querySelector("#sign_in_submit");

      handleValidation();
      handleSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTSigninGeneral.init();
});
