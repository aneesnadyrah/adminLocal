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

if ($tenant == 'KUTT') {

    if ((isset($POST['notes']))) {

        $notes = $POST['notes'];
        $systemId = $POST['systemId'];
        $timestamp = date('Y-m-d H:i:s', time());

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];
        $triggerPriority = true;

        // Execute a SELECT query on the database
        $checking1 = $conn->prepare("SELECT id FROM flw_appl_survey WHERE system_id = :systemId ");

        $checking1->bindParam(':systemId', $systemId);
        $checking1->execute();

        // Return the username information to the client
        if ($checking1->rowCount() > 0) {
            // Record exists, update the record
            $assign1 = $conn->prepare("UPDATE flw_appl_survey SET submission_date = :submitted, submission_name = :username WHERE system_id = :systemId");
            // $status = 2;
        } else {
            // Record doesn't exist, insert a new record
            $assign1 = $conn->prepare("INSERT INTO flw_appl_survey (system_id, submission_date, submission_name) VALUES   (:systemId, :submitted, :username)");
            // $status = 2;
        }
        // Bind the parameters
        $assign1->bindParam(':systemId', $systemId);
        $assign1->bindParam(':submitted', $timestamp);
        $assign1->bindParam(':username', $username);
        $assign1->execute();

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        // Return the username information to the client
        if ($checking->rowCount() > 0) {
            // Record exists, update the record
            $assign = $conn->prepare("UPDATE flw_survey_udm SET notes = :notes, submission_date = :submitted, submission_name =   :username, trigger_priority = :triggerPriority WHERE system_id = :systemId");
            // $status = 2;
        } else {
            // Record doesn't exist, insert a new record
            $assign = $conn->prepare("INSERT INTO flw_survey_udm (system_id, notes, submission_date, submission_name, trigger_priority) VALUES   (:systemId, :notes, :submitted, :username, :triggerPriority)");
            // $status = 2;
        }
        // Bind the parameters
        $assign->bindParam(':systemId', $systemId);
        $assign->bindParam(':notes', $notes);
        $assign->bindParam(':submitted', $timestamp);
        $assign->bindParam(':username', $username);
        $assign->bindParam(':triggerPriority', $triggerPriority);
        // $assign->execute();

        if ($assign->execute()) {

            $flwStatus = 41;
            $status = 39;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah meluluskan permohonan mula kerja ukur bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 39
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Permohonan Mula Kerja Ukur telah Diluluskan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Permohonan Mula Kerja Ukur telah Diluluskan. \n\n🔗 <strong> No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
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
            $stmt->execute();

            // Finally, return a JSON
            echo json_encode([
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
            ]);

        } else {
            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => "failed",
                "status" => 500,
            );
        }

        // Check if the query was successful
        if ($assign->rowCount() > 0) {
            // Execute a SELECT query on the database
            $select = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

            $select->bindParam(':systemId', $systemId);
            $select->execute();
            // Fetch the result from the executed SELECT query
            $result = $select->fetch(PDO::FETCH_ASSOC);
            // Retrieve the ID from the fetched result
            $lastInsertedId = $result['id'];
        }

        $assign2 = $conn->prepare("UPDATE flw_appl_survey SET udm_id = :udmId, submission_date = :submitted WHERE system_id = :systemId");
        // Bind the parameters
        $assign2->bindParam(':udmId', $lastInsertedId);
        $assign2->bindParam(':submitted', $timestamp);
        $assign2->bindParam(':systemId', $systemId);

        $message = 'Permohonan Mula Kerja Ukur telah Diluluskan 🎉';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => $message,
        ]);
        // Close the database connection
        $conn = null;

    } else {
        echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
    }
} else if ($tenant == 'KUP' || $tenant == 'KUDR' || $tenant == null) {

    if ((isset($POST['notes']))) {

        $notes = $POST['notes'];
        $systemId = $POST['systemId'];
        $timestamp = date('Y-m-d H:i:s', time());

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];
        $triggerPriority = true;

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        // Bind the parameters
        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        // Return the username information to the client
        if ($checking->rowCount() > 0) {
            // Record exists, update the record
            $assign = $conn->prepare("UPDATE flw_survey_udm SET notes = :notes, submission_date = :submitted, submission_name = :username, trigger_priority = :triggerPriority WHERE system_id = :systemId");
            // $status = 1;
        } else {
            // Record doesn't exist, insert a new record
            $assign = $conn->prepare("INSERT INTO flw_survey_udm (system_id, notes, submission_date, submission_name, trigger_priority) VALUES   (:systemId, :notes, :submitted, :username, :triggerPriority)");
            // $status = 1;
        }
        // Bind the parameters
        $assign->bindParam(':systemId', $systemId);
        $assign->bindParam(':notes', $notes);
        $assign->bindParam(':submitted', $timestamp);
        $assign->bindParam(':username', $username);
        $assign->bindParam(':triggerPriority', $triggerPriority);
        // $assign->execute();

        if ($assign->execute()) {

            $flwStatus = 40;
            $status = 39;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menghantar permohonan mula kerja ukur bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 39
            //nextstep for mapping
            // $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Permohonan Mula Kerja Ukur telah Dihantar bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Permohonan Mula Kerja Ukur telah dihantar. \n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html'); 
                $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html'); 
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
            $stmt->execute();

            // Finally, return a JSON
            echo json_encode([
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
            ]);

        } else {
            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => "failed",
                "status" => 500,
            );
        }

        $message = 'Permohonan Mula Kerja Ukur telah Dihantar 🎉';

        // Close the database connection
        $conn = null;

    } else {
        echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
    }
} else {
    echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
}
