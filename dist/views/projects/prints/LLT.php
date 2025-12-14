<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include_once "config/tenant.php";
include_once "config/autoload.php";

?>

<!DOCTYPE html>
<html lang="ms">

<!--begin::Head-->

<head>
    <base href="../../../../" />
    <title>
        <?php echo $appsTitle ?>
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/<?php echo $appsTitle ?>-small.svg" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <!--end::Global Stylesheets Bundle-->
    <link rel="stylesheet" href="assets/css/custom/prints/print.css">

    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/custom/leaflet/leaflet.bundle.css" rel="stylesheet" type="text/css" />
    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        h5,
        h2,
        p,
        span,
        th,
        td {
            font-family: Inter;
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
    <?php $create = array('LLT-view');
    foreach ($create as $key => $value) {
        include 'components/projects/' . $value . '.php';
    }
    ;
    ?>

    <script>
        var hostUrl = "assets/";
        var tenant = `<?php echo $system->App->tenant; ?>`;
        var geoserverLayer = `<?php echo $system->App->geoserver; ?>`;
    </script>
    <script src="assets/js/scripts.bundle.js"></script>
    <script src="assets/plugins/custom/leaflet/leaflet.bundle.js"></script>
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/custom/reports/print.js"></script>
    <script src="assets/plugins/custom/jspdf/jspdf.bundle.js"></script>

</body>

</html>