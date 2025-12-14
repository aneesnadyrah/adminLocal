"use strict";

// Class definition
var amendSurveyReportAsb = (function () {
    var handleSubmit = () => {
        // Define variables
        let validator;

        // Get elements
        let form = document.getElementById('form_amend_report_asb');
        let submitButton = document.getElementById('amend_report_asb_submit');

        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'notes': {
                        validators: {
                            notEmpty: {
                                message: 'Sila Isi Catatan Pindaan Laporan'
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
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        // Show loading indication  
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable button to avoid multiple click    
                        submitButton.disabled = true;

                        // Simulate form submission 
                        var serializedArray = $(form).serializeArray();
                        var formData = {};

                        serializedArray.forEach(item => {
                            formData[item.name] = item.value;
                        });

                        api.post('surveys/tasking', JSON.stringify(formData))
                        .then(response => {
                            // Hide loading indication
                            submitButton.removeAttribute('data-kt-indicator');

                            Swal.fire({
                                text: "Pindaan Laporan Kerja Lapangan telah berjaya disimpan.",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, maklum.",
                                customClass: {
                                  confirmButton: "btn btn-primary",
                                },
                                allowOutsideClick: false,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    // disable submit button after loading
                                    submitButton.disabled = true;
            
                                    // Redirect to new.php
                                    setTimeout(function () {
                                        location.href = `mapping/general/tasks/new`;
                                    }, 1500);
                                }
                            });

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
                        Swal.fire({
                            html: "Maaf, sila ambil perhatian untuk mengisi maklumat yang diperlukan.",
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
                                submitButton.disabled = false;
                            }
                        });
                    }
                    
                });
            }
        });
    }

    return {
        init: function () {
            handleSubmit();
        },
    };

})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    amendSurveyReportAsb.init();
}); 