<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// require "config/system.php";

header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

require_once "api/functions.php";
require_once "config/DBFactory.php";


$data = file_get_contents("php://input");
$POST = json_decode($data, true);

// Check if the user has entered their login information
if (isset($POST['username']) && isset($POST['password'])) {
    // Get the username and password from the POST request
    $username = $POST['username'];
    $password = $POST['password'];
    $systemId = $POST['systemId'];

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT role_id, username, password, activation, first_name, phone_no, telegram_id FROM sys_hr_employee LEFT JOIN sys_users ON sys_hr_employee.id = sys_users.employee_id WHERE username = :username ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':username', $username);

    // Execute the query
    $stmt->execute();

    // Fetch all rows from the result set as an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Return the user information to the client
    if (count($result) > 0) {
        foreach ($result as $row) {

            if (password_verify($password, $row["password"])) {
                $number = $row['phone_no'];
                $telegramID = $row['telegram_id'];
                $firstName = $row['first_name'];
                $maskedNumber = str_repeat("*", strlen($number) - 4) . substr($number, -4);
                // Start a session and store the user's information in the session
                if ($row["activation"] == "t") {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    ;

                    // regenerate the session ID
                    session_regenerate_id();

                    $_SESSION['username'] = $row["username"];
                    $_SESSION['roleId'] = $row["role_id"];


                    // Send data to tb_survey_attendance
                    $attendanceData = array(
                        'system_id' => $systemId,
                        'username' => $row["username"],
                        'clock_in' => date("H:i:s"), // Replace with the actual clock-in value (you can modify the format as needed)
                        'submitted' => date("Y-m-d H:i:s")
                    );
                    // $pCategory = 'PIU';

                    // Insert data into tb_survey_attendance
                    $attendanceStmt = $conn->prepare("INSERT INTO flw_survey_attandance (system_id, survey_username, clock_in, created_timestamp) VALUES (:system_id, :username, :clock_in, :submitted)");
                    $attendanceStmt->execute($attendanceData);

                    // Return a success message in the API response
                    http_response_code(200);
                    echo json_encode([
                        "message" => "success",
                        "status" => 200,
                        "first_name" => $firstName,
                    ]);
                }
            } else {
                // Return an error message
                http_response_code(500);
                echo json_encode([
                    "message" => "failed",
                    "status" => 500
                ]);
            }
        }
    } else {
        // Return an error message
        http_response_code(500);
        echo json_encode([
            "message" => "failed",
            "status" => 500
        ]);
    }

    // Close the database connection
    $conn = null;
} else {
}
