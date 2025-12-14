<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

session_start();

if (!isset($_SESSION["username"])) {
    header("location:/auth/signin");
    exit; // Stop execution after handling the controlled path
}

if(isset($_GET['sid'])){
    $systemId = $_GET['sid'];
}

if(isset($_GET['id'])){
    $systemId = $_GET['id'] ?? NULL;
}

if(isset($_SESSION["username"])){
    $username = $_SESSION["username"];
}

if(isset($_SESSION["roleId"])){
    $roleId = $_SESSION["roleId"];
}
require_once "config/system.php";
require_once "config/functions.php";
require_once "config/controller.php";
require_once "config/components.php";

$system = new System;
$controller = new Controller($username);
$Role = new Roles();

?>