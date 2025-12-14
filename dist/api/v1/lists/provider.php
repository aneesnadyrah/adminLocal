<?php

// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "api/functions.php";
require "config/DBFactory.php";

// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();

// Prepare a SELECT query on the database
$stmt = $conn->prepare("SELECT id, name FROM ls_provider ORDER BY name ASC");

// Execute the query
$stmt->execute(); 

// create an array to hold the query result
$data = array();

while ($row = $stmt->fetch()) {
    $data[] = $row;
}

// convert the result to a JSON string
$json = json_encode($data);

// return the JSON string to the client
echo $json;

// Close the database connection
$conn = null;
?>