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
                // Connect to the database using PDO
                $conn = $db->createConnection();

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $conn = null;

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
                }  elseif ($system->App->title == 'KUDRAT') {
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
                // Connect to the database using PDO
                $conn = $db->createConnection();

                // Declare the query
                $query = "SELECT *, ST_X(geom) AS longitude, ST_Y(geom) AS latitude FROM public.geom_site_visit WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND ST_GeometryType(geom) = 'ST_Point'";

                // Prepare the query
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);
                $stmt->bindParam(':codedReport', $codedReport);
                $stmt->bindParam(':authId', $authId);

                // Execute the query
                $stmt->execute();

                // Fetch the data from the query result
                $markers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $conn = null;
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
                    "markers" => $markers
                )
            );
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {// Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $POST = json_decode($data, true);

    if (isset($POST["action"])) {
        if ($POST["action"] == 'add') {

            $systemId = $POST['systemId'];
            $reportId = $POST['reportId'];
            $authId = $POST['authId'];

            try {
                // Connect to the database using PDO
                $conn = $db->createConnection();

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $conn = null;

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
                if ($system->App->title == 'UCIDOS' || $system->App->title == 'KITER' || $system->App->title == 'KUDRAT') {
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

            // Connect to the database using PDO
            $conn = $db->createConnection();

            $addedDate = date('Y-m-d H:i:s', time());
            $user = $_SESSION['username'];
            $desc = $POST['description'];
            $leafletID = $POST['markerId'];
            $lat = $POST['latitude'];
            $long = $POST['longitude'];

            $query = "INSERT INTO geom_site_visit (system_id, added_at, user_added, description, report_id, geom, geom_id, authority_id) VALUES
            (:systemId, :addedDate, :user, NULLIF(:desc, ''), :codedReport,  ST_SetSRID(ST_MakePoint(:long, :lat), 4326), :leafletID, :authId) RETURNING id";

            // Prepare the query
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':addedDate', $addedDate);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':desc', $desc);
            $stmt->bindParam(':codedReport', $codedReport);
            $stmt->bindParam(':long', $long);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':leafletID', $leafletID);
            $stmt->bindParam(':authId', $authId);

            $stmt->execute();
            $id = $stmt->fetchColumn();

            // Get the current Unix timestamp in milliseconds
            $timestamp = date('Y-m-d H:i:s', time());

            // add id to flw_appl_reports
            $query = "UPDATE flw_appl_reports SET geom_site_visit_id = COALESCE(geom_site_visit_id, '{}'::integer[]) || ARRAY[:geomSvId]::integer[], updated_timestamp = :timestamp WHERE system_id = :systemId AND authority_id = :authorityId AND report_no = :reportNo AND NOT (geom_site_visit_id @> ARRAY[:geomSvId]::integer[]);";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->bindParam(':geomSvId', $id, PDO::PARAM_INT);
            $stmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);
            $stmt->bindParam(':authorityId', $authId, PDO::PARAM_INT);
            $stmt->bindParam(':reportNo', $reportId, PDO::PARAM_INT);
            $stmt->execute();

            // Finally, return a JSON
            echo json_encode(
                array(
                    "message" => "Success",
                    "status" => 200,
                    "data" => array(
                        "id" => $id,
                        "systemID" => $systemId,
                        "markerID" => $leafletID
                    )
                )
            );

            // Close the database connection
            $conn = null;
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $PUT = json_decode($data, true);

    if (isset($PUT["action"])) {
        if ($PUT["action"] == 'edit') {

            $systemId = $PUT['systemId'];
            $reportId = $PUT['reportId'];
            $authId = $PUT['authId'];
            $id = $PUT['id'];
            $leafletID = $PUT['markerId'];

            try {
                // Connect to the database using PDO
                $conn = $db->createConnection();

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $conn = null;

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

            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Prepare the first update query
            $addedDate = date('Y-m-d H:i:s', time());
            $desc = $PUT['description'];
            $lat = $PUT['latitude'];
            $long = $PUT['longitude'];

            $updateQuery = "UPDATE geom_site_visit SET description = NULLIF(:desc, ''), geom = ST_SetSRID(ST_MakePoint(:long, :lat), 4326), added_at = :addedDate
                            WHERE geom_id = :leafletID AND (report_id = :codedReport AND id = :id)";
            $updateStatement = $conn->prepare($updateQuery);
            $updateStatement->bindParam(':desc', $desc);
            $updateStatement->bindParam(':long', $long);
            $updateStatement->bindParam(':lat', $lat);
            $updateStatement->bindParam(':addedDate', $addedDate);
            $updateStatement->bindParam(':leafletID', $leafletID);
            $updateStatement->bindParam(':codedReport', $codedReport);
            $updateStatement->bindParam(':id', $id);
            $selectStatement = $conn->prepare($selectQuery);
            $selectStatement->bindParam(':leafletID', $leafletID);
            $selectStatement->bindParam(':codedReport', $codedReport);
            $selectStatement->bindParam(':id', $id);
            $selectStatement->execute();

            // Fetch the result
            $row = $selectStatement->fetch(PDO::FETCH_ASSOC);

            $decodeUrl = base64_decode($row['url']);
            // Finally, return a JSON
            echo json_encode(
                array(
                    "message" => "Success",
                    "status" => 200,
                    "data" => array(
                        "id" => $row['id'],
                        "systemID" => $systemId,
                        "markerID" => $leafletID,
                        "img" => $decodeUrl
                    )
                )
            );
        } else if ($PUT["action"] == "update-geom-init") {
            $systemId = $PUT['systemId'];
            $id = $PUT['id'];
            $reportId = $PUT['reportId'];

            try {
                // Connect to the database using PDO
                $conn = $db->createConnection();

                // Declare the query
                $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

                // Prepare the query
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':systemId', $systemId);

                // Execute the query
                $stmt->execute();

                // Fetch the single value from the query result
                $referenceNo = $stmt->fetchColumn();

                // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
                $conn = null;

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
            $authId = $PUT['authId'];
            $geomId = $PUT['geomId'];
            $newGeomId = $PUT['newGeomId'];

            try {
                // Connect to the database using PDO
                $conn = $db->createConnection();

                // Declare the query
                $query = "UPDATE public.geom_site_visit SET geom_id = :newGeomId WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND geom_id = :geomId AND id = :id AND ST_GeometryType(geom) = 'ST_Point'";

                // Prepare the query
                $stmt = $conn->prepare($query);

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
                $conn = null;
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
    } else {
        $reportId = $PUT['reportId'];
        $systemId = $PUT['systemId'];
        $authId = $PUT['authId'];
        $leafletID = $PUT['markerId'];
        $id = $PUT['id'];

        try {
            // Connect to the database using PDO
            $conn = $db->createConnection();

            // Declare the query
            $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

            // Prepare the query
            $stmt = $conn->prepare($query);

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);

            // Execute the query
            $stmt->execute();

            // Fetch the single value from the query result
            $referenceNo = $stmt->fetchColumn();

            // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
            $conn = null;

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


        // Connect to the database using PDO
        $conn = $db->createConnection();

        // Prepare the update query
        $addedDate = date('Y-m-d H:i:s', time());
        $lat = $PUT['latitude'];
        $long = $PUT['longitude'];

        $updateQuery = "UPDATE geom_site_visit SET geom = ST_SetSRID(ST_MakePoint(:long, :lat), 4326), added_at = :addedDate
                        WHERE geom_id = :leafletID AND report_id = :codedReport";
        $updateStatement = $conn->prepare($updateQuery);
        $updateStatement->bindParam(':long', $long);
        $updateStatement->bindParam(':lat', $lat);
        $updateStatement->bindParam(':addedDate', $addedDate);
        $updateStatement->bindParam(':leafletID', $leafletID);
        $updateStatement->bindParam(':codedReport', $codedReport);
        $updateStatement->execute();

        // Prepare the select query
        $selectQuery = "SELECT url, id FROM geom_site_visit WHERE geom_id = :leafletID AND report_id = :codedReport AND id = :id";
        $selectStatement = $conn->prepare($selectQuery);
        $selectStatement->bindParam(':leafletID', $leafletID);
        $selectStatement->bindParam(':codedReport', $codedReport);
        $selectStatement->bindParam(':id', $id);
        $selectStatement->execute();

        // Fetch the result
        $row = $selectStatement->fetch(PDO::FETCH_ASSOC);

        $decodeUrl = base64_decode($row['url']);
        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "Success",
                "status" => 200,
                "data" => array(
                    "id" => $row['id'],
                    "systemID" => $systemId,
                    "markerID" => $leafletID,
                    "img" => $decodeUrl
                )
            )
        );
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $DELETE = json_decode($data, true);

    $reportId = $_GET['ref'];
    $systemId = $_GET['sid'];
    $authId = $_GET['auth'];
    $leafletID = $_GET['markerId'];
    $id = $_GET['id'];

    try {
        // Connect to the database using PDO
        $conn = $db->createConnection();

        // Declare the query
        $query = "SELECT reference_no FROM public.flw_appl_entries WHERE system_id = :systemId";

        // Prepare the query
        $stmt = $conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch the single value from the query result
        $referenceNo = $stmt->fetchColumn();

        // Close the database connection (optional since PDO automatically closes the connection when the PDO instance is destroyed)
        $conn = null;

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
        if ($system->App->title == 'UCIDOS' || $system->App->title == 'KITER' || $system->App->title == 'KUDRAT') {
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

    // Connect to the database using PDO
    $conn = $db->createConnection();

    $query = "DELETE FROM geom_site_visit WHERE geom_id = :leafletID AND report_id = :codedReport AND id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':leafletID', $leafletID);
    $stmt->bindParam(':codedReport', $codedReport);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Get the current Unix timestamp in milliseconds
    $timestamp = date('Y-m-d H:i:s', time());

    // Execute a SELECT query on the database
    $query = "UPDATE flw_appl_reports SET geom_site_visit_id = array_remove(geom_site_visit_id, :gid), updated_timestamp = :timestamp WHERE flw_appl_reports.system_id = :sid AND flw_appl_reports.report_no = :rn AND flw_appl_reports.authority_id = :aid RETURNING geom_site_visit_id";
    $stmt = $conn->prepare($query);
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
            "note" => "method--delete",
            "deleted_id" => $id,
            "leaflet_id" => $leafletID,
            "report_id" => $codedReport
        )
    );

    // Close the database connection
    $conn = null;

} else {
}