<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
};

require "api/header.php";
require "config/system.php";
include "api/functions.php";
require "config/DBFactory.php";

// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();

$systemId = isset($_GET['sid']) ? $_GET['sid'] : null;
// var_dump($systemId);

if ($systemId !== null) {
    // Prepare query on the database
    $stmt = $conn->prepare("SELECT * FROM view_authorities WHERE system_id = :systemId ORDER BY id ASC");
    $stmt->bindParam(':systemId', $systemId);
} else {
    $stmt = $conn->prepare("SELECT * FROM view_authorities ORDER BY id ASC");
}
// Execute the query
$stmt->execute();

// Fetch all rows from the result set as an associative array
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as &$row) {
    // Format the StatusID field to have leading zeroes if it's less than 100
    if ($row['status'] < 10) {
        $row['status'] = '00' . $row['status'];
    } else if ($row['status'] < 100) {
        $row['status'] = '0' . $row['status'];
    }

    if ($row['authority_status'] < 10) {
        $row['authority_status'] = '00' . $row['authority_status'];
    } else if ($row['authority_status'] < 100) {
        $row['authority_status'] = '0' . $row['authority_status'];
    }

}

// Create a JSON object with the "data" key and encode the data array
$json = json_encode(["data" => $data]);

// Return the JSON string to the client
echo $json;

// Close the database connection
$conn = null;
