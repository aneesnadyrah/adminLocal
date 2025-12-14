function generateRandom64BitString() {
    var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
    var result = "";
    for (var i = 0; i < 16; i++) {
      result += characters.charAt(
        Math.floor(Math.random() * characters.length)
      );
    }
    return result;
}

var minutesLabel = document.getElementById("countdown-minutes");
var secondsLabel = document.getElementById("countdown-seconds");
var totalSeconds = 300;

setInterval(function() {
    var minutes = Math.floor(totalSeconds / 60);
    var seconds = totalSeconds % 60;

    minutesLabel.innerHTML = pad(minutes);
    secondsLabel.innerHTML = pad(seconds);

    if (totalSeconds <= 0) {
        generateQRCode();
        totalSeconds = 300;
    }

    totalSeconds -= 1;
}, 1000);

function generateQRCode() {
    // clear previous qrcode
    document.getElementById("qrcode").innerHTML = '';

    var systemId = "<?php echo $systemId; ?>";
    var randomString = generateRandom64BitString();
    var url =
      "https://" +
      $_SERVER["HTTP_HOST"] +
      "/survey/sign-in.php?sid=" +
      systemId +
      "&proj=" +
      randomString;

    var qrcode = new QRCode(document.getElementById("qrcode"), {
        text: url,
        width: 256,
        height: 256,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
}

generateQRCode();

function pad(val) {
    var valString = val + "";
    if (valString.length < 2) {
        return "0" + valString;
    } else {
        return valString;
    }
}