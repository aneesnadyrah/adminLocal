<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
require_once "api/functions.php";

// Connect to the database using PDO
$conn = Utilities::DBFactory();

$systemId = isset($_GET['sid']) ? $_GET['sid'] : '';

// Prepare query on the database
$stmt = $conn->prepare("SELECT view_authorities.reference_no AS \"RefNo\", view_authorities.authority_name AS \"Authority\", view_authorities.authority_id AS \"AuthorityID\", view_authorities.authority_logo AS \"Logo\", view_authorities.wl_appl_letter_date AS \"LetterDate\", view_authorities.involved_appl_length AS \"Length\", flw_wayleave.status AS \"WyStatus\", flw_wayleave.id AS \"ID\", flw_wayleave.system_id AS \"SysID\", flw_wayleave.letter_wy_id AS \"LtrWyID\", status_appl.project_status AS \"Status\", status_appl.id AS \"StatusID\", view_authorities.pil_submitted_by AS \"PILby\", view_authorities.wl_wy_recv_date AS \"ReceiveDate\"
    FROM view_authorities
    LEFT JOIN flw_wayleave ON view_authorities.wl_id = flw_wayleave.id
    LEFT JOIN status_appl ON view_authorities.system_id = status_appl.system_id
    WHERE view_authorities.system_id = :systemId ");

// bind the parameter to the placeholder using the bindValue method
$stmt->bindValue(':systemId', $systemId);

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
