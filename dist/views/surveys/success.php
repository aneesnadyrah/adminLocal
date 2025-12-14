<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/system.php";
$system = new System;
$tenant = $system->App->tenant;
require_once "config/autoload.php";
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
    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" class="app-blank">
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Welcome-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Wrapper-->
            <div class="d-flex flex-center flex-column flex-column-fluid p-10">
                <!--begin::Logo-->
                <div id="success" class="w-300px"></div>
                <!--end::Logo-->
                <!--begin::Title-->
                <h1 class="fw-bold fs-2x text-gray-800 mb-12">Berjaya daftar masuk 🎉!</h1>
                <!--end::Title-->
                <!--begin::Description-->
                <p class="text-gray-600 fs-6 fw-bold">
                    Anda telah berjaya daftar masuk kerja ukur hari ini.
                </p>
                <!--end::Description-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Welcome-->
    </div>
    <!--end::Root-->
    <!--end::Main-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <script>
        bodymovin.loadAnimation({
            
            container: document.getElementById('success'),
            renderer: 'svg',
            loop: false,
            autoplay: true,
            path: "/assets/media/lottie/success.json"
        })
    </script>
    <!--end::Global Javascript Bundle-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>