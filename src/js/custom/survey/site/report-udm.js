"use strict";

// Class definition
var SurveyReport = (function () {
  // Shared variables
  // const element = document.getElementById('form-survey-add-team');

  // Init add task modal
  var initSurveyReport = () => {
    let submitId = "#submit-button";
    let submitId2 = "#submit-button2";
    let formId = "#form-report-review";
    let form = document.querySelector(formId);
    let submitButton = document.querySelector(submitId);
    let submitButton2 = document.querySelector(submitId2);
    // Retrieve the value from the hidden input field
    var systemId = document.getElementById("system-id").value;

    if (submitButton !== null) {
      // Handle form submit
      submitButton.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        // validator.validate().then(function (status) {
        // if (status == "Valid") {
        // Show loading indication
        submitButton.setAttribute("data-kt-indicator", "on");

        // Disable button to avoid multiple click
        // submitButton.disabled = true;
        Swal.fire({
          title: "Anda telah semak laporan ini?",
          text: "Pastikan anda telah menyemak laporan ini.",
          icon: "warning",
          buttonsStyling: false,
          confirmButtonText: "Ya !",
          cancelButtonText: "Tidak, kembali",
          customClass: {
            confirmButton: "btn fw-bold btn-primary",
            cancelButton: "btn fw-bold btn-light btn-active-light-primary",
          },
          allowOutsideClick: false,
          showCancelButton: true, // Add this line to show the Cancel button
        })
          .then(function (result) {
            if (result.isConfirmed) {
              // User confirmed, proceed with the API request
              api
                .post("surveys/tasking", { report_udm: 1, system_id: systemId })
                .then((response) => {
                  // Hide loading indication
                  submitButton.removeAttribute("data-kt-indicator");

                  // Enable submit button after loading
                  submitButton.disabled = false;

                  toastr.success("Laporan Penuh Berjaya Disemak! 🎉");

                  // Redirect to new.php
                  setTimeout(function () {
                    location.href = "mapping/general/tasks/new";
                  }, 1500);
                });
            }
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
    } else {
      // Handle form submit
      submitButton2.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();

        // Validate form
        // validator.validate().then(function (status) {
        // if (status == "Valid") {
        // Show loading indication
        submitButton2.setAttribute("data-kt-indicator", "on");

        // Disable button to avoid multiple click
        // submitButton2.disabled = true;
        Swal.fire({
          title: "Anda mahu sahkan laporan ini?",
          text: "Pastikan anda semak sebelum mengesahkan laporan ini.",
          icon: "warning",
          buttonsStyling: false,
          confirmButtonText: "Sahkan !",
          cancelButtonText: "Tidak, kembali",
          customClass: {
            confirmButton: "btn fw-bold btn-primary",
            cancelButton: "btn fw-bold btn-light btn-active-light-primary",
          },
          allowOutsideClick: false,
          showCancelButton: true, // Add this line to show the Cancel button
        })
          .then(function (result) {
            if (result.isConfirmed) {
              // User confirmed, proceed with the API request
              api
                .post("surveys/tasking", { report_udm: 2, system_id: systemId })
                .then((response) => {
                  // Hide loading indication
                  submitButton2.removeAttribute("data-kt-indicator");

                  // Enable submit button after loading
                  submitButton2.disabled = false;

                  toastr.success("Laporan Penuh Berjaya Disahkan! 🎉");

                  // Redirect to new.php
                  setTimeout(function () {
                    location.href = "mapping/general/tasks/new";
                  }, 1500);
                });
            }
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
                submitButton2.disabled = false;
                submitButton2.removeAttribute("data-kt-indicator");
              }
            });
          });
      });
    }
  };

  return {
    // Public functions
    init: function () {
      initSurveyReport();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  SurveyReport.init();
});
