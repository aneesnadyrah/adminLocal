<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/DBFactory.php";
require_once "api/functions/utilities.php";

class wayleaveEntry {
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
        WHERE ls_statuses.id = ctrl_statuses.status_id AND ctrl_statuses.department = 'operation') AS status,
        (SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ',') AS array_to_string FROM sys_upi
          WHERE sys_upi.state_code = flw_appl_entries.state AND (sys_upi.district_code= ANY (flw_appl_entries.districts))) AS districts
        FROM flw_appl_entries
        WHERE system_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $conn = null;
        $response = $result;
        return $response;
    }

    private function getDataAttachment($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare('SELECT flw_appl_attachments.*, ls_attachments.details FROM flw_appl_attachments
        LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type
        WHERE system_id = :id AND attachment_type IN (1,2,3,4)');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
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

    private function getDataPayment($systemId) {
        // Establish a database connection
        $conn = $this->dbFactory->createConnection();

        $query = 'SELECT 
        url, 
        paid_at, 
        transaction_id 
        FROM flw_charges 
        LEFT JOIN flw_appl_entries ON flw_appl_entries.charges_id = flw_charges.id
        WHERE flw_appl_entries.system_id = :id';

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $modals = ['approval-application', 'amend-application','project-wayleave-checking-edit-road'];
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
                break;
            case 'road':
                $getData = $this->getDataRoad($args[0]);
                break;
            case 'contact':
                $getData = $this->getDataContact($args[0]);
                break;
            case 'attachment':
                $getData = $this->getDataAttachment($args[0]);
                break;
            case 'payment':
                $getData = $this->getDataPayment($args[0]);
                break;
            case 'aside':
                $getData = new stdClass();
                $getData->provider = $this->getProvider($args[0]);
                $getData->notes = $this->getDataNotes($args[0]);
                $getData->reference = $this->getDataEntry($args[0])->reference_no;
                break;
            case 'provider':
                $getData = $this->getProvider($args[0]);
                break;
            case 'notes':
                $getData = $this->getDataNotes($args[0]);
                break;
            default:
                $getData = [];
            break;
        }
        return $this->Card->get('wl-'.$method, $getData);

    }
}


class permitEntry {

    public static function getPermitChecklist($sid) {
        // Establish a database connection
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare("SELECT entries.reference_no FROM flw_permit_checklist permit LEFT JOIN flw_appl_entries entries ON permit.system_id = entries.system_id WHERE permit.system_id = :sysid");
        $stmt->bindParam(':sysid', $sid);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $conn = null;
        return $result;
    }

    public static function checkPermitAttachment($sid, $type) {
        // Establish a database connection
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare("SELECT entries.reference_no,attachment_date,url,checked FROM flw_permit_checklist permit LEFT JOIN flw_appl_entries entries ON permit.system_id = entries.system_id WHERE permit.system_id = :sysid AND attachment_type = :type");
        $stmt->bindParam(':sysid', $sid);
        $stmt->bindParam(':type', $type);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $conn = null;
        return $result;
    }

    public static function getPermitOfficers($id) {
        // Establish a database connection
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT officers.id AS "Id",
                        officers.full_name AS "FullName",
                        officers.company_name AS "CompanyName",
                        officers.address_1 AS "Address1",
                        officers.address_2 AS "Address2",
                        officers.postcode AS "Postcode",
                        officers.city AS "City",
                        officers.state AS "State",
                        officers.email AS "Email",
                        officers.phone_no AS "PhoneNo",
                        officers.position AS "Position",
                        officers.type AS "Type"
                        FROM flw_permit_officers officers WHERE system_id = :id ORDER BY id ASC');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as associative arrays
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        // $data now holds the query result as an array of associative arrays
        $officers = $data;

        $conn = null;
        return $officers;
    }
}