<?php
class Traffic {
    private $dbFactory;

    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
    }
    public  function getAPI() {
        $conn = $this->dbFactory->createConnection();
        $stmt = $conn->prepare("SELECT * FROM sys_api_traffic");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function requestAPI($traffic, $url, $method, $headers, $body, $response, $status) {
        $conn = $this->dbFactory->createConnection();
        $stmt = $conn->prepare("INSERT INTO sys_api_traffic (traffic, url, method, headers, body, response, status) VALUES (:traffic, :url, :method, :headers, :body, :response, :status)");
        $stmt->bindParam(':traffic', $traffic);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':method', $method);
        $stmt->bindParam(':headers', $headers);
        $stmt->bindParam(':body', $body);
        $stmt->bindParam(':response', $response);
        $stmt->bindParam(':status', $status);
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
        
    }
}