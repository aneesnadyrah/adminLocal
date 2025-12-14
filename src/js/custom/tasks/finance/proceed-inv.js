"use strict";

var financeInv = (function () {
    var handleProceedNewInvoice = () => {

        // Define custom loader CSS properties
        const customLoaderCSS = `
        .custom-loader {
            animation: none;
            border-width: 0;
            margin-top: 15px;
          }
        `;

        // Append custom loader CSS to the head of the document
        const style = document.createElement('style');
        style.type = 'text/css';
        style.innerHTML = customLoaderCSS;
        document.head.appendChild(style);

        // Adding event listeners to buttons with the data-invois attribute
        document.querySelectorAll("[data-invois]").forEach(function(button) {
            button.addEventListener("click", function() {

                // Retrieve data attributes
                var invois = this.getAttribute("data-invois");
                var systemId = this.getAttribute("data-system-id");

                // Log the data attributes (you can perform any action you want here)
                console.log("Data Invois:", invois);
                console.log("System ID:", systemId);

                // You can add more actions here
                // Show confirmation message using SweetAlert
                Swal.fire({
                    title: "Majukan ke tindakan Muat naik Invois Perkhidmatan seterusnya?",
                    text: "Bukti bayaran invois tidak akan dapat dimuat naik selagi invois baharu tidak dikeluarkan.",
                    icon: "warning",
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    confirmButtonText: "Teruskan!",
                    cancelButtonText: "Batal",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                        cancelButton: "btn fw-bold btn-light btn-secondary",
                        loader: 'custom-loader',
                    },
                    loaderHtml: `<span class="spinner-border spinner-border-md text-primary align-middle"></span>`,
                    preConfirm: () => {

                        return new Promise((resolve) => {

                            const jsonData = {
                                systemId: systemId,
                            };

                            // Send notes data to the PHP API using the api.post method
                            api
                                .post(`tasks/finance/proceedInv`, jsonData)
                                .then((response) => {

                                toastr.options = {
                                    closeButton: false,
                                    debug: false,
                                    newestOnTop: false,
                                    progressBar: false,
                                    positionClass: "toastr-top-center",
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

                                toastr.success(response.message);

                                // Navigate to the new page after a successful API call
                                setTimeout(function () {
                                    location.href = "finance/general/tasks/new";
                                }, 2500);

                                resolve();
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

                                resolve();
                            });
                        });
                      },
                      allowOutsideClick: () => !Swal.isLoading()
                })
            });
        });

  };

  return {
    // Public functions
    init: function () {
        handleProceedNewInvoice();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  financeInv.init();
});
