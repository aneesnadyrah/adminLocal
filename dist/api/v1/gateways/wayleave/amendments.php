<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
include "config/functions.php";
include "api/functions.php";
require "config/autoload.php";

// set header control :: origin header are setted by gateway.php
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$curlApi = new Curl();
$taskAssignment = new TaskAssignments();
$system = new System();

if($method == 'GET'){

} else if ($method == "POST"){

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    $data = json_decode(json_encode($_POST));

    $submissions = [];

    if ($data->notes_1 !== '') {
        $submission1 = [
            "steps" => 1,
            "items" => [
                "title" => isset($data->title) ? true : false,
                "provider" => isset($data->provider) ? true : false,
                "info" => isset($data->info) ? true : false,
                "notes" => $data->notes_1,
                "attachment" => isset($data->appl_letter) ? true : false
            ]
        ];

        $submissions[] = $submission1;
    }

    if ($data->notes_2 !== '') {
        $submission2 = [
            "steps" => 2,
            "items" => [
                "applicant" => isset($data->applicant) ? true : false,
                "officer" => isset($data->officer) ? true : false,
                "notes" => $data->notes_2,
                "attachment" => isset($data->ack_letter) ? true : false
            ]
        ];

        $submissions[] = $submission2;
    }

    if ($data->notes_3 !== '') {
        $submission3 = [
            "steps" => 3,
            "items" => [
                "district" => isset($data->district) ? true : false,
                "roads" => isset($data->road) ? true : false,
                "notes" => $data->notes_3,
                "attachment-1" => isset($data->doc_technical) ? true : false,
                "attachment-2" => isset($data->plan_location) ? true : false
            ]
        ];

        $submissions[] = $submission3;
    }

    $token = ["secret" => $secret];
    $amendments = ["submissions" => ["amendments" => $submissions]];
    $merge = array_merge($token, $amendments);
    $json = json_encode($merge);

    $systemId = $_POST['system-id'];
    $stmt = $conn->prepare('SELECT submission_code FROM flw_appl_entries WHERE system_id = :systemId LIMIT 1');
    $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $subId = $result['submission_code'];
    $created = date('Y-m-d H:i:s', time());

    // NOTE - current status = 1
    // $statusUpdateResult = updateProjectStatus($conn, $systemId, 1, 997);
    $statusUpdateResult = $flow->goToNextFlow($systemId, "operation", "BF");

    // NOTE - Insert Project Changelog
    $details = 'Permohonan Telah Dipinda';
    $changelog->projectActivity($systemId, $statusUpdateResult, $details);

    // NOTE - Update Task Assignment
    $assignment = $taskAssignment->set($roleId, $systemId, $statusUpdateResult);

    // set initial status of changeLogUpdateResult
    // $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

    // Update changelog if status update successful
    // if ($statusUpdateResult['result'] == true) {
    //     insertSysRecordChangelog($conn, $systemId, 'Permohonan telah dipinda bagi no penyerahan #'. $subId, $statusUpdateResult['new_status']);
    // }

    $stmt = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES
    (:systemId, :notes, :created)");
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindValue(':notes', 'Permohonan telah dipinda bagi no penyerahan #' . $subId);
    $stmt->bindParam(':created', $created);
    $stmt->execute();

    $conn = null;

    // Send a POST request to Extercord API endpoint
    $ec_url = $system->App->ec_url;
    $url = $ec_url.'/gateway/internal/amendment/'.$subId;
    $curl = curl_init($url);

    $options = array(
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $json,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Origin: https://'.$_SERVER['HTTP_HOST'])
    );
    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);
    curl_close($curl);

    // Finally, return a JSON
    http_response_code(200);
    $result = array(
        "success" => true,
        "message" => "success",
    );

    $result['success'] === true ? $status = true : $status = false;        
    $headers = json_encode($curlApi->getHeaders());
    //get Full Domain
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $domain = $_SERVER['HTTP_HOST'];
    $requestUri = $_SERVER['REQUEST_URI'];
    $url = $protocol . '://' . $domain . $requestUri;
    $curlApi->callback($url, $headers, $json, json_encode($result), $_SERVER['REQUEST_METHOD'], $status);

    print_r($result = json_decode($response));

} else if ($method == "PUT") {
    // Get the token and request domain from the request
    $token = isset($_GET['token']) ? $_GET['token'] : $_GET['t'];
    $requestDomain = substr($_SERVER['HTTP_ORIGIN'], 8);

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Prepare a statement to select the API token that matches the token and domain in the request, and is still active
    $stmt = $conn->prepare('SELECT * FROM sys_api_tokens WHERE token = :token AND domain = :domain AND expires_at > NOW() AND is_active = true');
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':domain', $requestDomain);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Retrieve the JSON data
        $json_data = file_get_contents("php://input");

        // Check if JSON data was retrieved successfully
        if ($json_data === false) {
            echo 'Error retrieving JSON data';
            exit;
        }

        // Parse the JSON data
        $data = json_decode($json_data, true);

        // Check if the expected keys are present in the data
        if (!isset($data['subId'])) {
            echo 'JSON data is missing expected keys';
            exit;
        } else {
            $subId = $data['subId'];
        }

        // Prepare the UPDATE statement
        $stmt = $conn->prepare("UPDATE flw_appl_entries SET utility_provider = :provider, project_title = :title, link_id = :linkId, site_start = :siteStart, site_end = :siteEnd, type_application = :applType, application_code = :applCode, district = :district WHERE submission_code = :subId RETURNING system_id");

        // Bind the parameters
        $stmt->bindParam(':provider', $data['provider']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':linkId', $data['linkId']);
        $stmt->bindParam(':siteStart', $data['siteStart']);
        $stmt->bindParam(':siteEnd', $data['siteEnd']);
        $stmt->bindParam(':applType', $data['applType']);
        $stmt->bindParam(':applCode', $data['applCategory']);
        $stmt->bindValue(':district', '{' . implode(",", $data['district']) . '}', PDO::PARAM_STR);
        $stmt->bindParam(':subId', $data['subId']);
        // Execute the UPDATE statement
        $stmt->execute();

        $systemId = $stmt->fetchColumn();

        $stmt = $conn->prepare("UPDATE flw_appl_contacts SET full_name = :name, position = :position, email = :email, phone_no = :phone, company_name = :companyName, address_1 = :unit, address_2 = :street, postcode = :postcode, city = :city, state = :state, type = :type WHERE system_id = :systemId RETURNING id");

        $contactIds = array();
        foreach ($data['contacts'] as $contact) {
            $stmt1 = $conn->prepare('SELECT city, state FROM ls_postcode WHERE postcode = :postcode');
            $stmt1->execute([
                ':postcode' => $contact['postcode']
            ]);

            $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
            if ($row1) {
                $city = $row1['city'];
                $state = $row1['state'];
            } else {
                $city = null;
                $state = null;
            }

            $stmt->bindParam(':name', $contact['fullName']);
            $stmt->bindParam(':position', $contact['position']);
            $stmt->bindParam(':phone', $contact['phoneNo']);
            $stmt->bindParam(':email', $contact['email']);
            $stmt->bindParam(':companyName', $contact['companyName']);
            $stmt->bindParam(':unit', $contact['unitNo']);
            $stmt->bindParam(':street', $contact['streetName']);
            $stmt->bindParam(':postcode', $contact['postcode']);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':state', $state);
            $stmt->bindParam(':type', $contact['type']);
            $stmt->bindParam(':systemId', $systemId);

            // Execute the UPDATE statement
            $stmt->execute();

            $contactId = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $contactId[] = $row['id'];
            }

            $contactIds = array_merge($contactIds, $contactId);
        }

        $stmt = $conn->prepare("UPDATE flw_appl_roads SET road_name = :name, method = :method, road_length = :length, latitude_start = :slat, longitude_start = :slong, latitude_end = :elat, longitude_end = :elong WHERE system_id = :systemId RETURNING id");

        $roadIds = array();
        $tLength = 0;
        $cpLength = 0; //cable pulling length
        $otLength = 0; //other length
        $lengthCode = "";
        foreach ($data['roads'] as $road) {
            $stmt->bindParam(':name', $road['name']);
            $stmt->bindParam(':method', $methodId);
            $stmt->bindParam(':length', $road['length']);
            $stmt->bindParam(':slat', $slat);
            $stmt->bindParam(':slong', $slong);
            $stmt->bindParam(':elat', $elat);
            $stmt->bindParam(':elong', $elong);
            $stmt->bindParam(':systemId', $systemId);
            $tLength += $road['length'];
            $start = $road['start'];
            $end = $road['end'];

            if (is_array($method)) {
                $int = array_map('intval', $method);
                $methodId = '{' . implode(",", $int) . '}';
            } else {
                $methodId = '{' . intval($method) . '}';
            }

            //if have cable pulling add to cp Length
            if((in_array(5, explode(",", trim($methodId, '{}'))))){
                $cpLength += $length;
            } else { 
                // add to other Length
                $otLength += $length;
            }

            list($slat, $slong) = explode(",", $start);
            $slat = floatval($slat);
            $slong = floatval($slong);

            list($elat, $elong) = explode(",", $end);
            $elat = floatval($elat);
            $elong = floatval($elong);

            // Execute the UPDATE statement
            $stmt->execute();

            $roadId = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $roadId[] = $row['id'];
            }

            $roadIds = array_merge($roadIds, $roadId);
        }

        if (is_array($contactIds)) {
            $int = [];
            foreach($contactIds as $value) {
                $int[] = (int) $value;
            }
            $contactId = '{' . implode(",", $int) . '}';
        } else {
            $contactId = '{' . intval($contactIds) . '}';
        }

        if (is_array($roadIds)) {
            $int = [];
            foreach($roadIds as $value) {
                $int[] = (int) $value;
            }
            $roadId = '{' . implode(",", $int) . '}';
        } else {
            $roadId = '{' . intval($roadIds) . '}';
        }

        if ($tenant === 'KUDR') {
            if ($tLength > 450) {
                $lengthCode = "A";
            } else if ($tLength <= 450) {
                $lengthCode = "B";
            }
            // $lengthCode = ($tLength > 450) ? "A" : "B";
        } else if ($tenant === 'KUTT') {
            //if have method cable pullling & cp length > other length
            if($cpLength > $otLength){
                $lengthCode = "A4";
            } 
            else // check base on application_code
            {
                if ($tLength < 100) {
                    if($data->applCategory === 'KT'){$lengthCode = "A1";}
                    elseif($data->applCategory === 'KP'){$lengthCode = "R1";}

                } else if ($tLength >= 100 && $tLength < 1000) {
                    if($data->applCategory === 'KT'){$lengthCode = "A2";}
                    elseif($data->applCategory === 'KP'){$lengthCode = "R2";}
                } else {
                    if($data->applCategory === 'KT'){$lengthCode = "A3";}
                    elseif($data->applCategory === 'KP'){$lengthCode = "R3";}
                }
            }

        } else if ($tenant === 'KUP') {
            if ($tLength <= 100) {
                $lengthCode = "A1";
            } else if ($tLength > 100 && $tLength <= 1000) {
                $lengthCode = "A2";
            } else {
                $lengthCode = "A3";
            }
        }

        $stmt = $conn->prepare("UPDATE flw_appl_attachments SET details = :name, url = :url,  mime_type = :mime, size = :size, attachment_type = :type WHERE system_id = :systemId");

        foreach ($data['attachments'] as $key => $attachment) {
            $stmt->bindParam(':url', $attachment['url']);
            $stmt->bindParam(':name', $attachment['name']);
            $stmt->bindParam(':mime', $attachment['mime']);
            $stmt->bindParam(':size', $attachment['size']);
            $stmt->bindParam(':type', $key);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();
        }

        $stmt = $conn->prepare("UPDATE flw_appl_entries SET contact_id = :contactId, list_road_id = :roadId, application_length = :tLength, length_code = :lengthCode WHERE system_id = :systemId ");
        $stmt->bindParam(':contactId', $contactId);
        $stmt->bindParam(':roadId', $roadId);
        $stmt->bindParam(':tLength', $tLength);
        $stmt->bindParam(':lengthCode', $lengthCode);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();

        // NOTE - current status = 997
        // $statusUpdateResult = updateProjectStatus($conn, $systemId, 997, 1);
        $statusUpdateResult = $flow->goToNextFlow($systemId, "operation", "BF");
        
        // NOTE - Insert Project Changelog
        $details = 'Permohonan Telah Dikemaskini';
        $changelog->projectActivity($systemId, $statusUpdateResult, $details);

        // NOTE - Update Task Assignment
        $assignment = $taskAssignment->set($roleId, $systemId, $statusUpdateResult);

        // set initial status of changeLogUpdateResult
        // $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

        // Update changelog if status update successful
        // if ($statusUpdateResult['result'] == true) {
        //     insertSysRecordChangelog($conn, $systemId, 'Permohonan telah dikemaskini bagi no penyerahan #'. $subId, $statusUpdateResult['new_status']);
        // }

        $created = date('Y-m-d H:i:s', time());
        $stmt = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES
        (:systemId, :notes, :created)");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindValue(':notes', 'Permohonan telah dikemaskini bagi no penyerahan #'.$subId);
        $stmt->bindParam(':created', $created);
        $stmt->execute();

        // Finally, return a JSON
        if($assignment) {
            http_response_code(200);
            $result = array(
                "success" => true,
                "message" => "success",
            );
    
        } else {
            http_response_code(500);
            $result = array(
                "success" => false,
                "message" => "failed",
            );
        }

    } else {
        // The token or domain is invalid or the token has expired
        http_response_code(500);
        $result = array(
            "success" => false,
            "message" => "Invalid or expired API token",
        );

    }

}