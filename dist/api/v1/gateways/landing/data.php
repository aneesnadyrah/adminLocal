<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/system.php";
require "config/DBFactory.php";
include "api/functions.php";


header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $json = file_get_contents("php://input");
    $data = json_decode($json);
    $conn = General::connectToDatabase();

    if (Utilities::checkDomainToken($data->secret, $_SERVER['HTTP_ORIGIN'])) {

        // // Include the database connection parameters
        // global $PDO;



        $query = "SELECT application_length FROM flw_appl_entries";
        $stmt = $conn->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch the result as an associative array
        $result = $stmt->fetchAll();

        // Initialize a variable to hold the total length
        $totalLength = 0;

        // Iterate through the result array and sum up the application lengths
        foreach ($result as $row) {
            // Add the application length to the total
            $totalLength += $row['application_length'];
        }

        // Example with total length
        $totalLengthFormatted = number_format($totalLength);
        // Output the total length
        // echo "Total Application Length: " . $totalLength;

        //////////////////

        $query2 = "SELECT COUNT(id) AS wayleave_count FROM flw_wayleave";
        $stmt2 = $conn->prepare($query2);

        // Execute the query
        $stmt2->execute();

        // Fetch the result as an associative array
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Get the count of IDs
        $wayleaveCount = $result2['wayleave_count'];

        // Example with wayleave count
        $wayleaveCountFormatted = number_format($wayleaveCount);

        // Output the count of IDs
        // echo "Total IDs: " . $wayleaveCount;

        //////////////////

        $query3 = "SELECT COUNT(id) AS permit_count FROM flw_work_permit";
        $stmt3 = $conn->prepare($query3);

        // Execute the query
        $stmt3->execute();

        // Fetch the result as an associative array
        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

        // Get the count of IDs
        $permitCount = $result3['permit_count'];

        // Example with permit count
        $permitCountFormatted = number_format($permitCount);

        // Output the count of IDs
        // echo "Total Work Permit IDs: " . $permitCount;

        ////////////////////////////

        $query4 = "SELECT system_id, MAX(current_progress) AS latest_survey_distance 
            FROM flw_survey_reports_udm 
            GROUP BY system_id";
        $stmt4 = $conn->prepare($query4);

        // Execute the query
        $stmt4->execute();

        // Fetch the results as an associative array
        $results4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);

        // Initialize total survey distance
        $totalSurveyDistance = 0;

        // Iterate through results to calculate total survey distance
        foreach ($results4 as $row) {
            $totalSurveyDistance += $row['latest_survey_distance'];
        }

        // Example with permit count
        $surveyDistanceFormatted = number_format($totalSurveyDistance);

        // Output the total survey distance
        // echo "Total Survey Distance: " . $totalSurveyDistance;

        // Close the database connection
        // $conn = null;

        ////////////////////////////////////////

        http_response_code(200);
        $result = array(
            "success" => true,
            "message" => "success",
            "data" => [
                "izinlalu" => $wayleaveCountFormatted,
                "permit" => $permitCountFormatted,
                "permohonan" => $totalLengthFormatted,
                "ukur" => $surveyDistanceFormatted,
                // "secret" => $data->secret,
                // "server" => $_SERVER['HTTP_ORIGIN'],
                // "check" => Utilities::checkDomainToken($data->secret, $_SERVER['HTTP_ORIGIN']),
            ]
        );

    } else {
        // The token or domain is invalid or the token has expired
        http_response_code(400);
        $result = array(
            "success" => false,
            "message" => "Invalid or expired API token",
        );


    }

    echo json_encode($result);
    $conn = null;
}