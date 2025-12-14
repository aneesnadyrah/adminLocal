<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// };

require_once "api/header.php";
require_once "config/system.php";
require_once "api/functions.php";
require_once "config/DBFactory.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

$db = new DBConnectionFactory();
$conn = $db->createConnection();
//  var_dump($_GET['row_id']);

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    // Check if the row ID is provided
    if (isset($_GET['rowId'])) {

        // Retrieve the row ID from the AJAX request
        $id = $_GET['rowId'];

        // Construct the DELETE query
        $query = $conn->prepare("DELETE FROM ls_user_zones WHERE id = :id");
        // Bind the parameters
        $query->bindParam(':id', $id);
        $query->execute();

        // Update sys_user_assignments for zone_id = null
        $updateQuery = $conn->prepare("UPDATE sys_user_assignments SET zone_id = null WHERE zone_id = :id");
        $updateQuery->bindParam(':id', $id);
        $updateQuery->execute();

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "success" => "Success",
            "status" => 200,
        ]);

        // Close the database connection
        $conn = null;

    }
} else {
    echo "NOT AUTHORIZED!";
}
