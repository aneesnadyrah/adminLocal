"use strict";
if (document.getElementById("wo_action_form") === null) {
  // Element does not exist
} else {
  // Class definition
  var WOModal = (function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleValidation = function (e) {
      // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
      validator = FormValidation.formValidation(form, {
        fields: {
          "total-wo": {
            validators: {
              regexp: {
                regexp: /^\d+$/,
                message: "Sila isi angka digit sahaja",
              },
              notEmpty: {
                message: "Nilai Arahan Kerja diperlukan",
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
    };

  //   fetch("https://"+hostApps+"/v1/tasks/new.php")
  // .then(response => response.json())
  //     .then(result => {

  //       let data = result.data;

  //       for (let i = 0; i < data.length; i++){

  //         let ID = data[i].ID;

  //         let StatusID = data[i].StatusID;
  //         console.log(StatusID);
  //       initDropzone(ID,StatusID);
  //       }


  //     })
  //     .catch(error => console.error(error));

    if (document.getElementsByClassName("dropzone") === null) {
      initDropzone = () => {};
    } else {
      // Init DropzoneJS --- more info:
    initDropzone = () => {
        var myDropzone = new Dropzone(".dropzone", {
          url: "https://"+hostApps+"/v1/projects/upload.php", // Set the url for your upload script location
          autoProcessQueue: true,
          paramName: "file", // The name that will be used to transfer the file
          maxFiles: 5,
          maxFilesize: 1024, // MB
          addRemoveLinks: true,
          sending: function (file, xhr, formData) {
            formData.append(
              "systemId",
              document.querySelector('input[name="system-id"]').value
            );
            formData.append(
              "folder",
              document.querySelector('input[name="folder"]').value
            );
          },
          accept: function (file, done) {
            done();
          },
        });
    };
    }

// // Get all elements with the class "dropzone"
// var dropzones = document.getElementsByClassName("dropzone");
// for (var i = 0; i < dropzones.length; i++) {
//   initDropzone(dropzones[i]);
// }


// // Initialize Dropzone for a specific element
// function initDropzone(element) {
//   if (element) {
//     var myDropzone = new Dropzone(element, {
//       url: "https://"+hostApps+"/v1/projects/upload.php", // Set the url for your upload script location
//       autoProcessQueue: true,
//       paramName: "file", // The name that will be used to transfer the file
//       maxFiles: 5,
//       maxFilesize: 1024, // MB
//       addRemoveLinks: true,
//       sending: function (file, xhr, formData) {
//         formData.append(
//           "systemId",
//           document.querySelector('input[name="system-id"]').value
//         );
//         formData.append(
//           "folder",
//           document.querySelector('input[name="folder"]').value
//         );
//       },
//       accept: function (file, done) {
//         done();
//       },
//     });
//   }
// }

  //   fetch("https://"+hostApps+"/v1/tasks/new.php")
  // .then(response => response.json())
  //     .then(result => {

  //       let data = result.data;
  //   for (const [key, value] of Object.entries(data)) {
  //     const statusId = value.StatusID;
  //     const id = value.ID;
  //     const dropzoneElement = document.querySelector(`#add-attachment-${statusId}${id}`);
  //     if (dropzoneElement) {
  //       const myDropzone = new Dropzone(dropzoneElement, {
  //         url: "https://"+hostApps+"/v1/projects/upload.php",
  //         autoProcessQueue: true,
  //         paramName: "file",
  //         maxFiles: 5,
  //         maxFilesize: 1024,
  //         addRemoveLinks: true,
  //         sending: function (file, xhr, formData) {
  //           formData.append(
  //             "systemId",
  //             document.querySelector('input[name="system-id"]').value
  //           );
  //           formData.append(
  //             "folder",
  //             document.querySelector('input[name="folder"]').value
  //           );
  //         },
  //         accept: function (file, done) {
  //           done();
  //         },
  //       });
  //     } else {
  //       console.error(`Dropzone element not found for task with status ID ${statusId} and ID ${id}`);
  //     }
  //   }
  // })
  //     .catch(error => console.error(error));



    var handleSubmit = function (e) {
      // Handle form submit
      submitButton.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        validator.validate().then(function (status) {
          // console.log(status);
          if (status == "Valid") {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            $.ajax({
              url: "https://"+hostApps+"/v1/projects/tasking.php",
              type: "POST",
              data: $(form).serializeArray(),
              success: function (response) {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                // Enable button
                submitButton.disabled = false;

                // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  text: "Arahan Kerja Berjaya dihantar!",
                  icon: "success",
                  timer: 1500,
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function (result) {
                  if (result.isConfirmed) {
                    form.reset(); // reset form
                    // form.submit(); // submit form
                    location.href = "/tasks/new";
                  }
                });
              },
              error: function (XMLHttpRequest, textStatus, errorThrown) {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                // Enable button
                submitButton.disabled = false;

                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  text: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
                  icon: "error",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                });
              },
            });
          } else {
            // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
            Swal.fire({
              html: "Maaf, Sila masukkan <strong>nilai arahan kerja</strong> untuk permohonan ini.",
              icon: "warning",
              buttonsStyling: false,
              confirmButtonText: "Ok, maklum!",
              customClass: {
                confirmButton: "btn btn-primary",
              },
              allowOutsideClick: false,
            });
          }
        });
      });
    };

    // Public functions
    return {
      // Initialization
      init: function () {
        form = document.querySelector("#wo_action_form");
        submitButton = document.querySelector("#task_wo_submit");

        initDropzone();
        handleValidation();
        handleSubmit();
      },
    };
  })();

  // On document ready
  KTUtil.onDOMContentLoaded(function () {
    WOModal.init();
  });
}
