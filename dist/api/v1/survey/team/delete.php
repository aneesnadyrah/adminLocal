<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// };

require_once "config/system.php";
include_once "api/header.php";
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
        $updated = date('Y-m-d H:i:s', time());
        // var_dump($id);

        // Extract the numeric part from the row ID
        // $id = substr($id, strrpos($id, '-') + 1);
        // var_dump($id);

        // Sanitize the input to prevent SQL injection
        // $id = $conn->quote($id);
        // $id = intval($id);
        // var_dump($id);

        // Construct the DELETE query
        // $query = $conn->prepare("DELETE FROM flw_survey_team WHERE id = :id");
        $query = $conn->prepare("UPDATE flw_survey_team SET is_active = false, updated_timestamp = :updated WHERE id = :id");
        // Bind the parameters
        // $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->bindParam(':id', $id);
        $query->bindParam(':updated', $updated);
        $query->execute();
        // var_dump($query->execute());
        // Construct the JSON response
        $response = array('success' => true);

        // Send the JSON response to the client
        header('Content-Type: application/json');
        echo json_encode($response);

        // Close the database connection
        $conn = null;

    }
} else {
    echo "NOT AUTHORIZED!";
}