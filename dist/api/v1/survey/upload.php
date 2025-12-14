<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
require "config/DBFactory.php";
// include_once "api/header.php";

$system   = new System;
$tenant   = $system->App->tenant;
$ftpHost  = $system->FTPConnection->host;
$ftpPath  = $system->FTPConnection->path;

// Connect to the FTP server
$ftp      = new FTPConnectionFactory();
$ftpConn  = $ftp->createConnection();

// $data = file_get_contents("php://input");
// $_POST = json_decode($data, true);

// declare function to be used inside the upload controller

// Enable passive mode
ftp_pasv($ftpConn, true);

// Function to recursively create directories on the FTP server
function ftpRecursiveMkdir($ftpConn, $directory)
{
    // NOTE : uncomment for LOCAL
    // global $ftpPath;
    $parts = explode('/', $directory);
    $currentDir = '';
    foreach ($parts as $part) {
        $currentDir .= '/' . $part;
        if (!@ftp_chdir($ftpConn, $currentDir)) {
            ftp_mkdir($ftpConn, $currentDir);
            ftp_chdir($ftpConn, $currentDir);
            // NOTE : comment for LOCAL
            // Set permissions for the newly created directory
            ftp_chmod($ftpConn, 0755, $currentDir);
        }
    }
}
  
// get the uploaded file
$systemId = $_POST['systemId'];
$file = $_FILES['file'];
$refId = $_POST['refId'];
$exploded = explode("/", $refId);
$yearPart = $exploded[count($exploded) - 2]; // Get the second-to-last part
$timestamp = date('His'); // Retrieves the current time in the format HHMMSS
// $id = $_POST['rId']; // get the unique repeater id passed from dropzone
// $currentYear = intval($yearPart);
$currentYear = date('Y');

// create shortRef from refId for codedReport
$shortRef = $exploded[1] . "-" . $exploded[2] . "-" . $exploded[4] . "(" . $exploded[5] . ")";
$codedReport = base64_encode($shortRef);

// TODO: Make directory on the FTP server if it doesn't exist
// NOTE : Folder Path LOCAL
// $folderPath = $ftpPath . "/Projects/" . $currentYear . "/" . $systemId . "/" . "Reports/" . "Survey/";

// NOTE : Folder Path PROD
$folderPath = "/" . "projects/" . $currentYear . "/" . $systemId . "/" . "Reports/" . "Survey/";

$fileName = $file['name'];
$extension = pathinfo($fileName)['extension'];
// $newFileName = "(" . $exploded[5] . ")" . $exploded[2];
$newFileName = "(" . $exploded[5] . "-" . $timestamp . ")" . $exploded[2];
$destination = $folderPath . $newFileName;

// Check if the directory exists on the FTP server
if (!@ftp_chdir($ftpConn, $folderPath)) {
    // Create the directory if it doesn't exist
    ftpRecursiveMkdir($ftpConn, $folderPath);
}

// Generate the file path on the FTP server
$ftpFilePath = $folderPath . $newFileName . '.' . $extension;

// Upload the file to the FTP server
if (!ftp_put($ftpConn, $ftpFilePath, $file['tmp_name'], FTP_BINARY)) {
    die("Error uploading file to the FTP server");
} else {
    // Set permissions for the uploaded file
    // NOTE : uncomment PROD
    ftp_chmod($ftpConn, 0755, $ftpFilePath);

    // NOTE : uncomment LOCAL
    // ftp_raw($ftpConn, "SITE CHMOD 0755 $ftpFilePath");

}

// Close the FTP connection
ftp_close($ftpConn);

// Generate the URL based on the uploaded file path on the FTP server

$encodeUrl = base64_encode('https://' . $ftpHost . $ftpFilePath);


// $decodeUrl = base64_decode($encodeUrl);

http_response_code(200);
// Finally, return a JSON
echo json_encode(
    [
        "message" => "Success",
        "status" => 200,
        "systemID" => $systemId,
        "img" => $encodeUrl
    ]
);
