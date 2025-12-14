<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "api/header.php";
require_once "api/functions.php";
require_once "config/DBFactory.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);


// Get the username and password from the POST request
// $username = $POST['username'];
// $password = $POST['password'];
// $systemId = $POST['systemId'];

// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();
$system = new System;
$tenant = $system->App->title;

$user = $_SESSION['username'];
$submitted = date('Y-m-d H:i:s', time());
$systemId = $_GET['sid'];
$currentDate = date('Y-m-d');


$query = "SELECT sharing_code FROM flw_appl_plan WHERE system_id = :systemId";
$stmt = $conn->prepare($query);
$stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$uuid = $row['sharing_code'];

$qrCodeData = $system->App->url."/sharing/$uuid";

// Return a success message in the API response
http_response_code(200);
echo json_encode([
    "message" => "success",
    "status" => 200,
    "url" => $qrCodeData,
]);


// Close the database connection
$conn = null;

