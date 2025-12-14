/* Getting the sid from the URL. */
const url = window.location.href;
const sid = url.split("/").pop();
// console.log(sid);
// console.log(projectStatus);
// const ref = urlParams.get("ref");
// const auth = urlParams.get("auth");

("use strict");
flatpickr("#date", {
  locale: "ms",
  altInput: true,
  altFormat: "j F Y",
  dateFormat: "Y-m-d",
  defaultDate: "today", // Set the defaultDate option to "today"
});

flatpickr("#start-time", {
  enableTime: true,
  noCalendar: true,
  dateFormat: "h:i K",
  onClose: function (selectedDates, dateStr, instance) {
    if (selectedDates.length > 0) {
      const selectedTime = selectedDates[0].toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
      });
      instance.element.value = selectedTime;
    }
  },
  onValueUpdate: function (selectedDates, dateStr, instance) {
    if (selectedDates.length > 0) {
      const selectedTime = selectedDates[0].toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
      });
      instance.element.value = selectedTime;
    }
  },
});

flatpickr("#end-time", {
  enableTime: true,
  noCalendar: true,
  dateFormat: "h:i K",
  onClose: function (selectedDates, dateStr, instance) {
    if (selectedDates.length > 0) {
      const selectedTime = selectedDates[0].toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
      });
      instance.element.value = selectedTime;
    }
  },
  onValueUpdate: function (selectedDates, dateStr, instance) {
    if (selectedDates.length > 0) {
      const selectedTime = selectedDates[0].toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
      });
      instance.element.value = selectedTime;
    }
  },
});

// Allow user input in the text fields
const startTimeInput = document.getElementById("start-time");
const endTimeInput = document.getElementById("end-time");

startTimeInput.addEventListener("input", function () {
  flatpickr("#start-time").clear();
});

endTimeInput.addEventListener("input", function () {
  flatpickr("#end-time").clear();
});

// Class definition
var AddProgress = (function () {
  // Define variables
  let validator;

  // Get elements
  const form = document.getElementById("add_progress");
  const submitButton = document.getElementById("add_progress_submit");

  const initializeValidator = () => {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        "survey-team": {
          validators: {
            notEmpty: {
              message: "Kumpulan Ukur diperlukan",
            },
          },
        },
        "daily-progress": {
          validators: {
            notEmpty: {
              message: "Jarak harian diperlukan",
            },
          },
        },
        "pegging-distance": {
          validators: {
            notEmpty: {
              message: "Jarak pancangan diperlukan",
            },
          },
        },
        "detection-distance": {
          validators: {
            notEmpty: {
              message: "Jarak pengesanan diperlukan",
            },
          },
        },
        "start-coord": {
          validators: {
            notEmpty: {
              message: "Koordinat Mula diperlukan",
            },
          },
        },
        "end-coord": {
          validators: {
            notEmpty: {
              message: "Koordinat Akhir diperlukan",
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
  };

  // Submit form handler
  const handleSubmit = () => {
    initializeValidator();

    // Handle submit button
    submitButton.addEventListener("click", (e) => {
      e.preventDefault();

      // Validate form before submit
      if (validator) {
        validator.validate().then(function (status) {
          if (status == "Valid") {
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable submit button whilst loading
            submitButton.disabled = true;

            const formData = new FormData(form);
            const result = {};

            for (const [key, value] of formData.entries()) {
              const keys = key.split(/\]\[|\[|\]/).filter(Boolean);
              let obj = result;

              for (let i = 0; i < keys.length - 1; i++) {
                const currentKey = keys[i];
                const nextKey = keys[i + 1];
                const isArray = nextKey === "";

                if (!obj[currentKey]) {
                  obj[currentKey] = isArray ? [] : {};
                }

                if (isArray && !obj[currentKey].length) {
                  obj[currentKey].push({});
                }

                obj = isArray ? obj[currentKey][0] : obj[currentKey];
              }

              const lastKey = keys[keys.length - 1];

              if (Array.isArray(obj[lastKey])) {
                obj[lastKey].push(value);
              } else if (obj[lastKey]) {
                if (!Array.isArray(obj[lastKey])) {
                  obj[lastKey] = [obj[lastKey]];
                }
                obj[lastKey].push(value);
              } else {
                obj[lastKey] = value;
              }
            }

            const jsonData = JSON.stringify(result);
            
            // console.log(jsonData); 
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  // Hide loading indication
                  submitButton.removeAttribute("data-kt-indicator");

                  Swal.fire({
                    text: "Laporan harian telah berjaya disimpan!",
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

                      // Redirect to new.php
                      setTimeout(function () {
                        location.href = "mapping/general/tasks/new";
                      });
                    }
                  });
                })
                .catch((error) => {
                  Swal.fire({
                    html: "Maaf, terdapat beberapa ralat dibahagian sistem",
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
                      submitButton.removeAttribute("data-kt-indicator");
                    }
                  });
                });
          } else {
            Swal.fire({
              html: "Maaf, sila isi butiran yang diperlukan dalam <strong>Laporan Harian</strong>",
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
      // initSelect2($(".selectOption"));
      // Call initDropzone after repeater initialization
      // initDropzone($("#add-image"));
      // initImage();
      handleSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  AddProgress.init();
});