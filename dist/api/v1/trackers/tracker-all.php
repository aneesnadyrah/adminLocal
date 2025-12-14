<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
require_once "api/functions.php";

// Connect to the database using PDO
$conn = Utilities::DBFactory();

$systemId = isset($_GET['sid']) ? $_GET['sid'] : '';

//FIXME : check data needed
// Prepare query on the database
$stmt = $conn->prepare("SELECT * FROM view_data_entry ORDER BY id ASC");

// Execute the query
$stmt->execute();

// Fetch all rows from the result set as an associative array
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create a JSON object with the "data" key and encode the data array
$json = json_encode(["data" => $data]);

// Return the JSON string to the client
echo $json;

// Close the database connection
$conn = null;
