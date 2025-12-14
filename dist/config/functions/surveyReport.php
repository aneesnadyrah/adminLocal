<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';

class SurveyReport{
    private $dbFactory;
    private $Role;
    private $User;
    private $Table;
    private $system;

    public function __construct($username)
    {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->Table = new Table();
        $this->User = $username ?? $_SESSION['username'];
        $this->system = new System;
    }

    private function getTableData()
    {
        $db = $this->dbFactory->createConnection();
        $state = $this->system->App->state;
        $department = "mapping";

        $sql = "SELECT crn.id,fsru.system_id, fst.survey_team, fsru.reference_no, fsru.survey_date, (fsru.application_length-fsru.progress_pending) as survey_length, fae.utility_provider, fae.districts";
        $sql.=" FROM flw_survey_reports_udm fsru";
        $sql.=" LEFT JOIN ctrl_statuses cs ON fsru.system_id = cs.system_id";
        $sql.=" LEFT JOIN ctrl_reference_no crn ON fsru.system_id = crn.system_id";
        $sql.=" LEFT JOIN flw_survey_team fst ON fsru.survey_team::bigint = fst.id";
        $sql.=" LEFT JOIN flw_appl_entries fae ON fsru.system_id = fae.system_id";
        $sql.=" WHERE cs.department = :department AND cs.status_id > 48 AND fst.survey_team is not null";
        $sql.=" ORDER BY crn.id ASC, fsru.id DESC";

        $query = $db->prepare($sql);
        
        // // Bind the parameter
        $query->bindParam(':department', $department, PDO::PARAM_STR);
        // // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function viewData($systemID) {

        $db = $this->dbFactory->createConnection();
        $sql ="Select fsru.*, fst.survey_team as team, fae.project_title, fae.utility_provider as provider, fae.application_length";
        $sql.=" FROM flw_survey_reports_udm fsru";
        $sql.=" LEFT JOIN flw_survey_team fst ON fsru.survey_team::bigint = fst.id";
        $sql.=" LEFT JOIN flw_appl_entries fae ON fae.system_id = fsru.system_id";
        $sql.=" WHERE fsru.system_id = :systemID";
        $sql.=" ORDER BY fsru.id ASC";
        $query = $db->prepare($sql);
        
        // // Bind the parameter
        $query->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        // // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function getSurveyWorkDesc() {

        $db = $this->dbFactory->createConnection();
        $sql ="Select *";
        $sql.=" FROM ls_survey_work";
        $sql.=" ORDER BY id ASC";
        $query = $db->prepare($sql);
        
        // // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function getSurveyEquipmentDesc() {

        $db = $this->dbFactory->createConnection();
        $sql ="Select *";
        $sql.=" FROM ls_survey_equipment";
        $sql.=" ORDER BY id ASC";
        $query = $db->prepare($sql);
        
        // // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function getSurveyTeamMembersName($teamUsername) {

        $db = $this->dbFactory->createConnection();
        $sql ="Select she.first_name, she.last_name, su.username";
        $sql.=" FROM sys_hr_employee she";
        $sql.=" LEFT JOIN sys_users su ON she.id=su.employee_id";
        $sql.=" WHERE su.username = ANY(:teamUsername)";
        $query = $db->prepare($sql);
        
        // // Bind the parameter
        $query->bindParam(':teamUsername', $teamUsername, PDO::PARAM_INT);
        // // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function getSurveyImage($systemId, $idRepeat)
    {
        $db = $this->dbFactory->createConnection();
        $category = '{1,2}';
        $queryGetImageData = "SELECT id, url, category, description FROM flw_survey_report_images_udm WHERE id_repeat = ANY(:idRepeat) AND system_id = :systemId AND category = ANY(:category) ORDER BY category ASC, COALESCE(NULLIF(REGEXP_REPLACE(description, '[^0-9]', '', 'g'), '')::INTEGER, 0), id ASC;";
        $stmtImageData = $db->prepare($queryGetImageData);
        $stmtImageData->bindParam(':idRepeat', $idRepeat);
        $stmtImageData->bindParam(':systemId', $systemId);
        $stmtImageData->bindParam(':category', $category);
        $stmtImageData->execute();

        // Fetch the result as an associative array
        $result = $stmtImageData->fetchAll(PDO::FETCH_OBJ);

        $category = 3;
        $queryGetImageData03 = "SELECT id, url, category, description FROM flw_survey_report_images_udm WHERE id_repeat = ANY(:idRepeat) AND system_id = :systemId AND category = :category ORDER BY COALESCE(NULLIF(REGEXP_REPLACE(description, '[^0-9]', '', 'g'), '')::INT,0)";
        $stmtImageData03 = $db->prepare($queryGetImageData03);
        $stmtImageData03->bindParam(':idRepeat', $idRepeat);
        $stmtImageData03->bindParam(':systemId', $systemId);
        $stmtImageData03->bindParam(':category', $category);
        $stmtImageData03->execute();

        $result03 = $stmtImageData03->fetchAll(PDO::FETCH_OBJ);
        $resultImageData = array_merge($result, $result03);

        return $resultImageData;
    }

    public function getFullname($username)
    {
        $db = $this->dbFactory->createConnection();
        $query = "SELECT she.first_name, she.last_name FROM sys_hr_employee she";
        $query .= " LEFT JOIN sys_users su ON she.id=su.employee_id";
        $query .= " WHERE su.username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return isset($result) ? $result->first_name . ' ' . $result->last_name : '';
    }

    public function getDistrict()
    {
        $statecode = 11;
        $db = $this->dbFactory->createConnection();
        $query = "SELECT distinct state_code, district_name, district_code FROM sys_upi";
        $query .= " WHERE state_code = :statecode";
        $query .= " ORDER BY district_code ASC ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':statecode', $statecode);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    public function getPKDRoadList($systemId) {
        $db = $this->dbFactory->createConnection();
        $sql ="Select road_name";
        $sql.=" FROM flw_pkd_roads fpr";
        $sql.=" WHERE fpr.system_id = :systemId";
        $query = $db->prepare($sql);
        
        // // Bind the parameter
        $query->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        // // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
    
    public function __call($method, $args)
    {
        $section = $args[0];

        if ($method === 'table') {
            $getData = $this->getTableData();
            $districtList = $this->getDistrict();
            // var_dump($districtList);
            foreach ($getData as $data) {
                foreach ($districtList as $key) {
                    $district = str_replace(array("{","}"), "", $data->districts);
                    if($district == $key->district_code) {
                        $data->districts = $key->district_name;
                    } 
                }
            }
            return $this->Table->render($section, $getData);
        }
    }
}

?>