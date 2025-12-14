<?php

class Utilities {

    public static function DBFactory()
    {
        global $PDO;

        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        return $conn;
    }

    public static function extractSystemId($systemId, $method)
    {
        // global $conn;
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $query = "SELECT * FROM ctrl_reference_no WHERE system_id = :systemId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return null;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($method == 'digits') {
            return $row['year'].$row['running'];
        } else if ($method == 'running') {
            return $row['running'];
        } else if ($method == 'year') {
            return $row['year'];
        }
    }

    public static function getRefNo($systemId) {
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $stmt = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");
        $stmt->bindParam(":systemId", $systemId);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return null;
        } else {
            return $stmt->fetchColumn();
        }
    }


    public static function generateCode()
    {
        // global $conn;
        global $username;

        require_once "config/system.php";
        require_once "api/functions.php";
        // require "api/header.php";
        header('Access-Control-Allow-Credentials: true');
        header('Content-Type: application/json');

        // Connect to the database using PDO
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();
        $user = isset($_POST['username']) ? $_POST['username'] : $username;
        $code = rand(100000, 999999);
        $requested = date('Y-m-d H:i:s', time());

        // Prepare a SELECT query on the database
        $stmt = $conn->prepare("SELECT activation_code FROM sys_users WHERE activation_code = :code ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':code', $code);

        // Execute the query
        $stmt->execute();

        // Fetch all rows into an array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            // Prepare the query for update
            $insertCode = "UPDATE sys_users SET activation_code = :code, activation_request = :requested WHERE username = :user ";
            $stmt2 = $conn->prepare($insertCode);

            // Bind the parameters
            $stmt2->bindParam(':code', $code);
            $stmt2->bindParam(':requested', $requested);
            $stmt2->bindParam(':user', $user);

            // Execute the query
            $stmt2->execute();

            $ip = $_SERVER['REMOTE_ADDR'];
            $device = $_SERVER['HTTP_USER_AGENT'];
            $current_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $access_key = 'c02b3ede6aa928a2e78cf9796f3d10d9';
            $location_info = file_get_contents("http://api.ipapi.com/{$ip}?access_key={$access_key}");

            $detailCode = "permintaan kod pengesahan yang berdigit, $code";

            // Prepare the query for insert
            $userlog = "INSERT INTO sys_user_activity (url, location, ip_address, device, details, action_timestamp) VALUES(:current_url, :location_info, :ip, :device, :detailCode, :requested)";
            $stmt3 = $conn->prepare($userlog);

            // Bind the parameters
            $stmt3->bindParam(':current_url', $current_url);
            $stmt3->bindParam(':location_info', $location_info);
            $stmt3->bindParam(':ip', $ip);
            $stmt3->bindParam(':device', $device);
            $stmt3->bindParam(':detailCode', $detailCode);
            $stmt3->bindParam(':requested', $requested);

            // Execute the query
            $stmt3->execute();
            return $code;
        } else {
            return utilities::generateCode();
        }
    }

    public static function generateLetterId($length = 8)
    {
        global $conn;

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        // $tempCode = '';
        // $maxIndex = strlen($characters) - 1;

        // Shuffle the characters
        $shuffledChars = str_shuffle($characters);

        // Pick first $length characters from shuffled string
        $uniqueId = substr($shuffledChars, 0, $length);

        $checkCode = "SELECT id FROM gen_letters WHERE unique_id = :uniqueId";

        $stmt = $conn->prepare($checkCode);
        $stmt->bindParam(':uniqueId', $uniqueId);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return $uniqueId;
        } else {
            return Utilities::generateLetterId();
        }

    }

    public static function getStatus($systemId, $flowGroup, $authId = null)
    {
        global $conn;

        $condition = ($authId !== NULL) ? "AND authority = :authorityId" : "";
        $query = "SELECT {$flowGroup}_flow FROM ctrl_statuses WHERE system_id = :systemId {$condition} ORDER BY {$flowGroup}_flow ASC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        if($authId !== NULL) {
            $stmt->bindParam(':authorityId', $authId);
        }
        $stmt->execute();
        $status = $stmt->fetchColumn();

        return $status !== false ? $status : 'no column or row found'; // Return status or null if no rows are found
    }

    public static function checkDomainToken($token, $domain) {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM sys_api_tokens WHERE token = :token AND domain = :domain");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':domain', $domain);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public static function convertDateToMalay($dateString)
    {
        // Parse the input date string into a DateTime object
        $dateObj = new DateTime($dateString);

        // Create an array of month names in Malay
        $monthNames = [
            'Januari',
            'Februari',
            'Mac',
            'April',
            'Mei',
            'Jun',
            'Julai',
            'Ogos',
            'September',
            'Oktober',
            'November',
            'Disember'
        ];

        // Get the day, month, and year from the date object
        $day = $dateObj->format('d');
        $monthIndex = $dateObj->format('n') - 1; // Adjust month index to 0-based
        $year = $dateObj->format('Y');

        // Format the date as required: "25 Julai 2023"
        $formattedDate = "$day {$monthNames[$monthIndex]} $year";
        return $formattedDate;
    }

    public static function formatTimeToMalayTimeString($dateTimeString)
    {
        // Parse the input date and time string into a DateTime object
        $dateTimeObj = new DateTime($dateTimeString);

        // Get the time in 12-hour format with leading zero
        $time = $dateTimeObj->format('h:i A');

        return $time;
    }

    public static function getProvider($id) {
        $system = new System();
        $ec_url = $system->App->ec_url;
        $url = $ec_url . '/api/providers/' . $id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            // Handle cURL error, if needed
            $decodedResult = (object)["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
        } else {
            // Decode the JSON result
            $decodedResult = json_decode($result);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error, if needed
                $decodedResult = (object)["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
            }
        }
        curl_close($ch);

        return $decodedResult;
    }

    public static function getAuthorityName($authorityId) {
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();
        
        $stmt = $conn->prepare('SELECT sort_name FROM ls_authorities WHERE id = :authority_id');
        $stmt->bindParam(':authority_id', $authorityId);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return null;
        } else {
            return $stmt->fetchColumn();
        }
    }

    public static function getCurrentStatus($systemId, $department, $authority = NULL) {
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        if ($authority == NULL) {
            $authority = 0;
        }

        $query = "SELECT status_id FROM public.ctrl_statuses WHERE system_id = :systemId AND department = :department AND authority = :authority";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':authority', $authority);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return null;
        } else {
            return $stmt->fetchColumn();
        }
    }
}


