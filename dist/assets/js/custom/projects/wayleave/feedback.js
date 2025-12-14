
"use strict";

// Class definition
var WyFeedback = (function () {
    // Shared variables
    var table;
    var datatable;
    var flatpickr;
    var flatpickrSingleDate;
    var minDate, maxDate;
    var repeaterCopy;

    $("#kt_daterangepicker_1").daterangepicker();

    repeaterCopy = $('#copy-involved').repeater({
        initEmpty: false,

        defaultValues: {
            'text-input': 'foo'
        },

        show: function () {
            $(this).slideDown();
        },

        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });

    // JavaScript code using Axios
    var initPostcode = function () {
        $("#postcode").on("input", function () {
            var postcode = $("#postcode").val();

            // Send a GET request using Axios
            axios.get('/api/postcode/' + postcode)
                .then(function (response) {
                    console.log("API Response Data:", response.data);
                    // Handle the successful response
                    var data = response.data;
                    $("#city").val(data.city);
                    $("#state").val(data.state);
                })
                .catch(function (error) {
                    // Handle API request error
                    console.error(error);
                });
        });
        $("#postcode_client").on("input", function () {
            var postcode = $("#postcode_client").val();

            // Send a GET request using Axios
            axios.get('/api/postcode/' + postcode)
                .then(function (response) {
                    console.log("API Response Data:", response.data);
                    // Handle the successful response
                    var data = response.data;
                    $("#city_client").val(data.city);
                    $("#state_client").val(data.state);
                })
                .catch(function (error) {
                    // Handle API request error
                    console.error(error);
                });
        });
        $("#postcode_r").on("input", function () {
            var postcode = $("#postcode_r").val();

            // Send a GET request using Axios
            axios.get('/api/postcode/' + postcode)
                .then(function (response) {
                    console.log("API Response Data:", response.data);
                    // Handle the successful response
                    var data = response.data;
                    $("#city_r").val(data.city);
                    $("#state_r").val(data.state);
                })
                .catch(function (error) {
                    // Handle API request error
                    console.error(error);
                });
        });

    };

    // Private functions
    var initWyFeedback = function () {
        const sysid = window.location.pathname.split('/').pop();

        datatable = $(table).DataTable({
            ajax: apps + "/api/projects/wayleaves/list/"+sysid,
            columns: [
            { data: "RefNo" },
            { data: "Authority" },
            { data: "Length" },
            { data: "WyStatus" },
            { data: "ReceiveDate" },
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
                    if (key["WyStatus"] == 3) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-success">Surat Kelulusan Izin Lalu Diterima</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 4) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-info">Surat Maklum Balas Izin Lalu Dijana</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 5) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-primary">Surat Maklum Balas Izin Lalu Dikemaskini</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 6) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-secondary">Surat Maklum Balas Izin Lalu Dihantar Untuk Pengesahan</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 7) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-warning">Surat Maklum Balas Izin Lalu Disahkan</div>
                        <!--end::Badges-->`
                    } else if (key["WyStatus"] == 8) {
                        render =
                        `<!--begin::Badges-->
                        <div class="badge badge-light-danger">Surat Maklum Balas Izin Lalu Tidak Disahkan</div>
                        <!--end::Badges-->`
                    }
                    return render;
                },
            },
            {
                target: 4,
                render: function (index, type, key, meta) {
                    if (key["ReceiveDate"] == null) {
                        return `<div class="text-center">-</div>`
                    }
                }
            },
            {
                orderable: false,
                targets: 5,
                render: function (index, type, key, meta) {
                    let render;

                    if(tenant == 'UCIDOS' || tenant == 'KUDRAT') {
                        if (key["WyStatus"] == 3 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
                            // render =
                            // `<div class="text-center">
                            //     <button type="button" class="btn btn-icon btn-light-success"
                            //     data-bs-toggle="modal">
                            //     <i class="fad fa-eye fs-2"></i>
                            //     </button>
                            // </div>`;

                            // render = `
                            // <div class="text-center">
                            //     <button type="button" class="btn btn-icon btn-light-success view-pdf-${key["WyStatus"]}${key["ID"]}"
                            //         data-SysID="${key["SysID"]}" data-AuthID="${key["AuthorityID"]}">
                            //         <i class="fad fa-eye fs-2"></i>
                            //     </button>
                            // </div>`;

                            render = `
                            <div class="text-center">
                                <button type="button" class="btn btn-icon btn-light-success view-pdf-btn"
                                    data-SysID="${key["SysID"]}" data-AuthID="${key["AuthorityID"]}">
                                    <i class="fad fa-eye fs-2"></i>
                                </button>
                            </div>`;

                            // Assuming you have DataTable initialized, listen for button clicks
                            $('#wayleave-feedback').on('click', '.view-pdf-btn', function() {
                                var row = $(this).closest('tr');
                                var data = $('#wayleave-feedback').DataTable().row(row).data();

                                // Make an AJAX request to retrieve the PDF file path from the server
                                $.ajax({
                                    url: '/api/projects/wayleaves/feedback/attachment', // Update with your API endpoint
                                    method: 'GET',
                                    data: {
                                        sysID: data.SysID, // Assuming sysID is a property in your row data
                                        authID: data.AuthorityID, // Assuming authID is a property in your row data
                                        type: '18'
                                    },
                                    success: function(response) {
                                        // console.log('PDF File Path:', response.url);

                                        // Create an iframe element
                                        var iframe = document.createElement('iframe');
                                        iframe.src ="/components/partials/widgets/print.php?f="+response.url;
                                        iframe.width = '100%';
                                        iframe.height = '500px';

                                        // Append the iframe to a modal or any other container element
                                        $('#pdfModal .modal-body').empty().append(iframe);
                                        $('#pdfModal').modal('show'); // Show the modal
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error fetching PDF file:', error);
                                    }
                                });
                            });

                        } else if (key["WyStatus"] == 4 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                            `<div class="text-center">
                                <a href="projects/wayleave/wyFeedback/${key["SysID"]}/${key["LtrWyID"]}" class="btn btn-icon btn-light-info">
                                <i class="fad fa-envelope-open-text fs-2"></i>
                                </a>
                            </div>`;

                        } else if (key["WyStatus"] == 5 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                            `<div class="text-center">
                                <a href="projects/wayleave/wyFeedback/${key["SysID"]}/${key["LtrWyID"]}" class="btn btn-icon btn-light-primary">
                                <i class="fad fa-envelope-open fs-2"></i>
                                </a>
                            </div>`;

                        } else if (key["WyStatus"] == 6 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                            `<div class="text-center">
                                <a href="projects/wayleave/letterWyFeedback/${key["SysID"]}/${key["LtrWyID"]}" class="btn btn-icon btn-light-secondary" target="_blank">
                                <i class="fad fa-file-signature fs-2"></i>
                                </a>
                            </div>`;

                        } else if (key["WyStatus"] == 7 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-warning"
                                    data-bs-toggle="modal" data-bs-target="#uploadMkil-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelope-open-text fs-2"></i>
                                    </button>
                                </div>`;

                        } else if (key["WyStatus"] == 8 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-danger"
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
                        if (key["WyStatus"] == 3 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
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
                        } else if (key["WyStatus"] == 4 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                            `<div class="text-center">
                                <a href="projects/wayleave/wyFeedback/${key["SysID"]}/${key["LtrWyID"]}" class="btn btn-icon btn-light-info" target="_blank">
                                <i class="fad fa-envelope-open-text fs-2"></i>
                                </a>
                            </div>`;

                        } else if (key["WyStatus"] == 5 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                            `<div class="text-center">
                                <a href="projects/wayleave/wyFeedback/${key["SysID"]}/${key["LtrWyID"]}" class="btn btn-icon btn-light-primary" target="_blank">
                                <i class="fad fa-envelope-open fs-2"></i>
                                </a>
                            </div>`;

                        } else if (key["WyStatus"] == 6 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                            `<div class="text-center">
                                <a href="projects/wayleave/letterWyFeedback/${key["SysID"]}/${key["LtrWyID"]}" class="btn btn-icon btn-light-secondary" target="_blank">
                                <i class="fad fa-file-signature fs-2"></i>
                                </a>
                            </div>`;

                        } else if (key["WyStatus"] == 7 && (role == 41 || role == 42 || role == 43 || role == 44)) {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-warning"
                                    data-bs-toggle="modal" data-bs-target="#uploadMkil-` +
                                key["WyStatus"] +
                                key["ID"] +
                                `">
                                    <i class="fad fa-envelope-open-text fs-2"></i>
                                    </button>
                                </div>`;

                        } else if (key["WyStatus"] == 8 && (role == 41 || role == 42 || role == 43 || role == 44) )  {
                            render =
                                `<div class="text-center">
                                    <button type="button" class="btn btn-icon btn-light-danger"
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
                    }

                    return render;
                },
            },
            ],
            order: [[4, 'desc']], // sort by the 6th column in ascending order
        });
    };

    let item_42 = document.querySelector('input[name="item_42"]').value;
    let wy_status = document.querySelector('input[name="wy_status"]').value;
    let wy_id = document.querySelector('input[name="wy_id"]').value;

    let selectId = "#selection-" + wy_status + wy_id;
    let selectId2 = "#selection2-" + wy_status + wy_id;
    let selectId3 = "#selection3-" + wy_status + wy_id;

    let submitId = "#submit-" + wy_status + wy_id;
    let submitButton = document.querySelector(submitId);

    let sysId = "#sid-" + wy_status + wy_id;
    let folderId = "#f-" + wy_status + wy_id;

    if (item_42 === "wyFeedback-ucidos" || item_42 === "wyFeedback-kudr") {
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
            $('#selection-' + wy_status + wy_id).on('change', function () {
                var contact = $(this).find(':selected').data('contact');
                $(staff_contact).val(contact);
            });
            $('#selection2-' + wy_status + wy_id).on('change', function () {
                var contact2 = $(this).find(':selected').data('contact');
                $(staff_contact_2).val(contact2);
            });
            $('#selection3-' + wy_status + wy_id).on('change', function () {
                var position = $(this).find(':selected').data('position');
                $(approval_position).val(position);
            });
        });

        //stepper
        var stepperModal2 = "#kt_stepper_mbkil_"+ wy_status + wy_id ;
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
                    // console.log('validated!');

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
                        }).then(function () {});
                    }
                });
            } else {
                stepper.goNext();
            }
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            console.log('stepper.previous');
            stepper.goPrevious();
        });

        let formId = "#form-" + wy_status + wy_id;
        let form = document.querySelector(formId);

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

        // project
        validations.push(FormValidation.formValidation(form, {
            fields: {
                "small_title": {
                    validators: {
                        notEmpty: {
                            message: "Sila Pilih Tajuk Kecil",
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

        // Initialize the repeater once outside of the nested loops
        $('[data-repeater-list="kwc-list"]').repeater();

        // Function to collect data from the repeater fields
        function collectRepeaterData() {
            var data = [];

            // Loop through each repeated item
            $('[data-repeater-list="kwc-list"] [data-repeater-item]').each(function () {
                var periodKil = $(this).find('[name="period_kil"]').val();
                var paymentDetail = $(this).find('[name="payment_detail"]').val();
                var amount = $(this).find('[name="amount"]').val();
                var notes = $(this).find('[name="notes"]').val();
                var authorityId = $(this).find('[name="authority_id"]').val();

                // Check if all fields are not empty
                if (periodKil && paymentDetail && amount) {
                    var itemData = {
                        period_kil: periodKil,
                        payment_detail: paymentDetail,
                        amount: amount,
                        notes: notes,
                        authority_id: authorityId
                    };

                    data.push(itemData);
                }
            });

            return data;
        }

        // Initialize the repeater once outside of the nested loops
        $('[data-repeater-list="kt_copy_to"]').repeater();

        // Function to collect data from the repeater fields
        function salinanKpdRepeaterData() {
            let repeaterData = repeaterCopy.repeaterVal();
            // console.log(repeaterData);

            var data2 = [];

            repeaterData.kt_copy_to.forEach(function(data) {
                var company_name_r = data.company_name_r;
                var name_r = data.name_r;
                var addr_1_r = data.addr_1_r;
                var addr_2_r = data.addr_2_r;
                var postcode_r = data.postcode_r;
                var city_r = data.city_r;
                var state_r = data.state_r;

                var itemData2 = {
                    company_name_r: company_name_r,
                    name_r: name_r,
                    addr_1_r: addr_1_r,
                    addr_2_r: addr_2_r,
                    postcode_r: postcode_r,
                    city_r: city_r,
                    state_r: state_r
                };
                data2.push(itemData2);
            });
            // console.log("Data collected:", data2); // Debugging line

            return data2;
        }

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

                    var collectedData = collectRepeaterData();
                    // console.log(collectedData);
                    var salinanKpdData = salinanKpdRepeaterData();
                    // console.log(salinanKpdData);

                    // Add the "kwc-list" key to formData
                    formData["kwc-list"] = collectedData;
                    formData["salinan-list"] = salinanKpdData;

                    api.post('projects/wayleaves/tasks', JSON.stringify(formData))
                    .then(response => {
                        submitButton.setAttribute("data-kt-indicator", "on");
                        submitButton.disabled = false;

                        toastr.success(response.message);
                        setTimeout(function () {
                            location.href = `/projects/wayleave/feedback/${response.sysid}`;
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

    } else if (item_42 === "wyFeedback-kiter" || item_42 === "wyFeedback-kuk") {
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
            $('#selection-' + wy_status + wy_id).on('change', function () {
                var contact = $(this).find(':selected').data('contact');
                $(staff_contact).val(contact);
            });
            $('#selection3-' + wy_status + wy_id).on('change', function () {
                var position = $(this).find(':selected').data('position');
                $(approval_position).val(position);
            });
        });

        let invDate = "#inv-date-" + wy_status + wy_id;
        let invoiceDate = $(invDate).flatpickr({
            locale: "ms",
            altInput: true,
            defaultDate: "today",
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            // static: true,
        });

        //stepper
        var stepperModal2 = "#kt_stepper_mbkil_"+ wy_status + wy_id ;
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
                    // console.log('validated!');

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
                        }).then(function () {});
                    }
                });
            } else {
                stepper.goNext();
            }
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
            console.log('stepper.previous');
            stepper.goPrevious();
        });

        let formId = "#form-" + wy_status + wy_id;
        let form = document.querySelector(formId);

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

                    api.post('projects/wayleaves/tasks', JSON.stringify(formData))
                    .then(response => {
                        submitButton.setAttribute("data-kt-indicator", "on");
                        submitButton.disabled = true;

                        Swal.fire({
                            text: "Surat Maklum Balas Izin Lalu telah berjaya Dijana 🎉",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, Faham",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                            allowOutsideClick: false,
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                // Enable button
                                submitButton.disabled = false;

                                toastr.success(response.message);
                                setTimeout(function () {
                                    location.href = `/projects/wayleave/wyFeedback/${response.sysid}/${response.id}`;
                                }, 2500);
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

    // Initialize Dropzone for the modal
    let dropzone = new Dropzone("#dropzone_upload_mkil", {
        url: `${apps}/api/tasks/0046/upload`,
        paramName: "file",
        maxFiles: 1,
        maxFilesize: 1024,
        acceptedFiles: "application/pdf",
        autoProcessQueue: false,
        addRemoveLinks: true,
        sending: function (file, xhr, formData) {
            formData.append("systemId", document.querySelector(sysId).value);
            formData.append("folder", document.querySelector(folderId).value);
        },
        accept: function (file, done) {
            done();
        },
    });

    dropzone.on("success", function (file, response) {
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");
        // Enable button
        submitButton.disabled = false;

        toastr.success(response.message);

        setTimeout(function () {
            location.href = "/tasks/new";
        }, 2500);
    });

    dropzone.on("addedfile", function () {
        submitButton.classList.remove("d-none");
    });

    // Remove files when the modal is closed
    dropzone.on("hidden.bs.modal", function () {
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
            table = document.querySelector("#wayleave-feedback");

            if (!table) {
                return;
            }

            initPostcode();
            initWyFeedback();
            initFlatpickr();
            initFlatpickrSingleDate();
            handleSearchDatatable();
            handleClearFlatpickr();

        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    WyFeedback.init();
});