<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// require "api/header.php";
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/functions.php";
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

$data = file_get_contents("php://input");
$POST = json_decode($data, true);
// var_dump($POST);
// var_dump("123");
// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();

// Check if the user has entered their login information
if (isset($POST['ID-card-no']) && isset($POST['phone-no'])) {
    // var_dump("keluarrrr");
    // Get the username and password from the POST request
    $idCard   = $POST['ID-card-no'];
    $phoneNo  = $POST['phone-no'];

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT role_id, activation, phone_no, username, telegram_id FROM sys_hr_employee LEFT JOIN sys_users ON sys_hr_employee.id = sys_users.employee_id WHERE identification_card = :idCard AND phone_no = :phoneNo");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':idCard', $idCard);
    $stmt->bindValue(':phoneNo', $phoneNo);

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the user information to the client
    if (count($result) > 0) {
        // var_dump("keluarrrr123");
        foreach ($result as $row) {
            $number       = $row['phone_no'];
            $telegramID   = $row['telegram_id'];
            $username     = $row['username'];
            $maskedNumber = str_repeat("*", strlen($number) - 4) . substr($number, -4);
            // Start a session and store the user's information in the session
            $verificationCode = Utilities::generateCode();
            header('HTTP/2 200 OK');
            echo json_encode(
                array(
                    "message"       => "success",
                    "telegram_id"   => $telegramID,
                    "phone_no"      => $maskedNumber,
                    "code"          => $verificationCode,
                )
            );
        }
    } else if(count($result) == 0)  {
        echo json_encode(
            array(
                "message"   => "failed",
                "status"    => 200,
            )
        );
    } else  {
        // Return an error message
        echo json_encode(
            array(
                "message"   => "error",
                "status"    => 500,
            )
        );
    }

    // Close the database connection
    $conn = null;
} else {

}
