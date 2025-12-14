<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "api/header.php";
require_once "api/functions.php";
require_once "config/system.php";
require_once "config/DBFactory.php";

$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$telegram = new Telegram();
$system = new System();
$tenant = $system->App->tenant;
$curl = new Curl();
$traffic = new Traffic();
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();
$db = new DBConnectionFactory();
$conn = $db->createConnection();
$ftp = new FTPConnectionFactory();
$store = $ftp->createConnection();
$timestamp = date('Y-m-d H:i:s', time());
$systemId = $_GET['sid'];
$username = $_SESSION['username'];
$ec_url = $system->App->ec_url;

if(isset($systemId)) {
    //get ref no
    $refNo = Utilities::getRefNo($systemId);

    if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = file_get_contents("php://input");
        $POST = json_decode($data, true);

        // Approve submission
        if($_GET['option'] === 'approval') {

            $status = 17;
            $flwStatus = 163;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah semak Pindaan Cadangan Teknikal bagi permohonan ' . $refNo;
            $pages = 'task_operation';
            $changelog->userActivity($text, $pages);

            // NOTE - nextstep for OM
            $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);

            $details = 'Pindaan Cadangan Teknikal bagi permohonan ' . $refNo . ' telah Disemak. Nota: ' . $POST['notes'];
            
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // NOTE - send telegram notification for team management - OM
                $manageMsg = "Pindaan Cadangan Teknikal bagi permohonan ini telah disemak. Sila buat pengesahan bagi semakan Pindaan Cadangan Teknikal ini. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
                $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $manageMsg, 'html');
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

            
            $message = "Pindaan Cadangan Teknikal Telah Berjaya Disemak! 🎉";

            // Finally, return a JSON
            http_response_code(200);
            $result = array(
                "success" => true,
                "message" => $message,
            );
            echo json_encode($result);

        } else {

            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => 'Permohonan Tidak Dapat Diproses. Option is not approval.'
            );
        }

    } else if($_SERVER['REQUEST_METHOD'] == "POST") {
        $data = file_get_contents("php://input");
        $POST = json_decode($data, true);
        // var_dump($POST);

        if($_GET['option'] === 'amend') {
            // NOTE - kena tambah action utk amend kpd exterCord
            $provider = isset($POST['provider']) ? $POST['provider'] : NULL;
            $title = isset($POST['title']) ? $POST['title'] : NULL;
            $info = isset($POST['info']) ? $POST['info'] : NULL;
            $appl_letter = isset($POST['appl_letter']) ? $POST['appl_letter'] : NULL;
            $notes1 = $POST['notes_1'];

            // Pegawai
            $applicant = isset($POST['applicant']) ? $POST['applicant'] : NULL;
            $officer = isset($POST['officer']) ? $POST['officer'] : NULL;
            $ack_letter = isset($POST['ack_letter']) ? $POST['ack_letter'] : NULL;
            $notes2 = isset($POST['notes_2']) ? $POST['notes_2'] : '';
            // Jalan
            $district = isset($POST['district']) ? $POST['district'] : NULL;
            $road = isset($POST['road']) ? $POST['road'] : NULL;
            $doc_technical = isset($POST['doc_technical']) ? $POST['doc_technical'] : NULL;
            $plan_location = isset($POST['plan_location']) ? $POST['plan_location'] : NULL;
            $notes3 = isset($POST['notes_3']) ? $POST['notes_3'] : '';

            try {
                $data = [
                    "secret" => $system->App->secret,
                    "submissions" => [
                        "stage" => 1,
                        "username" => $username,
                        "notes" => "Terdapat Pindaan Permohonan",
                        "amendments" => [
                            [
                                "steps" => 1,
                                "items" => [
                                    "provider" => $provider,
                                    "title" => $title,
                                    "info" => $info,
                                    "notes" => $notes1,
                                    "attachment" => $appl_letter
                                ]
                            ],
                            [
                                "steps" => 2,
                                "items" => [
                                    "applicant" => $applicant,
                                    "officer" => $officer,
                                    "notes" => $notes2,
                                    "attachment" => $ack_letter
                                ]
                            ],
                            [
                                "steps" => 3,
                                "items" => [
                                    "district" => $district,
                                    "roads" => $road,
                                    "notes" => $notes3,
                                    "attachment-1" => $doc_technical,
                                    "attachment-2" => $plan_location
                                ]
                            ]
                        ]
                    ]
                ];

                // Convert the array to JSON
                $jsonData = json_encode($data);

                // Send a POST request to Extercord API endpoint
                // $result = $curl->request($ec_url.'/gateway/internal/amendment/'. $systemId, $jsonData);
                // $result = $curl->request($ec_url.'/gateway/internal/report/sitevisit/'. $systemId, $jsonData);

                // Decode the response JSON
                // $trafficReturn = $traffic->requestAPI($result['traffic'], $result['url'], $result['request_method'], $result['headers'], $result['body'], $result['response'], $result['status']);
                // $curlResult = json_decode($result['response']);

                // if($curlResult->message === "success") {
                    $flwStatus = 17;
                    // NOTE - Update Task Assignment
                    $taskAssignment->complete($username, $systemId, $flwStatus);

                    // Record Activity
                    $text = 'Pengguna ' . $username . ' telah semak Pindaan Cadangan Teknikal dan Cadangan Teknikal perlu dipinda semula' . $refNo;
                    //NOTE : check $page
                    $pages = 'task_operation';
                    $changelog->userActivity($text, $pages);

                    $notes_1 = '';
                    $notes_2 = '';
                    $notes_3 = '';

                    if ($notes1 !== '') {
                        $notes_1 = "<li>" . htmlspecialchars($notes1) . "</li>";
                    }
                    if ($notes2 !== '') {
                        $notes_2 = "<li>" . htmlspecialchars($notes2) . "</li>";
                    }
                    if ($notes3 !== '') {
                        $notes_3 = "<li>" . htmlspecialchars($notes3) . "</li>";
                    }

                    $refNo = Utilities::getRefNo($systemId);

                    $details_pinda = "Pindaan Cadangan Teknikal bagi permohonan " . $refNo . " telah disemak dan perlu dipinda semula. Nota: <ul>" . $notes_1 . $notes_2 . $notes_3 . "</ul>";
                    $details = $details_pinda;
                    
                    // NOTE - Insert Project Changelog
                    $changelog->projectActivity($systemId, $flwStatus, $details);

                    $chronoId = $chronology->create($username, $systemId, $flwStatus, 'notes');

                    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':details', $details);
                    $stmt->bindParam(':created', $timestamp);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':chronology_id', $chronoId);
                    $stmt->execute();

                    $query = "UPDATE flw_appl_reports SET active_amend_tp_notes = :activeNotes WHERE system_id = :systemId";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':activeNotes', $jsonData, PDO::PARAM_STR);
                    $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
                    $stmt->execute();

                    // setup api request payload data
                    $payload = [];
                    $payload['secret'] = $system->App->secret;
                    $payload['submissions'] = [
                        'site_visit'=>[
                            'username'=>$username,
                            'stage'=>1,
                            'is_amendments'=>true,
                            'notes'=>$details,
                            'amendments'=>json_decode($jsonData, true)['submissions']['amendments'],
                            ]
                        ];
                        
                    $payload['submissions']['site_visit']['items'] = ["TP", "LP"];

                    $requestUrl = $ec_url.'/gateway/internal/report/tpamendment/'.$systemId;
                    
                    // make api request to extercord
                    $apiRequest = $curl->request($requestUrl, json_encode($payload));

                    // Decode the response JSON
                    $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

                    $message = "Pindaan Cadangan Teknikal Telah Berjaya Dihantar 🎉";

                // }
                // Finally, return a JSON
                http_response_code(200);
                $result = array(
                    "success" => true,
                    "message" => $message
                );
                echo json_encode($result);


            } catch (PDOException $e) {
                // If there is an error, return a 500 response
                http_response_code(500);
                $result = array(
                    "success" => false,
                    "message" => 'Database error: '.$e->getMessage()
                );
            } 

        } else if($_GET['option'] === 'approvalTPAmend') { 
            // change ctrl_site_report complete status
            $completeStatus = true;
            $query = "UPDATE ctrl_site_report SET completed = :completed WHERE system_id = :systemId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':completed', $completeStatus);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();

            $status = 163;
            $flwStatusPKD = 30;
            $flwStatusAcc = 27;
            $flwStatusGIS = 24;
            
            //Record Activity
            $text = 'Pengguna ' . $username . ' telah mengesahkan semakan Pindaan Cadangan Teknikal bagi permohonan ' . $refNo . ' .';
            $pages = 'task_operation';
            $changelog->userActivity($text, $pages);

            // NOTE - next status for PKD upload KWC
            $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatusPKD);

            // NOTE - next status for Acc upload SH
            $NextStatus = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatusAcc, allRole: true);

            // NOTE - next status for GIS upload PIL
            $NextStatus = $flow->goToNextFlow($systemId, "geospatial", "BF", Steps: $flwStatusGIS, allRole: true);

            $details = 'Semakan Pindaan Cadangan Teknikal bagi permohonan ' . $refNo . ' telah disahkan. Nota: ' . $POST['notes'];
            
            // create general task assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatusAcc);
            $taskAssignment->create($systemId, $flwStatusPKD);
            $taskAssignment->create($systemId, $flwStatusGIS);

            // create general task for status 30
            // $taskAssignment->create($systemId, $flwStatusPKD);

            $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

            $dep = 'operation';
            $auth->bindParam(':systemId', $systemId);
            $auth->bindParam(':department', $dep);

            $auth->execute();

            $authIds = $auth->fetchAll();

            foreach($authIds as $authId){
                $taskAssignment->create($systemId, $flwStatusPKD, $authId->authority);

                // $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authId['authority'], $status);
            }
            
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
        
                // declare telegram notification string
                $telegramMsgFinance = "Pindaan Cadangan Teknikal telah disahkan untuk proses Permohonan Izin Lalu. Sila sediakan Sebut Harga. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('flow', $flwStatusAcc, $telegramMsgFinance, 'html');

                // declare telegram notification string
                $telegramMsgGeospation ="Pindaan Cadangan Teknikal telah disahkan untuk proses Permohonan Izin Lalu. Sila sediakan Pelan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('flow', $flwStatusGIS, $telegramMsgGeospation, 'html');

                // declare telegram notification string
                $telegramMsgOperation = "Pindaan Cadangan Teknikal telah disahkan untuk proses Permohonan Izin Lalu. Sila sediakan Ringkasan Projek dan Kiraan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";

                // NOTE - send telegram notification for team account
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsgOperation, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $flwStatusPKD, $telegramMsgOperation, 'html');
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

            $message = "Semakan Pindaan Cadangan Teknikal Telah Berjaya Disahkan! 🎉";

            // Finally, return a JSON
            http_response_code(200);
            $result = array(
                "success" => true,
                "message" => $message,
            );
            echo json_encode($result);

        } else {

            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => 'Permohonan Tidak Dapat Diproses. Option is not approval.'
            );
        }
    }

} else {
    http_response_code(404);
    $result = array(
        "success" => false,
        "message" => "No System ID provided."
    );
}