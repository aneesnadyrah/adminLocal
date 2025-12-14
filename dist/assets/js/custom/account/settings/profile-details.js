"use strict";

// Class definition
var KTAccountSettingsProfileDetails = function () {
    // Private variables
    var form;
    var submitButton;
    var validation;
	var postcode;

    // Private functions
    var initValidation = function () {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validation = FormValidation.formValidation(
            form,
            {
                fields: {
                    first_name: {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nama pertama anda'
                            }
                        }
                    },
                    last_name: {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nama akhir anda'
                            }
                        }
                    },
                    phone_no: {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi no telefon anda'
                            }
                        }
                    },
                    first_address: {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi alamat 1 anda'
                            }
                        }
                    },
                    second_address: {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi alamat 2 anda'
                            }
                        }
                    },
                    postcode: {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi poskod alamat anda'
                            },
							regexp: {
								regexp: /^\d{5}$/,
								message: "Sila isi nombor poskod yang sah",
							},
                        }
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

    }

    var handleForm = function () {
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            validation.validate().then(function (status) {
                if (status == 'Valid') {
                    Swal.fire({
                        text: "Adakah anda pasti dengan maklumat yang telah diisi?",
                        icon: "question",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-light",
                        },
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            var serializedArray = $(form).serializeArray();
                            var formData = {};

                            serializedArray.forEach(item => {
                                formData[item.name] = item.value;
                            });

                            api.put('account/profile', JSON.stringify(formData))
                            .then(response => {
                                submitButton.setAttribute("data-kt-indicator", "on");
                                submitButton.disabled = true;

                                toastr.success(response.message);
                                setTimeout(function () {
                                    location.href = "/account/profile";
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
                            // submitButton.removeAttribute("data-kt-indicator");
                            submitButton.disabled = false;
                        }
                    });

                } else {
                    swal.fire({
                        text: "Maaf, nampaknya terdapat beberapa masalah dikesan. Sila cuba lagi.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, baik!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-light-primary"
                        }
                    });
                }
            });
        });
    }

	const initPostcode = () => {
        postcode.addEventListener('keyup', function() {
            const postcode = this.value;
            if (postcode.length === 5) {
                axios.get(apps + '/api/postcode/' + postcode).then(response => {
                    const r = response.data;
                    if (r.message === 'Success') {
                        document.getElementById('city').value = r.city;
                        document.getElementById('state').value = r.state;
                    }
                }).catch(error => {
                    // handle error
                    console.log(error);
                });
            }
        });
    };

    // Public methods
    return {
        init: function () {
            form = document.getElementById('kt_account_profile_details_form');
            
            if (!form) {
                return;
            }

			postcode = document.getElementById('postcode');
            submitButton = form.querySelector('#kt_account_profile_details_submit');

            initValidation();
            initPostcode();
            handleForm();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAccountSettingsProfileDetails.init();
});
