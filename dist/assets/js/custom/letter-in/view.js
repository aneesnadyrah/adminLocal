"use strict";

// Class definition
var LetterView = (function () {

  // Submit form
  var submitActionLetterIn = () => {
    // Define variables
    let validator;

    // Get elements
    let form = document.getElementById('form_action_letter_in');
    let submitAction = document.getElementById('submit_action_letter');

    //for image
    let selectId = "#selection-staff";
    // Format options
    var optionFormat = function (item) {
      if (!item.id) {
        return item.text;
      }

      var span = document.createElement("span");
      var imgUrl = item.element.getAttribute("data-kt-select2-user");
      var template = "";

      template +=
        '<img src="' +
        imgUrl +
        '" class="rounded-circle h-30px me-2" alt="image"/>';
      template += item.text;

      span.innerHTML = template;

      return $(span);
    };

    // Init Select2 --- more info: https://select2.org/
    $(selectId).select2({
      minimumResultsForSearch: Infinity,
      templateSelection: optionFormat,
      templateResult: optionFormat,
    });
    $(selectId).val(null).trigger("change");


    //for status
    let selectStatus  = "#selection-status";
    $(selectStatus).select2({
      minimumResultsForSearch: Infinity,
    });
    $(selectStatus).val(null).trigger("change");

    // Event listener code
    $(selectStatus).on("select2:select", function () {
      var style = this.value == 1 ? 'block' : 'none';
      document.getElementById('myDiv').style.display = style;
    });

    $(selectStatus).on("select2:unselect", function () {
      document.getElementById('myDiv').style.display = 'none';
    });
  

    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(
      form,
        {
            fields: {
                'status': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Pilih Status'
                        }
                    }
                },
                'notes': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Isi Catatan'
                        }
                    }
                },
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

    // Handle submit button
    submitAction.addEventListener('click', e => {
        e.preventDefault();

        // Validate form before submit
        if (validator) {
          validator.validate().then(function (status) {

                if (status == 'Valid') {
                  submitAction.setAttribute('data-kt-indicator', 'on');

                    // Disable submit button whilst loading
                    submitAction.disabled = true;

                    var serializedArray = $(form).serializeArray();
                    var formData = {};

                    serializedArray.forEach(item => {
                        formData[item.name] = item.value;
                    });

                    api.post('letter/in/view', JSON.stringify(formData))
                    .then(response => {
                        // Hide loading indication
                        submitAction.removeAttribute('data-kt-indicator');

                        toastr.success(response.message);
                        setTimeout(function () {
                            location.href = "/letter/in/list";
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
                                submitAction.disabled = false;
                            }
                        });
                    });

                } else {
                    Swal.fire({
                        html: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila ambil perhatian untuk mengisi semua maklumat yang diperlukan.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, maklum!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                        allowOutsideClick: false
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            // Enable submit button after loading
                            submitAction.disabled = false;
                        }
                    });
                }
            });
        }
    })
  }

  // Public methods
  return {
    init: function () {
      submitActionLetterIn();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  LetterView.init();
});
