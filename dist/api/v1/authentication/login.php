<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
require "api/functions.php";

header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');


    $db         = new DBConnectionFactory();
    $data       = file_get_contents('php://input');

    $POST       = json_decode($data);

    // Get the username and password from the POST request
    $username   = $POST->username;
    $password   = $POST->password;
    $changelog  = new Changelog($username);

    // Create a new Telegram object
    $telegram = new Telegram();

    // Connect to the database using PDO
    $conn       = $db->createConnection();

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT role_id, username, password, activation, first_name, phone_no, email, telegram_id, sub_department FROM sys_hr_employee LEFT JOIN sys_users ON sys_hr_employee.id = sys_users.employee_id WHERE  username = :username ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindParam(':username', $username);

    // Execute the query
    $stmt->execute();

    // Fetch all rows from the result set as an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the user information to the client
    if (count($result) > 0) {
        foreach ($result as $row) {
            if(password_verify($password, $row["password"]))  {
                $number       = $row['phone_no'];
                $telegramID   = $row['telegram_id'];
                $firstName    = $row['first_name'];
                $maskedNumber = str_repeat("*", strlen($number) - 4) . substr($number, -4);
                // Start a session and store the user's information in the session
                if($row["activation"] == "t") {

                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    };

                    // regenerate the session ID
                    session_regenerate_id();

                    $_SESSION['username']   = $row["username"];
                    $_SESSION['roleId']       = $row["role_id"];
                    $_SESSION['sub_department']    = $row["sub_department"];
                    $message = "Pengguna " . $row["username"] . " telah log masuk.";
                    $pages = "login";
                    if($changelog->userActivity($message, $pages)){
                        // Return a success message in the API response
                        header('HTTP/2 200 OK');
                        echo json_encode(
                            array(
                                "message" => "success",
                                "first_name" => $firstName,
                            )
                        );
                    };

                } else {
                    $verificationCode = Utilities::generateCode();
                    $telegramVerifyMsg = "Kod Pengesahan Akaun Anda Adalah *$verificationCode*\\. Jika Anda Tidak Membuat Sebarang Permintaan Kod Pengesahan, Sila Hubungi Sokongan Teknikal Kami\\.";
                    $telegramResponse = $telegram->sendMessage('user', $username, $telegramVerifyMsg, 'html');
                    $message = "Pengguna " . $row["username"] . " telah log masuk dan telah diberi kod verifikasi. Kod verifikasi : " . $verificationCode;
                    $pages = "login";
                    if($changelog->userActivity($message, $pages)){
                        header('HTTP/2 200 OK');
                        echo json_encode(
                            array(
                                "message"   => "false",
                                "telegram_id"   => $telegramID,
                                "phone_no"      => $maskedNumber,
                                "code"          => $verificationCode,
                                "first_name"    => $firstName,
                            )
                        );
                    };

                }

            } else {
                // Return an error message
                echo json_encode(
                    array(
                        "message"   => "failed",
                    )
                );
            }
        }
    } else {
        // Return an error message
        echo json_encode(
            array(
                "message"   => "error",
            )
        );
    }
    // Close the database connection
    $conn = null;
