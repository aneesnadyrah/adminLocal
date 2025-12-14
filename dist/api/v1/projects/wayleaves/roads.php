<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/header.php";
include_once "api/functions.php";

$systemId = isset($_GET['sid']) ? $_GET['sid'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action == 'data') {
        $roads = getDataRoad($systemId);
        $work_methods = getWorkMethods();
        $districts = getDistricts();
        $response = [
            "status" => 200,
            "data" => [
                "roads" => $roads,
                "work_methods" => $work_methods,
                "districts" => $districts,
                "action" => $action,
                "systemId" => $systemId,
            ]
        ];
        echo json_encode($response);   
    } else if ($action == 'show') {
        
    }
    
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') { // RO update road details
    $payloadData = json_decode(file_get_contents('php://input'), true);
    $districts = array();
    foreach ($payloadData as $data) {
        $updateRoad = updateRoad($data);
        if (!$updateRoad) {
            $response = [
                "status" => 500,
                "message" => "Terdapat ralat di dalam pelayan data. Jika ini kembali terjadi, sila hubungi sokongan IT kami.",
            ];
            echo json_encode($response);
            exit;
        }

        $districts[] = $data['road-district'];
    }
    $unqDistrict = array_values(array_unique($districts));
    $district = '{' . implode(",", $unqDistrict) . '}';
    $updDE = updateDataEntriesDistricts($systemId,$district);
    if (!$updDE) {
        $response = [
            "status" => 500,
            "message" => "Terdapat ralat di dalam pelayan data. Jika ini kembali terjadi, sila hubungi sokongan IT kami.",
        ];
        echo json_encode($response);
    } else {
        $response = [
            "status" => 200,
            "data" => getApplicationData($systemId),
            "message" => "Maklumat butiran permohonan berjaya dikemaskini.",
        ];
    }
    
    echo json_encode($response);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') { // RO add new road
    $payloadData = file_get_contents("php://input");
    $POST = json_decode($payloadData, true);

    $UpdateData = updateDataEntries($POST, $systemId);
    $response = [
        "status" => 200,
        "message" => "Maklumat jalan berjaya dikemaskini.",
    ]; 

    echo json_encode($response);
}

function establishConnection(){
    // Establish a database connection
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();
    return $conn;
}

function getDistricts(){
     // Establish a database connection
    $conn = establishConnection();
    $query = "SELECT state_code, district_code, district_name 
                FROM sys_upi 
                WHERE state_code = '11' 
                GROUP BY state_code, district_name, district_code 
                ORDER BY district_code ASC";

    $stmt = $conn->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = $data;
    $conn = null;

    return $output;
}

function getWorkMethods(){
    // Establish a database connection
    $conn = establishConnection();

    $query = "SELECT         
            ls_work_methods.id AS id,
            ls_work_methods.name AS name, 
            ls_work_methods.method AS method
            FROM ls_work_methods";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $conn = null;

    $response = [];
    foreach ($result as $row) {
        $response[(int)$row->id] = $row->name;
    }

    return $response;
}

function getDataRoad($systemId) {
    // Establish a database connection
    $conn = establishConnection();

    $query = "SELECT            
        flw_appl_roads.id AS id, 
        flw_appl_roads.road_name AS name, 
        flw_appl_roads.road_length AS distance,
        flw_appl_roads.districts AS district,
        ARRAY_AGG(DISTINCT ls_work_methods.id) AS methods,
        CONCAT(flw_appl_roads.latitude_start, ', ', flw_appl_roads.longitude_start) AS start_coordinates,
        CONCAT(flw_appl_roads.latitude_end, ', ', flw_appl_roads.longitude_end) AS end_coordinates
        FROM flw_appl_roads
        LEFT JOIN ls_work_methods ON ls_work_methods.id = ANY (flw_appl_roads.method)
        LEFT JOIN flw_appl_entries ON flw_appl_roads.id = ANY (flw_appl_entries.list_road_id)
        WHERE flw_appl_roads.system_id = :id 
        AND flw_appl_roads.id = ANY (SELECT DISTINCT unnest(list_road_id) FROM flw_appl_entries)
        GROUP BY 
            flw_appl_roads.id, 
            flw_appl_roads.road_length, 
            flw_appl_roads.road_name
        ORDER BY flw_appl_roads.id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $systemId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    $conn = null;
    $response = $result;

    return $response;

}

function updateRoad($data) {
    $id = $data['road-id'];
    $name = $data['road-name'];
    $method = $data['road-method'];
    $district = $data['road-district'];
    $length = $data['road-length'];

    if (is_array($method)) {
        $int = array_map('intval', $method);
        $methodId = '{' . implode(",", $int) . '}';
    } else {
        $methodId = '{' . intval($method) . '}';
    }

    $start = $data['start-coord'];
    $end = $data['end-coord'];

    $slat = explode(",", $start)[0];
    $slong = explode(",", $start)[1];
    $elat = explode(",", $end)[0];
    $elong = explode(",", $end)[1];

    // Establish a database connection
    $conn = establishConnection();

    $sql = "UPDATE flw_appl_roads SET 
            road_name = :name, 
            method = :methodId, 
            districts = :district,
            road_length = :length,
            latitude_start = :slat,
            longitude_start = :slong,
            latitude_end = :elat,
            longitude_end = :elong
            WHERE id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':methodId', $methodId);
    $stmt->bindParam(':district', $district);
    $stmt->bindParam(':length', $length);
    $stmt->bindParam(':slat', $slat);
    $stmt->bindParam(':slong', $slong);
    $stmt->bindParam(':elat', $elat);
    $stmt->bindParam(':elong', $elong);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $conn = null;

    return true;
}

function updateDataEntriesDistricts($systemId, $district) {
    // Establish a database connection
    $conn = establishConnection();

    $sql = "UPDATE flw_appl_entries SET districts = :district WHERE system_id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':district', $district);
    $stmt->bindParam(':id', $systemId);
    $stmt->execute();

    $conn = null;

    return true;
} 
function updateDataEntries($data, $systemId) {
    $title = $data['title'];
    $site_start = $data['site_start'];
    $site_end = $data['site_end']; 
    $project_costs = (float) str_replace(',', '', $data['project_costs']);;
    $link_id = $data['link_id'];
    $application_date = $data['date'];
    $applength = $data['length'];

    // Establish a database connection
    $conn = establishConnection();

    $sql = "UPDATE flw_appl_entries SET
            project_title = :title,
            site_start = :site_start,
            site_end = :site_end,
            project_costs = :project_costs,
            link_id = :link_id,
            application_date = :application_date,
            application_length = :applength
            WHERE system_id = :systemId";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':site_start', $site_start);
    $stmt->bindParam(':site_end', $site_end);
    $stmt->bindParam(':project_costs', $project_costs);
    $stmt->bindParam(':link_id', $link_id);
    $stmt->bindParam(':application_date', $application_date);
    $stmt->bindParam(':applength', $applength);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();

    $conn = null;

    return true;
}


function getApplicationData($systemId) {
    $conn = establishConnection();

    $Rquery = "SELECT        
        flw_appl_roads.id AS id, 
        flw_appl_roads.road_name AS name, 
        flw_appl_roads.road_length AS distance,
        string_agg(ls_work_methods.name, ', ') AS methods, 
        CONCAT(flw_appl_roads.latitude_start, ', ', flw_appl_roads.longitude_start) AS start_coordinates,
        CONCAT(flw_appl_roads.latitude_end, ', ', flw_appl_roads.longitude_end) AS end_coordinates
        FROM flw_appl_roads
        LEFT JOIN ls_work_methods ON ls_work_methods.id = ANY (flw_appl_roads.method)
        LEFT JOIN flw_appl_entries ON flw_appl_roads.id = ANY (flw_appl_entries.list_road_id)
        WHERE flw_appl_roads.system_id = :id 
        AND flw_appl_roads.id = ANY (SELECT DISTINCT unnest(list_road_id) FROM flw_appl_entries)
        GROUP BY 
        flw_appl_roads.id, 
        flw_appl_roads.road_length, 
        flw_appl_roads.road_name
        ORDER BY flw_appl_roads.id";

        $Rstmt = $conn->prepare($Rquery);
        $Rstmt->bindParam(':id', $systemId);
        $Rstmt->execute();
        $Rresult = $Rstmt->fetchAll(PDO::FETCH_OBJ);
        $conn = null;
        $Rresponse = $Rresult;

    $conn = establishConnection();

    $DEquery = "SELECT  
        (SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ',') AS array_to_string FROM sys_upi
          WHERE sys_upi.state_code = flw_appl_entries.state AND (sys_upi.district_code= ANY (flw_appl_entries.districts))) AS districts
        FROM flw_appl_entries
        WHERE system_id = :id";
        $DEstmt = $conn->prepare($DEquery);
        $DEstmt->bindParam(':id', $systemId);
        $DEstmt->execute();
        $DEresult = $DEstmt->fetch(PDO::FETCH_OBJ);
        $conn = null;
        $DEresponse = $DEresult;
        
    return array(
        'dataentry' => $DEresponse, 
        'dataroad' => $Rresponse
    );
}

?>