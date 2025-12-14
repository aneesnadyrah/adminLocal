<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/system.php";
require_once "config/DBFactory.php";
include_once "api/header.php";
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
$whatsapp = new Whatsapp();

$ec_url = $system->App->ec_url;

$status = $_GET["status"];

$username = $_SESSION['username'];

$conn = $db->createConnection();
$tenant = $system->App->tenant;
$state = $system->App->state;

//TODO : note function
//TODO : telegram API
//TODO : change status
//TODO : Changelog

// Check if the URL contains a parameter named

if ($status == "1") {

    $timestamp = date('Y-m-d H:i:s', time());
    $systemId = $POST['systemId'];
    $dateInv = $POST['dt_inv'];

    $stmtUrl = $conn->prepare('SELECT id,name, url, mime_type, size FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 21 ORDER BY id DESC LIMIT 1');
    $stmtUrl->bindParam(':systemId', $systemId);
    $stmtUrl->execute();
    $resultUrl = $stmtUrl->fetch(PDO::FETCH_ASSOC);

    if ($stmtUrl->rowCount() > 0) {
        $filename = $resultUrl['name'];
        $url = $resultUrl['url'];
        $mimeType = $resultUrl['mime_type'];
        $size = $resultUrl['size'];
    } else {
        $filename = '';
        $url = '';
        $mimeType = '';
        $size = '';
    }

    $catatan = 'Invois Caj Pendaftaran Telah Dihantar. Nota: ' . $POST['notes'];

    // Create an associative array with the variables
    $applData = array(
        "secret" => $system->App->secret,
        "submissions" => array(
            "charges" => array(
                "notes" => $catatan,
                "payment_method" => 2,
                "inv_url" => $url,
                "username" => $username,
                "filename" => $filename,
                "mime_type" => $mimeType,
                "size" => $size,
                "stage" => 1
            )
        )
    );

    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/charges/' . $systemId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

    // Decode the response JSON
    $curlResult = json_decode($apiRequest['response']);

    if ($curlResult->message === "success") {

        //TODO : add data input from modal


        $flwStatus = 2;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Muat Naik Invois Caj Pendaftaran bagi permohonan ' . $systemId;
        $pages = 'task_finance';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 1
        //nextstep for finance
        $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");

        $details = 'Invois Caj Pendaftaran telah Dimuat Naik bagi permohonan ' . $systemId . '. Nota: ' . $POST['notes'];

        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // $telegramMsg = "Invois Caj Pendaftaran telah dimuat naik. \n\n<strong>?? No Rujukan : " . $systemId . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, systemId: $systemId);
            // NOTE - send telegram notification for team account
            // $response = $telegram->sendMessage('flow', $steps, $message);
            // $telegramResponse = $telegram->SendMessage('department', 'finance', $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 21 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 21 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();



        $message = 'Invois Caj Pendaftaran telah Dimuat Naik ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "method" => 'Invoice',
            "message" => $message,
            "inv_url" => $url,
            "size" => $size,
            "status" => 200
        ]);

    } else {
        // Finally, return a JSON
        http_response_code(500);
        echo json_encode(
            array(
                "message" => $curlResult->message,
                "status" => 500
            )
        );
    }
    // END API TO EXTERCORD
    // Close the database connection
    $conn = null;

} else if ($status == "2") {
    //payment-verified
    $timestamp = date('Y-m-d H:i:s', time());
    $systemId = $POST['systemId'];
    $verifyRcp = isset ($POST['verify_rcp']) ? $POST['verify_rcp'] : 0;
    $verifyRcp = isset ($POST['verify_rcp']) ? $POST['verify_rcp'] : 0;

    $stmt = $conn->prepare('SELECT payment_method FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
    $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $payment_method = $result['payment_method'];

    if($payment_method == 4) {

        if ($verifyRcp == 1) {
            $stmt1 = $conn->prepare('SELECT flw_charges.paid_at, flw_charges.bill_code FROM flw_appl_entries LEFT JOIN flw_charges ON flw_charges.id = flw_appl_entries.charges_id WHERE system_id = :systemId LIMIT 1');
            $stmt1->bindParam(':systemId', $systemId);
            $stmt1->execute();
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);

            $bill_code = $result['bill_code'];
            $paid_at = $result['paid_at'];
            // $project_title = $result['project_title'];

            $notes = 'Resit Caj Pendaftaran Diterima. Nota: ' . $POST['notes'];

            //verify the charge
            $stmt2 = $conn->prepare('UPDATE flw_charges SET is_paid = :verify WHERE bill_code = :bill_code');

            $verify = true;
            $stmt2->bindParam(':verify', $verify);
            $stmt2->bindParam(':bill_code', $bill_code);

            if ($stmt2->execute()) {

                $month = date('m', time());
                $shortYear = date('y', time());
                $fullYear = date('Y', time());

                $stmt = $conn->prepare('SELECT length_code, submission_code, created_at, utility_provider, utility_type, tags FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
                $stmt->bindParam(':systemId', $systemId);
                $stmt->execute();

                $record = $stmt->fetch(PDO::FETCH_OBJ);
                $lengthCode = $record->length_code;
                $providerId = $record->utility_provider;
                $subId = $record->submission_code;
                $createdAt = $record->created_at;
                $utilityType = $record->utility_type;
                $applLabel = $record->tags;

                $stmt = $conn->prepare("SELECT running FROM ctrl_reference_no WHERE year = :year ORDER BY running DESC LIMIT 1");

                $stmt->bindParam(':year', $fullYear);
                $stmt->execute();
                $record = $stmt->fetch(PDO::FETCH_OBJ);

                if ($stmt->rowCount() > 0) {
                    $log = $record->running + 1;
                } else {
                    $log = 1;
                }
                $runNo = str_pad($log, 4, '0', STR_PAD_LEFT);

                $query = 'INSERT INTO ctrl_reference_no (system_id, year, running) VALUES (:systemId, :year, :running)';
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':year', $fullYear);
                $stmt->bindParam(':running', $runNo);
                $stmt->execute();

                $stmt = $conn->prepare('SELECT districts, SUM(road_length), (SELECT state FROM flw_appl_entries WHERE system_id = :systemId) AS state FROM flw_appl_roads WHERE system_id = :systemId GROUP BY districts ORDER BY SUM DESC LIMIT 1');
                $stmt->bindParam(':systemId', $systemId);
                $stmt->execute();
                $record = $stmt->fetch(PDO::FETCH_OBJ);

                $stmt = $conn->prepare('SELECT short_code AS short FROM ls_districts WHERE district_code = :distirct AND state_code = :state');
                $stmt->bindParam(':distirct', $record->districts);
                $stmt->bindParam(':state', $record->state);
                $stmt->execute();
                $record = $stmt->fetch(PDO::FETCH_OBJ);

                $shortCode = $record->short;

                $provider = Utilities::getProvider($providerId);
                // KUTT.HT/TEL/A2/05/2023/0095
                $refNo = $tenant . "." . $shortCode . "/" . $utilityType . "/" . $lengthCode . "/" . $month . "/" . $fullYear . "/" . $runNo;

                $notes = "Caj Pendaftaran Telah Berjaya Dibayar. No Rujukan Anda : " . $refNo. " \nNota: " . $POST['notes'] . " ";

                $applData = [
                    "secret" => $system->App->secret,
                    "submissions" => [
                        "reference_no" => $refNo,
                        "username" => $username,
                        "notes" => $notes,
                        "stage" => 1
                    ]
                ];

                // NOTE - Update reference number
                $stmt = $conn->prepare("UPDATE flw_appl_entries SET reference_no = :refNo WHERE system_id = :systemId ");
                $stmt->bindParam(':refNo', $refNo);
                $stmt->bindParam(':systemId', $systemId);

                if ($stmt->execute()) {
                    $jsonData = json_encode($applData);
                    $request = $Curl->request($ec_url.'/gateway/internal/reference/'.$subId, $jsonData);
                    $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

                    $curlResult = json_decode($request['response']);
                }

                $flwStatus = 160;
                // NOTE - next step for registration for pengesahan NO Rujukan
                $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus, allRole: true);

                // NOTE - Update Task Assignment
                $taskAssignment->complete($username, $systemId, $status);
                $taskAssignment->create($systemId, $flwStatus);

                $details = 'Caj Pendaftaran Telah Berjaya Disemak. No Rujukan bagi permohonan ini : ' . $refNo . ' perlu disahkan. Nota: ' . $POST['notes'];
                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {
                    $message = "Caj Pendaftaran Telah Berjaya Disemak. Sila teruskan dengan Pengesahan No Fail. \n\n<strong>?? No Permohonan : #" . $systemId . " \n?? No Rujukan : " . $refNo . "\n?? Tarikh Bayaran : " . Utilities::convertDateToMalay($paid_at) . "</strong>";

                    $message2 = "Caj Pendaftaran Telah Berjaya Disemak. \n\n<strong>?? No Permohonan : #" . $systemId . " \n?? No Rujukan : " . $refNo . "\n?? Tarikh Bayaran : " . Utilities::convertDateToMalay($paid_at) . "</strong>";
                    
                    $telegram->sendMessage('group', 'management', $message2, 'html');
                    $telegram->sendMessage('department', 'finance', $message2, 'html');
                    $telegram->sendMessage('department', 'registration', $message, 'html');
                };

                $chronoId = $chronology->create($username, $systemId, $status, 'notes');

                $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':details', $details);
                $stmt->bindParam(':created', $timestamp);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':chronology_id', $chronoId);
                $stmt->execute();
            }

            $message = 'Resit Caj Pendaftaran Disahkan ??';

            // Finally, return a JSON
            http_response_code(200);
            echo json_encode([
                "message" => $message,
                "status" => 200,
                "reference" => $refNo
            ]);

            $conn = null;

        }

    } else {
        
        if ($verifyRcp == 1) {


            // START API TO EXTERCORD
            $stmt1 = $conn->prepare('SELECT flw_appl_entries.project_title, flw_charges.bill_code FROM flw_appl_entries LEFT JOIN flw_charges ON flw_charges.id = flw_appl_entries.charges_id WHERE system_id = :systemId LIMIT 1');

            $stmt1->bindParam(':systemId', $systemId);

            $stmt1->execute();

            $result = $stmt1->fetch(PDO::FETCH_ASSOC);

            $bill_code = $result['bill_code'];
            $project_title = $result['project_title'];

            $notes = 'Resit Caj Pendaftaran Diterima. Nota: ' . $POST['notes'];

            // Create an associative array with the variables
            $applData = [
                "secret" => $system->App->secret,
                "submissions" => [
                    "receipt" => [
                        "user_id" => $username,
                        "payment_id" => $bill_code,
                        "paid" => true,
                        "state" => "paid",
                        "notes" => $notes,
                        "stage" => 1
                    ]
                ]
            ];

            // Convert the array to JSON
            $jsonData = json_encode($applData);

            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/receipt/' . $systemId, $jsonData, method: 'PUT');

            // Decode the response JSON
            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);
            $curlResult = json_decode($apiRequest['response']);


            if ($curlResult->message === "success") {

                $stmt1 = $conn->prepare('SELECT paid_at FROM flw_charges WHERE bill_code = :bill_code');
                $stmt1->bindParam(':bill_code', $bill_code);
                $stmt1->execute();
                $result = $stmt1->fetch(PDO::FETCH_ASSOC);

                $paid_at = $result['paid_at'];

                //verify the charge
                $stmt2 = $conn->prepare('UPDATE flw_charges SET is_paid = :verify WHERE bill_code = :bill_code');
    
                $verify = true;
                $stmt2->bindParam(':verify', $verify);
                $stmt2->bindParam(':bill_code', $bill_code);
    
                if ($stmt2->execute()) {
    
                    $month = date('m', time());
                    $shortYear = date('y', time());
                    $fullYear = date('Y', time());
    
                    $stmt = $conn->prepare('SELECT length_code, submission_code, created_at, utility_provider, utility_type, tags FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();
    
                    $record = $stmt->fetch(PDO::FETCH_OBJ);
                    $lengthCode = $record->length_code;
                    $providerId = $record->utility_provider;
                    $subId = $record->submission_code;
                    $createdAt = $record->created_at;
                    $utilityType = $record->utility_type;
                    $applLabel = $record->tags;
    
                    $stmt = $conn->prepare("SELECT running FROM ctrl_reference_no WHERE year = :year ORDER BY running DESC LIMIT 1");
    
                    $stmt->bindParam(':year', $fullYear);
                    $stmt->execute();
                    $record = $stmt->fetch(PDO::FETCH_OBJ);
    
                    if ($stmt->rowCount() > 0) {
                        $log = $record->running + 1;
                    } else {
                        $log = 1;
                    }
                    $runNo = str_pad($log, 4, '0', STR_PAD_LEFT);
    
                    $query = 'INSERT INTO ctrl_reference_no (system_id, year, running) VALUES (:systemId, :year, :running)';
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':year', $fullYear);
                    $stmt->bindParam(':running', $runNo);
                    $stmt->execute();
    
                    $stmt = $conn->prepare('SELECT districts, SUM(road_length), (SELECT state FROM flw_appl_entries WHERE system_id = :systemId) AS state FROM flw_appl_roads WHERE system_id = :systemId GROUP BY districts ORDER BY SUM DESC LIMIT 1');
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();
                    $record = $stmt->fetch(PDO::FETCH_OBJ);
    
                    $stmt = $conn->prepare('SELECT short_code AS short FROM ls_districts WHERE district_code = :distirct AND state_code = :state');
                    $stmt->bindParam(':distirct', $record->districts);
                    $stmt->bindParam(':state', $record->state);
                    $stmt->execute();
                    $record = $stmt->fetch(PDO::FETCH_OBJ);
    
                    $shortCode = $record->short;
    
                    $provider = Utilities::getProvider($providerId);
                    if ($tenant === 'KUTT') {
                        // KUTT.HT/TEL/A2/05/2023/0095
                        $refNo = $tenant . "." . $shortCode . "/" . $utilityType . "/" . $lengthCode . "/" . $month . "/" . $fullYear . "/" . $runNo;
                    } else {
                        // KUP/TNBB/A2/09/23/0409
                        if ($providerId == 5 || $providerId == 7) {
                            if (isset ($applLabel)) {
                                $firstChar = $applLabel[0];
                            }
                            $provider->sort_name .= $firstChar;
                        }
                        $refNo = $tenant . "/" . $provider->sort_name . "/" . $lengthCode . "/" . $month . "/" . $shortYear . "/" . $runNo;
                    }

                    $stmt = $conn->prepare("UPDATE flw_appl_entries SET reference_no = :refNo WHERE system_id = :systemId ");
                    $stmt->bindParam(':refNo', $refNo);
                    $stmt->bindParam(':systemId', $systemId);
                    if ($stmt->execute()) {

                        $flwStatus = 160;
                        // NOTE - next step for registration for pengesahan NO Rujukan
                        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus, allRole: true);

                        // NOTE - Update Task Assignment
                        $taskAssignment->complete($username, $systemId, $status);
                        $taskAssignment->create($systemId, $flwStatus);

                        //Record Activity
                        $text = 'Pengguna ' . $username . ' telah Mengesahkan Resit Caj Pendaftaran bagi No Rujukan ' . $refNo;
                        $pages = 'task_finance';
                        $changelog->userActivity($text, $pages);

                        $details = 'Resit Caj Pendaftaran Telah Disahkan. No Rujukan bagi permohonan ini : ' . $refNo . '. Nota: ' . $POST['notes'];
                        // NOTE - Insert Project Changelog
                        if ($changelog->projectActivity($systemId, $status, $details)) {
                          
                            $message = "Caj Pendaftaran Telah Berjaya Disemak. Sila teruskan dengan Pengesahan No Fail. \n\n<strong>?? No Permohonan : #" . $systemId . " \n?? No Rujukan : " . $refNo . "\n?? Tarikh Bayaran : " . Utilities::convertDateToMalay($paid_at) . "</strong>";
                            $message2 = "Caj Pendaftaran Telah Berjaya Disemak. \n\n<strong>?? No Permohonan : #" . $systemId . " \n?? No Rujukan : " . $refNo . "\n?? Tarikh Bayaran : " . Utilities::convertDateToMalay($paid_at) . "</strong>";

                            // NOTE - send telegram notification for team account
                            $telegramResponse = $telegram->sendMessage('group', 'management', $message2, 'html');
                            $telegram->sendMessage('department', 'finance', $message2, 'html');
                            $telegramResponse = $telegram->sendMessage('department', 'registration', $message, 'html');
                        }

                        $chronoId = $chronology->create($username, $systemId, $status, 'notes');

                        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':systemId', $systemId);
                        $stmt->bindParam(':details', $details);
                        $stmt->bindParam(':created', $timestamp);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':chronology_id', $chronoId);
                        $stmt->execute();
                    }
                }
    
                $message = 'Resit Caj Pendaftaran Disahkan ??';
    
                // Finally, return a JSON
                http_response_code(200);
                echo json_encode([
                    "message" => $message,
                    "status" => 200,
                    "reference" => $refNo
                ]);
    
            } else {
                // Finally, return a JSON
                http_response_code(500);
                echo json_encode(
                    array(
                        "message" => $curlResult->message,
                        "status" => 500
                    )
                );
            }
    
            $conn = null;
            // END API TO EXTERCORD
    
        } else {
            //semakan bayaran tidak diterima
    
            // START API TO EXTERCORD
            $stmt1 = $conn->prepare('SELECT flw_appl_entries.submission_code, flw_appl_entries.project_title, flw_charges.bill_code FROM flw_appl_entries LEFT JOIN flw_charges ON flw_charges.id = flw_appl_entries.charges_id WHERE system_id = :systemId LIMIT 1');
            $stmt1->bindParam(':systemId', $systemId);
    
            $stmt1->execute();
    
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
    
            $subId = $result['submission_code'];
            $project_title = $result['project_title'];
            $bill_code = $result['bill_code'];
    
            $notes = 'Resit Caj Pendaftaran Tidak Diterima ' . $POST['notes'];
    
            // Create an associative array with the variables
            $applData = array(
                "secret" => $system->App->secret,
                "submissions" => array(
                    "receipt" => array(
                        "user_id" => $username,
                        "payment_id" => $bill_code,
                        "paid" => false,
                        "state" => "pending",
                        "notes" => $notes,
                        "stage" => 1
                    )
                )
            );
    
            // Convert the array to JSON
            $jsonData = json_encode($applData);
    
            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/receipt/' . $systemId, $jsonData, 'PUT');
    
            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);
    
            // Decode the response JSON
            $curlResult = json_decode($apiRequest['response']);
    
            if ($curlResult->message === "success") {
    
                $flwStatus = 3;
                // NOTE - Update Task Assignment
                $taskAssignment->complete($username, $systemId, $status);
                $taskAssignment->create($systemId, $flwStatus);
    
                //Record Activity
                $text = 'Pengguna ' . $username . ' menolak Resit Caj Pendaftaran bagi permohonan ' . $systemId;
                $pages = 'task_finance';
                $changelog->userActivity($text, $pages);
    
                // NOTE - current status = 2
                //nextstep for finance
                $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");
    
                $details = 'Resit Caj Pendaftaran Tidak Diterima bagi permohonan ' . $systemId . '. Nota: ' . $POST['notes'];
                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {
    
                    // declare telegram notification string
                    // $telegramMsg = "Resit Caj Pendaftaran Tidak Diterima. Sila semak permohonan ini. \n\n<strong>?? Tajuk : " . $project_title . " \n?? No Rujukan : " . $systemId . "</strong>";
                    $telegramMsg = $telegram->getMessageByFlow($flwStatus, item: $project_title, systemId: $systemId);
    
                    // NOTE - send telegram notification for team account
                    // $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');
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
    
                $message = 'Caj Pendaftaran Tidak Disahkan';
    
                // Finally, return a JSON
                http_response_code(200);
                echo json_encode([
                    "message" => $message,
                    "status" => 200
                ]);
    
            } else {
                // Finally, return a JSON
                http_response_code(500);
                echo json_encode(
                    array(
                        "message" => $curlResult->message,
                        "status" => 500
                    )
                );
            }
            // Close the database connection
            $conn = null;
            // END API TO EXTERCORD
        }

    }

} else if ($status == "6") {
    //total_wopw
    // Get the user's identification from the request parameters
    $totalWO = isset($POST['total_wop']) ? $POST['total_wop'] : '';
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    // Prepare a SELECT query to select based on system_id
    $checking = $conn->prepare("SELECT id FROM flw_appl_verifies WHERE system_id = :systemId ");

    // bind the parameter to the placeholder using the bindValue method
    $checking->bindValue(':systemId', $systemId);

    // Execute the query
    $checking->execute();

    // Fetch all rows from the result set as an array
    $result = $checking->fetchAll(PDO::FETCH_ASSOC);

    // start select reference no from flw_appl_entries
    $refNo = Utilities::getRefNo($systemId);

    // Return the user information to the client
    if (count($result) > 0) {
        // Prepare the query for update
        $query = "UPDATE flw_appl_verifies SET wop_submit_date = :submitted, wop_submitter = :username, wop_total = :totalWO WHERE system_id = :systemId ";
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':totalWO', $totalWO, PDO::PARAM_INT);
        $stmt->bindParam(':systemId', $systemId);

    } else {
        // Prepare the query for insert into table verify
        $query = "INSERT INTO flw_appl_verifies (system_id, wop_submit_date, wop_submitter, wop_total ) VALUES (:systemId, :submitted, :user, :totalWO) ";
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':user', $username);
        $stmt->bindParam(':totalWO', $totalWO, PDO::PARAM_INT);
    }

    // Execute the query
    if ($stmt->execute()) {

        $flwStatus = 7;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        $stmt1 = $conn->prepare('SELECT flw_appl_entries.project_title FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
        $stmt1->bindParam(':systemId', $systemId);
        $stmt1->execute();
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);
        $project_title = $result['project_title'];

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Arahan Kerja PIL bagi' . $refNo;
        $pages = 'task_finance';
        $changelog->userActivity($text, $pages);

        //NOTE - Create new ctrl for geospatial
        $addNewRecord = $flow->addRecordFlow($systemId, 'BF', $flwStatus);
        
        $details = 'Arahan Kerja PIL telah dimuat naik bagi ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');

        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsgGeospation = "Arahan Kerja PIL telah dimuat naik. Sila lantik Pegawai GIS untuk penyediaan Pelan Cadangan Laluan Utiliti. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";

            // NOTE - send telegram notification for team gis
            $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsgGeospation, 'html');
        };

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 5 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 5 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }

    $message = "Arahan Kerja PIL telah berjaya dimuat naik.??";
    $type = "";

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "type" => $type,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;

} else if ($status == "7") {
    //gis_assign
    $systemId = $POST['systemId'];
    $gisAssign = isset ($POST['gis_assign']) ? $POST['gis_assign'] : '';
    $timestamp = date('Y-m-d H:i:s', time());
    // $deliveryDate = $POST['delivery_date'];

    // Prepare the query for update flw_appl_verifies
    $assign = "UPDATE flw_appl_verifies SET gis_assignee = :gisAssign, gis_assignee_date = :submitted  WHERE system_id = :systemId ";
    $data = $conn->prepare($assign);

    // Bind the parameters
    $data->bindParam(':gisAssign', $gisAssign);
    $data->bindParam(':submitted', $timestamp);
    // $data->bindParam(':deliveryDate', $timestamp); // temporarily assign with submitted timestamp on estimate delivery date
    $data->bindParam(':systemId', $systemId);

    // Execute the query
    if (!$data->execute()) {
        $errorInfo = $data->errorInfo();
        echo "Error executing query: " . $errorInfo[2];
    }

    //TODO : add expired date of submission PCL, JS for time taken not yet created

    // start select reference no from flw_appl_entries
    $refNo = Utilities::getRefNo($systemId);

    // gis name
    $stmtG = $conn->prepare('SELECT sys_hr_employee.first_name FROM sys_users left join sys_hr_employee ON sys_users.employee_id = sys_hr_employee.id WHERE username = :gisAssign LIMIT 1');
    $stmtG->bindParam(':gisAssign', $gisAssign);
    $stmtG->execute();
    $rowG = $stmtG->fetch(PDO::FETCH_ASSOC);

    $gis_name = $rowG['first_name'];

    $flwStatus = 8;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Melantik ' . $gis_name . ' untuk penyediaan Pelan Cadangan Laluan ' . $refNo;
    $pages = 'task_geospatial';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 7
    //nextstep for geo
    $NextStatus = $flow->goToNextFlow($systemId, "geospatial", "BF");

    $details = $gis_name . ' telah di lantik Bagi Penyediaan Pelan Cadangan Laluan Utiliti ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // $telegramMsg = "Lantikan Pegawai GIS bagi penyediaan Pelan Cadangan Laluan Utiliti telah dilakukan. \n\n<strong>?????Pegawai GIS :  " . $gis_name . " \n?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, item: $gis_name, refNo: $refNo);
        // NOTE - send telegram notification for team charting
        $telegramResponse = $telegram->sendMessage('group', 'charting', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('department', 'charting', $telegramMsg, 'html');
        // $telegramResponse = $telegram->sendMessage('assignee', $gisAssign, $telegramMsg, 'html');

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

    //toast
    $message = 'Lantikan Pegawai GIS berjaya!??';

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "assignee" => $gisAssign,
        "message" => $message,
        "status" => 200
    ]);

    // Close the database connection
    $conn = null;

} else if ($status == "8") {
    //gis_assign
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    // start select reference no from flw_appl_entries
    $refNo = Utilities::getRefNo($systemId);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Memuat naik Pelan Cadangan Laluan bagi permohonan ' . $refNo;
    $pages = 'task_geospatial';
    $changelog->userActivity($text, $pages);

    $details = 'Pelan Cadangan Laluan Utiliti telah dimuat naik bagi Permohonan ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
    // NOTE - Insert Project Changelog
    $changelog->projectActivity($systemId, $status, $details);

    $chronoId = $chronology->create($username, $systemId, $status, 'notes');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    //toast
    $message = 'Muat Naik Pelan Cadangan Laluan Utiliti berjaya!??';

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "message" => $message,
        "status" => 200
    ]);

    // Close the database connection
    $conn = null;

} else if ($status == "9") {
    //total woo
    $totalWO = isset ($POST['total_woo']) ? $POST['total_woo'] : '';
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    // Prepare a SELECT query to select based on system_id
    $checking = $conn->prepare("SELECT id FROM flw_appl_verifies WHERE system_id = :systemId ");

    // bind the parameter to the placeholder using the bindValue method
    $checking->bindValue(':systemId', $systemId);

    // Execute the query
    $checking->execute();

    // Fetch all rows from the result set as an array
    $result = $checking->fetchAll(PDO::FETCH_ASSOC);

    // start select reference no from flw_appl_entries
    $refNo = Utilities::getRefNo($systemId);

    // Return the user information to the client
    if (count($result) > 0) {
        // Prepare the query for update
        $query = "UPDATE flw_appl_verifies SET woo_submit_date = :submitted, woo_submitter = :user, woo_total = :totalWO WHERE system_id = :systemId ";
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':user', $username);
        $stmt->bindParam(':totalWO', $totalWO);
        $stmt->bindParam(':systemId', $systemId);

    } else {
        // Prepare the query for insert into table verify
        $query = "INSERT INTO flw_appl_verifies (system_id, woo_submit_date, woo_submitter, woo_total ) VALUES (:systemId, :submitted, :user, :totalWO) ";
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':totalWO', $totalWO);
    }

    // Execute the query
    if ($stmt->execute()) {

        $flwStatus = 10;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Arahan Kerja Operasi bagi' . $refNo;
        $pages = 'task_finance';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 9
        //nextstep for finance
        $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");

        $details = 'Arahan Kerja Operasi telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Arahan Kerja Operasi telah dimuat naik. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
            // NOTE - send telegram notification for team charting
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');

        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 22 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 22 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Arahan Kerja Operasi telah berjaya Dimuat Naik ??';

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;
} else if ($status == "10") {
    // Extract the values from the JSON data
    $systemId = $POST['sid'];
    $reportType = $POST['rt'];
    $timestamp = date('Y-m-d H:i:s', time()); // current timestamp

    // Check if calendar already have ctrl_site_report value
    $query = "SELECT id, ctrl_site_report_id AS ctrl_id, authority_id, \"start\" FROM public.flw_calendars WHERE system_id = :systemId AND report_type = :reportType GROUP BY id, ctrl_site_report_id";
    $stmt = $conn->prepare($query);

    // Bind Parameter
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':reportType', $reportType);

    $stmt->execute();

    // Fetch the rows from the query result as an associative array
    $ongoingCals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $ctrlIds = [];
    $flwCalIds = [];
    foreach ($ongoingCals as $ctrlIdCheck) {
        $flwCalIds[] = $ctrlIdCheck['id'];
        if ($ctrlIdCheck['ctrl_id'] !== null) {
            $ctrlIds[] = $ctrlIdCheck['ctrl_id'];
        }

    }

    $ongoingCtrlId = null;

    if (count($ctrlIds) > 0) {
        // Remove duplicated ids
        $ctrlIds = array_unique($ctrlIds);
        // Execute a SELECT query
        $query = "SELECT id FROM ctrl_site_report WHERE id IN (" . implode(',', $ctrlIds) . ") AND completed = false LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $ongoingCtrlId = $stmt->fetchColumn();
    }

    // Execute a SELECT query
    $query = "SELECT COUNT(report_type) AS log FROM ctrl_site_report WHERE system_id = :systemId AND report_type = :reportType";
    $result = $conn->prepare($query);

    // Bind parameter
    $result->bindParam(':systemId', $systemId);
    $result->bindParam(':reportType', $reportType);

    // Execute the query
    $result->execute();

    $row = $result->fetch(PDO::FETCH_ASSOC);
    $log = $row['log'];

    if ($ongoingCtrlId == null && count($flwCalIds) > 0) {

        $check = $conn->prepare("SELECT id, system_id, completed FROM ctrl_site_report WHERE system_id = :systemId ORDER BY id DESC LIMIT 1");
        $check->bindParam(':systemId', $systemId);
        $check->execute();
        $checkRow = $check->fetch(PDO::FETCH_ASSOC);
        if (!$checkRow || $checkRow['completed'] == true) {
            // Increse log by 1
            $log = $row['log'] + 1;

            // Insert new entry to ctrl_site_report
            $query = "INSERT INTO ctrl_site_report (system_id, report_type, site_visit_no, created_at, site_officer) VALUES (:systemId, :type , :runNo , :createdAt, :user) RETURNING id";
            $result = $conn->prepare($query);

            // Bind Parameters
            $result->bindParam(':systemId', $systemId);
            $result->bindParam(':type', $reportType);
            $result->bindParam(':runNo', $log);
            $result->bindParam(':createdAt', $timestamp);
            $result->bindParam(':user', $username);

            // Execute
            $result->execute();

            $ongoingCtrlId = $result->fetchColumn();
        } else {
            $query = "SELECT id FROM ctrl_site_report WHERE system_id = :systemId AND completed = false";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();
            $ongoingCtrlId = $stmt->fetchColumn();
        }
    }

    $calendarAuthIds = [];
    if (count($flwCalIds) > 0) {
        $query = "UPDATE flw_calendars SET ctrl_site_report_id = :ongoingCtrlId WHERE id IN (" . implode(',', $flwCalIds) . ") AND (ctrl_site_report_id = :ongoingCtrlId OR ctrl_site_report_id IS NULL) RETURNING authority_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ongoingCtrlId', $ongoingCtrlId);
        $stmt->execute();
        $calendarAuthIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'authority_id');
    }

    $flwReports = [];
    $query = "SELECT id, authority_id FROM flw_appl_reports WHERE ctrl_site_report_id = :ongoingCtrlId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ongoingCtrlId', $ongoingCtrlId);
    $stmt->execute();
    $flwReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($flwReports) > 0) {
        // Extract authority_id values from arrays $a and $b
        $authorityIdsCal = array_map(function ($item) {
            return $item;
        }, $calendarAuthIds);

        $authorityIdsRep = array_map(function ($item) {
            return $item['authority_id'];
        }, $flwReports);

        // Find common authority_ids
        $bothArrays = array_intersect($authorityIdsCal, $authorityIdsRep);

        // Find authority_ids present in $a only
        $calOnly = array_diff($authorityIdsCal, $bothArrays);

        // Find authority_ids present in $b only
        $repOnly = array_diff($authorityIdsRep, $bothArrays);
    } else {
        $bothArrays = [];
        $calOnly = $calendarAuthIds;
        $repOnly = [];
    }

    // Filter reports collection by authority_ids present
    $delReportIds = [];
    foreach ($repOnly as $authorityIdToFind) {
        $foundId = null;

        foreach ($flwReports as $item) {
            if ($item['authority_id'] == $authorityIdToFind) {
                $foundId = $item['id'];
                break; // Stop iterating once a match is found
            }
        }

        if ($foundId !== null) {
            $delReportIds[$authorityIdToFind] = $foundId;
        } else {
            $delReportIds[$authorityIdToFind] = null;
        }
    }

    // Process report and authority to be deleted
    foreach ($delReportIds as $authorityId => $id) {
        if ($id !== null) {
            // Process report to be discard here
            $query = "DELETE FROM flw_appl_reports WHERE id = :id AND authority_id = :authorityId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':authorityId', $authorityId);
            $stmt->execute();

            // Delete flow statuses
            $deleteFlow = $flow->deleteAuthorityFlow($systemId, $authorityId);
        }
    }

    // Filter authority to be added to report and add new flow entry
    // Declare report reference number here
    if ($log < 10) {
        $logNo = "0" . $log;
    } else {
        $logNo = $log;
    }
    $reportRef = "LT/$reportType/$logNo";
    $flwStatus = 11;

    $addReportIds = [];
    foreach ($calOnly as $authorityIdToFind) {
        $newReportFlow = $flow->addRecordFlow($systemId, 'BF', $flwStatus, $authorityIdToFind);
        $foundIndex = null;

        foreach ($ongoingCals as $index => $item) {
            if ($item['authority_id'] == $authorityIdToFind) {
                $foundIndex = $index;
                break; // Stop iterating once a match is found
            }
        }

        if ($foundIndex !== null) {
            $addReportIds[] = $ongoingCals[$foundIndex];
        }
    }


    // Add new report
    foreach ($addReportIds as $addReportId) {
        //create new road_id for road_involve based on entry_road
        $getRoadId = $conn->prepare("SELECT gis_road_id FROM flw_appl_verifies WHERE system_id = :systemId");
        $getRoadId->bindParam(':systemId', $systemId);
        $getRoadId->execute();
        $getRoadIds = $getRoadId->fetch(PDO::FETCH_ASSOC);

        // Remove curly braces from the string
        $getRoadIds = trim($getRoadIds['gis_road_id'], '{}');

        // Split the string into an array based on commas
        $getIdArray = explode(',', $getRoadIds);

        // Convert each element to an integer
        $getRoadIdArray = array_map('intval', $getIdArray);

        // Now $idArray is an array of integers
        // var_dump($getRoadIdArray);
        $newRoadIds = [];
        foreach ($getRoadIdArray as $gisRoadId) {
            // var_dump($gisRoadId);
            $getRoadData = $conn->prepare("SELECT * FROM flw_gis_roads WHERE id = :roadId AND authority = :authority");
            $getRoadData->bindParam(':roadId', $gisRoadId);
            $getRoadData->bindParam(':authority', $addReportId['authority_id']);
            $getRoadData->execute();
            $roadData = $getRoadData->fetch(PDO::FETCH_ASSOC);

            if ($roadData) {
                $newRoad = $conn->prepare("INSERT INTO flw_pkd_roads (system_id, road_name, authority, created_at, latitude_start, longitude_start, latitude_end, longitude_end, gis_verify_date, created_by, districts, flw_gis_road_id) VALUES (:system_id, :road_name, :authority, :created_at, :latitude_start, :longitude_start, :latitude_end, :longitude_end, :gis_verify_date, :created_by, :districts, :flw_gis_road_id)");

                $newRoad->bindParam(':system_id', $roadData['system_id']);
                $newRoad->bindParam(':road_name', $roadData['road_name']);
                $newRoad->bindParam(':authority', $roadData['authority']);
                $newRoad->bindParam(':created_at', $timestamp);
                $newRoad->bindParam(':latitude_start', $roadData['latitude_start']);
                $newRoad->bindParam(':longitude_start', $roadData['longitude_start']);
                $newRoad->bindParam(':latitude_end', $roadData['latitude_end']);
                $newRoad->bindParam(':longitude_end', $roadData['longitude_end']);
                $newRoad->bindParam(':gis_verify_date', $roadData['gis_verify_date']);
                $newRoad->bindParam(':created_by', $username);
                $newRoad->bindParam(':districts', $roadData['districts']);
                $newRoad->bindParam(':flw_gis_road_id', $roadData['id']);

                $newRoad->execute();

                $newRoadId = $conn->lastInsertId();

                $newRoadIds[] = $newRoadId;

                if (!empty ($roadData['flw_appl_road_id'])) {
                    $getApplData = $conn->prepare("SELECT * FROM flw_appl_roads WHERE id = :roadId");
                    $getApplData->bindParam(':roadId', $roadData['flw_appl_road_id']);
                    $getApplData->execute();
                    $applData = $getApplData->fetch(PDO::FETCH_ASSOC);


                    $updateRoad = $conn->prepare("UPDATE flw_pkd_roads SET road_length = :roadLength, method = :method WHERE id = :roadId");
                    $updateRoad->bindParam(':roadLength', $applData['road_length']);
                    $updateRoad->bindParam(':method', $applData['method']);
                    $updateRoad->bindParam(':roadId', $newRoadId);
                    $updateRoad->execute();
                }


            }


        }

        // var_dump($newRoadIds);

        $newRoadIds = '{' . implode(', ', $newRoadIds) . '}';

        // var_dump($newRoadIds);

        $query = "INSERT INTO flw_appl_reports (system_id, authority_id, created_timestamp, road_involved_id, sv_date_suggested, report_no, calendar_id, ctrl_site_report_id) VALUES
                (:systemId, :authorityId, :submitted, :road_involved_id, :startDate, :reportRef, :calendarId, :ctrlId)";
        $stmt = $conn->prepare($query);

        // Bind the values to the named parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authorityId', $addReportId['authority_id']);
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':road_involved_id', $newRoadIds);
        $stmt->bindParam(':startDate', $addReportId['start']);
        $stmt->bindParam(':reportRef', $reportRef);
        $stmt->bindParam(':calendarId', $addReportId['id']);
        $stmt->bindParam(':ctrlId', $ongoingCtrlId);

        // Execute the INSERT query
        $stmt->execute();

        // TODO - Should make api request for site visit date add and update to extercord here
        // Retrieve data from view_calendar view based on $systemId
        $query = "SELECT * FROM view_calendar WHERE id = :calendarId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':calendarId', $addReportId['id']);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($data as $row) {
            $item = [
                'id' => $row['id'],
                'tenant' => $system->App->tenant,
                'user' => $row['user_created'],
                'reference_no' => $row['reference_no'],
                'title' => $row['title'],
                'start' => $row['start'],
                'end' => $row['end'],
                'description' => $row['description'],
                'all_day' => $row['all_day'],
                'guests' => $row['guests'],
                'creator' => $row['creator'],
                'created_at' => $row['created_at'],
                'external_guest' => $row['external_guest'],
                'location' => $row['location'],
                'authority_name' => $row['authority_name'],
                'event_type' => $row['report_type'],
            ];

            $result[] = $item;
        }



        // Create an associative array with the variables
        $applData = array(
            "secret" => $system->App->secret,
            "calendar" => $result,
            "stage" => 1
        );

        // Convert the array to JSON
        $jsonData = json_encode($applData);

        // make api request to extercord
        $apiRequest = $Curl->request($ec_url.'/gateway/internal/calendar/' . $systemId, $jsonData);

        $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

        // Decode the response JSON
        $curlResult = json_decode($apiRequest['response']);

        // if ($curlResult->message === "success") {

        // }
    }

    // NOTE - Update Task Assignment
    $actualStatus = $flow->getCurrentFlow($systemId, "operation");
    // var_dump($actualStatus);
    if ($actualStatus->status == 10) {
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);
    }

    $refNo = Utilities::getRefNo($systemId);

    // Assign reportType string
    switch ($reportType) {
        case 1:
            $reportTypeStr = "Lawatan Tapak Awalan";
            break;
        case 2:
            $reportTypeStr = "Lawatan Tapak Bersama";
            break;
        case 3:
            $reportTypeStr = "Lawatan Tapak Berkala";
            break;
        default:
            $reportTypeStr = "Not a valid reportType";
    }

    foreach ($addReportIds as $addReportId) {
        $actualAuthStatus = $flow->getCurrentFlow($systemId, "operation", $addReportId['authority_id']);
        if ($actualAuthStatus->status == 10) {
            $taskAssignment->create($systemId, $flwStatus, $addReportId['authority_id']);
        }

        // authority name
        $stmt = $conn->prepare('SELECT sort_name FROM ls_authorities WHERE id = :authId LIMIT 1');
        $stmt->bindParam(':authId', $addReportId['authority_id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $authority_name = $row['sort_name'];

        $detailsNote = 'Tetapan Tarikh Lawatan Tapak: ' . $reportTypeStr . ' bagi permohonan ' . $refNo ;
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $detailsNote)) {
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, reportType: $reportTypeStr, refNo: $refNo, item: $authority_name);
            // NOTE - send telegram notification for team charting
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
        };

        $chronoId = $chronology->create($username, $systemId, $status, 'notes', $addReportId['authority_id']);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id, authority_id ) VALUES (:systemId, :details, :created, :username, :chronology_id, :authority_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $detailsNote);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->bindParam(':authority_id', $addReportId['authority_id']);
        $stmt->execute();
    }

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah telah Menetapkan Tarikh Lawatan Tapak: ' . $reportTypeStr . ' bagi permohonan ' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 10
    //nextstep for opearation
    if ($actualStatus->status == 10) {
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);
    }

    // $details = 'Tetapan Tarikh Lawatan Tapak: ' . $reportTypeStr . ' bagi permohonan ' . $refNo;
    // // NOTE - Insert Project Changelog
    // if ($changelog->projectActivity($systemId, $status, $details)) {
    //     $telegramMsg = $telegram->getMessageByFlow($flwStatus, reportType: $reportTypeStr, refNo: $refNo);
    //     // NOTE - send telegram notification for team charting
    //     $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
    //     $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
    // }
    // ;

    // Finally, return a JSON
    http_response_code(200);
    // Finally, return a JSON
    echo json_encode([
        "systemID" => $systemId,
        "success" => true,
        "message" => "success",
        "status" => 200
    ]);
} else if ($status == "11") {


    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());

    // get the api request url for external system update
    // $extSysId = 1;
    // $projectStatus = 11;
    // $requestUrl = getRequestUrl($PDO, $extSysId, $projectStatus);
    // $apiRequestSecret = getApiRequestSecret($PDO, $extSysId);

    // get submission_code
    // $submissionCode = getSubmissionCode($PDO, $systemId);

    // $variableValues = $variableValues = array(
    //     'flw_appl_entries.submission_code' => $submissionCode
    // );

    // $requestUrl = supplyVariableValues($requestUrl, $variableValues);

    // countVariablesChecker($requestUrl);

    // prepare the requestPayload data
    // Retrieve data from entry_on_calendar view based on $systemId
    // $query = "SELECT * FROM entry_on_calendar WHERE system_id = :systemId";
    // $stmt = $conn->prepare($query);
    // $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
    // $stmt->execute();
    // $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data as JSON with column translations
    // $result = []; // used for apiRequestPayload
    // $contactId = []; // used to assign attendees
    // $roadId = []; // used to assign road list
    // foreach ($data as $row) {
    // Start::Update verified_at date
    $calUpdateQuery = "UPDATE flw_calendars SET verified_at = :timestamp, verify = :verify WHERE system_id = :system_id AND authority_id = :authority_id";
    $stmt = $conn->prepare($calUpdateQuery);
    $stmt->bindParam(':system_id', $systemId);
    $stmt->bindParam(':authority_id', $authorityId);
    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
    $stmt->bindValue(':verify', true, PDO::PARAM_BOOL);
    $stmt->execute();

    // TODO - Should make api request for site visit date add and update to extercord here
    // Retrieve data from view_calendar view based on $systemId
    $query = "SELECT * FROM view_calendar WHERE system_id = :system_id AND authority_id = :authority_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':system_id', $systemId);
    $stmt->bindParam(':authority_id', $authorityId);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    foreach ($data as $row) {
        $item = [
            'id' => $row['id'],
            'tenant' => $system->App->tenant,
            'user' => $row['user_created'],
            'reference_no' => $row['reference_no'],
            'verify' => $row['verify'],
            'verified_at' => $row['verified_at'],
            'start' => $row['start'],
            'end' => $row['end'],
            'location' => $row['location'],
            'authority_name' => $row['authority_name'],
        ];

        $result[] = $item;
    }



    // Create an associative array with the variables
    $applData = array(
        "secret" => $system->App->secret,
        "calendar" => $result,
        "stage" => 1
    );
    // var_dump($applData);
    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/calendar/' . $systemId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

    // Decode the response JSON
    $curlResult = json_decode($apiRequest['response']);

    // if ($curlResult->message === "success") {

    // }

    // End::Update verified_at date
    // $item = [
    //     'id' => $row['id'],
    //     'tenant' => $extTenantId,
    //     'user' => $row['user_created'],
    //     'reference_no' => $row['reference_no'],
    //     'title' => $row['title'],
    //     'start' => $row['start'],
    //     'end' => $row['end'],
    //     'description' => $row['description'],
    //     'all_day' => $row['all_day'],
    //     'guests' => $row['guests'],
    //     'creator' => $row['creator'],
    //     'verified_at' => $timestamp,
    //     'verify' => true,
    //     'external_guest' => $row['external_guest'],
    //     'location' => $row['location'],
    //     'authority_id' => $row['authority_id'],
    //     'authority_name' => $row['authority_name'],
    //     'event_type' => $row['report_type'],
    // ];

    // assign payload data to result
    //     $result[] = $item;

    //     // assign contactId and roadId
    //     $contactId[] = array('calendar_id' => $row['id'], 'contact_id' => explode(',', str_replace(array('{', '}'), '', $row['contact_id'])), 'authority_id' => $row['authority_id'], 'road_id' => explode(',', str_replace(array('{', '}'), '', $row['list_road_id'])));

    //     // assign roadId
    //     // $roadId[] = array('calendar_id'=>$row['id'], 'authority_id' => $row['authority_id'], 'road_id'=>explode(',', str_replace(array('{', '}'), '', $row['list_road_id'])));
    // }

    // $requestPayload = json_encode(array("secret" => $apiRequestSecret, "calendar" => $result, "stage" => 1));

    // // makeApiRequest($PDO, $requestUrl, $requestPayload,  $extSysId, getActionId($PDO, $projectStatus));
    // makeApiRequest($PDO, $requestUrl, $requestPayload, $extSysId, getActionId($PDO, $projectStatus), 'PUT');

    // update signature_id in flw_appl_reports
    // create signature entry for each contactId
    //ANCHOR : need to check
    // foreach ($contactId as $guest) {
    //     $signatureId = []; // used to assign flw_appl_reports.signature_id
    //     $newRoadId = [];
    //     foreach ($guest['contact_id'] as $contactSign) {
    //         $intContactId = intval($contactSign);
    //         $contactType = 4; // hardcoded for now // 2 - pemohon, 1 - koridor, 3 - authority

    //         $query = "INSERT INTO flw_report_signature (contact_id, type) VALUES (:contactId, :contactType) RETURNING id";
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':contactId', $intContactId, PDO::PARAM_INT);
    //         $stmt->bindParam(':contactType', $contactType, PDO::PARAM_INT);
    //         $stmt->execute();
    //         $generatedId = $stmt->fetchColumn();

    //         // assign to $signatureId
    //         $signatureId[] = $generatedId;

    //         // var_dump($generatedId);
    //     }

    //     //ANCHOR : need to check
    //     foreach ($guest['road_id'] as $roadIdSelect) {
    //         $intRoadId = intval($roadIdSelect);

    //         $query = "SELECT * FROM flw_appl_roads WHERE id = :intRoadId LIMIT 1;";
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':intRoadId', $intRoadId, PDO::PARAM_INT);
    //         $stmt->execute();
    //         $data = $stmt->fetch(PDO::FETCH_ASSOC);

    //         $query = "INSERT INTO flw_appl_roads (system_id, authority, road_name, road_length, method, created_at) VALUES (:systemId, :authority, :roadName, :roadLength, :method, :timestamp) RETURNING id";
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
    //         $stmt->bindParam(':authority', $guest['authority_id'], PDO::PARAM_INT);
    //         $stmt->bindParam(':roadName', $data['road_name'], PDO::PARAM_STR);
    //         $stmt->bindParam(':roadLength', $data['road_length'], PDO::PARAM_STR);
    //         $stmt->bindParam(':method', $data['method'], PDO::PARAM_STR);
    //         $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
    //         $stmt->execute();

    //         $newRoadId[] = $stmt->fetchColumn();
    //     }

    //     // Get the current Unix timestamp in milliseconds
    //     // $timestamp = date('Y-m-d H:i:s', time());

    //     // update to flw_appl_reports
    //     $signatureString = '{' . implode(',', $signatureId) . '}';
    //     $newRoadIdStr = '{' . implode(',', $newRoadId) . '}';
    //     $query = "UPDATE flw_appl_reports SET signature_id = :signatureId, road_involved_id = :newRoadIdStr, updated_timestamp = :timestamp WHERE system_id = :systemId AND calendar_id = :calendarId";
    //     $stmt = $conn->prepare($query);
    //     $stmt->bindParam(':signatureId', $signatureString, PDO::PARAM_STR);
    //     $stmt->bindParam(':newRoadIdStr', $newRoadIdStr, PDO::PARAM_STR);
    //     $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
    //     $stmt->bindParam(':calendarId', $guest['calendar_id'], PDO::PARAM_INT);
    //     $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
    //     $stmt->execute();
    // }

    // create new road list to be included in report as initial road list
    // foreach ($roadId as $road) {
    //     $newRoadId = [];
    //     foreach ($road['road_id'] as $roadIdSelect) {
    //         $intRoadId = intval($roadIdSelect);

    //         $query = "SELECT * FROM public.flw_appl_roads WHERE id = :intRoadId LIMIT 1;";
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':intRoadId', $intRoadId, PDO::PARAM_INT);
    //         $stmt->execute();
    //         $data = $stmt->fetch(PDO::FETCH_ASSOC);

    //         $query = "INSERT INTO flw_appl_roads (system_id, authority, road_name, road_length, method, created_at) VALUES (:systemId, :authority, :roadName, :roadLength, :method, :timestamp) RETURNING id";
    //         $stmt = $conn->prepare($query);
    //         $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
    //         $stmt->bindParam(':authority', $data['authority_id'], PDO::PARAM_INT);
    //         $stmt->bindParam(':roadName', $data['road_name'], PDO::PARAM_INT);
    //         $stmt->bindParam(':roadLength', $data['road_length'], PDO::PARAM_INT);
    //         $stmt->bindParam(':method', $data['method'], PDO::PARAM_INT);
    //         $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
    //         $stmt->execute();

    //         $newRoadId[] = $stmt->fetchColumn();
    //     }

    //     // update to flw_appl_reports
    //     $newRoadIdStr = '{' . implode(',', $newRoadId) . '}';


    // }
    // exit;

    // Update Status Application // (2 new application -> 4 work order submitted)
    // $projectStatusNew = 13;
    // $statusUpdateResult = updateProjectStatus($conn, $systemId, $projectStatus, $projectStatusNew);



    $flwStatus = 13;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status, $authorityId);
    $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

    // authority name
    $stmt = $conn->prepare('SELECT sort_name FROM ls_authorities WHERE id = :authId LIMIT 1');
    $stmt->bindParam(':authId', $authorityId);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $authority_name = $row['sort_name'];

    $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
    $referenceNo->bindParam(':systemId', $systemId);
    $referenceNo->execute();
    $refNo = $referenceNo->fetchColumn();

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah mengesahkan tarikh lawatan tapak bagi' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 9
    //nextstep for finance
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

    $details = 'Tarikh lawatan tapak telah disahkan bagi ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {
        // declare telegram notification string
        // $telegramMsg = "Tarikh lawatan tapak telah disahkan. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, item: $authority_name, refNo: $refNo);
        // NOTE - send telegram notification for team charting
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');

        //whatsapp notification for PBM/PBT involved
        $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'note', $authorityId);

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id, authority_id ) VALUES (:systemId, :details, :created, :username, :chronology_id, :authority_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->bindParam(':authority_id', $authorityId);
    $stmt->execute();

    // $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 22 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 22 ORDER BY id DESC LIMIT 1)";

    // $stmt2 = $conn->prepare($query2);
    // $stmt2->bindParam(':systemId', $systemId);
    // $stmt2->bindParam(':chronology_id', $chronoId);
    // $stmt2->execute();




    // json_encode
    $json = array(
        "message" => 'Maklumat Lawatan Tapak Telah Berjaya disahkan.??',
        "systemID" => $systemId,
        "status" => 200
        // "statusUpdate" => $statusUpdateResult,
        // "changeLogUpdate" => $changeLogUpdateResult
    );

    echo json_encode($json);

    // Close the database connection
    $conn = null;


    // } else if ($status == "12") {

    // } else if ($status == "13") {

    // } else if ($status == "14") {
//     // submit api here
//     $systemId = $POST['sid'];
//     $reportNo = $POST['rn'];
//     $authId = $POST['aid'];
//     $timestamp = date('Y-m-d H:i:s', time());

    //     // Connect to the database using PDO
//     $conn = $db->createConnection();

    //     // assign data to be processed
//     $requestData = $POST['data'];
//     $notes = $requestData['actionNotes'];

    //     // checklist check
//     $amendPlan = 0;
//     $pilExclusion = 0;
//     $privateRoad = 0;
//     $overLapping = 0;
//     $overlapDetails = "";
//     if (isset($requestData['amend-plan'])) {
//         $amendPlan = 1;
//     }
//     if (isset($requestData['wl-exclusion'])) {
//         $pilExclusion = 1;
//     }
//     if (isset($requestData['overlapping'])) {
//         $overLapping = 1;
//         $overlapDetails = isset($requestData['overlapDetails'])?$requestData['overlapDetails']:'';
//     }
//     if (isset($requestData['private-road'])) {
//         $privateRoad = 1;
//     }

    //     // update checklist data
//     $query = "UPDATE flw_appl_reports SET wl_exclusion_status = :exclusionStatus, amend_status = :amendPlan, overlap_status = :overLapping, overlap_details = :overlapDetails, private_road_involve = :privateRoad, updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo RETURNING id, report_trigger_id, road_involved_id;";
//     $stmt = $conn->prepare($query);
//     $stmt->bindParam(':exclusionStatus', $pilExclusion, PDO::PARAM_INT);
//     $stmt->bindParam(':amendPlan', $amendPlan, PDO::PARAM_INT);
//     $stmt->bindParam(':overLapping', $overLapping, PDO::PARAM_INT);
//     $stmt->bindParam(':privateRoad', $privateRoad, PDO::PARAM_INT);
//     $stmt->bindParam(':overlapDetails', $overlapDetails, PDO::PARAM_STR);
//     $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
//     $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
//     $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
//     $stmt->bindParam(':timestamp', $timestamp);
//     $stmt->execute();

    //     // Retrieve the updated data (for PostgreSQL or similar databases)
//     $updatedData = $stmt->fetch(PDO::FETCH_ASSOC);

    //     // Extract the specific values if needed
//     $reportId = $updatedData['id'];
//     $reportTriggerId = $updatedData['report_trigger_id'];
//     $roadInvolvedId = $updatedData['road_involved_id'];

    //     // insert into flw_appl_notes
//     $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authorityId)");
//     $stmtNote->bindParam(':systemId', $systemId);
//     $stmtNote->bindParam(':notes', $notes);
//     $stmtNote->bindParam(':created', $timestamp);
//     $stmtNote->bindParam(':authorityId', $authId);
//     $stmtNote->execute();

    //     // Update status_appl_reports entry by $reportTriggerId
//     $query = "UPDATE status_appl_reports SET report_submit = :submitStatus, submit_date = :timestamp WHERE id = :reportTriggerId;";
//     $stmt = $conn->prepare($query);
//     $stmt->bindValue(':submitStatus', 1, PDO::PARAM_INT);
//     $stmt->bindValue(':reportTriggerId', $reportTriggerId, PDO::PARAM_INT);
//     $stmt->bindValue(':timestamp', $timestamp, PDO::PARAM_INT);
//     $stmt->execute();

    //     // Check if the entry already exists
//     $checkQuery = "SELECT COUNT(*) FROM public.ctrl_authorities WHERE system_id = :systemId AND flw_appl_report_id = :reportId AND authority_id = :authorityId";
//     $checkStmt = $conn->prepare($checkQuery);
//     $checkStmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
//     $checkStmt->bindParam(':reportId', $reportId, PDO::PARAM_INT);
//     $checkStmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
//     $checkStmt->execute();

    //     if ($checkStmt->fetchColumn() == 0) {
//         $authorityStatus = 14;
//         // Entry does not exist, so insert new authority table entry
//         $insertQuery = "INSERT INTO public.ctrl_authorities (system_id, flw_appl_report_id, road_involved, authority_id, authority_status, created_at) VALUES (:systemId, :reportId, :roadInvolved, :authorityId, :authorityStatus, :timestamp)";
//         $insertStmt = $conn->prepare($insertQuery);
//         $insertStmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
//         $insertStmt->bindParam(':reportId', $reportId, PDO::PARAM_INT);
//         $insertStmt->bindParam(':roadInvolved', $roadInvolvedId, PDO::PARAM_STR);
//         $insertStmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
//         $insertStmt->bindParam(':authorityStatus', $authorityStatus, PDO::PARAM_INT);
//         $insertStmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
//         $insertStmt->execute();
//     }

    //     // check if all status_appl_reports are submitted by system_id
//     $query = "SELECT status_appl_reports.report_submit, flw_appl_reports.authority_id FROM status_appl_reports LEFT JOIN flw_appl_reports ON flw_appl_reports.report_trigger_id = status_appl_reports.id WHERE status_appl_reports.system_id = :systemId;";
//     $stmt = $conn->prepare($query);
//     $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
//     $stmt->execute();
//     $reportSubmitCheck = $stmt->fetchAll();

    //     $allSubmitted = true; // Assume all are submitted

    //     foreach ($reportSubmitCheck as $row) {
//         if ($row['report_submit'] != 1) {
//             $allSubmitted = false; // If any value is not 1, set to false
//             break; // No need to continue checking
//         }
//     }

    //     $reportSubmitStatus = $allSubmitted; // Set the final status

    //     // change the project status if all of the reports are submitted
//     if ($reportSubmitStatus) {

    //         // get all authority for current systemId
//         $query = "SELECT id FROM ctrl_authorities WHERE system_id = :systemId";
//         $stmt = $conn->prepare($query);
//         $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
//         $stmt->execute();
//         $flwAuthorities = $stmt->fetchAll();

    //         $authorityStatus = 15;
//         foreach ($flwAuthorities as $row) {
//             $query = "UPDATE ctrl_authorities SET authority_status = :authorityStatus WHERE id = :authorityId";
//             $stmt = $conn->prepare($query);
//             $stmt->bindParam(':authorityStatus', $authorityStatus);
//             $stmt->bindParam(':authorityId', $row['id']);
//             $stmt->execute();
//         }

    //         $projectStatus = getApplMainStatus($systemId);
//         $projectStatusNew = 15;
//         $statusUpdateResult = updateProjectStatus($conn, $systemId, $projectStatus, $projectStatusNew);

    //         // TODO: Update changelog table
//         // set initial status of changeLogUpdateResult
//         $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

    //         // Update changelog if status update successful
//         if ($statusUpdateResult['result'] == true) {
//             // update changelog for all the authority
//             $changeLogCount = 0;
//             foreach ($reportSubmitCheck as $row) {
//                 $changeLogUpdateResult = insertSysRecordChangelog($conn, $systemId, $notes, $statusUpdateResult['new_status'], $row['authority_id'], $changeLogCount);
//             }
//         }
//     }

    //     $conn = null; // close the pdo connection

    //     $response = array(
//         "message" => 'Laporan Berjaya Dihantar.',
//         "data" => array(
//             "allSubmitted" => $reportSubmitStatus,
//             "systemId" => $systemId,
//             "reportNo" => $reportNo,
//             "authId" => $authId
//         ),
//         "check" => $requestData
//     );

    //     echo json_encode($response);
// } else if ($status == "15") {

} else if ($status == "27") {
    //total_quote
    // Get the user's identification from the request parameters
    $totalQuote = isset ($POST['total_quote']) ? $POST['total_quote'] : '';
    $quoteNotes = isset ($POST['notes']) ? $POST['notes'] : '';
    $udmCheck = isset ($POST['check_udm']) ? $POST['check_udm'] : '0';
    $tmpCheck = isset ($POST['check_tmp']) ? $POST['check_tmp'] : '0';
    $asBuiltCheck = isset ($POST['check_asbuilt']) ? $POST['check_asbuilt'] : '0';
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    $servicesInclude = [$udmCheck, $tmpCheck, $asBuiltCheck];

    $integerServicesInclude = array_map('intval', $servicesInclude);

    $stmtUrl = $conn->prepare('SELECT id,name, url, mime_type, size FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 10 ORDER BY id DESC LIMIT 1');
    $stmtUrl->bindParam(':systemId', $systemId);
    $stmtUrl->execute();
    $resultUrl = $stmtUrl->fetch(PDO::FETCH_ASSOC);

    if ($stmtUrl->rowCount() > 0) {
        $filename = $resultUrl['name'];
        $url = $resultUrl['url'];
        $mimeType = $resultUrl['mime_type'];
        $size = $resultUrl['size'];
    } else {
        $filename = '';
        $url = '';
        $mimeType = '';
        $size = '';
    }

    $subNotes = 'Sebut Harga telah Dihantar. Nota: ' . $quoteNotes;

    // Create an associative array with the variables
    $applData = array(
        "secret" => $system->App->secret,
        "submissions" => array(
            "quotation" => array(
                "file_name" => $filename,
                "file_size" => $size,
                "file_url" => $url,
                "mime_type" => $mimeType,
                "username" => $username,
                "stage" => 1,
                "notes" => $subNotes,
                "amount" => $totalQuote,
                "items" => $integerServicesInclude,
            )
        )
    );

    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/quotation/' . $systemId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);


    // Decode the response JSON
    $curlResult = json_decode($apiRequest['response']);


    if ($curlResult->message === "success") {
        // Prepare the query for update flw_appl_verifies
        $assign = "UPDATE flw_appl_verifies SET quotation_submit_date = :submitted, quotation_submitter = :username, quotation_total = :totalQuote, quotation_notes = :quotationNotes, quote_services_included = :servicesInclude  WHERE system_id = :systemId ";
        $data = $conn->prepare($assign);

        // Bind the parameters
        $data->bindParam(':submitted', $timestamp);
        $data->bindParam(':username', $username);
        $data->bindParam(':totalQuote', $totalQuote);
        $data->bindParam(':systemId', $systemId);
        $data->bindParam(':quotationNotes', $quoteNotes);
        // Convert the PHP array to a PostgreSQL array format
        $servicesIncludePGArray = "{" . implode(",", $servicesInclude) . "}";

        // Bind the servicesInclude parameter as a PostgreSQL array
        $data->bindParam(':servicesInclude', $servicesIncludePGArray, PDO::PARAM_STR);

        // Execute the query
        if ($data->execute()) {
            //tak perlu create, EC akan create
            // $flwStatus = 28;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            // $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Sebut Harga bagi permohonan ' . $refNo;
            $pages = 'task_finance';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 27
            //nextstep for finance
            $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");

            $details = 'Sebut Harga telah di muatnaik bagi permohonan ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {

                // declare telegram notification string
                $telegramMsg = "Sebut Harga telah di muatnaik.  \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');
            }
            ;

            $chronoId = $chronology->create($username, $systemId, $status, 'file');

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 10 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 10 ORDER BY id DESC LIMIT 1)";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':chronology_id', $chronoId);
            $stmt2->execute();

        } else {
            $errorInfo = $data->errorInfo();
            echo "Error executing query: " . $errorInfo[2];
        }

        $message = 'Sebut Harga telah Berjaya dimuat naik!??';
        // Finally, return a JSON
        http_response_code(200);
        // Finally, return a JSON
        echo json_encode([
            "success" => true,
            "message" => $message,
            "status" => 200
        ]);


    } else {
        // Finally, return a JSON
        http_response_code(500);
        echo json_encode(
            array(
                "message" => $curlResult->message,
                "status" => 500,
            )
        );
    }

    // Close the database connection
    $conn = null;

} else if ($status == "28") {
    //quote-check
    $timestamp = date('Y-m-d H:i:s', time());
    $systemId = $POST['systemId'];
    $verifySh = isset ($POST['attach_agreed']) ? $POST['attach_agreed'] : 0;
    $note = isset ($POST['notes']) ? $POST['notes'] : '';

    if ($verifySh == 1) {
        $is_approve = "true";
        //semakan persetujuan sebut harga
        //API to ExterCord
        $refNo = Utilities::getRefNo($systemId);

        $notes = 'Persetujuan Sebut Harga Disahkan. Nota: ' . $note;

        // Create an associative array with the variables
        $applData = array(
            "secret" => $system->App->secret,
            "submissions" => array(
                "quotation" => array(
                    "is_approved" => $is_approve,
                    "username" => $username,
                    "stage" => 1,
                    "notes" => $notes,
                )
            )
        );

        // Convert the array to JSON
        $jsonData = json_encode($applData);

        // make api request to extercord
        $apiRequest = $Curl->request($ec_url.'/gateway/internal/quotation/' . $systemId, $jsonData, 'PUT');

        $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

        // Decode the response JSON
        $curlResult = json_decode($apiRequest['response']);

        if ($curlResult->message === "success") {

            $stmtNote = $conn->prepare("UPDATE flw_appl_verifies SET quotation_verify = :approval,  quotation_verifier = :username, quotation_verify_date = :created, quotation_verify_notes = :notes WHERE system_id = :systemId");

            $stmtNote->bindParam(':systemId', $systemId);
            $stmtNote->bindParam(':approval', $is_approve);
            $stmtNote->bindParam(':username', $username);
            $stmtNote->bindParam(':notes', $note);
            $stmtNote->bindParam(':created', $timestamp);

            if ($stmtNote->execute()) {

                $flwStatus = 30;
                // NOTE - Update Task Assignment
                $taskAssignment->complete($username, $systemId, $status);
                // $taskAssignment->create($systemId, $flwStatus); // task assigned by pengesahan laporan

                // ANCHOR - Create task for authority when next status using authority status
                //find authority
                // $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

                // $dep = 'operation';
                // $auth->bindParam(':systemId', $systemId);
                // $auth->bindParam(':department', $dep);

                // $auth->execute();

                // $authIds = $auth->fetchAll(PDO::FETCH_OBJ);

                // foreach ($authIds as $authId) {
                //     $taskAssignment->create($systemId, $flwStatus, $authId->authority);
                // }



                //Record Activity
                $text = 'Pengguna ' . $username . ' telah mengesahkan Persetujuan Sebut Harga bagi permohonan ' . $refNo;
                $pages = 'task_finance';
                $changelog->userActivity($text, $pages);

                // NOTE - current status = 28
                //nextstep for finance
                $steps = 36;
                $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $steps);

                $details = 'Persetujuan Sebut Harga telah Disahkan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {

                    // declare telegram notification string
                    // $telegramMsg = "Persetujuan Sebut Harga Disahkan. Sila teruskan dengan penjanaan Ringkasan Projek.  \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                    $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                    $telegramMsg2 = "Persetujuan Sebut Harga telah disahkan. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";

                    // NOTE - send telegram notification for team account
                    $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                    $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
                    $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg2, 'html');
                    $telegramResponse = $telegram->sendMessage('role', 12, $telegramMsg2, 'html');
                    $telegramResponse = $telegram->sendMessage('role', 13, $telegramMsg2, 'html');
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

            }

            $message = 'Semakan Persetujuan Sebut Harga Disahkan ??';

            // Finally, return a JSON
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => $message,
                "status" => 200
            ]);

        } else {
            // Finally, return a JSON
            http_response_code(500);
            echo json_encode(
                array(
                    "message" => $curlResult->message,
                    "status" => 500
                )
            );
        }

    } else if ($verifySh == 0) {

    } else {

    }

    $conn = null;

} else if ($status == "29") {
    //total_quote
    // Get the user's identification from the request parameters
    $totalQuote = isset ($POST['total_quote']) ? $POST['total_quote'] : '';
    $quoteNotes = isset ($POST['notes']) ? $POST['notes'] : '';
    $udmCheck = isset ($POST['check_udm']) ? $POST['check_udm'] : '0';
    $tmpCheck = isset ($POST['check_tmp']) ? $POST['check_tmp'] : '0';
    $asBuiltCheck = isset ($POST['check_asbuilt']) ? $POST['check_asbuilt'] : '0';
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    $servicesInclude = [$udmCheck, $tmpCheck, $asBuiltCheck];

    $integerServicesInclude = array_map('intval', $servicesInclude);

    $stmt1 = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
    $stmt1->bindParam(':systemId', $systemId);

    $stmt1->execute();

    $noRefResult = $stmt1->fetch(PDO::FETCH_ASSOC);

    $refNo = $noRefResult['reference_no'];

    $stmtUrl = $conn->prepare('SELECT id,name, url, mime_type, size FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 10 ORDER BY id DESC LIMIT 1');
    $stmtUrl->bindParam(':systemId', $systemId);
    $stmtUrl->execute();
    $resultUrl = $stmtUrl->fetch(PDO::FETCH_ASSOC);

    if ($stmtUrl->rowCount() > 0) {
        $filename = $resultUrl['name'];
        $url = $resultUrl['url'];
        $mimeType = $resultUrl['mime_type'];
        $size = $resultUrl['size'];
    } else {
        $filename = '';
        $url = '';
        $mimeType = '';
        $size = '';
    }

    $subNotes = 'Pindaan Sebut Harga telah Dihantar. Nota: ' . $quoteNotes;

    // Create an associative array with the variables
    $applData = array(
        "secret" => $system->App->secret,
        "submissions" => array(
            "quotation" => array(
                "file_name" => $filename,
                "file_size" => $size,
                "file_url" => $url,
                "mime_type" => $mimeType,
                "username" => $username,
                "stage" => 1,
                "notes" => $subNotes,
                "amount" => $totalQuote,
                "items" => $integerServicesInclude,
            )
        )
    );

    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/quotation/' . $systemId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);


    // Decode the response JSON
    $curlResult = json_decode($apiRequest['response']);


    if ($curlResult->message === "success") {
        // Prepare the query for update flw_appl_verifies
        $assign = "UPDATE flw_appl_verifies SET quotation_submit_date = :submitted, quotation_submitter = :username, quotation_total = :totalQuote, quotation_notes = :quotationNotes, quote_services_included = :servicesInclude  WHERE system_id = :systemId ";
        $data = $conn->prepare($assign);

        // Bind the parameters
        $data->bindParam(':submitted', $timestamp);
        $data->bindParam(':username', $username);
        $data->bindParam(':totalQuote', $totalQuote);
        $data->bindParam(':systemId', $systemId);
        $data->bindParam(':quotationNotes', $quoteNotes);
        // Convert the PHP array to a PostgreSQL array format
        $servicesIncludePGArray = "{" . implode(",", $servicesInclude) . "}";

        // Bind the servicesInclude parameter as a PostgreSQL array
        $data->bindParam(':servicesInclude', $servicesIncludePGArray, PDO::PARAM_STR);

        // Execute the query
        if ($data->execute()) {
            //tak perlu create, EC akan create
            // $flwStatus = 28;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            // $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Pindaan Sebut Harga bagi permohonan ' . $refNo;
            $pages = 'task_finance';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 29
            //nextstep for finance
            $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");

            $details = 'Pindaan Sebut Harga telah di muatnaik bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {

                // declare telegram notification string
                $telegramMsg = "Pindaan Sebut Harga telah di muatnaik.  \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');
            }

            $chronoId = $chronology->create($username, $systemId, $status, 'file');

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 10 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 10 ORDER BY id DESC LIMIT 1)";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':chronology_id', $chronoId);
            $stmt2->execute();

        } else {
            $errorInfo = $data->errorInfo();
            echo "Error executing query: " . $errorInfo[2];
        }

        $message = 'Pindaan Sebut Harga telah Berjaya dimuat naik!??';
        // Finally, return a JSON
        http_response_code(200);
        // Finally, return a JSON
        echo json_encode([
            "success" => true,
            "message" => $message,
            "status" => 200
        ]);


    } else {
        // Finally, return a JSON
        http_response_code(500);
        echo json_encode(
            array(
                "message" => $curlResult->message,
                "status" => 500,
            )
        );
    }

    // Close the database connection
    $conn = null;

} else if ($status == "344") {
    //upload mbkil
    // Value
    $systemId = $POST['system-id'];

    // Query message for telegram
    $stmt2 = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");
    $stmt2->bindValue(':systemId', $systemId);
    $stmt2->execute();
    $result = $stmt2->fetch(PDO::FETCH_ASSOC);

    $created = date('Y-m-d H:i:s', time());
    $catatan = 'Nota: Surat Maklum Balas Kelulusan Izin Lalu telah dimuat naik';

    //TODO : change new function addnote
    // insert to flw_appl_notes
    $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
    $stmtNote->bindParam(':systemId', $systemId);
    $stmtNote->bindParam(':notes', $catatan);
    $stmtNote->bindParam(':created', $created);
    $stmtNote->execute();

    // Get the ID of the inserted row
    $notesId = $conn->lastInsertId();

    //changeStatus
    $indexFlow = $flow->goToNextFlow($systemId, 'operation', 'BF');
    $assignment = $taskAssignment->set($username, $systemId, $indexFlow);

    // Record Changelog
    $detail = 'Surat Maklum Balas Kelulusan Izin Lalu telah dimuat naik';

    $changelog->projectActivity($systemId, $indexFlow, $detail);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah muat naik Surat Maklum Balas Kelulusan Izin Lalu';
    $page = 'task';
    $changelog->userActivity($text, $page);

    $message = 'Surat Maklum Balas Izin Lalu Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode([
        "success" => true,
        "message" => $message,
    ]);

    // Close the database connection
    $conn = null;

} else if ($status == "333") {
    // pil-upload
    // START API TO EXTERCORD
    $stmt1 = $conn->prepare('SELECT flw_appl_entries.reference_no, flw_appl_entries.submission_code FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
    $stmt1->bindParam(':systemId', $systemId);

    $stmt1->execute();

    $result = $stmt1->fetch(PDO::FETCH_ASSOC);

    $subId = $result['submission_code'];


    $stmtUrl = $conn->prepare('SELECT id,details, url, mime_type, size, user_added FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 21 ORDER BY id DESC LIMIT 1');
    $stmtUrl->bindParam(':systemId', $systemId);
    $stmtUrl->execute();
    $resultUrl = $stmtUrl->fetch(PDO::FETCH_ASSOC);

    if ($stmtUrl->rowCount() > 0) {
        $filename = $resultUrl['details'];
        $url = $resultUrl['url'];
        $mimeType = $resultUrl['mime_type'];
        $size = $resultUrl['size'];
        $userAdded = $resultUrl['user_added'];
    } else {
        $filename = '';
        $url = '';
        $mimeType = '';
        $size = '';
        $userAdded = '';
    }

    $applData = array(
        "secret" => $system->App->secret,
        "submissions" => array(
            "attachment" => array(
                "user_id" => $userAdded,
                "type" => "PIL",
                "file_name" => $filename,
                "url" => $url,
                "size" => $size,
                "mime_type" => $mimeType,
                "stage" => 1,
                "notes" => "Pelan Izin Lalu Daripada Hasil Laporan Tapak Awalan"
            ),
        )
    );

    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/attachment/' . $subId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

    // Decode the response JSON
    $result = json_decode($apiRequest['response'], true);

    if ($result['message'] == "success") {


        $systemId = $POST['system-id'];
        $submitted = date('Y-m-d H:i:s', time());

        // Prepare the query for update flw_appl_verifies
        $assign = "UPDATE flw_appl_verifies SET pil_submitted_by = :pilAssign, pil_submitted_date = :submitted WHERE system_id = :systemId ";
        $data = $conn->prepare($assign);

        // Bind the parameters
        $data->bindParam(':pilAssign', $username);
        $data->bindParam(':submitted', $submitted);
        $data->bindParam(':systemId', $systemId);

        // Execute the query
        if (!$data->execute()) {
            $errorInfo = $data->errorInfo();
            echo "Error executing query: " . $errorInfo[2];
        } else {

            //changeStatus
            $indexFlow = $flow->goToNextFlow($systemId, 'geospatial', 'BF');
            $assignment = $taskAssignment->set($username, $systemId, $indexFlow);

            // Record Changelog
            $detail = 'Pelan Izin Lalu telah dimuat naik.';

            $changelog->projectActivity($systemId, $indexFlow, $detail);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah muat naik Pelan Izin Lalu';
            $page = 'task';
            $changelog->userActivity($text, $page);

            $message = 'Muat Naik PIL Berjaya ??';

            // Finally, return a JSON
            http_response_code(200);
            echo json_encode([
                "assignee" => $username,
                "message" => $message

            ]);
        }

    } else {
        // Finally, return a JSON
        http_response_code(500);
        echo json_encode(
            array(
                "message" => $result['message'],
            )
        );
    }
    // END API TO EXTERCORD

    // Close the database connection
    $conn = null;
} else if ($status == "30") {
    //RP
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //get ref no
    $refNo = Utilities::getRefNo($systemId);

    //get authority name

    $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
    $authorityName->bindParam(':authority_id', $authorityId);
    $authorityName->execute();

    $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

    $authName = $resultAuthName['sort_name'];

    $flwStatus = 31;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status, $authorityId);
    $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Memuat naik Ringkasan Projek dan Kiraaan Wang Cagaran bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 30
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

    $details = 'Ringkasan Projek dan Kiraaan Wang Cagaran telah Dimuat Naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

        // declare telegram notification string
        // $telegramMsg = "Ringkasan Projek dan Kiraan Wang Cagaran telah Dimuat Naik. Sila muat naik Surat Permohonan Kelulusan Izin Lalu \n\n<strong>?? No Rujukan : " . $systemId . "\n??Pihak Berkuasa : ". $authName. "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

        // NOTE - send telegram notification for team account
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':authorityId', $authorityId);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    //RP
    $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 26 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 26 ORDER BY id DESC LIMIT 1)";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':authorityId', $authorityId);
    $stmt2->bindParam(':chronology_id', $chronoId);
    $stmt2->execute();

    //KWC
    $query3 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 27 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 27 ORDER BY id DESC LIMIT 1)";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId);
    $stmt3->bindParam(':chronology_id', $chronoId);
    $stmt3->execute();

    //toast
    $message = 'Ringkasan Projek dan Kiraaan Wang Cagaran Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "31") {
    //SPKIL
    $dateLetter = isset ($POST['dt_auth_ltr_created']) ? $POST['dt_auth_ltr_created'] : '';
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //insert input data into DB
    // start select appl_date from flw_appl_entries
    $applicationDate = $conn->prepare('SELECT created_at FROM flw_appl_entries WHERE system_id = :systemId');
    $applicationDate->bindParam(':systemId', $systemId);
    $applicationDate->execute();
    $resultDate = $applicationDate->fetch(PDO::FETCH_ASSOC);
    $applDate = $resultDate['created_at'];

    // Insert into flw_wayleave
    $query = "INSERT INTO flw_wayleave ( system_id, dt_appl_ltr_created, dt_appl_ltr_received, dt_auth_ltr_created, created_at) VALUES (:systemId, :applDate, :applDate, :dateLetter, :created )";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':applDate', $applDate);
    $stmt->bindParam(':dateLetter', $dateLetter);
    $stmt->bindParam(':created', $timestamp);

    $stmt->execute();

    // Retrieve the ID of the last inserted row
    $lastInsertedId = $conn->lastInsertId();

    //TODO : check who need to create ctrl_authorities
    //Update wayleave_id into ctrl_authorities
    // $query2 = "UPDATE ctrl_authorities SET wayleave_id = :wayleaveId WHERE system_id = :systemId AND authority_id = :authorityId";

    $query2 = "INSERT INTO ctrl_authorities (system_id, created_at,authority_id, wayleave_id) VALUES (:systemId, :created_at, :authorityId, :wayleaveId)";

    $wayleaveId = '{' . $lastInsertedId . '}';
    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':wayleaveId', $wayleaveId);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':authorityId', $authorityId);
    $stmt2->bindParam(':created_at', $timestamp);

    if ($stmt2->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 32;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Kelulusan Izin Lalu bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 31
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Permohonan Kelulusan Izin Lalu telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Permohonan Kelulusan Izin Lalu telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Kelulusan Izin Lalu. \n\n<strong>?? No Rujukan : " . $systemId . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 25 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 25 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Surat Permohonan Kelulusan Izin Lalu Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "dateLetter" => $dateLetter,
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "32") {
    //ASPKIL
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    // dates
    $dateSend = isset ($POST['dt_auth_ltr_send']) ? $POST['dt_auth_ltr_send'] : '';

    // Select wayleave_id
    $query3 = "SELECT wayleave_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $wayleaveId = $result3['wayleave_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWayleave = str_replace(['{', '}'], '', $wayleaveId);

    //Update flw_wayeave
    $query4 = "UPDATE flw_wayleave SET dt_auth_ltr_send = :dateSend WHERE id = :wayleaveId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':wayleaveId', $idWayleave);

    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 33;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Bukti Penghantaran Surat Permohonan KIL bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 32
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Bukti Penghantaran Surat Permohonan KIL telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // $telegramMsg = "Bukti Penghantaran Surat Permohonan KIL telah dimuat naik. Sila muat naik Surat Kelulusan Izin Lalu. \n\n<strong>?? No Rujukan : " . $systemId . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //NOTE - update current Wayleave status to Client
            $applData = array(
                "secret" => $system->App->secret,
                "submissions" => array(
                    "project" => array(
                        "status" => $status,
                        "username" => $username,
                        "authority_name" => $authName,
                        "notes" => $notes,
                        "stage" => 1,
                    )
                )
            );

            // Convert the array to JSON
            $jsonData = json_encode($applData);

            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/status/' . $systemId, $jsonData, 'PUT');

            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

            // Decode the response JSON
            $curlResult = json_decode($apiRequest['response']);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //ASPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 20 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 20 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Bukti Penghantaran Surat Permohonan KIL Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "33") {
    //SKIL
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_appv_ltr']) ? $POST['dt_appv_ltr'] : '';
    $dateRecv = isset ($POST['dt_appv_ltr_received']) ? $POST['dt_appv_ltr_received'] : '';

    $wyStart = isset ($POST['dt_start']) ? $POST['dt_start'] : '';
    $wyEnd = isset ($POST['dt_end']) ? $POST['dt_end'] : '';

    // Select wayleave_id
    $query3 = "SELECT wayleave_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $wayleaveId = $result3['wayleave_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWayleave = str_replace(['{', '}'], '', $wayleaveId);

    //Update flw_wayeave
    $query4 = "UPDATE flw_wayleave SET dt_appv_ltr = :dateLetter, dt_appv_ltr_received = :dateRecv, dt_start = :wyStart, dt_end = :wyEnd WHERE id = :wayleaveId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateRecv', $dateRecv);
    $stmt4->bindParam(':wyStart', $wyStart);
    $stmt4->bindParam(':wyEnd', $wyEnd);
    $stmt4->bindParam(':wayleaveId', $idWayleave);

    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 34;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Kelulusan Izin Lalu bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 33
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        if ($tenant == 'KUP' || $tenant == 'KUDR') {
            // NOTE - Check all KIL already done
            if ($flow->CheckAllComplete($systemId, $NextStatus)) {
                $surveyStatus = 39;
                $surveyFlwStatus = 41;
                $taskAssignment->complete($username, $systemId, $surveyStatus);
                $taskAssignment->create($systemId, $surveyFlwStatus);
                $NextStatusSurvey = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $surveyFlwStatus, allRole: true);
            }
        }

        $details = 'Surat Kelulusan Izin Lalu telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // $telegramMsg = "Surat Kelulusan Izin Lalu telah dimuat naik. Sila muat naik Surat Maklumbalas Kelulusan Izin Lalu. \n\n<strong>?? No Rujukan : " . $systemId . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //NOTE - update current Wayleave status to Client
            $applData = array(
                "secret" => $system->App->secret,
                "submissions" => array(
                    "project" => array(
                        "status" => $status,
                        "username" => $username,
                        "authority_name" => $authName,
                        "notes" => $notes,
                        "stage" => 1,
                    )
                )
            );

            // Convert the array to JSON
            $jsonData = json_encode($applData);

            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/status/' . $systemId, $jsonData, 'PUT');

            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

            // Decode the response JSON
            $curlResult = json_decode($apiRequest['response']);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 18 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 18 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Surat Kelulusan Izin Lalu Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "34") {
    //MKIL
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_fb_ltr_created']) ? $POST['dt_fb_ltr_created'] : '';
    $dateSend = isset ($POST['dt_fb_ltr_send']) ? $POST['dt_fb_ltr_send'] : '';

    // Select wayleave_id
    $query3 = "SELECT wayleave_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $wayleaveId = $result3['wayleave_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWayleave = str_replace(['{', '}'], '', $wayleaveId);

    //Update flw_wayeave
    $query4 = "UPDATE flw_wayleave SET dt_fb_ltr_created = :dateLetter, dt_fb_ltr_send = :dateSend WHERE id = :wayleaveId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':wayleaveId', $idWayleave);

    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 36;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Maklum Balas Kelulusan Izin Lalu bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 33
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);
        // NOTE - Check all MKIL already done
        if ($flow->CheckAllComplete($systemId, $NextStatus)) {
            $taskAssignment->create($systemId, $flwStatus);
        }

        $details = 'Surat Maklum Balas Kelulusan Izin Lalu telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu telah dimuat naik. \n\n<strong>?? No Rujukan : " . $systemId . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');


            //NOTE - update current Wayleave status to Client
            $applData = array(
                "secret" => $system->App->secret,
                "submissions" => array(
                    "project" => array(
                        "status" => $status,
                        "username" => $username,
                        "authority_name" => $authName,
                        "notes" => $notes,
                        "stage" => 1,
                    )
                )
            );

            // Convert the array to JSON
            $jsonData = json_encode($applData);

            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/status/' . $systemId, $jsonData, 'PUT');

            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

            // Decode the response JSON
            $curlResult = json_decode($apiRequest['response']);

        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 19 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 19 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Surat Maklum Balas Kelulusan Izin Lalu Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200
        )
    );

    $conn = null;
} else if ($status == "36") {
    //ICPP
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $invDivision = isset ($POST['invoice_division']) ? $POST['invoice_division'] : '';
    $noInvois = isset ($POST['no_icpp']) ? $POST['no_icpp'] : '';
    $totalInvois = isset ($POST['total_icpp']) ? $POST['total_icpp'] : '';
    $invDate = isset ($POST['dt_inv']) ? $POST['dt_inv'] : '';

    //TODO : Postpone find version
    // $query = "SELECT COUNT(id) AS version FROM flw_invoices WHERE system_id = :systemId AND invoice_division = :invDivision AND is_paid = :is_paid";
    // $result = $conn->prepare($query);
    // $bol = 'false';
    // // Bind parameter
    // $result->bindParam(':systemId', $systemId);
    // $result->bindParam(':invDivision', $invDivision);
    // $result->bindParam(':is_paid', $bol);

    // Execute the query
    // $result->execute();

    // $row = $result->fetch(PDO::FETCH_ASSOC);
    // $version = $row['version'] + 1;
    $version = 1;
    $is_paid = "false";
    //input
    $query3 = "INSERT INTO flw_invoices (system_id, invoice_division, amount_invoices, submitter, submitter_at, created_at,version,is_paid,invoice_no,balance) VALUES (:systemId, :invoice_division, :amount_invoices, :submitter, :submitter_at, :created_at, :version,:is_paid, :invoice_no,:balance)";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':invoice_division', $invDivision);
    $stmt3->bindParam(':amount_invoices', $totalInvois);
    $stmt3->bindParam(':submitter', $username);
    $stmt3->bindParam(':submitter_at', $timestamp);
    $stmt3->bindParam(':created_at', $invDate);
    $stmt3->bindParam(':version', $version);
    $stmt3->bindParam(':is_paid', $is_paid);
    $stmt3->bindParam(':invoice_no', $noInvois);
    $stmt3->bindParam(':balance', $totalInvois);
    $stmt3->execute();

    $stmtUrl = $conn->prepare('SELECT id,name, url, mime_type, size FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 12 ORDER BY id DESC LIMIT 1');
    $stmtUrl->bindParam(':systemId', $systemId);
    $stmtUrl->execute();
    $resultUrl = $stmtUrl->fetch(PDO::FETCH_ASSOC);

    if ($stmtUrl->rowCount() > 0) {
        $filename = $resultUrl['name'];
        $url = $resultUrl['url'];
        $mimeType = $resultUrl['mime_type'];
        $size = $resultUrl['size'];
    } else {
        $filename = '';
        $url = '';
        $mimeType = '';
        $size = '';
    }

    $subNotes = 'Invois telah Dikeluarkan. Nota: ' . $notes;

    // Create an associative array with the variables
    $applData = array(
        "secret" => $system->App->secret,
        "submissions" => array(
            "invoice" => array(
                "file_name" => $filename,
                "file_size" => $size,
                "file_url" => $url,
                "mime_type" => $mimeType,
                "username" => $username,
                "stage" => 1,
                "notes" => $subNotes,
                "amount_inv" => $totalInvois,
                "inv_date" => $invDate,
                "no_invoice" => $noInvois,
                "inv_percent" => $invDivision
            )
        )
    );

    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/invoice/' . $systemId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);


    // Decode the response JSON
    $curlResult = json_decode($apiRequest['response']);


    if ($curlResult->message === "success") {

        $refNo = Utilities::getRefNo($systemId);

        // NOTE - Update Task Assignment

        // $queryCheck = "SELECT invoice_division FROM flw_invoices WHERE system_id = :systemId";
        // $checking = $conn->prepare($queryCheck);
        // $checking->bindParam(':systemId', $systemId);
        // $checking->execute();

        // $checkResult = $checking->fetchAll(PDO::FETCH_ASSOC);

        // $sumDivision = 0;

        // foreach ($checkResult as $check) {
        //     $invoiceDivision = intval($check['invoice_division']);

        //     // Accumulate the invoice_division values
        //     $sumDivision += $invoiceDivision;

        // }

        // // Check if the sum is equal to 100
        // if ($sumDivision >= 100) {
        $taskAssignment->complete($username, $systemId, $status);
        // } else {

        // }
        $flwStatus = 35;

        $taskAssignment->create($systemId, $flwStatus);


        $query = "SELECT id FROM flw_invoices WHERE system_id = :systemId";
        $checking = $conn->prepare($query);
        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        $checkPermit = $checking->fetchAll(PDO::FETCH_ASSOC);
        if(count($checkPermit) < 2){
            $flwStatusOperation = 67;
           $taskAssignment->create($systemId, $flwStatusOperation);
        }

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Invois Caj Perkhidmatan bagi permohonan ' . $refNo;
        $pages = 'task_finance';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 2
        //nextstep for finance
        $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus);

        $details = 'Invois Caj Perkhidmatan telah Dimuat naik bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {

            // declare telegram notification string
            // $telegramMsg = "Invois Caj Perkhidmatan telah Dimuat naik. Sila Muat Naik Bukti Pembayaran Invois \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
            if (count($checkPermit) < 2) {
                $telegramMsgOperation = $telegram->getMessageByFlow($flwStatusOperation, refNo: $refNo);

                // NOTE - send telegram notification for status operation
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsgOperation, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $flwStatusOperation, $telegramMsgOperation, 'html');
            }
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 12 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 12 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Invois Caj Perkhidmatan Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200
        )
    );

    $conn = null;

} else if ($status == "35") {
    //RCPP
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $invCategory = isset ($POST['invoice_category']) ? $POST['invoice_category'] : '';
    // $invDivision = isset($POST['invoice_division']) ? $POST['invoice_division'] : '';
    $paymentMethod = isset ($POST['payment_method']) ? $POST['payment_method'] : '';
    $totalPayment = isset ($POST['total_rcpp']) ? $POST['total_rcpp'] : '';
    $totalPayment = floatval($totalPayment);
    $payee = isset ($POST['payee']) ? $POST['payee'] : '';

    //get current balance
    $query2 = "SELECT id, balance, invoice_division FROM flw_invoices WHERE system_id = :systemId ORDER BY id ASC";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->execute();

    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $totalRows = count($result2);
    $totalDivision = 0;
    $overdue = false;
    foreach ($result2 as $index => $row) {
        // Access each row's data
        $balance = (int)$row['balance'];
        $rowId = (int)$row['id'];

        $totalDivision += $row['invoice_division'];
        if ($balance <= 0) {
            continue;
        } else if ($balance > 0 && $index != $totalRows - 1) {

            if($overdue){
                $balOverdue = $balOverdue - $balance;
            } else {
                $balOverdue = $totalPayment - $balance;
            }

            $is_paid = 'true';
            $query = "UPDATE flw_invoices SET is_paid = :is_paid, balance = 0 WHERE id = :rowId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':is_paid', $is_paid);
            $stmt->bindParam(':rowId', $rowId, PDO::PARAM_INT);
            $stmt->execute();
            $overdue = true;
        } else {

            if($overdue){
                $newBalance = $balance - $balOverdue;
            } else {
                $newBalance = $balance - $totalPayment;
            }


            $isPaid = ($newBalance != 0) ? 'false' : 'true';

            //append recepit id
            $query3 = "SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 13 ORDER BY id DESC LIMIT 1";

            $stmt3 = $conn->prepare($query3);
            $stmt3->bindParam(':systemId', $systemId);
            $stmt3->execute();

            $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

            $newReceiptId = $result3['id'];

            $query = "UPDATE flw_invoices SET is_paid = :is_paid, payee = :payee, payment_method = :payment_method, payment_at = :payment_at, category = :category, amount_paid = :amount_paid, balance = :balance, receipt_id = ARRAY_APPEND(receipt_id, :receipt_id) WHERE id = :rowId";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':is_paid', $isPaid);
            $stmt->bindParam(':payee', $payee);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':payment_at', $timestamp);
            $stmt->bindParam(':category', $invCategory);
            $stmt->bindParam(':amount_paid', $totalPayment, PDO::PARAM_INT);
            $stmt->bindParam(':balance', $newBalance);
            $stmt->bindParam(':receipt_id', $newReceiptId);
            $stmt->bindParam(':rowId', $rowId, PDO::PARAM_INT);
            $stmt->execute();
        }
    }


    $stmtUrl = $conn->prepare('SELECT id,name, url, mime_type, size FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 13 ORDER BY id DESC LIMIT 1');
    $stmtUrl->bindParam(':systemId', $systemId);
    $stmtUrl->execute();
    $resultUrl = $stmtUrl->fetch(PDO::FETCH_ASSOC);

    if ($stmtUrl->rowCount() > 0) {
        $filename = $resultUrl['name'];
        $url = $resultUrl['url'];
        $mimeType = $resultUrl['mime_type'];
        $size = $resultUrl['size'];
    } else {
        $filename = '';
        $url = '';
        $mimeType = '';
        $size = '';
    }

    $subNotes = 'Resit Bukti Pembayaran Invois Caj Perkhidmatan telah Disahkan terima. Nota: ' . $notes;

    // Create an associative array with the variables
    $applData = array(
        "secret" => $system->App->secret,
        "submissions" => array(
            "invoice" => array(
                "file_name" => $filename,
                "file_size" => $size,
                "file_url" => $url,
                "mime_type" => $mimeType,
                "username" => $username,
                "stage" => 1,
                "notes" => $subNotes,
                "inv_category" => $invCategory,
                "pay_method" => $paymentMethod,
                "total_pay" => $totalPayment,
                "payee" => $payee
            )
        )
    );

    // Convert the array to JSON
    $jsonData = json_encode($applData);

    // make api request to extercord
    $apiRequest = $Curl->request($ec_url.'/gateway/internal/invoice/' . $systemId, $jsonData);

    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);


    // Decode the response JSON
    $curlResult = json_decode($apiRequest['response']);


    if ($curlResult->message === "success") {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        $taskAssignment->complete($username, $systemId, $status);

        // Check if is_paid is true
        if ($isPaid === 'true') {

            if($totalDivision < 100){
                // NOTE - Update Task Assignment
                $flwStatus = 36;
                $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus);$taskAssignment->create($systemId, $flwStatus);
            } else {
                //ANCHOR - Continue when flow after invoice is created
                $flwStatus = 37;
                $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus);
            }

        } else {
            $taskAssignment->create($systemId, $status);
        }

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Resit Bukti Pembayaran Invois Caj Perkhidmatan bagi permohonan ' . $refNo;
        $pages = 'task_finance';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 2

        $details = 'Resit Bukti Pembayaran Invois Caj Perkhidmatan telah Dimuat naik bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {

            // declare telegram notification string
            $telegramMsg = "Resit Bukti Pembayaran Invois Caj Perkhidmatan telah Dimuat naik. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $status, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 13 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 13 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }

    $message = 'Resit Bukti Bayaran Invois Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "created" => $timestamp,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "37") {
    //payment-verified
    $timestamp = date('Y-m-d H:i:s', time());
    $systemId = $POST['systemId'];
    $verifyRcp = isset ($POST['verify_rcpp']) ? $POST['verify_rcpp'] : 0;

    if ($verifyRcp == 1) {

        //verify the charge
        $stmt2 = $conn->prepare('UPDATE flw_invoices SET is_paid = :verify WHERE system_id = :systemId AND id = (
            SELECT id
            FROM flw_invoices
            WHERE system_id = :systemId
            ORDER BY id DESC
            LIMIT 1
        )');

        $verify = true;
        $stmt2->bindParam(':verify', $verify);
        $stmt2->bindParam(':systemId', $systemId);

        if ($stmt2->execute()) {

            //get ref no
            $refNo = Utilities::getRefNo($systemId);

            //semakan permohonan permit
            $flwStatus = 68;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Mengesahkan Resit Caj Perkhidmatan bagi permohonan ' . $refNo;
            $pages = 'task_finance';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 2
            //nextstep for finance
            $nextSteps = 65;
            $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $nextSteps);


            $details = 'Resit Caj Perkhidmatan telah Disahkan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {

                // declare telegram notification string
                // $telegramMsg = "Caj Perkhidmatan telah Disahkan.Sila teruskan dengan menyemak Permohonan Permit. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'registration', $telegramMsg, 'html');
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

        }

        $message = 'Resit Caj Perkhidmatan Disahkan ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "message" => $message,
            "status" => 200
        ]);

        $conn = null;

    } else {
        //verify the charge
        $stmt2 = $conn->prepare('UPDATE flw_invoices SET is_paid = :verify WHERE system_id = :systemId AND id = (
            SELECT id
            FROM flw_invoices
            WHERE system_id = :systemId
            ORDER BY id DESC
            LIMIT 1
        )');

        $verify = false;
        $stmt2->bindParam(':verify', $verify);
        $stmt2->bindParam(':systemId', $systemId);

        if ($stmt2->execute()) {

            //get ref no
            $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
            $referenceNo->bindParam(':systemId', $systemId);
            $referenceNo->execute();
            $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
            $refNo = $resultRef['reference_no'];

            //semakan permohonan permit
            // $flwStatus = 68;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            // $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' Menolak Resit Caj Perkhidmatan bagi permohonan ' . $refNo;
            $pages = 'task_finance';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 2
            //nextstep for finance
            // $nextSteps = 65;
            // $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $nextSteps);


            $details = 'Resit Caj Perkhidmatan telah Disahkan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {

                // declare telegram notification string
                // $telegramMsg = "Caj Perkhidmatan telah Ditolak. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');
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

        }

        $message = 'Resit Caj Perkhidmatan Ditolak ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "message" => $message,
            "status" => 200
        ]);

        $conn = null;
    }

} else if ($status == "67") {
    //SRPKPK
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_appl_ltr_created']) ? $POST['dt_appl_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_appl_ltr_received']) ? $POST['dt_appl_ltr_received'] : '';

    //All authority Involved
    $query3 = "SELECT authority_id FROM ctrl_authorities WHERE system_id = :systemId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->execute();

    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $authInv = [];

    foreach ($result3 as $row3) {
        $authInv[] = $row3['authority_id'];
    }

    foreach ($authInv as $authId) {

        // Insert into flw_work_permit
        $query = "INSERT INTO flw_work_permit ( system_id, dt_appl_ltr_created, dt_appl_ltr_recieved) VALUES (:systemId, :dateLetter, :dateRecv )";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':dateLetter', $dateLetter);
        $stmt->bindParam(':dateRecv', $dateRecv);
        $stmt->execute();

        // Retrieve the ID of the last inserted row
        $lastInsertedId = $conn->lastInsertId();

        //Update wayleave_id into Flw_authorities
        $query2 = "UPDATE ctrl_authorities SET work_permit_id = :workPermitId WHERE system_id = :systemId AND authority_id = :authorityId";

        $workPermitId = '{' . $lastInsertedId . '}';
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':workPermitId', $workPermitId);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authId);
        $stmt2->execute();
    }

    //get ref no
    $refNo = Utilities::getRefNo($systemId);

    $flwStatus = 70;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    // ANCHOR - Create task for authority when next status using authority status
    //find authority
    $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

    $dep = 'operation';
    $auth->bindParam(':systemId', $systemId);
    $auth->bindParam(':department', $dep);

    $auth->execute();

    $authIds = $auth->fetchAll(PDO::FETCH_OBJ);

    foreach ($authIds as $authId) {
        $taskAssignment->create($systemId, $flwStatus, $authId->authority);
    }

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Permohonan Permit Kerja bagi' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 79
    //nextstep
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);

    $details = 'Dokumen Permohonan Permit Kerja telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // declare telegram notification string
        // $telegramMsg = "Dokumen Permohonan Permit Kerja telah dimuat naik. Sila muat naik Surat Permohonan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
        // NOTE - send telegram notification
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'file');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 28 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 28 ORDER BY id DESC LIMIT 1)";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':chronology_id', $chronoId);
    $stmt2->execute();



    //toast
    $message = "Dokumen Permohonan Permit Kerja telah berjaya dimuat naik.??";

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;

} else if ($status == "70") {
    //SPKPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_auth_ltr_created']) ? $POST['dt_auth_ltr_created'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit SET dt_auth_ltr_created = :dateLetter WHERE id = :workPermitId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':workPermitId', $idWorkPermit);
    if ($stmt4->execute()) {


        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 71;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Kelulusan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Permohonan Kelulusan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Permohonan Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Kelulusan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 39 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 39 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Surat Permohonan Kelulusan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "71") {
    //ASPKPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    // dates
    $dateSend = isset ($POST['dt_auth_ltr_send']) ? $POST['dt_auth_ltr_send'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit SET dt_auth_ltr_send = :dateSend WHERE id = :workPermitId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':workPermitId', $idWorkPermit);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 72;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Permohonan Kelulusan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Akuan Serahan Permohonan Kelulusan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Permohonan Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Surat Kelulusan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 40 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 40 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahan Permohonan Kelulusan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;


} else if ($status == "72") {
    //SKPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    // dates
    $dateLetter = isset ($POST['dt_appv_ltr_created']) ? $POST['dt_appv_ltr_created'] : '';
    $dateReceive = isset ($POST['dt_appv_received']) ? $POST['dt_appv_received'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit SET dt_appv_ltr_created = :dateLetter, dt_appv_received = :dateReceive WHERE id = :workPermitId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateReceive', $dateReceive);
    $stmt4->bindParam(':workPermitId', $idWorkPermit);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 73;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Kelulusan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Kelulusan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Perakuan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 41 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 41 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Surat Kelulusan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;


} else if ($status == "73") {
    //PPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_wp_recog_created']) ? $POST['dt_wp_recog_created'] : '';
    $dateNotify = isset ($POST['dt_wp_recog_notify']) ? $POST['dt_wp_recog_notify'] : '';
    $noRecog = isset ($POST['no_wp_recog']) ? $POST['no_wp_recog'] : '';
    $wdayStart = isset ($POST['dt_wday_strt']) ? $POST['dt_wday_strt'] : '';
    $wdayEnd = isset ($POST['dt_wday_end']) ? $POST['dt_wday_end'] : '';
    $wendStart = (isset ($POST['dt_wend_strt']) && $POST['dt_wend_strt'] !='') ? $POST['dt_wend_strt'] : NULL;
    $wendEnd = (isset ($POST['dt_wend_end']) && $POST['dt_wend_strt'] !='') ? $POST['dt_wend_end'] : NULL;

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit SET dt_wp_recog_created = :dateLetter, dt_wp_recog_notify = :dateNotify, no_wp_recog = :noRecog, dt_wday_strt = :wdayStart, dt_wday_end = :wdayEnd, dt_wend_strt = :wendStart, dt_wend_end = :wendEnd WHERE id = :workPermitId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateNotify', $dateNotify);
    $stmt4->bindParam(':noRecog', $noRecog);
    $stmt4->bindParam(':wdayStart', $wdayStart);
    $stmt4->bindParam(':wdayEnd', $wdayEnd);
    $stmt4->bindParam(':wendStart', $wendStart);
    $stmt4->bindParam(':wendEnd', $wendEnd);
    $stmt4->bindParam(':workPermitId', $idWorkPermit);

    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 74;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Perakuan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Perakuan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Perakuan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 42 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 42 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Perakuan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "74") {
    //ASPPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateSubmit = isset ($POST['dt_wp_recog_submit']) ? $POST['dt_wp_recog_submit'] : '';
    $receivedBy = isset ($POST['wp_recog_received_by']) ? $POST['wp_recog_received_by'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit SET dt_wp_recog_submit = :dateSubmit, wp_recog_received_by = :receivedBy WHERE id = :workPermitId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSubmit', $dateSubmit);
    $stmt4->bindParam(':receivedBy', $receivedBy);
    $stmt4->bindParam(':workPermitId', $idWorkPermit);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 79;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Perakuan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        if ($flow->CheckAllComplete($systemId, $NextStatus)) {
            $taskAssignment->create($systemId, $flwStatus);
        }

        $details = 'Akuan Serahan Perakuan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Perakuan Permit Kerja telah dimuat naik. Sila muat naik Notis Mula Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 43 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 43 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahan Perakuan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "79") {
    //NMK
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_ws_ltr_created']) ? $POST['dt_ws_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_ws_ltr_received']) ? $POST['dt_ws_ltr_received'] : '';
    $dateStart = isset ($POST['dt_ws']) ? $POST['dt_ws'] : '';

    //All authority Involved
    $query3 = "SELECT authority_id FROM ctrl_authorities WHERE system_id = :systemId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->execute();

    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $authInv = [];

    foreach ($result3 as $row3) {
        $authInv[] = $row3['authority_id'];
    }

    foreach ($authInv as $authId) {
        $query4 = "INSERT INTO flw_work_notice (system_id, dt_ws_ltr_created, dt_ws_ltr_received, dt_ws) VALUES (:systemId, :dateLetter, :dateRecv, :dateStart)";

        $stmt4 = $conn->prepare($query4);
        $stmt4->bindParam(':systemId', $systemId);
        $stmt4->bindParam(':dateLetter', $dateLetter);
        $stmt4->bindParam(':dateRecv', $dateRecv);
        $stmt4->bindParam(':dateStart', $dateStart);
        $stmt4->execute();

        // Retrieve the ID of the last inserted row
        $lastInsertedId = $conn->lastInsertId();

        //Update wayleave_id into Flw_authorities
        $query5 = "UPDATE ctrl_authorities SET work_notice_id = :workNoticeId WHERE system_id = :systemId AND authority_id = :authorityId";

        $stmt5 = $conn->prepare($query5);
        $stmt5->bindParam(':workNoticeId', $lastInsertedId, PDO::PARAM_INT);
        $stmt5->bindParam(':systemId', $systemId);
        $stmt5->bindParam(':authorityId', $authId, PDO::PARAM_INT);

        $stmt5->execute();
    }

    //get ref no
    // $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
    // $referenceNo->bindParam(':systemId', $systemId);
    // $referenceNo->execute();
    // $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
    // $refNo = $resultRef['reference_no'];
    $refNo = Utilities::getRefNo($systemId);

    $flwStatus = 80;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    // ANCHOR - Create task for authority when next status using authority status
    //find authority
    $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

    $dep = 'operation';
    $auth->bindParam(':systemId', $systemId);
    $auth->bindParam(':department', $dep);

    $auth->execute();

    $authIds = $auth->fetchAll(PDO::FETCH_OBJ);

    foreach ($authIds as $authId) {
        $taskAssignment->create($systemId, $flwStatus, $authId->authority);
    }

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah muat naik Notis Mula Kerja bagi' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 79
    //nextstep
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF");

    $details = 'Notis Mula Kerja telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // declare telegram notification string
        // $telegramMsg = "Notis Mula Kerja telah dimuat naik. Sila muat naik Surat Makluman Mula Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
        // NOTE - send telegram notification
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'file');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 48 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 48 ORDER BY id DESC LIMIT 1)";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':chronology_id', $chronoId);
    $stmt2->execute();



    //toast
    $message = "Notis Mula Kerja telah berjaya dimuat naik.??";

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;

} else if ($status == "80") {
    //SMMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_ws_auth_ltr_created']) ? $POST['dt_ws_auth_ltr_created'] : '';

    $query3 = "SELECT work_notice_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workNoticeId = $result3['work_notice_id'];

    $query4 = "UPDATE flw_work_notice SET dt_ws_auth_ltr_created = :dateLetter WHERE id = :workNoticeId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':workNoticeId', $workNoticeId);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 81;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Makluman Mula Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Makluman Mula Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Makluman Mula Kerja telah dimuat naik. Sila muat naik Akuan Serahan Makluman Mula Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 49 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 49 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Surat Makluman Mula Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;


} else if ($status == "81") {
    //ASMMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateSubmit = isset ($POST['dt_ws_auth_ltr_send']) ? $POST['dt_ws_auth_ltr_send'] : '';

    $query3 = "SELECT work_notice_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workNoticeId = $result3['work_notice_id'];

    $query4 = "UPDATE flw_work_notice SET dt_ws_auth_ltr_send = :dateSubmit WHERE id = :workNoticeId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSubmit', $dateSubmit);
    $stmt4->bindParam(':workNoticeId', $workNoticeId);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 90;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Makluman Mula Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);
        // if ($flow->CheckAllComplete($systemId, $NextStatus)) {
        //     $taskAssignment->create($systemId, $flwStatus);
        // }

        $details = 'Akuan Serahan Makluman Mula Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Makluman Mula Kerja telah dimuat naik. Sila muat naik Notis Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 50 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 50 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahan Makluman Mula Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "88") {
    //NSK
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_wf_ltr_created']) ? $POST['dt_wf_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_wf_ltr_received']) ? $POST['dt_wf_ltr_received'] : '';
    $dateFinish = isset ($POST['dt_wf']) ? $POST['dt_wf'] : '';

    //All id in work_notice Involved
    $query3 = "SELECT work_notice_id FROM ctrl_authorities WHERE system_id = :systemId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->execute();

    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $workNoticeId = [];

    foreach ($result3 as $row3) {
        $workNoticeId[] = $row3['work_notice_id'];
    }

    foreach ($workNoticeId as $Id) {
        $query4 = "UPDATE flw_work_notice SET dt_wf_ltr_created = :dateLetter, dt_wf_ltr_received = :dateRecv, dt_wf = :dateFinish WHERE id = :Id";

        $stmt4 = $conn->prepare($query4);
        $stmt4->bindParam(':dateLetter', $dateLetter);
        $stmt4->bindParam(':dateRecv', $dateRecv);
        $stmt4->bindParam(':dateFinish', $dateFinish);
        $stmt4->bindParam(':Id', $Id);
        $stmt4->execute();

    }

    //get ref no
    // $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
    // $referenceNo->bindParam(':systemId', $systemId);
    // $referenceNo->execute();
    // $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
    // $refNo = $resultRef['reference_no'];
    $refNo = Utilities::getRefNo($systemId);

    //trigger survey as build
    $triggerAsb = Survey::checkASB($systemId);

    if ($triggerAsb) {
        $asbStatus = 97;
        // NOTE - Update Task Assignment
        // $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $asbStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah muat naik Notis Siap Kerja bagi' . $refNo;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 79
        //nextstep
        // $NextStatusAsb = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $asbStatus);

        $details = 'Notis Siap Kerja telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            $telegramMsg = $telegram->getMessageByFlow($asbStatus, refNo: $refNo);
            // NOTE - send telegram notification
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');

        }
        ;
    }

    $flwStatus = 118;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah muat naik Notis Siap Kerja bagi' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 79
    //nextstep
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);

    $details = 'Notis Siap Kerja telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // $telegramMsg = "Notis Siap Kerja telah dimuat naik. Sila muat naik Dokumen Permohonan Sijil Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
        // NOTE - send telegram notification
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

        //whatsapp notification for PBM/PBT involved
        $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");
        $dep = 'operation';
        $auth->bindParam(':systemId', $systemId);
        $auth->bindParam(':department', $dep);

        $auth->execute();

        $authIds = $auth->fetchAll(PDO::FETCH_OBJ);

        foreach ($authIds as $authId) {
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authId->authority, $status);
        }
    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'file');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 52 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 52 ORDER BY id DESC LIMIT 1)";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':chronology_id', $chronoId);
    $stmt2->execute();



    //toast
    $message = "Notis Siap Kerja telah berjaya dimuat naik.??";

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;

} else if ($status == "118") {
    //SRPSSK
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_appl_ltr_created']) ? $POST['dt_appl_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_appl_ltr_received']) ? $POST['dt_appl_ltr_received'] : '';

    //All authority Involved
    $query3 = "SELECT authority_id FROM ctrl_authorities WHERE system_id = :systemId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->execute();

    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $authInv = [];

    foreach ($result3 as $row3) {
        $authInv[] = $row3['authority_id'];
    }

    foreach ($authInv as $authId) {
        // Insert into flw_work_finish
        $query = "INSERT INTO flw_work_finish ( system_id, dt_appl_ltr_created, dt_appl_ltr_received) VALUES (:systemId, :dateLetter, :dateRecv)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':dateLetter', $dateLetter);
        $stmt->bindParam(':dateRecv', $dateRecv);

        $stmt->execute();

        // Retrieve the ID of the last inserted row
        $lastInsertedId = $conn->lastInsertId();

        //Update work_finish_id into ctrl_authorities
        $query2 = "UPDATE ctrl_authorities SET work_finish_id = :workFinishId WHERE system_id = :systemId AND authority_id = :authorityId";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':workFinishId', $lastInsertedId);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authId);

        $stmt2->execute();
    }

    //get ref no
    // $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
    // $referenceNo->bindParam(':systemId', $systemId);
    // $referenceNo->execute();
    // $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
    // $refNo = $resultRef['reference_no'];
    $refNo = Utilities::getRefNo($systemId);

    $flwStatus = 120;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    // ANCHOR - Create task for authority when next status using authority status
    //find authority
    $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

    $dep = 'operation';
    $auth->bindParam(':systemId', $systemId);
    $auth->bindParam(':department', $dep);

    $auth->execute();

    $authIds = $auth->fetchAll(PDO::FETCH_OBJ);

    foreach ($authIds as $authId) {
        $taskAssignment->create($systemId, $flwStatus, $authId->authority);
    }

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Permohonan Sijil Siap Kerja bagi' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 79
    //nextstep
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);

    $details = 'Dokumen Permohonan Sijil Siap Kerja telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // declare telegram notification string
        // $telegramMsg = "Dokumen Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Surat Permohonan Sijil Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
        // NOTE - send telegram notification
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'file');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 51 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 51 ORDER BY id DESC LIMIT 1)";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':chronology_id', $chronoId);
    $stmt2->execute();



    //toast
    $message = "Dokumen Permohonan Sijil Siap Kerja telah berjaya dimuat naik.??";

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;

} else if ($status == "120") {
    //SPSSK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_auth_ltr_created']) ? $POST['dt_auth_ltr_created'] : '';

    $query3 = "SELECT work_finish_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workFinishId = $result3['work_finish_id'];

    $query4 = "UPDATE flw_work_finish SET dt_auth_ltr_created = :dateLetter WHERE id = :workFinishId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':workFinishId', $workFinishId);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 121;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Sijil Siap Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Permohonan Sijil Siap Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Sijil Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 59 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 59 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Surat Permohonan Sijil Siap Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "121") {
    //ASPSSK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateSend = isset ($POST['dt_auth_ltr_send']) ? $POST['dt_auth_ltr_send'] : '';

    $query3 = "SELECT work_finish_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workFinishId = $result3['work_finish_id'];

    $query4 = "UPDATE flw_work_finish SET dt_auth_ltr_send = :dateSend WHERE id = :workFinishId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':workFinishId', $workFinishId);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 122;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Permohonan Sijil Siap Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Akuan Serahan Permohonan Sijil Siap Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Surat Kelulusan Permohonan Sijil Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 60 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 60 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahan Permohonan Sijil Siap Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "122") {
    //SKPSSK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dlpStart = isset ($POST['dt_dlp_start']) ? $POST['dt_dlp_start'] : '';
    $dlpEnd = isset ($POST['dlpEnd']) ? $POST['dlpEnd'] : '';
    $dateApprove = isset ($POST['dt_appv_ltr_created']) ? $POST['dt_appv_ltr_created'] : '';
    $dateReceive = isset ($POST['dt_appv_ltr_received']) ? $POST['dt_appv_ltr_received'] : '';

    // Update flw_work_finish
    $query3 = "SELECT work_finish_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workFinishId = $result3['work_finish_id'];

    $query4 = "UPDATE flw_work_finish SET dt_dlp_start = :dlpStart, dt_dlp_end = :dlpEnd, dt_appv_ltr_created = :dateApprove, dt_appv_ltr_received = :dateReceive WHERE id = :workFinishId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dlpStart', $dlpStart);
    $stmt4->bindParam(':dlpEnd', $dlpEnd);
    $stmt4->bindParam(':dateApprove', $dateApprove);
    $stmt4->bindParam(':dateReceive', $dateReceive);
    $stmt4->bindParam(':workFinishId', $workFinishId);

    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 123;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Kelulusan Permohonan Sijil Siap Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Kelulusan Permohonan Sijil Siap Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Kelulusan Permohonan Sijil Siap Kerja telah dimuat naik. Sila muat naik Perakuan Sijil Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 61 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 61 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Surat Kelulusan Permohonan Sijil Siap Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "123") {
    // PSSK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateRecog = isset ($POST['dt_wf_recog_created']) ? $POST['dt_wf_recog_created'] : '';
    $dateNotify = isset ($POST['dt_wf_recog_notify']) ? $POST['dt_wf_recog_notify'] : '';
    $noRecog = isset ($POST['no_wf_recog']) ? $POST['no_wf_recog'] : '';

    // Update flw_work_finish
    $query3 = "SELECT work_finish_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workFinishId = $result3['work_finish_id'];

    $query4 = "UPDATE flw_work_finish SET dt_wf_recog_created = :dateRecog, dt_wf_recog_notify = :dateNotify, no_wf_recog = :noRecog WHERE id = :workFinishId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateRecog', $dateRecog);
    $stmt4->bindParam(':dateNotify', $dateNotify);
    $stmt4->bindParam(':noRecog', $noRecog);
    $stmt4->bindParam(':workFinishId', $workFinishId);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 125;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Perakuan Sijil Siap Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Perakuan Sijil Siap Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Perakuan Sijil Siap Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 62 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 62 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Perakuan Sijil Siap Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "125") {
    // ASPKSSK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateSubmit = isset ($POST['dt_wf_recog_submit']) ? $POST['dt_wf_recog_submit'] : '';
    $receivedBy = isset ($POST['wf_recog_received_by']) ? $POST['wf_recog_received_by'] : '';

    // Update flw_work_finish
    $query3 = "SELECT work_finish_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workFinishId = $result3['work_finish_id'];

    $query4 = "UPDATE flw_work_finish SET dt_wf_recog_submit = :dateSubmit, wf_recog_received_by = :receivedBy WHERE id = :workFinishId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSubmit', $dateSubmit);
    $stmt4->bindParam(':receivedBy', $receivedBy);
    $stmt4->bindParam(':workFinishId', $workFinishId);
    $stmt4->execute();

    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 139;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);
        // if ($flow->CheckAllComplete($systemId, $NextStatus)) {
        //     $taskAssignment->create($systemId, $flwStatus);
        // }
        $details = 'Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja telah dimuat naik. Sila muat naik  Permohonan Pemulangan Wang Cagaran. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 83 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 83 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "144") {
    // SRPWC
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_appl_ltr_created']) ? $POST['dt_appl_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_appl_ltr_received']) ? $POST['dt_appl_ltr_received'] : '';

    //All authority Involved
    $query3 = "SELECT authority_id FROM ctrl_authorities WHERE system_id = :systemId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->execute();

    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $authInv = [];

    foreach ($result3 as $row3) {
        $authInv[] = $row3['authority_id'];
    }

    foreach ($authInv as $authId) {
        //insert date into DB
        $query2 = "INSERT INTO flw_deposit_returns (system_id, dt_appl_ltr_created, dt_appl_ltr_received) VALUES (:systemId, :dateLtr, :dateRecv)";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':dateLtr', $dateLetter);
        $stmt2->bindParam(':dateRecv', $dateRecv);
        $stmt2->execute();

        // Retrieve the ID of the last inserted row
        $lastInsertedId = $conn->lastInsertId();

        //Update wayleave_id into Flw_authorities
        $query5 = "UPDATE ctrl_authorities SET deposit_returns_id = :deposit_returns_id WHERE system_id = :systemId AND authority_id = :authorityId";

        $stmt5 = $conn->prepare($query5);
        $stmt5->bindParam(':deposit_returns_id', $lastInsertedId, PDO::PARAM_INT);
        $stmt5->bindParam(':systemId', $systemId);
        $stmt5->bindParam(':authorityId', $authId, PDO::PARAM_INT);

        $stmt5->execute();
    }

    //get ref no
    // $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
    // $referenceNo->bindParam(':systemId', $systemId);
    // $referenceNo->execute();
    // $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
    // $refNo = $resultRef['reference_no'];
    $refNo = Utilities::getRefNo($systemId);

    $flwStatus = 146;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    // ANCHOR - Create task for authority when next status using authority status
    //find authority
    $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

    $dep = 'operation';
    $auth->bindParam(':systemId', $systemId);
    $auth->bindParam(':department', $dep);

    $auth->execute();

    $authIds = $auth->fetchAll(PDO::FETCH_OBJ);

    foreach ($authIds as $authId) {
        $taskAssignment->create($systemId, $flwStatus, $authId->authority);
    }

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Permohonan Pemulangan Wang Cagaran bagi' . $refNo;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 79
    //nextstep
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);

    $details = 'Dokumen Permohonan Pemulangan Wang Cagaran telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // declare telegram notification string
        // $telegramMsg = "Dokumen Permohonan Pemulangan Wang Cagaran telah dimuat naik. Sila muat naik Surat Permohonan Pemulangan Wang Cagaran. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
        // NOTE - send telegram notification
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

    }
    ;

    $chronoId = $chronology->create($username, $systemId, $status, 'file');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 72 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 72 ORDER BY id DESC LIMIT 1)";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':chronology_id', $chronoId);
    $stmt2->execute();



    //toast
    $message = "Dokumen Permohonan Pemulangan Wang Cagaran telah berjaya dimuat naik.??";

    http_response_code(200);
    // Finally, return a JSON
    echo json_encode(
        array(
            "success" => true,
            "message" => $message,
            "status" => 200
        )
    );

    // Close the database connection
    $conn = null;


} else if ($status == "146") {
    // SPPWC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_auth_ltr_created']) ? $POST['dt_auth_ltr_created'] : '';

    $query3 = "SELECT deposit_returns_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $depositReturnsId = $result3['deposit_returns_id'];

    // Update a record in the database
    $query = "UPDATE flw_deposit_returns SET dt_auth_ltr_created = :dateLtr, authority_id = :authorityId WHERE id = :depositReturnsId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':dateLtr', $dateLetter);
    $stmt->bindParam(':authorityId', $authorityId);
    $stmt->bindParam(':depositReturnsId', $depositReturnsId);

    if ($stmt->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 147;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Pemulangan Wang Cagaran bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Surat Permohonan Pemulangan Wang Cagaran telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Permohonan Pemulangan Wang Cagaran telah dimuat naik. Sila muat naik  Akuan Serahan Permohonan Pemulangan Wang Cagaran. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 77 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 77 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Surat Permohonan Pemulangan Wang Cagaran Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "147") {
    // ASPPWC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateSend = isset ($POST['dt_auth_ltr_send']) ? $POST['dt_auth_ltr_send'] : '';

    $query2 = "SELECT deposit_returns_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt2->execute();

    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    $depositReturnsId = $result2['deposit_returns_id'];

    // Update a record in the database
    $query = "UPDATE flw_deposit_returns SET dt_auth_ltr_send = :dateSend WHERE id = :depositReturnsId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':dateSend', $dateSend);
    $stmt->bindParam(':depositReturnsId', $depositReturnsId);
    if ($stmt->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 148;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Permohonan Pemulangan Wang Cagaran bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);

        $details = 'Akuan Serahan Permohonan Pemulangan Wang Cagaran telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Permohonan Pemulangan Wang Cagaran telah dimuat naik. Sila muat naik Baucer Pemulangan Wang Cagaran. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 78 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 78 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahan Permohonan Pemulangan Wang Cagaran Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;


} else if ($status == "148") {
    // BPWC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLtr = isset ($POST['dt_appv_ltr_created']) ? $POST['dt_appv_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_appv_ltr_received']) ? $POST['dt_appv_ltr_received'] : '';

    //input
    $noVoucher = isset ($POST['no_voucher']) ? $POST['no_voucher'] : '';

    $query2 = "SELECT deposit_returns_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt2->execute();

    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    $depositReturnsId = $result2['deposit_returns_id'];

    // Update a record in the database
    $query = "UPDATE flw_deposit_returns SET dt_appv_ltr_created = :dateLtr, dt_appv_ltr_received = :dateRecv, no_voucher = :noVoucher WHERE id = :depositReturnsId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':dateLtr', $dateLtr);
    $stmt->bindParam(':dateRecv', $dateRecv);
    $stmt->bindParam(':noVoucher', $noVoucher, PDO::PARAM_STR);
    $stmt->bindParam(':depositReturnsId', $depositReturnsId);
    if ($stmt->execute()) {
        //get ref no
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 149;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Baucer Pemulangan Wang Cagaran bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId);
        if ($flow->CheckAllComplete($systemId, $NextStatus)) {
            $taskAssignment->create($systemId, $flwStatus);
        }
        $details = 'Baucer Pemulangan Wang Cagaran telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Baucer Pemulangan Wang Cagaran telah dimuat naik. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 79 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 79 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Baucer Pemulangan Wang Cagaran Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "114") {
    //CDPSB
    $systemId = $POST['system-id'];
    $created = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes-CDPSB']) ? $POST['notes-CDPSB'] : '';

    //date

    //TODO : change to new function Addnote
    //notes
    $defaultNote = 'Pelan Siap Bina telah dimuat naik. Nota : ' . $notes;

    $query = "INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':notes', $defaultNote);
    $stmt->bindParam(':created', $created);
    $stmt->execute();

    $query1 = "SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachmentType";

    $attachmentType = 58;

    $stmt1 = $conn->prepare($query1);
    $stmt1->bindParam(':systemId', $systemId);
    $stmt1->bindParam(':attachmentType', $attachmentType, PDO::PARAM_INT);
    $stmt1->execute();

    $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    $attach_id = [];

    foreach ($result as $row) {
        $attach_id[] = $row['id'];
    }

    $attachment_id = '{' . implode(',', $attach_id) . '}';

    //All authority Involved
    $query3 = "SELECT authority_id FROM ctrl_authorities WHERE system_id = :systemId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->execute();

    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $authInv = [];

    foreach ($result3 as $row3) {
        $authInv[] = $row3['authority_id'];
    }

    $authorityInvolved = '{' . implode(',', $authInv) . '}';

    $query2 = "INSERT INTO flw_appl_chronologies (system_id, notes, created_at, \"user\", dt_notes, authority_id, attachment_id, status_id) VALUES (:systemId, :notes, :created, :user, :dt_notes, :authorityId, :attachment_id, :status_id)";

    $status_id = '128';

    $stmt2 = $conn->prepare($query2);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->bindParam(':notes', $defaultNote);
    $stmt2->bindParam(':created', $created);
    $stmt2->bindParam(':user', $username);
    $stmt2->bindParam(':dt_notes', $created);
    $stmt2->bindParam(':authorityId', $authorityInvolved);
    $stmt2->bindParam(':attachment_id', $attachment_id, PDO::PARAM_STR);
    $stmt2->bindParam(':status_id', $status_id);
    $stmt2->execute();

    //teleNoti
    $refNo = Utilities::getRefNo($systemId);

    $timeConverted = Utilities::convertDateToMalay($created);

    //changeStatus
    $indexFlow = $flow->goToNextFlow($systemId, 'mapping', 'BF');
    $assignment = $taskAssignment->set($username, $systemId, $indexFlow);

    // Record Changelog
    $detail = 'Pelan Siap Bina telah di Muat Naik';

    $changelog->projectActivity($systemId, $indexFlow, $detail);

    $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Pelan Siap Bina';
    $page = 'new task';

    if ($changelog->userActivity($text, $page)) {
        $telegramMsg = "Pelan Siap Bina telah dimuat naik. Sila muat naik Surat Permohonan Sijil Siap Kerja." . PHP_EOL . PHP_EOL . "?? No Rujukan : " . $refNo . PHP_EOL . "?? Tarikh : " . $timeConverted . PHP_EOL . "?? Nota : " . $notes;

        //tele to admin permit
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg);
        $telegramResponse = $telegram->sendMessage('role', '12', $telegramMsg);
    }

    $message = 'Pelan Siap Bina Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "created" => $created,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
        )
    );

    $conn = null;
} else if ($status == "90") {

    //SRPLPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_appl_ltr_created']) ? $POST['dt_appl_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_appl_ltr_received']) ? $POST['dt_appl_ltr_received'] : '';

    // Select work_permit_id
    $query = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt->execute();

    $workPermitId = $stmt->fetchColumn();

    // Get single permitID
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    // Insert new Work Permit Extend entry
    $query = "INSERT INTO flw_work_permit_extend ( system_id, dt_appl_ltr_created, dt_appl_ltr_received) VALUES (:systemId, :dateLetter, :dateRecv) RETURNING id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':dateLetter', $dateLetter);
    $stmt->bindParam(':dateRecv', $dateRecv);
    if ($stmt->execute()) {
        $extendId = $stmt->fetchColumn();

        // Get available flw_work_permit_extend id
        $query = "SELECT extend_id FROM flw_work_permit WHERE id = :workPermitId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':workPermitId', $idWorkPermit);
        $stmt->execute();
        $existingExtend = $stmt->fetchColumn();

        if ($existingExtend == null) {
            $extendIds = '{' . $extendId . '}';
        } else {
            $extendIds = '{' . str_replace(['{', '}'], '', $existingExtend) . ',' . $extendId . '}';
        }

        // NOTE - Update flw_work_permit
        $query = "UPDATE flw_work_permit SET extend_id = :extendId WHERE id = :workPermitId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':extendId', $extendIds, PDO::PARAM_INT);
        $stmt->bindParam(':workPermitId', $idWorkPermit);
        if ($stmt->execute()) {
            //get ref no
            $refNo = Utilities::getRefNo($systemId);

            //get authority name
            $authName = Utilities::getAuthorityName($authorityId);

            $flwStatus = 92;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status, $authorityId);
            $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah muat naik Dokumen Permohonan Lanjutan Permit Kerja bagi' . $refNo;
            $pages = 'task_operation';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 90
            //nextstep
            $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

            $details = 'Dokumen Permohonan Lanjutan Permit Kerja telah dimuat naik bagi ' . $refNo . 'Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Dokumen Permohonan Permit Kerja telah dimuat naik. Sila muat naik Surat Permohonan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);
                // NOTE - send telegram notification
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            }
            ;

            $chronoId = $chronology->create($username, $systemId, $status, 'file');

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 43 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 43 ORDER BY id DESC LIMIT 1)";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':chronology_id', $chronoId);
            $stmt2->execute();

        }

        //toast
        $message = "Dokumen Permohonan Lanjutan Permit Kerja telah berjaya dimuat naik.??";

        http_response_code(200);
        // Finally, return a JSON
        echo json_encode(
            array(
                "success" => true,
                "message" => $message,
                "status" => 200
            )
        );
    }

    // Close the database connection
    $conn = null;



} else if ($status == "92") {
    //SPLPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_auth_ltr_created']) ? $POST['dt_auth_ltr_created'] : '';

    // Select work_permit_id
    $query = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt->execute();

    $workPermitId = $stmt->fetchColumn();

    // Get single permitID
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    // Get available flw_work_permit_extend id
    $query = "SELECT extend.id as e_id, extend.dt_appl_ltr_created as e_dt_appl_ltr_created FROM flw_work_permit permit CROSS JOIN LATERAL unnest(permit.extend_id) e_id JOIN flw_work_permit_extend extend ON extend.id = e_id WHERE permit.id = :workPermitId ORDER BY e_dt_appl_ltr_created DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':workPermitId', $idWorkPermit);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingExtend = $result['e_id'];

    // NOTE - Update flw_work_permit
    $query = "UPDATE flw_work_permit_extend SET dt_auth_ltr_created = :dateLetter WHERE id = :workPermitId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':dateLetter', $dateLetter);
    $stmt->bindParam(':workPermitId', $existingExtend);
    if ($stmt->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 93;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Lanjutan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Surat Permohonan Lanjutan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Permohonan Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Kelulusan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPLPK
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 44 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 44 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }

    $message = 'Surat Permohonan Lanjutan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );


    $conn = null;

} else if ($status == "93") {
    //ASPLPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    // dates
    $dateSend = isset ($POST['dt_auth_ltr_send']) ? $POST['dt_auth_ltr_send'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    // Ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    // Get available flw_work_permit_extend id
    $query = "SELECT extend.id as e_id, extend.dt_appl_ltr_created as e_dt_appl_ltr_created FROM flw_work_permit permit CROSS JOIN LATERAL unnest(permit.extend_id) e_id JOIN flw_work_permit_extend extend ON extend.id = e_id WHERE permit.id = :workPermitId ORDER BY e_dt_appl_ltr_created DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':workPermitId', $idWorkPermit);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingExtend = $result['e_id'];

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit_extend SET dt_auth_ltr_send = :dateSend WHERE id = :workPermitExtendId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':workPermitExtendId', $existingExtend);
    if ($stmt4->execute()) {
        // get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 94;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Permohonan Lanjutan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 93
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Akuan Serahan Permohonan Lanjutan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 78 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 78 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }

    $message = 'Akuan Serahan Permohonan Lanjutan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );
    $conn = null;

} else if ($status == "94") {
    // SKLPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    // dates
    $dateLetter = isset ($POST['dt_appv_ltr_created']) ? $POST['dt_appv_ltr_created'] : '';
    $dateReceive = isset ($POST['dt_appv_received']) ? $POST['dt_appv_received'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    // Get available flw_work_permit_extend id
    $query = "SELECT extend.id as e_id, extend.dt_appl_ltr_created as e_dt_appl_ltr_created FROM flw_work_permit permit CROSS JOIN LATERAL unnest(permit.extend_id) e_id JOIN flw_work_permit_extend extend ON extend.id = e_id WHERE permit.id = :workPermitId ORDER BY e_dt_appl_ltr_created DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':workPermitId', $idWorkPermit);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingExtend = $result['e_id'];

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit_extend SET dt_appv_ltr_created = :dateLetter, dt_appv_received = :dateReceive WHERE id = :workPermitExtendId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateReceive', $dateReceive);
    $stmt4->bindParam(':workPermitExtendId', $existingExtend);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 95;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Kelulusan Lanjutan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 94
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Surat Kelulusan Lanjutan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Perakuan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 45 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 45 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Surat Kelulusan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "95") {

    //PLPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateLetter = isset ($POST['dt_wp_recog_created']) ? $POST['dt_wp_recog_created'] : '';
    $dateNotify = isset ($POST['dt_wp_recog_notify']) ? $POST['dt_wp_recog_notify'] : '';
    $noRecog = isset ($POST['no_wp_recog']) ? $POST['no_wp_recog'] : '';
    $wdayStart = isset ($POST['dt_wday_strt']) ? $POST['dt_wday_strt'] : '';
    $wdayEnd = isset ($POST['dt_wday_end']) ? $POST['dt_wday_end'] : '';
    $wendStart = isset ($POST['dt_wend_strt']) ? $POST['dt_wend_strt'] : '';
    $wendEnd = isset ($POST['dt_wend_end']) ? $POST['dt_wend_end'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    // Get available flw_work_permit_extend id
    $query = "SELECT extend.id as e_id, extend.dt_appl_ltr_created as e_dt_appl_ltr_created FROM flw_work_permit permit CROSS JOIN LATERAL unnest(permit.extend_id) e_id JOIN flw_work_permit_extend extend ON extend.id = e_id WHERE permit.id = :workPermitId ORDER BY e_dt_appl_ltr_created DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':workPermitId', $idWorkPermit);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingExtend = $result['e_id'];

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit_extend SET dt_wp_recog_created = :dateLetter, dt_wp_recog_notify = :dateNotify, no_wp_recog = :noRecog, dt_wday_strt = :wdayStart, dt_wday_end = :wdayEnd, dt_wend_strt = :wendStart, dt_wend_end = :wendEnd WHERE id = :workPermitExtendId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateNotify', $dateNotify);
    $stmt4->bindParam(':noRecog', $noRecog);
    $stmt4->bindParam(':wdayStart', $wdayStart);
    $stmt4->bindParam(':wdayEnd', $wdayEnd);
    $stmt4->bindParam(':wendStart', $wendStart);
    $stmt4->bindParam(':wendEnd', $wendEnd);
    $stmt4->bindParam(':workPermitExtendId', $existingExtend);

    if ($stmt4->execute()) {
        //get ref no
        // $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        // $referenceNo->bindParam(':systemId', $systemId);
        // $referenceNo->execute();
        // $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        // $refNo = $resultRef['reference_no'];
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 96;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Perakuan Lanjutan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Perakuan Lanjutan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Perakuan Lanjutan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Lanjutan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //PLPK
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 46 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 46 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Perakuan Lanjutan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "96") {
    // ASLPPK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';

    //date
    $dateSubmit = isset ($POST['dt_wp_recog_handover']) ? $POST['dt_wp_recog_handover'] : '';
    $receivedBy = isset ($POST['wp_recog_received_by']) ? $POST['wp_recog_received_by'] : '';

    // Select work_permit_id
    $query3 = "SELECT work_permit_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workPermitId = $result3['work_permit_id'];

    //TODO: ubah semula selepas dah ada revision
    $idWorkPermit = str_replace(['{', '}'], '', $workPermitId);

    // Get available flw_work_permit_extend id
    $query = "SELECT extend.id as e_id, extend.dt_appl_ltr_created as e_dt_appl_ltr_created FROM flw_work_permit permit CROSS JOIN LATERAL unnest(permit.extend_id) e_id JOIN flw_work_permit_extend extend ON extend.id = e_id WHERE permit.id = :workPermitId ORDER BY e_dt_appl_ltr_created DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':workPermitId', $idWorkPermit);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $existingExtend = $result['e_id'];

    //Update flw_work_permit
    $query4 = "UPDATE flw_work_permit_extend SET dt_wp_recog_handover = :dateSubmit, wp_recog_received_by = :receivedBy WHERE id = :workPermitExtendId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSubmit', $dateSubmit);
    $stmt4->bindParam(':receivedBy', $receivedBy);
    $stmt4->bindParam(':workPermitExtendId', $existingExtend);
    if ($stmt4->execute()) {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        //get authority name
        $authorityName = $conn->prepare('SELECT * FROM ls_authorities WHERE id = :authority_id');
        $authorityName->bindParam(':authority_id', $authorityId);
        $authorityName->execute();

        $resultAuthName = $authorityName->fetch(PDO::FETCH_ASSOC);

        $authName = $resultAuthName['sort_name'];

        $flwStatus = 90;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahahan Lanjutan Perakuan Permit Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 70
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);

        // if ($flow->CheckAllComplete($systemId, $NextStatus)) {
        //     $taskAssignment->create($systemId, $flwStatus);
        // }

        $details = 'Akuan Serahahan Lanjutan Perakuan Permit Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Perakuan Permit Kerja telah dimuat naik. Sila muat naik Notis Mula Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            $telegramMsg = "Akuan Serahan Lanjutan Perakuan Permit Kerja telah dimuat naik. Sila muat naik Notis Siap Kerja setelah Kerja Selesai. \n\n<strong>?? No Rujukan : " . $refNo . "\n ?? Pihak Berkuasa : " . $authName . "</strong>";


            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPKIL
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 79 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 79 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();
    }


    $message = 'Akuan Serahahan Lanjutan Perakuan Permit Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "134") {
    // SRPSSMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_cmgd_appl_ltr_created']) ? $POST['dt_cmgd_appl_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_cmgd_appl_ltr_received']) ? $POST['dt_cmgd_appl_ltr_received'] : '';

    // Insert into flw_work_permit
    $query = "INSERT INTO flw_work_defects ( system_id, dt_cmgd_appl_ltr_created, dt_cmgd_appl_ltr_received) VALUES (:systemId, :dateLetter, :dateRecv) RETURNING id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':dateLetter', $dateLetter);
    $stmt->bindParam(':dateRecv', $dateRecv);
    if ($stmt->execute()) {

        $workDefectsId = $stmt->fetchColumn();

        //Update wayleave_id into Flw_authorities
        $query2 = "UPDATE ctrl_authorities SET work_defects_id = :workDefectsId WHERE system_id = :systemId AND authority_id = :authorityId";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':workDefectsId', $workDefectsId);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);

        if ($stmt2->execute()) {
            //get ref no
            $refNo = Utilities::getRefNo($systemId);

            //get authority name
            $authName = Utilities::getAuthorityName($authorityId);

            $flwStatus = 136;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status, $authorityId);
            $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);
            // $taskAssignment->create($systemId, $flwStatus, 0);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Memuat naik Permohonan Sijil Siap Memperbaiki Kecacatan bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
            $pages = 'task_operation';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 134
            $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

            $details = 'Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

                // declare telegram notification string
                // $telegramMsg = "Surat Permohonan Kelulusan Permit Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Kelulusan Permit Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
            }
            ;

            $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':authorityId', $authorityId);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            //SPLPK
            $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 60 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 60 ORDER BY id DESC LIMIT 1)";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':authorityId', $authorityId);
            $stmt2->bindParam(':chronology_id', $chronoId);
            $stmt2->execute();
        }

        $message = 'Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik!??';

        http_response_code(200);
        echo json_encode(
            array(
                "systemId" => $systemId,
                "authorityId" => $authorityId,
                "user" => $username,
                "notes" => $notes,
                "message" => $message,
                "status" => 200,
            )
        );

    }

    $conn = null;

} else if ($status == "136") {
    // SPSSMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_cmgd_auth_ltr_created']) ? $POST['dt_cmgd_auth_ltr_created'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET dt_cmgd_auth_ltr_created = :dateLetter WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 137;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);
        // $taskAssignment->create($systemId, $flwStatus, 0);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Sijil Siap Memperbaiki Kecacatan bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 136
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Surat Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

            //whatsapp notification for PBM/PBT involved
            $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPSSMK
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 62 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 62 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Surat Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;

} else if ($status == "137") {
    // ASPSSMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateSend = isset ($POST['dt_cmgd_auth_send']) ? $POST['dt_cmgd_auth_send'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET dt_cmgd_auth_send = :dateSend WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 138;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);
        // $taskAssignment->create($systemId, $flwStatus, 0);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 137
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Ulasan Laporan Sijil Siap Memperbaiki Kecacatan. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPSSMK
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 63 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 63 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "138") {
    // UPSSMK
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_cmgd_appv_ltr_created']) ? $POST['dt_cmgd_appv_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_cmgd_appv_ltr_received']) ? $POST['dt_cmgd_appv_ltr_received'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id, work_finish_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];
    $workFinishId = $result3['work_finish_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET dt_cmgd_appv_ltr_created = :dateLetter, dt_cmgd_appv_ltr_received = :dateRecv  WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateRecv', $dateRecv);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 144;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);

        $query = "SELECT *
        FROM flw_work_finish
        WHERE CURRENT_DATE BETWEEN dt_dlp_start AND dt_dlp_end AND id = :workFinishId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':workFinishId', $workFinishId);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

        } else {
            $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);
        }


        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 138
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            $telegramMsg = "Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan telah dimuat naik. Sila muat naik Permohonan Pemulangan Wang Cagaran. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SPSSMK
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 64 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 64 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "139") {
    // SPCCC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_ccc_auth_ltr_created']) ? $POST['dt_ccc_auth_ltr_created'] : '';

    // Insert into flw_work_permit
    $query = "INSERT INTO flw_work_defects ( system_id, dt_ccc_auth_ltr_created) VALUES (:systemId, :dateLetter) RETURNING id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':dateLetter', $dateLetter);
    if ($stmt->execute()) {

        $workDefectsId = $stmt->fetchColumn();

        //Update wayleave_id into Flw_authorities
        $query2 = "UPDATE ctrl_authorities SET work_defects_id = :workDefectsId WHERE system_id = :systemId AND authority_id = :authorityId";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':workDefectsId', $workDefectsId);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);

        if ($stmt2->execute()) {

            $refNo = Utilities::getRefNo($systemId);
            $authName = Utilities::getAuthorityName($authorityId);

            $flwStatus = 140;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status, $authorityId);
            $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Permohonan Sijil Sempurna Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
            $pages = 'task_operation';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 139
            $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

            $details = 'Surat Permohonan Sijil Sempurna Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

                // declare telegram notification string
                // $telegramMsg = "Surat Permohonan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Akuan Serahan Permohonan Sijil Sempurna Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');

                //whatsapp notification for PBM/PBT involved
                $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authorityId, $status);
            }
            ;

            $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':authorityId', $authorityId);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            //SPCCC
            $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 65 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 65 ORDER BY id DESC LIMIT 1)";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':authorityId', $authorityId);
            $stmt2->bindParam(':chronology_id', $chronoId);
            $stmt2->execute();

        }

        $message = 'Surat Permohonan Sijil Sempurna Kerja Berjaya Dimuat Naik!??';

        http_response_code(200);
        echo json_encode(
            array(
                "systemId" => $systemId,
                "authorityId" => $authorityId,
                "user" => $username,
                "notes" => $notes,
                "message" => $message,
                "status" => 200,
            )
        );
    }

    $conn = null;

} else if ($status == "140") {
    // ASPCCC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateSend = isset ($POST['dt_ccc_auth_send']) ? $POST['dt_ccc_auth_send'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET dt_ccc_auth_send = :dateSend  WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateSend', $dateSend);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 141;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Permohonan Sijil Sempurna Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 140
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Akuan Serahan Permohonan Sijil Sempurna Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Akuan Serahan Permohonan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Kelulusan Sijil Sempurna Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //ASPCCC
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 66 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 66 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Akuan Serahan Permohonan Sijil Sempurna Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "141") {
    // SKPCCC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateLetter = isset ($POST['dt_ccc_appv_ltr_created']) ? $POST['dt_ccc_appv_ltr_created'] : '';
    $dateRecv = isset ($POST['dt_ccc_appv_received']) ? $POST['dt_ccc_appv_received'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET dt_ccc_appv_ltr_created = :dateLetter, dt_ccc_appv_received = :dateRecv WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateLetter', $dateLetter);
    $stmt4->bindParam(':dateRecv', $dateRecv);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 142;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Surat Kelulusan Permohonan Sijil Sempurna Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 141
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Surat Kelulusan Permohonan Sijil Sempurna Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Surat Kelulusan Permohonan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Perakuan Sijil Sempurna Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SKPCCC
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 67 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 67 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Surat Kelulusan Permohonan Sijil Sempurna Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "142") {
    // PCCC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $dateRecog = isset ($POST['dt_ccc_recog_created']) ? $POST['dt_ccc_recog_created'] : '';
    $dateNotify = isset ($POST['dt_ccc_recog_notify']) ? $POST['dt_ccc_recog_notify'] : '';
    $noRecog = isset ($POST['no_ccc_recog']) ? $POST['no_ccc_recog'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET dt_ccc_recog_created = :dateRecog, dt_ccc_recog_notify = :dateNotify, no_ccc_recog = :noRecog WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':dateRecog', $dateRecog);
    $stmt4->bindParam(':dateNotify', $dateNotify);
    $stmt4->bindParam(':noRecog', $noRecog);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 143;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Perakuan Sijil Sempurna Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 142
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Perakuan Sijil Sempurna Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            // $telegramMsg = "Perakuan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Akuan Serahan Perakuan Sijil Sempurna Kerja. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : ". $authName. "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //SKPCCC
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 68 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 68 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Perakuan Sijil Sempurna Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "143") {
    // ASPKCCC
    $systemId = $POST['systemId'];
    $authorityId = $POST['authorityId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $notes = isset ($POST['notes']) ? $POST['notes'] : '';
    $nameRecv = isset ($POST['ccc_recog_received_by']) ? $POST['ccc_recog_received_by'] : '';
    $dateSubmit = isset ($POST['dt_ccc_recog_submit']) ? $POST['dt_ccc_recog_submit'] : '';

    // Select work_defects_id
    $query3 = "SELECT work_defects_id FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId";

    $stmt3 = $conn->prepare($query3);
    $stmt3->bindParam(':systemId', $systemId);
    $stmt3->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
    $stmt3->execute();

    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $workDefectsId = $result3['work_defects_id'];

    //Update flw_work_defects
    $query4 = "UPDATE flw_work_defects SET ccc_recog_received_by = :nameRecv, dt_ccc_recog_submit = :dateSubmit WHERE id = :workDefectsId";

    $stmt4 = $conn->prepare($query4);
    $stmt4->bindParam(':nameRecv', $nameRecv);
    $stmt4->bindParam(':dateSubmit', $dateSubmit);
    $stmt4->bindParam(':workDefectsId', $workDefectsId);
    if ($stmt4->execute()) {

        $refNo = Utilities::getRefNo($systemId);
        $authName = Utilities::getAuthorityName($authorityId);

        $flwStatus = 144;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status, $authorityId);
        $taskAssignment->create($systemId, $flwStatus, $authorityId, $status);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Memuat naik Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 143
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authorityId, Steps: $flwStatus);

        $details = 'Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja telah dimuat naik bagi permohonan ' . $refNo . ' untuk Pihak Berkuasa ' . $authName . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details, $authorityId)) {

            // declare telegram notification string
            $telegramMsg = "Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja telah dimuat naik. Sila muat naik Permohonan Pemulangan Wang Cagaran. \n\n<strong>?? No Rujukan : " . $refNo . "\n ??Pihak Berkuasa : " . $authName . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $authName);

            // NOTE - send telegram notification for team account
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file', $authorityId);

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id ) VALUES (:systemId, :details, :created, :authorityId, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        //ASPKCCC
        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 81 AND authority = :authorityId AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 81 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':authorityId', $authorityId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();

    }

    $message = 'Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja Berjaya Dimuat Naik!??';

    http_response_code(200);
    echo json_encode(
        array(
            "systemId" => $systemId,
            "authorityId" => $authorityId,
            "user" => $username,
            "notes" => $notes,
            "message" => $message,
            "status" => 200,
        )
    );

    $conn = null;
} else if ($status == "24") {
    //gis_assign
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    // start select reference no from flw_appl_entries
    $refNo = Utilities::getRefNo($systemId);
    // end select reference no from flw_appl_entries

    // gis name
    $stmtG = $conn->prepare('SELECT sys_hr_employee.first_name FROM sys_users left join sys_hr_employee ON sys_users.employee_id = sys_hr_employee.id WHERE username = :gisAssign LIMIT 1');
    $stmtG->bindParam(':gisAssign', $username);
    $stmtG->execute();
    $rowG = $stmtG->fetch(PDO::FETCH_ASSOC);

    $gis_name = $rowG['first_name'];

    $flwStatus = 25;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Memuat naik Pelan Izin Lalu bagi permohonan ' . $refNo;
    $pages = 'task_geospatial';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 24
    //nextstep for geo
    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF");

    $details = 'Pelan Izin Lalu telah dimuat naik bagi Permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {

        // declare telegram notification string
        $telegramMsg = "Dokumen Pelan Izin Lalu telah dimuat naik. \n\n<strong>?????Pegawai GIS :  " . $gis_name . " \n?? No Rujukan : " . $refNo . "</strong>";
        // NOTE - send telegram notification for team PKD
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('department', 'operation', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('role', 4, $telegramMsg, 'html');
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

    //toast
    $message = 'Muat Naik Dokumen Pelan Izin Lalu selesai??';

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "message" => $message,
        "status" => 200
    ]);

    // Close the database connection
    $conn = null;

}

//survey
else if ($status == "41") {
    //survey-team-assign
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);
    $surveyProvider = isset($POST['survey-provider']) ? $POST['survey-provider'] : '';
    $dateRange = isset($POST['modal-date-range']) ? $POST['modal-date-range'] : '';
    list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
    $startDate = date('Y-m-d', strtotime($startDateStr));
    $endDate = date('Y-m-d', strtotime($endDateStr));
    $timestamp = date('Y-m-d H:i:s', time());
    $timeConverted = Utilities::convertDateToMalay($timestamp);
    $user = $_SESSION['username'];

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    //get all attachment
    $queryAttach = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type IN ('8','3','4','7','87') AND active = true");
    // Bind the parameters
    $queryAttach->bindParam(':systemId', $systemId);
    $queryAttach->execute();
    // Fetch the result row
    $urlList = $queryAttach->fetch(PDO::FETCH_ASSOC);

    if ($surveyProvider == 'Inhouse') {
        $surveyTeamAssign = isset($POST['survey-team-assign']) ? $POST['survey-team-assign'] : '';
        $surveyMemberLeader = SurveyApi::getSurveyMemberGroup($surveyTeamAssign);
        var_dump($surveyMemberLeader);
        // //NOTE: get team
        $query4 = $conn->prepare("SELECT survey_team FROM flw_survey_team WHERE id = :surveyTeamAssign AND is_active = true");

        // // Bind the parameters
        $query4->bindParam(':surveyTeamAssign', $surveyTeamAssign);
        $query4->execute();

        // // Fetch the result row
        $row4 = $query4->fetch(PDO::FETCH_ASSOC);

        // // Extract the team value from the row
        $team = $row4['survey_team'];

        // Execute a SELECT query on the database
        $checking2 = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        $checking2->bindParam(':systemId', $systemId);
        $checking2->execute();

        // Return the user information to the client
        if ($checking2->rowCount() > 0) {
            // Execute a SELECT query on the database
            $assign2 = $conn->prepare("UPDATE flw_survey_udm SET team_id = :surveyTeamAssign, est_date_start = :startDate, est_date_end = :endDate, submission_date = :submitted, submission_name = :user, survey_provider = :surveyProvider WHERE system_id = :systemId");
        } else {
            // Execute a SELECT query on the database
            $assign2 = $conn->prepare("INSERT INTO flw_survey_udm (system_id, team_id, est_date_start, est_date_end, submission_date, submission_name, survey_provider) VALUES (:systemId, :surveyTeamAssign, :startDate, :endDate, :submitted, :user, :surveyProvider)");
        }
        // Bind the parameters
        $assign2->bindParam(':surveyTeamAssign', $surveyTeamAssign);
        // $assi  gn->bindParam(':surveyLeaderAssign', $surveyLeaderAssign);
        $assign2->bindParam(':startDate', $startDate);
        $assign2->bindParam(':endDate', $endDate);
        $assign2->bindParam(':submitted', $timestamp);
        $assign2->bindParam(':user', $user);
        $assign2->bindParam(':systemId', $systemId);
        $assign2->bindParam(':surveyProvider', $surveyProvider);
        $assign2->execute();

        // Check if the query was successful
        if ($assign2->rowCount() > 0) {
            // Execute a SELECT query on the database
            $select = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

            $select->bindParam(':systemId', $systemId);
            $select->execute();
            // Fetch the result from the executed SELECT query
            $result = $select->fetch(PDO::FETCH_ASSOC);
            // Retrieve the ID from the fetched result
            $lastInsertedId = $result['id'];
        }

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_appl_survey WHERE system_id = :systemId ");

        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        // Return the user information to the client
        if ($checking->rowCount() > 0) {
            // Execute a SELECT query on the database
            $assign = $conn->prepare("UPDATE flw_appl_survey SET udm_id = :udmId, submission_date = :submitted WHERE system_id = :systemId");
        } else {
            // Execute a SELECT query on the database
            $assign = $conn->prepare("INSERT INTO flw_appl_survey (system_id, udm_id, submission_date) VALUES (:systemId, :udmId, :submitted)");
        }
        // Bind the parameters
        $assign->bindParam(':udmId', $lastInsertedId);
        $assign->bindParam(':submitted', $timestamp);
        $assign->bindParam(':systemId', $systemId);

        if ($assign->execute()) {

            $flwStatus = 42;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menyelesaikan pembahagian tugasan pengukuran bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 41
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Pembahagian Tugasan Pengukuran Selesai bagi ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $message = "Pembahagian Tugasan Pengukuran Selesai.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? Kumpulan : " . $team . "</strong>";
                // $message = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, date: $timeConverted, item: $team);

                // NOTE - send telegram notification for role 28-Ketua Survey
                $response = $telegram->sendMessage('group', 'management', $message, 'html');
                $response = $telegram->sendMessage('role', 28, $message, 'html');

                if($urlList){
                    foreach($urlList as $url){
                        if($url['url'] != ''){
                            $telegram->sendDocument('role', 28, base64_decode($url['url']), '');
                        }
                        if($url['attachment_type'] == 8 || $url['attachment_type'] == 3){
                            $telegram->sendDocument('group', 'management', base64_decode($url['url']), '');
                        }
                    }
                }

                // declare telegram notification string
                $message2 = "Pembahagian Tugasan Pengukuran Selesai. Sila Mula Kerja Ukur.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? Kumpulan : " . $team . "</strong>";

                foreach($surveyMemberLeader as $member){

                    // NOTE - send telegram notification for role Kumpulan Ukur Survey
                    $telegram->sendMessage('assignee', $member, $message2, 'html');
                    if($urlList){
                        foreach($urlList as $url){
                            if($url['url'] != ''){
                                $telegram->sendDocument('assignee', $member, base64_decode($url['url']), '');
                            }
                        }
                    }
                }
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
                "member" => $surveyMemberLeader,
            ]);

        } else {
            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => "failed",
                "status" => 500,
            );
        }
    } else if ($surveyProvider == 'Outsource') {
        $idSurveyor = isset($POST['survey-surveyor']) ? $POST['survey-surveyor'] : '';

        //NOTE: get team
        $query4 = $conn->prepare("SELECT full_name FROM ls_sr_contact WHERE id = :idSurveyor");

        // // Bind the parameters
        $query4->bindParam(':idSurveyor', $idSurveyor);
        $query4->execute();

        // // Fetch the result row
        $row4 = $query4->fetch(PDO::FETCH_ASSOC);

        // // Extract the team value from the row
        $surveySurveyor = $row4['full_name'];

        // Execute a SELECT query on the database
        $checking2 = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        $checking2->bindParam(':systemId', $systemId);
        $checking2->execute();

        // Return the user information to the client
        if ($checking2->rowCount() > 0) {
            // Execute a SELECT query on the database
            $assign2 = $conn->prepare("UPDATE flw_survey_udm SET surveyor_id = :idSurveyor, est_date_start = :startDate, est_date_end = :endDate, submission_date = :submitted, submission_name = :user, survey_provider = :surveyProvider WHERE system_id = :systemId");
        } else {
            // Execute a SELECT query on the database
            $assign2 = $conn->prepare("INSERT INTO flw_survey_udm (system_id, surveyor_id, est_date_start, est_date_end, submission_date, submission_name, survey_provider) VALUES (:systemId, :idSurveyor, :startDate, :endDate, :submitted, :user, :surveyProvider)");
        }
        // Bind the parameters
        $assign2->bindParam(':idSurveyor', $idSurveyor);
        // $assi  gn->bindParam(':surveyLeaderAssign', $surveyLeaderAssign);
        $assign2->bindParam(':startDate', $startDate);
        $assign2->bindParam(':endDate', $endDate);
        $assign2->bindParam(':submitted', $timestamp);
        $assign2->bindParam(':user', $user);
        $assign2->bindParam(':systemId', $systemId);
        $assign2->bindParam(':surveyProvider', $surveyProvider);
        $assign2->execute();

        // Check if the query was successful
        if ($assign2->rowCount() > 0) {
            // Execute a SELECT query on the database
            $select = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

            $select->bindParam(':systemId', $systemId);
            $select->execute();
            // Fetch the result from the executed SELECT query
            $result = $select->fetch(PDO::FETCH_ASSOC);
            // Retrieve the ID from the fetched result
            $lastInsertedId = $result['id'];
        }

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_appl_survey WHERE system_id = :systemId ");

        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        // Return the user information to the client
        if ($checking->rowCount() > 0) {
            // Execute a SELECT query on the database
            $assign = $conn->prepare("UPDATE flw_appl_survey SET udm_id = :udmId, submission_date = :submitted WHERE system_id = :systemId");
        } else {
            // Execute a SELECT query on the database
            $assign = $conn->prepare("INSERT INTO flw_appl_survey (system_id, udm_id, submission_date) VALUES (:systemId, :udmId, :submitted)");
        }
        // Bind the parameters
        $assign->bindParam(':udmId', $lastInsertedId);
        $assign->bindParam(':submitted', $timestamp);
        $assign->bindParam(':systemId', $systemId);

        if ($assign->execute()) {

            $flwStatus = 55;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menyelesaikan pembahagian tugasan pengukuran bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 41
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Pembahagian Tugasan Pengukuran Selesai bagi ' . $refNo . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $message = "Pembahagian Tugasan Pengukuran Selesai.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? SR Dilantik : " . $surveySurveyor . "</strong>";
                // $message = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, date: $timeConverted, item: $team);

                // NOTE - send telegram notification for role 28-Ketua Survey
                $response = $telegram->sendMessage('group', 'management', $message, 'html');
                $response = $telegram->sendMessage('role', 28, $message, 'html');
                if($urlList){
                    foreach($urlList as $url){
                        if($url['url'] != '' && ($url['attachment_type'] == 8 || $url['attachment_type'] == 3) ){
                            $telegram->sendDocument('role', 28, base64_decode($url['url']), '');
                            $telegram->sendDocument('group', 'management', base64_decode($url['url']), '');
                        }
                    }
                }

                // Set your API parameters
                $whatsappMsg = "Pembahagian Tugasan Pengukuran Selesai. \n\n No Rujukan : " . $refNo . " \n Julat Tarikh Jangkaan Mula - Tamat : " . $dateRange . "";

                $getSRContact = $conn->prepare("SELECT phone_no FROM ls_sr_contact WHERE id = :idSurveyor");
                $getSRContact->bindParam(':idSurveyor', $idSurveyor);
                $getSRContact->execute();

                $result = $getSRContact->fetch(PDO::FETCH_ASSOC);

                $SRContact = $result['phone_no'];

                $applData = array(
                    "secret" => $system->App->secret,
                    "notification" => array(
                        "message" => $whatsappMsg,
                        "phones" => [
                            $SRContact
                        ],
                        "attachments" => [
                        ]
                    )
                );

                // Convert the array to JSON
                $jsonData = json_encode($applData);

                // https //:wapi.asiadebut.tech portnumber:8552
                //     / api / chat / nophone / message;
                // make api request to extercord
                $apiRequest = $Curl->request($ec_url.'/gateway/internal/notify/' . $systemId, $jsonData);

                $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

                // Decode the response JSON
                $curlResult = json_decode($apiRequest['response']);
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
    }

    // Close the database connection
    $conn = null;
}
else if ($status == "42") {
    //clockin = pengesahan kehadiran hari ini
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);
    $pCategory = 'PIU';

    function generateRandom64BitString()
    {
        $randomBytes = random_bytes(8); // 64 bits = 8 bytes
        return bin2hex($randomBytes);
    }
    $bit64 = generateRandom64BitString();
    // $bit64 = isset($POST['bit64']) ? $POST['bit64'] : '';
    // var_dump($bit64);
    // $systemId = $POST['system-id'];
    $protocol = ((!empty ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $protocol . $_SERVER['HTTP_HOST'];
    $qrUrl = $host . "/surveys/signin/{$systemId}/{$bit64}";

    $clockinTime = isset ($POST['clock-in']) ? date('H:i:s', strtotime($POST['clock-in'])) : '';
    $numberTeam = isset ($POST['number-team']) ? intval($POST['number-team']) : null;
    $timestamp = date('Y-m-d H:i:s', time());
    $user = $_SESSION['username'];
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Check if a record with the same system ID and clock-in time already exists
    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM flw_survey_attandance WHERE system_id = :systemId AND clock_in = :clockinTime");

    $stmtCheck->bindParam(':systemId', $systemId);
    $stmtCheck->bindParam(':clockinTime', $clockinTime);
    $stmtCheck->execute();

    $recordCount = $stmtCheck->fetchColumn();
    // if ($recordCount > 0) {
    //     // A record already exists, handle the duplication error
    //     echo json_encode([
    //         "success" => "Error",
    //         "status" => 400,
    //         "message" => "Duplicate entry. Please check the clock-in time.",
    //     ]);
    // } else {

    // No duplicate record found, proceed with the INSERT query

    // Record doesn't exist, insert a new record
    $assign = $conn->prepare("INSERT INTO flw_survey_attandance (system_id, clock_in, number_team, created_timestamp, survey_username, qr_url, qrcode_session, initial_code, jenis_pelan) VALUES (:systemId, :clockinTime, :numberTeam, :submitted, :user, :qrUrl, 1, :bit64, :pCategory)");
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':clockinTime', $clockinTime);
    $assign->bindParam(':numberTeam', $numberTeam);
    $assign->bindParam(':submitted', $timestamp);
    $assign->bindParam(':user', $user);
    $assign->bindParam(':pCategory', $pCategory);

    $assign->bindParam(':qrUrl', $qrUrl);
    $assign->bindParam(':bit64', $bit64);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 43;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Mengesahkan Kehadiran bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 42
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Pengesahan Kehadiran Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            //query for get user full name.
            $fnquery = "SELECT name FROM view_users WHERE username = :username";
            $fnstmt = $conn->prepare($fnquery);
            $fnstmt->bindParam(':username', $username);
            $fnstmt->execute();
            // Fetch the result
            $userFirstName = $fnstmt->fetchColumn();
            // declare telegram notification string
            // $telegramMsg = "Pengesahan Kehadiran Berjaya. Sila teruskan dengan mengimbas QR Code Kehadiran. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Nama Pengguna : " . $user . " </strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $userFirstName);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            // $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('user', $username, $telegramMsg, 'html');
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

    $message = "Pengesahan Kehadiran Berjaya! ??";

    // Close the database connection
    $conn = null;
} else if ($status == "43") {
    //action = pengesahan waktu masuk kehadiran
    // $action = $POST['action'];
    $systemId = $POST['systemId'];
    $teams = SurveyApi::getUniqueSurveyUsernames($systemId);
    $totalTeam = SurveyApi::totalTeam($systemId);
    $user = $_SESSION['username'];

    // Handle the second POST action
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $timestamp = date('Y-m-d H:i:s', time());

    $flwStatus = 44;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Mengesahkan Waktu masuk bagi ' . $refNo;
    $pages = 'task_mapping';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 43
    //nextstep for mapping
    $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

    $details = 'Pengesahan Waktu Masuk Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        $fnquery = "SELECT name FROM view_users WHERE username = :username";
        $fnstmt = $conn->prepare($fnquery);
        $fnstmt->bindParam(':username', $username);
        $fnstmt->execute();
        // Fetch the result
        $userFirstName = $fnstmt->fetchColumn();
        // declare telegram notification string
        // $telegramMsg = "Pengesahan Waktu Masuk Berjaya. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Nama Pengguna : " . $user . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $userFirstName);
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

    // Close the database connection
    $conn = null;

} else if ($status == "44") {
    //clockout = pengesahan waktu keluar kehadiran
    $systemId = $POST['systemId'];
    $user = $_SESSION['username'];

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);
    $timestamp = date('Y-m-d H:i:s', time());

    $clockoutTime = isset ($POST['clock-out']) ? date('H:i:s', strtotime($POST['clock-out'])) : '';
    $current_date = date('Y-m-d');

    // Update the clock-out time for the first clock-in entry with the same system ID, clock_out is null, and current_date = created_timestamp
    $updateQuery = $conn->prepare("UPDATE flw_survey_attandance SET clock_out = :clockoutTime
                            WHERE clock_in = (
                                SELECT clock_in
                                FROM flw_survey_attandance
                                WHERE system_id = :systemId AND clock_out IS NULL AND date(current_date) = date(created_timestamp::timestamp) AND initial_code IS NOT NULL
                                ORDER BY id DESC LIMIT 1
                            )");

    // Bind the parameters
    $updateQuery->bindParam(':clockoutTime', $clockoutTime);
    $updateQuery->bindParam(':systemId', $systemId);
    // $updateQuery->execute();

    if ($updateQuery->execute()) {

        $flwStatus = 45;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Mengesahkan Waktu Keluar bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 44
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Pengesahan Waktu Keluar Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {

            $fnquery = "SELECT name FROM view_users WHERE username = :username";
            $fnstmt = $conn->prepare($fnquery);
            $fnstmt->bindParam(':username', $username);
            $fnstmt->execute();
            // Fetch the result
            $userFirstName = $fnstmt->fetchColumn();
 
            // declare telegram notification string
            // $telegramMsg = "Pengesahan Waktu Keluar Berjaya. Sila teruskan dengan Kemaskini Kemajuan Laporan Harian \n\n?? <strong> No Rujukan : " . $refNo . "   \n?? Nama Pengguna : " . $user . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $userFirstName);
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

    // Close the database connection
    $conn = null;

    // Disable the submit button to prevent multiple submissions
    // echo '<script>document.getElementById("submit-btn").disabled = true;</script>';
} else if ($status == "46") {
    //kerja lapangan = upload raw data kerja ukur (PIU)
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $remark = $POST['notes'];
    $date = date('Y-m-d H:i:s');
    $timestamp = date('Y-m-d H:i:s');

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Connect to the database
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_appl_survey WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("UPDATE flw_survey_udm SET upload_rdpiu_remark = :remark, upload_rdpiu_date = :date WHERE system_id = :systemId");

    } else {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("INSERT INTO flw_survey_udm (system_id, upload_rdpiu_date, upload_rdpiu_remark) VALUES (:systemId, :date, :remark)");
    }
    // Bind the parameters
    $assign->bindParam(':remark', $remark);
    $assign->bindParam(':date', $date);
    $assign->bindParam(':systemId', $systemId);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 47;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Menyerahkan Data Kerja Ukur bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 46
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Serahan Data Kerja Ukur Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Serahan Data Kerja Ukur Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 33 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 33 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();


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

    $message = "Serahan Data Kerja Ukur Berjaya! ??";

    // Close the database connection
    $conn = null;

} else if ($status == "49") {
    //plan-assign-udm
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);

    $planAssign = isset ($POST['plan-assign-udm']) ? $POST['plan-assign-udm'] : '';
    $dateRange = isset ($POST['modal-date-range']) ? $POST['modal-date-range'] : '';
    list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
    $startDate = !empty ($startDateStr) ? date('Y-m-d', strtotime($startDateStr)) : null;
    $endDate = !empty ($endDateStr) ? date('Y-m-d', strtotime($endDateStr)) : null;
    $timestamp = date('Y-m-d H:i:s', time());

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Execute a SELECT query on the database
    // $checking = $conn->prepare("SELECT id FROM flw_plan_udm WHERE system_id = :systemId ");

    // $checking->bindParam(':systemId', $systemId);
    // $checking->execute();

    // Return the user information to the client
    // if ($checking->rowCount() > 0) {
    //     $assign = $conn->prepare("UPDATE flw_plan_udm SET plan_udm_assignee = :planAssign, est_date_start = :startDate, est_date_end = :endDate, created_timestamp = :submitted WHERE system_id = :systemId");
    // } else {
    $assign = $conn->prepare("INSERT INTO flw_plan_udm (system_id, plan_udm_assignee, est_date_start, est_date_end, submission_name, created_timestamp) VALUES (:systemId, :planAssign, :startDate, :endDate, :user, :submitted)");
    $assign->bindParam(':user', $username);
    // }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':planAssign', $planAssign);
    $assign->bindParam(':startDate', $startDate);
    $assign->bindParam(':endDate', $endDate);
    $assign->bindParam(':submitted', $timestamp);
    $assign->execute();

    // Get the ID of the inserted or updated record
    $insertedId = $conn->lastInsertId();

    // Insert into flw_appl_plan using the retrieved ID
    $applPlanInsert = $conn->prepare("UPDATE flw_appl_plan SET udm_id = :udmId WHERE system_id = :systemId");
    $applPlanInsert->bindParam(':systemId', $systemId);
    $applPlanInsert->bindParam(':udmId', $insertedId);

    if ($applPlanInsert->execute()) {

        $flwStatus = 50;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah melantik pelukis PIU bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 49
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Perlantikan Pelukis PIU Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            //query for get user full name.
            $fnquery = "SELECT name FROM view_users WHERE username = :planAssign";
            $fnstmt = $conn->prepare($fnquery);
            $fnstmt->bindParam(':planAssign', $planAssign);
            $fnstmt->execute();

            // Fetch the result
            $planName = $fnstmt->fetchColumn();

            // declare telegram notification string
            // $telegramMsg = "Perlantikan Pelukis PIU Berjaya. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Pelukis Dilantik : " . $planAssign . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $planName);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('assignee', $planAssign, $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 26, $telegramMsg, 'html');
        };

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

    // Close the database connection
    $conn = null;

} else if ($status == "50") {
    //progress-udm
    $systemId = $POST['systemId'];
    $surveyApi = new SurveyApi($username);
    $length = $surveyApi->getSurveyLength($systemId);
    $progressUDM = intval(Survey::getProgressUDM($systemId));

    $conn = $db->createConnection();

    // $progressUDM = isset($POST['progress-udm']) ? $POST['progress-udm'] : '';
    $progressDaily = isset ($POST['progress-daily-udm']) ? $POST['progress-daily-udm'] : '';
    $timestamp = date('Y-m-d H:i:s', time());
    // $user = $_SESSION['username'];

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_plan_udm WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    $currentProgress = $progressUDM + $progressDaily;
    $progressBalance = intval(($length - $currentProgress) * 100);

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $timeConverted = Utilities::convertDateToMalay($timestamp);

    // Return the user information to the client
    $assign = $conn->prepare("UPDATE flw_plan_udm SET progress_udm = :currentProgress, updated_timestamp = :submitted WHERE system_id = :systemId");

    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':currentProgress', $currentProgress);
    $assign->bindParam(':submitted', $timestamp);
    // $assign->execute();

    if ($assign->execute()) {

        if ($progressBalance <= 0) {

            $flwStatus = 51;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Mengesahkan Kemajuan Pelan Infrastruktur Utiliti Selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 50
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Kemajuan Pelan Infrastruktur Utiliti Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Kemajuan Pelan Infrastruktur Utiliti Selesai. \n\n?? <strong> No Rujukan : " . $refNo . " \n?? Jarak UDM : " . $currentProgress . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, progress: $progressBalance, item: $currentProgress);
                // NOTE - send telegram notification for team survey
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
            $stmt->execute();

        } else {

            $flwStatus = 51;
            // // NOTE - Update Task Assignment
            // $taskAssignment->complete($username, $systemId, $status);
            // $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah mengemaskini Kemajuan Pelan Infrastruktur Utiliti bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 50
            //nextstep for mapping
            // $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Kemajuan Pelan Infrastruktur Utiliti telah dikemaskini bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Kemajuan Pelan Infrastruktur Utiliti telah dikemaskini. \n?? </strong> No Rujukan : " . $refNo . " \n?? Jarak UDM : " . $currentProgress . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, progress: $progressBalance, item: $currentProgress);
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('role', 26, $telegramMsg, 'html');
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
        }

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

    // Close the database connection
    $conn = null;

} else if ($status == "51") {
    //plan-assign-tmp
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);

    $planAssign = isset ($POST['plan-assign-tmp']) ? $POST['plan-assign-tmp'] : '';
    $dateRange = isset ($POST['modal-date-range']) ? $POST['modal-date-range'] : '';
    list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
    $startDate = !empty ($startDateStr) ? date('Y-m-d', strtotime($startDateStr)) : null;
    $endDate = !empty ($endDateStr) ? date('Y-m-d', strtotime($endDateStr)) : null;
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Record exists, update the record
    $assign = $conn->prepare("INSERT INTO flw_plan_tmp (system_id, plan_tmp_assignee, est_date_start, est_date_end, submission_name, created_timestamp) VALUES (:systemId, :planAssign, :startDate, :endDate, :user, :submitted)");
    $assign->bindParam(':user', $username);

    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':planAssign', $planAssign);
    $assign->bindParam(':startDate', $startDate);
    $assign->bindParam(':endDate', $endDate);
    $assign->bindParam(':submitted', $timestamp);
    $assign->execute();

    // Get the ID of the inserted or updated record
    $insertedId = $conn->lastInsertId();

    // Insert into flw_appl_plan using the retrieved ID
    $applPlanInsert = $conn->prepare("UPDATE flw_appl_plan SET tmp_id = :tmpId WHERE system_id = :systemId");

    $applPlanInsert->bindParam(':systemId', $systemId);
    $applPlanInsert->bindParam(':tmpId', $insertedId);

    if ($applPlanInsert->execute()) {

        $flwStatus = 52;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah melantik pelukis TMP bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 51
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Perlantikan Pelukis TMP Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            //query for get user full name.
            $fnquery = "SELECT name FROM view_users WHERE username = :planAssign";
            $fnstmt = $conn->prepare($fnquery);
            $fnstmt->bindParam(':planAssign', $planAssign);
            $fnstmt->execute();

            // Fetch the result
            $planName = $fnstmt->fetchColumn();
            // declare telegram notification string
            // $telegramMsg = "Perlantikan Pelukis PPT Berjaya. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Pelukis Dilantik : " . $planAssign . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $planName);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('assignee', $planAssign, $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 26, $telegramMsg, 'html');
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

    // Close the database connection
    $conn = null;

} else if ($status == "52") {
    //progress-tmp
    $systemId = $POST['systemId'];
    $surveyApi = new SurveyApi($username);
    $length = $surveyApi->getSurveyLength($systemId);
    $progressTMP = intval(Survey::getProgressTMP($systemId));

    $conn = $db->createConnection();

    $progressDaily = isset ($POST['progress-daily-tmp']) ? $POST['progress-daily-tmp'] : '';
    $timestamp = date('Y-m-d H:i:s', time());

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_plan_tmp WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $timeConverted = Utilities::convertDateToMalay($timestamp);

    $currentProgress = $progressTMP + $progressDaily;
    $progressBalance = intval(($length - $currentProgress) * 100);

    // Return the user information to the client
    $assign = $conn->prepare("UPDATE flw_plan_tmp SET progress_tmp = :currentProgress, updated_timestamp = :submitted WHERE system_id = :systemId");

    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':currentProgress', $currentProgress);
    $assign->bindParam(':submitted', $timestamp);
    // $assign->execute();

    if ($assign->execute()) {

        if ($progressBalance <= 0) {

            $flwStatus = 53;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Mengesahkan Kemajuan Pelan Kawalan Trafik Selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 52
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Kemajuan Pelan Kawalan Trafik Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Kemajuan Pelan Kawalan Trafik Selesai. \n\n<strong>?? No Rujukan : " . $refNo . " \n?? Jarak TMP : " . $currentProgress . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, progress: $progressBalance, item: $currentProgress);
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
                $telegram->sendMessage('role', 28, $telegramMsg, 'html');
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

        } else {

            $flwStatus = 53;
            // // NOTE - Update Task Assignment
            // $taskAssignment->complete($username, $systemId, $status);
            // $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah mengemaskini Kemajuan Pelan Kawalan Trafik bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 50
            //nextstep for mapping
            // $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Kemajuan Pelan Kawalan Trafik telah dikemaskini bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Kemajuan Pelan Kawalan Trafik telah dikemaskini. \n<strong>?? No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, progress: $progressBalance, item: $currentProgress);
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('role', 26, $telegramMsg, 'html');
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
        }

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

    // Close the database connection
    $conn = null;

} else if ($status == "53") {
    //sr-arrival-date
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $srArrivalDate = isset ($POST['sr-arrival-date']) ? $POST['sr-arrival-date'] : '';
    $srArrivalRemark = isset ($POST['notes']) ? $POST['notes'] : '';

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_appl_plan WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Record exists, update the record
        $assign = $conn->prepare("UPDATE flw_plan_udm SET sr_arrival_date = :srArrivalDate, sr_arrival_remark = :srArrivalRemark WHERE system_id = :systemId");
    } else {
        // Record doesn't exist, insert a new record
        $assign = $conn->prepare("INSERT INTO flw_plan_udm (system_id, sr_arrival_date, sr_arrival_remark) VALUES   (:systemId, :srArrivalDate, :srArrivalRemark)");
    }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':srArrivalDate', $srArrivalDate);
    $assign->bindParam(':srArrivalRemark', $srArrivalRemark);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 54;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah menetapkan Tarikh Pengesahan Juru Ukur bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 53
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Tarikh Pengesahan Juru Ukur telah ditetapkan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Tarikh Pengesahan Juru Ukur telah ditetapkan. \n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh yang ditetapkan : " . $srArrivalDate . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $srArrivalDate);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');

            // Set your API parameters
            $whatsappMsg = "Tarikh Pengesahan Juru Ukur telah ditetapkan. \n\n No Rujukan : " . $refNo . " \n Tarikh yang ditetapkan : " . $srArrivalDate . "";

            $getSRContact = $conn->prepare("SELECT phone_no FROM ls_sr_contact WHERE state = :state");
            $getSRContact->bindParam(':state', $state);
            $getSRContact->execute();

            $result = $getSRContact->fetch(PDO::FETCH_ASSOC);

            $SRContact = $result['phone_no'];

            $applData = array(
                "secret" => $system->App->secret,
                "notification" => array(
                    "message" => $whatsappMsg,
                    "phones" => [
                        $SRContact
                    ],
                    "attachments" => [
                    ]
                )
            );

            // Convert the array to JSON
            $jsonData = json_encode($applData);

            // https //:wapi.asiadebut.tech portnumber:8552
            //     / api / chat / nophone / message;
            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/notify/' . $systemId, $jsonData);

            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

            // Decode the response JSON
            $curlResult = json_decode($apiRequest['response']);
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



    // Close the database connection
    $conn = null;

} else if ($status == "54") {
    //set-sr-sign
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $setSRSign = isset ($POST['set-sr-sign']) ? $POST['set-sr-sign'] : '';

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_appl_plan WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Record exists, update the record
        $assign = $conn->prepare("UPDATE flw_plan_udm SET sr_total_sign = :setSRSign WHERE system_id = :systemId");
    } else {
        // Record doesn't exist, insert a new record
        $assign = $conn->prepare("INSERT INTO flw_plan_udm (system_id, sr_total_sign) VALUES (:systemId, :setSRSign)");
    }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':setSRSign', $setSRSign);
    $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 55;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan cop bilangan endorsement bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 54
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

        $details = 'Pengesahan Cop Bilangan Endorsement bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsg = "Pengesahan Cop Bilangan Endorsement. \n\n<strong>?? No Rujukan : " . $refNo . " \n?? Bilangan Set Pelan : " . $setSRSign . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $setSRSign);
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

    // Close the database connection
    $conn = null;

} else if ($status == "55") {
    //udm upload
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    $submitted = date('Y-m-d');
    
    // $value = SurveyApi::getUploadData($systemId);
    $surveyProvider = SurveyApi::checkSurveyProvider($systemId);
    $currentLog = SurveyApi::getLogUdm($systemId);
    $logUdm = $currentLog + 1;

    $conn = $db->createConnection();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $query9 = $conn->prepare("SELECT survey_provider FROM flw_survey_udm WHERE system_id = :systemId");
    $query9->bindParam(':systemId', $systemId);
    $query9->execute();
    $row9 = $query9->fetch(PDO::FETCH_ASSOC);

    if($surveyProvider == 2) {
        $srArrivalDate = isset ($POST['sr-arrival-date']) ? $POST['sr-arrival-date'] : '';
        $srArrivalRemark = isset ($POST['notes']) ? $POST['notes'] : ''; 
        $panelName = isset ($POST['survey-panel-name']) ? $POST['survey-panel-name'] : ''; 

        // Check if the row exists
        $query_select = $conn->prepare("SELECT * FROM flw_plan_udm WHERE system_id = :systemId");
        $query_select->bindParam(':systemId', $systemId);
        $query_select->execute();
        $row_count = $query_select->rowCount();

        if ($row_count > 0) {
            // Row exists, perform update
            $query1 = $conn->prepare("UPDATE flw_plan_udm SET submitted_udm = :submitted, log_pelan = :logUdm, sr_arrival_date = :srArrivalDate, sr_arrival_remark = :srArrivalRemark, plan_udm_assignee = :panelName, updated_timestamp = :timestamp WHERE system_id = :systemId");
            $query1->bindParam(':systemId', $systemId);
            $query1->bindParam(':logUdm', $logUdm);
            $query1->bindParam(':submitted', $submitted);
            $query1->bindParam(':timestamp', $timestamp);
            $query1->bindParam(':srArrivalDate', $srArrivalDate);
            $query1->bindParam(':srArrivalRemark', $srArrivalRemark);
            $query1->bindParam(':panelName', $panelName);
            // $query1->execute();
        } else {
            // Row doesn't exist, perform insert
            $query1 = $conn->prepare("INSERT INTO flw_plan_udm (system_id, submitted_udm, log_pelan, sr_arrival_date, sr_arrival_remark, plan_udm_assignee, created_timestamp) VALUES (:systemId, :submitted, :logUdm, :srArrivalDate, :srArrivalRemark, :panelName, :timestamp)");
            $query1->bindParam(':systemId', $systemId);
            $query1->bindParam(':logUdm', $logUdm);
            $query1->bindParam(':submitted', $submitted);
            $query1->bindParam(':timestamp', $timestamp);
            $query1->bindParam(':srArrivalDate', $srArrivalDate);
            $query1->bindParam(':srArrivalRemark', $srArrivalRemark);
            $query1->bindParam(':panelName', $panelName);
            // $query1->execute();
        }
        
    } else {
        // Check if the row exists
        $query_select = $conn->prepare("SELECT * FROM flw_plan_udm WHERE system_id = :systemId");
        $query_select->bindParam(':systemId', $systemId);
        $query_select->execute();
        $row_count = $query_select->rowCount();

        if ($row_count > 0) {
            // Row exists, perform update
            $query1 = $conn->prepare("UPDATE flw_plan_udm SET submitted_udm = :submitted, log_pelan = :logUdm WHERE system_id = :systemId");
            $query1->bindParam(':systemId', $systemId);
            $query1->bindParam(':logUdm', $logUdm);
            $query1->bindParam(':submitted', $submitted);
            // $query1->execute();
        } else {
            // Row doesn't exist, perform insert
            $query1 = $conn->prepare("INSERT INTO flw_plan_udm (system_id, submitted_udm, log_pelan) VALUES (:systemId, :submitted, :logUdm)");
            $query1->bindParam(':systemId', $systemId);
            $query1->bindParam(':logUdm', $logUdm);
            $query1->bindParam(':submitted', $submitted);
            // $query1->execute();
        }
    }

    if ($query1->execute()) {

        if ($surveyProvider == 1) {
            $flwStatus = 155; //Inhouse : upload TMP
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah memuatnaik PIU bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 55
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Muatnaik PIU Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Muatnaik PIU Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                // NOTE - send telegram notification for team survey
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
            $stmt->execute();

            // Finally, return a JSON
            echo json_encode([
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
            ]);
        } else if ($surveyProvider == 2) {
            $flwStatus = 57; //Outsource : Semak PIU
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah memuatnaik PIU bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 55
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Muatnaik PIU Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Muatnaik PIU Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
            };

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
        }

    } else {
        http_response_code(500);
        $result = array(
            "success" => false,
            "message" => "failed",
            "status" => 500,
        );
    }

    // Close the database connection
    $conn = null;

} else if ($status == "56") {
    //handover
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);

    $handoverDate = date('Y-m-d H:i:s', time());
    $handoverRemark = isset ($POST['notes']) ? $POST['notes'] : '';
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_appl_plan WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("UPDATE flw_plan_udm SET handover_date = :handoverDate, handover_remark = :handoverRemark WHERE system_id = :systemId");
    } else {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("INSERT INTO flw_plan_udm (system_id, handover_date, handover_remark, submission_name) VALUES (:systemId, :handoverDate, :handoverRemark, :user)");

        $assign->bindParam(':user', $user);
    }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':handoverDate', $handoverDate);
    $assign->bindParam(':handoverRemark', $handoverRemark);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 97;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        // $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah menyerahkan PIU & PPT bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 56
        //nextstep for mapping
        // $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

        $details = 'PIU & PPT telah diserahkan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsg = "PIU & PPT telah diserahkan. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow(57, refNo: $refNo);
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

    // Close the database connection
    $conn = null;

} else if ($status == "58") {
    //Pembetulan PIU
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    // $value = SurveyApi::getUploadData($systemId);
    $currentLog = SurveyApi::getLogUdm($systemId);
    $logUdm = $currentLog + 1;

    $conn = $db->createConnection();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Update submitted_udm in flw_plan_udm
    $query1 = $conn->prepare("UPDATE flw_plan_udm SET correction_date = :submitted, log_pelan = :logUdm WHERE system_id = :systemId");
    $query1->bindParam(':systemId', $systemId);
    $query1->bindParam(':logUdm', $logUdm);
    $query1->bindParam(':submitted', $timestamp);
    // $query1->execute();

    if ($query1->execute()) {

        $flwStatus = 57; //Outsource : Review Udm
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah memuatnaik Pembetulan PIU bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 58
        //nextstep for mapping
        $NextStatus = $flow->goToPrevFlow($systemId, "mapping", "BF", Steps: $flwStatus);

        $details = 'Muatnaik Pembetulan PIU Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsg = "Muatnaik Pembetulan PIU Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
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

    // Close the database connection
    $conn = null;

} else if ($status == "60") {
    //Pembetulan PPT
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    // $value = SurveyApi::getUploadData($systemId);
    $surveyProvider = SurveyApi::checkSurveyProvider($systemId);
    $currentLog = SurveyApi::getLogTmp($systemId);
    $logTmp = $currentLog + 1;

    $conn = $db->createConnection();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Update submitted_udm in flw_plan_udm
    $query1 = $conn->prepare("UPDATE flw_plan_tmp SET correction_date = :submitted, log_pelan = :logTmp WHERE system_id = :systemId");
    $query1->bindParam(':systemId', $systemId);
    $query1->bindParam(':logTmp', $logTmp);
    $query1->bindParam(':submitted', $timestamp);
    // $query1->execute();

    if ($query1->execute()) {

        $flwStatus = 59; //Outsource : Review Tmp
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah memuatnaik Pembetulan PPT bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 60
        //nextstep for mapping
        $NextStatus = $flow->goToPrevFlow($systemId, "mapping", "BF", Steps: $flwStatus);

        $details = 'Muatnaik Pembetulan PPT Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsg = "Muatnaik Pembetulan PPT Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
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

    // Close the database connection
    $conn = null;

} else if ($status == "97") {
    //survey-team-assign
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);

    $surveyTeamAssign = isset ($POST['survey-team-assign']) ? $POST['survey-team-assign'] : '';
    $surveyGroupLeader = SurveyApi::getSurveyLeaderGroup($surveyTeamAssign);
    // $surveyLeaderAssign = isset($POST['survey-leader-assign']) ? $POST['survey-leader-assign'] : '';
    $dateRange = isset ($POST['modal-date-range']) ? $POST['modal-date-range'] : '';
    list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
    $startDate = date('Y-m-d', strtotime($startDateStr));
    $endDate = date('Y-m-d', strtotime($endDateStr));
    $timestamp = date('Y-m-d H:i:s', time());
    $user = $_SESSION['username'];
    $surveyProvider = isset ($POST['survey-provider']) ? $POST['survey-provider'] : '';

    //To get reference number

    $refNo = Utilities::getRefNo($systemId);

    // //NOTE: get team
    $query4 = $conn->prepare("SELECT survey_team FROM flw_survey_team WHERE id = :surveyTeamAssign AND is_active = true");

    // // Bind the parameters
    $query4->bindParam(':surveyTeamAssign', $surveyTeamAssign);
    $query4->execute();

    // // Fetch the result row
    $row4 = $query4->fetch(PDO::FETCH_ASSOC);

    // // Extract the team value from the row
    $team = $row4['survey_team'];


    $timeConverted = Utilities::convertDateToMalay($timestamp);

    // $triggerSPKU = isset($POST['trigger-spku']) ? $POST['trigger-spku'] : '';

    // Execute a SELECT query on the database
    $checking2 = $conn->prepare("SELECT id FROM flw_survey_asb WHERE system_id = :systemId ");

    $checking2->bindParam(':systemId', $systemId);
    $checking2->execute();

    // Return the user information to the client
    if ($checking2->rowCount() > 0) {
        // Execute a SELECT query on the database
        $assign2 = $conn->prepare("UPDATE flw_survey_asb SET team_id = :surveyTeamAssign, est_date_start = :startDate, est_date_end = :endDate, submission_date = :submitted, submission_name = :user, survey_provider = :surveyProvider WHERE system_id = :systemId");
    } else {
        // Execute a SELECT query on the database
        $assign2 = $conn->prepare("INSERT INTO flw_survey_asb (system_id, team_id, est_date_start, est_date_end, submission_date, submission_name, survey_provider) VALUES (:systemId, :surveyTeamAssign, :startDate, :endDate, :submitted, :user, :surveyProvider)");
    }
    // Bind the parameters
    $assign2->bindParam(':surveyTeamAssign', $surveyTeamAssign);
    // $assi  gn->bindParam(':surveyLeaderAssign', $surveyLeaderAssign);
    $assign2->bindParam(':startDate', $startDate);
    $assign2->bindParam(':endDate', $endDate);
    $assign2->bindParam(':submitted', $timestamp);
    $assign2->bindParam(':user', $user);
    $assign2->bindParam(':systemId', $systemId);
    $assign2->bindParam(':surveyProvider', $surveyProvider);
    $assign2->execute();

    // Check if the query was successful
    if ($assign2->rowCount() > 0) {
        // For INSERT query, get the last inserted ID
        if ($checking2->rowCount() == 0) {
            $lastInsertedId = $conn->lastInsertId();
        }
    } else {
        // Execute a SELECT query on the database
        $select = $conn->prepare("SELECT id FROM flw_survey_asb WHERE system_id = :systemId ");

        $select->bindParam(':systemId', $systemId);
        $select->execute();
        // Fetch the result from the executed SELECT query
        $result = $select->fetch(PDO::FETCH_ASSOC);
        // Retrieve the ID from the fetched result
        $lastInsertedId = $result['id'];
    }

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_appl_survey WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("UPDATE flw_appl_survey SET asb_id = :asbId, submission_date = :submitted WHERE system_id = :systemId");
    } else {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("INSERT INTO flw_appl_survey (system_id, asb_id, submission_date) VALUES (:systemId, :asbId, :submitted)");
    }
    // Bind the parameters
    $assign->bindParam(':asbId', $lastInsertedId);
    $assign->bindParam(':submitted', $timestamp);
    $assign->bindParam(':systemId', $systemId);

    if ($assign->execute()) {

        $flwStatus = 98;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah menyelesaikan pembahagian tugasan pengukuran (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 97
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Pembahagian Tugasan Pengukuran (PSB) Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $message = "Pembahagian Tugasan Pengukuran Selesai.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? Kumpulan : " . $team . "</strong>";
            $message = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, date: $timeConverted, item: $team);

            // NOTE - send telegram notification for role 28-Ketua Survey
            $response = $telegram->sendMessage('group', 'management', $message, 'html');
            $response = $telegram->sendMessage('role', 28, $message, 'html');

            // declare telegram notification string
            $message2 = "Pembahagian Tugasan Pengukuran (PSB) Selesai. Sila Mula Kerja Ukur.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? Kumpulan : " . $team . "</strong>";

            // NOTE - send telegram notification for role 23-Ketua Kumpulan Ukur Survey
            $response4 = $telegram->sendMessage('group', 'management', $message2, 'html');
            $response4 = $telegram->sendMessage('assignee', $surveyGroupLeader, $message2, 'html');
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


    // header('HTTP/2 200 OK');
    // echo json_encode([
    //     "success"   => "Success",
    //     "status" => 200,
    // ]);

    // Close the database connection
    $conn = null;

} else if ($status == "98") {
    //clockin = pengesahan kehadiran hari ini
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);
    $pCategory = 'PSB';

    function generateRandom64BitString()
    {
        $randomBytes = random_bytes(8); // 64 bits = 8 bytes
        return bin2hex($randomBytes);
    }
    $bit64 = generateRandom64BitString();
    // $bit64 = isset($POST['bit64']) ? $POST['bit64'] : '';
    // var_dump($bit64);
    // $systemId = $POST['system-id'];
    $protocol = ((!empty ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $protocol . $_SERVER['HTTP_HOST'];
    $qrUrl = $host . "/surveys/signin/{$systemId}/{$bit64}";

    $clockinTime = isset ($POST['clock-in']) ? date('H:i:s', strtotime($POST['clock-in'])) : '';
    $numberTeam = isset ($POST['number-team']) ? intval($POST['number-team']) : null;
    $timestamp = date('Y-m-d H:i:s', time());
    $user = $_SESSION['username'];
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Check if a record with the same system ID and clock-in time already exists
    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM flw_survey_attandance WHERE system_id = :systemId AND clock_in = :clockinTime");

    $stmtCheck->bindParam(':systemId', $systemId);
    $stmtCheck->bindParam(':clockinTime', $clockinTime);
    $stmtCheck->execute();

    $recordCount = $stmtCheck->fetchColumn();
    // if ($recordCount > 0) {
    //     // A record already exists, handle the duplication error
    //     echo json_encode([
    //         "success" => "Error",
    //         "status" => 400,
    //         "message" => "Duplicate entry. Please check the clock-in time.",
    //     ]);
    // } else {

    // No duplicate record found, proceed with the INSERT query

    // Record doesn't exist, insert a new record
    $assign = $conn->prepare("INSERT INTO flw_survey_attandance (system_id, clock_in, number_team, created_timestamp, survey_username, qr_url, qrcode_session, initial_code, jenis_pelan) VALUES (:systemId, :clockinTime, :numberTeam, :submitted, :user, :qrUrl, 1, :bit64, :pCategory)");
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':clockinTime', $clockinTime);
    $assign->bindParam(':numberTeam', $numberTeam);
    $assign->bindParam(':submitted', $timestamp);
    $assign->bindParam(':user', $user);
    $assign->bindParam(':pCategory', $pCategory);

    $assign->bindParam(':qrUrl', $qrUrl);
    $assign->bindParam(':bit64', $bit64);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 99;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Mengesahkan Kehadiran (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 98
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Pengesahan Kehadiran (PSB) Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Pengesahan Kehadiran Berjaya. Sila teruskan dengan mengimbas QR Code Kehadiran. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Nama Pengguna : " . $user . " </strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $user);
            // NOTE - send telegram notification for team survey
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

    $message = "Pengesahan Kehadiran Berjaya! ??";

    // Close the database connection
    $conn = null;
} else if ($status == "99") {
    //action = pengesahan waktu masuk kehadiran
    // $action = $POST['action'];
    $systemId = $POST['systemId'];
    $teams = SurveyApi::getUniqueSurveyUsernames($systemId);
    $totalTeam = SurveyApi::totalTeam($systemId);
    $user = $_SESSION['username'];

    // Handle the second POST action
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $timestamp = date('Y-m-d H:i:s', time());

    $flwStatus = 100;
    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah Mengesahkan Waktu masuk (PSB) bagi ' . $refNo;
    $pages = 'task_mapping';
    $changelog->userActivity($text, $pages);

    // NOTE - current status = 99
    //nextstep for mapping
    $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

    $details = 'Pengesahan Waktu Masuk (PSB) Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // declare telegram notification string
        // $telegramMsg = "Pengesahan Waktu Masuk Berjaya. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Nama Pengguna : " . $user . "</strong>";
        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $user);
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

    // Close the database connection
    $conn = null;

} else if ($status == "100") {
    //clockout = pengesahan waktu keluar kehadiran
    $systemId = $POST['systemId'];
    $user = $_SESSION['username'];

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);
    $timestamp = date('Y-m-d H:i:s', time());

    $clockoutTime = isset ($POST['clock-out']) ? date('H:i:s', strtotime($POST['clock-out'])) : '';
    $current_date = date('Y-m-d');

    // Update the clock-out time for the first clock-in entry with the same system ID, clock_out is null, and current_date = created_timestamp
    $updateQuery = $conn->prepare("UPDATE flw_survey_attandance SET clock_out = :clockoutTime
                            WHERE clock_in = (
                                SELECT clock_in
                                FROM flw_survey_attandance
                                WHERE system_id = :systemId AND clock_out IS NULL AND date(current_date) = date(created_timestamp::timestamp) AND initial_code IS NOT NULL
                                ORDER BY id DESC LIMIT 1
                            )");

    // Bind the parameters
    $updateQuery->bindParam(':clockoutTime', $clockoutTime);
    $updateQuery->bindParam(':systemId', $systemId);
    // $updateQuery->execute();

    if ($updateQuery->execute()) {

        $flwStatus = 101;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Mengesahkan Waktu Keluar (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 100
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Pengesahan Waktu Keluar (PSB) Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Pengesahan Waktu Keluar Berjaya. Sila teruskan dengan Kemaskini Kemajuan Laporan Harian \n\n?? <strong> No Rujukan : " . $refNo . "   \n?? Nama Pengguna : " . $user . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $user);
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

    // Close the database connection
    $conn = null;

    // Disable the submit button to prevent multiple submissions
    // echo '<script>document.getElementById("submit-btn").disabled = true;</script>';
} else if ($status == "102") {
    //kerja lapangan = upload raw data kerja ukur (PSB)
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $remark = $POST['notes'];
    $date = date('Y-m-d H:i:s');
    $timestamp = date('Y-m-d H:i:s');

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Connect to the database
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_survey_asb WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("UPDATE flw_survey_asb SET upload_rdpsb_remark = :remark, upload_rdpsb_date = :date WHERE system_id = :systemId");

    } else {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("INSERT INTO flw_survey_asb (system_id, upload_rdpsb_date, upload_rdpsb_remark) VALUES (:systemId, :date, :remark)");
    }
    // Bind the parameters
    $assign->bindParam(':remark', $remark);
    $assign->bindParam(':date', $date);
    $assign->bindParam(':systemId', $systemId);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 103;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah Menyerahkan Data Kerja Ukur (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 102
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Serahan Data Kerja Ukur (PSB) Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Serahan Data Kerja Ukur Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
        }
        ;

        $chronoId = $chronology->create($username, $systemId, $status, 'file');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        $query2 = "UPDATE flw_appl_attachments SET chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = 82 AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 82 ORDER BY id DESC LIMIT 1)";

        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':chronology_id', $chronoId);
        $stmt2->execute();


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

    $message = "Serahan Data Kerja Ukur Berjaya! ??";

    // Close the database connection
    $conn = null;

} else if ($status == "105") {
    //plan-assign-asb
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);

    $planAssign = isset ($POST['plan-assign-asb']) ? $POST['plan-assign-asb'] : '';
    $dateRange = isset ($POST['modal-date-range']) ? $POST['modal-date-range'] : '';
    list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
    $startDate = !empty ($startDateStr) ? date('Y-m-d', strtotime($startDateStr)) : null;
    $endDate = !empty ($endDateStr) ? date('Y-m-d', strtotime($endDateStr)) : null;
    $timestamp = date('Y-m-d H:i:s', time());
    $uuid = SurveyApi::generateUUID();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Execute a SELECT query on the database
    // $checking = $conn->prepare("SELECT id FROM flw_plan_udm WHERE system_id = :systemId ");

    // $checking->bindParam(':systemId', $systemId);
    // $checking->execute();

    // Return the user information to the client
    // if ($checking->rowCount() > 0) {
    //     $assign = $conn->prepare("UPDATE flw_plan_udm SET plan_udm_assignee = :planAssign, est_date_start = :startDate, est_date_end = :endDate, created_timestamp = :submitted WHERE system_id = :systemId");
    // } else {
    $assign = $conn->prepare("INSERT INTO flw_plan_asb (system_id, plan_asb_assignee, est_date_start, est_date_end, submission_name, created_timestamp) VALUES (:systemId, :planAssign, :startDate, :endDate, :user, :submitted)");
    $assign->bindParam(':user', $username);
    // }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':planAssign', $planAssign);
    $assign->bindParam(':startDate', $startDate);
    $assign->bindParam(':endDate', $endDate);
    $assign->bindParam(':submitted', $timestamp);
    $assign->execute();

    // Get the ID of the inserted or updated record
    $insertedId = $conn->lastInsertId();

    // Insert into flw_appl_plan using the retrieved ID
    $applPlanInsert = $conn->prepare("INSERT INTO flw_appl_plan (system_id, asb_id, created_timestamp, sharing_code) VALUES (:systemId, :asbId, :submitted, :uuid)");

    $applPlanInsert->bindParam(':systemId', $systemId);
    $applPlanInsert->bindParam(':asbId', $insertedId);
    $applPlanInsert->bindParam(':submitted', $timestamp);
    $applPlanInsert->bindParam(':uuid', $uuid);

    if ($applPlanInsert->execute()) {

        $flwStatus = 106;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah melantik pelukis PSB bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 105
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Perlantikan Pelukis PSB Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Perlantikan Pelukis PSB Berjaya. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Pelukis Dilantik : " . $planAssign . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $planAssign);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('assignee', $planAssign, $telegramMsg, 'html');
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

    // Close the database connection
    $conn = null;

} else if ($status == "106") {
    //progress-asb
    $systemId = $POST['systemId'];
    $surveyApi = new SurveyApi($username);
    $length = $surveyApi->getSurveyLengthASB($systemId);
    $progressASB = intval(Survey::getProgressASB($systemId));

    $conn = $db->createConnection();

    // $progressASB = isset($POST['progress-asb']) ? $POST['progress-asb'] : '';
    $progressDaily = isset ($POST['progress-daily-asb']) ? $POST['progress-daily-asb'] : '';
    $timestamp = date('Y-m-d H:i:s', time());
    // $user = $_SESSION['username'];

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_plan_asb WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    $currentProgress = $progressASB + $progressDaily;
    $progressBalance = intval(($length - $currentProgress) * 100);

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $timeConverted = Utilities::convertDateToMalay($timestamp);

    // Return the user information to the client
    $assign = $conn->prepare("UPDATE flw_plan_asb SET progress_asb = :currentProgress, updated_timestamp = :submitted WHERE system_id = :systemId");

    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':currentProgress', $currentProgress);
    $assign->bindParam(':submitted', $timestamp);
    // $assign->execute();

    if ($assign->execute()) {

        if ($progressBalance <= 0) {

            $flwStatus = 107;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Mengesahkan Kemajuan Pelan Siap Bina Selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 106
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Kemajuan Pelan Siap Bina Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Kemajuan Pelan Infrastruktur Utiliti Selesai. \n\n?? <strong> No Rujukan : " . $refNo . " \n?? Jarak UDM : " . $currentProgress . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, progress: $progressBalance, item: $currentProgress);
                // NOTE - send telegram notification for team survey
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
            $stmt->execute();

        } else {

            $flwStatus = 107;
            // // NOTE - Update Task Assignment
            // $taskAssignment->complete($username, $systemId, $status);
            // $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah mengemaskini Kemajuan Pelan Siap Bina bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 106
            //nextstep for mapping
            // $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Kemajuan Pelan Siap Bina telah dikemaskini bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Kemajuan Pelan Infrastruktur Utiliti telah dikemaskini. \n?? </strong> No Rujukan : " . $refNo . " \n?? Jarak UDM : " . $currentProgress . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, progress: $progressBalance, item: $currentProgress);
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
        }

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

    // Close the database connection
    $conn = null;

} else if ($status == "107") {
    //sr-arrival-date
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $srArrivalDate = isset ($POST['sr-arrival-date']) ? $POST['sr-arrival-date'] : '';
    $srArrivalRemark = isset ($POST['notes']) ? $POST['notes'] : '';

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_plan_asb WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Record exists, update the record
        $assign = $conn->prepare("UPDATE flw_plan_asb SET sr_arrival_date = :srArrivalDate, sr_arrival_remark = :srArrivalRemark WHERE system_id = :systemId");
    } else {
        // Record doesn't exist, insert a new record
        $assign = $conn->prepare("INSERT INTO flw_plan_asb (system_id, sr_arrival_date, sr_arrival_remark) VALUES   (:systemId, :srArrivalDate, :srArrivalRemark)");
    }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':srArrivalDate', $srArrivalDate);
    $assign->bindParam(':srArrivalRemark', $srArrivalRemark);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 108;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah menetapkan Tarikh Pengesahan Juru Ukur (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 107
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Tarikh Pengesahan Juru Ukur telah ditetapkan (PSB) bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Tarikh Pengesahan Juru Ukur telah ditetapkan. \n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh yang ditetapkan : " . $srArrivalDate . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $srArrivalDate);
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');

            // Set your API parameters
            $whatsappMsg = "Tarikh Pengesahan Juru Ukur (PSB) telah ditetapkan. \n\n No Rujukan : " . $refNo . " \n Tarikh yang ditetapkan : " . $srArrivalDate . "";

            $getSRContact = $conn->prepare("SELECT phone_no FROM ls_sr_contact WHERE state = :state");
            $getSRContact->bindParam(':state', $state);
            $getSRContact->execute();

            $result = $getSRContact->fetch(PDO::FETCH_ASSOC);

            $SRContact = $result['phone_no'];

            $applData = array(
                "secret" => $system->App->secret,
                "notification" => array(
                    "message" => $whatsappMsg,
                    "phones" => [
                        $SRContact
                    ],
                    "attachments" => [
                    ]
                )
            );

            // Convert the array to JSON
            $jsonData = json_encode($applData);

            // https //:wapi.asiadebut.tech portnumber:8552
            //     / api / chat / nophone / message;
            // make api request to extercord
            $apiRequest = $Curl->request($ec_url.'/gateway/internal/notify/' . $systemId, $jsonData);

            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

            // Decode the response JSON
            $curlResult = json_decode($apiRequest['response']);
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



    // Close the database connection
    $conn = null;

} else if ($status == "108") {
    //set-sr-sign
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    $setSRSign = isset ($POST['set-sr-sign']) ? $POST['set-sr-sign'] : '';

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_plan_asb WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Record exists, update the record
        $assign = $conn->prepare("UPDATE flw_plan_asb SET sr_total_sign = :setSRSign WHERE system_id = :systemId");
    } else {
        // Record doesn't exist, insert a new record
        $assign = $conn->prepare("INSERT INTO flw_plan_asb (system_id, sr_total_sign) VALUES (:systemId, :setSRSign)");
    }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':setSRSign', $setSRSign);
    $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 109;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan cop bilangan endorsement (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 108
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Pengesahan Cop Bilangan Endorsement (PSB) bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Pengesahan Cop Bilangan Endorsement. \n\n?? <strong> No Rujukan : " . $refNo . " \n?? Bilangan Set Pelan : " . $setSRSign . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, item: $setSRSign);
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

    // Close the database connection
    $conn = null;

} else if ($status == "109") {
    //asb upload
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());

    $conn = $db->createConnection();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Update submitted_udm in flw_plan_asb
    $query1 = $conn->prepare("UPDATE flw_plan_asb SET submitted_asb = :submitted WHERE system_id = :systemId");
    $query1->bindParam(':systemId', $systemId);
    $query1->bindParam(':submitted', $timestamp);
    $query1->execute();

    if ($query1->rowCount() > 0) {

        $flwStatus = 110;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah memuatnaik PSB bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 109
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Muatnaik PSB Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Muatnaik PSB Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
            $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
            // NOTE - send telegram notification for team survey
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

    // Close the database connection
    $conn = null;

} else if ($status == "110") {
    //handover
    $systemId = $POST['systemId'];
    $projectStatus = SurveyApi::getProjectStatus($systemId);

    $handoverDate = date('Y-m-d H:i:s', time());
    $handoverRemark = isset ($POST['notes']) ? $POST['notes'] : '';
    $timestamp = date('Y-m-d H:i:s', time());
    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Execute a SELECT query on the database
    $checking = $conn->prepare("SELECT id FROM flw_plan_asb WHERE system_id = :systemId ");

    $checking->bindParam(':systemId', $systemId);
    $checking->execute();

    // Return the user information to the client
    if ($checking->rowCount() > 0) {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("UPDATE flw_plan_asb SET handover_date = :handoverDate, handover_remark = :handoverRemark WHERE system_id = :systemId");
    } else {
        // Execute a SELECT query on the database
        $assign = $conn->prepare("INSERT INTO flw_plan_asb (system_id, handover_date, handover_remark, submission_name) VALUES (:systemId, :handoverDate, :handoverRemark, :user)");

        $assign->bindParam(':user', $user);
    }
    // Bind the parameters
    $assign->bindParam(':systemId', $systemId);
    $assign->bindParam(':handoverDate', $handoverDate);
    $assign->bindParam(':handoverRemark', $handoverRemark);
    // $assign->execute();

    if ($assign->execute()) {

        $flwStatus = 57;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah menyerahkan PIU & PPT bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 56
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'PIU & PPT telah diserahkan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "PIU & PPT telah diserahkan. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
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

    // Close the database connection
    $conn = null;

} else if ($status == "158") {
    $timestamp = date('Y-m-d H:i:s', time());
    $systemId = $POST['systemId'];
    $verifySC = isset ($POST['verify_service_charge']) ? $POST['verify_service_charge'] : 0;

    if ($verifySC == 1) {
        //verify the charge
        $stmt2 = $conn->prepare('UPDATE flw_appl_verifies SET srvc_charge_included = :verify, srvc_charge_verify_date = :scVerifyDate WHERE system_id = :systemId');

        $verify = true;
        $stmt2->bindParam(':verify', $verify);
        $stmt2->bindParam(':scVerifyDate', $timestamp);
        $stmt2->bindParam(':systemId', $systemId);

        if ($stmt2->execute()) {

            //get ref no
            $refNo = Utilities::getRefNo($systemId);

            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);

            $stmt1 = $conn->prepare('SELECT flw_appl_entries.project_title FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
            $stmt1->bindParam(':systemId', $systemId);
            $stmt1->execute();
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $project_title = $result['project_title'];

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah mengesahkan Pengecualian Caj Perkhidmatan bagi permohonan ' . $refNo;
            $pages = 'task_finance';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 158
            //nextstep for finance
            $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");

            $details = 'Caj Perkhidmatan dikecualikan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // NOTE - send telegram notification for team management - finance
                $manageMsg = "Permohonan ini mendapat pengecualian caj perkhidmatan. \n\n<strong>?? No Rujukan : " . $refNo . "\n?? Tajuk : " . $project_title . "</strong>";
                $telegramResponse = $telegram->sendMessage('group', 'management', $manageMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'registration', $manageMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'finance', $manageMsg, 'html');
            };

            $chronoId = $chronology->create($username, $systemId, $status, 'notes');

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

        }

        $message = 'Caj Perkhidmatan Disahkan ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "message" => $message,
            "status" => 200
        ]);

        $conn = null;

    } else {
        //verify the charge
        $stmt2 = $conn->prepare('UPDATE flw_appl_verifies SET srvc_charge_included = :verify, srvc_charge_verify_date = :scVerifyDate WHERE system_id = :systemId');

        $verify = true;
        $stmt2->bindParam(':verify', $verifySC, PDO::PARAM_BOOL);
        $stmt2->bindParam(':scVerifyDate', $timestamp);
        $stmt2->bindParam(':systemId', $systemId);

        if ($stmt2->execute()) {

            //get ref no
            $refNo = Utilities::getRefNo($systemId);

            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);

            $stmt1 = $conn->prepare('SELECT flw_appl_entries.project_title FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
            $stmt1->bindParam(':systemId', $systemId);
            $stmt1->execute();
            $result = $stmt1->fetch(PDO::FETCH_ASSOC);
            $project_title = $result['project_title'];

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah mengesahkan caj perkhidmatan tidak dikecualikan bagi permohonan ' . $refNo;
            $pages = 'task_finance';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 158
            //nextstep for finance
            $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF");

            $details = 'Caj Perkhidmatan tidak dikecualikan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // NOTE - send telegram notification for team management - finance
                $manageMsg = "Permohonan ini tidak mendapat pengecualian caj perkhidmatan. \n\n<strong>?? No Rujukan : " . $refNo . "\n?? Tajuk : " . $project_title . "</strong>";
                $telegramResponse = $telegram->sendMessage('group', 'management', $manageMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'registration', $manageMsg, 'html');
                $telegramResponse = $telegram->sendMessage('department', 'finance', $manageMsg, 'html');
            };

            $chronoId = $chronology->create($username, $systemId, $status, 'notes');

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

        }

        $message = 'Caj Perkhidmatan Disahkan ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "message" => $message,
            "status" => 200
        ]);

        $conn = null;
    }

} else if ($status == "155") {
    //tmp upload
    // Get the system ID from the POST request
    $systemId = $POST['systemId'];
    $timestamp = date('Y-m-d H:i:s', time());
    // $value = SurveyApi::getUploadData($systemId);
    $surveyProvider = SurveyApi::checkSurveyProvider($systemId);
    $currentLog = SurveyApi::getLogTmp($systemId);
    $logTmp = $currentLog + 1;

    $conn = $db->createConnection();

    //To get reference number
    $refNo = Utilities::getRefNo($systemId);

    // Check if the row exists
    $query_select = $conn->prepare("SELECT * FROM flw_plan_tmp WHERE system_id = :systemId");
    $query_select->bindParam(':systemId', $systemId);
    $query_select->execute();
    $row_count = $query_select->rowCount();

    if ($row_count > 0) {
        // Row exists, perform update
        $query1 = $conn->prepare("UPDATE flw_plan_tmp SET submitted_tmp = :submitted, log_pelan = :logTmp WHERE system_id = :systemId");
        $query1->bindParam(':systemId', $systemId);
        $query1->bindParam(':logTmp', $logTmp);
        $query1->bindParam(':submitted', $timestamp);
        // $query1->execute();
    } else {
        // Row doesn't exist, perform insert
        $query1 = $conn->prepare("INSERT INTO flw_plan_tmp (system_id, submitted_tmp, log_pelan) VALUES (:systemId, :submitted, :logTmp)");
        $query1->bindParam(':systemId', $systemId);
        $query1->bindParam(':logTmp', $logTmp);
        $query1->bindParam(':submitted', $timestamp);
        // $query1->execute();
    }

    if ($query1->execute()) {

        if ($surveyProvider == 1) {
            $flwStatus = 56; //Inhouse : Serahan
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah memuatnaik PPT bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 155
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Muatnaik PPT Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Muatnaik PPT Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                // NOTE - send telegram notification for team survey
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
            $stmt->execute();

            // Finally, return a JSON
            echo json_encode([
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
            ]);
        } else if ($surveyProvider == 2) {
            $flwStatus = 59; //Outsource : Semak PPT
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah memuatnaik PPT bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 155
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Muatnaik PPT Berjaya bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Muatnaik PPT Berjaya. \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
                // $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
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
        }

    } else {
        http_response_code(500);
        $result = array(
            "success" => false,
            "message" => "failed",
            "status" => 500,
        );
    }

    // Close the database connection
    $conn = null;

} else if ($status == "151") {
    $systemId = $POST['systemId'];
    $verifyCA = isset ($POST['verify_cancel_application']) ? $POST['verify_cancel_application'] : 0;

    if ($verifyCA == 1) {

        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        $flwStatus = 152;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);

        $stmt1 = $conn->prepare('SELECT flw_appl_entries.project_title FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
        $stmt1->bindParam(':systemId', $systemId);
        $stmt1->execute();
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);
        $project_title = $result['project_title'];

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan Pembatalan Permohonan bagi permohonan ' . $refNo;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 151
        //nextstep for all - cancel application
        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);
        // $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus);

        $details = 'Pembatalan Permohonan disahkan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // NOTE - send telegram notification for team management - finance
            $manageMsg = "Pembatalan Permohonan Disahkan. \n\n<strong>?? No Rujukan : " . $refNo . "\n?? Tajuk : " . $project_title . "</strong>";
            $telegramResponse = $telegram->sendMessage('group', 'management', $manageMsg, 'html');
        };

        $chronoId = $chronology->create($username, $systemId, $status, 'notes');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();


        $message = 'Pembatalan Permohonan Disahkan ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "message" => $message,
            "status" => 200
        ]);

        $conn = null;

    } else {
        //get ref no
        $refNo = Utilities::getRefNo($systemId);

        $flwStatus = 152;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);

        $stmt1 = $conn->prepare('SELECT flw_appl_entries.project_title FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
        $stmt1->bindParam(':systemId', $systemId);
        $stmt1->execute();
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);
        $project_title = $result['project_title'];

        //Record Activity
        $text = 'Pengguna ' . $username . ' tidak mengesahkan Pembatalan Permohonan bagi permohonan ' . $refNo;
        $pages = 'task_operation';
        $changelog->userActivity($text, $pages);

        $stmtC = $conn->prepare('SELECT * FROM ctrl_statuses WHERE system_id = :systemId');
        $stmtC->bindParam(':systemId', $systemId);
        $stmtC->execute();
        $result = $stmtC->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $NextStatus = $flow->goToNextFlow($systemId, $row['department'], "BF", Steps: $row['cancellation_status'], allRole: true);
        }

        $details = 'Pembatalan Permohonan tidak disahkan bagi permohonan ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // NOTE - send telegram notification for team management - finance
            $manageMsg = "Pembatalan Permohonan Tidak Disahkan. \n\n<strong>?? No Rujukan : " . $refNo . "\n?? Tajuk : " . $project_title . "</strong>";
            // $telegramResponse = $telegram->sendMessage('department', 'registration', $manageMsg, 'html');
            // $telegramResponse = $telegram->sendMessage('department', 'finance', $manageMsg, 'html');
        };

        $chronoId = $chronology->create($username, $systemId, $status, 'notes');

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();


        $message = 'Pembatalan Permohonan Tidak Disahkan ??';

        // Finally, return a JSON
        http_response_code(200);
        echo json_encode([
            "message" => $message,
            "status" => 200
        ]);

        $conn = null;
    }

} else if ($status == "160") {
    $systemId = $POST['systemId'];
    $refNo = $POST['no_ref'];

    $notes = "No Rujukan Anda : " . $refNo . " \nNota: " . $POST['notes'] . " ";

    $applData = [
        "secret" => $system->App->secret,
        "submissions" => [
            "reference_no" => $refNo,
            "username" => $username,
            "notes" => $notes,
            "stage" => 1
        ]
    ];

    $explodedRef = explode("/", $refNo);
    $lengthCode = $explodedRef[2];
    $running = $explodedRef[5];

    $stmt = $conn->prepare("UPDATE flw_appl_entries SET reference_no = :refNo, length_code = :lengthCode WHERE system_id = :systemId ");
    $stmt->bindParam(':refNo', $refNo);
    $stmt->bindParam(':lengthCode', $lengthCode);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();

    $stmt2 = $conn->prepare("UPDATE ctrl_reference_no SET running = :running WHERE system_id = :systemId ");
    $stmt2->bindParam(':running', $running);
    $stmt2->bindParam(':systemId', $systemId);
    $stmt2->execute();

    if ($stmt->execute()) {
        $jsonData = json_encode($applData);
        $request = $Curl->request($ec_url.'/gateway/internal/reference/'.$systemId, $jsonData);
        $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

        $curlResult = json_decode($request['response']);
    }

    // NOTE - flow ACC upload WO PCL
    $flwStatus = 6;
    // NOTE - current status = 160
    $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus, allRole: true);

    // NOTE - Update Task Assignment
    $taskAssignment->complete($username, $systemId, $status);
    $taskAssignment->create($systemId, $flwStatus);

    $stmt1 = $conn->prepare('SELECT project_title FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
    $stmt1->bindParam(':systemId', $systemId);
    $stmt1->execute();
    $result = $stmt1->fetch(PDO::FETCH_ASSOC);
    $project_title = $result['project_title'];

    //Record Activity
    $text = 'Pengguna ' . $username . ' telah mengesahkan No Fail Permohonan bagi no permohonan ' . $systemId;
    $pages = 'task_operation';
    $changelog->userActivity($text, $pages);

    $details = 'No Fail Permohonan telah disahkan bagi no permohonan ' . $systemId . '. Nota: ' . (!empty($POST['notes']) ? $POST['notes'] : '-tiada-');
    // NOTE - Insert Project Changelog
    if ($changelog->projectActivity($systemId, $status, $details)) {
        // NOTE - send telegram notification for team management - finance
        $telegramMsg = "No Fail Permohonan telah disahkan bagi no permohonan <strong> " . $systemId . "</strong>. Sila muatnaik Arahan Kerja PIL . \n\n<strong>?? No Rujukan : " . $refNo . "</strong>";
        // NOTE - send telegram notification for team
        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
        $telegramResponse = $telegram->sendMessage('department', 'finance', $telegramMsg, 'html');
    };

    $chronoId = $chronology->create($username, $systemId, $status, 'notes');

    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':details', $details);
    $stmt->bindParam(':created', $timestamp);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':chronology_id', $chronoId);
    $stmt->execute();

    $message = 'No Fail Permohonan telah Disahkan ??';

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "message" => $message,
        "status" => 200
    ]);

    $conn = null;
} else {
    http_response_code(500);
    echo "Tidak Menjumpai Data Yang Di Perlukan";
}