<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
};

require "api/header.php";
require "config/system.php";

$store = "ucidos.test";

// get the uploaded file
// $systemId = $_POST['systemId'];
$file = $_FILES['file'];
$fileName = $file['name'];
$fileNameWithoutExt = str_replace("." . pathinfo(basename($fileName), PATHINFO_EXTENSION), "", basename($fileName));
$letter_id = $_POST['id'];
$folder = $_POST['code_name'];
$type = $_POST['type'];
$extension = pathinfo($fileName)['extension'];
$newFileName = $fileNameWithoutExt . '-' . $folder;
$folderPath = '../../storage/letter-in/' . date('Y') . '/' . base64_encode($letter_id) . '/';


// Connect to the database using PDO
try {
    $conn = new PDO($PDO);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error in connection: " . $e->getMessage());
}

if (!is_dir('../../storage/letter-in/' . date('Y'))) {
    mkdir('../../storage/letter-in/' . date('Y'), 0777, true);
    mkdir($folderPath, 0777, true);
    // move the uploaded file to a new location
    move_uploaded_file($file['tmp_name'], $folderPath. $newFileName.'.'.$extension);
    $encodeUrl = base64_encode('https://'. $store .'/storage/letter-in/'. date('Y') .'/'. base64_encode($letter_id)  . '/'. $newFileName.'.'. $extension);

} else {
    if(!is_dir($folderPath)) {
        mkdir($folderPath, 0777, true);
        // move the uploaded file to a new location
        move_uploaded_file($file['tmp_name'], $folderPath. $newFileName.'.'.$extension);
        $encodeUrl = base64_encode('https://'. $store .'/storage/letter-in/'. date('Y') .'/'. base64_encode($letter_id) .'/'. $newFileName.'.'. $extension);
    } else {
        // move the uploaded file to a new location
        move_uploaded_file($file['tmp_name'], $folderPath. $newFileName.'.'.$extension);
        $encodeUrl = base64_encode('https://'. $store .'/storage/letter-in/'. date('Y') .'/'. base64_encode($letter_id) . '/'. $newFileName.'.'. $extension);
    }
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

// Close the database connection
$conn = null;

// Finally, return a JSON
echo json_encode(
    array(
        "message" => "Success",
        "status" => 200
    )
);