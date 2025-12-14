<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
require "api/functions.php";

// Check if the URL contains a parameter named
if(isset($_GET['postcode'])){
    // Get the postcode from the request parameters
    $postcode = isset($_GET['postcode']) ? $_GET['postcode'] : '';

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT * FROM ls_postcode WHERE postcode = :postcode ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':postcode', $postcode);

    // Execute the query
    $stmt->execute(); 

    // Fetch all rows into an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $row = $result[0]; // Get the first row of the result set
        $postcode = $row["postcode"];
        $city = $row["city"];
        $state = $row["state"];
    }
        
    // Finally, return a JSON response
    http_response_code(200);
    echo json_encode([
        'message' => 'Success',
        'status' => 200,
        'postcode' => $postcode,
        'city' => $city,
        'state' => $state,
    ]);
    
    // Close the database connection
    $conn = null;

} else {

}
