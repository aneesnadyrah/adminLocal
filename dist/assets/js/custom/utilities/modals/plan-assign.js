"use strict";
if (document.getElementById('plan_action_form') === null) {
  // Element does not exist
} else {
// Class definition
var planModal = (function () {
  // Elements
  var form;
  var submitButton;
  var validator;

  // Init flatpickr --- more info :https://flatpickr.js.org/getting-started/
  var initFlatpickr = () => {
    const datePicker1Element = document.getElementById(
      "plan_start_picker_linked_1"
    );
    const linked1 = new tempusDominus.TempusDominus(datePicker1Element);
    const linked2 = new tempusDominus.TempusDominus(
      document.getElementById("plan_end_picker_linked_2"),
      {
        useCurrent: false,
      }
    );

    //using event listeners
    datePicker1Element.addEventListener(
      tempusDominus.Namespace.events.change,
      (e) => {
        linked2.updateOptions({
          restrictions: {
            minDate: e.detail.date,
          },
        });
      }
    );

    //using subscribe method
    const subscription = linked2.subscribe(
      tempusDominus.Namespace.events.change,
      (e) => {
        linked1.updateOptions({
          restrictions: {
            maxDate: e.date,
          },
        });
      }
    );
  };

//   flatpickr(form.querySelector('[name="start-date"]'), {
//     enableTime: false,
//     // See https://flatpickr.js.org/formatting/
//     dateFormat: 'Y/m/d',
//     // After selecting a date, we need to revalidate the field
//     onChange: function () {
//         fv.revalidateField('start-date');
//     },
// });

// flatpickr(form.querySelector('[name="end-date"]'), {
//     enableTime: false,
//     dateFormat: 'Y/m/d',
//     onChange: function () {
//         fv.revalidateField('endD-date');
//     },
// });

  // Handle form
  var handleValidation = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        "plan-assign": {
          validators: {
            notEmpty: {
              message: "Sila Pilih Kumpulan plan",
            },
          },
        },
        "start-date": {
          validators: {
            notEmpty: {
              message: "Sila Pilih Tarikh Mula",
            },
            // date: {
            //   format: "DD/MM/YYYY, hh:mm a",
            //   message: "Tarikh Mula Tidak Sah",
            // },
          },
        },
        "end-date": {
          validators: {
            notEmpty: {
              message: "Sila Pilih Tarikh Tamat",
            },
            // date: {
            //   format: "DD/MM/YYYY",
            //   message: "Tarikh Tamat Tidak Sah",
            // },
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
        // startEndDate: new FormValidation.plugins.StartEndDate({
        //   format: 'YYYY/MM/DD',
        //   startDate: {
        //       field: 'start-date',
        //       message: 'The start date must be a valid date and ealier than the end date',
        //   },
        //   endDate: {
        //     field: 'end-date',
        //     message: 'The end date must be a valid date and later than the start date',
        // },
        // })
      },
    });
  };

  var handleplanAssign = function (e) {
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
            url: "https://"+hostApps+"/v1/projects/plan-assign.php",
            type: "POST",
            data: $(form).serializeArray(),
            success: function (response) {
              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

              // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
              Swal.fire({
                text: "Lantikan Kumpulan Berjaya!",
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
                  // form.reset(); // reset form
                  //form.submit(); // submit form
                  window.location.href = "/plan/tasks/new.php";
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
            html: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
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

  // Format options
  const optionFormat = (item) => {
    if (!item.id) {
      return item.text;
    }

    var span = document.createElement("span");
    var template = "";

    template += '<div class="d-flex align-items-center">';
    template +=
      '<img src="' +
      item.element.getAttribute("data-icon") +
      '" class="rounded-circle h-30px me-3" alt="' +
      item.text +
      '"/>';
    template += '<div class="d-flex flex-column">';
    template += '<span class="fs-5 fw-semibold lh-1">' + item.text + "</span>";
    template += "</div>";
    template += "</div>";

    span.innerHTML = template;

    return $(span);
  };

  // Init Select2 --- more info: https://select2.org/
  $("#select-icon-plan").select2({
    placeholder: "Select an option",
    minimumResultsForSearch: Infinity,
    templateSelection: optionFormat,
    templateResult: optionFormat,
  });

  // Public functions
  return {
    // Initialization
    init: function () {
      form = document.querySelector("#plan_action_form");
      submitButton = document.querySelector("#plan_action_submit");

      handleValidation();
      handleplanAssign();
      initFlatpickr();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  planModal.init();
});

}
