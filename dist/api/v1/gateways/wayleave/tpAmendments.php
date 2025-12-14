<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";

header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET') {
    $json = file_get_contents("php://input");
    $data = json_decode($json);

    http_response_code(200);
    echo json_encode(
        array(
            "success" => true,
            "data" => $data
        )
    );

} else if ($method == "PUT") {
    $json = file_get_contents("php://input");
    $data = json_decode($json);
    $db = new DBConnectionFactory();
    $taskAssignment = new TaskAssignments();
    $conn = $db->createConnection();
    $telegram = new Telegram();
    $system = new System;
    $curl = new Curl();
    $tenant = $system->App->tenant;
    $timestamp = date('Y-m-d H:i:s', time());

    if (Utilities::checkDomainToken($data->token, $_SERVER['HTTP_ORIGIN'])) {

        $dateYear = date('my', time());
        $dateFull = date('dmY', time());
        $systemId = $data->subId;
        $userAdded = $data->userCreated;
        $state = $data->state;
        $startDate = new DateTime(substr($data->projectDate, 1, 10));
        $endDate = new DateTime(substr($data->projectDate, 12, -1));
        $successCounter = 0;

        $refNo = Utilities::getRefNo($systemId);

        // NOTE - get road data
        if (isset($data->roads)) {
            $active_status = 'false';

            // select all road
            $getRoad = $conn->prepare("SELECT * FROM flw_appl_roads WHERE system_id = :systemId");
            $getRoad->bindParam(':systemId', $systemId);
            $getRoad->execute();
            $roadData = $getRoad->fetchAll(PDO::FETCH_ASSOC);

            // update version and active for old road
            $updateRoad = $conn->prepare("UPDATE flw_appl_roads SET active = :active_status WHERE system_id = :systemId");

            foreach ($roadData as $key => $road) {
                $updateRoad->bindParam(':active_status', $active_status);
                $updateRoad->bindParam(':systemId', $systemId);
                $updateRoad->execute();
            }

            // get version of road
            $stmt = $conn->prepare("SELECT version FROM flw_appl_roads WHERE system_id = :systemId ORDER BY version DESC LIMIT 1");
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_OBJ);

            $new_version = $record->version + 1;
            $active_latest = 'true';

            // insert new road from amendment
            $roadIds = array();
            $tLength = 0;
            $cpLength = 0; //cable pulling length
            $otLength = 0; //other length
            $lengthCode = "";

            foreach ($data->roads as $key => $road) {
                $name = $road->name;
                $method = $road->method;
                $length = $road->length;
                $start = $road->start;
                $end = $road->end;
                $rDistrict = $road->district;
                $tLength += $length;

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
                $number1 = floatval($slat);
                $slat = number_format($number1, 8, '.', '');
                $number2 = floatval($slong);
                $slong = number_format($number2, 8, '.', '');

                list($elat, $elong) = explode(",", $end);
                $number1 = floatval($elat);
                $elat = number_format($number1, 8, '.', '');
                $number2 = floatval($elong);
                $elong = number_format($number2, 8, '.', '');

                // Insert data into roadList table
                $stmt = $conn->prepare("INSERT INTO flw_appl_roads (system_id, road_name, method, road_length, created_at, latitude_start, longitude_start, latitude_end, longitude_end, districts, updated_at, version, active) VALUES (:systemId, :name, :method, :length, :timestamp, :slat, :slong, :elat, :elong, :districts, :updated_at, :new_version, :active_latest) RETURNING id");

                if (
                    $stmt->execute([
                        ':systemId' => $systemId,
                        ':name' => $name,
                        ':method' => $methodId,
                        ':length' => $length,
                        ':timestamp' => $timestamp,
                        ':slat' => $slat,
                        ':slong' => $slong,
                        ':elat' => $elat,
                        ':elong' => $elong,
                        ':districts' => $rDistrict,
                        ':updated_at' => $timestamp,
                        ':new_version' => $new_version,
                        ':active_latest' => $active_latest
                    ])
                ) {
                    $successCounter++;
                    $roadId = $stmt->fetchColumn(); // Get the newly inserted road ID
                    if ($roadId) {
                        $roadIds[] = $roadId; // Collect all road IDs
                    }
                }
            }

            // Step 1: Retrieve existing list_road_id
            $stmt2 = $conn->prepare("SELECT list_road_id FROM flw_appl_entries WHERE system_id = :systemId");
            $stmt2->execute([':systemId' => $systemId]);
            $existingRoadIds = $stmt2->fetchColumn();

            // Step 2: Convert the existing road IDs into an array
            $existingRoadIdsArray = $existingRoadIds ? explode(',', trim($existingRoadIds, '{}')) : [];

            // Step 3: Merge existing IDs with new ones and remove duplicates
            $newRoadIdsArray = array_merge($existingRoadIdsArray, $roadIds);
            $newRoadIdsArray = array_unique($newRoadIdsArray); // Remove duplicates

            // Step 4: Convert the array back to the desired format (e.g., {12,14})
            $newRoadIdsString = '{' . implode(',', $newRoadIdsArray) . '}';

            // Step 5: Update the entry with the new road IDs
            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET list_road_id = :roadId WHERE system_id = :systemId ");
            $stmt3->bindParam(':roadId', $newRoadIdsString);
            $stmt3->bindParam(':systemId', $systemId);
            $stmt3->execute();
        }

        // Execute the statement
        $username = $data->userCreated;
        $flow = new FlowStatuses($username);
        $chronology = new Chronology();
        $changelog = new Changelog($username);

        // next status : semakan pindaan pelan TP oleh PKD
        $flwStatus = 17; 

        $result = $flow->goToNextFlow($systemId, "operation", $data->applType, Steps: $flwStatus, allRole: true);

        if ($result) {
            // NOTE - Assign task
            $assignment = $taskAssignment->create($systemId, $flwStatus);
            // NOTE - Insert chronology create
            $chronoId = $chronology->create($username, $systemId, 0, 'file');

            $details = 'Pindaan Pelan Cadangan Teknikal';

            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
                $message = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);

                // NOTE - send telegram notification
                $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $message, 'html');
                $successCounter++;
            };
        }

        $chronoId = $chronology->create($username, $systemId, $flwStatus, 'notes');

        // NOTE - insert attachment data
        if (isset($data->attachments)) {
            $activeAttachment_status = 'false';

            // select all documents
            $getAttach = $conn->prepare("SELECT * FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type IN(1,2,3,4) AND active = true ");
            $getAttach->bindParam(':systemId', $systemId);
            $getAttach->execute();
            $attachData = $getAttach->fetchAll(PDO::FETCH_ASSOC);

            // update version and active for old road
            $updateAttach = $conn->prepare("UPDATE flw_appl_attachments SET active = :activeAttachment_status WHERE system_id = :systemId AND attachment_type IN(1,2,3,4) AND active = true ");

            foreach ($attachData as $key => $road) {
                $updateAttach->bindParam(':activeAttachment_status', $activeAttachment_status);
                $updateAttach->bindParam(':systemId', $systemId);
                $updateAttach->execute();
            }

            foreach ($data->attachments as $key => $attachment) {
                $url = $attachment->url;
                $mime = $attachment->mime;
                $name = $attachment->name;
                $size = $attachment->size;
                $type = $key;
                $userAdded = $data->userCreated;

                // get version of attachments
                $stmtA = $conn->prepare("SELECT version FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :type ORDER BY version DESC LIMIT 1");
                $stmtA->bindParam(':systemId', $systemId);
                $stmtA->bindParam(':type', $type);
                $stmtA->execute();
                $recordA = $stmtA->fetch(PDO::FETCH_OBJ);

                $new_versions = $recordA->version + 1;
                $activeAttachment_latest = 'true';

                $stmt = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date, created_date, attachment_type, user_added, mime_type, size, chronology_id, version, active) VALUES (:systemId, :name, :url, :applyDate, :timestamp, :type, :userAdded, :mime, :size, :chronoId, :new_version, :activeAttachment_latest)");

                if (
                    $stmt->execute([
                        ':systemId' => $systemId,
                        ':name' => $name,
                        ':url' => $url,
                        ':applyDate' => $data->submittedAt,
                        ':timestamp' => $timestamp,
                        ':type' => $type,
                        ':userAdded' => $userAdded,
                        ':mime' => $mime,
                        ':size' => $size,
                        ':chronoId' => $chronoId,
                        ':new_version' => $new_versions,
                        ':activeAttachment_latest' => $activeAttachment_latest
                    ])
                ) {
                    $successCounter++;
                };
            }
        }

        $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':created', $timestamp);
        $stmt->bindParam(':username', $data->userCreated);
        $stmt->bindParam(':chronology_id', $chronoId);
        $stmt->execute();

        if ($successCounter >= 3 && $assignment) {
            // Finally, return a JSON
            http_response_code(200);
            $result = array(
                "success" => true,
                "message" => "success",
            );

        } else {
            // Finally, return a JSON
            http_response_code(400);
            $result = array(
                "success" => false,
                "message" => "failed",
            );
        }

        $result['success'] === true ? $status = true : $status = false;
        $headers = json_encode($curl->getHeaders());
        //get Full Domain
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $domain = $_SERVER['HTTP_HOST'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $url = $protocol . '://' . $domain . $requestUri;
        $curl->callback($url, $headers, $json, json_encode($result), $_SERVER['REQUEST_METHOD'], $status);

        echo json_encode($result);
        $conn = null;


    } else {
        // The token or domain is invalid or the token has expired
        http_response_code(400);
        $result = array(
            "success" => false,
            "message" => "Invalid or expired API token",
        );
        echo json_encode($result);
        $conn = null;

        // $response = json_encode(array("message" => 'Invalid or expired API token'));
        // echo $response;
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'error' => 'Method not allowed'
    ]);
}