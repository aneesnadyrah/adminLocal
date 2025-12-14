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

$uniqueId = generateLetterId();

// Prepare the query
$query = "INSERT INTO gen_letters (title, type, method, sender, receiver, status, no_ref_letter, no_ref_tuan, document, notes, date_receive, created_at, created_by, unique_id ) VALUES(:title, :type, :method, :sender, :receiver, :status, :no_ref_letter, :no_ref_tuan, :document, :notes, :date_receive, :created, :created_by, :unique_id) RETURNING id";
$stmt = $conn->prepare($query);

// Bind the parameters
$stmt->bindParam(':title', $POST['title']);
$stmt->bindParam(':type', $POST['type']);
$stmt->bindParam(':method', $POST['method']);
$stmt->bindParam(':sender', $POST['sender']);
$stmt->bindParam(':receiver', $POST['receiver']);
$stmt->bindParam(':status', $stat);
$stmt->bindParam(':no_ref_letter', $POST['no_ref_letter']);
$stmt->bindParam(':no_ref_tuan', $POST['no_ref_tuan']);
$stmt->bindParam(':document', $doc);
$stmt->bindParam(':notes', $POST['notes']);
$stmt->bindParam(':date_receive', $POST['date_receive']);
$stmt->bindParam(':created', $timestamp);
$stmt->bindParam(':created_by', $_SESSION['username']);
$stmt->bindParam(':unique_id', $uniqueId);

// Values
$stat = '0';
$doc = '';
$timestamp = date('Y-m-d H:i:s', time());

// Execute the query and get the ID from the result set
$stmt->execute();
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
// $letter_id = $row['id'];


// Prepare the query
$query2 = "INSERT INTO gen_letter_cronologies (title, notes, created_at, created_by, letter_id, status, minute_to) VALUES(:title_cronology, :notes, :created, :created_by, :letter_id, :status, :receiver)";
$stmt2 = $conn->prepare($query2);

// Bind the parameters
$stmt2->bindParam(':title_cronology', $title_cronology);
$stmt2->bindParam(':notes', $POST['notes']);
$stmt2->bindParam(':created', $timestamp);
$stmt2->bindParam(':created_by', $_SESSION['username']);
$stmt2->bindParam(':letter_id', $uniqueId);
$stmt2->bindParam(':status', $stat);
$stmt2->bindParam(':receiver', $POST['receiver']);

// Values
$title_cronology = 'Maklumat Surat Diisi dalam Sistem';

// Execute the query and get the ID from the result set
$stmt2->execute();

$type_id = $POST['type'];
// select a particular column
$stmt3 = $conn->prepare("SELECT id,code_name FROM ls_letters WHERE id = '$type_id' ");
$stmt3->execute(); 
$row3 = $stmt3->fetch(PDO::FETCH_ASSOC);

$message = "Rekod Surat telah berjaya disimpan 🎉";

// Finally, return a JSON
header('HTTP/2 200 OK');
echo json_encode([
    "message" => $message,
    "status" => 200,
    "id" => $uniqueId,
    "code_name" => $row3['code_name'],
    "type" => $type_id

]);

// Close the database connection
$conn = null;