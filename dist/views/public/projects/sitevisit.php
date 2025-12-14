<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "config/functions.php";
$system = new System;

$systemId = $_GET['sid'];
$reportId = $_GET['r'];
$tenant = $system->App->tenant;
$company = $system->App->company;

?>

<!DOCTYPE html>
<html lang="ms">

<!--begin::Head-->

<head>
    <base href="/" />
    <title>
        <?= $system->App->title ?>
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/<?= strtolower($system->App->title) ?>-small.svg" />
    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <!--end::Global Stylesheets Bundle-->
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/leaflet/leaflet.bundle.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="assets/css/custom/prints/print.css">
    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @media print {
            #printButton {
                display: none;
            }
        }

        @page {
            size: A4
        }

        h5,
        h2,
        p,
        span,
        th,
        td {
            font-family: Arial;
        }

        h2 {
            font-size: 16px;
        }

        h5 {
            font-size: 14px;
        }

        p {
            font-size: 12px;
        }

        th,
        td {
            font-size: 13px;
        }

        table {
            width: 100%;
        }

        .table-bordered {
            border-collapse: collapse;
            text-align: center;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid black;
            padding: 0.25rem;
        }
    </style>
</head>
<!--end::Head-->
<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->

<body class="A4">
    <div class="d-flex justify-content-center align-items-center pt-5">
        <button id="printButton" type="button" class="btn btn-light" onclick="window.print();">
            <i class="fad fa-print fs-4"></i>Cetak
        </button>
    </div>
    <?php include 'components/projects/public-sv-view.php';?>

    <script>
        var hostUrl = "assets/";
        var tenant = `<?php echo $system->App->tenant; ?>`;
        var geoserverLayer = `<?php echo $system->App->geoserver; ?>`;
        var sysId = `<?php echo $systemId; ?>`;
        var authValue = `<?php echo $authValue; ?>`;
        var currentReports = <?php echo json_encode($currentReports); ?>;
    </script>
    <script src="assets/js/scripts.bundle.js"></script>
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/plugins/custom/leaflet/leaflet.bundle.js"></script>
    <script src="assets/js/custom/reports/public-view.js"></script>
</body>

</html>