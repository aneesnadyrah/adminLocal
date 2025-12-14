function handleActionSubmit(
  statusId,
  form,
  submitButton,
  validator = null,
  dropzone = null,
  modalId = null,
  sysId = null,
  folderId = null
) {
  if (statusId === "005") {
    $redirect = "/geospatial/PCL/extract/" + $(sysId).val();
  } else if (
    role === 33 &&
    (statusId === "029" ||
      statusId === "031" ||
      statusId === "032" ||
      statusId === "033" ||
      statusId === "034" ||
      statusId === "040" ||
      statusId === "041" ||
      statusId === "045")
  ) {
    $redirect = "/geospatial/PCL/extract/" + $(sysId).val();
  } else {
    $redirect = "/tasks/new";
  }

  if (typeof validator === "undefined") {
    submitButton.classList.remove("d-none");
  }

  // Handle form submit
  submitButton.addEventListener("click", async function (e) {
    // Prevent button default action
    e.preventDefault();

    if (typeof validator === "undefined" || validator === null) {
      if (statusId === "002" || statusId === "996") {
        let payment_method = document
          .querySelector(modalId)
          .querySelector('input[name="payment_method"]').value;

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

        if (
          statusId === "128" ||
          statusId === "131" ||
          statusId === "151" ||
          statusId === "121" ||
          statusId === "085" ||
          statusId === "103" ||
          statusId === "101" ||
          statusId === "046" ||
          statusId === "051"
        ) {
          await new Promise((resolve, reject) => {
            dropzone.on("queuecomplete", () => {
              resolve();
            });
            dropzone.processQueue();
          });
        } else {
        }

        // Disable button to avoid multiple clicks
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

            if (
              typeof Dropzone !== "undefined" &&
              document.querySelector(modalId) &&
              document.querySelector(sysId) &&
              document.querySelector(folderId)
            ) {
              if (
                statusId === "128" ||
                statusId === "131" ||
                statusId === "151" ||
                statusId === "121" ||
                statusId === "085" ||
                statusId === "103" ||
                statusId === "101" ||
                statusId === "046" ||
                statusId === "051"
              ) {
                toastr.success(response.message);
                setTimeout(function () {
                  window.location.reload();
                }, 2500);
              } else {
                dropzone.processQueue();
              }
            } else {
              toastr.success(response.message);
              setTimeout(function () {
                location.href = $redirect;
              }, 2500);
            }
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
            statusId === "128" ||
            statusId === "131" ||
            statusId === "151" ||
            statusId === "121" ||
            statusId === "085" ||
            statusId === "103" ||
            statusId === "101" ||
            statusId === "046" ||
            statusId === "051"
          ) {
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
            .post(`tasks/${statusId}/submit`, jsonData)
            .then((response) => {
              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

              if (
                typeof Dropzone !== "undefined" &&
                document.querySelector(modalId) &&
                document.querySelector(sysId) &&
                document.querySelector(folderId)
              ) {
                if (
                  statusId === "128" ||
                  statusId === "131" ||
                  statusId === "151" ||
                  statusId === "121" ||
                  statusId === "085" ||
                  statusId === "103" ||
                  statusId === "101" ||
                  statusId === "046" ||
                  statusId === "051"
                ) {
                  toastr.success(response.message);
                  setTimeout(function () {
                    window.location.reload();
                  }, 2500);
                } else {
                  dropzone.processQueue();
                }
              } else {
                toastr.success(response.message);
                setTimeout(function () {
                  location.href = $redirect;
                }, 2500);
              }
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
    document.querySelector(folderId)
  ) {
    // Initialize Dropzone for the modal
    dropzone = new Dropzone($(modalId).find(".dropzone")[0], {
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

    if (statusId === "002" || statusId === "996") {
      let payment_method = document
        .querySelector(modalId)
        .querySelector('input[name="payment_method"]').value;

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
            .post(`tasks/${statusId}/submit`, jsonData)
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
        if (
          statusId === "128" ||
          statusId === "131" ||
          statusId === "151" ||
          statusId === "121" ||
          statusId === "085" ||
          statusId === "103" ||
          statusId === "101" ||
          statusId === "046" ||
          statusId === "051"
        ) {
          // toastr.success(response.message);
          // console.log(response.message);
        } else {
          submitButton.removeAttribute("data-kt-indicator");
          submitButton.disabled = false;

          toastr.success(response.message);

          setTimeout(function () {
            location.href = $redirect;
          }, 2500);
        }
      });
    }

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
}
