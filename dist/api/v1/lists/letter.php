<?php

require "api/header.php";
require "config/system.php";
include_once "api/functions.php";
require "config/DBFactory.php";

// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();

// Execute a SELECT query on the database
$stmt = $conn->prepare("SELECT id, details, type FROM ls_letters WHERE type='1' ORDER BY details ASC");

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
