"use strict";
//Change Provider Image
function updateImage(value) {
  document.getElementById("provider-img").src =
    "assets/media/provider/" + value + ".webp";
}
flatpickr("#application-date", {
  locale: "ms",
  altInput: true,
  altFormat: "j F Y",
  dateFormat: "Y-m-d",
});
// Class definition
var AddRecord = (function () {
  // Private functions

  // Init form repeater --- more info: https://github.com/DubFriend/jquery.repeater
  const initFormRepeater = () => {
    $("#road-invloved").repeater({
      initEmpty: false,

      show: function () {
        $(this).slideDown();
      },

      hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
      },
    });
  };

  // Init DropzoneJS --- more info:
  const initDropzone = () => {
    var myDropzone = new Dropzone("#add-attachment", {
      url: "https://"+hostApps+"/v1/projects/upload.php", // Set the url for your upload script location
      paramName: "file", // The name that will be used to transfer the file
      maxFiles: 5,
      maxFilesize: 1024, // MB
      addRemoveLinks: true,
      sending: function (file, xhr, formData) {
        formData.append(
          "systemId",
          document.querySelector('input[name="system-id"]').value
        );
        formData.append("folder", "SRIL");
      },
      accept: function (file, done) {
        if (file.name == "wow.jpg") {
          done("Naha, you don't.");
        } else {
          done();
        }
      },
    });
  };

  // Handle discount options
  const handleBFCateogry = () => {
    const discountOptions = document.querySelectorAll(
      'input[name="type-application"]'
    );
    const BFCategoryEl = document.getElementById("brownfield-category");
    discountOptions.forEach((option) => {
      option.addEventListener("change", (e) => {
        const value = e.target.value;

        switch (value) {
          case "BF": {
            BFCategoryEl.classList.remove("d-none");
            break;
          }
          default: {
            BFCategoryEl.classList.add("d-none");
            break;
          }
        }
      });
    });
  };

  // Category status handler
  const handleStatus = () => {
    const target = document.getElementById("project_status");
    const select = document.getElementById("project_status_select");
    const statusClasses = ["bg-warning", "bg-success"];

    $(select).on("change", function (e) {
      const value = e.target.value;

      switch (value) {
        case "1": {
          target.classList.remove(...statusClasses);
          target.classList.add("bg-warning");
          break;
        }
        case "2": {
          target.classList.remove(...statusClasses);
          target.classList.add("bg-success");
          break;
        }
        default:
          break;
      }
    });
  };

  // Submit form handler
  const handleSubmit = () => {
    // Define variables
    let validator;

    // Get elements
    const form = document.getElementById("add_record");
    const submitButton = document.getElementById("add_record_submit");

    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        "utility-provider": {
          validators: {
            notEmpty: {
              message: "Sila pilih penyedia utiliti permohonan ini.",
            },
          },
        },
        district: {
          validators: {
            notEmpty: {
              message: "Sila pilih daerah terlibat",
            },
          },
        },
        region: {
          validators: {
            notEmpty: {
              message: "Sila pilih zon terlibat",
            },
          },
        },
        "project-title": {
          validators: {
            notEmpty: {
              message: "Sila isi tajuk permohonan",
            },
          },
        },
        "application-length": {
          validators: {
            notEmpty: {
              message: "Sila isi jarak permohonan",
            },
          },
        },
        "type-application": {
          validators: {
            notEmpty: {
              message: "Sila pilih jenis permohonan",
            },
          },
        },
        "brownfield-category": {
          validators: {
            notEmpty: {
              message: "Sila pilih kategori permohonan brownfield",
            },
          },
        },
        "appl-company-name": {
          validators: {
            notEmpty: {
              message: "Sila isi nama syarikat pemohon",
            },
          },
        },
        "applicant-type": {
          validators: {
            notEmpty: {
              message: "Sila pilih jenis pemohon",
            },
          },
        },
        "appl-address-1": {
          validators: {
            notEmpty: {
              message: "Sila isi alamat pertama pemohon",
            },
          },
        },
        "appl-postcode": {
          validators: {
            notEmpty: {
              message: "Sila nyatakan 5 digit poskod alamat pemohon",
            },
          },
        },
        "appl-city": {
          validators: {
            notEmpty: {
              message: "Sila isi bandar alamat pemohon",
            },
          },
        },
        "appl-state": {
          validators: {
            notEmpty: {
              message: "Sila isi negeri alamat pemohon",
            },
          },
        },
        "appl-full-name": {
          validators: {
            notEmpty: {
              message: "Sila isi nama penuh pemohon",
            },
          },
        },
        "appl-position": {
          validators: {
            notEmpty: {
              message: "Sila isi jawatan pemohon",
            },
          },
        },
        "appl-phone-no": {
          validators: {
            notEmpty: {
              message: "Sila isi nombor telefon pemohon",
            },
          },
        },
        "appl-email": {
          validators: {
            notEmpty: {
              message: "Sila isi alamat e-mel Pemohon",
            },
          },
        },
        "officer-address-1": {
          validators: {
            notEmpty: {
              message: "Sila isi alamat pertama pegawai utiliti",
            },
          },
        },
        "officer-postcode": {
          validators: {
            notEmpty: {
              message: "Sila nyatakan 5 digit poskod alamat pegawai utiliti",
            },
          },
        },
        "officer-city": {
          validators: {
            notEmpty: {
              message: "Sila isi bandar alamat pegawai utiliti",
            },
          },
        },
        "officer-state": {
          validators: {
            notEmpty: {
              message: "Sila isi negeri alamat pegawai utiliti",
            },
          },
        },
        "officer-full-name": {
          validators: {
            notEmpty: {
              message: "Sila isi nama penuh pegawai utiliti",
            },
          },
        },
        "officer-position": {
          validators: {
            notEmpty: {
              message: "Sila isi jawatan pegawai utiliti",
            },
          },
        },
        "officer-phone-no": {
          validators: {
            notEmpty: {
              message: "Sila isi nombor telefon pegawai utiliti",
            },
          },
        },
        "officer-email": {
          validators: {
            notEmpty: {
              message: "Sila isi alamat e-mel pegawai Utiliti",
            },
          },
        },
        "road-name": {
          validators: {
            notEmpty: {
              message: "Sila isi nama jalan di dalam BKIL",
            },
          },
        },
        "road-length": {
          validators: {
            notEmpty: {
              message: "Sila isi jarak jalan",
            },
          },
        },
        "road-method": {
          validators: {
            notEmpty: {
              message: "Sila isi sila isi kaedah pengorekkan",
            },
          },
        },
        "system-id": {
          validators: {
            notEmpty: {
              message: "Sila isi sila isi kaedah pengorekkan",
            },
          },
        },
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap: new FormValidation.plugins.Bootstrap5({
          rowSelector: ".fv-row",
          eleInvalidClass: "",
          eleValidClass: "",
        }),
      },
    });

    // Handle submit button
    submitButton.addEventListener("click", (e) => {
      e.preventDefault();

      // Validate form before submit
      if (validator) {
        validator.validate().then(function (status) {
          console.log("validated!");

          if (status == "Valid") {
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable submit button whilst loading
            submitButton.disabled = true;

            $.ajax({
              url: "https://"+hostApps+"/v1/projects/add.php",
              type: "POST",
              data: $(form).serializeArray(),
              success: function (response) {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                Swal.fire({
                  text: "Rekod Permohonan telah berjaya disimpan!",
                  icon: "success",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function (result) {
                  if (result.isConfirmed) {
                    // Enable submit button after loading
                    submitButton.disabled = false;

                    // Redirect to customers list page
                    window.location = form.getAttribute("data-kt-redirect");
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
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function (result) {
                  if (result.isConfirmed) {
                    // Enable submit button after loading
                    submitButton.disabled = false;
                  }
                });
              },
            });
          } else {
            Swal.fire({
              html: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila ambil perhatian bahawa mungkin terdapat ralat dalam tab <strong>Permohonan</strong>, <strong>Pegawai</strong> atau <strong>Jalan</strong>",
              icon: "error",
              buttonsStyling: false,
              confirmButtonText: "Ok, maklum!",
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
          }
        });
      }
    });
  };

  // Public methods
  return {
    init: function () {
      // Init forms
      initFormRepeater();
      initDropzone();

      // Handle forms
      handleStatus();
      handleBFCateogry();
      handleSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  AddRecord.init();
});
