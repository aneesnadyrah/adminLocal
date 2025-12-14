<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "api/header.php";
require_once "config/system.php";
include_once "api/functions.php";
require_once "config/DBFactory.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

// Connect to the database using PDO
$db = new DBConnectionFactory();
$telegram = new Telegram();
$system = new System;
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$Curl = new Curl();
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();

$username = $_SESSION['username'];

$conn = $db->createConnection();
$tenant = $system->App->tenant;

if ((isset($POST['systemId']))) {

    //RCPP
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    //get ref no
    $refNo = Utilities::getRefNo($systemId);

    $currentStatus = 35;
    $taskAssignment->complete($username, $systemId, $currentStatus);

    $flwStatus = 36;
    $taskAssignment->create($systemId, $flwStatus);

    $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Memajukan Tindakan ke Muat Naik Invois Perkhidmatan seterusnya bagi permohonan ' . $refNo;
    $pages = 'task_finance';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 35
    $details = 'Tindakan Muat Naik Invois Perkhidmatan seterusnya telah Dimajukan bagi permohonan ' . $refNo;

    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $currentStatus, $details)) {

        // declare telegram notification string
        $telegramMsg = "Tindakan Muat Naik Invois Perkhidmatan seterusnya telah Dimajukan. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
        // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

        // NOTE - send telegram notification for team account
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
    }
    ;

    $chronoId = $chronology->create($username, $systemId, $currentStatus, 'note');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $message = 'Memajukan Tindakan berjaya! 🎉';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "created" => $timestamp,
            "user" => $username,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else {
    echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
}


