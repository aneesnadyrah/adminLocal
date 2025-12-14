<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

if (isset($POST['id'])){
    $chatId = $POST['id'];
    $message = $POST['message'];

    if ($appState == '11') {
        $token = '5384315062:AAFe8ygl6RJX_xjdxMu1P53CaW4YsujHEXA';
    } elseif ($appState == '06') {
        $token = '5384315062:AAFe8ygl6RJX_xjdxMu1P53CaW4YsujHEXA';
    }

    $response = urlencode($message);
    
    // Use cURL to send the message to Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendMessage");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id=$chatId&text=$response&parse_mode=markdownv2");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL and get the response
    $result = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    if ($result === false) {
        // Return a JSON response for error
        echo json_encode(["message" => "error"]);
    } else {
        // Return a JSON response for success
        echo json_encode(["message" => "success"]);
    }
}







?>