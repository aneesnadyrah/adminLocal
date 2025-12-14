"use strict";
if (document.getElementById("approve_sv_amendReview_form") === null && document.getElementById("amend_sv_amendReview_form") === null) {
  // Element does not exist
} else {

  // Class definition
  var approveModal = (function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleValidation = function (e) {
      // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
      validator = FormValidation.formValidation(form, {
        fields: {
          "notes": {
            validators: {
              notEmpty: {
                message: "Ruang catatan hendaklah di isi",
              },
            },
          },
        },
      });
    };

    var handleSubmit = function (e) {
      // Get the URL path, Split the URL path into segments
      const systemId = window.location.pathname.split('/').pop(); 

      // Handle form submit
      submitButton.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        validator.validate().then(function (status) {
          // console.log(status);
          if (status == "Valid") {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            var serializedArray = $(form).serializeArray();
            var formData = {};

            serializedArray.forEach(item => {
                formData[item.name] = item.value;
            });

            api.put('reports/site/amend/review/'+systemId, JSON.stringify(formData))
            .then(response => {
                submitButton.setAttribute("data-kt-indicator", "on");
                submitButton.disabled = true;

                toastr.success(response.message);
                setTimeout(function () {
                    location.href = "/tasks/new";
                }, 2500);

            })
            .catch(error => {
                Swal.fire({
                    text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, faham",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // Enable submit button after loading
                        submitButton.disabled = false;
                    }
                });
            });
          } else {
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
              html: "Maaf, Sila masukkan <strong>catatan</strong> untuk permohonan ini.",
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

    // Public functions
    return {
      // Initialization
      init: function () {
        form = document.querySelector("#approve_sv_amendReview_form");
        submitButton = document.querySelector("#approve_sv_amendReview_submit");

        handleValidation();
        handleSubmit();
      },
    };
  })();

  // Class definition
  var amendModal = (function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleValidation = function (e) {
      // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
      validator = FormValidation.formValidation(form, {
        fields: {
          "notes": {
            validators: {
              notEmpty: {
                message: "Ruang catatan hendaklah di isi",
              },
            },
          },
        },
      });
    };

    var handleSubmit = function (e) {
      // Get the URL path, Split the URL path into segments
      const systemId = window.location.pathname.split('/').pop(); 

      // Handle form submit
      submitButton.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        validator.validate().then(function (status) {
          // console.log(status);
          if (status == "Valid") {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            var serializedArray = $(form).serializeArray();
            var formData = {};

            serializedArray.forEach(item => {
                formData[item.name] = item.value;
            });

            api.put('reports/site/amend/review/'+systemId, JSON.stringify(formData))
            .then(response => {
                submitButton.setAttribute("data-kt-indicator", "on");
                submitButton.disabled = true;

                toastr.success(response.message);
                setTimeout(function () {
                    location.href = "/tasks/new";
                }, 2500);

            })
            .catch(error => {
                Swal.fire({
                    text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, faham",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // Enable submit button after loading
                        submitButton.disabled = false;
                    }
                });
            });

          } else {
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
              html: "Maaf, Sila masukkan <strong>catatan</strong> untuk permohonan ini.",
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

    // Public functions
    return {
      // Initialization
      init: function () {
        form = document.querySelector("#amend_sv_amendReview_form");
        submitButton = document.querySelector("#amend_sv_amendReview_submit");

        handleValidation();
        handleSubmit();
      },
    };
  })();

  // On document ready
  KTUtil.onDOMContentLoaded(function () {
    approveModal.init();
    amendModal.init();
  });
}
