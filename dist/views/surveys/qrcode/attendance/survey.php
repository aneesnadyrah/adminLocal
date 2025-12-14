<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "config/functions.php";

$system = new System;

// Define the generateRandom64BitString function
function generateRandom64BitString()
{
    $randomBytes = random_bytes(8);
    $hexString = bin2hex($randomBytes);

    return $hexString;
}

// Add counter variable
// $counter = 0;
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
    global $PDO;

    $conn = General::connectToDatabase();
    
    $user = $_SESSION['username'];
    $submitted = date('Y-m-d H:i:s', time());
    $systemId = $_GET['sid'];
    $currentDate = date('Y-m-d');

    // Check if a matching row exists based on your conditions
    // $query = "SELECT qr_url, clock_in, qrcode_session, updated_timestamp FROM flw_survey_attandance WHERE system_id = '$systemId' AND  DATE(created_timestamp) = '$currentDate' AND initial_code IS NOT NULL AND clock_in IS NOT NULL AND clock_out IS NULL";
    $query = "SELECT qr_url, clock_in, qrcode_session, updated_timestamp 
          FROM flw_survey_attandance 
          WHERE system_id = :systemId 
          AND DATE(created_timestamp) = :currentDate 
          AND initial_code IS NOT NULL 
          AND clock_in IS NOT NULL 
          AND clock_out IS NULL";
    $stmt = $conn->prepare($query);
    // Bind parameters
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':currentDate', $currentDate);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // var_dump($currentDate);

    // Check if the row exists and if the balance time (current time - clock_in) is >= 5 minutes
    if ($row) {
        
        $session = $row["qrcode_session"];  
        $updatedSession = $session + 1;

        if($row['updated_timestamp'] == null){
            $clockInTime = strtotime($row["clock_in"]);
        } else {
            $updatedTimestamp = strtotime($row["updated_timestamp"]);
            $updatedTime = date("H:i:s", $updatedTimestamp);
            $clockInTime = strtotime($updatedTime);
        }
        $currentTime = time();
        // $currentTime = strtotime($time);
        
        $timeDifference =  $currentTime - $clockInTime;

        // function generateRandom64BitString() {
        //     $randomBytes = random_bytes(8); // 64 bits = 8 bytes
        //     return bin2hex($randomBytes);
        // }

        // var_dump($timeDifference);
        if ($timeDifference >= 300) {
            // Update the QR code and the updated_timestamp field
            $qrUrl = $system->App->url . "/surveys/signin/" . $systemId . "/" . generateRandom64BitString();
            // $query = "UPDATE flw_survey_attandance SET qr_url = '$qrUrl', updated_timestamp = '$submitted', qrcode_session = '$updatedSession' 
            // WHERE system_id = '$systemId' AND DATE(created_timestamp) = '$currentDate' AND initial_code IS NOT NULL AND clock_in IS NOT NULL AND clock_out IS NULL";
            $query = "UPDATE flw_survey_attandance 
                        SET qr_url = :qrUrl, 
                            updated_timestamp = :submitted, 
                            qrcode_session = :updatedSession 
                        WHERE system_id = :systemId 
                            AND DATE(created_timestamp) = :currentDate 
                            AND initial_code IS NOT NULL 
                            AND clock_in IS NOT NULL 
                            AND clock_out IS NULL";
            $stmt = $conn->prepare($query);
            // Bind parameters
            $stmt->bindParam(':qrUrl', $qrUrl);
            $stmt->bindParam(':submitted', $submitted);
            $stmt->bindParam(':updatedSession', $updatedSession);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':currentDate', $currentDate);

            // Execute the query
            $stmt->execute();

            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
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

        function generateRandom64BitString() {
          const randomBytes = new Uint8Array(64);
          crypto.getRandomValues(randomBytes);
          return Array.from(randomBytes, (byte) =>
            ("0" + byte.toString(16)).slice(-2)
          ).join("");
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

        var systemId = <?php echo json_encode($systemId); ?>;
        var randomString = generateRandom64BitString();
        var url = "<?php echo $system->App->url; ?>/survey/signin/" + systemId + "/" + randomString;

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