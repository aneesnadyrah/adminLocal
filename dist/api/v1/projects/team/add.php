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
if (isset($POST['ppkd-team-name'])) {
    $name = $POST['ppkd-team-name'];
    $districts = $POST['ppkd-add-zone'];
    $state = $POST['state'];
    $members = $POST['ppkd-add-team'];

    // Ensure $districts is an array
    if (!is_array($districts)) {
        $districtsString = '{' . $districts . '}';
    } else {
        // Convert array to a string representation
        $districtsString = '{' . implode(',', $districts) . '}';
    }

    // Prepare the SQL statement
    $query = $conn->prepare("INSERT INTO ls_user_zones (name, districts, state) VALUES (:name, :districts, :state)");

    // Bind the parameters
    $query->bindParam(':name', $name);
    // $query->bindParam(':districts', $districts);
    $query->bindParam(':districts', $districtsString, PDO::PARAM_STR); // Use PDO::PARAM_STR to bind as a string
    $query->bindParam(':state', $state);
    $query->execute();

    // Get the last inserted ID
    $zone_id = $conn->lastInsertId();

    // Now, update with the new members
    $updateQuery = $conn->prepare("UPDATE sys_user_assignments SET zone_id = :zoneId WHERE username = :username");
    $updateQuery->bindParam(':zoneId', $zone_id);

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
