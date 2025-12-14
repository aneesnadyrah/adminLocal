"use strict";
//Change Provider Image
function updateImage(value) {
    document.getElementById('provider-img').src = "assets/media/provider/" + value + ".webp";
}
flatpickr('#application-date', {
    locale: "ms",
    altInput: true,
    altFormat: "j F Y",
    dateFormat: "Y-m-d",
});

var initImage = function () {
    const value = document.getElementById('utility-provider').value;
    document.getElementById('provider-img').src = "assets/media/provider/" + value + ".webp";
};

var initPostcode = function () {
    $("#a-postcode").on("input", function () {
        console.log(hostApps);
        var postcode = $("#a-postcode").val();
        $.ajax({
            url: "https://"+hostApps+"/v1/lists/postcode.php",
            type: "GET",
            data: { postcode: postcode },
            success: function (r) {
                $("#a-city").val(r.data.city);
                $("#a-state").val(r.data.state);
            },
        });
    });
    $("#o-postcode").on("input", function () {
        var postcode = $("#o-postcode").val();
        $.ajax({
            url: "https://"+hostApps+"/v1/lists/postcode.php",
            type: "GET",
            data: { postcode: postcode },
            success: function (r) {
                $("#o-city").val(r.data.city);
                $("#o-state").val(r.data.state);
            },
        });
    });
};



function initMethod(el) {

    el.select2({
        placeholder: 'Sila pilih kaedah kerja pada jalan terlibat',
        allowClear: true,
        multiple: true,
        language: {
            errorLoading: function () {
                return 'Pilihan tidak dapat dimuatkan.';
            },
            searching: function () {
                return 'Memuatkan...';
            },
        },
        ajax: {
            url: "https://"+hostApps+"/v1/lists/method.php", // your API endpoint
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
                q: params.term // user's input
            };
            },
            processResults: function (data) {
            var results = [];
            data.forEach(function (item) {
                results.push({
                id: item.id,
                text: item.name
                });
            });
            return {
                results: results
            };
            },
            cache: true
        }
    });
}

// Class definition
var AddRecord = function () {

    // Private functions
    
    // Init form repeater --- more info: https://github.com/DubFriend/jquery.repeater

    const initFormRepeater = () => {

        $('#road-involved').repeater({
            show: function () {
                $(this).slideDown();
                
            },
        
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            },
        });

        // Declare repeater list element
        const roadListEl = $('[data-repeater-list="road-list"]');
        var counts2=1;
        roadListEl.bind("DOMNodeInserted", function(event) {
            const addedChildEl = event.target;
            const $select2El = $(addedChildEl).find('.method-select');
            if($select2El.length == 1){
                $select2El.attr('id', 'road-method-'+counts2);
                // initialize
                initMethod($select2El);
                counts2++;
            }
        });

        /* Initializing the select2 plugin on the first select element in the repeater. */
        const select2First = $('[data-repeater-list="road-list"]').find('.method-select');
            $.each(select2First, function(item, index){
                // console.log(item);
                // initMethod();
            })
        initMethod(select2First);


        // // Get all the method-select elements
        // const methodSelects = document.querySelectorAll('.method-select');

        // // Loop through the method-select elements and get their values
        // const methodValues = [];
        // methodSelects.forEach(select => {
        //     methodValues.push(select.value);
        // });

        let totalSum;
        let totalLength = document.getElementById('application-length');

        function calculateLength() {
          totalSum = 0;
          $('div[data-repeater-item]').each(function() {
            var $input = $(this).find('.repeater-value');
            if ($input.length > 0) {
              for (var i = 0; i < $input.length; i++) {
                var value = parseFloat($input[i].value);
                if (!isNaN(value)) {
                  totalSum += value;
                }
              }
            }
          });
          return totalSum;
        }
        
        // Loop through all the input fields with class "repeater-value" and initialize the totalSum variable
        totalSum = 0;
        $('.repeater-value').each(function() {
          var value = parseFloat($(this).val());
          if (!isNaN(value)) {
            totalSum += value;
          }
        });
        $(totalLength).val(totalSum);

        
        // Add event listener to each input element
        $('div[data-repeater-item]').on('input', '.repeater-value', function() {
          totalSum = calculateLength();
          $(totalLength).val(totalSum);
        });
        
        // Add event listener to #road-involved element to clear input
        document.getElementById('road-involved').addEventListener('keyup', function(event) {
          if (event.target && event.target.classList.contains('repeater-value')) {
            if (event.target.value === '') {
              event.target.value = 0;
              event.target.dispatchEvent(new Event('input'));
            }
          }
        });
        
        // Add event listener to "repeater-add" button
        $(document).ready(function() {
            $('button[data-repeater-create]').on('click', function() {
                // Add event listener to each input element
                $('div[data-repeater-item]').on('input', '.repeater-value', function() {
                    totalSum = calculateLength();
                    $(totalLength).val(totalSum);
                });

            });
        });

        $(document).ready(function() {
              // Add event listener to "repeater-delete" button
            $('div[data-repeater-list]').on('click', 'button[data-repeater-delete]', function() {
                $(this).closest('div[data-repeater-item]').find('.repeater-value').each(function() {
                $(this).val('');
                $(this).trigger('input');
                totalSum = calculateLength();
                $(totalLength).val(totalSum);
                });
            });
        });

        // log total sum

    
    }
    // Init DropzoneJS --- more info:
    const initDropzone = () => {
        var dropzone = new Dropzone("#add-attachment", {
            url: "https://"+hostApps+"/v1/projects/upload.php", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 5,
            maxFilesize: 1024, // MB
            acceptedFiles: 'application/pdf',
            addRemoveLinks: true,
            sending: function (file,xhr, formData) {
                formData.append('systemId', document.querySelector('input[name="system-id"]').value);
                formData.append('folder', "SRIL");
            },
            accept: function (file, done) {
                if (file.name == "wow.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            }
        });
    }

    // Handle discount options
    const handleBFCateogry = () => {
        const discountOptions = document.querySelectorAll('input[name="type-application"]');
        const BFCategoryEl = document.getElementById('brownfield-category');
        discountOptions.forEach(option => {
            option.addEventListener('change', e => {
                const value = e.target.value;

                switch (value) {
                    case 'BF': {
                        BFCategoryEl.classList.remove('d-none');
                        break;
                    }
                    default: {
                        BFCategoryEl.classList.add('d-none');
                        break;
                    }
                }
            });
        });
    }

    // Category status handler
    const handleStatus = () => {
        const target = document.getElementById('project_status');
        const select = document.getElementById('project_status_select');
        const statusClasses = ['bg-warning', 'bg-success'];

        $(select).on('change', function (e) {
            const value = e.target.value;

            switch (value) {
                case "1": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-warning');
                    break;
                }
                case "2": {
                    target.classList.remove(...statusClasses);
                    target.classList.add('bg-success');
                    break;
                }
                default:
                    break;
            }
        });

    }

    // Submit form handler
    const handleSubmit = () => {
        // Define variables
        let validator;

        // Get elements
        const form = document.getElementById('add_record');
        const submitButton = document.getElementById('add_record_submit');

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'utility-provider': {
                        validators: {
                            notEmpty: {
                                message: 'Sila pilih penyedia utiliti permohonan ini.'
                            }
                        }
                    },
                    'district': {
                        validators: {
                            notEmpty: {
                                message: 'Sila pilih daerah terlibat'
                            }
                        }
                    },
                    'region': {
                        validators: {
                            notEmpty: {
                                message: 'Sila pilih zon terlibat'
                            }
                        }
                    },
                    'project-title': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi tajuk permohonan'
                            }
                        }
                    },
                    'application-length': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi jarak permohonan'
                            }
                        }
                    },
                    'type-application': {
                        validators: {
                            notEmpty: {
                                message: 'Sila pilih jenis permohonan'
                            }
                        }
                    },
                    'brownfield-category': {
                        validators: {
                            // notEmpty: {
                            //     message: 'Sila pilih kategori permohonan brownfield'
                            // }
                        }
                    },
                    'appl-company-name': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nama syarikat pemohon'
                            }
                        }
                    },
                    'applicant-type': {
                        validators: {
                            notEmpty: {
                                message: 'Sila pilih jenis pemohon'
                            }
                        }
                    },
                    'appl-address-1': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi alamat pertama pemohon'
                            }
                        }
                    },
                    'appl-postcode': {
                        validators: {
                            notEmpty: {
                                message: 'Sila nyatakan 5 digit poskod alamat pemohon'
                            }
                        }
                    },
                    'appl-city': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi bandar alamat pemohon'
                            }
                        }
                    },
                    'appl-state': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi negeri alamat pemohon'
                            }
                        }
                    },
                    'appl-full-name': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nama penuh pemohon'
                            }
                        }
                    },
                    'appl-position': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi jawatan pemohon'
                            }
                        }
                    },
                    'appl-phone-no': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nombor telefon pemohon'
                            }
                        }
                    },
                    'appl-email': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi alamat e-mel Pemohon'
                            }
                        }
                    },
                    'officer-address-1': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi alamat pertama pegawai utiliti'
                            }
                        }
                    },
                    'officer-postcode': {
                        validators: {
                            notEmpty: {
                                message: 'Sila nyatakan 5 digit poskod alamat pegawai utiliti'
                            }
                        }
                    },
                    'officer-city': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi bandar alamat pegawai utiliti'
                            }
                        }
                    },
                    'officer-state': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi negeri alamat pegawai utiliti'
                            }
                        }
                    },
                    'officer-full-name': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nama penuh pegawai utiliti'
                            }
                        }
                    },
                    'officer-position': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi jawatan pegawai utiliti'
                            }
                        }
                    },
                    'officer-phone-no': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nombor telefon pegawai utiliti'
                            }
                        }
                    },
                    'officer-email': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi alamat e-mel pegawai Utiliti'
                            }
                        }
                    },
                    'road-name': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi nama jalan di dalam BKIL'
                            }
                        }
                    },
                    'road-length': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi jarak jalan'
                            }
                        }
                    },
                    'road-method': {
                        validators: {
                            notEmpty: {
                                message: 'Sila isi sila isi kaedah pengorekkan'
                            }
                        }
                    }
                    
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
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable submit button whilst loading
                        submitButton.disabled = true;

                        console.log($(form).serializeArray());

                        $.ajax({
                            url: "https://"+hostApps+"/v1/projects/add.php",
                            type: "POST",
                            data: $(form).serializeArray(),
                            success: function (response) {
    
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');

                                Swal.fire({
                                    text: "Rekod Permohonan telah berjaya disimpan!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, maklum!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    },
                                    allowOutsideClick: false
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        const params = new URLSearchParams(window.location.search);
                                        const id = params.get('sid');
                                        // Enable submit button after loading
                                        submitButton.disabled = false;
    
                                        // Redirect to customers list page
                                        // window.location = form.getAttribute("data-kt-redirect");
                                        window.location = "/projects/reviewForm.php?sid=" + id;
                                    }
                                });
                            },
                            error: function (response) {
                                Swal.fire({
                                    html: "Maaf, nampaknya terdapat beberapa ralat dibahagian sistem",
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
                                        submitButton.disabled = false;
                                    }
                                });
                            }
                        })
                    } else {
                        Swal.fire({
                            html: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila ambil perhatian bahawa mungkin terdapat ralat dalam tab <strong>Permohonan</strong>, <strong>Pegawai</strong> atau <strong>Jalan</strong>",
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
                                submitButton.disabled = false;
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
            // Init forms
            initFormRepeater();
            initDropzone();
            initImage();
            // initMethod();

            // Handle forms
            handleStatus();
            handleBFCateogry();
            handleSubmit();
            initPostcode();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    AddRecord.init();
});
