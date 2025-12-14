<?php
// Define parameters
$method = 'POST';
$contentType = 'json';
$ssl = false;
$data = null;

// Determine content type header
switch ($contentType) {
    case 'json':
        $type = "Content-Type: application/json";
        break;
    case 'urlencoded':
        $type = "Content-Type: application/x-www-form-urlencoded";
        break;
    case 'multipart':
        $type = "Content-Type: multipart/form-data";
        break;
    default:
        $type = "";
}

// Define common headers
$headers = [
    $type,
    'X-Server-Request: S8R2D7A1F6G4L5V3W9U6XpMjTbNzKqYv',
];

// Define domains to trigger check
$appDomains = [
    'admin.kutt.my',
    'admin.kup.my',
    'admin.kudr.my'
];

// Function to make cURL request
function makeCurlRequest($url, $method, $ssl, $headers, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Iterate through domains and make cURL requests
foreach ($appDomains as $domain) {
    $url = 'https://' . $domain . '/api/projects/permits/expiry';
    $result = makeCurlRequest($url, $method, $ssl, $headers, $data);
    // Process $result
}