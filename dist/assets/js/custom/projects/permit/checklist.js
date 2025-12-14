"use strict";

// Class definition
var PermitChecklist = (function () {

    // Get the URL path, Split the URL path into segments
    const systemId = window.location.pathname.split('/').pop();

    // Submit form
    var submitPermitChecklist = () => {
        // Get elements
        let form = document.getElementById('form_action_permit_checklist');
        let submitButton = document.getElementById('submit_action_permit_checklist');
        let validator;

        validator = FormValidation.formValidation(form, {
            fields: {
                "notes": {
                    validators: {
                        notEmpty: {
                            message: "Sila Isi Catatan",
                        }
                    }
                }
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

        // Handle submit button
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            // Validate form
            validator.validate().then(function (status) {
                // console.log(status);
                if (status == "Valid") {
                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;

                    Swal.fire({
                        text: "Adakah anda pasti dokumen ini lengkap ?",
                        icon: "question",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        customClass: {
                            confirmButton: "btn btn-primary",
                            cancelButton: "btn btn-light",
                        },
                        allowOutsideClick: true,
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            var serializedArray = $(form).serializeArray();
                            var formData = {};

                            serializedArray.forEach(item => {
                                formData[item.name] = item.value;
                            });

                            api.post('projects/permits/checklist/' + systemId, JSON.stringify(formData))
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
                            submitButton.removeAttribute("data-kt-indicator");
                            submitButton.disabled = false;
                        }
                    });

                } else {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");
                    // Disable button to avoid multiple click
                    submitButton.disabled = false;
                }
            });

        })
    };

    // Modal check
    var initializeModalValidation = () => {
        let modalIdName = "#modal_";
        let counter = 1;
        let modalIds = modalIdName + counter;
        let validator;

        while ($(modalIds).length > 0) {
            let modalEl = $(modalIds);
            let modal = new bootstrap.Modal(modalEl);
            let modalOpenBtn = $('[data-target="' + modalIds + '"]');
            let formModal = "form_modal_" + counter;
            let submitModal = "submit_modal_" + counter;
            let checkModal = "check_modal_" + counter;
            let noteModal = "note_modal_" + counter;

            modalOpenBtn.click(function () {
                // show modal
                modal.show();

                // Initialize FormValidation for the form with ID "form_modal_1" (or another dynamic form ID)
                let validator = FormValidation.formValidation(document.getElementById(formModal), {
                    fields: {
                        [checkModal]: {
                            validators: {
                                notEmpty: {
                                    message: "Sila tanda mana yang berkenaan",
                                },
                            },
                        },
                        [noteModal]: {
                            validators: {
                                notEmpty: {
                                    message: "Sila isi catatan",
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

                modalEl.find(".submit-button").click(function () {
                    // Disable the button to prevent multiple submissions
                    //   $(this).prop("disabled", true);
                    modalOpenBtn.prop("disabled", true);
                    modalOpenBtn.removeClass("btn-primary");
                    modalOpenBtn.addClass("btn-secondary");

                    // Get form data within the modal
                    let formData = {
                        // check_modal: modalEl.find(".check-modal").val(),
                        // check_modal: modalEl.find('input[name="check_modal"]:checked').length > 0,
                        check_modal: modalEl.find(".check-modal:checked").length > 0,
                        note_modal: modalEl.find(".note-modal").val(),
                        phpValue: modalEl.find(".phpValue").data("modalId"),
                    };

                    // let formData = {
                    //     note_modal: form.find(".note-modal").val(),
                    //     check_modal: form.find('input[name="check_modal"]:checked').length > 0
                    // };
                
                    // Validate form
                    validator.validate().then(function (status) {
                        // console.log(status);
                        if (status == "Valid") {

                            
                            // api.post('projects/permits/checklist/'+systemId, JSON.stringify(formData))
                            // .then(response => {
                            //     submitModal.setAttribute("data-kt-indicator", "on");
                            //     submitModal.disabled = true;

                            //     toastr.success(response.message);
                            //     setTimeout(function () {
                            //         // location.href = "/tasks/new";
                            //     }, 2500);

                            // })
                            // .catch(error => {
                            //     Swal.fire({
                            //         text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                            //         icon: "error",
                            //         buttonsStyling: false,
                            //         confirmButtonText: "Ok, faham",
                            //         customClass: {
                            //             confirmButton: "btn btn-primary",
                            //         },
                            //         allowOutsideClick: false,
                            //     }).then(function (result) {
                            //         if (result.isConfirmed) {
                            //             // Enable submit button after loading
                            //             submitModal.disabled = false;
                            //         }
                            //     });
                            // });


                        }
                    });


                    if (formData.note_modal.trim() === "" || !formData.check_modal) {
                        // Display an error message or handle the validation as needed for each form
                        console.log("Form input is empty or radio option is not selected. Please complete the form.");
                    } else {
                        // Perform AJAX POST request to submit the form data
                        console.log('Hello from modal: ' + modalEl.attr("id") + ' ' + formData.note_modal);
                
                        // Close the modal
                        modal.hide();
                    }
                    
                });

            });


            modalEl.find('[data-dismiss="modal"]').click(function () {
                modal.hide();
            });

            // add counter and assign new modal id
            counter++;
            modalIds = modalIdName + counter;
        }

    };

    // Public methods
    return {
        init: function () {
            submitPermitChecklist();
            initializeModalValidation();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    PermitChecklist.init();
});