<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "api/header.php";
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/functions.php";

// Check if the URL contains a parameter named
if(isset($_GET['search'])) {
    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Get the user's identification from the request parameters
    $s = isset($_GET['search']) ? $_GET['search'] : '';
    $search = '%' . strtoupper($s) . '%';
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;

    // Prepare the SELECT query
    $query = "SELECT flw_appl_entries.id AS \"ID\",
        flw_appl_entries.system_id AS \"systemID\",
        flw_appl_entries.reference_no AS \"referenceNo\",
        ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ',') AS array_to_string
        FROM sys_upi
        WHERE sys_upi.state_code = flw_appl_entries.state AND (sys_upi.district_code = ANY (flw_appl_entries.districts))) AS \"District\",
        flw_appl_entries.application_length AS \"Length\",
        flw_appl_entries.utility_provider AS \"providerID\",
        flw_appl_entries.project_title AS \"Title\",
        flw_appl_entries.old_system AS \"OldSystem\"
        FROM flw_appl_entries
        WHERE flw_appl_entries.reference_no LIKE :search OR flw_appl_entries.system_id LIKE :search OR flw_appl_entries.project_title LIKE :search
        GROUP BY flw_appl_entries.id, flw_appl_entries.system_id, flw_appl_entries.reference_no, flw_appl_entries.application_length, flw_appl_entries.utility_provider, flw_appl_entries.state, flw_appl_entries.districts
        LIMIT :limit";

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind the parameters
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the rows from the query result as an associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Iterate through each element in the $data array
    foreach ($data as &$row) {
        // Get provider data for each element
        $provider = Utilities::getProvider($row['providerID']);

        // Append the provider data to the current row
        $row['providerData'] = $provider;
    }

    // Finally, return a JSON
    http_response_code(200);
    echo json_encode([
        "message"   => "Success",
        "data"      => $data
    ]);
    // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
    $conn = null;

}