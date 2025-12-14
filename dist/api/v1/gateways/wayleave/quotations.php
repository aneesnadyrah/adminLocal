<?php

// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// require "api/gateway.php";
// include_once "config/functions.php";

require_once "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";

// set header control :: origin header are setted by gateway.php
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Retrieve the JSON data
$json_data = file_get_contents("php://input");
// $callbackId = ExternalApi::insertCallbackData(1, 32, $json_data, $_SERVER['REQUEST_URI'], http_response_code());

// Check if JSON data was retrieved successfully
if ($json_data === false) {
    $error = 'Error retrieving JSON data';
    echo 'Error retrieving JSON data';
    exit;

} else {
    // Parse the JSON data

    $data = json_decode($json_data);


}

$db = new DBConnectionFactory();
$conn = $db->createConnection();
$taskAssignment = new TaskAssignments();
$telegram = new Telegram();
$system = new System();
$curl = new Curl();

$changelog = new Changelog($data->username);
$flow = new FlowStatuses($data->username);

$chronology = new Chronology();
$tenant = $system->App->tenant;
$timestamp = date('Y-m-d H:i:s', time());

// Check if the URL contains a parameter named
if ($_SERVER['REQUEST_METHOD'] == "GET") {

    // Get the token and request domain from the request
    $token = '';
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    } elseif (isset($_GET['t'])) {
        $token = $_GET['t'];
    }

    $name = 'Bentong';
    $subId = $_GET['subId'];

    // $stmt = $conn->prepare('SELECT * FROM sys_api_tokens WHERE token = :token AND domain = :domain AND expires_at > NOW() AND is_active = true');
    $stmt = $conn->prepare('SELECT * FROM flw_appl_entries WHERE system_id = :subId');

    $stmt->bindParam(':subId', $subId);
    $stmt->execute();

    // create an array to hold the query result
    $data = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    // convert the result to a JSON string
    $json = json_encode($data);

    if ($stmt->rowCount() > 0) {
        // return the JSON string to the client

        http_response_code(200);
        echo json_encode(
            array(
                "success" => true,
                "message" => "success",
                "data" => $data
            )
        );

    } else {
        http_response_code(204);
        echo json_encode(
            array(
                "success" => false,
                "message" => "no content",
            )
        );

    }

    $conn = null;

} else if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Get the token and request domain from the request
    $token = '';
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    } elseif (isset($_GET['t'])) {
        $token = $_GET['t'];
    }

    $requestDomain = substr($_SERVER['HTTP_ORIGIN'], 8);


    if (Utilities::checkDomainToken($data->token, $_SERVER['HTTP_ORIGIN'])) {

        // Check if the expected keys are present in the data
        if (!isset($data->sub_id) || !isset($data->approval)) {

            echo 'Data is missing expected keys.';
            $error = 'Data is missing expected keys.';
            $response = json_encode(array("error" => $error));

        } else {
            // Retrieve the form data using $_POST

            $subId = $data->sub_id;
            $approval = $data->approval;


            // Prepare a statement to check whether the entry has reference no or not
            $stmt = $conn->prepare('SELECT reference_no, system_id FROM flw_appl_entries WHERE system_id = :subId LIMIT 1');
            $stmt->bindParam(':subId', $subId);

            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $refNo = $row->reference_no;
                $systemId = $row->system_id;

                if (isset($refNo)) {

                    if ($approval === true) {
                        // Setuju Sebut Harga

                        $submitter = $data->username;  // Accessing the "username" property
                        $url = $data->file_url;        // Accessing the "file_url" property
                        $filename = $data->file_name;
                        $mimeType = $data->mime_type;
                        $size = $data->file_size; 

                        $created = date('Y-m-d H:i:s', time());
                        $notes = 'Sebut Harga telah dipersetujui oleh ' . $submitter . ' pada ' . $created;

                        // Format the DateTime object to Y-m-d (year-month-day) format
                        $attachDate = date('Y-m-d', strtotime($created));

                        $stmt = $conn->prepare("UPDATE flw_appl_verifies SET quote_approve = :approval,  quote_approver = :username, quote_approve_date = :approvalDate, quote_approve_notes = :quoteNote, quote_approve_url = :url, quote_services_included_approved = quote_services_included, quote_services_included_amended = null WHERE system_id = :systemId");

                        $stmt->bindParam(':approval', $approval, PDO::PARAM_BOOL);
                        $stmt->bindParam(':quoteNote', $notes);
                        $stmt->bindParam(':username', $submitter);
                        $stmt->bindParam(':approvalDate', $created);
                        $stmt->bindParam(':systemId', $systemId);
                        $stmt->bindParam(':url', $url);
                        $stmt->execute();

                        // Prepare the INSERT resit bayaran into flw_appl_attachments
                        $stmtAttachment = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date,created_date, attachment_type, user_added, mime_type, size) VALUES (:systemId, :filename, :url, :attachDate, :createdAt, 11, :user, :mimeType, :size) RETURNING id");

                        $stmtAttachment->bindParam(':systemId', $systemId);
                        $stmtAttachment->bindParam(':filename', $filename);
                        $stmtAttachment->bindParam(':url', $url);
                        $stmtAttachment->bindParam(':attachDate', $attachDate);
                        $stmtAttachment->bindParam(':createdAt', $created);
                        $stmtAttachment->bindParam(':user', $submitter);
                        $stmtAttachment->bindParam(':mimeType', $mimeType);
                        $stmtAttachment->bindParam(':size', $size);


                        if($stmtAttachment->execute()) {
                            $flwStatus = 28;
                            // $status = 154;
                            // NOTE - Update Task Assignment

                            // $taskAssignment->complete($submitter, $systemId, $status);

                            $taskAssignment->create($systemId, $flwStatus);

                            //Record Activity
                            $text = 'Pengguna ' . $submitter . ' telah bersetuju dengan sebut harga bagi permohonan ' . $systemId ;
                            $pages = 'task_finance';
                            // $changelog->userActivity($text, $pages);

                            // NOTE - current status = 1
                            //nextstep for finance

                            $current = $flow->getCurrentFlow($systemId, "finance");
                            $status = $current->status;
                            
                            if ($status == 29) {
                                $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus, apiToken: $data->token);
                            } else {
                                $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", apiToken:$data->token);
                            }
                            
                            $details = 'Sebut Harga telah Disetujui bagi permohonan '.$systemId .'. Nota: '.$data->notes;

                            // NOTE - Insert Project Changelog
                            if($changelog->projectActivity($systemId, $status, $details)) {
                                // declare telegram notification string
                                // $telegramMsg = "Sebut Harga telah di muatnaik oleh " . $submitter . ". Sila semak maklumat Sebut Harga ini. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
                                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $submitter);
                                // NOTE - send telegram notification for team account
                                $telegramResponse = $telegram->SendMessage('group', 'management', $telegramMsg, 'html');
                                $telegramResponse = $telegram->SendMessage('department', 'finance', $telegramMsg, 'html');
                                $telegramResponse = $telegram->SendMessage('role', 12, $telegramMsg, 'html');
                                $telegramResponse = $telegram->SendMessage('role', 13, $telegramMsg, 'html');
 				$telegramResponse = $telegram->SendMessage('role', 2, $telegramMsg, 'html');
                                $telegramResponse = $telegram->SendMessage('role', 3, $telegramMsg, 'html');
                            }

                            $chronoId = $chronology->create($submitter, $systemId, $status, 'file');

                            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                            $stmt = $conn->prepare($query);
                            $stmt->bindParam(':systemId', $systemId);
                            $stmt->bindParam(':details', $details);
                            $stmt->bindParam(':created', $timestamp);
                            $stmt->bindParam(':username', $submitter);
                            $stmt->bindParam(':chronology_id', $chronoId);
                            $stmt->execute();

                            $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 11 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 11 ORDER BY id DESC LIMIT 1)";

                            $stmt2 = $conn->prepare($query2);
                            $stmt2->bindParam(':systemId', $systemId);
                            $stmt2->bindParam(':chronology_id', $chronoId);
                            $stmt2->execute();

                            // Finally, return a JSON
                            http_response_code(200);
                            $response = json_encode(
                                array(
                                    "success" => true,
                                    "message" => "Quotation has been Approved for " . $refNo,
                                )
                            );

                        } else {
                            http_response_code(400);
                            $response = json_encode(
                                array(
                                    "success" => false,
                                    "message" => "File attachment insertion failed for " . $refNo,
                                )
                            );

                        }

                        echo $response;

                        // Update Status Application // 7 (Registration Payment -> 8 Check Registration Payment)

                        // $query = "SELECT id FROM ctrl_authorities WHERE system_id = :systemId";
                        // $stmt = $conn->prepare($query);
                        // $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
                        // $stmt->execute();
                        // $flwAuthorities = $stmt->fetchAll();

                        // $authorityStatus = 32;
                        // foreach ($flwAuthorities as $row) {
                        //     $query = "UPDATE ctrl_authorities SET authority_status = :authorityStatus WHERE id = :authorityId";
                        //     $stmt = $conn->prepare($query);
                        //     $stmt->bindParam(':authorityStatus', $authorityStatus);
                        //     $stmt->bindParam(':authorityId', $row['id']);
                        //     $stmt->execute();
                        // }

                    } else {
                        //TODO: sambung sini untuk Pindaan Sebut Harga

                        $submitter = $data->username;
                        $items = $data->items;

                        $itemsArray = "{" . implode(",", $items) . "}";
                        $notes = $data->notes;
                        $flwStatus = 29;
                        $status = 154;
                        $details = 'Sebut Harga bagi permohonan '.$systemId.' Tidak Disetujui. Nota: '.$data->notes;

                        $stmt = $conn->prepare("UPDATE flw_appl_verifies SET quote_approve = :approval,  quote_approver = :username, quote_approve_notes = :quoteNote, quote_services_included_amended = :items WHERE system_id = :systemId");

                        $stmt->bindParam(':approval', $approval, PDO::PARAM_BOOL);
                        $stmt->bindParam(':quoteNote', $notes);
                        $stmt->bindParam(':items', $itemsArray, PDO::PARAM_STR);
                        $stmt->bindParam(':username', $submitter);
                        $stmt->bindParam(':systemId', $systemId);
                        $stmt->execute();

                        // NOTE - Update Task Assignment
                        $taskAssignment->complete($submitter, $systemId, $status);
                        $taskAssignment->create($systemId, $flwStatus);

                        $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus, apiToken: $data->token);

                        // NOTE - Insert Project Changelog
                        if($changelog->projectActivity($systemId, $status, $details)) {
                            // declare telegram notification string
                            // $telegramMsg = "Sebut Harga telah ditolak oleh " . $submitter . ". Sila muat naik Sebut Harga yang baru. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
                            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $submitter);
                            // NOTE - send telegram notification for team account
                            $telegramResponse = $telegram->SendMessage('group', 'management', $telegramMsg, 'html');
                            $telegramResponse = $telegram->SendMessage('department', 'finance', $telegramMsg, 'html');
                        }
                        
                        $chronoId = $chronology->create($submitter, $systemId, $status, 'notes');

                        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':systemId', $systemId);
                        $stmt->bindParam(':details', $details);
                        $stmt->bindParam(':created', $timestamp);
                        $stmt->bindParam(':username', $submitter);
                        $stmt->bindParam(':chronology_id', $chronoId);
                        $stmt->execute();

                        http_response_code(200);
                        echo json_encode(
                            array(
                                "success" => true,
                                "message" => "Quotation has been Rejected. Please upload a new quotation for " . $refNo,
                            )
                        );
                    }

                } else {
                    // Handle the case when the reference number is not found
                    http_response_code(200);
                    $response = json_encode(
                        array(
                            "success" => false,
                            "message" => "This entry does not have reference no",
                        )
                    );
                    // ExternalApi::updateCallbackData($callbackId, $response, false);
                    echo $response;
                }
            }
        }

    } else {
        // The token or domain is invalid or the token has expired
        http_response_code(400);
        $result = json_encode(
            array(
                "success" => false,
                "message" => "Invalid or expired API token",
            )
        );

        echo $result;

    }

}