<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once('config/DBFactory.php');

class Roles
{
    private $dbFactory;

    public function __construct()
    {
        $this->dbFactory = new DBConnectionFactory();
    }
    private function getRole($username)
    {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT
        users.user_role,
        users.role_id,
		users.department,
        users.sub_department,
		tasks.status_id,
        assign.zone_id,
        zone.districts AS zone_districts
        FROM view_users users
        LEFT JOIN sys_user_assignments assign ON assign.role_id = users.role_id
        LEFT JOIN ls_user_zones zone ON assign.zone_id = zone.id
		LEFT JOIN flw_task_assignments tasks ON tasks.username = users.username
        WHERE users.username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function getAssignment($username)
    {
        $db = $this->dbFactory->createConnection();
        $query = "SELECT status_id FROM sys_user_assignments WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $arrayStatusId = array_map(function ($item) {
            return trim($item->status_id, '{}');
        }, $result);

        $statusId = implode(',', array_unique(explode(',', implode(',', $arrayStatusId))));
        return '{' . $statusId . '}';
    }

    public function setAssignment($statusId) {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT username, zone_id, role_id FROM sys_user_assignments WHERE :status_id = ANY(status_id) GROUP BY username, zone_id,role_id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':status_id', $statusId);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $result;

    }


    public function __get($property)
    {
        return $this->getRole($property);
    }
}