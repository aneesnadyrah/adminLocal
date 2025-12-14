"use strict";

// Class definition
var addNotes = (function () {
  // Private variables
  var modalRef;
  var modalNote;

  var formAuth;
  var formNotes;
  var formReference;

  var formViewAuth;
  var formViewAttach;
  var formViewLetter;
  var formViewOther;
  var formViewNote;

  var refRadio;
  var writeRadio;
  var attachRadio;
  var attachSelect;
  var letterSelect;
  var authorityElement;
  var authCheckbox;
  var dropzoneNotes;
  var refButton;
  var backButton;
  var submitButton;
  var pdfDropzone;
  var noteType;
  var otherLetterName;
  var validator;

  var searchHandler;
  var searchInput;
  var resultsElement;
  var wrapperElement;
  var emptyElement;
  var searchObject;
  var searchView = `
  <!--begin:Option-->
  <label class="d-flex flex-stack mb-5 cursor-pointer">
      <!--begin:Label-->
      <span class="d-flex align-items-center me-2">
          <!--begin:Icon-->
          <div class="symbol symbol-50px me-6">
            <img alt="Pic" src="{{providerLogo}}" />
          </div>
          <!--end:Icon-->

          <!--begin:Info-->
          <span class="d-flex flex-column">
              <span class="fw-bold fs-6">{{referenceNo}}</span>
              <span class="fs-7 text-muted">{{District}}</span>
          </span>
          <!--end:Info-->
      </span>
      <!--end:Label-->

      <!--begin:Input-->
      <span class="form-check form-check-custom form-check-solid">
          <input id="reference_checkbox" class="form-check-input" type="radio"  name="system_id" value="{{systemID}}"/>
      </span>
      <!--end:Input-->
  </label>
  <!--end::Option-->
  <div class="separator separator-dotted mb-2"></div>`;

  var authorityView = `
  <!--begin::Col-->
  <div class="form-check form-check-custom form-check-solid m-2">
      <input class="form-check-input check-authorities" type="checkbox" value="{{authorityID}}" name="authorities"/>
      <div class="d-flex flex-column align-items-center ms-3 w-100px">
        <div class="form-check-wrapper mb-0">
          <img class="w-30px" src="assets/media/authorities/{{authorityLogo}}.png"/>
        </div>
        <div class="form-check-label fw-semibold text-center">
          {{authorityName}}
        </div>
      </div>

  </div>
  <!--end::Col-->
  `;

  const searchProject = () => {
    // Private functions
    var processs = function (search) {
      api
        .get("search/projects/" + searchObject.getQuery())
        .then((response) => {
          var data = response.data;

          // Clear the results container
          resultsElement.innerHTML = "";

          // Loop through the data
          if (data.length > 0) {
            // Hide results
            resultsElement.classList.remove("d-none");
            // Show empty message
            emptyElement.classList.add("d-none");

            authorityElement.classList.add("d-none");



            data.forEach(function (item) {
              // Create a new element to display the data
              var searchData = document.createElement("div");

              searchData.innerHTML = searchView
                .replace(/{{ID}}/g, item.ID)
                .replace(/{{systemID}}/g, item.systemID)
                .replace(/{{providerLogo}}/g, item.providerData.logo)
                .replace(/{{referenceNo}}/g, item.referenceNo)
                .replace(/{{District}}/g, item.District)
                .replace(/{{action}}/g, "pin-project")
                .replace(/{{color}}/g, "dark");

              // Append the new element to the results container
              resultsElement.appendChild(searchData);


            });
          } else {
            // Hide results
            resultsElement.classList.add("d-none");
            // Show empty message
            emptyElement.classList.remove("d-none");

            authorityElement.classList.add("d-none");


          }
        })
        .catch((error) => {
          console.log(response);
          // Hide results
          resultsElement.classList.add("d-none");
          // Show empty message
          emptyElement.classList.remove("d-none");

          authorityElement.classList.add("d-none");


        });
      // Complete search
      search.complete();
    };

    var clear = function (search) {

      formReference.reset();
      // Hide results
      resultsElement.classList.add("d-none");
      // Hide empty message
      emptyElement.classList.add("d-none");

      authorityElement.classList.add("d-none");

      refRadio.checked = '';


    };

    // Ajax search handler
    searchObject.on("kt.search.process", processs);
    // Clear handler
    searchObject.on("kt.search.clear", clear);
  };
  const RefModalSetup = () => {
    var lastSelectedValue = null;

    modalRef.addEventListener("click", function (event) {

      if (event.target && event.target.id === "reference_checkbox") {


        refRadio = modalRef.querySelector("#reference_checkbox");

        if (refRadio.checked === true) {
          var systemId = refRadio.value;


          // Clear existing data only if the value has changed
          if (systemId !== lastSelectedValue) {
            lastSelectedValue = systemId; // Update the last selected value

            // Clear existing data
            while (authorityElement.firstChild) {
              authorityElement.removeChild(authorityElement.firstChild);
            }


            api
              .get("notes/authority/" + systemId)
              .then((response) => {



                // Loop through the data
                if (response.length > 0) {

                  authorityElement.classList.remove("d-none");

                  response.forEach(function (item) {

                    // Create a new element to display the data
                    var authorityData = document.createElement("div");
                    authorityData.innerHTML = authorityView
                      .replace(/{{authorityID}}/g, item.id)
                      .replace(/{{authorityLogo}}/g, item.logo)
                      .replace(/{{authorityName}}/g, item.sort_name);

                    // Append the new element to the results container
                    authorityElement.appendChild(authorityData);

                  });

                  authCheckbox = authorityElement.querySelectorAll('.check-authorities');

                      authCheckbox.forEach(checkbox => {
                        checkbox.addEventListener('click', () => {
                          // Uncheck all checkboxes
                          authCheckbox.forEach(otherCheckbox => {
                            if (otherCheckbox !== checkbox) {
                              otherCheckbox.checked = false;
                            }
                          });
                        });
                      });

                } else {

                  authorityElement.classList.add("d-none");

                }
              })
              .catch((error) => {

                console.log(response);

                authorityElement.classList.add("d-none");

              });



          }
        }
      }
    });

    // Add an event listener for the "Seterusnya" button click
    refButton.addEventListener("click", function () {

      if (refRadio.checked === true) {
        var systemId = refRadio.value;
        $(modalRef).modal('hide');
        new bootstrap.Modal(modalNote).show();
      }

    });
  };

  const NoteModalSetup = () => {

    function radioChange() {

      if (attachRadio.checked) {
        // Clear specific input fields by setting their values to an empty string
        pdfDropzone.removeAllFiles();

        api
        .get("notes/getListLetter")
        .then((response) => {

          while (letterSelect.firstChild) {
            letterSelect.removeChild(letterSelect.firstChild);
          }

          // Loop through the data
          if (response.length > 0) {

            // Create the first option for placeholder
            addOption('', '');

            // Loop through the response and add options
            response.forEach(function(item) {
              addOption(item.code_name, item.details);
            });

            // Create the option for 'Lain-lain Lampiran' after looping through the response
            addOption('LL-OF', 'Lain-lain Lampiran');

          } else {

          }


        })
        .catch((error) => {
          console.log(response);
        });

        formViewAttach.classList.remove("d-none");
        formViewNote.classList.remove("d-none");
        const attachType = attachSelect.value;


        if (attachType === "TL") {
          formViewLetter.classList.add("d-none");
        } else if (attachType === "SRT") {
          formViewLetter.classList.remove("d-none");

        }
      } else if (writeRadio.checked) {

        formViewAttach.classList.add("d-none");
        formViewNote.classList.remove("d-none");
      }
    }

    // Function to add a new option to the select element
    function addOption(value, text) {
      var newOption = document.createElement('option');
      newOption.value = value;
      newOption.text = text;
      letterSelect.appendChild(newOption);
    }

    function select2Change() {

      radioChange(); // Call the radioChange function when the select2 value changes
    }

    function otherLetter() {

      const letterType = letterSelect.value;

        if (letterType === "LL-OF") {
          formViewOther.classList.remove("d-none");
        } else {
          formViewOther.classList.add("d-none");
        }
    }



    attachRadio.addEventListener("change", radioChange);
    writeRadio.addEventListener("change", radioChange);

    $("#select_lampiran").on("change", select2Change);

    $("#letter_selection").on("change", otherLetter);

    backButton.addEventListener("click", function () {
      $("#select_lampiran").val(null).trigger('change');
      pdfDropzone.removeAllFiles();
      formNotes.reset();
      formViewAttach.classList.add("d-none");
      formViewNote.classList.add("d-none");
      formViewLetter.classList.add("d-none");
      formViewOther.classList.add("d-none");
    })
  };

  const handleDropzone = () => {
      pdfDropzone = new Dropzone($(modalNote).find(".pdf-dropzone")[0], {
        url: `${apps}/api/notes/upload`,
        paramName: "file",
        maxFiles: 1,
        maxFilesize: 1024,
        acceptedFiles: "application/pdf",
        autoProcessQueue: false,
        addRemoveLinks: true,
        sending: function (file, xhr, formData) {


          formData.append("systemId", modalRef.querySelector("#reference_checkbox").value);

          const attachType = attachSelect.value;
          if (attachType === "TL") {
            formData.append("folder",'TL-OF');
          } else if (attachType === "SRT") {
            const letterType = letterSelect.value;

            formData.append("folder",letterType);
          }

          const otherLetter = otherLetterName.value;
          if (otherLetter.length > 0) {
            formData.append("other-letter-name", otherLetter);
          }

          var authIdCheck = authorityElement.querySelectorAll('.check-authorities');

          authIdCheck.forEach(function (checkbox) {
            var checkboxValue = checkbox.value;

            if (checkbox.checked) {
              formData.append("authorityId", checkboxValue);
            }

          });

        },
        accept: function (file, done) {
          done();
        },
      });

      // pdfDropzone.on("success", function (file, response) {
      //   toastr.success(response.message);

      //   // setTimeout(function () {
      //   //   location.reload();
      //   // }, 2500);
      // });

      pdfDropzone.on("addedfile", function () {
        // submitButton.classList.remove("d-none");
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
  };
  // });
  var handleValidationWrite = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

    // var validator;
      validator = FormValidation.formValidation(formNotes, {
        fields: {
          "notes": {
            validators: {
              notEmpty: {
                message: "Sila Isi Catatan",
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

  var handleValidationAttach = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/

      validator = FormValidation.formValidation(formNotes, {
        fields: {
          "select_lampiran": {
            validators: {
              notEmpty: {
                message: "Sila Pilih Jenis Lampiran",
              },
            },
          },
          "date_attachment": {
            validators: {
              notEmpty: {
                message: "Sila Pilih Tarikh",
              },
            },
          },
          "#dropzone-notes": {
              validators: {
                  notEmpty: {
                    message: 'Please select an image',
                },
              },
          },
          "notes": {
            validators: {
              notEmpty: {
                message: "Sila Isi Catatan",
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

      $("#select_lampiran").select2().on('change.select2', function () {
        // Revalidate the color field when an option is chosen
        validator.revalidateField('select_lampiran');
    });
  };

  const handleSubmit = () => {


    submitButton.addEventListener("click", async (e) => {


      if (attachRadio.checked) {
        handleValidationAttach();
      } else if (writeRadio.checked) {
        handleValidationWrite();
      }
      // Prevent button default action
      e.preventDefault();

// Validate form
validator.validate().then(function (status) {
  if (status == "Valid") {
    const isDropzoneEmpty = pdfDropzone.getQueuedFiles().length > 0;

    if (isDropzoneEmpty) {
      pdfDropzone.processQueue();
      submitButton.setAttribute("data-kt-indicator", "on");
      submitButton.disabled = true;


      if (attachRadio.checked) {
        noteType = "attachment";


        const attachType = attachSelect.value;

        if (attachType === "TL") {

        } else if (attachType === "SRT") {

        }
      } else if (writeRadio.checked) {
        noteType = "write";
      }


      refRadio = modalRef.querySelector("#reference_checkbox");
      var systemId = refRadio.value;

      const serializedArrayNotes = $(formNotes).serializeArray();
      const serializedArrayAuth = $(formAuth).serializeArray();
      const additionalVariable = { name: 'system-id', value: systemId };
      const combinedArray = serializedArrayNotes.concat(serializedArrayAuth, additionalVariable);

      // const serializedArray = $(formNotes,formAuth).serializeArray();
      const formData = {};
      combinedArray.forEach((item) => {
        formData[item.name] = item.value;
        // formData["systemId"] = systemId;
      });
      const json = JSON.stringify(formData);

      // var noteType = "attachment";
      pdfDropzone.on("success", function (file, response) {
        toastr.success(response.message);


      api.post(`notes/${noteType}`, json).then(response => {
        if (response.status === 200) {
          submitButton.removeAttribute("data-kt-indicator");
          submitButton.disabled = false;
          toastr.success(response.message);
          setTimeout(function () {

            window.location.reload();

          }, 2500);

        } else if (response.status === 500) {
          submitButton.removeAttribute("data-kt-indicator");
          submitButton.disabled = false;
          toastr.error(response.message);
        } else {
          submitButton.removeAttribute("data-kt-indicator");
          submitButton.disabled = false;
          toastr.error('Tugasan anda tidak berjaya dikemaskini');
        }

      }).catch(error => {
        submitButton.removeAttribute("data-kt-indicator");
        submitButton.disabled = false;
        toastr.error(`Maaf, nampaknya terdapat ralat dikesan, ${error} . Sila cuba sekali lagi.`);
      });

      });
    } else {
      // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
      Swal.fire({
        html: "Sila masukkan Lampiran yang Diperlukan",
        icon: "warning",
        buttonsStyling: false,
        confirmButtonText: "Ok, maklum!",
        customClass: {
          confirmButton: "btn btn-primary",
        },
        allowOutsideClick: false,
      });
    }
    } else {
      // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
      Swal.fire({
        html: "Sila isi Semua Butiran yang Diperlukan.",
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
    })
  }


  return {
    init: function () {
      // Elements
      searchHandler = document.querySelector("#modal_note_handler");
      modalRef = document.querySelector("#modal_note_ref");
      modalNote = document.querySelector("#modal_note_item");

      if (!searchHandler && !modalRef && !modalNote) {
        return;
      }
      wrapperElement = searchHandler.querySelector(
        '[data-kt-search-element="wrapper"]'
      );
      resultsElement = searchHandler.querySelector(
        '[data-kt-search-element="results"]'
      );
      emptyElement = searchHandler.querySelector(
        '[data-kt-search-element="empty"]'
      );

      searchInput = modalRef.querySelector('[data-kt-search-element="input"]');
      authorityElement = modalRef.querySelector('[data-view="authority"]');

      //Form
      formReference = modalRef.querySelector('[data-form="reference"]');
      formAuth = modalRef.querySelector('[data-form="authority"]');
      formNotes = modalNote.querySelector('[data-form="notes"]');

      //Form Views
      formViewAuth = modalRef.querySelector('[data-form-view="authority"]');
      formViewAttach = modalNote.querySelector('[data-form-view="attachment"]');
      formViewLetter = modalNote.querySelector('[data-form-view="letter"]');
      formViewOther = modalNote.querySelector(
        '[data-form-view="other-letter"]'
      );
      formViewNote = modalNote.querySelector('[data-form-view="notes"]');

      dropzoneNotes = modalNote.querySelector("#dropzone-notes");

      writeRadio = modalNote.querySelector("#type-1");
      attachRadio = modalNote.querySelector("#type-2");
      attachSelect = modalNote.querySelector("#select_lampiran");
      letterSelect = modalNote.querySelector("#letter_selection");
      otherLetterName = modalNote.querySelector("#other-letter-name");



      refButton = modalRef.querySelector('[data-modal-action="continue"]');
      backButton = modalNote.querySelector('[data-modal-action="back"]');
      submitButton = modalNote.querySelector('#submit-add-notes');

      // Initialize search handler
      searchObject = new KTSearch(searchHandler);

      searchProject();
      RefModalSetup();
      NoteModalSetup();
      handleDropzone();
      handleSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  addNotes.init();
});
