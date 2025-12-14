<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Get the raw input data
    $inputData = file_get_contents('php://input');

    $formData = json_decode($inputData, true);
    // Check if JSON data was parsed successfully
    if ($formData === null) {
        echo 'Error parsing JSON data';
        exit;
    }

    // Gather form data
    $firstName    = $formData['first_name'];
    $lastName     = $formData['last_name'];
    $phoneNo      = $formData['phone_no'];
    $address1     = $formData['first_address'];
    $address2     = $formData['second_address'];
    $postcode     = $formData['postcode'];
    $updatedAt    = date('Y-m-d H:i:s', time());
    // Add other form fields here

    $stmt = $conn->prepare("UPDATE sys_hr_employee SET first_name = :firstName, last_name = :lastName, phone_no = :phoneNo, first_address = :address1, second_address = :address2, postcode = :postcode, updated_at = :updatedAt WHERE id = (SELECT employee_id FROM sys_users WHERE username = :username)");
    $stmt->bindParam(':firstName', $firstName);
    $stmt->bindParam(':lastName', $lastName);
    $stmt->bindParam(':phoneNo', $phoneNo);
    $stmt->bindParam(':address1', $address1);
    $stmt->bindParam(':address2', $address2);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':updatedAt', $updatedAt);
    $stmt->bindParam(':username', $_SESSION['username']);

    // Execute the query
    $stmt->execute();

    $message = "Terima kasih! Maklumat anda berjaya dikemaskini 🎉";

    http_response_code(200);
    $result = array(
        "success" => true,
        "message" => $message,
    );

    $result['success'] === true ? $status = true : $status = false;        
    $headers = json_encode($curl->getHeaders());
    // Get protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $domain = $_SERVER['HTTP_HOST'];
    $requestUri = $_SERVER['REQUEST_URI'];
    $url = $protocol . '://' . $domain . $requestUri;
    $curl->callback($url, $headers, $inputData, json_encode($result), $_SERVER['REQUEST_METHOD'], $status);

    echo json_encode($result);
    
    // Close the database connection
    $conn = null;

} else {

    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    $stmt = $conn->prepare("SELECT * FROM sys_hr_employee
                LEFT JOIN sys_users ON sys_hr_employee.id = sys_users.employee_id
                WHERE sys_users.username = :username LIMIT 1");
    $stmt->bindValue(':username', $_SESSION['username']);
    $stmt->execute();

    // Fetch all rows from the result set as an associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    $result = array(
        "success" => true,
        "data" => $data,
    );

    // Return the JSON string to the client
    echo json_encode($result);

    // Close the database connection
    $conn = null;

}
