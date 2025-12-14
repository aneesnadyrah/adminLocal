<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
// include "config/tenant.php";
include "config/autoload.php";

// Define the generateRandom64BitString function
function generateRandom64BitStrings()
{
    $randomBytes = random_bytes(8); // 64 bits = 8 bytes
    return bin2hex($randomBytes);
}

// Generate random string in PHP
$randomString = generateRandom64BitStrings();

?>

<!DOCTYPE html>
<html>

<head>
    <title>QR Code Generator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@600&display=swap" rel="stylesheet">
    <style type="text/css">
        #qrcode {
            display: flex;
            justify-content: center;
            margin: .25em auto;
            align-items: center;
        }

        .countdown {
            margin: .5em auto;
            font-family: 'Inter', sans-serif;
            font-size: 2.5em;
            display: flex;
            justify-content: center;
            color: #fd7e14;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
</head>

<body>
    <?php
    require_once "config/system.php";
    require_once "config/DBFactory.php";
    $appsTitle = $system->App->title;
    $appsURL = $system->App->url;
    // var_dump($appsURL);

    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    $user = $_SESSION['username'];
    $submitted = date('Y-m-d H:i:s', time());
    $systemId = $_GET['sid'];
    $currentDate = date('Y-m-d');

    // Check if a matching row exists based on your conditions
    $checking = $conn->prepare("SELECT id, qr_url, created_timestamp, qrcode_session, updated_timestamp FROM flw_survey_attandance WHERE system_id = :systemId AND  DATE(created_timestamp) = :currentDate AND initial_code IS NOT NULL AND clock_in IS NOT NULL AND clock_out IS NULL");
    $checking->bindParam(':systemId', $systemId);
    $checking->bindParam(':currentDate', $currentDate);
    $checking->execute();

    // Fetch all rows from the result set as an array
    $row = $checking->fetch(PDO::FETCH_ASSOC);
    
    // var_dump($row);
    
    // Check if the row exists and if the balance time (current time - clock_in) is >= 5 minutes
    if ($row) {
        
        $session = $row["qrcode_session"];  
        $updatedSession = $session + 1;

        if($row['updated_timestamp'] == null){
            $createdTimestamp = strtotime($row["created_timestamp"]);
            $createdTime = date("H:i:s", $createdTimestamp);
            $clockInTime = strtotime($createdTime);
        } else {
            $updatedTimestamp = strtotime($row["updated_timestamp"]);
            $updatedTime = date("H:i:s", $updatedTimestamp);
            $clockInTime = strtotime($updatedTime);
        }
        $currentTime = time();
        // $currentTime = strtotime($time);
        
        $timeDifference =  $currentTime - $clockInTime;
        $qrUrl = $row['qr_url'];
        $id = $row['id'];
        if ($timeDifference >= 300) {
            // Update the QR code and the updated_timestamp field
            $qrUrl = $appsURL . "/surveys/signin/" . $systemId . "/" . $randomString;

            $query = "UPDATE flw_survey_attandance SET qr_url = :qrUrl, updated_timestamp = :submitted, qrcode_session = :updatedSession WHERE system_id = :systemId AND id = :id AND DATE(created_timestamp) = :currentDate AND initial_code IS NOT NULL AND clock_in IS NOT NULL AND clock_out IS NULL";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':qrUrl', $qrUrl);
            $stmt->bindParam(':submitted', $submitted);
            $stmt->bindParam(':updatedSession', $updatedSession);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':currentDate', $currentDate);

            $result = $stmt->execute();

            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            // var_dump($qrUrl);
        }

    }

    // Close database connection
    $conn = null;
    ?>

    <div id="qrcode"></div>
    <div class="countdown">
        <span id="countdown-minutes">05</span>:<span id="countdown-seconds">00</span>
    </div>

    <script>
        // var phpRandomString = '<?php echo $randomString; ?>';
        // function generateRandom64BitStrings() {
            // const randomBytes = new Uint8Array(8); // 8 bytes for 16 characters
            // crypto.getRandomValues(randomBytes);
            // return Array.from(randomBytes, (byte) =>
            //     ("0" + byte.toString(16)).slice(-2)
            // ).join("");
        //     return phpRandomString;
        // }

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
                location.reload();
                totalSeconds = 300;
            }

            totalSeconds -= 1;
        }, 1000);

        function generateQRCode() {
            // clear previous qrcode
            document.getElementById("qrcode").innerHTML = '';

            var systemId = <?php echo json_encode($systemId); ?>;
            // var randomString1 = generateRandom64BitStrings();
            // var url = "https://" + "<?php echo $system->App->url; ?>" + "/surveys/signin/" + systemId + "/" + "<?= $randomString ?>";
            var url = "<?php  echo $qrUrl; ?>";
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
    </script>

</body>

</html>