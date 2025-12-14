/* Getting the sid from the URL. */
const url = window.location.href;
const sid = url.split("/").pop();
// console.log(sid);

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
});

flatpickr("#end-time", {
  enableTime: true,
  noCalendar: true,
  dateFormat: "h:i K",
});

// Class definition
var EditReportUdm = (function () {
  // Initialize your functions here
  var deleteImage = function () {
      // Add event listeners to delete buttons
      document.querySelectorAll(".confirm-delete-btn").forEach((button) => {
          button.addEventListener("click", function () {
              const rowId = this.getAttribute("data-row-id");
              handleDeleteButtonClick(this, rowId);
          });
      });
  };

  // Define variables
  let validator;

  // Get elements
  const form = document.getElementById("report_edit");
  const submitButton = document.getElementById("report_edit_submit");

  const initializeValidator = () => {
      // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
      validator = FormValidation.formValidation(form, {
        fields: {
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
          // "workDone-list[0][survey-work-select]": {
          //     validators: {
          //         notEmpty: {
          //             message: "Kategori Kerja Ukur diperlukan",
          //         },
          //     },
          // }, 
          // "workDone-list[0][repeater-dropzone]": {
          //     validators: {
          //         notEmpty: {
          //             message: "Muatnaik Gambar diperlukanaaa",
          //         },
          //     },
          // },

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

  const initDropzone = (element) => {
    // const initDropzone = (dropzone, repeaterIndex) => {
    // const id = "#dropzonejs";
    const dropzone = element;
    var RefNo = document.getElementById("RefNo").value;

    const myDropzone = new Dropzone(dropzone[0], {
      url: `${apps}/api/surveys/upload`,
      // parallelUploads: 20,
      maxFilesize: 1,
      // previewTemplate: previewTemplate,
      clickable: dropzone.find(".dropzone-select").get(0),
      acceptedFiles: "image/*", // Accept all image file types
      paramName: "file",
      thumbnailWidth: 600,
      thumbnailHeight: 600,
      maxFilesize: 1024,
      autoProcessQueue: true,
      previewTemplate: document.querySelector('.preview-item').innerHTML,

      init: function() {
        const updateValidationStatus = (status, index) => {
        //   console.log(`Updating validation status: ${status} for index: ${index}`);

          // Ensure a hidden input exists
          let hiddenInput = document.querySelector(`input[name="workDone-list[${index}][repeater-dropzone]"]`);
          if (!hiddenInput) {
              hiddenInput = document.createElement('input');
              hiddenInput.type = 'hidden';
              hiddenInput.name = 'workDone-list[0][repeater-dropzone]';
              form.appendChild(hiddenInput);
          }
          hiddenInput.value = status ? 'uploaded' : '';
          // validator.updateFieldStatus(fieldName, status ? 'Valid' : 'Invalid', 'notEmpty');
        };

        this.on("addedfile", function(file) {
            // Show preview container when files are added
            document.querySelector('.preview-area').style.display = 'block';

            file.previewElement.classList.add('uploading');
            
            // Reset validation status - file added but not uploaded yet
            updateValidationStatus(0);

        });

        this.on("removedfile", function(file) {
          // Try finding the repeater item through parent traversal
          const repeaterItem = $(file.previewElement).parents('[data-repeater-item]').first()[0];
          
          // Get the index of the repeater item
          const index = repeaterItem ? 
              Array.from(document.querySelectorAll('[data-repeater-item]')).indexOf(repeaterItem) : 
              -1;
          
        //   console.log('Repeater index:', index);
      
          if (repeaterItem) {
              // Handle repeater item case
              const imageUrlInput = repeaterItem.querySelector('.image-url-input') || 
                                  repeaterItem.querySelector('input[name^="image-url"]');
              if (imageUrlInput) {
                  imageUrlInput.value = '';
              }
          } else {
              document.querySelector('.image-url-input').value = '';
          }
      
          // Update validation status
          updateValidationStatus(false, index);
      
          // Remove the preview element
          if (file.previewElement != null && file.previewElement.parentNode != null) {
              file.previewElement.parentNode.removeChild(file.previewElement);
          }
        });
        
        this.on("success", function(file, response) {
          file.previewElement.classList.remove('uploading');
          
          // Find the repeater item containing this file
          const repeaterItem = file.previewElement.closest('[data-repeater-item]');
        //   console.log(repeaterItem);
          
          // Show success message
          let successDiv = file.previewElement.querySelector('.upload-success');
          if (successDiv) {
              successDiv.style.display = 'block';
          }
      
          // Update image URL
          if (response && response.img) {
              if (repeaterItem) {
                  // For repeater items
                  const imageUrlInput = repeaterItem.querySelector('.image-url-input');
                  if (imageUrlInput) {
                      imageUrlInput.value = response.img;
                  }
              } else {
                  document.querySelector('.image-url-input').value = response.img;
              }
          }
          
          // Get index and update validation
          const index = Array.from(document.querySelectorAll('[data-repeater-item]')).findIndex(item => {
              return item.contains(file.previewElement);
          });
      
          // Disable validation for the field upon successful upload
          if (validator) {
              validator.disableValidator(`workDone-list[${index}][repeater-dropzone]`);
          }
      
          // Update validation status
          updateValidationStatus(true, index);
        });
        
        this.on("error", function(file, errorMessage) {
            // Handle upload error
            file.previewElement.classList.remove('uploading');
            
            // Show error message
            let errorDiv = file.previewElement.querySelector('.upload-error');
            if (errorDiv) {
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
            }
            
            // Update validation status - upload failed
            updateValidationStatus(0);
        });
      },

      sending: function (file, xhr, formData) {
        formData.append("systemId", sid);
        formData.append("refId", RefNo);
        // formData.append("rId", dropzone.attr("name"));
      },
      accept: function (file, done) {
        done();
      },
    });

    return myDropzone;

  };

  // Format options
  var optionFormat = function (item) {
    if (!item.id) {
      return item.text;
    }

    var span = document.createElement("span");
    var template = "";

    template += item.text;

    span.innerHTML = template;

    return span;
  };

  // Initialize Select2
  const initSelect2 = (element) => {
    element.select2({
      minimumResultsForSearch: Infinity,
      templateSelection: optionFormat,
      templateResult: optionFormat,
      escapeMarkup: function (markup) {
        return markup;
      },
    });
  };

  // Init form repeater --- more info: https://github.com/DubFriend/jquery.repeater
  const initFormRepeater = () => {
    $("#add-image").repeater({
      initEmpty: false,

      show: function () {
        $(this).slideDown();

        initDropzone($(this).find('[data-custom-instance="dropzone"]'));

        // Re-init select2 for the current form repeater
        initSelect2($(this).find(".selectOption"));
      },

      hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
      },

      ready: function () {

        /* The above code is initializing a dropzone for the element with the id "add-image" using
        jQuery. */
        initSelect2($(".selectOption"));
        initDropzone($("#add-image").find('[data-custom-instance="dropzone"]'));
      },
    });
  };

  // Submit form handler
  const handleSubmit = () => {
    initializeValidator();

    // Add validation for a given index (row)
    function addRepeaterValidation(index) {
        validator.addField(`workDone-list[${index}][repeater-dropzone]`, {
            validators: {
                notEmpty: {
                    message: "Muatnaik Gambar diperlukan",
                },
            }
        });

        validator.addField(`workDone-list[${index}][survey-work-select]`, {
            validators: {
                notEmpty: {
                    message: "Kategori Kerja Ukur diperlukan",
                },
            }
        });

        validator.addField(`workDone-list[${index}][survey-work-desc]`, {
            validators: {
                notEmpty: {
                    message: "Ulasan Kerja Ukur diperlukan",
                },
            }
        });

        validator.addField(`workDone-list[${index}][chainage-value]`, {
            validators: {
                notEmpty: {
                    message: "Nilai Kedudukan Chainage diperlukan",
                },
            }
        });

        // console.log(`Validation added for row ${index}`);
    }

    // Initialize Select2 for specific row
    function initializeSelect2ForRow($row) {
      const $selectSurveyWork = $row.find('select[name="survey-work-select"]');
      const $selectSurveyDesc = $row.find('select[name="survey-work-desc"]');

      if ($selectSurveyWork.length && !$selectSurveyWork.hasClass('select2-hidden-accessible')) {
          $selectSurveyWork.select2({
              placeholder: "Pilih kategori kerja ukur",
              allowClear: true
          });
      }

      if ($selectSurveyDesc.length && !$selectSurveyDesc.hasClass('select2-hidden-accessible')) {
          $selectSurveyDesc.select2({
              placeholder: "Pilih ulasan kerja ukur",
              allowClear: true
          });
      }
    }

    // Initialize for existing rows on page load
    function initializeExistingRows() {
      $('[data-repeater-item]').each(function(index) {
        const $row = $(this);
        // Get the survey image data count from PHP
        const surveyImageCount = parseInt(document.getElementById('surveyImageCount').value) || 0;

        if (surveyImageCount == 0) {
            addRepeaterValidation(index);
            initializeSelect2ForRow($row);
        } 

          // const $row = $(this);
          // addRepeaterValidation(index);
          // initializeSelect2ForRow($row);
          // Add delete event listener for each row
        $row.find('[data-repeater-delete]').on('click', function() {
          const rowIndex = $row.data('repeater-item');
          
          // Remove validation for the specific row
          try {
              validator
                  .removeField(`workDone-list[${rowIndex}][repeater-dropzone]`)
                  .removeField(`workDone-list[${rowIndex}][survey-work-select]`)
                  .removeField(`workDone-list[${rowIndex}][survey-work-desc]`)
                  .removeField(`workDone-list[${rowIndex}][chainage-value]`);
          } catch (error) {
              console.warn(`Could not remove validation for deleted row ${rowIndex}:`, error);
          }
        });

      });
    }
    // Initialize on page load
    initializeExistingRows();

    // Repeater add event
    $('[data-repeater-create]').on('click', function() {
      setTimeout(function() {
        const $newRow = $('[data-repeater-item]').last();
        const newIndex = $('[data-repeater-item]').length - 1;

        addRepeaterValidation(newIndex);
        initializeSelect2ForRow($newRow);

        // Add delete event listener for the new row
        $newRow.find('[data-repeater-delete]').on('click', function() {
          try {
              validator
                  .removeField(`workDone-list[${newIndex}][repeater-dropzone]`)
                  .removeField(`workDone-list[${newIndex}][survey-work-select]`)
                  .removeField(`workDone-list[${newIndex}][survey-work-desc]`)
                  .removeField(`workDone-list[${newIndex}][chainage-value]`);
          } catch (error) {
              console.warn(`Could not remove validation for deleted row ${newIndex}:`, error);
          }
        });

      }, 100);
    });

    // Handle select2 dropdown close after selection
    $(document).on('select2:select', 'select[name^="workDone-list"]', function () {
        $(this).select2('close');
    });

    $(document).on('change', '#surveyWorkSelect', function () {
        var selectedValue = $(this).val();
        var currentItem = $(this).closest('[data-repeater-item]');
        var index = currentItem.index(); // Get the current item's index
    
        // Clear and revalidate fields based on selected value
        if (selectedValue !== '1') {
            currentItem.find(`select[name="workDone-list[${index}][survey-work-desc]"]`).val('');
            validator.disableValidator(`workDone-list[${index}][survey-work-desc]`);
        } else {
            validator.enableValidator(`workDone-list[${index}][survey-work-desc]`);
        }
    
        if (selectedValue !== '2') {
            currentItem.find(`input[name="workDone-list[${index}][chainage-value]"]`).val('');
            validator.disableValidator(`workDone-list[${index}][chainage-value]`);
        } else {
            validator.enableValidator(`workDone-list[${index}][chainage-value]`);
        }
    
        if (selectedValue !== '3') {
            currentItem.find(`textarea[name="workDone-list[${index}][notes]"]`).val('');
            validator.disableValidator(`workDone-list[${index}][notes]`);
        } else {
            validator.enableValidator(`workDone-list[${index}][notes]`);
        }
    
        // Show/hide fields based on selected value
        currentItem.find('.surveyWork1').toggle(selectedValue === '1');
        currentItem.find('.surveyWork2').toggle(selectedValue === '2');
        currentItem.find('.surveyWork3').toggle(selectedValue === '3');
    
        // Close the dropdown
        $(this).select2('close');
    });

    // Handle submit button
    submitButton.addEventListener("click", (e) => {
      e.preventDefault();
      // console.log($(form).serializeArray());
      // Get the necessary data from HTML elements
      var dropzoneItems = document.getElementsByClassName("dropzone-item");
      var selectOption = document.getElementsByClassName("selectOption")[0].value;

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
                    text: "Kemaskini Laporan Kerja Lapangan telah berjaya disimpan!",
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
                        location.href = `surveys/site/reportUdm/`+sid;
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
              html: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila ambil perhatian bahawa mungkin terdapat ralat dalam butiran <strong>Laporan Harian</strong>",
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
      initFormRepeater();
      handleSubmit();
      deleteImage();
    },
  };
})();

// Function to handle delete button click
var handleDeleteButtonClick = (button, rowId) => {
  // Show confirmation message using SweetAlert
  Swal.fire({
      title: "Adakah anda pasti mahu buang gambar ini?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ya",
      cancelButtonText: "Tidak",
      customClass: {
          confirmButton: "btn fw-bold btn-danger",
          cancelButton: "btn fw-bold btn-light btn-active-light-primary",
      },
  }).then((result) => {
      if (result.isConfirmed) {
          handleDeleteConfirmation(rowId, button);
      }
  });
};

// Function to handle delete confirmation
var handleDeleteConfirmation = (rowId, button) => {
  api
      .delete(`surveys/tasking?rowId=` + rowId)
      .then((response) => {
          handleDeleteSuccess();
      })
      .catch((error) => {
          handleDeleteError();
      });
};

// Function to handle delete success
var handleDeleteSuccess = () => {
  // button.closest("tr").remove();
  Swal.fire({
    text: "Gambar berjaya dibuang.",
    icon: "success",
    buttonsStyling: false,
    confirmButtonText: "Ok, maklum.",
    customClass: {
      confirmButton: "btn btn-primary",
    },
    allowOutsideClick: false,
  }).then(function (result) {
    if (result.isConfirmed) {
      // Redirect
      setTimeout(function () {
        location.href = `surveys/site/reportUdmEdit/`+sid;
      });
    }
  });

};

// Function to handle delete error
var handleDeleteError = () => {
  Swal.fire({
    html: "Maaf, terdapat beberapa ralat semasa buang gambar.",
    icon: "error",
    buttonsStyling: false,
    confirmButtonText: "Ok, maklum.",
    customClass: {
      confirmButton: "btn btn-primary",
    },
    allowOutsideClick: false,
  }).then(function (result) {
    if (result.isConfirmed) {
      // Redirect
      setTimeout(function () {
        location.href = `surveys/site/reportUdmEdit/`+sid;
      });
    }
  });

};

// On document ready
KTUtil.onDOMContentLoaded(function () {
    EditReportUdm.init();
});