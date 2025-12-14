<?php
/**
 * Simple File Download Handler for KITER System
 * Handles direct file downloads without complex dependencies
 */

// Basic security check
if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400);
    die('Invalid request: URL parameter required');
}

function normalize_url($url) {
    $parts = parse_url($url);

    return
        (isset($parts['scheme']) ? $parts['scheme'] . '://' : '') .
        (isset($parts['host']) ? $parts['host'] : '') .
        (isset($parts['path']) ? implode('/', array_map('rawurlencode', explode('/', $parts['path']))) : '') .
        (isset($parts['query']) ? '?' . http_build_query(parse_str($parts['query'], $out) ? $out : []) : '') .
        (isset($parts['fragment']) ? '#' . rawurlencode($parts['fragment']) : '');
}

// Get parameters
$fileUrl = base64_decode($_GET['url']);
$forceDownload = isset($_GET['download']) && $_GET['download'] == '1';
$mimeType = $_GET['mime'] ?? 'application/pdf';

$fileUrl = normalize_url($fileUrl);
// Basic URL validation
if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    die('Invalid URL format');
}

// Extract filename from URL
$urlParts = parse_url($fileUrl);
$pathInfo = pathinfo($urlParts['path']);
$filename = $pathInfo['basename'];

// Clean filename for download
$cleanFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

// Set appropriate headers
if ($forceDownload) {
    // Force download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
} else {
    // Display inline
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: inline; filename="' . $cleanFilename . '"');
}

// Additional headers
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Create context for external requests (handle SSL)
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
    'http' => [
        'timeout' => 30,
        'user_agent' => 'KITER-Download/1.0'
    ]
]);

// Stream the file
try {
    // Get file headers to check if it exists
    $headers = get_headers($fileUrl, 1, $context);
    
    if (!$headers || strpos($headers[0], '200') === false) {
        http_response_code(404);
        die('File not found or inaccessible');
    }
    
    // Stream the file content
    $fileContent = file_get_contents($fileUrl, false, $context);
    
    if ($fileContent === false) {
        http_response_code(500);
        die('Error reading file');
    }
    
    // Output file content
    echo $fileContent;
    
} catch (Exception $e) {
    error_log("Download error: " . $e->getMessage());
    http_response_code(500);
    die('Error processing download request');
}

exit;
?>