<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/DBFactory.php";
require_once "config/functions/roles.php";

class Lists {

    private $dbFactory;
    private $Roles;


    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
        $this->Roles = new Roles();
    }

    public function get($dbTable) {

        $db = $this->dbFactory->createConnection();
        $sql = "SELECT * FROM $dbTable";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }

    public function getBy($dbTable, $column, $value) {
        switch ($column) {
            case "flow_role_id":
                $value = $this->Roles->getAssignment($value);
                $sql = "SELECT flow_name AS status FROM $dbTable WHERE id = ANY ( :value )";
            break;
            case "departments":
                $sql = "SELECT id,flow_name FROM $dbTable WHERE flow_group = :value";
            break;
            default:
                $sql = "SELECT * FROM $dbTable WHERE {$column} = :value";
            break;
        }

        $db = $this->dbFactory->createConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
}