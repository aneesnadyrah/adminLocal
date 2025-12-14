<?php
// STUB: Assign to atikah
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// require_once "config/system.php";
// require_once "config/functions.php";

class Survey
{

    public static function selectSurveyEdit($subDepartment, $id)
    {
        $db = General::connectToDatabase();

        switch ($subDepartment) {
            default:
            //     $query = "SELECT name, profile_pic AS url, username AS value, position AS description
            // FROM view_users
            // WHERE sub_department = :sub AND
            // (
            //     username NOT IN (SELECT UNNEST(team_members) FROM flw_survey_team WHERE id <> CAST(:id AS bigint))
            // )";

                $query = "SELECT DISTINCT ON (username) name, profile_pic AS url, username AS value, position AS description
                FROM view_users
                WHERE sub_department = :sub";

                break;
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':sub', $subDepartment);
        // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function selectSurveyAdd($subDepartment, $id)
    {
        $db = General::connectToDatabase();

        switch ($subDepartment) {
            default:
                // $query = "SELECT name, profile_pic AS url, username AS value, position AS description
                // FROM view_users WHERE sub_department = :sub AND username NOT IN (SELECT UNNEST(team_members) FROM flw_survey_team)";

                $query = "SELECT DISTINCT ON (username) name, profile_pic AS url, username AS value, position AS description
                FROM view_users
                WHERE sub_department = :sub";

                break;
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':sub', $subDepartment);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function getDataEntry($systemId)
    {
        // Establish a database connection
        global $PDO;

        $conn = General::connectToDatabase();

        $query = "SELECT flw_appl_entries.*,
        (SELECT status FROM ls_statuses
        LEFT JOIN ctrl_statuses ON flw_appl_entries.system_id = ctrl_statuses.system_id
        WHERE ls_statuses.id = ctrl_statuses.status_id AND ctrl_statuses.department = 'operation' AND ctrl_statuses.authority = 0) AS status,
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

    public static function getDataRoad($systemId)
    {
        // Establish a database connection
        // global $PDO;

        $conn = General::connectToDatabase();

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

    public static function totalProjectProgress()
    {
        global $PDO;

        $conn = General::connectToDatabase();

        $currentYear = date('Y');

        // Execute a SELECT query on the database
        $query = "SELECT COUNT(id) AS total_application FROM flw_appl_survey WHERE EXTRACT(year FROM est_date_start) = :currentYear";

        // Prepare the query statement
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':currentYear', $currentYear);

        // Execute the query
        $stmt->execute();

        // Fetch the first row from the query result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set the $total variable to the value of total_application from the first row
        $total = $result['total_application'];

        // Close the database connection
        $conn = null;

        return $total;
    }

    public static function totalProjectProgressPlan()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $currentYear = date('Y');

        // Prepare the SELECT query
        $query = "SELECT COUNT(id) AS total_application FROM flw_plan_udm WHERE EXTRACT(year FROM est_date_start) = :currentYear";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':currentYear', $currentYear);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Set the $total variable to the value of total_application
        $total = $result['total_application'];

        // Close the database connection
        $conn = null;

        return $total;
    }

    public static function tableWidgetSelection($selection, $filter)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        if ($selection == 1) {
            // Retrieve data from flw_appl_entries and join with flw_appl_survey based on system_id
            $query = "SELECT
            e.reference_no AS \"refNo\",
            e.system_id AS \"sysID\",
            e.utility_provider AS \"providerID\",
            e.application_length AS \"length\",
            s.est_date_start AS \"startDate\",
            s.est_date_end AS \"endDate\",
            e.project_region AS \"district\"
        FROM flw_appl_entries AS e
        JOIN flw_appl_survey AS s ON e.system_id = s.system_id
        ORDER BY e.id ASC LIMIT 5";
        } elseif ($selection == 2) {
            // Retrieve data from flw_appl_entries and join with flw_appl_survey based on system_id
            $query = "SELECT
            s.reference_no AS \"refNo\",
            s.system_id AS \"sysID\",
            s.\"provider_id\" AS \"providerID\",
            s.application_length AS \"length\",
            s.\"mapping_status\" AS \"status\",
            s.\"district\" AS \"district\"
        FROM public.view_survey_plan_tasks AS s
        LEFT JOIN flw_appl_mapping AS m ON s.system_id = m.system_id
        WHERE m.\"sub_mapping_status\" = 1
        ORDER BY m.\"id\" ASC LIMIT 5";
        } else if ($selection == 3) {
            // Retrieve data from flw_appl_entries and join with flw_appl_survey based on system_id
            $query = "SELECT
            e.reference_no AS \"refNo\",
            e.system_id AS \"sysID\",
            e.utility_provider AS \"providerID\",
            e.application_length AS \"length\",
            s.est_date_start AS \"startDate\",
            s.est_date_end AS \"endDate\",
            e.project_region AS \"district\"
        FROM flw_appl_entries AS e
        JOIN flw_plan_udm AS s ON e.system_id = s.system_id
        ORDER BY s.id ASC LIMIT 5";
        } else if ($selection == 4) {
            // Retrieve data from flw_appl_entries and join with flw_appl_survey based on system_id
            $query = "SELECT
                e.reference_no AS \"refNo\",
                e.system_id AS \"sysID\",
                e.utility_provider AS \"providerID\",
                e.application_length AS \"length\",
                s.est_date_start AS \"startDate\",
                s.est_date_end AS \"endDate\",
                e.project_region AS \"district\"
            FROM flw_appl_entries AS e
            JOIN flw_plan_tmp AS s ON e.system_id = s.system_id
            ORDER BY s.id ASC LIMIT 5";
        } elseif ($selection == 5) {
            // Retrieve data from flw_appl_entries and join with flw_appl_survey based on system_id
            //     $query = "SELECT
            //     s.reference_no AS \"refNo\",
            //     s.system_id AS \"sysID\",
            //     s.\"provider_id\" AS \"providerID\",
            //     s.application_length AS \"length\",
            //     s.\"mapping_status\" AS \"status\",
            //     s.\"district\" AS \"district\"
            // FROM public.view_survey_plan_tasks AS s
            // LEFT JOIN flw_appl_mapping AS m ON s.system_id = m.system_id
            // WHERE m.\"sub_mapping_status\" = 1
            // ORDER BY m.\"id\" ASC LIMIT 5";
            $query = "
                SELECT
                    authority,
                    public.view_survey_priority.system_id AS \"sysID\",
                    quote_approve,
                    \"wy_approval_date\",
                    public.view_survey_priority.id AS \"ID\",
                    status_id,
                    \"flow_name\",
                    \"project_status\" AS \"status\",
                    \"status_color\",
                    \"status_icon\",
                    reference_no AS \"refNo\",
                    application_length AS \"length\",
                    \"submit_date\",
                    \"provider_name\",
                    \"provider_id\" AS \"providerID\",
                    \"district\" AS \"district\"
                FROM public.view_survey_priority
                LEFT JOIN flw_survey_udm AS fas ON public.view_survey_priority.system_id = fas.system_id
                WHERE fas.trigger_priority = true AND status_id = 39";
        }

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function currentProgressUdm($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
        current_progress AS \"current_progress\",
        daily_progress AS \"daily_progress\",
        application_length AS \"application_length\",
        progress_pending AS \"progress_pending\"
        FROM flw_survey_reports_udm
        WHERE system_id = :systemId
        ORDER BY id DESC
        LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there is a row in the flw_survey_reports_udm table with the provided systemId
        if ($row) {
            $current_progress = ($row['application_length'] - $row['progress_pending']);
        } else {
            // If there is no row with the matching systemId, set the current progress to 0
            $current_progress = 0;
        }

        // Close the database connection
        $conn = null;

        // Return the current progress value
        return $current_progress;
    }

    public static function currentProgressAsb($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
        current_progress AS \"current_progress\",
        daily_progress AS \"daily_progress\",
        application_length AS \"application_length\",
        progress_pending AS \"progress_pending\"
        FROM flw_survey_reports_asb
        WHERE system_id = :systemId
        ORDER BY id DESC
        LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there is a row in the flw_survey_reports_asb table with the provided systemId
        if ($row) {
            $current_progress = ($row['application_length'] - $row['progress_pending']);
        } else {
            // If there is no row with the matching systemId, set the current progress to 0
            $current_progress = 0;
        }

        // Close the database connection
        $conn = null;

        // Return the current progress value
        return $current_progress;
    }

    public static function getForecast()
    {
        $apiKey = 'fd060369bbbe5ac9cdea0d5fb90cf812';
        $lat = '5.3302489611848065';
        $lon = '103.1481347118348';
        $apiUrl = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&appid=$apiKey";

        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $weatherData = json_decode($response, true);

        if ($weatherData === null || $weatherData['cod'] !== "200") {
            return "Failed to retrieve weather data";
        }

        $description = $weatherData['list'][0]['weather'][0]['description'];
        $translationApiUrl = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl=ms&dt=t&q=" . urlencode($description);
        $translationCurl = curl_init($translationApiUrl);
        curl_setopt($translationCurl, CURLOPT_RETURNTRANSFER, true);
        $translationResponse = curl_exec($translationCurl);
        curl_close($translationCurl);

        $translationData = json_decode($translationResponse, true);
        $translatedDescription = ucfirst($translationData[0][0][0]); // capitalize the first word

        return $translatedDescription;
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

    public static function surveyTeamModal()
    {

        // Include the database connection parameters
        global $PDO;
        require_once "config/system.php";
        $system = new System;
        $state = $system->App->state;
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT * FROM flw_survey_team WHERE state = :state AND is_active = true");
        // Bind the parameter
        $query->bindParam(':state', $state, PDO::PARAM_INT);
        $query->execute();

        // Fetch the result as an associative array
        $teams = $query->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $teams;
    }

    public static function listProvider($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
        vot.reference_no AS \"RefNo\",
        vot.project_title AS \"Title\",
        vot.system_id AS \"SysID\",
        vot.payment_method  AS \"PaymentMethod\",
        vot.\"districts\" AS \"District\",
        vot.application_length AS \"Length\",
        vot.\"provider_id\" AS \"ProviderID\",
        ls_provider.name AS \"Provider\",
        ls_provider.company_logo AS \"ProviderLogo\",
        vot.\"status\" AS \"Status\",
        vot.\"status_color\" AS \"StatusColor\",
        vot.\"status_icon\" AS \"StatusIcon\",
        vot.\"submitted_date\" AS \"SubmitDate\",
        vot.\"status_id\" AS \"StatusID\",
        vot.\"flow_department\" AS \"Department\",
        ta.*, lr.*,
	(SELECT SUM(road_length) 
            FROM flw_appl_roads 
            WHERE flw_appl_roads.system_id =  :systemID
            AND active = true) as sitevisit_length
        FROM public.view_tasks AS vot
        INNER JOIN flw_appl_entries AS ta ON vot.system_id = ta.system_id
        INNER JOIN flw_appl_roads AS lr ON vot.system_id = lr.system_id
        LEFT JOIN ls_provider ON ls_provider.id = ta.utility_provider
        WHERE vot.system_id = :systemID";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function surveyMembersUdm($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_team
        $query = "SELECT * FROM flw_survey_reports_udm 
        WHERE system_id = :systemID
        ORDER BY id DESC LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there are results
        if (!$row) {
            return []; // Return an empty array if no results
        }

        // Remove the curly braces {} from the team_members field
        $teamMembers = !empty($row['team_members']) ? trim($row['team_members'], '{}') : '';
        $teamMembers = $teamMembers ? explode(',', $teamMembers) : [];
        $teamMembers = array_map('trim', $teamMembers);

        // Fetch the employee_id and username from sys_users table based on team_members
        $staffIds = array();
        $usernames = array();
        foreach ($teamMembers as $member) {
            $userQuery = "SELECT employee_id, username FROM sys_users WHERE username = :member";
            $userStmt = $conn->prepare($userQuery);
            $userStmt->bindParam(':member', $member);
            $userStmt->execute();

            $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
            if (!$userRow) {
                continue; // Skip if no result
            }
            $staffIds[] = $userRow['employee_id'];
            $usernames[] = $userRow['username'];
        }

        // Fetch the first_name and last_name from sys_hr_employee based on employee_ids
        $fullNames = array();
        foreach ($staffIds as $id) {
            $staffQuery = "SELECT id, first_name, last_name FROM sys_hr_employee WHERE id = :id";
            $staffStmt = $conn->prepare($staffQuery);
            $staffStmt->bindParam(':id', $id);
            $staffStmt->execute();

            $staffRow = $staffStmt->fetch(PDO::FETCH_ASSOC);
            if (!$staffRow) {
                continue; // Skip if no result
            }
            $fullNames[] = $staffRow['first_name'] . ' ' . $staffRow['last_name'];
        }

        $row['team_members'] = $fullNames;
        $row['team_usernames'] = $usernames;

        // Close the database connection
        $conn = null;

        return [$row];
    }

    public static function surveyMembersAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_team
        $query = "SELECT * FROM flw_survey_reports_asb 
        WHERE system_id = :systemID
        ORDER BY id DESC LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there are results
        if (!$row) {
            return []; // Return an empty array if no results
        }

        // Remove the curly braces {} from the team_members field
        $teamMembers = !empty($row['team_members']) ? trim($row['team_members'], '{}') : '';
        $teamMembers = $teamMembers ? explode(',', $teamMembers) : [];
        $teamMembers = array_map('trim', $teamMembers);

        // Fetch the employee_id and username from sys_users table based on team_members
        $staffIds = array();
        $usernames = array();
        foreach ($teamMembers as $member) {
            $userQuery = "SELECT employee_id, username FROM sys_users WHERE username = :member";
            $userStmt = $conn->prepare($userQuery);
            $userStmt->bindParam(':member', $member);
            $userStmt->execute();

            $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
            if (!$userRow) {
                continue; // Skip if no result
            }
            $staffIds[] = $userRow['employee_id'];
            $usernames[] = $userRow['username'];
        }

        // Fetch the first_name and last_name from sys_hr_employee based on employee_ids
        $fullNames = array();
        foreach ($staffIds as $id) {
            $staffQuery = "SELECT id, first_name, last_name FROM sys_hr_employee WHERE id = :id";
            $staffStmt = $conn->prepare($staffQuery);
            $staffStmt->bindParam(':id', $id);
            $staffStmt->execute();

            $staffRow = $staffStmt->fetch(PDO::FETCH_ASSOC);
            if (!$staffRow) {
                continue; // Skip if no result
            }
            $fullNames[] = $staffRow['first_name'] . ' ' . $staffRow['last_name'];
        }

        $row['team_members'] = $fullNames;
        $row['team_usernames'] = $usernames;

        // Close the database connection
        $conn = null;

        return [$row];
    }

    public static function surveyLogBookUdm($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_team
        $query = "SELECT log.*, team.survey_team, team.team_members
        FROM flw_survey_reports_udm AS log
        INNER JOIN flw_survey_team AS team ON log.survey_team::bigint = team.id::bigint
        WHERE log.system_id = :systemID
        ORDER BY log.id DESC
        LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there are results
        if ($row) {
            // Remove the curly braces {} from the team_members field
            $teamMembers = trim($row['team_members'], '{}');

            // Split the team members' names into an array
            $teamMembers = explode(',', $teamMembers);

            // Trim whitespace from each team member's name
            $teamMembers = array_map('trim', $teamMembers);

            // Fetch the employee_id and username from sys_users table based on team_members
            $staffIds = array();
            $usernames = array();
            foreach ($teamMembers as $member) {
                $userQuery = "SELECT employee_id, username FROM sys_users WHERE username = :member";
                $userStmt = $conn->prepare($userQuery);
                $userStmt->bindParam(':member', $member);
                $userStmt->execute();

                $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
                $staffIds[] = $userRow['employee_id'];
                $usernames[] = $userRow['username'];
            }

            // Fetch the first_name and last_name from sys_hr_employee based on employee_ids
            $fullNames = array();
            foreach ($staffIds as $id) {
                $staffQuery = "SELECT id, first_name, last_name FROM sys_hr_employee WHERE id = :id";
                $staffStmt = $conn->prepare($staffQuery);
                $staffStmt->bindParam(':id', $id);
                $staffStmt->execute();

                $staffRow = $staffStmt->fetch(PDO::FETCH_ASSOC);
                $fullNames[] = $staffRow['first_name'] . ' ' . $staffRow['last_name'];
            }

            $row['team_members'] = $fullNames;
            $row['team_usernames'] = $usernames;
        }

        // Close the database connection
        $conn = null;

        return $row ? [$row] : [];
    }

    public static function surveyLogBookAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_team
        $query = "SELECT log.*, team.survey_team, team.team_members
        FROM flw_survey_reports_asb AS log
        INNER JOIN flw_survey_team AS team ON log.survey_team::bigint = team.id::bigint
        WHERE log.system_id = :systemID
        ORDER BY log.id DESC
        LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there are results
        if ($row) {
            // Remove the curly braces {} from the team_members field
            $teamMembers = trim($row['team_members'], '{}');

            // Split the team members' names into an array
            $teamMembers = explode(',', $teamMembers);

            // Trim whitespace from each team member's name
            $teamMembers = array_map('trim', $teamMembers);

            // Fetch the employee_id and username from sys_users table based on team_members
            $staffIds = array();
            $usernames = array();
            foreach ($teamMembers as $member) {
                $userQuery = "SELECT employee_id, username FROM sys_users WHERE username = :member";
                $userStmt = $conn->prepare($userQuery);
                $userStmt->bindParam(':member', $member);
                $userStmt->execute();

                $userRow = $userStmt->fetch(PDO::FETCH_ASSOC);
                $staffIds[] = $userRow['employee_id'];
                $usernames[] = $userRow['username'];
            }

            // Fetch the first_name and last_name from sys_hr_employee based on employee_ids
            $fullNames = array();
            foreach ($staffIds as $id) {
                $staffQuery = "SELECT id, first_name, last_name FROM sys_hr_employee WHERE id = :id";
                $staffStmt = $conn->prepare($staffQuery);
                $staffStmt->bindParam(':id', $id);
                $staffStmt->execute();

                $staffRow = $staffStmt->fetch(PDO::FETCH_ASSOC);
                $fullNames[] = $staffRow['first_name'] . ' ' . $staffRow['last_name'];
            }

            $row['team_members'] = $fullNames;
            $row['team_usernames'] = $usernames;
        }

        // Close the database connection
        $conn = null;

        return $row ? [$row] : [];
    }

    public static function getAllSurveyWorkData($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_reports_udm
        $query = "SELECT DISTINCT unnest(survey_work) AS survey_work
        FROM flw_survey_reports_udm
        WHERE system_id = :systemID AND array_length(survey_work, 1) > 0";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $descriptions = array(
            '1' => 'MENJALANKAN KERJA-KERJA PENGESANAN UTILITI BAWAH TANAH',
            '2' => 'MENJALANKAN KERJA-KERJA PENGUKURAN TOPOGRAFI',
            // Add more mappings as needed
        );

        $data = array();

        foreach ($result as $row) {
            // Split the survey_work string into an array of values
            $values = $row['survey_work'];

            // Remove any duplicates and trim whitespace
            $uniqueValues = array_map('trim', array_unique(explode(',', $values)));

            // Map values to their descriptions
            $descriptionsArray = array_map(function ($value) use ($descriptions) {
                return $descriptions[$value] ?? $value;
            }, $uniqueValues);

            // Concatenate the descriptions into a comma-separated string
            $commaSeparatedDescriptions = implode(', ', $descriptionsArray);

            // Add the comma-separated descriptions to the result array
            $data[] = $commaSeparatedDescriptions;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function getAllSurveyWorkDataAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_reports_udm
        $query = "SELECT DISTINCT unnest(survey_work) AS survey_work
        FROM flw_survey_reports_asb
        WHERE system_id = :systemID AND array_length(survey_work, 1) > 0";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $descriptions = array(
            '1' => 'MENJALANKAN KERJA-KERJA PENGESANAN UTILITI BAWAH TANAH',
            '2' => 'MENJALANKAN KERJA-KERJA PENGUKURAN TOPOGRAFI',
            // Add more mappings as needed
        );

        $data = array();

        foreach ($result as $row) {
            // Split the survey_work string into an array of values
            $values = $row['survey_work'];

            // Remove any duplicates and trim whitespace
            $uniqueValues = array_map('trim', array_unique(explode(',', $values)));

            // Map values to their descriptions
            $descriptionsArray = array_map(function ($value) use ($descriptions) {
                return $descriptions[$value] ?? $value;
            }, $uniqueValues);

            // Concatenate the descriptions into a comma-separated string
            $commaSeparatedDescriptions = implode(', ', $descriptionsArray);

            // Add the comma-separated descriptions to the result array
            $data[] = $commaSeparatedDescriptions;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function getAllEquipmentData($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_reports_udm
        $query = "SELECT DISTINCT unnest(equipment) AS equipment
        FROM flw_survey_reports_udm
        WHERE system_id = :systemID AND array_length(equipment, 1) > 0";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $descriptions = array(
            '1' => 'GLOBAL POSITIONING SYSTEM CHC i73 (CHC)',
            '2' => 'RADIO DETECTION 8200',
            '3' => 'RODA PENGUKURAN JARAK',
            '4' => 'GROUND PENETRATING RADAR (GPR) MALA',
            '5' => 'PENGESAN ELEKTRO MAGNETIK (EML) / PCL',
            // Add more mappings as needed
        );

        $data = array();

        foreach ($result as $row) {
            // Split the survey_work string into an array of values
            $values = $row['equipment'];

            // Remove any duplicates and trim whitespace
            $uniqueValues = array_map('trim', array_unique(explode(',', $values)));

            // Map values to their descriptions
            $descriptionsArray = array_map(function ($value) use ($descriptions) {
                return $descriptions[$value] ?? $value;
            }, $uniqueValues);

            // Concatenate the descriptions into a comma-separated string
            $commaSeparatedDescriptions = implode(', ', $descriptionsArray);

            // Add the comma-separated descriptions to the result array
            $data[] = $commaSeparatedDescriptions;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function getAllEquipmentDataAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database with a join on flw_survey_reports_asb
        $query = "SELECT DISTINCT unnest(equipment) AS equipment
        FROM flw_survey_reports_asb
        WHERE system_id = :systemID AND array_length(equipment, 1) > 0";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $descriptions = array(
            '1' => 'GLOBAL POSITIONING SYSTEM CHC i73 (CHC)',
            '2' => 'RADIO DETECTION 8200',
            '3' => 'RODA PENGUKURAN JARAK',
            '4' => 'GROUND PENETRATING RADAR (GPR) MALA',
            '5' => 'PENGESAN ELEKTRO MAGNETIK (EML) / PCL',
            // Add more mappings as needed
        );

        $data = array();

        foreach ($result as $row) {
            // Split the survey_work string into an array of values
            $values = $row['equipment'];

            // Remove any duplicates and trim whitespace
            $uniqueValues = array_map('trim', array_unique(explode(',', $values)));

            // Map values to their descriptions
            $descriptionsArray = array_map(function ($value) use ($descriptions) {
                return $descriptions[$value] ?? $value;
            }, $uniqueValues);

            // Concatenate the descriptions into a comma-separated string
            $commaSeparatedDescriptions = implode(', ', $descriptionsArray);

            // Add the comma-separated descriptions to the result array
            $data[] = $commaSeparatedDescriptions;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function generateNumberedNames($teamMembers)
    {
        if ($teamMembers === null) {
            return ''; // Return an empty string if team members data is missing or null
        }

        $html = ''; // Initialize an empty string to store the HTML

        foreach ($teamMembers as $index => $name) {
            $number = $index + 1; // Generate the automatic number

            $html .= $number . '. ' . $name . '<br>'; // Concatenate the numbered name to the HTML
        }

        return $html; // Return the HTML string
    }

    public static function timeAttend($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $current_date = date('Y-m-d');

        // Execute a SELECT query on the database
        $query = "SELECT * FROM flw_survey_attandance WHERE system_id = :systemId AND DATE(created_timestamp) = :currentDate AND clock_out IS NOT NULL ORDER BY created_timestamp DESC LIMIT 1";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':currentDate', $current_date);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $row;
    }

    public static function getSurveyImageData($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Get all id_repeat values for the given system_id from flw_survey_reports_udm
        $queryGetIdRepeats = "SELECT id_repeat FROM flw_survey_reports_udm WHERE system_id = :systemId";
        $stmtIdRepeats = $conn->prepare($queryGetIdRepeats);
        $stmtIdRepeats->bindParam(':systemId', $systemId);

        // Execute the query
        $stmtIdRepeats->execute();

        // Fetch the result as an associative array
        $resultIdRepeats = $stmtIdRepeats->fetchAll(PDO::FETCH_ASSOC);

        $surveyImageData = [];

        // Loop through each id_repeat and fetch its corresponding data from flw_survey_report_images_udm
        foreach ($resultIdRepeats as $rowIdRepeat) {
            $idRepeat = $rowIdRepeat['id_repeat'];

            // Fetch data from flw_survey_report_images_udm for the current id_repeat
            $queryGetImageData = "SELECT url, category, description FROM flw_survey_report_images_udm WHERE id_repeat = :idRepeat AND system_id = :systemId ORDER BY id ASC";
            $stmtImageData = $conn->prepare($queryGetImageData);
            $stmtImageData->bindParam(':idRepeat', $idRepeat);
	    $stmtImageData->bindParam(':systemId', $systemId);


            // Execute the query
            $stmtImageData->execute();

            // Fetch the result as an associative array
            $resultImageData = $stmtImageData->fetchAll(PDO::FETCH_ASSOC);

            // Decode base64 for each 'url' in $resultImageData
            foreach ($resultImageData as &$imageData) {
                $imageData['url'] = base64_decode($imageData['url']);
            }

            // Add the fetched data to $surveyImageData array
            $surveyImageData = array_merge($surveyImageData, $resultImageData);
        }

        // Close the database connection
        // $conn = null;

        return $surveyImageData;
    }

    public static function getSurveyImageDataAsb($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Get all id_repeat values for the given system_id from flw_survey_reports_asb
        $queryGetIdRepeats = "SELECT id_repeat FROM flw_survey_reports_asb WHERE system_id = :systemId";
        $stmtIdRepeats = $conn->prepare($queryGetIdRepeats);
        $stmtIdRepeats->bindParam(':systemId', $systemId);

        // Execute the query
        $stmtIdRepeats->execute();

        // Fetch the result as an associative array
        $resultIdRepeats = $stmtIdRepeats->fetchAll(PDO::FETCH_ASSOC);

        $surveyImageData = [];

        // Loop through each id_repeat and fetch its corresponding data from flw_survey_report_images_asb
        foreach ($resultIdRepeats as $rowIdRepeat) {
            $idRepeat = $rowIdRepeat['id_repeat'];

            // Fetch data from flw_survey_report_images_udm for the current id_repeat
            $queryGetImageData = "SELECT url, category, description FROM flw_survey_report_images_asb WHERE id_repeat = :idRepeat ORDER BY id ASC";
            $stmtImageData = $conn->prepare($queryGetImageData);
            $stmtImageData->bindParam(':idRepeat', $idRepeat);

            // Execute the query
            $stmtImageData->execute();

            // Fetch the result as an associative array
            $resultImageData = $stmtImageData->fetchAll(PDO::FETCH_ASSOC);

            // Decode base64 for each 'url' in $resultImageData
            foreach ($resultImageData as &$imageData) {
                $imageData['url'] = base64_decode($imageData['url']);
            }

            // Add the fetched data to $surveyImageData array
            $surveyImageData = array_merge($surveyImageData, $resultImageData);
        }

        // Close the database connection
        // $conn = null;

        return $surveyImageData;
    }

    public static function getProgressUDM($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Prepare and execute the SELECT query
        $query = "SELECT progress_udm FROM flw_plan_udm WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Check if any rows were returned
        if ($result !== false) {
            // Get the value of progress_udm
            $progressUDM = $result['progress_udm'];

            // Return 0 if the value is null or 0
            return ($progressUDM === null || $progressUDM == 0) ? 0 : $progressUDM;
        } else {
            // If there's no data, return 0
            return 0;
        }
    }

    public static function getProgressTMP($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Prepare and execute the SELECT query
        $query = "SELECT progress_tmp FROM flw_plan_tmp WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Check if any rows were returned
        if ($result !== false) {
            // Get the value of progress_tmp
            $progressTMP = $result['progress_tmp'];

            // Return 0 if the value is null or 0
            return ($progressTMP === null || $progressTMP == 0) ? 0 : $progressTMP;
        } else {
            // If there's no data, return 0
            return 0;
        }
    }

    public static function getProgressASB($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Prepare and execute the SELECT query
        $query = "SELECT progress_asb FROM flw_plan_asb WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Check if any rows were returned
        if ($result !== null) {
            // Get the value of progress_udm
            $progressASB = $result['progress_asb'];

            // Return 0 if the value is null or 0
            return ($progressASB === null || $progressASB == 0) ? 0 : $progressASB;
        } else {
            // If there's no data, return 0
            return 0;
        }
    }

    //Get the status of the project
    public static function getProjectStatus($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Prepare and execute the SELECT query
        $query = "SELECT project_status FROM status_appl WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result;
    }

    public static function getcurrentStatus($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Prepare and execute the SELECT query
        $query = "SELECT status_id FROM ctrl_statuses WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result;
    }

    //Get data for widget distance overview
    public static function getMetrics()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Calculate Jarak Mohon
        $queryMohon = "SELECT SUM(application_length) as total_mohon FROM flw_appl_entries";
        $stmtMohon = $conn->query($queryMohon);
        $rowMohon = $stmtMohon->fetch(PDO::FETCH_ASSOC);
        $totalMohon = $rowMohon['total_mohon'];

        // Calculate Jarak Ukur
        $queryUkur = "SELECT system_id, MAX(current_progress) as latest_progress FROM flw_survey_reports_udm GROUP BY system_id";
        $stmtUkur = $conn->query($queryUkur);
        $totalUkur = 0;

        while ($rowUkur = $stmtUkur->fetch(PDO::FETCH_ASSOC)) {
            $totalUkur += $rowUkur['latest_progress'];
        }

        // Calculate Beza Jarak
        $bezaJarak = $totalMohon - $totalUkur;

        // Calculate Jarak GIS
        $queryGIS = "SELECT SUM(gis_length) as total_gis FROM flw_appl_verifies";
        $stmtGIS = $conn->query($queryGIS);
        $rowGIS = $stmtGIS->fetch(PDO::FETCH_ASSOC);
        $totalGIS = $rowGIS['total_gis'];

        // Return 0 if any total is null or 0
        $totalMohon = $totalMohon === null || $totalMohon === 0 ? 0 : $totalMohon;
        $totalUkur = $totalUkur === null || $totalUkur === 0 ? 0 : $totalUkur;
        $totalGIS = $totalGIS === null || $totalGIS === 0 ? 0 : $totalGIS;

        // Close the database connection
        $conn = null;

        return [
            "jarak_mohon" => $totalMohon,
            "jarak_ukur" => $totalUkur,
            "beza_jarak" => $bezaJarak,
            "jarak_gis" => $totalGIS,
        ];
    }

    //Get data for widget staff overview (in dashboard Surveys)
    public static function getStaffOverview()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Get team data from flw_survey_team
        $teamQuery = "SELECT id, survey_team, total_survey_member, profile_picture FROM flw_survey_team WHERE is_active = true";
        $stmtTeam = $conn->query($teamQuery);

        if (!$stmtTeam) {
            die ("Error in team query: " . $conn->errorInfo()[2]);
        }

        $staffOverview = array();

        while ($teamRow = $stmtTeam->fetch(PDO::FETCH_ASSOC)) {
            $teamName = $teamRow['survey_team'];
            $teamId = $teamRow['id'];
            $teamProfile = $teamRow['profile_picture'];
            $masaMasuk = "-";
            $masaKeluar = "-";
            $status = "Tidak dilantik";

            // Get survey reports for the current date
            $currentDate = date('Y-m-d');
            $reportQuery = "
            SELECT DISTINCT ON (system_id)
                survey_time_start, survey_time_end
            FROM
                flw_survey_reports_udm
            WHERE
                survey_team = :teamId
                AND survey_date::date = :currentDate
            ORDER BY system_id, id DESC
            LIMIT 1
        ";
            $stmtReport = $conn->prepare($reportQuery);
            $stmtReport->bindParam(':teamId', $teamId, PDO::PARAM_INT);
            $stmtReport->bindParam(':currentDate', $currentDate);
            $stmtReport->execute();

            if (!$stmtReport) {
                die ("Error in report query: " . $conn->errorInfo()[2]);
            }

            // Fetch the first row of the report result
            $reportRow = $stmtReport->fetch(PDO::FETCH_ASSOC);
            if ($reportRow) {
                // Data is available for the current date
                $masaMasuk = $reportRow['survey_time_start'];
                $masaKeluar = $reportRow['survey_time_end'];

                if (!empty ($masaMasuk) && !empty ($masaKeluar)) {
                    // Both survey_time_start and survey_time_end are present
                    $status = "Di pejabat";
                } elseif (!empty ($masaMasuk)) {
                    // Only survey_time_start is present
                    $status = "Di tapak";
                }
            } else {
                // Check if team is assigned in flw_appl_survey
                $teamIdQuery = "SELECT id FROM flw_survey_team WHERE survey_team = :teamName AND is_active = true";
                $stmtTeamId = $conn->prepare($teamIdQuery);
                $stmtTeamId->bindParam(':teamName', $teamName);
                $stmtTeamId->execute();

                if (!$stmtTeamId) {
                    die ("Error in team ID query: " . $conn->errorInfo()[2]);
                }

                $teamIdRow = $stmtTeamId->fetch(PDO::FETCH_ASSOC);
                $teamId = $teamIdRow['id'];

                $assignQuery = "SELECT * FROM flw_appl_survey WHERE team_id = :teamId";
                $stmtAssign = $conn->prepare($assignQuery);
                $stmtAssign->bindParam(':teamId', $teamId, PDO::PARAM_INT);
                $stmtAssign->execute();

                if (!$stmtAssign) {
                    die ("Error in assign query: " . $conn->errorInfo()[2]);
                }

                if ($stmtAssign->rowCount() > 0) {
                    $status = "Telah Dilantik";
                }
            }

            $teamData = array(
                'team_name' => $teamName,
                'profile_picture' => $teamProfile,
                'total_members' => $teamRow['total_survey_member'],
                'masa_masuk' => $masaMasuk,
                'masa_keluar' => $masaKeluar,
                'status' => $status
            );

            $staffOverview[] = $teamData;
        }

        // Close the database connection
        $conn = null;

        return $staffOverview;
    }

    //Get data for widget staff workload (for dashboard Head Survey & Plan)
    public static function getStaffWorkloadData()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $staffWorkloadData = array();

        // Retrieve staff data from sys_users where role_id is 63 or 66
        $staffQuery = "SELECT employee_id, username, profile_pic FROM sys_users WHERE role_id IN (63, 66)";
        $stmtStaff = $conn->query($staffQuery);

        if (!$stmtStaff) {
            die ("Error in staff query: " . $conn->errorInfo()[2]);
        }

        while ($staffData = $stmtStaff->fetch(PDO::FETCH_ASSOC)) {
            $staffId = $staffData['employee_id'];
            $username = $staffData['username'];
            $profilePic = $staffData['profile_pic'];

            // Retrieve first name from sys_hr_employee
            $query = "SELECT first_name FROM sys_hr_employee WHERE id = :staffId";
            $stmtFirstName = $conn->prepare($query);
            $stmtFirstName->bindParam(':staffId', $staffId, PDO::PARAM_INT);
            $stmtFirstName->execute();

            if (!$stmtFirstName) {
                die ("Error in staff query: " . $conn->errorInfo()[2]);
            }

            $staffRow = $stmtFirstName->fetch(PDO::FETCH_ASSOC);
            $firstName = $staffRow['first_name'];

            // Count workload based on flw_plan_udm data for the staff
            $workloadQuery = "SELECT COUNT(*) AS total FROM flw_plan_udm WHERE plan_udm_assignee = :username";
            $stmtWorkload = $conn->prepare($workloadQuery);
            $stmtWorkload->bindParam(':username', $username);
            $stmtWorkload->execute();

            if (!$stmtWorkload) {
                die ("Error in workload query: " . $conn->errorInfo()[2]);
            }

            $workloadRow = $stmtWorkload->fetch(PDO::FETCH_ASSOC);
            $totalWorkload = $workloadRow['total'];

            // Create an array for staff data
            $staffData = array(
                'username' => $username,
                'firstName' => $firstName,
                'profilePic' => $profilePic,
                'totalWorkload' => $totalWorkload
            );

            // Add staff data to the main array
            $staffWorkloadData[] = $staffData;
        }

        // Close the database connection
        $conn = null;

        return $staffWorkloadData;
    }

    //Function to get data for widget staff attandance (dashboard team survey)
    public static function getSurveyStaffData($username)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Check if the username exists in flw_survey_team
        $teamQuery = "SELECT survey_team, total_survey_member, profile_picture FROM flw_survey_team WHERE :username = ANY (team_members) AND is_active = true";
        $stmtTeam = $conn->prepare($teamQuery);
        $stmtTeam->bindParam(':username', $username);
        $stmtTeam->execute();

        if (!$stmtTeam) {
            die ("Error in team query: " . $conn->errorInfo()[2]);
        }

        $teamData = $stmtTeam->fetch(PDO::FETCH_ASSOC);

        if (!$teamData) {
            // No team data found, status is "Tidak dilantik"
            return array(
                'team_name' => '',
                'total_members' => '',
                'profile_picture' => '',
                'status' => 'Tidak dilantik'
            );
        }

        $teamName = $teamData['survey_team'];
        $teamPicture = $teamData['profile_picture'];
        $totalMembers = $teamData['total_survey_member'];

        // Get the latest attendance data for the current date and username
        $currentDate = date('Y-m-d');
        $attendanceQuery = "
            SELECT clock_in, clock_out
            FROM flw_survey_attandance
            WHERE survey_username = :username
            AND created_timestamp::date = :currentDate
            ORDER BY created_timestamp DESC
            LIMIT 1
        ";
        $stmtAttendance = $conn->prepare($attendanceQuery);
        $stmtAttendance->bindParam(':username', $username);
        $stmtAttendance->bindParam(':currentDate', $currentDate);
        $stmtAttendance->execute();

        if (!$stmtAttendance) {
            die ("Error in attendance query: " . $conn->errorInfo()[2]);
        }

        $attendanceData = $stmtAttendance->fetch(PDO::FETCH_ASSOC);

        // Get the latest clock-out data for the current date and username
        $clockOutQuery = "
            SELECT clock_out
            FROM flw_survey_attandance
            WHERE created_timestamp::date = :currentDate
            AND clock_out IS NOT NULL
            ORDER BY created_timestamp DESC
            LIMIT 1
        ";
        $stmtClockOut = $conn->prepare($clockOutQuery);
        $stmtClockOut->bindParam(':currentDate', $currentDate);
        $stmtClockOut->execute();

        if (!$stmtClockOut) {
            die ("Error in clock out query: " . $conn->errorInfo()[2]);
        }

        $clockOutData = $stmtClockOut->fetch(PDO::FETCH_ASSOC);

        // Check if $clockOutData is an array and the 'clock_out' key is set
        if (is_array($clockOutData) && isset ($clockOutData['clock_out'])) {
            $attendanceData = $clockOutData['clock_out'];
        } else {
            $attendanceData = 'Tiada Data';
        }

        $status = '';

        if (!$attendanceData || !is_array($attendanceData)) {
            // No attendance data found, status is "Assign"
            $status = 'Dilantik';
        } elseif (isset ($attendanceData['clock_in']) && isset ($attendanceData)) {
            // Both clock-in and clock-out data found, status is "Office"
            $status = 'Di pejabat';
        } elseif (isset ($attendanceData['clock_in']) && !isset ($attendanceData)) {
            // Only clock-in data found, status is "Di tapak"
            $status = 'Di tapak';
        } else {
            // Unexpected scenario, handle as needed
            $status = 'Tiada';
        }

        // Close the database connection
        $conn = null;

        return array(
            'team_name' => $teamName,
            'total_members' => $totalMembers,
            'profile_picture' => $teamPicture,
            'clock_in' => isset ($attendanceData['clock_in']) ? $attendanceData : null,
            'clock_out' => isset ($attendanceData) ? $attendanceData : null,
            'status' => $status
        );
    }

    // Custom function to format English date to Malay date
    public static function formatMalayDate($englishDate)
    {
        $englishMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $malayMonths = ['Januari', 'Februari', 'Mac', 'April', 'Mei', 'Jun', 'Julai', 'Ogos', 'September', 'Oktober', 'November', 'Disember'];

        $malayDate = str_replace($englishMonths, $malayMonths, $englishDate);
        return $malayDate;
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

    public static function subSurveyAction($role, $app = null)
    {
        if ($app == 'UCIDOS' || $app == 'KITER' || $app == 'KUDRAT' || $app == null) {
            if ($role == 61) {
                $subMapping = '(2,9,10,12)';
            } elseif ($role == 62 || $role == 64 || $role == 65) {
                $subMapping = '(3,4,5,6,7,8)';
                // } elseif ($role == 65) {
                //     $subMapping = '(3,4,5,6,7,8)';
            } elseif ($role == 63 || $role == 66) {
                $subMapping = '(10,11,12,13,18)';
            } elseif ($role == 67) {
                $subMapping = '(14,15,16,17)';
            }
        } else {
        }

        return $subMapping;
    }

    public static function getSurveyName($user)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Query
        $queryEmployeeId = "SELECT name FROM view_users WHERE username = :username";
        $stmtEmployeeId = $conn->prepare($queryEmployeeId);
        $stmtEmployeeId->bindParam(':username', $user);
        // $stmtEmployeeId->execute();

        $stmtEmployeeId->execute();
        $name = $stmtEmployeeId->fetchAll(PDO::FETCH_COLUMN);

        // Close the database connection
        $conn = null;

        return $name;
    }

    public static function surveyTaskModal()
    {
        // Include the database connection parameters
        global $PDO;
        $conn = General::connectToDatabase();
        include "config/tenant.php";
        $role = $_SESSION['roleId'];
        $app = $appsTitle;

        // Execute query on the database
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

        $firstName = SurveyApi::getSurveyFirstname($username);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            // specific filtering on each needed status (primarily used for specific user assignment)
            if ($row['MappingID'] == 62 || $row['MappingID'] == 63) {
                if ($row['SurveyAssign'] != $firstName) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            } else if ($row['subMappingID'] == 11) {
                if ($row['UDMAssign'] != $username) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            } else if ($row['MappingID'] == 73) {
                if ($row['TMPAssign'] != $username) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            }

            // Format the StatusID field to have leading zeroes if it's less than 100
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

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function getUploadData($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Query to get submitted_udm and submitted_tmp from flw_appl_plan
        $query = "SELECT submitted_udm, submitted_tmp FROM flw_appl_plan WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();

        if (!$stmt) {
            die ("Error executing the query: " . $conn->errorInfo()[2]);
        }

        // Fetch the result row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Check the conditions and return the appropriate value
        if (empty ($row['submitted_udm']) && empty ($row['submitted_tmp'])) {
            return 0; // No data in both columns
        } elseif (!empty ($row['submitted_udm']) && empty ($row['submitted_tmp'])) {
            return 1; // Data in submitted_udm, but not in submitted_tmp
        } elseif (empty ($row['submitted_udm']) && !empty ($row['submitted_tmp'])) {
            return 2; // Data in submitted_udm, but not in submitted_tmp
        } elseif (!empty ($row['submitted_udm']) && !empty ($row['submitted_tmp'])) {
            return 3; // Data in both submitted_udm and submitted_tmp
        }
    }

    public static function getTotalSurvey()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // $currentYear = date('Y');

        // Execute a SELECT query on the database
        $query = "SELECT COUNT(id) AS total_application FROM flw_appl_mapping WHERE sub_mapping_status = 1";
        $stmt = $conn->query($query);

        if (!$stmt) {
            die ("Error in query: " . $conn->errorInfo()[2]);
        }

        // Fetch the result row as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Return the total count
        return $row['total_application'];
    }

    public static function getSurveyNotes($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Use a prepared statement to prevent SQL injection
        $query = "SELECT notes FROM flw_survey_udm WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);
        $stmt->execute();

        // Fetch the result row as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Return the notes, or a default message if no notes are found
        return $row ? $row['notes'] : "Tiada catatan";
    }

    public static function SurveyNotesModal()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT * FROM flw_survey_udm";
        $stmt = $conn->query($query);

        // Fetch all the rows as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Return the data
        return $data;
    }

    public static function getSurveyLength($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch application_length and progress_pending
        $query = "SELECT application_length, progress_pending FROM flw_survey_reports_udm WHERE system_id = :systemID ORDER BY id DESC LIMIT 1";
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
        $conn = null;

        return $jarakDiukur;
    }

    public static function getSurveyLengthAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

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
        $conn = null;

        return $jarakDiukur;
    }

    public static function getStartCoordinates($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the first latitude_start and longitude_start for the given system_id
        $query = "SELECT latitude_start, longitude_start FROM flw_survey_reports_udm WHERE system_id = :systemID ORDER BY id ASC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing latitude_start and longitude_start
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $row; // This will return an associative array with 'latitude_start' and 'longitude_start'
    }

    public static function getStartCoordinatesAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the first latitude_start and longitude_start for the given system_id
        $query = "SELECT latitude_start, longitude_start FROM flw_survey_reports_asb WHERE system_id = :systemID ORDER BY id ASC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing latitude_start and longitude_start
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $row; // This will return an associative array with 'latitude_start' and 'longitude_start'
    }

    public static function getEndCoordinates($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the latest latitude_end and longitude_end for the given system_id
        $query = "SELECT latitude_end, longitude_end FROM flw_survey_reports_udm WHERE system_id = :systemID ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing latitude_end and longitude_end
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $row; // This will return an associative array with 'latitude_end' and 'longitude_end'
    }

    public static function getEndCoordinatesAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the latest latitude_end and longitude_end for the given system_id
        $query = "SELECT latitude_end, longitude_end FROM flw_survey_reports_asb WHERE system_id = :systemID ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing latitude_end and longitude_end
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $row; // This will return an associative array with 'latitude_end' and 'longitude_end'
    }

    public static function getCurrentQR($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        $currentDate = date('Y-m-d');

        // Execute a SELECT query on the database to fetch the latest qr_url for the given system_id
        $query = "SELECT qr_url FROM flw_survey_attandance WHERE system_id = :systemID AND clock_out IS NULL AND initial_code IS NOT NULL AND DATE(created_timestamp) = :currentDate";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->bindParam(':currentDate', $currentDate, PDO::PARAM_STR);
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

        return $row['qr_url']; // This will return the qr_url
    }

    public static function getStartAndEndDates($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the earliest and latest dates for the given system_id
        $query = "SELECT MIN(survey_date) AS earliest_date, MAX(survey_date) AS latest_date FROM flw_survey_reports_udm WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing earliest and latest dates
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Format the dates
        // $earliestDate = date('d F Y', strtotime($row['earliest_date']));
        // $latestDate = date('d F Y', strtotime($row['latest_date']));

        $earliestDate = $row['earliest_date'] ? date('d F Y', strtotime($row['earliest_date'])) : null;
        $latestDate = $row['latest_date'] ? date('d F Y', strtotime($row['latest_date'])) : null;
        $start_date = date('Y-m-d', strtotime($row['earliest_date']));
        $end_date = date('Y-m-d', strtotime($row['latest_date']));

        return [
            'earliest_date' => $earliestDate,
            'latest_date' => $latestDate,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
    }

    public static function getStartAndEndDatesAsb($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the earliest and latest dates for the given system_id
        $query = "SELECT MIN(created_timestamp) AS earliest_date, MAX(created_timestamp) AS latest_date FROM flw_survey_reports_asb WHERE system_id = :systemID";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the row containing earliest and latest dates
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        // Format the dates
        // $earliestDate = date('d F Y', strtotime($row['earliest_date']));
        // $latestDate = date('d F Y', strtotime($row['latest_date']));

        $earliestDate = $row['earliest_date'] ? date('d F Y', strtotime($row['earliest_date'])) : null;
        $latestDate = $row['latest_date'] ? date('d F Y', strtotime($row['latest_date'])) : null;
        $start_date = date('Y-m-d', strtotime($row['earliest_date']));
        $end_date = date('Y-m-d', strtotime($row['latest_date']));

        return [
            'earliest_date' => $earliestDate,
            'latest_date' => $latestDate,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
    }

    public static function generateUniqueId()
    {
        $randomBytes = random_bytes(64);
        $hexString = bin2hex($randomBytes);

        return $hexString;
    }

    public static function projectDetails($id, $selection, $type)
    {
        // Establish a database connection
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        if ($selection == 1) {
            //for query project details
            // $query = "SELECT * FROM public.view_data_entry WHERE system_id = '$id'";
            $query = "SELECT v.*, a.created_at
                        FROM public.view_data_entry v
                        LEFT JOIN flw_appl_entries a ON v.system_id = a.system_id
                        WHERE v.system_id = '$id'";
        } else if ($selection == 2) {
            if ($type == 1) {
                //for query attachment for survey UDM
                $attachment_type = '(15)';
                $query = "SELECT
               flw_appl_attachments.id AS \"AttachmentID\",
               flw_appl_attachments.url AS \"AttachmentUrl\",
               flw_appl_attachments.created_date AS \"AttacmenthDate\",
               flw_appl_attachments.attachment_type AS \"AttachmentType\",
               ls_attachments.details as \"AttachmentDetails\",
               ls_attachments.code_name as \"AttachmentCode\"
               FROM flw_appl_attachments
               LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type
               WHERE flw_appl_attachments.system_id = '$id' AND
                   flw_appl_attachments.attachment_type IN $attachment_type
               ORDER BY ls_attachments.id ASC";
            } else if ($type == 2) {
                //for query attachment for tmp
                $attachment_type = '(16)';
                $query = "SELECT
               flw_appl_attachments.id AS \"AttachmentID\",
               flw_appl_attachments.url AS \"AttachmentUrl\",
               flw_appl_attachments.created_date AS \"AttacmenthDate\",
               flw_appl_attachments.attachment_type AS \"AttachmentType\",
               ls_attachments.details as \"AttachmentDetails\",
               ls_attachments.code_name as \"AttachmentCode\"
               FROM flw_appl_attachments
               LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type
               WHERE flw_appl_attachments.system_id = '$id' AND
                   flw_appl_attachments.attachment_type IN $attachment_type
               ORDER BY ls_attachments.id ASC";
            }
        } else if ($selection == 3) {
            //for query contact
            $query = "SELECT
            full_name AS \"FullName\",
            email AS \"Email\",
            phone_no AS \"PhoneNo\",
            position AS \"Position\",
            type AS \"Type\",
            address_1 AS \"Address1\",
            address_2 AS \"Address2\",
            postcode AS \"Postcode\",
            city AS \"City\",
            state AS \"State\"
            FROM flw_appl_contacts
            WHERE system_id = '$id'
            ORDER BY id ASC";
        }

        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ANCHOR - temporarily set data = result for report summary page
        $data = $result;

        // // create an array to hold the query result
        // $data = array();

        // // fetch the rows from the query result as an associative array
        // while ($row = $result) {
        //     $data[] = $row;
        // }

        if ($selection == 3 || $selection == 4) {
            if (count($result) == 0) {
                $detail = "no record";
            } else {
                // convert the result to a Array
                $detail = $data;
            }
        } else {
            // convert the result to a Array
            $detail = $data;
        }

        // Close the database connection
        $conn = null;

        return $detail;
    }

    public static function checkASB($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the latest qr_url for the given system_id
        $query = "SELECT quote_services_included_approved FROM flw_appl_verifies WHERE system_id = :systemID";
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

        $data = $row['quote_services_included_approved'];

        // Convert the string of comma-separated integers into an array
        $data = str_replace([',', '{', '}'], '', $data);
        $dataArray = explode(' ', $data);

        // Get the last integer from the array
        $lastInteger = end($dataArray);

        //checkAsb
        $value = intval($lastInteger) > 0 ? 1 : 0;

        // Check if the last integer is greater than 0
        return $value;
    }

    public static function getRemarkUDM($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the latest qr_url for the given system_id
        $query = "SELECT plan_remark FROM flw_plan_udm WHERE system_id = :systemID";
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

        return $row['plan_remark']; // This will return the qr_url
    }

    public static function getRemarkTMP($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database to fetch the latest qr_url for the given system_id
        $query = "SELECT plan_remark FROM flw_plan_tmp WHERE system_id = :systemID";
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

        return $row['plan_remark']; // This will return the qr_url
    }

    public static function selectUsers($subDepartment)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();
        require_once "config/system.php";
        $system = new System;
        $state = $system->App->state;

        switch ($subDepartment) {
            case 'team_survey':
                $query = "SELECT survey_team AS name, team_profile AS url, survey_team_id AS value FROM view_users WHERE team_state = :state AND team_is_active = 'true' GROUP BY survey_team, team_profile, survey_team_id";
                break;
            case 'surveyor':
                $query = "SELECT full_name AS name, id AS value FROM ls_sr_contact WHERE state = :state";
                break;
            default:
                $query = "SELECT name, profile_pic AS url, username AS value, position AS description FROM view_users WHERE sub_department = :sub";
                break;
        }

        $stmt = $conn->prepare($query);
        if ($subDepartment == 'team_survey' || $subDepartment == 'surveyor') {
            $stmt->bindParam(':state', $state);
        } else {
            $stmt->bindParam(':sub', $subDepartment);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function listSurveyWork()
    {
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT * FROM ls_survey_work ORDER BY id ASC");
        $query->execute();

        // Fetch the result as an associative array
        $works = $query->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $works;
    }

    public static function listSurveyWorkDescription()
    {
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT * FROM ls_survey_work_desc ORDER BY id ASC");
        $query->execute();

        // Fetch the result as an associative array
        $worksDesc = $query->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $worksDesc;
    }

    public static function getUpdateSurveyWorkDataUdm($systemID)
    {
        $conn = General::connectToDatabase();

        $kategori = 'edit';

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT survey_work, equipment, category FROM flw_survey_reports_udm 
                            WHERE system_id = :systemId AND category = :kategori ORDER BY id DESC LIMIT 1");
        $query->bindParam(':systemId', $systemID);
        $query->bindParam(':kategori', $kategori);
        $query->execute();

        // echo "Number of rows found: " . $query->rowCount();

        if($query->rowCount() > 0) {
            $query = "SELECT survey_work, equipment 
            FROM flw_survey_reports_udm 
            WHERE system_id = :systemID 
            AND (array_length(survey_work, 1) > 0 OR array_length(equipment, 1) > 0) AND category = :kategori 
	        ORDER BY id DESC LIMIT 1";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemID', $systemID);
            $stmt->bindParam(':kategori', $kategori);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else{
            $query = "SELECT survey_work, equipment 
            FROM flw_survey_reports_udm 
            WHERE system_id = :systemID 
            AND (array_length(survey_work, 1) > 0 OR array_length(equipment, 1) > 0)";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemID', $systemID);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Initialize arrays to store all values
        $surveyWorks = [];
        $equipments = [];
        
        // Process each row
        foreach ($results as $row) {
            // Process survey_work
            if (!empty($row['survey_work'])) {
                $surveyWork = trim($row['survey_work'], '{}');
                $surveyWorkValues = $surveyWork ? explode(',', $surveyWork) : [];
                $surveyWorks = array_merge($surveyWorks, $surveyWorkValues);
            }
            
            // Process equipment
            if (!empty($row['equipment'])) {
                $equipment = trim($row['equipment'], '{}');
                $equipmentValues = $equipment ? explode(',', $equipment) : [];
                $equipments = array_merge($equipments, $equipmentValues);
            }
        }
        
        // Remove duplicates and clean values
        $surveyWorks = array_unique(array_map('trim', $surveyWorks));
        $equipments = array_unique(array_map('trim', $equipments));

        $returnData = [
            'survey_work' => array_values($surveyWorks),
            'equipment' => array_values($equipments)
        ];
        
        $conn = null;
        
        return $returnData;
    }

    public static function getUpdateSurveyWorkDataAsb($systemID)
    {
        $conn = General::connectToDatabase();

        $kategori = 'edit';

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT survey_work, equipment, category FROM flw_survey_reports_udm 
                            WHERE system_id = :systemId AND category = :kategori ORDER BY id DESC LIMIT 1");
        $query->bindParam(':systemId', $systemID);
        $query->bindParam(':kategori', $kategori);
        $query->execute();

        // echo "Number of rows found: " . $query->rowCount();

        if($query->rowCount() > 0) {
            $query = "SELECT survey_work, equipment 
            FROM flw_survey_reports_asb 
            WHERE system_id = :systemID 
            AND (array_length(survey_work, 1) > 0 OR array_length(equipment, 1) > 0) AND category = :kategori 
	        ORDER BY id DESC LIMIT 1";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemID', $systemID);
            $stmt->bindParam(':kategori', $kategori);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } else{
            $query = "SELECT survey_work, equipment 
            FROM flw_survey_reports_asb 
            WHERE system_id = :systemID 
            AND (array_length(survey_work, 1) > 0 OR array_length(equipment, 1) > 0)";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemID', $systemID);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Initialize arrays to store all values
        $surveyWorks = [];
        $equipments = [];
        
        // Process each row
        foreach ($results as $row) {
            // Process survey_work
            if (!empty($row['survey_work'])) {
                $surveyWork = trim($row['survey_work'], '{}');
                $surveyWorkValues = $surveyWork ? explode(',', $surveyWork) : [];
                $surveyWorks = array_merge($surveyWorks, $surveyWorkValues);
            }
            
            // Process equipment
            if (!empty($row['equipment'])) {
                $equipment = trim($row['equipment'], '{}');
                $equipmentValues = $equipment ? explode(',', $equipment) : [];
                $equipments = array_merge($equipments, $equipmentValues);
            }
        }
        
        // Remove duplicates and clean values
        $surveyWorks = array_unique(array_map('trim', $surveyWorks));
        $equipments = array_unique(array_map('trim', $equipments));

        $returnData = [
            'survey_work' => array_values($surveyWorks),
            'equipment' => array_values($equipments)
        ];
        
        $conn = null;
        
        return $returnData;
    }

    public static function totalDistanceUdm($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT pegging_distance, detection_distance FROM flw_survey_reports_udm WHERE system_id = :systemId AND category is null";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_pegging = 0;
        $total_detection = 0;

        foreach($rows as $row) {

            $total_pegging += $row['pegging_distance'];
            $total_detection += $row['detection_distance'];

            // Check if the arrays exist and count them
            // if (is_array($row['pegging_distance'])) {
            //     $total_pegging += count(array_filter($row['pegging_distance']));
            // }
            
            // if (is_array($row['detection_distance'])) {
            //     $total_detection += count(array_filter($row['detection_distance']));
            // }
        }
        $conn = null;

        return [
            'total_pegging' => $total_pegging,
            'total_detection' => $total_detection,
            'grand_total' => $total_pegging + $total_detection
        ];
    }

    public static function totalDistanceAsb($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT pegging_distance, detection_distance FROM flw_survey_reports_asb WHERE system_id = :systemId";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_pegging = 0;
        $total_detection = 0;

        foreach($rows as $row) {

            $total_pegging += $row['pegging_distance'];
            $total_detection += $row['detection_distance'];

            // Check if the arrays exist and count them
            // if (is_array($row['pegging_distance'])) {
            //     $total_pegging += count(array_filter($row['pegging_distance']));
            // }
            
            // if (is_array($row['detection_distance'])) {
            //     $total_detection += count(array_filter($row['detection_distance']));
            // }
        }
        $conn = null;

        return [
            'total_pegging' => $total_pegging,
            'total_detection' => $total_detection,
            'grand_total' => $total_pegging + $total_detection
        ];
    }

    public static function getCategoryReportsUdm($systemId)
    {
        $conn = General::connectToDatabase();

        $kategori = 'edit';

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT category FROM flw_survey_reports_udm WHERE system_id = :systemId AND category = :kategori ORDER BY id DESC LIMIT 1");
        $query->bindParam(':systemId', $systemId);
        $query->bindParam(':kategori', $kategori);
        $query->execute();

        // Fetch the result as an associative array
        $category = $query->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $category;
    }

    public static function getCategoryReportsAsb($systemId)
    {
        $conn = General::connectToDatabase();

        $kategori = 'edit';

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT category FROM flw_survey_reports_asb WHERE system_id = :systemId AND category = :kategori ORDER BY id DESC LIMIT 1");
        $query->bindParam(':systemId', $systemId);
        $query->bindParam(':kategori', $kategori);
        $query->execute();

        // Fetch the result as an associative array
        $category = $query->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $category;
    }

    public static function getUpdateDataReportUdm($systemId)
    {
        $conn = General::connectToDatabase();

        $kategori = 'edit';

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT survey_work, equipment, category FROM flw_survey_reports_udm 
                            WHERE system_id = :systemId AND category = :kategori ORDER BY id DESC LIMIT 1");
        $query->bindParam(':systemId', $systemId);
        $query->bindParam(':kategori', $kategori);
        $query->execute();

        // Fetch the result as an associative array
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if($query->rowCount() > 0) {
            $surveyWork = $data['survey_work'];
            $equipment = $data['equipment'];

            // Define mapping of numbers to words
            $workDescriptions = [
                '1' => 'MENJALANKAN KERJA-KERJA PENGESANAN UTILITI BAWAH TANAH',
                '2' => 'MENJALANKAN KERJA-KERJA PENGUKURAN TOPOGRAFI',
                // Add other mappings as needed
            ];

            $equipmentDescriptions = [
                '1' => 'GLOBAL POSITIONING SYSTEM CHC i73 (CHC)',
                '2' => 'RADIO DETECTION 8200',
                '3' => 'RODA PENGUKURAN JARAK',
                '4' => 'GROUND PENETRATING RADAR (GPR) MALA',
                '5' => 'PENGESAN ELEKTRO MAGNETIK (EML) / PCL',
                // Add other mappings as needed
            ];

            // Process survey_work
            if(!empty($surveyWork) || !empty($equipment)) {
                $workIds = explode(',', trim($surveyWork, '{}'));
                $workDescriptionsArray = array_map(function($id) use ($workDescriptions) {
                    return $workDescriptions[$id] ?? '';
                }, $workIds);
                // $workDescriptionsString = implode(', ', array_filter($workDescriptionsArray));

                $workDescriptionsString = ''; // Initialize an empty string to store the HTML

                foreach ($workDescriptionsArray as $index => $name) {
                    $number = $index + 1; // Generate the automatic number

                    $workDescriptionsString .= $number . '. ' . $name . '<br>'; // Concatenate the numbered name to the HTML
                }

                // Process equipment
                $equipmentIds = explode(',', trim($equipment, '{}'));
                $equipmentDescriptionsArray = array_map(function($id) use ($equipmentDescriptions) {
                    return $equipmentDescriptions[$id] ?? ''; // Map ID to description or empty
                }, $equipmentIds);
                // $equipmentDescriptionsString = implode('<br> ', array_filter($equipmentDescriptionsArray));

                $equipmentDescriptionsString = ''; // Initialize an empty string to store the HTML

                foreach ($equipmentDescriptionsArray as $index => $name) {
                    $number = $index + 1; // Generate the automatic number

                    $equipmentDescriptionsString .= $number . '. ' . $name . '<br>'; // Concatenate the numbered name to the HTML
                }

                // Close the database connection
                $conn = null;

                return [
                    'survey_work' => $workDescriptionsString,
                    'equipment' => $equipmentDescriptionsString,
                ];

            } else{
                return [
                    'survey_work' => '',
                    'equipment' => '',
                ];
            }
            
        }
    }

    public static function getUpdateDataReportAsb($systemId)
    {
        $conn = General::connectToDatabase();

        $kategori = 'edit';

        // Execute a SELECT query on the database
        $query = $conn->prepare("SELECT survey_work, equipment, category FROM flw_survey_reports_asb
                            WHERE system_id = :systemId AND category = :kategori ORDER BY id DESC LIMIT 1");
        $query->bindParam(':systemId', $systemId);
        $query->bindParam(':kategori', $kategori);
        $query->execute();

        // Fetch the result as an associative array
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if($query->rowCount() > 0) {
            $surveyWork = $data['survey_work'];
            $equipment = $data['equipment'];

            // Define mapping of numbers to words
            $workDescriptions = [
                '1' => 'MENJALANKAN KERJA-KERJA PENGESANAN UTILITI BAWAH TANAH',
                '2' => 'MENJALANKAN KERJA-KERJA PENGUKURAN TOPOGRAFI',
                // Add other mappings as needed
            ];

            $equipmentDescriptions = [
                '1' => 'GLOBAL POSITIONING SYSTEM CHC i73 (CHC)',
                '2' => 'RADIO DETECTION 8200',
                '3' => 'RODA PENGUKURAN JARAK',
                '4' => 'GROUND PENETRATING RADAR (GPR) MALA',
                '5' => 'PENGESAN ELEKTRO MAGNETIK (EML) / PCL',
                // Add other mappings as needed
            ];

            if(!empty($surveyWork) || !empty($equipment)) {
                // Process survey_work
                $workIds = explode(',', trim($surveyWork, '{}'));
                $workDescriptionsArray = array_map(function($id) use ($workDescriptions) {
                    return $workDescriptions[$id] ?? '';
                }, $workIds);
                // $workDescriptionsString = implode(', ', array_filter($workDescriptionsArray));

                $workDescriptionsString = ''; // Initialize an empty string to store the HTML
                foreach ($workDescriptionsArray as $index => $name) {
                    $number = $index + 1; // Generate the automatic number

                    $workDescriptionsString .= $number . '. ' . $name . '<br>'; // Concatenate the numbered name to the HTML
                }

                // Process equipment
                $equipmentIds = explode(',', trim($equipment, '{}'));
                $equipmentDescriptionsArray = array_map(function($id) use ($equipmentDescriptions) {
                    return $equipmentDescriptions[$id] ?? ''; // Map ID to description or empty
                }, $equipmentIds);
                // $equipmentDescriptionsString = implode('<br> ', array_filter($equipmentDescriptionsArray));

                $equipmentDescriptionsString = ''; // Initialize an empty string to store the HTML

                foreach ($equipmentDescriptionsArray as $index => $name) {
                    $number = $index + 1; // Generate the automatic number

                    $equipmentDescriptionsString .= $number . '. ' . $name . '<br>'; // Concatenate the numbered name to the HTML
                }

                // Close the database connection
                $conn = null;

                return [
                    'survey_work' => $workDescriptionsString,
                    'equipment' => $equipmentDescriptionsString,
                ];

            } else {
                return [
                    'survey_work' => '',
                    'equipment' => '',
                ];
            }
        }
    }

    public static function getSurveyImageDataReportUdm($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Get all id_repeat values for the given system_id from flw_survey_reports_udm
        $queryGetIdRepeats = "SELECT id_repeat FROM flw_survey_reports_udm WHERE system_id = :systemId AND category = 'edit' ";
        $stmtIdRepeats = $conn->prepare($queryGetIdRepeats);
        $stmtIdRepeats->bindParam(':systemId', $systemId);

        // Execute the query
        $stmtIdRepeats->execute();

        // Fetch the result as an associative array
        $resultIdRepeats = $stmtIdRepeats->fetchAll(PDO::FETCH_ASSOC);

        $surveyImageData = [];

        // Loop through each id_repeat and fetch its corresponding data from flw_survey_report_images_udm
        foreach ($resultIdRepeats as $rowIdRepeat) {
            $idRepeat = $rowIdRepeat['id_repeat'];

            // Fetch data from flw_survey_report_images_udm for the current id_repeat
            $queryGetImageData = "SELECT id, url, category, description FROM flw_survey_report_images_udm WHERE id_repeat = :idRepeat AND system_id = :systemId ORDER BY id ASC";
            $stmtImageData = $conn->prepare($queryGetImageData);
            $stmtImageData->bindParam(':idRepeat', $idRepeat);
	    $stmtImageData->bindParam(':systemId', $systemId);

            // Execute the query
            $stmtImageData->execute();

            // Fetch the result as an associative array
            $resultImageData = $stmtImageData->fetchAll(PDO::FETCH_ASSOC);

            // Decode base64 for each 'url' in $resultImageData
            foreach ($resultImageData as &$imageData) {
                $imageData['url'] = base64_decode($imageData['url']);
            }

            // Add the fetched data to $surveyImageData array
            $surveyImageData = array_merge($surveyImageData, $resultImageData);
        }

        // Close the database connection
        // $conn = null;

        return $surveyImageData;
    }

    public static function getSurveyImageDataReportAsb($systemId)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Get all id_repeat values for the given system_id from flw_survey_reports_udm
        $queryGetIdRepeats = "SELECT id_repeat FROM flw_survey_reports_asb WHERE system_id = :systemId AND category = 'edit' ";
        $stmtIdRepeats = $conn->prepare($queryGetIdRepeats);
        $stmtIdRepeats->bindParam(':systemId', $systemId);

        // Execute the query
        $stmtIdRepeats->execute();

        // Fetch the result as an associative array
        $resultIdRepeats = $stmtIdRepeats->fetchAll(PDO::FETCH_ASSOC);

        $surveyImageData = [];

        // Loop through each id_repeat and fetch its corresponding data from flw_survey_report_images_udm
        foreach ($resultIdRepeats as $rowIdRepeat) {
            $idRepeat = $rowIdRepeat['id_repeat'];

            // Fetch data from flw_survey_report_images_udm for the current id_repeat
            $queryGetImageData = "SELECT id, url, category, description FROM flw_survey_report_images_asb WHERE id_repeat = :idRepeat ORDER BY id ASC";
            $stmtImageData = $conn->prepare($queryGetImageData);
            $stmtImageData->bindParam(':idRepeat', $idRepeat);

            // Execute the query
            $stmtImageData->execute();

            // Fetch the result as an associative array
            $resultImageData = $stmtImageData->fetchAll(PDO::FETCH_ASSOC);

            // Decode base64 for each 'url' in $resultImageData
            foreach ($resultImageData as &$imageData) {
                $imageData['url'] = base64_decode($imageData['url']);
            }

            // Add the fetched data to $surveyImageData array
            $surveyImageData = array_merge($surveyImageData, $resultImageData);
        }

        // Close the database connection
        // $conn = null;

        return $surveyImageData;
    }

    public static function getCategorySvyUdm($systemId)
    {
        $conn = General::connectToDatabase();
        
        $stmt = $conn->prepare('SELECT survey_provider FROM flw_survey_udm
                WHERE system_id = :id');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $surveyProvider = $result['survey_provider'];

        // Map survey_provider values to specific return values
        if ($surveyProvider === 'Inhouse') {
            return 1; // Return 1 for Inhouse
        } elseif ($surveyProvider === 'Outsource') {
            return 2; // Return 2 for Outsource
        } else {
            return null; // Handle unexpected values
        }
    }

    public static function getTeamSvyUdm($systemId)
    {
        $conn = General::connectToDatabase();
        
        $stmt = $conn->prepare('SELECT survey_team FROM flw_survey_reports_udm
                WHERE system_id = :id ORDER BY id DESC LIMIT 1');        
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        $surveyTeam = $result['survey_team'];

        return $surveyTeam;
    }

    public static function getTeamSvyAsb($systemId)
    {
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT report.survey_team AS team_id, team.survey_team AS team_name FROM flw_survey_reports_asb report
                                        LEFT JOIN flw_survey_team team ON report.survey_team::bigint = team.id
                                        WHERE report.system_id = :id
                                        ORDER BY report.id DESC LIMIT 1');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result; 
    }

	public static function listProviderPKD ($systemID)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
        vot.reference_no AS \"RefNo\",
        -- vot.submission_code AS \"SubCode\",
        vot.project_title AS \"Title\",
        vot.system_id AS \"SysID\",
        vot.payment_method  AS \"PaymentMethod\",
        vot.\"districts\" AS \"District\",
        vot.application_length AS \"Length\",
        vot.\"provider_id\" AS \"ProviderID\",
        ls_provider.name AS \"Provider\",
        ls_provider.company_logo AS \"ProviderLogo\",
        vot.\"status\" AS \"Status\",
        vot.\"status_color\" AS \"StatusColor\",
        vot.\"status_icon\" AS \"StatusIcon\",
        vot.\"submitted_date\" AS \"SubmitDate\",
        -- vot.id AS \"ID\",
        -- vot.\"mapping_id\" AS \"MappingID\",
        vot.\"status_id\" AS \"StatusID\",
        vot.\"flow_department\" AS \"Department\",
        -- vot.report_no AS \"ReportNo\",
        -- vot.gis_assignee AS \"GISAssign\",
        -- vot.pil_submitted_by AS \"PILBy\",
        -- vot.pkd_assignee AS \"PKDAssign\",
        ta.*, lr.*,
	(SELECT SUM(road_length) 
            FROM flw_appl_roads 
            WHERE flw_appl_roads.system_id =  :systemID
            AND active = true) as sitevisit_length
        FROM public.view_tasks AS vot
        INNER JOIN flw_appl_entries AS ta ON vot.system_id = ta.system_id
        INNER JOIN flw_pkd_roads AS lr ON vot.system_id = lr.system_id
        LEFT JOIN ls_provider ON ls_provider.id = ta.utility_provider
        WHERE vot.system_id = :systemID";

        // Prepare the SELECT query
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemID', $systemID);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $data;
    }


}