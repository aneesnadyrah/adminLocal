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
$conn = $db->createConnection();

$telegram = new Telegram();
$system = new System;
$username = $_SESSION['username'];
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$Curl = new Curl();
$traffic = new Traffic();
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();
// $status = $_GET["status"];
$ec_url = $system->App->ec_url;

$tenant = $system->App->tenant;
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($_GET['data'] === "note") {
        // Get the system ID from the POST request
        // Connect to the database
        // $db = new DBConnectionFactory();
        // $conn = $db->createConnection();

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT * FROM flw_survey_udm");

        // Execute the query
        $stmt->execute();

        // Fetch the data as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return success response
        echo json_encode([
            "success" => "Success",
            "status" => 200,
            "data" => $result,
        ]);

        // Close the database connection
        $conn = null;

    } else if ($_GET['data'] == "time") {
        // Get the current Unix timestamp in milliseconds
        $timestamp = round(microtime(true) * 1000);

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $systemId = $_GET['sid'];

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT created_timestamp, updated_timestamp FROM flw_survey_attandance WHERE system_id = :systemId AND initial_code IS NOT NULL ORDER BY id DESC LIMIT 1");

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Check if updated_timestamp is not null
            if (!is_null($row['updated_timestamp'])) {
                $latestTime = $row['updated_timestamp'];
            } else {
                $latestTime = $row['created_timestamp'];
            }
        } else {
            // Handle the case where no row is found
            $latestTime = null; // or set a default value
        }

        // Return success response
        echo json_encode([
            "success" => "Success",
            "status" => 200,
            'timestamp' => $timestamp,
            'latestTime' => $latestTime,
        ]);

        // Close the database connection
        $conn = null;
    } else if ($_GET['data'] == "pdfFilePIU") {

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $systemId = $_GET['system-id'];
        $attachmentType15 = 15;
        $mimeType15 = "application/pdf";

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachmentType15 AND mime_type = :mimeType15  ORDER BY id DESC LIMIT 1");

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':attachmentType15', $attachmentType15);
        $stmt->bindParam(':mimeType15', $mimeType15);

        // Execute the query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Extract the URL value from the row
            $url15 = base64_decode($row['url']);

            // Get the file name from the URL
            $fileName = $systemId . '-PIU.pdf';

            // Define the directory where you want to save the files
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/storage/pelan/piu/';

            // Check if the directory exists, if not create it
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true); // Change permission according to your requirement
            }

            // Get the file contents
            $fileContents = file_get_contents($url15);

            // Check if file contents were retrieved successfully
            if ($fileContents !== false) {
                // Save the file to the directory
                $savedFilePath = $uploadDirectory . $fileName;
                if (file_put_contents($savedFilePath, $fileContents) !== false) {
                    // Return success response
                    echo json_encode([
                        "success" => "Success",
                        "status" => 200,
                        "filePath" => $savedFilePath,
                        "fileName" => $fileName,
                    ]);

                } else {
                    // Error saving file 
                    echo "Failed to save the file.";
                }
            } else {
                // Error getting file contents
                echo "Failed to retrieve file contents.";
            }
        } else {
            echo "No attachment found for the specified criteria.";
        }
        // Close the database connection
        $conn = null;
    } else if ($_GET['data'] == "zipFilePIU") {

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $systemId = $_GET['system-id'];
        $attachmentType15 = 15;
        $mimeType15 = "application/zip";

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachmentType15 AND mime_type = :mimeType15  ORDER BY id DESC LIMIT 1");

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':attachmentType15', $attachmentType15);
        $stmt->bindParam(':mimeType15', $mimeType15);

        // Execute the query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // var_dump($row);

        if ($row) {
            // Extract the team value from the row
            $url15 = base64_decode($row['url']);

            // Get the file name from the URL
            $fileName = $systemId . '-PIU.zip';

            // Define the directory where you want to save the files
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/storage/pelan/piu/';

            // Check if the directory exists, if not create it
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true); // Change permission according to your requirement
            }

            // Get the file contents
            $fileContents = file_get_contents($url15);

            // Check if file contents were retrieved successfully
            if ($fileContents !== false) {
                // Save the file to the directory
                $savedFilePath = $uploadDirectory . $fileName;
                if (file_put_contents($savedFilePath, $fileContents) !== false) {
                    // Return success response
                    echo json_encode([
                        "success" => "Success",
                        "status" => 200,
                        "filePath" => $savedFilePath,
                        "fileName" => $fileName,
                    ]);

                } else {
                    // Error saving file 
                    echo "Failed to save the file.";
                }
            } else {
                // Error getting file contents
                echo "Failed to retrieve file contents.";
            }
        } else {
            echo "No attachment found for the specified criteria.";
        }

        // Close the database connection
        $conn = null;
    } else if ($_GET['data'] == "pdfFilePPT") {

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $systemId = $_GET['system-id'];
        $attachmentType16 = 16;
        $mimeType16 = "application/pdf";

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachmentType16 AND mime_type = :mimeType16  ORDER BY id DESC LIMIT 1");

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':attachmentType16', $attachmentType16);
        $stmt->bindParam(':mimeType16', $mimeType16);

        // Execute the query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Extract the team value from the row
            $url16 = base64_decode($row['url']);
            // Get the file name from the URL
            $fileName = $systemId . '-PPT.pdf';

            // Define the directory where you want to save the files
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/storage/pelan/ppt/';

            // Check if the directory exists, if not create it
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true); // Change permission according to your requirement
            }

            // Get the file contents
            $fileContents = file_get_contents($url16);

            // Check if file contents were retrieved successfully
            if ($fileContents !== false) {
                // Save the file to the directory
                $savedFilePath = $uploadDirectory . $fileName;

                // Save the file content to local directory
                if (file_put_contents($savedFilePath, $fileContents) !== false) {
                    // Return success response
                    echo json_encode([
                        "success" => "Success",
                        "status" => 200,
                        "filePath" => $savedFilePath,
                        "fileName" => $fileName,
                    ]);
                } else {
                    // Error saving file
                    echo "Failed to save the file.";
                }
            } else {
                // Error retrieving file contents
                echo "Failed to retrieve the file contents.";
            }
        } else {
            echo "No attachment found for the specified criteria.";
        }
        // Close the database connection
        $conn = null;
    } else if ($_GET['data'] == "zipFilePPT") {

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $systemId = $_GET['system-id'];
        $attachmentType16 = 16;
        $mimeType16 = "application/zip";

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachmentType16 AND mime_type = :mimeType16  ORDER BY id DESC LIMIT 1");

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':attachmentType16', $attachmentType16);
        $stmt->bindParam(':mimeType16', $mimeType16);

        // Execute the query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Extract the team value from the row
            $url16 = base64_decode($row['url']);
            // Get the file name from the URL
            $fileName = $systemId . '-PPT.zip';

            // Define the directory where you want to save the files
            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/storage/pelan/ppt/';

            // Check if the directory exists, if not create it
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true); // Change permission according to your requirement
            }

            // Get the file contents
            $fileContents = file_get_contents($url16);

            // Check if file contents were retrieved successfully
            if ($fileContents !== false) {
                // Save the file to the directory
                $savedFilePath = $uploadDirectory . $fileName;
                if (file_put_contents($savedFilePath, $fileContents) !== false) {
                    // Return success response
                    echo json_encode([
                        "success" => "Success",
                        "status" => 200,
                        "filePath" => $savedFilePath,
                        "fileName" => $fileName,
                    ]);

                } else {
                    // Error saving file 
                    echo "Failed to save the file.";
                }
            } else {
                // Error getting file contents
                echo "Failed to retrieve file contents.";
            }
        } else {
            echo "No attachment found for the specified criteria.";
        }

        // Close the database connection
        $conn = null;
    } else {
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if the URL contains a parameter named
    if (isset($POST['finance-remark'])) {
        //finance-remark
        $systemId = $POST['system-id'];
        $confirmValue = $POST['confirm'];

        // Connect to the database
        // $db = new DBConnectionFactory();
        // $conn = $db->createConnection();

        $timestamp = date('Y-m-d H:i:s', time());
        $financeRemark = isset($POST['finance-remark']) ? $POST['finance-remark'] : '';
        $user = $_SESSION['username'];

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        // Return the user information to the client
        if ($checking->rowCount() > 0) {
            // Record exists, update the record
            $assign = $conn->prepare("UPDATE flw_survey_udm SET finance_date = :financeDate, finance_remark = :financeRemark, finance_submitted_by = :user WHERE system_id = :systemId");
        } else {
            // Record doesn't exist, insert a new record
            $assign = $conn->prepare("INSERT INTO flw_survey_udm (system_id, finance_date, finance_remark, finance_submitted_by) VALUES (:systemId, :financeDate, :financeRemark, :user)");
        }
        // Bind the parameters
        $assign->bindParam(':systemId', $systemId);
        $assign->bindParam(':financeDate', $timestamp);
        $assign->bindParam(':financeRemark', $financeRemark);
        $assign->bindParam(':user', $user);
        $assign->execute();

        if ($confirmValue === "1") {
            //finance luluskan

            $flwStatus = 41;
            $status = 40;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);
            // var_dump('masukk');
            //Record Activity
            $text = 'Pengguna ' . $username . ' telah meluluskan permohonan mula kerja ukur bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 39 goToNextFlow = 41, flw_task_assignment = 40
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");
            // var_dump($NextStatus);

            $details = 'Permohonan Mula Kerja Ukur telah Diluluskan bagi ' . $refNo . '. Nota: ' . $financeRemark;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, 39, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Permohonan Mula Kerja Ukur telah Diluluskan. \n\n?? <strong> No Rujukan : " . $refNo . "</strong>";
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

        } else if ($confirmValue === "2") {
            //finance tak luluskan

            $triggerPriority = false;
            // Record exists, update the record
            $query = $conn->prepare("UPDATE flw_survey_udm SET trigger_priority = :triggerPriority WHERE system_id = :systemId");

            // Bind the parameters
            $query->bindParam(':systemId', $systemId);
            $query->bindParam(':triggerPriority', $triggerPriority, PDO::PARAM_BOOL);
            $query->execute();

            $flwStatus = 39;
            $status = 40;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menolak permohonan mula kerja ukur bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 39,  flw_task_assignment = 40
            //nextstep for mapping
            // $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Permohonan Mula Kerja Ukur ditolak bagi ' . $refNo . '. Nota: ' . $financeRemark;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Permohonan Mula Kerja Ukur telah ditolak. \n?? <strong> No Rujukan : " . $refNo . "</strong>";
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

        }

        // Close the database connection
        $conn = null;

    } else if (isset($POST['survey_team'])) {
        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        // Retrieve the selected group from the request
        $surveyTeam = $POST['survey_team'];

        // Get the team members for the selected group
        $query = $conn->prepare("SELECT * FROM flw_survey_team WHERE id = :surveyTeam AND is_active = true");

        // Bind the parameters
        $query->bindParam(':surveyTeam', $surveyTeam);

        // Execute the query
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);

        $dataR = $row['team_members'];
        $memberImg = $row['profile_picture'];

        $dataT = trim($dataR, '{}"');

        $data = explode(",", $dataT);

        $array = [];
        // var_dump($data);
        // $userRole = 64;

        foreach ($data as $name) {
            // Get the team members for the selected group
            $query2 = $conn->prepare("SELECT employee_id, role_id FROM sys_users WHERE username = :name");

            // Bind the parameters
            $query2->bindParam(':name', $name);

            // Execute the query
            $query2->execute();

            if ($query2) {
                $row2 = $query2->fetch(PDO::FETCH_ASSOC);
                $accessRole = $row2['role_id'];

                // Check if accessRole is equal to 64 before appending it to the array
                if ($accessRole == 64) {
                    $id = $row2['employee_id'];
                    $array[] = $id;
                }

            } else {
                // Handle the case where the query encountered an error
                // die("Error in query2: " . $query2->errorInfo()[2]);
                die("Error in query2: employee_id not found.");
            }
        }

        $array2 = [];

        foreach ($array as $staffId) {
            // Get the team members for the selected group
            $query3 = $conn->prepare("SELECT first_name FROM sys_hr_employee WHERE id = :staffId");

            // Bind the parameters
            $query3->bindParam(':staffId', $staffId);

            // Execute the query
            $query3->execute();

            $row3 = $query3->fetch(PDO::FETCH_ASSOC);

            $data3 = $row3['first_name'];
            $array2[] = $data3;
        }

        // Close the database connection
        $conn = null;

        $response = array(
            'teamMembers' => $array2,
            'membersImg' => $memberImg
        );
        echo json_encode($response);

    } else if (isset($POST['progress-udm'])) {
        $systemId = $POST['system-id'];
        // Call the generateID function to fetch the ID value
        $idRepeat = SurveyApi::repeaterID();

        // Get the user's identification from the request parameters
        $surveyTeam = isset($POST['survey-team']) ? $POST['survey-team'] : '';
        $refNo = isset($POST['noRef']) ? $POST['noRef'] : '';
        $length = isset($POST['application-length']) ? $POST['application-length'] : '';
        $application_length = floatval($length);
        $datePicker = isset($POST['date']) ? $POST['date'] : '';
        $startTime = isset($POST['start-time']) ? date('H:i', strtotime($POST['start-time'])) : '';
        $endTime = isset($POST['end-time']) ? date('H:i', strtotime($POST['end-time'])) : '';
        $startCoord = isset($POST['start-coord']) ? $POST['start-coord'] : '';
        $endCoord = isset($POST['end-coord']) ? $POST['end-coord'] : '';
        list($slat, $slong) = explode(",", $startCoord);
        $slat = floatval($slat);
        $slong = floatval($slong);

        list($elat, $elong) = explode(",", $endCoord);
        $elat = floatval($elat);
        $elong = floatval($elong);
        // Get the current progress with appropriate checks and default value
        $currentProgres = isset($POST['current-progress']) && is_numeric($POST['current-progress']) ? $POST['current-progress'] : 0;
        $dailyProgress = isset($POST['daily-progress']) ? floatval($POST['daily-progress']) : 0;
        $currentProgress = $currentProgres + $dailyProgress;
        // Get the double precision values with appropriate checks and default values
        $peggingDistance = isset($POST['pegging-distance']) && is_numeric($POST['pegging-distance']) ? floatval($POST['pegging-distance']) : 0;
        $detectionDistance = isset($POST['detection-distance']) && is_numeric($POST['detection-distance']) ? floatval($POST['detection-distance']) : 0;

        // $systemId = $POST['system-id'];
        $timestamp = date('Y-m-d H:i:s', time());
        $user = $_SESSION['username'];
        $name = SurveyApi::getSurveyFirstname($user);
        $jarakPengukuran = isset($POST['jarak_pengukuran']) ? $POST['jarak_pengukuran'] : 0;

        $timeConverted = Utilities::convertDateToMalay($timestamp);
        $surveyApi = new SurveyApi($username);
        $surveyLength = $surveyApi->getSurveyLength($systemId);

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_survey_reports_udm WHERE system_id = :systemId ");

        // Bind the parameters
        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        //kalay sistem id dah ada
        if ($checking->rowCount() > 0) {
            // Prepare the SELECT query using PDO
            $extractCurrentDistance = $conn->prepare("SELECT progress_pending, pegging_pending, detection_pending FROM flw_survey_reports_udm WHERE system_id = :systemId ORDER BY id DESC LIMIT 1");

            $extractCurrentDistance->bindParam(':systemId', $systemId);
            $extractCurrentDistance->execute();

            // Fetch the row from the query result as an associative array
            $currentDistanceRow = $extractCurrentDistance->fetch(PDO::FETCH_ASSOC);

            // Extract values from the row
            $progress_pending = $currentDistanceRow['progress_pending'];
            $pegging_pending = $currentDistanceRow['pegging_pending'];
            $detection_pending = $currentDistanceRow['detection_pending'];

            $progressLength = floatval($currentDistanceRow['progress_pending']);
            $peggingLength = floatval($currentDistanceRow['pegging_pending']);
            $detectionLength = floatval($currentDistanceRow['detection_pending']);

            $progressPending = $progressLength - $dailyProgress;
            $peggingPending = $peggingLength - $peggingDistance;
            $detectionPending = $detectionLength - $detectionDistance;
            // $currentProgress = $application_length - $progressPending;

        
        }
        //kalau sistem id belum ada
        else {
            $progressPending = $application_length - $dailyProgress;
            $peggingPending = $application_length - $peggingDistance;
            $detectionPending = $application_length - $detectionDistance;
            // $currentProgress = 0;
        }

        if ($peggingPending === null || $peggingPending == 0 || $peggingDistance === $application_length) {
            $currentPegging = $application_length; 
        } else {
            $currentPegging = $application_length - $peggingPending;
        }

        if ($detectionPending === null || $detectionPending == 0 || $detectionDistance === $application_length) {
            $currentDetection = $application_length;
        } else {
            $currentDetection = $application_length - $detectionPending;
        }
            
        // Get the team members for the selected group
        $query = $conn->prepare("SELECT * FROM flw_survey_team WHERE id = :surveyTeam");
        $query->bindParam(':surveyTeam', $surveyTeam);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        $members = $row['team_members'];

        // Build the INSERT query with the modified current progress value
        $query = "INSERT INTO flw_survey_reports_udm (system_id, reference_no, survey_team, application_length, survey_date, survey_time_start, survey_time_end, current_progress, daily_progress, pegging_distance, detection_distance, progress_pending, pegging_pending, detection_pending, created_timestamp, created_id, id_repeat, latitude_start, longitude_start, latitude_end, longitude_end, team_members)
        VALUES (:systemId, :noRef, :surveyTeam, :application_length, :datePicker, :startTime, :endTime, :currentProgress, :dailyProgress, :peggingDistance, :detectionDistance, :progressPending, :peggingPending, :detectionPending, :submitted, :user, :idRepeat, :slat, :slong, :elat, :elong, :members)";

        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':noRef', $refNo); 
        $stmt->bindParam(':surveyTeam', $surveyTeam);
        $stmt->bindParam(':application_length', $application_length);
        $stmt->bindParam(':datePicker', $datePicker);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->bindParam(':endTime', $endTime);
        $stmt->bindParam(':currentProgress', $currentProgress);
        $stmt->bindParam(':dailyProgress', $dailyProgress);
        $stmt->bindParam(':peggingDistance', $peggingDistance);
        $stmt->bindParam(':detectionDistance', $detectionDistance);
        $stmt->bindParam(':progressPending', $progressPending);
        $stmt->bindParam(':peggingPending', $peggingPending);
        $stmt->bindParam(':detectionPending', $detectionPending);
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':idRepeat', $idRepeat);
        $stmt->bindParam(':slat', $slat);
        $stmt->bindParam(':slong', $slong);
        $stmt->bindParam(':elat', $elat);
        $stmt->bindParam(':elong', $elong);
        $stmt->bindParam(':members', $members);

        // Execute the query
        $result = $stmt->execute();
        $status = 45;

        $diffLength = $application_length - $currentProgress;

        // Update the record
        $query2 = "UPDATE flw_survey_udm SET survey_length = :surveyLength, diff_length = :diffLength WHERE system_id = :systemId";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':surveyLength', $currentProgress);
        $stmt2->bindParam(':diffLength', $diffLength);
        // Execute the query
        $result2 = $stmt2->execute();

        // Select the record
        $query3 = "SELECT survey_team FROM flw_survey_team WHERE id = :surveyTeam AND is_active = true";
        $stmt3 = $conn->prepare($query3);
        $stmt3->bindParam(':surveyTeam', $surveyTeam);
        // Execute the query
        $stmt3->execute();

        // Fetch the result
        $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);

        // Rename the survey_team to teamName
        $teamName = $row3['survey_team'];

        // Check for balance
        if (($progressPending == 0) && ($peggingPending == 0) && ($detectionPending == 0)) {

            $flwStatus = 46;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah sahkan kerja pengukuran selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 45
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Selesai Kerja Pengukuran bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "<strong>LAPORAN HARIAN TAPAK </strong> \n\nKumpulan : " . $teamName . " \nNo Rujukan : " . $refNo . " \nJarak Permohonan : " . $application_length . " meter \nTarikh : " . $datePicker . " \nWaktu Kerja : " . $startTime . " - " . $endTime . " \n\nProgress Harian \nDetection : " . $detectionDistance . " meter \nPegging : " . $peggingDistance . " meter \n\nProgress Semasa \nDetection : " . $currentDetection . " meter \nPegging : " . $currentPegging . " meter \n\nSelesai Kerja Pengukuran.";
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
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

        } elseif (($progressPending < 0)) {

            $flwStatus = 46;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah sahkan kerja pengukuran selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 45
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Selesai Kerja Pengukuran. Jarak Ukur Lebih Daripada Jarak Permohonan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "<strong>LAPORAN HARIAN TAPAK </strong> \n\nKumpulan : " . $teamName . " \nNo Rujukan : " . $refNo . " \nJarak Permohonan : " . $application_length . " meter \nTarikh : " . $datePicker . " \nWaktu Kerja : " . $startTime . " - " . $endTime . " \n\nProgress Harian \nDetection : " . $detectionDistance . " meter \nPegging : " . $peggingDistance . " meter \n\nProgress Semasa \nDetection : " . $currentDetection . " meter \nPegging : " . $currentPegging . " meter \n\nSelesai Kerja Pengukuran.\nJarak Ukur Lebih Daripada Jarak Permohonan.";
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
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

        } elseif ($jarakPengukuran == 1) {

            $flwStatus = 46;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah sahkan kerja pengukuran selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 45
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Selesai Kerja Pengukuran. Jarak Ukur Kurang Daripada Jarak Permohonan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "<strong>LAPORAN HARIAN TAPAK  </strong> \n\nKumpulan : " . $teamName . " \nNo Rujukan : " . $refNo . " \nJarak Permohonan : " . $application_length . " meter \nTarikh : " . $datePicker . " \nWaktu Kerja : " . $startTime . " - " . $endTime . " \n\nProgress Harian \nDetection : " . $detectionDistance . " meter \nPegging : " . $peggingDistance . " meter \n\nProgress Semasa \nDetection : " . $currentDetection . " meter \nPegging : " . $currentPegging . " meter \n\nSelesai Kerja Pengukuran.\nJarak Ukur Kurang Daripada Jarak Permohonan.";
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
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

        } else {
            //jump step backward from 45 to 42
            // to create new status in task assignment DB
            $flwStatus = 42;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menerima laporan harian kerja ukur bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 45
            //nextstep for mapping
            $NextStatus = $flow->goToPrevFlow($systemId, "mapping", "BF", Steps: 42);
            // var_dump($NextStatus);  

            $details = 'Laporan Harian telah diterima bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "<strong>LAPORAN HARIAN TAPAK </strong> \n\nKumpulan : " . $teamName . " \nNo Rujukan : " . $refNo . " \nJarak Permohonan : " . $application_length . " meter \nTarikh : " . $datePicker . " \nWaktu Kerja : " . $startTime . " - " . $endTime . " \n\nProgress Harian \nDetection : " . $detectionDistance . " meter \nPegging : " . $peggingDistance . " meter \n\nProgress Semasa \nDetection : " . $currentDetection . " meter \nPegging : " . $currentPegging . " meter";
                // NOTE - send telegram notification for team survey
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
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
        echo json_encode(
            [
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
                "noRef" => $refNo,
                "progress" => ($currentProgress + $dailyProgress),
                "application_length" => $application_length,
                "created_id" => $user,
            ]
        );

        // Close the database connection
        $conn = null;

    } else if (isset($POST['report_udm']) && $POST['report_udm'] == 1) {

        // Get the system ID from the POST request
        $systemId = $POST['system_id'];
        $notes = "disemak";
        $projectStatus = SurveyApi::getProjectStatus($systemId);
        $user = $_SESSION['username'];

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        $timestamp = date('Y-m-d H:i:s', time());
        $timeConverted = Utilities::convertDateToMalay($timestamp);

        $flwStatus = 48;
        $status = 47;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan laporan penuh diterima bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 47
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Laporan Penuh telah diterima bagi ' . $refNo . '. Nota: ' . $notes;
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Laporan Penuh telah disemak. Sila teruskan dengan Pengesahan Laporan Penuh. \n\n<strong>?? No Rujukan : " . $refNo . " \n?? Nama Pengguna : " . $user . "  </strong>";
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

    } else if (isset($POST['report_udm']) && $POST['report_udm'] == 2) {

        // Get the system ID from the POST request
        $systemId = $POST['system_id'];
        $notes = "disahkan";
        $projectStatus = SurveyApi::getProjectStatus($systemId);
        $user = $_SESSION['username'];

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        $timestamp = date('Y-m-d H:i:s', time());
        $timeConverted = Utilities::convertDateToMalay($timestamp);

        $flwStatus = 49;
        $status = 48;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan laporan penuh bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 48
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Laporan Penuh telah disahkan bagi ' . $refNo . '. Nota: ' . $notes;
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Laporan Penuh telah disahkan. \n\n<strong>?? No Rujukan : " . $refNo . "  \n?? Nama Pengguna : " . $user . "  </strong>";
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

    } else if (isset($POST['progress-asb'])) {
        $systemId = $POST['system-id'];
        // Call the generateID function to fetch the ID value
        $idRepeat = SurveyApi::repeaterID_ASB();

        // Get the user's identification from the request parameters
        $surveyTeam = isset($POST['survey-team']) ? $POST['survey-team'] : '';
        $refNo = isset($POST['noRef']) ? $POST['noRef'] : '';
        $length = isset($POST['application-length']) ? $POST['application-length'] : '';
        $application_length = floatval($length);
        $datePicker = isset($POST['date']) ? $POST['date'] : '';
        $startTime = isset($POST['start-time']) ? date('H:i', strtotime($POST['start-time'])) : '';
        $endTime = isset($POST['end-time']) ? date('H:i', strtotime($POST['end-time'])) : '';
        $startCoord = isset($POST['start-coord']) ? $POST['start-coord'] : '';
        $endCoord = isset($POST['end-coord']) ? $POST['end-coord'] : '';
        list($slat, $slong) = explode(",", $startCoord);
        $slat = floatval($slat);
        $slong = floatval($slong);

        list($elat, $elong) = explode(",", $endCoord);
        $elat = floatval($elat);
        $elong = floatval($elong);
        // Get the current progress with appropriate checks and default value
        $currentProgres = isset($POST['current-progress']) && is_numeric($POST['current-progress']) ? $POST['current-progress'] : null;
        $dailyProgress = isset($POST['daily-progress']) ? floatval($POST['daily-progress']) : 0;
        $currentProgress = $currentProgres + $dailyProgress;
        // Get the double precision values with appropriate checks and default values
        $peggingDistance = isset($POST['pegging-distance']) && is_numeric($POST['pegging-distance']) ? floatval($POST['pegging-distance']) : null;
        $detectionDistance = isset($POST['detection-distance']) && is_numeric($POST['detection-distance']) ? floatval($POST['detection-distance']) : null;
        0;
        // $systemId = $POST['system-id'];
        $timestamp = date('Y-m-d H:i:s', time());
        $user = $_SESSION['username'];
        $name = SurveyApi::getSurveyFirstname($user);
        $jarakPengukuran = isset($POST['jarak_pengukuran']) ? $POST['jarak_pengukuran'] : 0;

        $timeConverted = Utilities::convertDateToMalay($timestamp);
        $surveyApi = new SurveyApi($username);
        $surveyLength = $surveyApi->getSurveyLengthASB($systemId);

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_survey_reports_asb WHERE system_id = :systemId ");

        // Bind the parameters
        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        //kalay sistem id dah ada
        if ($checking->rowCount() > 0) {
            // Prepare the SELECT query using PDO
            $extractCurrentDistance = $conn->prepare("SELECT progress_pending, pegging_pending, detection_pending FROM flw_survey_reports_asb WHERE system_id = :systemId ORDER BY id DESC LIMIT 1");

            $extractCurrentDistance->bindParam(':systemId', $systemId);
            $extractCurrentDistance->execute();

            // Fetch the row from the query result as an associative array
            $currentDistanceRow = $extractCurrentDistance->fetch(PDO::FETCH_ASSOC);

            // Extract values from the row
            $progress_pending = $currentDistanceRow['progress_pending'];
            $pegging_pending = $currentDistanceRow['pegging_pending'];
            $detection_pending = $currentDistanceRow['detection_pending'];

            $progressLength = floatval($currentDistanceRow['progress_pending']);
            $peggingLength = floatval($currentDistanceRow['pegging_pending']);
            $detectionLength = floatval($currentDistanceRow['detection_pending']);

            $progressPending = $progressLength - $dailyProgress;
            $peggingPending = $peggingLength - $peggingDistance;
            $detectionPending = $detectionLength - $detectionDistance;
            // $currentProgress = $application_length - $progressPending;
        }
        //kalau sistem id belum ada
        else {
            $progressPending = $application_length - $dailyProgress;
            $peggingPending = $application_length - $peggingDistance;
            $detectionPending = $application_length - $detectionDistance;
            // $currentProgress = 0;
        }

        // Get the team members for the selected group
        $query = $conn->prepare("SELECT * FROM flw_survey_team WHERE id = :surveyTeam");
        $query->bindParam(':surveyTeam', $surveyTeam);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        $members = $row['team_members'];

        // Build the INSERT query with the modified current progress value
        $query = "INSERT INTO flw_survey_reports_asb (system_id, reference_no, survey_team, application_length, survey_date, survey_time_start, survey_time_end, current_progress, daily_progress, pegging_distance, detection_distance, progress_pending, pegging_pending, detection_pending, created_timestamp, created_id, id_repeat, latitude_start, longitude_start, latitude_end, longitude_end, team_members)
        VALUES (:systemId, :noRef, :surveyTeam, :application_length, :datePicker, :startTime, :endTime, :currentProgress, :dailyProgress, :peggingDistance, :detectionDistance, :progressPending, :peggingPending, :detectionPending, :submitted, :user, :idRepeat, :slat, :slong, :elat, :elong, :members)";

        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':noRef', $refNo);
        $stmt->bindParam(':surveyTeam', $surveyTeam);
        $stmt->bindParam(':application_length', $application_length);
        $stmt->bindParam(':datePicker', $datePicker);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->bindParam(':endTime', $endTime);
        $stmt->bindParam(':currentProgress', $currentProgress);
        $stmt->bindParam(':dailyProgress', $dailyProgress);
        $stmt->bindParam(':peggingDistance', $peggingDistance);
        $stmt->bindParam(':detectionDistance', $detectionDistance);
        $stmt->bindParam(':progressPending', $progressPending);
        $stmt->bindParam(':peggingPending', $peggingPending);
        $stmt->bindParam(':detectionPending', $detectionPending);
        $stmt->bindParam(':submitted', $timestamp);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':idRepeat', $idRepeat);
        $stmt->bindParam(':slat', $slat);
        $stmt->bindParam(':slong', $slong);
        $stmt->bindParam(':elat', $elat);
        $stmt->bindParam(':elong', $elong);
        $stmt->bindParam(':members', $members);

        // Execute the query
        $result = $stmt->execute();
        $status = 101;

        $diffLength = $application_length - $currentProgress;

        // Update the record
        $query2 = "UPDATE flw_survey_asb SET survey_length = :surveyLength, diff_length = :diffLength WHERE system_id = :systemId";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindParam(':surveyLength', $currentProgress);
        $stmt2->bindParam(':diffLength', $diffLength);
        // Execute the query
        $result2 = $stmt2->execute();

        // Check for balance
        if (($progressPending == 0) && ($peggingPending == 0) && ($detectionPending == 0)) {

            $flwStatus = 102;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah sahkan kerja pengukuran (PSB) selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 101
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Selesai Kerja Pengukuran (PSB) bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Selesai Kerja Pengukuran (PSB). \n\n?? <strong> No Rujukan : " . $refNo . " \n?? Jarak Permohonan : " . $application_length . " meter \n?? Jarak Diukur (PSB): " . $currentProgress . " meter </strong>";
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

        } elseif (($progressPending < 0)) {

            $flwStatus = 102;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah sahkan kerja pengukuran (PSB) selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 101
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Selesai Kerja Pengukuran (PSB). Jarak Ukur Lebih Daripada Jarak Permohonan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Selesai Kerja Pengukuran (PSB). Jarak Ukur Lebih Daripada Jarak Permohonan. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Jarak Permohonan : " . $application_length . " meter \n?? Jarak Diukur (PSB) : " . $currentProgress . " meter </strong>";
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

        } elseif ($jarakPengukuran == 1) {

            $flwStatus = 102;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah sahkan kerja pengukuran (PSB) selesai bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 101
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

            $details = 'Selesai Kerja Pengukuran (PSB). Jarak Ukur Kurang Daripada Jarak Permohonan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Selesai Kerja Pengukuran (PSB). Jarak Ukur Kurang Daripada Jarak Permohonan. \n\n?? <strong> No Rujukan : " . $refNo . "  \n?? Jarak Permohonan : " . $application_length . " meter \n?? Jarak Diukur (PSB) : " . $currentProgress . " meter </strong>";
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

        } else {
            //jump step backward from 101 to 98
            // to create new status in task assignment DB
            $flwStatus = 98;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menerima laporan harian kerja ukur (PSB) bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 101
            //nextstep for mapping
            $NextStatus = $flow->goToPrevFlow($systemId, "mapping", "BF", Steps: 98);
            // var_dump($NextStatus);  

            $details = 'Laporan Harian (PSB) telah diterima bagi ' . $refNo . '. Nota: ' . $POST['notes'];
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Laporan Harian (PSB) telah diterima. \n\n?? <strong> No Rujukan : " . $refNo . " \n?? Jarak Permohonan : " . $application_length . " meter \n?? Jarak Diukur (PSB) : " . $currentProgress . " meter </strong>";
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
        echo json_encode(
            [
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
                "noRef" => $refNo,
                "progress" => ($currentProgress + $dailyProgress),
                "application_length" => $application_length,
                "created_id" => $user,
            ]
        );

        // Close the database connection
        $conn = null;

    } else if (isset($POST['report_asb']) && $POST['report_asb'] == 1) {

        // Get the system ID from the POST request
        $systemId = $POST['system_id'];
        $notes = "disemak";
        $projectStatus = SurveyApi::getProjectStatus($systemId);
        $user = $_SESSION['username'];

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        $timestamp = date('Y-m-d H:i:s', time());
        $timeConverted = Utilities::convertDateToMalay($timestamp);

        $flwStatus = 104;
        $status = 103;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan laporan penuh (PSB) diterima bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 103
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Laporan Penuh (PSB) telah diterima bagi ' . $refNo . '. Nota: ' . $notes;
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Laporan Penuh telah disemak. Sila teruskan dengan Pengesahan Laporan Penuh. \n\n<strong>?? No Rujukan : " . $refNo . " \n?? Nama Pengguna : " . $user . "  </strong>";
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

    } else if (isset($POST['report_asb']) && $POST['report_asb'] == 2) {

        // Get the system ID from the POST request
        $systemId = $POST['system_id'];
        $notes = "disahkan";
        $projectStatus = SurveyApi::getProjectStatus($systemId);
        $user = $_SESSION['username'];

        // Connect to the database
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        $timestamp = date('Y-m-d H:i:s', time());
        $timeConverted = Utilities::convertDateToMalay($timestamp);

        $flwStatus = 105;
        $status = 104;
        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        $taskAssignment->create($systemId, $flwStatus);

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengesahkan laporan penuh (PSB) bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        // NOTE - current status = 104
        //nextstep for mapping
        $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF");

        $details = 'Laporan Penuh (PSB) telah disahkan bagi ' . $refNo . '. Nota: ' . $notes;
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            // $telegramMsg = "Laporan Penuh (PSB) telah disahkan. \n\n<strong>?? No Rujukan : " . $refNo . "  \n?? Nama Pengguna : " . $user . "  </strong>";
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

    } else if (isset($POST['survey-provider'])) {
        //survey-team-assign
        $systemId = $POST['system-id'];
        $projectStatus = SurveyApi::getProjectStatus($systemId);
        $surveyProvider = isset($POST['survey-provider']) ? $POST['survey-provider'] : '';
        $timestamp = date('Y-m-d H:i:s', time());
        $timeConverted = Utilities::convertDateToMalay($timestamp);
        $user = $_SESSION['username'];
        $uuid = SurveyApi::generateUUID();

        // Insert into flw_appl_plan using the retrieved ID
        $applPlanInsert = $conn->prepare("INSERT INTO flw_appl_plan (system_id, created_timestamp, sharing_code) VALUES (:systemId, :submitted, :uuid)");

        $applPlanInsert->bindParam(':systemId', $systemId);
        $applPlanInsert->bindParam(':submitted', $timestamp);
        $applPlanInsert->bindParam(':uuid', $uuid);
        $applPlanInsert->execute();

        //To get reference number
        $refNo = Utilities::getRefNo($systemId);

        //NOTE: get url attachment type = 8
        $query5 = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 8");

        // Bind the parameters
        $query5->bindParam(':systemId', $systemId);
        $query5->execute();

        // Fetch the result row
        $row5 = $query5->fetch(PDO::FETCH_ASSOC);

        if ($row5) {
            // Extract the team value from the row
            $url8 = base64_decode($row5['url']);
        } else {
            $url8 = '';
        }

        //NOTE: get url attachment type = 3
        $query6 = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 3");

        // Bind the parameters
        $query6->bindParam(':systemId', $systemId);
        $query6->execute();

        // Fetch the result row
        $row6 = $query6->fetch(PDO::FETCH_ASSOC);

        if ($row6) {
            // Extract the team value from the row
            $url3 = base64_decode($row6['url']);
        } else {
            $url3 = '';
        }

        //NOTE: get url attachment type = 4 Gambar Lokasi
        $query7 = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 4");

        // Bind the parameters
        $query7->bindParam(':systemId', $systemId);
        $query7->execute();

        // Fetch the result row
        $row7 = $query7->fetch(PDO::FETCH_ASSOC);

        if ($row7) {
            // Extract the team value from the row
            $url4 = base64_decode($row7['url']);
        } else {
            $url4 = '';
        }

        //NOTE: get url attachment type = 7 Laporan LTA
        $query8 = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 7");

        // Bind the parameters
        $query8->bindParam(':systemId', $systemId);
        $query8->execute();

        // Fetch the result row
        $row8 = $query8->fetch(PDO::FETCH_ASSOC);

        if ($row8) {
            // Extract the team value from the row
            $url7 = base64_decode($row8['url']);
        } else {
            $url7 = '';
        }

        //NOTE: get url attachment type = 87 Data Lot
        $query9 = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 87");

        // Bind the parameters
        $query9->bindParam(':systemId', $systemId);
        $query9->execute();

        // Fetch the result row
        $row9 = $query9->fetch(PDO::FETCH_ASSOC);

        if ($row9) {
            // Extract the team value from the row
            $url87 = base64_decode($row9['url']);
        } else {
            $url87 = '';
        }

        if ($surveyProvider == 'Inhouse') {
            $surveyTeamAssign = isset($POST['survey-group']) ? $POST['survey-group'] : '';
            $surveyGroupMember = SurveyApi::getSurveyMemberGroup($surveyTeamAssign);
            $dateRange = isset($POST['modal-date-range']) ? $POST['modal-date-range'] : '';
            list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
            $startDate = date('Y-m-d', strtotime($startDateStr));
            $endDate = date('Y-m-d', strtotime($endDateStr));

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
                $status = 41;
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

                $details = 'Pembahagian Tugasan Pengukuran Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {
                    // declare telegram notification string
                    $message = "Pembahagian Tugasan Pengukuran Selesai.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? Kumpulan : " . $team . "</strong>";
                    // $message = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, date: $timeConverted, item: $team);

                    // NOTE - send telegram notification for role 28-Ketua Survey
                    $response = $telegram->sendMessage('group', 'management', $message, 'html');
                    $response = $telegram->sendMessage('role', 28, $message, 'html');
                    $response2 = $telegram->sendDocument('group', 'management', $url8, '');
                    $response2 = $telegram->sendDocument('role', 28, $url8, '');
                    $response3 = $telegram->sendDocument('group', 'management', $url3, '');
                    $response3 = $telegram->sendDocument('role', 28, $url3, '');
                    $response4 = $telegram->sendMessage('role', 28, $url7, 'html');
                    $response5 = $telegram->sendDocument('role', 28, $url4, '');
                    $response6 = $telegram->sendDocument('role', 28, $url87, '');

                    // declare telegram notification string
                    $message2 = "Pembahagian Tugasan Pengukuran Selesai. Sila Mula Kerja Ukur.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? Kumpulan : " . $team . "</strong>";

                    foreach($surveyGroupMember as $member){
                        // NOTE - send telegram notification for role 23-Ketua Kumpulan Ukur Survey
                        $response7 = $telegram->sendMessage('assignee', $member, $message2, 'html');
                        $response8 = $telegram->sendDocument('assignee', $member, $url8, '');
                        $response9 = $telegram->sendDocument('assignee', $member, $url3, '');
                        $response10 = $telegram->sendMessage('assignee', $member, $url7, 'html');
                        $response11 = $telegram->sendDocument('assignee', $member, $url4, '');
                        $response12 = $telegram->sendDocument('assignee', $member, $url87, '');
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
            // Execute a SELECT query on the database
            $checking2 = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

            $checking2->bindParam(':systemId', $systemId);
            $checking2->execute();

            // Return the user information to the client
            if ($checking2->rowCount() > 0) {
                // Execute a SELECT query on the database
                $assign2 = $conn->prepare("UPDATE flw_survey_udm SET submission_date = :submitted, submission_name = :user, survey_provider = :surveyProvider WHERE system_id = :systemId");
            } else {
                // Execute a SELECT query on the database
                $assign2 = $conn->prepare("INSERT INTO flw_survey_udm (system_id, submission_date, submission_name, survey_provider) VALUES (:systemId, :submitted, :user, :surveyProvider)");
            }
            // Bind the parameters
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
                $status = 41;
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

                $details = 'Pembahagian Tugasan Pengukuran Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {
                    // declare telegram notification string
                    $message = "Pembahagian Tugasan Pengukuran Selesai.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . "</strong>";

                    // NOTE - send telegram notification for role 28-Ketua Survey
                    $response = $telegram->sendMessage('group', 'management', $message, 'html');
                    $response = $telegram->sendMessage('role', 28, $message, 'html');
                    $response2 = $telegram->sendDocument('group', 'management', $url8, '');
                    $response2 = $telegram->sendDocument('role', 28, $url8, '');
                    $response3 = $telegram->sendDocument('group', 'management', $url3, '');
                    $response3 = $telegram->sendDocument('role', 28, $url3, '');

                    // Set your API parameters
                    // $whatsappMsg = "Pembahagian Tugasan Pengukuran Selesai. \n\n No Rujukan : " . $refNo . " ";

                    // $applData = array(
                    //     "secret" => $system->App->secret,
                    //     "notification" => array(
                    //         "message" => $whatsappMsg,
                    //         "phones" => [],
                    //         "attachments" => [
                    //         ]
                    //     )
                    // );

                    // Convert the array to JSON
                    // $payload = json_encode($applData);

                    // $requestUrl = $ec_url.'/gateway/internal/notify/'.$systemId;
                    
                    // make api request to extercord
                    // $apiRequest = $Curl->request($requestUrl, json_encode($payload));

                    // https //:wapi.asiadebut.tech portnumber:8552
                    // make api request to extercord
                    // $apiRequest = $Curl->request('https://extercord.asiadebut.tech/gateway/internal/notify/' . $systemId, $jsonData);

                    // $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

                    // Decode the response JSON
                    // $curlResult = json_decode($apiRequest['response']);
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
        }
        // else if ($surveyProvider == 'Outsource') {
        //     $idSurveyor = isset($POST['survey-outsource']) ? $POST['survey-outsource'] : '';
        //     $dateRange = isset($POST['modal-date-range2']) ? $POST['modal-date-range2'] : '';
        //     list($startDateStr, $endDateStr) = explode(' hingga ', $dateRange);
        //     $startDate = date('Y-m-d', strtotime($startDateStr));
        //     $endDate = date('Y-m-d', strtotime($endDateStr));
        //     // var_dump($idSurveyor);

        //     //NOTE: get team
        //     $query4 = $conn->prepare("SELECT full_name FROM ls_sr_contact WHERE id = :idSurveyor");

        //     // // Bind the parameters
        //     $query4->bindParam(':idSurveyor', $idSurveyor);
        //     $query4->execute();

        //     // // Fetch the result row
        //     $row4 = $query4->fetch(PDO::FETCH_ASSOC);

        //     // // Extract the team value from the row
        //     $surveySurveyor = $row4['full_name'];

        //     // Execute a SELECT query on the database
        //     $checking2 = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        //     $checking2->bindParam(':systemId', $systemId);
        //     $checking2->execute();

        //     // Return the user information to the client
        //     if ($checking2->rowCount() > 0) {
        //         // Execute a SELECT query on the database
        //         $assign2 = $conn->prepare("UPDATE flw_survey_udm SET surveyor_id = :idSurveyor, est_date_start = :startDate, est_date_end = :endDate, submission_date = :submitted, submission_name = :user, survey_provider = :surveyProvider WHERE system_id = :systemId");
        //     } else {
        //         // Execute a SELECT query on the database
        //         $assign2 = $conn->prepare("INSERT INTO flw_survey_udm (system_id, surveyor_id, est_date_start, est_date_end, submission_date, submission_name, survey_provider) VALUES (:systemId, :idSurveyor, :startDate, :endDate, :submitted, :user, :surveyProvider)");
        //     }
        //     // Bind the parameters
        //     $assign2->bindParam(':idSurveyor', $idSurveyor);
        //     // $assi  gn->bindParam(':surveyLeaderAssign', $surveyLeaderAssign);
        //     $assign2->bindParam(':startDate', $startDate);
        //     $assign2->bindParam(':endDate', $endDate);
        //     $assign2->bindParam(':submitted', $timestamp);
        //     $assign2->bindParam(':user', $user);
        //     $assign2->bindParam(':systemId', $systemId);
        //     $assign2->bindParam(':surveyProvider', $surveyProvider);
        //     $assign2->execute();

        //     // Check if the query was successful
        //     if ($assign2->rowCount() > 0) {
        //         // Execute a SELECT query on the database
        //         $select = $conn->prepare("SELECT id FROM flw_survey_udm WHERE system_id = :systemId ");

        //         $select->bindParam(':systemId', $systemId);
        //         $select->execute();
        //         // Fetch the result from the executed SELECT query
        //         $result = $select->fetch(PDO::FETCH_ASSOC);
        //         // Retrieve the ID from the fetched result
        //         $lastInsertedId = $result['id'];
        //     }

        //     // Execute a SELECT query on the database
        //     $checking = $conn->prepare("SELECT id FROM flw_appl_survey WHERE system_id = :systemId ");

        //     $checking->bindParam(':systemId', $systemId);
        //     $checking->execute();

        //     // Return the user information to the client
        //     if ($checking->rowCount() > 0) {
        //         // Execute a SELECT query on the database
        //         $assign = $conn->prepare("UPDATE flw_appl_survey SET udm_id = :udmId, submission_date = :submitted WHERE system_id = :systemId");
        //     } else {
        //         // Execute a SELECT query on the database
        //         $assign = $conn->prepare("INSERT INTO flw_appl_survey (system_id, udm_id, submission_date) VALUES (:systemId, :udmId, :submitted)");
        //     }
        //     // Bind the parameters
        //     $assign->bindParam(':udmId', $lastInsertedId);
        //     $assign->bindParam(':submitted', $timestamp);
        //     $assign->bindParam(':systemId', $systemId);

        //     if ($assign->execute()) {

        //         $flwStatus = 55;
        //         $status = 41;
        //         // NOTE - Update Task Assignment
        //         $taskAssignment->complete($username, $systemId, $status);
        //         $taskAssignment->create($systemId, $flwStatus);

        //         //Record Activity
        //         $text = 'Pengguna ' . $username . ' telah menyelesaikan pembahagian tugasan pengukuran bagi ' . $refNo;
        //         $pages = 'task_mapping';
        //         $changelog->userActivity($text, $pages);

        //         // NOTE - current status = 41
        //         //nextstep for mapping
        //         $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

        //         $details = 'Pembahagian Tugasan Pengukuran Selesai bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        //         // NOTE - Insert Project Changelog
        //         if ($changelog->projectActivity($systemId, $status, $details)) {
        //             // declare telegram notification string
        //             $message = "Pembahagian Tugasan Pengukuran Selesai.\n\n<strong>?? No Rujukan : " . $refNo . " \n?? Tarikh : " . $timeConverted . " \n?? SR Dilantik : " . $surveySurveyor . "</strong>";
        //             // $message = $telegram->getMessageByFlow($flwStatus, refNo: $refNo, date: $timeConverted, item: $team);

        //             // NOTE - send telegram notification for role 28-Ketua Survey
        //             $response = $telegram->sendMessage('group', 'management', $message, 'html');
        //             $response = $telegram->sendMessage('role', 28, $message, 'html');
        //             $response2 = $telegram->sendDocument('group', 'management', $url8, '');
        //             $response2 = $telegram->sendDocument('role', 28, $url8, '');
        //             $response3 = $telegram->sendDocument('group', 'management', $url3, '');
        //             $response3 = $telegram->sendDocument('role', 28, $url3, '');

        //             // Set your API parameters
        //             $whatsappMsg = "Pembahagian Tugasan Pengukuran Selesai. \n\n No Rujukan : " . $refNo . " \n Julat Tarikh Jangkaan Mula - Tamat : " . $dateRange . "";

        //             $getSRContact = $conn->prepare("SELECT phone_no FROM ls_sr_contact WHERE id = :idSurveyor");
        //             $getSRContact->bindParam(':idSurveyor', $idSurveyor);
        //             $getSRContact->execute();

        //             $result = $getSRContact->fetch(PDO::FETCH_ASSOC);

        //             $SRContact = $result['phone_no'];

        //             $applData = array(
        //                 "secret" => $system->App->secret,
        //                 "notification" => array(
        //                     "message" => $whatsappMsg,
        //                     "phones" => [
        //                         $SRContact
        //                     ],
        //                     "attachments" => [
        //                     ]
        //                 )
        //             );

        //             // Convert the array to JSON
        //             $jsonData = json_encode($applData);

        //             // https //:wapi.asiadebut.tech portnumber:8552
        //             //     / api / chat / nophone / message;
        //             // make api request to extercord
        //             $apiRequest = $Curl->request('https://extercord.asiadebut.tech/gateway/internal/notify/' . $systemId, $jsonData);

        //             $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

        //             // Decode the response JSON
        //             $curlResult = json_decode($apiRequest['response']);
        //         }
        //         ;

        //         $chronoId = $chronology->create($username, $systemId, $status, 'notes');

        //         $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        //         $stmt = $conn->prepare($query);
        //         $stmt->bindParam(':systemId', $systemId);
        //         $stmt->bindParam(':details', $details);
        //         $stmt->bindParam(':created', $timestamp);
        //         $stmt->bindParam(':username', $username);
        //         $stmt->bindParam(':chronology_id', $chronoId);
        //         $stmt->execute();

        //         // Finally, return a JSON
        //         echo json_encode([
        //             "success" => "Success",
        //             "status" => 200,
        //             "system_id" => $systemId,
        //         ]);

        //     } else {
        //         http_response_code(500);
        //         $result = array(
        //             "success" => false,
        //             "message" => "failed",
        //             "status" => 500,
        //         );
        //     }
        // }

        // Close the database connection
        $conn = null;
    } else if (isset($POST['review-udm-remark'])) {
        //pelan-udm-remark
        $systemId = $POST['system-id'];
        $confirmValue = $POST['confirm'];

        $timestamp = date('Y-m-d H:i:s', time());
        $planRemark = isset($POST['review-udm-remark']) ? $POST['review-udm-remark'] : '';
        $user = $_SESSION['username'];

        // Update plan_remark in flw_plan_udm
        $query1 = $conn->prepare("UPDATE flw_plan_udm SET plan_remark = :planRemark, plan_udm_assignee = :user, created_timestamp = :timestamp WHERE system_id = :systemId");
        $query1->bindParam(':systemId', $systemId);
        $query1->bindParam(':planRemark', $planRemark);
        $query1->bindParam(':user', $user);
        $query1->bindParam(':timestamp', $timestamp);
        $query1->execute();

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        if ($confirmValue === "1") {
            //plan luluskan

            $flwStatus = 155;
            $status = 57;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);
            // var_dump('masukk');
            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menyemak dan menerima pelan infrastruktur utiliti bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 57
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);
            // var_dump($NextStatus);

            $details = 'Pelan Infrastruktur Utiliti telah Disemak & Diterima bagi ' . $refNo . '. Nota: ' . $planRemark;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, 57, $details)) {
                // declare telegram notification string
                $telegramMsg = "Pelan Infrastruktur Utiliti telah Disemak & Diterima. \n\n?? <strong>No Rujukan:</strong> " . $refNo . "\n?? <strong>Catatan Pelan:</strong> " . $planRemark;
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

        } else if ($confirmValue === "2") {
            //plan tak luluskan

            $flwStatus = 58;
            $status = 57;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menyemak dan menolak pelan infrastruktur utiliti bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 57
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Pelan Infrastruktur Utiliti telah disemak dan ditolak bagi ' . $refNo . '. Nota: ' . $planRemark;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Pelan Infrastruktur Utiliti telah disemak dan ditolak. \n?? <strong>No Rujukan:</strong> " . $refNo . "\n?? <strong>Catatan Pelan:</strong> " . $planRemark;
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

        // Close the database connection
        $conn = null;

    } else if (isset($POST['review-tmp-remark'])) {
        //finance-remark
        $systemId = $POST['system-id'];
        $confirmValue = $POST['confirm'];

        $timestamp = date('Y-m-d H:i:s', time());
        $planRemark = isset($POST['review-tmp-remark']) ? $POST['review-tmp-remark'] : '';
        $user = $_SESSION['username'];

        // Update plan_remark in flw_plan_udm
        $query1 = $conn->prepare("UPDATE flw_plan_tmp SET plan_remark = :planRemark, plan_tmp_assignee = :user, created_timestamp = :timestamp WHERE system_id = :systemId");
        $query1->bindParam(':systemId', $systemId);
        $query1->bindParam(':planRemark', $planRemark);
        $query1->bindParam(':user', $user);
        $query1->bindParam(':timestamp', $timestamp);
        $query1->execute();

        //To get reference number
        $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
        $referenceNo->bindParam(':systemId', $systemId);
        $referenceNo->execute();
        $resultRef = $referenceNo->fetch(PDO::FETCH_ASSOC);
        $refNo = $resultRef['reference_no'];

        if ($confirmValue === "1") {
            //plan luluskan

            $flwStatus = 56;
            $status = 59;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);
            // var_dump('masukk');
            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menyemak dan menerima pelan kawalan trafik bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 59
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);
            // var_dump($NextStatus);

            $details = 'Pelan Kawalan Trafik telah disemak dan diterima bagi ' . $refNo . '. Nota: ' . $planRemark;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, 59, $details)) {
                // declare telegram notification string
                $telegramMsg = "Pelan Kawalan Trafik telah disemak dan diterima. \n\n?? <strong>No Rujukan:</strong> " . $refNo . "\n?? <strong>Catatan Pelan:</strong> " . $planRemark;
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

        } else if ($confirmValue === "2") {
            //plan tak luluskan

            $flwStatus = 60;
            $status = 59;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status);
            $taskAssignment->create($systemId, $flwStatus);

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menyemak dan menolak pelan kawalan trafik bagi ' . $refNo;
            $pages = 'task_mapping';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 59
            //nextstep for mapping
            $NextStatus = $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);

            $details = 'Pelan Kawalan Trafik telah disemak dan ditolak bagi ' . $refNo . '. Nota: ' . $planRemark;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                $telegramMsg = "Pelan Kawalan Trafik telah disemak dan ditolak. \n?? <strong>No Rujukan:</strong> " . $refNo . "\n?? <strong>Catatan Pelan:</strong> " . $planRemark;
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

        // Close the database connection
        $conn = null;

    } else if (isset($POST['report-udm-edit'])) {
        $systemId = $POST['system-id'];
        // Call the generateID function to fetch the ID value
        $idRepeat = SurveyApi::repeaterID();

        // Get the data from flw_survey_reports_udm to update
        $queryS = $conn->prepare("SELECT id, id_repeat, survey_date, survey_time_start, survey_time_end, current_progress, daily_progress, pegging_distance, detection_distance, progress_pending, pegging_pending, detection_pending FROM flw_survey_reports_udm WHERE system_id = :systemId ORDER BY id DESC LIMIT 1");
        $queryS->bindParam(':systemId', $systemId);
        $queryS->execute();
        $rowS = $queryS->fetch(PDO::FETCH_ASSOC);

        // Get the user's identification from the request parameters
        $surveyTeam = isset($POST['survey-team']) ? $POST['survey-team'] : '';
        $refNo = isset($POST['noRef']) ? $POST['noRef'] : '';
        $length = isset($POST['application-length']) ? $POST['application-length'] : '';
        $application_length = floatval($length);
        $datePicker = $rowS['survey_date'];
        $startTime = $rowS['survey_time_start'];
        $endTime = $rowS['survey_time_end'];
        $startCoord = isset($POST['start-coord']) ? $POST['start-coord'] : '';
        $endCoord = isset($POST['end-coord']) ? $POST['end-coord'] : '';
        list($slat, $slong) = explode(",", $startCoord);
        $slat = floatval($slat);
        $slong = floatval($slong);

        list($elat, $elong) = explode(",", $endCoord);
        $elat = floatval($elat);
        $elong = floatval($elong);
        // Get the current progress with appropriate checks and default value
        $currentProgres = isset($POST['current-progress']) && is_numeric($POST['current-progress']) ? $POST['current-progress'] : 0;
        $dailyProgress = $rowS['daily_progress'];
        $currentProgress = $rowS['current_progress'];
        // Get the double precision values with appropriate checks and default values
        $peggingDistance = $rowS['pegging_distance'];
        $detectionDistance = $rowS['detection_distance'];
        $progressPending = $rowS['progress_pending'];
        $peggingPending = $rowS['pegging_pending'];
        $detectionPending = $rowS['detection_pending'];


        // $systemId = $POST['system-id'];
        $timestamp = date('Y-m-d H:i:s', time());
        $user = $_SESSION['username'];
        $name = SurveyApi::getSurveyFirstname($user);
        $jarakPengukuran = isset($POST['jarak_pengukuran']) ? $POST['jarak_pengukuran'] : 0;

        $timeConverted = Utilities::convertDateToMalay($timestamp);
        $surveyApi = new SurveyApi($username);
        $surveyLength = $surveyApi->getSurveyLength($systemId);

        $surveyWork = isset($POST['survey-work']) ? $POST['survey-work'] : [];
        $equipment = isset($POST['equipment']) ? $POST['equipment'] : [];

        // Ensure $surveyWork and $equipment are arrays
        $surveyWork = is_array($surveyWork) ? $surveyWork : [$surveyWork];
        $equipment = is_array($equipment) ? $equipment : [$equipment];

        $surveyWorkArray = '{' . implode(', ', $surveyWork) . '}';
        $equipmentArray = '{' . implode(', ', $equipment) . '}';

        $workDoneList = $POST['workDone-list'];
        $categoryReport = 'edit';

        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        foreach ($workDoneList as $workDone) {
            // Get the all data in form repeater
            $imageUrl = $workDone['image-url'];
            $category = $workDone['survey-work-select'];
            $lat = $workDone['latitude'];
            $long = $workDone['longitude'];
            $longs = floatval($long);
            $lats = floatval($lat);

            if($category == 1) {
                $workDescName = $workDone['survey-work-desc'];

                // Get the work description
                $query3 = $conn->prepare("SELECT name FROM ls_survey_work_desc WHERE id = :workDescId");
                $query3->bindParam(':workDescId', $workDescName);
                $query3->execute();
                $row3 = $query3->fetch(PDO::FETCH_ASSOC);
                $work_desc = $row3['name'];

                $description = $work_desc;
            } else if($category == 2) {
                $description = 'Gambaran Chainage '.$workDone['chainage-value'];
            } else {
                $description = $workDone['notes'];
            }

            $id = $idRepeat;

            if(!empty($imageUrl)) {
                $query = $conn->prepare("INSERT INTO flw_survey_report_images_udm (system_id, url, category, description, created_at, geom, id_repeat) VALUES (:systemId, :imageUrl, :category, :description, :submitted, ST_SetSRID(ST_MakePoint(:longs, :lats), 4326), :id)");

                // Bind the parameters
                $query->bindParam(':systemId', $systemId);
                $query->bindParam(':imageUrl', $imageUrl);
                $query->bindParam(':category', $category);
                $query->bindParam(':description', $description);
                $query->bindParam(':submitted', $timestamp);
                $query->bindParam(':longs', $longs);
                $query->bindParam(':lats', $lats);
                $query->bindParam(':id', $id);
                $query->execute();

                $decodeUrl = base64_decode($imageUrl);
            }

            // NOTE - send telegram notification for role 28-Ketua Survey & Group Management
            $response = $telegram->sendPhoto('role', 28, $decodeUrl, "", $description);
            $response2 = $telegram->sendPhoto('group', 'management', $decodeUrl, "", $description);
            $response3 = $telegram->sendPhoto('group', 'mapping', $decodeUrl, "", $description);
        }

        // Execute a SELECT query on the database
        $checking = $conn->prepare("SELECT id FROM flw_survey_reports_udm WHERE system_id = :systemId ");

        // Bind the parameters
        $checking->bindParam(':systemId', $systemId);
        $checking->execute();

        if ($peggingPending === null || $peggingPending == 0 || $peggingDistance === $application_length) {
            $currentPegging = $application_length; 
        } else {
            $currentPegging = $application_length - $peggingPending;
        }

        if ($detectionPending === null || $detectionPending == 0 || $detectionDistance === $application_length) {
            $currentDetection = $application_length;
        } else {
            $currentDetection = $application_length - $detectionPending;
        }
            
        // Get the team members for the selected group
        $query = $conn->prepare("SELECT * FROM flw_survey_team WHERE id = :surveyTeam");
        $query->bindParam(':surveyTeam', $surveyTeam);
        $query->execute();

        $row = $query->fetch(PDO::FETCH_ASSOC);
        $members = $row['team_members'];

        // Build the INSERT query with the modified current progress value
        $queryU = "INSERT INTO flw_survey_reports_udm (system_id, reference_no, survey_team, application_length, survey_date, survey_time_start, survey_time_end, current_progress, daily_progress, pegging_distance, detection_distance, progress_pending, pegging_pending, detection_pending, created_timestamp, created_id, id_repeat, survey_work, equipment, latitude_start, longitude_start, latitude_end, longitude_end, category, team_members)
        VALUES (:systemId, :noRef, :surveyTeam, :application_length, :datePicker, :startTime, :endTime, :currentProgress, :dailyProgress, :peggingDistance, :detectionDistance, :progressPending, :peggingPending, :detectionPending, :submitted, :user, :idRepeat, :surveyWorkArray, :equipmentArray, :slat, :slong, :elat, :elong, :categoryReport, :members)";

        $stmtU = $conn->prepare($queryU);

        // Bind the parameters
        $stmtU->bindParam(':systemId', $systemId);
        $stmtU->bindParam(':noRef', $refNo); 
        $stmtU->bindParam(':surveyTeam', $surveyTeam);
        $stmtU->bindParam(':application_length', $application_length);
        $stmtU->bindParam(':datePicker', $datePicker);
        $stmtU->bindParam(':startTime', $startTime);
        $stmtU->bindParam(':endTime', $endTime);
        $stmtU->bindParam(':currentProgress', $currentProgress);
        $stmtU->bindParam(':dailyProgress', $dailyProgress);
        $stmtU->bindParam(':peggingDistance', $peggingDistance);
        $stmtU->bindParam(':detectionDistance', $detectionDistance);
        $stmtU->bindParam(':progressPending', $progressPending);
        $stmtU->bindParam(':peggingPending', $peggingPending);
        $stmtU->bindParam(':detectionPending', $detectionPending);
        $stmtU->bindParam(':submitted', $timestamp);
        $stmtU->bindParam(':user', $user);
        $stmtU->bindParam(':idRepeat', $idRepeat);
        $stmtU->bindParam(':surveyWorkArray', $surveyWorkArray);
        $stmtU->bindParam(':equipmentArray', $equipmentArray);
        $stmtU->bindParam(':slat', $slat);
        $stmtU->bindParam(':slong', $slong);
        $stmtU->bindParam(':elat', $elat);
        $stmtU->bindParam(':elong', $elong);
        $stmtU->bindParam(':categoryReport', $categoryReport);
        $stmtU->bindParam(':members', $members);
        $stmtU->execute();

        // Select the record
        $query3 = "SELECT survey_team FROM flw_survey_team WHERE id = :surveyTeam";
        $stmt3 = $conn->prepare($query3);
        $stmt3->bindParam(':surveyTeam', $surveyTeam);
        $stmt3->execute();

        $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $teamName = $row3['survey_team'];

        $flwStatus = 47;

        //Record Activity
        $text = 'Pengguna ' . $username . ' telah mengemaskini Penyediaan Laporan Kerja Lapangan bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        $details = 'Kemaskini Laporan Kerja Lapangan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
            // declare telegram notification string
            $telegramMsg = "<strong>Laporan Kerja Lapangan telah dikemaskini </strong> \n\nKumpulan : " . $teamName . " \nNo Rujukan : " . $refNo;
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
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

        // Finally, return a JSON
        echo json_encode(
            [
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
                "noRef" => $refNo,
                "created_id" => $user,
            ]
        );

        // Close the database connection
        $conn = null;

    } else if(isset($POST['amend-survey-report-udm'])) {
        $systemId = $POST['system-id'];
        $refNo = Utilities::getRefNo($systemId);
        $timestamp = date('Y-m-d H:i:s', time());
        $timeConverted = Utilities::convertDateToMalay($timestamp);
        $username = $_SESSION['username'];
        $status = 48;
        $flwStatus = 47;

        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        // NOTE - assign task to Ketua Survey for amendment reports
        $taskAssignment->create($systemId, $flwStatus);
    
        //Record Activity
        $text = 'Pengguna ' . $username . ' telah membuat Pindaan Laporan Kerja Lapangan bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        //NOTE - go next flow for lead survey
        $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);
    
        $details = 'Pindaan Laporan Kerja Lapangan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsg = "Laporan Kerja Lapangan telah dipinda. Sila kemaskini mengikut catatan yang telah dimasukkan. \n\n<strong>?? No Rujukan : " . $refNo . " \n ?? Tarikh Pindaan : " . $timeConverted . "\n ?? Catatan : ". $POST['notes'] ."</strong>";
            // NOTE - send telegram notification for team survey 
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('role', 28, $telegramMsg, 'html');

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
        echo json_encode(
            [
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
            ]
        );
    
        // Close the database connection
        $conn = null;
    
    } else if(isset($POST['amend-survey-report-asb'])) {
        $systemId = $POST['system-id'];
        $refNo = Utilities::getRefNo($systemId);
        $timestamp = date('Y-m-d H:i:s', time());
        $username = $_SESSION['username'];
        $status = 48;
        $flwStatus = 47;

        // NOTE - Update Task Assignment
        $taskAssignment->complete($username, $systemId, $status);
        // NOTE - assign task to Ketua Survey for amendment reports
        $taskAssignment->create($systemId, $flwStatus);
    
        //Record Activity
        $text = 'Pengguna ' . $username . ' telah membuat Pindaan Laporan Kerja Lapangan bagi ' . $refNo;
        $pages = 'task_mapping';
        $changelog->userActivity($text, $pages);

        //NOTE - go next flow for lead survey
        $flow->goToNextFlow($systemId, "mapping", "BF", Steps: $flwStatus);
    
        $details = 'Pindaan Laporan Kerja Lapangan bagi ' . $refNo . '. Nota: ' . $POST['notes'];
        // NOTE - Insert Project Changelog
        if ($changelog->projectActivity($systemId, $status, $details)) {
            // declare telegram notification string
            $telegramMsg = "<strong>Laporan Kerja Lapangan telah dipinda </strong> \n\nNo Rujukan : " . $refNo;
            // NOTE - send telegram notification for team survey
            $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
            $telegramResponse = $telegram->sendMessage('group', 'mapping', $telegramMsg, 'html');
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
        echo json_encode(
            [
                "success" => "Success",
                "status" => 200,
                "system_id" => $systemId,
            ]
        );
    
        // Close the database connection
        $conn = null;
    
    } else {
        echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Check if the row ID is provided
    if (isset($_GET['rowId'])) {
        $id = $_GET['rowId'];

        // Construct the DELETE query
        $query = $conn->prepare("DELETE FROM flw_survey_report_images_udm WHERE id = :id");
        // Bind the parameters
        $query->bindParam(':id', $id);
        $query->execute();

        // Construct the JSON response
        $response = array('success' => true);

        // Send the JSON response to the client
        header('Content-Type: application/json');
        echo json_encode($response);

        // Close the database connection
        $conn = null;
    }

} else {
}

