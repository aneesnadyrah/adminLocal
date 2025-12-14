"use strict";

// Class definition
var FeedbackView = (function () {

    $("#kt_daterangepicker_1").daterangepicker();

    var actionFeedbackWy = () => {
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
    var submitWyFeedbackGenerate = () => {
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
                            location.href = "/projects/wayleave/feedback/"+response.sysid;
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

        // Get a reference to the element with the ID "printing"
        var printing = document.getElementById("print_feedback");

        // Add a click event listener to the button
        printing.addEventListener("click", function (e) {
            e.preventDefault();

            // Disable button to avoid multiple click
            printing.disabled = true;

            printing.setAttribute("data-kt-indicator", "on");

            // Code to execute when the button is clicked
            // In this example, we'll print a message to the console
            console.log("Button with ID 'printing' was clicked!");

            // Create a style element for custom print styles
            const printStyle = document.createElement('style');

            // Add your custom print styles inside this style element
            printStyle.innerHTML = `
                @media print {
                    .container {
                        width: 100%;
                    }
                    /*remove header and footer of window and sizing of A4*/
                    @page {
                        size: A4;
                        // margin-top: 2cm;
                        margin-left: 2cm;
                        margin-right: 2cm;
                        margin-bottom: 0.2cm;
                    }
                    // .print_content {
                    //     font-size: 14pt;
                    //     color: white;
                    // }
                    .page-break {
                        page-break-before: always !important;
                    }
                    .section-logo {
                        margin-top: 1cm;
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-end;
                        align-items: flex-end;
                    }
                    .section-header {
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-end;
                        align-items: flex-end;
                    }
                    .section-header-2 {
                        display: flex;
                        flex-direction: column;
                        justify-content: flex-start;
                        align-items: flex-start;
                    }
                    .section-address {
                        font-size: 14pt;
                        color: black;
                    }
                    footer {
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        text-align: center;
                        font-size: 12px;
                    }
                }
            `;

            // Append the style element to the document's head
            document.head.appendChild(printStyle);

            // Create a new div to hold the print-only content
            var printSection = document.createElement("div");
            var printContent = document.getElementById("print_content").innerHTML;
            printSection.innerHTML = printContent;

            // Append the print-only section to the document body
            document.body.appendChild(printSection);

            // Call the print() method to print the current page
            window.print();

            // Remove the custom style,the print-only section element after printing
            document.head.removeChild(printStyle);
            document.body.removeChild(printSection);
            printing.removeAttribute("data-kt-indicator");
            // Enable button
            printing.disabled = false;

        });

        

        // Create a <style> element
        // var printStyle = document.createElement('style');
        // printStyle.setAttribute('media', 'print');

        // // Define the print styles
        // printStyle.textContent = `
        //     @media print {
        //         @page {
        //             /* Print-specific styles for the page */
        //             size: A4;
        //             margin-top: 0.5cm;
        //             margin-left: 2cm;
        //             margin-right: 2cm;
        //             margin-bottom: 0.2cm;
        //         }
        //         .page-break {
        //             page-break-inside: avoid;
        //         }
        //         .print-end {
        //             display: flex;
        //             flex-direction: column;
        //             justify-content: flex-end;
        //             align-items: flex-end;
        //         }
        //         .print-start {
        //             display: flex;
        //             flex-direction: column;
        //             justify-content: flex-start;
        //             align-items: flex-start;
        //         }
        //         footer {
        //             position: fixed;
        //             bottom: 0;
        //             left: 0;
        //             width: 100%;
        //             text-align: center;
        //             font-size: 12px;
        //         }
        //         .kutt_print {
        //             size: A4;
        //             margin-top: 3cm;
        //             margin-bottom: 2cm;
        //         }
        //     }
        // `;

        // // Append the <style> element to the document head
        // document.head.appendChild(printStyle);

        // // Handle print button click
        // var printButton = document.getElementById('print_feedback');
        // printButton.addEventListener('click', function() {
        //     // Create a new div to hold the print-only content
        //     var printSection = document.createElement("div");
        //     // Copy the content of the specified element to the print-only section
        //     var printContent = document.getElementById("print_content").innerHTML;
        //     printSection.innerHTML = printContent;

        //     // Append the print-only section to the document body
        //     document.body.appendChild(printSection);

        //     // Trigger the browser's print functionality
        //     window.print();

        //     // Remove the print-only section from the document body
        //     document.body.removeChild(printSection);
        // });

    };

    // let item = document.querySelector('input[name="item"]').value;

    // if(item == "generate-wyFeedback-edit") {
        let item_42_edit = document.querySelector('input[name="item_42_edit"]').value;

        if(item_42_edit === "wyFeedback-edit-ucidos" || item_42_edit === "wyFeedback-edit-kudr") {
            let selectId = "#selection-staff";
            let selectId2 = "#selection-staff2";
            let selectId3 = "#selection-approval";

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
                    var contact = $(this).find(':selected').data('contact');
                    $(staff_contact_2).val(contact);
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
            let submitButton = document.getElementById('wyFeedback-edit-submit');
            let validations = [];

            // provider
            validations.push(FormValidation.formValidation(form, {
                fields: {
                    "addr_provider_1": {
                        validators: {
                            notEmpty: {
                                message: "Sila Isi Alamat",
                            },
                        },
                    },
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
                    "addr_client_1": {
                        validators: {
                            notEmpty: {
                                message: "Sila Isi Alamat",
                            },
                        },
                    },
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
                            location.href = `/projects/wayleave/wyFeedback/${response.sysid}/${response.id}`;
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


        } else if(item_42_edit === "wyFeedback-edit-kiter" || item_42_edit === "wyFeedback-edit-kuk") {
            let selectId = "#selection-staff";
            let selectId3 = "#selection-approval";

            console.log(item_42_edit);

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
            let submitButton = document.getElementById('wyFeedback-edit-submit');
            let validations = [];

            // provider
            validations.push(FormValidation.formValidation(form, {
                fields: {
                    "addr_provider_1": {
                        validators: {
                            notEmpty: {
                                message: "Sila Isi Alamat",
                            },
                        },
                    },
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
                    "addr_client_1": {
                        validators: {
                            notEmpty: {
                                message: "Sila Isi Alamat",
                            },
                        },
                    },
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
                            location.href = `/projects/wayleave/wyFeedback/${response.sysid}/${response.id}`;
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

    // }

    // Public methods
    return {
        init: function () {
            actionFeedbackWy();
            submitWyFeedbackGenerate();
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    FeedbackView.init();
});