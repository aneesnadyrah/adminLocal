<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";

header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Check if the URL contains a parameter named
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $json = file_get_contents("php://input");
    $data = json_decode($json);
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();
    $taskAssignment = new TaskAssignments();
    $telegram = new Telegram();
    $system = new System;
    $curl = new Curl();
    $traffic = new Traffic();
    $chronology = new Chronology();
    $tenant = $system->App->tenant;
    $ec_url = $system->App->ec_url;
    $timestamp = date('Y-m-d H:i:s', time());

    $response = null;
    if (Utilities::checkDomainToken($data->token, $_SERVER['HTTP_ORIGIN'])) {
        // Check if the expected keys are present in the data
        if (!isset($data->sub_id) || !isset($data->bill_code) || !isset($data->is_paid) || !isset($data->paid_at) || !isset($data->url)) {
            // NOTE -
            // Set the 400 Bad Request status code
            http_response_code(400);

            // Create an error message
            $response = array(
                'error' => 'Bad Request',
                'message' => 'Mandatory key-value pairs are missing. Please include the required parameters in your request.'
            );
        } else {
            // Retrieve the payload data value
            $subId = $data->sub_id;
            $billCode = $data->bill_code;
            $paidStatus = $data->paid; // this is in string "true"|"false"
            $isPaid = $data->is_paid;
            $paidAt = $data->paid_at;
            $paidAmount = $data->paid_amount; // this return "5300"
            $transactionId = $data->transaction_id;
            $transactionStatus = $data->transaction_status; // this return "complete"
            $url = $data->url;
            $filename = $data->filename;
            $mimeType = $data->mime_type;
            $size = $data->size;
            $username = $data->username;

            // Create flowstatuses instance
            $flow = new FlowStatuses($username);

            // Convert the string to a DateTime object
            $dateTime = new DateTime($paidAt);

            $attachDate = $dateTime->format('Y-m-d');
            $created = date('Y-m-d H:i:s', time());

            // Prepare a statement to check whether the entry has reference no or not
            $stmt = $conn->prepare('SELECT payment_method FROM flw_appl_entries WHERE system_id = :subId LIMIT 1');
            $stmt->bindParam(':subId', $subId);
            if ($stmt->execute()) {
                $paymentMethod = $stmt->fetchColumn();
            }

            // Prepare the INSERT statement into flw_charges
            $stmtC = $conn->prepare("INSERT INTO flw_charges (bill_code, url, is_paid, paid_at, method, transaction_id, amount) VALUES (:billCode, :url, :isPaid, :paidAt, :method, :transactionId, :paidAmount) RETURNING id");
            $stmtC->bindParam(':billCode', $billCode);
            $stmtC->bindParam(':url', $url);
            $stmtC->bindParam(':isPaid', $isPaid);
            $stmtC->bindParam(':paidAt', $paidAt);
            $stmtC->bindParam(':method', $paymentMethod);
            $stmtC->bindParam(':transactionId', $transactionId);
            $stmtC->bindParam(':paidAmount', $paidAmount);

            if ($stmtC->execute()) {
                $chargesId = $stmtC->fetchColumn();

                $stmtUC = $conn->prepare("UPDATE flw_appl_entries SET charges_id = :chargesId WHERE system_id = :subId ");
                $stmtUC->bindParam(':chargesId', $chargesId);
                $stmtUC->bindParam(':subId', $subId);
                $stmtUC->execute();
            };

            // NOTE - Take out from paymentMethod 1
            $month = date('m', time());
            $shortYear = date('y', time());
            $fullYear = date('Y', time());

            $stmt = $conn->prepare('SELECT length_code, submission_code, created_at, utility_provider, utility_type, tags FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
            $stmt->bindParam(':systemId', $subId);
            $stmt->execute();

            $record = $stmt->fetch(PDO::FETCH_OBJ);
            $lengthCode = $record->length_code;
            $providerId = $record->utility_provider;
            // $subId = $record->submission_code;
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

            // NOTE - Assign task to account
            $flwStatus = 2;
            if ($paymentMethod == 1) {
                
                // Jika Billplz berjaya
                if ($isPaid == true) {
                    
                    $query = 'INSERT INTO ctrl_reference_no (system_id, year, running) VALUES (:systemId, :year, :running)';
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemId', $subId);
                    $stmt->bindParam(':year', $fullYear);
                    $stmt->bindParam(':running', $runNo);
                    $stmt->execute();

                    $stmt = $conn->prepare('SELECT districts, SUM(road_length), (SELECT state FROM flw_appl_entries WHERE system_id = :systemId) AS state FROM flw_appl_roads WHERE system_id = :systemId GROUP BY districts ORDER BY SUM DESC LIMIT 1');
                    $stmt->bindParam(':systemId', $subId);
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
                    
                    $notes = "Caj Pendaftaran Telah Berjaya Dibayar. No Rujukan Anda : " . $refNo . " \nNota: " . $POST['notes'] . " ";

                    $applData = [
                        "secret" => $system->App->secret,
                        "submissions" => [
                            "reference_no" => $refNo,
                            "username" => $username,
                            "notes" => $notes,
                            "stage" => 1
                        ]
                    ];

                    $stmt = $conn->prepare("UPDATE flw_appl_entries SET reference_no = :refNo WHERE system_id = :systemId ");
                    $stmt->bindParam(':refNo', $refNo);
                    $stmt->bindParam(':systemId', $subId);

                    if ($stmt->execute()) {
                        $jsonData = json_encode($applData);
                        $request = $curl->request($ec_url.'/gateway/internal/reference/'.$subId, $jsonData);
                        $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

                        $curlResult = json_decode($request['response']);
                    }

                }
                
                $flwStatus = 6;
                $assignment = $taskAssignment->create($subId, $flwStatus);
                $NextStatus = $flow->goToNextFlow($subId, "finance", "BF", Steps: $flwStatus, apiToken: $data->token);

                $message = "Pembayaran Caj Pendaftaran telah dibuat oleh Pemohon. Sila teruskan dengan Penyediaan Arahan Kerja PIL. \n\n<strong>🔗 No Permohonan : #" . $data->sub_id . " \n🔗 No Rujukan : " . $refNo . " \n📆 Tarikh Bayaran : " . Utilities::convertDateToMalay($data->paid_at) . "</strong>";
                // $message = $telegram->getMessageByFlow($flwStatus, systemId: $subId, date: Utilities::convertDateToMalay($data->paid_at));
                $telegram->sendMessage('group', 'management', $message, 'html');
                $telegram->sendMessage('department', 'finance', $message, 'html');

                $chronoId = $chronology->create($username, $subId, 0, 'file');

                // 23 == RCP
                $attachType = 23;
                // Prepare the INSERT resit qr code into flw_appl_attachments
                $stmtReceipt = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date,created_date, attachment_type, user_added, mime_type, size, chronology_id) VALUES (:sysId, :details, :url, :attachDate, :createdAt, :attachType, :user, :mimeType, :size, :chronoId) RETURNING id");

                $stmtReceipt->bindParam(':sysId', $subId);
                $stmtReceipt->bindParam(':details', $filename);
                $stmtReceipt->bindParam(':url', $url);
                $stmtReceipt->bindParam(':attachDate', $attachDate);
                $stmtReceipt->bindParam(':createdAt', $paidAt);
                $stmtReceipt->bindParam(':attachType', $attachType);
                $stmtReceipt->bindParam(':user', $username);
                $stmtReceipt->bindParam(':mimeType', $mimeType);
                $stmtReceipt->bindParam(':size', $size);
                $stmtReceipt->bindParam(':chronoId', $chronoId);
                $stmtReceipt->execute();

            } else if ($paymentMethod == 4) {

                // NOTE - nextstep for finance - semakan caj bayaran
                $flwStatus = 2;
                $assignment = $taskAssignment->create($subId, $flwStatus);

                $NextStatus = $flow->goToNextFlow($subId, "finance", "BF", Steps: $flwStatus, allRole: true);

                $details = "Caj Pendaftaran Telah Berjaya Dibayar. No Permohonan : #" . $subId ;

                $message = "Caj Pendaftaran Telah Berjaya Dibayar. Sila teruskan dengan Semakan Caj Bayaran. \n\n<strong>🔗 No Permohonan : #" . $subId . "\n📆 Tarikh Bayaran : " . Utilities::convertDateToMalay($data->paid_at) . "</strong>";

                $message2 = "Caj Pendaftaran Telah Berjaya Dibayar. \n\n<strong>🔗 No Permohonan : #" . $subId . "\n📆 Tarikh Bayaran : " . Utilities::convertDateToMalay($data->paid_at) . "</strong>";
                
                $telegram->sendMessage('group', 'management', $message2, 'html');
                $telegram->sendMessage('department', 'finance', $message, 'html');
                $telegram->sendMessage('department', 'registration', $message2, 'html');
                $telegram->sendMessage('role', 4, $message, 'html');

                $chronoId = $chronology->create($username, $subId, 164, 'notes');

                $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $subId);
                $stmt->bindParam(':details', $details);
                $stmt->bindParam(':created', $timestamp);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':chronology_id', $chronoId);
                $stmt->execute();

                // 23 == RCP
                $attachType = 23;
                // Prepare the INSERT resit qr code into flw_appl_attachments
                $stmtReceipt = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date,created_date, attachment_type, user_added, mime_type, size, chronology_id) VALUES (:sysId, :details, :url, :attachDate, :createdAt, :attachType, :user, :mimeType, :size, :chronoId) RETURNING id");

                $stmtReceipt->bindParam(':sysId', $subId);
                $stmtReceipt->bindParam(':details', $filename);
                $stmtReceipt->bindParam(':url', $url);
                $stmtReceipt->bindParam(':attachDate', $attachDate);
                $stmtReceipt->bindParam(':createdAt', $paidAt);
                $stmtReceipt->bindParam(':attachType', $attachType);
                $stmtReceipt->bindParam(':user', $username);
                $stmtReceipt->bindParam(':mimeType', $mimeType);
                $stmtReceipt->bindParam(':size', $size);
                $stmtReceipt->bindParam(':chronoId', $chronoId);
                $stmtReceipt->execute();

            } else {
                $assignment = $taskAssignment->create($subId, $flwStatus);


                $NextStatus = $flow->goToNextFlow($subId, "finance", "BF", Steps: $flwStatus, apiToken: $data->token);


                $message = "Pembayaran Caj Pendaftaran telah dibuat oleh Pemohon. Sila semak resit pembayaran yang dimuat naik oleh pemohon. \n\n<strong>🔗 No Rujukan : " . $data->sub_id . " \n📆 Tarikh Bayaran : " . Utilities::convertDateToMalay($data->paid_at) . "</strong>";
                // $message = $telegram->getMessageByFlow($flwStatus, systemId: $subId, date: Utilities::convertDateToMalay($data->paid_at));
                $telegram->sendMessage('group', 'management', $message, 'html');
                $telegram->sendMessage('department', 'finance', $message, 'html');
            
                $chronoId = $chronology->create($username, $subId, 0, 'file');

                // 23 == RCP
                $attachType = 23;
                // Prepare the INSERT resit qr code into flw_appl_attachments
                $stmtReceipt = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date,created_date, attachment_type, user_added, mime_type, size, chronology_id) VALUES (:sysId, :details, :url, :attachDate, :createdAt, :attachType, :user, :mimeType, :size, :chronoId) RETURNING id");

                $stmtReceipt->bindParam(':sysId', $subId);
                $stmtReceipt->bindParam(':details', $filename);
                $stmtReceipt->bindParam(':url', $url);
                $stmtReceipt->bindParam(':attachDate', $attachDate);
                $stmtReceipt->bindParam(':createdAt', $paidAt);
                $stmtReceipt->bindParam(':attachType', $attachType);
                $stmtReceipt->bindParam(':user', $username);
                $stmtReceipt->bindParam(':mimeType', $mimeType);
                $stmtReceipt->bindParam(':size', $size);
                $stmtReceipt->bindParam(':chronoId', $chronoId);
                $stmtReceipt->execute();
            }

            http_response_code(200);
            $result = array(
                "success" => true,
                "message" => 'Caj Pendaftaran Telah Berjaya Dibayar.',
            );

            echo json_encode($result);
        }
    } else {
        // The token or domain is invalid or the token has expired
        http_response_code(400);
        $result = array(
            "success" => false,
            "message" => "Invalid or expired API token",
        );

        echo json_encode($result);
        $conn = null;
    }

    $conn = null;

}