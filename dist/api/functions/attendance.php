<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once 'config/DBFactory.php';
require_once 'config/components.php';

class SurveyAttendanceModel {
    private $dbFactory;

    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
    }

    public function getSurveyAttendance(){
        // Get the database connection
        $conn = $this->dbFactory->createConnection();

        // Check if connection is valid
        if (!$conn) {
            die("Database connection failed.");
        }

        $sql = "SELECT fsa.id, fsa.system_id, fae.reference_no, fsa.created_timestamp, 
                       fsa.survey_username, fsu.team_id, fst.survey_team, 
                       she.first_name, she.last_name
                FROM flw_survey_attandance fsa
                LEFT JOIN flw_appl_entries fae ON fae.system_id = fsa.system_id
                LEFT JOIN flw_survey_udm fsu ON fsu.system_id = fsa.system_id
                LEFT JOIN flw_survey_team fst ON fst.id = fsu.team_id
                LEFT JOIN sys_users su ON su.username = fsa.survey_username
                LEFT JOIN sys_hr_employee she ON su.employee_id = she.id
                ORDER BY fsa.created_timestamp DESC";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage());
        }
        // print_r($result);
        return $result;
    }
}
?>

