<?php
require_once 'config/DBFactory.php';
require_once 'config/components.php';
class Sitevisit {

    private $type;
    private $department;
    private $Table;
    private $dbFactory;

    public function __construct($type, $department) {
        $this->dbFactory = new DBConnectionFactory();
        $this->type = $type;
        $this->department = $department;
        $this->Table = new Table();
    }

    public function fetch() {
        $getData = $this->getTableData();
        return $this->Table->get($this->type, $getData);
    }

    private function getTableData() {
        $db = $this->dbFactory->createConnection(); 

        $statusId = '{13}';

        $query = "SELECT 
        tasks.system_id,
        tasks.reference_no,
        tasks.provider_id,
        tasks.districts,
        site.authority_id,
        site.start,
        site.description,
        site.location,
        authority.sort_name AS authority_name,
        authority.logo AS authority_logo
        FROM view_tasks tasks
        LEFT JOIN flw_calendars site ON site.system_id = tasks.system_id
        LEFT JOIN ls_authorities authority ON authority.id = site.authority_id
        WHERE tasks.status_id = ANY(:statusId)
        ORDER BY site.start DESC";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':statusId', $statusId);

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        $a['field'] = [];

        foreach ($data as $field) {
            
            if(!$field->system_id){continue;}

            // echo $authName = Utilities::getAuthorityName($field->authority_id);
            $provider = General::getProvider($field->provider_id);

//             $utility = 
// "<div class='' data-bs-toggle='tooltip' data-bs-placement='top' title='$provider->name'><div class='symbol symbol-50px'><img src='$provider->logo' alt='$provider->name'/></div></div><span class='d-none'>$provider->name</span>";
            $utility = [
                "name" => $provider->name,
                "logo" => $provider->logo
            ];
            
            $authority = [
                "name" => $field->authority_name,
                "logo" => $field->authority_logo
            ];

            $a['field'][] = [
                "reference_no" => $field->reference_no,
                "tarikh" => date('d/m/Y', strtotime($field->start)),
                "masa" => date('H:i', strtotime($field->start)),
                "utility" => $utility,
                "districts" => $field->districts,
                "authority" => $authority,
                "description" => $field->description,
                "location" => $field->location,

            ];
        }

        return json_encode($a);

    }

}



