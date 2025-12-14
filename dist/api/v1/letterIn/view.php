
<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// import connection and api setup
require "api/header.php";
require "config/system.php";
include_once "config/functions.php";

$json_data = file_get_contents("php://input");
// Check if JSON data was retrieved successfully
if ($json_data === false) {
    echo 'Error retrieving JSON data';
    exit;
}

$POST = json_decode($json_data, true);
// Check if JSON data was parsed successfully
if ($POST === null) {
    echo 'Error parsing JSON data';
    exit;
}

// Connect to the database using PDO
try {
    $conn = new PDO($PDO);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error in connection: " . $e->getMessage());
}

// Prepare the query for insert
$query = "INSERT INTO gen_letter_cronologies (title, notes, created_at, created_by, letter_id, status, minute_to) VALUES(:title, :notes, :created, :created_by, :letter_id, :status, :minute_to)";
$stmt = $conn->prepare($query);

// Bind the parameters
$stmt->bindParam(':title', $title);
$stmt->bindParam(':notes', $POST['notes']);
$stmt->bindParam(':created', $timestamp);
$stmt->bindParam(':created_by', $_SESSION['username']);
$stmt->bindParam(':letter_id', $unique_id);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':minute_to', $minute_to);

// Values
$timestamp = date('Y-m-d H:i:s', time());
$unique_id = $POST['unique_id'];

if ($POST['status'] == '1') {
    $title = 'Minit Surat';
    $minute_to = $POST['receiver'];
    $status = '1';
} else {
    $title = 'Minit Surat Selesai';
    $minute_to = '';
    $status = '2';
}

// Execute the query and get the ID from the result set
$stmt->execute();


// Prepare the query for update
$query2 = "UPDATE gen_letters SET status = :status, receiver = :minute_to, updated_at = :updated WHERE unique_id = :unique_id ";
$stmt2 = $conn->prepare($query2);

// Bind the parameters
$stmt2->bindParam(':status', $status);
$stmt2->bindParam(':minute_to', $minute_to);
$stmt2->bindParam(':updated', $timestamp);
$stmt2->bindParam(':unique_id', $unique_id);

// Values
$timestamp = date('Y-m-d H:i:s', time());
$unique_id = $POST['unique_id'];

// Execute the query and get the ID from the result set
$stmt2->execute();

$message = "Minit Surat telah berjaya disimpan 🎉";

header('HTTP/2 200 OK');
echo json_encode([
    "message" => $message,
    "id" => $unique_id,
    "status" => 200,

]);

// Close the database connection
$conn = null;
