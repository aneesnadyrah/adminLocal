"use strict";

// Class definition
var SurveyStartWork = (function () {
  var initializeSurveyStartWork = function () {
    let id = "";
    api
      .get(`surveys/tasking?data=note`)
      .then((response) => {
        console.log(response);
        console.log(response.data);
        const data = response.data;
        // id = data.id;
        for (let i = 0; i < data.length; i++) {
          let obj = data[i];

          let ID = obj.id;
          let SystemID = obj.system_id;
          console.log(ID);
          console.log(SystemID);

          // let modalId = $(this).data("bs-target");
          // let modal = document.querySelector(modalId);
          // let system_id = document.querySelector("#system-id").value;
          let submitId = "#submit-confirm-start-survey-" + ID;
          let submitButton = document.querySelector(submitId);
          let submitId2 = "#submit-no-start-survey-" + ID;
          let submitButton2 = document.querySelector(submitId2);

          const form = document.querySelector("#form-survey-start-work-" + ID);

          let validator;
          validator = FormValidation.formValidation(form, {
            fields: {
              "finance-remark": {
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

                const serializedArray = $(form).serializeArray().concat({
                  name: "confirm",
                  value: "1",
                });

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
                      "Kelulusan Mula Kerja Ukur Berjaya Disimpan! 🎉"
                    );

                    // Navigate to the new page after a successful API call
                    setTimeout(function () {
                      location.href = "/surveys/priority/finance";
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
                  html: "Maaf, Sila Isi <strong>Catatan</strong> untuk permohonan ini.",
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

          submitButton2.classList.remove("d-none");
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

                const serializedArray = $(form).serializeArray().concat({
                  name: "confirm",
                  value: "2",
                });

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
                      "Kelulusan Mula Kerja Ukur Berjaya Disimpan! 🎉"
                    );

                    // Navigate to the new page after a successful API call
                    setTimeout(function () {
                      location.href = "/surveys/priority/finance";
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
                  html: "Maaf, Sila Isi <strong>Catatan</strong> untuk permohonan ini.",
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
      })
      .catch((error) => {});
  };

  // Public methods
  return {
    init: function () {
      // ...

      initializeSurveyStartWork();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  SurveyStartWork.init();
});
