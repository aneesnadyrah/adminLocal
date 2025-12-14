 <?php

set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "api/functions.php";
 require "config/DBFactory.php";
 $System = new System;
$tenant = $System->App->tenant;

$app = $appsTitle;

// Connect to the database using PDO
 $db = new DBConnectionFactory();
 $conn = $db->createConnection();

// Execute a SELECT query on the database
 $query = $conn->prepare("SELECT 
            authority,
            system_id,
            quotation_verify,
            \"wy_approval_date\",
            \"wy_fb_date\",
            \"payment\",
            id AS \"ID\",
            \"status_id\",
            \"flow_name\",
            \"project_status\",
            \"status_color\",
            \"status_icon\",
            reference_no,
            application_length,
            \"sv_completed\",
            \"submit_date\",
            \"provider_name\",
            \"provider_id\",
            \"district\"
            FROM public.view_survey_priority WHERE \"status_id\" BETWEEN 1 AND 38");

// Execute the query
$query->execute();

// Create an array to hold the query result
$data = array();
$uniqueSystemIds = array(); // To track unique system_ids

// Fetch the rows from the query result as an associative array
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $systemId = $row['system_id'];

    // Check if system_id is already processed
    if (isset($uniqueSystemIds[$systemId])) {
        // Check if the current row has a later dt_appv_ltr/wy_approval_date
        if ($row['wy_approval_date'] > $uniqueSystemIds[$systemId]['wy_approval_date']) {
            // Update the row with the later dt_appv_ltr/wy_approval_date
            $uniqueSystemIds[$systemId] = $row;
        }
    } else {
        // Add the row for a new system_id
        $uniqueSystemIds[$systemId] = $row;
    }
}

// Determine the priority for each unique row
foreach ($uniqueSystemIds as $row) {
     //site visit completed
     $svComleted = $row['sv_completed'];
     //sebut harga
     $quoteApprove = $row['quotation_verify'];
     //kelulusan izin lalu
     $wyApproval = $row['wy_approval_date'];
     //mbkil
     $wyFeedbackDate = $row['wy_fb_date'];
     //bayaran (invois)
     $payment = $row['payment'];

     // Determine the priority based on the conditions
     $priority = 0;
     if ($svComleted === true) {
         $priority = 5;
         if ($quoteApprove === true) {
             $priority = 4;
             if ($wyApproval !== null) {
                 $priority = 3;
                 if ($wyFeedbackDate !== null) {
                     $priority = 2;
                 }
                 if ($payment !== null) {
                     $priority = 1;
                 }
             }
         }
     }
     
    // Add the priority to the row
    $row['priority'] = $priority;

    if ($row['StatusID'] < 10) {
            $row['StatusID'] = '00' . $row['StatusID'];
        } else if ($row['StatusID'] < 100) {
            $row['StatusID'] = '0' . $row['StatusID'];
        }

        if ($row['MappingID'] < 10) {
            $row['MappingID'] = '00' . $row['MappingID'];
        } else if ($row['MappingID'] < 100) {
            $row['MappingID'] = '0' . $row['MappingID'];
        }

        if ($row['SubMappingID'] < 10) {
            $row['SubMappingID'] = '00' . $row['SubMappingID'];
        } else if ($row['SubMappingID'] < 100) {
            $row['SubMappingID'] = '0' . $row['SubMappingID'];
        }

    // Add the row to the data array
    $data[] = $row;
}

// Create an associative array with the 'data' key and the JSON-encoded data
$response = array('data' => $data);

// Convert the response to a JSON string
$json = json_encode($response);

// Set the Content-Type header to indicate JSON response
// header('Content-Type: application/json');

// Return the JSON string to the client
echo $json;

// Close the database connection
$conn = null;
