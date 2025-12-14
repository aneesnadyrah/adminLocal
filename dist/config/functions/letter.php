<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'config/components.php';

Class Letter {
    private $dbFactory;

    private $Table;

    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
        $this->Table = new Table();
    }

    private function getTableData() {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT *,INITCAP (view_users.name) AS minute_to, gen_letters.id AS letter_id FROM gen_letters LEFT JOIN view_users ON view_users.username = gen_letters.receiver";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function __call($method, $args) {
        $section = $args[0];

        if($method === 'table') {
            $getData = $this->getTableData();
            return $this->Table->get($section, $getData);
        }
        
    }

}