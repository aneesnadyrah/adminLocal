"use strict";

// Class definition
var initTable = (function () {
  // Shared variables
  let table;
  let datatable;
  let dropzone;

  toastr.options = {
    closeButton: false,
    debug: false,
    newestOnTop: false,
    progressBar: false,
    positionClass: "toastr-bottom-right",
    preventDuplicates: true,
    onclick: null,
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "2000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut",
  };

  var initDatatable = function () {
    // Init datatable --- more info on datatables: https://datatables.net/manual/
    datatable = $(table).DataTable({
      info: false,
      language: {
        loadingRecords: "Sila Tunggu...",
        zeroRecords: "Tiada Rekod Dijumpai",
      },
      pageLength: 10,
      order: [[5, "desc"]], // sort by the 6th column in ascending order
    });

    // Initialize Tooltip for all reference_no
    datatable.on('init.dt', function () {
      // Initialize tooltips for elements with the class 'datatable-tooltip'
      $('[data-bs-toggle="datatable-tooltip"]').tooltip();

      // Find all reference_no button
      let detailsButton = document.querySelectorAll('[data-task-table="detail-button"]');

      // Find modal element
      let detailModalEl = document.getElementById('task-projects-details');

      // declare modal
      let detailModal = new bootstrap.Modal(detailModalEl);

      // add event listener to each button
      detailsButton.forEach((button) => {
        button.addEventListener("click", function () {
          const buttonSysId = this.getAttribute('data-task-custom');
          alert(buttonSysId);
          detailModal.show();
        })
      })
    });

    // Initialize Dropzone for each modal
    datatable.on("click", "button", function () {
      let modalId = $(this).data("bs-target");
      let submitId = "#submit-" + modalId.slice(8);
      let formId = "#form-" + modalId.slice(8);
      let formSurveyId = "#formSurvey-" + modalId.slice(8);
      let sysId = "#sid-" + modalId.slice(8);
      let folderId = "#f-" + modalId.slice(8);
      let form = document.querySelector(formId);
      let formSurvey = document.querySelector(formSurveyId);

      let selectId = "#selection-" + modalId.slice(8);
      let selectId2 = "#selection2-" + modalId.slice(8);
      let selectId3 = "#selection3-" + modalId.slice(8);

      let submitButton = document.querySelector(submitId);
      let previousId = "#previous-" + modalId.slice(8);
      let previousButton = document.querySelector(previousId);

      if (
        role === 61 ||
        role === 62 ||
        role === 63 ||
        role === 64 ||
        role === 65 ||
        role === 66 ||
        role === 67
      ) {
        if (formSurveyId.slice(0, 15) === "#formSurvey-002") {
          // Format options
          var optionFormat = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var imgUrl = item.element.getAttribute("data-profile-picture");
            var template = "";

            template += `<img src=${imgUrl} class="rounded-circle h-30px me-2" alt="image"/>`;
            template += item.text;

            span.innerHTML = template;

            return $(span);
          };

          $(document).ready(function () {
            // Update second selection options when the first selection changes
            $(selectId).on("change", function () {
              var firstSelection = $(this).val();

              //   const serializedArray = $(firstSelection).serializeArray();
              //   const formData = {};

              //   serializedArray.forEach((item) => {
              //     formData[item.name] = item.value;
              //   });

              //   const jsonData = JSON.stringify(formData);
              const jsonData = { survey_team: firstSelection };

              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  console.log("Response received:", response.teamMembers);

                  // Split the response into separate values
                  var values = response.teamMembers.split(",");

                  // Clear the second selection
                  $(selectId2).empty();

                  // Add each value as an option to the second selection
                  values.forEach(function (value) {
                    var profilePicture = response.memberImg
                      ? "assets/media/avatars/" + response.memberImg + ".jpg"
                      : "assets/media/avatars/blank.jpg"; // Conditional check

                    $(selectId2).append(
                      '<option value="' +
                        value.trim() +
                        '" data-profile-picture="' +
                        profilePicture +
                        '">' +
                        value.trim() +
                        "</option>"
                    );
                  });
                })
                .catch((error) => {
                  // Hide loading indication
                  submitButton.removeAttribute("data-kt-indicator");

                  // Enable button
                  submitButton.disabled = false;

                  // Show error popup
                  Swal.fire({
                    text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, faham",
                    customClass: {
                      confirmButton: "btn btn-primary",
                    },
                    allowOutsideClick: false,
                  });
                });
            });
          });

          // Init Select2 --- more info: https://select2.org/
          $(selectId).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat,
          });

          $(selectId).val(null).trigger("change");

          // Init Select2 --- more info: https://select2.org/
          $(selectId2).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat,
          });

          $(selectId2).val(null).trigger("change");

          let validator = FormValidation.formValidation(formSurvey, {
            fields: {
              "survey-team-assign": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Kumpulan Ukur",
                  },
                },
              },
              "survey-leader-assign": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Ketua Kumpulan Ukur",
                  },
                },
              },
              "modal-date-range": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Jangkaan Julat Tarikh Mula & Tamat",
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

          submitButton.classList.remove("d-none");
          // Handle form submit
          submitButton.addEventListener("click", function (e) {
            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
              console.log("validated!:", status);
              if (status == "Valid") {
                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // /v1/projects/tasking.php
                // Send Axios POST request
                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success("Lantikan Kumpulan Survey Berjaya! 🎉");

                    setTimeout(function () {
                      location.href = "tasks/new";
                    }, 2500);
                  })
                  .catch((error) => {
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
                  });
              } else {
                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  html: "Maaf, Sila pilih <strong>Kumpulan Survey</strong> untuk permohonan ini.",
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

          const modalElement = document.querySelector(
            "#modal-date-range-" + modalId.slice(8)
          );
          flatpickrModal = $(modalElement).flatpickr({
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            mode: "range",
            onChange: function (selectedDates, dateStr, instance) {
              initConfig.handleFlatpickrModal(selectedDates, dateStr, instance);
            },
          });
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-003") {
          let validator;
          let timePicker = "#time-picker-" + modalId.slice(8);

          // Format options
          var optionFormat = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var iconClass = item.element.getAttribute("data-icon-class");
            var template = "";

            template += '<i class="' + iconClass + '"></i>';
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

          $(selectId).val(null).trigger("change");

          // Format options
          var optionFormat2 = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var iconClass = item.element.getAttribute("data-icon-class");
            var template = "";

            template += '<i class="' + iconClass + '"></i>';
            template += item.text;

            span.innerHTML = template;

            return $(span);
          };

          // Init Select2 --- more info: https://select2.org/
          $(selectId2).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat2,
            templateResult: optionFormat2,
          });

          $(selectId2).val("3").trigger("change");

          validator = FormValidation.formValidation(formSurvey, {
            fields: {
              "number-team": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Bilangan Ahli Kumpulan",
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

          // Get the current date and time
          let now = new Date();

          // Define a function that updates the value of the time picker input every second
          function updateTime() {
            // Get the current time as HH:MM:SS (e.g. 09:30:00)
            let time = now.toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
              second: "2-digit",
            });

            // Set the value of the time picker input to the current time
            document.querySelector(timePicker).value = time;

            // Update the current date and time for the next second
            now = new Date();
          }

          // Call the updateTime function every second to update the time picker input
          setInterval(updateTime, 1000);

          function generateUniqueId() {
            const randomBytes = new Uint8Array(64);
            crypto.getRandomValues(randomBytes);
            return Array.from(randomBytes, (byte) =>
              ("0" + byte.toString(16)).slice(-2)
            ).join("");
          }

          const base64Input = document.getElementById("bit64");

          // Handle form submit
          submitButton.classList.remove("d-none");
          // Handle form submit
          submitButton.addEventListener("click", function (e) {
            // Add an event listener to the submit button that gets the current time and stores it in a hidden input field
            // document.querySelector("#submit-button").addEventListener("click", function() {
            // let now = new Date();
            // let timestamp = now.toISOString();
            // document.querySelector("#hidden-timestamp-input").value = timestamp;
            // });

            // Generate unique ID
            const uniqueId = generateUniqueId();
            console.log(uniqueId);

            // Set the value of the base64 input to the unique ID
            base64Input.value = uniqueId;

            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
              if (status == "Valid") {
                // console.log($(form).serializeArray());
                // return;
                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // /v1/projects/tasking.php
                // Send Axios POST request
                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success(response.message);

                    setTimeout(function () {
                      location.href = "tasks/new";
                    }, 2500);
                  })
                  .catch((error) => {
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
                  });
              } else {
                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  html: "Maaf, Sila Semak <strong>Bilangan Ahli</strong> untuk kumpulan ini.",
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
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-004") {
          let progressId = "#progress-" + modalId.slice(8);
          let progress = document.querySelector(progressId).value;
          console.log(progress);
          let timePicker2 = "#time-picker2-" + modalId.slice(8);

          // validate attendance = 1
          // qr-code = 2

          if (progress == 1) {
            let validator;

            // Format options
            var optionFormat = function (item) {
              if (!item.id) {
                return item.text;
              }

              var span = document.createElement("span");
              var iconClass = item.element.getAttribute("data-icon-class");
              var template = "";

              template += '<i class="' + iconClass + '"></i>';
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

            $(selectId).val(null).trigger("change");

            // Format options
            var optionFormat2 = function (item) {
              if (!item.id) {
                return item.text;
              }

              var span = document.createElement("span");
              var iconClass = item.element.getAttribute("data-icon-class");
              var template = "";

              template += '<i class="' + iconClass + '"></i>';
              template += item.text;

              span.innerHTML = template;

              return $(span);
            };

            // Init Select2 --- more info: https://select2.org/
            $(selectId2).select2({
              minimumResultsForSearch: Infinity,
              templateSelection: optionFormat2,
              templateResult: optionFormat2,
            });

            $(selectId2).val("3").trigger("change");

            validator = FormValidation.formValidation(formSurvey, {
              fields: {
                "number-team": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Bilangan Ahli Kumpulan",
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

            // Get the current date and time
            let now = new Date();

            // Define a function that updates the value of the time picker input every second
            function updateTime() {
              // Get the current time as HH:MM:SS (e.g. 09:30:00)
              let time = now.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
              });

              // Set the value of the time picker input to the current time
              document.querySelector(timePicker2).value = time;

              // Update the current date and time for the next second
              now = new Date();
            }

            // Call the updateTime function every second to update the time picker input
            setInterval(updateTime, 1000);

            function generateUniqueId() {
              const randomBytes = new Uint8Array(64);
              crypto.getRandomValues(randomBytes);
              return Array.from(randomBytes, (byte) =>
                ("0" + byte.toString(16)).slice(-2)
              ).join("");
            }

            // const base64Input = document.getElementById("bit64");

            // Handle form submit
            submitButton.classList.remove("d-none");
            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Add an event listener to the submit button that gets the current time and stores it in a hidden input field
              // document.querySelector("#submit-button").addEventListener("click", function() {
              // let now = new Date();
              // let timestamp = now.toISOString();
              // document.querySelector("#hidden-timestamp-input").value = timestamp;
              // });

              // Generate unique ID
              const uniqueId = generateUniqueId();
              console.log(uniqueId);

              // Set the value of the base64 input to the unique ID
              // base64Input.value = uniqueId;

              // Prevent button default action
              e.preventDefault();

              // Validate form
              validator.validate().then(function (status) {
                if (status == "Valid") {
                  // console.log($(form).serializeArray());
                  // return;
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  const serializedArray = $(formSurvey)
                    .serializeArray()
                    .concat({
                      name: "bit64",
                      value: uniqueId,
                    });
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // /v1/projects/tasking.php
                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(response.message);

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila Semak <strong>Bilangan Ahli</strong> untuk kumpulan ini.",
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
          } else if (progress == 2) {
            let validator;

            validator = FormValidation.formValidation(formSurvey, {
              fields: {},
              plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                  rowSelector: ".fv-row",
                  eleInvalidClass: "", // comment to enable invalid state icons
                  eleValidClass: "", // comment to enable valid state icons
                }),
              },
            });

            // Function to retrieve data from the database using API
            function fetchDataFromAPI() {
              const serializedArray = $(formSurvey)
                .serializeArray()
                .concat({ name: "action", value: "1" });
              const formData = {};

              serializedArray.forEach((item) => {
                formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);

              // /v1/projects/tasking.php
              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  console.log(response);
                  console.log(response.data.showButton);
                  // Check the response and determine if the button should be displayed
                  if (response.data.showButton === "showButton") {
                    submitButton.classList.remove("d-none");
                  } else {
                    submitButton.classList.add("d-none");
                  }
                })
                .catch((error) => {
                  console.log(error);
                });
            }

            // fetchDataFromAPI();

            setInterval(fetchDataFromAPI, 5000);
            // setInterval(fetchDataFromAPI(), 5000);

            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Validate form
              validator.validate().then(function (status) {
                if (status == "Valid") {
                  // console.log($(form).serializeArray());
                  // return;
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  const serializedArray = $(formSurvey)
                    .serializeArray()
                    .concat({
                      name: "action",
                      value: "2",
                    });
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // /v1/projects/tasking.php
                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      // toastr.success("Daftar Masuk Berjaya! 🎉");

                      setTimeout(function () {
                        location.href = "tasks/new";
                      });
                    })
                    .catch((error) => {
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
                    });
                } else {
                }
              });
            });
          }
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-005") {
          let progressId = "#progress-" + modalId.slice(8);
          let progress = document.querySelector(progressId).value;
          console.log(progress);

          // clock-out = 3

          if (progress == 3) {
            let validator;
            let timePicker3 = "#time-picker3-" + modalId.slice(8);

            validator = FormValidation.formValidation(formSurvey, {
              fields: {},
              plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                  rowSelector: ".fv-row",
                  eleInvalidClass: "", // comment to enable invalid state icons
                  eleValidClass: "", // comment to enable valid state icons
                }),
              },
            });

            // Get the current date and time
            let now = new Date();

            // Define a function that updates the value of the time picker input every second
            function updateTime() {
              // Get the current time as HH:MM:SS (e.g. 09:30:00)
              let time = now.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
              });

              // Set the value of the time picker input to the current time
              document.querySelector(timePicker3).value = time;

              // Update the current date and time for the next second
              now = new Date();
            }

            // Call the updateTime function every second to update the time picker input
            setInterval(updateTime, 1000);

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(formSurvey).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success("Pengesahan Keluar Berjaya! 🎉");

                      setTimeout(function () {
                        location.href =
                          "surveys/site/progress/" + response.system_id;
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                }
              });
            });
          }
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-008") {
          let progressId = "#progress-" + modalId.slice(8);
          let progress = document.querySelector(progressId).value;
          console.log(progress);

          // upload = 4

          if (progress == 4) {
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                //simlulate AJAX
                // Upload files
                dropzone.processQueue();
              });
            });

            const statusId = formId.slice(6, 9);

            // Initialize Dropzone for the modal
            let dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
              url: `${apps}/api/tasks/${statusId}/upload`,
              paramName: "file",
              maxFiles: 5,
              maxFilesize: 1024,
              acceptedFiles: ".dwg,.acad", // DWG MIME type
              autoProcessQueue: false,
              addRemoveLinks: true,
              sending: function (file, xhr, formData) {
                formData.append(
                  "systemId",
                  document.querySelector(sysId).value
                );
                formData.append(
                  "folder",
                  document.querySelector(folderId).value
                );
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

              const serializedArray = $(formSurvey).serializeArray();
              const formData = {};

              serializedArray.forEach((item) => {
                formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);
              // /v1/projects/tasking.php
              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  // Hide loading indication
                  submitButton.removeAttribute("data-kt-indicator");

                  // Enable button
                  submitButton.disabled = false;

                  toastr.options = {
                    closeButton: false,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toastr-bottom-right",
                    preventDuplicates: true,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "2000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                  };

                  toastr.success(response.message);

                  setTimeout(function () {
                    location.href = "tasks/new";
                  }, 2500);
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
          }
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-009") {
          let validator;
          // Format options
          var optionFormat = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var img = item.element.getAttribute("data-profile-picture");
            var template = "";

            template +=
              '<img src="' +
              img +
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

          $(selectId).val(null).trigger("change");

          validator = FormValidation.formValidation(formSurvey, {
            fields: {
              "pelan-assign": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Pelukis Pelan",
                  },
                },
              },
              "modal-date-range": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Jangkaan Julat Tarikh Mula & Tamat",
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

          submitButton.classList.remove("d-none");
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

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success("Lantikan Pelukis Pelan Berjaya! 🎉");

                    setTimeout(function () {
                      location.href = "tasks/new";
                    }, 2500);
                  })
                  .catch((error) => {
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
                  });
              } else {
                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  html: "Maaf, Sila pilih <strong>Pelukis Pelan</strong> untuk permohonan ini.",
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

          const modalElement = document.querySelector(
            "#modal-date-range-" + modalId.slice(8)
          );
          flatpickrModal = $(modalElement).flatpickr({
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            mode: "range",
            onChange: function (selectedDates, dateStr, instance) {
              initConfig.handleFlatpickrModal(selectedDates, dateStr, instance);
            },
          });
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-010") {
          let udmId = "#udm-" + modalId.slice(8);
          let udm = document.querySelector(udmId).value;
          console.log(udm);
          let datePicker = "#date-picker-" + modalId.slice(8);

          if (udm == 1) {
            // Function to update the endorse_length field based on the difference between application_length and survey_length
            function updateEndorseLength() {
              // Get the values of application_length and survey_length
              var applicationLength = parseFloat(
                document.getElementById("jarak-projek").value
              );
              var surveyLength = parseFloat(
                document.getElementById("jarak-diukur").value
              );

              // Calculate the endorse_length
              var endorseLength = applicationLength - surveyLength;

              // Update the endorse_length input field with the calculated value
              document.getElementById("jarak-endorse").value = isNaN(
                endorseLength
              )
                ? ""
                : endorseLength;
            }

            // Attach an event listener to the survey_length input field to call the updateEndorseLength function when it changes
            document
              .getElementById("jarak-diukur")
              .addEventListener("input", updateEndorseLength);

            let validator;

            validator = FormValidation.formValidation(formSurvey, {
              fields: {
                "jarak-diukur": {
                  validators: {
                    notEmpty: {
                      message: "Sila Masukkan Jarak Diukur",
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

            // Get the current date
            let now = new Date();

            // Define a function that updates the value of the date picker input
            function updateDate() {
              // Get the current date as dd-mm-yyyy (e.g., 24-07-2023)
              let day = String(now.getDate()).padStart(2, "0");
              let month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-based
              let year = now.getFullYear();
              let date = day + "-" + month + "-" + year;

              // Set the value of the date picker input to the current date
              document.querySelector(datePicker).value = date;

              // Update the current date for the next iteration
              now = new Date();
            }

            // Call the updateDate function to set the initial value
            updateDate();

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(formSurvey).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(
                        "Pengesahan Pelan Infrastruktur Utiliti Berjaya! 🎉"
                      );

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila isi <strong>Jarak Diukur</strong> untuk permohonan ini.",
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
          }
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-011") {
          let udmId = "#udm-" + modalId.slice(8);
          let udm = document.querySelector(udmId).value;
          console.log(udm);

          if (udm == 2) {
            let statusId = formId.slice(6, 9);
            // PDF Dropzone
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // Send Axios POST request
                api
                  .post(`tasks/${statusId}/submit`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    // submitButton.removeAttribute("data-kt-indicator");
                    // Enable button
                    // submitButton.disabled = false;

                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");

                    // Disable button to avoid multiple clicks
                    submitButton.disabled = true;

                    // Upload files
                    pdfDropzone.processQueue();
                    dwgDropzone.processQueue();
                  })
                  .catch((error) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // Show error popup
                    Swal.fire({
                      text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, faham",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      allowOutsideClick: false,
                    });
                  });
              });
            });

            // Initialize PDF Dropzone for the modal
            let pdfDropzone = new Dropzone(
              $(modalId).find(".pdf-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: "application/pdf",
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            pdfDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            pdfDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              pdfDropzone.removeAllFiles();
              pdfDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            pdfDropzone.on("error", function (file, errorMessage) {
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

            // DWG Dropzone

            // Initialize DWG Dropzone for the modal
            let dwgDropzone = new Dropzone(
              $(modalId).find(".dwg-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: ".dwg,.acad", // DWG MIME type
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            dwgDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            dwgDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dwgDropzone.removeAllFiles();
              dwgDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            dwgDropzone.on("error", function (file, errorMessage) {
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
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-012") {
          let tmpId = "#tmp-" + modalId.slice(8);
          let tmp = document.querySelector(tmpId).value;
          console.log(tmp);
          let datePicker = "#date-picker-" + modalId.slice(8);

          if (tmp == 1) {
            let validator;

            validator = FormValidation.formValidation(formSurvey, {
              fields: {
                "tmp-remark": {
                  validators: {
                    notEmpty: {
                      message: "Sila Masukkan Catatan TMP",
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

            // Get the current date
            let now = new Date();

            // Define a function that updates the value of the date picker input
            function updateDate() {
              // Get the current date as dd-mm-yyyy (e.g., 24-07-2023)
              let day = String(now.getDate()).padStart(2, "0");
              let month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-based
              let year = now.getFullYear();
              let date = day + "-" + month + "-" + year;

              // Set the value of the date picker input to the current date
              document.querySelector(datePicker).value = date;

              // Update the current date for the next iteration
              now = new Date();
            }

            // Call the updateDate function to set the initial value
            updateDate();

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(formSurvey).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(
                        "Pengesahan Pelan Infrastruktur Utiliti Berjaya! 🎉"
                      );

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila isi <strong>Jarak Diukur</strong> untuk permohonan ini.",
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
          }
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-013") {
          let tmpId = "#tmp-" + modalId.slice(8);
          let tmp = document.querySelector(tmpId).value;
          console.log(tmp);

          if (tmp == 2) {
            let statusId = formId.slice(6, 9);
            // PDF Dropzone
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // Send Axios POST request
                api
                  .post(`tasks/${statusId}/submit`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    // submitButton.removeAttribute("data-kt-indicator");
                    // Enable button
                    // submitButton.disabled = false;

                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");

                    // Disable button to avoid multiple clicks
                    submitButton.disabled = true;

                    // Upload files
                    pdfDropzone.processQueue();
                    dwgDropzone.processQueue();
                  })
                  .catch((error) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // Show error popup
                    Swal.fire({
                      text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, faham",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      allowOutsideClick: false,
                    });
                  });
              });
            });

            // Initialize PDF Dropzone for the modal
            let pdfDropzone = new Dropzone(
              $(modalId).find(".pdf-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: "application/pdf",
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            pdfDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            pdfDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              pdfDropzone.removeAllFiles();
              pdfDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            pdfDropzone.on("error", function (file, errorMessage) {
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

            // DWG Dropzone

            // Initialize DWG Dropzone for the modal
            let dwgDropzone = new Dropzone(
              $(modalId).find(".dwg-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: ".dwg,.acad", // DWG MIME type
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            dwgDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            dwgDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dwgDropzone.removeAllFiles();
              dwgDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            dwgDropzone.on("error", function (file, errorMessage) {
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
        } else if (formSurveyId.slice(0, 15) === "#formSurvey-014") {
          let datePicker = "#date-picker-" + modalId.slice(8);

          let validator;

          validator = FormValidation.formValidation(formSurvey, {
            fields: {
              "handover-remark": {
                validators: {
                  notEmpty: {
                    message: "Sila Masukkan Catatan Serahan",
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

          // Get the current date
          let now = new Date();

          // Define a function that updates the value of the date picker input
          function updateDate() {
            // Get the current date as dd-mm-yyyy (e.g., 24-07-2023)
            let day = String(now.getDate()).padStart(2, "0");
            let month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-based
            let year = now.getFullYear();
            let date = day + "-" + month + "-" + year;

            // Set the value of the date picker input to the current date
            document.querySelector(datePicker).value = date;

            // Update the current date for the next iteration
            now = new Date();
          }

          // Call the updateDate function to set the initial value
          updateDate();

          submitButton.classList.remove("d-none");
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

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success("Pengesahan Serahan PIU & PPT Berjaya! 🎉");

                    setTimeout(function () {
                      location.href = "tasks/new";
                    }, 2500);
                  })
                  .catch((error) => {
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
                  });
              } else {
                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  html: "Maaf, Sila isi <strong>Catatan</strong> untuk pengesahan ini.",
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
        }
      } else {

          if (formId.slice(0, 9) === "#form-002") {
            let payment_method = document.querySelector('input[name="payment_method"]').value;

            //paymentMethod : 1 = transfer; 2 = Invoice; 3 = DuitNow
            if(payment_method === '1' || payment_method === '3'){
                $(modalId).on("shown.bs.modal", function () {
                  let validator = FormValidation.formValidation(form, {
                    fields: {
                      "total-wop": {
                        validators: {
                          regexp: {
                            regexp: /^\d+(\.\d+)?$/,
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

                  // Call the function with the appropriate status ID
                  handleActionSubmit(
                    formId.slice(6, 9),
                    form,
                    submitButton,
                    validator,
                    dropzone,
                    modalId,
                    sysId,
                    folderId
                  );
                });

                // Remove files when the modal is closed
                $(modalId).on("hidden.bs.modal", function () {
                  dropzone.removeAllFiles();
                  dropzone.destroy();
                  submitButton.classList.add("d-none");
                });
            } else{
                  $(modalId).on("shown.bs.modal", function () {
                    // console.log(folderId);
                    // console.log(sysId);exit;

                    // Call the function with the appropriate status ID
                    handleActionSubmit(
                      formId.slice(6, 9),
                      form,
                      submitButton,
                      null,
                      dropzone,
                      modalId,
                      sysId,
                      folderId
                    );
                  });

                  $(modalId).on("hidden.bs.modal", function () {
                    dropzone.removeAllFiles();
                    dropzone.destroy();
                    submitButton.classList.add("d-none");
                  });

            }

          } else if (formId.slice(0, 9) === "#form-004") {
            let form = document.querySelector(formId);
            // Format options
            var optionFormat = function (item) {
              if (!item.id) {
                return item.text;
              }

            var span = document.createElement("span");
            var imgUrl = item.element.getAttribute("data-profile-picture");
            var template = "";

            template += `<img src=${imgUrl} class="rounded-circle h-30px me-2" alt="image"/>`;
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

          // Add change event handler to the select element
          $(selectId).on("change", function () {
            var selectedValue = $(this).val();

            // Check if a selection is made (selectedValue is not null or an empty array)
            if (selectedValue !== null && selectedValue.length > 0) {
              // If a selection is made, remove the 'd-none' class from the submitButton
              submitButton.classList.remove("d-none");
            } else {
              // If no selection is made, add the 'd-none' class to the submitButton
              submitButton.classList.add("d-none");
            }
          });

          $(selectId).val(null).trigger("change");

          let validator = FormValidation.formValidation(form, {
            fields: {
              "gis-assign": {
                validators: {
                  notEmpty: {
                    message: "Sila pilih Juruteknik GIS",
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

          handleActionSubmit(
            formId.slice(6, 9),
            form,
            submitButton,
            validator,
            dropzone,
            modalId,
            sysId,
            folderId
          );
        } else if (formId.slice(0, 9) === "#form-005") {
          $(modalId).on("shown.bs.modal", function () {
            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              null,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });
        } else if (formId.slice(0, 9) === "#form-029") {
          // console.log("userRole :" +role);
          if (role == 33) {
            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          } else {
            $(modalId).on("shown.bs.modal", function () {
              let form = document.querySelector(formId);
              let validator = FormValidation.formValidation(form, {
                fields: {
                  "total-quote": {
                    validators: {
                      regexp: {
                        regexp: /^\d+(\.\d+)?$/,
                        message: "Sila isi angka digit sahaja",
                      },
                      notEmpty: {
                        message: "Amaun Sebut Harga diperlukan",
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

              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                validator,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          }
        } else if (formId.slice(0, 9) === "#form-043") {
          $(modalId).on("shown.bs.modal", function () {
            let form = document.querySelector(formId);
            let validator;

            // Format options
            var optionFormat = function (item) {
              if (!item.id) {
                return item.text;
              }

              var span = document.createElement("span");
              var img = item.element.getAttribute("data-profile-picture");
              var template = "";

              template +=
                '<img src="' +
                img +
                '" class="rounded-circle h-30px me-2" alt="image"/>';
              template += item.text;

              span.innerHTML = template;

              return $(span);
            };

            $(document).ready(function () {
              // Update second selection options when the first selection changes
              $(selectId).on("change", function () {
                var firstSelection = $(this).val();

                // Retrieve options for the second selection
                $.ajax({
                  url: apps + "/api/tasks/lists",
                  method: "POST",
                  data: {
                    survey_team: firstSelection,
                  },
                  success: function (response) {
                    console.log("Response received:", response.teamMembers);

                    // Split the response into separate values
                    var values = response.teamMembers.split(",");

                    // Clear the second selection
                    $(selectId2).empty();

                    // Add each value as an option to the second selection
                    values.forEach(function (value) {
                      var profilePicture = response.memberImg
                        ? "assets/media/avatars/" + response.memberImg + ".jpg"
                        : "assets/media/avatars/blank.jpg"; // Conditional check

                      $(selectId2).append(
                        '<option value="' +
                          value.trim() +
                          '" data-profile-picture="' +
                          profilePicture +
                          '">' +
                          value.trim() +
                          "</option>"
                      );
                    });
                  },
                });
              });
            });

            // Init Select2 --- more info: https://select2.org/
            $(selectId).select2({
              minimumResultsForSearch: Infinity,
              templateSelection: optionFormat,
              templateResult: optionFormat,
            });

            $(selectId).val(null).trigger("change");

            // Init Select2 --- more info: https://select2.org/
            $(selectId2).select2({
              minimumResultsForSearch: Infinity,
              templateSelection: optionFormat,
              templateResult: optionFormat,
            });

            $(selectId2).val(null).trigger("change");

            validator = FormValidation.formValidation(form, {
              fields: {
                "survey-team-assign": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Kumpulan Ukur",
                    },
                  },
                },
                "survey-leader-assign": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Ketua Kumpulan Ukur",
                    },
                  },
                },
                "modal-date-range": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Jangkaan Julat Tarikh Mula & Tamat",
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

            submitButton.classList.remove("d-none");
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
                    url: apps + "/api/tasks/lists",
                    type: "POST",
                    data: $(form).serializeArray(),
                    success: function (r) {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success("Lantikan Kumpulan Survey Berjaya! 🎉");

                      setTimeout(function () {
                        location.href = "/tasks/new";
                      }, 2500);
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
                    html: "Maaf, Sila pilih <strong>Kumpulan Survey</strong> untuk permohonan ini.",
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
          });

          const modalElement = document.querySelector(
            "#modal-date-range-" + modalId.slice(8)
          );
          flatpickrModal = $(modalElement).flatpickr({
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            mode: "range",
            onChange: function (selectedDates, dateStr, instance) {
              initConfig.handleFlatpickrModal(selectedDates, dateStr, instance);
            },
          });
        } else if (formId.slice(0, 9) === "#form-061") {
          let form = document.querySelector(formId);
          let validator;
          let timePicker = "#time-picker-" + modalId.slice(8);

          // Format options
          var optionFormat = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var iconClass = item.element.getAttribute("data-icon-class");
            var template = "";

            template += '<i class="' + iconClass + '"></i>';
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

          $(selectId).val(null).trigger("change");

          // Format options
          var optionFormat2 = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var iconClass = item.element.getAttribute("data-icon-class");
            var template = "";

            template += '<i class="' + iconClass + '"></i>';
            template += item.text;

            span.innerHTML = template;

            return $(span);
          };

          // Init Select2 --- more info: https://select2.org/
          $(selectId2).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat2,
            templateResult: optionFormat2,
          });

          $(selectId2).val("3").trigger("change");

          validator = FormValidation.formValidation(form, {
            fields: {
              "number-team": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Bilangan Ahli Kumpulan",
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

          // Get the current date and time
          let now = new Date();

          // Define a function that updates the value of the time picker input every second
          function updateTime() {
            // Get the current time as HH:MM:SS (e.g. 09:30:00)
            let time = now.toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
              second: "2-digit",
            });

            // Set the value of the time picker input to the current time
            document.querySelector(timePicker).value = time;

            // Update the current date and time for the next second
            now = new Date();
          }

          // Call the updateTime function every second to update the time picker input
          setInterval(updateTime, 1000);

          function generateUniqueId() {
            const randomBytes = new Uint8Array(64);
            crypto.getRandomValues(randomBytes);
            return Array.from(randomBytes, (byte) =>
              ("0" + byte.toString(16)).slice(-2)
            ).join("");
          }

          const base64Input = document.getElementById("bit64");

          // Handle form submit
          submitButton.classList.remove("d-none");
          // Handle form submit
          submitButton.addEventListener("click", function (e) {
            // Add an event listener to the submit button that gets the current time and stores it in a hidden input field
            // document.querySelector("#submit-button").addEventListener("click", function() {
            // let now = new Date();
            // let timestamp = now.toISOString();
            // document.querySelector("#hidden-timestamp-input").value = timestamp;
            // });

            // Generate unique ID
            const uniqueId = generateUniqueId();
            console.log(uniqueId);

            // Set the value of the base64 input to the unique ID
            base64Input.value = uniqueId;

            // Prevent button default action
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
              if (status == "Valid") {
                // console.log($(form).serializeArray());
                // return;
                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(form).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // /v1/projects/tasking.php
                // Send Axios POST request
                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success(response.message);

                    setTimeout(function () {
                      location.href = "tasks/new";
                    }, 2500);
                  })
                  .catch((error) => {
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
                  });
              } else {
                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  html: "Maaf, Sila Semak <strong>Bilangan Ahli</strong> untuk kumpulan ini.",
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
        } else if (formId.slice(0, 9) === "#form-063") {
          let form = document.querySelector(formId);
          let validator;
          // Format options
          var optionFormat = function (item) {
            if (!item.id) {
              return item.text;
            }

            var span = document.createElement("span");
            var img = item.element.getAttribute("data-profile-picture");
            var template = "";

            template +=
              '<img src="' +
              img +
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

          $(selectId).val(null).trigger("change");

          validator = FormValidation.formValidation(form, {
            fields: {
              "pelan-assign": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Pelukis Pelan",
                  },
                },
              },
              "modal-date-range": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Jangkaan Julat Tarikh Mula & Tamat",
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

          submitButton.classList.remove("d-none");
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

                const serializedArray = $(form).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success("Lantikan Pelukis Pelan Berjaya! 🎉");

                    setTimeout(function () {
                      location.href = "tasks/new";
                    }, 2500);
                  })
                  .catch((error) => {
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
                  });
              } else {
                // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                  html: "Maaf, Sila pilih <strong>Pelukis Pelan</strong> untuk permohonan ini.",
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

          const modalElement = document.querySelector(
            "#modal-date-range-" + modalId.slice(8)
          );
          flatpickrModal = $(modalElement).flatpickr({
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            mode: "range",
            onChange: function (selectedDates, dateStr, instance) {
              initConfig.handleFlatpickrModal(selectedDates, dateStr, instance);
            },
          });
        } else if (formId.slice(0, 8) === "#form-001") {
          $(modalId).on("shown.bs.modal", function () {
            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Show loading indication
              submitButton.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              submitButton.disabled = true;

              //simlulate AJAX
              // Upload files
              dropzone.processQueue();
            });
          });

          // Initialize Dropzone for the modal
          let dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
            url: apps + "/api/projects/upload.php",
            paramName: "file",
            maxFiles: 5,
            maxFilesize: 1024,
            acceptedFiles: "application/pdf",
            autoProcessQueue: false,
            addRemoveLinks: true,
            sending: function (file, xhr, formData) {
              formData.append("systemId", document.querySelector(sId).value);
              formData.append("folder", document.querySelector(fId).value);
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

            toastr.options = {
              closeButton: false,
              debug: false,
              newestOnTop: false,
              progressBar: false,
              positionClass: "toastr-bottom-right",
              preventDuplicates: true,
              onclick: null,
              showDuration: "300",
              hideDuration: "1000",
              timeOut: "2000",
              extendedTimeOut: "1000",
              showEasing: "swing",
              hideEasing: "linear",
              showMethod: "fadeIn",
              hideMethod: "fadeOut",
            };

            toastr.success("Kerja Lapangan Berjaya Dimuatnaik! 🎉");

            setTimeout(function () {
              location.href = "/tasks/new";
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
        } else if (formId.slice(0, 9) === "#form-071") {
          let udmId = "#udm-" + modalId.slice(8);
          let udm = document.querySelector(udmId).value;
          console.log(udm);
          let datePicker = "#date-picker-" + modalId.slice(8);

          if (udm == 1) {
            // Function to update the endorse_length field based on the difference between application_length and survey_length
            function updateEndorseLength() {
              // Get the values of application_length and survey_length
              var applicationLength = parseFloat(
                document.getElementById("jarak-projek").value
              );
              var surveyLength = parseFloat(
                document.getElementById("jarak-diukur").value
              );

              // Calculate the endorse_length
              var endorseLength = applicationLength - surveyLength;

              // Update the endorse_length input field with the calculated value
              document.getElementById("jarak-endorse").value = isNaN(
                endorseLength
              )
                ? ""
                : endorseLength;
            }

            // Attach an event listener to the survey_length input field to call the updateEndorseLength function when it changes
            document
              .getElementById("jarak-diukur")
              .addEventListener("input", updateEndorseLength);

            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "jarak-diukur": {
                  validators: {
                    notEmpty: {
                      message: "Sila Masukkan Jarak Diukur",
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

            // Get the current date
            let now = new Date();

            // Define a function that updates the value of the date picker input
            function updateDate() {
              // Get the current date as dd-mm-yyyy (e.g., 24-07-2023)
              let day = String(now.getDate()).padStart(2, "0");
              let month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-based
              let year = now.getFullYear();
              let date = day + "-" + month + "-" + year;

              // Set the value of the date picker input to the current date
              document.querySelector(datePicker).value = date;

              // Update the current date for the next iteration
              now = new Date();
            }

            // Call the updateDate function to set the initial value
            updateDate();

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(form).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(
                        "Pengesahan Pelan Infrastruktur Utiliti Berjaya! 🎉"
                      );

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila isi <strong>Jarak Diukur</strong> untuk permohonan ini.",
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
          } else if (udm == 2) {
            let statusId = formId.slice(6, 9);
            // PDF Dropzone
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(form).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // Send Axios POST request
                api
                  .post(`tasks/${statusId}/submit`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    // submitButton.removeAttribute("data-kt-indicator");
                    // Enable button
                    // submitButton.disabled = false;

                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");

                    // Disable button to avoid multiple clicks
                    submitButton.disabled = true;

                    // Upload files
                    pdfDropzone.processQueue();
                    dwgDropzone.processQueue();
                  })
                  .catch((error) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // Show error popup
                    Swal.fire({
                      text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, faham",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      allowOutsideClick: false,
                    });
                  });
              });
            });

            // Initialize PDF Dropzone for the modal
            let pdfDropzone = new Dropzone(
              $(modalId).find(".pdf-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: "application/pdf",
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            pdfDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            pdfDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              pdfDropzone.removeAllFiles();
              pdfDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            pdfDropzone.on("error", function (file, errorMessage) {
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

            // DWG Dropzone

            // Initialize DWG Dropzone for the modal
            let dwgDropzone = new Dropzone(
              $(modalId).find(".dwg-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: ".dwg,.acad", // DWG MIME type
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            dwgDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            dwgDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dwgDropzone.removeAllFiles();
              dwgDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            dwgDropzone.on("error", function (file, errorMessage) {
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
        } else if (formId.slice(0, 9) === "#form-074") {
          let tmpId = "#tmp-" + modalId.slice(8);
          let tmp = document.querySelector(tmpId).value;
          console.log(tmp);
          let datePicker = "#date-picker-" + modalId.slice(8);

          if (tmp == 1) {
            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "tmp-remark": {
                  validators: {
                    notEmpty: {
                      message: "Sila Masukkan Catatan TMP",
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

            // Get the current date
            let now = new Date();

            // Define a function that updates the value of the date picker input
            function updateDate() {
              // Get the current date as dd-mm-yyyy (e.g., 24-07-2023)
              let day = String(now.getDate()).padStart(2, "0");
              let month = String(now.getMonth() + 1).padStart(2, "0"); // Months are zero-based
              let year = now.getFullYear();
              let date = day + "-" + month + "-" + year;

              // Set the value of the date picker input to the current date
              document.querySelector(datePicker).value = date;

              // Update the current date for the next iteration
              now = new Date();
            }

            // Call the updateDate function to set the initial value
            updateDate();

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(form).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(
                        "Pengesahan Pelan Infrastruktur Utiliti Berjaya! 🎉"
                      );

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila isi <strong>Jarak Diukur</strong> untuk permohonan ini.",
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
          } else if (tmp == 2) {
            let statusId = formId.slice(6, 9);
            // PDF Dropzone
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const serializedArray = $(form).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                const jsonData = JSON.stringify(formData);

                // Send Axios POST request
                api
                  .post(`tasks/${statusId}/submit`, jsonData)
                  .then((response) => {
                    // Hide loading indication
                    // submitButton.removeAttribute("data-kt-indicator");
                    // Enable button
                    // submitButton.disabled = false;

                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");

                    // Disable button to avoid multiple clicks
                    submitButton.disabled = true;

                    // Upload files
                    pdfDropzone.processQueue();
                    dwgDropzone.processQueue();
                  })
                  .catch((error) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // Show error popup
                    Swal.fire({
                      text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, faham",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      allowOutsideClick: false,
                    });
                  });
              });
            });

            // Initialize PDF Dropzone for the modal
            let pdfDropzone = new Dropzone(
              $(modalId).find(".pdf-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: "application/pdf",
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            pdfDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            pdfDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              pdfDropzone.removeAllFiles();
              pdfDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            pdfDropzone.on("error", function (file, errorMessage) {
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

            // DWG Dropzone

            // Initialize DWG Dropzone for the modal
            let dwgDropzone = new Dropzone(
              $(modalId).find(".dwg-dropzone")[0],
              {
                url: `${apps}/api/tasks/${statusId}/upload`,
                paramName: "file",
                maxFiles: 1,
                maxFilesize: 1024,
                acceptedFiles: ".dwg,.acad", // DWG MIME type
                autoProcessQueue: false,
                addRemoveLinks: true,
                sending: function (file, xhr, formData) {
                  formData.append(
                    "systemId",
                    document.querySelector(sysId).value
                  );
                  formData.append(
                    "folder",
                    document.querySelector(folderId).value
                  );
                },
                accept: function (file, done) {
                  done();
                },
              }
            );

            dwgDropzone.on("success", function (file, response) {
              submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                location.href = "/tasks/new";
              }, 2500);
            });

            dwgDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dwgDropzone.removeAllFiles();
              dwgDropzone.destroy();
              submitButton.classList.add("d-none");
            });

            dwgDropzone.on("error", function (file, errorMessage) {
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
        } else if (formId.slice(0, 9) === "#form-062") {
          let progressId = "#progress-" + modalId.slice(8);
          let progress = document.querySelector(progressId).value;
          console.log(progress);
          let timePicker2 = "#time-picker2-" + modalId.slice(8);

          // validate attendance = 1
          // qr-code = 2
          // clock-out = 3
          // upload = 4

          if (progress == 1) {
            let form = document.querySelector(formId);
            let validator;

            // Format options
            var optionFormat = function (item) {
              if (!item.id) {
                return item.text;
              }

              var span = document.createElement("span");
              var iconClass = item.element.getAttribute("data-icon-class");
              var template = "";

              template += '<i class="' + iconClass + '"></i>';
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

            $(selectId).val(null).trigger("change");

            // Format options
            var optionFormat2 = function (item) {
              if (!item.id) {
                return item.text;
              }

              var span = document.createElement("span");
              var iconClass = item.element.getAttribute("data-icon-class");
              var template = "";

              template += '<i class="' + iconClass + '"></i>';
              template += item.text;

              span.innerHTML = template;

              return $(span);
            };

            // Init Select2 --- more info: https://select2.org/
            $(selectId2).select2({
              minimumResultsForSearch: Infinity,
              templateSelection: optionFormat2,
              templateResult: optionFormat2,
            });

            $(selectId2).val("3").trigger("change");

            validator = FormValidation.formValidation(form, {
              fields: {
                "number-team": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Bilangan Ahli Kumpulan",
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

            // Get the current date and time
            let now = new Date();

            // Define a function that updates the value of the time picker input every second
            function updateTime() {
              // Get the current time as HH:MM:SS (e.g. 09:30:00)
              let time = now.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
              });

              // Set the value of the time picker input to the current time
              document.querySelector(timePicker2).value = time;

              // Update the current date and time for the next second
              now = new Date();
            }

            // Call the updateTime function every second to update the time picker input
            setInterval(updateTime, 1000);

            function generateUniqueId() {
              const randomBytes = new Uint8Array(64);
              crypto.getRandomValues(randomBytes);
              return Array.from(randomBytes, (byte) =>
                ("0" + byte.toString(16)).slice(-2)
              ).join("");
            }

            // const base64Input = document.getElementById("bit64");

            // Handle form submit
            submitButton.classList.remove("d-none");
            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Add an event listener to the submit button that gets the current time and stores it in a hidden input field
              // document.querySelector("#submit-button").addEventListener("click", function() {
              // let now = new Date();
              // let timestamp = now.toISOString();
              // document.querySelector("#hidden-timestamp-input").value = timestamp;
              // });

              // Generate unique ID
              const uniqueId = generateUniqueId();
              console.log(uniqueId);

              // Set the value of the base64 input to the unique ID
              // base64Input.value = uniqueId;

              // Prevent button default action
              e.preventDefault();

              // Validate form
              validator.validate().then(function (status) {
                if (status == "Valid") {
                  // console.log($(form).serializeArray());
                  // return;
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  const serializedArray = $(form).serializeArray().concat({
                    name: "bit64",
                    value: uniqueId,
                  });
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // /v1/projects/tasking.php
                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(response.message);

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila Semak <strong>Bilangan Ahli</strong> untuk kumpulan ini.",
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
          } else if (progress == 2) {
            let form = document.querySelector(formId);
            let validator;
            // let submitIdQr = "#submit-" + modalId.slice(8);
            // let submitButton = document.querySelector(submitIdQr);
            // let submitButton = document.querySelector('#submit-' + modalId.slice(8) + ' button[type="submit"]');

            validator = FormValidation.formValidation(form, {
              fields: {},
              plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                  rowSelector: ".fv-row",
                  eleInvalidClass: "", // comment to enable invalid state icons
                  eleValidClass: "", // comment to enable valid state icons
                }),
              },
            });

            // Function to retrieve data from the database using API
            function fetchDataFromAPI() {
              const serializedArray = $(form)
                .serializeArray()
                .concat({ name: "action", value: "1" });
              const formData = {};

              serializedArray.forEach((item) => {
                formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);

              // /v1/projects/tasking.php
              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  console.log(response);
                  console.log(response.data.showButton);
                  // Check the response and determine if the button should be displayed
                  if (response.data.showButton === "showButton") {
                    submitButton.classList.remove("d-none");
                  } else {
                    submitButton.classList.add("d-none");
                  }
                })
                .catch((error) => {
                  console.log(error);
                });
            }

            // fetchDataFromAPI();

            setInterval(fetchDataFromAPI, 5000);
            // setInterval(fetchDataFromAPI(), 5000);

            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Validate form
              validator.validate().then(function (status) {
                if (status == "Valid") {
                  // console.log($(form).serializeArray());
                  // return;
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  const serializedArray = $(form).serializeArray().concat({
                    name: "action",
                    value: "2",
                  });
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // /v1/projects/tasking.php
                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      // toastr.success("Daftar Masuk Berjaya! 🎉");

                      setTimeout(function () {
                        location.href = "tasks/new";
                      });
                    })
                    .catch((error) => {
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
                    });
                } else {
                }
              });
            });
          } else if (progress == 3) {
            let form = document.querySelector(formId);
            let validator;
            let timePicker3 = "#time-picker3-" + modalId.slice(8);

            validator = FormValidation.formValidation(form, {
              fields: {},
              plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                  rowSelector: ".fv-row",
                  eleInvalidClass: "", // comment to enable invalid state icons
                  eleValidClass: "", // comment to enable valid state icons
                }),
              },
            });

            // Get the current date and time
            let now = new Date();

            // Define a function that updates the value of the time picker input every second
            function updateTime() {
              // Get the current time as HH:MM:SS (e.g. 09:30:00)
              let time = now.toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
              });

              // Set the value of the time picker input to the current time
              document.querySelector(timePicker3).value = time;

              // Update the current date and time for the next second
              now = new Date();
            }

            // Call the updateTime function every second to update the time picker input
            setInterval(updateTime, 1000);

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(form).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success("Pengesahan Keluar Berjaya! 🎉");

                      setTimeout(function () {
                        location.href =
                          "surveys/site/progress/" + response.system_id;
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                }
              });
            });
          } else if (progress == 4) {
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                //simlulate AJAX
                // Upload files
                dropzone.processQueue();
              });
            });

            const statusId = formId.slice(6, 9);

            // Initialize Dropzone for the modal
            let dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
              url: `${apps}/api/tasks/${statusId}/upload`,
              paramName: "file",
              maxFiles: 5,
              maxFilesize: 1024,
              acceptedFiles: ".dwg,.acad", // DWG MIME type
              autoProcessQueue: false,
              addRemoveLinks: true,
              sending: function (file, xhr, formData) {
                formData.append(
                  "systemId",
                  document.querySelector(sysId).value
                );
                formData.append(
                  "folder",
                  document.querySelector(folderId).value
                );
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

              const serializedArray = $(form).serializeArray();
              const formData = {};

              serializedArray.forEach((item) => {
                formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);
              // /v1/projects/tasking.php
              // Send Axios POST request
              api
                .post(`surveys/tasking`, jsonData)
                .then((response) => {
                  // Hide loading indication
                  submitButton.removeAttribute("data-kt-indicator");

                  Swal.fire({
                    text: "Kerja Lapangan telah berjaya disimpan!",
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
                      location.href = "tasks/new";
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
          } else {
            console.log("error in progress status.");
          }
        } else if (formId.slice(0, 9) === "#form-031") {
          if (role == 33) {
            $(modalId).on("shown.bs.modal", function () {
              let form = document.querySelector(formId);
              let validator;

              validator = FormValidation.formValidation(form, {
                fields: {
                  "quote-verify-notes": {
                    validators: {
                      notEmpty: {
                        message: "Catatan diperlukan",
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

              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                validator,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          }
        } else if (formId.slice(0, 9) === "#form-012") {
          $(modalId).on("shown.bs.modal", function () {
            let lId = "#listing-" + modalId.slice(8);
            let aId = "#authority-" + modalId.slice(8);
            let rId = "#report-" + modalId.slice(8);

            let form = document.querySelector(formId);
            let validator;

            $(selectId).select2({
              placeholder: "Sila pilih jenis laporan",
            });

            $(selectId).on("change", function () {
              var validValues = ["1", "2", "3"];
              if (validValues.includes($(this).val())) {
                // console.log($(form).serializeArray());return;
                console.log("changed!");

                $.ajax({
                  url: apps + "/v1/reports/",
                  method: "POST",
                  data: $(form).serializeArray(),
                  success: function (r) {
                    console.log(r);
                    $(rId).val(r.data.reportNumber);
                    submitButton.classList.remove("d-none");

                    // Parse authID string into array of integers
                    var authIDs = r.data.authID.match(/\d+/g).map(Number);

                    // Loop through select options and set selected attribute as needed
                    var select = $(lId);
                    var options = select.find("option");
                    for (var i = 0; i < options.length; i++) {
                      var value = parseInt(options[i].value);
                      if (authIDs.includes(value)) {
                        options[i].setAttribute("selected", true);
                      }
                    }

                    // Initialize Select2 with multiple selection enabled
                    $(lId).select2({
                      placeholder: "Sila pilih satu atau lebih pihak berkuasa",
                      allowClear: true,
                      multiple: true,
                      minimumInputLength: 3, // Change the minimum input length to 3
                      language: {
                        inputTooShort: function (args) {
                          var remainingChars = args.minimum - args.input.length;
                          return (
                            "Taip sekurang-kurangnya  " +
                            remainingChars +
                            " aksara lagi"
                          );
                        },
                      },
                    });

                    $(aId).removeClass("d-none");
                  },
                  error: function (error) {
                    console.log(error);
                  },
                });
              } else {
                submitButton.classList.add("d-none");
                console.log("unchanged!");
              }
            });

            validator = FormValidation.formValidation(form, {
              fields: {
                "report-no": {
                  validators: {
                    notEmpty: {
                      message: "No Rujukan Laporan diperlukan",
                    },
                    regexp: {
                      regexp:
                        /^(KUP|KUTT|KUDR)\/LT\/[1-3]\/2[0-9][2-9][0-9]\/[0-9][0-9][0-9]\/(0[1-9]|[1-9][0-9])$/,
                      message:
                        "Sila semak no rujukan laporan anda. Pastikan anda mengikut format yang dinyatakan",
                    },
                  },
                },
                authority: {
                  validators: {
                    notEmpty: {
                      message: "Sila pilih berkuasa yang terlibat",
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

            submitButton.classList.remove("d-none");
            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Validate form
              validator.validate().then(function (status) {
                if (status == "Valid") {
                  // console.log($(form).serializeArray());return;
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  $.ajax({
                    url: apps + "/api/tasks/lists",
                    type: "POST",
                    data: $(form).serializeArray(),
                    success: function (r) {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success(
                        "No Rujukan Laporan Lawatan Tapak Berjaya Dicipta! 🎉"
                      );

                      setTimeout(function () {
                        location.href =
                          "/projects/site/list.php?sid=" +
                          r.data.systemID +
                          "&ref=" +
                          r.data.reportID;
                      }, 2500);
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
                    html: "Maaf, Sila penuhi butiran yang dikehendaki.",
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
          });
        } else if (formId.slice(0, 9) === "#form-048") {
          $(modalId).on("shown.bs.modal", function () {
            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "total-icpp": {
                  validators: {
                    regexp: {
                      regexp: /^\d+(\.\d+)?$/,
                      message: "Sila isi angka digit sahaja",
                    },
                    notEmpty: {
                      message: "Amaun Bayaran Invois diperlukan",
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

            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              validator,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });

            //new status 51
        } else if (formId.slice(0, 9) === "#form-051") {
          $(modalId).on("shown.bs.modal", function () {
            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "total-rcpp": {
                  validators: {
                    regexp: {
                      regexp: /^\d+(\.\d+)?$/,
                      message: "Sila isi angka digit sahaja",
                    },
                    notEmpty: {
                      message: "Amaun Bayaran Invois diperlukan",
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

            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              validator,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });

            //old status 51
        } else if (formId.slice(0, 9) === "#form-00051") {
          // console.log(role);
          if (role === 41 || role === 42 || role === 43 || role === 44) {
            // Code for role 41,42,43,44
            let item_42 = document.querySelector('input[name="item_42"]').value;

            if (item_42 === "wyFeedback-ucidos") {
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
                $("#selection-" + modalId.slice(8)).on("change", function () {
                  var contact = $(this).find(":selected").data("contact");
                  $(staff_contact).val(contact);
                });
                $("#selection2-" + modalId.slice(8)).on("change", function () {
                  var contact2 = $(this).find(":selected").data("contact");
                  $(staff_contact_2).val(contact2);
                });
                $("#selection3-" + modalId.slice(8)).on("change", function () {
                  var position = $(this).find(":selected").data("position");
                  $(approval_position).val(position);
                });
              });

              //stepper
              var stepperModal2 = "#kt_stepper_mbkil_" + modalId.slice(8);
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

                    if (status == "Valid") {
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
                console.log("stepper.previous");
                stepper.goPrevious();
              });

              let formId = "#form-" + modalId.slice(8);
              let form = document.querySelector(formId);

              let validations = [];

              // provider
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    addr_provider_1: {
                      validators: {
                        notEmpty: {
                          message: "Sila Isi Alamat",
                        },
                      },
                    },
                    up_provider: {
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
                })
              );

              // client
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    addr_client_1: {
                      validators: {
                        notEmpty: {
                          message: "Sila Isi Alamat",
                        },
                      },
                    },
                    up_pemohon: {
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
                })
              );

              // test
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    jumlah: {
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
                })
              );

              // surat
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    client_ref_no: {
                      validators: {
                        notEmpty: {
                          message: "Sila Isi No Rujukan Tuan",
                        },
                      },
                    },
                    staff_name: {
                      validators: {
                        notEmpty: {
                          message: "Sila Pilih Pegawai 1 Untuk Dihubungi",
                        },
                      },
                    },
                    staff_name_2: {
                      validators: {
                        notEmpty: {
                          message: "Sila Pilih Pegawai 2 Untuk Dihubungi",
                        },
                      },
                    },
                    approval_by: {
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
                })
              );

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

                    serializedArray.forEach((item) => {
                      formData[item.name] = item.value;
                    });

                    api
                      .post(
                        "projects/wayleaves/tasks",
                        JSON.stringify(formData)
                      )
                      .then((response) => {
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
                              // location.href = "/projects/wayleave/wyFeedback/"+response.sysid+"/"+letterRefNo+"/"+response.id;
                              // location.href = "/projects/wayleave/wyFeedback/"+sysId+"/"+letterRefNo+"/"+response.id;
                              location.href = `/projects/wayleave/wyFeedback/${response.sysid}/${response.id}`;
                            }, 2500);
                          }
                        });
                      })
                      .catch((error) => {
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
            } else if (
              item_42 === "wyFeedback-kiter" ||
              item_42 === "wyFeedback-kudr" ||
              item_42 === "wyFeedback-kuk"
            ) {
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
                $("#selection-" + modalId.slice(8)).on("change", function () {
                  var contact = $(this).find(":selected").data("contact");
                  $(staff_contact).val(contact);
                });
                $("#selection3-" + modalId.slice(8)).on("change", function () {
                  var position = $(this).find(":selected").data("position");
                  $(approval_position).val(position);
                });
              });

              let invDate = "#inv-date-" + modalId.slice(8);
              let invoiceDate = $(invDate).flatpickr({
                locale: "ms",
                altInput: true,
                defaultDate: "today",
                altFormat: "j F Y",
                dateFormat: "Y-m-d",
                // static: true,
              });

              //stepper
              var stepperModal2 = "#kt_stepper_mbkil_" + modalId.slice(8);
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

                    if (status == "Valid") {
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
                console.log("stepper.previous");
                stepper.goPrevious();
              });

              let formId = "#form-" + modalId.slice(8);
              let form = document.querySelector(formId);

              let validations = [];

              // provider
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    addr_provider_1: {
                      validators: {
                        notEmpty: {
                          message: "Sila Isi Alamat",
                        },
                      },
                    },
                    up_provider: {
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
                })
              );

              // client
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    addr_client_1: {
                      validators: {
                        notEmpty: {
                          message: "Sila Isi Alamat",
                        },
                      },
                    },
                    up_pemohon: {
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
                })
              );

              // test
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    jumlah: {
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
                })
              );

              // invoice
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    inv_no: {
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
                })
              );

              // surat
              validations.push(
                FormValidation.formValidation(form, {
                  fields: {
                    date_hijri: {
                      validators: {
                        notEmpty: {
                          message: "Sila Isi Tarikh Dalam Hijrah",
                        },
                      },
                    },
                    staff_name: {
                      validators: {
                        notEmpty: {
                          message: "Sila Pilih Pegawai Untuk Dihubungi",
                        },
                      },
                    },
                    approval_by: {
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
                })
              );

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

                    serializedArray.forEach((item) => {
                      formData[item.name] = item.value;
                    });

                    api
                      .post(
                        "projects/wayleaves/tasks",
                        JSON.stringify(formData)
                      )
                      .then((response) => {
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
                      .catch((error) => {
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
          }
        } else if (formId.slice(0, 9) === "#form-083") {
          $(modalId).on("shown.bs.modal", function () {
            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "total-inv": {
                  validators: {
                    regexp: {
                      regexp: /^\d+(\.\d+)?$/,
                      message: "Sila isi angka digit sahaja",
                    },
                    notEmpty: {
                      message: "Amaun Invois diperlukan",
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

            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              validator,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });
        } else if (formId.slice(0, 9) === "#form-083") {
          $(modalId).on("shown.bs.modal", function () {
            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "total-inv": {
                  validators: {
                    regexp: {
                      regexp: /^\d+(\.\d+)?$/,
                      message: "Sila isi angka digit sahaja",
                    },
                    notEmpty: {
                      message: "Amaun Invois diperlukan",
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

            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              validator,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });
        } else if (formId.slice(0, 9) === "#form-091") {
          $(modalId).on("shown.bs.modal", function () {
            let form = document.querySelector(formId);
            let validator;

            validator = FormValidation.formValidation(form, {
              fields: {
                "total-inv-pay-2": {
                  validators: {
                    regexp: {
                      regexp: /^\d+(\.\d+)?$/,
                      message: "Sila isi angka digit sahaja",
                    },
                    notEmpty: {
                      message: "Amaun Bayaran Invois diperlukan",
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

            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              validator,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });
        } else if (formId.slice(0, 9) === "#form-006") {
          if (tenant === "KUDRAT") {
            $(modalId).on("shown.bs.modal", function () {
              let validator = FormValidation.formValidation(form, {
                fields: {
                  "total-woo": {
                    validators: {
                      regexp: {
                        regexp: /^\d+(\.\d+)?$/,
                        message: "Sila isi angka digit sahaja",
                      },
                      notEmpty: {
                        message: "Amaun Arahan Kerja diperlukan",
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

              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                validator,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          } else {
            // action for status 6
            $(modalId).on("shown.bs.modal", function () {
              // declare formEl
              let form = document.querySelector(formId);
              console.log(form);
              let validator;

              $(selectId).select2({
                placeholder: "Sila pilih jenis lawatan tapak",
              });

              validator = FormValidation.formValidation(form, {
                fields: {
                  "report-type": {
                    validators: {
                      notEmpty: {
                        message: "Sila pilih jenis lawatan tapak",
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

              submitButton.classList.remove("d-none");
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

                    // Hide loading indication
                    submitButton.setAttribute("data-kt-indicator", "off");

                    toastr.options = {
                      closeButton: false,
                      debug: false,
                      newestOnTop: false,
                      progressBar: false,
                      positionClass: "toastr-bottom-right",
                      preventDuplicates: true,
                      onclick: null,
                      showDuration: "300",
                      hideDuration: "1000",
                      timeOut: "2000",
                      extendedTimeOut: "1000",
                      showEasing: "swing",
                      hideEasing: "linear",
                      showMethod: "fadeIn",
                      hideMethod: "fadeOut",
                    };

                    toastr.success(
                      "Sila Tetapkan Tarikh Lawatan Tapak pada Paparan Kalendar! 🎉"
                    );

                    // send to calendar page
                    setTimeout(function () {
                      location.href =
                        "/calendar/" +
                        form.querySelector('input[name="system-id"]').value +
                        "/" +
                        $(selectId).val();
                    }, 2500);
                  } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                      html: "Maaf, Sila penuhi butiran yang dikehendaki.",
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
            });
          }
        } else if (formId.slice(0, 9) === "#form-010") {
          // action for status 6
          $(modalId).on("shown.bs.modal", function () {
            // declare formEl
            let form = document.querySelector(formId);
            console.log(form);
            let validator;

            $(selectId).select2({
              placeholder: "Sila pilih jenis lawatan tapak",
            });

            validator = FormValidation.formValidation(form, {
              fields: {
                "report-type": {
                  validators: {
                    notEmpty: {
                      message: "Sila pilih jenis lawatan tapak",
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

            submitButton.classList.remove("d-none");
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

                  // Hide loading indication
                  submitButton.setAttribute("data-kt-indicator", "off");

                  toastr.options = {
                    closeButton: false,
                    debug: false,
                    newestOnTop: false,
                    progressBar: false,
                    positionClass: "toastr-bottom-right",
                    preventDuplicates: true,
                    onclick: null,
                    showDuration: "300",
                    hideDuration: "1000",
                    timeOut: "2000",
                    extendedTimeOut: "1000",
                    showEasing: "swing",
                    hideEasing: "linear",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                  };

                  toastr.success(
                    "Sila Tetapkan Tarikh Lawatan Tapak pada Paparan Kalendar! 🎉"
                  );

                  // send to calendar page
                  setTimeout(function () {
                    location.href =
                      "/calendar/" +
                      form.querySelector('input[name="system-id"]').value +
                      "/" +
                      $(selectId).val();
                  }, 2500);
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila penuhi butiran yang dikehendaki.",
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
          });
        } else if (formId.slice(0, 9) === "#form-011") {
          $(modalId)
            .off("shown.bs.modal")
            .on("shown.bs.modal", function () {
              // **********************************
              // let validator = FormValidation.formValidation(form, {
              //     fields: {
              //         "total-wo": {
              //             validators: {
              //                 regexp: {
              //                     regexp: /^\d+$/,
              //                     message: "Sila isi angka digit sahaja",
              //                 },
              //                 notEmpty: {
              //                     message: "Nilai Arahan Kerja diperlukan",
              //                 },
              //             },
              //         },
              //     },
              //     plugins: {
              //         trigger: new FormValidation.plugins.Trigger(),
              //         bootstrap: new FormValidation.plugins.Bootstrap5({
              //             rowSelector: ".fv-row",
              //             eleInvalidClass: "", // comment to enable invalid state icons
              //             eleValidClass: "", // comment to enable valid state icons
              //         }),
              //     },
              // });

              // // Call the function with the appropriate status ID
              // handleActionSubmit(formId.slice(6, 9), form, submitButton, validator, dropzone, modalId, sysId, folderId)
              // *************************************************

              var reportNoSubmit = null;
              // declare formEl
              let form = document.querySelector(formId);
              let systemId = form.querySelector(
                'input[name="system-id"]'
              ).value;
              var svListContainer = form.querySelector(
                'div[name="calendar-sv-list"]'
              );
              // add loading spinner to container
              svListContainer.innerHTML = `<div class="d-flex justify-content-center align-items-center">
                        <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>`;

              // api request to get required data for task action
              console.log({ systemId: systemId });
              api
                .get("reports/" + systemId + "?data=task-011")
                .then((response) => {
                  console.log(response);
                  let html = "";
                  response.forEach((r) => {
                    // assign report_no to the variable to be used on submit
                    if (reportNoSubmit == null) {
                      reportNoSubmit = r.report_no;
                    }
                    const svDate = new Date(r.sv_date);
                    const dateOptions = {
                      year: "numeric",
                      month: "long",
                      day: "numeric",
                    };
                    const timeOptions = {
                      hour: "numeric",
                      minute: "numeric",
                      hour12: true,
                    };
                    const formattedDate = svDate.toLocaleDateString(
                      "ms",
                      dateOptions
                    );
                    const formattedTime = svDate.toLocaleTimeString(
                      "ms",
                      timeOptions
                    );

                    html += `<div class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                    <div class="d-flex align-items-center me-auto">
                                        <div class="symbol symbol-35px">
                                        <img alt="Pic" src="assets/media/authorities/${r.logo}.png">
                                        </div>
                                        <div class="ms-6">
                                        <a href="#" class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">${r.name}
                                            <!--
                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Art Director</span>
                                            -->
                                        </a>
                                        <div class="fw-semibold text-muted">${r.report_no}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex ms-auto">
                                        <div class="text-end">
                                        <div class="fs-5 fw-bold text-dark">${formattedDate}</div>
                                        <div class="fs-7 text-muted">${formattedTime}</div>
                                        </div>
                                    </div>
                                    </div>`;
                  });

                  // set the container's HTML to the generated HTML
                  svListContainer.innerHTML = html;
                })
                .catch((error) => {
                  console.log(error);
                });

              // Handle form submit
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                null,
                modalId,
                sysId,
                folderId
              );

              // clear list on modal close
              $(modalId)
                .off("hidden.bs.modal")
                .on("hidden.bs.modal", function () {
                  // clear svListContainer's HTML
                  svListContainer.innerHTML = "";
                });
            });
        } else if (formId.slice(0, 9) === "#form-021") {
          $(modalId).on("shown.bs.modal", function () {
            let create = "#cipta-" + modalId.slice(8);
            let create2 = "#cipta1-" + modalId.slice(8);
            let letterType = "#letter-type-" + modalId.slice(8);
            let letterButton = "#letter-button-" + modalId.slice(8);
            let selectName = "#approval-name-" + modalId.slice(8);
            let selectPosition = "#approval-position-" + modalId.slice(8);

            let toogleBP = "#tanda-bagiPihak-" + modalId.slice(8);
            let inputBP = "#bp-position-" + modalId.slice(8);
            let noSurat = "#report-" + modalId.slice(8);

            let noSuratSelect = document.querySelector(noSurat);
            let targetSelect = document.querySelector(letterType);
            let targetButton = document.querySelector(letterButton);
            const toggleInput = document.querySelector(toogleBP);
            const mySelect = document.querySelector(inputBP);
            // let selectedValue = $(letterType).val();

            $(targetSelect).on("change", function () {
              let selectedValue = $(letterType).val();
              // console.log(selectedValue);
              if (selectedValue == "2") {
                $(targetButton).attr("data-bs-target", create);
              } else if (selectedValue == "3") {
                $(targetButton).attr("data-bs-target", create2);
              } else {
                $(targetButton).removeAttr("data-bs-target");
              }
            });

            let selectDate = "#letter-date-" + modalId.slice(8);
            let selectAuthority = "#authority-name-" + modalId.slice(8);
            let officerName = "#officer-name-" + modalId.slice(8);
            let officerContact = "#officer-contact-" + modalId.slice(8);
            let projectTitle = "#project-title-" + modalId.slice(8);

            $(document).ready(function () {
              $(selectDate).on("change", function () {
                var authories = $(this).find(":selected").data("authority");
                $(selectAuthority).val(authories);
                var officers = $(this).find(":selected").data("officer");
                $(officerName).val(officers);
                var officersContact = $(this).find(":selected").data("contact");
                $(officerContact).val(officersContact);
                var projectsTitle = $(this).find(":selected").data("title");
                $(projectTitle).val(projectsTitle);
              });
            });

            $(document).ready(function () {
              $(selectName).on("change", function () {
                var position = $(this).find(":selected").data("position");
                $(selectPosition).val(position);
              });
            });

            toggleInput.addEventListener("change", () => {
              if (toggleInput.checked) {
                mySelect.removeAttribute("disabled");
              } else {
                mySelect.setAttribute("disabled", "");
              }
            });

            let formId = "#form-" + modalId.slice(8);
            let form = document.querySelector(formId);

            let validator2 = FormValidation.formValidation(form, {
              fields: {
                "letter-type": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Jenis Surat",
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

            targetButton.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Show loading indication
              targetButton.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              targetButton.disabled = true;

              // Validate form
              validator2.validate().then(function (status) {
                if (status == "Valid") {
                  // Show loading indication
                  targetButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  targetButton.disabled = true;

                  $.ajax({
                    url: apps + "/v1/letters/tasking.php",
                    type: "POST",
                    data: $(form).serializeArray(),
                    success: function (r) {
                      var letterNum = r.data.letterNum;

                      noSuratSelect.placeholder = letterNum;
                      noSuratSelect.value = letterNum;

                      // $(noSurat).attr('placeholder', letterNum);
                      console.log(r.data.letterNum);
                      // Hide loading indication
                      targetButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      targetButton.disabled = false;
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                      // Hide loading indication
                      targetButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      targetButton.disabled = false;

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
                  targetButton.removeAttribute("data-kt-indicator");
                  // Disable button to avoid multiple click
                  targetButton.disabled = false;
                }
              });
            });

            // // console.log(selectedValue);

            let formId2 = "#form1-" + modalId.slice(8);
            let form2 = document.querySelector(formId2);
            // console.log(form2);

            // document.getElementById("close-button").addEventListener("click", function() {
            //   document.getElementById("formId").reset();
            //   document.getElementById("formId2").reset();
            // });

            let validator = FormValidation.formValidation(form2, {
              fields: {
                "letter-date": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Tarikh Laporan",
                    },
                  },
                },
                "letter-notes": {
                  validators: {
                    notEmpty: {
                      message: "Sila isi Catatan Surat",
                    },
                  },
                },
                "approval-name": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Nama Pegawai Melulus",
                    },
                  },
                },
                // "approval-unit": {
                //   validators: {
                //     notEmpty: {
                //       message: "Sila isi Unit Pegawai Melulus",
                //     },
                //   },
                // },
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

            // submitButton.classList.remove('d-none');
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
                if (status == "Valid") {
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  $.ajax({
                    url: apps + "/v1/letters/tasking.php",
                    type: "POST",
                    data: $(form2).serializeArray(),
                    success: function (r) {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      console.log(r.data);

                      setTimeout(function () {
                        location.href =
                          "/components/views/letter-reviews.php?sid=" +
                          r.data.systemId +
                          "&lno=" +
                          r.data.letterNo;
                      }, 2500);
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
          });
        } else if (formId.slice(0, 9) === "#form-040") {
          // let sNavElParentInitInnerHTML = "";
          // let sContentElParentInitInnerHTML = "";
          // var isContentAppended = false;
          // var modalStatusOpen = "modal-open-status-" + modalId.slice(8);
          // var modalOpenStatus = document.getElementById(modalStatusOpen);
          // var modalOpenStatuss = localStorage.getItem('modalOpenStatus') || '0';
          // var previousModalOpenStatusValue = modalOpenStatus.value;
          if (role == 33) {
            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          } else {
            $(modalId).on("shown.bs.modal", function () {
              // TODO: new signature script test
              const selectmethod = [];
              const selectitem = [];

              var systemID = document.querySelector(sysId).value;
              api
                .get("wayleave/summary/index/" + systemID)
                .then((response) => {
                  // console.log(response);
                  // console.log(response.work_method);
                  //   console.log(JSON.stringify(response.work_method));
                  selectitem.push(...response.rp_item);
                  console.log(selectitem);

                  selectmethod.push(...response.work_method);
                  console.log(selectmethod);
                  // console.log(response.authority_id);

                  //     authorityId.push(...response.authority_id);
                  //   console.log(JSON.stringify(authorityId));

                  const formattedSM = selectmethod.map((item) => ({
                    id: item.method,
                    text: item.name,
                  }));

                  const formattedSI = selectitem
                    .filter((item) => item.work_method_parent === null)
                    .map((item) => ({
                      id: item.name,
                      text: item.name,
                    }));

                  const formattedHDD = selectitem
                    .filter((item) => item.work_method_parent === "HDD")
                    .map((item) => ({
                      id: item.name,
                      text: item.name,
                    }));

                  const formattedGV = selectitem
                    .filter((item) => item.work_method_parent === "GV")
                    .map((item) => ({
                      id: item.name,
                      text: item.name,
                    }));

                  const formattedCW = selectitem
                    .filter((item) => item.work_method_parent === "CW")
                    .map((item) => ({
                      id: item.name,
                      text: item.name,
                    }));

                  const formattedDataSelect = [...formattedSM, ...formattedSI];

                  //   const authorityId = [1, 5];

                  // const authorityId = response.authority_id;
                  const authorityId = response.authority_id.map(
                    (item) => item.authority_id
                  );

                  console.log(authorityId);

                  console.log(formattedDataSelect);

                  // document.addEventListener("DOMContentLoaded", function() {
                  var modalStatusOpen =
                    "#modal-open-status-" + modalId.slice(8);
                  var modalOpenStatus = $(modalStatusOpen).val();
                  // var modalOpenStatus = document.querySelector(modalStatusOpen);
                  // modalOpenStatus.value = '0';

                  if (modalOpenStatus == "0") {
                    modalContent(authorityId);

                    roadRepeaterInit(
                      authorityId,
                      formattedDataSelect,
                      formattedHDD,
                      formattedGV,
                      formattedCW
                    );

                    totalLengthRepeaterInit(authorityId);

                    $(modalStatusOpen).val("1");
                    // modalOpenStatus.value = '1';
                    console.log(modalOpenStatus);
                  }
                  // });
                })
                .catch((error) => {
                  console.log(error);
                });

              function modalContent(authorityId) {
                // Stepper lement
                let stepperModal =
                  "#kt_stepper_project_summary_" + modalId.slice(8);
                // Stepper lement
                var element = document.querySelector(stepperModal);

                // element setup
                console.log(element);

                var sNavElParent = element.querySelector(
                  '[data-rp-stepper="nav"]'
                );
                var sContentElParent = element.querySelector(
                  '[data-rp-stepper="content"]'
                );
                //   var modalStatusOpen = "modal-open-status-" + modalId.slice(8);
                //   console.log(modalStatusOpen);
                // var modalOpenStatus = document.getElementById(modalStatusOpen);

                console.log({ sNavElParent: sNavElParent });
                // const authTest = [1, 5];
                const authTest = authorityId;
                let authCount = 0;

                //   console.log({
                //   modalstatus:
                //     modalOpenStatus.previousElementSibling.previousElementSibling
                //       .previousElementSibling.value,
                // });
                // if (!isContentAppended) {
                authTest.forEach((element) => {
                  let stepperNavEl = `<div class="stepper-item mx-8 my-4" data-kt-stepper-element="nav">
            <!--begin::Wrapper-->
            <div class="stepper-wrapper d-flex align-items-center">
                <!--begin::Icon-->
                <div class="stepper-icon w-40px h-40px">
                    <i class="stepper-check fas fa-check"></i>
                    <span class="stepper-number">${authCount + 2}</span>
                </div>
                <!--end::Icon-->

                <!--begin::Label-->
                <div class="stepper-label">
                    <h3 class="stepper-title">
                        Maklumat ${element}
                    </h3>

                    <div class="stepper-desc">
                        Authority ${element}
                    </div>
                </div>
                <!--end::Label-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Line-->
            <div class="stepper-line h-40px"></div>
            <!--end::Line-->
        </div>`;

                  sNavElParent.innerHTML += stepperNavEl;

                  let stepperContentEl = `<div class="flex-column " data-kt-stepper-element="content">

          <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
              <span class="col-4">PBM Terlibat</span>
              <span class="col-1">:</span>
              <div class="col-7">
                  <span id="road-name" name="road-name" type="text"
                      class="fw-bold fs-6 text-gray-800">Authority ${element}</span>
              </div>
          </div>

          <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
              <span class="col-4">Nama Jalan/Lokasi Terlibat</span>
              <span class="col-1">:</span>
              <div class="col-7">
                  <span id="road-name" name="road-name" type="text"
                      class="fw-bold fs-6 text-gray-800">Jalan Matahari</span>
              </div>
          </div>


          <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
              <span class="col-4">Pegawai Bertanggungjawab</span>
              <span class="col-1">:</span>
              <div class="col-7">
                  <span id="road-name" name="road-name" type="text"
                      class="fw-bold fs-6 text-gray-800">Ts. Mohd Sukry Ismail</span>
              </div>
          </div>

          <!--begin::Repeater-->
          <div id="method-involved-${element}" class="mt-5">
              <!--begin::Form group-->
              <div class="form-group">
                  <div data-repeater-list="rp-list-${element}">
                      <div data-repeater-item>
                          <div class="form-group row">

                              <div class="col-md-7 mb-5 w-450px fv-row">
                                  <label class="form-label required">Kaedah
                                      Pemasangan</label>
                                  <select name="road-method-${element}" data-rp-repeater-${element}="select2"
                                      data-rp-parent class="form-select method-select pe-5"
                                      data-placeholder="Sila Pilih Kaedah Kerja">
                                      <option></option>
                                  </select>
                              </div>

                              <div class="col-md-3 mb-5 fv-row w-225px d-none" data-rp-nested-${element}>
                                  <label class="form-label required">Jarak</label>
                                  <input type="text" name="road-length-${element}"
                                      class="form-control repeater-value-${element}"
                                      placeholder="Sila Isi Jarak" />
                              </div>


                              <div class="col-md-1">
                                  <button type="button" data-repeater-delete
                                      class="btn btn-light-danger mt-8 min-w-100">
                                      <i class="fad fa-trash fs-4"></i>
                                  </button>
                              </div>

                              <div class="row d-none" id="nested_hdd" data-rp-nested-hdd-${element}>
                                  <div class="inner-repeater-hdd-${element} d-flex">
                                      <div class="col-md-1 mt-8 mb-9">
                                          <button
                                              class="btn btn-sm btn-flex btn-light-primary p-4"
                                              data-repeater-create type="button">
                                              <i class="fad fa-plus fs-5"></i>
                                          </button>
                                      </div>
                                      <div data-repeater-list="inner-repeater-hdd-${element}"
                                          class="col-md-11 mb-5">
                                          <div data-repeater-item>
                                              <div class="form-group row">
                                                  <div class="col-md-7 mb-5 fv-row">
                                                      <label
                                                          class="form-label required">Senarai</label>
                                                      <select name="road-method-hdd-${element}"
                                                          data-rp-hdd-repeater-${element}="select2"
                                                          class="form-select method-select"
                                                          data-placeholder="Sila Pilih Kaedah Kerja">
                                                          <option></option>
                                                      </select>
                                                  </div>

                                                  <div class="col-md-4 mb-5 fv-row">
                                                      <label
                                                          class="form-label required">Kuantiti</label>
                                                      <input type="text"
                                                          name="road-length-hdd-${element}"
                                                          class="form-control repeater-value-${element}"
                                                          placeholder="Sila Isi Jarak" />
                                                  </div>
                                                  <div class="col-md-1">
                                                      <button type="button" data-repeater-delete
                                                          class="btn btn-light-danger mt-8 min-w-100">
                                                          <i class="fad fa-trash fs-4"></i>
                                                      </button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="row d-none" id="nested_gv" data-rp-nested-gv-${element}>
                                  <div class="inner-repeater-gv-${element} d-flex">
                                      <div class="col-md-1 mt-8 mb-9">
                                          <button
                                              class="btn btn-sm btn-flex btn-light-primary p-4"
                                              data-repeater-create type="button">
                                              <i class="fad fa-plus fs-5"></i>
                                          </button>
                                      </div>
                                      <div data-repeater-list="inner-repeater-gv-${element}"
                                          class="col-md-11 mb-5">
                                          <div data-repeater-item>
                                              <div class="form-group row">

                                                  <div class="col-md-7 mb-5 fv-row">
                                                      <label
                                                          class="form-label required">Senarai</label>
                                                      <select name="road-method-gv"
                                                          data-rp-gv-repeater-${element}="select2"
                                                          class="form-select method-select"
                                                          data-placeholder="Sila Pilih Kaedah Kerja">
                                                          <option></option>

                                                      </select>
                                                  </div>

                                                  <div class="col-md-4 mb-5 fv-row">
                                                      <label
                                                          class="form-label required">Kuantiti</label>
                                                      <input type="text" name="road-length-gv-${element}"
                                                          class="form-control repeater-value-${element}"
                                                          placeholder="Sila Isi Jarak" />
                                                  </div>
                                                  <div class="col-md-1">
                                                      <button type="button" data-repeater-delete
                                                          class="btn btn-light-danger mt-8 min-w-100">
                                                          <i class="fad fa-trash fs-4"></i>
                                                      </button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                  </div>
                              </div>

                              <div class="row d-none" id="nested_cw" data-rp-nested-cw-${element}>
                                  <div class="inner-repeater-cw-${element} d-flex">
                                      <div class="col-md-1 mt-8 mb-9">
                                          <button
                                              class="btn btn-sm btn-flex btn-light-primary p-4"
                                              data-repeater-create type="button">
                                              <i class="fad fa-plus fs-5"></i>
                                          </button>
                                      </div>
                                      <div data-repeater-list="inner-repeater-cw-${element}"
                                          class="col-md-11 mb-5">
                                          <div data-repeater-item>
                                              <div class="form-group row">

                                                  <div class="col-md-7 mb-5 fv-row">
                                                      <label
                                                          class="form-label required">Senarai</label>
                                                      <select name="road-method-cw"
                                                          data-rp-cw-repeater-${element}="select2"
                                                          class="form-select method-select"
                                                          data-placeholder="Sila Pilih Kaedah Kerja">
                                                          <option></option>

                                                      </select>
                                                  </div>

                                                  <div class="col-md-4 mb-5 fv-row">
                                                      <label
                                                          class="form-label required">Kuantiti</label>
                                                      <input type="text" name="road-length-cw-${element}"
                                                          class="form-control repeater-value-${element}"
                                                          placeholder="Sila Isi Jarak" />
                                                  </div>
                                                  <div class="col-md-1">
                                                      <button type="button" data-repeater-delete
                                                          class="btn btn-light-danger mt-8 min-w-100">
                                                          <i class="fad fa-trash fs-4"></i>
                                                      </button>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                  </div>
                              </div>

                          </div>
                      </div>
                  </div>

                  <div class="d-flex justify-content-between align-items-center">
                      <!--begin::Form group-->
                      <div class="form-group">
                          <button type="button" id="btn-create" data-repeater-create
                              class="btn btn-light-primary">
                              <i class="fad fa-plus"></i>Tambah Kaedah
                          </button>
                      </div>
                      <!--end::Form group-->
                      <div class="d-flex flex-end row">
                          <div class="col-12 fv-row d-flex flex-row">
                              <label class="w-100px d-flex align-items-center">Jumlah
                                  Jarak</label>
                              <input type="text" name="application-length-${element}"
                                  id="application-length-${element}"
                                  class="form-control form-control-solid w-150px"
                                  placeholder="Jarak Permohonan" value="" readonly />
                          </div>
                      </div>
                  </div>
              </div>
              <!--end::Form group-->

          </div>
          <!--end::Repeater-->
        </div>
        `;
                  sContentElParent.innerHTML += stepperContentEl;

                  authCount++;
                });
                // isContentAppended = true;
                // console.log({ modalStatus: modalOpenStatus.value });
                // }

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

              // console.log(formattedData);

              function roadRepeaterInit(
                authoirtyId,
                formattedDataSelect,
                formattedHDD,
                formattedGV,
                formattedCW
              ) {
                // initialize repeater
                authoirtyId.forEach((element) => {
                  var roadRepeaterJkr = $(
                    `#method-involved-${element}`
                  ).repeater({
                    initEmpty: false,

                    // defaultValues: {
                    //   "road-method": "HDD",
                    // },

                    repeaters: [
                      {
                        selector: `.inner-repeater-hdd-${element}`,

                        show: function () {
                          $(this).slideDown();

                          $(this)
                            .find(`[data-rp-hdd-repeater-${element}="select2"]`)
                            .select2({ data: formattedHDD });
                        },

                        hide: function (deleteElement) {
                          $(this).slideUp(deleteElement);
                        },

                        ready: function () {
                          // Init select2
                          $(
                            `[data-rp-hdd-repeater-${element}="select2"]`
                          ).select2({
                            data: formattedHDD,
                          });
                        },
                      },
                      {
                        selector: `.inner-repeater-gv-${element}`,

                        show: function () {
                          $(this).slideDown();

                          $(this)
                            .find(`[data-rp-gv-repeater-${element}="select2"]`)
                            .select2({ data: formattedGV });
                        },

                        hide: function (deleteElement) {
                          $(this).slideUp(deleteElement);
                        },

                        ready: function () {
                          // Init select2
                          $(
                            `[data-rp-gv-repeater-${element}="select2"]`
                          ).select2({
                            data: formattedGV,
                          });
                        },
                      },
                      {
                        selector: `.inner-repeater-cw-${element}`,

                        show: function () {
                          $(this).slideDown();

                          $(this)
                            .find(`[data-rp-cw-repeater-${element}="select2"]`)
                            .select2({ data: formattedCW });
                        },

                        hide: function (deleteElement) {
                          $(this).slideUp(deleteElement);
                        },

                        ready: function () {
                          // Init select2
                          $(
                            `[data-rp-cw-repeater-${element}="select2"]`
                          ).select2({
                            data: formattedCW,
                          });
                        },
                      },
                    ],

                    show: function () {
                      // alert('new repeater item added');
                      $(this).slideDown();

                      // Init select2
                      $(this)
                        .find(`[data-rp-repeater-${element}="select2"]`)
                        .select2({ data: formattedDataSelect });

                      var nestedHddSection = $(this).find(
                        `[data-rp-nested-hdd-${element}]`
                      );
                      var nestedGvSection = $(this).find(
                        `[data-rp-nested-gv-${element}]`
                      );
                      var nestedCwSection = $(this).find(
                        `[data-rp-nested-cw-${element}]`
                      );
                      var nestedSection = $(this).find(
                        `[data-rp-nested-${element}]`
                      );

                      nestedHddSection.removeClass("d-none");
                      nestedGvSection.removeClass("d-none");
                      nestedSection.removeClass("d-none");
                      nestedCwSection.removeClass("d-none");
                      // Hide the nested sections by default
                      nestedHddSection.hide();
                      nestedGvSection.hide();
                      nestedSection.hide();
                      nestedCwSection.hide();

                      // Add event listener to parent select element
                      $(this)
                        .find(`[data-rp-repeater-${element}="select2"]`)
                        .on("change", function () {
                          var selectedValue = $(this).val();

                          nestedHddSection.find("input").val("");
                          nestedGvSection.find("input").val("");
                          nestedCwSection.find("input").val("");
                          nestedSection.find("input").val("");
                          nestedHddSection.find("select").val("");
                          nestedGvSection.find("select").val("");
                          nestedCwSection.find("select").val("");
                          nestedSection.find("select").val("");

                          // Show/hide nested sections based on selected value
                          if (selectedValue === "HDD") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "GV") {
                            nestedHddSection.slideUp();
                            nestedGvSection.slideDown();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "MT") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "PJ") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "TB") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "CW") {
                            nestedCwSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedHddSection.slideUp();
                          } else {
                            nestedSection.slideDown();
                            nestedHddSection.slideUp();
                            nestedGvSection.slideUp();
                            nestedCwSection.slideUp();
                          }
                        });
                    },

                    hide: function (deleteElement) {
                      // Show confirmation popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                      // Swal.fire({
                      //   text: "Padam Kaedah Pemasangan ini?",
                      //   icon: "warning",
                      //   showCancelButton: true,
                      //   buttonsStyling: false,
                      //   confirmButtonText: "Ya, Padam!",
                      //   cancelButtonText: "Tidak, Kembali!",
                      //   customClass: {
                      //     confirmButton: "btn btn-primary",
                      //     cancelButton: "btn btn-active-light",
                      //   },
                      // }).then((result) => {
                      //   if (result.isConfirmed) {
                      $(this).slideUp(deleteElement);
                      // }
                      // });
                    },

                    ready: function () {
                      // Init select2
                      $(`[data-rp-repeater-${element}="select2"]`).select2({
                        data: formattedDataSelect,
                      });

                      // $('[data-rp-nested-hdd]').hide();
                      // $('[data-rp-nested-gv]').hide();

                      var nestedSection = $(`[data-rp-nested-${element}]`);
                      var nestedHddSection = $(
                        `[data-rp-nested-hdd-${element}]`
                      );
                      var nestedGvSection = $(`[data-rp-nested-gv-${element}]`);
                      var nestedCwSection = $(`[data-rp-nested-cw-${element}]`);

                      // Hide the nested sections by default
                      nestedHddSection.removeClass("d-none");
                      nestedGvSection.removeClass("d-none");
                      nestedSection.removeClass("d-none");
                      nestedCwSection.removeClass("d-none");
                      nestedHddSection.hide();
                      nestedGvSection.hide();
                      nestedSection.hide();
                      nestedCwSection.hide();

                      $(`[data-rp-repeater-${element}="select2"]`).on(
                        "change",
                        function () {
                          var selectedValue = $(this).val();

                          nestedHddSection.find("input").val("");
                          nestedGvSection.find("input").val("");
                          nestedCwSection.find("input").val("");
                          nestedSection.find("input").val("");
                          nestedHddSection.find("select").val("");
                          nestedGvSection.find("select").val("");
                          nestedCwSection.find("select").val("");
                          nestedSection.find("select").val("");

                          // Show/hide nested sections based on selected value
                          if (selectedValue === "HDD") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "GV") {
                            nestedHddSection.slideUp();
                            nestedGvSection.slideDown();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "MT") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "PJ") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "TB") {
                            nestedHddSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedCwSection.slideUp();
                          } else if (selectedValue === "CW") {
                            nestedCwSection.slideDown();
                            nestedGvSection.slideUp();
                            nestedSection.slideUp();
                            nestedHddSection.slideUp();
                          } else {
                            nestedSection.slideDown();
                            nestedHddSection.slideUp();
                            nestedGvSection.slideUp();
                            nestedCwSection.slideUp();
                          }
                        }
                      );
                    },

                    isFirstItemUndeletable: false,
                  });
                });

                // // manipulate repeater
                // var roadData = [];
                // var rawRoadData = {
                //   roads: [
                //     { name: "jalan-1", method: "HDD", length: 200 },
                //     { name: "jalan-2", method: ["HDD", "CW"], length: 200 },
                //   ],
                // };

                // // Loop through the JSON data and add each item to the repeater
                // $.each(rawRoadData.roads, function (index, road) {
                //   // Push roads info as a separate object into the array
                //   roadData.push({
                //     "road-name": road.name,
                //     "road-method": road.method,
                //     "start-coord":
                //       road.latitude_start + ", " + road.longitude_start,
                //     "end-coord": road.latitude_end + ", " + road.longitude_end,
                //     "road-length": road.length,
                //   });
                // });
                // roadRepeater.setList(roadData);
              }

              function totalLengthRepeaterInit(authoirtyId) {
                authoirtyId.forEach((element) => {
                  let totalSum;
                  let totalLength = document.getElementById(
                    `application-length-${element}`
                  );

                  function calculateLength() {
                    totalSum = 0;
                    $(
                      "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item])"
                    ).each(function () {
                      var $input = $(this).find(`.repeater-value-${element}`);
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
                  $(`.repeater-value-${element}`).each(function () {
                    var value = parseFloat($(this).val());
                    if (!isNaN(value)) {
                      totalSum += value;
                    }
                  });
                  $(totalLength).val(totalSum);

                  // Add event listener to each input element
                  $(
                    "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item])"
                  ).on("input", `.repeater-value-${element}`, function () {
                    totalSum = calculateLength();
                    $(totalLength).val(totalSum);
                  });

                  // Add event listener to #method-involved element to clear input
                  document
                    .getElementById(`method-involved-${element}`)
                    .addEventListener("keyup", function (event) {
                      if (
                        event.target &&
                        event.target.classList.contains(
                          `repeater-value-${element}`
                        )
                      ) {
                        if (event.target.value === "") {
                          event.target.value = 0;
                          event.target.dispatchEvent(new Event("input"));
                        }
                      }
                    });

                  // Add event listener to "repeater-add" button
                  $(document).ready(function () {
                    $("button[data-repeater-create]").on("click", function () {
                      // Add event listener to each input element
                      $(
                        "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item])"
                      ).on("input", `.repeater-value-${element}`, function () {
                        totalSum = calculateLength();
                        $(totalLength).val(totalSum);
                      });
                    });
                  });

                  $(document).ready(function () {
                    // Add event listener to "repeater-delete" button
                    $("div[data-repeater-list]").on(
                      "click",
                      "button[data-repeater-delete]",
                      function () {
                        $(this)
                          .closest(
                            "div[data-repeater-item]:not(#nested_hdd [data-repeater-item]):not(#nested_gv [data-repeater-item]):not(#nested_cw [data-repeater-item])"
                          )
                          .find(`.repeater-value-${element}`)
                          .each(function () {
                            $(this).val("");
                            $(this).trigger("input");
                            totalSum = calculateLength();
                            $(totalLength).val(totalSum);
                          });
                      }
                    );
                  });
                });
              }

              //   let formId = "#form-" + modalId.slice(8);
              //   let form = document.querySelector(formId);
              // let formData = JSON.stringify(form);

              submitButton.addEventListener("click", function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                // Validate form
                // validator.validate().then(function (status) {
                //   if (status == "Valid") {
                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const formData = new FormData(form);
                // formData.append('subId', code);

                console.log(formData);

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
                  } else {
                    obj[lastKey] = value;
                  }
                }

                console.log(JSON.stringify(result, null, 2));

                // const serializedArray = $(form).serializeArray();
                // const formData = {};

                // serializedArray.forEach(item => {
                //     formData[item.name] = item.value;
                // });

                // const jsonData = JSON.stringify(formData);

                // Send Axios POST request
                api
                  .post(`wayleave/summary/index`, JSON.stringify(result))
                  .then((response) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // toastr.success("Berjaya! 🎉");

                    toastr.success(response.message);
                    // setTimeout(function () {
                    //     location.href = `/tasks/new`;
                    // }, 2500);
                  })
                  .catch((error) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // Show error popup
                    Swal.fire({
                      text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, faham",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      allowOutsideClick: false,
                    });
                  });
              });
            });
          }

          // $(modalId).on("hidden.bs.modal", function () {
          //   modalOpenStatus.value = '1';
          //   console.log(modalOpenStatus.value);
          //   localStorage.setItem('modalOpenStatus', modalOpenStatus.value);
          // });
        } else if (formId.slice(0, 9) === "#form-041") {
          $(modalId).on("shown.bs.modal", function () {
            var SysID = "#system-id-" + modalId.slice(8);
            var RefNo = "#ref-no-" + modalId.slice(8);

            var systemID = document.querySelector(SysID).value;
            var refNo = document.querySelector(RefNo).value;

            let noSurat = "#letter-no-" + modalId.slice(8);
            let noSuratSelect = document.querySelector(noSurat);

            const dataGenerate = {
              "letter-type": "3",
              "system-id": systemID,
              "ref-no": refNo,
            };

            // Send Axios POST request
            api
              .post(`letters/index`, JSON.stringify(dataGenerate))
              .then((response) => {
                var letterNum = response.letterNum;

                noSuratSelect.placeholder = letterNum;
                noSuratSelect.value = letterNum;
              })
              .catch((error) => {
                console.log("Maaf Nombor Surat Gagal Dijana!");
              });

            let stepperModal =
              "#kt_stepper_generate_letter_PIL_" + modalId.slice(8);
            // Stepper lement
            var element = document.querySelector(stepperModal);

            // Initialize Stepper
            var stepper = new KTStepper(element);

            // Handle navigation click
            stepper.on("kt.stepper.click", function (stepper) {
              stepper.goTo(stepper.getClickedStepIndex()); // go to clicked step
            });

            // Handle next step
            stepper.on("kt.stepper.next", function (stepper) {
              // console.log('stepper.next');

              // Validate form before change stepper step
              var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step

              if (validator) {
                validator.validate().then(function (status) {
                  // console.log('validated!');

                  if (status == "Valid") {
                    stepper.goNext();

                    //KTUtil.scrollTop();
                  } else {
                    // Show error message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
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
                      //KTUtil.scrollTop();
                    });
                  }
                });
              } else {
                stepper.goNext();

                // KTUtil.scrollTop();
              }
            });

            // Handle previous step
            stepper.on("kt.stepper.previous", function (stepper) {
              // console.log('stepper.previous');

              stepper.goPrevious();
              // KTUtil.scrollTop();
            });

            var startDate = "#date-start-" + modalId.slice(8);
            var endDate = "#date-finish-" + modalId.slice(8);

            // $(startDate).flatpickr({
            //   locale: "ms",
            //   altInput: true,
            //   altFormat: "j F Y",
            //   dateFormat: "Y-m-d",
            // });
            var startfaltpickr = $(startDate).flatpickr({
              locale: "ms",
              altInput: true,
              altFormat: "j F Y",
              dateFormat: "Y-m-d",
              position: "above",
              // static: true,
            });

            var finishfaltpickr = $(endDate).flatpickr({
              locale: "ms",
              altInput: true,
              altFormat: "j F Y",
              dateFormat: "Y-m-d",
              position: "above",
              // static: true,
            });

            let selectName = "#approval-name-" + modalId.slice(8);
            let selectPosition = "#approval-position-" + modalId.slice(8);

            let officerName1 = "#officer-name1-" + modalId.slice(8);
            let officerContact1 = "#officer-contact1-" + modalId.slice(8);
            let officerName2 = "#officer-name2-" + modalId.slice(8);
            let officerContact2 = "#officer-contact2-" + modalId.slice(8);

            $(document).ready(function () {
              $(selectName).on("change", function () {
                var position = $(this).find(":selected").data("position");
                $(selectPosition).val(position);
              });
              $(officerName1).on("change", function () {
                var contact1 = $(this).find(":selected").data("contact");
                $(officerContact1).val(contact1);
              });
              $(officerName2).on("change", function () {
                var contact2 = $(this).find(":selected").data("contact");
                $(officerContact2).val(contact2);
              });
            });

            let toogleBP = "#tanda-bagiPihak-" + modalId.slice(8);
            let inputBP = "#bp-position-" + modalId.slice(8);
            const toggleInput = document.querySelector(toogleBP);
            const mySelect = document.querySelector(inputBP);

            $(toggleInput).on("change", function () {
              if (this.checked) {
                $(mySelect).removeClass("d-none");
              } else {
                $(mySelect).addClass("d-none");
              }
            });

            let formId = "#form-" + modalId.slice(8);
            let form = document.querySelector(formId);

            var validations = [];

            validations.push(
              FormValidation.formValidation(form, {
                fields: {
                  "authority-attention": {
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
              })
            );

            validations.push(
              FormValidation.formValidation(form, {
                fields: {
                  "date-start": {
                    validators: {
                      date: {
                        format: "YYYY-MM-DD",
                        message: "Nilai itu bukan tarikh yang sah",
                      },
                      notEmpty: {
                        message: "Sila Pilih Tarikh Jangka Mula",
                      },
                    },
                  },
                  "date-finish": {
                    validators: {
                      date: {
                        format: "YYYY-MM-DD",
                        message: "Nilai itu bukan tarikh yang sah",
                      },
                      notEmpty: {
                        message: "Sila Pilih Tarikh Jangka Siap",
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
              })
            );

            validations.push(
              FormValidation.formValidation(form, {
                fields: {
                  "officer-name1": {
                    validators: {
                      notEmpty: {
                        message: "Sila Pilih Nama Pegawai Melulus",
                      },
                    },
                  },
                  "officer-name2": {
                    validators: {
                      notEmpty: {
                        message: "Sila Pilih Nama Pegawai Melulus",
                      },
                    },
                  },
                  "approval-name": {
                    validators: {
                      notEmpty: {
                        message: "Sila Pilih Nama Pegawai Melulus",
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
              })
            );

            // submitButton.classList.remove('d-none');
            // Handle form submit
            submitButton.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Show loading indication
              submitButton.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              submitButton.disabled = true;

              var validator = validations[2];
              // Validate form
              validator.validate().then(function (status) {
                if (status == "Valid") {
                  // Show loading indication
                  submitButton.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  submitButton.disabled = true;

                  const serializedArray = $(form).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // Send Axios POST request
                  api
                    .post(`letters/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      console.log(response);

                      let res_sid = response.systemId;
                      let res_lno = response.letterNo;
                      let res_lid = response.letterId;
                      console.log(res_sid);
                      console.log(res_lno);
                      console.log(res_lid.id);

                      Swal.fire({
                        text: "Surat Permohonan Izin Lalu telah berjaya Dijana!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, maklum!",
                        customClass: {
                          confirmButton: "btn btn-primary",
                        },
                        allowOutsideClick: false,
                      }).then(function (result) {
                        if (result.isConfirmed) {
                          // Enable button
                          submitButton.disabled = false;
                          console.log(
                            "/letters/PIL/" + res_sid + "/" + res_lid.id
                          );

                          location.href =
                            "/letters/PIL/" + res_sid + "/" + res_lid.id;
                        }
                      });
                    })
                    .catch((error) => {
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
          });
        } else if (formId.slice(0, 9) === "#form-042") {
          console.log(role);
          if (role === 21 || role === 22 || role === 23) {
            $(modalId).on("shown.bs.modal", function () {
              let form = document.querySelector(formId);
              let validator;

              validator = FormValidation.formValidation(form, {
                fields: {
                  "total-inv": {
                    validators: {
                      regexp: {
                        regexp: /^\d+$/,
                        message: "Sila isi angka digit sahaja",
                      },
                      notEmpty: {
                        message: "Amaun Invois diperlukan",
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

              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                validator,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          } else if (role === 61 || role === 62 || role === 64) {
            // Format options
            var optionFormat = function (item) {
              if (!item.id) {
                return item.text;
              }

              var span = document.createElement("span");
              var imgUrl = item.element.getAttribute("data-profile-picture");
              var template = "";

              template += `<img src=${imgUrl} class="rounded-circle h-30px me-2" alt="image"/>`;
              template += item.text;

              span.innerHTML = template;

              return $(span);
            };

            $(document).ready(function () {
              // Update second selection options when the first selection changes
              $(selectId).on("change", function () {
                var firstSelection = $(this).val();

                //   const serializedArray = $(firstSelection).serializeArray();
                //   const formData = {};

                //   serializedArray.forEach((item) => {
                //     formData[item.name] = item.value;
                //   });

                //   const jsonData = JSON.stringify(formData);
                const jsonData = { survey_team: firstSelection };

                // Send Axios POST request
                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    console.log("Response received:", response.teamMembers);

                    // Split the response into separate values
                    var values = response.teamMembers.split(",");

                    // Clear the second selection
                    $(selectId2).empty();

                    // Add each value as an option to the second selection
                    values.forEach(function (value) {
                      var profilePicture = response.memberImg
                        ? "assets/media/avatars/" + response.memberImg + ".jpg"
                        : "assets/media/avatars/blank.jpg"; // Conditional check

                      $(selectId2).append(
                        '<option value="' +
                          value.trim() +
                          '" data-profile-picture="' +
                          profilePicture +
                          '">' +
                          value.trim() +
                          "</option>"
                      );
                    });
                  })
                  .catch((error) => {
                    // Hide loading indication
                    submitButton.removeAttribute("data-kt-indicator");

                    // Enable button
                    submitButton.disabled = false;

                    // Show error popup
                    Swal.fire({
                      text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                      icon: "error",
                      buttonsStyling: false,
                      confirmButtonText: "Ok, faham",
                      customClass: {
                        confirmButton: "btn btn-primary",
                      },
                      allowOutsideClick: false,
                    });
                  });
              });
            });

            // Init Select2 --- more info: https://select2.org/
            $(selectId).select2({
              minimumResultsForSearch: Infinity,
              templateSelection: optionFormat,
              templateResult: optionFormat,
            });

            $(selectId).val(null).trigger("change");

            // Init Select2 --- more info: https://select2.org/
            $(selectId2).select2({
              minimumResultsForSearch: Infinity,
              templateSelection: optionFormat,
              templateResult: optionFormat,
            });

            $(selectId2).val(null).trigger("change");

            let validator = FormValidation.formValidation(form, {
              fields: {
                "survey-team-assign": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Kumpulan Ukur",
                    },
                  },
                },
                "survey-leader-assign": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Ketua Kumpulan Ukur",
                    },
                  },
                },
                "modal-date-range": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Jangkaan Julat Tarikh Mula & Tamat",
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

            submitButton.classList.remove("d-none");
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

                  const serializedArray = $(form).serializeArray();
                  const formData = {};

                  serializedArray.forEach((item) => {
                    formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // /v1/projects/tasking.php
                  // Send Axios POST request
                  api
                    .post(`surveys/tasking`, jsonData)
                    .then((response) => {
                      // Hide loading indication
                      submitButton.removeAttribute("data-kt-indicator");

                      // Enable button
                      submitButton.disabled = false;

                      toastr.options = {
                        closeButton: false,
                        debug: false,
                        newestOnTop: false,
                        progressBar: false,
                        positionClass: "toastr-bottom-right",
                        preventDuplicates: true,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        timeOut: "2000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                      };

                      toastr.success("Lantikan Kumpulan Survey Berjaya! 🎉");

                      setTimeout(function () {
                        location.href = "tasks/new";
                      }, 2500);
                    })
                    .catch((error) => {
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
                    });
                } else {
                  // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                  Swal.fire({
                    html: "Maaf, Sila pilih <strong>Kumpulan Survey</strong> untuk permohonan ini.",
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

            const modalElement = document.querySelector(
              "#modal-date-range-" + modalId.slice(8)
            );
            flatpickrModal = $(modalElement).flatpickr({
              altInput: true,
              altFormat: "d/m/Y",
              dateFormat: "Y-m-d",
              mode: "range",
              onChange: function (selectedDates, dateStr, instance) {
                initConfig.handleFlatpickrModal(
                  selectedDates,
                  dateStr,
                  instance
                );
              },
            });
          }
        } else if (formId.slice(0, 9) === "#form-046") {
          $(modalId).on("shown.bs.modal", function () {
            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              null,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

              // Remove files when the modal is closed
              $(modalId).on("hidden.bs.modal", function () {
                dropzone.removeAllFiles();
                dropzone.destroy();
                submitButton.classList.add("d-none");
              });
          } else if (formId.slice(0, 9) === "#form-009") {
              $(modalId).on("shown.bs.modal", function () {
                let validator = FormValidation.formValidation(form, {
                  fields: {
                    "total-wop": {
                      validators: {
                        regexp: {
                          regexp: /^\d+(\.\d+)?$/,
                          message: "Sila isi angka digit sahaja",
                        },
                        notEmpty: {
                          message: "Amaun Arahan Kerja diperlukan",
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

            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              validator,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });

          // Remove files when the modal is closed
          $(modalId).on("hidden.bs.modal", function () {
            dropzone.removeAllFiles();
            dropzone.destroy();
            submitButton.classList.add("d-none");
          });
        } else if (formId.slice(0, 9) === "#form-008") {
          $(modalId).on("shown.bs.modal", function () {
            // Call the function with the appropriate status ID
            handleActionSubmit(
              formId.slice(6, 9),
              form,
              submitButton,
              null,
              dropzone,
              modalId,
              sysId,
              folderId
            );
          });
        } else if (formId.slice(0, 9) === "#form-032") {
          if (role == 33) {
            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          } else {
            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });
          }
        } else if (formId.slice(0, 9) === "#form-033") {
          if (role == 33) {
            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("hidden.bs.modal", function () {
              dropzone.removeAllFiles();
              dropzone.destroy();
              submitButton.classList.add("d-none");
            });
          }
        } else if (formId.slice(0, 9) === "#form-034") {
          if (role == 33) {
            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

                // Remove files when the modal is closed
                $(modalId).on("hidden.bs.modal", function () {
                  dropzone.removeAllFiles();
                  dropzone.destroy();
                  submitButton.classList.add("d-none");
                });

              }
          } else if (formId.slice(0, 9) === "#form-996") {
              // console.log(role);
              if (role === 21 || role === 22 || role === 23) {
                $(modalId).on("shown.bs.modal", function () {
                  let form = document.querySelector(formId);

                  // Call the function with the appropriate status ID
                  handleActionSubmit(formId.slice(6,9), form, submitButton, null, dropzone, modalId, sysId, folderId)
                });

                // Remove files when the modal is closed
                $(modalId).on("hidden.bs.modal", function () {
                    dropzone.removeAllFiles();
                    dropzone.destroy();
                    submitButton.classList.add("d-none");
                });
              }

          } //1 dropzone 3 date 1 note
          else if (
            formId.slice(0, 9) === "#form-101" ||
            formId.slice(0, 9) === "#form-103"
          ) {
            let labelId = "#label-" + modalId.slice(8);
            let labelId2 = "#label2-" + modalId.slice(8);
            let labelId3 = "#label3-" + modalId.slice(8);
            let label = document.querySelector(labelId).value;
            let label2 = document.querySelector(labelId2).value;
            let label3 = document.querySelector(labelId3).value;
            let folder = document.querySelector(folderId).value;
            var systemId = document.querySelector(sysId).value;
            // var authorityId = document.querySelector(AID).value;

            $(modalId).on("shown.bs.modal", function () {
              let form = document.querySelector(formId);
              let validator;

              validator = FormValidation.formValidation(form, {
                fields: {
                  [`${label}-${folder}`]: {
                    validators: {
                      notEmpty: {
                        message: "Tarikh diperlukan",
                      },
                    },
                  },
                  [`${label2}-${folder}2`]: {
                    validators: {
                      notEmpty: {
                        message: "Tarikh diperlukan",
                      },
                    },
                  },
                  [`${label3}-${folder}3`]: {
                    validators: {
                      notEmpty: {
                        message: "Tarikh diperlukan",
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

              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                validator,
                dropzone,
                modalId,
                sysId,
                folderId,
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
              // Trigger the "hidden.bs.modal" event when the element is clicked
              let form = $(modalId).find('form')[0];
              form.reset();
              dropzone.removeAllFiles();
              dropzone.destroy();

              submitButton.classList.add("d-none");
            });
          } //1 dropzone 2 date 1 note
          else if (
            formId.slice(0, 9) === "#form-085" ||
            formId.slice(0, 9) === "#form-121" ||
            formId.slice(0, 9) === "#form-151" ||
            formId.slice(0, 9) === "#form-131"
          ) {
            let labelId = "#label-" + modalId.slice(8);
            let labelId2 = "#label2-" + modalId.slice(8);
            let label = document.querySelector(labelId).value;
            let label2 = document.querySelector(labelId2).value;
            let folder = document.querySelector(folderId).value;
            var systemId = document.querySelector(sysId).value;
            // var authorityId = document.querySelector(AID).value;

            $(modalId).on("shown.bs.modal", function () {
              let form = document.querySelector(formId);
              let validator;

              validator = FormValidation.formValidation(form, {
                fields: {
                  [`${label}-${folder}`]: {
                    validators: {
                      notEmpty: {
                        message: "Tarikh diperlukan",
                      },
                    },
                  },
                  [`${label2}-${folder}2`]: {
                    validators: {
                      notEmpty: {
                        message: "Tarikh diperlukan",
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

              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                validator,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
              // Trigger the "hidden.bs.modal" event when the element is clicked
              let form = $(modalId).find('form')[0];
              form.reset();
              dropzone.removeAllFiles();
              dropzone.destroy();

              submitButton.classList.add("d-none");
            });
          } //1 dropzone 1 date 1 note
          else if (
            formId.slice(0, 9) === "#form-128"
          ) {
            var systemId = document.querySelector(sysId).value;

            $(modalId).on("shown.bs.modal", function () {
              // Call the function with the appropriate status ID
              handleActionSubmit(
                formId.slice(6, 9),
                form,
                submitButton,
                null,
                dropzone,
                modalId,
                sysId,
                folderId
              );
            });

            // Remove files when the modal is closed
            $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
              // Trigger the "hidden.bs.modal" event when the element is clicked
              let form = $(modalId).find('form')[0];
              form.reset();
              dropzone.removeAllFiles();
              dropzone.destroy();

              submitButton.classList.add("d-none");
            });
          } else {
            console.log("Error in formID");
          }
      }
    });
  };

  // Public methods
  return {
    init: function () {
      table = document.querySelector("#project-task");

      if (!table) {
        return;
      }
      initDatatable();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  initTable.init();
});
