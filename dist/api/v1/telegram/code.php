<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
require_once "config/system.php";
$system = new System;
$state = $system->App->state;
$botToken = $system->App->botToken;

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

if (isset($POST['code'])){
    $code = $POST['code'];
    $chatId = $POST['id'];
    $type = $POST['type'];

    if ($state == '11') {
        $token = $botToken;
    } elseif ($state == '06') {
        $token = $botToken;
    } elseif ($state == '08') {
        $token = $botToken;
    }

    if ($type == 1) {
        $message = "Kod Pengesahan Akaun Anda Adalah *$code*\\. Jika Anda Tidak Membuat Sebarang Permintaan Kod Pengesahan, Sila Hubungi Sokongan Teknikal Kami\\.";
    } else {
        $message = "Kod Keselamatan Anda Adalah *$code*\\. Jika Anda Tidak Membuat Sebarang Permintaan Kod Keselamatan, Sila Hubungi Sokongan Teknikal Kami\\.";
    }

    // URL encode the message
    $message = urlencode($message);

    // Use cURL to send the message to Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/$token/sendMessage");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "chat_id=$chatId&text=$message&parse_mode=markdownv2");
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


} else {


}
?>