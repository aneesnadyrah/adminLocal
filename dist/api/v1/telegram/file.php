<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "notification/_api.php";

if (isset($_POST['id'])){
    $chatId = $_POST['id'];
    $caption = $_POST['desc']; 
    $filePath = base64_decode($_POST['document']);

    if ($appState == '11') {
        $token = '5384315062:AAFe8ygl6RJX_xjdxMu1P53CaW4YsujHEXA';
    } elseif ($appState == '06') {
        $token = '5384315062:AAFe8ygl6RJX_xjdxMu1P53CaW4YsujHEXA';
    }

    // Create a cURL file object
    $file = curl_file_create($filePath);
    // API endpoint and parameters
    $url = 'https://api.telegram.org/bot' . $token . '/sendDocument';
    $params = array(
        'chat_id' => $chatId,
        'document' => $file,
        'caption' => $caption,
        'disable_notification' => false // sends the msg silently. IOS user will not receive notification, Android user will receive notification but no sound.
    );

    // Initialize cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $result = curl_exec($ch);

    // $result = file_get_contents("https://api.telegram.org/bot$token/sendDocument?chat_id=$chatId&document=$filePath&caption=$fileName");

    if ($result === false) {
        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "error",
                "status" => 500,
            )
        );

    } else {
        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => "success",
                "status" => 200,
            )
        );
    }
    
} else {


}







?>
