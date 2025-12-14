function generateQR(element) {
  var systemId = element.getAttribute('data-system-id');
  console.log("Generate QR for:", systemId);
  
  var container = document.getElementById("qrcodePlan-" + systemId);
  var downloadLink = document.getElementById("downloadLink-" + systemId);
  
  // Disable button
  element.disabled = true;
  
  api.get("qrcode/download/qrcodePlan/" + systemId)
    .then(function(response) {
      container.innerHTML = "";
      
      new QRCode(container, {
        text: response.url,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
      
      setTimeout(function() {
        var canvas = container.querySelector("canvas");
        if (canvas) {
          downloadLink.href = canvas.toDataURL("image/png");
          downloadLink.download = "qrcode-" + systemId + ".png";
          downloadLink.click();
        }
        element.disabled = false;
      }, 300);
    })
    .catch(function(error) {
      console.error("Error:", error);
      alert("Failed to generate QR code.");
      element.disabled = false;
    });
}