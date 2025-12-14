<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// import connection and api setup
require_once "config/system.php";
require_once "config/DBFactory.php";
include_once "api/header.php";
require_once "api/functions.php";

// Connect to the database using PDO
$db               = new DBConnectionFactory();
$telegram         = new Telegram();
$system           = new System;
// $flow             = new FlowStatuses($username);
// $changelog        = new Changelog($username);
$Curl             = new Curl();
$traffic          = new Traffic();
$taskAssignment   = new TaskAssignments();
$chronology       = new Chronology();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["action"])) {
        // check for init request
        if ($_GET["action"] == "init") {
            $systemId = $_GET['sid'];
            $reportId = $_GET['ref'];
            $authId = $_GET['auth'];

            try {
                // Create a new PDO instance
                $connPdo = $db->createConnection();

                // Set error mode to exceptions
                $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $connPdo->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $connPdo = null;

                // Process the retrieved value
                if ($referenceNo !== false) {
                    // Value exists, handle it accordingly
                    // echo "Reference No: " . $referenceNo;
                } else {
                    // No value found
                    echo "No reference number found.";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }

            if (isset($system->App->title)) {
                if ($system->App->title == 'UCIDOS') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } elseif ($system->App->title == 'KITER') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } elseif ($system->App->title == 'KUDRAT') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } else {
                    echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                    exit;
                }
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }

            try {
                // Create a new PDO instance
                $connPdo = $db->createConnection();

                // Set error mode to exceptions
                $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Declare the query
                $query = "SELECT *, ST_AsGeoJSON(geom) AS geometry FROM public.geom_site_visit WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND ST_GeometryType(geom) = 'ST_Polygon';";

                // Prepare the query
                $stmt = $connPdo->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':authId', $authId);

                // Execute the query
                $stmt->execute();

                // Fetch the data from the query result
                $markers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $connPdo = null;
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }

            // Finally, return a JSON
            echo json_encode(
                array(
                    "message" => "Success",
                    "status" => 200,
                    "systemID" => $systemId,
                    "reportID" => $reportId,
                    "authID" => $authId,
                    "polygon" => $markers
                )
            );
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $POST = json_decode($data, true);

    $systemId = $POST['sid'];
    $reportId = $POST['ref'];
    $authId = $POST['auth'];

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $connPdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $connPdo = null;

        // Process the retrieved value
        if ($referenceNo !== false) {
            // Value exists, handle it accordingly
            // echo "Reference No: " . $referenceNo;
        } else {
            // No value found
            echo "No reference number found.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    if (isset($system->App->title)) {
        if ($system->App->title == 'UCIDOS') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KITER') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KUDRAT') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }
    } else {
        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
        exit;
    }

    $id = isset($POST['id']) ? $POST['id'] : '';

    $addedDate = date('Y-m-d H:i:s', time());
    $user = $_SESSION['username'];
    $notes = $POST['notes'];
    $leafletID = $POST['polygonId'];
    $features = $POST['geom'];

    $coordinates = $features['geometry']['coordinates'][0];
    $points = [];
    foreach ($coordinates as $coord) {
        $points[] = "{$coord[0]} {$coord[1]}";
    }
    $polygon = "POLYGON((" . implode(",", $points) . "))";

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($POST['action'] == 'add') {
            $query = "INSERT INTO geom_site_visit (system_id, added_at, user_added, description, report_id, geom, geom_id, authority_id) VALUES
            (:systemId, :addedDate, :user, NULLIF(:notes, ''), :codedReport, ST_GeomFromText(:polygon, 4326), :leafletID, :authId) RETURNING id";
            $stmt = $connPdo->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':addedDate', $addedDate);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':polygon', $polygon);
            $stmt->bindParam(':leafletID', $leafletID);
            $stmt->bindParam(':authId', $authId);
            $stmt->execute();
            $id = $stmt->fetchColumn();

            // Get the current Unix timestamp in milliseconds
            $timestamp = date('Y-m-d H:i:s', time());

            // add id to flw_appl_reports
            $query = "UPDATE flw_appl_reports SET geom_site_visit_id = COALESCE(geom_site_visit_id, '{}'::integer[]) || ARRAY[:geomSvId]::integer[], updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo AND NOT (geom_site_visit_id @> ARRAY[:geomSvId]::integer[]);";
            $stmt = $connPdo->prepare($query);
            $stmt->bindParam(':geomSvId', $id, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportId, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();
        } else if ($POST['action'] == 'edit') {
            $query = "UPDATE geom_site_visit SET description = NULLIF(:notes, ''), geom = ST_GeomFromText(:polygon, 4326) WHERE report_id = :codedReport AND geom_id = :leafletID";
            $stmt = $connPdo->prepare($query);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':polygon', $polygon);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':leafletID', $leafletID);
            $stmt->execute();
        } else {
            $query = "DELETE FROM geom_site_visit WHERE geom_id = :leafletID AND report_id = :codedReport AND id = :id";
            $stmt = $connPdo->prepare($query);
            $stmt->bindParam(':leafletID', $leafletID);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Get the current Unix timestamp in milliseconds
            $timestamp = date('Y-m-d H:i:s', time());

            // Execute a SELECT query on the database
            $query = "UPDATE flw_appl_reports SET geom_site_visit_id = array_remove(geom_site_visit_id, :gid), updated_timestamp = :timestamp WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid RETURNING geom_site_visit_id";
            $stmt = $connPdo->prepare($query);
            $stmt->bindValue(':gid', $id, PDO::PARAM_INT);
            $stmt->bindValue(':sid', $systemId, PDO::PARAM_STR);
            $stmt->bindValue(':rn', $reportId, PDO::PARAM_STR);
            $stmt->bindValue(':aid', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();
            $geomSvId = $stmt->fetchColumn();
        }

        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "Success",
                "status" => 200,
                "data" => array(
                    "id" => $id,
                    "systemID" => $systemId,
                    "polygonID" => $leafletID,
                    "notes" => $notes
                )
            )
        );
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $PUT = json_decode($data, true);

    if (isset($PUT["action"])) {
        if ($PUT["action"] == 'update-geom-init') {
            $systemId = $PUT['sid'];
            $id = $PUT['id'];
            $reportId = $PUT['ref'];

            try {
                // Create a new PDO instance
                $connPdo = $db->createConnection();

                // Set error mode to exceptions
                $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $connPdo->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $connPdo = null;

                // Process the retrieved value
                if ($referenceNo !== false) {
                    // Value exists, handle it accordingly
                    // echo "Reference No: " . $referenceNo;
                } else {
                    // No value found
                    echo "No reference number found.";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }

            if (isset($system->App->title)) {
                if ($system->App->title == 'UCIDOS') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } elseif ($system->App->title == 'KITER') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } elseif ($system->App->title == 'KUDRAT') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } else {
                    echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                    exit;
                }
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }
            $authId = $PUT['auth'];
            $geomId = $PUT['geomId'];
            $newGeomId = $PUT['newGeomId'];

            try {
                // Create a new PDO instance
                $connPdo = $db->createConnection();

                // Set error mode to exceptions
                $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Declare the query
                $query = "UPDATE public.geom_site_visit SET geom_id = :newGeomId WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND id = :id AND geom_id = :geomId AND ST_GeometryType(geom) = 'ST_Polygon'";

                // Prepare the query
                $stmt = $connPdo->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':newGeomId', $newGeomId);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':authId', $authId);
                $stmt->bindParam(':geomId', $geomId);
                $stmt->bindParam(':id', $id);

                // Execute the query
                $stmt->execute();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $connPdo = null;
            } catch (PDOException $e) {
                // Handle the exception gracefully (e.g., log the error, display an error message)
                echo "Error: " . $e->getMessage();
            }

            // Finally, return a JSON
            echo json_encode(
                array(
                    "message" => "Success",
                    "status" => 200,
                    "systemID" => $systemId,
                    "reportID" => $reportId,
                    "authID" => $authId,
                    "geomID" => $geomId,
                    "newGeomID" => $newGeomId
                )
            );
        } else if ($PUT["action"] == 'edit') {

            $systemId = $PUT['sid'];
            $reportId = $PUT['ref'];
            $authId = $PUT['auth'];
            try {
                // Create a new PDO instance
                $connPdo = $db->createConnection();

                // Set error mode to exceptions
                $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $connPdo->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $connPdo = null;

                // Process the retrieved value
                if ($referenceNo !== false) {
                    // Value exists, handle it accordingly
                    // echo "Reference No: " . $referenceNo;
                } else {
                    // No value found
                    echo "No reference number found.";
                }
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }

            if (isset($system->App->title)) {
                if ($system->App->title == 'UCIDOS') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } elseif ($system->App->title == 'KITER') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } elseif ($system->App->title == 'KUDRAT') {
                    // generate codedReport
                    $explodedRef = explode("/", $referenceNo);
                    $exploded = explode("/", $reportId);
                    $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                    $codedReport = base64_encode($shortRef);
                } else {
                    echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                    exit;
                }
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }
            $id = isset($PUT['id']) ? $PUT['id'] : '';

            $addedDate = date('Y-m-d H:i:s', time());
            $user = $_SESSION['username'];
            $notes = $PUT['notes'];
            $leafletID = $PUT['polygonId'];
            $features = $PUT['geom'];

            $coordinates = $features['geometry']['coordinates'][0];
            $points = [];
            foreach ($coordinates as $coord) {
                $points[] = "{$coord[0]} {$coord[1]}";
            }
            $polygon = "POLYGON((" . implode(",", $points) . "))";

            // Create a new PDO instance
            $pdo = $db->createConnection();

            // Prepare and execute SQL queries using PDO with bindParam
            if ($PUT['action'] == 'add') {
                $query = "INSERT INTO geom_site_visit (system_id, added_at, user_added, description, report_id, geom, geom_id, authority_id) VALUES
                (:systemId, :addedDate, :user, NULLIF(:notes, ''), :codedReport, ST_GeomFromText(:polygon, 4326), :leafletID, :authId) RETURNING id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':addedDate', $addedDate);
                $stmt->bindParam(':user', $user);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':polygon', $polygon);
                $stmt->bindParam(':leafletID', $leafletID);
                $authIdInt = intval($authId);
                $stmt->bindParam(':authId', $authIdInt, PDO::PARAM_INT);
                $stmt->execute();
                $id = $stmt->fetchColumn();
            } else if ($PUT['action'] == 'edit') {
                $query = "UPDATE geom_site_visit SET description = NULLIF(:notes, ''), geom = ST_GeomFromText(:polygon, 4326) WHERE report_id = :codedReport AND geom_id = :leafletID";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':polygon', $polygon);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':leafletID', $leafletID);
                $stmt->execute();
            } else {
                $query = "DELETE FROM geom_site_visit WHERE geom_id = :leafletID AND report_id = :codedReport AND id = :id";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':leafletID', $leafletID);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            // Return a JSON response
            $response = [
                "message" => "Success",
                "status" => 200,
                "data" => [
                    "id" => $id,
                    "systemID" => $systemId,
                    "polygonID" => $leafletID,
                    "notes" => $notes
                ]
            ];
            echo json_encode($response);

            // Close the PDO connection
            $pdo = null;


        }
    } else {
        $reportId = $PUT['ref'];
        $systemId = $PUT['sid'];
        $authId = $PUT['auth'];
        $leafletID = $PUT['polygonId'];
        $id = isset($PUT['id']) ? $PUT['id'] : '';

        try {
            // Create a new PDO instance
            $connPdo = $db->createConnection();

            // Set error mode to exceptions
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

            // Prepare the query
            $stmt = $connPdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);

            // Execute the query
            $stmt->execute();

            // Fetch the single value from the query result
            $referenceNo = $stmt->fetchColumn();

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $connPdo = null;

            // Process the retrieved value
            if ($referenceNo !== false) {
                // Value exists, handle it accordingly
                // echo "Reference No: " . $referenceNo;
            } else {
                // No value found
                echo "No reference number found.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        if (isset($system->App->title)) {
            if ($system->App->title == 'UCIDOS') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } elseif ($system->App->title == 'KITER') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } elseif ($system->App->title == 'KUDRAT') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }

        $features = $PUT['geom'];

        $coordinates = $features['geometry']['coordinates'][0];
        $points = [];
        foreach ($coordinates as $coord) {
            $points[] = "{$coord[0]} {$coord[1]}";
        }
        $polygon = "POLYGON((" . implode(",", $points) . "))";


        // Connect to the database using PDO
        $pdo = $db->createConnection();

        // Set the added date
        $addedDate = date('Y-m-d H:i:s', time());

        // Update the record
        $query = "UPDATE geom_site_visit SET geom = ST_GeomFromText(:polygon, 4326), added_at = :addedDate WHERE geom_id = :leafletID AND (report_id = :codedReport AND id = :id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':polygon', $polygon);
        $stmt->bindParam(':addedDate', $addedDate);
        $stmt->bindParam(':leafletID', $leafletID);
        $stmt->bindParam(':codedReport', $codedReport);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Fetch the updated record
        $query = "SELECT description, id FROM geom_site_visit WHERE geom_id = :leafletID AND report_id = :codedReport AND id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':leafletID', $leafletID);
        $stmt->bindParam(':codedReport', $codedReport);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $notes = $row['description'];

        // Return a JSON response
        $response = [
            "message" => "Success",
            "status" => 200,
            "data" => [
                "id" => $row['id'],
                "systemID" => $systemId,
                "polygonID" => $leafletID,
                "notes" => $notes
            ]
        ];
        echo json_encode($response);

        // Close the PDO connection
        $pdo = null;

    }
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $reportId = $_GET['ref'];
    $systemId = $_GET['sid'];
    $authId = $_GET['auth'];
    $leafletID = $_GET['polygonId'];
    $id = $_GET['id'];

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $connPdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $connPdo = null;

        // Process the retrieved value
        if ($referenceNo !== false) {
            // Value exists, handle it accordingly
            // echo "Reference No: " . $referenceNo;
        } else {
            // No value found
            echo "No reference number found.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    if (isset($system->App->title)) {
        if ($system->App->title == 'UCIDOS') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KITER') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KUDRAT') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }
    } else {
        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
        exit;
    }

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "DELETE FROM geom_site_visit WHERE geom_id = :leafletID AND (report_id = :codedReport AND id = :id)";
        $stmt = $connPdo->prepare($query);
        $stmt->bindParam(':leafletID', $leafletID);
        $stmt->bindParam(':codedReport', $codedReport);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Get the current Unix timestamp in milliseconds
        $timestamp = date('Y-m-d H:i:s', time());

        // Execute a SELECT query on the database
        $query = "UPDATE flw_appl_reports SET geom_site_visit_id = array_remove(geom_site_visit_id, :gid), updated_timestamp = :timestamp WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid RETURNING geom_site_visit_id";
        $stmt = $connPdo->prepare($query);
        $stmt->bindValue(':gid', $id, PDO::PARAM_INT);
        $stmt->bindValue(':sid', $systemId, PDO::PARAM_STR);
        $stmt->bindValue(':rn', $reportId, PDO::PARAM_STR);
        $stmt->bindValue(':aid', $authId, PDO::PARAM_INT);
        $stmt->bindParam(':timestamp', $timestamp);
        $stmt->execute();
        $geomSvId = $stmt->fetchColumn();

        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "Success",
                "status" => 200,
            )
        );
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
}
// devmode
exit;

if (isset($_POST["item"])) {
    // TODO: api for init
    // check for init request
    if ($_POST["item"] == "init") {
        $systemId = $_POST['systemId'];
        $reportId = $_POST['reportId'];
        $authId = $_POST['authId'];

        try {
            // Create a new PDO instance
            $connPdo = $db->createConnection();

            // Set error mode to exceptions
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

            // Prepare the query
            $stmt = $connPdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);

            // Execute the query
            $stmt->execute();

            // Fetch the single value from the query result
            $referenceNo = $stmt->fetchColumn();

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $connPdo = null;

            // Process the retrieved value
            if ($referenceNo !== false) {
                // Value exists, handle it accordingly
                // echo "Reference No: " . $referenceNo;
            } else {
                // No value found
                echo "No reference number found.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        if (isset($system->App->title)) {
            if ($system->App->title == 'UCIDOS') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } elseif ($system->App->title == 'KITER') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } elseif ($system->App->title == 'KUDRAT') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }

        try {
            // Create a new PDO instance
            $connPdo = $db->createConnection();

            // Set error mode to exceptions
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "SELECT *, ST_AsGeoJSON(geom) AS geometry FROM public.geom_site_visit WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND ST_GeometryType(geom) = 'ST_Polygon';";

            // Prepare the query
            $stmt = $connPdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':authId', $authId);

            // Execute the query
            $stmt->execute();

            // Fetch the data from the query result
            $markers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $connPdo = null;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "Success",
                "status" => 200,
                "systemID" => $systemId,
                "reportID" => $reportId,
                "authID" => $authId,
                "polygon" => $markers
            )
        );
    } else if ($_POST["item"] == "update-geom") {
        $systemId = $_POST['systemId'];
        $id = $_POST['id'];
        $reportId = $_POST['reportId'];

        try {
            // Create a new PDO instance
            $connPdo = $db->createConnection();

            // Set error mode to exceptions
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

            // Prepare the query
            $stmt = $connPdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);

            // Execute the query
            $stmt->execute();

            // Fetch the single value from the query result
            $referenceNo = $stmt->fetchColumn();

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $connPdo = null;

            // Process the retrieved value
            if ($referenceNo !== false) {
                // Value exists, handle it accordingly
                // echo "Reference No: " . $referenceNo;
            } else {
                // No value found
                echo "No reference number found.";
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }

        if (isset($system->App->title)) {
            if ($system->App->title == 'UCIDOS') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } elseif ($system->App->title == 'KITER') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } elseif ($system->App->title == 'KUDRAT') {
                // generate codedReport
                $explodedRef = explode("/", $referenceNo);
                $exploded = explode("/", $reportId);
                $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
                $codedReport = base64_encode($shortRef);
            } else {
                echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
                exit;
            }
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }
        $authId = $_POST['authId'];
        $geomId = $_POST['geomId'];
        $newGeomId = $_POST['newGeomId'];

        try {
            // Create a new PDO instance
            $connPdo = $db->createConnection();

            // Set error mode to exceptions
            $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Declare the query
            $query = "UPDATE public.geom_site_visit SET geom_id = :newGeomId WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND id = :id AND geom_id = :geomId AND ST_GeometryType(geom) = 'ST_Polygon'";

            // Prepare the query
            $stmt = $connPdo->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':newGeomId', $newGeomId);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':authId', $authId);
            $stmt->bindParam(':geomId', $geomId);
            $stmt->bindParam(':id', $id);

            // Execute the query
            $stmt->execute();

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $connPdo = null;
        } catch (PDOException $e) {
            // Handle the exception gracefully (e.g., log the error, display an error message)
            echo "Error: " . $e->getMessage();
        }

        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "Success",
                "status" => 200,
                "systemID" => $systemId,
                "reportID" => $reportId,
                "authID" => $authId,
                "geomID" => $geomId,
                "newGeomID" => $newGeomId
            )
        );
    }
}
// TODO: api for create
else if (isset($_POST['notes']) && isset($_POST['polygonId'])) {

    $systemId = $_POST['systemId'];
    $reportId = $_POST['reportId'];
    $authId = $_POST['authId'];

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $connPdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $connPdo = null;

        // Process the retrieved value
        if ($referenceNo !== false) {
            // Value exists, handle it accordingly
            // echo "Reference No: " . $referenceNo;
        } else {
            // No value found
            echo "No reference number found.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    if (isset($system->App->title)) {
        if ($system->App->title == 'UCIDOS') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KITER') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KUDRAT') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }
    } else {
        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
        exit;
    }
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    $addedDate = date('Y-m-d H:i:s', time());
    $user = $_SESSION['username'];
    $notes = $_POST['notes'];
    $leafletID = $_POST['polygonId'];
    $features = $_POST['geom'];

    $coordinates = $features['geometry']['coordinates'][0];
    $points = [];
    foreach ($coordinates as $coord) {
        $points[] = "{$coord[0]} {$coord[1]}";
    }
    $polygon = "POLYGON((" . implode(",", $points) . "))";

    // Connect to the database
    $conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpwd");
    // Check for errors in the connection
    if (!$conn) {
        die("Error in connection: " . pg_last_error());
    }

    if ($_POST['action'] == 'add') {

        $query = "INSERT INTO geom_site_visit (system_id, added_at, user_added, description, report_id, geom, geom_id, authority_id) VALUES
    ('$systemId', '$addedDate', '$user', NULLIF('$notes', ''), '$codedReport', (ST_GeomFromText('$polygon', 4326)), $leafletID, " . intval($authId) . ") RETURNING id";
        $result = pg_query($conn, $query);
        $id = pg_fetch_result($result, 0, 'id');
    } else if ($_POST['action'] == 'edit') {

        $query = "UPDATE geom_site_visit SET description = NULLIF('$notes', ''), geom = (ST_GeomFromText('$polygon', 4326))
    WHERE report_id = '$codedReport' AND geom_id = $leafletID ";
        $result = pg_query($conn, $query);
    } else {

        $query = "DELETE FROM geom_site_visit WHERE geom_id = $leafletID AND report_id = '$codedReport' AND id=$id";
        $result = pg_query($conn, $query);
    }



    // Finally, return a JSON
    echo json_encode(
        array(
            "message" => "Success",
            "status" => 200,
            "data" => array(
                "id" => $id,
                "systemID" => $systemId,
                "polygonID" => $leafletID,
                "notes" => $notes
            )
        )
    );

    pg_close($conn);
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $_PUT = json_decode($data, true);

    $reportId = $_PUT['reportId'];
    $systemId = $_PUT['systemId'];
    $authId = $_PUT['authId'];
    $leafletID = $_PUT['polygonId'];
    $id = isset($_PUT['id']) ? $_PUT['id'] : '';

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $connPdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $connPdo = null;

        // Process the retrieved value
        if ($referenceNo !== false) {
            // Value exists, handle it accordingly
            // echo "Reference No: " . $referenceNo;
        } else {
            // No value found
            echo "No reference number found.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    if (isset($system->App->title)) {
        if ($system->App->title == 'UCIDOS') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KITER') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KUDRAT') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }
    } else {
        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
        exit;
    }

    $features = $_PUT['geom'];

    $coordinates = $features['geometry']['coordinates'][0];
    $points = [];
    foreach ($coordinates as $coord) {
        $points[] = "{$coord[0]} {$coord[1]}";
    }
    $polygon = "POLYGON((" . implode(",", $points) . "))";

    // Connect to the database
    $conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpwd");
    // Check for errors in the connection
    if (!$conn) {
        die("Error in connection: " . pg_last_error());
    }

    $addedDate = date('Y-m-d H:i:s', time());

    $query = "UPDATE geom_site_visit SET geom = (ST_GeomFromText('$polygon', 4326)) , added_at = '$addedDate'
WHERE geom_id = $leafletID AND (report_id = '$codedReport'  AND id=$id)";
    $result = pg_query($conn, $query);

    $query = "SELECT description, id FROM geom_site_visit WHERE geom_id = $leafletID AND report_id = '$codedReport' AND id=$id";
    $result = pg_query($conn, $query);

    $row = pg_fetch_assoc($result);

    $notes = $row['description'];
    // Finally, return a JSON
    echo json_encode(
        array(
            "message" => "Success",
            "status" => 200,
            "data" => array(
                "id" => $row['id'],
                "systemID" => $systemId,
                "polygonID" => $leafletID,
                "notes" => $notes
            )
        )
    );

    pg_close($conn);
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $_DELETE = json_decode($data, true);

    $reportId = $_DELETE['reportId'];
    $systemId = $_DELETE['systemId'];
    $authId = $_DELETE['authId'];
    $leafletID = $_DELETE['polygonId'];
    $id = $_DELETE['id'];

    try {
        // Create a new PDO instance
        $connPdo = $db->createConnection();

        // Set error mode to exceptions
        $connPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $connPdo->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $connPdo = null;

        // Process the retrieved value
        if ($referenceNo !== false) {
            // Value exists, handle it accordingly
            // echo "Reference No: " . $referenceNo;
        } else {
            // No value found
            echo "No reference number found.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    if (isset($system->App->title)) {
        if ($system->App->title == 'UCIDOS') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KITER') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } elseif ($system->App->title == 'KUDRAT') {
            // generate codedReport
            $explodedRef = explode("/", $referenceNo);
            $exploded = explode("/", $reportId);
            $shortRef = $explodedRef[4] . "-" . $explodedRef[5] . "-" . $exploded[1] . "(" . $exploded[2] . ")";
            $codedReport = base64_encode($shortRef);
        } else {
            echo json_encode(array('message' => 'error', 'reason' => '$system->App->title value is wrong, check your _tenant.php setup! or you might already change the $system->App->title value after including _tenant.php', 'at' => __FILE__ . ' on line ' . __LINE__));
            exit;
        }
    } else {
        echo json_encode(array('message' => 'error', 'reason' => '$system->App->title is not declared, check your _tenant.php include!', 'at' => __FILE__ . ' on line ' . __LINE__));
        exit;
    }

    // Connect to the database
    $conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpwd");
    // Check for errors in the connection
    if (!$conn) {
        die("Error in connection: " . pg_last_error());
    }

    $query = "DELETE FROM geom_site_visit WHERE geom_id = $leafletID AND (report_id = '$codedReport' AND id = $id) ";
    $result = pg_query($conn, $query);

    // Finally, return a JSON
    echo json_encode(
        array(
            "message" => "Success",
            "status" => 200,
        )
    );
} else {
}
