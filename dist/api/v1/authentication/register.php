<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
require "api/functions.php";

$system = new System;
$db = new DBConnectionFactory();

// Check if the URL contains a parameter named
if(isset($_GET['username'])){
    // Get the user's identification from the request parameters
    $username = isset($_GET['username']) ? $_GET['username'] : '';

    // Connect to the database using PDO
    $conn = $db->createConnection();

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT username FROM sys_users WHERE username = :username");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':username', $username);

    // Execute the query
    $stmt->execute();

    // Fetch all rows into an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the user information to the client
    if (count($result) > 0) {
        $isAvailable    = false;
    } else {
        $isAvailable    = true;
    }

    // Finally, return a JSON
    echo json_encode(array(
        "valid" => $isAvailable,
    ));

    // Close the database connection
    $conn = null;


} elseif (isset($_POST['ID_card_no']) && !isset($_POST['password'])){
    // Get the user's identification from the request parameters
    $idcard = isset($_POST['ID_card_no']) ? $_POST['ID_card_no'] : '';

    // Connect to the database using PDO
    $conn = $db->createConnection();
    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT *, sys_hr_employee.id AS employee_id FROM sys_hr_employee LEFT JOIN sys_users ON sys_hr_employee.id = sys_users.employee_id WHERE sys_hr_employee.identification_card = :idcard ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindValue(':idcard', $idcard);

    // Execute the query
    $stmt->execute();

    // Fetch all rows into an array
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the user information to the client
    $isAvailable    = false;
    $registered     = false;
    $staffID        = "";
    $firstName      = "";
    $lastName       = "";
    $position       = "";
    $phoneNo        = "";
    $email          = "";
    $firstAddress   = "";
    $secondAddress  = "";
    $postcode       = "";


    if (count($result) > 0) {
        $isAvailable    = true;
        $row            = $result[0]; // Get the first row of the result set
        $staffID        = $row["employee_id"];
        $firstName      = $row["first_name"];
        $lastName       = $row["last_name"];
        $position       = $row["position"];
        $email          = $row["email"];
        $phoneNo        = $row["phone_no"];
        $firstAddress   = $row["first_address"];
        $secondAddress  = $row["second_address"];
        $postcode       = $row["postcode"];
        $register       = $row["activation"];

        if ($register == "t") {
            $registered = true;
        }
    }

    // Finally, return a JSON
    echo json_encode(
        array(
            "message"           => "Success",
            "valid"             => $isAvailable,
            "registered"        => $registered,
            "employee_id"       => $staffID,
            "first_name"        => $firstName,
            "last_name"         => $lastName,
            "position"          => $position,
            "email"             => $email,
            "phone_no"          => $phoneNo,
            "first_address"     => $firstAddress,
            "second_address"    => $secondAddress,
            "postcode"          => $postcode,
            "tenant"            => $system->App->tenant
        )
    );

    // Close the database connection
    $conn = null;


} elseif (isset($_POST['ID_card_no']) && isset($_POST['password'])){
    // Get the user's identification from the request parameters
    $idcard = isset($_POST['ID_card_no']) ? $_POST['ID_card_no'] : '';

    // Connect to the database using PDO
    $conn = $db->createConnection();

    // Prepare a SELECT query on the database
    $stmt = $conn->prepare("SELECT id, user_role FROM sys_hr_employee WHERE identification_card = :idcard ");

    // bind the parameter to the placeholder using the bindValue method
    $stmt->bindParam(':idcard', $idcard);
    // Execute the query
    $stmt->execute();
    // Fetch all rows into an array
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare('SELECT id FROM ls_user_roles WHERE role = :role');
    $stmt->bindParam(':role', $row['user_role']);
    $stmt->execute();
    $roleId = $stmt->fetchColumn();


    // Get the all value in input

    $staffID            = $row["id"];
    $role               = $roleId;
    $firstName          = isset($_POST['first-name']) ? $_POST['first-name'] : '';
    $lastName           = isset($_POST['last-name']) ? $_POST['last-name'] : '';
    $phoneNo            = isset($_POST['phone-no']) ? $_POST['phone-no'] : '';
    $firstAddress       = isset($_POST['first-address']) ? $_POST['first-address'] : '';
    $secondAddress      = isset($_POST['second-address']) ? $_POST['second-address'] : '';
    $postcode           = isset($_POST['postcode']) ? $_POST['postcode'] : '';
    $telegramId         = isset($_POST['telegram-id']) ? $_POST['telegram-id'] : '';
    $email              = isset($_POST['email']) ? $_POST['email'] : '';
    $username           = isset($_POST['username']) ? $_POST['username'] : '';
    $password           = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword    = isset($_POST['confirm-password']) ? $_POST['confirm-password'] : '';
    $created            = date('Y-m-d H:i:s', time());

    if ($password === $confirmPassword){
        // Hash the password for security
        $options = [
            'cost' => 14,
        ];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

    }


    // Prepare the query for update
    $query2 = "UPDATE sys_hr_employee SET first_name = :firstName, last_name = :lastName, postcode = :postcode, phone_no = :phoneNo, first_address = :firstAddress, second_address = :secondAddress, updated_at = :created WHERE id = :staffID ";
    $stmt2 = $conn->prepare($query2);

    // Bind the parameters
    $stmt2->bindParam(':firstName', $firstName);
    $stmt2->bindParam(':lastName', $lastName);
    $stmt2->bindParam(':postcode', $postcode);
    $stmt2->bindParam(':phoneNo', $phoneNo);
    $stmt2->bindParam(':firstAddress', $firstAddress);
    $stmt2->bindParam(':secondAddress', $secondAddress);
    $stmt2->bindParam(':created', $created);
    $stmt2->bindParam(':staffID', $staffID);

    // Execute the query
    $stmt2->execute();


    // Prepare the query for insert Telegram
    $query3 = "INSERT INTO sys_users (telegram_id, username, password, role_id, created_at, employee_id) VALUES(:telegramId, :username, :hashedPassword, :role, :created, :staffID) ";
    $stmt3 = $conn->prepare($query3);

    // Bind the parameters
    $stmt3->bindParam(':telegramId', $telegramId);
    $stmt3->bindParam(':username', $username);
    $stmt3->bindParam(':hashedPassword', $hashedPassword);
    $stmt3->bindParam(':role', $role);
    $stmt3->bindParam(':created', $created);
    $stmt3->bindParam(':staffID', $staffID);

    // Execute the query
    $stmt3->execute();

    //select task_assignment from ls_user_roles
    $stmtR = $conn->prepare("SELECT task_assignment FROM ls_user_roles WHERE id = :role ");

    $stmtR->bindParam(':role', $role);
    $stmtR->execute();
    $rowR = $stmtR->fetch(PDO::FETCH_ASSOC);

    // Prepare the query for insert sys_user_assignments
    $query4 = "INSERT INTO sys_user_assignments (username, role_id, status_id) VALUES(:username, :role, :statusID) ";
    $stmt4 = $conn->prepare($query4);

    // Bind the parameters
    $stmt4->bindParam(':username', $username);
    $stmt4->bindParam(':role', $role);
    $stmt4->bindParam(':statusID', $rowR['task_assignment']);

    // Execute the query
    $stmt4->execute();

    // Finally, return a JSON
    header('HTTP/2 200 OK');
    echo json_encode(
        array(
            "message"   => "success",
        )
    );

    // Close the database connection
    $conn = null;
}
