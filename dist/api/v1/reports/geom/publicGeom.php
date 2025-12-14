<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));


header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// import connection and api setup
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/functions.php";

// Connect to the database using PDO
$db = new DBConnectionFactory();
$telegram = new Telegram();
$system = new System;
// $flow             = new FlowStatuses($username);
// $changelog        = new Changelog($username);
$Curl = new Curl();
$traffic = new Traffic();
$taskAssignment = new TaskAssignments();
$chronology = new Chronology();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET["action"])) {
        // check for init request
        if ($_GET["action"] == "marker") {
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
        } else if ($_GET["action"] == "polygon") {
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
        } else if ($_GET['action'] == "polyline") {
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
                $query = "SELECT *, ST_AsGeoJSON(geom) AS geometry FROM public.geom_site_visit WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND ST_GeometryType(geom) = 'ST_LineString';
            ";

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
                    "polyline" => $markers
                )
            );
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Get the request body
    $data = file_get_contents('php://input');
    // Parse the request body if it's in JSON format
    $PUT = json_decode($data, true);

    if (isset($PUT["action"])) {
        if ($PUT["init"] == "marker") {
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
        } else if ($PUT['init'] == 'polyline') {
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
                $query = "UPDATE public.geom_site_visit SET geom_id = :newGeomId WHERE system_id = :systemId AND report_id = :codedReport AND authority_id = :authId AND id = :id AND geom_id = :geomId AND ST_GeometryType(geom) = 'ST_LineString'";

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
        } else if ($PUT['init'] == 'polygon') {
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
        }
    }
}
