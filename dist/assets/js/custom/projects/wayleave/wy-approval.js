
"use strict";

// Class definition
var WayleaveApprove = (function () {
    // Shared variables
    var table;
    var datatable;
    var flatpickr;
    var flatpickrSingleDate;
    var minDate, maxDate;

    // Private functions
    var initWayleaveApprove = function () {
        // const urlParams = new URLSearchParams(window.location.search);
        // const sysid = urlParams.get('sid');
        // Get the URL path, Split the URL path into segments
        const sysid = window.location.pathname.split('/').pop();

        datatable = $(table).DataTable({
            ajax: apps + "/api/projects/wayleaves/list/"+sysid,
            columns: [
            { data: "RefNo" },
            { data: "Authority" },
            { data: "Length" },
            { data: "WyStatus" },
            { data: "LetterDate" },
            { data: null },
            ],
            info: false,
            language: {
            loadingRecords: "Sila Tunggu...",
            zeroRecords: "Tiada Rekod Dijumpai",
            },
            pageLength: 10,
            columnDefs: [
            {
                target: 1,
                render: function (index, type, key, meta) {
                var $provider_img = key["Logo"];

                if ($provider_img) {
                    // For Avatar image
                    var $output =
                    `<img src="assets/media/authorities/` +
                    key["Logo"] +
                    `.png" alt="` +
                    key["Authority"] +
                    `" />`;
                } else {
                    // For Avatar badge
                    var stateNum = Math.floor(Math.random() * 6);
                    var states = [
                    "success",
                    "danger",
                    "warning",
                    "info",
                    "dark",
                    "primary",
                    "secondary",
                    ];
                    var $state = states[stateNum],
                    $named,
                    $name = key["Authority"];
                    if ($name === null) {
                    $named = "Tidak Diketahui";
                    } else {
                    $named = $name;
                    }
                    var $initials = $named.match(/\b\w/g) || [];
                    $initials = (
                    ($initials.shift() || "") + ($initials.pop() || "")
                    ).toUpperCase();
                    $output =
                    `<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                        <div class="symbol-label fs-3 bg-light-` +
                    $state +
                    ` text-` +
                    $state +
                    `">` +
                    $initials +
                    `</div>
                    </div>`;
                }
                return (
                    `<div class="d-flex align-items-center">
                    <!--begin:: Avatar -->
                    <div class="symbol symbol-50px me-2">` +
                    $output +
                    `</div>
                    <!--end::Avatar-->
                    <!--begin::Title-->
                    <a href="javascript:void()" class="text-gray-700 text-hover-primary">` +
                    key["Authority"] +
                    `</a>
                    <!--end::Title-->
                    </div>`
                );
                },
            },
            {
                target: 2,
                render: function (index, type, key, meta) {
                return (
                    parseInt(key["Length"]).toLocaleString("ms-MY", {
                    useGrouping: true,
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                    }) + " m"
                );
                },
            },
            {
                target: 3,
                render: function (index, type, key, meta) {
                    let render;
                    if (key["WyStatus"] == 1) {
                        render = 
                        `<!--begin::Badges-->
                        <div class="badge badge-light-primary">Surat Permohonan Disediakan</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 2) {
                        render = 
                        `<!--begin::Badges-->
                        <div class="badge badge-light-info">Surat Permohonan Dihantar</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 3) {
                        render = 
                        `<!--begin::Badges-->
                        <div class="badge badge-light-success">Surat Kelulusan Izin Lalu Diterima</div>
                        <!--end::Badges-->`
                    }
                    return render;
                },
            },
            {
                orderable: false,
                targets: 5,
                render: function (index, type, key, meta) {
                    let render;
                    if (tenant == 'UCIDOS') {
                        // tenant action for UCIDOS
                        if (key["WyStatus"] == 1) {
                            if (key["PILby"] == null) {
                                render = `
                                <div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-primary envelope-button" data-modal-id="#action-`+key["WyStatus"]+key["ID"]+`">
                                        <i class="fad fa-envelope-circle-check fs-2"></i>
                                    </button>
                                </div>`;
                                
                                $(document).on('click', '.envelope-button', function () {
                                    Swal.fire({
                                        title: 'Pengesahan',
                                        text: 'Pelan Izin Lalu belum dimuat naik, adakah anda pasti untuk teruskan penghantaran permohonan Kelulusan Izin Lalu?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, Teruskan',
                                        cancelButtonText: 'Tidak',
                                        customClass: {
                                            confirmButton: "btn btn-warning",
                                            cancelButton: 'btn btn-secondary'
                                        },
                                        reverseButtons: true
                                    }).then((result) => {
                                        if (result.value) {
                                            // If user clicks "Yes" in the confirmation dialog, show the modal popup
                                            $('#action-' + key["WyStatus"] + key["ID"]).modal('show');
                                        } 
                                        // else if (result.dismiss === Swal.DismissReason.cancel) {
                                        //     Swal.fire(
                                        //         'Cancelled',
                                        //         'Your data is safe :)',
                                        //         'error'
                                        //     );
                                        // }
                                    });
                                });
                            } else{
                                render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-primary"
                                    data-bs-toggle="modal" data-bs-target="#action-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelope-circle-check fs-2"></i>
                                    </button>
                                </div>`;
                            }
                        } else if (key["WyStatus"] == 2) {
                            render =
                            `<div class="text-center">
                                <button type="button" class="btn btn-icon btn-light-info"
                                data-bs-toggle="modal" data-bs-target="#action-` +
                            key["WyStatus"] +
                            key["ID"] +
                            `">
                                <i class="fad fa-envelope-open-text fs-2"></i>
                                </button>
                            </div>`;
                        } else if (key["WyStatus"] == 3 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-success"
                                    data-bs-toggle="modal" data-bs-target="#action-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelopes-bulk fs-2"></i>
                                    </button>
                                </div>`;
                        } else {
                            render =
                            `<div class="text-center">
                                <i class="fad fa-circle-check text-success fs-2 "></i>
                            </div>`;
                        }
                    } else if (tenant == 'KITER') {
                        // tenant action for KITER
                        if (key["WyStatus"] == 1) {
                            if (key["PILby"] == null) {
                                render = `
                                <div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-primary envelope-button" data-modal-id="#action-`+key["WyStatus"]+key["ID"]+`">
                                        <i class="fad fa-envelope-circle-check fs-2"></i>
                                    </button>
                                </div>`;
                                
                                $(document).on('click', '.envelope-button', function () {
                                    Swal.fire({
                                        title: 'Pengesahan',
                                        text: 'Pelan Izin Lalu belum dimuat naik, adakah anda pasti untuk teruskan penghantaran permohonan Kelulusan Izin Lalu?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, Teruskan',
                                        cancelButtonText: 'Tidak',
                                        customClass: {
                                            confirmButton: "btn btn-warning",
                                            cancelButton: 'btn btn-secondary'
                                        },
                                        reverseButtons: true
                                    }).then((result) => {
                                        if (result.value) {
                                            // If user clicks "Yes" in the confirmation dialog, show the modal popup
                                            $('#action-' + key["WyStatus"] + key["ID"]).modal('show');
                                        } 
                                    });
                                });
                            } else{
                                render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-primary"
                                    data-bs-toggle="modal" data-bs-target="#action-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelope-circle-check fs-2"></i>
                                    </button>
                                </div>`;
                            }
                        } else if (key["WyStatus"] == 2) {
                            render =
                            `<div class="text-center">
                                <button type="button" class="btn btn-icon btn-light-info"
                                data-bs-toggle="modal" data-bs-target="#action-` +
                            key["WyStatus"] +
                            key["ID"] +
                            `">
                                <i class="fad fa-envelope-open-text fs-2"></i>
                                </button>
                            </div>`;
                        } else if (key["WyStatus"] == 3 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-success"
                                    data-bs-toggle="modal" data-bs-target="#action-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelopes-bulk fs-2"></i>
                                    </button>
                                </div>`;
                        } else {
                            render =
                            `<div class="text-center">
                                <i class="fad fa-circle-check text-success fs-2 "></i>
                            </div>`;
                        }
                    } else if (tenant == 'KUDRAT') {
                        // tenant for KUDR
                        if (key["WyStatus"] == 1) {
                            if (key["PILby"] == null) {
                                render = `
                                <div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-primary envelope-button" data-modal-id="#action-`+key["WyStatus"]+key["ID"]+`">
                                        <i class="fad fa-envelope-circle-check fs-2"></i>
                                    </button>
                                </div>`;
                                
                                $(document).on('click', '.envelope-button', function () {
                                    Swal.fire({
                                        title: 'Pengesahan',
                                        text: 'Pelan Izin Lalu belum dimuat naik, adakah anda pasti untuk teruskan penghantaran permohonan Kelulusan Izin Lalu?',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, Teruskan',
                                        cancelButtonText: 'Tidak',
                                        customClass: {
                                            confirmButton: "btn btn-warning",
                                            cancelButton: 'btn btn-secondary'
                                        },
                                        reverseButtons: true
                                    }).then((result) => {
                                        if (result.value) {
                                            // If user clicks "Yes" in the confirmation dialog, show the modal popup
                                            $('#action-' + key["WyStatus"] + key["ID"]).modal('show');
                                        } 
                                    });
                                });
                            } else{
                                render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-primary"
                                    data-bs-toggle="modal" data-bs-target="#action-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelope-circle-check fs-2"></i>
                                    </button>
                                </div>`;
                            }
                        } else if (key["WyStatus"] == 2) {
                            render =
                            `<div class="text-center">
                                <button type="button" class="btn btn-icon btn-light-info"
                                data-bs-toggle="modal" data-bs-target="#action-` +
                            key["WyStatus"] +
                            key["ID"] +
                            `">
                                <i class="fad fa-envelope-open-text fs-2"></i>
                                </button>
                            </div>`;
                        } else if (key["WyStatus"] == 3 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-success"
                                    data-bs-toggle="modal" data-bs-target="#action-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelopes-bulk fs-2"></i>
                                    </button>
                                </div>`;
                        } else {
                            render =
                            `<div class="text-center">
                                <i class="fad fa-circle-check text-success fs-2 "></i>
                            </div>`;
                        }
                    } else {

                    }

                    return render;
                },
            },
            ],
            order: [[4, 'desc']], // sort by the 6th column in ascending order
        });

        // Initialize Dropzone for each modal
        datatable.on("click", "button", function () {
            let modalId;

            // Check if the button has data-modal-id attribute
            if ($(this).data("modal-id")) {
                modalId = $(this).data("modal-id");
            } // Check if the button has data-bs-target attribute
            else if ($(this).data("bs-target")) {
                modalId = $(this).data("bs-target");
            } 

            let submitId = "#submit-" + modalId.slice(8);
            let formId = "#form-" + modalId.slice(8);
            let sId = "#sid-" + modalId.slice(8);
            let submitButton = document.querySelector(submitId);
            let fId = "#fid-" + modalId.slice(8);
            let authId = "#authorityid-" + modalId.slice(8);

            let statusId = document.querySelector('input[name="statusId"]').value;

            // console.log(modalId); 
            // console.log(modalId.slice(8)); 33070

            if (formId.slice(0, 7) === "#form-1") {
                $(modalId).on("shown.bs.modal", function () {
                    let form = document.querySelector(formId);
                    let validator;

                    validator = FormValidation.formValidation(form, {
                        fields: {
                            "dt-send-wayleave": {
                                validators: {
                                    notEmpty: {
                                        message: "Tarikh Hantar diperlukan",
                                    }
                                }
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
        
                    // Handle form submit
                    submitButton.addEventListener("click", function (e) {
                        // Prevent button default action
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

                                var serializedArray = $(form).serializeArray();
                                var formData = {};

                                serializedArray.forEach(item => {
                                    formData[item.name] = item.value;
                                });

                                api.put('projects/wayleaves/tasks', JSON.stringify(formData))
                                .then(response => {
                                    submitButton.setAttribute("data-kt-indicator", "on");
                                    submitButton.disabled = true;

                                    dropzone.processQueue();

                                    toastr.success(response.message);
                                    setTimeout(function () {
                                        location.href = "/projects/wayleave/approval/"+sysid;
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
                                // Hide loading indication
                                submitButton.removeAttribute("data-kt-indicator");
                                // Disable button to avoid multiple click
                                submitButton.disabled = false;
                            }
                        });
                    });
                });

                // let payment_method = document.querySelector('input[name="payment_method"]').value;
        
                // Initialize Dropzone for the modal
                let dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
                    //   url: "https://" + hostApps + "/api/projects/uploads",
                  url: `${apps}/api/tasks/${statusId}/upload`,
                  paramName: "file",
                  maxFiles: 5,
                  maxFilesize: 1024,
                  acceptedFiles: "application/pdf",
                  autoProcessQueue: false,
                  addRemoveLinks: true,
                  sending: function (file, xhr, formData) {
                    formData.append("systemId", document.querySelector(sId).value);
                    formData.append("folder", document.querySelector(fId).value);
                    formData.append("authorityId", document.querySelector(authId).value);
                  },
                  accept: function (file, done) {
                    done();
                  },
                });
        
                dropzone.on("success", function (f, r) {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;
        
                    toastr.success(response.message);
                    setTimeout(function () {
                        location.href = "/projects/wayleave/approval/"+sysid;
                    }, 2500);
        
                });
        
                dropzone.on("addedfile", function () {
                  submitButton.classList.remove("d-none");
                });
        
                // Remove files when the modal is closed
                $(modalId).on("hidden.bs.modal", function () {
                  dropzone.removeAllFiles();
                  dropzone.destroy();
                  submitButton.classList.add("d-none");
                });
        
                // Add Dropzone events to the modal
                dropzone.on("error", function (file, errorMessage) {
                  this.removeFile(file);
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    text:
                      "Maaf, nampaknya terdapat beberapa ralat dikesan semasa muat naik fail. " +
                      errorMessage,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, maklum!",
                    customClass: {
                      confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                  });
                });
            } else if (formId.slice(0, 7) === "#form-2") {
                $(modalId).on("shown.bs.modal", function () {
                    let form = document.querySelector(formId);
                    let validator;

                    validator = FormValidation.formValidation(form, {
                        fields: {
                            "dt-approval-wayleave": {
                                validators: {
                                    notEmpty: {
                                        message: "Tarikh Surat Disahkan diperlukan",
                                    }
                                }
                            },
                            "dt-receive-wayleave": {
                                validators: {
                                    notEmpty: {
                                        message: "Tarikh Terima Surat diperlukan",
                                    }
                                }
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
        
                    // Handle form submit
                    submitButton.addEventListener("click", function (e) {
                        // Prevent button default action
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

                                // Trigger Dropzone upload manually
                                dropzone.processQueue();

                                // Listen to Dropzone's success event
                                dropzone.on("success", function (f, r) {
                                    var serializedArray = $(form).serializeArray();
                                    var formData = {};

                                    serializedArray.forEach(item => {
                                        formData[item.name] = item.value;
                                    });

                                    api.put('projects/wayleaves/tasks', JSON.stringify(formData))
                                    .then(response => {
                                        submitButton.setAttribute("data-kt-indicator", "on");
                                        submitButton.disabled = true;

                                        // dropzone.processQueue();
                                        toastr.success(response.message);
                                        setTimeout(function () {
                                            location.href = "/projects/wayleave/approval/"+sysid;
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
                        
                                });

                            } else {
                                // Hide loading indication
                                submitButton.removeAttribute("data-kt-indicator");
                                // Disable button to avoid multiple click
                                submitButton.disabled = false;
                            }
                        });
                    });
                });

                let dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
                    url: `${apps}/api/tasks/${statusId}/upload`,
                    paramName: "file",
                    maxFiles: 5,
                    maxFilesize: 1024,
                    acceptedFiles: "application/pdf",
                    autoProcessQueue: false,
                    addRemoveLinks: true,
                    sending: function (file, xhr, formData) {
                        formData.append("systemId", document.querySelector(sId).value);
                        formData.append("folder", document.querySelector(fId).value);
                        formData.append("authorityId", document.querySelector(authId).value);
                    },
                    accept: function (file, done) {
                        done();
                    },
                });

                // dropzone.on("success", function (f, r) {
                //     // Hide loading indication
                //     submitButton.removeAttribute("data-kt-indicator");

                //     // Enable button
                //     submitButton.disabled = false;
        
                //     toastr.success(response.message);
                //     setTimeout(function () {
                //         location.href = "/projects/wayleave/approval/"+sysid;
                //     }, 2500);
        
                // });
        
                dropzone.on("addedfile", function () {
                  submitButton.classList.remove("d-none");
                });
        
                // Remove files when the modal is closed
                $(modalId).on("hidden.bs.modal", function () {
                  dropzone.removeAllFiles();
                  dropzone.destroy();
                  submitButton.classList.add("d-none");
                });
        
                // Add Dropzone events to the modal
                dropzone.on("error", function (file, errorMessage) {
                  this.removeFile(file);
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    text:
                      "Maaf, nampaknya terdapat beberapa ralat dikesan semasa muat naik fail. " +
                      errorMessage,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, maklum!",
                    customClass: {
                      confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                  });
                });
            } 
        });

    };

    // Init flatpickr --- more info :https://flatpickr.js.org/getting-started/
    var initFlatpickr = () => {
        const element = document.querySelector("#table-date-range");
        flatpickr = $(element).flatpickr({
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        mode: "range",
        onChange: function (selectedDates, dateStr, instance) {
            handleFlatpickr(selectedDates, dateStr, instance);
        },
        });
    };

    var initFlatpickrSingleDate = () => {
        var elements = document.querySelectorAll('[data-datepicker-id]');
        elements.forEach(function(element) {
            // var datepickerId = element.getAttribute('data-datepicker-id');
            flatpickrSingleDate = $(element).flatpickr({
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                onChange: function (selectedDates, dateStr, instance) {
                  handleFlatpickrSingleDate(selectedDates, dateStr, instance);
                },
              });
        });
    };

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector(
          '[data-table-filter="search"]'
        );
        filterSearch.addEventListener("keyup", function (e) {
          datatable.search(e.target.value).draw();
        });
    };
    
    // Handle flatpickr --- more info: https://flatpickr.js.org/events/
    var handleFlatpickr = (selectedDates, dateStr, instance) => {
        minDate = selectedDates[0] ? new Date(selectedDates[0]) : null;
        maxDate = selectedDates[1] ? new Date(selectedDates[1]) : null;
    
        // Datatable date filter --- more info: https://datatables.net/extensions/datetime/examples/integration/datatables.html
        // Custom filtering function which will search data in column four between two values
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
          var min = minDate;
          var max = maxDate;
          var dateAdded = new Date(moment(data[4], "YYYY-MM-DD"));
    
          if (
            (min === null && max === null) ||
            (min === null && dateAdded <= max) ||
            (max === null && dateAdded >= min) ||
            (min <= dateAdded && max >= dateAdded)
          ) {
            return true;
          }
          return false;
        });
        datatable.draw();
    };

    // console.log("handleFlatpickrSingleDate called");
    var handleFlatpickrSingleDate = (selectedDates, dateStr, instance) => {
        // Access the selected date using selectedDates[0]
        const selectedDate = selectedDates[0];
    
        // Format the date string for display in the input field
        const formattedDate = instance.formatDate(selectedDate, "d/m/Y");
    
        // Update the input field value with the formatted date string
        instance.altInput.value = formattedDate;
    
        // Do any additional processing or validation here...
    };

    // Handle clear flatpickr
    var handleClearFlatpickr = () => {
        const clearButton = document.querySelector("#date-range-clear");
        clearButton.addEventListener("click", (e) => {
        flatpickr.clear();
        });
    };

    // Public methods
    return {
        init: function () {
        table = document.querySelector("#wayleave-approval");

        if (!table) {
            return;
        }

        initWayleaveApprove();
        initFlatpickr();
        initFlatpickrSingleDate();
        handleSearchDatatable();
        handleClearFlatpickr();

        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    WayleaveApprove.init();
});