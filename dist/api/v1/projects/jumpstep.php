<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "api/header.php";
require_once "config/system.php";
require_once "api/functions.php";
require_once "config/DBFactory.php";

// Connect to the database using PDO
$db = new DBConnectionFactory();
$chronology = new Chronology();
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$System   = new System;
$tenant   = $System->App->title;
$conn = $db->createConnection();

$telegram = new Telegram();
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

$systemId = $POST['system-id'];
$notes = $POST['notes']; 
$department = $POST['department'];
$jump_status = $POST['jump_status'];
$current_status = $POST['current-status'];
$timestamp = date('Y-m-d H:i:s', time());

$timeConverted = Utilities::convertDateToMalay($timestamp);

// NOTE - Update Task Assignment
$taskAssignment->complete($username, $systemId, $current_status);
if ($jump_status == 162) {
    // Do not create a new task assignment
} else {
    $taskAssignment->create($systemId, $jump_status);
}

$stmt = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
$stmt->bindParam(':systemId', $systemId);
$stmt->execute();
// $result = $stmt->fetch(PDO::FETCH_ASSOC);
$referenceNo =  $stmt->fetchColumn();

if($referenceNo > 0) {
    $refNo = $referenceNo;
} else {
    $refNo = $systemId;
}

$stmtS = $conn->prepare('SELECT flow_name FROM ls_statuses WHERE id = :current_status');
$stmtS->bindParam(':current_status', $current_status);
$stmtS->execute();
$statusCurrent =  $stmtS->fetchColumn();

$stmtN = $conn->prepare('SELECT flow_name FROM ls_statuses WHERE id = :jump_status');
$stmtN->bindParam(':jump_status', $jump_status);
$stmtN->execute();
$statusNew =  $stmtN->fetchColumn();

//Record Activity
$text = 'Pengguna ' . $username . ' telah membuat langkau status permohonan dari status semasa <b>'.$statusCurrent.'</b> ke status baru <b>'.$statusNew.'</b> bagi No Permohonan ' . $refNo;
$pages = 'task_operation';
$changelog->userActivity($text, $pages);

$NextStatus = $flow->goToNextFlow($systemId, $department, "BF", Steps: $jump_status, allRole: true);

$details = 'Langkau status dari status semasa '.$statusCurrent.' ke status baru '.$statusNew.' telah dilakukan bagi permohonan ' . $refNo . '. Nota: ' . $notes;
// NOTE - Insert Project Changelog
if ($changelog->projectActivity($systemId, $jump_status, $details)) {
    $telegramMsg = "Langkau status dari status semasa <b>".$statusCurrent."</b> ke status baru <b>".$statusNew."</b> telah dilakukan bagi permohonan ini. \n\n<strong>👩‍💻No Permohonan : " . $refNo . " \n📆 Tarikh Langkau Status : " . $timeConverted . "</strong>";    // NOTE - send telegram notification for OM
    // $telegramResponse = $telegram->sendMessage('role', 18, $telegramMsg, 'html');
    $telegramResponse = $telegram->SendMessage('group', 'management', $telegramMsg, 'html');
    $telegramResponse = $telegram->SendMessage('department', $department, $telegramMsg, 'html');

if($department == "mapping"){
        $telegramResponse = $telegram->SendMessage('role', 28, $telegramMsg, 'html');
    }
};

$chronoId = $chronology->create($username, $systemId, $current_status, 'notes');

$query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':systemId', $systemId);
$stmt->bindParam(':details', $details);
$stmt->bindParam(':created', $timestamp);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':chronology_id', $chronoId);
$stmt->execute();

$message = 'Langkau Status Berjaya 🎉';

http_response_code(200);
echo json_encode(
    array(
        "systemId" => $systemId,
        "message" => $message,
    )
);

$conn = null;