"use strict";

// Class definition
var TeamSurvey = (function () {
  // Shared variables
  var table;
  var datatable;

  // Private functions
  var initDatatable = function () {
    // Init datatable --- more info on datatables: https://datatables.net/manual/
    datatable = $(table).DataTable({
      ajax: apps + "/api/surveys/team/team",
      columns: [
        { data: "survey_team" },
        { data: "total_survey_member" },
        { data: "team_members" },
        { data: null },
      ],
      info: false,
      language: {
        loadingRecords: "Sila Tunggu...",
        zeroRecords: "Tiada Rekod Dijumpai",
      },
      pageLength: 10,
      columnDefs: [
        {
          target: 0,
          render: function (index, type, key, meta) {
            return (
              `<div class="d-flex align-items-center">
                <!--begin::Title-->
                <a href="javascript:void()" class="text-gray-700 text-hover-primary">` +
              "Kumpulan " +
              key["survey_team"] +
              `</a>
                <!--end::Title-->
              </div>`
            );
          },
        },
        {
          target: 1,
          render: function (index, type, key, meta) {
            var $nums,
              $num = key["team_members"].slice(1, -1).split(",").length;
            if ($num === null) {
              $nums = "0";
            } else {
              $nums = $num;
            }
            return (
              parseInt($nums).toLocaleString("ms-MY", {
                useGrouping: true,
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
              }) + " orang"
            );
          },
        },
        {
          target: 2,
          render: function (index, type, key, meta) {
            var $named = key["team_members"].slice(1, -1);
            var members = $named.split(",");

            var $output = "";

            var colors = [
              "success",
              "danger",
              "warning",
              "info",
              "dark",
              "primary",
              "secondary",
            ];

            for (var i = 0; i < members.length; i++) {
              var member = members[i].trim();
              var $initials = member.charAt(0).toUpperCase();
              var randomIndex = Math.floor(Math.random() * colors.length);
              var $state = colors[randomIndex];

              colors.splice(randomIndex, 1); // Remove the selected color from the array

              $output += '<div class="symbol symbol-circle symbol-40px">';
              $output +=
                '<div class="symbol-label fs-5 fw-semibold bg-' +
                $state +
                " text-inverse-" +
                $state +
                '" data-bs-toggle="tooltip" data-bs-placement="bottom" title="' +
                member +
                '">' +
                $initials +
                "</div>";
              $output += "</div>";
            }

            return $output;
          },
        },
        {
          orderable: false,
          targets: 3,
          render: function (index, type, key, meta) {
            let render;
            render =
              `<div class="text-start">
              <button type="button" class="btn btn-icon btn-light-primary"
              data-bs-toggle="modal" data-bs-target="#action-edit-` +
              key["id"] +
              `">
                <i class="fad fa-pen fs-2"></i>
              </button>
              <button type="button" id="delete_button" data-row-id="action-delete-` +
              key["id"] +
              `" class="btn btn-icon btn-light-danger">
                <i class="fad fa-trash-can fs-2"></i>
              </button>
            </div>`;
            return render;
          },
        },
      ],
    });

    $(table).on("click", "a", function (e) {
      // Prevent default link behavior
      e.preventDefault();

      // Get the href value of the clicked link
      var href = $(this).attr("data-source");
      var lightbox = new FsLightbox();
      lightbox.props.sources = [href];
      lightbox.open();
    });

    // Initialize for each modal
    datatable.on("click", "button", function () {
      var key = datatable.row($(this).closest("tr")).data();
      let modalId = $(this).data("bs-target");
      let submitId = "#submit-survey-team-" + key["id"];
      let formId = "#form-survey-team-" + key["id"];
      let selectId = "#selection-survey-team-" + key["id"];
      let submitButton = document.querySelector(submitId);
      console.log(submitId);

      if (formId === "#form-survey-team-" + key["id"]) {
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

          // Init Select2 --- more info: https://select2.org/
          $(selectId).select2({
            minimumResultsForSearch: Infinity,
            templateSelection: optionFormat,
            templateResult: optionFormat,
          });

          $(selectId).val(null).trigger("change");

          validator = FormValidation.formValidation(form, {
            fields: {
              "survey-team-edit": {
                validators: {
                  notEmpty: {
                    message: "Sila Masukkan Nama Kumpulan",
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
              console.log(status);
              if (status == "Valid") {
                // Show loading indication
                submitButton.setAttribute("data-kt-indicator", "on");

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                const formData = new FormData(form);

                const data = {};

                formData.forEach((value, name) => {
                  if (data.hasOwnProperty(name)) {
                    if (!Array.isArray(data[name])) {
                      data[name] = [data[name]];
                    }
                    data[name].push(value);
                  } else {
                    data[name] = value;
                  }
                });

                const jsonData = JSON.stringify(data);

                console.log(jsonData);

                api
                  .post(`surveys/team/tasking`, jsonData)
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

                    toastr.success("Kumpulan Survey Berjaya Di Kemaskini! 🎉");

                    setTimeout(function () {
                      location.href = "surveys/team";
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
                  html: "Maaf, Sila pilih <strong>Ahli Kumpulan Survey</strong> untuk permohonan ini.",
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
      } else {
      }

      // Delete button clicked
      if ($(this).attr("id") === "delete_button") {
        const rowId = this.getAttribute("data-row-id");

        // Show confirmation message using SweetAlert
        Swal.fire({
          title: "Adakah anda pasti mahu memadamkan kumpulan ini?",
          text: "Anda tidak akan dapat mengembalikan kumpulan ini!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Ya !",
          cancelButtonText: "Batal",
          customClass: {
            confirmButton: "btn fw-bold btn-danger",
            cancelButton: "btn fw-bold btn-light btn-active-light-primary",
          },
        }).then((result) => {
          if (result.isConfirmed) {
            const jsonData = {
              row_id: rowId,
            };

            api
              .post(`surveys/team/delete`, jsonData)
              .then((response) => {
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

                toastr.success("Kumpulan berjaya dipadamkan! 🎉");

                // Navigate to the new page after a successful API call
                setTimeout(function () {
                  location.href = "surveys/team";
                }, 2500);
              })
              .catch((error) => {
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
          }
        });
      }
    });
  };

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector('[data-table-filter="search"]');
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  // Public methods
  return {
    init: function () {
      table = document.querySelector("#survey-team");

      if (!table) {
        return;
      }

      initDatatable();
      handleSearchDatatable();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  TeamSurvey.init();
});
