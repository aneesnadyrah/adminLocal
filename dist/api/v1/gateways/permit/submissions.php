<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "api/functions.php";
require "config/system.php";
require "config/DBFactory.php";
require "config/autoload.php";

// set header control :: origin header are setted by gateway.php
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $db = new DBConnectionFactory();
    $conn = $db->createConnection();
    $telegram = new Telegram();
    $system = new System;
    $tenant = $system->App->tenant;
    $traffic = new Traffic();
    $curl = new Curl();
    $username = $_SESSION['username'];
    $flow = new FlowStatuses($username);
    $changelog = new Changelog($username);

    // Get the token and request domain from the request
    $token = isset($_GET['token']) ? $_GET['token'] : $_GET['t'];
    $requestDomain = substr($_SERVER['HTTP_ORIGIN'], 8);

    // Prepare a statement to select the API token that matches the token and domain in the request, and is still active
    $stmt = $conn->prepare('SELECT * FROM sys_api_tokens WHERE token = :token AND domain = :domain AND expires_at > NOW() AND is_active = true');
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':domain', $requestDomain);
    $stmt->execute();

    // Check if a row was returned from the query
    if ($stmt->rowCount() > 0) {
        // Retrieve the JSON data
        $json_data = file_get_contents("php://input");

        // Parse the JSON data
        $data = json_decode($json_data, true);

        // Check if JSON data was retrieved successfully
        if ($json_data === false || $data === null) {
            // Finally, return a JSON
            header("HTTP/2 204 No Content");
            $result = array(
                "success" => false,
                "message" => "Error retrieving JSON data",
            );

            $error = 'Error retrieving JSON data';
            // ExternalApi::recordCallbackData( 1, 9, $json_data, json_encode($error), $_SERVER['REQUEST_URI'], http_response_code(), false);
            
            $result['success'] === true ? $status = true : $status = false;        
            $headers = json_encode($curl->getHeaders());
            // Get protocol
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'];
            $requestUri = $_SERVER['REQUEST_URI'];
            $url = $protocol . '://' . $domain . $requestUri;
            $curl->callback($url, $headers, $json_data, json_encode($result), $_SERVER['REQUEST_METHOD'], $status);
            
            echo json_encode($result);
            $conn = null;
            exit;
        }

        // Check if the expected keys are present in the data
        if (!isset($data['subId']) || !isset($data['district'])) {
            header("HTTP/2 204 No Content");
            $result = array(
                "success" => false,
                "message" => "JSON data is missing expected keys",
            );

            $error = 'JSON data is missing expected keys';
            // ExternalApi::recordCallbackData( 1, 9, json_encode($data), json_encode($error), $_SERVER['REQUEST_URI'], http_response_code(), false);

            $result['success'] === true ? $status = true : $status = false;        
            $headers = json_encode($curl->getHeaders());
            // Get protocol
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'];
            $requestUri = $_SERVER['REQUEST_URI'];
            $url = $protocol . '://' . $domain . $requestUri;
            $curl->callback($url, $headers, $json_data, json_encode($result), $_SERVER['REQUEST_METHOD'], $status);
            
            echo json_encode($result);
            $conn = null;
            exit;
        }

        $dateYear = date('my', time());
        $dateFull = date('dmY', time());
        $shortRef = $data['subId'] . "-" . $dateYear;
        // $systemId = base64_encode($shortRef);
        $systemId = $data['subId'] . $dateFull;
        $userAdded = $data['userCreated'];

        $timestamp = date('Y-m-d H:i:s', time());
        $applyDate = date('Y-m-d', time());

        $startDate = new DateTime(substr($data['projectDate'], 1, 10));
        $endDate = new DateTime(substr($data['projectDate'], 12, -1));

        // Prepare the INSERT statement
        $stmt = $conn->prepare("INSERT INTO flw_appl_entries (system_id, project_title, utility_provider, application_date, type_application, application_code, district, site_start, site_end, link_id, user_created, created_at, submission_code, project_costs, project_date, tags, submitted_at) VALUES (:systemId, :title, :provider, :date, :applType, :applCode, :district, :siteStart, :siteEnd, :linkId, :userCreated, :timestamp, :subid, :projectCosts, :projectDate, :projectTags, :submittedAt) RETURNING id");

        // Bind parameters to the statement
        // $stmt->bindParam(':subid', $data['subId']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':provider', $data['provider']);
        $stmt->bindParam(':date', $applyDate);
        $stmt->bindValue(':district', '{' . implode(",", $data['district']) . '}', PDO::PARAM_STR);
        // $stmt->bindValue(':region', '{' . implode(",", $data['region']) . '}', PDO::PARAM_STR);
        $stmt->bindParam(':siteStart', $data['siteStart']);
        $stmt->bindParam(':siteEnd', $data['siteEnd']);
        $stmt->bindParam(':linkId', $data['linkId']);
        $stmt->bindParam(':userCreated', $data['userCreated']);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->bindParam(':subid', $data['subId']);
        $stmt->bindParam(':applType', $data['applType']);
        $stmt->bindParam(':applCode', $data['applCategory']);
        $stmt->bindParam(':projectCosts', $data['projectCosts']);
        $stmt->bindParam(':projectDate', $data['projectDate']);
        $stmt->bindParam(':projectTags', $data['projectTags']);
        $stmt->bindParam(':submittedAt', $data['submittedAt']);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        $id = $stmt->fetchColumn();

        // Execute the statement
        if (isset($id)) {
            // Insert data into roadList table
            $stmt2 = $conn->prepare("INSERT INTO flw_appl_roads (system_id, road_name, method, road_length, created_at, latitude_start, longitude_start, latitude_end, longitude_end) VALUES (:systemId, :name, :method, :length, :timestamp, :slat, :slong, :elat, :elong) RETURNING id");

            $roadIds = array();
            $tLength = 0;
            $cpLength = 0; //cable pulling length
            $otLength = 0; //other length
            $lengthCode = "";
            foreach ($data['roads'] as $road) {
                $name = $road['name'];
                $method = $road['method'];
                $length = $road['length'];
                $start = $road['start'];
                $end = $road['end'];
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
                $slat = floatval($slat);
                $slong = floatval($slong);

                list($elat, $elong) = explode(",", $end);
                $elat = floatval($elat);
                $elong = floatval($elong);

                $stmt2->execute([
                    ':systemId' => $systemId,
                    ':name' => $name,
                    ':method' => $methodId,
                    ':length' => $length,
                    ':timestamp' => $timestamp,
                    ':slat' => $slat,
                    ':slong' => $slong,
                    ':elat' => $elat,
                    ':elong' => $elong
                ]);

                $roadId = [];
                while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                    $roadId[] = $row['id'];
                }

                $roadIds = array_merge($roadIds, $roadId);
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

            if (is_array($roadIds)) {
                $int = [];
                foreach ($roadIds as $value) {
                    $int[] = (int) $value;
                }
                $roadId = '{' . implode(",", $int) . '}';
            } else {
                $roadId = '{' . intval($roadIds) . '}';
            }

            $timestamp = date('Y-m-d H:i:s', time());
            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET list_road_id = :roadId, created_at = :timestamp, application_length = :tLength, length_code = :lengthCode WHERE id = :id ");
            $stmt3->bindParam(':roadId', $roadId);
            $stmt3->bindParam(':id', $id);
            $stmt3->bindParam(':timestamp', $timestamp);
            $stmt3->bindParam(':tLength', $tLength);
            $stmt3->bindParam(':lengthCode', $lengthCode);
            $stmt3->execute();

        } else {
            echo 'Error inserting data: ' . $stmt->errorInfo()[2] . "\n";
        }

        // Execute the statement
        if (isset($id)) {
            // Insert data into contacts table
            $stmt4 = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, email, phone_no, type, position, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, :email, :phone, :type, :position, :timestamp) RETURNING id");

            $contactIds = array();
            // $city = "";
            // $state = "";
            foreach ($data['contacts'] as $contact) {
                $name = $contact['fullName'];
                $companyName = $contact['companyName'];
                $email = $contact['email'];
                $unit = $contact['unitNo'];
                $phone = $contact['phoneNo'];
                $type = $contact['category'];
                $position = $contact['position'];
                $postcode = $contact['postcode'];
                $street = $contact['streetName'];
                $timestamp = date('Y-m-d H:i:s', time());

                $stmt5 = $conn->prepare('SELECT city, state FROM ls_postcode WHERE postcode = :postcode');
                $stmt5->execute([
                    ':postcode' => $postcode
                ]);

                $row5 = $stmt5->fetch(PDO::FETCH_ASSOC);

                $stmt4->execute([
                    ':systemId' => $systemId,
                    ':name' => $name,
                    ':companyName' => $companyName,
                    ':email' => $email,
                    ':unit' => $unit,
                    ':phone' => $phone,
                    ':type' => $type,
                    ':position' => $position,
                    ':postcode' => $postcode,
                    ':street' => $street,
                    ':city' => $row5['city'],
                    ':state' => $row5['state'],
                    ':timestamp' => $timestamp
                ]);

                $contactId = [];
                while ($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                    $contactId[] = $row['id'];
                }

                $contactIds = array_merge($contactIds, $contactId);
            }

            if (is_array($contactIds)) {
                $int = [];
                foreach ($contactIds as $value) {
                    $int[] = (int) $value;
                }
                $contactId = '{' . implode(",", $int) . '}';
            } else {
                $contactId = '{' . intval($contactIds) . '}';
            }

            $timestamp = date('Y-m-d H:i:s', time());
            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET contact_id = :contactId WHERE id = :id ");
            $stmt3->bindParam(':contactId', $contactId);
            $stmt3->bindParam(':id', $id);
            $stmt3->execute();

        } else {
            echo 'Error inserting data: ' . $stmt->errorInfo()[2] . "\n";
        }

        // Execute the statement
        if (isset($id)) {
            // Insert data into attachments table
            $stmt6 = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, details, url, attachment_date, created_date, attachment_type, user_added, mime_type, size,project_status) VALUES (:systemId, :name, :url, :applyDate, :timestamp, :type, :userAdded, :mime, :size,:status)");

            foreach ($data['attachments'] as $key => $attachment) {
                $url = $attachment['url'];
                $mime = $attachment['mime'];
                $name = $attachment['name'];
                $size = $attachment['size'];
                $applyDate = date('Y-m-d', time());
                $timestamp = date('Y-m-d H:i:s', time());
                $type = $key;
                $userAdded = $data['userCreated'];
                $statusId = 81;

                $stmt6->execute([
                    ':systemId' => $systemId,
                    ':url' => $url,
                    ':mime' => $mime,
                    ':name' => $name,
                    ':size' => $size,
                    ':applyDate' => $applyDate,
                    ':timestamp' => $timestamp,
                    ':type' => $type,
                    ':userAdded' => $userAdded,
                    ':status' => $statusId
                ]);
            }

        } else {
            echo 'Error inserting data: ' . $stmt->errorInfo()[2] . "\n";
        }

        // Execute the statement
        if (isset($id)) {
            // Insert data into application status table
            $stmt7 = $conn->prepare("INSERT INTO status_appl (system_id, project_status) VALUES (:systemId, :status)");
            $stmt7->execute([
                ':systemId' => $systemId,
                ':status' => 1
            ]);

        } else {
            echo 'Error inserting data: ' . $stmt->errorInfo()[2] . "\n";
        }

        // Execute the statement
        if (isset($id)) {
            $userAdded = $data['userCreated'];

            // Insert data into application status table
            $stmt8 = $conn->prepare("INSERT INTO sys_record_changelog (system_id, new_status, new_timestamp, details, username) VALUES (:systemId, :newStatus, :createdAt, :details, :userName)");
            $stmt8->execute([
                ':systemId' => $systemId,
                ':newStatus' => 1,
                ':createdAt' => $timestamp,
                ':details' => 'Permohonan Baru Dari Pemohon',
                ':userName' => $userAdded,
            ]);

        } else {
            echo 'Error inserting data: ' . $stmt->errorInfo()[2] . "\n";
        }

        // ExternalApi::recordCallbackData( 1, 9, json_encode($data), $response, $_SERVER['REQUEST_URI'], http_response_code(), true);

        header("HTTP/2 200 OK");
        $result = array(
            "success" => true,
            "message" => "success",
        );
        
        $result['success'] === true ? $status = true : $status = false;        
        $headers = json_encode($curl->getHeaders());
        // Get protocol
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $domain = $_SERVER['HTTP_HOST'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $url = $protocol . '://' . $domain . $requestUri;
        $curl->callback($url, $headers, $json_data, json_encode($result), $_SERVER['REQUEST_METHOD'], $status);
        
        echo json_encode($result);

        if($result['success'] === true) {
            // NOTE: current status = 
            $status2 = $flow->goToNextFlow($systemId, "operation", "BF");

            // NOTE - Update Task Assignment
            $assignment = $taskAssignment->set($roleId, $systemId, $status2);

            // NOTE - Insert Project Changelog
            $details = 'Permohonan Permit Baru Diterima';
            if($changelog->projectActivity($systemId, $status2, $details)) {
                $timeConverted = Utilities::convertDateToMalay($data->submittedAt);
                // declare telegram notification string
                $message = "Permohonan Permit Baru telah diterima. Sila semak permohonan ini \n\n<strong>📝 Tajuk : " . $data->title . " \n📆 Tarikh Permohonan : " . $timeConverted . "</strong>";

                // NOTE - send telegram notification
                $response = $telegram->sendMessage('group', 'management', $message, 'html');
                $response = $telegram->sendMessage('department', 'registration', $message, 'html');

            };
        }

        // Close the connection
        $conn = null;

    } else {
        // The token or domain is invalid or the token has expired
        $response = json_encode(array("message" => 'Invalid or expired API token'));
        echo $response;

        // ExternalApi::recordCallbackData( 1, 9, file_get_contents("php://input"), $response, $_SERVER['REQUEST_URI'], http_response_code(), false);

        $traffic->requestAPI('inbound', $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $json_data, http_response_code(), true);


    }

} else {

    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Prepare a statement to select the API token that matches the token and domain in the request, and is still active
    $stmt = $conn->prepare('SELECT id, system_id, reference_no, project_title, application_length, user_created, submission_code FROM flw_appl_entries ORDER BY id DESC LIMIT 2');
    $stmt->execute();

    // Create an associative array to hold the query result
    $data = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Use the 'id' as the key to remove duplicates
        $data[$row['id']] = $row;
    }

    // Convert the result to a JSON string
    $json = json_encode(array_values($data));

    // Return the JSON string to the client
    echo $json;

    // Close the database connection
    $conn = null;
}