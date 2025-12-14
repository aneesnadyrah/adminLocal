"use strict";

// Class definition
var FeedbackApproval = (function () {

    // Submit form
    var submitWyFeedbackApproval = () => {
        // Get elements
        let form = document.getElementById('form_wayFeedback_approval');
        let submitButton = document.getElementById('submit_wayFeedback_approval');

        // Handle submit button
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");
        
            // Disable button to avoid multiple click
            submitButton.disabled = true;

            Swal.fire({
                text: "Adakah anda pasti untuk hantar pengesahan ?",
                icon: "question",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-light",
                },
                allowOutsideClick: false,
            }).then(function (result) {
                if (result.isConfirmed) {
                    var serializedArray = $(form).serializeArray();
                    var formData = {};

                    serializedArray.forEach(item => {
                        formData[item.name] = item.value;
                    });

                    api.put('projects/wayleaves/tasks', JSON.stringify(formData))
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
        })
    };
    
    // Public methods
    return {
        init: function () {
            submitWyFeedbackApproval();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    FeedbackApproval.init();
});