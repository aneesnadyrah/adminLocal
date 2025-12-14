<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "api/functions.php";
$System = new System;
$tenant = $System->App->tenant;

// Connect to the database using PDO
$db = new DBConnectionFactory();
$conn = $db->createConnection();

echo Task::getList();
