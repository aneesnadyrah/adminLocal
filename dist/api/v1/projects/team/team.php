<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "api/functions.php";
require "config/DBFactory.php";

// Connect to the database
$db = new DBConnectionFactory();
$conn = $db->createConnection();

// Prepare a SELECT query on the database
$stmt = $conn->prepare("SELECT
                            uz.*,
                            ua.zone_id,
                            ARRAY_AGG(ua.username) AS username,
                            (SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text)
                            FROM sys_upi
                            WHERE sys_upi.state_code::text = uz.state::text
                            AND (sys_upi.district_code::text = ANY (uz.districts::text[]))) AS districts
                        FROM
                            ls_user_zones AS uz
                        LEFT JOIN
                            sys_user_assignments AS ua ON uz.id = ua.zone_id
                        GROUP BY
                            uz.id, uz.name, uz.state, ua.zone_id;              
                        ");

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
