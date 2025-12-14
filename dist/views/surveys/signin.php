<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once "config/system.php";
require_once "config/functions.php";
require_once "config/functions/survey.php";
// require_once "config/autoload.php";
$system = new System;

$tenant = $system->App->tenant;
$currentURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$systemId = $_GET['sid'];

$currentQR = Survey::getCurrentQR($systemId);

if ($currentQR !== null && $currentURL !== null && strtolower($currentQR) == strtolower($currentURL)) {
    $active = true;
} else {
    $active = false;
}

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

<!-- <body id="kt_body" style="background-image: url()" data-kt-aside-minimize="on" class="page-loading-enabled page-loading header-fixed header-tablet-and-mobile-fixed aside-fixed aside-secondary-enabled"> -->
<body id="kt_body" class="app-blank">
	<!--begin::loader-->
		<!-- <div class="page-loader flex-column">
			<img alt="Logo" class="theme-light-show h-40px" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default.svg" />
		<img alt="Logo" class="theme-dark-show h-40px"
			src="assets/media/logos/<?= strtolower($system->App->title) ?>-dark.svg" />
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
	<div class="d-flex flex-column flex-root">
		<!--begin::Authentication - Sign-in -->
		<div class="d-flex flex-column flex-lg-row flex-column-fluid">
			<!--begin::Aside-->
			<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
				<!--begin::Wrapper-->
				<div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-450px">
					<!--begin::Body-->
					<!-- Include the survey-signin component -->
					<?php
					if ($active) {
						include("components/views/survey-signin.php");
					} else {
						echo "<div style='text-align: center; display: flex; flex-direction: column; align-items: center; margin-top: 10px;'>
								<span style='font-size: 22px;'>Kod QR semasa telah tamat tempoh😞 </br>  Sila imbas kod QR baharu.</span>
							  </div>";
					}
					?>
					<!--end::Body-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Aside-->
		</div>
		<!--end::Authentication - Sign-in-->
	</div>
	<!--end::Root-->
	<!--end::Main-->
	<!--begin::Javascript-->
	<script>
		var hostUrl = "assets/"; 
		var sid = "<?php echo $systemId; ?>";
		var activeStatus = <?php if ($active) {
						echo "true";
					} else {
						echo "false";
					} ?>;
	</script>
	<!--begin::Global Javascript Bundle(mandatory for all pages)-->
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<!--end::Global Javascript Bundle-->
	<!--begin::Custom Javascript(used for this page only)-->
	<script src="assets/js/custom/survey/sign-in/general.js"></script>
	<script src="assets/js/custom/survey/sign-in/i18n.js"></script>
	<!--end::Custom Javascript-->
	<!--end::Javascript-->
</body>
<!--end::Body-->

</html>