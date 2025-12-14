<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    };

    // import connection and api setup
    require "api/header.php";
    require "config/system.php";

    // Connect to the database using PDO
    try {
        $conn = new PDO($PDO);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Error in connection: " . $e->getMessage());
    }

    // Prepare query on the database
    if( $_SESSION['roleId'] == 11 ) {
        $stmt = $conn->prepare("SELECT *,INITCAP (sys_hr_employee.first_name) AS minute_to, gen_letters.id AS letter_id FROM gen_letters LEFT JOIN sys_users ON gen_letters.receiver=sys_users.username LEFT JOIN sys_hr_employee ON sys_users.employee_id=sys_hr_employee.id ");
    } else {
        $stmt = $conn->prepare("SELECT *,INITCAP (sys_hr_employee.first_name) AS minute_to, gen_letters.id AS letter_id FROM gen_letters LEFT JOIN sys_users ON gen_letters.receiver=sys_users.username LEFT JOIN sys_hr_employee ON sys_users.employee_id=sys_hr_employee.id WHERE gen_letters.receiver= :username ");

        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':username', $username);
    }

    // create an array to hold the query result
    $data = array();

    // Execute the query
    $stmt->execute();

    while ($row = $stmt->fetch()) {
        // echo $row['name']."<br />\n";
        $data[] = $row;
    }

    // convert the result to a JSON string
    $json = '{"data":' . json_encode($data) . '}';

    // return the JSON string to the client
    echo $json;

    // Close the database connection
    $conn = null;


