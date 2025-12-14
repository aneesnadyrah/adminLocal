<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/header.php";
require_once "api/functions.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

// Connect to the database using PDO
$db = new DBConnectionFactory();
$telegram = new Telegram();
$system = new System;
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$Curl = new Curl();
$traffic = new Traffic();
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();

$phase = $POST['phase'];

$username = $_SESSION['username'];

$conn = $db->createConnection();
$tenant = $system->App->tenant;
$state = $system->App->state;

if ($phase == 'permit'){
    // NSK
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset($POST['notes']) ? $POST['notes'] : '';

    //get ref no
    $refNo = Utilities::getRefNo($systemId);

    $flwStatus = 88;
    $status = Utilities::getCurrentStatus($systemId, 'operation');

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Mengesahkan penetapan tugasan Muat Naik Notis Siap Kerja bagi permohonan ' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus, allRole: true);
    $taskAssignment->create($systemId, $flwStatus);

    $details = 'Tugasan untuk Muat Naik Notis Siap Kerja telah dicipta bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
    // Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {

        // declare telegram notification string
        $telegramMsg = "Kerja Pengorekan telah disahkan selesai. Sila muat naik Notis Siap Kerja. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";

        // NOTE - send telegram notification for team account
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'notes');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    

    if ($stmt->execute()){
        $message = 'Tugasan untuk Muat Naik Notis Siap Kerja telah berjaya dicipta!🎉';
    
        http_response_code(200);
        echo json_encode(
            array(
                "systemId" => $systemId,
                "user" => $username,
                "notes" => $notes,
                "message" => $message,
                "status" => 200,
            )
        );
    }

    $conn = null;
} else if ($phase == 'liability'){
    //NMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset($POST['notes']) ? $POST['notes'] : '';

    //get ref no
    $refNo = Utilities::getRefNo($systemId);

    //get authority name
    $authName = Utilities::getAuthorityName($authorityId);

    $flwStatus = 134;
    $status = Utilities::getCurrentStatus($systemId, 'operation', $authorityId);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Mengesahkan penetapan tugasan Muat Naik Surat Permohonan Sijil Siap Membaiki Kecatatan bagi permohonan ' . $refNo. ' untuk Pihak Berkuasa ' . $authName;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus, allRole: true);
    $taskAssignment->create($systemId, $flwStatus, $authorityId);
    $taskAssignment->create($systemId, $flwStatus, 0);

    $details = 'Tugasan untuk Muat Naik Surat Permohonan Sijil Siap Membaiki Kecatatan telah dicipta bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
    // Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

        // declare telegram notification string
        $telegramMsg = "Permohonan ini telah disahkan berlaku kecacatan. Sila muat naik Surat Permohonan Sijil Siap Membaiki Kecatatan. \n\n<strong>🔗 No Rujukan : " . $refNo . "\n 📋Pihak Berkuasa : ". $authName. "</strong>";

        // NOTE - send telegram notification for team account
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'notes', $authorityId);

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':authorityId', $authorityId);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    

    if ($stmt->execute()){
        $message = 'Tugasan untuk Muat Naik Surat Permohonan Sijil Siap Membaiki Kecatatan telah berjaya dicipta!🎉';
    
        http_response_code(200);
        echo json_encode(
            array(
                "systemId" => $systemId,
                "user" => $username,
                "notes" => $notes,
                "message" => $message,
                "status" => 200,
            )
        );
    }

    $conn = null;
}