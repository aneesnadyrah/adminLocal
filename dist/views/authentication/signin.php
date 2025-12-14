<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
include "config/system.php";
$system = new System;
session_start();

if($_SESSION){
	header("location:/dashboard");
}

?>

<!DOCTYPE html>
<html lang="ms">
<!--begin::Head-->

<head>
	<base href="../../" />
	<title><?= $system->App->title; ?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" href="assets/media/logos/<?= strtolower($system->App->title);?>-small.svg" />
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
					<!--begin::Header-->
					<div class="d-flex flex-end py-2">
						<!--begin::Sign Up link-->
						<div class="m-0">
							<span class="text-gray-400 fw-bold fs-5 me-2" data-kt-translate="sign-in-head-desc">Tidak Mempunyai akaun?</span>
							<a href="auth/signup.php" class="link-primary fw-bold fs-5" data-kt-translate="sign-in-head-link">Daftar disini</a>
						</div>
						<!--end::Sign Up link=-->
					</div>
					<!--end::Header-->
					<!--begin::Body-->
					<div class="py-20">
						<!--begin::Logo-->
						<a href="index.php" class="d-flex d-md-block py-10 justify-content-center">
							<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title);?>-default.svg" class="theme-light-show h-45px" />
							<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title);?>-dark.svg" class="theme-dark-show h-45px" />
						</a>
						<!--end::Logo-->
						<!--begin::Form-->
						<form class="form w-100" novalidate="novalidate" id="sign_in_form">
							<!--begin::Body-->
							<div class="card-body">

								<!--begin::Heading-->
								<div class="text-start mb-10">
									<!--begin::Title-->
									<h1 class="text-dark mb-3 fs-2x" data-kt-translate="sign-in-title">Log Masuk</h1>
									<!--end::Title-->
									<!--begin::Text-->
									<div class="text-gray-400 fw-semibold fs-6" data-kt-translate="general-desc">Sistem Pengurusan <?= $system->App->company;?></div>
									<!--end::Link-->
								</div>
								<!--begin::Heading-->
								<!--begin::Input group=-->
								<div class="fv-row mb-8">
									<!--begin::Email-->
									<input type="text" placeholder="Nama Pengguna" name="username" autocomplete="off" data-kt-translate="sign-in-input-email" class="form-control form-control-solid" />
									<!--end::Email-->
								</div>
								<!--end::Input group=-->
								<div class="fv-row mb-7" data-kt-password-meter="true">
									<!--begin::Password-->
									<input type="password" placeholder="Kata Laluan" name="password" autocomplete="off" data-kt-translate="sign-in-input-password" class="form-control form-control-solid" />
									<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
										<i class="fad fa-eye-slash fs-4"></i>
										<i class="fad fa-eye fs-4 d-none"></i>
									</span>
									<!--end::Password-->
								</div>
								<!--end::Input group=-->

								<!--begin::Actions-->
								<div class="d-flex flex-stack">
									<!--begin::Submit-->
									<button id="sign_in_submit" class="btn btn-primary me-2 flex-shrink-0">
										<!--begin::Indicator label-->
										<span class="indicator-label" data-kt-translate="sign-in-submit">Log Masuk</span>
										<!--end::Indicator label-->
										<!--begin::Indicator progress-->
										<span class="indicator-progress">
											<span data-kt-translate="general-progress">Sila Tunggu...</span>
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
										</span>
										<!--end::Indicator progress-->
									</button>
									<!--end::Submit-->
									<!--begin::Wrapper-->
									<div class="fw-semibold">
										<div></div>
										<!--begin::Link-->
										<a href="auth/reset" class="link-primary" data-kt-translate="sign-in-forgot-password">Terlupa Kata Laluan?</a>
										<!--end::Link-->
									</div>
									<!--end::Wrapper-->
								</div>
								<!--end::Actions-->
							</div>
							<!--begin::Body-->
						</form>
						<!--end::Form-->
					</div>
					<!--end::Body-->
					<!--begin::Footer-->
					<div class="m-0">
						<!--begin::Toggle-->
						<button class="btn btn-flex btn-link rotate" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" data-kt-menu-offset="0px, 0px">
							<img data-kt-element="current-lang-flag" class="w-25px h-25px rounded-circle me-3" src="assets/media/flags/malaysia.svg" alt="" />
							<span data-kt-element="current-lang-name" class="me-2">Bahasa Melayu</span>
							<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
							<span class="svg-icon svg-icon-3 svg-icon-muted rotate-180 m-0">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
								</svg>
							</span>
							<!--end::Svg Icon-->
						</button>
						<!--end::Toggle-->
						<!--begin::Menu-->
						<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-4" data-kt-menu="true" id="kt_auth_lang_menu">
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="javascript:void()" class="menu-link d-flex px-5" data-kt-lang="Malay">
									<span class="symbol symbol-20px me-4">
										<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/malaysia.svg" alt="" />
									</span>
									<span data-kt-element="lang-name">Bahasa Melayu</span>
								</a>
							</div>
							<!--end::Menu item-->
							<!--begin::Menu item-->
							<div class="menu-item px-3">
								<a href="javascript:void()" class="menu-link d-flex px-5" data-kt-lang="English">
									<span class="symbol symbol-20px me-4">
										<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/united-kingdom.svg" alt="" />
									</span>
									<span data-kt-element="lang-name">English (UK)</span>
								</a>
							</div>
							<!--end::Menu item-->
						</div>
						<!--end::Menu-->
					</div>
					<!--end::Footer-->
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
	</script>
	<!--begin::Global Javascript Bundle(mandatory for all pages)-->
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<!--end::Global Javascript Bundle-->
	<!--begin::Custom Javascript(used for this page only)-->
	<script src="assets/js/custom/authentication/sign-in/login.js"></script>
	<!--end::Custom Javascript-->
	<!--end::Javascript-->
</body>
<!--end::Body-->

</html>