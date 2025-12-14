<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// require "api/header.php";
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/functions.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

$db = new DBConnectionFactory();

// Check if the user has entered their login information
if (isset($POST['active-code']) && !isset($POST['emp-id'])) {
    // Get the code from the POST request
    $code = $POST['active-code'];

    // Connect to the database using PDO
    $conn = $db->createConnection();

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT activation, id FROM sys_users WHERE activation_code = :code ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':code', $code);

    // Execute the query
    $stmt->execute(); 

    // Fetch all rows into an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $row = $result[0]; // Get the first row of the result set
        $staffID = $row["id"];
        $activate = date('Y-m-d H:i:s', time());
        $activation = 't';
        $activation_code = NULL;
        $activation_request = NULL;

        // Prepare the query for update
        $query2 = "UPDATE sys_users SET activation = :activation, activation_date = :activate, activation_code = :activation_code, activation_request = :activation_request WHERE id = :emp_id ";
        $stmt2 = $conn->prepare($query2);

        // Bind the parameters
        $stmt2->bindParam(':activation', $activation);
        $stmt2->bindParam(':activate', $activate);
        $stmt2->bindParam(':activation_code', $activation_code);
        $stmt2->bindParam(':activation_request', $activation_request);
        $stmt2->bindParam(':emp_id', $staffID);

        // Execute the query
        $stmt2->execute();   

        // Return an error message
        echo json_encode( 
            array(
                "message" => 'activated',
            )
        );
    } else {
        // Return an error message
        echo json_encode( 
            array(
                "message" => 'invalid',
            )
        );

    }

    // Close the database connection
    $conn = null;

} else if (isset($POST['reset-code']) && !isset($POST['emp-id'])) {
    // Get the code from the POST request
    $code = $POST['reset-code'];
    // Connect to the database using PDO
    $conn = $db->createConnection();
    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT id, username FROM sys_users WHERE activation_code = :code ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':code', $code);

    // Execute the query
    $stmt->execute(); 

    // Fetch all rows into an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $row = $result[0]; // Get the first row of the result set
        $staffID = $row["id"];
        $username = $row['username'];
        
        // Return an error message
        echo json_encode( 
            array(
                "message" => "success",
                "id" => $staffID,
                "username" => $username
            )
        );
    } else {
        // Return an error message
        echo json_encode( 
            array(
                "message" => "error",
            )
        );

    }

    // Close the database connection
    $conn = null;

} else if (isset($POST['emp-id']) && isset($POST['username'])) {
    // Connect to the database using PDO
    $conn = $db->createConnection();

    $empId = isset($POST['emp-id']) ? $POST['emp-id'] : '';
    $username = isset($POST['username']) ? $POST['username'] : '';
    $password = isset($POST['password']) ? $POST['password'] : '';
    $confirmPassword = isset($POST['confirm-password']) ? $POST['confirm-password'] : '';
    $timestamp = date('Y-m-d H:i:s', time());
    $activation_code = NULL;
    $activation_request = NULL;

    if ($password === $confirmPassword){
        // Hash the password for security
        $options = [
            'cost' => 14,
        ];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);
    }


    // Prepare the query for update
    $query = "UPDATE sys_users SET password = :hashedPassword, activation_code = :activation_code, activation_request = :activation_request WHERE id = :emp_id ";
    $stmt = $conn->prepare($query);

    // Bind the parameters
    $stmt->bindParam(':hashedPassword', $hashedPassword);
    $stmt->bindParam(':activation_code', $activation_code);
    $stmt->bindParam(':activation_request', $activation_request);
    $stmt->bindParam(':emp_id', $empId);

    // Execute the query
    $stmt->execute(); 

    // Create a changelog instance
    $changelog = new Changelog($username);

    $message = "Kata Laluan $username telah diset semula.";
    $pages = "verify";
    if($changelog->userActivity($message,$pages)) {
        // Return an error message
        header('HTTP/2 200 OK');
        echo json_encode( 
            array(
                "message" => "success",
            )
        );
    }
    // Close the database connection
    $conn = null;

} else {

}


?>