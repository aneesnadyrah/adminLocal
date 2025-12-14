<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
};

require "api/header.php";
require "config/system.php";
include "config/tenant.php";

// get the uploaded file
$file = $_FILES['file'];
$fileName = $file['name'];
$letterId = $_POST['id'];
$folder = $_POST['code_name'];
$extension = pathinfo($fileName)['extension'];
$newFileName = base64_decode($letterId) . '-' . $folder;
$folderPath = '/letters/' . date('Y') . '/' . $letterId . '/';

// Connect to the FTP server
$ftpConn = ftp_connect($ftpHost, $ftpPort);
if (!$ftpConn) {
  die("Failed to connect to FTP server");
}

// Login to the FTP server
$ftpLogin = ftp_login($ftpConn, $ftpUser, $ftpPwd);
if (!$ftpLogin) {
  die("FTP login failed");
}

// Enable passive mode
// ftp_set_option($ftpConn, FTP_USEPASVADDRESS, true);
ftp_pasv($ftpConn, true);

// Change directory to projects
if (!ftp_chdir($ftpConn, "letters")) {
  if (ftp_mkdir($ftpConn, "letters")) {
    ftp_chdir($ftpConn, "letters");
    // Set permissions for the newly created directory
    ftp_chmod($ftpConn, 0755, "letters");
  } else {
    die("Failed to create letters directory on FTP server");
  }
}

// Check if the year directory exists, create if not
$yearDirectory = date('Y');
if (!ftp_chdir($ftpConn, $yearDirectory)) {
  if (ftp_mkdir($ftpConn, $yearDirectory)) {
    ftp_chdir($ftpConn, $yearDirectory);
    // Set permissions for the newly created directory
    ftp_chmod($ftpConn, 0755, $yearDirectory);
  } else {
    die("Failed to create year directory on FTP server");
  }
}

// Check if the letterId directory exists, create if not
if (!ftp_chdir($ftpConn, $letterId)) {
  if (ftp_mkdir($ftpConn, $letterId)) {
    ftp_chdir($ftpConn, $letterId);
    // Set permissions for the newly created directory
    ftp_chmod($ftpConn, 0755, $letterId);
  } else {
    die("Failed to create letter ID directory on FTP server");
  }
}

// Upload the file to the FTP server
$remoteFilePath = $folderPath . $newFileName . '.' . $extension;
if (ftp_put($ftpConn, $remoteFilePath, $file['tmp_name'], FTP_BINARY)) {
  // Set permissions for the uploaded file
  ftp_chmod($ftpConn, 0755, $remoteFilePath);
  $encodedUrl = base64_encode('https://' . $ftpHost . '/' . $appsTitle . $remoteFilePath);
} else {
  die("Failed to upload file to FTP server");
}

// Close the FTP connection
ftp_close($ftpConn);

// Connect to the database using PDO
try {
    $conn = new PDO($PDO);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error in connection: " . $e->getMessage());
}

// Prepare the query for update
$query = "UPDATE gen_letters SET document = :doc WHERE id = :id ";
$stmt = $conn->prepare($query);

// Bind the parameters
$stmt->bindParam(':doc', $encodeUrl);
$stmt->bindParam(':id', $ltr_id);

// Values
$ltr_id = $_POST['id'];

// Execute the query and get the ID from the result set
$stmt->execute();

// Finally, return a JSON
header("HTTP/1.1 200 OK");
echo json_encode([
    "message" => "Success",
    "status" => 200
]);

$conn = null;