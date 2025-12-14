<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "api/header.php";
require_once "config/system.php";
require_once "api/functions.php";
require_once "config/DBFactory.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

$db = new DBConnectionFactory();
$conn = $db->createConnection();

// Check if the URL contains a parameter named
if (isset($POST['row-id'])) {
    $id = $POST['row-id'];
    $zoneId = $POST['zone-id'];
    $districts = $POST['ppkd-edit-zone'];
    $members = $POST['ppkd-team-edit'];

    // Convert array to a string representation
    $districtsString = '{' . implode(',', $districts) . '}';


    // Assuming $id is the value you want to use in the condition
    $updateQuery = $conn->prepare("UPDATE ls_user_zones SET districts = :districts WHERE id = :id");

    // Bind the parameters
    $updateQuery->bindParam(':districts', $districtsString, PDO::PARAM_STR); // Use PDO::PARAM_STR to bind as a string
    $updateQuery->bindParam(':id', $id);
    $updateQuery->execute();

    // Update table sys_user_assignments to set zone_id as null
    $nullUpdateQuery = $conn->prepare("UPDATE sys_user_assignments SET zone_id = NULL WHERE zone_id = :zoneId");
    $nullUpdateQuery->bindParam(':zoneId', $zoneId, PDO::PARAM_INT);
    $nullUpdateQuery->execute();

    // Now, update with the new members
    $updateQuery = $conn->prepare("UPDATE sys_user_assignments SET zone_id = :zoneId WHERE username = :username");
    $updateQuery->bindParam(':zoneId', $zoneId);

    // Check if $members is an array
    if (!is_array($members)) {
        // If it's not an array, convert it to an array with a single element
        $members = array($members);
    }

    foreach ($members as $username) {
        $updateQuery->bindParam(':username', $username, PDO::PARAM_STR);
        $updateQuery->execute();
    }

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "success" => "Success",
        "status" => 200,
    ]);

    // Close the database connection
    $conn = null;

} else {
    echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
}
?>