<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/autoload.php";

$system = new System;
?>

<!DOCTYPE html>
<html lang="ms">

<!--begin::Head-->

<head>
    <base href="/" />
	<title><?= $system->App->title ?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" href="assets/media/logos/<?= strtolower($system->App->title) ?>-small.svg" />
	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="assets/css/custom/prints/print.css">
    <!-- Set page size here: A5, A4 or A3 -->
    <style>
        @page {
            size: A4
        }

        h5,h2,p,span,th,td {
            font-family: Inter;
        }
        h2 {
            font-size: 16px;
        }
        h5 {
            font-size: 14px;
        }
        p{
            font-size: 12px;
        }
        th,td{
            font-size: 13px;
        }

        table {
            width:100%;
        }

        .table-bordered {
            border-collapse: collapse;
            text-align:center;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid black;
            padding: 0.25rem;
        }
        /* Fix for cut out page in bkil */
        .sheet {
            overflow: visible;
        }
    </style>
</head>
<!--end::Head-->
<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">
    <?php 
    if($system->App->title == 'KITER') {
        $create = array('bkil-kutt-1', 'bkil-kutt-2'); 
        foreach ($create as $key => $value){
            include General::getForm($value);
        };

    } else if($system->App->title == 'KUDRAT') {
        $create = array('bkil-kudr-1','bkil-kudr-2'); 
        foreach ($create as $key => $value){
            include General::getForm($value);
        };

    }
    
    
    ?>

<script>
    window.print();
</script>
</body>

</html>