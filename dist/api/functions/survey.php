<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once 'config/DBFactory.php';
// require_once 'roles.php';
require_once 'config/components.php';

class SurveyApi {

    private $dbFactory;
    private $Role;
    private $User;
    private $Table;
    private $Modal;

    public function __construct($username)
    {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->Table = new Table();
        $this->Modal = new Modal();
        $this->User = $username ?? $_SESSION['username'];
    }

    // Calculate the total number of people who have logged in
    public static function totalTeam($systemId)
    {
        // Include the database connection parameters
        global $conn;

        $current_date = date('Y-m-d');

        // Execute a SELECT query to get the latest non-null value of 'number_team' column for the current date
        $query = "SELECT number_team, id FROM flw_survey_attandance WHERE system_id = :systemId AND DATE(created_timestamp) = :currentDate AND number_team IS NOT NULL ORDER BY created_timestamp DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        $stmt->bindParam(':currentDate', $current_date, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($row) && isset($row['number_team'])) {
            $countLoggedIn = $row['number_team']; // Extract the value from the array
        } else {
            $countLoggedIn = 0; // Set a default value if the array is not valid or the offset is not set
        }

        // Calculate the total teams
        $totalTeams = $countLoggedIn;
        // $id = $row['id'];
        $id = $row['id'] ?? 0;

        // Execute a SELECT query to get the number of teams where number_team is not null
        $query2 = "SELECT COUNT(*) AS total FROM flw_survey_attandance
                WHERE system_id = :systemId
                AND DATE(created_timestamp) = :currentDate
                AND clock_in IS NOT NULL
                AND id > :id";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        $stmt2->bindParam(':currentDate', $current_date, PDO::PARAM_STR);
        $stmt2->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt2->execute();

        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $countTeams = $row2['total'];

        // Debugging: Print the values for troubleshooting
        // var_dump("countTeams: $countTeams, totalTeams: $totalTeams");

        // Check if the total teams is more and equal to the count of logged-in users
        if ($countTeams >= $totalTeams) {
            return true;
        } else {
            return false;
        }
    }
    public static function repeaterID()
    {
        // Include the database connection parameters
        global $conn;

        // Check the previous id_repeat value in flw_survey_reports_udm
        $query = "SELECT id_repeat FROM flw_survey_reports_udm ORDER BY id DESC LIMIT 1";
        $stmt = $conn->query($query);

        $idRepeat = 1;

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idRepeat = $row['id_repeat'] + 1;
        }

        return $idRepeat;
    }

    public static function repeaterID_ASB()
    {
        // Include the database connection parameters
        global $conn;

        // Check the previous id_repeat value in flw_survey_reports_udm
        $query = "SELECT id_repeat FROM flw_survey_reports_asb ORDER BY id DESC LIMIT 1";
        $stmt = $conn->query($query);

        $idRepeat = 1;

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idRepeat = $row['id_repeat'] + 1;
        }

        return $idRepeat;
    }

    //Get the status of the project
    public static function getProjectStatus($systemId)
    {
        global $conn;

        // Execute a SELECT query on the database
        $query = "SELECT project_status FROM status_appl WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the results as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }

    public static function SurveyTable()
    {
        global $role;
        // Include the database connection parameters
        global $conn;

        require_once "config/system.php";
        include_once "api/header.php";
        include "config/tenant.php";
        $app = $appsTitle;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT * FROM public.view_survey_plan_tasks WHERE \"MappingID\" IN " . SurveyApi::surveyAction($role, $app) . " AND \"subMappingID\" IN " . Survey::subSurveyAction($role, $app));

        // $stmt = $conn->prepare("SELECT * FROM public.view_operation_task WHERE \"StatusID\" IN " . roleAction($role) . " AND (\"GISAssign\" = :userName OR \"StatusID\" <> 5) ORDER BY \"SubmitDate\" DESC ");
        $stmt = $conn->prepare("SELECT
        reference_no AS \"RefNo\",
    system_id,
    \"district\" AS \"District\",
    \"status_id\" AS \"StatusID\",
    \"mapping_id\" AS \"MappingID\",
    \"sub_mapping_id\" AS \"subMappingID\",
    application_length AS \"Length\",
    \"provider_id\" AS \"ProviderID\",
    \"provider_name\" AS \"Provider\",
    \"project_status\" AS \"ProjectStatus\",
    \"mapping_status\" AS \"MappingStatus\",
    \"status_color\" AS \"StatusColor\",
    \"status_icon\" AS \"StatusIcon\",
    \"submit_date\" AS \"SubmitDate\",
    id AS \"ID\",
    report_no AS \"ReportNo\",
    \"survey_assign\" AS \"SurveyAssign\",
    trigger_spku AS \"triggerSPKU\",
    \"udm_assign\" AS \"UDMAssign\",
    \"tmp_assign\" AS \"TMPAssign\" 
        FROM public.view_survey_plan_tasks WHERE \"mapping_id\" IN " . SurveyApi::surveyAction($role, $app) . " AND \"sub_mapping_id\" IN " . Survey::subSurveyAction($role, $app));

        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':userName', $username);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $row['userRole'] = $role;

            if ($row['MappingID'] < 10) {
                $row['MappingID'] = '00' . $row['MappingID'];
            } else if ($row['MappingID'] < 100) {
                $row['MappingID'] = '0' . $row['MappingID'];
            }

            if ($row['subMappingID'] < 10) {
                $row['subMappingID'] = '00' . $row['subMappingID'];
            } else if ($row['subMappingID'] < 100) {
                $row['subMappingID'] = '0' . $row['subMappingID'];
            }

            $data[] = $row;
        }

        // convert the result to a JSON string
        $json = '{"data":' . json_encode($data) . '}';

        // Close the database connection
        $conn = null;

        return $json;
    }

    public static function surveyAction($role, $app = null)
    {
        if ($app == 'UCIDOS' || $app == 'KITER' || $app == 'KUDRAT' || $app == null) {
            if ($role == 61) {
                $mapping = '(0,61,63,71)';
            } elseif ($role == 62 || $role == 64 || $role == 65) {
                $mapping = '(61,62,63)';
                // } elseif ($role == 65) {
                //     $mapping = '(0,62,63)';
            } elseif ($role == 63 || $role == 66) {
                $mapping = '(71,72,73,77,79,114)';
            } elseif ($role == 67) {
                $mapping = '(74,75,76,78,79)';
            }
        } else {
        }

        return $mapping;
    }

    public static function getSurveyFirstname($user)
    {
        // Include the database connection parameters
        global $conn;

        // Fetch the employee_id from sys_users
        $query3 = "SELECT employee_id FROM sys_users WHERE username = :user";
        $stmt3 = $conn->prepare($query3);
        $stmt3->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt3->execute();
        $row2 = $stmt3->fetch(PDO::FETCH_ASSOC);

        // Fetch the first_name from sys_hr_employee
        $staffID = $row2['employee_id'];
        $query4 = "SELECT first_name FROM sys_hr_employee WHERE id = :staffID";
        $stmt4 = $conn->prepare($query4);
        $stmt4->bindParam(':staffID', $staffID, PDO::PARAM_STR);
        $stmt4->execute();
        $row3 = $stmt4->fetch(PDO::FETCH_ASSOC);

        // Get the first name
        $name = $row3['first_name'];

        return $name;
    }

    public function getSurveyLength($systemID)
    {
        // Include the database connection parameters
        $conn = $this->dbFactory->createConnection();

        // Execute a SELECT query on the database to fetch application_length and progress_pending
        $query = "SELECT application_length, progress_pending FROM flw_survey_reports_udm WHERE system_id = :systemID ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing application_length and progress_pending
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate jarak diukur
        if ($row && isset($row['application_length']) && isset($row['progress_pending'])) {
            // Access the array elements safely
            $applicationLength = $row['application_length'];
            $progressPending = $row['progress_pending'];

            // Perform calculations with the values
            $jarakDiukur = $applicationLength - $progressPending;
        } else {
            // Perform calculations with the values
            $jarakDiukur = "-";
        }

        // Close the database connection
        // $conn = null;

        return $jarakDiukur;
    }

    public function getSurveyLengthASB($systemID)
    {
        // Include the database connection parameters
        $conn = $this->dbFactory->createConnection();

        // Execute a SELECT query on the database to fetch application_length and progress_pending
        $query = "SELECT application_length, progress_pending FROM flw_survey_reports_asb WHERE system_id = :systemID ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing application_length and progress_pending
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate jarak diukur
        if ($row && isset ($row['application_length']) && isset ($row['progress_pending'])) {
            // Access the array elements safely
            $applicationLength = $row['application_length'];
            $progressPending = $row['progress_pending'];

            // Perform calculations with the values
            $jarakDiukur = $applicationLength - $progressPending;
        } else {
            // Perform calculations with the values
            $jarakDiukur = "-";
        }

        // Close the database connection
        // $conn = null;

        return $jarakDiukur;
    }

    public static function getUniqueSurveyUsernames($systemID)
    {
        // Include the database connection parameters
        global $conn;

        // Get today's date in the  'YYYY-MM-DD'
        $today = date("Y-m-d");

        // Execute a SELECT query to fetch unique survey usernames and their associated first names
        $query = "SELECT DISTINCT sa.survey_username, hs.first_name
                    FROM flw_survey_attandance sa
                    LEFT JOIN sys_users su ON sa.survey_username = su.username
                    LEFT JOIN sys_hr_employee hs ON su.employee_id = hs.id
                    WHERE sa.system_id = :systemID
                    AND sa.clock_out IS NULL
                    AND DATE(sa.created_timestamp) = :today";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->bindParam(':today', $today, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the unique survey usernames and their associated first names
        $usernamesWithFirstName = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $usernamesWithFirstName;
    }

    public static function generateUUID() 
    {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version (4) and variant (10xx)
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Convert to hexadecimal representation
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    
        return $uuid;
    }

    public static function getAttand($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $currentDate = date("Y-m-d"); // Get the current date in the format 'YYYY-MM-DD'

        $query = "SELECT * FROM flw_survey_attandance WHERE system_id = :systemId AND clock_out IS NULL AND initial_code IS NOT NULL AND DATE(created_timestamp) = :currentDate";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':currentDate', $currentDate);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll();

        // Close the database connection
        $conn = null;

        return (count($result) > 0);
    }

    public static function getSurveyLeaderGroup($id)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $query = "SELECT team_members FROM flw_survey_team WHERE id = :id AND is_active = true";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Fetch team_members from the first query
        $teamMembersData = $stmt->fetch(PDO::FETCH_ASSOC);
        $teamMembersString = $teamMembersData['team_members'];

        // Convert the string representation into an array
        // Remove curly braces and split by comma
        $teamMembersArray = explode(',', trim($teamMembersString, '{}'));

        // Prepare an array to hold the usernames
        $usernames = "";

        foreach ($teamMembersArray as $teamMember) {
            $query2 = "SELECT username FROM sys_users WHERE username = :username AND role_id = 23";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':username', $teamMember);
            $stmt2->execute();

            // Fetch the username if found
            $userData = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                $usernames = $userData['username'];
            }
        }

        // Close the database connection
        $conn = null;

        // Return the array of usernames
        return $usernames;
    }

    public static function getSurveyMemberGroup($id)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $query = "SELECT team_members FROM flw_survey_team WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Fetch team_members from the first query
        $teamMembersData = $stmt->fetch(PDO::FETCH_ASSOC);
        $teamMembersString = $teamMembersData['team_members'];

        // Convert the string representation into an array
        // Remove curly braces and split by comma
        $teamMembersArray = explode(',', trim($teamMembersString, '{}'));

        // // Prepare an array to hold the usernames
        // $usernames = [];

        // foreach ($teamMembersArray as $teamMember) {
        //     $query2 = "SELECT username FROM sys_users WHERE username = :username";
        //     $stmt2 = $conn->prepare($query2);
        //     $stmt2->bindParam(':username', $teamMember);
        //     $stmt2->execute();

        //     // Fetch the username if found
        //     $userData = $stmt2->fetch(PDO::FETCH_ASSOC);

        //     // if ($userData) {
        //         $usernames[] = $userData['username'];
        //     // }
        // }

        // Close the database connection
        $conn = null;

        // Return the array of usernames
        return $teamMembersArray;
    }

    public static function checkSurveyProvider($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the data for the given system_id
        $query = "SELECT survey_provider FROM flw_survey_udm WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing qr_url
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if any rows were returned
        if (!$row) {
            // Handle the case where no rows were found
            return null;
        }

        // Close the database connection
        $conn = null;

        $surveyProvider = $row['survey_provider'];

        // Map survey_provider values to specific return values
        if ($surveyProvider === 'Inhouse') {
            return 1; // Return 1 for Inhouse
        } elseif ($surveyProvider === 'Outsource') {
            return 2; // Return 2 for Outsource
        } else {
            return null; // Handle unexpected values
        }
    }

    public static function getLogUdm($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the data for the given system_id
        $query = "SELECT log_pelan FROM flw_plan_udm WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing qr_url
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if any rows were returned
        if (!$row) {
            // Handle the case where no rows were found
            return null;
        }

        // Close the database connection
        $conn = null;

        $logUdm = $row['log_pelan'];

        return $logUdm;
    }

    public static function getLogTmp($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the data for the given system_id
        $query = "SELECT log_pelan FROM flw_plan_tmp WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing qr_url
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if any rows were returned
        if (!$row) {
            // Handle the case where no rows were found
            return null;
        }

        // Close the database connection
        $conn = null;

        $logTmp = $row['log_pelan'];

        return $logTmp;
    }

}