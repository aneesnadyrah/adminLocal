<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

class Report
{
    public static function getActiveReport($systemId){
        $conn = General::connectToDatabase();

        $query = "SELECT id FROM ctrl_site_report WHERE system_id = :systemId AND completed = false ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $ctrlId = $stmt->fetchColumn();

        $query = "SELECT report_no FROM flw_appl_reports WHERE ctrl_site_report_id = :ctrlId LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':ctrlId', $ctrlId);
        $stmt->execute();
        $reportNo = $stmt->fetchColumn();

        return (object)[
            'reportCtrl' => $ctrlId,
            'reportNo' => $reportNo
        ];
    }
    // public static function for reportDetails retrieval
    public static function getReportDetails($systemId, $reportId, $authId = null)
    {
        try {
            $pdo = General::connectToDatabase();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($authId == null) {
                $query = 'SELECT * FROM view_report_sitevisit WHERE system_id = :systemId AND report_no = :reportId';
            } else {
                $query = 'SELECT * FROM view_report_sitevisit WHERE system_id = :systemId AND report_no = :reportId AND authority_id = :authId';
            }
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':reportId', $reportId);
            if ($authId != null) {
                $stmt->bindParam(':authId', $authId);
            }
            $stmt->execute();

            if ($authId == null) {
                $currentReport = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $currentReport = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            $pdo = null;
            return $currentReport;
        } catch (PDOException $e) {
            return 'Connection failed: ' . $e->getMessage();
        }
    }

    // public static function for review retrieval
    public static function getTextReview($referenceNo, $reportId, $systemId, $authId)
    {
        $explodedRef = explode("/", $referenceNo);
        $exploded = explode("/", $reportId);
        $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
        $codedReport = base64_encode($shortRef);

        try {
            // Create a new PDO instance
            $connPdo = General::connectToDatabase();

            // Set error mode to exceptions
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "SELECT * FROM public.geom_site_visit WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND ST_GeometryType(geom) != 'ST_Point'";

            // Prepare the query
            $stmt = $connPdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':authId', $authId);
            // Execute the query
            $stmt->execute();

            // Fetch the data from the query result
            $reviewCol = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $connPdo = null;

            return $reviewCol;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function getImgReview($referenceNo, $reportId, $systemId, $authId)
    {
        $explodedRef = explode("/", $referenceNo);
        $exploded = explode("/", $reportId);
        $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
        $codedReport = base64_encode($shortRef);

        try {
            // Create a new PDO instance
            $conn = General::connectToDatabase();

            // Set error mode to exceptions
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "SELECT *, ST_X(geom) AS longitude, ST_Y(geom) AS latitude  FROM public.geom_site_visit WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND ST_GeometryType(geom) = 'ST_Point'";

            // Prepare the query
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':authId', $authId);

            // Execute the query
            $stmt->execute();

            // Fetch the data from the query result
            $reviewColPic = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $reviewColPic;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function getSignatureValidation($systemId, $reportId, $authId, $signType)
    {
        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        // Get the contact signature list
        $query = "SELECT view_report_sitevisit.signature_id
    FROM view_report_sitevisit
    WHERE view_report_sitevisit.system_id = :systemId AND view_report_sitevisit.report_no = :reportId AND view_report_sitevisit.authority_id = :authId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':reportId', $reportId);
        $stmt->bindParam(':authId', $authId);
        $stmt->execute();
        $signatureIdStr = $stmt->fetchColumn();

        // convert into array
        $signatureIdArray = array_map('intval', explode(',', str_replace(array('{', '}'), '', $signatureIdStr)));

        // get the contact details
        $guestDetails = [];
        foreach ($signatureIdArray as $signatureId) {
            // TODO: this query can be simplified using where in condition on id with sets of signatureId
            $query = "SELECT * FROM view_signature WHERE id = :signatureId AND sign_type = :signType;";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':signatureId', $signatureId, PDO::PARAM_STR);
            $stmt->bindParam(':signType', $signType, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $guestDetails[] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        // Return the JSON string to the client
        return $guestDetails;
    }

    public static function getRoadList($systemId, $reportId, $authId)
    {
        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        // Get the contact signature list
        $query = "SELECT view_report_sitevisit.road_involved_id
    FROM view_report_sitevisit
    WHERE view_report_sitevisit.system_id = :systemId AND view_report_sitevisit.report_no = :reportId AND view_report_sitevisit.authority_id = :authId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':reportId', $reportId);
        $stmt->bindParam(':authId', $authId);
        $stmt->execute();
        $roadIdStr = $stmt->fetchColumn();

        // convert into array
        $roadIdArray = array_map('intval', explode(',', str_replace(array('{', '}'), '', $roadIdStr)));

        // var_dump($roadIdArray);

        // get the contact details
        $roadDetails = [];
        foreach ($roadIdArray as $roadId) {
            $query = "SELECT * FROM flw_pkd_roads WHERE id = :roadId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':roadId', $roadId, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $roadDetails[] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        foreach ($roadDetails as &$roadDetail) {
            // Extract the integers from the "method" value
            if (isset($roadDetail['method'])) {
                $methodStr = $roadDetail['method'];
                $methodStr = str_replace(array('{', '}'), '', $methodStr); // Remove curly braces from the string
                $methodArray = array_map('intval', explode(',', $methodStr));
                $methodName = [];

                foreach ($methodArray as $method) {
                    $query = "SELECT * FROM ls_work_methods WHERE id = :roadId";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':roadId', $method, PDO::PARAM_STR);
                    $stmt->execute();
                    $methodName[] = $stmt->fetch(PDO::FETCH_ASSOC);
                }



                // Replace the "method" value with the array of integers
                $roadDetail['method'] = $methodName;
            }
        }

        // Close the database connection
        $conn = null;

        // Return the JSON string to the client
        return $roadDetails;
    }

    //public static function to make change the date an time to string
    public static function formatDateAndTimeToMalayDateTimeString($dateTimeString)
    {
        // Parse the input date and time string into a DateTime object
        $dateTimeObj = new DateTime($dateTimeString);

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

        // Get the day, month, and year from the DateTime object
        $day = $dateTimeObj->format('d');
        $monthIndex = $dateTimeObj->format('n') - 1; // Adjust month index to 0-based
        $year = $dateTimeObj->format('Y');

        // Get the time in 12-hour format with leading zero
        $time = $dateTimeObj->format('h:i A');

        // Format the date and time as required: "06 Ogos 2023 05:55 PM"
        $formattedDateTime = "$day {$monthNames[$monthIndex]} $year $time";
        return $formattedDateTime;
    }

    //public static function to make change the date only to string
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

    //public static function to make change the time only to string
    public static function formatTimeToMalayTimeString($dateTimeString)
    {
        // Parse the input date and time string into a DateTime object
        $dateTimeObj = new DateTime($dateTimeString);

        // Get the time in 12-hour format with leading zero
        $time = $dateTimeObj->format('h:i A');

        return $time;
    }

}

// * api functions
//function to make change the date only to string
function convertDateToMalay($dateString)
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

// * api functions
//function to make change the time only to string
function formatTimeToMalayTimeString($dateTimeString)
{
    // Parse the input date and time string into a DateTime object
    $dateTimeObj = new DateTime($dateTimeString);

    // Get the time in 12-hour format with leading zero
    $time = $dateTimeObj->format('h:i A');

    return $time;
}

// * api functions
function generateCodedReport($systemId, $reportId)
{

    try {
        // Create a new PDO instance
        $connPdo = General::connectToDatabase();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $connPdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection
        $connPdo = null;

        if ($referenceNo) {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);

            return $codedReport;
        }

        return false;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}