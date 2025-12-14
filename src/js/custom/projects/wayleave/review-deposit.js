var reviewDeposit = (function () {

  // let PageIDs = [];
  console.log("berjaya");

  $.ajax({
    url: apps + "/api/wayleave/deposit/list",
    dataType: "json",
    success: function (response) {
      console.log("berjaya jugak");

      let data = response.data;
      console.log("data");

    for (let i = 0; i < data.length; i++) {
      let obj = data[i];

      // let statusID = obj.StatusID;
      let ID = obj.id;
      let DepositStatus = obj.deposit_status;
      let systemId = obj.system_id;
      // let letterID = obj.LetterID;
      let PageID = DepositStatus.toString() + ID.toString();
      console.log(PageID);

      // PageIDs.push(PageID); // push the PageID into the array

      if (DepositStatus == '1') {

        // document.addEventListener('DOMContentLoaded', function() {

          let send = "#send-button-" + PageID;
          let formReview = "#form-deposit-return-" + PageID;
          let sending = document.querySelector(send);

        // try {
          if (sending) {
            sending.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Disable button to avoid multiple click
              sending.disabled = true;

              // Show confirmation message using SweetAlert
              Swal.fire({
                title: "Hantar Wang Cagaran yang dijana?",
                text: "Pastikan Anda Semak Sebelum Menghantar!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya !",
                cancelButtonText: "Batal",
                customClass: {
                  confirmButton: "btn fw-bold btn-primary",
                  cancelButton: "btn fw-bold btn-light btn-active-light-primary",
                },
              }).then((result) => {
                if (result.isConfirmed) {

                  sending.setAttribute("data-kt-indicator", "on");

                  const serializedArray = $(formReview).serializeArray().concat({
                    name: 'DepositStatus',
                    value : '2',
                  });

                  const formData = {};

                  serializedArray.forEach(item => {
                      formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // Send Axios POST request
                  api.post(`wayleave/deposit/tasking`, jsonData)
                    .then(response => {

                            sending.removeAttribute("data-kt-indicator");

                            Swal.fire({
                              text: "Wang Cagaran telah berjaya dihantar untuk Disahkan!",
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
                                sending.disabled = false;

                                location.href = "projects/record/authority?sid=" + systemId;

                              }
                            });

                    })
                    .catch(error => {
                        // Hide loading indication
                      sending.removeAttribute("data-kt-indicator");

                      // Enable button
                      sending.disabled = false;

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
              // Enable button
              sending.disabled = false;

            });
          }



      } else if (DepositStatus == '2') {

        // document.addEventListener('DOMContentLoaded', function() {

          let approve = "#approve-button-" + PageID;
          let formReview = "#form-deposit-return-" + PageID;
          let approving = document.querySelector(approve);

        // try {
          if (approving) {
            approving.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              Swal.fire({
                title: "Sahkan Wang Cagaran?",
                text: "Pastikan Anda Semak Sebelum Mengesahkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya !",
                cancelButtonText: "Batal",
                customClass: {
                  confirmButton: "btn fw-bold btn-primary",
                  cancelButton: "btn fw-bold btn-light btn-active-light-primary",
                },
              }).then((result) => {
                if (result.isConfirmed) {

                  // Show loading indication
                  approving.setAttribute("data-kt-indicator", "on");

                  // Disable button to avoid multiple click
                  approving.disabled = true;

                  const serializedArray = $(formReview).serializeArray().concat({
                    name: 'DepositStatus',
                    value : '3',
                  });

                  const formData = {};

                  serializedArray.forEach(item => {
                      formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // Send Axios POST request
                  api.post(`wayleave/deposit/tasking`, jsonData)
                    .then(response => {



                            approving.removeAttribute("data-kt-indicator");

                            Swal.fire({
                              text: "Wang Cagaran telah berjaya Disahkan!",
                              icon: "success",
                              buttonsStyling: false,
                              confirmButtonText: "Ok, maklum!",
                              customClass: {
                                confirmButton: "btn btn-primary",
                              },
                              allowOutsideClick: false,
                            }).then(function (result) {
                              if (result.isConfirmed) {

                                approving.disabled = false;

                                location.href = "projects/record/authority?sid=" + systemId;
                              }
                            });

                    })
                    .catch(error => {
                      // Hide loading indication
                      approving.removeAttribute("data-kt-indicator");

                      // Enable button
                      approving.disabled = false;

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

              // Enable button
              approving.disabled = false;

            });
          }

          // } catch (error) {
          //   console.error(error);
          // }

        // });

      //   let formCom = "#form-comment-letter-" + PageID;
      //   let formComment = document.querySelector(formCom);
      //   let SubmitCom = "#comment-letter-button-" + PageID;
      //   let SubmitComment = document.querySelector(SubmitCom);


      //   let validatorCom = FormValidation.formValidation(formComment, {
      //     fields: {
      //       "letter-comment": {
      //         validators: {
      //           notEmpty: {
      //             message: "Sila isi Catatan",
      //           },
      //         },
      //       },
      //     },
      //     plugins: {
      //       trigger: new FormValidation.plugins.Trigger(),
      //       bootstrap: new FormValidation.plugins.Bootstrap5({
      //         rowSelector: ".fv-row",
      //         eleInvalidClass: "", // comment to enable invalid state icons
      //         eleValidClass: "", // comment to enable valid state icons
      //       }),

      //     },

      //   });

      //   // submitButton.classList.remove('d-none');

      //   // Handle form submit
      //   SubmitComment.addEventListener("click", function (e) {
      //     // Prevent button default action
      //     e.preventDefault();

      //     // Show loading indication
      //     SubmitComment.setAttribute("data-kt-indicator", "on");

      //     // Disable button to avoid multiple click
      //     SubmitComment.disabled = true;

      //     // Validate form
      //     validatorCom.validate().then(function (status) {
      //       if (status == "Valid") {
      //         // Show loading indication
      //         SubmitComment.setAttribute("data-kt-indicator", "on");

      //         // Disable button to avoid multiple click
      //         SubmitComment.disabled = true;

      //         const serializedArray = $(formCom).serializeArray();

      //         const formData = {};

      //         serializedArray.forEach(item => {
      //             formData[item.name] = item.value;
      //         });

      //         const jsonData = JSON.stringify(formData);

      //         // Send Axios POST request
      //         api.post(`letters/tasking`, jsonData)
      //           .then(response => {



      //               const serializedArray2 = {
      //                 id: response.telegram_id,
      //                 msg: response.msg,
      //                 server: response.server,
      //                 mode: 'markdownv2',
      //               };

      //             const formData2 = serializedArray2;

      //               const jsonData2 = JSON.stringify(formData2);

      //               api.post(`bot/message`, jsonData2)
      //                 .then(response => {

      //                   SubmitComment.removeAttribute("data-kt-indicator");

      //                     Swal.fire({
      //                       text: "Surat Permohonan Izin Lalu telah berjaya Dipinda!",
      //                       icon: "success",
      //                       buttonsStyling: false,
      //                       confirmButtonText: "Ok, maklum!",
      //                       customClass: {
      //                         confirmButton: "btn btn-primary",
      //                       },
      //                       allowOutsideClick: false,
      //                     }).then(function (result) {
      //                       if (result.isConfirmed) {

      //                         SubmitComment.disabled = false;

      //                         location.href = "/letters/list"
      //                       }
      //                     });

      //               })
      //                 .catch(error => {
      //                 // Hide loading indication
      //                 SubmitComment.removeAttribute("data-kt-indicator");

      //                 // Enable button
      //                 SubmitComment.disabled = false;

      //                 // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
      //                 Swal.fire({
      //                   text: "Maaf, nampaknya terdapat beberapa ralat dikesan",
      //                   icon: "error",
      //                   buttonsStyling: false,
      //                   confirmButtonText: "Ok, maklum!",
      //                   customClass: {
      //                     confirmButton: "btn btn-primary",
      //                   },
      //                   allowOutsideClick: false,
      //                 });



      //               });

      //           })
      //           .catch(error => {
      //             // Hide loading indication
      //             SubmitComment.removeAttribute("data-kt-indicator");

      //             // Enable button
      //             SubmitComment.disabled = false;

      //             // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
      //             Swal.fire({
      //               text: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi.",
      //               icon: "error",
      //               buttonsStyling: false,
      //               confirmButtonText: "Ok, maklum!",
      //               customClass: {
      //                 confirmButton: "btn btn-primary",
      //               },
      //               allowOutsideClick: false,
      //             });

      //           });

      //       } else {
      //         // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
      //         Swal.fire({
      //           html: "Maaf, sila isi butiran bagi surat ini.",
      //           icon: "warning",
      //           buttonsStyling: false,
      //           confirmButtonText: "Ok, maklum!",
      //           customClass: {
      //             confirmButton: "btn btn-primary",
      //           },
      //           allowOutsideClick: false,
      //         });

      //         // Hide loading indication
      //         SubmitComment.removeAttribute("data-kt-indicator");
      //         // Disable button to avoid multiple click
      //         SubmitComment.disabled = false;
      //       }
      //     });
      //   });
      } else if (DepositStatus == '3') {


        let print = "#print-button-" + PageID;
        let formReview = "#form-PIL-review-" + PageID;
        let printing = document.querySelector(print);
        let printForm = document.querySelector(formReview);

        // try {
        if (printing) {
          printing.addEventListener("click", function (e) {
            e.preventDefault();

            // Disable button to avoid multiple click
            printing.disabled = true;

            printing.setAttribute("data-kt-indicator", "on");
            // fetch('https://kiter.test/components/views/generated-letter-PIL.php')
            //   .then(response => response.text())
            //   .then(pdfPath => {
            //     window.open(pdfPath);
            //   })
            //   .catch(error => {
            //     console.error('Error:', error);
            //   });

            // Create a new style element
            const style = document.createElement('style');

            // Set the CSS content for the @media print rule
            const printCSS = `@media print {
              .container {
                width: 100%;
              }
              /*remove header and footer of window and sizing of A4*/
              @page {
                size: A4;
                margin: 0px;

              }
              /*break between letter*/
              .page-break {
                page-break-before: always !important;
              }

              .section-1 {
                padding-top: -40px !important;
                page-break-inside: avoid;
                page-break-after: always !important;

              }

              .section-2{

                page-break-inside: avoid !important;
                padding-top: 60px !important;
                padding-bottom: -20px !important;
                position: static !important;
              }

              /*hide sidebar*/
              #kt_aside {
                display: none;
              }
              /*hide header*/
              #kt_header {
                display: none !important;
              }
              /*hide footer*/
              #kt_footer {
                display: none !important;
              }
              /*remove card*/
              .card {
                box-shadow: none;
                border: none;
                background-color: transparent;
                position: static !important;
              }

              .card-body {
                /* Adjust the font size, margins, padding, etc. as needed */
                font-size: 12px;
                margin: 10px;
                padding: 10px;
              }

            }`;

            // Set the style element's type and content
            style.type = 'text/css';
            style.textContent = printCSS;

            // Append the style element to the document's head
            document.head.appendChild(style);

            // scrollTop.go();
            setTimeout(() => {
              window.print();
              printing.removeAttribute("data-kt-indicator");

              // Enable button
              printing.disabled = false;
            }, 1000);

          });
        }

          // async function generatePDFFromContent(content) {
          //   const pdfDoc = await PDFLib.PDFDocument.create();
          //   const page = pdfDoc.addPage();

          //   const { width, height } = page.getSize();

          //   const font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);
          //   const fontSize = 12;

          //   const textWidth = font.widthOfTextAtSize(content, fontSize);
          //   const textHeight = font.heightAtSize(fontSize);

          //   const textX = (width - textWidth) / 2;
          //   const textY = (height - textHeight) / 2;

          //   page.drawText(content, {
          //     x: textX,
          //     y: textY,
          //     size: fontSize,
          //     font: font,
          //   });

          //   return pdfDoc;
          // }

          // // const generateButton = document.getElementById('generate-button');
          // printing.addEventListener('click', async () => {
          //   // Get the content from an input field or any other source
          //   const content = printForm.value;

          //   // Generate the PDF
          //   const pdfDoc = await generatePDFFromContent(content);

          //   // Save or download the PDF
          //   const pdfBytes = await pdfDoc.save();
          //   const pdfBlob = new Blob([pdfBytes], { type: 'application/pdf' });
          //   const pdfUrl = URL.createObjectURL(pdfBlob);

          //   // Open the PDF in a new window
          //   window.open(pdfUrl);
          // });



        // } catch (error) {
        //   console.error(error);
        // }

        //TODO: Sambung SIni
      } else if (DepositStatus == '4') {

        // document.addEventListener('DOMContentLoaded', function() {
          let send = "#send-button-" + PageID;
          let formReview = "#form-PIL-review-" + PageID;
          let sending = document.querySelector(send);


        // try {
          if (sending) {
            sending.addEventListener("click", function (e) {
              // Prevent button default action
              e.preventDefault();

              // Disable button to avoid multiple click
              sending.disabled = true;

              // Show confirmation message using SweetAlert
              Swal.fire({
                title: "Hantar Wang Cagaran yang Dijana?",
                text: "Pastikan Anda Semak Sebelum Menghantar!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya !",
                cancelButtonText: "Batal",
                customClass: {
                  confirmButton: "btn fw-bold btn-primary",
                  cancelButton: "btn fw-bold btn-light btn-active-light-primary",
                },
              }).then((result) => {
                if (result.isConfirmed) {

                  sending.setAttribute("data-kt-indicator", "on");

                  const serializedArray = $(formReview).serializeArray().concat({
                    name: 'letterStatus',
                    value : '2',
                  });

                  const formData = {};

                  serializedArray.forEach(item => {
                      formData[item.name] = item.value;
                  });

                  const jsonData = JSON.stringify(formData);

                  // Send Axios POST request
                  api.post(`letters/index`, jsonData)
                    .then(response => {



                        const serializedArray2 = {
                          id: response.telegram_id,
                          msg: response.msg,
                          server: response.server,
                          mode: 'markdownv2',
                        };

                      const formData2 = serializedArray2;

                        const jsonData2 = JSON.stringify(formData2);

                        api.post(`bot/message`, jsonData2)
                          .then(response => {

                            sending.removeAttribute("data-kt-indicator");

                          Swal.fire({
                            text: "Wang Cagaran telah berjaya dihantar untuk Disahkan!",
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
                              sending.disabled = false;

                              location.href = "projects/record/authority?sid=" + systemId;

                            }
                          });

                        })
                          .catch(error => {
                          // Hide loading indication
                          sending.removeAttribute("data-kt-indicator");

                          // Enable button
                          sending.disabled = false;

                          // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                          Swal.fire({
                            text: "Maaf, nampaknya terdapat beberapa ralat dikesan",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, maklum!",
                            customClass: {
                              confirmButton: "btn btn-primary",
                            },
                            allowOutsideClick: false,
                          });



                        });

                    })
                    .catch(error => {
                       // Hide loading indication
                      sending.removeAttribute("data-kt-indicator");

                      // Enable button
                      sending.disabled = false;

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

              // Enable button
              sending.disabled = false;

            });
          }

          // } catch (error) {
          //   console.error(error);
          // }

        // });

        let stepperModal = "#kt_stepper_update_letter_PIL_" + PageID;
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
          console.log('stepper.next');

          // Validate form before change stepper step
          var validator = validations[stepper.getCurrentStepIndex() - 1]; // get validator for currnt step

          if (validator) {
            validator.validate().then(function (status) {
              console.log('validated!');

              if (status == 'Valid') {
                stepper.goNext();

                //KTUtil.scrollTop();
              } else {
                // Show error message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                Swal.fire({
                html: "Maaf, sila isi butiran bagi Wang Cagaran ini.",
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
          console.log('stepper.previous');

          stepper.goPrevious();
          // KTUtil.scrollTop();
        });

        let selectName = "#approval-name-" + PageID;
        let selectPosition = "#approval-position-" + PageID;
        let officerName1 = "#officer-name1-" + PageID;
        let officerContact1 = "#officer-contact1-" + PageID;
        let officerName2 = "#officer-name2-" + PageID;
        let officerContact2 = "#officer-contact2-" + PageID;

        $(document).ready(function () {
          $(selectName).on('change', function () {
            var position = $(this).find(':selected').data('position');
            $(selectPosition).val(position);
          });
          $(officerName1).on('change', function () {
            var contact1 = $(this).find(':selected').data('contact');
            $(officerContact1).val(contact1);
          });
          $(officerName2).on('change', function () {
            var contact2 = $(this).find(':selected').data('contact');
            $(officerContact2).val(contact2);
          });
        });

          let toogleBP = "#tanda-bagiPihak-" + PageID;
          let inputBP = "#bp-position-" + PageID;
          const toggleInput = document.querySelector(toogleBP);
          const mySelect = document.querySelector(inputBP);

                // Check the initial state of the toggle input
        if (toggleInput.checked) {
          $(mySelect).removeClass('d-none');
        } else {
          $(mySelect).addClass('d-none');
        }

          $(toggleInput).on('change', function () {
            if (this.checked) {
              $(mySelect).removeClass('d-none');
            } else {
              $(mySelect).addClass('d-none');
            }
          });


          var startDate = "#date-start-" + PageID;
          var endDate = "#date-finish-" + PageID;
          var endDates = document.querySelector(endDate);
          // var modalBody = "#update-letter-" + PageID;
          // var modalBodys = document.querySelector(modalBody);

          // flatpickr(startDate, {
          //   locale: "ms",
          //   altInput: true,
          //   altFormat: "j F Y",
          //   dateFormat: "Y-m-d",
          // });

          $(startDate).flatpickr({
            locale: "ms",
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            // static: true,
            position: "above",
          });

          $(endDates).flatpickr({
            locale: "ms",
            altInput: true,
            altFormat: "j F Y",
            dateFormat: "Y-m-d",
            // static: true,
            position: "above",
          });


        let formUp = "#form-update-" + PageID;
        let formUpdate = document.querySelector(formUp);
        let submitUp = "#submit-update-" + PageID;
        let submitUpdate = document.querySelector(submitUp);


          var validations = [];

          validations.push(FormValidation.formValidation(formUpdate, {
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
          }));

          validations.push(FormValidation.formValidation(formUpdate, {
            fields: {
              'date-start': {
                validators: {
                    date: {
                        format: 'YYYY-MM-DD',
                        message: 'Nilai itu bukan tarikh yang sah',
                    },
                    notEmpty: {
                        message: 'Sila Pilih Tarikh Jangka Mula'
                    }
                }
            },
            'date-finish': {
              validators: {
                  date: {
                      format: 'YYYY-MM-DD',
                      message: 'Nilai itu bukan tarikh yang sah',
                  },
                  notEmpty: {
                      message: 'Sila Pilih Tarikh Jangka Siap'
                  }
              }
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
          }));

          validations.push(FormValidation.formValidation(formUpdate, {
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
          }));

        // submitButton.classList.remove('d-none');

        // Handle form submit
        submitUpdate.addEventListener("click", function (e) {
          // Prevent button default action
          e.preventDefault();

          // Show loading indication
          submitUpdate.setAttribute("data-kt-indicator", "on");

          // Disable button to avoid multiple click
          submitUpdate.disabled = true;

          var validator = validations[2];
          // Validate form
          validator.validate().then(function (status) {
            if (status == "Valid") {
              // Show loading indication
              submitUpdate.setAttribute("data-kt-indicator", "on");

              // Disable button to avoid multiple click
              submitUpdate.disabled = true;

              const serializedArray = $(formUpdate).serializeArray();
              const formData = {};

              serializedArray.forEach(item => {
                  formData[item.name] = item.value;
              });

              const jsonData = JSON.stringify(formData);

            // Send Axios POST request
            api.post(`letters/tasking`, jsonData)
                .then(response => {
                  // Hide loading indication
                  submitUpdate.removeAttribute("data-kt-indicator");

                  let res_sid = response.systemId;
                  let res_lid = response.letterId.id;



                  Swal.fire({
                    text: "Wang Cagaran telah berjaya Dikemaskini!",
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
                      submitUpdate.disabled = false;

                      location.href = "/letters/PIL/" + res_sid + "/" + res_lid;
                    }
                  });
                })
                .catch(error => {
                  // Hide loading indication
                  submitUpdate.removeAttribute("data-kt-indicator");

                  // Enable button
                  submitUpdate.disabled = false;

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
                html: "Maaf, sila isi butiran bagi Wang Cagaran ini.",
                icon: "warning",
                buttonsStyling: false,
                confirmButtonText: "Ok, maklum!",
                customClass: {
                  confirmButton: "btn btn-primary",
                },
                allowOutsideClick: false,
              });

              // Hide loading indication
              submitUpdate.removeAttribute("data-kt-indicator");
              // Disable button to avoid multiple click
              submitUpdate.disabled = false;
            }
          });
        });
      }

      // call your function and pass in the PageID
      // myFunction(PageID);
      // mySubmit(PageID);
      // }
    }

    // Access the PageIDs array here and perform further operations if needed
    // console.log(PageIDs);
    // ...
  },
  error: function(xhr, status, error) {
    console.log("AJAX request error:", error);
  }

  });




  // define your function outside of the AJAX call
  function myFunction(PageID) {










  }

  return {
    init: function () {
      myFunction();
    },
  };


})();


KTUtil.onDOMContentLoaded(function () {
  reviewDeposit.init();
});
