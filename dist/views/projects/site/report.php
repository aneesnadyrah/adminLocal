<!DOCTYPE html>
<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// include_once "config/system.php";
// include_once "config/tenant.php";
// include_once "config/autoload.php";
require_once "config/autoload.php";
$system = new System;
?>

<html lang="ms">
<!--begin::Head-->

<head>
    <base href="../../" />
    <title>
        <?php echo $system->App->title ?>
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="shortcut icon" href="assets/media/logos/<?php echo strtolower($system->App->title) ?>-small.svg" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="assets/plugins/custom/leaflet/leaflet.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->
    <!--begin::Custom Stylesheets(used for this page only)-->
    <link href="assets/css/custom/reports/site-visit.css" rel="stylesheet" type="text/css" />
    <!--end::Custom Stylesheets(used for this page only)-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on"
    class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
    <!--begin::loader-->
    <div class="page-loader flex-column">
        <img alt="Logo" class="theme-light-show h-40px"
            src="assets/media/logos/<?php echo strtolower($system->App->title) ?>-default.svg" />
        <img alt="Logo" class="theme-dark-show h-40px"
            src="assets/media/logos/<?php echo strtolower($system->App->title) ?>-dark.svg" />
        <div class="d-flex align-items-center mt-5">
            <span class="spinner-border text-primary" role="status"></span>
        </div>
    </div>
    <!--end::Loader-->
    <!--begin::Theme mode setup on page load-->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
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
    <!--end::Theme mode setup on page load-->
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid" report-custom-data="map-container">
            <?php include "components/wizards/report-site.php" ?>
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--begin::Drawers-->
    <?php // foreach ($drawers as $drawer) { include $drawer; } ?>
    <!--end::Drawers-->
    <!--end::Main-->
    <!--begin::Modals-->
    <?php // foreach ($generalModals as $modal) { include $modal; }  ?>
    <!--end::Modals-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
        var systemID = "<?php echo $systemId ?>"
        var tenant = `<?php echo $system->App->tenant; ?>`;
        var geoserverLayer = `<?php echo $system->App->geoserver; ?>`;
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/leaflet/leaflet.bundle.js"></script>
    <script src="assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
    <script src="assets/plugins/custom/signature-pad/signature-pad.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/reports/site-visit.js"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>