if (document.getElementById('gpkg-attachment') === null) {
    initDropzone = () => {}
} else {
    // Class definition
    var GISModal = function() {
        // Elements
        var form;
        var submitButton = document.getElementById('action_submit');
        var validator;

        // Handle form
        var handleValidation = function(e) {
            // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
            validator = FormValidation.formValidation(
                form,
                {
                    fields: {					
                        'gpkg-attachment': {
                            validators: {
                                notEmpty: {
                                    message: 'Sila Pilih Fail gpkg'
                                }
                            }
                        },
                    },
                    plugins: {
                        trigger: new FormValidation.plugins.Trigger(),
                        bootstrap: new FormValidation.plugins.Bootstrap5({
                            rowSelector: '.fv-row',
                            eleInvalidClass: '',  // comment to enable invalid state icons
                            eleValidClass: '' // comment to enable valid state icons
                        })
                    }
                }
            );	
        }

        // var initDropzone;
        if (document.getElementById('gpkg-attachment') === null){
            initDropzone = () => {}
        } else {
            // Init DropzoneJS --- more info:
            initDropzone = () => {
                var myDropzone = new Dropzone("#gpkg-attachment", {
                    url: "https://"+hostApps+"/v1/projects/gis-gpkg.php", // Set the url for your upload script location
                    // autoQueue: true,
                    // autoProcessQueue: false,
                    paramName: "file", // The name that will be used to transfer the file
                    maxFiles: 5,
                    maxFilesize: 1024, // MB
                    addRemoveLinks: true,
                    sending: function (file,xhr, formData) {
                        formData.append('systemId', document.querySelector('input[name="system-id"]').value);
                        formData.append('folder', document.querySelector('input[name="folder"]').value);
                        formData.append('refNo', document.querySelector('input[name="ref-no"]').value);
                    },
                    accept: function (file, done) {
                            done();
                    },
                    success: function (response) {
                        // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
                            text: "Muatnaik Berjaya!",
                            icon: "success",
                            // timer: 1500,
                            buttonsStyling: false,
                            confirmButtonText: "Ok, maklum!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            },
                            allowOutsideClick: false,
                        }).then(function (result) {
                            if (result.isConfirmed) { 
                                //Enable submit button after loading
                                submitButton.disabled = false;

                                // Redirect to customers list page
                                window.location = form.getAttribute("data-kt-redirect");
                            }
                        });

                    },
                });
                document.getElementById("action_submit").addEventListener("click", function(e) {
                    e.preventDefault();
                  
                    if (myDropzone.files.length === 0) {
                        Swal.fire({
                            html: "Maaf, Sila muatnaik <strong>Fail gpkg</strong> untuk permohonan ini.",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, maklum!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            },
                            allowOutsideClick: false
                        });
                      return false;
                    }
                });

            };

        }

        // Public functions
        return {
            // Initialization
            init: function() {
                form = document.querySelector('#gis_action');
                submitButton = document.querySelector('#action_submit');
                
                initDropzone();
                handleValidation();
            }
        };
    }();

    // On document ready
    KTUtil.onDOMContentLoaded(function() {
        GISModal.init();
    });
}
		
		
