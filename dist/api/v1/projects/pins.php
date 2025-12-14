<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require "api/header.php";
require "config/system.php";
include_once "api/functions.php";
require_once "config/DBFactory.php";

$db = new DBConnectionFactory();
$conn = $db->createConnection();

// retrieve the raw HTTP request body
$data = file_get_contents('php://input');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($username)) {

        // Execute a SELECT query on the database
        $query = "SELECT
        flw_appl_entries.reference_no AS \"referenceNo\",
        flw_appl_entries.utility_provider AS \"providerID\",
        flw_appl_entries.system_id AS \"systemID\",
        (
            SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ',') AS array_to_string
            FROM sys_upi
            WHERE sys_upi.state_code = flw_appl_entries.state
              AND (sys_upi.district_code = ANY (flw_appl_entries.districts))
            GROUP BY sys_upi.state_code
        ) AS \"District\",
        sys_pin_project.user_pinned AS \"username\"
        FROM sys_pin_project
        LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = sys_pin_project.system_id
        WHERE user_pinned = :username
        GROUP BY
        flw_appl_entries.reference_no,
        flw_appl_entries.utility_provider,
        flw_appl_entries.system_id,
        flw_appl_entries.state,
        flw_appl_entries.districts,
        sys_pin_project.user_pinned";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Fetch the rows from the query result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert the result to a JSON string
        header('HTTP/2 200 OK');
        echo json_encode(array( 'data' => $data));

        $conn = null;

    } else if (isset($_GET['systemID'])) {

        $created = date('Y-m-d H:i:s', time());
        $systemID = isset($_GET['systemID']) ? $_GET['systemID'] : '';

        // Execute an INSERT INTO query on the database
        $query = "INSERT INTO sys_pin_project (system_id,user_pinned,pinned_at) VALUES (:systemID,:username,:created)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':created', $created);
        $stmt->execute();

        // Execute a SELECT query on the database
        $query = "SELECT
            flw_appl_entries.reference_no AS \"referenceNo\",
            flw_appl_entries.utility_provider AS \"providerID\",
            flw_appl_entries.system_id AS \"systemID\",
            ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
           FROM sys_upi
          WHERE sys_upi.state_code::text = flw_appl_entries.state::text AND (sys_upi.district_code::text = ANY (flw_appl_entries.districts::text[]))) as \"District\",
            sys_pin_project.user_pinned AS \"username\"
            FROM sys_pin_project
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = sys_pin_project.system_id
            WHERE user_pinned = :username
            GROUP BY sys_pin_project.id, flw_appl_entries.reference_no, flw_appl_entries.utility_provider, flw_appl_entries.system_id, sys_pin_project.user_pinned
            ORDER BY sys_pin_project.id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Fetch the rows from the query result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the JSON string to the client
        header('HTTP/2 200 OK');
        echo json_encode(
            array(
                "message" => "Success",
                "data" => $data
            )
        );

        $conn = null;
    }


} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $POST = json_decode($data);

    // Check if the URL contains a parameter named
    if (isset($_SESSION['username']) && isset($POST->systemID)) {

        $created = date('Y-m-d H:i:s', time());
        $systemID = isset($POST->systemID) ? $POST->systemID : '';

        // Check if the user has already pinned the system
        $checkPinQuery = "SELECT COUNT(id) AS pin FROM sys_pin_project WHERE system_id = :systemID AND user_pinned = :username";
        $checkPinStmt = $conn->prepare($checkPinQuery);
        $checkPinStmt->bindParam(':systemID', $systemID);
        $checkPinStmt->bindParam(':username', $username);
        $checkPinStmt->execute();

        $row = $checkPinStmt->fetch(PDO::FETCH_ASSOC);
        if ($row['pin'] == 0) {
            // Insert the pin into sys_pin_project table
            $insertPinQuery = "INSERT INTO sys_pin_project (system_id, user_pinned, pinned_at) VALUES (:systemID, :username, :created)";
            $insertPinStmt = $conn->prepare($insertPinQuery);
            $insertPinStmt->bindParam(':systemID', $systemID);
            $insertPinStmt->bindParam(':username', $username);
            $insertPinStmt->bindParam(':created', $created);
            $insertPinStmt->execute();

            // Retrieve the pinned information
            $retrieveQuery = "SELECT
            flw_appl_entries.reference_no AS \"referenceNo\",
            flw_appl_entries.utility_provider AS \"providerID\",
            flw_appl_entries.system_id AS \"systemID\",
            ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
           FROM sys_upi
          WHERE sys_upi.state_code::text = flw_appl_entries.state::text AND (sys_upi.district_code::text = ANY (flw_appl_entries.districts::text[]))) as \"District\",
            sys_pin_project.user_pinned AS \"username\"
            FROM sys_pin_project
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = sys_pin_project.system_id
            WHERE user_pinned = :username
            GROUP BY sys_pin_project.id, flw_appl_entries.reference_no, flw_appl_entries.utility_provider, flw_appl_entries.system_id, sys_pin_project.user_pinned
            ORDER BY sys_pin_project.id DESC LIMIT 1";
            $retrieveStmt = $conn->prepare($retrieveQuery);
            $retrieveStmt->bindParam(':username', $username);
            $retrieveStmt->execute();

            // Fetch the rows from the query result as an associative array
            $data = $retrieveStmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the JSON response to the client
            header('HTTP/2 200 OK');
            echo json_encode(
                array(
                    "message" => "success",
                    "data" => $data
                )
            );

        } else {
            // Return the JSON response indicating error
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(
                array(
                    "message" => "projek ini telah dipinkan",
                )
            );
        }
        $conn = null;
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

}

?>