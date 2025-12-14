"use strict";

// Class definition
var cancelApplication = (function () {
    // Submit form
    var handleSubmit = () => {
        // Define variables
        let validator;

        // Get elements
        let form = document.getElementById('form_cancel_application');
        let submitAction = document.getElementById('submit_cancel_application');

        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'notes': {
                        validators: {
                            notEmpty: {
                                message: 'Sila Isi Catatan Pembatalan Permohonan'
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

                        api.post('projects/archieve', JSON.stringify(formData))
                        .then(response => {
                            // Hide loading indication
                            submitAction.removeAttribute('data-kt-indicator');

                            toastr.success(response.message);
                            setTimeout(function () {
                                window.location.reload();
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
                            confirmButtonText: "Ok, Faham!",
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

    return {
        init: function () {
            handleSubmit();
        },
    };

})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    cancelApplication.init();
});