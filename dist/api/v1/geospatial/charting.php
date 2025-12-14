<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (session_status() == PHP_SESSION_NONE) {
    session_start();
};

require "api/header.php";
require "config/system.php";
include "api/functions.php";
require "config/DBFactory.php";

if (isset($_GET['g'])) {

    $systemId = $_GET['g'];

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Query the database to retrieve the Linestring geometry
    $stmt = $conn->prepare("SELECT ST_AsGeoJSON(ST_MakeLine(geom)) AS geojson FROM geom_gis_trace WHERE system_id = :sysID AND revision = 0 ");

    // bind the parameter to the placeholder using the bindParam method
    $stmt->bindParam(':sysID', $systemId);

    // Execute the query
    $stmt->execute();

    // Convert the result into a GeoJSON string
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $geojson = $row['geojson'];

    // Output the GeoJSON string
    header('Content-Type: application/json');
    echo $geojson;

    // Close the database connection
    $conn = null;
}
?>