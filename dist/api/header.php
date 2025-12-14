<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location:/auth/signin");
    exit; // Stop execution after handling the controlled path
}
if(isset($_SESSION["username"])){
    $username = $_SESSION["username"];
}

if(isset($_SESSION["roleId"])){
    $roleId = $_SESSION["roleId"];
}


// declare function to set accesscontrolheaders for internal api request
if (!function_exists('setHttpsAccessControlHeaders')){
    function setHttpsAccessControlHeaders($httpHost) {
        header("Access-Control-Allow-Origin: https://$httpHost");
        header("Access-Control-Allow-Headers: https://$httpHost");
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE');
    }
}

// check allowed domain to set accesscontrolheader
if ($_SERVER['HTTP_HOST'] == 'kiter.test' || $_SERVER['HTTP_HOST'] == 'kiter.asiadebut.tech' || $_SERVER['HTTP_HOST'] == 'admin.kutt.my' || $_SERVER['HTTP_HOST'] == 'ucidos.test' || $_SERVER['HTTP_HOST'] == 'ucidos.asiadebut.tech' || $_SERVER['HTTP_HOST'] == 'admin.kup.my' || $_SERVER['HTTP_HOST'] == 'kudr.test' || $_SERVER['HTTP_HOST'] == 'kudr.asiadebut.tech' || $_SERVER['HTTP_HOST'] == 'kudrat.asiadebut.tech' || $_SERVER['HTTP_HOST'] == 'admin.kudr.my' || $_SERVER['HTTP_HOST'] == 'demo.asiadebut.tech') {
    setHttpsAccessControlHeaders($_SERVER['HTTP_HOST']);
}

header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');


?>