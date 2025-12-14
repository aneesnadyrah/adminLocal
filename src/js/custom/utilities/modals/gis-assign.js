if (document.getElementById("gis-assign-modal") === null) {
  // Element does not exist
} else {
  // Class definition
  var GISModal = (function () {
    // Elements
    var form;
    var submitButton;
    var validator;

    // Handle form
    var handleValidation = function (e) {
      // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
      validator = FormValidation.formValidation(form, {
        fields: {
          "gis-assign": {
            validators: {
              notEmpty: {
                message: "Sila Pilih Pegawai GIS",
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

    var handleAssignGis = function (e) {
      // Handle form submit
      submitButton.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        validator.validate().then(function (status) {
          if (status == "Valid") {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple click
            submitButton.disabled = true;

            $.ajax({
              url: "https://"+hostApps+"/v1/projects/gis-assign.php",
              type: "POST",
              data: $(form).serializeArray(),
              success: function (response) {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                // Enable button
                submitButton.disabled = false;

                // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  text: "Lantikan Pegawai Berjaya!",
                  icon: "success",
                  // timer: 1500,
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function (result) {
                  if (result.isConfirmed) {
                    window.location.href = "/tasks/new";
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
              html: "Maaf, Sila pilih <strong>Pegawai GIS</strong> untuk permohonan ini.",
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
        form = document.querySelector("#gis-assign-form");
        submitButton = document.querySelector("#gis-assign-submit");

        handleValidation();
        handleAssignGis();
      },
    };
  })();

  // On document ready
  KTUtil.onDOMContentLoaded(function () {
    GISModal.init();
  });

  // Format options
  var optionFormat = function (item) {
    if (!item.id) {
      return item.text;
    }

    var span = document.createElement("span");
    var imgUrl = item.element.getAttribute("data-profile-picture");
    var template = "";

    template +=
      '<img src="' +
      imgUrl +
      '" class="rounded-circle h-30px me-2" alt="image"/>';
    template += item.text;

    span.innerHTML = template;

    return $(span);
  };

  // Init Select2 --- more info: https://select2.org/
  $("#select-option").select2({
    minimumResultsForSearch: Infinity,
    templateSelection: optionFormat,
    templateResult: optionFormat,
  });
}
