"use strict";

// Class definition
var KTSigninGeneral = (function () {
  // Elements
  var form;
  var submitButton;
  var validator;
  let intervalId; // used to update clock

  // setup clock on modal

  // Initialize the timestamp with the server time when the page loads
  let serverTimestamp;

  let latestTime; // Declare latestTime as a global variable

  // Function to fetch the timestamp from the PHP backend
  function fetchServerTimestamp() {
    // Replace 'timestamp.php' with the actual URL of your PHP backend script
    api
      .get(`surveys/tasking?data=time&sid=${sid}`)
      .then((response) => {
        console.log(response);
        // Store the server timestamp
        serverTimestamp = new Date(response.timestamp);

        latestTime = new Date(response.latestTime);
        // Start the clock
        updateClock();
      })
      .catch((error) => {
        console.error("Error fetching server timestamp:", error);
        // timeEl.textContent = "Error fetching server timestamp";
      });
  }

  // Call the fetchServerTimestamp function to initiate fetching the timestamp from the server
  fetchServerTimestamp();

  // Function to update the clock continuously
  function updateClock() {
    // Clear the interval if it's already running to avoid multiple intervals
    clearInterval(intervalId);

    // Start a new interval and store its ID in the intervalId variable
    if (activeStatus) {
      intervalId = setInterval(() => {
        // Get the current time
        const currentTime = new Date();
        // Add 5 minutes (300,000 milliseconds) to the latestTime
        const expiredTime = new Date(latestTime.getTime() + 300000);
        // Calculate the time difference in milliseconds
        const timeDifference = currentTime - serverTimestamp;
        // Update the time based on the difference
        const updatedTime = new Date(
          serverTimestamp.getTime() + timeDifference
        );
        // console.log({ serverTimestamp: serverTimestamp });
        // console.log({ latestTime: latestTime });
        // console.log({ currentTime: currentTime });
        // console.log({ updatedTime: updatedTime });
        // console.log({ expiredTime: expiredTime });
        // Check if the current time is greater than or equal to expiredTime
        if (updatedTime > expiredTime) {
          // console.log({ activeStatus: activeStatus });
          // If the current time is greater, it's time to refresh the page
          window.location.reload();
          return; // Stop the interval after refreshing the page
        }
      }, 1000); // Update the clock every second
    }
  }

  // Handle form
  var handleValidation = function (e) {
    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(form, {
      fields: {
        username: {
          validators: {
            notEmpty: {
              message: "Nama pengguna diperlukan",
            },
            stringLength: {
              min: 6,
              max: 30,
              message: "Nama pengguna mestilah lebih daripada 6 aksara",
            },
            regexp: {
              regexp: /^[a-zA-Z0-9_\.]+$/,
              message:
                "Nama pengguna hanya boleh terdiri daripada abjad, nombor, titik dan garis bawah",
            },
          },
        },
        password: {
          validators: {
            notEmpty: {
              message: "Kata Laluan Diperlukan",
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

  var handleSubmit = function (e) {
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

          let currentURL = window.location.href;

          formData.loginURL = currentURL;

          let jsonData = JSON.stringify(formData);

          // /v1/projects/tasking.php
          // Send Axios POST request
          api
            .post(`surveys/signin`, jsonData)
            .then((response) => {
              console.log(response.data);

              // Hide loading indication
              submitButton.removeAttribute("data-kt-indicator");

              // Enable button
              submitButton.disabled = false;

              if (response.message === "success") {
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

                toastr.success("Selamat Datang, " + response.first_name + "!");

                setTimeout(function () {
                  form.querySelector('[name="username"]').value = "";
                  form.querySelector('[name="password"]').value = "";

                  location.href = "/surveys/success";
                }, 2500);
              } else if (response.message === "failed") {
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

                toastr.error(
                  "Maaf, Sila Semak Nama Pengguna Atau Kata Laluan Anda."
                );
              }
            })
            .catch((error) => {
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

              toastr.error(
                "Maaf, Terdapat Ralat di dalam sistem. Sila cuba sekali lagi."
              );
            });
        } else {
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

          toastr.error(
            "Maaf, Terdapat Ralat di dalam sistem. Sila cuba sekali lagi."
          );
        }
      });
    });
  };

  // Public functions
  return {
    // Initialization
    init: function () {
      form = document.querySelector("#sign_in_form");
      submitButton = document.querySelector("#sign_in_submit");

      handleValidation();
      handleSubmit();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTSigninGeneral.init();
});
