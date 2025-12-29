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
        $POST = json_decode($data);
        if($_GET['option'] === 'approval') {

            $curlResult = notifyExtecord($POST,$system,$username,$curl,$traffic); 
            $currentStatus = getCurrentStatus($conn,$systemId, "operation");

            if($curlResult->message === "success") {
                if ($POST->utility_provider == 5 || $POST->utility_provider == 7){
                    $applLabel = $POST->appl_label;
                    $utilityType = $POST->utility_type;
                    $payment_method = $POST->fee_label;

                    $sql = "UPDATE flw_appl_entries SET tags = :applLabel, utility_type = :utilityType, payment_method= :feeLabel WHERE system_id = :systemId RETURNING project_title, created_at";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':applLabel', $applLabel);
                    $stmt->bindParam(':utilityType', $utilityType);
                    $stmt->bindParam(':feeLabel', $payment_method); 
                    $stmt->bindParam(':systemId', $systemId);
                }else{
                    $sql = "UPDATE flw_appl_entries SET utility_type = :utilityType, payment_method= :feeLabel WHERE system_id = :systemId RETURNING project_title, created_at";        
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':utilityType', $utilityType);
                    $stmt->bindParam(':feeLabel', $payment_method);
                    $stmt->bindParam(':systemId', $systemId);
                }

                if(!createFTPDirectories($systemId,$entry->created_at,$system)){
                    http_response_code(500);
                    $result = array(
                        "success" => false,
                        "message" => "Folder tidak berjaya dicipta pada server FTP. Sila hubungi pegawai IT kami."
                    );
                }

                if($stmt->execute()) {
                    $entry = $stmt->fetch(PDO::FETCH_OBJ);
                    $chronoId = $chronology->create($username, $systemId, $currentStatus, 'notes');
                    $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':details', $details);
                    $stmt->bindParam(':created', $createdAt);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':chronology_id', $chronoId);
                    $stmt->execute();

                    $message = 'Mengesahkan Permohonan bagi No Permohonan #'.$systemId;
                    $pages = 'application_approval';
                    $changelog->userActivity($message, $pages);

                    if($payment_method == 2) {
                        $flwStatus = 1;
                        $taskAssignment->complete($username, $systemId, $currentStatus); 
                        $taskAssignment->create($systemId, $flwStatus);
                    }else if($payment_method == 4){
                        $taskAssignment->complete($username, $systemId, $currentStatus); 
                    }
                    $steps = 10;
                    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $steps);
                    if(!createFTPDirectories($systemId,$entry->created_at,$system)){
                        http_response_code(500);
                        $result = array(
                            "success" => false,
                            "message" => "Folder tidak berjaya dicipta pada server FTP. Sila hubungi pegawai IT kami."
                        );
                    }

                }else{ //tidak berjaya update data pada flw_appl_entries.
                    http_response_code(500);
                    $result = array(
                        "success" => false,
                        "message" => "Maklumat Tidak Dapat Dikemaskini. Sila Hubungi Pegawai IT kami."
                    );
                }
                $conn = null;
                echo json_encode($result);

            } else { //tidak berjaya update data pada extercord.
                http_response_code(500);
                $result = array(
                    "success" => false,
                    "message" => "Permohonan Tidak Dapat Diproses. Sila Hubungi Pegawai IT kami."
                );
            }

        }else{
            // Finally, return a JSON
            http_response_code(403);
            $result = array(
                "success" => false,
                "message" => "Not Authorized"
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

function notifyExtecord($POST,$system,$username,$curl,$traffic) {
    $notes = "Permohonan Lengkap.\nNota: ".$POST->notes." ";
    $applData = [
        "secret" => $system->App->secret,
        "submissions" => [
            // "reference_no" => $refNo,
            "approval_submission" => true,
            "username" => $username,
            "notes" => $notes,
            "state" => $POST->state,
            "payment_method" => $POST->fee_label,
            "stage" => 1
        ]
    ];

    $jsonData = json_encode($applData);
    $request = $curl->request($system->App->ec_url.'/gateway/internal/reference/'.$_GET['id'], $jsonData);
    $trafficReturn = $traffic->requestAPI($request['traffic'], $request['url'], $request['request_method'], $request['headers'], $request['body'], $request['response'], $request['status']);

    $curlResult = json_decode($request['response']);

    return $curlResult;
}

function getCurrentStatus($conn,$systemId, $department) {
    $authority = 0;
    $stmt = $conn->prepare('SELECT status_id FROM ctrl_statuses WHERE system_id = :systemId AND department = :department AND authority = :authority');
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':authority', $authority);
    
    if($stmt->execute()){
        return $status_id = $stmt->fetch(PDO::FETCH_OBJ)->status_id;
    }else{
        http_response_code(403);
        $result = array(
            "success" => false,
            "message" => "Cannot get current status, please contact IT support."
        );
    }
}

function createFTPDirectories($systemId, $createdAt,$system) {
    $ftp = new FTPConnectionFactory();
    $store = $ftp->createConnection();
    if (!$store) {
        return "Cannot connect to FTP server";
    }
    // FTP directory structure
    $year = date("Y", strtotime("2025-06-15")); // Example date, replace with actual date if needed
    $baseDir = '/ftp';
    
    $directories = [
        $baseDir,
        "$baseDir/Projects",
        "$baseDir/Projects/$year",
        "$baseDir/Projects/$year/Documents",
        "$baseDir/Projects/$year/Documents/Submission",
        "$baseDir/Projects/$year/Documents/Plan",
        "$baseDir/Projects/$year/Geospatial",
        "$baseDir/Projects/$year/Geospatial/Maps",
        "$baseDir/Projects/$year/Geospatial/GISReady",
        "$baseDir/Projects/$year/Reports",
        "$baseDir/Projects/$year/Reports/SiteVisit",
        "$baseDir/Projects/$year/Reports/Survey",
    ];

        foreach ($directories as $dir) {
        
        // Check directory existence
        if (!ftp_chdir($store, $dir)) {
            // Try to create directory
            if (!ftp_mkdir($store, $dir)) {
                error_log("FTP mkdir failed: $dir");
                ftp_close($store);
                return false; // Failed to create directory
            }

            $systemType = ftp_systype($ftp);
            if (stripos($systemType, 'Windows') === false) {
                ftp_chmod($ftp, 0755, $path);
            }
        }else{
            error_log($dir . " does not exist. Creating...\n");
        }
    }

    ftp_close($store);
    return true; // All directories exist or created successfully
}