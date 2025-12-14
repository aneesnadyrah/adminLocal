"use strict";

// Class definition
var KTDatatablesExample = (function () {
  // Shared variables
  var table;
  var datatable;
  var minDate, maxDate;
  let dropzone;

  // Private functions
  var initDatatable = function () {
    // // Get the current URL
    // let currentURL = new URL(window.location.href);
    // let urlWithOutParams = currentURL.origin + currentURL.pathname;

    // // Get the values of the parameters
    // let htaccessUrlParams = urlWithOutParams.replace(apps + '/projects/record/authority', '').split("/");
    // // urlParams[0] is ignore as it handle the first / in the trimmed url
    // let sid = htaccessUrlParams[1];

    var currentURL = window.location.href;

    // Create a URL object
    var urlObject = new URL(currentURL);

    // Get the value of the "sid" parameter
    var sid = urlObject.searchParams.get("sid");

    let link;
    console.log(urlObject);
    console.log(sid);

    // Check if "sid" exists in the URL
    if (sid !== null) {
      link = apps + "/api/records/entry/authority?sid=" + sid;
    } else {
      link = apps + "/api/records/entry/authority";
    }

    console.log(link);
    console.log(role);

    // Init datatable --- more info on datatables: https://datatables.net/manual/
    datatable = $(table).DataTable({
      ajax: link,
      columns: [
        {
          data: "reference_no",
        },
        {
          data: "authority_sort",
        },
        {
          data: "authority_district",
        },
        {
          data: "entry_appl_length",
        },
        {
          data: "status",
        },
        {
          data: "submit_date",
        },
        {
          data: null,
        },
        {
          data: "id",
        },
      ],
      order: [[7, "desc"]],
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
            var $authority_img = key["authority_logo"];

            if ($authority_img) {
              // For Avatar image
              var $output =
                `<img src="assets/media/authorities/` +
                key["authority_logo"] +
                `.png" alt="` +
                key["authority_sort"] +
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
                $name = key["authority_sort"];
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
                  key["authority_sort"] +
              `</a>
                  <!--end::Title-->
                </div>`
            );
          },
        },
        {
          target: 3,
          render: function (index, type, key, meta) {
            if (key["entry_appl_length"] === null) {
              return "-";
            } else {
              return (
                parseInt(key["entry_appl_length"]).toLocaleString("ms-MY", {
                  useGrouping: true,
                  minimumFractionDigits: 0,
                  maximumFractionDigits: 0,
                }) + "m"
              );
            }
          },
        },
        {
          target: 4,
          render: function (index, type, key, meta) {
            if (key["authority_status"] == "049") {
              if (key["deposit_status"] != null) {
                return (
                  `<div class="badge badge-light-` +
                  key["Depositstatus_color"] +
                  `">` +
                  key["DepositStatus"] +
                  `</div>`
                );
              } else {
                return (
                  `<div class="badge badge-light-` +
                  key["status_color"] +
                  `">` +
                  key["status"] +
                  `</div>`
                );
              }
            } else if (key["authority_status"] == "041") {
              if (key["LetterStatusID"] != null) {
                return (
                  `<div class="badge badge-light-` +
                  key["Letterstatus_color"] +
                  `">` +
                  key["LetterStatus"] +
                  `</div>`
                );
              } else {
                return (
                  `<div class="badge badge-light-` +
                  key["status_color"] +
                  `">` +
                  key["status"] +
                  `</div>`
                );
              }
            } else {
              return (
                `<div class="badge badge-light-` +
                key["status_color"] +
                `">` +
                key["status"] +
                `</div>`
              );
            }
          },
        },
        {
          target: 5,
          render: function (index, type, key, meta) {
            if (key["submit_date"] == null) {
              return "-";
            } else {
              return (
                "<div>" +
                (key["submit_date"]
                  ? new Date(key["submit_date"])
                      .toLocaleDateString("en-GB")
                      .replace(/\//g, "-")
                  : "-") +
                "</div>"
              );
            }
          },
        },
        {
          orderable: false,
          targets: 6,
          render: function (index, type, key, meta) {
            let StatusID = key["authority_status"];
            let render;

            if (StatusID == "040") {
              render = `<div class="text-center">
                      <button type="button" class="btn btn-icon btn-light-${key["status_color"]}"
                        data-bs-toggle="modal" data-bs-target="#action-${key["authority_status"]}${key["id"]}">
                          <i class="fad fa-${key["status_icon"]} fs-2"></i>
                      </button>
                    </div>`;
            } else if (StatusID == "041" || StatusID == "045") {
              render = `<div class="text-center">
              <button type="button" class="btn btn-icon btn-light-${key["status_color"]}"
                data-bs-toggle="modal" data-bs-target="#action-${key["authority_status"]}${key["id"]}">
                  <i class="fad fa-${key["status_icon"]} fs-2"></i>
              </button>
            </div>`;
            } else if (StatusID == "049") {
              // deposit related action control
              if (role == 52) {
                if (
                  key["deposit_status"] == 1 ||
                  key["deposit_status"] == 3 ||
                  key["deposit_status"] == 4
                ) {
                  // button to
                  // https://ucidos.test/projects/wayleave/deposit/vp20du23jA00Ha12?aid=15
                  render = `<div class="text-center">
                  <a href="projects/wayleave/deposit/${key["system_id"]}?aid=${key["flw_authorities_id"]}"  class="btn btn-icon btn-light-${key["Depositstatus_color"]}">
                  <i class="fad fa-${key["Depositstatus_icon"]} fs-2"></i>
                  </a>
              </div>`;
                } else if (key["deposit_status"] == 2) {
                  // button to
                  render = `<div class="text-center">
                  <a href="#"  class="btn btn-icon btn-light-secondary disabled">
                  <i class="fad fa-${key["status_icon"]} fs-2"></i>
                  </a>
              </div>`;
                } else {
                  render = `<div class="text-center">
                            <a type="submit" id="confirm-btn-wc" class="btn btn-icon btn-light-${key["status_color"]} confirm-btn-wc" data-row-id="#confirm-${key["ID"]}" data-system-id="${key["system_id"]}" data-authority-name="${key["sort_name"]}" data-flw-auth-id="${key["flw_authorities_id"]}" data-authority-id="${key["authority_id"]}">
                              <i class="fad fa-${key["status_icon"]} fs-2"></i>
                            </a>
                          </div>`;
                }
              } else if (role == 73) {
                if (
                  key["deposit_status"] == 1 ||
                  key["deposit_status"] == 3 ||
                  key["deposit_status"] == 4
                ) {
                  // button to
                  render = `<div class="text-center">
                  <a href="#"  class="btn btn-icon btn-light-secondary disabled">
                  <i class="fad fa-${key["status_icon"]} fs-2"></i>
                  </a>
              </div>`;
                } else if (key["deposit_status"] == 2) {
                  // buton to view page
                  render = `<div class="text-center">
                  <a href="projects/wayleave/deposit/${key["system_id"]}?aid=${key["flw_authorities_id"]}"  class="btn btn-icon btn-light-${key["Depositstatus_color"]}">
                  <i class="fad fa-${key["Depositstatus_icon"]} fs-2"></i>
                  </a>
              </div>`;
                } else {
                  render = `<div class="text-center">
                                <a href="#"  class="btn btn-icon btn-light-secondary disabled">
                                <i class="fad fa-${key["status_icon"]} fs-2"></i>
                                </a>
                            </div>`;
                }
              } else {
                render = `<div class="text-center">
                                <a href="#"  class="btn btn-icon btn-light-secondary disabled">
                                <i class="fad fa-${key["status_icon"]} fs-2"></i>
                                </a>
                            </div>`;
              }
            } else {
              // render = `<div class="text-center">
              //                   <a href="projects/details/${key["system_id"]}" class="btn btn-icon btn-light-${key["status_color"]}">
              //                       <i class="fad fa-${key["status_icon"]} fs-3"></i>
              //                   </a>
              //               </div>`;
              render = `<div class="text-center">
                      <button type="button" class="btn btn-icon btn-light-${key["status_color"]}"
                        data-bs-toggle="modal" data-bs-target="#action-${key["authority_status"]}${key["id"]}">
                          <i class="fad fa-${key["status_icon"]} fs-2"></i>
                      </button>
                    </div>`;
            }

            return render;
          },
        },
        {
          target: 7,
          visible: false,
          render: function (index, type, key, meta) {
            return `<div">` + key["id"] + `</div>`;
          },
        },
      ],
    });

    datatable.on("click", "button", function () {
      let modalId = $(this).data("bs-target");
      let submitId = "#submit-" + modalId.slice(8);
      let formId = "#form-" + modalId.slice(8);
      let formSurveyId = "#formSurvey-" + modalId.slice(8);
      let sysId = "#sid-" + modalId.slice(8);
      let folderId = "#f-" + modalId.slice(8);
      let authId = "#aid-" + modalId.slice(8);
      let form = document.querySelector(formId);
      let formSurvey = document.querySelector(formSurveyId);
      let authorityId = document.querySelector(authId);

      let selectId = "#selection-" + modalId.slice(8);
      let selectId2 = "#selection2-" + modalId.slice(8);
      let selectId3 = "#selection3-" + modalId.slice(8);

      let submitButton = document.querySelector(submitId);
      let previousId = "#previous-" + modalId.slice(8);
      let previousButton = document.querySelector(previousId);

      //1 dropzone 1 date 1 note
      if (
        formId.slice(0, 9) === "#form-041" ||
        formId.slice(0, 9) === "#form-045" ||
        formId.slice(0, 9) === "#form-821" ||
        formId.slice(0, 9) === "#form-871" ||
        formId.slice(0, 9) === "#form-872" ||
        formId.slice(0, 9) === "#form-127" ||
        formId.slice(0, 9) === "#form-134" ||
        formId.slice(0, 9) === "#form-142" ||
        formId.slice(0, 9) === "#form-153" ||
        formId.slice(0, 9) === "#form-093" ||
        formId.slice(0, 9) === "#form-132" ||
        formId.slice(0, 9) === "#form-141" ||
        formId.slice(0, 9) === "#form-082" ||
        formId.slice(0, 9) === "#form-122" ||
        formId.slice(0, 9) === "#form-152"
      ) {
        // var SysID = "#system-id-" + modalId.slice(8);
        // var AID = "#aid-" + modalId.slice(8);
        let labelId = "#label-" + modalId.slice(8);
        let label = document.querySelector(labelId).value;
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
            authId
          );
        });


        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
      }
      //1 dropzone 2 date 1 note
      else if (
        formId.slice(0, 9) === "#form-046" ||
        formId.slice(0, 9) === "#form-083" ||
        formId.slice(0, 9) === "#form-087" ||
        formId.slice(0, 9) === "#form-088" ||
        formId.slice(0, 9) === "#form-121" ||
        formId.slice(0, 9) === "#form-122" ||
        formId.slice(0, 9) === "#form-131" ||
        formId.slice(0, 9) === "#form-133" ||
        formId.slice(0, 9) === "#form-143" ||
        formId.slice(0, 9) === "#form-151"
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
            folderId,
            authId
          );
        });

        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
      }

      //1 dropzone 1 date 1 input 1 note
      else if (
        formId.slice(0, 9) === "#form-822" ||
        formId.slice(0, 9) === "#form-891" ||
        formId.slice(0, 9) === "#form-129" ||
        formId.slice(0, 9) === "#form-145"
      ) {
        let labelId = "#label-" + modalId.slice(8);
        let labelId2 = "#label2-" + modalId.slice(8);
        let label = document.querySelector(labelId).value;
        let label2 = document.querySelector(labelId2).value;
        let folder = document.querySelector(folderId).value;
        var systemId = document.querySelector(sysId).value;
        // var authorityId = document.querySelector(AID).value;

        console.log(label);
        console.log(label2);
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
                    message: "Input diperlukan",
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
            authId
          );
        });

        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
      }
      //1 dropzone 2 date 1 input 1 note
      else if (
        formId.slice(0, 9) === "#form-124" ||
        formId.slice(0, 9) === "#form-089" ||
        formId.slice(0, 9) === "#form-154" ||
        formId.slice(0, 9) === "#form-144"
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
        console.log(label);
        console.log(label2);
        console.log(label3);
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
                    message: "Input diperlukan",
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
            authId
          );
        });

        // Remove files when the modal is closed
        // $(modalId).on("hidden.bs.modal", function () {
        //   dropzone.removeAllFiles();
        //   dropzone.destroy();
        //   submitButton.classList.add("d-none");
        // });


        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });

      }
      // rpkwc
      else if (formId.slice(0, 9) === "#form-040") {
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
            folderId,
            authId
          );
        });

        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
      } else if (formId.slice(0, 9) === "#form-042") {
        var systemId = document.querySelector(sysId).value;
        // var authorityId = document.querySelector(AID).value;

        $(modalId).on("shown.bs.modal", function () {
          let form = document.querySelector(formId);
          let validator;

          validator = FormValidation.formValidation(form, {
            fields: {
              "dt-kil-ltr": {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              "dt-kil-recv": {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              "dt-kil-strt": {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              "dt-kil-end": {
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
            authId
          );
        });

        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
      } else if (formId.slice(0, 9) === "#form-084") {
        var systemId = document.querySelector(sysId).value;
        // var authorityId = document.querySelector(AID).value;

        $(modalId).on("shown.bs.modal", function () {
          let form = document.querySelector(formId);
          let validator;

          validator = FormValidation.formValidation(form, {
            fields: {
              dt_wp_perakuan_created: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dt_wp_perakuan_notify: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              no_wp_perakuan: {
                validators: {
                  notEmpty: {
                    message: "Input diperlukan",
                  },
                },
              },
              dt_wday_strt: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dt_wday_end: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dt_wend_strt: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dt_wend_end: {
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
            authId
          );
        });

        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
      } else if (formId.slice(0, 9) === "#form-123") {
        var systemId = document.querySelector(sysId).value;
        // var authorityId = document.querySelector(AID).value;

        $(modalId).on("shown.bs.modal", function () {
          let form = document.querySelector(formId);
          let validator;

          validator = FormValidation.formValidation(form, {
            fields: {
              dt_cpc_dlp_start: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dt_cpc_dlp_end: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dt_cpc_appv_ltr: {
                validators: {
                  notEmpty: {
                    message: "Input diperlukan",
                  },
                },
              },
              dt_cpc_appc_recv: {
                validators: {
                  notEmpty: {
                    message: "Tarikh diperlukan",
                  },
                },
              },
              dlp_period: {
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
            authId
          );
        });

        // $(document).ready(function () {
        // Remove files when the modal is closed
        $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
          // Trigger the "hidden.bs.modal" event when the element is clicked

          dropzone.removeAllFiles();
          dropzone.destroy();
          $(modalId).find('form')[0].reset();
          submitButton.classList.add("d-none");
        });
        // });
      }
    });
  };

  // Hook export buttons
  var exportButtons = () => {
    const documentTitle = "Rumusan Penjejak";
    var buttons = new $.fn.dataTable.Buttons(table, {
      buttons: [
        {
          extend: "copyHtml5",
          title: documentTitle,
        },
        {
          extend: "excelHtml5",
          title: documentTitle,
        },
        {
          extend: "csvHtml5",
          title: documentTitle,
        },
        {
          extend: "pdfHtml5",
          title: documentTitle,
          exportOptions: {
            columns: ":not(.no-export)", // Export only visible columns
          },
          customize: function (doc) {
            // Set landscape orientation
            doc.pageOrientation = "landscape";
          },
        },
      ],
    })
      .container()
      .appendTo($("#kt_datatable_example_buttons"));

    // Hook dropdown menu click event to datatable export buttons
    const exportButtons = document.querySelectorAll(
      "#kt_datatable_example_export_menu [data-kt-export]"
    );
    exportButtons.forEach((exportButton) => {
      exportButton.addEventListener("click", (e) => {
        e.preventDefault();

        // Get clicked export value
        const exportValue = e.target.getAttribute("data-kt-export");
        const target = document.querySelector(
          ".dt-buttons .buttons-" + exportValue
        );

        // Trigger click event on hidden datatable export buttons
        target.click();
      });
    });
  };

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

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector('[data-table-filter="search"]');
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  var initFlatpickrSingleDate = () => {
    var elements = document.querySelectorAll("[data-datepicker-id]");
    elements.forEach(function (element) {
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

  // Handle flatpickr --- more info: https://flatpickr.js.org/events/
  var handleFlatpickr = (selectedDates, dateStr, instance) => {
    minDate = selectedDates[0] ? new Date(selectedDates[0]) : null;
    maxDate = selectedDates[1] ? new Date(selectedDates[1]) : null;

    // Datatable date filter --- more info: https://datatables.net/extensions/datetime/examples/integration/datatables.html
    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
      var min = minDate;
      var max = maxDate;
      var dateAdded = new Date(moment(data[3], "YYYY-MM-DD"));

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

  function handleActionSubmit(
    authority_status,
    form,
    submitButton,
    validator = null,
    dropzone = null,
    modalId = null,
    sysId = null,
    folderId = null,
    authId = null
  ) {
    let $redirect;

    if (authority_status === "005") {
      $redirect = "/geospatial/PCL/extract/" + $(sysId).val();
    } else if (
      role === 33 &&
      (authority_status === "029" ||
        authority_status === "031" ||
        authority_status === "032" ||
        authority_status === "033" ||
        authority_status === "034" ||
        authority_status === "040" ||
        authority_status === "041" ||
        authority_status === "045")
    ) {
      $redirect = "/geospatial/PCL/extract/" + $(sysId).val();
    } else {
      $redirect = "/projects/record/authority?sid=" + $(sysId).val();
    }

    if (typeof validator === "undefined") {
      submitButton.classList.remove("d-none");
    }

    // Handle form submit
    submitButton.addEventListener("click",async function (e) {
      // Prevent button default action
      e.preventDefault();

      if (typeof validator === "undefined" || validator === null) {
        if (authority_status === "002" || authority_status === "996") {
          let payment_method = document.querySelector(
            'input[name="payment_method"]'
          ).value;

          if (
            typeof Dropzone !== "undefined" &&
            document.querySelector(modalId) &&
            document.querySelector(sysId) &&
            document.querySelector(folderId) &&
            payment_method === "2"
          ) {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple clicks
            submitButton.disabled = true;

            dropzone.processQueue();
          }
        } else {
          // Show loading indication
          submitButton.setAttribute("data-kt-indicator", "on");

          // Disable button to avoid multiple clicks
          submitButton.disabled = true;

          if (
            typeof Dropzone !== "undefined" &&
            document.querySelector(modalId) &&
            document.querySelector(sysId) &&
            document.querySelector(folderId)
          ) {
              // Process the Dropzone queue and wait for it to complete
              await new Promise((resolve, reject) => {
                dropzone.on("queuecomplete", () => {
                  resolve();
                });
                dropzone.processQueue();
              });
          } else {

          }

          const serializedArray = $(form).serializeArray();
          const formData = {};

          serializedArray.forEach((item) => {
            formData[item.name] = item.value;
          });

          const jsonData = JSON.stringify(formData);

          // Send Axios POST request
          api
            .post(`tasks/${authority_status}/submit`, jsonData)
            .then((response) => {
              // Hide loading indication
              // submitButton.removeAttribute("data-kt-indicator");
              // Enable button
              // submitButton.disabled = false;

              // // Show loading indication
              // submitButton.setAttribute("data-kt-indicator", "on");

              // // Disable button to avoid multiple clicks
              // submitButton.disabled = true;

              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

              // if (
              //   typeof Dropzone !== "undefined" &&
              //   document.querySelector(modalId) &&
              //   document.querySelector(sysId) &&
              //   document.querySelector(folderId)
              // ) {
              // } else {
                console.log('submit : ' + response);
                toastr.success(response.message);
                setTimeout(function () {
                  // location.href = $redirect;
                  window.location.reload();
                }, 2500);
              // }
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
        }
      } else if (typeof validator.validate === "function") {
        validator.validate().then(async function (result) {
          if (result === "Valid") {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple clicks
            submitButton.disabled = true;

            if (
              typeof Dropzone !== "undefined" &&
              document.querySelector(modalId) &&
              document.querySelector(sysId) &&
              document.querySelector(folderId)
            ) {
                // Process the Dropzone queue and wait for it to complete
                await new Promise((resolve, reject) => {
                  dropzone.on("queuecomplete", () => {
                    resolve();
                  });
                  dropzone.processQueue();
                });
            } else {

            }

            const serializedArray = $(form).serializeArray();
            const formData = {};

            serializedArray.forEach((item) => {
              formData[item.name] = item.value;
            });

            const jsonData = JSON.stringify(formData);

            // Send Axios POST request
            api
              .post(`tasks/${authority_status}/submit`, jsonData)
              .then((response) => {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                // Enable button
                submitButton.disabled = false;

                // if (
                //   typeof Dropzone !== "undefined" &&
                //   document.querySelector(modalId) &&
                //   document.querySelector(sysId) &&
                //   document.querySelector(folderId)
                // ) {

                // } else {
                  console.log('submit : ' + response);
                  toastr.success(response.message);
                  setTimeout(function () {
                    // location.href = $redirect;
                    window.location.reload();

                    // dropzone.removeAllFiles();
                    // dropzone.destroy();
                    // $(modalId).find('form')[0].reset();
                    // $(modalId).modal("hide");
                  }, 2500);
                // }
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
          } else {
            // Form is invalid, handle the validation errors
            console.log("Form is invalid");
            // Display error messages or perform other actions here
          }
        });
      } else {
        // Hide loading indication
        submitButton.removeAttribute("data-kt-indicator");

        // Enable button
        submitButton.disabled = false;
      }
    });

    if (
      typeof Dropzone !== "undefined" &&
      document.querySelector(modalId) &&
      document.querySelector(sysId) &&
      document.querySelector(folderId) && document.querySelector(authId)
    ) {
      // Initialize Dropzone for the modal
      dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
        url: `${apps}/api/tasks/${authority_status}/upload`,
        paramName: "file",
        maxFiles: 1,
        maxFilesize: 1024,
        acceptedFiles: "application/pdf",
        autoProcessQueue: false,
        addRemoveLinks: true,
        sending: function (file, xhr, formData) {

          console.log("Sending function is being called");
          formData.append("systemId", document.querySelector(sysId).value);
          formData.append("folder", document.querySelector(folderId).value);
          formData.append("authorityId", document.querySelector(authId).value);

        },
        accept: function (file, done) {
          done();
        },
      });



      if (authority_status === "002" || authority_status === "996") {
        let payment_method = document.querySelector(
          'input[name="payment_method"]'
        ).value;

        if (payment_method === "2") {
          dropzone.on("success", function (file, response) {
            // Show loading indication
            submitButton.setAttribute("data-kt-indicator", "on");

            // Disable button to avoid multiple clicks
            submitButton.disabled = true;

            // Assuming you have a variable to store the Dropzone response
            const dropzoneResponse = {
              attachDetails: response.attachDetails,
              url: response.url,
              mimeType: response.mimeType,
              fileSize: response.fileSize,
            };

            const serializedArray = $(form).serializeArray();
            const formData = {};

            serializedArray.forEach((item) => {
              formData[item.name] = item.value;
            });

            const combinedData = { ...formData, ...dropzoneResponse };

            const jsonData = JSON.stringify(combinedData);
            // console.log("jsonData:", jsonData);

            // Send Axios POST request
            api
              .post(`tasks/${authority_status}/submit`, jsonData)
              .then((responseSubmit) => {
                // Hide loading indication
                submitButton.removeAttribute("data-kt-indicator");

                // // Enable button
                // submitButton.disabled = false;

                // Disable button to avoid multiple clicks
                submitButton.disabled = true;

                toastr.success(responseSubmit.message);
                setTimeout(function () {
                  location.href = $redirect;
                }, 2500);
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
        } else {
          dropzone.on("success", function (file, response) {
            submitButton.removeAttribute("data-kt-indicator");
            submitButton.disabled = false;

            toastr.success(response.message);

            setTimeout(function () {
              location.href = $redirect;
            }, 2500);
          });
        }
      } else {
        dropzone.on("success", function (file, response) {
          // submitButton.removeAttribute("data-kt-indicator");
          // submitButton.disabled = false;

          // console.log('dropzone : ' + response);
          // toastr.success(response.message);

          // setTimeout(function () {
          //   location.href = $redirect;
          // }, 3500);
        });
      }

      dropzone.on("addedfile", function () {
        submitButton.classList.remove("d-none");
      });

      // Remove files when the modal is closed
      $(modalId).on("click", '[data-bs-dismiss="modal"]', function () {
        // Trigger the "hidden.bs.modal" event when the element is clicked

        dropzone.removeAllFiles();
        dropzone.destroy();
        $(modalId).find('form')[0].reset();
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
  }

  // Public methods
  return {
    init: function () {
      table = document.querySelector("#tracker-entry");

      if (!table) {
        return;
      }

      initDatatable();
      exportButtons();
      initFlatpickr();
      initFlatpickrSingleDate();
      handleSearchDatatable();
      // handleActionSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTDatatablesExample.init();
});
