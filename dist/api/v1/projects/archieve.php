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
$timestamp = date('Y-m-d H:i:s', time());

$timeConverted = Utilities::convertDateToMalay($timestamp);

$flwStatus = 151;
// NOTE - Update Task Assignment
$taskAssignment->create($systemId, $flwStatus);

// // Construct the SQL query to retrieve the submission code
// $sql = "SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId";

// // Prepare and execute the query
// $stmt = $conn->prepare($sql);
// $stmt->execute([
//     ':systemId' => $systemId
// ]);

// // Fetch the submission code
// $referenceNo = $stmt->fetchColumn();

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

//Record Activity
$text = 'Pengguna ' . $username . ' telah membuat pembatalan permohonan bagi No Permohonan ' . $refNo;
$pages = 'task_operation';
$changelog->userActivity($text, $pages);

$stmtC = $conn->prepare('SELECT * FROM ctrl_statuses WHERE system_id = :systemId');
$stmtC->bindParam(':systemId', $systemId);
$stmtC->execute();
$result = $stmtC->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    $updCtrlStat = "UPDATE ctrl_statuses SET cancellation_status = :cancel_status WHERE id = :id AND system_id = :systemId AND department = :department ";
    $data = $conn->prepare($updCtrlStat);

    $data->bindParam(':id', $row['id'] );
    $data->bindParam(':cancel_status', $row['status_id'] );
    $data->bindParam(':department', $row['department'] );
    $data->bindParam(':systemId', $systemId);
    
    $data->execute();

    $NextStatus = $flow->goToNextFlow($systemId, $row['department'], "BF", Steps: $flwStatus, allRole: true);
}

// Prepare the query for update flw_appl_verifies
$updEntries = "UPDATE flw_appl_entries SET cancellation_notes = :notes WHERE system_id = :systemId ";
$data = $conn->prepare($updEntries);

$data->bindParam(':notes', $notes);
$data->bindParam(':systemId', $systemId);
$data->execute();

$details = 'Pembatalan Permohonan Telah Dilakukan bagi Permohonan ' . $refNo . '. Nota: ' . $notes;
// NOTE - Insert Project Changelog
if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
    $telegramMsg = "Pembatalan Permohonan telah dilakukan bagi permohonan ini. \n\n<strong>👩‍💻No Permohonan :  " . $refNo . " \n📆 Tarikh Batal Permohonan : " . $timeConverted . "</strong> Sila membuat pengesahan pembatalan ini.";
    // NOTE - send telegram notification for OM
    $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
    $telegramResponse = $telegram->sendMessage('role', 18, $telegramMsg, 'html');

};

$chronoId = $chronology->create($username, $systemId, $flwStatus, 'notes');

$query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':systemId', $systemId);
$stmt->bindParam(':details', $details);
$stmt->bindParam(':created', $timestamp);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':chronology_id', $chronoId);
$stmt->execute();

$message = 'Pembatalan Permohonan Berjaya 🎉';

http_response_code(200);
echo json_encode(
    array(
        "systemId" => $systemId,
        "message" => $message,
    )
);

$conn = null;