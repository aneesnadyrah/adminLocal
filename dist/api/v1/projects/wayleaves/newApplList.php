<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
require_once "api/functions.php";

// Connect to the database using PDO
$conn = Utilities::DBFactory();

$sql = "SELECT system_id AS \"SysID\",
            reference_no AS \"RefNo\",
            provider_id AS \"ProviderID\",
            provider_logo AS \"ProviderLogo\",
            provider_name AS \"Provider\",
            districts AS \"District\",
            application_length AS \"Length\",
            status_id AS \"StatusID\",
            status AS \"Status\",
            submit_date AS \"SubmitDate\"
        FROM view_operation_tasks
        WHERE status_id = 4";

// Prepare query on the database
$stmt = $conn->prepare($sql);

// create an array to hold the query result
$data = array();

// Execute the query
$stmt->execute();

while ($row = $stmt->fetch()) {
    // echo $row['name']."<br />\n";
    $data[] = $row;
}

// convert the result to a JSON string
$json = '{"data":' . json_encode($data) . '}';

// return the JSON string to the client
echo $json;

// Close the database connection
$conn = null;
