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
    $members = $POST['survey-team-edit'];

    if (is_array($members)) {
        // value is an array
        $membersId = '{' . implode(', ', $members) . '}';
        $totalTeamMembers = count($members);
    } else {
        // value is a single value
        $membersId = '{' .$members. '}';
        $totalTeamMembers = 1;
    }
    $submitted = date('Y-m-d H:i:s', time());

    // Update table flw_survey_team
    $query = $conn->prepare("UPDATE flw_survey_team SET total_survey_member = :totalTeamMembers, team_members = :membersId, updated_timestamp = :submitted WHERE id = :id");

    // Bind the parameters
    $query->bindParam(':totalTeamMembers', $totalTeamMembers);
    $query->bindParam(':membersId', $membersId);
    $query->bindParam(':submitted', $submitted);
    $query->bindParam(':id', $id);
    $query->execute();

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "success"   => "Success",
        "status" => 200,
    ]);

    // Close the database connection
    $conn = null;

} else {
    echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
}
