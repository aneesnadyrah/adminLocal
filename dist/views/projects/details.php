<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "config/autoload.php";

// Auto-detect which class to use based on system_id
$systemId = $_GET['id'] ?? NULL;
$roleId = $_SESSION["roleId"];

// Use our unified class instead of separate Details/DetailsV1
$details = new ProjectDetails();
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
	<link href="assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
	<link rel="stylesheet" href="assets/plugins/custom/jstree/jstree.bundle.css" />
	<link rel="stylesheet" href="assets/plugins/custom/swiper/swiper.bundle.css" />
	<!--end::Vendor Stylesheets-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	<script>var hostUrl = "assets/";</script>
	<!--begin::Global Javascript Bundle(mandatory for all pages)-->
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used for this page only)-->
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
	<script src="assets/plugins/custom/jstree/jstree.bundle.js"></script>
	<!--end::Vendors Javascript-->
	<style>
		.doc-content{
			display: none;
		}
		.doc-content.current{
			display: inherit;
		}

		.card .officer {
			max-height: 100px; /* Example height, adjust as needed */
			overflow: hidden;
			margin: 0;
			padding: 0;
			display: flex;
			flex-direction: column;
			flex: 1
		}

		.timeline .timeline-line {
			display: block;
			content: " ";
			justify-content: center;
			position: absolute;
			z-index: 0;
			left: 0;
			top: 38px;
			bottom: 0;
			transform: translate(50%);
			border-left-width: 1px;
			border-left-style: solid;
			border-left-color: #DBDFE9;
			width: 38px;
		}

		.timeline .timeline-icon {
			z-index: 1;
			flex-shrink: 0;
			margin-right: 1rem;
			width: 38px;
			height: 38px;
			display: flex;
			text-align: center;
			align-items: center;
			justify-content: center;
			border: 1px solid var(--bs-gray-300);
			border-radius: 50%;
		}
    </style>
	<!--end::Global Stylesheets Bundle-->

	<!-- Swiper CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/freeps2/a7rarpress@main/swiper-bundle.min.css">

	<style>
		.overlay{
		position: absolute;
		left: 0;
		top: 0;
		height: 100%;
		width: 100%;
		background-color: #4070F4;
		border-radius: 25px 25px 0 25px;
		}
		.overlay::before,
		.overlay::after{
		content: '';
		position: absolute;
		right: 0;
		bottom: -40px;
		height: 40px;
		width: 40px;
		background-color: #4070F4;
		}
		.overlay::after{
		border-radius: 0 25px 0 0;
		background-color: #FFF;
		}
		.swiper-navBtn{
			color: var(--bs-text-gray-400);
			transition: color 0.3s ease;
		}
		.swiper-navBtn:hover{
			color: var(--bs-text-primary);
		}
		.swiper-navBtn::before,
		.swiper-navBtn::after{
			font-size: 20px;
			margin-bottom: 35px;
		}
		.swiper-button-next{
		right: 0;
		}
		.swiper-button-prev{
		left: 0;
		}
		.swiper-pagination-bullet{
		background-color: var(--bs-text-gray-400);
		opacity: 1;
		}
		.swiper-pagination-bullet-active{
		background-color: var(--bs-text-primary);
		}

		@media screen and (max-width: 768px) {
		.slide-content{
			margin: 0 10px;
		}
		.swiper-navBtn{
			display: none;
		}
		}
	</style>
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
						<!-- 
							This automatically detects version and renders appropriate content
							- If system_id exists in flw_appl_entries: uses V1 templates
							- If system_id exists in view_tasks: uses original templates
							- Seamlessly works with both old and new system IDs
						-->
						<?php $details->details($systemId); ?>
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
	<!--end::Main-->

	<!--begin::Modals-->
	<?php include General::getModal("cancel-application") ?>
	<?php include General::getModal("jump-step-application") ?>
	<!--end::Modals-->

	<!--begin::Javascript-->

	<!--begin::Custom Javascript(used for this page only)-->
	<script src="assets/js/widgets.bundle.js"></script>
	<script src="assets/js/custom/utilities/sidebar/pin-projects.js"></script>
	<script src="assets/js/custom/cancel-application.js"></script>
	<script src="assets/js/custom/jump-step-application.js"></script>


	<!-- Swiper JS - Combined from both versions -->
	<script src="assets/plugins/custom/swiper/swiper.bundle.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<script>
		var swiper = new Swiper(".mySwiper", {
			slidesPerView: 3,
			spaceBetween: 25,
			loop: true,
			centerSlide: 'true',
			fade: 'true',
			grabCursor: 'true',
			pagination: {
				el: ".swiper-pagination",
				clickable: true,
				dynamicBullets: true,
				enabled: false
			},
			navigation: {
				nextEl: ".swiper-button-next",
				prevEl: ".swiper-button-prev",
			},
			breakpoints:{
				0: {
					slidesPerView: 1,
				},
				520: {
					slidesPerView: 2,
				},
				950: {
					slidesPerView: 3,
				},
			},
		});
	</script>
	<!--end::Custom Javascript-->
	<!--end::Javascript-->
</body>
<!--end::Body-->
</html>