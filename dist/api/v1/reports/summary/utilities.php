<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// import connection and api setup
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/header.php";
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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['item'])) {
        if ($_GET['item'] == "guestList") {
            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Execute a SELECT query on the database
            // Get the contact signature list
            $query = "SELECT flw_appl_reports.signature_id
            FROM flw_appl_reports
            WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':sid', $_GET['sid'], PDO::PARAM_STR);
            $stmt->bindValue(':rn', $_GET['rn'], PDO::PARAM_STR);
            $stmt->bindValue(':aid', $_GET['aid'], PDO::PARAM_STR);
            $stmt->execute();
            $signatureIdStr = $stmt->fetchColumn();

            // convert into array
            $signatureIdArray = array_map('intval',explode(',', str_replace(array('{', '}'), '', $signatureIdStr)));

            // get the contact details
            $guestDetails = [];
            foreach ($signatureIdArray as $signatureId) {
                // TODO: this query can be simplified using where in condition on id with sets of signatureId
                $query = "SELECT * FROM view_signature WHERE id = :signatureId AND sign_type NOT IN (1, 2, 3);";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':signatureId', $signatureId, PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $guestDetails[] = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }

            // var_dump($guestDetails);

            // Close the database connection
            $conn = null;

            // Convert the result to a JSON string
            $json = json_encode($guestDetails);

            // Return the JSON string to the client
            echo $json;

        } else if ($_GET['item'] == "roadList") {
            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Execute a SELECT query on the database
            // Get the contact signature list
            $query = "SELECT flw_appl_reports.road_involved_id
            FROM flw_appl_reports
            WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':sid', $_GET['sid'], PDO::PARAM_STR);
            $stmt->bindValue(':rn', $_GET['rn'], PDO::PARAM_STR);
            $stmt->bindValue(':aid', $_GET['aid'], PDO::PARAM_STR);
            $stmt->execute();
            $roadIdStr = $stmt->fetchColumn();

            // convert into array
            $roadIdArray = array_map('intval',explode(',', str_replace(array('{', '}'), '', $roadIdStr)));

            // var_dump($roadIdArray);

            // get the contact details
            $roadDetails = [];
            foreach ($roadIdArray as $roadId) {
                $query = "SELECT * FROM flw_pkd_roads WHERE id = :roadId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':roadId', $roadId, PDO::PARAM_STR);
                $stmt->execute();
                if ( $stmt->rowCount() > 0) {
                    $roadDetails[] = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }

            foreach ($roadDetails as &$roadDetail) {
                // Extract the integers from the "method" value
                if (isset($roadDetail['method'])) {
                    $methodStr = $roadDetail['method'];
                    $methodStr = str_replace(array('{', '}'), '', $methodStr); // Remove curly braces from the string
                    $methodArray = array_map('intval', explode(',', $methodStr));

                    // Replace the "method" value with the array of integers
                    $roadDetail['method'] = $methodArray;
                }
            }

            // Close the database connection
            $conn = null;

            // Convert the result to a JSON string
            $json = json_encode($roadDetails);

            // Return the JSON string to the client
            echo $json;

        } else if ($_GET['item'] == "reportSign") {
            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Execute a SELECT query on the database
            // Get the contact signature list
            $query = "SELECT flw_appl_reports.signature_id
            FROM flw_appl_reports
            WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid";

            $stmt = $conn->prepare($query);
            $stmt->bindValue(':sid', $_GET['sid'], PDO::PARAM_STR);
            $stmt->bindValue(':rn', $_GET['rn'], PDO::PARAM_STR);
            $stmt->bindValue(':aid', $_GET['aid'], PDO::PARAM_STR);
            $stmt->execute();
            $signatureIdStr = $stmt->fetchColumn();

            // convert into array
            $signatureIdArray = array_map('intval',explode(',', str_replace(array('{', '}'), '', $signatureIdStr)));

            // get the contact details
            $reportSign = [];
            foreach ($signatureIdArray as $signatureId) {
                // TODO: this query can be simplified using where in condition on id with sets of signatureId
                $query = "SELECT * FROM view_signature WHERE id = :signatureId AND sign_type = :signType;";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':signatureId', $signatureId, PDO::PARAM_STR);
                $stmt->bindParam(':signType', $_GET['signType'], PDO::PARAM_STR);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    $reportSign[] = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }

            // Close the database connection
            $conn = null;

            // Convert the result to a JSON string
            if (isset($reportSign[0])) {
                $json = json_encode($reportSign[0]);
            } else {
                $json = json_encode(array(
                    'available' => 'none'
                ));
            }

            // Return the JSON string to the client
            echo $json;

        } else {
            echo "Invalid Parameter!!!";
        }
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $POST = json_decode($data, true);

    if (isset($POST['item'])) {
        if ($POST['item'] == 'road-list') {
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // echo json_encode($POST);
            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            // process all of the road submitted
            foreach ($POST['data'] as $roadDetails) {
                if ($roadDetails['road-id'] == '') {
                    // controller for new road added

                    // convert road-method value to match database format
                    $methodInStr = '{' . implode(',', $roadDetails['road-method']) . '}';

                    $query = "INSERT INTO flw_pkd_roads (system_id, authority, road_name, road_length, method, created_at, created_by) VALUES (:systemId, :authority, :roadName, :roadLength, :method, :timestamp, :created_by) RETURNING *";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
                    $stmt->bindParam(':authority', $authId, PDO::PARAM_INT);
                    $stmt->bindParam(':roadName', $roadDetails['road-name'], PDO::PARAM_INT);
                    $stmt->bindParam(':roadLength', $roadDetails['road-length'], PDO::PARAM_INT);
                    $stmt->bindParam(':method', $methodInStr, PDO::PARAM_INT);
                    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                    $stmt->bindParam(':created_by', $username);
                    $stmt->execute();
                    $processedData[] = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    // controller for road with ready data in database

                    // convert road-method value to match database format
                    $methodInStr = '{' . implode(',', $roadDetails['road-method']) . '}';

                    $query = "UPDATE flw_pkd_roads SET road_name = :roadName, road_length = :roadLength, method = :method, updated_at = :timestamp WHERE id = :roadId RETURNING *";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':roadId', $roadDetails['road-id'], PDO::PARAM_INT); //
                    $stmt->bindParam(':roadName', $roadDetails['road-name'], PDO::PARAM_INT);
                    $stmt->bindParam(':roadLength', $roadDetails['road-length'], PDO::PARAM_INT);
                    $stmt->bindParam(':method', $methodInStr, PDO::PARAM_INT);
                    $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                    $stmt->execute();
                    $processedData[] = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            // Extract the "id" values from each sub-array of processedData
            $processedDataId = array_map(function ($item) {
                return $item['id'];
            }, $processedData);

            // Convert the array of "id" values to a string
            $roadListIdStr = '{' . implode(',', $processedDataId) . '}';

            // update roadinvolved list in flw_appl_reports
            $query = "UPDATE flw_appl_reports SET road_involved_id = :roadInvolvedId, updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':roadInvolvedId', $roadListIdStr, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            $conn = null;

            // var_dump($roadListIdStr);
            // json_encode
            $json = array(
                "message" => 'Maklumat Jalan Berjaya Disimpan.',
                "data" => $processedData,
            );

            echo json_encode($json);
        } else if ($POST['item'] == 'road-list-single') {
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // echo json_encode($POST);
            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            // process all of the road submitted
            $roadDetails = $POST['data'];
            if ($roadDetails['road-id'] == '') {
                // controller for new road added

                // convert road-method value to match database format
                $methodInStr = '{' . implode(',', $roadDetails['road-method']) . '}';

                $query = "INSERT INTO flw_pkd_roads (system_id, authority, road_name, road_length, method, created_at) VALUES (:systemId, :authority, :roadName, :roadLength, :method, :timestamp) RETURNING *";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
                $stmt->bindParam(':authority', $authId, PDO::PARAM_INT);
                $stmt->bindParam(':roadName', $roadDetails['road-name'], PDO::PARAM_INT);
                $stmt->bindParam(':roadLength', $roadDetails['road-length'], PDO::PARAM_INT);
                $stmt->bindParam(':method', $methodInStr, PDO::PARAM_INT);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                $stmt->execute();
                $processedData = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                // controller for road with ready data in database

                // convert road-method value to match database format
                $methodInStr = '{' . implode(',', $roadDetails['road-method']) . '}';

                $query = "UPDATE flw_pkd_roads SET road_name = :roadName, road_length = :roadLength, method = :method, updated_at = :timestamp WHERE id = :roadId RETURNING *";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':roadId', $roadDetails['road-id'], PDO::PARAM_INT); //
                $stmt->bindParam(':roadName', $roadDetails['road-name'], PDO::PARAM_INT);
                $stmt->bindParam(':roadLength', $roadDetails['road-length'], PDO::PARAM_INT);
                $stmt->bindParam(':method', $methodInStr, PDO::PARAM_INT);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                $stmt->execute();
                $processedData = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Extract the "id" values from each sub-array of processedData
            $processedDataId = $processedData['id'];

            // update roadinvolved list in flw_appl_reports
            $query = "UPDATE flw_appl_reports SET road_involved_id = COALESCE(road_involved_id, '{}'::integer[]) || ARRAY[:roadInvolvedId]::integer[], updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo AND NOT (road_involved_id @> ARRAY[:roadInvolvedId]::integer[]);";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':roadInvolvedId', $processedDataId, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            $conn = null;

            // var_dump($roadListIdStr);
            // json_encode
            $json = array(
                "message" => 'Maklumat Jalan Berjaya Disimpan.',
                "data" => $processedData,
            );

            echo json_encode($json);
        } else if ($POST['item'] == 'guest-list') {
            // REVIEW - fakhri:: check if this still needed in future?
            // controller for updating guestlist as a whole
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            $sChecker = ""; // TODO: this variable used for development checking only

            // json_encode
            $json = array(
                "message" => 'Maklumat Kehadiran Berjaya Disimpan.',
                "data" => $POST,
            );

            // Close connection
            $conn = null;

            echo json_encode($json);
        } else if ($POST['item'] == 'guest-list-single') {
            // controller for updating single guestlist
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // echo json_encode($POST);
            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            $sChecker = ""; // TODO: this variable used for development checking only

            // process all of the road submitted
            $guestDetails = $POST['data'];
            if ($guestDetails['guest-signature-id'] == '') {
                // controller for new guest added

                // add contact to flw_appl_contacts table
                $query = "INSERT INTO flw_appl_contacts (system_id, full_name, company_name, position, added_at) VALUES (:systemId, :fullName, :companyName, :position, :timestamp) RETURNING *";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
                $stmt->bindParam(':fullName', $guestDetails['guest-name'], PDO::PARAM_INT);
                $stmt->bindParam(':companyName', $guestDetails['guest-company'], PDO::PARAM_INT);
                $stmt->bindParam(':position', $guestDetails['guest-position'], PDO::PARAM_INT);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                $stmt->execute();
                $processedData = $stmt->fetch(PDO::FETCH_ASSOC);

                // add added contact to the signature table
                $query = 'INSERT INTO flw_report_signature (contact_id, type) VALUES (:contactId, 4) RETURNING id';
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':contactId', $processedData['id'], PDO::PARAM_INT);
                $stmt->execute();
                $processedData['sign_id'] = $stmt->fetchColumn();

            } else {
                // controller for guest with ready data in database

                // update contact details
                $query = "UPDATE flw_appl_contacts SET full_name = :fullName, company_name = :companyName, position = :position, added_at = :timestamp WHERE id = :contactId RETURNING *";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':contactId', $guestDetails['guest-contact-id'], PDO::PARAM_INT);
                $stmt->bindParam(':fullName', $guestDetails['guest-name'], PDO::PARAM_INT);
                $stmt->bindParam(':companyName', $guestDetails['guest-company'], PDO::PARAM_INT);
                $stmt->bindParam(':position', $guestDetails['guest-position'], PDO::PARAM_INT);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                $stmt->execute();
                $processedData = $stmt->fetch(PDO::FETCH_ASSOC);

                // assign signature id to the processedData
                $processedData['sign_id'] = $guestDetails['guest-signature-id'];

            }

            // Extract the "id" values from each sub-array of processedData
            $processedDataId = $processedData['sign_id'];

            // update signature list in flw_appl_reports
            $query = "UPDATE flw_appl_reports SET signature_id = COALESCE(signature_id, '{}'::integer[]) || ARRAY[:signatureId]::integer[], updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo AND NOT (signature_id @> ARRAY[:signatureId]::integer[]);";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':signatureId', $processedDataId, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            $conn = null;

            // json_encode
            $json = array(
                "message" => 'Maklumat Kehadiran Berjaya Disimpan.',
                "data" => $processedData,
            );

            echo json_encode($json);
        } else if ($POST['item'] == 'report-signature') {
            // controller for updating single guestlist
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // echo json_encode($POST);
            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            // controller for new guest added

            $signatureData = $POST['data'];
            // add contact to flw_appl_contacts table
            $query = "INSERT INTO flw_appl_contacts (system_id, full_name, position, added_at) VALUES (:systemId, :fullName, :position, :timestamp) RETURNING *";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':fullName', $signatureData['name'], PDO::PARAM_INT);
            $stmt->bindParam(':position', $signatureData['position'], PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
            $stmt->execute();
            $processedData = $stmt->fetch(PDO::FETCH_ASSOC);

            // add added contact to the signature table
            $query = 'INSERT INTO flw_report_signature (contact_id, signature, type, signed_timestamp) VALUES (:contactId, :signature, :type, :timestamp) RETURNING id';
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':contactId', $processedData['id'], PDO::PARAM_INT);
            $stmt->bindParam(':signature', $signatureData['signImg'], PDO::PARAM_INT);
            $stmt->bindParam(':type', $signatureData['signType'], PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
            $stmt->execute();
            $processedData['sign_id'] = $stmt->fetchColumn();

            // Extract the "id" values from each sub-array of processedData
            $processedDataId = $processedData['sign_id'];

            // update signature list in flw_appl_reports
            $query = "UPDATE flw_appl_reports SET signature_id = COALESCE(signature_id, '{}'::integer[]) || ARRAY[:signatureId]::integer[], updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo AND NOT (signature_id @> ARRAY[:signatureId]::integer[]);";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':signatureId', $processedDataId, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            $conn = null;

            // json_encode
            $json = array(
                "message" => 'Tandatangan Berjaya Disimpan.',
                "data" => $processedData,
            );

            echo json_encode($json);
        } else if ($POST['item'] == 'report-signature-img') {
            // controller for updating single guestlist
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // echo json_encode($POST);
            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            // controller for new guest added

            $signatureData = $POST['data'];

            // update signature list in flw_appl_reports
            $query = "UPDATE flw_report_signature SET signature = :signatureData, signed_timestamp = :timestamp WHERE id = :signatureId RETURNING signature, signed_timestamp;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':signatureData', $signatureData['signImg'], PDO::PARAM_STR);
            $stmt->bindParam(':signatureId', $signatureData['signId'], PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
            $stmt->execute();
            $processedData = $stmt->fetch(PDO::FETCH_ASSOC);

            $conn = null;

            // json_encode
            $json = array(
                "message" => 'Tandatangan Berjaya Disimpan.',
                "data" => $processedData,
            );

            echo json_encode($json);
        } else if ($POST['item'] == 'amend-list-single') {
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // echo json_encode($POST);
            // declare processed roadData to be return as response
            $processedData = [];
            $timestamp = date('Y-m-d H:i:s', time());

            // process all of the road submitted
            $amendDetails = $POST['data'];
            if ($amendDetails['amend-id'] == '') {
                // controller for new road added

                $query = "INSERT INTO flw_report_amendment (system_id, proposed_details, amend_details, road_name, created_at) VALUES (:systemId, :originalDetails, :amendDetails, :roadName, :timestamp) RETURNING *";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
                $stmt->bindParam(':originalDetails', $amendDetails['amend-original'], PDO::PARAM_STR);
                $stmt->bindParam(':amendDetails', $amendDetails['amend-proposed'], PDO::PARAM_STR);
                $stmt->bindParam(':roadName', $amendDetails['amend-road-name'], PDO::PARAM_STR);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                $stmt->execute();
                $processedData = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                // controller for road with ready data in database

                $query = "UPDATE flw_report_amendment SET road_name = :roadName, proposed_details = :proposedDetails, amend_details = :amendDetails, updated_at = :timestamp WHERE id = :amendId RETURNING *";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':amendId', $amendDetails['amend-id'], PDO::PARAM_INT); //
                $stmt->bindParam(':roadName', $amendDetails['amend-road-name'], PDO::PARAM_STR);
                $stmt->bindParam(':proposedDetails', $amendDetails['amend-original'], PDO::PARAM_STR);
                $stmt->bindParam(':amendDetails', $amendDetails['amend-proposed'], PDO::PARAM_STR);
                $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
                $stmt->execute();
                $processedData = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Extract the "id" values from each sub-array of processedData
            $processedDataId = $processedData['id'];

            // update roadinvolved list in flw_appl_reports
            $query = "UPDATE flw_appl_reports SET amend_id = COALESCE(amend_id, '{}'::integer[]) || ARRAY[:amendId]::integer[], updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo AND NOT (amend_id @> ARRAY[:amendId]::integer[]);";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':amendId', $processedDataId, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            $conn = null;

            // var_dump($roadListIdStr);
            // json_encode
            $json = array(
                "message" => 'Maklumat Pindaan Berjaya Disimpan.',
                "data" => $processedData,
            );

            echo json_encode($json);
        } else if ($POST['item'] == 'summary-submit') {
            // submit api here
            $systemId = $POST['sid'];
            $reportNo = $POST['rn'];
            $authId = $POST['aid'];
            $timestamp = date('Y-m-d H:i:s', time());

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // assign data to be processed
            $requestData = $POST['data'];
            $notes = $requestData['actionNotes'];

            // checklist check
            $amendPlan = 0;
            $pilExclusion = 0;
            $privateRoad = 0;
            $overLapping = 0;
            $overlapDetails = "";
            if (isset($requestData['amend-plan'])) {
                $amendPlan = 1;
            }
            if (isset($requestData['wl-exclusion'])) {
                $pilExclusion = 1;
            }
            if (isset($requestData['overlapping'])) {
                $overLapping = 1;
                $overlapDetails = isset($requestData['overlapDetails'])?$requestData['overlapDetails']:'';
            }
            if (isset($requestData['private-road'])) {
                $privateRoad = 1;
            }

            // update checklist data
            $query = "UPDATE flw_appl_reports SET wl_exclusion_status = :exclusionStatus, amend_status = :amendPlan, overlap_status = :overLapping, overlap_details = :overlapDetails, private_road_involve = :privateRoad, updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo RETURNING id, report_trigger_id, road_involved_id;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':exclusionStatus', $pilExclusion, PDO::PARAM_INT);
            $stmt->bindParam(':amendPlan', $amendPlan, PDO::PARAM_INT);
            $stmt->bindParam(':overLapping', $overLapping, PDO::PARAM_INT);
            $stmt->bindParam(':privateRoad', $privateRoad, PDO::PARAM_INT);
            $stmt->bindParam(':overlapDetails', $overlapDetails, PDO::PARAM_STR);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            // Retrieve the updated data (for PostgreSQL or similar databases)
            $updatedData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Extract the specific values if needed
            $reportId = $updatedData['id'];
            $reportTriggerId = $updatedData['report_trigger_id'];
            $roadInvolvedId = $updatedData['road_involved_id'];

            $status = 13;
            $flwStatus = 15;
            // NOTE - Update Task Assignment
            $taskAssignment->complete($username, $systemId, $status, $authId);

            $referenceNo = $conn->prepare('SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId');
            $referenceNo->bindParam(':systemId', $systemId);
            $referenceNo->execute();
            $refNo = $referenceNo->fetchColumn();

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah menghantar laporan lawatan tapak bagi' . $refNo;
            $pages = 'task_operation';
            $changelog->userActivity($text, $pages);

            // NOTE - current status = 13
            //nextstep for finance
            $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", $authId, Steps: $flwStatus);

            // ANCHOR - Check LTA finished or not (Check all authority flow done before using main flow) #need to do after goToNextFlow
            if ($flow->CheckAllComplete($systemId, $NextStatus)) {
                $taskAssignment->create($systemId, $flwStatus);
            }

            $details = 'Laporan Lawatan Tapak telah disediakan bagi ' . $refNo . 'Nota: ' . $notes;
            // NOTE - Insert Project Changelog
            if ($changelog->projectActivity($systemId, $status, $details)) {
                // declare telegram notification string
                // $telegramMsg = "Laporan Lawatan Tapak telah disediakan. \n\n<strong>🔗 No Rujukan : " . $refNo . "</strong>";
                $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                // NOTE - send telegram notification for team charting
                $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');

            };

            $chronoId = $chronology->create($username, $systemId, $status, 'notes', $authId);

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, username, chronology_id ) VALUES (:systemId, :details, :created, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $details);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            // // insert into flw_appl_notes
            // $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authorityId)");
            // $stmtNote->bindParam(':systemId', $systemId);
            // $stmtNote->bindParam(':notes', $notes);
            // $stmtNote->bindParam(':created', $timestamp);
            // $stmtNote->bindParam(':authorityId', $authId);
            // $stmtNote->execute();

            // // Update status_appl_reports entry by $reportTriggerId
            // $query = "UPDATE status_appl_reports SET report_submit = :submitStatus, submit_date = :timestamp WHERE id = :reportTriggerId;";
            // $stmt = $conn->prepare($query);
            // $stmt->bindValue(':submitStatus', 1, PDO::PARAM_INT);
            // $stmt->bindValue(':reportTriggerId', $reportTriggerId, PDO::PARAM_INT);
            // $stmt->bindValue(':timestamp', $timestamp, PDO::PARAM_INT);
            // $stmt->execute();

            // // Check if the entry already exists
            // $checkQuery = "SELECT COUNT(*) FROM public.ctrl_authorities WHERE system_id = :systemId AND flw_appl_report_id = :reportId AND authority_id = :authorityId";
            // $checkStmt = $conn->prepare($checkQuery);
            // $checkStmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
            // $checkStmt->bindParam(':reportId', $reportId, PDO::PARAM_INT);
            // $checkStmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            // $checkStmt->execute();

            // if ($checkStmt->fetchColumn() == 0) {
            //     $authorityStatus = 14;
            //     // Entry does not exist, so insert new authority table entry
            //     $insertQuery = "INSERT INTO public.ctrl_authorities (system_id, flw_appl_report_id, road_involved, authority_id, authority_status, created_at) VALUES (:systemId, :reportId, :roadInvolved, :authorityId, :authorityStatus, :timestamp)";
            //     $insertStmt = $conn->prepare($insertQuery);
            //     $insertStmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
            //     $insertStmt->bindParam(':reportId', $reportId, PDO::PARAM_INT);
            //     $insertStmt->bindParam(':roadInvolved', $roadInvolvedId, PDO::PARAM_STR);
            //     $insertStmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            //     $insertStmt->bindParam(':authorityStatus', $authorityStatus, PDO::PARAM_INT);
            //     $insertStmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
            //     $insertStmt->execute();
            // }

            // // check if all status_appl_reports are submitted by system_id
            // $query = "SELECT status_appl_reports.report_submit, flw_appl_reports.authority_id FROM status_appl_reports LEFT JOIN flw_appl_reports ON flw_appl_reports.report_trigger_id = status_appl_reports.id WHERE status_appl_reports.system_id = :systemId;";
            // $stmt = $conn->prepare($query);
            // $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
            // $stmt->execute();
            // $reportSubmitCheck = $stmt->fetchAll();

            // $allSubmitted = true; // Assume all are submitted

            // foreach ($reportSubmitCheck as $row) {
            //     if ($row['report_submit'] != 1) {
            //         $allSubmitted = false; // If any value is not 1, set to false
            //         break; // No need to continue checking
            //     }
            // }

            // $reportSubmitStatus = $allSubmitted; // Set the final status

            // // change the project status if all of the reports are submitted
            // if ($reportSubmitStatus) {

            //     // get all authority for current systemId
            //     $query = "SELECT id FROM ctrl_authorities WHERE system_id = :systemId";
            //     $stmt = $conn->prepare($query);
            //     $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
            //     $stmt->execute();
            //     $flwAuthorities = $stmt->fetchAll();

            //     $authorityStatus = 15;
            //     foreach ($flwAuthorities as $row) {
            //         $query = "UPDATE ctrl_authorities SET authority_status = :authorityStatus WHERE id = :authorityId";
            //         $stmt = $conn->prepare($query);
            //         $stmt->bindParam(':authorityStatus', $authorityStatus);
            //         $stmt->bindParam(':authorityId', $row['id']);
            //         $stmt->execute();
            //     }

            //     $projectStatus = getApplMainStatus($systemId);
            //     $projectStatusNew = 15;
            //     $statusUpdateResult = updateProjectStatus($conn, $systemId, $projectStatus, $projectStatusNew);

            //     // TODO: Update changelog table
            //     // set initial status of changeLogUpdateResult
            //     $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

            //     // Update changelog if status update successful
            //     if ($statusUpdateResult['result'] == true) {
            //         // update changelog for all the authority
            //         $changeLogCount = 0;
            //         foreach ($reportSubmitCheck as $row) {
            //             $changeLogUpdateResult = insertSysRecordChangelog($conn, $systemId, $notes, $statusUpdateResult['new_status'], $row['authority_id'], $changeLogCount);
            //         }
            //     }
            // }

            $conn = null; // close the pdo connection

            $response = array(
                "message" => 'Laporan Berjaya Dihantar.',
                "data" => array(
                    "allSubmitted" => false,
                    "systemId" => $systemId,
                    "reportNo" => $reportNo,
                    "authId" => $authId
                ),
                "check" => $requestData
            );

            echo json_encode($response);
        }
    }

}
else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $PUT = json_decode($data, true);

    // Connect to the database using PDO
    $conn = $db->createConnection();

    if (isset($PUT['item'])) {
        if ($PUT['item'] == "amendcheck") {
            // Get data to be updated
            $amendData = $PUT['data'];
            $systemId = $amendData['systemId'];
            $reportNo = $amendData['reportNo'];
            $authorityId = $amendData['authorityId'];
            $amendResetStatus = $amendData['amendReset'];

            // sv review
            $reviewCheckedIds = $amendData['reviewCheckedIds'];
            $reviewUncheckedIds = $amendData['reviewUncheckedIds'];
            // plot information to be updated to extercord
            $amendReview = [];

            // appl_info details
            $appCheckedVal = $amendData['appCheckedVal'];
            $appUncheckedVal = $amendData['appUncheckedVal'];
            $appInfoNote = $amendData['appInfoNote'];
            $appInfoSysNote = "Terdapat Pindaan pada bahagian Maklumat Permohonan.";
            // plot information following extercord setup
            $steps1 = ["title"=>false, "provider"=>false, "info"=>false, "attachment"=>false, "notes"=>$appInfoSysNote." Nota: ".$appInfoNote];
            foreach($appCheckedVal as $checked) {
                if ($checked == "title") {
                    $steps1["title"] = true;
                }
                if ($checked == "provider") {
                    $steps1["provider"] = true;
                }
                if ($checked == "info") {
                    $steps1["info"] = true;
                }
                if ($checked == "appl_letter") {
                    $steps1["attachment"] = true;
                }
            }

            // officer details
            $officerCheckedVal = $amendData['officerCheckedVal'];
            $officerUncheckedVal = $amendData['officerUncheckedVal'];
            $officerInfoNote = $amendData['officerInfoNote'];
            $officerInfoSysNote = "Terdapat Pindaan pada bahagian Maklumat Pegawai.";
            // plot information following extercord setup
            $steps2 = ["applicant"=>false, "officer"=>false, "attachment"=>false, "notes"=>$officerInfoSysNote." Nota: ".$officerInfoNote];
            foreach($officerCheckedVal as $checked) {
                if ($checked == "applicant") {
                    $steps2["applicant"] = true;
                }
                if ($checked == "officer") {
                    $steps2["officer"] = true;
                }
                if ($checked == "ack_letter") {
                    $steps2["attachment"] = true;
                }
            }

            // route details
            $routeCheckedVal = $amendData['routeCheckedVal'];
            $routeUncheckedVal = $amendData['routeUncheckedVal'];
            $routeInfoNote = $amendData['routeInfoNote'];
            $routeInfoSysNote = "Terdapat Pindaan pada bahagian Maklumat Jalan.";
            // plot information following extercord setup
            $steps3 = ["district"=>false, "road"=>false, "attachment_1"=>false, "attachment_2"=>false, "notes"=>$routeInfoSysNote." Nota: ".$routeInfoNote];
            foreach($routeCheckedVal as $checked) {
                if ($checked == "district") {
                    $steps3["district"] = true;
                }
                if ($checked == "road") {
                    $steps3["road"] = true;
                }
                if ($checked == "doc_technical") {
                    $steps3["attachment_1"] = true;
                }
                if ($checked == "plan_location") {
                    $steps3["attachment_2"] = true;
                }
            }

            $amendReviewCounter = 0;
            foreach ($reviewCheckedIds as $checked) {
                // Update the geom entry
                $query = "UPDATE geom_site_visit SET amend = true WHERE id = :id RETURNING description";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $checked, PDO::PARAM_INT);
                $stmt->execute();
                $amendReview[$amendReviewCounter] = $stmt->fetchColumn();
                $amendReviewCounter++;
            }

            foreach ($reviewUncheckedIds as $unchecked) {
                // Update the geom entry
                $query = "UPDATE geom_site_visit SET amend = false WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $unchecked, PDO::PARAM_INT);
                $stmt->execute();
            }

            // empty the active amend column if reset trigger
            if ($amendResetStatus) {
                $query = "UPDATE flw_appl_reports SET active_amend_notes = NULL WHERE system_id = :systemId AND report_no = :reportNo AND authority_id = :authorityId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
                $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_STR);
                $stmt->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // record amendments details
                $jsonAmendData = json_encode(array("amendments" => [["amend_review"=> $amendReview],["steps"=>1,"items"=>$steps1],["steps"=>2,"items"=>$steps2],["steps"=>3,"items"=>$steps3]]));
                $query = "UPDATE flw_appl_reports SET active_amend_notes = :activeNotes WHERE system_id = :systemId AND report_no = :reportNo AND authority_id = :authorityId";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':activeNotes', $jsonAmendData, PDO::PARAM_STR);
                $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
                $stmt->bindParam(':reportNo', $reportNo, PDO::PARAM_STR);
                $stmt->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
                $stmt->execute();
            }

            echo json_encode(array(
                "message" => "Butiran Ulasan Pindaan berjaya dikemaskini.",
                "checker" => $amendData,
                "amendments" => [["amend_review"=> $amendReview],["steps"=>1,"items"=>$steps1],["steps"=>2,"items"=>$steps2],["steps"=>3,"items"=>$steps3]]
            ));
        }
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // controller for DELETE request
    if (isset($_GET['item'])) {
        if ($_GET['item'] == "guestList") {
            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Get the current Unix timestamp in milliseconds
            $timestamp = date('Y-m-d H:i:s', time());

            // Execute a SELECT query on the database
            $query = "UPDATE flw_appl_reports SET signature_id = array_remove(signature_id, :gid), updated_timestamp = :timestamp WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid RETURNING signature_id";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':gid', $_GET['gid'], PDO::PARAM_STR);
            $stmt->bindValue(':sid', $_GET['sid'], PDO::PARAM_STR);
            $stmt->bindValue(':rn', $_GET['rn'], PDO::PARAM_STR);
            $stmt->bindValue(':aid', $_GET['aid'], PDO::PARAM_STR);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();
            $signatureIdStr = $stmt->fetchColumn();

            // Close the database connection
            $conn = null;

            // Convert the result to a JSON string
            $json = json_encode(array(
                "message" => "Senarai Kehadiran Berjaya Dipadam.",
                "check" => $signatureIdStr
            ));

            // Return the JSON string to the client
            echo $json;

        }
    }
}
else {}