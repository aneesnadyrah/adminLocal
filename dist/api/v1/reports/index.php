<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// import connection and api setup
require_once "config/system.php";
require_once "config/DBFactory.php";
include_once "api/header.php";
require_once "api/functions.php";

// Connect to the database using PDO
$db               = new DBConnectionFactory();
$telegram         = new Telegram();
$system           = new System;
$flow             = new FlowStatuses($username);
$changelog        = new Changelog($username);
$Curl             = new Curl();
$traffic          = new Traffic();
$taskAssignment   = new TaskAssignments();
$chronology       = new Chronology();
$whatsapp = new Whatsapp();

$ec_url = $system->App->ec_url;

if (isset($_GET["id"])) {
    $systemId = $_GET["id"];
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['ref'])) {
        $reportId = $_GET['ref'];

        // Connect to the database using PDO
        $conn = $db->createConnection();

        // Prepare the SELECT query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $referenceNo = $stmt->fetchColumn();

        // Process the retrieved value
        if ($referenceNo !== false) {
            // Value exists, handle it accordingly
            if (isset($system->App->title)) {
                if ($system->App->title == 'UCIDOS' || $system->App->title == 'KITER' || $system->App->title == 'KUDRAT') {
                    // Generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } else {
                    echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                    exit;
                }
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }

            // Retrieve the geospatial data
            $stmt = $conn->prepare("SELECT *, ST_AsGeoJSON(geom) as geom FROM  geom_site_visit WHERE site_status = false AND system_id = :systemId AND report_id = :codedReport");
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->execute();

            // Create an array to hold the data
            $geojson = array(
                'type' => 'FeatureCollection',
                'features' => array()
            );

            // Add the data to the array
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $geometry = $row['geom'];
                if ($geometry !== null) {
                    $feature = array(
                        'type' => 'Feature',
                        'geometry' => json_decode($row['geom']),
                        'properties' => array(
                            'id' => $row['id'],
                            'systemId' => $row['system_id'],
                            'reportId' => $row['report_id'],
                            'description' => $row['description'],
                            'image' => $row['url'],
                            'leafletId' => $row['geom_id']
                        )
                    );
                    array_push($geojson['features'], $feature);
                }
            }

            $conn = null;

            // Convert the array to a GeoJSON string
            $geojson = json_encode($geojson);

            echo $geojson;
        } else {
            // No value found
            echo "No reference number found.";
        }

    } else if (isset($_GET['report-id'])) {

        // Connect to the database using PDO
        $conn = $db->createConnection();

        // Get the Report List from the request parameters
        $reportID = isset($_GET['report-id']) ? $_GET['report-id'] : '';

        // Prepare the SELECT query
        if (!isset($_GET['report-id'])) {
            // Query without report ID
            $query = "SELECT
            id AS \"ID\",
            report_trigger_id AS \"TriggerID\",
            report_submit AS  \"ReportStat\",
            submit_date AS  \"SubmitDate\",
            report_no AS \"ReportNo\",
            system_id AS \"SysID\",
            authority_id AS \"AuthID\",
            authority_name AS \"Authority\",
            authority_logo AS \"AuthLogo\",
            authority_district AS \"AuthDistrict\",
            calendar_sv_date AS \"SVDate\",
            entry_appl_length AS \"Length\",
            report_type AS \"ReportType\"
            FROM view_report_sitevisit
            WHERE system_id = :systemId
            ORDER BY id DESC";
        } else {
            // Query with report ID
            $query = "SELECT
            id AS \"ID\",
            report_trigger_id AS \"TriggerID\",
            report_submit AS  \"ReportStat\",
            submit_date AS  \"SubmitDate\",
            reference_no AS \"RefNo\",
            report_no AS \"ReportNo\",
            system_id AS \"SysID\",
            authority_id AS \"AuthID\",
            authority_name AS \"Authority\",
            authority_logo AS \"AuthLogo\",
            authority_district AS \"AuthDistrict\",
            calendar_sv_date AS \"SVDate\",
            entry_appl_length AS \"Length\",
            report_type AS \"ReportType\"
            FROM view_report_sitevisit
            WHERE system_id = :systemId AND report_no = :reportID
            ORDER BY id DESC";
        }

        // Prepare the query
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);
        if (!empty($reportID)) {
            $stmt->bindParam(':reportID', $reportID);
        }

        // Execute the query
        $stmt->execute();

        // Fetch the rows from the query result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process the retrieved values
        foreach ($data as &$row) {
            // Format the StatusID field to have leading zeroes if it's less than 100
            // if ($row['StatusID'] < 10) {
            //     $row['StatusID'] = '00' . $row['StatusID'];
            // } elseif ($row['StatusID'] < 100) {
            //     $row['StatusID'] = '0' . $row['StatusID'];
            // }

            // Format the Logo field to have leading zeroes if it's less than 100
            if ($row['AuthLogo'] < 10) {
                $row['AuthLogo'] = '0' . $row['AuthLogo'];
            }
            if ($row['AuthDistrict'] < 10) {
                $row['AuthDistrict'] = '0' . $row['AuthDistrict'];
            }
        }

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $conn = null;

        // Output the JSON response
        echo json_encode(
            array(
                "message" => "success",
                "status" => 200,
                "data" => $data
            )
        );
    } else if (isset($_GET['data'])) {
        // Connect to the database using PDO
        $conn = $db->createConnection();

        if ($_GET['data'] == 'task-011') {
            // Execute a SELECT query on the database
            // Get the user list
            $query = "SELECT id, system_id, report_no, calendar_sv_date as sv_date, authority_name as name, authority_logo as logo, approval_date FROM view_report_sitevisit
            WHERE view_report_sitevisit.system_id = :sid AND view_report_sitevisit.approval_date IS NULL ORDER BY id ASC";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':sid', $systemId, PDO::PARAM_STR);
            $stmt->execute();
            $siteVisitList = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $conn = null;

            // Convert the result to a JSON string
            $json = json_encode($siteVisitList);

            // Return the JSON string to the client
            echo $json;
        }
    } else if (isset($_GET['site-visit'])) {
        // Connect to the database using PDO
        $conn = $db->createConnection();

        if ($_GET['site-visit'] == "time") {
            // Get the current Unix timestamp in milliseconds
            $timestamp = round(microtime(true) * 1000);

            // Return the timestamp as a JSON response
            echo json_encode(['timestamp' => $timestamp]);
        } else if  ($_GET['site-visit'] == "svDate") {
            // Prepare the query
            $query = "SELECT sv_date_done FROM flw_appl_reports WHERE system_id = :systemId AND report_no = :reportNo AND authority_id = :authorityId";
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':reportNo', $_GET['rid']);
            $stmt->bindParam(':authorityId', $_GET['auth']);

            // Execute the query
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                echo "Error executing query: " . $errorInfo[2];
            }

            $svDate = $stmt->fetchColumn();

            // Return the timestamp as a JSON response
            echo json_encode(['svDate' => $svDate]);
        }

        $conn = null;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents("php://input");
    $POST = json_decode($data, true);

    if (isset($POST['report-type'])) {

        // Connect to the database using PDO
        $conn = $db->createConnection();

        $type = $POST['report-type'];
        
        $query = "SELECT COUNT(report_type) AS log FROM ctrl_site_report WHERE system_id = :systemId AND report_type = :type";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':type', $type, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $log = $row['log'] + 1;

        if ($log < 10) {
            $logNo = "0" . $log;
        } else {
            $logNo = $log;
        }

        // FIXME: Unknown Function To Get Something Does Not Exist
        $decode = Utilities::extractSystemId($systemId, 'running');

        $pattern = "/\((\d+)\)/"; // regular expression to match the number inside parentheses
        preg_match($pattern, $decode, $matches); // find the first match

        $number = "";
        if (count($matches) > 1) {
            $number = $matches[1]; // extract the number from the second element of the matches array
            if ($number < 100) {
                $number = "0" . $number; // add two leading zeros if the number is less than 10
            }
        }

        $runNumber = "KUK/LT/$type/" . date('Y') . "/$number/$logNo";

        $query = "SELECT authority FROM geom_gis_trace WHERE system_id = :systemId AND authority IS NOT NULL GROUP BY authority;";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $authID = $row['authority'];

        // Close the database connection
        $conn = null;

        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "success",
                "status" => 200,
                "data" => array(
                    "reportNumber" => $runNumber,
                    "authID" => $authID
                ),
            )
        );
    
    } else if (isset($POST['locked'])) {
        $reportId = $POST['reportId'];
        $authId = $POST['authId'];
        $locked = $POST['locked'];

        // Connect to the database using PDO
        $conn = $db->createConnection();

        
            // Declare the query
            $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

            // Prepare the query
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);

            // Execute the query
            $stmt->execute();

            // Fetch the single value from the query result
            $referenceNo = $stmt->fetchColumn();

            // Process the retrieved value
            if ($referenceNo !== false) {
                // Value exists, handle it accordingly
                if (isset($system->App->title)) {
                    if ($system->App->title == 'UCIDOS' || $system->App->title == 'KITER' || $system->App->title == 'KUDRAT') {
                        // Generate codedReport
                        $explodedRef = explode("/", $referenceNo);
                        $exploded = explode("/", $reportId);
                        $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                        $codedReport = base64_encode($shortRef);
                    } else {
                        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                        exit;
                    }
                } else {
                    echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
                    exit;
                }

                // Update geom_site_visit table
                $stmt = $conn->prepare("UPDATE geom_site_visit SET site_status = true WHERE report_id = :codedReport AND system_id = :systemId");
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->execute();

                // Delete rows from geom_site_visit table
                $stmt = $conn->prepare("SELECT id FROM geom_site_visit WHERE description IS NULL AND report_id = :codedReport AND system_id = :systemId ORDER BY id DESC");
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->execute();

                $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $rowDeleted = 0;

                if (!empty($ids)) {
                    $output = '(' . implode(',', $ids) . ')';
                    $stmt = $conn->prepare("DELETE FROM geom_site_visit WHERE id IN $output");
                    $stmt->execute();
                    $rowDeleted = $stmt->rowCount();
                }

                // change status to from 13 to 14
                // get current appl status
                // ANCHOR - temporary commented for video
                // $projectStatus = Utilities::getStatus($systemId, 'operation');
                // if ($projectStatus == 13) {
                //     $projectStatusNew = 14;
                //     $statusUpdateResult = updateProjectStatus($conn, $systemId, $projectStatus, $projectStatusNew);

                //     // TODO: Update changelog table
                //     // set initial status of changeLogUpdateResult
                //     $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

                //     // Update changelog if status update successful
                //     if ($statusUpdateResult['result'] == true) {
                //         $new_timestamp = date('Y-m-d H:i:s', time()); // current timestamp
                //         $changeLogUpdateResult = insertSysRecordChangelog($conn, $systemId, 'Penyediaan Laporan Lawatan Tapak', $statusUpdateResult['new_status']);
                //     }
                // }

                // Close the database connection
                $conn = null;

                echo json_encode(
                    array(
                        "message" => "success",
                        "status" => 200,
                            "reportId" => $reportId,
                            // originally codedReport by en hassan change to reportId since reportId store codedReport value
                            "systemId" => $systemId,
                            "authId" => $authId,
                            "rowDeleted" => $rowDeleted
                    )
                );
            } else {
                // No value found
                echo "No reference number found.";
            }
    } else if (isset($POST['item'])) {
        if ($POST['item'] == 'report-review') {

            $systemId = $POST['system-id'];
            $reportData = $POST['report'];
            $reportCtrl = $POST['reportCtrl'];
            $reportNo = ""; // declare reportNo with empty string
            $submissionCode = $systemId;
            $refNo = Utilities::getRefNo($systemId);
            $timestamp = date('Y-m-d H:i:s', time());
            $pindaanStatus = true;
            if (isset($POST['proceed-wl'])) {
                $pindaanStatus = false;
                $notes = "Pengesahan Laporan Lawatan Tapak. Teruskan dengan Penyediaan Sebut Harga & Permohonan Izin Lalu.\n\nNota: ".$POST['notes'];
            } else {
                $notes = "Pengesahan Laporan Lawatan Tapak. Teruskan dengan Pindaan Pelan Cadangan Teknikal.\n\nNota: ".$POST['notes'];
            }

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // TODO: update approval date, approval id in flw_appl_reports and get ammend data
            $amendData = [];
            $amendItem = [];
            
            $amendDetails = [["steps"=>1,"items"=>["provider"=>null,"title"=>null,"info"=>null,"notes"=>"","attachment"=>null]],["steps"=>2,"items"=>["applicant"=>null,"officer"=>null,"notes"=>"","attachment"=>null]],["steps"=>3,"items"=>["district"=>null,"roads"=>null,"notes"=>"","attachment-1"=>null,"attachment-2"=>null]]];
            foreach ($reportData as &$report) {
                $reportNo = $report['report_no'];
                
                $query = "UPDATE flw_appl_reports SET approval_date = :timestamp, approver = :username WHERE id = :reportId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':timestamp', $timestamp);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':reportId', $report['id']);
                $stmt->execute();

                // get geom entry for each report
                $codedReport = generateCodedReport($systemId, $report['report_no']);
                $query = "SELECT * FROM geom_site_visit WHERE report_id = :codedReport AND system_id = :systemId AND authority_id = :authorityId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':authorityId', $report['authority_id']);
                $stmt->execute();
                $geomData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $report['geom']=$geomData;

                // get amend data
                $currentId = intval($report['id']);
                $query = "SELECT active_amend_notes FROM flw_appl_reports WHERE id = :reportId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':reportId', $currentId);
                $stmt->execute();
                $amendData[$report['authority_name']] = $stmt->fetchColumn();

                // SP = surat permohonan, RL = surat perakuan, TP document teknikal, LP gambar lokasi
                if ($amendData[$report['authority_name']] !== null) {
                    $amendData[$report['authority_name']] = json_decode($amendData[$report['authority_name']], true);
                    if ($amendData[$report['authority_name']]['amendments'][1]['items']['provider'] || $amendData[$report['authority_name']]['amendments'][1]['items']['title'] || $amendData[$report['authority_name']]['amendments'][1]['items']['info'] || $amendData[$report['authority_name']]['amendments'][1]['items']['attachment']) {
                        $amendDetails[0]['items']['notes'] = $amendDetails[0]['items']['notes'].$report['authority_name'].' : '.$amendData[$report['authority_name']]['amendments'][1]['items']['notes'];
                        if ($amendData[$report['authority_name']]['amendments'][1]['items']['provider']) {
                            $amendDetails[0]['items']['provider'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][1]['items']['title']) {
                            $amendDetails[0]['items']['title'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][1]['items']['info']) {
                            $amendDetails[0]['items']['info'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][1]['items']['attachment']) {
                            $amendDetails[0]['items']['attachment'] = 'on';
                        }
                    }
                    if ($amendData[$report['authority_name']]['amendments'][2]['items']['applicant'] || $amendData[$report['authority_name']]['amendments'][2]['items']['officer'] || $amendData[$report['authority_name']]['amendments'][2]['items']['attachment']) {
                        $amendDetails[1]['items']['notes'] = $amendDetails[1]['items']['notes'].$report['authority_name'].' : '.$amendData[$report['authority_name']]['amendments'][2]['items']['notes'];
                        if ($amendData[$report['authority_name']]['amendments'][2]['items']['applicant']) {
                            $amendDetails[1]['items']['applicant'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][2]['items']['officer']) {
                            $amendDetails[1]['items']['officer'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][2]['items']['attachment']) {
                            $amendDetails[1]['items']['attachment'] = 'on';
                        }
                    }
                    if ($amendData[$report['authority_name']]['amendments'][3]['items']['district'] || $amendData[$report['authority_name']]['amendments'][3]['items']['road'] || $amendData[$report['authority_name']]['amendments'][3]['items']['attachment_1'] || $amendData[$report['authority_name']]['amendments'][3]['items']['attachment_2']) { 
                        $amendDetails[2]['items']['notes'] = $amendDetails[2]['items']['notes'].$report['authority_name'].' : '.$amendData[$report['authority_name']]['amendments'][3]['items']['notes'];
                        if ($amendData[$report['authority_name']]['amendments'][3]['items']['district']) {
                            $amendDetails[2]['items']['district'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][3]['items']['road']) {
                            $amendDetails[2]['items']['roads'] = 'on';
                        }
                        if ($amendData[$report['authority_name']]['amendments'][3]['items']['attachment_1']) {
                            $amendDetails[2]['items']['attachment-1'] = 'on';
                            $amendItem[] = "TP";
                        }
                        if ($amendData[$report['authority_name']]['amendments'][3]['items']['attachment_2']) {
                            $amendDetails[2]['items']['attachment-2'] = 'on';
                            $amendItem[] = "LP";
                        }
                    }
                }
            }
            $amendItem = array_unique($amendItem);

            $svReportUrl = base64_encode($system->App->url.'/projects/view/sitevisit/'.$submissionCode.'?r='.$reportNo.($pindaanStatus?'&hl=true':''));

            // save to flw_appl_attachment table
            $fileName = 'Laporan Lawatan Tapak';
            $attachDate = date('Y-m-d');
            $attachId = 7; // REVIEW - currently hardcoded 7 == laporan lawatan tapak
            $mimeType = 'text/html';
            $fileSize = 0;
            $authorityId = null;

            $query = "INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date, user_added, created_date, attachment_type, mime_type, size, authority) VALUES
            (:systemId, :attachDetails, :encodeUrl, :attachDate, :user, :created, :attachId, :mimeType, :fileSize, :authorityId)";
            $stmt = $conn->prepare($query);

            $stmt->bindParam(':systemId', $systemId); // Assuming $systemId is defined somewhere
            $stmt->bindParam(':attachDetails', $fileName);
            $stmt->bindParam(':encodeUrl', $svReportUrl);
            $stmt->bindParam(':attachDate', $attachDate);
            $stmt->bindParam(':user', $username);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':attachId', $attachId);
            $stmt->bindParam(':mimeType', $mimeType);
            $stmt->bindParam(':fileSize', $fileSize);
            $stmt->bindParam(':authorityId', $authorityId);

            $result = $stmt->execute();

            // setup api request payload data
            $payload = [];
            $payload['secret'] = $system->App->secret;
            // TODO:
            $payload['submissions'] = [
                'site_visit'=>[
                    'url'=>$svReportUrl,
                    'mime_type'=>'text/html',
                    'username'=>$username,
                    'stage'=>1,
                    'is_amendments'=>$pindaanStatus,
                    'notes'=>$notes,
                    'amendments'=>$amendDetails
                    ]
                ];
            if ($pindaanStatus) {
                $payload['submissions']['site_visit']['items'] = $amendItem;
            } else {
                $payload['submissions']['site_visit']['items'] = $amendItem;
            }

            $requestUrl = $ec_url.'/gateway/internal/report/sitevisit/'.$submissionCode;
            
            // make api request to extercord
            $apiRequest = $Curl->request($requestUrl, json_encode($payload));

            // Decode the response JSON
            $trafficReturn = $traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);
            $curlResult = json_decode($apiRequest['response']);

            if ($curlResult->message === "success") {
                // Change status
                $status = 15;
                if (!$pindaanStatus) {
                    // change ctrl_site_report complete status
                    $completeStatus = true;
                    $query = "UPDATE ctrl_site_report SET completed = :completed WHERE id = :ctrlId";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':completed', $completeStatus);
                    $stmt->bindParam(':ctrlId', $reportCtrl);
                    $stmt->execute();

                    $flwStatus = 27;
                    $flwStatusGis = 24;
                    $flwStatusPKD = 30;
                    
                    $taskAssignment->complete($username, $systemId, $status);
                    $taskAssignment->create($systemId, $flwStatus);
                    $taskAssignment->create($systemId, $flwStatusGis);
    
                    //Record Activity
                    $text = 'Pengguna ' . $username . ' telah Mengesahkan Laporan Lawatan Tapak bagi permohonan ' . $refNo . ' .';
                    $pages = 'task_operation';
                    $changelog->userActivity($text, $pages);
                    
                    // NOTE - next step for finance for upload quotation
                    $NextStatusAcc = $flow->goToNextFlow($systemId, "finance", "BF", Steps: $flwStatus, allRole: true);
                    // NOTE - next step for gis for upload PIL
                    $NextStatusGIS = $flow->goToNextFlow($systemId, "geospatial", "BF", Steps: $flwStatusGis, allRole: true);
                    // NOTE - next step for PKD for upload KWC,RP
                    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatusPKD);
                    
                    // create general task for status 30
                    $taskAssignment->create($systemId, $NextStatus);
                    
                    $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");

                    $dep = 'operation';
                    $auth->bindParam(':systemId', $systemId);
                    $auth->bindParam(':department', $dep);

                    $auth->execute();

                    $authIds = $auth->fetchAll();

                    foreach($authIds as $authId){
                        $taskAssignment->create($systemId, $NextStatus, $authId->authority);

                        $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authId->authority, $status);
                    }
    
                    $details = 'Laporan Lawatan Tapak bagi permohonan ' . $refNo . ' telah diluluskan. Nota: ' . $POST['notes'];
                    // NOTE - Insert Project Changelog
                    if ($changelog->projectActivity($systemId, $status, $details)) {
        
                        // declare telegram notification string
                        $telegramMsgFinance = "Laporan Lawatan Tapak telah diluluskan untuk proses Permohonan Izin Lalu. Sila muat naik Sebut Harga. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
        
                        // NOTE - send telegram notification for team account
                        $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsgFinance, 'html');

                        // declare telegram notification string
                        $telegramMsgGeospation = "Laporan Lawatan Tapak telah diluluskan untuk proses Permohonan Izin Lalu. Sila sediakan Pelan Izin Lalu. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
        
                        // NOTE - send telegram notification for team GIS
                        $telegramResponse = $telegram->sendMessage('flow', $flwStatusGis, $telegramMsgGeospation, 'html');
        
                        // declare telegram notification string
                        $telegramMsgOperation = "Laporan Lawatan Tapak telah diluluskan untuk proses Permohonan Izin Lalu. Sila sediakan Ringkasan Projek dan Kiraan Wang Cagaran. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
        
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
                } else {
                    $flwStatus = 16;
                    
                    $taskAssignment->complete($username, $systemId, $status);
    
                    //Record Activity
                    $text = 'Pengguna ' . $username . ' telah Mengesahkan Laporan Lawatan Tapak bagi permohonan ' . $refNo . ' .';
                    $pages = 'task_operation';
                    $changelog->userActivity($text, $pages);
        
                    // NOTE - current status = 15
                    $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus);
    
                    $details = 'Laporan Lawatan Tapak bagi permohonan ' . $refNo . ' telah diluluskan. Nota: ' . $POST['notes'];
                    // NOTE - Insert Project Changelog
                    if ($changelog->projectActivity($systemId, $status, $details)) {
        
                        // declare telegram notification string
                        // $telegramMsg = "Laporan Lawatan Tapak telah diluluskan untuk proses Pindaan Cadangan Teknikal. Sila tunggu notifikasi selanjutnya untuk tindakan semakan Pindaan Cadangan Teknikal. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
                        $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
        
                        // NOTE - send telegram notification for team account
                        $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                        $telegramResponse = $telegram->sendMessage('flow', $NextStatus, $telegramMsg, 'html');
                    }

                    $auth = $conn->prepare("SELECT authority FROM ctrl_statuses  WHERE system_id = :systemId AND department = :department AND authority <> 0");
                    $dep = 'operation';
                    $auth->bindParam(':systemId', $systemId);
                    $auth->bindParam(':department', $dep);

                    $auth->execute();

                    $authIds = $auth->fetchAll();

                    foreach ($authIds as $authId) {
                        $whatsappResponse = $whatsapp->sendMessageAuthority($systemId, $authId->authority, $status);
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
                // Finally, return a JSON
                http_response_code(200);
                echo json_encode(['message'=> 'Pengesahan Laporan Berjaya Dilakukan', 'status'=> '200']);
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
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = file_get_contents("php://input");
    $PUT = json_decode($data, true);

    if (isset($PUT['site-visit'])) {
        if ($PUT['site-visit'] == "time") {
            // Get the current Unix timestamp in milliseconds
            $timestamp = date('Y-m-d H:i:s', time());
            $updateStatus = true;

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Prepare the query
            $query = "UPDATE flw_appl_reports SET sv_date_done = :timestamp, updated_timestamp = :timestamp WHERE system_id = :systemId AND report_no = :reportNo AND authority_id = :authorityId";
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':reportNo', $PUT['rid']);
            $stmt->bindParam(':authorityId', $PUT['auth']);

            // Execute the query
            if (!$stmt->execute()) {
                $errorInfo = $stmt->errorInfo();
                $updateStatus = false;
                echo "Error executing query: " . $errorInfo[2];
            }

            // Return the timestamp as a JSON response
            header('HTTP/2 200 OK');
            echo json_encode([
                'timestamp' => $timestamp,
                'systemId' => $systemId,
                "success" => $updateStatus,
                "message" => "Maklumat masa lawatan tapak berjaya disimpan."
            ]);
        }
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
}