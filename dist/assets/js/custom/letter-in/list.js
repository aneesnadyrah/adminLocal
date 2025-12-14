"use strict";

// Class definition
var LetterIn = (function () {
  // Shared variables
  // var table;
  var dataTable;
  var flatpickr;
  var minDate, maxDate;

  // Private functions
  // var initAddLetterIn = function () {
  //   // Init datatable --- more info on datatables: https://datatables.net/manual/
  //   datatable = $(table).DataTable({
  //     ajax: apps + "/api/letter/in/list",
  //     columns: [
  //       { data: "date_receive" },
  //       { data: "no_ref_tuan" },
  //       { data: "sender" },
  //       { data: "minute_to" },
  //       { data: "method" },
  //       { data: "Status" },
  //       { data: null },
  //     ],
  //     info: false,
  //     language: {
  //       loadingRecords: "Sila Tunggu...",
  //       zeroRecords: "Tiada Rekod Dijumpai",
  //     },
  //     pageLength: 10,
  //     columnDefs: [
  //       {
  //         target: 0,
  //         render: function (index, type, key, meta) {
  //             let timestamp = key["date_receive"]; // replace with your timestamp
  //             let date = new Date(timestamp);
  //             let year = date.getFullYear();
  //             let month = date.getMonth() + 1;
  //             let day = date.getDate();
  //             let formattedDate = year + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
  //             // output: "2021-02-27"

  //             if (key["date_receive"] === null) {
  //                 date = " - ";
  //             } else {
  //                 date = formattedDate;
  //             }

  //           return (
  //             `<div class="text-center">` + date + `</div>`
  //           );
  //         },
  //       },
  //       {
  //         target: 1,
  //         render: function (index, type, key, meta) {
  //           return ( key["no_ref_tuan"] );
  //         },
  //       },
  //       {
  //         target: 2,
  //         render: function (index, type, key, meta) {
  //           return ( key["sender"] );
  //         },
  //       },
  //       {
  //         target: 3,
  //         render: function (index, type, key, meta) {
  //           return ( key["minute_to"] );
  //         },
  //       },
  //       {
  //         target: 4,
  //         render: function (index, type, key, meta) {
  //           let render;
  //           if( key["method"] == 'emel' ){
  //             render =  `<div class="badge badge-light-info">Emel</div>`
  //           } else if( key["method"] == 'fax' ){
  //             render =  `<div class="badge badge-light-primary">Fax</div>`
  //           } else if( key["method"] == 'pos' ){
  //             render =  `<div class="badge badge-light-danger">Pos</div>`
  //           } else if( key["method"] == 'byHand' ){
  //             render =  `<div class="badge badge-light-success">Serahan Tangan</div>`
  //           } else {
  //             render =  `<div class="badge badge-light-secondary">-</div>`
  //           }
  //           return render;
  //         },
  //       },
  //       {
  //         target: 5,
  //         render: function (index, type, key, meta) {
  //           let render;
  //           if( key['status'] == 0 ){
  //             render =  `<div class="badge badge-light-primary">Baru</div>`
  //           } else if( key['status'] == 1 ){
  //             render =  `<div class="badge badge-light-warning">Dalam Proses</div>`
  //           } else if( key['status'] == 2 ) {
  //             render =  `<div class="badge badge-light-success">Selesai</div>`
  //           } else {
  //             render =  `<div>-</div>`
  //           }
  //           return render;
  //         },
  //       },
  //       {
  //         orderable: false,
  //         targets: 6,
  //         render: function (index, type, key, meta) {
  //           return (
  //             `<div class="text-center">
  //                 <a href="/letter/in/view/`+ key["unique_id"] +`" class="btn btn-icon btn-light-primary" title="Lihat Maklumat Surat">
  //                   <i class="fad fa-file-pen fs-2"></i>
  //                 </a>
  //             </div>`
              
  //           );
  //         },
  //       },
  //     ],

  //   });
  // }

  var initTable = () => {
    return new Promise((resolve) => {
        dataTable = new DataTable(table, {
            info: false,
            language: {
                loadingRecords: "Sila Tunggu...",
                zeroRecords: "Tiada Rekod Dijumpai",
            },
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Semua']],
            drawCallback: function () {
                resolve();
            },
        });
    });
};

  // Submit form
  var submitLetterIn = () => {
    // Define variables
    let validator;

    // Get elements
    let form = document.getElementById('form_add_letter_in');
    let submitButton = document.getElementById('submit_add_letter');

    //for image
    let selectId = "#selection-staff";
    // Format options
    var optionFormat = function (item) {
      if (!item.id) {
        return item.text;
      }

      var span = document.createElement("span");
      var imgUrl = item.element.getAttribute("data-kt-select2-user");
      var template = "";

      template +=
        '<img src="' +
        imgUrl +
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


    //for icon
    let selectIcon = "#selection-by";
    // Format options
    var optionIcon = function (option) {
      if (!option.id) {
        return option.text;
      }
      
      var iconClass = $(option.element).data('icon');
      var $icon = $('<i>').addClass('fad ' + iconClass);
      var $text = $('<span>').text('  ' + option.text);
      return $('<span>').append($icon).append($text);

    };
   
    // Init Select2 --- more info: https://select2.org/
    $(selectIcon).select2({
      minimumResultsForSearch: Infinity,
      templateSelection: optionIcon,
      templateResult: optionIcon,
    });
    $(selectIcon).val(null).trigger("change");


    //for letter
    let selectType = "#selection-type";
    // Format options
    var optionFormat = function (item) {
      if (!item.id) {
        return item.text;
      }

      var span = document.createElement("span");
      var template = "";

      template += item.text;

      span.innerHTML = template;

      return $(span);
    };

    // Init Select2 --- more info: https://select2.org/
    $(selectType).select2({
      minimumResultsForSearch: Infinity,
      templateSelection: optionFormat,
      templateResult: optionFormat,
    });

    $(selectType).val(null).trigger("change");
    

    // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
    validator = FormValidation.formValidation(
        form,
        {
            fields: {
                'title': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Isi Tajuk Surat'
                        }
                    }
                },
                'no_ref_letter': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Isi No Rujukan Surat'
                        }
                    }
                },
                'sender': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Isi Pengirim Surat'
                        }
                    }
                },
                'receiver': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Isi Penerima Surat'
                        }
                    }
                },
                'type': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Pilih Jenis Surat'
                        }
                    }
                },
                'date_receive': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Pilih Tarikh Surat Diterima'
                        }
                    }
                },
                'method': {
                    validators: {
                        notEmpty: {
                            message: 'Sila Pilih Surat Diterima'
                        }
                    }
                },
                'notes': {
                  validators: {
                      notEmpty: {
                          message: 'Sila Isi Catatan'
                      }
                  }
              },
                
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
                })
            }
        }
    );

    let letterId;
    let type;
    let code_name;
    // Handle submit button
    submitButton.addEventListener('click', e => {
        e.preventDefault();

        // Validate form before submit
        if (validator) {
            validator.validate().then(function (status) {

                if (status == 'Valid') {
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable submit button whilst loading
                    submitButton.disabled = true;

                    var serializedArray = $(form).serializeArray();
                    var formData = {};

                    serializedArray.forEach(item => {
                        formData[item.name] = item.value;
                    });
          
                    api.post('letter/in/add', JSON.stringify(formData))
                    .then(response => {
                        submitButton.removeAttribute('data-kt-indicator');

                        letterId = response.id;
                        type = response.type;
                        code_name = response.code_name;

                        // process the Dropzone queue
                        dropzone.processQueue();
                        toastr.success(response.message);
                        setTimeout(function () {
                            location.href = "/letter/in/list";
                        }, 2500);

                    })
                    .catch(error => {
                        Swal.fire({
                            text: `Maaf, nampaknya terdapat beberapa ralat dikesan, sila cuba lagi. Ralat: ${error}`,
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, faham",
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
                    });

                } else {
                    Swal.fire({
                        html: "Maaf, nampaknya terdapat beberapa ralat dikesan, sila ambil perhatian untuk mengisi semua maklumat yang diperlukan.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, maklum!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        },
                        allowOutsideClick: false
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            // Enable submit button after loading
                            submitButton.disabled = false;
                        }
                    });
                }
            });
        }
    })

    // Initialize Dropzone for the modal
    let dropzone = new Dropzone("#dropzone_letter_in", { 
      url: apps+"/api/letter/in/upload",
      // url: `${apps}/api/tasks/${statusId}/upload`,
      paramName: "file",
      maxFiles: 5,
      maxFilesize: 1024,
      acceptedFiles: "application/pdf",
      autoProcessQueue: false,
      addRemoveLinks: true,
      sending: function (file, xhr, formData) {
        formData.append("id", letterId);
        formData.append("type", type);
        formData.append("code_name", code_name);
      },
      accept: function (file, done) {
        done();
      },
    });

    dropzone.on("success", function (f, response) {
      submitButton.removeAttribute("data-kt-indicator");
      submitButton.disabled = false;
  
      // toastr.options = {
      //   closeButton: false,
      //   debug: false,
      //   newestOnTop: false,
      //   progressBar: false,
      //   positionClass: "toastr-bottom-right",
      //   preventDuplicates: true,
      //   onclick: null,
      //   showDuration: "300",
      //   hideDuration: "1000",
      //   timeOut: "2000",
      //   extendedTimeOut: "1000",
      //   showEasing: "swing",
      //   hideEasing: "linear",
      //   showMethod: "fadeIn",
      //   hideMethod: "fadeOut",
      // };

      toastr.success(response.message);
      setTimeout(function () {
          location.href = "/letter/in/list";
      }, 2500);

    });
  
    dropzone.on("addedfile", function () {
      submitButton.classList.remove("d-none");
    });
  
    // Remove files when the modal is closed
    dropzone.on("hidden.bs.modal", function () {
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

  var initFlatpickrSingleDate = () => {
    const element = document.querySelector("#single-date-picker");
    flatpickrSingleDate = $(element).flatpickr({
      altInput: true,
      altFormat: "d/m/Y",
      dateFormat: "Y-m-d",
      onChange: function (selectedDates, dateStr, instance) {
        handleFlatpickrSingleDate(selectedDates, dateStr, instance);
      },
    });
  };

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = () => {
    const filterSearch = document.querySelector(
      '[data-table-filter="search"]'
    );
    filterSearch.addEventListener("keyup", function (e) {
      datatable.search(e.target.value).draw();
    });
  };

  // Handle status filter dropdown
  var handleStatusFilter = () => {
    const filterStatus = document.querySelector(
      '[data-table-filter="status-letter"]'
    );
    $(filterStatus).on("change", (e) => {
      let value = e.target.value;
      if (value === "all") {
        value = "";
      }
      datatable.column(5).search(value).draw();
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
      var dateAdded = new Date(moment(data[0], "YYYY-MM-DD"));

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

  var handleFlatpickrSingleDate = (selectedDates, dateStr, instance) => {
    // Access the selected date using selectedDates[0]
    const selectedDate = selectedDates[0];

    // Format the date string for display in the input field
    const formattedDate = instance.formatDate(selectedDate, "d/m/Y");

    // Update the input field value with the formatted date string
    instance.altInput.value = formattedDate;

    // Do any additional processing or validation here...
  };

  // Handle clear flatpickr
  var handleClearFlatpickr = () => {
    const clearButton = document.querySelector("#date-range-clear");
    clearButton.addEventListener("click", (e) => {
      flatpickr.clear();
    });
  };

  // Public methods
  // return {
  //   init: function () {
  //     table = document.querySelector("#list-letter-in");

  //     if (!table) {
  //       return;
  //     }

  //     initAddLetterIn();
  //     initFlatpickr();
  //     initFlatpickrSingleDate();
  //     handleSearchDatatable();
  //     handleStatusFilter();
  //     handleClearFlatpickr();
  //     submitLetterIn();
  //   },
  // };

  return {
    init: function () {
        // filterSearch = document.querySelector('[data-table-filter="search"]');
        // filterStatus = document.querySelector('[data-table-filter="status"]');

        initTable().then(() => {
          initFlatpickr();
          initFlatpickrSingleDate();
          handleSearchDatatable();
          handleStatusFilter();
          handleClearFlatpickr();
          submitLetterIn();
        });
    },
  };

})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  LetterIn.init();
});
