<?php
class Changelog {

    private $dbFactory;
    private $user;

    public function __construct($username) {
        $this->dbFactory = new DBConnectionFactory();
        $this->user = isset($username) ?  $username : $_SESSION['username'];
    }

    public function userActivity($message, $pages) {
        $conn = $this->dbFactory->createConnection();
        $timestamp = date('Y-m-d H:i:s', time());

        $ip = $_SERVER['REMOTE_ADDR'];
        $device = $_SERVER['HTTP_USER_AGENT'];
        $current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $access_key = 'c02b3ede6aa928a2e78cf9796f3d10d9';
        //$location_info = file_get_contents("http://api.ipapi.com/{$ip}?access_key={$access_key}");

	$location_info = null;
        if ($ip !== '127.0.0.1') {
        $url = "http://api.ipapi.com/{$ip}?access_key={$access_key}";
        $response = @file_get_contents($url); // Suppress warning if fails

        if ($response !== false) {
        $location_info = $response;
        } else {
        $location_info = json_encode(['error' => 'API call failed']);
        error_log("Failed to fetch IP info for {$ip}"); // Optional logging
        }
        } else {
        $location_info = json_encode(['note' => 'Localhost - no IP lookup']);
        }

        // Prepare the query for insert
        $query = "INSERT INTO sys_user_activity (url, pages, location, ip_address, device, details, action_timestamp) VALUES(:url, :pages, :location, :ip, :device, :detailCode, :timestamp)";
        $stmt = $conn->prepare($query);
        // Bind the parameters
        $stmt->bindParam(':url', $current_url);
        $stmt->bindParam(':pages', $pages);
        $stmt->bindParam(':location', $location_info);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':device', $device);
        $stmt->bindParam(':detailCode', $message);
        $stmt->bindParam(':timestamp', $timestamp);
        // Execute the query
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserActivity() {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT * FROM sys_user_activity WHERE username = :username ORDER BY action_timestamp DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $this->user);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function projectActivity($systemId, $currentFlow, $details, $authorityId = NULL) {
        $conn = $this->dbFactory->createConnection();
        $timestamp = date('Y-m-d H:i:s', time());

        $authority = ($authorityId !== NULL) ? ", authority" : "";
        $authoParam = ($authorityId !== NULL) ? ", :authority" : "";
        $query = "INSERT INTO sys_record_changelog (system_id, current_flow, flow_timestamp, username, details {$authority}) VALUES(:system_id, :currentFlow, :timestamp, :username, :details {$authoParam})";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':system_id', $systemId);
        $stmt->bindParam(':currentFlow', $currentFlow);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->bindParam(':username', $this->user);
        $stmt->bindParam(':details', $details);
        if($authorityId !== NULL) {
            $stmt->bindParam(':authority', $authorityId);
        }
        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getProjectActivity($systemId) {
        $conn = $this->dbFactory->createConnection();

        $query = "SELECT * FROM sys_record_changelog WHERE system_id = :systemId ORDER BY flow_timestamp DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public static function apiActivity(){

    }

    public static function updateApiActivity(){
        
    }
}