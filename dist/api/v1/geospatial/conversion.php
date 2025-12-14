<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
include_once "api/header.php";
require_once "api/functions.php";
require_once "config/DBFactory.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);
$db = new DBConnectionFactory();
$conn = $db->createConnection();
$telegram = new Telegram();
$system = new System;
$tenant = $system->App->tenant;

$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Check if the URL contains a parameter named
    if (isset($POST['action'])) {
        if ($POST['action'] == "gpkg") {
            if ($POST['type'] == "line") {
                $geom       = $POST['geom'];
                $systemId   = $POST['systemID'];
                $submitted  = date('Y-m-d H:i:s', time());
                $type = $POST['type'];
    
                // Prepare the query
                $checkVersion = "SELECT COUNT(DISTINCT node) as version FROM geom_gis_trace WHERE system_id = :systemID AND type = :gpkgType";
                $checkResult  = $conn->prepare($checkVersion);
    
                // Bind the parameters
                $checkResult->bindParam(':systemID', $systemId);
                $checkResult->bindParam(':gpkgType', $type);
    
                // Execute the query
                $checkResult->execute();
    
                if (!$checkResult) {
                    die("Error in query: " . $conn->errorInfo()[2]);
                }
    
                $row = $checkResult->fetch(PDO::FETCH_ASSOC);
    
                $versionNumber    = $row['version'];
                $dataversion      = $versionNumber + 1;
                $total            = 0;
                $rowToReturn      = [];
    
                foreach ($geom as $data) {
                    $length   = preg_replace('/\D/', '', $data['properties']['dist'] ?? $data['properties']['JRK']);
                    $method   = $data['properties']['jns'] ?? $data['properties']['JNS'];
                    $geometry = json_encode($data['geometry']);
                    $total    += $length;
    
                    // Prepare the query
                    $query = "INSERT INTO geom_gis_trace (geom, system_id, drawn_by, created_at, node, revision, length, method, type) VALUES (ST_SetSRID(ST_GeomFromGeoJSON(:geometry), 4326), :systemID, :username, :submitted, :dataversion, :versionNumber, :length, :method, :type)";
                    $stmt  = $conn->prepare($query);
    
                    // Bind the parameters
                    $stmt->bindParam(':geometry', $geometry);
                    $stmt->bindParam(':systemID', $systemId);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':submitted', $submitted);
                    $stmt->bindParam(':dataversion', $dataversion);
                    $stmt->bindParam(':versionNumber', $versionNumber);
                    $stmt->bindParam(':length', $length);
                    $stmt->bindParam(':method', $method);
                    $stmt->bindParam(':type', $type);
    
                    // Execute the query
                    $stmt->execute();
                }
    
                if (!$stmt) {
                    die("Error in query: " . $conn->errorInfo()[2]);
                }
    
                // Finally, return a JSON
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "length"    => $total,
                    "revision"  => $versionNumber,
                    "line"      => $geom
                ]);
    
                // Close the database connection
                $conn = null;
    
            } else if ($POST['type'] == "point") {
                $geom       = $POST['geom'];
                $systemId   = $POST['systemID'];
                $submitted  = date('Y-m-d H:i:s', time());
                $type = $POST['type'];
        
                // Prepare the query
                $checkVersion = "SELECT COUNT(DISTINCT node) as version FROM geom_gis_trace WHERE system_id = :systemID AND type = :gpkgType";
                $checkResult  = $conn->prepare($checkVersion);
        
                // Bind the parameters
                $checkResult->bindParam(':systemID', $systemId);
                $checkResult->bindParam(':gpkgType', $type);
        
                // Execute the query
                $checkResult->execute();
        
                if (!$checkResult) {
                    die("Error in query: " . $conn->errorInfo()[2]);
                }
        
                $row = $checkResult->fetch(PDO::FETCH_ASSOC);
        
                $versionNumber    = $row['version'];
                $dataversion      = $versionNumber + 1;
                $total            = 0;
                $rowToReturn      = [];
        
                foreach ($geom as $data) {
                    $method   = $data['properties']['jns'] ?? $data['properties']['JNS'];
                    $geometry = json_encode($data['geometry']);
                    // $total    += $length;
                    $name   = $data['properties']['name'] ?? $data['properties']['NAME'];
        
                    // Prepare the query
                    $query = "INSERT INTO geom_gis_trace (geom, system_id, drawn_by, created_at, node, revision, method, type, name) VALUES (ST_SetSRID(ST_GeomFromGeoJSON(:geometry), 4326), :systemID, :username, :submitted, :dataversion, :versionNumber, :method, :type, :name)";
                    $stmt  = $conn->prepare($query);
        
                    // Bind the parameters
                    $stmt->bindParam(':geometry', $geometry);
                    $stmt->bindParam(':systemID', $systemId);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':submitted', $submitted);
                    $stmt->bindParam(':dataversion', $dataversion);
                    $stmt->bindParam(':versionNumber', $versionNumber);
                    $stmt->bindParam(':method', $method);
                    $stmt->bindParam(':type', $type);
                    $stmt->bindParam(':name', $name);
        
                    // Execute the query
                    $stmt->execute();
                }
        
                if (!$stmt) {
                    die("Error in query: " . $conn->errorInfo()[2]);
                }
        
                // Finally, return a JSON
                header("HTTP/1.1 200 OK");
                echo json_encode([
                    "revision"  => $versionNumber,
                    "point"     => $geom
                ]);
        
                // Close the database connection
                $conn = null;
            }
    
        } elseif ($POST['action'] == "route-list") {
            $POST['message'] = "success";

            $route = $POST['gis_route_list'];
            $version = $POST['data-revision'];
            $notes = $POST['notes'];
            $systemId = $POST['sysId'];
            $timestamp = date('Y-m-d H:i:s', time());
            $authorityCol = Geospatial::extractArraySubkeyValues($route, 'authority');
            $authorityColUnique = array_unique($authorityCol);
            $districtCol = Geospatial::extractArraySubkeyValues($route, 'district');
            $districtColUnique = array_unique($districtCol);
            $densityCol = Geospatial::extractArraySubkeyValues($route, 'density');
            $density = Geospatial::calculateAverage($densityCol);
            $gisLength = $POST['gpkg-length'];

            $authId = '';
            if (is_array($authorityColUnique)) {
                // value is an array
                $int_array = array_map('intval', $authorityColUnique);
                $authId = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $authId = '{' . intval($authorityColUnique) . '}';
            }

            $districtId = '';
            if (is_array($districtColUnique)) {
                // value is an array
                $int_array = array_map('intval', $districtColUnique);
                $districtId = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $districtId = '{' . intval($districtColUnique) . '}';
            }

            // insert new entry to flw_app_road
            $newRoadId = [];
            foreach ($route as $key => $value) {

                if ($value['road-id'] == '') {
                    $coorS = Geospatial::extractCoordinate($value['coor_start']);
                    $coorE = Geospatial::extractCoordinate($value['coor_end']);
                    $query = "INSERT INTO flw_gis_roads (system_id, road_name, authority, created_at, latitude_start, longitude_start, latitude_end, longitude_end, gis_verify_date, created_by, districts) VALUES (:systemID, :name, :authority, :timestamp, :slat, :slong, :elat, :elong, :timestamp, :created_by, :districts) RETURNING id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemID', $systemId);
                    $stmt->bindParam(':name', $value['road_name']);
                    $stmt->bindParam(':authority', $value['authority']);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':slat', $coorS['lat']);
                    $stmt->bindParam(':slong', $coorS['lng']);
                    $stmt->bindParam(':elat', $coorE['lat']);
                    $stmt->bindParam(':elong', $coorE['lng']);
                    $stmt->bindParam(':created_by', $username);
                    $stmt->bindParam(':districts', $value['district']);
                    $stmt->execute();
                    $newRoadId[] = $stmt->fetchColumn();

                } else {
                    $coorS = Geospatial::extractCoordinate($value['coor_start']);
                    $coorE = Geospatial::extractCoordinate($value['coor_end']);

                    $query = "UPDATE flw_gis_roads SET road_name = :roadName, authority = :authority, updated_at = :timestamp, latitude_start = :slat, longitude_start = :slong, latitude_end = :elat, longitude_end = :elong, gis_verify_date = :timestamp, districts = :districts  WHERE id = :roadId RETURNING id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':roadId', $value['road-id'], PDO::PARAM_INT); //
                    $stmt->bindParam(':roadName', $value['road_name'], PDO::PARAM_INT);
                    $stmt->bindParam(':authority', $value['authority']);

                    $stmt->bindParam(':slat', $coorS['lat']);
                    $stmt->bindParam(':slong', $coorS['lng']);
                    $stmt->bindParam(':elat', $coorE['lat']);
                    $stmt->bindParam(':elong', $coorE['lng']);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':districts', $value['district']);
                    $stmt->execute();

                    $newRoadId[] = $stmt->fetchColumn();

                }
            }

            // TODO: update geom_gis_trace
            // Insert the item into the database using the item name value
            $query = "UPDATE geom_gis_trace SET authority = :authId, district = :districtId, state = :states, notes = :notes, updated_at = :updated, density = :density WHERE revision = :versions AND system_id = :systemID ";
            $stmt = $conn->prepare($query);
            // Bind parameter
            $stmt->bindValue(':authId', implode(",", (array) $authId), PDO::PARAM_STR);
            $stmt->bindValue(':districtId', implode(",", (array) $districtId), PDO::PARAM_STR);
            $stmt->bindParam(':states', $appState);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':updated', $timestamp);
            $stmt->bindParam(':density', $density);
            $stmt->bindParam(':versions', $version);
            $stmt->bindParam(':systemID', $systemId);
            // Execute the query
            $stmt->execute();

            // TODO: update flw_appl_verifies
            if (is_array($newRoadId)) {
                // value is an array
                $int_array = array_map('intval', $newRoadId);
                $newRoadIdStr = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $newRoadIdStr = '{' . intval($newRoadId) . '}';
            }

            $query = "UPDATE flw_appl_verifies SET gis_road_id = :newRoadId, gis_road_submit_date = :timestamp, gis_length = :gisLength WHERE system_id = :systemID";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':newRoadId', $newRoadIdStr);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':gisLength', $gisLength);
            $stmt->bindParam(':systemID', $systemId);
            $stmt->execute();

            // fetch reference_no
            $refNo = Utilities::getRefNo($systemId);
            // end select reference no from flw_appl_entries

            if ($version == 0) {
                //current status
                $status = 8;

                // NOTE - Update Task Assignment
                $taskAssignment->complete($username, $systemId, $status);

                $flwStatus = 10;
                $taskAssignment->create($systemId, $flwStatus);

                //Record Activity
                $text = 'Pengguna ' . $username . ' telah Muat Naik Pelan Cadangan Laluan bagi permohonan ' . $refNo;
                $pages = 'task_geospatial';
                $changelog->userActivity($text, $pages);

                // NOTE - current status = 8
                // NOTE - nextstep for geospatial
                $NextStatus = $flow->goToNextFlow($systemId, "geospatial", "BF");

                // NOTE - nextstep for PKD
                $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus, allRole: true);

                $details = 'Fail gpkg telah dimuat naik dan butiran jalan telah diisi bagi permohonan ' . $refNo . '. Nota: ' . (!empty($notes) ? $notes : '-tiada-');

                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {
                    // declare telegram notification string
                    $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                    $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                    $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
                    $telegramResponse2 = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
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
                http_response_code(200);
                $result = array(
                    "success" => true,
                    "message" => "success",
                    "systemID" => $systemId
                );


            } else if ($version > 0) {
                //current status
                $status = 8;

                // NOTE - Update Task Assignment
                $taskAssignment->complete($username, $systemId, $status);

                $flwStatus = 10;
                $taskAssignment->create($systemId, $flwStatus);

                $stmt = $conn->prepare('SELECT pcl_verify FROM flw_appl_verifies WHERE system_id = :systemID LIMIT 1');
                $stmt->bindParam(':systemID', $systemId);
                $stmt->execute();
                $record = $stmt->fetch(PDO::FETCH_OBJ);
                $pcl_verify = $record->pcl_verify;

                //Record Activity
                $text = 'Pengguna ' . $username . ' telah Muat Naik Pelan Cadangan Laluan bagi permohonan ' . $refNo;
                $pages = 'task_geospatial';
                $changelog->userActivity($text, $pages);

                // NOTE - current status = 8
                //nextstep for geospatial
                $NextStatus = $flow->goToNextFlow($systemId, "geospatial", "BF");

                // NOTE - nextstep for PKD
                $NextStatus = $flow->goToNextFlow($systemId, "operation", "BF", Steps: $flwStatus, allRole: true);

                $details = 'Fail gpkg telah dimuat naik dan butiran jalan telah diisi bagi permohonan ' . $refNo . '. Nota: ' . (!empty($notes) ? $notes : '-tiada-');

                // NOTE - Insert Project Changelog
                if ($changelog->projectActivity($systemId, $status, $details)) {
                    // declare telegram notification string
                    $telegramMsg = $telegram->getMessageByFlow($flwStatus, refNo: $refNo);
                    // NOTE - send telegram notification for team account
                    $telegramResponse = $telegram->sendMessage('group', 'management', $telegramMsg, 'html');
                    $telegramResponse = $telegram->sendMessage('flow', $flwStatus, $telegramMsg, 'html');
                    $telegramResponse2 = $telegram->sendMessage('role', 28, $telegramMsg, 'html');
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
                http_response_code(200);
                $result = array(
                    "success" => true,
                    "message" => "success",
                    "systemID" => $systemId
                );

            } else {
                // Finally, return a JSON
                http_response_code(500);
                $result = array(
                    "success" => false,
                    "message" => "failed",
                );
            }

            echo json_encode($result);
            // Close the database connection
            $conn = null;
        } elseif ($POST['action'] == "route-pil") {
            $POST['message'] = "success";

            $route = $POST['gis_route_list'];
            $notes = $POST['notes'];
            $systemId = $POST['sysId'];
            $timestamp = date('Y-m-d H:i:s', time());
            $authorityCol = Geospatial::extractArraySubkeyValues($route, 'authority');
            $authorityColUnique = array_unique($authorityCol);
            $districtCol = Geospatial::extractArraySubkeyValues($route, 'district');
            $districtColUnique = array_unique($districtCol);

            $authId = '';
            if (is_array($authorityColUnique)) {
                // value is an array
                $int_array = array_map('intval', $authorityColUnique);
                $authId = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $authId = '{' . intval($authorityColUnique) . '}';
            }

            $districtId = '';
            if (is_array($districtColUnique)) {
                // value is an array
                $int_array = array_map('intval', $districtColUnique);
                $districtId = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $districtId = '{' . intval($districtColUnique) . '}';
            }

            // insert new entry to flw_app_road
            $newRoadId = [];
            foreach ($route as $key => $value) {

                if ($value['road-id'] == '') {
                    $coorS = Geospatial::extractCoordinate($value['coor_start']);
                    $coorE = Geospatial::extractCoordinate($value['coor_end']);
                    $verified = true;

                    $query = "INSERT INTO flw_gis_roads (system_id, road_name, authority, created_at, latitude_start, longitude_start, latitude_end, longitude_end, gis_verify_date, created_by, districts, verified) VALUES (:systemID, :name, :authority, :timestamp, :slat, :slong, :elat, :elong, :timestamp, :created_by, :districts, :verified) RETURNING id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':systemID', $systemId);
                    $stmt->bindParam(':name', $value['road_name']);
                    $stmt->bindParam(':authority', $value['authority']);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':slat', $coorS['lat']);
                    $stmt->bindParam(':slong', $coorS['lng']);
                    $stmt->bindParam(':elat', $coorE['lat']);
                    $stmt->bindParam(':elong', $coorE['lng']);
                    $stmt->bindParam(':created_by', $username);
                    $stmt->bindParam(':districts', $value['district']);
                    $stmt->bindParam(':verified', $verified);
                    $stmt->execute();
                    $newRoadId[] = $stmt->fetchColumn();

                } else {
                    $coorS = Geospatial::extractCoordinate($value['coor_start']);
                    $coorE = Geospatial::extractCoordinate($value['coor_end']);
                    $verified = true;

                    $query = "UPDATE flw_gis_roads SET road_name = :roadName, authority = :authority, updated_at = :timestamp, latitude_start = :slat, longitude_start = :slong, latitude_end = :elat, longitude_end = :elong, gis_verify_date = :timestamp, districts = :districts , verified = :verified WHERE id = :roadId RETURNING id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':roadId', $value['road-id'], PDO::PARAM_INT);
                    $stmt->bindParam(':roadName', $value['road_name'], PDO::PARAM_INT);
                    $stmt->bindParam(':authority', $value['authority']);
                    $stmt->bindParam(':slat', $coorS['lat']);
                    $stmt->bindParam(':slong', $coorS['lng']);
                    $stmt->bindParam(':elat', $coorE['lat']);
                    $stmt->bindParam(':elong', $coorE['lng']);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':districts', $value['district']);
                    $stmt->bindParam(':verified', $verified);
                    $stmt->execute();

                    $newRoadId[] = $stmt->fetchColumn();

                }
            }

            // TODO: update flw_appl_verifies
            if (is_array($newRoadId)) {
                // value is an array
                $int_array = array_map('intval', $newRoadId);
                $newRoadIdStr = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $newRoadIdStr = '{' . intval($newRoadId) . '}';
            }

            $query = "UPDATE flw_appl_verifies SET gis_road_id = :newRoadId, gis_road_submit_date = :timestamp WHERE system_id = :systemID";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':newRoadId', $newRoadIdStr);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':systemID', $systemId);
            $stmt->execute();

            //toast
            $message = 'Butiran Jalan Berjaya Disimpan 🎉';

            // Finally, return a JSON
            http_response_code(200);
            echo json_encode([
                "message" => $message,
                "status" => 200,
                "systemId" => $systemId,
                "notes" => $notes
            ]);

            // echo json_encode($result);
            // Close the database connection
            $conn = null;
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($_GET['action'] == "route-entry") {

        $systemId = $_GET['systemId'];
        $timestamp = date('Y-m-d H:i:s', time());

        // var_dump($systemId);
        //create new road_id for road_involve based on gis_road
        $getRoadEntry = $conn->prepare("SELECT list_road_id FROM flw_appl_entries WHERE system_id = :systemId");
        $getRoadEntry->bindParam(':systemId', $systemId);
        $getRoadEntry->execute();
        $getRoadEntries = $getRoadEntry->fetch(PDO::FETCH_ASSOC);

        // var_dump($getRoadEntries);
        // Remove curly braces from the string
        $getRoadEntries = trim($getRoadEntries['list_road_id'], '{}');

        // Split the string into an array based on commas
        $getIdArray = explode(',', $getRoadEntries);

        // Convert each element to an integer
        $getRoadIdArray = array_map('intval', $getIdArray);

        $roadDetails = [];
        $newRoadIds = [];
        foreach ($getRoadIdArray as $roadId) {
            $getRoadData = $conn->prepare("SELECT * FROM flw_appl_roads WHERE id = :roadId");
            $getRoadData->bindParam(':roadId', $roadId);
            $getRoadData->execute();
            if ($getRoadData->rowCount() > 0) {
                $roadData = $getRoadData->fetch(PDO::FETCH_ASSOC);

                if ($roadData) {
                    $newRoad = $conn->prepare("INSERT INTO flw_gis_roads (system_id, road_name, created_at, latitude_start, longitude_start, latitude_end, longitude_end, gis_verify_date, created_by, districts, flw_appl_road_id) VALUES (:system_id, :road_name, :created_at,  :latitude_start, :longitude_start, :latitude_end, :longitude_end, :gis_verify_date, :created_by, :districts, :flw_appl_road_id)");

                    $newRoad->bindParam(':system_id', $roadData['system_id']);
                    $newRoad->bindParam(':road_name', $roadData['road_name']);
                    $newRoad->bindParam(':created_at', $timestamp);
                    $newRoad->bindParam(':latitude_start', $roadData['latitude_start']);
                    $newRoad->bindParam(':longitude_start', $roadData['longitude_start']);
                    $newRoad->bindParam(':latitude_end', $roadData['latitude_end']);
                    $newRoad->bindParam(':longitude_end', $roadData['longitude_end']);
                    $newRoad->bindParam(':gis_verify_date', $roadData['gis_verify_date']);
                    $newRoad->bindParam(':districts', $roadData['districts']);
                    $newRoad->bindParam(':created_by', $username);
                    $newRoad->bindParam(':flw_appl_road_id', $roadId);

                    $newRoad->execute();

                    $newRoadId = $conn->lastInsertId();
                    $newRoadIds[] = $newRoadId;
                }
            }
        }

        $newRoadIds = '{' . implode(', ', $newRoadIds) . '}';

        $query = "UPDATE flw_appl_verifies SET gis_road_id = :newRoadId WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':newRoadId', $newRoadIds);
        $stmt->bindParam(':systemID', $systemId);
        $stmt->execute();

        $getRoadGIS = $conn->prepare("SELECT gis_road_id FROM flw_appl_verifies WHERE system_id = :systemId");
        $getRoadGIS->bindParam(':systemId', $systemId);
        $getRoadGIS->execute();
        $getRoadDetails = $getRoadGIS->fetch(PDO::FETCH_ASSOC);

        // var_dump($getRoadEntries);
        // Remove curly braces from the string
        $getRoadDetails = trim($getRoadDetails['gis_road_id'], '{}');

        // Split the string into an array based on commas
        $getIdGISArray = explode(',', $getRoadDetails);

        // Convert each element to an integer
        $getRoadGISArray = array_map('intval', $getIdGISArray);

        foreach ($getRoadGISArray as $gisId) {
            $getRoadDataGIS = $conn->prepare("SELECT * FROM flw_gis_roads WHERE id = :roadId");
            $getRoadDataGIS->bindParam(':roadId', $gisId);
            $getRoadDataGIS->execute();
            if ($stmt->rowCount() > 0) {
                $roadDetails[] = $getRoadDataGIS->fetch(PDO::FETCH_ASSOC);
            }
        }

        http_response_code(200);
        echo json_encode(
            array(
                "success" => true,
                "message" => "success",
                "road_data" => $roadDetails
            )
        );

        // Close the database connection
        $conn = null;

    } else if ($_GET['action'] == "route-pkd") {

        $systemId = $_GET['systemId'];

        $roadPKDDetails = [];

        // get PKD Road
        $getRoadPKD = $conn->prepare("SELECT * FROM flw_pkd_roads WHERE system_id = :systemId");
        $getRoadPKD->bindParam(':systemId', $systemId);
        $getRoadPKD->execute();
        // Fetch all rows and store them in the array
        while ($row = $getRoadPKD->fetch(PDO::FETCH_ASSOC)) {
            $roadPKDDetails[] = $row;
        }
        
        http_response_code(200);
        echo json_encode(
            array(
                "success" => true,
                "message" => "success",
                "road_pkd" => $roadPKDDetails,
            )
        );

        // Close the database connection
        $conn = null;

    }
}
?>