<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/autoload.php";
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
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="assets/plugins/custom/leaflet/leaflet.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        #map {
            height: calc(100vh - 15vh);
        }

        .basemap img{
            border-radius: 1rem !important;
        }

        /* zoom control */
        .leaflet-touch .leaflet-control-layers, .leaflet-touch .leaflet-bar{
            border: none !important;
        }
        .leaflet-control-zoom-in,
        .leaflet-control-zoom-out {
            border-radius: 0.5rem !important;
            margin: 0.1rem;
            border: none;
            }

        /* attribution and scale controls */
        .leaflet-container .leaflet-control-attribution {
            display: none;
        }
        input[type="file"] {
  display: none;
}
    </style>
    <style>
        .mapboxgl-popup-content {
            background-color: transparent !important;
            border-radius: 0px !important;
            box-shadow: none !important;
            padding: 0px !important;
            pointer-events: auto;
            position: relative;
        }

        .mapboxgl-ctrl-logo {
            display: none !important;
        }

        .blockui-overlay {
            z-index: 1000 !important;
        }
    </style>
    <style>
        #dropzoneIndicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999; /* Adjust the z-index as needed to bring it to the forefront */
        }

        .blurred {
            filter: blur(2px); /* Adjust the blur intensity as needed */
        }
    </style>
    <script>
        function ga() {};
        ga('send', 'pageview');
    </script>

    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
    <!--begin::loader-->
    <div class="page-loader flex-column">
        <img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default.svg" />
        <img alt="Logo" class="theme-dark-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-dark.svg" />
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
                        <?php include "components/wizards/upload-gpkg.php" ?>
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
    <!--end::Root-->
    <!--begin::Drawers-->
    <?php
    // foreach ($drawers as $drawer) {
    //     include $drawer;
    // }
    ?>
    <!--end::Drawers-->
    <!--end::Main-->
    <!--begin::Modals-->
    <!--begin::Modal - Create App-->
    <?php
    // foreach ($generalModals as $modal) {
	// 	include $modal;
	// }
    ?>
		<?php
    //     foreach ($actionModals as $modal) {
	// 	include $modal;
	// }
    ?>
    <!--end::Modal - Create App-->

    <!--end::Modals-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "assets/";
        var systemID = "<?php echo $systemId ?>"
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/leaflet/leaflet.bundle.js"></script>
    <script src="assets/js/custom/geospatial/geopackage-extractor.js"></script>
    <script src="assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/geospatial/map-extractor.js"></script>
    <script id="feature-table-template" type="x-tmpl-mustache">
        <div class="mt-5 d-flex justify-content-between" data-map-extractor="action1">
            <div class="form-check form-switch form-check-custom form-check-solid">
                <input class="form-check-input" type="checkbox" name="onoffswitch-{{tableName}}"
                id="myonoffswitch-{{tableName}}" onchange="toggleLayer('feature', '{{tableName}}')" data-map-extractor="view-toggle"/>
                <label class="form-check-label" for="flexSwitchDefault">Tunjuk Laluan</label>
            </div>
            <div id="feature-{{tableName}}">
                <button type="button" class="btn btn-icon btn-active-light-primary" id="zoom-{{tableName}}"
                onclick="zoomTo({{contents.minX}}, {{contents.minY}}, {{contents.maxX}}, {{contents.maxY}},
                '{{contents.srs.organization}}:{{contents.srs.organization_coordsys_id}}')" data-map-extractor="view-zoom">
                    <i class="fad fa-magnifying-glass-location fs-3"></i>
                </button>
                <!--begin::Save-->
                <button class="btn btn-icon btn-active-light-success" data-action="save"  onclick="saveGeoJSON('{{tableName}}')">
                    <!--begin::Indicator label-->
                    <i class="fad fa-floppy-disk fs-3 indicator-label"></i>
                    <!--end::Indicator label-->
                    <!--begin::Indicator progress-->
                    <span class="indicator-progress">
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                    <!--end::Indicator progress-->
                </button>
                <!--end::Save-->
            </div>
        </div>
    </script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>