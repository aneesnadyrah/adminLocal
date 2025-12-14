<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "notification/_api.php";

if (isset($_POST['id'])){
    $chatId = $_POST['id'];
    $stickerId = $_POST['sticker_id'];

    if ($appState == '11') {
        $token = '5384315062:AAFe8ygl6RJX_xjdxMu1P53CaW4YsujHEXA';
    } elseif ($appState == '06') {
        $token = '5384315062:AAFe8ygl6RJX_xjdxMu1P53CaW4YsujHEXA';
    }
    
    // Use cURL to send the message to Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendSticker");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id=$chatId&sticker=$stickerId");
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