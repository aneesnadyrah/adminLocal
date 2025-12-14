<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// import connection and api setup
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/header.php";
require_once "api/functions.php";

// Connect to the database using PDO
$db               = new DBConnectionFactory();
$telegram         = new Telegram();
$system           = new System;
$flow             = new FlowStatuses($username);
$changelog        = new Changelog($username);
$Curl             = new Curl();
$traffic          = new Traffic();
$taskAssignment   = new TaskAssignments();
$chronology       = new Chronology();
$ftp              = new FTPConnectionFactory();
$ftpConn          = $ftp->createConnection();
$ftpHost          = $system->FTPConnection->host;
$ftpPath          = $system->FTPConnection->path;



// declare function to be used inside the upload controller

// Function to recursively create directories on the FTP server
function ftpRecursiveMkdir($ftpConn, $directory)
{
    $parts = explode('/', $directory);
    $currentDir = '';
    foreach ($parts as $part) {
        $currentDir .= '/' . $part;
        if (!@ftp_chdir($ftpConn, $currentDir)) {
            ftp_mkdir($ftpConn, $currentDir);
            ftp_chdir($ftpConn, $currentDir);
            // Set permissions for the newly created directory
            ftp_chmod($ftpConn, 0755, $currentDir);
        }
    }
}

// get the uploaded file
$systemId = $_POST['systemId'];
$authId = $_POST['authId'];
$file = $_FILES['file'];
$reportId = $_POST['reportId'];
$leafletID = $_POST['markerId'];
$id = $_POST['id'];

try {
    // Create a new PDO instance
    $connPdo = $db->createConnection();

    // Set error mode to exceptions
    $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Declare the query
    $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

    // Prepare the query
    $stmt = $connPdo->prepare($query);

    // Bind the parameters
    $stmt->bindParam(':systemId', $systemId);

    // Execute the query
    $stmt->execute();

    // Fetch the single value from the query result
    $referenceNo = $stmt->fetchColumn();

    // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
    $connPdo = null;

    // Process the retrieved value
    if ($referenceNo !== false) {
        // Value exists, handle it accordingly
        // echo "Reference No: " . $referenceNo;
    } else {
        // No value found
        echo "No reference number found.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (isset($system->App->title)){
    if ($system->App->title == 'UCIDOS'){
        // generate codedReport
        $explodedRef = explode("/", $referenceNo);
        $exploded = explode("/", $reportId);
        $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
        $codedReport = base64_encode($shortRef);
    } elseif ($system->App->title == 'KITER') {
        // generate codedReport
        $explodedRef = explode("/", $referenceNo);
        $exploded = explode("/", $reportId);
        $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
        $codedReport = base64_encode($shortRef);
    } elseif ($system->App->title == 'KUDRAT') {
        // generate codedReport
        $explodedRef = explode("/", $referenceNo);
        $exploded = explode("/", $reportId);
        $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
        $codedReport = base64_encode($shortRef);
    } else {
        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__.' on line '.__LINE__));exit;
    }
} else {
    echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__.' on line '.__LINE__));exit;
}

// TODO: Make directory on the FTP server if it doesn't exist

// $folderPath = "projects/" . date('Y') . "/" . $systemId . "/site/" . $codedReport . "/";
$folderPath = "projects/" . date('Y') . "/" . $systemId . "/Reports/SiteVisit/" . $codedReport . "/";

if ($_POST['action'] =='add') {

    $fileName = $file['name'];
    $extension = pathinfo($fileName)['extension'];
    $newFileName = "(". $exploded[2]. ")". $exploded[1] . '-'.$authId.'-'.$leafletID.'-0';

    try {
        // Create a PDO connection
        $pdo = $db->createConnection();

        // Check for existing record in the database
        $checkQuery = "SELECT COUNT(id) AS version FROM geom_site_visit WHERE system_id = :systemId AND (report_id = :codedReport AND geom_id = :leafletID AND id = :id AND authority_id = :authId)";
        $checkStatement = $pdo->prepare($checkQuery);
        $checkStatement->bindParam(':systemId', $systemId);
        $checkStatement->bindParam(':codedReport', $codedReport);
        $checkStatement->bindParam(':leafletID', $leafletID);
        $checkStatement->bindParam(':id', $id);
        $checkStatement->bindParam(':authId', $authId);
        $checkStatement->execute();

        $version = $checkStatement->fetchColumn();

        // // Connect to the FTP server
        // $ftpConn = ftp_connect($ftpHost, $ftpPort);
        // if (!$ftpConn) {
        //     die("Error connecting to the FTP server");
        // }

        // // Login to the FTP server
        // $login = ftp_login($ftpConn, $ftpUser, $ftpPwd);
        // if (!$login) {
        //     die("Error logging in to the FTP server");
        // }

        // Enable passive mode
        ftp_pasv($ftpConn, true);

        // Check if the directory exists on the FTP server
        $ftpDirectory = "/" . $folderPath;
        if (!@ftp_chdir($ftpConn, $ftpDirectory)) {
            // Create the directory if it doesn't exist
            ftpRecursiveMkdir($ftpConn, $ftpDirectory);
        }

        // Generate the file path on the FTP server
        $ftpFilePath = $ftpDirectory . $newFileName . $version . '.' . $extension;

        // Upload the file to the FTP server
        if (!ftp_put($ftpConn, $ftpFilePath, $file['tmp_name'], FTP_BINARY)) {
            die("Error uploading file to the FTP server");
        } else {
            // Set permissions for the uploaded file
            ftp_chmod($ftpConn, 0755, $ftpFilePath);
        }

        // Close the FTP connection
        ftp_close($ftpConn);

        // Generate the URL based on the uploaded file path on the FTP server

        $encodeUrl = base64_encode('https://' . $ftpHost . '/' . $ftpFilePath);


        // Update the URL in the database
        $updateQuery = "UPDATE geom_site_visit SET url = :encodeUrl WHERE system_id = :systemId AND (report_id = :codedReport AND geom_id = :leafletID AND id = :id AND authority_id = :authId)";
        $updateStatement = $pdo->prepare($updateQuery);
        $updateStatement->bindParam(':encodeUrl', $encodeUrl);
        $updateStatement->bindParam(':systemId', $systemId);
        $updateStatement->bindParam(':codedReport', $codedReport);
        $updateStatement->bindParam(':leafletID', $leafletID);
        $updateStatement->bindParam(':id', $id);
        $updateStatement->bindParam(':authId', $authId);
        $updateStatement->execute();

        // Finally, return a JSON response
        echo json_encode(
            array(
                "message" => "Success",
                "status" => 200,
                "data" => array(
                    "systemID" => $systemId,
                    "img" => base64_decode($encodeUrl)
                )
            )
        );
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    } finally {
        // Close the PDO connection
        $pdo = null;
    }

} else if ($_POST['action'] =='edit') {

$pdo = $db->createConnection();

// Check for existing record in the database
$checkQuery = "SELECT geom_id, url FROM geom_site_visit WHERE system_id = :systemId AND (report_id = :codedReport AND geom_id = :leafletID AND id = :id AND authority_id = :authId)";
$checkStatement = $pdo->prepare($checkQuery);
$checkStatement->bindParam(':systemId', $systemId);
$checkStatement->bindParam(':codedReport', $codedReport);
$checkStatement->bindParam(':leafletID', $leafletID);
$checkStatement->bindParam(':id', $id);
$checkStatement->bindParam(':authId', $authId);
$checkStatement->execute();

$c = $checkStatement->fetch(PDO::FETCH_ASSOC);

$geomId = $c['geom_id'];
$img = $c['url'];
$decodeImg = base64_decode($img);

if ($leafletID == $geomId) {
    $overwriteFileName = basename($decodeImg);
    $targetFile = $folderPath . $overwriteFileName;

    // // Connect to the FTP server
    // $ftpConn = ftp_connect($ftpHost, $ftpPort);
    // if (!$ftpConn) {
    //     die("Error connecting to the FTP server");
    // }

    // // Login to the FTP server
    // $login = ftp_login($ftpConn, $ftpUser, $ftpPwd);
    // if (!$login) {
    //     die("Error logging in to the FTP server");
    // }

    // Enable passive mode
    ftp_pasv($ftpConn, true);

    // Check if the file exists on the FTP server
    if (ftp_size($ftpConn, $targetFile) !== -1) {
        // Delete the existing file
        if (!ftp_delete($ftpConn, $targetFile)) {
            die("Error deleting the existing file from the FTP server");
        }
    }

    // Upload the new file to the FTP server
    if (!ftp_put($ftpConn, $targetFile, $file['tmp_name'], FTP_BINARY)) {
        die("Error uploading the new file to the FTP server");
    } else {
        // Set permissions for the uploaded file
        ftp_chmod($ftpConn, 0755, $targetFile);
    }

    // Close the FTP connection
    ftp_close($ftpConn);


    $encodeUrl = base64_encode('https://' . $ftpHost . '/' . $targetFile);

}

// Update the URL in the database
$updateQuery = "UPDATE geom_site_visit SET url = :encodeUrl WHERE system_id = :systemId AND (report_id = :codedReport AND geom_id = :leafletID AND id = :id)";
$updateStatement = $pdo->prepare($updateQuery);
$updateStatement->bindParam(':encodeUrl', $encodeUrl);
$updateStatement->bindParam(':systemId', $systemId);
$updateStatement->bindParam(':codedReport', $codedReport);
$updateStatement->bindParam(':leafletID', $leafletID);
$updateStatement->bindParam(':id', $id);
$updateStatement->execute();

$decodeUrl = base64_decode($encodeUrl);

// Finally, return a JSON response
echo json_encode(
    array(
        "message" => "Success",
        "status" => 200,
        "data" => array(
            "systemID" => $systemId,
            "img" => $decodeUrl
        )
    )
);

// Close the PDO connection
$pdo = null;

}