<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';

class TeamPPKD
{
    private $dbFactory;
    private $Role;
    private $User;
    private $Table;
    private $Modal;
    private $system;

    public function __construct($username)
    {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->Table = new Table();
        $this->Modal = new Modal();
        $this->User = $username ?? $_SESSION['username'];
        $this->system = new System;
    }

    private function queryTasks()
    {
        $state = $this->system->App->state;
        $db = $this->dbFactory->createConnection();
       
        // $department = $this->Role->{$this->User}->department;
        // $statusId = $this->Role->getAssignment($this->User);

        $query = $db->prepare("SELECT
                                    uz.*,
                                    ua.zone_id,
                                    ARRAY_AGG(ua.username) AS username,
                                    (SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text)
                                    FROM sys_upi
                                    WHERE sys_upi.state_code::text = uz.state::text
                                    AND (sys_upi.district_code::text = ANY (uz.districts::text[]))) AS districts
                                FROM
                                    ls_user_zones AS uz
                                LEFT JOIN
                                    sys_user_assignments AS ua ON uz.id = ua.zone_id
                                WHERE
                                    uz.state = :state
                                GROUP BY
                                    uz.id, uz.name, uz.state, ua.zone_id;              
                            ");
        // Bind the parameter
        $query->bindParam(':state', $state, PDO::PARAM_INT); // Assuming $id is an integer
        // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;


    }

    private function getAttachment($systemId, $code)
    {
        $db = $this->dbFactory->createConnection();
        $query = "SELECT flw_appl_attachments.url, flw_appl_attachments.mime_type FROM flw_appl_attachments LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type WHERE ls_attachments.code_name = :code AND flw_appl_attachments.system_id = :systemId ORDER BY flw_appl_attachments.id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        $data = new stdClass();
        if ($stmt->rowCount() == 0) {
            $data = (object) [
                'url' => NULL,
                'mime_type' => NULL,
            ];
        } else {
            $data->url = $row->url;
            $data->mime_type = $row->mime_type;
        }

        return $data;


    }

    private function selectUsers($subDepartment)
    {
        $db = $this->dbFactory->createConnection();

        switch ($subDepartment) {
            case 'survey':
                $query = "SELECT survey_team AS name, team_profile AS url, survey_team_id AS value FROM view_users GROUP BY survey_team, team_profile, survey_team_id";
                break;
            default:
                $query = "SELECT name, profile_pic AS url, username AS value, position AS description FROM view_users WHERE sub_department = :sub";
                break;
        }

        $stmt = $db->prepare($query);
        if ($subDepartment !== 'survey') {
            $stmt->bindParam(':sub', $subDepartment);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    private function mappingModal($systemId, $statusId)
    {
        $mapping = new stdClass();

        return $mapping->$statusId ?? null;
    }

    private function getTableData()
    {
        $data = $this->queryTasks();


        // foreach ($data as $item) {
        //     // var_dump($item);
        //     switch ($item->id) {
        //         default:
        //             $item->route = $item->system_id . '-' . $item->status_id;
        //             break;
        //     }
        // }
        return $data;
    }

    private function getModalData()
    {
        $data = $this->queryTasks();

        $combinedData = [];

        foreach ($data as $item) {
            $id = $item->status_id;
            $modalData = $this->mappingModal($item->system_id, $id);

            $needed = (object) [
                'system_id' => $item->system_id,
                'status_id' => $item->status_id,
                'color' => $item->status_color,
                'id' => $item->system_id . '-' . $item->status_id,
            ];

            if ($modalData !== null) {
                // Merge the data from queryTasks and mappingModal
                $combinedItem = (object) array_merge((array) $needed, (array) $modalData);

                $combinedData[] = $combinedItem;
            }
        }

        return $combinedData;
    }

    public function getJSData($method)
    {
        $Role = new Roles();
        $department = $Role->{$this->User}->department;
    }

    public function __call($method, $args)
    {
        $section = $args[0];

        if ($method === 'table') {
            $getData = $this->getTableData();
            return $this->Table->render($section, $getData);

        }

        if ($method === 'modal') {
            $getData = $this->getModalData();
            return $this->Modal->render($section, $getData);
        }

        // if ($method === 'javascript') {
        //     $getData = $this->getJSData($section, $roleId);
        //     return  $this->Pages[$subDepartment]->Javascript->get($section, $getData);

        // }
    }

    public static function PPKDTeamModal()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT * FROM ls_user_zones";
        $stmt = $conn->query($query);

        // Fetch the result as an associative array
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $teams;
    }

    public static function selectPPKDEdit($subDepartment)
    {
       
        $db = General::connectToDatabase();

        switch ($subDepartment) {
            default:
                // $query = "SELECT name, profile_pic AS url, username AS value, position AS description 
                //         FROM view_users 
                //         WHERE sub_department = :sub";
                // $query = "SELECT v.name, v.profile_pic AS url, v.username AS value, v.position AS description
                //             FROM view_users v
                //             LEFT JOIN sys_user_assignments s ON v.username = s.username
                //             WHERE v.sub_department = :sub
                //             AND ((s.zone_id IS NULL) OR (s.zone_id = :id))";
                $query = "SELECT v.name, v.profile_pic AS url, v.username AS value, v.position AS description
                            FROM view_users v
                            LEFT JOIN sys_user_assignments s ON v.username = s.username
                            WHERE v.sub_department = :sub";
                break;
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':sub', $subDepartment);
        // $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function selectPPKDAdd($subDepartment)
    {
        $db = General::connectToDatabase();

        switch ($subDepartment) {
            default:
                // $query = "SELECT v.name, v.profile_pic AS url, v.username AS value, v.position AS description 
                //             FROM view_users v
                //             LEFT JOIN sys_user_assignments s ON v.username = s.username
                //             WHERE v.sub_department = :sub 
                //             AND v.role_id = 17
                //                 AND (s.zone_id IS NULL)";
                $query = "SELECT v.name, v.profile_pic AS url, v.username AS value, v.position AS description 
                            FROM view_users v
                            LEFT JOIN sys_user_assignments s ON v.username = s.username
                            WHERE v.sub_department = :sub 
                            AND (v.role_id = 17 OR v.role_id = 16)";
                break;
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':sub', $subDepartment);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function selectDistrictAdd($state)
    {
        $system = new System();
        $state = $system->App->state;
        $db = General::connectToDatabase();

        switch ($state) {
            default:
                $query = "SELECT MAX(su.id) as id, su.district_code, MAX(su.district_name) as district_name, MAX(su.state_code) as state_code
                            FROM sys_upi su
                            WHERE su.state_code = :state
                            GROUP BY su.district_code
                            HAVING NOT EXISTS (
                                SELECT 1
                                FROM ls_user_zones uz
                                WHERE uz.districts @> ARRAY[su.district_code] AND state = :state
                            )";
                break;
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':state', $state, PDO::PARAM_INT); // Assuming $id is an integer
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public static function selectDistrictEdit($state, $id)
    {
        $system = new System();
        $state = $system->App->state;
        $db = General::connectToDatabase();

        switch ($id) {
            default:
                $query = "SELECT MAX(su.id) as id, su.district_code, MAX(su.district_name) as district_name, MAX(su.state_code) as state_code
                            FROM sys_upi su
                            WHERE su.state_code = :state
                            GROUP BY su.district_code
                            HAVING NOT EXISTS (
                                SELECT 1
                                FROM ls_user_zones uz
                                WHERE uz.districts @> ARRAY[su.district_code]::character varying[] 
                                AND uz.id != :id  AND state = :state
                            )";
                break;
        }

        $stmt = $db->prepare($query);
        $stmt->bindParam(':state', $state, PDO::PARAM_INT); // Assuming $id is an integer
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

}