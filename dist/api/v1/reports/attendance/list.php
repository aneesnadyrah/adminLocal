<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
};

require "api/header.php";
require "config/system.php";
require "config/DBFactory.php";
require "config/tenant.php";

// initiate db connection instance
$db = new DBConnectionFactory();

if (isset($_POST['item'])) {
    if ($_POST['item'] == "guestList") {
        // Connect to the database using PDO
        $conn = $db->createConnection();

        // Execute a SELECT query on the database
        // Get the contact signature list
        $query = "SELECT flw_appl_reports.signature_id
        FROM flw_appl_reports
        WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid";

        $stmt = $conn->prepare($query);
        $stmt->bindValue(':sid', $_POST['sid'], PDO::PARAM_STR);
        $stmt->bindValue(':rn', $_POST['rn'], PDO::PARAM_STR);
        $stmt->bindValue(':aid', $_POST['aid'], PDO::PARAM_STR);
        $stmt->execute();
        $signatureIdStr = $stmt->fetchColumn();

        // convert into array
        $signatureIdArray = array_map('intval',explode(',', str_replace(array('{', '}'), '', $signatureIdStr)));

        // get the contact details
        $guestDetails = [];
        foreach ($signatureIdArray as $signatureId) {
            $query = "SELECT * FROM contact_on_signature WHERE id = :signatureId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':signatureId', $signatureId, PDO::PARAM_STR);
            $stmt->execute();
            $guestDetails[] = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // var_dump($guestDetails);
        
        // Close the database connection
        $conn = null;

        // Convert the result to a JSON string
        $json = json_encode($guestDetails);

        // Return the JSON string to the client
        echo $json;

    } else {
        echo "Invalid Parameter!!!";
    }
} else {
    echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
}