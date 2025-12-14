<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
class TaskAssignments
{

    private $dbFactory;
    private $Role;
    private $System;

    public function __construct()
    {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->System = new System;
    }

    private function getLastStatus($systemId)
    {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT status_id FROM ctrl_chronologies WHERE system_id = :systemId AND department = :department ORDER BY created_at DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':department', $this->System->App->department);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $status = $stmt->fetch(PDO::FETCH_OBJ)->status_id;

            $query = "SELECT * FROM sys_tenant_flows WHERE :status = ANY (flow) AND tenant = :tenant";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':tenant', $this->System->App->tenant);
            $stmt->execute();
            $flow = $stmt->fetch(PDO::FETCH_OBJ)->flow;

            // Extract the flow data and convert it to a PHP array
            $flowArray = explode(',', trim($flow, '{}'));
            $index = array_search($status, $flowArray);

            if ($index !== false) {
                $previousIndex = $index;
                $previousValue = $flowArray[$previousIndex];
                return (object) [
                    'valid' => true,
                    'index' => $previousIndex,
                    'status' => $previousValue,
                ];
            } else {
                return (object) [
                    'valid' => false,
                ];
            }
        } else {
            return (object) [
                'valid' => true,
                'index' => 0,
                'status' => NULL,
            ];
        }
    }

    private function getFlowIndex($currentStatus)
    {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT * FROM sys_tenant_flows WHERE :status = ANY (flow) AND tenant = :tenant";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':status', $currentStatus);
        $stmt->bindParam(':tenant', $this->System->App->tenant);
        $stmt->execute();
        $flow = $stmt->fetch(PDO::FETCH_OBJ)->flow;

        // Extract the flow data and convert it to a PHP array
        $flowArray = explode(',', trim($flow, '{}'));
        $index = array_search($currentStatus, $flowArray);

        $currentIndex = $index;
        $currentValue = $flowArray[$currentIndex];
        return (object) [
            'valid' => true,
            'index' => $currentIndex,
            'status' => $currentValue,
        ];

    }

    private function getNextStatus($statusId)
    {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT * FROM sys_tenant_flows WHERE :status = ANY (flow) AND tenant = :tenant";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':status', $statusId);
        $stmt->bindParam(':tenant', $this->System->App->tenant);
        $stmt->execute();
        $flowData = $stmt->fetch(PDO::FETCH_OBJ);
        $flow = $flowData->flow;

        // Extract the flow data and convert it to a PHP array
        $flowArray = explode(',', trim($flow, '{}'));
        $index = array_search($statusId, $flowArray);

        if ($index !== false && $index < count($flowArray) - 1) {
            $nextIndex = $index + 1;
            $nextValue = $flowArray[$nextIndex];

            return (object) [
                'valid' => true,
                'index' => $nextIndex,
                'status' => $nextValue,
            ];
        } else {
            // Check if it's the last index or status
            if ($index === count($flowArray) - 1) {
                return (object) [
                    'valid' => false
                ];
            } else {
                return (object) [
                    'valid' => false
                ];
            }
        }
    }


    public function create($systemId, $nextStatus, $authority = NULL, $currentStatus = NULL)
    {
        $user = $this->Role->setAssignment($nextStatus);
        $db = $this->dbFactory->createConnection();
        $timestamp = date('Y-m-d H:i:s', time());

        $authQuery = $authority == NULL ? "" : ", authority";
        $authParam = $authority == NULL ? "" : ", :authority";

        $response = array();
        foreach ($user as $index => $value) {

            if($value->role_id == 16 || $value->role_id == 17){
                $query = "SELECT districts FROM flw_appl_entries WHERE system_id = :systemId";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $projectDistrict = $row['districts'];

                // Remove curly braces and split the string by commas
                $districtArray = explode(',', trim($projectDistrict, '{}'));

                // Convert each element to an integer
                // $districtArray = array_map('intval', $districtArray);
                // var_dump($districtArray);
                $zoneId = array();

                foreach($districtArray as $row){

                    $query = "SELECT id FROM ls_user_zones WHERE :district = ANY (districts) AND state = :state";

                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':district', $row);
                    $state = $this->System->App->state;
                    $stmt->bindParam(':state', $state);
                    $stmt->execute();

                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    // var_dump($row);
                    $zoneId[] = $row['id'];
                }

                foreach($zoneId as $zone){
                    // var_dump('find:'.$zone);
                    // var_dump('value:'.$value->zone_id);
                    // var_dump('user:'.$value->username);
                    if($zone == $value->zone_id){
                        $query = "INSERT INTO flw_task_assignments (system_id, username, created_at, status_id, zone_id {$authQuery}) VALUES (:systemId, :username, :timestamp, :status, :zone {$authParam})";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':systemId', $systemId);
                        $stmt->bindParam(':username', $value->username);
                        $stmt->bindParam(':timestamp', $timestamp);
                        $stmt->bindParam(':status', $nextStatus);
                        $stmt->bindParam(':zone', $value->zone_id);
                        if ($authority != NULL) {
                            $stmt->bindParam(':authority', $authority);
                        }

                        if ($stmt->execute()) {
                            $response[] = true;
                        } else {
                            $response[] = false;
                        }
                        ;
                    }
                }

                //assign survey
            } else if ($value->role_id == 22 || $value->role_id == 23 || $value->role_id == 24 || ($value->role_id == 28 && $nextStatus == 42) || ($value->role_id == 28 && $nextStatus == 98)){

                if ($nextStatus >= 42 && $nextStatus <= 48) {
                    $stmt = $db->prepare("SELECT team_id FROM flw_survey_udm WHERE system_id = :systemId");
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();
                    $teamId = $stmt->fetch(PDO::FETCH_ASSOC);
                } else if ($nextStatus >= 98 && $nextStatus <= 104) {
                    $stmt = $db->prepare("SELECT team_id FROM flw_survey_asb WHERE system_id = :systemId");
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();
                    $teamId = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                
                if (!empty($teamId['team_id'])) {
                    $stmt = $db->prepare("SELECT team_members FROM flw_survey_team WHERE id = :teamId AND is_active = true");
                    $stmt->bindParam(':teamId', $teamId['team_id']);
                    $stmt->execute();

                    $teamMembers = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Remove curly braces from the string
                    $getTeamMembers = trim($teamMembers['team_members'], '{}');

                    // Split the string into an array based on commas
                    $getTeamMember = explode(',', $getTeamMembers);

                    foreach ($getTeamMember as $teamMember) {
                        if ($value->username == $teamMember) {
                            $query = "INSERT INTO flw_task_assignments (system_id, username, created_at, status_id, zone_id {$authQuery}) VALUES (:systemId, :username, :timestamp, :status, :zone {$authParam})";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':systemId', $systemId);
                            $stmt->bindParam(':username', $value->username);
                            $stmt->bindParam(':timestamp', $timestamp);
                            $stmt->bindParam(':status', $nextStatus);
                            $stmt->bindParam(':zone', $value->zone_id);
                            if ($authority != NULL) {
                                $stmt->bindParam(':authority', $authority);
                            }

                            if ($stmt->execute()) {
                                $response[] = true;
                            } else {
                                $response[] = false;
                            }
                            ;
                        }
                    }
                } else {
                    $query = "INSERT INTO flw_task_assignments (system_id, username, created_at, status_id, zone_id {$authQuery}) VALUES (:systemId, :username, :timestamp, :status, :zone {$authParam})";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':username', $value->username);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':status', $nextStatus);
                    $stmt->bindParam(':zone', $value->zone_id);
                    if ($authority != NULL) {
                        $stmt->bindParam(':authority', $authority);
                    }

                    if ($stmt->execute()) {
                        $response[] = true;
                    } else {
                        $response[] = false;
                    }
                    ;
                }
            } else if ((($value->role_id == 28 && ($nextStatus == 50)) || ($value->role_id == 28 && ($nextStatus == 52))) || ($value->role_id == 28 && $nextStatus == 106) || ($value->role_id == 26 && ($nextStatus == 50 || $nextStatus == 52))) {

                $udmAssignee = '';
                $tmpAssignee = '';
                $asbAssignee = '';
                //Plan UDM Assignee
                if($nextStatus == 50) {
                    $stmt = $db->prepare("SELECT plan_udm_assignee FROM flw_plan_udm WHERE system_id = :systemId ORDER BY plan_udm_assignee DESC LIMIT 1");
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();

                    $assignee = $stmt->fetch(PDO::FETCH_ASSOC);
                    $udmAssignee = $assignee['plan_udm_assignee'];

                //PLAN TMP Assignee
                } else if ($nextStatus == 52) {
                    $stmt = $db->prepare("SELECT plan_tmp_assignee FROM flw_plan_tmp WHERE system_id = :systemId ORDER BY plan_tmp_assignee DESC LIMIT 1");
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();

                    $assignee = $stmt->fetch(PDO::FETCH_ASSOC);
                    $tmpAssignee = $assignee['plan_tmp_assignee'];

                } else if ($nextStatus == 106) {
                    $stmt = $db->prepare("SELECT plan_asb_assignee FROM flw_plan_asb WHERE system_id = :systemId ORDER BY plan_asb_assignee DESC LIMIT 1");
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->execute();

                    $assignee = $stmt->fetch(PDO::FETCH_ASSOC);
                    $tmpAssignee = $assignee['plan_asb_assignee'];

                }

                if($value->username == $udmAssignee || $value->username == $tmpAssignee || $value->username == $asbAssignee){
                    $query = "INSERT INTO flw_task_assignments (system_id, username, created_at, status_id, zone_id {$authQuery}) VALUES (:systemId, :username, :timestamp, :status, :zone {$authParam})";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':username', $value->username);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':status', $nextStatus);
                    $stmt->bindParam(':zone', $value->zone_id);
                    if ($authority != NULL) {
                        $stmt->bindParam(':authority', $authority);
                    }

                    if ($stmt->execute()) {
                        $response[] = true;
                    } else {
                        $response[] = false;
                    }
                    ;
                }


            } else if (($value->role_id == 6 || $value->role_id == 7 || $value->role_id == 10 || $value->role_id == 11) && $nextStatus == 8){

                $gisAssignee = '';

                $stmt = $db->prepare("SELECT gis_assignee FROM flw_appl_verifies WHERE system_id = :systemId");
                $stmt->bindParam(':systemId', $systemId);
                $stmt->execute();

                $assignee = $stmt->fetch(PDO::FETCH_ASSOC);
                $gisAssignee = $assignee['gis_assignee'];

                if($value->username == $gisAssignee) {
                    $query = "INSERT INTO flw_task_assignments (system_id, username, created_at, status_id, zone_id {$authQuery}) VALUES (:systemId, :username, :timestamp, :status, :zone {$authParam})";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':username', $value->username);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':status', $nextStatus);
                    $stmt->bindParam(':zone', $value->zone_id);
                    if ($authority != NULL) {
                        $stmt->bindParam(':authority', $authority);
                    }

                    if ($stmt->execute()) {
                        $response[] = true;
                    } else {
                        $response[] = false;
                    }
                    ;
                }

            } else {
                $query = "INSERT INTO flw_task_assignments (system_id, username, created_at, status_id, zone_id {$authQuery}) VALUES (:systemId, :username, :timestamp, :status, :zone {$authParam})";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':username', $value->username);
                $stmt->bindParam(':timestamp', $timestamp);
                $stmt->bindParam(':status', $nextStatus);
                $stmt->bindParam(':zone', $value->zone_id);
                if ($authority != NULL) {
                    $stmt->bindParam(':authority', $authority);
                }

                if ($stmt->execute()) {
                    $response[] = true;
                } else {
                    $response[] = false;
                }
                ;
            }
        }

        if ($authority != NULL && $currentStatus != NULL) {

            //check main
            $query = "SELECT system_id, completed, authority, status_id FROM flw_task_assignments WHERE system_id = :systemId AND status_id = :status_id AND authority = 0";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':status_id', $currentStatus);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {

                $query = "SELECT system_id, completed, authority, status_id FROM flw_task_assignments WHERE system_id = :systemId AND status_id = :status_id AND authority <> 0";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':status_id', $currentStatus);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


                $updateMain = true;
                foreach ($result as $check) {

                    if ($check['completed'] == false) {
                        $updateMain = false;
                        break;
                    }
                }

                if ($updateMain) {
                    $query = "SELECT status_id FROM flw_task_assignments WHERE system_id = :systemId AND status_id = :status_id AND authority = 0 AND completed = false";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':systemId', $systemId);
                    $stmt->bindParam(':status_id', $nextStatus);
                    $stmt->execute();

                    // Check if there are results
                    if ($stmt->rowCount() > 0) {

                    } else {
                        // Create task for main
                        $this->create($systemId, $nextStatus);
                    }
                }
            }
        }

        return $response;
    }

    private function check()
    {


    }

    public function complete($submitter, $systemId, $currentStatus, $authority = NULL)
    {
        $db = $this->dbFactory->createConnection();
        $last = $this->getLastStatus($systemId);
        $actual = $this->getFlowIndex($currentStatus);

        if ($actual->index === 0) {
            $complete = true;
            $status = $currentStatus;

        } else {
            $complete = $actual->index > $last->index ? 'true' : 'false';
            $status = $actual->status;
        }

        $users = $this->Role->setAssignment($status);

        $response = [];

        $condition = $authority == NULL ? "" : "AND authority = :authority";
        $timestamp = date('Y-m-d H:i:s', time());

        foreach ($users as $index => $value) {

            $query = "UPDATE flw_task_assignments SET completed = :completed, completed_at = :timestamp, submitter = :submitter WHERE system_id = :systemId AND (status_id = :status AND username = :username) {$condition}";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':completed', $complete);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':submitter', $submitter);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':username', $value->username);
            if ($authority != NULL) {
                $stmt->bindParam(':authority', $authority);
            }

            if ($stmt->execute()) {
                $response[] = true;
            } else {
                $response[] = false;
            }
        }

        if ($authority != NULL) {
            $query = "SELECT system_id, completed, authority, status_id FROM flw_task_assignments WHERE system_id = :systemId AND status_id = :status_id AND authority <> 0";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':status_id', $status);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $updateMain = true;
            foreach ($result as $check) {

                if ($check['completed'] == false) {
                    $updateMain = false;
                    break;
                }
            }

            if ($updateMain) {
                $completeMain = $this->complete($submitter, $systemId, $currentStatus, 0);
            }
        }

        return $response;

    }

    public function set($submitter, $systemId, $currentStatus, $authority = NULL)
    {
        $prevStatus = $this->getLastStatus($systemId);
        $nextStatus = $this->getNextStatus($currentStatus);

        if ($nextStatus->valid) {
            $createResult = $this->create($systemId, $nextStatus->status, $authority);
            if (!$createResult) {
                // Handle the case where create fails
                return "Error: Create assignment failed";
            }
        }


        if ($prevStatus->valid && $prevStatus->status !== NULL) {
            $completeResult = $this->complete($submitter, $systemId, $prevStatus->status, $authority);

            if (!$completeResult) {
                // Handle the case where complete fails
                return "Error: Complete assignment failed";
            }
        } else {
            $completeResult = $this->complete($submitter, $systemId, $currentStatus, $authority);

            if (!$completeResult) {
                // Handle the case where complete fails
                return "Error: Complete assignment failed";
            }
        }
        // Both create and complete operations were successful
        return true;
    }

}