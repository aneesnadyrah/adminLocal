<?php

class Chronology {

    private $dbFactory;
    private $System;

    public function __construct() {
        $this->dbFactory = new DBconnectionFactory();
        $this->System = new System;
    }

    // public function getLastStatus($systemId) {
    //     $db = $this->dbFactory->createConnection();

    //     $query = "SELECT status_id FROM ctrl_chronologies WHERE system_id = :systemId ORDER BY created_at DESC LIMIT 1";
    //     $stmt = $db->prepare($query);
    //     $stmt->bindParam(':systemId', $systemId);
    //     $stmt->execute();
    //     if($stmt->rowCount() > 0) {

    //         $status = $stmt->fetch(PDO::FETCH_OBJ)->status_id;

    //         $query = "SELECT * FROM sys_tenant_flows WHERE :status = ANY (flow) AND tenant = :tenant";
    //         $stmt = $db->prepare($query);
    //         $stmt->bindParam(':status', $status);
    //         $stmt->bindParam(':tenant', $this->System->App->tenant);
    //         $stmt->execute();
    //         $flow = $stmt->fetch(PDO::FETCH_OBJ)->flow;

    //         // Extract the flow data and convert it to a PHP array
    //         $flowArray = explode(',', trim($flow, '{}'));
    //         $index = array_search($status, $flowArray);

    //         if ($index !== false && $index > 0) {
    //             $previousIndex = $index;
    //             $previousValue = $flowArray[$previousIndex];
    //             return (object)[
    //                 'index' => $previousIndex,
    //                 'status' => $previousValue,
    //             ];
    //         } else {
    //             return (object)[];
    //         }

    //     } else {
    //         return (object)[
    //             'index' => 0,
    //             'status' => 155 ,
    //         ];
    //     }
    // }

    private function getDepartmentById($statusId) {
        $conn = $this->dbFactory->createConnection();
        $tenant = $this->System->App->tenant;
        $query = "SELECT department FROM sys_tenant_flows WHERE :status = ANY (flow) AND tenant = :tenant";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':status', $statusId);
        $stmt->bindParam(':tenant', $tenant);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getChronology($systemId, $authorityId = NULL) {
        $conn = $this->dbFactory->createConnection();

        $condition = ($authorityId !== NULL) ? " AND authority_id = :authorityId" : "";
        $query = "SELECT * FROM ctrl_chronologies WHERE system_id = :systemId {$condition}";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        if($authorityId !== NULL) {
            $stmt->bindParam(':authorityId', $authorityId);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function create($username, $systemId, $statusId, $type, $authorityId = NULL, $reset = false) {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT version FROM ctrl_chronologies WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        if($stmt->rowCount() === 0) {
            $version = 0;
        } else {
            $version = $reset === true ? $version = $stmt->fetchColumn() + 1 : $version = $stmt->fetchColumn();
        }

        $timestamp = date('Y-m-d H:i:s', time());
        $authParam = ($authorityId !== NULL) ? ", authority_id" : "";
        $authCond = ($authorityId !== NULL) ? ", :authorityId" : "";
        $query = "INSERT INTO ctrl_chronologies (system_id, type, username, status_id, created_at, version, department {$authParam}) VALUES (:systemId, :type, :username, :statusId, :timestamp, :version, :department {$authCond}) RETURNING id";
        //TODO : add department
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':statusId', $statusId);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->bindParam(':version', $version);
        $department = $this->getDepartmentById($statusId);
        $stmt->bindParam(':department', $department);
        if($authorityId !== NULL) {
            $stmt->bindParam(':authorityId', $authorityId);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

}