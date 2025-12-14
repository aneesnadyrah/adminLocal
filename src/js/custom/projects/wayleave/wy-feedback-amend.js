"use strict";

// Class definition
var FeedbackAmend = (function () {

    var actionFeedbackWyAmend = () => {
        var element = document.querySelector("#kt_stepper_wyFeedback");

        // Initialize Stepper
        var stepper = new KTStepper(element);

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
            stepper.goNext(); // go next step
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            stepper.goPrevious(); // go previous step
        });
    }

    // Submit form generate
    var submitWyFeedbackGenerateAmend = () => {
        // Get elements
        let form = document.getElementById('form_wayFeedback_generate');
        let submitButton = document.getElementById('submit_wayFeedback_generate');

        // Handle submit button
        submitButton.addEventListener('click', e => {
            e.preventDefault();

            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            // Display confirmation popup
            Swal.fire({
                text: "Adakah anda pasti untuk hantar pengesahan?",
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

        // Create a <style> element
        var printStyle2 = document.createElement('style');
        printStyle2.setAttribute('media', 'print');

        // Define the print styles
        printStyle2.textContent = `
            @media print {
                @page {
                    /* Print-specific styles for the page */
                    size: A4;
                    margin-top: 0.5cm;
                    margin-left: 2cm;
                    margin-right: 2cm;
                    margin-bottom: 0.2cm;
                }
                .page-break {
                    page-break-inside: avoid;
                    margin-bottom: 2cm;
                }
                .print-end {
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-end;
                    align-items: flex-end;
                }
                .print-start {
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: flex-start;
                }
                footer {
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    text-align: center;
                    font-size: 12px;
                }
                .kutt_print {
                    size: A4;
                    margin-top: 3cm;
                    margin-bottom: 2cm;
                }
            }
        `;

        // Append the <style> element to the document head
        document.head.appendChild(printStyle2);

        // Handle print button click
        var printButton2 = document.getElementById('print_feedback');
        printButton2.addEventListener('click', function() {
            // Create a new div to hold the print-only content
            var printSection = document.createElement("div");
            // Copy the content of the specified element to the print-only section
            var printContent = document.getElementById("print_content").innerHTML;
            printSection.innerHTML = printContent;

            // Append the print-only section to the document body
            document.body.appendChild(printSection);

            // Trigger the browser's print functionality
            window.print();

            // Remove the print-only section from the document body
            document.body.removeChild(printSection);
        });
    };

    let item_42_edit = document.querySelector('input[name="item_42_edit"]').value;

    if(item_42_edit === "wyFeedback-edit-ucidos" || item_42_edit === "wyFeedback-edit-kudr") {
        let selectId = "#selection-staff";
        let selectId2 = "#selection-staff2";
        let selectId3 = "#selection-approval";
        let submitButton = document.querySelector("#wyFeedback-edit-submit");

        // Format options
        let optionFormat = function (item) {
            if (!item.id) {
            return item.text;
            }

            let span = document.createElement("span");
            let imgUrl = item.element.getAttribute("data-staff");
            let template = "";

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
        $(selectId2).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat,
        });
        $(selectId3).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat,
        });

        $(document).ready(function () {
            $('#selection-staff').on('change', function() {
                var contact = $(this).find(':selected').data('contact');
                $(staff_contact).val(contact);
            });
            $('#selection-staff2').on('change', function() {
                var contact2 = $(this).find(':selected').data('contact');
                $(staff_contact_2).val(contact2);
            });
            $('#selection-approval').on('change', function() {
                var position = $(this).find(':selected').data('position');
                $(approval_position).val(position);
            });
        });

        //stepper
        var stepperModal2 = "#kt_stepper_mbkil_edit";
        // Stepper lement
        var element2 = document.querySelector(stepperModal2);

        // Initialize Stepper
        var stepper = new KTStepper(element2);

        // Handle navigation click
        stepper.on("kt.stepper.click", function (stepper) {
            stepper.goTo(stepper.getClickedStepIndex()); // go to clicked step
        });

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
            console.log(stepper);
            // Validate form before change stepper step
            var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step
            
            // console.log(validator);
            if (validator) {
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    stepper.goNext();
                } else {
                    Swal.fire({
                    html: "Maaf, sila isi butiran bagi surat ini.",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, maklum!",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                    }).then(function () {
                    });
                }
            });
            } else {
                stepper.goNext();
            }
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            // console.log('stepper.previous');
            stepper.goPrevious();
        });

        let form = document.querySelector("#form-mbkil-edit");
        let validations = [];

        // provider
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "up_provider": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Untuk Perhatian",
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
        }));
            
        // client
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "up_pemohon": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Untuk Perhatian",
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
        }));

        // test
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "jumlah": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Jumlah",
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
        }));

        // surat
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "client_ref_no": { 
                validators: {
                    notEmpty: {
                        message: "Sila Isi No Rujukan Tuan",
                    },
                },
            },
            "staff_name": {
                validators: {
                    notEmpty: {
                        message: "Sila Pilih Pegawai 1 Untuk Dihubungi",
                    },
                },
            },
            "staff_name_2": { 
                validators: {
                    notEmpty: {
                        message: "Sila Pilih Pegawai 2 Untuk Dihubungi",
                    },
                },
            },
            "approval_by": { 
                validators: {
                    notEmpty: {
                        message: "Sila Pilih Pegawai Melulus",
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
        }));

        // Handle form submit
        submitButton.addEventListener("click", function (e) {
            // Prevent button default action
            e.preventDefault();

            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            let validator = validations[3];
            // Validate form
            validator.validate().then(function (status) {
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

                api.put('projects/wayleaves/tasks', JSON.stringify(formData))
                .then(response => {
                    submitButton.setAttribute("data-kt-indicator", "on");
                    submitButton.disabled = true;

                    toastr.success(response.message);
                    setTimeout(function () {
                        location.href = `/projects/wayleave/wyFbAmend/${response.sysid}/4`;
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
                html: "Maaf, sila isi butiran bagi surat ini.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                allowOutsideClick: false,
                });

                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");
                // Disable button to avoid multiple click
                submitButton.disabled = false;
            }
            });
        });


    } else if(item_42_edit === "wyFeedback-edit-kiter") {
        let selectId = "#selection-staff";
        let selectId3 = "#selection-approval";
        let submitButton = document.querySelector("#wyFeedback-edit-submit");

        // Format options
        let optionFormat = function (item) {
            if (!item.id) {
            return item.text;
            }
            let span = document.createElement("span");
            let imgUrl = item.element.getAttribute("data-staff");
            let template = "";

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
        $(selectId3).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat,
        });

        $(document).ready(function () {
            $('#selection-staff').on('change', function() {
                var contact = $(this).find(':selected').data('contact');
                $(staff_contact).val(contact);
            });
            $('#selection-approval').on('change', function() {
                var position = $(this).find(':selected').data('position');
                $(approval_position).val(position);
            });
        });

        let invDate = "#inv-date";
        let invoiceDate = $(invDate).flatpickr({
            locale: "ms",
            altInput: true,
            defaultDate: "today",
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            // static: true,
        });

        //stepper
        var stepperModal2 = "#kt_stepper_mbkil_edit";
        // Stepper lement
        var element2 = document.querySelector(stepperModal2);

        // Initialize Stepper
        var stepper = new KTStepper(element2);

        // Handle navigation click
        stepper.on("kt.stepper.click", function (stepper) {
            stepper.goTo(stepper.getClickedStepIndex()); // go to clicked step
        });

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
            console.log(stepper);
            // Validate form before change stepper step
            var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step
            
            // console.log(validator);
            if (validator) {
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    stepper.goNext();
                } else {
                    Swal.fire({
                    html: "Maaf, sila isi butiran bagi surat ini.",
                    icon: "warning",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, maklum!",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                    }).then(function () {
                    });
                }
            });
            } else {
                stepper.goNext();
            }
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            // console.log('stepper.previous');
            stepper.goPrevious();
        });

        let form = document.querySelector("#form-mbkil-edit");
        let validations = [];

        // provider
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "up_provider": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Untuk Perhatian",
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
        }));
            
        // client
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "up_pemohon": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Untuk Perhatian",
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
        }));

        // test
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "jumlah": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Jumlah",
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
        }));

        // invoice
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "inv_no": {
                validators: {
                notEmpty: {
                    message: "Sila Isi No Invois",
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
        }));

        // surat
        validations.push(FormValidation.formValidation(form, {
            fields: {
            "date_hijri": {
                validators: {
                notEmpty: {
                    message: "Sila Isi Tarikh Dalam Hijrah",
                },
                },
            },
            "staff_name": {
                validators: {
                notEmpty: {
                    message: "Sila Pilih Pegawai Untuk Dihubungi",
                },
                },
            },
            "approval_by": { 
                validators: {
                    notEmpty: {
                        message: "Sila Pilih Pegawai Melulus",
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
        }));

        // Handle form submit
        submitButton.addEventListener("click", function (e) {
            // Prevent button default action
            e.preventDefault();

            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            let validator = validations[3];
            // Validate form
            validator.validate().then(function (status) {
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

                api.put('projects/wayleaves/tasks', JSON.stringify(formData))
                .then(response => {
                    submitButton.setAttribute("data-kt-indicator", "on");
                    submitButton.disabled = true;

                    toastr.success(response.message);
                    setTimeout(function () {
                        location.href = `/projects/wayleave/wyFbAmend/${response.sysid}/4`;
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
                html: "Maaf, sila isi butiran bagi surat ini.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                allowOutsideClick: false,
                });

                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");
                // Disable button to avoid multiple click
                submitButton.disabled = false;
            }
            });
        });


    }

    // Public methods
    return {
        init: function () {
            actionFeedbackWyAmend();
            submitWyFeedbackGenerateAmend();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    FeedbackAmend.init();
});