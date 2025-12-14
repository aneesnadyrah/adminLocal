"use strict";

// Class definition
var KTLayoutSearch = function () {
    // Private variables
    var element;
    var formElement;
    var mainElement;
    var resultsElement;
    var wrapperElement;
    var emptyElement;

    var searchObject;

    // Private functions
    var processs = function (search) {
        var timeout = setTimeout(function () {
            var number = KTUtil.getRandomInt(1, 3);

            // Hide recently viewed
            mainElement.classList.add("d-none");

            if (number === 3) {
                // Hide results
                resultsElement.classList.add("d-none");
                // Show empty message
                emptyElement.classList.remove("d-none");
            } else {
                // Show results
                resultsElement.classList.remove("d-none");
                // Hide empty message
                emptyElement.classList.add("d-none");
            }

            // Complete search
            search.complete();
        }, 1500);
    };

    var processs = function (search) {
        api.get("search/projects/" + searchObject.getQuery()).then(response => {
            var data = response.data;

            // Clear the results container
            resultsElement.innerHTML = "";

            // Loop through the data
            if (data.length > 0) {
                data.forEach(function (item) {
                    var displayText = item.referenceNo !== null ? item.referenceNo : "#" + item.systemID;
                    // Create a new element to display the data
                    var newElement = document.createElement("div");
                    var hrefValue;

                    console.log(item.OldSystem);

                    if (item.OldSystem === null) {
                        hrefValue = '/projects/details/v2/' + item.systemID;
                    } else {
                        hrefValue = '/projects/details/v1/' + item.systemID;
                    }

                    newElement.innerHTML =
                        `<div class="scroll-y mh-200px mh-lg-350px mb-0">
                            <a href="` + hrefValue + `"
                                class="d-flex text-dark text-hover-primary align-items-center my-3">
                                <div class="symbol symbol-40px me-4">
                                    <img src="` + item.providerData.logo + `" alt="" />
                                </div>
                                <div
                                    class="d-flex flex-column justify-content-start fw-semibold">
                                    <span class="fs-6 fw-semibold">` +
                                    displayText +
                        `</span>
                                    <span class="fs-7 fw-semibold text-muted">` +
                        item.District +
                        `</span>
                                </div>
                            </a>
                        </div>`;
                    // Append the new element to the results container
                    resultsElement.appendChild(newElement);
                });

                // Show results
                resultsElement.classList.remove("d-none");
                // Hide empty message
                emptyElement.classList.add("d-none");

                mainElement.classList.add("d-none");
            } else {
                // Hide results
                resultsElement.classList.add("d-none");
                // Show empty message
                emptyElement.classList.remove("d-none");

                mainElement.classList.add("d-none");
            }


        }).catch(error => {
            console.log(response);
            // Hide results
            resultsElement.classList.add("d-none");
            // Show empty message
            emptyElement.classList.remove("d-none");
        })
        // Complete search
        search.complete();
    };

    var clear = function (search) {
        // Show recently viewed
        mainElement.classList.remove("d-none");
        // Hide results
        resultsElement.classList.add("d-none");
        // Hide empty message
        emptyElement.classList.add("d-none");
    };

    // Public methods
    return {
        init: function () {
            // Elements
            element = document.querySelector("#kt_header_search");

            if (!element) {
                return;
            }

            wrapperElement = element.querySelector('[data-kt-search-element="wrapper"]');
            formElement = element.querySelector('[data-kt-search-element="form"]');
            mainElement = element.querySelector('[data-kt-search-element="main"]');
            resultsElement = element.querySelector(
                '[data-kt-search-element="results"]'
            );
            emptyElement = element.querySelector('[data-kt-search-element="empty"]');

            // Initialize search handler
            searchObject = new KTSearch(element);

            // Ajax search handler
            searchObject.on("kt.search.process", processs);

            // Clear handler
            searchObject.on("kt.search.clear", clear);
        },
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTLayoutSearch.init();
});