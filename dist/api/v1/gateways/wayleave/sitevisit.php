<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/gateway.php";
require "config/system.php";
include_once "config/tenant.php";
include_once "config/functions.php";

// set header control :: origin header are setted by gateway.php
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET'){
    // record the callback
    $callbackId = ExternalApi::insertCallbackData(1, 15, null, $_SERVER['REQUEST_URI'], http_response_code());

    // update the callback
    $jsonResponse = json_encode(["error"=>"Method not allowed"]);
    ExternalApi::updateCallbackData($callbackId, $jsonResponse, true);

    echo $jsonResponse;

} else if ($method == "POST"){
    // Retrieve the JSON data
    $json_data = file_get_contents("php://input");
    $callbackId = ExternalApi::insertCallbackData(1, 15, $json_data, $_SERVER['REQUEST_URI'], http_response_code());

    // Get the token and request domain from the request
    $token = isset($_GET['token']) ? $_GET['token'] : $_GET['t'];
    $requestDomain = substr($_SERVER['HTTP_ORIGIN'], 8);

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Prepare a statement to select the API token that matches the token and domain in the request, and is still active
    $stmt = $conn->prepare('SELECT * FROM sys_api_tokens WHERE token = :token AND domain = :domain AND expires_at > NOW() AND is_active = true');
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':domain', $requestDomain);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // The token is valid
        // TODO: // control the payload data here

    } else {
        // The token or domain is invalid or the token has expired
        $jsonResponse = json_encode(
            array("message" => "Invalid or expired API token")
        );
        ExternalApi::updateCallbackData($callbackId, $jsonResponse, true);
        echo $jsonResponse;

    }

} else if ($method == "PUT") {
    // Retrieve the JSON data
    $json_data = file_get_contents("php://input");
    $callbackId = ExternalApi::insertCallbackData(1, 15, $json_data, $_SERVER['REQUEST_URI'], http_response_code());

    // Get the token and request domain from the request
    $token = isset($_GET['token']) ? $_GET['token'] : $_GET['t'];
    $requestDomain = substr($_SERVER['HTTP_ORIGIN'], 8);

    // Connect to the database using PDO
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();

    // Prepare a statement to select the API token that matches the token and domain in the request, and is still active
    $stmt = $conn->prepare('SELECT * FROM sys_api_tokens WHERE token = :token AND domain = :domain AND expires_at > NOW() AND is_active = true');
    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':domain', $requestDomain);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // token valid
        // TODO: // control the payload data here
        
    } else {
        // The token or domain is invalid or the token has expired
        $jsonResponse = json_encode(
            array("message" => "Invalid or expired API token")
        );
        ExternalApi::updateCallbackData($callbackId, $jsonResponse, true);

        echo $jsonResponse;

    }

} else if($method == 'DELETE'){
    // record the callback
    $callbackId = ExternalApi::insertCallbackData(1, 15, null, $_SERVER['REQUEST_URI'], http_response_code());

    // update the callback
    $jsonResponse = json_encode(["error"=>"Method not allowed"]);
    ExternalApi::updateCallbackData($callbackId, null, true);

    echo $jsonResponse;
}