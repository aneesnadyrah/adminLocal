<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";


header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

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
    $date = date('Y-m-d', time());

    if (Utilities::checkDomainToken($data->token, $_SERVER['HTTP_ORIGIN'])) {

        $dateYear = date('my', time());
        $dateFull = date('dmY', time());
        $systemId = $data->subId;
        $userAdded = $data->userCreated;
        $state = $data->state;
        $startDate = new DateTime(substr($data->projectDate, 1, 10));
        $endDate = new DateTime(substr($data->projectDate, 12, -1));
        $successCounter = 0;

        // Check if the submission_code already exists
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM flw_appl_entries WHERE system_id = :subid");
        $checkStmt->bindParam(':subid', $systemId);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn();

        if ($exists == 0) {
            // NOTE - Prepare the INSERT statement
            $stmt = $conn->prepare("INSERT INTO flw_appl_entries (system_id, project_title, utility_provider, type_application, application_code, districts, site_start, site_end, link_id, user_created, application_date, created_at, project_costs, project_date, tags, submitted_at, payment_method, state) VALUES (:systemId, :title, :provider, :applType, :applCode, :districts, :siteStart, :siteEnd, :linkId, :userCreated, :date, :timestamp, :projectCosts, :projectDate, :projectTags, :submittedAt, :paymentMethod, :state) RETURNING id");

            $stmt->bindParam(':title', $data->title);
            $stmt->bindParam(':provider', $data->provider);
            $stmt->bindValue(':districts', '{' . implode(",", $data->district) . '}', PDO::PARAM_STR);
            $stmt->bindParam(':siteStart', $data->siteStart);
            $stmt->bindParam(':siteEnd', $data->siteEnd);
            $stmt->bindParam(':linkId', $data->linkId);
            $stmt->bindParam(':userCreated', $data->userCreated);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':applType', $data->applType);
            $stmt->bindParam(':applCode', $data->applCategory);
            $stmt->bindParam(':projectCosts', $data->projectCosts);
            $stmt->bindParam(':projectDate', $data->projectDate);
            $stmt->bindParam(':projectTags', $data->projectTags);
            $stmt->bindParam(':submittedAt', $timestamp);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':paymentMethod', $data->paymentMethod);
            $stmt->bindParam(':state', $state);
            if ($stmt->execute()) {
                $successCounter++;
                $id = $stmt->fetchColumn();
            }

        } else {
            // Finally, return a JSON
            http_response_code(400);
            $result = array(
                "success" => false,
                "message" => "Submission code already exists. Record not inserted.",
            );
            echo json_encode($result);
            exit;

        }

        // NOTE - get road data
        if (isset($data->roads)) {

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
                $stmt = $conn->prepare("INSERT INTO flw_appl_roads (system_id, road_name, method, road_length, created_at, latitude_start, longitude_start, latitude_end, longitude_end, districts) VALUES (:systemId, :name, :method, :length, :timestamp, :slat, :slong, :elat, :elong, :districts) RETURNING id");

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
                        ':districts' => $rDistrict
                    ])
                ) {
                    $successCounter++;
                    $roadId = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $roadId[] = $row['id'];
                    }

                    $roadIds = array_merge($roadIds, $roadId);
                }

                //

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

            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET list_road_id = :roadId, created_at = :timestamp, application_length = :tLength, length_code = :lengthCode WHERE id = :id ");
            $stmt3->bindParam(':roadId', $roadId);
            $stmt3->bindParam(':id', $id);
            $stmt3->bindParam(':timestamp', $timestamp);
            $stmt3->bindParam(':tLength', $tLength);
            $stmt3->bindParam(':lengthCode', $lengthCode);
            $stmt3->execute();

        }

        // NOTE - get contact data
        // Execute the statement
        if (isset($data->contacts)) {

            $contactIds = array();
            foreach ($data->contacts as $key => $contact) {
                $name = $contact->fullName;
                $companyName = $contact->companyName;
                $email = $contact->email;
                $unit = $contact->unitNo;
                $phone = $contact->phoneNo;
                $type = $contact->category;
                $position = $contact->position;
                $postcode = $contact->postcode;
                $street = $contact->streetName;

                $stmt = $conn->prepare('SELECT city, state FROM ls_postcode WHERE postcode = :postcode');
                if (
                    $stmt->execute([
                        ':postcode' => $postcode
                    ])
                ) {
                    $successCounter++;
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                // Insert data into contacts table
                $stmt = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, email, phone_no, type, position, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, :email, :phone, :type, :position, :timestamp) RETURNING id");

                if (
                    $stmt->execute([
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
                        ':city' => $row['city'],
                        ':state' => $row['state'],
                        ':timestamp' => $timestamp
                    ])
                ) {
                    $successCounter++;
                    $contactId = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $contactId[] = $row['id'];
                    }

                    $contactIds = array_merge($contactIds, $contactId);
                }
                ;
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

            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET contact_id = :contactId WHERE id = :id ");
            $stmt3->bindParam(':contactId', $contactId);
            $stmt3->bindParam(':id', $id);
            $stmt3->execute();

        }

        // NOTE - process payment
        // Execute the statement
        if (isset($data->paymentMethod)) {
            $username = $data->userCreated;
            $flow = new FlowStatuses($username);
            $chronology = new Chronology();
            $changelog = new Changelog($username);

            // Change flow to RO with selected payment method
            $flwStatus = 4;
            $result = $flow->addRecordFlow($systemId, $data->applType, $flwStatus);

            if ($result) {
                if ($data->paymentMethod == 1 || $data->paymentMethod == 4) {
                    $financeFlwStatus = 6;
                    $financeResult = $flow->addRecordFlow($systemId, $data->applType, $financeFlwStatus);
                } else if ($data->paymentMethod == 2) {
                    $financeFlwStatus = 1;
                    $financeResult = $flow->addRecordFlow($systemId, $data->applType, $financeFlwStatus);
                } else {
                    $financeFlwStatus = 2;
                    $financeResult = $flow->addRecordFlow($systemId, $data->applType, $financeFlwStatus);
                }
                // NOTE - Assign task
                $assignment = $taskAssignment->create($systemId, $flwStatus);
                // NOTE - Insert chronology create
                $chronoId = $chronology->create($username, $systemId, 0, 'file');

                // NOTE - Insert Project Changelog
                $details = 'Permohonan Baru Diterima';
                if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
                    // $timeConverted = Utilities::convertDateToMalay($data->submittedAt);
                    // declare telegram notification string
                    $message = "Permohonan Baru telah dihantar oleh pemohon. Sila semak maklumat permohonan ini. \n\n<strong>🔗 No Permohonan : #" . $data->subId . " \n📝 Tajuk : " . $data->title . " \n📆 Tarikh Permohonan : " . Utilities::convertDateToMalay($data->submittedAt) . "</strong>";

                    // NOTE - send telegram notification
                    if ($telegram->sendMessage('department', 'registration', $message, 'html')) {
                        $telegram->sendMessage('group', 'management', $message, 'html');
                        $successCounter++;
                    }

                }

                // NOTE - Insert notes create
                $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':details', $details);
                $stmt->bindParam(':created', $timestamp);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':chronology_id', $chronoId);
                $stmt->execute();

            }

            // if ($data->paymentMethod == 1) {
            //     //paymentMethod : 1 = transfer; 2 = Invoice; 3 = DuitNow
            //     $flwStatus = 4;
            //     $result = $flow->addRecordFlow($systemId, $data->applType, $flwStatus);

            //     if ($result) {
            //         $financeFlwStatus = 6;
            //         $financeResult = $flow->addRecordFlow($systemId, $data->applType, $financeFlwStatus);
            //         // NOTE - Assign task
            //         $assignment = $taskAssignment->create($systemId, $flwStatus);
            //         // NOTE - Insert chronology create
            //         $chronoId = $chronology->create($username, $systemId, 0, 'file');
            //         // NOTE - Insert Project Changelog
            //         $details = 'Permohonan Baru Diterima';
            //         if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
            //             // $timeConverted = Utilities::convertDateToMalay($data->submittedAt);
            //             // declare telegram notification string

            //             // $message = "Permohonan Baru telah dihantar oleh pemohon menggunakan kaedah bayaran atas talian. Sila semak maklumat permohonan ini. \n\n<strong>📝 Tajuk : " . $data->title . " \n🔗 No Rujukan : " . $data->subId . " \n📆 Tarikh Permohonan : " . Utilities::convertDateToMalay($data->submittedAt) . "</strong>";
            //             $message = $telegram->getMessageByFlow($flwStatus, paymentMethod: 1, systemId: $systemId, item: $data->title, date: Utilities::convertDateToMalay($data->submittedAt));

            //             // NOTE - send telegram notification
            //             if ($telegram->sendMessage('department', 'registration', $message, 'html')) {
            //                 $successCounter++;
            //             }
            //             ;

            //         }
            //         ;
            //     }
            // } else if ($data->paymentMethod == 2) {
            //     //paymentMethod : 1 = transfer; 2 = Invoice; 3 = DuitNow
            //     $flwStatus = 1;
            //     $result = $flow->addRecordFlow($systemId, $data->applType, $flwStatus);

            //     if ($result) {
            //         // NOTE - Assign task
            //         $assignment = $taskAssignment->create($systemId, $flwStatus);
            //         // NOTE - Insert chronology create
            //         $chronoId = $chronology->create($username, $systemId, 0, 'file');
            //         // NOTE - Insert Project Changelog
            //         $details = 'Permohonan Baru Diterima dan Penyediaan Caj Pendaftaran Diperlukan';
            //         if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
            //             $timeConverted = Utilities::convertDateToMalay($data->submittedAt);
            //             // declare telegram notification string
            //             // $message = "Permohonan Baru telah diterima. Sila sediakan Invois Caj Pendaftaran. \n\n<strong>🔗 No Rujukan : " . $refNo . "\n📝 Tajuk : " . $data->title . " \n📆 Tarikh Permohonan : " . $timeConverted . "</strong>";
            //             $message = $telegram->getMessageByFlow($flwStatus, systemId: $systemId, item: $data->title, date: $timeConverted);

            //             // NOTE - send telegram notification
            //             if ($telegram->sendMessage('department', 'finance', $message, 'html')) {
            //                 $successCounter++;
            //             }
            //             ;
            //         }
            //         ;
            //     }

            // } else if ($data->paymentMethod == 3) {
            //     //paymentMethod : 1 = transfer; 2 = Invoice; 3 = DuitNow
            //     $flwStatus = 2;
            //     $result = $flow->addRecordFlow($systemId, $data->applType, $flwStatus);

            //     if ($result) {
            //         // NOTE - Assign task (assigned in payment)
            //         // $assignment = $taskAssignment->create($systemId, $flwStatus);
            //         // NOTE - Insert chronology create
            //         $chronoId = $chronology->create($username, $systemId, 0, 'file');
            //         $details = 'Permohonan Baru Diterima';
            //         if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
            //             $successCounter++;
            //         }
            //         ;
            //     }
            //     $assignment = true;

            // }
        } else if ($data->paymentMethod == null) {
            $username = $data->userCreated;
            $flow = new FlowStatuses($username);
            $chronology = new Chronology();
            $changelog = new Changelog($username);

            $flwStatus = 4;
            $result = $flow->addRecordFlow($systemId, $data->applType, $flwStatus);

            if ($result) {
                $financeFlwStatus = 1;
                $financeResult = $flow->addRecordFlow($systemId, $data->applType, $financeFlwStatus);
                // NOTE - Assign task
                $assignment = $taskAssignment->create($systemId, $flwStatus);
                // NOTE - Insert chronology create
                $chronoId = $chronology->create($username, $systemId, 0, 'file');
                // NOTE - Insert Project Changelog
                $details = 'Permohonan Baru Diterima';
                if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
                    // $timeConverted = Utilities::convertDateToMalay($data->submittedAt);
                    // declare telegram notification string
                    $message = "Permohonan Baru telah dihantar oleh pemohon. Sila semak maklumat permohonan ini. \n\n<strong>📝 Tajuk : " . $data->title . " \n🔗 No Permohonan : " . $data->subId . " \n📆 Tarikh Permohonan : " . Utilities::convertDateToMalay($data->submittedAt) . "</strong>";

                    // NOTE - send telegram notification
                    if ($telegram->sendMessage('department', 'registration', $message, 'html')) {
                        $telegram->sendMessage('group', 'management', $message, 'html');
                        $successCounter++;
                    }
                    ;

                }
                ;
            }
        }

        // NOTE - get attachment data
        // Execute the statement
        // if (isset($data->attachments)) {
            foreach ($data->attachments as $key => $attachment) {
                $url = $attachment->url;
                $mime = $attachment->mime;
                $name = $attachment->name;
                $size = $attachment->size;
                $type = $key;
                $userAdded = $data->userCreated;
                $statusId = 1;

                $stmt = $conn->prepare("INSERT INTO flw_appl_attachments (system_id, name, url, attachment_date, created_date, attachment_type, user_added, mime_type, size, chronology_id) VALUES (:systemId, :name, :url, :applyDate, :timestamp, :type, :userAdded, :mime, :size, :chronoId)");

                if (
                    $stmt->execute([
                        ':systemId' => $systemId,
                        ':url' => $url,
                        ':mime' => $mime,
                        ':name' => $name,
                        ':size' => $size,
                        ':applyDate' => $date,
                        ':timestamp' => $timestamp,
                        ':type' => $type,
                        ':userAdded' => $userAdded,
                        ':chronoId' => $chronoId,
                    ])
                ) {
                    $successCounter++;
                }
                ;
            }

        // }

        // $roleId = 2;
        // $assignment = $taskAssignment->create($roleId, $systemId, $flwStatus);

        if ($successCounter >= 6 && $assignment) {
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
} elseif ($_SERVER['REQUEST_METHOD'] == "PUT") {

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

        // NOTE - Insert new application data
        // Prepare the INSERT statement
        $stmt = $conn->prepare("UPDATE flw_appl_entries
                        SET project_title = :title,
                            utility_provider = :provider,
                            districts = :districts,
                            site_start = :siteStart,
                            site_end = :siteEnd,
                            link_id = :linkId,
                            user_created = :userCreated,
                            type_application = :applType,
                            application_code = :applCode,
                            project_costs = :projectCosts,
                            project_date = :projectDate,
                            tags = :projectTags,
                            submitted_at = :submittedAt,
                            state = :state
                        WHERE system_id = :systemId
                        RETURNING id");

        $stmt->bindParam(':title', $data->title);
        $stmt->bindParam(':provider', $data->provider);
        $stmt->bindValue(':districts', '{' . implode(",", $data->district) . '}', PDO::PARAM_STR);
        $stmt->bindParam(':siteStart', $data->siteStart);
        $stmt->bindParam(':siteEnd', $data->siteEnd);
        $stmt->bindParam(':linkId', $data->linkId);
        $stmt->bindParam(':userCreated', $data->userCreated);
        $stmt->bindParam(':applType', $data->applType);
        $stmt->bindParam(':applCode', $data->applCategory);
        $stmt->bindParam(':projectCosts', $data->projectCosts);
        $stmt->bindParam(':projectDate', $data->projectDate);
        $stmt->bindParam(':projectTags', $data->projectTags);
        $stmt->bindParam(':submittedAt', $data->submittedAt);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':state', $state);
        if ($stmt->execute()) {
            $successCounter++;
            $id = $stmt->fetchColumn();
        }
        ;


        // NOTE - get road data
        if (isset($data->roads)) {
            //delete road entry then update new road entry
            $stmt = $conn->prepare("DELETE FROM flw_appl_roads WHERE system_id = :systemId");
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();

            $timestamp = date('Y-m-d H:i:s', time());

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
                $stmt = $conn->prepare("INSERT INTO flw_appl_roads (system_id, road_name, method, road_length, created_at, latitude_start, longitude_start, latitude_end, longitude_end, districts, updated_at) VALUES (:systemId, :name, :method, :length, :timestamp, :slat, :slong, :elat, :elong, :districts, :updated_at) RETURNING id");

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
                        ':updated_at' => $timestamp
                    ])
                ) {
                    $successCounter++;
                    $roadId = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $roadId[] = $row['id'];
                    }

                    $roadIds = array_merge($roadIds, $roadId);
                }
                ;

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
                } else if ($tLength > 100 && $tLength < 1000) {
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

            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET list_road_id = :roadId, application_length = :tLength, length_code = :lengthCode WHERE id = :id ");
            $stmt3->bindParam(':roadId', $roadId);
            $stmt3->bindParam(':id', $id);
            $stmt3->bindParam(':tLength', $tLength);
            $stmt3->bindParam(':lengthCode', $lengthCode);
            $stmt3->execute();

        }

        // NOTE - get contact data
        // Execute the statement
        if (isset($data->contacts)) {
            //delete contacts then update new contacts
            $stmt = $conn->prepare("DELETE FROM flw_appl_contacts WHERE system_id = :systemId");
            $stmt->bindParam(':systemId', $systemId);
            $stmt->execute();

            $contactIds = array();
            foreach ($data->contacts as $key => $contact) {
                $name = $contact->fullName;
                $companyName = $contact->companyName;
                $email = $contact->email;
                $unit = $contact->unitNo;
                $phone = $contact->phoneNo;
                $type = $contact->category;
                $position = $contact->position;
                $postcode = $contact->postcode;
                $street = $contact->streetName;

                $stmt = $conn->prepare('SELECT city, state FROM ls_postcode WHERE postcode = :postcode');
                if (
                    $stmt->execute([
                        ':postcode' => $postcode
                    ])
                ) {
                    $successCounter++;
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                // Insert data into contacts table
                $stmt = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, email, phone_no, type, position, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, :email, :phone, :type, :position, :timestamp) RETURNING id");

                if (
                    $stmt->execute([
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
                        ':city' => $row['city'],
                        ':state' => $row['state'],
                        ':timestamp' => $timestamp
                    ])
                ) {
                    $successCounter++;
                    $contactId = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $contactId[] = $row['id'];
                    }

                    $contactIds = array_merge($contactIds, $contactId);
                }
                ;
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

            $stmt3 = $conn->prepare("UPDATE flw_appl_entries SET contact_id = :contactId WHERE id = :id ");
            $stmt3->bindParam(':contactId', $contactId);
            $stmt3->bindParam(':id', $id);
            $stmt3->execute();

        }

        // Execute the statement
        $username = $data->userCreated;
        $flow = new FlowStatuses($username);
        $chronology = new Chronology();
        $changelog = new Changelog($username);

        // next status
        $flwStatus = 5;


        $result = $flow->goToNextFlow($systemId, "operation", $data->applType, Steps: $flwStatus, apiToken: $data->token);

        if ($result) {
            // NOTE - Assign task
            $assignment = $taskAssignment->create($systemId, $flwStatus);
            // NOTE - Insert chronology create
            $chronoId = $chronology->create($username, $systemId, 0, 'file');
            // NOTE - Insert Project Changelog
            $details = 'Permohonan Telah Dikemaskini';
            if ($changelog->projectActivity($systemId, $flwStatus, $details)) {
                // $timeConverted = Utilities::convertDateToMalay($data->submittedAt);
                // declare telegram notification string
                // $message = "Maklumat Permohonan telah dikemaskini oleh pemohon. Sila semak maklumat terkini bagi permohonan ini. \n\n<strong>🔗 No Rujukan : " . $data->subId . " \n📝 Tajuk : " . $data->title . " \n📆 Tarikh Kemaskini : " . Utilities::convertDateToMalay($data->submittedAt) . "</strong>";
                $message = $telegram->getMessageByFlow($flwStatus, systemId: $systemId, item: $data->title, date: Utilities::convertDateToMalay($data->submittedAt));

                // NOTE - send telegram notification
                if ($telegram->sendMessage('department', 'registration', $message, 'html')) {
                    $telegram->sendMessage('group', 'management', $message, 'html');
                    $successCounter++;
                };

            };

            // NOTE - Insert notes create
            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

        }

        // NOTE - get attachment data
        // Execute the statement
        if (isset($data->attachments)) {
            foreach ($data->attachments as $key => $attachment) {
                $url = $attachment->url;
                $mime = $attachment->mime;
                $name = $attachment->name;
                $size = $attachment->size;
                $type = $key;
                $userAdded = $data->userCreated;
                $statusId = 1;

                $stmt = $conn->prepare("UPDATE flw_appl_attachments
                        SET name = :name,
                            url = :url,
                            attachment_date = :applyDate,
                            created_date = :timestamp,
                            user_added = :userAdded,
                            mime_type = :mime,
                            size = :size,
                            chronology_id = :chronoId
                        WHERE system_id = :systemId
                          AND attachment_type = :type");

                if (
                    $stmt->execute([
                        ':systemId' => $systemId,
                        ':url' => $url,
                        ':mime' => $mime,
                        ':name' => $name,
                        ':size' => $size,
                        ':applyDate' => $data->submittedAt,
                        ':timestamp' => $timestamp,
                        ':type' => $type,
                        ':userAdded' => $userAdded,
                        ':chronoId' => $chronoId,
                    ])
                ) {
                    $successCounter++;
                }
                ;
            }

        }

        // $roleId = 2;
        // $assignment = $taskAssignment->create($roleId, $systemId, $flwStatus);

        if ($successCounter >= 6 && $assignment) {
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
}