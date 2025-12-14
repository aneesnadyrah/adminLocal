<?php

// Gis Tracer

// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// require "api/gateway.php";
require "config/system.php";
include_once "config/functions.php";

// set header control :: origin header are setted by gateway.php
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Check if the URL contains a parameter named
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Get the token and request domain from the request
    $token = '';
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    } elseif (isset($_GET['t'])) {
        $token = $_GET['t'];
    }
    // $token = isset($_GET['token']) ? $_GET['token'] : $_GET['t'];
    $domain = substr($_SERVER['HTTP_ORIGIN'], 8);

    $subId = $_GET['subId'];

    // Prepare a statement to select the API token that matches the token and domain in the request, and is still active
    $stmt1 = $conn->prepare('SELECT system_id FROM flw_appl_entries WHERE system_id = :subId');
    $stmt1->bindParam(':subId', $subId);
    $stmt1->execute();

    if ($stmt1->rowCount() > 0) {
        $result = $stmt1->fetch(PDO::FETCH_ASSOC);
        $sysId = $result['system_id'];

        $stmtgeom = $conn->prepare('SELECT id, length, method, ST_AsGeoJSON(ST_MakeLine(geom)) AS geojson FROM geom_gis_trace WHERE system_id = :systemId AND revision = 1 GROUP BY id, length, method');
            $stmtgeom->bindParam(':systemId', $sysId);
            $stmtgeom->execute();

        if ($stmtgeom->rowCount() > 0) {
            // $resultGeom = $stmtgeom->fetchAll(PDO::FETCH_ASSOC);
            $results = $stmtgeom->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $resultGeom) {
                $id = $resultGeom['id'];
                $length = $resultGeom['length'];
                $method = $resultGeom['method'];
                
                $geojson = json_decode($resultGeom['geojson'], true);
                $type = $geojson['type'];
                $coordinates = $geojson['coordinates'];

                $geom = [
                    "type" => "Feature",
                    "properties" => [
                        "length" => $length,
                        "method" => $method
                    ],
                    "geometry" => [
                        "type" => $type,
                        "coordinates" => $coordinates
                    ],
                ];
                $geometries[] = $geom;
            }

            http_response_code(200);
            $tracerData = array(
                "type" => "FeatureCollection",
                "features" => $geometries,
                "success" => true,
            );
            
            $tracerData['success'] === true ? $status = true : $status = false;        
            $headers = json_encode($curl->getHeaders());
            // Get protocol
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $domain = $_SERVER['HTTP_HOST'];
            $requestUri = $_SERVER['REQUEST_URI'];
            $url = $protocol . '://' . $domain . $requestUri;
            $curl->callback($url, $headers, $results, json_encode($tracerData), $_SERVER['REQUEST_METHOD'], $status);
            
            // ExternalApi::recordCallbackData( 1, 6, null, $tracerData, $_SERVER['REQUEST_URI'], http_response_code(), true);

        } else {
            http_response_code(204);
            $tracerData = array(
                "success" => false,
                "message" => "No Content"
            );

        }

        $conn = null;

        // Output the response as JSON
        echo json_encode($tracerData);

    } else {
        // $response = json_encode(array("message" => 'Invalid submission code'));

        http_response_code(401);
        $response = array(
            "success" => false,
            "message" => "Invalid submission code"
        );
        
        echo json_encode($response);
    }

} 
