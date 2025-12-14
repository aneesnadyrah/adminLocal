<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

class General
{
    // Function to establish a PDO connection
    public static function connectToDatabase()
    {
        require_once "config/system.php";
        $system = new System;
        $PDO = $system->DBConnection;

        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Handle connection error
            die('Connection failed: ' . $e->getMessage());
        }
        return $conn;
    }

    // function to get this reference_no
    public static function getReferenceNo($systemId)
    {
        require "config/system.php";
        $conn = General::connectToDatabase();

        // Construct the SQL query to retrieve the submission code
        $sql = "SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId";

        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':systemId' => $systemId
        ]);

        // Fetch the submission code
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection
        $conn = null;

        return $referenceNo;
    }

    // function to get all reference_no other than this one
    public static function getAllRefExcept($systemId)
    {
        // Establish a database connection
        $pdo = General::connectToDatabase();

        // Construct the SQL query to retrieve the submission code
        $sql = "SELECT reference_no FROM flw_appl_entries WHERE system_id <> :systemId";

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':systemId' => $systemId
        ]);

        // Fetch the submission code
        $referenceNo = $stmt->fetchAll();

        return $referenceNo;
    }

    // function to retrieve authority_id from ctrl_authorities
    public static function getAuthorityId($flwAuthId)
    {
        // retrieve global variable
        global $PDO;

        // Establish a database connection
        $pdo = General::connectToDatabase();

        // Construct the SQL query to retrieve the submission code
        $sql = "SELECT authority_id FROM ctrl_authorities WHERE id = :flwAuthId";

        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':flwAuthId' => $flwAuthId
        ]);

        // Fetch the submission code
        $authId = $stmt->fetchColumn();

        return $authId;
    }

    // function to get this reference_no
    public static function getDataEntry($systemId)
    {
        $conn = General::connectToDatabase();

        // Construct the SQL query to retrieve the submission code
        $sql = "SELECT * FROM flw_appl_entries WHERE system_id = :systemId";

        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':systemId' => $systemId
        ]);

        // Fetch the submission code
        $dataEntry = $stmt->fetch(PDO::FETCH_ASSOC);        

        // Close the database connection
        $conn = null;

        return $dataEntry;
    }

    //Functions Get Assets or Components
    public static function getDashboard($path)
    {
        return "components/dashboards/" . $path . ".php";
    }

    public static function getButton($path)
    {
        return "components/partials/quick-buttons/" . $path . ".php";
    }

    public static function getWidget($path)
    {
        return "components/partials/widgets/" . $path . ".php";
    }

    public static function getDrawer($path)
    {
        return "components/partials/drawers/" . $path . ".php";
    }

    public static function getModal($path)
    {
        return "components/partials/modals/" . $path . ".php";
    }

    public static function getForm($path)
    {
        return "components/forms/" . $path . ".php";
    }

    public static function getTable($path)
    {
        return "components/tables/" . $path . ".php";
    }

    public static function getProfile($path)
    {
        return "assets/media/avatars/" . $path;
    }

     private static $dictionary = [
        // Main modules
        'operation' => 'Operasi',
        'projects' => 'Projek',
        'letters' => 'Surat',
        'surveys' => 'Ukur',
        'reports' => 'Laporan',
        'dashboard' => 'Papan Pemuka',
        'account' => 'Akaun',
        'wayleave' => 'Izin Lalu',
        'geospatial' => 'Geospatial',
        'tracker' => 'Penjejak',
        'tasks' => 'Tugasan',
        'api' => 'API',
        'components' => 'Komponen',
        'views' => 'Paparan',
        'letter' => 'Surat',
        'cpc' => 'Sijil Siap Kerja',

        // Sub-modules/actions
        'general' => 'Umum',
        'authority' => 'Pihak Berkuasa',
        'details' => 'Butiran',
        'site' => 'Tapak',
        'record' => 'Rekod',
        'team' => 'Kumpulan',
        'priority' => 'Keutamaan',
        'summary' => 'Ringkasan',
        'deposit' => 'Wang Cagaran',
        'calendar' => 'Kalendar',
        'list' => 'Senarai',
        'entry' => 'Kemasukan',
        'check' => 'Semak',
        'approval' => 'Kelulusan',
        'feedback' => 'Maklum Balas',
        'progress' => 'Kemajuan',
        'report' => 'Laporan',
        'profile' => 'Profil',
        'new' => 'Baharu',
        'add' => 'Tambah',
        'edit' => 'Edit',
        'view' => 'Lihat',
        'create' => 'Cipta',
        'update' => 'Kemaskini',
        'delete' => 'Padam',
        'search' => 'Cari',
        'filter' => 'Tapis',
        'export' => 'Eksport',
        'import' => 'Import',
        'download' => 'Muat Turun',
        'upload' => 'Muat Naik',
        'admin' => 'Pentadbir',
        'user' => 'Pengguna',
        'settings' => 'Tetapan',
        'config' => 'Konfigurasi',
        'manage' => 'Urus',
        'management' => 'Pengurusan',
        'reviews' => 'Semakan',
        'extract' => 'Ekstrak',

        // Status/States
        'pending' => 'Tertunggak',
        'approved' => 'Diluluskan',
        'rejected' => 'Ditolak',
        'completed' => 'Selesai',
        'in-progress' => 'Dalam Proses',
        'draft' => 'Draf',
        'published' => 'Diterbitkan',
        'active' => 'Aktif',
        'inactive' => 'Tidak Aktif',
        'archived' => 'Diarkib',

        // File types
        'php' => '',
        'html' => '',
        'pdf' => 'PDF',
        'doc' => 'Dokumen',
        'xls' => 'Spreadsheet',

        // Common terms
        'in' => 'Masuk',
        'out' => 'Keluar',
        'internal' => 'Dalaman',
        'external' => 'Luaran',
        'public' => 'Awam',
        'private' => 'Peribadi',
        'system' => 'Sistem',
        'data' => 'Data',
        'file' => 'Fail',
        'image' => 'Imej',
        'document' => 'Dokumen',
    ];

   /**
     * Extract and clean URL segments
     */
    private static function getUrlSegments()
    {
        $url = $_SERVER['REQUEST_URI'];
        $path = strtok($url, '?');
        $segments = array_filter(explode('/', trim($path, '/')));
        
        $cleanSegments = [];
        foreach ($segments as $segment) {
            $segment = preg_replace('/\.(php|html|htm)$/i', '', $segment);
            if (!empty($segment)) {
                $cleanSegments[] = strtolower($segment);
            }
        }
        
        return $cleanSegments;
    }

    /**
     * Translate segment using dictionary
     */
    private static function translateSegment($segment)
    {
        return self::$dictionary[strtolower($segment)] ?? ucfirst($segment);
    }

    /**
     * Page Title = First segment (module/section)
     */
    public static function pageTitle()
    {
        $segments = self::getUrlSegments();
        
        if (empty($segments) || $segments[0] === 'dashboard') {
            return 'Papan Pemuka';
        }
        
        return self::translateSegment($segments[0]);
    }

    /**
     * Main Breadcrumb = Second to last segment
     */
    public static function mainBreadcrumb()
    {
        $segments = self::getUrlSegments();
        
        if (empty($segments) || $segments[0] === 'dashboard') {
            return 'Laman Utama';
        }
        
        if (count($segments) === 1) {
            return self::translateSegment($segments[0]);
        }
        
        // Get second to last segment
        $secondToLast = $segments[count($segments) - 2];
        return self::translateSegment($secondToLast);
    }

    /**
     * Sub Breadcrumb = Last segment  
     */
    public static function subBreadcrumb()
    {
        $segments = self::getUrlSegments();
        
        if (empty($segments) || $segments[0] === 'dashboard') {
            return 'Papan Pemuka';
        }
        
        if (count($segments) === 1) {
            return self::translateSegment($segments[0]);
        }
        
        // Get last segment
        $lastSegment = end($segments);
        return self::translateSegment($lastSegment);
    }

    /**
     * Add new dictionary entry
     */
    public static function addToDictionary($key, $value)
    {
        self::$dictionary[strtolower($key)] = $value;
    }

    public static function isMenuActive($path)
    {
        if ($_SERVER["REQUEST_URI"] == $path) {
            return "active";
        }
        return "";
    }

    public static function isAccordianActive($path)
    {
        if ($_SERVER["REQUEST_URI"] == $path) {
            return "hover show";
        }
        return "";
    }

    public static function convertTime($date)
    {
        $formatter = new IntlDateFormatter('ms_MY', IntlDateFormatter::NONE, IntlDateFormatter::SHORT);
        $time = $formatter->format(new DateTime($date));

        return $time;
    }

    public static function convertDate($date)
    {
        $formatter = new IntlDateFormatter('ms_MY', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        $new_date = $formatter->format(strtotime($date));

        return $new_date;
    }

    public static function UPISelection($state, $query = NULL)
    {
        require_once "config/system.php";

        $conn = General::connectToDatabase();

        if ($query == NULL) {
            $stmt = $conn->prepare("SELECT district_code, district_name FROM sys_upi  WHERE state_code = :state  GROUP BY district_code, district_name ORDER BY district_code ASC");
            $stmt->bindParam(":state", $state);
        } else {
        }
        // Execute the query

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $list = $data;

        // Close the database connection
        $conn = null;

        return $list;
    }

    public static function authoritySelection($state, $query = NULL)
    {
        // require "config/system.php";

        $conn = General::connectToDatabase();

        if ($query == NULL) {
            $stmt = $conn->prepare("SELECT id, sort_name, district_code FROM ls_authorities WHERE state_code = :state GROUP BY id,sort_name, district_code ORDER BY sort_name ASC");
            $stmt->bindParam(":state", $state);
        } else {
        }
        // Execute the query

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $list = $data;

        // Close the database connection
        $conn = null;

        return $list;
    }

    //Function for assign selection
    public static function selection($path)
    {
        // require "config/system.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare("SELECT * FROM $path ORDER BY id ASC");

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $list = $data;

        // Close the database connection
        $conn = null;

        return $list;
    }

    // REVIEW - no reference to this function
    // public static function authorityDetails($id, $selection)
    // {
    //     require "config/config.php";

    //     $conn = General::connectToDatabase();

    //     try {
    //         // Execute a SELECT query on the database based on the condition
    //         if ($selection == 1) {
    //             $query = "SELECT
    //         ls_authorities.id AS \"AuthorityID\",
    //         ls_authorities.name AS \"Authority\"
    //         FROM sys_record_changelog
    //         LEFT JOIN ls_authorities ON ls_authorities.id = sys_record_changelog.authority
    //         WHERE sys_record_changelog.authority IS NOT NULL
    //         AND sys_record_changelog.system_id = :id
    //         ORDER BY ls_authorities.name DESC";

    //             $stmt = $conn->prepare($query);
    //             $stmt->bindParam(':id', $id);
    //         }

    //         // Execute the query
    //         $stmt->execute();

    //         // Fetch the rows as an associative array
    //         $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         // Convert the result to an array
    //         $detail = $data;

    //         // Close the database connection
    //         $conn = null;

    //     } catch (PDOException $e) {
    //         die("Error in query: " . $e->getMessage());
    //     }


    //     return $detail;
    // }

    public static function getStaff($username)
    {
        require "config/system.php";

        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT * FROM sys_users LEFT JOIN sys_hr_employee ON sys_users.employee_id = sys_hr_employee.id LEFT JOIN ls_postcode ON sys_hr_employee.postcode = ls_postcode.postcode WHERE username = :username LIMIT 1');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result;
    }

    public static function getProvider($id)
    {
        require_once "config/system.php";
        $system = new System;
        $ec_url = $system->App->ec_url;
        $url = $ec_url.'/api/providers/' . $id;
        $timeout = 3;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);
        if ($result === false) {
            // Handle cURL error, if needed
            $decodedResult = (object) ["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
        } else {
            // Decode the JSON result
            $decodedResult = json_decode($result);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error, if needed
                $decodedResult = (object) ["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
            }
        }
        curl_close($ch);

        return $decodedResult;
    }

    public static function getAllProvider()
    {
        require_once "config/system.php";

        $conn = General::connectToDatabase();
        $sql = "SELECT * FROM ls_providers ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $conn = null;

        $system = new System;
        $decodedResult = [];
        foreach($result as $provider) {
            $url = $system->App->ec_url .'/api/providers/' . $provider->id;
            $timeout = 3;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $result = curl_exec($ch);
            if ($result === false) {
                // Handle cURL error, if needed
                $decodedResult[] = (object) ["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
            } else {
                // Decode the JSON result
                $decodedResult[] = json_decode($result);

                // Check for JSON decoding errors
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Handle JSON decoding error, if needed
                    $decodedResult[] = (object) ["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
                }
            }
            curl_close($ch);
        }
        
        return $decodedResult;
    }

    public static function getAttachment($systemId, $code)
    {
        $db = General::connectToDatabase();

        $query = "SELECT flw_appl_attachments.url, flw_appl_attachments.mime_type FROM flw_appl_attachments 
        LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type 
        WHERE ls_attachments.code_name = :code AND flw_appl_attachments.system_id = :systemId 
        ORDER BY flw_appl_attachments.id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        $data = new stdClass();
        if ($stmt->rowCount() == 0) {
            $data = (object) [
                'url' => NULL,
                'mime_type' => NULL,
            ];
        } else {
            $data->url = $row->url;
            $data->mime_type = $row->mime_type;
        }

        return $data;


    }

    public static function getPaymentMethod($systemId)
    {

        require_once "config/system.php";
        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT * FROM flw_appl_entries
                WHERE system_id = :id');
        $stmt->bindParam(':id', $systemId);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result;
    }

    public static function getClientStatus() {
        $system = new System();
        $ec_url = $system->App->ec_url;
        $url = $ec_url . '/api/clientStatus';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            // Handle cURL error, if needed
            $decodedResult = (object)["id" => "", "reference_no" => "", "sub_id" => "", "created_at" => "", "provider" => "", "provider_img" => "", "district" => "", "payment_method" => "", "status" => "", "length" => ""];
        } else {
            // Decode the JSON result
            $decodedResult = json_decode($result);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error, if needed
                $decodedResult = (object)["id" => "", "reference_no" => "", "sub_id" => "", "created_at" => "", "provider" => "", "provider_img" => "", "district" => "", "payment_method" => "", "status" => "", "length" => ""];
            }
        }
        curl_close($ch);

        return $decodedResult;
    }
}

// * api function
function convertDate($date)
{
    $formatter = new IntlDateFormatter('ms_MY', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    $new_date = $formatter->format(strtotime($date));

    return $new_date;
}
