<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/tenant.php";
include "config/autoload.php";
?>


<!DOCTYPE html>
<html lang="ms">
<!--begin::Head-->

<head>
    <base href="../../../../" />
    <title><?php echo $appsTitle ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/<?php echo $appsTitle ?>-small.svg" />
    <!--begin::Fonts(mandatory for all pages)-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Vendor Stylesheets(used for this page only)-->
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <!-- <style>
        .justify-text {
            text-align: justify;
            text-justify: inter-word;
        }

        @media print {
            @page {
                size: A4;
                margin-top: 30mm;
                margin-bottom: 20mm;
                margin-left: 25mm;
                margin-right: 25mm;
            }

            /* body {
                margin-top: 0;
                margin-bottom: 0; 
            } */

            header {
                display: none;
            }

            .content-container {
                margin-top: 13mm; /* Add desired space between top and title */
            }

            .page-number {
                position: fixed;
                bottom: 8mm; /* Adjust the vertical position of the page number */
                right: 1mm; /* Adjust the horizontal position of the page number */
                text-align: right; /* Change the alignment to right */
                font-size: 12px;
            }

            .page-break {
                page-break-after: always;
            }
        }
        .underline-td {
            text-decoration: underline;
        }
        .italic-text {
            font-style: italic;
        }
        .content-check {
            height: 110px; /* Add desired space between top and title */
        }
    </style> -->

    <!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
	<!--begin::loader-->
	<!-- <div class="page-loader flex-column d-print-none">
		<img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?php echo $appsTitle ?>-default.svg" />
		<img alt="Logo" class="theme-dark-show h-40px" src="assets/media/logos/<?php echo $appsTitle ?>-dark.svg" />
		<div class="d-flex align-items-center mt-5">
			<span class="spinner-border text-primary" role="status"></span>
		</div>
	</div> -->
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
    <div class="d-flex flex-column flex-root d-print-none">
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
                            include "components/views/letter-reviews-wyFeedback.php";
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
    <?php foreach ($drawers as $drawer) {
        include $drawer;
    } ?>
    <!--end::Drawers-->
    <!--end::Main-->
    <!--begin::Modals-->
    <!--begin::Modal - Create App-->
    <?php foreach ($generalModals as $modal) {
        include $modal;
    } ?>
    <?php foreach ($actionModals as $modal) {
        include $modal;
    } ?>

    <!--end::Modals-->
    <!--begin::Javascript-->
    <script>
        var tenant = <?php echo json_encode($appsTitle); ?>;
    </script>
    <!--begin::Global Javascript Bundle(mandatory for all pages)-->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Vendors Javascript(used for this page only)-->
    <!-- <script src="assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script> -->
    <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--end::Vendors Javascript-->
    <!--begin::Custom Javascript(used for this page only)-->
    <script src="assets/js/custom/utilities/sidebar/pin-projects.js"></script>
    <script async src="assets/js/custom/projects/wayleave/wy-feedback.js"></script>
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>