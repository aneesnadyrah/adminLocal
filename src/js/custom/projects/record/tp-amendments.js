"use strict";
// Class definition
var approvalTPModal = (function () {
  // Elements
  let form;
  let submitButton;

  var handleSubmit = function (e) {
    // Get the URL path, Split the URL path into segments
    const systemId = window.location.pathname.split("/").pop();

    var validator = FormValidation.formValidation(
			form,
			{
				fields: {
          'notes': {
						validators: {
							notEmpty: {
								message: 'Catatan tidak boleh dibiarkan kosong.'
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

            api.put("reports/tp/amend/" + route + "/" + systemId, jsonData)
            .then((response) => {
              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");
              // Enable button
              submitButton.disabled = false;

              Swal.fire({
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
      form = document.querySelector('[data-form="form-approval-tp"]');
      submitButton = document.querySelector('[data-submit="approval-tp"]');

      handleSubmit();
    },
  };
})();

var amendTPModal = (function () {
  // Elements
  let form;
  let submitButton;

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

      api.post("reports/tp/amend/" + route + "/" + systemId, jsonData)
      .then((response) => {
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");
        // Enable button
        submitButton.disabled = false;

        Swal.fire({
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

  });

  };

    // Public functions
    return {
      // Initialization
      init: function () {
        form = document.querySelector('[data-form="form-amend-tp"]');
        submitButton = document.querySelector('[data-submit="amend-tp"]');
  
        handleSubmit();
      },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    approvalTPModal.init();
    amendTPModal.init();
});