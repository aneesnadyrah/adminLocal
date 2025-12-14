<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "config/functions.php";
require_once "api/functions.php";
require "config/DBFactory.php";
include "config/autoload.php";
$System = new System;
$tenant = $System->App->tenant;

$json_data = file_get_contents("php://input");
// Check if JSON data was retrieved successfully
if ($json_data === false) {
    echo 'Error retrieving JSON data';
    exit;
}

$data = json_decode($json_data, true);
// Check if JSON data was parsed successfully
if ($data === null) {
    echo 'Error parsing JSON data';
    exit;
}

// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();

$username = $_SESSION['username'];
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);

$taskAssignment = new TaskAssignments();

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if ($data["route"] == "approve") {

        $systemId = $_GET['sid'];
        $created = date('Y-m-d H:i:s', time());
        $notes = $data['notes'];
        $sitevisit = isset($data['sitevisit']) ? $data['sitevisit'] : 0;

        if($sitevisit == 1) {
            $status = 18; //have site visit
        } else {
            $status = 21; // no site visit
        }

        //TODO: change to new function
        $stmt = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES
        (:systemId, :notes, :created)");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':created', $created);
        $stmt->execute();

        // Get the ID of the inserted row
        $notesId = $conn->lastInsertId();

        // Update Status Application // (17 Revised Technical Proposal Plan Review -> 21 Revised Technical Proposal Plan Approval)
        $indexFlow = $flow->goToNextFlow($systemId, 'operation', 'BF');
        $assignment = $taskAssignment->set($roleId, $systemId, $indexFlow);

        // Record Changelog
        $detail = 'Pindaan Permohonan telah Disemak dan Disahkan';

        $changelog->projectActivity($systemId, $indexFlow, $detail);

        // Record Activity
        $text = 'Pengguna ' . $username . ' telah semak dan mengesahkan Pindaan Permohonan';
        //NOTE : check $page
        $page = 'site report review';

        //No telegram message
        $changelog->userActivity($text, $pages);

        $message = "Pengesahan berjaya dihantar 🎉";

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => $message,
        ]);
    }
    if ($data["route"] == "amend") {

        $systemId = $_GET['sid'];
        $created = date('Y-m-d H:i:s', time());
        $notes = $data['notes'];

        // Insert into tbl notes

        //TODO: change to new function
        $stmt = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES
        (:systemId, :notes, :created)");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':created', $created);
        $stmt->execute();

        // Get the ID of the inserted row
        $notesId = $conn->lastInsertId();

        // Update Status Application // (17 Revised Technical Proposal Plan Review -> 16 Revised Technical Proposal Plan)
        $indexFlow = $flow->goToPrevFlow($systemId, 'operation', 'BF');
        $assignment = $taskAssignment->set($roleId, $systemId, $indexFlow);

        // Record Changelog
        $detail = 'Pindaan Permohonan telah Disemak dan Perlu Dipinda Semula';

        $changelog->projectActivity($systemId, $indexFlow, $detail);

        // Record Activity
        $text = 'Pengguna ' . $username . ' telah semak dan mengesahkan Pindaan Permohonan Perlu Dipinda Semula';
        //NOTE : check $page
        $page = 'site report review';

        //No telegram message
        $changelog->userActivity($text, $pages);

        // // Update changelog if status update successful
        // if ($statusUpdateResult['result'] == true) {
        //     insertSysRecordChangelog($conn, $systemId, 'Pindaan Permohonan telah Disemak dan Perlu Dipinda Semula', $statusUpdateResult['new_status'],notes_id :$notesId);
        // }

        $message = "Pengesahan berjaya dihantar 🎉";

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => $message,
        ]);
    }

}