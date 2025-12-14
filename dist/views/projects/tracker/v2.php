<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/autoload.php";

$tracker = new Tracker();
$system = new System();
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
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        #tracker-v2 > thead > tr > th {
            text-align: center;
            padding: 0.8rem 0.8rem;
        }
        #tracker-v1 > tbody > tr > td {
            text-align: center;
            /* padding: 0rem 0.5rem; */
        }
    </style>
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
        var table = '#tracker-v2';
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
                        <?php
                            $tracker->trackerNew('tracker-v2');
                        ?>

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
    <!--end::Drawers-->

    <!--begin::Modals-->
    <!--end::Modals-->

    <!--begin::Javascript-->
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->

    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->

    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/projects/tracker/new.js"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>