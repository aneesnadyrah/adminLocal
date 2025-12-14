<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/DBFactory.php";
require_once "api/functions/utilities.php";

class wayleaveAmendments {
    private $dbFactory;
    private $Card;

    private $Modal;

    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
        $this->Card = new Card();
        $this->Modal = new Modal();
    }

    private function getDataEntry($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT flw_appl_entries.*, 
        (SELECT status FROM ls_statuses 
        LEFT JOIN ctrl_statuses ON flw_appl_entries.system_id = ctrl_statuses.system_id
        WHERE ls_statuses.id = ctrl_statuses.status_id AND ctrl_statuses.department = 'operation' AND authority != 0 LIMIT 1) AS status,
        (SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ',') AS array_to_string FROM sys_upi
          WHERE sys_upi.state_code = flw_appl_entries.state AND (sys_upi.district_code= ANY (flw_appl_entries.districts))) AS districts
        FROM flw_appl_entries
        WHERE system_id = :id LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $conn = null;
        $response = $result;
        return $response;
    }

    private function getDataRoad($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT        
        flw_appl_roads.id AS id, 
        flw_appl_roads.road_name AS name, 
        flw_appl_roads.road_length AS distance,
        string_agg(ls_work_methods.name, ', ') AS methods, 
        CONCAT(flw_appl_roads.latitude_start, ', ', flw_appl_roads.longitude_start) AS start_coordinates,
        CONCAT(flw_appl_roads.latitude_end, ', ', flw_appl_roads.longitude_end) AS end_coordinates
        FROM flw_appl_roads
        LEFT JOIN ls_work_methods ON ls_work_methods.id = ANY (flw_appl_roads.method)
        LEFT JOIN flw_appl_entries ON flw_appl_roads.id = ANY (flw_appl_entries.list_road_id)
        WHERE flw_appl_roads.system_id = :id AND flw_appl_roads.version = 1
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

    public static function getDataRoadTP($systemId) {
        // Establish a database connection
        $conn = General::connectToDatabase();

        $query = "SELECT        
        flw_appl_roads.id AS id, 
        flw_appl_roads.road_name AS name, 
        flw_appl_roads.road_length AS distance,
        string_agg(ls_work_methods.name, ', ') AS methods, 
        string_agg(ls_work_methods.method, ', ') AS methods_shortform, 
        CONCAT(flw_appl_roads.latitude_start, ', ', flw_appl_roads.longitude_start) AS start_coordinates,
        CONCAT(flw_appl_roads.latitude_end, ', ', flw_appl_roads.longitude_end) AS end_coordinates
        FROM flw_appl_roads
        LEFT JOIN ls_work_methods ON ls_work_methods.id = ANY (flw_appl_roads.method)
        LEFT JOIN flw_appl_entries ON flw_appl_roads.id = ANY (flw_appl_entries.list_road_id)
        WHERE flw_appl_roads.system_id = :id AND flw_appl_roads.active = true AND flw_appl_roads.version != 1
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

    private function getDataAttachment($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare('SELECT flw_appl_attachments.*, ls_attachments.details FROM flw_appl_attachments
        LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type
        WHERE system_id = :id AND attachment_type IN (1,2,3,4) AND active = false AND version = 1 ORDER BY attachment_type ASC');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $conn = null;

        $response = $result;
        return $response;
    }

    public static function getDataAttachmentTP($systemId) {
        // Establish a database connection
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT flw_appl_attachments.*, ls_attachments.details FROM flw_appl_attachments
        LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type
        WHERE system_id = :id AND attachment_type IN (1,2,3,4) AND active = true ORDER BY attachment_type ASC');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $conn = null;

        $response = $result;
        return $response;
    }

    public static function getAttachmentLTA($systemId) {
        // Establish a database connection
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT flw_appl_attachments.*, ls_attachments.details FROM flw_appl_attachments
        LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type
        WHERE system_id = :id AND attachment_type = 7 ORDER BY id DESC LIMIT 1 ');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $conn = null;

        $response = $result;
        return $response;
    }

    private function getDataContact($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT        
        flw_appl_contacts.id AS id, 
        flw_appl_contacts.full_name AS name,
        flw_appl_contacts.email,
        ls_type_contacts.name AS type,
        flw_appl_contacts.company_name,
        flw_appl_contacts.position,
        flw_appl_contacts.phone_no AS phone,
        flw_appl_contacts.address_1 AS unit_no,
        flw_appl_contacts.address_2 AS street,
        flw_appl_contacts.postcode,
        flw_appl_contacts.city,
        flw_appl_contacts.state
        FROM flw_appl_contacts
        LEFT JOIN ls_type_contacts ON flw_appl_contacts.type = ls_type_contacts.id
        LEFT JOIN flw_appl_entries ON flw_appl_contacts.id = ANY (flw_appl_entries.contact_id)
        WHERE flw_appl_contacts.system_id = :id AND flw_appl_contacts.id = ANY (SELECT DISTINCT unnest(contact_id) FROM flw_appl_entries)
        GROUP BY
        flw_appl_contacts.id,
        flw_appl_contacts.full_name,
        flw_appl_contacts.email,
        ls_type_contacts.name,
        flw_appl_contacts.company_name,
        flw_appl_contacts.position,
        flw_appl_contacts.phone_no,
        flw_appl_contacts.address_1,
        flw_appl_contacts.address_2,
        flw_appl_contacts.postcode,
        flw_appl_contacts.city,
        flw_appl_contacts.state
        ORDER BY flw_appl_contacts.id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $conn = null;
        $response = $result;
        return $response;


    }

    private function getProvider($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare('SELECT utility_provider FROM flw_appl_entries WHERE system_id = :id');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $id = $stmt->fetch(PDO::FETCH_OBJ);
        $conn = null;

        $result = json_encode(Utilities::getProvider($id->utility_provider));

        return json_decode($result);
    }

    private function getDataNotes($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();
        return (object)[];
    }

    public function __call($method, $args) {
        if ($method === "modal") {
            $modals = ['approval-tp', 'amend-tp'];
            $getData = new stdClass();
            $getData->reference = $this->getDataEntry($args[0])->reference_no;
            
            $results = []; // Initialize an array to store results
        
            foreach ($modals as $modal) {
                // Collect the result for each modal in the array
                $results[$modal] = $this->Modal->get($modal, $getData);
            }
        
            return $results; // Return the array of results after the loop
        }

        switch($method) {
            case 'entry':
                $getData = $this->getDataEntry($args[0]);
                $results = $this->Card->get('wl-'.$method, $getData);
                break;
            case 'road':
                $getData = $this->getDataRoad($args[0]);
                $results = $this->Card->get('wl-'.$method.'-tp', $getData);
                break;
            case 'contact':
                $getData = $this->getDataContact($args[0]);
                $results = $this->Card->get('wl-'.$method, $getData);
                break;
            case 'attachment':
                $getData = $this->getDataAttachment($args[0]);
                $results = $this->Card->get('wl-'.$method.'-tp', $getData);
                break;
            case 'aside':
                $getData = new stdClass();
                $getData->provider = $this->getProvider($args[0]);
                $getData->notes = $this->getDataNotes($args[0]);
                $getData->reference = $this->getDataEntry($args[0])->reference_no;

                $results = $this->Card->get('wl-'.$method.'-tp', $getData);
                break;
            case 'asideReview':
                $getData = new stdClass();
                $getData->provider = $this->getProvider($args[0]);
                $getData->notes = $this->getDataNotes($args[0]);
                $getData->reference = $this->getDataEntry($args[0])->reference_no;

                $results = $this->Card->get('wl-'.$method.'-tp', $getData);
                break;
            default:
                $getData = [];
            break;
        }
        return $results;

    }
}