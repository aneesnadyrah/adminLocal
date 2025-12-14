<?php
class FlowStatuses
{
    private $dbFactory;
    private $system;
    private $user;
    private $flow;

    private $current;

    public function __construct($username) {
        $this->dbFactory = new DBConnectionFactory();
        $this->system = new System;
        $this->user = isset($username) ?  $username : $_SESSION['username'];

    }

    private function checkUserRole($username) {
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT status_id FROM sys_user_assignments WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $flowIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'status_id');

        // Convert array values
        $flowArrays = array_map(function($item) {
            return explode(',', trim($item, '{}'));
        }, $flowIds);

        // Check if a value exists in any array
        $found = false;

        foreach ($flowArrays as $flowArray) {
            if (in_array($this->current->status, $flowArray)) {
                $found = true;
                break;
            }
        }
        // Return the result
        if ($found) {
            return true;
        } else {
            return false;
        }
    }

    private function checkApiAccess($apiToken, $action) {
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT level_access FROM sys_api_tokens WHERE token = :apiToken ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':apiToken', $apiToken);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $level = 'level_' . $result['level_access'];

        $stmt = $conn->prepare("SELECT $level FROM sys_api_access_level WHERE action = :action");
        $stmt->bindParam(':action', $action);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $levelAccess = $result[$level];
        // Return the result
        if($levelAccess === true){
            return true;
        } else {
            return false;
        }
    }

    private function updateFlow($flowGroup, $index, $value, $systemId, $authorityId = 0) {
        $conn = $this->dbFactory->createConnection();
        $condition = ($authorityId !== 0) ? "AND authority = :authorityId" : "";
        $stmt = $conn->prepare("UPDATE ctrl_statuses SET flow_index = :nextStep, status_id = :indexValue WHERE system_id = :systemId AND department = :flowGroup {$condition}");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':nextStep', $index);
        $stmt->bindParam(':indexValue', $value);
        $stmt->bindParam(':flowGroup', $flowGroup);
        if ($authorityId !== 0) {
            $stmt->bindParam(':authorityId', $authorityId);
        }
        if ($stmt->execute()) {
            $response = true;
        } else {
            $response = json_encode($stmt->errorInfo());
        }

        //check to update main status
        if ($authorityId !== 0) {
            $query = "SELECT system_id, authority, department, flow_index, status_id FROM ctrl_statuses WHERE system_id = :systemId AND department = :flowGroup AND authority <> 0";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':flowGroup', $flowGroup);
            $stmt->execute();

            $resultAuthority = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $query2 = "SELECT system_id, authority, department, flow_index, status_id FROM ctrl_statuses WHERE system_id = :systemId AND department = :flowGroup AND authority = 0";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':flowGroup', $flowGroup);
            $stmt2->execute();

            $resultMain = $stmt2->fetch(PDO::FETCH_ASSOC);
            $MainFlowIndex = $resultMain['flow_index'];
            // var_dump($resultAuthority);
            // var_dump($resultMain);
            $updateMain = true;

            $newMainIndex = 9999999;
            foreach ($resultAuthority as $check) {
                // var_dump($check['flow_index']);

                if ( $MainFlowIndex >= $check['flow_index'] ) {
                    $updateMain = false;
                    break;
                }


                if($newMainIndex > $check['flow_index']) {
                    $newMainIndex = $check['flow_index'];
                    $newMainValue = $check['status_id'];
                }

            }


            // var_dump($updateMain);



            if ($updateMain) {
                // Update task for main
                $stmt = $conn->prepare("UPDATE ctrl_statuses SET flow_index = :nextStep, status_id = :indexValue WHERE system_id = :systemId AND department = :flowGroup AND authority = 0");
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':nextStep', $newMainIndex);
                $stmt->bindParam(':indexValue', $newMainValue);
                $stmt->bindParam(':flowGroup', $flowGroup);
                $stmt->execute();

            }
        }

        return $response;
    }

    public function listFlows($applicationType) {
        $conn = $this->dbFactory->createConnection();
        $tenant = $this->system->App->tenant;

        $stmt = $conn->prepare("SELECT department, flow FROM sys_tenant_flows WHERE tenant = :tenant AND category = :type");
        $stmt->bindParam(':tenant', $tenant);
        $stmt->bindParam(':type', $applicationType);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($rows as $row) {
            $department = $row['department'];
            $flowString = str_replace(['{', '}'], '', $row['flow']);
            $flow = array_map('intval', explode(',', $flowString));
            if (!isset($result[$department])) {
                $result[$department] = $flow;
            } else {
                $result[$department] = array_merge($result[$department], $flow);
            }
        }

        return $result;

    }

    private function getSequence($flowGroup, $applicationType)
    {
        $conn = $this->dbFactory->createConnection();
        $tenant = $this->system->App->tenant;

        $stmt = $conn->prepare("SELECT flow FROM sys_tenant_flows WHERE tenant = :tenant AND department = :flowGroup AND category = :type");
        $stmt->bindParam(':tenant', $tenant);
        $stmt->bindParam(':flowGroup', $flowGroup);
        $stmt->bindParam(':type', $applicationType);
        $stmt->execute();
        $array = explode(',', str_replace(['{', '}'], '', $stmt->fetchColumn()));
        $sequenceFlows = array_map('intval', $array);
        $maxFlows = count($sequenceFlows);

        return array(
            'sequence'    => $sequenceFlows,
            'max'         => $maxFlows
        );
    }

    public function getCurrentFlow($systemId, $flowGroup, $authorityId = 0){
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT flow_index AS flow, status_id AS status,authority FROM ctrl_statuses WHERE system_id = :systemId AND department = :flowGroup AND authority = :authorityId");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':flowGroup', $flowGroup);
        $stmt->bindParam(':authorityId', $authorityId);


        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = new stdClass();

        foreach ($rows as $row) {
            $results->flow = $row['flow'];
            $results->status = $row['status'];
            $results->authority = $row['authority'];
        }

        return $results;
    }

    public function addRecordFlow($systemId, $applicationType, $flowValue = NULL, $authorityId = 0){
        $conn = $this->dbFactory->createConnection();
        $flows = $this->listFlows($applicationType);

        $results = [];
        foreach ($flows as $flow => $array) {
            $index = array_search($flowValue, $array);
            if ($index !== false) {
                $stmt = $conn->prepare("INSERT INTO ctrl_statuses (system_id, department, flow_index, status_id, authority) VALUES (:systemId, :department, :flowIndex, :flowValue, :authorityId)");
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':department', $flow);
                $stmt->bindParam(':flowIndex', $index);
                $stmt->bindParam(':flowValue', $flowValue);
                $stmt->bindParam(':authorityId', $authorityId);

                if ($stmt->execute()) {
                    $results[] = true;
                } else {
                    $results[] = false;
                }
            }
        }
        if($results) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAuthorityFlow($systemId, $authorityId){
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("DELETE FROM ctrl_statuses WHERE system_id = :systemId AND authority = :authorityId");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authorityId', $authorityId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAuthorityAssignment($systemId, $authorityId){
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("DELETE FROM flw_task_assignments WHERE system_id = :systemId AND authority = :authorityId");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authorityId', $authorityId);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }



    public function goToNextFlow($systemId, $flowGroup, $applicationType, $authorityId = 0, $Steps = NULL, $apiToken = NULL, $allRole = false) {


        // NOTE -  Get Current Flow for each records
        $this->current = $this->getCurrentFlow($systemId, $flowGroup, $authorityId);
        // NOTE - Get Max Flow Based on Tenant
        $sequence = $this->getSequence($flowGroup, $applicationType);
        $sequenceFlows = $sequence['sequence'];
        $maxFlows = $sequence['max'];


        $conn = $this->dbFactory->createConnection();

        //NOTE - Check User Role

        if ($apiToken == NULL){
            if ($allRole == false) {
                if (!$this->checkUserRole( $this->user)) {
                    return json_encode(
                        [
                            'status' => false,
                            'message' => 'You are not authorized to perform this action',
                        ]
                    );
                }
            }
        } else {
            if (!$this->checkApiAccess($apiToken, action: 'update_status')) {
                return json_encode(
                    [
                        'status' => false,
                        'message' => 'You are not authorized to perform this action',
                    ]
                );
            }

        }


        // NOTE: Update Status Based On Authority Else Update All Record
        if (is_array($this->current->flow) && count($this->current->flow) > 1) {
            // NOTE: Get Current Step and Add To Next Step By Sequence Flows
            //REVIEW - what if sequence having low and high number
            $highestIndex = max($this->current->flow);
            // Check if the current step is the last one
            if ($highestIndex < $maxFlows - 1) {
                // Get the next step index directly
                if ($Steps == NULL) {
                    $nextIndex = $highestIndex + 1;
                } else {
                    $indexStep = array_search($Steps, $sequenceFlows);
                    // Compare with the provided index and use the higher one
                    $nextIndex = $indexStep;
                }

                $nextValue = $sequenceFlows[$nextIndex];
                // NOTE: Send to Update Record
                return $this->updateFlow($flowGroup, $nextIndex, $nextValue, $systemId) ? $nextValue : false;
            }

        } else {
            // NOTE -  Get Current Step and Add To Next Step By Sequence Flows
            // Check if the current step is the last one
            if ($this->current->flow < $maxFlows - 1) {
                // Get the next step
                if ($Steps == NULL) {
                    $nextIndex = $this->current->flow + 1;
                } else {
                    $indexStep = array_search($Steps, $sequenceFlows);
                    // Compare with the provided index and use the higher one
                    $nextIndex = $indexStep;
                }

                $nextValue = $sequenceFlows[$nextIndex];

                // NOTE -  Send to Update Record
                return $this->updateFlow($flowGroup, $nextIndex, $nextValue, $systemId, $authorityId)? $nextValue : false;

            } else {
                return 'Last Step';
            }
        }
    }

    public function goToPrevFlow($systemId, $flowGroup, $applicationType, $authorityId = 0, $Steps = NULL)
    {
        // NOTE -  Get Current Flow for each records
        $this->current = $this->getCurrentFlow($systemId, $flowGroup, $authorityId);

        // NOTE -  Get Max Flow Based on Tenant
        $sequence = $this->getSequence($flowGroup, $applicationType);
        $sequenceFlows = $sequence['sequence'];
        $maxFlows = $sequence['max'];

        if (!$this->checkUserRole( $this->user)) {
            return json_encode(
                [
                    'status' => false,
                    'message' => 'You are not authorized to perform this action',
                ]
            );
        }

        // NOTE: Update Status Based On Authority Else Update All Record
        if (is_array($this->current->flow) && count($this->current->flow) > 1) {
            // NOTE: Get Current Step and Add To Next Step By Sequence Flows
            //REVIEW - what if sequence having low and high number
            $highestIndex = max($this->current->flow);
            // Check if the current step is the last one
            if ($highestIndex < $maxFlows - 1) {
                // Get the next step index directly
                if ($Steps == NULL) {
                    $prevIndex = $highestIndex - 1;
                } else {
                    $indexStep = array_search($Steps, $sequenceFlows);
                    // Compare with the provided index and use the higher one
                    $prevIndex = $indexStep;
                }

                $prevValue = $sequenceFlows[$prevIndex];
                // NOTE: Send to Update Record
                return $this->updateFlow($flowGroup, $prevIndex, $prevValue, $systemId) ? $prevIndex : false;
            }

        } else {
            // NOTE -  Get Current Step and Add To Next Step By Sequence Flows
            // Check if the current step is the last one
            if ($this->current->flow < $maxFlows - 1) {
                // Get the next step
                if ($Steps == NULL) {
                    $prevIndex = $this->current->flow - 1;
                } else {
                    $indexStep = array_search($Steps, $sequenceFlows);
                    // Compare with the provided index and use the higher one
                    $prevIndex = $indexStep;
                }

                $prevValue = $sequenceFlows[$prevIndex];

                // NOTE -  Send to Update Record
                return $this->updateFlow($flowGroup, $prevIndex, $prevValue, $systemId, $authorityId)? $prevIndex : false;

            } else {
                return 'Last Step';
            }
        }
    }

    //NOTE - (Check all authority flow done before using main flow) #need to do after goToNextFlow
    public function CheckAllComplete($systemId, $nextStatus) {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT status_id FROM ctrl_statuses WHERE system_id = :systemId AND department = :flowGroup AND authority = 0";
        $stmt = $conn->prepare($query);

        $depart = "operation";
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':flowGroup', $depart);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $mainFlowStatus = $result['status_id'];

        return $mainFlowStatus == $nextStatus;
    }
}
?>