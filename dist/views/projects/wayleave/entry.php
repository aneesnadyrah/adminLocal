<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

include "config/system.php";
include "config/autoload.php";
include "config/functions.php";

if (!empty($systemId)) {
	// $e= ApplicationEntry::viewEntry($systemId,0);
	// $r= ApplicationEntry::viewRoad($systemId, '-1');
	// $s= ApplicationEntry::viewStatus($systemId);
	// $c= ApplicationEntry::contactDetails($systemId);

	$wayleaveEntry = new wayleaveEntry();
	$e = $wayleaveEntry->getDataEntry($systemId);
	$r = $wayleaveEntry->getDataRoad($systemId);
	
	$system = new System;
	$appsTitle = $system->App->title;
}

?>

<!DOCTYPE html>
<html lang="ms">
<!--begin::Head-->

<head>

	<base href="../../../" />
	<title><?php echo $appsTitle ?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" href="assets/media/logos/<?php echo $appsTitle ?>-small.svg" />
	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Vendor Stylesheets(used for this page only)-->
	<!--end::Vendor Stylesheets-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	<!--end::Global Stylesheets Bundle-->
</head>
<!--end::Head-->
<!--begin::Body-->
  
<body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled">
	<!--begin::loader-->
	<div class="page-loader flex-column">
		<img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?php echo $appsTitle ?>-default.svg" />
		<img alt="Logo" class="theme-dark-show h-40px" src="assets/media/logos/<?php echo $appsTitle ?>-dark.svg" />
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
						<!--begin::Row-->
							<?php include General::getForm('add-record') ?>
						<!--end::Row-->
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
		<?php include "components/partials/drawers/drawer-1.php" ?>
	<!--end::Drawers-->
	<!--end::Main-->

	<!--begin::Modals-->
		<?php
			foreach($taskModals as $modal) {
				include $modal;
			}
		?>
		<?php
			foreach($generalModals as $modal) {
				include $modal;
			}
		?>
	<!--end::Modals-->

	<script>
		var hostUrl = "assets/";
	</script>
	<!--begin::Global Javascript Bundle(mandatory for all pages)-->
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<!--end::Global Javascript Bundle-->
	<!--begin::Vendors Javascript(used for this page only)-->
	<script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
	<!--end::Vendors Javascript-->
	<!--begin::Custom Javascript(used for this page only)-->
	<script src="assets/js/widgets.bundle.js"></script>
	<script src="assets/js/custom/widgets.js"></script>
	<script src="assets/js/custom/apps/chat/chat.js"></script>
	<script src="assets/js/custom/utilities/modals/create-app.js"></script>
	<script src="assets/js/custom/pages/projects/add-project.js"></script>
	<script src="assets/js/custom/utilities/sidebar/pin-projects.js"></script>
	<!--end::Custom Javascript-->
</body>
<!--end::Body-->

</html>