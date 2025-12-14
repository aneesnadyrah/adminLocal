<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require "config/autoload.php";

$type = $_GET['type'] ?? NULL;
$department = $_GET['department'] ?? NULL;
$sitevisit = new Sitevisit($type, $department);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <base href="/" />
    <title>
        <?= $system->App->title ?>
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/<?= strtolower($system->App->title) ?>-small.svg" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
</head>
<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on"
    class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
    <div class="page-loader flex-column">
        <img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default.svg" />
        <img alt="Logo" class="theme-dark-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-dark.svg" />
        <div class="d-flex align-items-center mt-5">
            <span class="spinner-border text-primary" role="status"></span>
        </div>
    </div>
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        var table = '#sitevisit-table';
        var tenant = '<?= $system->App->tenant ?>';
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Aside-->
            <?php include "components/layouts/sidebar/mainLayout.php" ?>
            <!--end::Aside-->
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!--begin::Header-->
                <?php include "components/layouts/header.php" ?>
                <!--end::Header-->
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Container-->
                    <div class="container-xxl" id="kt_content_container">
                        <?php $sitevisit->fetch(); ?>
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Content-->
                <!--begin::Footer-->
                <?php include "components/layouts/footer.php" ?>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="assets/js/custom/details/sitevisit.js"></script>
</body>
<!--end::Body-->

</html>