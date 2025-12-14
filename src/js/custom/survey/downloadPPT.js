"use strict";

// Class definition
var downloadPPT = (function () {
  var initDownloadPPT = function () {
    var elements = [].slice.call(
      document.querySelectorAll("[data-download-ppt]")
    );

    elements.map(function (items) {
      var systemId = items.getAttribute("data-system-id");
      //   var Id = items.getAttribute("data-id");

      let downloadPdf = items.querySelector('[data-file="pdf"]');
      let downloadZip = items.querySelector('[data-file="zip"]');

      console.log(systemId);     

      // Get the current date
      const currentDate = new Date();

      // Extract year, month, and day components
      const year = currentDate.getFullYear();
      // Month is zero-based, so add 1 to get the correct month
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0");
      const day = currentDate.getDate().toString().padStart(2, "0");

      // Construct the date string in the format "YYYYMMDD"
      const currentDateStr = `${year}${month}${day}`;
      console.log(currentDateStr);

      // Handle form submit
      downloadPdf.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();
        console.log(downloadPdf);

        const serializedArray = [
          {
            name: "data",
            value: "pdfFilePPT",
          },
          {
            name: "system-id",
            value: systemId,
          },
        ];

        const params = new URLSearchParams();

        serializedArray.forEach((item) => {
          params.append(item.name, item.value);
        });

        const queryString = params.toString();
        const apiUrl = `surveys/tasking?${queryString}`;

        api
          .get(apiUrl)
          .then((response) => {
            console.log(response);
            //// Extract file path and name from the response
            const filePath = response.filePath;
            console.log(response.filePath);
            const fileName = response.fileName;

            // Find the index where "storage" starts
            const startIndex = filePath.indexOf("storage");

            // Extract the substring from the startIndex to the end of the filePath
            const relativePath = filePath.substring(startIndex - 1); // Include the "storage" part
            console.log(relativePath);

            // Construct the file URL using the file path
            const fileUrl = `https://${window.location.hostname}/${relativePath}`;
            console.log(fileUrl);

            //// Create a temporary anchor element
            const downloadLink = document.createElement("a");

            // Set the href attribute to the file URL
            downloadLink.href = fileUrl;

            //// Set the download attribute to specify the filename
            downloadLink.download = fileName;

            // Append the anchor element to the document body
            document.body.appendChild(downloadLink);

            console.log(downloadLink);
            downloadLink.click();

            //// Remove the anchor element from the document body
            document.body.removeChild(downloadLink);
          })
          .catch((error) => {
            console.log(error);
          });
      });

      // Handle form submit
      downloadZip.addEventListener("click", function (e) {
        // Prevent button default action
        e.preventDefault();
        console.log(downloadZip);

        const serializedArray = [
          {
            name: "data",
            value: "zipFilePPT",
          },
          {
            name: "system-id",
            value: systemId,
          },
        ];

        const params = new URLSearchParams();

        serializedArray.forEach((item) => {
          params.append(item.name, item.value);
        });

        const queryString = params.toString();
        const apiUrl = `surveys/tasking?${queryString}`;

        api
          .get(apiUrl)
          .then((response) => {
            console.log(response);
            //// Extract file path and name from the response
            const filePath = response.filePath;
            console.log(response.filePath);
            const fileName = response.fileName;

            // Find the index where "storage" starts
            const startIndex = filePath.indexOf("storage");

            // Extract the substring from the startIndex to the end of the filePath
            const relativePath = filePath.substring(startIndex - 1); // Include the "storage" part
            console.log(relativePath);

            // Construct the file URL using the file path
            const fileUrl = `https://${window.location.hostname}/${relativePath}`;
            console.log(fileUrl);

            //// Create a temporary anchor element
            const downloadLink = document.createElement("a");

            // Set the href attribute to the file URL
            downloadLink.href = fileUrl;

            //// Set the download attribute to specify the filename
            downloadLink.download = fileName;

            // Append the anchor element to the document body
            document.body.appendChild(downloadLink);

            console.log(downloadLink);
            downloadLink.click();

            //// Remove the anchor element from the document body
            document.body.removeChild(downloadLink);
          })
          .catch((error) => {
            console.error("Error fetching file:", error);
          });
      });
    });
  };

  // Public methods
  return {
    init: function () {
      // ...

      initDownloadPPT();
    },
  };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
  downloadPPT.init();
});
