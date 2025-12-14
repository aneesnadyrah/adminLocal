var summary = (function () {
  // ANCHOR - Begin::Api request queue function::maybe need to move this somewhere else in the future
  // Create a queue to store API requests
  const apiRequestQueue = new Map();

  // Function to make API requests
  async function makeApiRequest({ method, gateway, data }) {
    try {
      const response = await api[method.toLowerCase()](gateway, data);
      return response; // Change this line to return the entire response
    } catch (error) {
      throw error;
    }
  }

  // Function to process the API request queue
  async function processApiQueue(tag) {
    if (!apiRequestQueue.has(tag)) return;

    const queue = apiRequestQueue.get(tag);
    const delayBetweenRequests = 0; // Set the delay in milliseconds (adjust as needed)

    while (queue.length > 0) {
        const requestData = queue.shift(); // Get the first request

        try {
            // Make the API request using makeApiRequest function after the delay
            await new Promise(resolve => setTimeout(resolve, delayBetweenRequests));
            const response = await makeApiRequest(requestData);
            // console.log({ makeApiRequest: requestData });

            // If resolve function is provided, call it with the response
            if (requestData.resolve) {
                requestData.resolve(response);
                // console.log({ apiResponseFor: requestData, response: response });
            }
        } catch (error) {
            // If reject function is provided, call it with the error
            if (requestData.reject) {
                requestData.reject(error);
            }
            console.error(`Error processing request ${requestData}:`, error);
        }
    }

    // Clear the queue after processing
    apiRequestQueue.delete(tag);
}

  // Function to add requests to the queue
  function addToApiQueue({ method, gateway, data, tag = 'default' }) {
    return new Promise((resolve, reject) => {
        if (!apiRequestQueue.has(tag)) {
            apiRequestQueue.set(tag, []);
        }

        const queue = apiRequestQueue.get(tag);
        queue.push({ method, gateway, data, resolve, reject });
        // console.log({ [`${tag}Queue.length`]: queue.length });

        // If the queue was empty and not processing, start processing
        if (queue.length === 1) {
            processApiQueue(tag);
        }
    });
}
  // ANCHOR - End::Api request queue function::maybe need to move this somewhere else in the future

  // Use for modal control //
  const bodyEl = document.getElementById('kt_body');

  /* Getting the sid and ref from the URL. */
  const urlParams = new URLSearchParams(window.location.search);
  const reportNo = urlParams.get('r');
  const authorityId = urlParams.get('a');

  // Get the current URL
  let currentURL = new URL(window.location.href);
  let urlWithOutParams = currentURL.origin + currentURL.pathname;

  // Get the values of the parameters
  let htaccessUrlParams = urlWithOutParams.replace(apps + '/reports/site/summary', '').split("/");
  // urlParams[0] is ignore as it handle the first / in the trimmed url
  const systemId = htaccessUrlParams[1];

  // Initialize an empty FormData object in the global scope
  let formData = new FormData();

  const checkListForm = document.getElementById('reportChecklist');
  // Initialize an empty checkListFormData object in the global scope
  let checkListFormData = new FormData(checkListForm);
  let actionNotes;
  let amendDetailsModal;

  function amendDetails() {
    const checkAmendPlan = document.getElementById('amend-plan');
    const amendDetailsModalEl = document.getElementById('amend-details-select');
    const amendCancelBtn = amendDetailsModalEl.querySelector('#amendCancelBtn');
    const amendDetailsForm = amendDetailsModalEl.querySelector('#amendDetailsForm');
    amendDetailsModal = new bootstrap.Modal(amendDetailsModalEl);
    let submitBtnClicked = false;

    amendCancelBtn.addEventListener('click', function () {
      checkAmendPlan.checked = false;

      const reviewCheckboxes = amendDetailsForm.querySelectorAll('[name="amendLists[]"]');
      const appInfoCheckboxes = amendDetailsForm.querySelectorAll('[name="app_infos[]"]');
      const officerInfoCheckboxes = amendDetailsForm.querySelectorAll('[name="officer_infos[]"]');
      const routeInfoCheckboxes = amendDetailsForm.querySelectorAll('[name="route_infos[]"]');
      const appInfoNote = amendDetailsForm.querySelector('[name="notes_app_infos"]');
      const officerInfoNote = amendDetailsForm.querySelector('[name="notes_officer_infos"]');
      const routeInfoNote = amendDetailsForm.querySelector('[name="notes_route_infos"]');
      const reviewCheckedIds = [];
      const reviewUncheckedIds = [];
      const appCheckedVal = [];
      const appUncheckedVal = [];
      const officerCheckedVal = [];
      const officerUncheckedVal = [];
      const routeCheckedVal = [];
      const routeUncheckedVal = [];

      console.log({ reviewCheckedIdslength: reviewCheckedIds.length });

      // uncheck all checkboxes
      reviewCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        reviewUncheckedIds.push(checkbox.value);
      });
      appInfoCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        appUncheckedVal.push(checkbox.value);
      });
      officerInfoCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        officerUncheckedVal.push(checkbox.value);
      });
      routeInfoCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        routeUncheckedVal.push(checkbox.value);
      });
      appInfoNote.value = "";
      officerInfoNote.value = "";
      routeInfoNote.value = "";

      // save data through api
      let payload = {
        item: "amendcheck",
        data: {
          reviewCheckedIds: reviewCheckedIds,
          reviewUncheckedIds: reviewUncheckedIds,
          appCheckedVal: appCheckedVal,
          appUncheckedVal: appUncheckedVal,
          officerCheckedVal: officerCheckedVal,
          officerUncheckedVal: officerUncheckedVal,
          routeCheckedVal: routeCheckedVal,
          routeUncheckedVal: routeUncheckedVal,
          appInfoNote: appInfoNote.value,
          officerInfoNote: officerInfoNote.value,
          routeInfoNote: routeInfoNote.value,
          systemId: systemId,
          reportNo: reportNo,
          authorityId: authorityId,
          amendReset: true
        }
      }

      addToApiQueue({method: "PUT", gateway: `reports/summary/utilities`, data: payload}).then((response) => {
        console.log(response);
        toastr.success(response.message);
      }).catch((error) => { });
    });

    amendDetailsForm.addEventListener("submit", function (event) {
      event.preventDefault();
      submitBtnClicked = true;

      const reviewCheckboxes = this.querySelectorAll('[name="amendLists[]"]');
      const appInfoCheckboxes = this.querySelectorAll('[name="app_infos[]"]');
      const officerInfoCheckboxes = this.querySelectorAll('[name="officer_infos[]"]');
      const routeInfoCheckboxes = this.querySelectorAll('[name="route_infos[]"]');
      const appInfoNote = this.querySelector('[name="notes_app_infos"]');
      const officerInfoNote = this.querySelector('[name="notes_officer_infos"]');
      const routeInfoNote = this.querySelector('[name="notes_route_infos"]');
      const reviewCheckedIds = [];
      const reviewUncheckedIds = [];
      const appCheckedVal = [];
      const appUncheckedVal = [];
      const officerCheckedVal = [];
      const officerUncheckedVal = [];
      const routeCheckedVal = [];
      const routeUncheckedVal = [];

      // check all checkboxes for checked value
      reviewCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
          reviewCheckedIds.push(checkbox.value);
        } else {
          reviewUncheckedIds.push(checkbox.value);
        }
      });
      appInfoCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
          appCheckedVal.push(checkbox.value);
        } else {
          appUncheckedVal.push(checkbox.value);
        }
      });
      officerInfoCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
          officerCheckedVal.push(checkbox.value);
        } else {
          officerUncheckedVal.push(checkbox.value);
        }
      });
      routeInfoCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
          routeCheckedVal.push(checkbox.value);
        } else {
          routeUncheckedVal.push(checkbox.value);
        }
      });

      let amendWarning = {
        html: `Sila tanda dan tinggalkan catatan pada ruangan yang terlibat`,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, Baik!",
        customClass: {
          confirmButton: "btn btn-secondary",
        }
      };

      // check value for all input
      if (appInfoNote.value == "" && officerInfoNote.value == "" && routeInfoNote.value == "" && reviewCheckedIds.length == 0 && appCheckedVal.length == 0 && officerCheckedVal.length == 0 && routeCheckedVal.length == 0) {
        Swal.fire(amendWarning);
      } else {
        let payload = {
          item: "amendcheck",
          data: {
            reviewCheckedIds: reviewCheckedIds,
            reviewUncheckedIds: reviewUncheckedIds,
            appCheckedVal: appCheckedVal,
            appUncheckedVal: appUncheckedVal,
            officerCheckedVal: officerCheckedVal,
            officerUncheckedVal: officerUncheckedVal,
            routeCheckedVal: routeCheckedVal,
            routeUncheckedVal: routeUncheckedVal,
            appInfoNote: appInfoNote.value,
            officerInfoNote: officerInfoNote.value,
            routeInfoNote: routeInfoNote.value,
            systemId: systemId,
            reportNo: reportNo,
            authorityId: authorityId,
            amendReset: false
          }
        }
        addToApiQueue({method: "PUT", gateway: `reports/summary/utilities`, data: payload}).then((response) => {
          console.log(response);
          toastr.success(response.message);
          submitBtnClicked = false;
        }).catch((error) => { });
        amendDetailsModal.hide();
      }
    });

    amendDetailsModalEl.addEventListener('hide.bs.modal', function (event) {
      // check if close by submit button or not
      if (submitBtnClicked == false) {
        amendCancelBtn.click();
      }

    });
  }

  // Declare checkbox to be listen
  function checklistControl() {
    const checkWlExclusion = document.getElementById('wl-exclusion');
    const checkOverlapping = document.getElementById('overlapping');
    const checkAmendPlan = document.getElementById('amend-plan');
    const checkPrivateRoad = document.getElementById('private-road');
    const overlappingDetails = document.getElementById('overlapDetails');
    const overlappingDetailsContainer = overlappingDetails.parentElement;
    const amendPlanContainer = document.getElementById('amend-details-card');
    const amendDetailsForm = document.getElementById('amendDetailsForm');
    actionNotes = document.querySelector('[data-report-note="action"]');

    // initialize specific containers to be hidden
    if (!overlappingDetailsContainer.classList.contains("d-none")) {
      overlappingDetailsContainer.classList.add("d-none");
    }
    if (!amendPlanContainer.classList.contains("d-none")) {
      amendPlanContainer.classList.add("d-none");
    }

    checkWlExclusion.addEventListener('change', function () {
      if (this.checked) {
        // Checkbox is checked
        // Perform actions for checked state
        console.log('checkWlExclusion is checked');
        // Do something...
      } else {
        // Checkbox is unchecked
        // Perform actions for unchecked state
        console.log('checkWlExclusion is unchecked');
        // Do something...
      }
    });

    checkOverlapping.addEventListener('change', function () {
      if (this.checked) {
        // Checkbox is checked
        // Perform actions for checked state
        // console.log('checkOverlapping is checked');
        overlappingDetailsContainer.classList.remove("d-none");
      } else {
        // Checkbox is unchecked
        // Perform actions for unchecked state
        // console.log('checkOverlapping is unchecked');
        overlappingDetailsContainer.classList.add("d-none");
      }
    });

    checkAmendPlan.addEventListener('change', function () {
      if (this.checked) {
        // Checkbox is checked
        // Perform actions for checked state
        console.log('checkAmendPlan is checked');
        // open amend details modal
        amendDetailsModal.show();
        // amendPlanContainer.classList.remove("d-none");

        // Wait for the class addition/removal to complete
        // setTimeout(() => {
        //   amendPlanContainer.scrollIntoView({ behavior: "smooth" });
        // }, 0);
        // Do something...
      } else {
        // Checkbox is unchecked
        // Perform actions for unchecked state
        console.log('checkAmendPlan is unchecked');

        const reviewCheckboxes = amendDetailsForm.querySelectorAll('[name="amendLists[]"]');
        const appInfoCheckboxes = amendDetailsForm.querySelectorAll('[name="app_infos[]"]');
        const officerInfoCheckboxes = amendDetailsForm.querySelectorAll('[name="officer_infos[]"]');
        const routeInfoCheckboxes = amendDetailsForm.querySelectorAll('[name="route_infos[]"]');
        const appInfoNote = amendDetailsForm.querySelector('[name="notes_app_infos"]');
        const officerInfoNote = amendDetailsForm.querySelector('[name="notes_officer_infos"]');
        const routeInfoNote = amendDetailsForm.querySelector('[name="notes_route_infos"]');
        const reviewCheckedIds = [];
        const reviewUncheckedIds = [];
        const appCheckedVal = [];
        const appUncheckedVal = [];
        const officerCheckedVal = [];
        const officerUncheckedVal = [];
        const routeCheckedVal = [];
        const routeUncheckedVal = [];

        // uncheck all checkboxes
        reviewCheckboxes.forEach(checkbox => {
          checkbox.checked = false;
          reviewUncheckedIds.push(checkbox.value);
        });
        appInfoCheckboxes.forEach(checkbox => {
          checkbox.checked = false;
          appUncheckedVal.push(checkbox.value);
        });
        officerInfoCheckboxes.forEach(checkbox => {
          checkbox.checked = false;
          officerUncheckedVal.push(checkbox.value);
        });
        routeInfoCheckboxes.forEach(checkbox => {
          checkbox.checked = false;
          routeUncheckedVal.push(checkbox.value);
        });
        appInfoNote.value = "";
        officerInfoNote.value = "";
        routeInfoNote.value = "";

        // save data through api
        let payload = {
          item: "amendcheck",
          data: {
            reviewCheckedIds: reviewCheckedIds,
            reviewUncheckedIds: reviewUncheckedIds,
            appCheckedVal: appCheckedVal,
            appUncheckedVal: appUncheckedVal,
            officerCheckedVal: officerCheckedVal,
            officerUncheckedVal: officerUncheckedVal,
            routeCheckedVal: routeCheckedVal,
            routeUncheckedVal: routeUncheckedVal,
            appInfoNote: appInfoNote.value,
            officerInfoNote: officerInfoNote.value,
            routeInfoNote: routeInfoNote.value,
            systemId: systemId,
            reportNo: reportNo,
            authorityId: authorityId,
            amendReset: true
          }
        }
        addToApiQueue({method: "PUT", gateway: `reports/summary/utilities`, data: payload}).then((response) => {
          console.log(response);
          toastr.success(response.message);
        }).catch((error) => { });

        // amendPlanContainer.classList.add("d-none");
        // Do something...
      }
    });

    checkPrivateRoad.addEventListener('change', function () {
      if (this.checked) {
        // Checkbox is checked
        // Perform actions for checked state
        console.log('checkPrivateRoad is checked');
        // Do something...
      } else {
        // Checkbox is unchecked
        // Perform actions for unchecked state
        console.log('checkPrivateRoad is unchecked');
        // Do something...
      }
    });
  }

  // declare usable function
  function formatDateToMalayDateString(dateString) {
    // Parse the input date string into a Date object
    const dateObj = new Date(dateString);

    // Create an array of month names in Malay
    const monthNames = [
      'Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun',
      'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'
    ];

    // Get the day, month, and year from the date object
    const day = dateObj.getDate();
    const monthIndex = dateObj.getMonth();
    const year = dateObj.getFullYear();

    // Format the date as required: "25 Julai 2023"
    const formattedDate = `${day} ${monthNames[monthIndex]} ${year}`;
    return formattedDate;
  }

  // signaturepad counter repeater
  let signCountInitRepeater = 4;
  let signaturePadIdInitRepeater;

  // signaturepad counter for fixed signature
  let signCountInit = 1;
  let signaturePadIdInit;
  if (signCountInit < 10) {
    signaturePadIdInit = `signature-pad-0${signCountInit}`;
  } else {
    signaturePadIdInit = `signature-pad-${signCountInit}`;
  }
  while (document.getElementById(signaturePadIdInit)) {
    const signModal = document.getElementById(signaturePadIdInit);
    const signType = signaturePadIdInit.charAt(signaturePadIdInit.length - 1);
    const signModalButton = document.querySelector('[data-bs-target="#' + signaturePadIdInit + '"]');
    const signModalViewSection = signModalButton.parentElement.parentElement.parentElement;
    // modal input
    const modalNameInputEl = signModal.querySelector('[data-report-repeater="signature-name-modal"]');
    const modalPositionInputEl = signModal.querySelector('[data-report-repeater="signature-position-modal"]');
    // display element
    const signDisplayEl = signModalButton.parentElement.querySelector('[data-report-signature="signature-img"]');
    const nameDisplayEl = signModalViewSection.querySelector('[data-report-signature="signature-name"]');
    const positionDisplayEl = signModalViewSection.querySelector('[data-report-signature="signature-position"]');
    const dateDisplayEl = signModalViewSection.querySelector('[data-report-signature="signature-date"]');
    // input element
    const signInputEl = signModalButton.parentElement.querySelector('[data-report-signature="signature-img-input"]');
    const nameInputEl = signModalViewSection.querySelector('[data-report-signature="signature-name-input"]');
    const positionInputEl = signModalViewSection.querySelector('[data-report-signature="signature-position-input"]');
    const dateInputEl = signModalViewSection.querySelector('[data-report-signature="signature-date-input"]');
    const saveButton = signModal.querySelector('[data-report-button="saveSignature"]');
    const canvas = signModal.querySelector("canvas");
    const signaturePad = new SignaturePad(canvas, {
      // It's Necessary to use an opaque color when saving image as JPEG;
      // this option can be omitted if only saving as PNG or SVG
      // backgroundColor: 'rgb(255, 255, 255)'
    });

    // get ready data if exist
    addToApiQueue({method: "GET", gateway: `reports/summary/utilities?item=reportSign&sid=${systemId}&rn=${reportNo}&aid=${authorityId}&signType=${signType}`}).then((response) => {
      // plot ready data to the view
      console.log({ reportSign: response });

      if (response.available != 'none') {
        nameDisplayEl.innerHTML = `Nama : ${response.full_name}`
        $(nameInputEl).val(response.full_name);
        positionDisplayEl.innerHTML = `Jawatan : ${response.position}`
        $(positionInputEl).val(response.position);
        dateDisplayEl.innerHTML = `Tarikh : ${formatDateToMalayDateString(response.signed_timestamp)}`
        $(dateInputEl).val(response.signed_timestamp);


        signDisplayEl.src = response.signature;
        $(signInputEl).val(response.signature);


        // Change signature button
        signModalButton.classList.add('d-none');
        signDisplayEl.classList.remove('d-none');
      }

    }).catch((error) => {
      console.log(error);
    });

    // Adjust canvas coordinate space taking into account pixel ratio,
    // to make it look crisp on mobile devices.
    // This also causes canvas to be cleared.
    function resizeCanvas() {
      // When zoomed out to less than 100%, for some very strange reason,
      // some browsers report devicePixelRatio as less than 1
      // and only part of the canvas is cleared then.
      const ratio = Math.max(window.devicePixelRatio || 1, 1);

      // This part causes the canvas to be cleared
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext("2d").scale(ratio, ratio);

      // This library does not listen for canvas changes, so after the canvas is automatically
      // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
      // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
      // that the state of this library is consistent with visual state of the canvas, you
      // have to clear it manually.
      signaturePad.clear();

      // If you want to keep the drawing on resize instead of clearing it you can reset the data.
      // signaturePad.fromData(signaturePad.toData());
    }

    // On mobile devices it might make more sense to listen to orientation change,
    // rather than window resize events.
    window.addEventListener('resize', resizeCanvas);
    signModal.addEventListener('shown.bs.modal', resizeCanvas);

    resizeCanvas();

    saveButton.addEventListener("click", () => {
      if (signaturePad.isEmpty()) {
        alert("Sila turunkan tandatangan dahulu.");
      } else if (modalNameInputEl.value == '' || modalNameInputEl.value == undefined) {
        alert("Sila masukkan Nama Pegawai.");
      } else if (modalPositionInputEl.value == '' || modalPositionInputEl.value == undefined) {
        alert("Sila masukkan maklumat Jawatan.");
      } else {
        const dataURL = signaturePad.toDataURL();// Process the signature data
        processSignatureData(dataURL)
          .then(croppedDataURL => {
            // Use the croppedDataURL as needed (e.g., save or further process it)
            nameDisplayEl.innerHTML = `Nama : ${modalNameInputEl.value}`
            $(nameInputEl).val(modalNameInputEl.value);
            positionDisplayEl.innerHTML = `Jawatan : ${modalPositionInputEl.value}`
            $(positionInputEl).val(modalPositionInputEl.value);


            signDisplayEl.src = croppedDataURL;
            $(signInputEl).val(croppedDataURL);


            // Change signature button
            signModalButton.classList.add('d-none');
            signDisplayEl.classList.remove('d-none');

            // Hide modal
            signModal.style.display = 'none';
            document.querySelector('.modal-backdrop').classList.remove('show');
            bodyEl.style.overflow = '';
            bodyEl.style.paddingRight = '';
            bodyEl.classList.remove('modal-open');
            document.querySelector('.modal-backdrop').remove();

            // TODO: make api request to save data here
            let payload = {
              sid: systemId,
              rn: reportNo,
              aid: authorityId,
              item: "report-signature",
              data: {
                signImg: croppedDataURL,
                name: modalNameInputEl.value,
                position: modalPositionInputEl.value,
                signType: signType
              }
            };
            addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
              console.log(response);
              toastr.success(response.message);
              dateDisplayEl.innerHTML = `Tarikh : ${formatDateToMalayDateString(response.data.added_at)}`
              $(dateInputEl).val(response.data.added_at);
            }).catch(function (error) {
              console.log(error);
            });
          })
          .catch(error => {
            console.error(error);
          });
      }
    });

    function processSignatureData(dataURL) {
      return new Promise((resolve, reject) => {
        // Create an Image object to load the signature data
        const image = new Image();
        image.src = dataURL;

        image.onload = function () {
          // Create a canvas to perform image processing operations
          const tempCanvas = document.createElement('canvas');
          const tempContext = tempCanvas.getContext('2d');

          // Find the signature bounding box
          const boundingBox = findSignatureBoundingBox(image);

          // Calculate the width and height of the bounding box
          const width = boundingBox.maxX - boundingBox.minX;
          const height = boundingBox.maxY - boundingBox.minY;

          // Draw the signature data onto the temporary canvas, preserving the signature position
          tempCanvas.width = width;
          tempCanvas.height = height;
          tempContext.drawImage(
            image,
            boundingBox.minX,
            boundingBox.minY,
            width,
            height,
            0,
            0,
            width,
            height
          );

          // Get the cropped signature data as a base64 encoded PNG
          const croppedDataURL = tempCanvas.toDataURL('image/png');

          // Resolve the Promise with the croppedDataURL
          resolve(croppedDataURL);
        };

        image.onerror = function () {
          // In case of an error, reject the Promise
          reject(new Error('Failed to load the image.'));
        };
      });
    }

    function findSignatureBoundingBox(image) {
      // Get the pixel data of the image
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      canvas.width = image.width;
      canvas.height = image.height;
      context.drawImage(image, 0, 0);
      const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
      const pixels = imageData.data;

      // Find the minimum and maximum coordinates of the non-transparent pixels
      let minX = canvas.width;
      let minY = canvas.height;
      let maxX = 0;
      let maxY = 0;
      for (let i = 0; i < pixels.length; i += 4) {
        const alpha = pixels[i + 3];
        if (alpha > 0) {
          const x = (i / 4) % canvas.width;
          const y = Math.floor((i / 4) / canvas.width);
          minX = Math.min(minX, x);
          minY = Math.min(minY, y);
          maxX = Math.max(maxX, x);
          maxY = Math.max(maxY, y);
        }
      }

      // Return the bounding box coordinates
      return {
        minX,
        minY,
        maxX,
        maxY
      };
    }

    signCountInit++;
    if (signCountInit < 10) {
      signaturePadIdInit = `signature-pad-0${signCountInit}`;
    } else {
      signaturePadIdInit = `signature-pad-${signCountInit}`;
    }
  }

  function updateFormData() {
    const form = document.getElementById('reportSummary');
    formData = new FormData(form); // Update the global formData with the latest form data
    return formData;
  }

  // Function to convert FormData to a JSON object
  function formDataToJson(formData) {
    const jsonObject = {};

    for (const [key, value] of formData) {
      const keys = key.split(/\]\[|\[|\]/).filter(Boolean);

      let currentObject = jsonObject;
      for (let i = 0; i < keys.length - 1; i++) {
        const currentKey = keys[i];
        if (!currentObject.hasOwnProperty(currentKey)) {
          // If the key doesn't exist, create an object
          if (/^\d+$/.test(keys[i + 1])) {
            currentObject[currentKey] = []; // Use an array if the next key is a number
          } else {
            currentObject[currentKey] = {}; // Use an object if the next key is not a number
          }
        }
        currentObject = currentObject[currentKey];
      }

      // Handle the last key (leaf)
      const lastKey = keys[keys.length - 1];
      if (lastKey === "" || /\[\]$/.test(key)) {
        // If the last key is empty or ends with '[]', treat the value as an array
        if (!Array.isArray(currentObject[lastKey])) {
          currentObject[lastKey] = [value];
        } else {
          currentObject[lastKey].push(value);
        }
      } else {
        currentObject[lastKey] = value;
      }
    }

    return jsonObject;
  }

  function submitControl() {
    const submitButton = document.getElementById('report-submit');
    const discardButton = document.getElementById('report-discard');

    submitButton.addEventListener('click', (event) => {
      // TODO: Sneak peak code to see what the formData value are
      // for (const entry of formData) {
      //   const fieldName = entry[0];
      //   const fieldValue = entry[1];
      //   console.log(fieldName + ':', fieldValue);
      // }

      event.preventDefault();

      // Call the updateFormData function to ensure formData is up-to-date
      updateFormData();

      // Convert formData to JSON object
      const jsonFormData = formDataToJson(formData);

      // Update checklist formdata
      checkListFormData = new FormData(checkListForm);

      // Loop through the FormData entries and populate the JSON object
      for (const [key, value] of checkListFormData.entries()) {
        if (jsonFormData[key]) {
          // If the key already exists (e.g., for checkboxes), add the value to an array
          if (!Array.isArray(jsonFormData[key])) {
            jsonFormData[key] = [jsonFormData[key]];
          }
          jsonFormData[key].push(value);
        } else {
          jsonFormData[key] = value;
        }
      }

      // Log the JSON string
      console.log(jsonFormData);

      var warningNotify = {
        html: `Maaf, Tandatangan Pengesahan tidak lengkap. Sila lengkapkan Tandatangan Pengesahan sebelum membuat penghantaran!`,
        icon: "error",
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Laporan Rosak, Buang!",
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: 'btn btn-secondary'
        }
      };

      var submitNotify = {
        html: `Anda pasti untuk menghantar laporan ini?`,
        icon: "question",
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Ok, Teruskan!",
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: "btn btn-primary",
          cancelButton: 'btn btn-secondary'
        }
      };

      var noteWarning = {
        html: `Catatan masih kosong, Sila masukkan catatan di ruangan catatan`,
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, Baik!",
        customClass: {
          confirmButton: "btn btn-secondary",
        }
      };

      // if (jsonFormData['signature-date-01'] != "" && jsonFormData['signature-date-02'] != "" && jsonFormData['signature-date-03'] != "") {
      if (jsonFormData['signature-date-01'] != "" && jsonFormData['signature-date-02'] != "") {
        if (actionNotes.value.trim() === '') {
          Swal.fire(noteWarning);
        } else {
          Swal.fire(submitNotify).then((result) => {
            if (result.isConfirmed) {
              // TODO: make api request to save data here
              let payload = {
                sid: systemId,
                rn: reportNo,
                aid: authorityId,
                item: "summary-submit",
                data: jsonFormData
              };
              addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
                console.log(response);
                toastr.success(response.message);
                // check if all reports are submitted
                if (response.data.allSubmitted == true) {
                  window.location.href = apps + `/operation/general/tasks/new`;
                } else {
                  window.location.href = apps + `/projects/tasks/${response.data.systemId}`;
                }
              }).catch(function (error) {
                console.log(error);
              });
            }
          });
        }
      } else {
        Swal.fire(warningNotify).then((result) => {
          if (result.isConfirmed) {
            // TODO: make api request to save data here
            // Trigger a click event on the discardButton
            discardButton.click();
          }
        });
      }

    });

    discardButton.addEventListener('click', (event) => {
      var discardNotify = {
        html: `Anda pasti untuk buang laporan ini daripada proses permohonan kerja pengorekan ini?`,
        icon: "error",
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Saya Pasti, Teruskan!",
        cancelButtonText: 'Batal',
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: 'btn btn-secondary'
        }
      };
      Swal.fire(discardNotify).then((result) => {
        if (result.isConfirmed) {
          // TODO: make api request to discard the report
          console.log('discard the report');
        }
      });
    });
  }

  function roadRepeaterInit() {
    // initialize repeater
    var roadRepeater = $('#road-involved').repeater({
      initEmpty: true,

      defaultValues: {
        'text-input': 'foo'
      },

      show: function () {
        // alert('new repeater item added');
        $(this).slideDown();

        // Init select2
        $(this).find('[data-report-repeater="road-method"]').select2();

        // autosave controls
        const roadNameEl = $(this).find('[data-report-repeater="road-name"]');
        const roadIdEl = $(this).find('[data-report-repeater="road-id"]');
        const roadMethodEl = $(this).find('[data-report-repeater="road-method"]');
        const roadLengthEl = $(this).find('[data-report-repeater="road-length"]');
        var inputSaveTimer;

        // function to get values for the current repeater item
        function getRepeaterItemValues(repeaterItem) {
          const values = {};

          repeaterItem.find('[data-report-repeater]').each(function () {
            const dataType = $(this).data('report-repeater');
            let value;

            if ($(this).is('input') || $(this).is('select')) {
              if ($(this).is('[data-report-repeater="road-method"]')) {
                // For select2 elements, use the select2('data') method to get the selected data
                value = $(this).select2('data').map(item => item.id);
              } else {
                value = $(this).val().trim();
              }
            } else if ($(this).is(':checkbox')) {
              value = $(this).is(':checked');
            } else {
              value = $(this).text().trim();
            }

            values[dataType] = value;
          });

          return values;
        }

        // Function to check if all fields, except the signature, are filled
        function checkAllInputFilled() {
          // For regular input elements
          const roadNameValue = roadNameEl.val().trim();
          const roadLengthValue = roadLengthEl.val().trim();

          // For Select2 element (single-select or multi-select)
          const roadMethodValue = roadMethodEl.val();

          return (
            roadNameValue !== '' &&
            roadMethodValue !== null &&
            roadMethodValue.length > 0 &&
            roadLengthValue !== ''
          );
        }

        // function to trigger data save request
        function saveDataToDatabase() {
          // Your code to save data to the database goes here
          // console.log('TODO: Saving road list data to the database...',roadRepeater.repeaterVal());
          let repeaterItem = roadNameEl.closest('[data-repeater-item]');
          // let repeaterVal = roadRepeater.repeaterVal(repeaterItem);
          let repeaterVal = getRepeaterItemValues(repeaterItem);
          // console.log(repeaterVal);
          let payload = {
            sid: systemId,
            rn: reportNo,
            aid: authorityId,
            item: "road-list-single",
            data: repeaterVal
          };
          addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
            console.log(response);
            roadIdEl.val(response.data.id);
            toastr.success(response.message);
          }).catch(function (error) {
            console.log(error);
          });
        }

        roadNameEl.on('input change', function () {
          if (checkAllInputFilled()) {
            clearTimeout(inputSaveTimer);
            inputSaveTimer = setTimeout(saveDataToDatabase, 1500);
          }
        })

        roadMethodEl.on('select2:select', function () {
          if (checkAllInputFilled()) {
            clearTimeout(inputSaveTimer);
            inputSaveTimer = setTimeout(saveDataToDatabase, 1500);
          }
        })

        roadLengthEl.on('input change', function () {
          if (checkAllInputFilled()) {
            clearTimeout(inputSaveTimer);
            inputSaveTimer = setTimeout(saveDataToDatabase, 1500);
          }
        })

        // TODO: check repeater value
        // console.log(roadRepeater.repeaterVal());

        // Trigger the change event manually after adding the repeater item
        document.getElementById('reportSummary').dispatchEvent(new Event('change'));
      },

      hide: function (deleteElement) {
        // element variable to save and update data
        const roadNameEl = $(this).find('[data-report-repeater="road-name"]');
        const roadIdEl = $(this).find('[data-report-repeater="road-id"]');
        const roadMethodEl = $(this).find('[data-report-repeater="road-method"]');
        const roadLengthEl = $(this).find('[data-report-repeater="road-length"]');

        if (confirm('Anda Pasti Untuk Hapus Maklumat Jalan ini daripada Senarai Jalan Terlibat?')) {
          // Store a reference to the current element
          let currentElement = $(this);

          currentElement.slideUp(function () {
            // Call deleteElement() to remove the element from the DOM
            deleteElement();

            // The callback function will be executed after the slideUp animation is complete,
            // ensuring that the element is removed from the DOM.

            // Now that the element is removed from the DOM, you can directly use repeaterVal() to get the updated data
            let repeaterVal = roadRepeater.repeaterVal();
            // Check if the object has the "road-list" property
            // Check if the object has the "road-list" property
            if (!repeaterVal.hasOwnProperty("road-list")) {
              // If "road-list" does not exist, assign an empty array to it
              repeaterVal["road-list"] = [];
            }
            let payload = {
              sid: systemId,
              rn: reportNo,
              aid: authorityId,
              item: "road-list",
              data: repeaterVal["road-list"]
            };

            addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
              console.log(response);
              toastr.success(response.message);
            }).catch(function (error) {
              console.log(error);
            });
          });
        }
      },

      ready: function () {
        // Init select2
        $('[data-report-repeater="select2"]').select2();
      },

      isFirstItemUndeletable: false
    });

    // manipulate repeater
    var roadData = [];
    // var rawRoadData = { "roads": [{ "name": "jalan-1", "method": "HDD", "length": 200 }, { "name": "jalan-2", "method": ["HDD", "CW"], "length": 200 }] };
    var rawRoadData;

    // TODO: axios request to backend to obtain guest list
    addToApiQueue({method: "GET", gateway: `reports/summary/utilities?item=roadList&sid=${systemId}&rn=${reportNo}&aid=${authorityId}`}).then((response) => {
      // console.log({ guests: response.data });
      rawRoadData = { roads: response };

      console.log(rawRoadData);

      // Loop through the JSON data and add each item to the repeater
      $.each(rawRoadData.roads, function (index, road) {

        // Push roads info as a separate object into the array
        roadData.push({
          "road-name": road.road_name,
          "road-id": road.id,
          "road-method": road.method,
          "road-length": road.road_length
        });
      });

      console.log({ roadData: roadData });

      // set the list to repeater items
      roadRepeater.setList(roadData);

      // decide to run totallengthfunction with calculation or not based on roadData value
      if (roadData.length > 0) {
        totalLengthRepeaterInit().initCalculate();
      } else {
        totalLengthRepeaterInit()
      }
    }).catch((error) => {
      console.log(error);
    });
  }

  function totalLengthRepeaterInit() {
    let totalSum;
    let totalLength = document.getElementById('application-length');

    function calculateLength() {
      totalSum = 0;
      $('div[data-repeater-item]').each(function () {
        var $input = $(this).find('.repeater-value');
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

    // Function to update the total sum and set it to the totalLength element
    function updateTotalSum() {
      totalSum = calculateLength();
      $(totalLength).val(totalSum);
    }

    // Loop through all the input fields with class "repeater-value" and initialize the totalSum variable
    totalSum = 0;
    $('.repeater-value').each(function () {
      var value = parseFloat($(this).val());
      if (!isNaN(value)) {
        totalSum += value;
      }
    });
    $(totalLength).val(totalSum);

    // Add event listener to each input element
    $('div[data-repeater-item]').on('input', '.repeater-value', function () {
      updateTotalSum();
    });

    // Add event listener to #road-involved element to clear input
    document.getElementById('road-involved').addEventListener('keyup', function (event) {
      if (event.target && event.target.classList.contains('repeater-value')) {
        if (event.target.value === '') {
          event.target.value = 0;
          event.target.dispatchEvent(new Event('input'));
        }
      }
    });

    // Add event listener to "repeater-add" button
    $(document).ready(function () {
      $('button[data-repeater-create]').on('click', function () {
        // Add event listener to each input element
        $('div[data-repeater-item]').on('input', '.repeater-value', function () {
          updateTotalSum();
        });
        // Trigger update when new repeater item is added
        updateTotalSum();
      });
    });

    $(document).ready(function () {
      // Add event listener to "repeater-delete" button
      $('div[data-repeater-list]').on('click', 'button[data-repeater-delete]', function () {
        $(this).closest('div[data-repeater-item]').find('.repeater-value').each(function () {
          $(this).val('');
          $(this).trigger('input');
          updateTotalSum();
        });
      });
    });

    return {
      initCalculate: function () {
        updateTotalSum();
      }
    }
  }

  function guestRepeaterInit() {
    // initialize repeater
    var guestRepeater = $('#guest-involved').repeater({
      initEmpty: true,

      defaultValues: {
        'text-input': 'foo'
      },

      show: function () {
        $(this).slideDown();

        // signature generate and initialization
        const signatureInputEl = $(this).find('[data-report-repeater="guest-signature"]');
        const signatureModalButton = signatureInputEl.prev();
        if (signatureInputEl.val() !== '') {
          signatureModalButton.removeClass('btn-light-primary');
          signatureModalButton.addClass('btn-secondary');
          signatureModalButton.attr('disabled', true);
        }

        // Integrate signature_pad initialization here
        var tagName = 'div'; // The HTML tag name of the element to create

        if (signCountInitRepeater < 10) {
          signaturePadIdInitRepeater = `signature-pad-0${signCountInitRepeater}`;
        } else {
          signaturePadIdInitRepeater = `signature-pad-${signCountInitRepeater}`;
        }

        var attributes = {
          'class': 'modal fade',
          'tabindex': "-1",
          'id': signaturePadIdInitRepeater
        };
        var newElement = document.createElement(tagName);
        for (var key in attributes) {
          if (attributes.hasOwnProperty(key)) {
            newElement.setAttribute(key, attributes[key]);
          }
        }

        var parentForNewEl = document.querySelector('[data-report-body="container"]');

        parentForNewEl.appendChild(newElement);
        newElement.innerHTML = `<div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tandatangan Pegawai</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fad fa-xmark fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <div name="signature-pad" class="signature-pad m-auto">
                    <div class="signature-pad--body">
                        <canvas></canvas>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" data-report-button="saveSignature">Simpan</button>
            </div>
        </div>
    </div>`;

        // Set modal button attributes for signature-pad
        $(this).find('[data-report-repeater="signature-modal-button"]').attr({
          'data-bs-toggle': 'modal',
          'data-bs-target': '#' + signaturePadIdInitRepeater
        });
        signCountInitRepeater++;
        while (document.getElementById(signaturePadIdInit)) {
          const signModal = document.getElementById(signaturePadIdInit);
          // const signModal = document.getElementById("signature-pad-01");
          const canvas = signModal.querySelector("canvas");
          const saveButton = signModal.querySelector('[data-report-button="saveSignature"]');
          const signaturePad = new SignaturePad(canvas, {
            // It's Necessary to use an opaque color when saving image as JPEG;
            // this option can be omitted if only saving as PNG or SVG
            // backgroundColor: 'rgb(255, 255, 255)'
          });

          // Adjust canvas coordinate space taking into account pixel ratio,
          // to make it look crisp on mobile devices.
          // This also causes canvas to be cleared.
          function resizeCanvas() {
            // When zoomed out to less than 100%, for some very strange reason,
            // some browsers report devicePixelRatio as less than 1
            // and only part of the canvas is cleared then.
            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            // This part causes the canvas to be cleared
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            // This library does not listen for canvas changes, so after the canvas is automatically
            // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
            // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
            // that the state of this library is consistent with visual state of the canvas, you
            // have to clear it manually.
            signaturePad.clear();

            // If you want to keep the drawing on resize instead of clearing it you can reset the data.
            // signaturePad.fromData(signaturePad.toData());
          }

          // On mobile devices it might make more sense to listen to orientation change,
          // rather than window resize events.
          window.addEventListener('resize', resizeCanvas);
          signModal.addEventListener('shown.bs.modal', resizeCanvas);

          resizeCanvas();

          saveButton.addEventListener("click", () => {
            if (signaturePad.isEmpty()) {
              alert("Please provide a signature first.");
            } else {
              const dataURL = signaturePad.toDataURL();// Process the signature data
              processSignatureData(dataURL).then((croppedDataURL) => {
                // Use the croppedDataURL as needed (e.g., save or further process it)
                signatureInputEl.val(croppedDataURL);
                console.log({ "original": dataURL, "converted": signatureInputEl.val() })

                // TODO: make api request to save data here
                let payload = {
                  sid: systemId,
                  rn: reportNo,
                  aid: authorityId,
                  item: "report-signature-img",
                  data: {
                    signImg: croppedDataURL,
                    signId: guestSignatureIdEl.val()
                  }
                };
                addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
                  console.log(response);
                  toastr.success(response.message);
                  guestSignatureEl.val(response.data.signature);
                }).catch(function (error) {
                  console.log(error);
                });

                // Change signature button
                signatureModalButton.removeClass('btn-light-primary');
                signatureModalButton.addClass('btn-secondary');
                signatureModalButton.attr('disabled', true);

                // Hide modal
                signModal.style.display = 'none';
                document.querySelector('.modal-backdrop').classList.remove('show');
                bodyEl.style.overflow = '';
                bodyEl.style.paddingRight = '';
                bodyEl.classList.remove('modal-open');
                document.querySelector('.modal-backdrop').remove();
              }).catch((error) => {
                console.log(error);
              });
            }
          });

          function processSignatureData(dataURL) {
            return new Promise((resolve, reject) => {
              // Create an Image object to load the signature data
              const image = new Image();
              image.src = dataURL;

              image.onload = function () {
                // Create a canvas to perform image processing operations
                const tempCanvas = document.createElement('canvas');
                const tempContext = tempCanvas.getContext('2d');

                // Find the signature bounding box
                const boundingBox = findSignatureBoundingBox(image);

                // Calculate the width and height of the bounding box
                const width = boundingBox.maxX - boundingBox.minX;
                const height = boundingBox.maxY - boundingBox.minY;

                // Draw the signature data onto the temporary canvas, preserving the signature position
                tempCanvas.width = width;
                tempCanvas.height = height;
                tempContext.drawImage(
                  image,
                  boundingBox.minX,
                  boundingBox.minY,
                  width,
                  height,
                  0,
                  0,
                  width,
                  height
                );

                // Get the cropped signature data as a base64 encoded PNG
                const croppedDataURL = tempCanvas.toDataURL('image/png');

                // Resolve the Promise with the croppedDataURL
                resolve(croppedDataURL);
              };

              image.onerror = function () {
                // In case of an error, reject the Promise
                reject(new Error('Failed to load the image.'));
              };
            });
          }

          function findSignatureBoundingBox(image) {
            // Get the pixel data of the image
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = image.width;
            canvas.height = image.height;
            context.drawImage(image, 0, 0);
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const pixels = imageData.data;

            // Find the minimum and maximum coordinates of the non-transparent pixels
            let minX = canvas.width;
            let minY = canvas.height;
            let maxX = 0;
            let maxY = 0;
            for (let i = 0; i < pixels.length; i += 4) {
              const alpha = pixels[i + 3];
              if (alpha > 0) {
                const x = (i / 4) % canvas.width;
                const y = Math.floor((i / 4) / canvas.width);
                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x);
                maxY = Math.max(maxY, y);
              }
            }

            // Return the bounding box coordinates
            return {
              minX,
              minY,
              maxX,
              maxY
            };
          }

          signCountInit++;
          if (signCountInit < 10) {
            signaturePadIdInit = `signature-pad-0${signCountInit}`;
          } else {
            signaturePadIdInit = `signature-pad-${signCountInit}`;
          }
        }

        // autosave controls
        const guestNameEl = $(this).find('[data-report-repeater="guest-name"]');
        const guestCompanyEl = $(this).find('[data-report-repeater="guest-company"]');
        const guestPositionEl = $(this).find('[data-report-repeater="guest-position"]');
        const guestSignatureEl = $(this).find('[data-report-repeater="guest-signature"]');
        const guestTypeEl = $(this).find('[data-report-repeater="guest-type"]');
        const guestSignatureIdEl = $(this).find('[data-report-repeater="guest-signature-id"]');
        const guestContactIdEl = $(this).find('[data-report-repeater="guest-contact-id"]');
        var inputSaveTimer;

        // function to get values for the current repeater item
        function getRepeaterItemValues(repeaterItem) {
          const values = {};

          repeaterItem.find('[data-report-repeater]').each(function () {
            const dataType = $(this).data('report-repeater');
            let value;

            if ($(this).is('input') || $(this).is('select')) {
              if ($(this).is('[data-report-repeater="select2"]')) {
                // For select2 elements, use the select2('data') method to get the selected data
                value = $(this).select2('data').map(item => item.id);
              } else {
                value = $(this).val().trim();
              }
            } else if ($(this).is(':checkbox')) {
              value = $(this).is(':checked');
            } else {
              value = $(this).text().trim();
            }

            values[dataType] = value;
          });

          return values;
        }

        // Function to check if all fields, except the signature, are filled
        function checkAllInputFilled() {
          return guestNameEl.val().trim() !== '' && guestCompanyEl.val().trim() !== '' && guestPositionEl.val().trim() !== '';
        }

        // function to trigger data save request
        let isSaving = false;
        function saveDataToDatabase() {
          // Your code to save data to the database goes here
          // console.log('TODO: Saving road list data to the database...',roadRepeater.repeaterVal());
          
                    if (isSaving) {
                        return; // Prevent additional save attempts
                    }

                    isSaving = true;
          let repeaterItem = guestNameEl.closest('[data-repeater-item]');
          // let repeaterVal = roadRepeater.repeaterVal(repeaterItem);
          let repeaterVal = getRepeaterItemValues(repeaterItem);
          // console.log(repeaterVal);
          let payload = {
            sid: systemId,
            rn: reportNo,
            aid: authorityId,
            item: "guest-list-single",
            data: repeaterVal
          };
          addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
            console.log(response);
            guestSignatureIdEl.val(response.data.sign_id);
            guestContactIdEl.val(response.data.id);
            toastr.success(response.message);
          }).catch(function (error) {
            console.log(error);
                    }).finally(function () {
                        isSaving = false;
          });
        }

        guestNameEl.on('input change', function () {
          if (checkAllInputFilled()) {
              clearTimeout(inputSaveTimer);
              inputSaveTimer = setTimeout(saveDataToDatabase, 100);
          }
        })

        guestCompanyEl.on('input change', function () {
            if (checkAllInputFilled()) {
                clearTimeout(inputSaveTimer);
                inputSaveTimer = setTimeout(saveDataToDatabase, 100);
            }
        })

        guestPositionEl.on('input change', function () {
            if (checkAllInputFilled()) {
                clearTimeout(inputSaveTimer);
                inputSaveTimer = setTimeout(saveDataToDatabase, 100);
            }
        })

        // TODO: check repeater value
        console.log(guestRepeater.repeaterVal());

        // Trigger the change event manually after adding the repeater item
        document.getElementById('reportSummary').dispatchEvent(new Event('change'));
      },

      hide: function (deleteElement) {
        function getRepeaterItemValues(repeaterItem) {
          const values = {};

          repeaterItem.find('[data-report-repeater]').each(function () {
            const dataType = $(this).data('report-repeater');
            let value;

            if ($(this).is('input') || $(this).is('select')) {
              if ($(this).is('[data-report-repeater="select2"]')) {
                // For select2 elements, use the select2('data') method to get the selected data
                value = $(this).select2('data').map(item => item.id);
              } else {
                value = $(this).val().trim();
              }
            } else if ($(this).is(':checkbox')) {
              value = $(this).is(':checked');
            } else {
              value = $(this).text().trim();
            }

            values[dataType] = value;
          });

          return values;
        }

        let repeaterItem = $(this).closest('[data-repeater-item]');
        // let repeaterVal = roadRepeater.repeaterVal(repeaterItem);
        let repeaterVal = getRepeaterItemValues(repeaterItem);

        console.log(repeaterVal);

        // Check if guest-contact-id is null
        if (repeaterVal['guest-contact-id'] === "") {
            // If guest-contact-id is null, simply remove the element without making an API call
            $(this).slideUp(function () {
                deleteElement();
            });
            return; // Exit the function
        }

        if (confirm('Anda pasti untuk hapus individu ini dari Senarai Kehadiran?')) {
          $(this).slideUp(function () {
            // Call deleteElement() to remove the element from the DOM
            deleteElement();

            // The callback function will be executed after the slideUp animation is complete,
            // ensuring that the element is removed from the DOM.

            // Now that the element is removed from the DOM, you can directly use repeaterVal() to get the updated data

            addToApiQueue({method: "DELETE", gateway: `reports/summary/utilities?item=guestList&sid=${systemId}&rn=${reportNo}&aid=${authorityId}&gid=${repeaterVal["guest-signature-id"]}`}).then(function (response) {
              console.log(response);
              toastr.success(response.message);
            }).catch(function (error) {
              console.log(error);
            });
          });
        }
      },

      ready: function () {
        // Init select2
        $('[data-report-repeater="select2"]').select2();
      },

      isFirstItemUndeletable: false
    });

    // manipulate repeater
    var guestData = [];
    var rawGuestData;

    // TODO: axios request to backend to obtain guest list
    addToApiQueue({method: "GET", gateway: `reports/summary/utilities?item=guestList&sid=${systemId}&rn=${reportNo}&aid=${authorityId}`}).then((response) => {
      // console.log({ guests: response.data });
      rawGuestData = { guests: response };

      // Loop through the JSON data and add each item to the repeater
      $.each(rawGuestData.guests, function (index, guest) {

        // Push roads info as a separate object into the array
        guestData.push({
          "guest-name": guest.full_name,
          "guest-company": guest.company_name,
          "guest-position": guest.position,
          "guest-signature": guest.signature,
          "guest-type": guest.sign_type,
          "guest-signature-id": guest.id,
          "guest-contact-id": guest.contact_id
        });
      });

      // set the list to repeater items
      guestRepeater.setList(guestData);
    }).catch((error) => {
      console.log(error);
    });


  }

  function amendDetailsRepeaterInit() {
    var amendRepeater = $('#amend-details').repeater({
      initEmpty: true,

      defaultValues: {
        'text-input': 'foo'
      },
      isFirstItemUndeletable: false,
      show: function () {
        $(this).slideDown();

        // autosave controls
        const amendRoadEl = $(this).find('[data-report-repeater="amend-road-name"]');
        const amendIdEl = $(this).find('[data-report-repeater="amend-id"]');
        const amendOriginalEl = $(this).find('[data-report-repeater="amend-original"]');
        const amendProposedEl = $(this).find('[data-report-repeater="amend-proposed"]');
        var inputSaveTimer;

        // initiate autosize for textarea
        autosize(amendOriginalEl);
        autosize(amendProposedEl);

        // function to get values for the current repeater item
        function getRepeaterItemValues(repeaterItem) {
          const values = {};

          repeaterItem.find('[data-report-repeater]').each(function () {
            const dataType = $(this).data('report-repeater');
            let value;

            if ($(this).is('input') || $(this).is('select')) {
              if ($(this).is('[data-report-repeater="select2"]')) {
                value = $(this).select2('data').map(item => item.id);
              } else {
                value = $(this).val().trim();
              }
            } else if ($(this).is('textarea')) {
              value = $(this).val().trim(); // Use .val() for textarea
            } else if ($(this).is(':checkbox')) {
              value = $(this).is(':checked');
            } else {
              value = $(this).text().trim();
            }

            values[dataType] = value;
          });

          return values;
        }

        // Function to check if all fields, except the signature, are filled
        function checkAllInputFilled() {
          return amendRoadEl.val().trim() !== '' && amendOriginalEl.val().trim() !== '' && amendProposedEl.val().trim() !== '';
        }

        // function to trigger data save request
        function saveDataToDatabase() {
          // Your code to save data to the database goes here
          // console.log('TODO: Saving road list data to the database...',roadRepeater.repeaterVal());
          let repeaterItem = amendRoadEl.closest('[data-repeater-item]');
          // let repeaterVal = roadRepeater.repeaterVal(repeaterItem);
          let repeaterVal = getRepeaterItemValues(repeaterItem);
          // console.log(repeaterVal);

          let payload = {
            sid: systemId,
            rn: reportNo,
            aid: authorityId,
            item: "amend-list-single",
            data: repeaterVal
          };
          addToApiQueue({method: "POST", gateway: `reports/summary/utilities`, data: payload}).then(function (response) {
            console.log(response);
            amendIdEl.val(response.data.id);
            toastr.success(response.message);
          }).catch(function (error) {
            console.log(error);
          });
        }

        amendRoadEl.on('input change', function () {
          if (checkAllInputFilled()) {
            clearTimeout(inputSaveTimer);
            inputSaveTimer = setTimeout(saveDataToDatabase, 1500);
          }
        })

        amendOriginalEl.on('input change', function () {
          if (checkAllInputFilled()) {
            clearTimeout(inputSaveTimer);
            inputSaveTimer = setTimeout(saveDataToDatabase, 1500);
          }
        })

        amendProposedEl.on('input change', function () {
          if (checkAllInputFilled()) {
            clearTimeout(inputSaveTimer);
            inputSaveTimer = setTimeout(saveDataToDatabase, 1500);
          }
        })

        // TODO: check repeater value
        console.log(amendRepeater.repeaterVal());

        // Trigger the change event manually after adding the repeater item
        document.getElementById('reportSummary').dispatchEvent(new Event('change'));
      },
      hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
      },
      ready: function () {
        // initiate autosize for textarea
      }
    });
  }

  function mapSetup() {
    // setup variable for uiblock checking
    let uiBlockTotal = { marker: null, polygon: null, polyline: null };
    let uiBlockCount = { marker: 0, polygon: 0, polyline: 0 };

    // Define Leaflet map
    var map = L.map('map', {
      zoomControl: false,
      scrollWheelZoom: false, // Disable the default zoom control buttons
      dragging: false // Disable dragging option
    }).setView([5.085266518158232, 102.95232001242346], 16);

    var tiles = [
      'https://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibXJoNHMiLCJhIjoiY2poeDh3bmtoMDk5ZTNrcDhkaDBnamFiMiJ9.G-NU8f4IrxF2vr7X43lE0w',
      'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
      // Add more tile URLs here as needed
    ];

    var tileIndex = 0; // Variable to keep track of the current tile index

    // Create a temporary tile layer with the first tile URL
    var tempTileLayer = L.tileLayer(tiles[tileIndex], {
      maxZoom: 21,
      zoomControl: false,
    });

    // Attach the 'tileerror' event to the temporary tile layer
    tempTileLayer.on('tileerror', function (error, tile) {
      console.log('Tile error:', error, tile);

      // Move to the next tile URL
      tileIndex++;
      if (tileIndex < tiles.length) {
        // If there are more tiles, replace the tile layer with the next tile URL
        tempTileLayer.setUrl(tiles[tileIndex]);
      } else {
        // If there are no more tiles, handle the error as desired
        console.log('No more tiles to try.');
      }
    });

    // Add the temporary tile layer to the map
    tempTileLayer.addTo(map);

    /* Creating a new icon object called warningIcon. */
    var warningIcon = L.icon({
      iconUrl: 'assets/media/maps/warning-marker.svg',
      shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    /* Creating a variable called primaryIcon and assigning it a value of L.icon. */
    var primaryIcon = L.icon({
      iconUrl: 'assets/media/maps/primary-marker.svg',
      shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    var successIcon = L.icon({
      iconUrl: 'assets/media/maps/success-marker.svg',
      shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    var infoIcon = L.icon({
      iconUrl: 'assets/media/maps/info-marker.svg',
      shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    var dangerIcon = L.icon({
      iconUrl: 'assets/media/maps/danger-marker.svg',
      shadowUrl: 'assets/media/maps/null.png' // Disable shadow
    });

    //LAYER REQUEST
    var chartingLine = L.Geoserver.wfs("https://map.asiadebut.tech/geoserver/wfs", {
      pmIgnore: true,
      layers: geoserverLayer,
      style: function (f) {
        switch (f.properties.method) {
          case 'CW':
            return {
              color: "#057af6"
            };
          case 'GI':
            return {
              color: "#ec7800"
            };
          case 'HDD':
            return {
              color: "#f31869"
            };
          case 'PJ':
            return {
              color: "#f2dd08"
            };
          case 'GV':
            return {
              color: "#00db3e"
            };
          default:
            return {
              color: "#717071"
            };
        }
      },
      onEachFeature: function (f, l) {

        let color;
        switch (f.properties.method) {
          case 'HDD':
            color = "danger";
            break;
          case 'CW':
            color = "primary";
            break;
          case 'GV':
            color = "success";
            break;
          case 'GI':
            color = "warning";
            break;
          case 'OH':
            color = "dark";
            break;
          default:
            color = "secondary";
        }
      },
      CQL_FILTER: "system_id='" + systemId + "' AND revision = 0",
    });
    chartingLine.addTo(map);

    //MAP CONFIRGURATION
    map.on('zoomend', function () {
      currentZoom = map.getZoom();
      if (currentZoom > 16) {
        chartingLine.setStyle({
          weight: 5,
          dashArray: '20'
        });
      } else if (currentZoom == 16) {
        chartingLine.setStyle({
          weight: 4,
          dashArray: '15'
        });
      } else if (currentZoom < 16) {
        chartingLine.setStyle({
          weight: 3,
          dashArray: '10'
        });
      } else {
        chartingLine.setStyle({
          weight: 2,
          dashArray: '5'
        });
      }
    });

    // TODO: Begin::Do event fetch api to update saved site visit data
    // Marker
    addToApiQueue({method: "GET", gateway: `reports/geometry/marker?action=init&sid=${systemId}&ref=${reportNo}&auth=${authorityId}`, tag: 'marker'}).then(response => {
      console.log({ "marker-init": response });
      uiBlockTotal.marker = response.markers.length;
      if ((uiBlockTotal.marker == 0) && (uiBlockTotal.polygon == 0) && (uiBlockTotal.polyline == 0)) {
        // initLoadBlock.release();
      }
      // var marker;
      response.markers.forEach(element => {
        // create marker
        var marker = L.marker([element.latitude, element.longitude]).addTo(map);

        // Do event fetch api to update unique _leaflet_id to the db
        let payload = {
          action: "update-geom-init",
          systemId: systemId,
          reportId: reportNo,
          authId: authorityId,
          id: element.id,
          geomId: element.geom_id,
          newGeomId: marker._leaflet_id
        };
        addToApiQueue({method: "PUT", gateway: `reports/geometry/marker`, data: payload, tag: 'marker'}).then(response => {
          // console.log(response.data);
          console.log({ "marker-each-update-geom": response });

          // initializing check
          uiBlockCount.marker++;
          if (((uiBlockTotal.marker - uiBlockCount.marker) + (uiBlockTotal.polygon - uiBlockCount.polygon) + (uiBlockTotal.polyline - uiBlockCount.polyline)) == 0) {
            // initLoadBlock.release();
          }
        }).catch(error => {
          console.log(error);
        });


        console.log({ "marker-each-instance": marker });

        // Create a unique ID for the modal using the marker's L.stamp property
        var modalId = 'marker-' + marker._leaflet_id;

        // Create the modal HTML using the unique ID
        var modalHtml = `
      <div class="modal fade" data-bs-backdrop="static" id="` + modalId + `" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                  <form id="form-` + modalId + `">
                      <div class="modal-header">
                          <div class="dropzone">
                              <!--begin::Message-->
                              <div class="dz-message needsclick">
                                  <!--begin::Icon-->
                                  <span class="fa-stack">
                                      <i class="fad fa-image fa-stack-1x"
                                      style="font-size:9rem; position: absolute; left:70px; line-height: 250px;" ></i>
                                      <i class="fad fa-expand fa-stack-2x"
                                      style="font-size:20rem; position: absolute; left:15px; line-height:250px;"></i>
                                  </span>
                                  <!--end::Icon-->
                              </div>
                          </div>
                      </div>
                      <input type="text" id="rowId-` + modalId + `" hidden />
                      <input type="text" id="action-` + modalId + `" value="add" hidden />
                      <div class="modal-body">
                          <div class="mb-0 form-floating fv-row">
                              <textarea  data-kt-autosize="true" rows="4" class="form-control form-control-flush" placeholder="Nyatakan Keterangan"
                              id="desc-` + modalId + `" name="description">` + element.description + `</textarea>
                              <label for="desc-` + modalId + `" class="form-label">Keterangan</label>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>`;

        // Append the modal HTML to the body
        $('body').append(modalHtml);

        // Show the modal when the marker is created
        // $('#' + modalId).modal('show');

        // Set the marker ID as the modal's data attribute
        $('#' + modalId).data('markerId', marker._leaflet_id);

        // Initialize Dropzone for the modal
        const latlng = marker.getLatLng();

        // TODO: request to geom/upload.php
        let dropzone = new Dropzone($('#' + modalId).find('.dropzone')[0], {
          url: apps + "/api/reports/geometry/upload",
          paramName: 'file',
          maxFiles: 1,
          acceptedFiles: "image/*",
          thumbnailWidth: 600,
          thumbnailHeight: 600,
          maxFilesize: 1024,
          autoProcessQueue: false,
          addRemoveLinks: true,
          sending: function (file, xhr, formData) {

            formData.append('systemId', sid);
            formData.append('reportId', ref);
            formData.append('authId', auth);
            formData.append('markerId', marker._leaflet_id);
            formData.append('action', $('#action-' + modalId).val());
            formData.append('id', $('#rowId-' + modalId).val());


          },
          accept: function (file, done) {
            done();
          }
        });

        console.log({ "marker-init-dropzone-var": dropzone });

        let markerRowId;
        markerRowId = element.id;
        $('#rowId-' + modalId).val(markerRowId);

        // Get the marker ID from the modal's data attribute
        var markerId = $('#' + modalId).data('markerId');

        // Find the marker with the given ID
        var marker = map._layers[markerId];

        // Get the input values from the modal
        var description = element.description;

        // Get current timestamp to be patch with image url
        var currentImgDate = new Date();
        var currentImgTimestamp = currentImgDate.getTime();
        var image = atob(element.url) + '?t=' + currentImgTimestamp;

        $('#img-' + modalId).val(image);
        $('#action-' + modalId).val('edit');
        $('#cancel-' + modalId).removeClass("d-none");
        $('#delete-' + modalId).addClass("d-none");
        $('#save-' + modalId).addClass("d-none");
        $('#update-' + modalId).removeClass("d-none");

        // Set the marker's popup content
        marker.bindPopup(`
          <div class="d-flex flex-column">
              <img src="` + image + `" class="report-image" />
              <h4 class="fw-semibold">` + description + `</h4>
          </div>
          `, {
          minWidth: 400,
          closeButton: false,
        });

        // Hide loading indication
        $('#save-' + modalId).removeAttr("data-kt-indicator");

        // Enable button
        $('#save-' + modalId).prop("disabled", false);

        // Hide the modal
        // $('#' + modalId).modal('hide');
        marker.setIcon(primaryIcon);
        map.pm.disableDraw();

        // Handle the "Cancel" button click for edit mode
        $('#cancel-' + modalId).click(function () {
          // Hide the modal
          $('#' + modalId).modal('hide');
        });

        // Handle the "Update" button click
        $('#update-' + modalId).click(function () {
          // TODO: api request to geom/marker.php
          let payload = {
            systemId: systemId,
            reportId: reportNo,
            authId: authorityId,
            description: $('#desc-' + modalId).val(),
            markerId: marker._leaflet_id,
            latitude: latlng.lat,
            longitude: latlng.lng,
            action: $('#action-' + modalId).val(),
            url: $('#img-' + modalId).attr('src'),
            id: markerRowId
          };
          addToApiQueue({method: "PUT", gateway: `reports/geometry/marker`, data: payload, tag: 'marker'}).then(response => {
            var queuedFiles = dropzone.getQueuedFiles();
            if (queuedFiles.length > 0) {
              dropzone.processQueue();
              dropzone.on("success", function (file, result) {
                // Handle success after uploading all files
                if (dropzone.getQueuedFiles().length === 0 && dropzone.getUploadingFiles().length === 0) {
                  handleSuccess(result);
                }
              });
            } else {
              handleSuccess(response);
            }
          }).catch(error => {
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

          function handleSuccess(response) {
            toastr.options = optionToast;
            toastr.success("Pin Lokasi Berjaya Dikemaskini!");

            // Get the marker ID from the modal's data attribute
            var markerId = $('#' + modalId).data('markerId');

            // Find the marker with the given ID
            var marker = map._layers[markerId];

            // Get the input values from the modal
            var description = $('#desc-' + modalId).val();

            // Get current timestamp to be patch with image url
            var currentImgDate = new Date();
            var currentImgTimestamp = currentImgDate.getTime();
            var image = response.data.img + '?t=' + currentImgTimestamp;

            // Set the marker's popup content (on update success)
            marker.bindPopup(`
              <div class="d-flex flex-end">
              </div>
              <div class="d-flex flex-column">
              <img src="` + image + `" class="report-image"/>
              <h4 class="fw-semibold" style="position: relative; top: -30px; word-wrap: break-word;">` + description + `</h4>
              </div>
          `, {
              minWidth: 400,
              closeButton: false,
            }).openPopup();

            // Hide the modal
            $('#' + modalId).modal('hide');
            marker.setIcon(warningIcon);
          }

        });

        // Add a listener for the "pm:edit" event
        marker.on('pm:edit', function (event) {
          // alert('edit event for marker');
          // Get the new marker position
          var newLatLng = event.layer.getLatLng();
          let payload = {
            id: markerRowId,
            systemId: systemId,
            authId: authorityId,
            markerId: marker._leaflet_id,
            latitude: newLatLng.lat,
            longitude: newLatLng.lng,
            reportId: reportNo
          };

          // TODO: api request to geom/marker.php
          addToApiQueue({method: "PUT", gateway: `reports/geometry/marker`, data: payload, tag: 'marker'}).then(response => {
            toastr.options = optionToast;
            // Show a success message to the user
            toastr.success('Lokasi Pin Telah Berubah!');
            marker.setIcon(infoIcon);
          }).catch(error => {
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

        marker.on('pm:remove', function () {
          // TODO: api request to geom/marker.php
          addToApiQueue({method: "DELETE", gateway: `reports/geometry/marker?id=${markerRowId}&markerId=${marker._leaflet_id}&sid=${systemId}&ref=${reportNo}&auth=${authorityId}`, tag: 'marker'}).then(response => {
            toastr.options = optionToast;
            // Show a success message to the user
            toastr.error('Pin bergambar Telah dibuang!');
          }).catch(error => {
            console.log(error);
          });
        });
      });
    }).catch(error => {
      console.log(error);
    });

    // TODO: Polygon
    addToApiQueue({method: "GET", gateway: `reports/geometry/polygon?action=init&sid=${systemId}&ref=${reportNo}&auth=${authorityId}`, tag: 'polygon'}).then(response => {
      console.log({ "polygon-init": response });
      uiBlockTotal.polygon = response.polygon.length;
      if ((uiBlockTotal.marker == 0) && (uiBlockTotal.polygon == 0) && (uiBlockTotal.polyline == 0)) {
        // initLoadBlock.release();
      }
      response.polygon.forEach(element => {
        var dbPolygon = JSON.parse(element.geometry).coordinates[0].map(function (coord) {
          return [coord[1], coord[0]];
        });
        console.log({ "polygon-each": dbPolygon });
        var polygon = L.polygon(dbPolygon, { color: 'blue' }).addTo(map);
        console.log({ "polygon-each-instance": polygon });

        // Do event fetch api to update unique _leaflet_id to the db
        let payload = {
          action: "update-geom-init",
          sid: systemId,
          ref: reportNo,
          auth: authorityId,
          id: element.id,
          geomId: element.geom_id,
          newGeomId: polygon._leaflet_id
        };
        addToApiQueue({method: "PUT", gateway: `reports/geometry/polygon`, data: payload, tag: 'polygon'}).then(response => {
          // console.log(response.data);
          console.log({ "polygon-each-update-geom": response });

          // initializing check
          uiBlockCount.polygon++;
          if (((uiBlockTotal.marker - uiBlockCount.marker) + (uiBlockTotal.polygon - uiBlockCount.polygon) + (uiBlockTotal.polyline - uiBlockCount.polyline)) == 0) {
            // initLoadBlock.release();
          }
        }).catch(error => {
          console.log(error);
        });

        var polygonModalId = 'polygon-' + polygon._leaflet_id;
        let polygonRowId;

        polygon.setStyle({
          color: '#3E97FF'
        });

        // Create the modal HTML using the unique ID
        var modalHtml = `
      <div class="modal fade" data-bs-backdrop="static" id="` + polygonModalId + `" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                  <form>
                      <div class="modal-body mb-0 form-floating">
                          <div class="form-floating mb-5">
                              <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                              id="notes-` + polygonModalId + `" style="height: 100px"></textarea>
                              <label for="notes-` + polygonModalId + `" class="form-label">Catatan</label>
                          </div>
                      </div>
                      <input type="text" id="rowId-` + element.id + `" hidden/>
                      <input type="text" id="action-` + polygonModalId + `" value="edit" hidden/>
                  </form>
              </div>
          </div>
      </div>`;

        // Append the modal HTML to the body
        $('body').append(modalHtml);

        // insert rowId retrieved from db
        polygonRowId = element.id;
        $('#rowId-' + polygonModalId).val(polygonRowId);

        // insert recorded description from db into modal input
        $('#notes-' + polygonModalId).val(element.description);

        // Show the modal when the marker is created
        // $('#' + polygonModalId).modal('show');

        // Set the marker ID as the modal's data attribute
        $('#' + polygonModalId).data('polygonId', polygon._leaflet_id);

        let geom = polygon.toGeoJSON();
        console.log({ "polygon-togeojson": geom });
        console.log({ "polygon-togeojson-sendToRequest": geom });
        // Handle the "Save" button click
        $('#save-' + polygonModalId).click(function () {
          // TODO: api request to geom/polygon.php
          let payload = {
            sid: sid,
            ref: ref,
            auth: auth,
            notes: $('#notes-' + polygonModalId).val(),
            polygonId: polygon._leaflet_id,
            geom: geom,
            action: $('#action-' + polygonModalId).val(),
            id: $('#rowId-' + polygonModalId).val()
          };
          addToApiQueue({method: "POST", gateway: 'reports/geometry/polygon', data: payload, tag: 'polygon'}).then(response => {
            console.log({ 'response.data.id': response.data.id });
            polygonRowId = response.data.id;
            $('#rowId-' + polygonModalId).val(polygonRowId);

            toastr.options = optionToast;
            toastr.success("Poligon Bernota Berjaya Disimpan!");

            // Get the marker ID from the modal's data attribute
            var polygonId = $('#' + polygonModalId).data('polygonId');

            // Find the marker with the given ID
            var polygon = map._layers[polygonId];

            // Get the input values from the modal
            var notes = $('#notes-' + polygonModalId).val();

            $('#cancel-' + polygonModalId).removeClass("d-none");
            $('#delete-' + polygonModalId).addClass("d-none");
            $('#action-' + polygonModalId).val('edit');

            // Set the marker's popup content
            polygon.bindPopup(`
                      <div class="d-flex flex-column">
                          <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
                      </div>
                      `, {
              minWidth: 350,
              closeButton: false,
            }).openPopup();

            // Hide the modal
            $('#' + polygonModalId).modal('hide');
            polygon.setStyle({
              color: '#75CC68'
            });

          })
            .catch(error => {
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

        // initialize bindpopup

        // Get the input values from the modal
        var notes = $('#notes-' + polygonModalId).val();

        $('#cancel-' + polygonModalId).removeClass("d-none");
        $('#delete-' + polygonModalId).addClass("d-none");
        $('#action-' + polygonModalId).val('edit');

        // Set the marker's popup content
        polygon.bindPopup(`
          <div class="d-flex flex-column">
              <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
          </div>
          `, {
          minWidth: 350,
          closeButton: false,
        });

        // Handle the "Cancel" button click for new markers
        $('#delete-' + polygonModalId).click(function () {
          // Get the marker ID from the modal's data attribute
          var polygonId = $('#' + polygonModalId).data('polygonId');

          // Find the marker with the given ID and remove it from the map
          if (polygonId) {
            map.removeLayer(map._layers[polygonId]);
          }

          // Hide the modal
          $('#' + polygonModalId).modal('hide');
        });

        // Add a listener for the "pm:edit" event
        polygon.on('pm:edit', function (event) {
          // TODO: api request to geom/polygon.php
          payload = {
            id: element.id,
            sid: sid,
            auth: auth,
            polygonId: polygon._leaflet_id,
            geom: event.layer.toGeoJSON(),
            ref: ref
          };
          // Send an AJAX request to update the marker in the database
          addToApiQueue({method: "PUT", gateway: 'reports/geometry/polygon', data: payload, tag: 'polygon'}).then(response => {
            toastr.options = optionToast;
            // Show a success message to the user
            toastr.success('Poligon Telah Berubah!');
            polygon.setStyle({
              color: '#7239ea'
            });
          })
            .catch(error => {
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

        polygon.on('pm:remove', function () {
          // TODO: api request to geom/polygon.php
          // Send an AJAX request to delete the marker from the database
          addToApiQueue({method: "DELETE", gateway: `reports/geometry/polygon?sid=${systemId}&ref=${reportNo}&auth=${authorityId}&id=${element.id}&polygonId=${polygon._leaflet_id}`, tag: 'polygon'}).then(response => {
            toastr.options = optionToast;
            // Show a success message to the user
            toastr.error('Poligon Telah dibuang!');
          }).catch(error => {
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
      })
    }).catch(error => {
      console.log(error);
    });

    // TODO: polyline
    addToApiQueue({method: "GET", gateway: `reports/geometry/polyline?action=init&sid=${systemId}&ref=${reportNo}&auth=${authorityId}`, tag: 'polyline'}).then(response => {
      console.log({ "polyline-init": response });
      uiBlockTotal.polyline = response.polyline.length;
      if ((uiBlockTotal.marker == 0) && (uiBlockTotal.polygon == 0) && (uiBlockTotal.polyline == 0)) {
        // initLoadBlock.release();
      }
      response.polyline.forEach(element => {
        var dbLine = JSON.parse(element.geometry).coordinates.map(function (coord) {
          return [coord[1], coord[0]];
        });
        console.log({ "polyline-each": dbLine });
        var polyline = L.polyline(dbLine, { color: 'red' }).addTo(map);
        console.log({ "polyline-each-instance": polyline });

        // Do event fetch api to update unique _leaflet_id to the db
        let payload = {
          action: "update-geom-init",
          sid: systemId,
          ref: reportNo,
          auth: authorityId,
          id: element.id,
          geomId: element.geom_id,
          newGeomId: polyline._leaflet_id
        };
        addToApiQueue({method: "PUT", gateway: 'reports/geometry/polyline', data: payload, tag: 'polyline'}).then(response => {
          // console.log(response.data);
          console.log({ "polyline-each-update-geom": response });

          // initializing check
          uiBlockCount.polyline++;
          if (((uiBlockTotal.marker - uiBlockCount.marker) + (uiBlockTotal.polygon - uiBlockCount.polygon) + (uiBlockTotal.polyline - uiBlockCount.polyline)) == 0) {
            // initLoadBlock.release();
          }
        }).catch(error => {
          console.log(error);
        });

        var polylineModalId = 'line-' + polyline._leaflet_id;

        polyline.setStyle({
          color: '#3E97FF'
        });

        // Create the modal HTML using the unique ID
        var modalHtml = `
      <div class="modal fade" data-bs-backdrop="static" id="` + polylineModalId + `" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                  <form>
                      <div class="modal-body mb-0 form-floating">
                          <div class="form-floating mb-5">
                              <textarea class="form-control form-control-flush" placeholder="Tinggalkan Catatan Disini"
                              id="notes-` + polylineModalId + `" style="height: 100px"></textarea>
                              <label for="notes-` + polylineModalId + `" class="form-label">Catatan</label>
                          </div>
                      </div>
                      <input type="text" id="rowId-` + element.id + `" hidden/>
                      <input type="text" id="action-` + polylineModalId + `" value="edit" hidden/>
                  </form>
              </div>
          </div>
      </div>`;

        // Append the modal HTML to the body
        $('body').append(modalHtml);

        // insert recorded description from db into modal input
        $('#notes-' + polylineModalId).val(element.description);

        let polylineRowId;
        polylineRowId = element.id;
        $('#rowId-' + polylineModalId).val(polylineRowId);

        // Show the modal when the marker is created
        // $('#' + polylineModalId).modal('show');

        // Set the marker ID as the modal's data attribute
        $('#' + polylineModalId).data('lineId', polyline._leaflet_id);

        const geoJSON = polyline.toGeoJSON();
        let geom = geoJSON.geometry.coordinates;
        // Handle the "Save" button click
        $('#save-' + polylineModalId).click(function () {
          // TODO: api request to geom/polyline.php
          let payload = {
            sid: sid,
            ref: ref,
            auth: auth,
            notes: $('#notes-' + polylineModalId).val(),
            polylineId: polyline._leaflet_id,
            geom: geom,
            action: $('#action-' + polylineModalId).val(),
            id: element.id
          };
          addToApiQueue({method: "POST", gateway: 'reports/geometry/polyline', data: payload, tag: 'polyline'}).then((response) => {
            toastr.options = optionToast;
            toastr.success("Lukisan Bernota Berjaya Disimpan!");

            // Get the marker ID from the modal's data attribute
            var polylineId = $('#' + polylineModalId).data('lineId');

            // Find the marker with the given ID
            var polyline = map._layers[polylineId];

            // Get the input values from the modal
            var notes = $('#notes-' + polylineModalId).val();

            $('#cancel-' + polylineModalId).removeClass("d-none");
            $('#delete-' + polylineModalId).addClass("d-none");
            $('#action-' + polylineModalId).val('edit');

            // Set the marker's popup content
            polyline.bindPopup(`
                  <div class="d-flex flex-column">
                      <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
                  </div>
                  `, {
              minWidth: 350,
              closeButton: false,
            }).openPopup();

            // Hide the modal
            $('#' + polylineModalId).modal('hide');
            polyline.setStyle({
              color: '#75CC68'
            });

          }).catch(error => {
            console.log(error);
          });
        });

        // initiate bind popup

        // Get the marker ID from the modal's data attribute
        var polylineId = $('#' + polylineModalId).data('lineId');

        // Find the marker with the given ID
        var polyline = map._layers[polylineId];

        // Get the input values from the modal
        var notes = $('#notes-' + polylineModalId).val();

        $('#cancel-' + polylineModalId).removeClass("d-none");
        $('#delete-' + polylineModalId).addClass("d-none");
        $('#action-' + polylineModalId).val('edit');

        // Set the marker's popup content
        polyline.bindPopup(`
          <div class="d-flex flex-column">
              <h5 class="fw-semibold" style="position: relative; word-wrap: break-word;">` + notes + `</h5>
          </div>
          `, {
          minWidth: 350,
          closeButton: false,
        })

        // Handle the "Cancel" button click for new markers
        $('#delete-' + polylineModalId).click(function () {
          // Get the marker ID from the modal's data attribute
          var polylineId = $('#' + polylineModalId).data('lineId');

          // Find the marker with the given ID and remove it from the map
          if (polylineId) {
            map.removeLayer(map._layers[polylineId]);
          }

          // Hide the modal
          $('#' + polylineModalId).modal('hide');
        });

        // Add a listener for the "pm:edit" event
        polyline.on('pm:edit', function (event) {
          // TODO: api request to geom/polyline.php
          let payload = {
            id: element.id,
            sid: sid,
            auth: auth,
            polylineId: polyline._leaflet_id,
            geom: event.layer.toGeoJSON().geometry.coordinates,
            ref: ref
          };
          // Send an AJAX request to update the marker in the database
          addToApiQueue({method: "PUT", gateway: 'reports/geometry/polyline', data: payload, tag: 'polyline'}).then((response) => {
            toastr.options = optionToast;
            // Show a success message to the user
            toastr.success('Garisan Telah Berubah!');
            polyline.setStyle({
              color: '#7239ea'
            });
          }).catch(error => {
            console.log(error);
          });
        });

        polyline.on('pm:remove', function () {
          // TODO: api request to geom/polyline.php
          // Send an AJAX request to delete the marker from the database
          addToApiQueue({method: "DELETE", gateway: `reports/geometry/polyline?sid=${systemId}&ref=${reportNo}&auth=${authorityId}&id=${element.id}&polylineId=${polyline._leaflet_id}`, tag: 'polyline'}).then(response => {
            toastr.options = optionToast;
            // Show a success message to the user
            toastr.error('Garisan Telah dibuang!');
          }).catch(error => {
            console.log(error);
          });
        });
      })

    }).catch(error => {
      console.log(error);
    });
    // TODO: End::Do event fetch api to update saved site visit data
  }

  // Add event listener to the form to update FormData when changes occur
  document.getElementById('reportSummary').addEventListener('change', updateFormData);

  // Call the function initially to capture the initial form state
  formData = updateFormData();

  return {
    init: function () {
      // totalLengthRepeaterInit(); called inside roadRepeaterInit() after getting list from api
      roadRepeaterInit();
      guestRepeaterInit();
      amendDetailsRepeaterInit();
      submitControl();
      amendDetails();
      checklistControl();
      mapSetup();
    },
  };
})();

KTUtil.onDOMContentLoaded(function () {
  summary.init();
});
