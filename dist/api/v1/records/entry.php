<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
include_once "api/header.php";
require_once "api/functions.php";
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
$ftpPath = $system->FTPConnection->path;
$store = $ftp->createConnection();
$timestamp = date('Y-m-d H:i:s', time());
$systemId = $_GET['id'];
$ec_url = $system->App->ec_url;

$data = file_get_contents('php://input');

if(isset($systemId)) {
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $POST = json_decode($data);

        if($_GET['option'] === 'amendment') {
            // Connect to the database using PDO
            $db = new DBConnectionFactory();
            $conn = $db->createConnection();

            // $systemId = isset($POST->system_id) ? $POST->system_id : NULL;
            $provider = isset($POST->provider) ? $POST->provider : NULL;
            $title = isset($POST->title) ? $POST->title : NULL;
            $info = isset($POST->info) ? $POST->info : NULL;
            $appl_letter = isset($POST->appl_letter) ? $POST->appl_letter : NULL;
            $notes1 = $POST->notes_1;

            // Pegawai
            $applicant = isset($POST->applicant) ? $POST->applicant : NULL;
            $officer = isset($POST->officer) ? $POST->officer : NULL;
            $ack_letter = isset($POST->ack_letter) ? $POST->ack_letter : NULL;
            $notes2 = $POST->notes_2;

            // Jalan
            $district = isset($POST->district) ? $POST->district : NULL;
            $road = isset($POST->road) ? $POST->road : NULL;
            $doc_technical = isset($POST->doc_technical) ? $POST->doc_technical : NULL;
            $plan_location = isset($POST->plan_location) ? $POST->plan_location : NULL;
            $notes3 = $POST->notes_3;

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
                $result = $curl->request($ec_url.'/gateway/internal/amendment/'. $systemId, $jsonData);
                $trafficReturn = $traffic->requestAPI($result['traffic'], $result['url'], $result['request_method'], $result['headers'], $result['body'], $result['response'], $result['status']);
                $curlResult = json_decode($result['response']);
                if($curlResult->message === "success") {
                    // NOTE - Update Task Assignment
                    $dept = "operation";
                    $stmt1 = $conn->prepare('SELECT status_id FROM ctrl_statuses WHERE system_id = :systemId AND department = :department LIMIT 1');
                    $stmt1->bindParam(':systemId', $systemId);
                    $stmt1->bindParam(':department', $dept);
                    $stmt1->execute();

                    $status_amendment = $stmt1->fetch(PDO::FETCH_OBJ)->status_id;

                    $taskAssignment->complete($username, $systemId, $status_amendment); 

                    $message = 'Menyemak Maklumat Permohonan bagi No Permohonan '.$systemId;
                    $pages = 'application_approval';
                    $changelog->userActivity($message, $pages);

                    // NOTE - current status = 4
                    $flowStatus = 5;
                    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flowStatus);

                    $notes_1 = '';
                    $notes_2 = '';
                    $notes_3 = '';

                    // if($notes1 !== ''){
                    //     $notes_1 = "\n" . $notes1;
                    // }
                    // if($notes2 !== ''){
                    //     $notes_2 = "\n" . $notes2;
                    // }
                    // if($notes3 !== ''){
                    //     $notes_3 = "\n" . $notes3;
                    // }

                    // $details_pinda = 'Permohonan telah dipinda bagi no penyerahan '.$systemId . ". Nota: " .$notes_1 . $notes_2 . $notes_3;
                    // $details = $details_pinda;


                    if ($notes1 !== '') {
                        $notes_1 = "<li>" . htmlspecialchars($notes1) . "</li>";
                    }
                    if ($notes2 !== '') {
                        $notes_2 = "<li>" . htmlspecialchars($notes2) . "</li>";
                    }
                    if ($notes3 !== '') {
                        $notes_3 = "<li>" . htmlspecialchars($notes3) . "</li>";
                    }
                    
                    $details_pinda = 'Permohonan telah dipinda bagi no penyerahan ' . $systemId . ". Nota: <ul>" . $notes_1 . $notes_2 . $notes_3 . "</ul>";
                    $details = $details_pinda;

                    // NOTE - Insert Project Changelog
                    $changelog->projectActivity($systemId, 4, $details);
                    $chronoId = $chronology->create($username, $systemId, 4, 'notes');

                    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':details', $details);
                    $stmt->bindParam(':created', $createdAt);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':chronology_id', $chronoId);
                    if($stmt->execute()) {
                        // Finally, return a JSON
                        $message = 'Permohonan ini sudah berjaya disemak! Pihak pemohon telah dimaklumkan mengenai butiran pindaan.';
                        http_response_code(200);
                        $result = array(
                            "success" => true,
                            "message" => $message
                        );
                        echo json_encode($result);
                    } else {
                        // Finally, return a JSON
                        $message = 'Terdapat ralat dalam proses semakan dan kemaskini Permohonan ini. Sila Hubungi Pegawai IT kami.';
                        http_response_code(500);
                        $result = array(
                            "success" => false,
                            "message" => $message,
                        );
                        echo json_encode($result);
                    }
                }
            } catch (PDOException $e) {
                // If there is an error, return a 500 response
                http_response_code(500);
                $result = array(
                    "success" => false,
                    "message" => 'Database error: '.$e->getMessage()
                );
            }

            // Close the database connection
            $conn = null;

        } else {
            // Finally, return a JSON
            http_response_code(403);
            $result = array(
                "success" => false,
                "message" => "Not Authorized"
            );
        }

    } else if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        // Approve submission
        $POST = json_decode($data);
        if($_GET['option'] === 'approval') {
            $month = date('m', time());
            $shortYear = date('y', time());
            $fullYear = date('Y', time());

            // $stmt = $conn->prepare('SELECT length_code, submission_code, created_at, utility_provider FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
            // $stmt->bindParam(':systemId', $systemId);
            // $stmt->execute();
            $stmt = $conn->prepare('SELECT project_title, length_code, submission_code, created_at, utility_provider, payment_method, state FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();

            $record = $stmt->fetch(PDO::FETCH_OBJ);
            // $lengthCode = $record->length_code;
            $providerId = $record->utility_provider;
            // $subId = $record->submission_code;
            $createdAt = $record->created_at;
            $state = $record->state;
            $payment_method = $record->payment_method;
            $project_title = $record->project_title;

            $utilityType = $POST->utility_type;
            
            if ($providerId == 5 || $providerId == 7) {
                $applLabel = $POST->appl_label;
            }

            $notes = "Permohonan Lengkap.\nNota: ".$POST->notes." ";

            $applData = [
                "secret" => $system->App->secret,
                "submissions" => [
                    // "reference_no" => $refNo,
                    "approval_submission" => true,
                    "username" => $username,
                    "notes" => $notes,
                    "state" => $state,
                    "payment_method" => $payment_method,
                    "stage" => 1
                ]
            ];

            $jsonData = json_encode($applData);
            $decodedData = json_decode($jsonData);
            $request = $curl->request($ec_url.'/gateway/internal/reference/'.$systemId, $jsonData);
            $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

            $curlResult = json_decode($request['response']);

            if($curlResult->message === "success") {

                // If only TNB or TM
                if ($providerId == 5 || $providerId == 7) {
                    $stmt = $conn->prepare("UPDATE flw_appl_entries SET tags = :applLabel WHERE system_id = :systemId");
                    $stmt->bindParam(':applLabel', $applLabel);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();
                }
                
                $stmt = $conn->prepare("UPDATE flw_appl_entries SET utility_type = :utilityType WHERE system_id = :systemId RETURNING project_title");
                // $stmt->bindParam(':refNo', $refNo);
                $stmt->bindParam(':utilityType', $utilityType);
                $stmt->bindParam(':systemId', $systemId);
                if($stmt->execute()) {
                    $project_title = $stmt->fetchColumn();

                    if ($payment_method == 2) {
                        
                        # Method Invoice, next flow go to role Account
                        // NOTE - Update Task Assignment
                        $dept = "operation";
                        $stmt1 = $conn->prepare('SELECT status_id FROM ctrl_statuses WHERE system_id = :systemId AND department = :department LIMIT 1');
                        $stmt1->bindParam(':systemId', $systemId);
                        $stmt1->bindParam(':department', $dept);
                        $stmt1->execute();

                        $status_amendment = $stmt1->fetch(PDO::FETCH_OBJ)->status_id;

                        $taskAssignment->complete($username, $systemId, $status_amendment); 
                        $taskAssignment->create($systemId, 1);

                        $message = 'Mengesahkan Permohonan bagi No Permohonan #'.$systemId;
                        $pages = 'application_approval';
                        $changelog->userActivity($message, $pages);

                        // NOTE - current status = 4
                        $flwStatus = 1;
                        $steps = 10;
                        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $steps);

                        //Create new ctrl for mapping
                        $addNewRecord = $flow->addRecordFlow($systemId, "BF", 39);

                        // NOTE - Insert Project Changelog
                        $details = 'Permohonan diluluskan bagi no permohonan #'.$systemId. ". Nota: ".$POST->notes;
                        if($changelog->projectActivity($systemId, 4, $details)) {
                            // NOTE - send telegram notification for team account
                            $message = $telegram->getMessageByFlow($flwStatus, systemId: $systemId, item: $project_title);
                            $response = $telegram->sendMessage('group', 'management', $message, 'html');
                            $response = $telegram->sendMessage('flow', 1, $message, 'html');
                        }
                        $chronoId = $chronology->create($username, $systemId, 4, 'notes');

                    } else if ($payment_method == 1 || $payment_method == 4) {
                        
                        # Method Online Banking & QR Code, next flow go to Extercord
                        // $request = $curl->request($ec_url.'/gateway/internal/charges/'.$systemId, $jsonData);
                        // $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

                        // NOTE - Update Task Assignment
                        $taskAssignment->complete($username, $systemId, 4);
                        // $taskAssignment->create($systemId, 1);

                        $message = 'Mengesahkan Permohonan bagi No Permohonan #'.$systemId;
                        $pages = 'application_approval';
                        $changelog->userActivity($message, $pages);

                        // NOTE - current status = 4
                        $flwStatus = 1;
                        $steps = 10;
                        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $steps);

                        //Create new ctrl for mapping
                        $addNewRecord = $flow->addRecordFlow($systemId, "BF", 39);

                        // NOTE - Insert Project Changelog
                        $details = 'Permohonan diluluskan bagi no permohonan #'.$systemId . '. Nota: ' . $POST->notes;
                        if($changelog->projectActivity($systemId, 4, $details)) {
                            // NOTE - send telegram notification for team account
                            $message = "Pengesahan Permohonan telah dilakukan. Sila tunggu Pembayaran Caj Pendaftaran yang akan dibuat oleh pemohon. \n\n<strong>🔗 No Permohonan : ".$systemId."\n📝 Tajuk : " . $project_title . "</strong>";
                            // $message = $telegram->getMessageByFlow($flwStatus, systemId: $systemId, item: $project_title);
                            // $response = $telegram->sendMessage('flow', 1, $message, 'html');
                        }
                        $chronoId = $chronology->create($username, $systemId, 4, 'notes');

                    } else {
                        
                        # Method Online Banking & QR Code, next flow go to Extercord
                        $request = $curl->request($ec_url.'/gateway/internal/reference/'.$systemId, $jsonData);
                        $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

                        // NOTE - Update Task Assignment
                        $taskAssignment->complete($username, $systemId, 4);
                        // $taskAssignment->create($systemId, 1);

                        $message = 'Mengesahkan Permohonan bagi No Permohonan #'.$systemId;
                        $pages = 'application_approval';
                        $changelog->userActivity($message, $pages);

                        // NOTE - current status = 4
                        $flwStatus = 1;
                        $steps = 10;
                        $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $steps);

                        //Create new ctrl for mapping
                        $addNewRecord = $flow->addRecordFlow($systemId, "BF", 39);

                        // NOTE - Insert Project Changelog
                        $details = 'Permohonan diluluskan bagi no permohonan #'. $systemId . '. Nota: ' . $POST->notes;
                        if($changelog->projectActivity($systemId, 4, $details)) {
                            // NOTE - send telegram notification for team account
                            $message = "Pengesahan Permohonan telah dilakukan. Sila tunggu Pembayaran Caj Pendaftaran yang akan dibuat oleh pemohon. \n\n<strong>🔗 No Permohonan : ".$systemId."\n📝 Tajuk : " . $project_title . "</strong>";
                            // $message = $telegram->getMessageByFlow($flwStatus, systemId: $systemId, item: $project_title);
                            // $response = $telegram->sendMessage('flow', 1, $message, 'html');
                        }
                        $chronoId = $chronology->create($username, $systemId, 4, 'notes');

                    }

                    $stmt = $conn->prepare("UPDATE flw_appl_entries SET utility_type = :utilityType WHERE system_id = :systemId RETURNING project_title");
                    $stmt->bindParam(':utilityType', $utilityType);
                    $stmt->bindParam(':systemId', $systemId);

                    if($stmt->execute()) {
                        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':systemId', $systemId);
                        $stmt->bindParam(':details', $details);
                        $stmt->bindParam(':created', $createdAt);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':chronology_id', $chronoId);
                        $stmt->execute();

                        $year = date("Y", strtotime($createdAt));

                        // FTP directory structure
                        $ftpBaseDir = '/Projects';
                        $ftpYearDir = $ftpBaseDir.'/'.$year;
                        $ftpSystemDir = $ftpYearDir.'/'.$systemId;
                        $ftpReportsDir = $ftpSystemDir.'/Reports';
                        $ftpGeospatialDir = $ftpSystemDir.'/Geospatial';
                        $ftpDocumentsDir = $ftpSystemDir.'/Documents';
                        $ftpSiteVisitDir = $ftpReportsDir.'/SiteVisit';
                        $ftpSurveyDir = $ftpReportsDir.'/Survey';
                        $ftpMapsDir = $ftpGeospatialDir.'/Maps';
                        $ftpGISReadyDir = $ftpGeospatialDir.'/GISReady';
                        $ftpPlanDir = $ftpDocumentsDir.'/Plan';
                        $ftpSubmissionDir = $ftpDocumentsDir.'/Submission';

                        $success = 0;

                            // if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpYearDir")){
                            //     $return = true;
                            // } else {
                            //     if(ftp_mkdir($store, $ftpYearDir)){
                            //         $return = true;
                            //     } else {
                            //         $return = false;
                            //     }
                            // }

                            // if($return === true) {
                            //     if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpSystemDir")){
                            //         $success++;
                            //     } else {
                            //         if(ftp_mkdir($store, $ftpSystemDir)){
                            //             $success++;
                            //         }
                            //     }

                            //     if($success === 1) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpReportsDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpReportsDir)){
                            //                 $success++;
                            //             }
                            //         }

                            //     }

                            //     if($success === 2) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpSiteVisitDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpSiteVisitDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 3) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpSurveyDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpSurveyDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 4) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpGeospatialDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpGeospatialDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 5) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpMapsDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpMapsDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 6) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpGISReadyDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpGISReadyDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 7) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpDocumentsDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpDocumentsDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 8) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpPlanDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpPlanDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }

                            //     if($success === 9) {
                            //         if(is_dir("ftp://kiter:JW2P5vt3@dev.storage.kutt.my/$ftpSubmissionDir")){
                            //             $success++;
                            //         } else {
                            //             if(ftp_mkdir($store, $ftpSubmissionDir)){
                            //                 $success++;
                            //             }
                            //         }
                            //     }
                            // }

                            // total success = 10

                        if(!ftp_chdir($store, $ftpBaseDir.'/'.$year)) {
                            if(!ftp_mkdir($store, $ftpBaseDir.'/'.$year)) {
                                $message = 'Tidak Berjaya Untuk Membuka Fail Kerja Permohonan ini. Sila Hubungi Pegawai IT kami.';
                                $return = true;
                            } else {
                                $return = false;
                            }
                        } else {
                            $return = true;
                        }

                        if($return === true) {
                            if(ftp_mkdir($store, $ftpSystemDir)) {
                                // Set permissions for the newly created directory
                                ftp_chmod($store, 0755, $ftpSystemDir);
                                if(ftp_mkdir($store, $ftpReportsDir)) {
                                    // Set permissions for the newly created directory
                                    ftp_chmod($store, 0755, $ftpReportsDir);
                                    if(ftp_mkdir($store, $ftpSiteVisitDir)) {
                                        // Set permissions for the newly created directory
                                        ftp_chmod($store, 0755, $ftpSiteVisitDir);
                                        $success++;
                                    }
                                    if(ftp_mkdir($store, $ftpSurveyDir)) {
                                        // Set permissions for the newly created directory
                                        ftp_chmod($store, 0755, $ftpSurveyDir);
                                        $success++;
                                    }
                                }
                                if(ftp_mkdir($store, $ftpGeospatialDir)) {
                                    // Set permissions for the newly created directory
                                    ftp_chmod($store, 0755, $ftpGeospatialDir);
                                    if(ftp_mkdir($store, $ftpMapsDir)) {
                                        // Set permissions for the newly created directory
                                        ftp_chmod($store, 0755, $ftpMapsDir);
                                        $success++;
                                    }
                                    if(ftp_mkdir($store, $ftpGISReadyDir)) {
                                        // Set permissions for the newly created directory
                                        ftp_chmod($store, 0755, $ftpGISReadyDir);
                                        $success++;
                                    }
                                }
                                if(ftp_mkdir($store, $ftpDocumentsDir)) {
                                    // Set permissions for the newly created directory
                                    ftp_chmod($store, 0755, $ftpDocumentsDir);
                                    if(ftp_mkdir($store, $ftpPlanDir)) {
                                        // Set permissions for the newly created directory
                                        ftp_chmod($store, 0755, $ftpPlanDir);
                                        $success++;
                                    }
                                    if(ftp_mkdir($store, $ftpSubmissionDir)) {
                                        // Set permissions for the newly created directory
                                        ftp_chmod($store, 0755, $ftpSubmissionDir);
                                        $success++;
                                    }
                                }
                            }
                        } else {
                            $message = 'Tidak Berjaya Untuk Membuka Fail Kerja Permohonan ini. Sila Hubungi Pegawai IT kami.';
                        }
                        // Close the FTP connection
                        ftp_close($store);

                        if($success === 6) {
                            // $reference = $refNo;
                            $message = 'Permohonan ini sudah berjaya disemak!';
                            // Finally, return a JSON
                            http_response_code(200);
                            $result = array(
                                "success" => true,
                                // "reference" => $reference,
                                "message" => $message,
                                "sysId" => $systemId
                            );
                        } else {
                            $message = 'Tidak Berjaya Untuk Membuka Fail Kerja Permohonan ini. Sila Hubungi Pegawai IT kami.';
                            // Finally, return a JSON
                            http_response_code(500);
                            $result = array(
                                "success" => false,
                                "message" => $message,
                                "sysId" => $systemId
                            );
                        }
                    }

                } else {
                    http_response_code(500);
                    $result = array(
                        "success" => false,
                        "message" => 'Permohonan Tidak Dapat Diproses. Jenis Utiliti Tidak Ditetapkan.'
                    );

                }

            } else {
                http_response_code(500);
                $result = array(
                    "success" => false,
                    "message" => 'Permohonan Tidak Dapat Diproses. API ke Corridor Tidak Berjaya.'
                );

            }
            
            // Close the database connection
            $conn = null;
            echo json_encode($result);

        } else{

            // Finally, return a JSON
            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => 'Permohonan Tidak Dapat Diproses. Option is not approval.'
            );
        }


    } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {


    }
} else {
    http_response_code(404);
    $result = array(
        "success" => false,
        "message" => "No System ID provided."
    );
}