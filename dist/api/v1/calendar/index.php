<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// import connection and api setup
require_once "api/header.php";
require_once "api/functions.php";
require_once "config/system.php";
require_once "config/DBFactory.php";

// Initiate connection instance
$system = new System;
$db = new DBConnectionFactory();
$conn = $db->createConnection();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET["item"])) {

        if ($_GET["item"] == "event") {

            $dataEvent = Calendar::fetchCalendarEvent();
            $json = json_encode($dataEvent);
            echo $json;

        } else if ($_GET["item"] == "reference") {
            $systemId = $_GET["sid"];

            // Prepare the projectRefNo fetch statement
            $query = "SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId";
            $statement = $conn->prepare($query);
            $statement->bindParam(':systemId', $systemId);

            // Execute the projectRefNo fetch statement
            if (!$statement->execute()) {
                echo "An error occurred.\n";
                exit;
            }

            // Fetch the row from the query result as an associative array
            $dataProjectRefNo = $statement->fetch(PDO::FETCH_ASSOC);

            // Check if $dataProjectRefNo is declared
            if (!isset($dataProjectRefNo)) {
                $dataProjectRefNo = '';
            }

            // Convert the result to a JSON string
            $json = json_encode($dataProjectRefNo);

            // Return the JSON string to the client
            echo $json;

        } else if ($_GET["item"] == "suggestion") {

            $systemId = $_GET["sid"];

            // Prepare the SELECT query to get authority IDs
            $query1 = "SELECT authority FROM geom_gis_trace WHERE system_id = :systemId AND authority IS NOT NULL GROUP BY authority";
            $stmt1 = $conn->prepare($query1);
            $stmt1->bindParam(':systemId', $systemId);

            // Execute the first query
            if (!$stmt1->execute()) {
                echo "An error occurred.\n";
                exit;
            }

            // Fetch the row from the query result as an associative array
            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
            // var_dump($row);exit;
            if (isset($row['authority'])) {
                $authID = $row['authority'];
            } else {
                $authID = [];
            }

            // Prepare the SELECT query to get authority data
            $query2 = "SELECT * FROM ls_authorities WHERE id IN (" . str_replace(array('{', '}'), '', $authID) . ")";
            $stmt2 = $conn->prepare($query2);

            // Execute the second query
            if (!$stmt2->execute()) {
                echo "An error occurred.\n";
                exit;
            }

            // Fetch the rows from the query result as an associative array
            $authorityData = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Prepare the SELECT query to get assigned authorities
            $query3 = "SELECT id as calendar_id, authority_id FROM flw_calendars WHERE system_id = :systemId AND authority_id IN (" . str_replace(array('{', '}'), '', $authID) . ")";
            $stmt3 = $conn->prepare($query3);
            $stmt3->bindParam(':systemId', $systemId);

            // Execute the third query
            if (!$stmt3->execute()) {
                echo "An error occurred.\n";
                exit;
            }

            // Fetch the rows from the query result as an associative array
            $assignedAuthority = $stmt3->fetchAll(PDO::FETCH_ASSOC);


            /* Checking if the authorityData has assigned value inside assignedAuthority. */
            Calendar::CheckAndAssign($authorityData, $assignedAuthority, 'id', 'authority_id', 'calendar_id', 'calendar_id', null);

            // Convert the result to a JSON string
            $json = json_encode(
                array(
                    "authoritySuggest" => $authorityData
                )
            );

            // Return the JSON string to the client
            echo $json;
        }
    } else {

        // Prepare the SELECT query to get the calendar category
        $query1 = "SELECT id, category_name, color_class FROM ls_calendar_categories ORDER BY id ASC";
        $stmt1 = $conn->prepare($query1);

        // Execute the first query
        if (!$stmt1->execute()) {
            echo "An error occurred.\n";
            exit;
        }

        // Fetch the rows from the query result as an associative array
        $dataCategory = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        // Prepare the SELECT query to get the user list
        $query2 = "SELECT id, username, role_id FROM sys_users ORDER BY role_id ASC";
        $stmt2 = $conn->prepare($query2);

        // Execute the second query
        if (!$stmt2->execute()) {
            echo "An error occurred.\n";
            exit;
        }

        // Fetch the rows from the query result as an associative array
        $dataUser = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Execute event fetch function which will run the query
        $dataEvent = Calendar::fetchCalendarEvent();

        // Prepare the SELECT query to get the authority list
        $query3 = "SELECT * FROM ls_authorities WHERE state_code = :state GROUP BY id,sort_name, district_code ORDER BY sort_name ASC";
        $stmt3 = $conn->prepare($query3);
        $stmt3->bindParam(':state', $system->App->state);

        // Execute the third query
        if (!$stmt3->execute()) {
            echo "An error occurred.\n";
            exit;
        }

        // Fetch the rows from the query result as an associative array
        $dataAuthority = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        // Convert the result to a JSON string
        $json = '{"calendar_category":' . json_encode($dataCategory) . ',"calendar_authority":' . json_encode($dataAuthority) . ',"calendar_user":' . json_encode($dataUser) . ',"calendar_event":' . json_encode($dataEvent);
        if (isset($_SESSION)) {
            $json .= ',"current_user":"' . $_SESSION['username'] . '"}';
        } else {
            $json .= '}';
        }

        // Return the JSON string to the client
        echo $json;
    }

    // Close the database connection
    $conn = null;
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get payload data
    $payloadData = file_get_contents("php://input");
    $POST = json_decode($payloadData, true);
    // var_dump($POST);exit;

    // Get the current timestamp
    $currentTimestamp = date('Y-m-d H:i:s');

    if (isset($POST['report_type'])) {
        $systemId = $_GET["item"];

        // Extract the values from the JSON data
        $title = $POST['title'];
        $group_id = $POST['groupId'];
        $start = $POST['start'];
        $end = $POST['end'];
        $description = $POST['description'];
        $location = $POST['location'];
        $all_day = $POST['allDay'];
        $guests = $POST['guests'];
        $creator = $POST['creator'];
        $authorityId = $POST['authority_id'];
        $reportType = $POST['report_type'];

        // Build the SQL query to insert the data into the table
        $sql = "INSERT INTO public.flw_calendars (title, group_id, start, \"end\", description, location, all_day, guests, creator, system_id, authority_id, report_type, created_at) VALUES (:title, :group_id, :start, :end, :description, :location, :all_day, :guests, :creator, :systemId, :authorityId, :reportType, :currentTimestamp)";

        if ($all_day == 1 || $all_day == "true") {
            $all_day = "true";
        } else {
            $all_day = "false";
        }

        $result = $conn->prepare($sql);

        // bind the parameter to the placeholder using the bindValue method
        $result->bindParam(':title', $title);
        $result->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $result->bindParam(':start', $start);
        $result->bindParam(':end', $end);
        $result->bindParam(':description', $description);
        $result->bindParam(':location', $location);
        $result->bindParam(':all_day', $all_day);
        $result->bindParam(':guests', $guests);
        $result->bindParam(':creator', $creator);
        $result->bindParam(':systemId', $systemId);
        $result->bindParam(':currentTimestamp', $currentTimestamp);
        $result->bindParam(':authorityId', $authorityId, PDO::PARAM_INT);
        $result->bindParam(':reportType', $reportType, PDO::PARAM_INT);

        // Execute the query
        $result->execute();

        // Close the database connection
        $conn = null;
    } else {

        // Extract the values from the JSON data
        $title = $POST['title'];
        $group_id = $POST['groupId'];
        $start = $POST['start'];
        $end = $POST['end'];
        $description = $POST['description'];
        $location = $POST['location'];
        $all_day = $POST['allDay'];
        $guests = $POST['guests'];
        $creator = $POST['creator'];

        // Build the SQL query to insert the data into the table
        $sql = "INSERT INTO public.flw_calendars (title, group_id, start, \"end\", description, location, all_day, guests, creator, created_at) VALUES (:title, :group_id, :start, :end, :description, :location, :all_day, :guests, :creator, :currentTimestamp)";

        if ($all_day == 1 || $all_day == "true") {
            $all_day = "true";
        } else {
            $all_day = "false";
        }

        $result = $conn->prepare($sql);

        // bind the parameter to the placeholder using the bindValue method
        $result->bindParam(':title', $title);
        $result->bindParam(':group_id', $group_id, PDO::PARAM_INT);
        $result->bindParam(':start', $start);
        $result->bindParam(':end', $end);
        $result->bindParam(':description', $description);
        $result->bindParam(':location', $location);
        $result->bindParam(':all_day', $all_day);
        $result->bindParam(':guests', $guests);
        $result->bindParam(':creator', $creator);
        $result->bindParam(':currentTimestamp', $currentTimestamp);

        // Execute the query
        $result->execute();

        // Close the database connection
        $conn = null;
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // retrieve the raw HTTP request body
    $payloadData = file_get_contents('php://input');

    // decode the request body as JSON
    $_PUT = json_decode($payloadData, true);
    // var_dump($_PUT);exit;

    // Extract the values from the JSON data
    $id = $_PUT['id'];
    $title = $_PUT['title'];
    $group_id = $_PUT['groupId'];
    $start = $_PUT['start'];
    $end = $_PUT['end'];
    $description = $_PUT['description'];
    $location = $_PUT['location'];
    $all_day = $_PUT['allDay'];
    $guests = $_PUT['guests'];
    $creator = $_PUT['creator'];

    // Prepare the SQL statement
    $sql = "UPDATE flw_calendars SET title=:title, group_id=:group_id, start=:start, \"end\"=:end, description=:description, location=:location, guests=:guests, creator=:creator, all_day=:all_day WHERE id=:id";
    $stmt = $conn->prepare($sql);

    // Bind the values to the named placeholders
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
    $stmt->bindParam(':start', $start);
    $stmt->bindParam(':end', $end);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':guests', $guests);
    $stmt->bindParam(':creator', $creator);
    $stmt->bindParam(':all_day', $all_day, PDO::PARAM_BOOL);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execute the SQL statement
    try {
        $stmt->execute();
        $num_rows = $stmt->rowCount();
        if ($num_rows > 0) {
            // Return a success response to the client.
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Event updated successfully.']);
        } else {
            // Return an error response to the client.
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to update event.']);
        }
    } catch (PDOException $e) {
        // Handle the exception and return an error response to the client.
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Failed to update event: ' . $e->getMessage()]);
    }

    $conn = null;

} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    if (isset($_GET['item'])) {
        // Extract the value of the ID to be deleted
        $id = $_GET['item'];

        // Prepare the SQL statement with a named placeholder
        $sql = "DELETE FROM flw_calendars WHERE id=:id";
        $stmt = $conn->prepare($sql);

        // Bind the ID value to the named placeholder
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the SQL statement
        try {
            $stmt->execute();
            $num_rows = $stmt->rowCount();
            if ($num_rows > 0) {
                // Return a success response to the client.
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully.']);
            } else {
                // Return an error response to the client.
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete event.']);
            }
        } catch (PDOException $e) {
            // Handle the exception and return an error response to the client.
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete event: ' . $e->getMessage()]);
        }

        // Close the database connection
        $conn = null;
    } else {
        // action if parameter send is invalid | no control parameter passed
        http_response_code(400);
        die('Deleting entry is a sensitive action!!! Please provide the Mandatory parameter and value declaring you want to DELETE the data entry');
    }
}
