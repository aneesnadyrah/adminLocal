"use strict";

// Class definition
var SurveyTask = (function () {
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
      ajax: apps + "/api/surveys/tasks/new",
      columns: [
        {
          data: "RefNo",
        },
        {
          data: "Provider",
        },
        {
          data: "District",
        },
        {
          data: "Length",
        },
        {
          data: "Status",
        },
        {
          data: "SubmitDate",
        },
        {
          data: null,
        },
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
            var $provider_img = key["ProviderID"];

            if ($provider_img) {
              // For Avatar image
              var $output =
                `<img src="assets/media/provider/` +
                key["ProviderID"] +
                `.webp" alt="` +
                key["Provider"] +
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
                $name = key["Provider"];
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
              key["Provider"] +
              `</a>
                <!--end::Title-->
              </div>`
            );
          },
        },
        {
          target: 3,
          render: function (index, type, key, meta) {
            if (key["Length"] === null) {
              return "-";
            } else {
              return (
                parseInt(key["Length"]).toLocaleString("ms-MY", {
                  useGrouping: true,
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0,
                }) + " m"
              );
            }
          },
        },
        {
          target: 4,
          render: function (index, type, key, meta) {
            var $status =
              `<div class="badge badge-light-` +
              key["StatusColor"] +
              `">` +
              key["MappingStatus"] +
              `</div>`;

            return $status;
          },
        },
        {
          target: 5,
          render: function (index, type, key, meta) {
            if (key["SubmitDate"] == null) {
              return "-";
            } else {
              return key["SubmitDate"];
            }
          },
        },
        {
          orderable: false,
          targets: 6,
          render: function (index, type, key, meta) {
            let render;
            // console.log(key["subMappingID"]);

            if (key["subMappingID"] === "000") {
              render = `<div class="text-center">
                          <button type="button" id="confirm-alert" class="btn btn-icon btn-light-dark confirm-alert-btn" data-row-id="#confirm-${key["ID"]}" data-system-id="${key["system_id"]}">
                            <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
                          </button>
                        </div>`;
            } else if (key["subMappingID"] === "006") {
              render = `<div class="text-center">
                        <a href="surveys/site/progress/${key["system_id"]}" target="_blank" class="btn btn-icon btn-light-${key["StatusColor"]}">
                          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
                        </a>
                      </div>`;
            } else if (key["subMappingID"] === "008") {
              render = `<div class="text-center">
                        <a href="surveys/site/report/${key["system_id"]}" target="_blank" class="btn btn-icon btn-light-${key["StatusColor"]}">
                          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
                        </a>
                      </div>`;
            } else if (key["subMappingID"] === "009") {
              render = `<div class="text-center">
                        <a href="surveys/site/report/${key["system_id"]}" target="_blank" class="btn btn-icon btn-light-${key["StatusColor"]}">
                          <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
                        </a>
                      </div>`;
            } else if (key["subMappingID"] === "016") {
              render = `<div class="text-center">
                            <a href="qrcode/download/plan/${key["system_id"]}" id="downloadLink" download="qr_code.png" type="png">
                                <button type="button" class="btn btn-icon btn-light-dark">
                                    <i class="fad fa-qrcode fs-3"></i>
                                </button>
                            </a>

                            <button type="button" class="btn btn-icon btn-light-${key["StatusColor"]}" data-bs-toggle="modal" data-bs-target="#action-${key["subMappingID"]}${key["MappingID"]}${key["ID"]}">
                                <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
                            </button>
                        </div>`;
            } else {
              render = `<div class="text-center">
                          <button type="button" class="btn btn-icon btn-light-${key["StatusColor"]}" data-bs-toggle="modal" data-bs-target="#action-${key["subMappingID"]}${key["MappingID"]}${key["ID"]}">
                            <i class="fad fa-${key["StatusIcon"]} fs-2"></i>
                          </button>
                        </div>`;
            }

            return render;
          },
        },
      ],
      order: [[5, "desc"]], // sort by the 6th column in ascending order
    });

    // Initialize Dropzone for each modal
    datatable.on("click", "button", function () {
      let modalId = $(this).data("bs-target");
      let submitId = "#submit-" + modalId.slice(8);
      let formSurveyId = "#formSurvey-" + modalId.slice(8);
      let sysId = "#sid-" + modalId.slice(8);
      let folderId = "#f-" + modalId.slice(8);
      let formSurvey = document.querySelector(formSurveyId);

      let selectId = "#selection-" + modalId.slice(8);
      let selectId2 = "#selection2-" + modalId.slice(8);
      let selectId3 = "#selection3-" + modalId.slice(8);
      let submitButton = document.querySelector(submitId);

      if (formSurveyId.slice(0, 15) === "#formSurvey-002") {
        let stepperModal = "#stepper-" + modalId.slice(8);
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
                  html: "Maaf, sila pilih jenis kerja ukur!",
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

        var optionFormat3 = function (item) {
          if (!item.id) {
            return item.text;
          }

          return item.text;
        };

        $(document).ready(function () {
          // Update second selection options when the first selection changes
          $(selectId).on("change", function () {
            var firstSelection = $(this).val();

            //   const jsonData = JSON.stringify(formData);
            const jsonData = { survey_team: firstSelection };

            // Send Axios POST request
            api
              .post(`surveys/tasking`, jsonData)
              .then((response) => {
                console.log("Response received:", response.teamMembers);

                // Split the response into separate values
                var values = response.teamMembers;

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

        // Init Select2 --- more info: https://select2.org/
        $(selectId3).select2({
          minimumResultsForSearch: Infinity,
          templateSelection: optionFormat3,
          templateResult: optionFormat3,
        });

        $(selectId3).val(null).trigger("change");

        var validations = [];

        validations.push(
          FormValidation.formValidation(formSurvey, {
            fields: {
              "selection-survey": {
                validators: {
                  notEmpty: {
                    message: "Sila Pilih Jenis Kerja Ukur",
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
          FormValidation.formValidation(formSurvey, {
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
          })
        );

        submitButton.classList.remove("d-none");
        // Handle form submit
        submitButton.addEventListener("click", function (e) {
          // Prevent button default action
          e.preventDefault();

          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable button to avoid multiple click
          submitButton.disabled = true;

          var validator = validations[1];

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

                  toastr.success("Lantikan Kumpulan Ukur Berjaya! 🎉");

                  setTimeout(function () {
                    location.href = "/surveys/tasks/new";
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
            handleFlatpickrModal(selectedDates, dateStr, instance);
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
              console.log("jsonData : " + jsonData);

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
                    location.href = "/surveys/tasks/new";
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

        // validate attendance = 1
        // qr-code = 2

        if (progress == 1) {
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

          // function generateUniqueId() {
          //   const randomBytes = new Uint8Array(64);
          //   crypto.getRandomValues(randomBytes);
          //   return Array.from(randomBytes, (byte) =>
          //     ("0" + byte.toString(16)).slice(-2)
          //   ).join("");
          // }

          // const base64Input = document.getElementById("bit64");

          // Handle form submit
          submitButton.classList.remove("d-none");
          // Handle form submit
          submitButton.addEventListener("click", function (e) {

            // Generate unique ID
            // const uniqueId = generateUniqueId();
            // console.log(uniqueId);

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
                      location.href = "/surveys/tasks/new";
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

                const serializedArray = $(formSurvey).serializeArray().concat({
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
                      location.href = "/surveys/tasks/new";
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
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-007") {
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

          const statusId = formSurveyId.slice(15, 18);

          console.log(statusId);

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
              formData.append("systemId", document.querySelector(sysId).value);
              formData.append("folder", document.querySelector(folderId).value);
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
                  location.href = "/surveys/tasks/new";
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
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-010") {
        let planId = "#plan-" + modalId.slice(8);
        let plan_assignee = document.querySelector(planId).value;
        let validator;

        if (plan_assignee == 1) {
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
                      location.href = "/surveys/tasks/new";
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

        }
        const modalElement = document.querySelector(
          "#modal-date-range-" + modalId.slice(8)
        );
        var flatpickrModal = $(modalElement).flatpickr({
          altInput: true,
          altFormat: "d/m/Y",
          dateFormat: "Y-m-d",
          mode: "range",
          onChange: function (selectedDates, dateStr, instance) {
            handleFlatpickrModal(selectedDates, dateStr, instance);
          },
        });

      } else if (formSurveyId.slice(0, 15) === "#formSurvey-011") {

        let validator;

        validator = FormValidation.formValidation(formSurvey, {
          fields: {
            "progress-daily-udm": {
              validators: {
                notEmpty: {
                  message: "Sila Masukkan Progress Hari Ini",
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
                    "Kemaskini Progress PIU Berjaya! 🎉"
                  );

                  setTimeout(function () {
                    location.href = "/surveys/tasks/new";
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
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-012") {
        let planId = "#plan-" + modalId.slice(8);
        let plan_assignee = document.querySelector(planId).value;
        let validator;

        if (plan_assignee == 2) {
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
                      location.href = "/surveys/tasks/new";
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

        }

        const modalElement2 = document.querySelector(
          "#modal-date-range2-" + modalId.slice(8)
        );
        var flatpickrModal2 = $(modalElement2).flatpickr({
          altInput: true,
          altFormat: "d/m/Y",
          dateFormat: "Y-m-d",
          mode: "range",
          onChange: function (selectedDates, dateStr, instance) {
            handleFlatpickrModal2(selectedDates, dateStr, instance);
          },
        });
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-013") {

          let validator;

          validator = FormValidation.formValidation(formSurvey, {
            fields: {
              "progress-daily-tmp": {
                validators: {
                  notEmpty: {
                    message: "Sila Masukkan Progress TMP",
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

                    toastr.success(
                      "Kemaskini Progress PPT Berjaya! 🎉"
                    );

                    setTimeout(function () {
                      location.href = "/surveys/tasks/new";
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

      } else if (formSurveyId.slice(0, 15) === "#formSurvey-014") {
        let datePicker = "#sr-arrival-date-" + modalId.slice(8);

        let validator;
        var flatpickrSingleDate;

        var initFlatpickrSingleDate = () => {
          const element = document.querySelector(datePicker);
          flatpickrSingleDate = $(element).flatpickr({
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            onChange: function (selectedDates, dateStr, instance) {
              handleFlatpickrSingleDate(
                selectedDates,
                dateStr,
                instance
              );
            },
          });
        };

          var handleFlatpickrSingleDate = (
            selectedDates,
            dateStr,
            instance
          ) => {
            // Access the selected date using selectedDates[0]
            const selectedDate = selectedDates[0];

            // Format the date string for display in the input field
            const formattedDate = instance.formatDate(selectedDate, "d/m/Y");

            // Update the input field value with the formatted date string
            instance.altInput.value = formattedDate;

            // Do any additional processing or validation here...
        };


      initFlatpickrSingleDate();

        validator = FormValidation.formValidation(formSurvey, {
          fields: {
            "sr-arrival-remark": {
              validators: {
                notEmpty: {
                  message: "Sila Masukkan Catatan",
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

          flatpickrSingleDate.destroy();
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

                  toastr.success("Tetapan Tarikh Kehadiran SR Berjaya! 🎉");

                  setTimeout(function () {
                    location.href = "surveys/tasks/new";
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
                html: "Maaf, Sila isi <strong>Catatan</strong> untuk penetapan tarikh ini.",
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


      } else if (formSurveyId.slice(0, 15) === "#formSurvey-015") {
        let validator;

        validator = FormValidation.formValidation(formSurvey, {
          fields: {
            "set-sr-sign": {
              validators: {
                notEmpty: {
                  message: "Sila Pilih Bilangan Tandatangan",
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

                  toastr.success(
                    "Tetapan Set Bilangan Tandatangan Berjaya! 🎉"
                  );

                  setTimeout(function () {
                    location.href = "/surveys/tasks/new";
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
                html: "Maaf, Sila pilih <strong>Bilangan Tandatangan</strong> untuk tetapan ini.",
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
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-016") {
        // let uploadValue;
        let uploadId = "#upload-" + modalId.slice(8);
        let upload = document.querySelector(uploadId).value;
        console.log("upload:" + upload);

        let check1;

        const checkboxes = document.querySelectorAll(
          'input[name="selection-plan"]'
        );

        checkboxes.forEach((checkbox) => {
          if (checkbox.checked) {
            // This checkbox is checked
            // console.log(`Checkbox with value ${checkbox.value} is checked.`);

            check1 = checkbox.value;
          } else {
          }
        });

        let stepperModal = "#stepper-" + modalId.slice(8);
        // Stepper lement
        var element = document.querySelector(stepperModal);

        // Initialize Stepper
        var stepper = new KTStepper(element);

        // Handle next step
        stepper.on("kt.stepper.next", function (stepper) {
          console.log(check1);
          if (check1 === "1") {
            // Checkbox with value 1 is checked, and Checkbox with value 2 is not checked
            // Perform form validation for all checkboxes
            var validator = FormValidation.formValidation(formSurvey, {
              fields: {
                "selection-plan": {
                  validators: {
                    // notEmpty: {
                    //   message: "Sila Pilih Jenis Pelan",
                    // },
                    choice: {
                      min: 2,
                      message: "Sila Pilih Jenis Pelan",
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

            validator.validate().then(function (status) {
              if (status === "Valid") {
                stepper.goNext();
              } else {
                // Handle validation error
                Swal.fire({
                  html: "Maaf, sila lengkapkan semua maklumat yang diperlukan.",
                  icon: "warning",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function () {
                  // Handle validation error action
                });
              }
            });
          } else if (check1 !== "1") {
            // Checkbox with value 1 is not checked, and Checkbox with value 2 is not checked
            // Perform form validation for all checkboxes
            var validator = FormValidation.formValidation(formSurvey, {
              fields: {
                "selection-plan": {
                  validators: {
                    notEmpty: {
                      message: "Sila Pilih Jenis Pelan",
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

            validator.validate().then(function (status) {
              if (status === "Valid") {
                stepper.goNext();
              } else {
                // Handle validation error
                Swal.fire({
                  html: "Maaf, sila lengkapkan semua maklumat yang diperlukan.",
                  icon: "warning",
                  buttonsStyling: false,
                  confirmButtonText: "Ok, maklum!",
                  customClass: {
                    confirmButton: "btn btn-primary",
                  },
                  allowOutsideClick: false,
                }).then(function () {
                  // Handle validation error action
                });
              }
            });
          }
        });

        // Handle previous step
        stepper.on("kt.stepper.previous", function (stepper) {
          // console.log('stepper.previous');
          stepper.goPrevious();
          // KTUtil.scrollTop();
        });

        if (upload == 1) {
          let statusId = formSurveyId.slice(15, 18);
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

              // Show confirmation message using SweetAlert
              Swal.fire({
                title: "Pengesahan",
                text: "Adakah anda ingin meneruskan dengan proses muat naik Pelan Kawalan Trafik?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya !",
                cancelButtonText: "Tidak",
                customClass: {
                  confirmButton: "btn fw-bold btn-primary",
                  cancelButton: "btn fw-bold btn-light btn-active-secondary",
                },
              }).then((result) => {
                if (result.isConfirmed) {
                  // If the user clicks "Ya," set pass to 1
                  formData.pass = "1";
                } else {
                  // If the user clicks "Tidak," set pass to 2
                  formData.pass = "2";
                }

                const jsonData = JSON.stringify(formData);

                // Send notes data to the PHP API using the api.post method
                api
                  .post(`surveys/tasking`, jsonData)
                  .then((response) => {
                    // Show loading indication
                    submitButton.setAttribute("data-kt-indicator", "on");
                    // Disable button to avoid multiple clicks
                    submitButton.disabled = true;
                    // Upload files
                    pdfDropzone.processQueue();
                    dwgDropzone.processQueue();
                  })
                  .catch((error) => {
                    // Show error popup. For more info, check the plugin's official documentation: https://sweetalert2.github.io/
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
              });

              // Send Axios POST request
              // api
              //   .post(`surveys/tasking`, jsonData)
              //   .then((response) => {

              //     // Show loading indication
              //     submitButton.setAttribute("data-kt-indicator", "on");

              //     // Disable button to avoid multiple clicks
              //     submitButton.disabled = true;

              //     // Upload files
              //     pdfDropzone.processQueue();
              //     dwgDropzone.processQueue();
              //   })
              //   .catch((error) => {
              //     // Hide loading indication
              //     submitButton.removeAttribute("data-kt-indicator");

              //     // Enable button
              //     submitButton.disabled = false;

              //     // Show error popup
              //     Swal.fire({
              //       text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
              //       icon: "error",
              //       buttonsStyling: false,
              //       confirmButtonText: "Ok, faham",
              //       customClass: {
              //         confirmButton: "btn btn-primary",
              //       },
              //       allowOutsideClick: false,
              //     });
              //   });
            });
          });

          // Initialize PDF Dropzone for the modal
          let pdfDropzone = new Dropzone($(modalId).find(".pdf-dropzone")[0], {
            url: `${apps}/api/tasks/${statusId}/upload`,
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

          pdfDropzone.on("success", function (file, response) {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;

            toastr.success(response.message);

            setTimeout(function () {
              location.href = "/surveys/tasks/new";
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
          let dwgDropzone = new Dropzone($(modalId).find(".dwg-dropzone")[0], {
            url: `${apps}/api/tasks/${statusId}/upload`,
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 1024,
            acceptedFiles: ".dwg,.acad", // DWG MIME type
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

          dwgDropzone.on("success", function (file, response) {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;

            toastr.success(response.message);

            setTimeout(function () {
              location.href = "/surveys/tasks/new";
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
        } else if (upload == 2) {
          let statusId = formSurveyId.slice(15, 18);
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
                .post(`surveys/tasking`, jsonData)
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
          let pdfDropzone = new Dropzone($(modalId).find(".pdf-dropzone")[0], {
            url: `${apps}/api/tasks/${statusId}/upload`,
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

          pdfDropzone.on("success", function (file, response) {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;

            toastr.success(response.message);

            setTimeout(function () {
              location.href = "/surveys/tasks/new";
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
          let dwgDropzone = new Dropzone($(modalId).find(".dwg-dropzone")[0], {
            url: `${apps}/api/tasks/${statusId}/upload`,
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 1024,
            acceptedFiles: ".dwg,.acad", // DWG MIME type
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

          dwgDropzone.on("success", function (file, response) {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;

            toastr.success(response.message);

            // setTimeout(function () {
            //   location.href = "/surveys/tasks/new";
            // }, 2500);
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
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-017") {
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
                    location.href = "surveys/tasks/new";
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
      } else if (formSurveyId.slice(0, 15) === "#formSurvey-018") {
          // let uploadValue;
          // let uploadId = "#upload-" + modalId.slice(8);
          // let upload = document.querySelector(uploadId).value;
        // console.log("upload:" + upload);


        let folderId1 = "#f1-" + modalId.slice(8);
        let folderId2 = "#f2-" + modalId.slice(8);

            let statusId = 114;
            // PDF Dropzone
            $(modalId).on("shown.bs.modal", function () {
              // Handle form submit
              submitButton.addEventListener("click",async function (e) {
                // Prevent button default action
                e.preventDefault();

                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                await new Promise((resolve, reject) => {
                  pdfDropzone.on("queuecomplete", () => {
                    resolve();
                  });
                  // Upload files
                  pdfDropzone.processQueue();

                  dwgDropzone.on("queuecomplete", () => {
                    resolve();
                  });
                  dwgDropzone.processQueue();
                });

                const serializedArray = $(formSurvey).serializeArray();
                const formData = {};

                serializedArray.forEach((item) => {
                  formData[item.name] = item.value;
                });

                  const jsonData = JSON.stringify(formData);

                  // Send notes data to the PHP API using the api.post method
                  api
                    .post(`tasks/${statusId}/submit`, jsonData)
                    .then((response) => {
                      // // Show loading indication
                      // submitButton.setAttribute("data-kt-indicator", "on");
                      // // Disable button to avoid multiple clicks
                      // submitButton.disabled = true;

                      submitButton.removeAttribute("data-kt-indicator");
              submitButton.disabled = false;

              toastr.success(response.message);

              setTimeout(function () {
                window.location.reload();
              }, 2500);

                    })
                    .catch((error) => {
                      // Show error popup. For more info, check the plugin's official documentation: https://sweetalert2.github.io/
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

              });
            });

            // Initialize PDF Dropzone for the modal
            let pdfDropzone = new Dropzone($(modalId).find(".pdf-dropzone")[0], {
              url: `${apps}/api/tasks/${statusId}/upload`,
              paramName: "file",
              maxFiles: 1,
              maxFilesize: 1024,
              acceptedFiles: "application/pdf",
              autoProcessQueue: false,
              addRemoveLinks: true,
              sending: function (file, xhr, formData) {
                formData.append("systemId", document.querySelector(sysId).value);
                formData.append("folder", document.querySelector(folderId1).value);
              },
              accept: function (file, done) {
                done();
              },
            });

            pdfDropzone.on("success", function (file, response) {
              // submitButton.removeAttribute("data-kt-indicator");
              // submitButton.disabled = false;

              toastr.success(response.message);

              // setTimeout(function () {
              //   location.href = "/surveys/tasks/new";
              // }, 2500);
            });

            pdfDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            // $(modalId).on("hidden.bs.modal", function () {
            //   pdfDropzone.removeAllFiles();
            //   pdfDropzone.destroy();
            //   submitButton.classList.add("d-none");
            // });

            $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
              // Trigger the "hidden.bs.modal" event when the element is clicked
              let form = $(modalId).find('form')[0];
              form.reset();
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
            let dwgDropzone = new Dropzone($(modalId).find(".dwg-dropzone")[0], {
              url: `${apps}/api/tasks/${statusId}/upload`,
              paramName: "file",
              maxFiles: 1,
              maxFilesize: 1024,
              acceptedFiles: ".dwg,.acad", // DWG MIME type
              autoProcessQueue: false,
              addRemoveLinks: true,
              sending: function (file, xhr, formData) {
                formData.append("systemId", document.querySelector(sysId).value);
                formData.append("folder", document.querySelector(folderId2).value);
              },
              accept: function (file, done) {
                done();
              },
            });

            dwgDropzone.on("success", function (file, response) {
              // submitButton.removeAttribute("data-kt-indicator");
              // submitButton.disabled = false;

              toastr.success(response.message);

              // setTimeout(function () {
              //   location.href = "/surveys/tasks/new";
              // }, 2500);
            });

            dwgDropzone.on("addedfile", function () {
              submitButton.classList.remove("d-none");
            });

            // Remove files when the modal is closed
            // $(modalId).on("hidden.bs.modal", function () {
            //   dwgDropzone.removeAllFiles();
            //   dwgDropzone.destroy();
            //   submitButton.classList.add("d-none");
            // });

            $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
              // Trigger the "hidden.bs.modal" event when the element is clicked
              let form = $(modalId).find('form')[0];
              form.reset();
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

      } else {
        console.log("Error in formSurveyId");
      }
    });
  };

    var handleFlatpickrModal = (selectedDates, dateStr, instance) => {
      var minDate, maxDate; // declare variables to store selected dates

      // Update minDate and maxDate based on selected dates
      if (selectedDates && selectedDates.length === 2) {
        minDate = new Date(selectedDates[0]);
        maxDate = new Date(selectedDates[1]);
      } else {
        minDate = null;
        maxDate = null;
      }
    };
  var handleFlatpickrModal2 = (selectedDates, dateStr, instance) => {
    var minDate, maxDate; // declare variables to store selected dates

    // Update minDate and maxDate based on selected dates
    if (selectedDates && selectedDates.length === 2) {
      minDate = new Date(selectedDates[0]);
      maxDate = new Date(selectedDates[1]);
    } else {
      minDate = null;
      maxDate = null;
    }
  };

  // Public methods
  return {
    init: function () {
      table = document.querySelector("#survey-task");

      if (!table) {
        return;
      }
      initDatatable();
      handleFlatpickrModal();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  SurveyTask.init();
});
