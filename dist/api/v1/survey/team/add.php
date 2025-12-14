<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "api/functions.php";
include_once "api/header.php";
require_once "config/DBFactory.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

$db = new DBConnectionFactory();
$conn = $db->createConnection();
$system = new System;

// Check if the URL contains a parameter named
if (isset($POST['survey-team-name'])) {
    $team = $POST['survey-team-name'];
    $members = $POST['survey-add-team'];
    $state = $system->App->state;
    $is_active = true;

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

    // Prepare the SQL statement
    $query = $conn->prepare("INSERT INTO flw_survey_team (survey_team, total_survey_member, team_members, created_timestamp, state, is_active) VALUES (:team, :totalTeamMembers, :membersId, :submitted, :state, :is_active)");

    // Bind the parameters
    $query->bindParam(':team', $team);
    $query->bindParam(':totalTeamMembers', $totalTeamMembers);
    $query->bindParam(':membersId', $membersId);
    $query->bindParam(':submitted', $submitted);
    $query->bindParam(':state', $state);
    $query->bindParam(':is_active', $is_active);
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