<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/DBFactory.php";

Class User {
    private $dbFactory;

    private $Card;

    private $Layout;

    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
        $this->Card = new Card();
        $this->Layout = new Layout();
    }

    private function getDataStaff($username)
    {
        $db = $this->dbFactory->createConnection();

        $stmt = $db->prepare('SELECT * FROM sys_users LEFT JOIN sys_hr_employee ON sys_users.employee_id = sys_hr_employee.id LEFT JOIN ls_postcode ON sys_hr_employee.postcode = ls_postcode.postcode WHERE username = :username LIMIT 1');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // return $data;

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        $response = $result;
        return $response;
    }

    public function __call($method, $args)
    {
        if ($method === "primary") {
            $getData = $this->getDataStaff($args[0]);
        
            return $this->Layout->get($method.'Sidebar', $getData);
        }

        switch($method) {
            case 'header':
                $getData = $this->getDataStaff($args[0]);
                break;
            case 'details':
                $getData = $this->getDataStaff($args[0]);
                break;
            default:
                $getData = [];
            break;
        }
        return $this->Card->get('user-'.$method, $getData);

    }

}