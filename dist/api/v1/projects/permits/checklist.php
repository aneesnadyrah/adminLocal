
<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "config/functions.php";
require_once "api/functions.php";
require "config/DBFactory.php";
include "config/autoload.php";

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

$taskAssignment = new TaskAssignments();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($data['complete_permit']))  {

        $created          = date('Y-m-d H:i:s', time());
        $systemId         = $data['system-id'];
        $notes            = $data['notes'];
        $completePermit   = isset($data['complete_permit']) ? $data['complete_permit'] : 0;
        $username         = isset($_SESSION['username']) ? $_SESSION['username'] : '';

        $flow = new FlowStatuses($username);
        $changelog = new Changelog($username);

        //get Ref No
        $stmtRef = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
        $stmtRef->bindParam(':systemId', $systemId);
        $stmtRef->execute();
        $resultRef = $stmtRef->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        if($completePermit == 1) {
            $catatan = "Dokumen Permohonan Permit Lengkap. No Rujukan: ".$refNo." \nNota: ".$notes." ";

            //TODO: change to new function
            // insert to flw_appl_notes
            $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
            $stmtNote->bindParam(':systemId', $systemId);
            $stmtNote->bindParam(':notes', $notes);
            $stmtNote->bindParam(':created', $created);
            $stmtNote->execute();

            // Get the ID of the inserted row
            $notesId = $conn->lastInsertId();

            //NOTE : change into new function  
            // Update Status Application // (82 Work Permit Approval Application -> 83 Work Permit Approval)
            $indexFlow = $flow->goToNextFlow($systemId, 'operation', 'BF');
            $assignment = $taskAssignment->set($roleId, $systemId, $indexFlow);

            // Record Changelog
            $detail = 'Dokumen Permit Lengkap';

            $changelog->projectActivity($systemId, $indexFlow, $detail);

            // Record Activity
            $text = 'Pengguna ' . $username . ' telah mengesahkan Dokumen Permit lengkap';
            $page = 'checklist';

            if ($changelog->userActivity($text, $pages)) {
                // declare telegram notification string
                $telegramMsg = "Dokumen Permohonan Permit Lengkap. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>\nCatatan : " . $notes;
                // NOTE : get send to role 21|22|23 - account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');

            }

            $message = 'Dokumen Permit Lengkap 🎉';

            // Finally, return a JSON
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => $message,
            ]);
            // Close the database connection
            $conn = null;
        } else {
            //TODO: change to new function
            // insert to flw_appl_notes
            $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
            $stmtNote->bindParam(':systemId', $systemId);
            $stmtNote->bindParam(':notes', $notes);
            $stmtNote->bindParam(':created', $created);
            $stmtNote->execute();


            //TODO : change into new function
            // Update Status Application // (82 Work Permit Approval Application -> 81 Preparing Work Permit Documents)
            $indexFlow = $flow->goToPrevFlow($systemId, 'operation', 'BF');
            $assignment = $taskAssignment->set($roleId, $systemId, $indexFlow);

            // Record Changelog
            $detail = 'Dokumen Permit Tidak Lengkap';

            $changelog->projectActivity($systemId, $indexFlow, $detail);

            // Record Activity
            $text = 'Pengguna ' . $username . ' telah mengesahkan Dokumen Permit tidak lengkap';
            $page = 'checklist';

            //No telegram message
            $changelog->userActivity($text, $pages);

            $message = 'Dokumen Permit Tidak Lengkap';

            // Finally, return a JSON
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => $message,
            ]);
            // Close the database connection
            $conn = null;
        }
    }
}