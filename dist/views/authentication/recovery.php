
<?php 
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
// include "config/tenant.php";
require_once "config/system.php";
$system = new System;
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
			<!--begin::Authentication - Password reset -->
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				<!--begin::Logo-->
				<a href="../../index.html" class="d-block d-lg-none mx-auto py-20">
					<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default.svg" class="theme-light-show h-25px" />
					<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default-dark.svg" class="theme-dark-show h-25px" />
				</a>
				<!--end::Logo-->
				<!--begin::Aside-->
				<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
					<!--begin::Wrapper-->
					<div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-450px">
						<!--begin::Header-->
						<div class="d-flex flex-end py-2">

							<!--begin::Sign Up link-->
							<div class="m-0">
								<span class="text-gray-400 fw-bold fs-5 me-2" data-kt-translate="password-reset-head-desc">Akaun telah berdaftar ?</span>
								<a href="authentication/sign-in.php" class="link-primary fw-bold fs-5" data-kt-translate="password-reset-head-link">Log Masuk</a>
							</div>
							<!--end::Sign Up link=-->
						</div>
						<!--begin::Body-->
					<div class="py-20">
						<!--begin::Logo-->
						<a href="index.php" class="d-flex d-md-block py-10 justify-content-center">
							<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title) ?>-default.svg" class="theme-light-show h-45px" />
							<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title) ?>-dark.svg" class="theme-dark-show h-45px" />
						</a>
						<!--end::Logo-->
						<!--begin::Form-->
							<form class="form w-100" novalidate="novalidate" id="form-recovery-password">
								<!--begin::Heading-->
								<div class="text-start mb-3">
									<!--begin::Title-->
									<h1 class="text-dark mb-3 fs-3x">Set Kata Laluan Baharu</h1>
									<!--end::Title-->
									<!--begin::Sub-title-->
									<div class="text-muted fw-semibold fs-5 mb-5" id="masked-title">
										Masukkan Kod Keselamatan yang telah kami hantar pada</div>
									<!--end::Sub-title-->
									<!--begin::Mobile no-->
									<div class="fw-bold text-dark fs-1 text-center" id="masked-number"></div>
								<!--end::Mobile no-->
								</div>
								<!--end::Heading-->
								<!--begin::Section-->
							<div class="mb-10">
								<!--begin::Label-->
								<div class="fw-bold text-start text-dark fs-6 mb-1 ms-1" id="masked-subtitle">Taip Kod Keselamatan 6 digit anda</div>
								<!--end::Label-->
								<!--begin::Input group-->
								<div class="d-flex flex-wrap flex-stack" data-stepper="validate">
									<input type="text" name="code_1" 
										maxlength="1"
										class="form-control form-control-solid h-60px w-60px fs-2qx text-center border-primary border-hover mx-1 my-2"
										value="" />
									<input type="text" name="code_2" 
										maxlength="1"
										class="form-control form-control-solid h-60px w-60px fs-2qx text-center border-primary border-hover mx-1 my-2"
										value="" />
									<input type="text" name="code_3" 
										maxlength="1"
										class="form-control form-control-solid h-60px w-60px fs-2qx text-center border-primary border-hover mx-1 my-2"
										value="" />
									<input type="text" name="code_4" 
										maxlength="1"
										class="form-control form-control-solid h-60px w-60px fs-2qx text-center border-primary border-hover mx-1 my-2"
										value="" />
									<input type="text" name="code_5" 
										maxlength="1"
										class="form-control form-control-solid h-60px w-60px fs-2qx text-center border-primary border-hover mx-1 my-2"
										value="" />
									<input type="text" name="code_6" 
										maxlength="1"
										class="form-control form-control-solid h-60px w-60px fs-2qx text-center border-primary border-hover mx-1 my-2"
										value="" />
								</div>
								<!--end::Input group-->
								<div class="d-none" data-stepper="submit">
									<!--begin::Input group-->
									<div class="mb-10 fv-row" data-kt-password-meter="true" >
										<!--begin::Wrapper-->
										<div class="mb-1">
											<!--begin::Input wrapper-->
											<div class="position-relative mb-3">
												<input class="form-control form-control-lg form-control-solid" type="password" placeholder="Password" name="password" autocomplete="off" data-kt-translate="new-password-input-password" />
												<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
													<i class="fad fa-eye-slash fs-4"></i>
													<i class="fad fa-eye fs-4 d-none"></i>
												</span>
											</div>
											<!--end::Input wrapper-->
											<!--begin::Meter-->
											<div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
												<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
												<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
												<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
												<div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
											</div>
											<!--end::Meter-->
										</div>
										<!--end::Wrapper-->
										<!--begin::Hint-->
										<div class="text-muted mb-2" data-kt-translate="new-password-hint">Gunakan 8 atau lebih aksara dengan gabungan huruf, nombor & simbol.</div>
										<!--end::Hint-->
									</div>
									<!--end::Input group=-->
									<!--begin::Input group=-->
									<div class="fv-row mb-10">
										<input class="form-control form-control-lg form-control-solid" type="password" placeholder="Confirm Password" name="confirm-password" autocomplete="off" data-kt-translate="new-password-confirm-password" />
									</div>
									<!--end::Input group=-->
								</div>
							</div>
							<!--end::Section-->
								<!--begin::Actions-->
								<div class="d-flex flex-end">
									<!--begin::Link-->
									<button id="validate-recovery-password" class="btn btn-primary me-2">
										<!--begin::Indicator label-->
										<span class="indicator-label">Pengesahan</span>
										<!--end::Indicator label-->
										<!--begin::Indicator progress-->
										<span class="indicator-progress">Sila Tunggu...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										<!--end::Indicator progress-->
									</button>
									<!--end::Link-->
									<!--begin::Link-->
									<button id="submit-recovery-password" class="btn btn-primary d-none">
										<!--begin::Indicator label-->
										<span class="indicator-label">Hantar</span>
										<!--end::Indicator label-->
										<!--begin::Indicator progress-->
										<span class="indicator-progress">Sila Tunggu...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										<!--end::Indicator progress-->
									</button>
									<!--end::Link-->
								</div>
								<!--end::Actions-->
							</form>
							<!--end::Form-->
						</div>
						<!--end::Body-->
						<!--begin::Footer-->
						<div class="m-0">
							<!--begin::Toggle-->
							<button class="btn btn-flex btn-link rotate" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" data-kt-menu-offset="0px, 0px">
								<img data-kt-element="current-lang-flag" class="w-25px h-25px rounded-circle me-3" src="assets/media/flags/united-states.svg" alt="" />
								<span data-kt-element="current-lang-name" class="me-2">English</span>
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
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="English">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/united-states.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">English</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="Spanish">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/spain.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">Spanish</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="German">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/germany.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">German</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="Japanese">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/japan.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">Japanese</span>
									</a>
								</div>
								<!--end::Menu item-->
								<!--begin::Menu item-->
								<div class="menu-item px-3">
									<a href="#" class="menu-link d-flex px-5" data-kt-lang="French">
										<span class="symbol symbol-20px me-4">
											<img data-kt-element="lang-flag" class="rounded-1" src="assets/media/flags/france.svg" alt="" />
										</span>
										<span data-kt-element="lang-name">French</span>
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
			<!--end::Authentication - New password-->
		</div>
		<!--end::Root-->
		<!--end::Main-->
		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Custom Javascript(used for this page only)-->
		<script src="assets/js/custom/authentication/reset-password/new-password.js"></script>
		<script src="assets/js/custom/authentication/sign-in/i18n.js"></script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
	</body>
	<!--end::Body-->
</html>
