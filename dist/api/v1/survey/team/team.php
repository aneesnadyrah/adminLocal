<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
include_once "api/header.php";
include_once "api/functions.php";
require "config/DBFactory.php";

// Connect to the database
$db = new DBConnectionFactory();
$conn = $db->createConnection();

// Prepare a SELECT query on the database
$stmt = $conn->prepare("SELECT * FROM flw_survey_team WHERE is_active = true");

// Execute the query
$stmt->execute(); 

// create an array to hold the query result
$data = array();

// fetch the rows from the query result as an associative array
while ($row = $stmt->fetch()) {
    $data[] = $row;
}

// convert the result to a JSON string
$json = json_encode($data);

// return the JSON string to the client
echo $json;

// Close the database connection
$conn = null;