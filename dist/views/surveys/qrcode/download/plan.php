<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
// require_once "config/tenant.php";
require_once "config/autoload.php";
require_once "config/system.php";
?>

<!DOCTYPE html>
<html>

<head>
    <title>QR Code Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600&display=swap" rel="stylesheet">
    <style type="text/css">
        /* #qrcodePlan {
            display: flex;
            justify-content: center;
            margin: .25em auto;
            align-items: center;
        } */

        body {
            font-family: 'Inter', sans-serif;
            text-align: center;
            background-color: #f3f3f3;
        }

        #qrcodeContainer {
            margin-top: 50px;
        }

        #qrcodePlan {
            display: flex;
            justify-content: center;
        }

        #downloadButton {
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        #downloadButton:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
</head>

<body>
    <?php
    $db = General::connectToDatabase();
    $system = new System;
    $tenant = $system->App->title;
    $appsURL = $system->App->url;

    $user = $_SESSION['username'];
    $submitted = date('Y-m-d H:i:s', time());
    $systemId = $_GET['sid'];
    $currentDate = date('Y-m-d');

    try {
        $query = "SELECT sharing_code FROM flw_appl_plan WHERE system_id = :systemId";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $uuid = $row['sharing_code'];
    } catch (PDOException $e) {
        die("Error in query: " . $e->getMessage());
    }

    // Close the database connection
    $conn = null;

    // Generate the QR code data
    $qrCodeData = "$appsURL/sharing/$uuid";
    ?>

<div id="qrcodeContainer">
    <h1>QR Code</h1>
    <div id="qrcodePlan">
    </div>
    <a id="downloadLink" download="qr_code.png" style="display: none;"></a>
</div>

    <script>
        function generateQRCode() {
            // clear previous qrcodePlan
            document.getElementById("qrcodePlan").innerHTML = '';

            var url = "<?php echo $qrCodeData; ?>";

            var qrcodePlan = new QRCode(document.getElementById("qrcodePlan"), {
                text: url,
                width: 256,
                height: 256,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H,
                typeNumber: -1,
            });

            // Convert the QR code canvas to a data URL (PNG format)
            var qrCodeCanvas = document.getElementById("qrcodePlan").getElementsByTagName("canvas")[0];
            var qrCodeDataUrl = qrCodeCanvas.toDataURL("image/png");

            // Set the download link's href to the generated QR code image URL
            var downloadLink = document.getElementById("downloadLink");
            // downloadLink.href = document.getElementById("qrcodePlan").getElementsByTagName("img")[0].src;
            downloadLink.href = qrCodeDataUrl;

            console.log("<?php echo $qrCodeData; ?>");
            console.log(downloadLink.href);

            // Trigger a click event on the download link to initiate the download
            downloadLink.click();
        }

        generateQRCode();
    </script>
</body>
</html>
