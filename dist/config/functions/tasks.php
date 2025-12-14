<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';
require_once 'config/functions/survey.php';

class Tasks
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

    public function getAllApplication()
    {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT a.*, b.*, c.*, 
        (SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
            FROM sys_upi
            WHERE sys_upi.state_code::text = a.state::text 
            AND (sys_upi.district_code::text = ANY (a.districts::text[]))) AS districts
            FROM flw_appl_entries a
            LEFT JOIN ctrl_statuses b ON a.system_id = b.system_id
            LEFT JOIN ls_statuses c ON b.status_id = c.id
            WHERE b.department = 'operation' 
            AND b.authority = 0
            ORDER BY a.created_at ASC;";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    private function queryGeneralTasks($filter, $phase)
    {
        $db = $this->dbFactory->createConnection();
        $department = $this->Role->{$this->User}->department;

        $statusId = $this->Role->getAssignment($this->User);

        if ($department == 'management') {
            $set_array = explode(',', str_replace(['{', '}'], '', $statusId)); // Convert string to array

            if (in_array(158, $set_array)) {
                $department = 'finance';
            } else {
                $department = $this->Role->{$this->User}->sub_department;
            }
        } else {
            $department = $this->Role->{$this->User}->department;
        }

        switch ($filter) {
            case 'new':
                $condition = "AND tasks.status_id = ANY (:statusId) AND tasks.status_id != 162 AND DATE(assign.created_at) >= CURRENT_DATE - INTERVAL '7 days' AND assign.completed = false ";
                break;
            case 'pending':
                $condition = "AND tasks.status_id = ANY (:statusId) AND tasks.status_id != 162 AND DATE(assign.created_at) < CURRENT_DATE - INTERVAL '7 days' AND assign.completed = false ";
                break;
            case 'done':
                $condition = "AND NOT tasks.status_id = ANY (:statusId) AND assign.completed_at IS NOT NULL AND assign.completed = true ";
                break;
            case 'regcharges':
                $condition = "AND tasks.status_id = 2 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'wo':
                $condition = "AND tasks.status_id = 6 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'pcl':
                $condition = "AND tasks.status_id = 8 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'reportlta':
                $condition = "AND tasks.status_id = 13 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'approvereport':
                $condition = "AND tasks.status_id = 15 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'amendtp':
                $condition = "AND tasks.status_id = 17 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'pil':
                $condition = "AND tasks.status_id = 24 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'checkpil':
                $condition = "AND tasks.status_id = 25 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'quotation':
                $condition = "AND tasks.status_id IN (27,28,29) AND assign.completed = false ";
                $statusId = null;
                break;
            case 'amendquotation':
                $condition = "AND tasks.status_id = 29 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'pkil':
                $condition = "AND tasks.status_id = 31 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'invoice':
                $condition = "AND tasks.status_id = 37 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'approvepiu':
                $condition = "AND tasks.status_id = 48 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'uploadpiu':
                $condition = "AND tasks.status_id = 55 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'datalot':
                $condition = "AND tasks.status_id = 63 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'pkpk':
                $condition = "AND tasks.status_id = 70 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'ppk':
                $condition = "AND tasks.status_id = 73 AND assign.completed = false ";
                $statusId = null;
                break;
            default:
                $condition = "";
                break;
        }

        switch ($_SESSION['sub_department']) {
            case 'plan':
                $joincondition = " LEFT JOIN flw_plan_udm udm ON udm.system_id = tasks.system_id 
                               LEFT JOIN flw_plan_tmp tmp ON tmp.system_id = tasks.system_id 
                               AND (udm.username = :username OR tmp.username = :username)";
                break;
            default:
                $joinCondition = "";
                break;
        }


        if ($phase !== NULL) {
            $condition .= " AND flow_phase = :phase";
        }

        $query = "SELECT * FROM view_tasks tasks LEFT JOIN flw_task_assignments assign ON assign.system_id = tasks.system_id";
        if (!empty($joinCondition)) {
            $query .= $joinCondition;
        }
        $query .= " AND assign.status_id = tasks.status_id 
        WHERE flow_department = :department AND username = :username AND authority = 0
          {$condition} ORDER BY created_at ASC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':username', $this->User);
        if ($statusId !== null) {
            $stmt->bindParam(':statusId', $statusId);
        }
        if ($phase !== NULL) {
            $stmt->bindParam(':phase', $phase);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;


    }

    private function getInvPercent($systemId)
    {
        $db = $this->dbFactory->createConnection();
        $query = "SELECT invoice_division, balance FROM flw_invoices WHERE system_id = :systemId";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = new stdClass();

        $data->percent = 0;
        $data->balance = 0;

        foreach ($row as $division) {
            $data->percent += $division->invoice_division;
            $data->balance += $division->balance;
        }

        return $data;
    }

    private function selectUsers($subDepartment)
    {
        $db = $this->dbFactory->createConnection();
        $state = $this->system->App->state;

        switch ($subDepartment) {
            case 'team_survey':
                $query = "SELECT survey_team AS name, team_profile AS url, survey_team_id AS value FROM view_users WHERE team_state = :state GROUP BY survey_team, team_profile, survey_team_id";
                break;
            case 'surveyor':
                $query = "SELECT full_name AS name, id AS value FROM ls_sr_contact WHERE state = :state";
                break;
            default:
                $query = "SELECT name, profile_pic AS url, username AS value, position AS description FROM view_users WHERE sub_department = :sub";
                break;
        }

        $stmt = $db->prepare($query);
        if ($subDepartment == 'team_survey' || $subDepartment == 'surveyor') {
            $stmt->bindParam(':state', $state);
        } else {
            $stmt->bindParam(':sub', $subDepartment);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    private function mappingGeneralModal($systemId, $statusId)
    {
        $mapping = new stdClass();
        include "config/lists/generalModal.php";
        return $mapping->$statusId ?? null;
    }

    private function getGeneralTableData($filter, $phase)
    {
        $data = $this->queryGeneralTasks($filter, $phase);

        foreach ($data as $item) {
            switch ($item->status_id) {
                // NOTE - declare task action route here
                case 4:
                    $item->route = "projects/wayleave/checking/{$item->system_id}";
                    break;
                case 5:
                    $item->route = "projects/wayleave/checking/{$item->system_id}";
                    break;
                case 10:
                    $item->route = "calendar/{$item->system_id}/1";
                    break;
                case 11:
                    $item->route = "projects/tasks/{$item->system_id}";
                    break;
                case 12:
                    $item->route = "projects/tasks/{$item->system_id}";
                    break;
                case 13:
                    $item->route = "projects/tasks/{$item->system_id}";
                    break;
                case 14:
                    $item->route = "projects/tasks/{$item->system_id}";
                    break;
                case 15:
                    $item->route = "reports/site/review/{$item->system_id}";
                    break;
                case 17:
                    $item->route = "projects/site/tp/amend/{$item->system_id}";
                    break;
                case 24:
                    $item->route = "geospatial/PIL/upload/{$item->system_id}";
                    break;
                case 30:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 31:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 32:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 33:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 34:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 35:
                    $item->route = $item->system_id . '-' . $item->status_id;
                    $item->percent = $this->getInvPercent($item->system_id)->percent;
                    break;
                case 41:
                    $item->route = 'custom-' . $item->system_id . '-' . $item->status_id;
                    break;
                case 45:
                    $item->route = "surveys/site/progressUdm/{$item->system_id}";
                    break;
                case 47:
                    $item->route = "surveys/site/reportUdm/{$item->system_id}";
                    break;
                case 48:
                    $item->route = "surveys/site/reportUdm/{$item->system_id}";
                    break;
                case 57:
                    $item->route = 'review-' . $item->system_id . '-' . $item->status_id;
                    break;
                case 59:
                    $item->route = 'review-' . $item->system_id . '-' . $item->status_id;
                    break;
                case 70:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 71:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 72:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 73:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 74:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 80:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 81:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 90:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 92:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 93:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 94:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 95:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 96:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 101:
                    $item->route = "surveys/site/progressAsb/{$item->system_id}";
                    break;
                case 103:
                    $item->route = "surveys/site/reportAsb/{$item->system_id}";
                    break;
                case 104:
                    $item->route = "surveys/site/reportAsb/{$item->system_id}";
                    break;
                case 120:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 121:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 122:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 123:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 125:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 146:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 147:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 148:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 134:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 136:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 137:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 138:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 139:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 140:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 141:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 142:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 143:
                    $item->route = "operation/authority/tasks/$filter/{$item->system_id}";
                    break;
                case 163:
                    $item->route = "projects/site/tp/amend/review/{$item->system_id}";
                    break;
                default:
                    $item->route = $item->system_id . '-' . $item->status_id;
                    break;
            }
        }
        return $data;
    }

    private function getGeneralModalData($filter, $phase)
    {
        $data = $this->queryGeneralTasks($filter, $phase);

        $combinedData = [];

        foreach ($data as $item) {
            $id = $item->status_id;
            $modalData = $this->mappingGeneralModal($item->system_id, $id);

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
            } else {
                $combinedItem = (object) (array) $needed;

                $combinedData[] = $combinedItem;
            }
        }

        return $combinedData;
    }

    //authority
    private function queryAuthorityTasks($filter, $phase, $id)
    {
        $db = $this->dbFactory->createConnection();
        $department = $this->Role->{$this->User}->department;
        if ($department == 'management') {
            $department = $this->Role->{$this->User}->sub_department;
        } else {
            $department = $this->Role->{$this->User}->department;
        }
        $statusId = $this->Role->getAssignment($this->User);

        switch ($filter) {
            case 'new':
                $condition = "AND tasks.status_id = ANY (:statusId) AND DATE(assign.created_at) >= CURRENT_DATE - INTERVAL '7 days' AND assign.completed = false ";
                break;
            case 'pending':
                $condition = "AND tasks.status_id = ANY (:statusId) AND DATE(assign.created_at) < CURRENT_DATE - INTERVAL '7 days' AND assign.completed = false ";
                break;
            case 'done':
                $condition = "AND NOT tasks.status_id = ANY (:statusId) AND assign.completed_at IS NOT NULL AND assign.completed = true ";
                break;
            case 'regcharges':
                $condition = "AND tasks.status_id = 2 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'wo':
                $condition = "AND tasks.status_id = 6 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'quotation':
                $condition = "AND tasks.status_id = 28 AND assign.completed = false ";
                $statusId = null;
                break;
            case 'invoice':
                $condition = "AND tasks.status_id = 37 AND assign.completed = false ";
                $statusId = null;
                break;
            default:
                $condition = "";
                break;
        }

        if ($phase !== NULL) {
            $condition .= "AND flow_phase = :phase ";
        }

        if ($id !== NULL) {
            $condition .= "AND tasks.system_id = :id";
        }

        $query = "SELECT task.*
                FROM (SELECT tasks.*, status.authority
                      ,assign.username, assign.created_at, ls_authorities.sort_name, ls_authorities.logo
                      FROM public.view_tasks tasks
                LEFT JOIN ctrl_statuses status ON status.system_id = tasks.system_id AND status.status_id = tasks.status_id
                LEFT JOIN flw_task_assignments assign ON assign.system_id = status.system_id AND assign.status_id = status.status_id AND assign.authority = status.authority
                LEFT JOIN ls_authorities ON ls_authorities.id = status.authority
                WHERE status.authority != 0
                AND flow_department = :department
                AND username = :username
                {$condition}
                GROUP BY status.authority, tasks.reference_no,tasks.system_id,tasks.project_title,tasks.payment_method,tasks.length_code,tasks.application_length, tasks.approved_date, tasks.submitted_date, tasks.created_date,tasks.districts, tasks.provider_id, tasks.status_id, tasks.flow_action,tasks.flow_phase, tasks.status, tasks.status_color, tasks.status_icon, tasks.flow_department, tasks.gis_assignee, tasks.gis_assignee_date, tasks.pil_submitted_by, tasks.pkd_assignee, tasks.pkd_assignee_date, tasks.roads, tasks.authority_icon
                      , tasks.application_date,assign.username,assign.created_at,ls_authorities.sort_name,ls_authorities.logo
                     ) AS task";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':username', $this->User);

        if ($statusId !== null) {
            $stmt->bindParam(':statusId', $statusId);
        }

        if ($phase !== NULL) {
            $stmt->bindParam(':phase', $phase);
        }

        if ($id !== NULL) {
            $stmt->bindParam(':id', $id);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;


    }

    private function mappingAuthorityModal($systemId, $statusId)
    {
        $authority = new stdClass();
        include "config/lists/authorityModal.php";
        return $authority->$statusId ?? null;
    }

    private function getAuthorityTableData($filter, $phase, $id)
    {
        $data = $this->queryAuthorityTasks($filter, $phase, $id);

        foreach ($data as $item) {
            switch ($item->status_id) {
                default:
                    $item->route = $item->system_id . '-' . $item->status_id . '-' . $item->authority;
                    break;
            }
        }
        return $data;
    }

    private function getAuthorityModalData($filter, $phase, $id)
    {
        $data = $this->queryAuthorityTasks($filter, $phase, $id);

        $combinedData = [];

        foreach ($data as $item) {
            $id = $item->status_id;
            $modalData = $this->mappingAuthorityModal($item->system_id, $id);

            $needed = (object) [
                'system_id' => $item->system_id,
                'status_id' => $item->status_id,
                'authority_id' => $item->authority,
                'color' => $item->status_color,
                'id' => $item->system_id . '-' . $item->status_id . '-' . $item->authority,
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
        $filter = $args[1];
        $phasing = $args[2];


        switch ($phasing) {
            case 'wayleave':
                $phase = 'way_leave';
                break;
            case 'permit':
                $phase = 'work_permit';
                break;
            case 'cpc':
                $phase = 'work_finish';
                break;
            case 'cmgd':
                $phase = 'work_defect';
                break;
            case 'ccc':
                $phase = 'work_complete';
                break;
            case 'deposit':
                $phase = 'deposit_return';
                break;
            default:
                $phase = NULL;
                break;
        }

        if ($method === 'generalTable') {
            $getData = $this->getGeneralTableData($filter, $phase);
            return $this->Table->get($section, $getData);

        }

        if ($method === 'generalModal') {
            $getData = $this->getGeneralModalData($filter, $phase);
            return [
                $this->Modal->get($section, $getData),
                $this->Modal->render('survey-assign-piu', $getData),
                $this->Modal->render('review-udm', $getData),
                $this->Modal->render('review-tmp', $getData),
            ];
        }

        if ($method === 'authorityTable') {
            $id = $args[3];
            $getData = $this->getAuthorityTableData($filter, $phase, $id);
            return $this->Table->get($section, $getData);

        }

        if ($method === 'authorityModal') {
            $id = $args[3];
            $getData = $this->getAuthorityModalData($filter, $phase, $id);
            return $this->Modal->get($section, $getData);
        }

        // if ($method === 'javascript') {
        //     $getData = $this->getJSData($section, $roleId);
        //     return  $this->Pages[$subDepartment]->Javascript->get($section, $getData);

        // }
    }
}