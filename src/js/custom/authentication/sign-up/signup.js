"use strict";

// Class definition
var KTCreateApp = (function () {


  var origin = new URL(window.location.href).origin;
  var stepper;
  var form;
  var formSubmitButton;
  var formContinueButton;
  var formValidateButton;
  var formBackButton;

  // Variables
  var stepperObj;
  var validations = [];
  var passwordMeter;

  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: true,
    progressBar: false,
    positionClass: "toastr-bottom-right",
    preventDuplicates: true,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "2000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  var initPostcode = function () {
    $("#postcode").on("input", function () {
      var postcode = $("#postcode").val();
      api.get('postcode/' + postcode).then(function (response) {
        $("#city").val(response.city);
        $("#state").val(response.state);
    }).catch(function (error) {
      console.log(error);
    })
    });
  };

  // Private Functions
  var initStepper = function () {
    // Initialize Stepper
    stepperObj = new KTStepper(stepper);

    // Stepper change event(handle hiding submit button for the last step)
    stepperObj.on("kt.stepper.changed", function (stepper) {
      if (stepperObj.getCurrentStepIndex() === 3) {
        formSubmitButton.classList.remove("d-none");
        formSubmitButton.classList.add("d-inline-block");
        formContinueButton.classList.add("d-none");
        formBackButton.classList.add("d-inline-block");
        formBackButton.classList.remove("d-none");
      } else if (stepperObj.getCurrentStepIndex() === 4) {
        formBackButton.classList.remove("d-inline-block");
        formBackButton.classList.add("d-none");
        formSubmitButton.classList.add("d-none");
        formContinueButton.classList.add("d-none");
        formValidateButton.classList.add("d-none");
      } else if (stepperObj.getCurrentStepIndex() === 2) {
        formSubmitButton.classList.add("d-none");
        formContinueButton.classList.remove("d-none");
        formContinueButton.classList.add("d-inline-block");
        formValidateButton.classList.add("d-none");
        formValidateButton.classList.remove("d-inline-block");
        formBackButton.classList.add("d-none");
      } else {
        formSubmitButton.classList.remove("d-inline-block");
        formSubmitButton.classList.add("d-none");
        formContineButton.classList.remove("d-inline-block");
        formContinueButton.classList.add("d-none");
      }
    });

    // Validation before going to next page
    stepperObj.on("kt.stepper.next", function (stepper) {
      console.log("stepper.next");

      // Validate form before change stepper step
      var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step

      if (validator) {
        validator.validate().then(function (status) {
          console.log("validated!");

          if (status == "Valid") {
            stepper.goNext();

            //KTUtil.scrollTop();
          } else {
            // Show error message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
              text: "Harap Maaf, anda perlu mengisi kesemua butiran sebelum meneruskan langkah seterusnya",
              icon: "error",
              buttonsStyling: false,
              confirmButtonText: "Ok, maklum!",
              customClass: {
                confirmButton: "btn btn-light",
              },
            }).then(function () {
              //KTUtil.scrollTop();
            });
          }
        });
      } else {
        stepper.goNext();

        KTUtil.scrollTop();
      }
    });

    // Prev event
    stepperObj.on("kt.stepper.previous", function (stepper) {
      console.log("stepper.previous");

      stepper.goPrevious();
      KTUtil.scrollTop();
    });

    formValidateButton.addEventListener("click", function (e) {
      // Validate form before change stepper step
      var validator = validations[0]; // get validator for first form

      validator.validate().then(function (status) {
        // console.log("validated!");

        if (status == "Valid") {
          // Prevent default button action
          e.preventDefault();

          // Disable button to avoid multiple click
          formValidateButton.disabled = true;

          // Show loading indication
          formValidateButton.setAttribute("data-kt-indicator", "on");

          var formData = new FormData();
          formData.append('ID_card_no', $("input[name='ID_card_no']").val());

          // api.post('auth/register', formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(function (response) {
          api.post('auth/register', formData).then(response => {
            // console.log(response);
            formValidateButton.removeAttribute("data-kt-indicator");
              // Enable button
              formValidateButton.disabled = false;

              if (response.valid === true && response.registered === false) {

                toastr.success(`Selamat Datang, ${response.first_name}!`);

                setTimeout(function () {
                  $("#first-name").val(response.first_name);
                  $("#last-name").val(response.last_name);
                  $("#position").val(response.position);
                  $("#phone-no").val(response.phone_no);
                  $("#email").val(response.email);
                  $("#first-address").val(response.first_address);
                  $("#second-address").val(response.second_address);
                  $("#postcode").val(response.postcode);
                  $("#staff-id").val(response.employee_id);

                  api.get('postcode/' + response.postcode).then(function (response) {
                      $("#city").val(response.city);
                      $("#state").val(response.state);

                  }).catch(function (error) {
                    console.log(error);
                  })
                  stepperObj.goNext();
                }, 2500);

              } else if (response.valid === false) {
                toastr.error(`Anda bukan kakitangan ${response.tenant}, Sila Hubungi Pegawai Sumber Manusia untuk semakan.`);
              } else {
                toastr.warning(`${response.first_name}, anda telah mempunyai akaun berdaftar sila klik pautan log masuk`);
              }

          }).catch(function (error) {
            console.log(error);
            // Hide loading indication
            formValidateButton.removeAttribute("data-kt-indicator");
            // Enable button
            formValidateButton.disabled = false;
            toastr.error("Terdapat Ralat Pada Pendaftaran! Sila Cuba Sekali lagi");
          });
        }
      });
    });

    formSubmitButton.addEventListener("click", function (e) {
      // Validate form before change stepper step
      var validator = validations[2]; // get validator for last form

      validator.validate().then(function (status) {
        console.log("validated!");

        if (status == "Valid") {
          // Prevent default button action
          e.preventDefault();

          // Disable button to avoid multiple click
          formSubmitButton.disabled = true;

          // Show loading indication
          formSubmitButton.setAttribute("data-kt-indicator", "on");

          var formData = new FormData(form);
          api.post('auth/register', formData, {headers: {'Content-Type': 'multipart/form-data'}}).then(function (response) {

            formSubmitButton.removeAttribute("data-kt-indicator");
              // Enable button
              formSubmitButton.disabled = false;

              toastr.success(`Tahniah, ${$("#first-name").val()}! Akaun berjaya didaftarkan`);

              setTimeout(function () {
                form.reset(); // reset form
                passwordMeter.reset(); // reset password meter

                stepperObj.goNext();
              }, 2500);
            
          }).catch(function (error) {
            // Hide loading indication
            formSubmitButton.removeAttribute("data-kt-indicator");
            // Enable button
            formSubmitButton.disabled = false;
            toastr.error("Terdapat Ralat Pada Pendaftaran! Sila Cuba Sekali lagi");
            console.log(error);
          });
        }
      });
    });
  };

  var initValidation = function () {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    // Step 1
    validations.push(
      FormValidation.formValidation(form, {
        fields: {
          "ID_card_no": {
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
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap: new FormValidation.plugins.Bootstrap5({
            rowSelector: ".fv-row",
            eleInvalidClass: "",
            eleValidClass: "",
          }),
        },
      })
    );

    // Step 2
    validations.push(
      FormValidation.formValidation(form, {
        fields: {
          "first-name": {
            validators: {
              notEmpty: {
                message: "Nama Awal anda diperlukan",
              },
            },
          },
          "last-name": {
            validators: {
              notEmpty: {
                message: "Nama Akhir anda diperlukan",
              },
            },
          },
          "position": {
            validators: {
              notEmpty: {
                message: "Jawatan anda diperlukan",
              },
            },
          },
          "email": {
            validators: {
              regexp: {
                regexp: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                message: "Nilai itu bukan alamat e-mel yang sah",
              },
              notEmpty: {
                message: "Alamat e-mel anda diperlukan",
              },
            },
          },
          "first-address": {
            validators: {
              notEmpty: {
                message: "Nombor Unit/Rumah dan Bangunan diperlukan",
              },
            },
          },
          "second-address": {
            validators: {
              notEmpty: {
                message: "Nama Jalan atau Taman diperlukan",
              },
            },
          },
          "postcode": {
            validators: {
              notEmpty: {
                message: "Poskod diperlukan",
              },
              regexp: {
                regexp: /^\d{5}$/,
                message: "Sila isi Nombor Poskod yang sah",
              },
            },
          },
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          // Bootstrap Framework Integration
          bootstrap: new FormValidation.plugins.Bootstrap5({
            rowSelector: ".fv-row",
            eleInvalidClass: "",
            eleValidClass: "",
          }),
        },
      })
    );

    // Step 3
    validations.push(
      FormValidation.formValidation(form, {
        fields: {
          "telegram-id": {
            validators: {
              regexp: {
                regexp: /^\d{6,}$/,
                message: "Sila semak kembali ID Telegram anda",
              },
              notEmpty: {
                message: "ID Telegram diperlukan",
              },
            },
          },
          username: {
            validators: {
              notEmpty: {
                message: "Nama pengguna diperlukan",
              },
              stringLength: {
                min: 6,
                max: 30,
                message:
                  "Nama pengguna mestilah lebih daripada 6 dan panjang kurang daripada 30 aksara",
              },
              regexp: {
                regexp: /^[a-zA-Z0-9_\.]+$/,
                message:
                  "Nama pengguna hanya boleh terdiri daripada abjad, nombor, titik dan garis bawah",
              },
              // Place the remote validator in the last
              remote: {
                url: `${origin}/api/auth/register`,
                type: "GET",
                message:
                  "Nama Pengguna telah diambil. Sila cuba nama pengguna yang lain.",
              },
            },
          },
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
          message: new FormValidation.plugins.Message({
            clazz: "text-danger",
            container: function (field, ele) {
              return FormValidation.plugins.Message.getClosestContainer(
                ele,
                form,
                /^(.*)fv-row(.*)$/
              );
            },
          }),
        },
      })
    );

    // Password input validation
    var validatePassword = function () {
      return passwordMeter.getScore() === 100;
    };
  };

  return {
    // Public Functions
    init: function () {
      stepper = document.querySelector("#register_stepper");
      form = document.querySelector("#register_form");
      formSubmitButton = stepper.querySelector(
        '[data-kt-stepper-action="submit"]'
      );
      formContinueButton = stepper.querySelector(
        '[data-kt-stepper-action="next"]'
      );
      formValidateButton = stepper.querySelector(
        '[data-kt-stepper-action="validate"]'
      );
      formBackButton = stepper.querySelector(
        '[data-kt-stepper-action="previous"]'
      );

      passwordMeter = KTPasswordMeter.getInstance(
        form.querySelector('[data-kt-password-meter="true"]')
      );

      initStepper();
      initValidation();
      initPostcode();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTCreateApp.init();
});
