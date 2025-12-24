"use strict";
// Class definition
var approvalModal = (function () {
  // Elements
  let form;
  var submitButton;

  var handleSubmit = function (e) {
    // Get the URL path, Split the URL path into segments
    const systemId = window.location.pathname.split("/").pop();

		var validator = FormValidation.formValidation(
			form,
			{
				fields: {
          'utility_type': {
						validators: {
							notEmpty: {
								message: 'Jenis Utiliti diperlukan.'
							}
						}
					},
          'appl_label': {
						validators: {
							notEmpty: {
								message: 'Label Permohonan diperlukan.'
							}
						}
					},
          'fee_label': {
						validators: {
							notEmpty: {
								message: 'Jenis bayaran caj pendaftaran diperlukan.'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
						rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
					})
				}
			}
		);

    // Handle form submit
    submitButton.addEventListener("click", function (e) {
      // Prevent button default action
      e.preventDefault();

      // Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					console.log('validated!');

          if (status == 'Valid') {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;
            const serializedArray = $(form).serializeArray();
            const formData = {};
            let route = form.getAttribute("data-route");

              console.log(route);

            serializedArray.forEach((item) => {
              formData[item.name] = item.value;
            });

            const jsonData = JSON.stringify(formData);

            api
              .put("records/entry/" + route + "/" + systemId, jsonData)
              .then((response) => {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");
                // Enable button
                submitButton.disabled = false;

                Swal.fire({
                  // title: response.reference,
                  title: response.message,
                  // text: response.message,
                  icon: "success",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function (result) {
                  if (result.isConfirmed) {
                    location.href = "/operation/general/tasks/new";
                  }
                });
              })
              .catch((error) => {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                // Enable button
                submitButton.disabled = false;

                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  text: error.message,
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
						toastr.error('Sila isi maklumat yang diperlukan.');
					}

        });
      }
    });
  };

  // Public functions
  return {
    // Initialization
    init: function () {
      form = document.querySelector('[data-form="form-approval"]');
      submitButton = document.querySelector('[data-submit="approval"]');

      handleSubmit();
    },
  };
})();

// Class definition
var amendModal = (function () {
  // Elements
  let form;
  var submitButton;
  var validator;

  // Handle form
  var handleValidation = function (e) {
    var elements = [].slice.call(
      document.querySelectorAll('[data-form="form-amend"]')
    );
    elements.map(function (element) {
      // Get all required fields inside the current form
      var requiredFields = [].slice.call(
        element.querySelectorAll("[required]")
      );

      var fields = {};
      requiredFields.forEach(function (field) {
        // Use the name attribute as the key for each field
        fields[field.getAttribute("name")] = {
          validators: {
            notEmpty: {
              message: "Sila isi ruangan ini.",
            },
          },
        };
      });

      validator = FormValidation.formValidation(element, {
        fields: fields,
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap: new FormValidation.plugins.Bootstrap5({
            rowSelector: ".fv-row",
            eleInvalidClass: "", // comment to enable invalid state icons
            eleValidClass: "", // comment to enable valid state icons
          }),
        },
      });

      // Store the validator on the element for later use
      element.validator = validator;
    });
  };

  var handleSubmit = function (e) {
    // Get the URL path, Split the URL path into segments
    const systemId = window.location.pathname.split("/").pop();

    // Handle form submit
    submitButton.addEventListener("click", function (e) {
      // Prevent button default action
      e.preventDefault();

      // Show loading indication
      submitButton.setAttribute("data-kt-indicator", "on");

      // Disable button to avoid multiple click
      submitButton.disabled = true;
      const serializedArray = $(form).serializeArray();
      const formData = {};
      let route = form.getAttribute("data-route");

        console.log(route);

      serializedArray.forEach((item) => {
        formData[item.name] = item.value;
      });

      const jsonData = JSON.stringify(formData);

      api
        .post("records/entry/" + route + "/" + systemId, jsonData)
        .then((response) => {
          // Hide loading indication
          submitButton.removeAttribute("data-kt-indicator");
          // Enable button
          submitButton.disabled = false;

          Swal.fire({
            title: response.reference,
            text: response.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, maklum!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
            allowOutsideClick: false,
          }).then(function (result) {
            if (result.isConfirmed) {
              location.href = "/operation/general/tasks/new";
            }
          });
        })
        .catch((error) => {
          // Hide loading indication
          submitButton.removeAttribute("data-kt-indicator");

          // Enable button
          submitButton.disabled = false;

          Swal.fire({
            title: response.reference,
            text: response.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, maklum!",
            customClass: {
              confirmButton: "btn btn-primary",
            },
            allowOutsideClick: false,
          });
        });
    });
  };

  // Public functions
  return {
    // Initialization
    init: function () {
      form = document.querySelector('[data-form="form-amend"]');
      submitButton = document.querySelector('[data-submit="amend"]');

      handleValidation();
      handleSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  approvalModal.init();
  amendModal.init();
});
