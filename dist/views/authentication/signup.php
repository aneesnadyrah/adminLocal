
<?php 
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
include "config/system.php";
$system = new System;
?>

<!DOCTYPE html>
<html lang="ms">
<!--begin::Head-->

<head>
	<base href="../../" />
	<title><?= $system->App->title; ?></title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="shortcut icon" href="assets/media/logos/<?= $system->App->title; ?>-small.svg" />
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

		<!--begin::Authentication - Sign-up -->
		<div class="d-flex flex-column flex-lg-row flex-column-fluid">
			<!--begin::Aside-->
			<div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
				<!--begin::Wrapper-->
				<div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-900px">
					<!--begin::Header-->
					<div class="d-flex flex-end py-2">
						<!--begin::Sign Up link-->
						<div class="m-0">
							<span class="text-gray-400 fw-bold fs-5 me-2" data-kt-translate="sign-up-head-desc">Telah
								daftar akaun?</span>
							<a href="auth/signin" class="link-primary fw-bold fs-5" data-kt-translate="sign-up-head-link">Log masuk disini</a>
						</div>
						<!--end::Sign Up link=-->
					</div>
					<!--end::Header-->
					<!--begin::Body-->
					<div class="py-20 pt-0">
						<!--begin::Logo-->
						<a href="/" class="py-20">
							<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title);?>-default.svg" class="theme-light-show h-45px" />
							<img alt="Logo" src="assets/media/logos/<?= strtolower($system->App->title);?>-dark.svg" class="theme-dark-show h-45px" />
						</a>
						<!--end::Logo-->
						<!--begin::Stepper-->
						<div class="stepper stepper-pills stepper-column d-flex flex-column flex-xl-row flex-row-fluid" id="register_stepper">
							<!--begin::Aside-->
							<div class="d-flex justify-content-start flex-row-auto w-100 w-xl-250px mt-20">
								<!--begin::Nav-->
								<div class="stepper-nav">
									<!--begin::Step 1-->
									<div class="stepper-item current" data-kt-stepper-element="nav">
										<!--begin::Wrapper-->
										<div class="stepper-wrapper">
											<!--begin::Icon-->
											<div class="stepper-icon w-40px h-40px">
												<i class="stepper-check fas fa-check"></i>
												<span class="stepper-number">1</span>
											</div>
											<!--end::Icon-->
											<!--begin::Label-->
											<div class="stepper-label">
												<h3 class="stepper-title">Semakan</h3>
												<div class="stepper-desc">No Kad Pengenalan</div>
											</div>
											<!--end::Label-->
										</div>
										<!--end::Wrapper-->
										<!--begin::Line-->
										<div class="stepper-line h-40px"></div>
										<!--end::Line-->
									</div>
									<!--end::Step 1-->
									<!--begin::Step 2-->
									<div class="stepper-item" data-kt-stepper-element="nav">
										<!--begin::Wrapper-->
										<div class="stepper-wrapper">
											<!--begin::Icon-->
											<div class="stepper-icon w-40px h-40px">
												<i class="stepper-check fas fa-check"></i>
												<span class="stepper-number">2</span>
											</div>
											<!--begin::Icon-->
											<!--begin::Label-->
											<div class="stepper-label">
												<h3 class="stepper-title">Maklumat Peribadi</h3>
												<div class="stepper-desc">Butiran Kakitangan</div>
											</div>
											<!--begin::Label-->
										</div>
										<!--end::Wrapper-->
										<!--begin::Line-->
										<div class="stepper-line h-40px"></div>
										<!--end::Line-->
									</div>
									<!--end::Step 2-->
									<!--begin::Step 3-->
									<div class="stepper-item" data-kt-stepper-element="nav">
										<!--begin::Wrapper-->
										<div class="stepper-wrapper">
											<!--begin::Icon-->
											<div class="stepper-icon w-40px h-40px">
												<i class="stepper-check fas fa-check"></i>
												<span class="stepper-number">3</span>
											</div>
											<!--end::Icon-->
											<!--begin::Label-->
											<div class="stepper-label">
												<h3 class="stepper-title">Maklumat Akaun</h3>
												<div class="stepper-desc">Butiran Log Masuk Akaun</div>
											</div>
											<!--end::Label-->
										</div>
										<!--end::Wrapper-->
										<!--begin::Line-->
										<div class="stepper-line h-40px"></div>
										<!--end::Line-->
									</div>
									<!--end::Step 3-->
									<!--begin::Step 4-->
									<div class="stepper-item mark-completed" data-kt-stepper-element="nav">
										<!--begin::Wrapper-->
										<div class="stepper-wrapper">
											<!--begin::Icon-->
											<div class="stepper-icon w-40px h-40px">
												<i class="stepper-check fas fa-check"></i>
												<span class="stepper-number">4</span>
											</div>
											<!--end::Icon-->
											<!--begin::Label-->
											<div class="stepper-label">
												<h3 class="stepper-title">Selesai</h3>
												<div class="stepper-desc">Mendaftar Akaun</div>
											</div>
											<!--end::Label-->
										</div>
										<!--end::Wrapper-->
									</div>
									<!--end::Step 4-->
								</div>
								<!--end::Nav-->
							</div>
							<!--begin::Aside-->

							<!--begin::Content-->
							<div class="flex-row-fluid py-lg-5 px-lg-10 mt-20">
								<!--begin::Form-->
								<form class="form" novalidate="novalidate" id="register_form">
									<!--begin::Step 1-->
									<div class="current" data-kt-stepper-element="content">
										<div class="w-100">
											<!--begin::Input group-->
											<div class="fv-row mb-10">
												<!--begin::Label-->
												<label class="d-flex align-items-center fs-5 fw-semibold mb-2">
													<span class="required">No Kad Pengenalan</span>
													<i class="fad fa-exclamation-circle ms-2 fs-4" data-bs-toggle="tooltip" title="Sila isi nombor Kad Pengenalan tanpa sempang"></i>
												</label>
												<!--end::Label-->
												<!--begin::Input-->
												<input type="text" class="form-control form-control-lg form-control-solid" name="ID_card_no" maxlength="12" autocomplete="off" />
												<!--end::Input-->
											</div>
											<!--end::Input group-->
										</div>
									</div>
									<!--end::Step 1-->
									<!--begin::Step 2-->
									<div data-kt-stepper-element="content">
										<div class="w-100">
											<!--begin::Label-->
											<label class="d-flex align-items-center fs-5 fw-semibold mb-2">
												<span class="required">Maklumat Pengenalan Diri</span>
												<i class="fad fa-exclamation-circle ms-2 fs-4" data-bs-toggle="tooltip" title="Pastikan maklumat di bawah adalah benar"></i>
											</label>
											<!--end::Label-->
											<div class="row mb-5">
												<div class="col-md-6 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="first-name" name="first-name" placeholder="Nama Awal">
														<label for="first-name">Nama Awal</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-6 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="last-name" name="last-name" placeholder="Nama Akhir" />
														<label for="last-name">Nama Akhir</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-6 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="position" name="position" placeholder="Jawatan" readonly />
														<label for="position">Jawatan</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-6 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="phone-no" name="phone-no" placeholder="No Telefon Bimbit" />
														<label for="phone-no">No Telefon Bimbit</label>
													</div>
													<!--end::Input group-->
												</div>
											</div>
											<!--begin::Label-->
											<label class="d-flex align-items-center fs-5 fw-semibold mb-2">
												<span class="required">Alamat Surat Menyurat</span>
												<i class="fad fa-exclamation-circle ms-2 fs-4" data-bs-toggle="tooltip" title="Sila isi alamat kediaman semasa anda."></i>
											</label>
											<!--end::Label-->
											<div class="row mb-5">
												<div class="col-md-12 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="first-address" name="first-address" placeholder="Alamat Pertama" />
														<label for="first-address">Alamat Pertama</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-12 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="second-address" name="second-address" placeholder="Alamat Kedua" />
														<label for="second-address">Alamat Kedua</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-4 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="postcode" name="postcode" placeholder="Poskod" />
														<label for="postcode">Poskod</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-4 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="city" name="city" placeholder="Bandar" />
														<label for="city">Bandar</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-4 fv-row">
													<!--begin::Input group-->
													<div class="form-floating">
														<input type="text" class="form-control form-control-solid" id="state" name="state" placeholder="Negeri" />
														<label for="state">Negeri</label>
													</div>
													<!--end::Input group-->
												</div>
											</div>
										</div>
									</div>
									<!--end::Step 2-->
									<!--begin::Step 3-->
									<div data-kt-stepper-element="content">
										<div class="w-100">
											<!--begin::Label-->
											<label class="d-flex align-items-center fs-5 fw-semibold mb-2">
												<span class="required">Maklumat Akaun</span>
												<i class="fad fa-exclamation-circle ms-2 fs-4" data-bs-toggle="tooltip" title="Buka aplikasi Telegram anda dan lakukan carian @kuttsb_bot dan taip /start untuk mengaktifkan notifikasi sistem. Seterusnya lakukan carian @userinfobot dan taip /start untuk mendapatkan ID Telegram anda."></i>
											</label>
											<!--end::Label-->
											<div class="row mb-4">
												<div class="col-md-6 fv-row">
													<!--begin::Input group-->
													<div class="form-floating mb-3">
														<input type="text" class="form-control form-control-solid" id="telegram-id" name="telegram-id" autocomplete="off" placeholder="ID Telegram">
														<label for="telegram-id">ID Telegram</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-6 fv-row">
													<!--begin::Input group-->
													<div class="form-floating mb-3">
														<input type="text" class="form-control form-control-solid" id="email" name="email" placeholder="Nama Pengguna" />
														<label for="email">Alamat E-mel</label>
													</div>
													<!--end::Input group-->
												</div>
												<div class="col-md-12 fv-row">
													<!--begin::Input group-->
													<div class="form-floating mb-3">
														<input type="text" class="form-control form-control-solid" id="username" name="username" autocomplete="off" placeholder="Nama Pengguna" />
														<label for="username">Nama pengguna (Username)</label>
													</div>
													<!--end::Input group-->
												</div>
												<!--begin::Input group-->
												<div class="col-md-12 fv-row" data-kt-password-meter="true">
													<!--begin::Wrapper-->
													<div class="mb-1">
														<!--begin::Input wrapper-->
														<div class="form-floating position-relative mb-3">
															<input class="form-control form-control-solid" type="password" name="password" autocomplete="off" placeholder="Kata Laluan" />
															<span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
																<i class="fad fa-eye-slash fs-4"></i>
																<i class="fad fa-eye fs-4 d-none"></i>
															</span>
															<label for="password">Kata Laluan</label>
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
												<div class="mb-10 col-md-12 fv-row">
													<div class="form-floating">
														<input class="form-control form-control-lg form-control-solid" type="password" name="confirm-password" autocomplete="off" placeholder="Pengesahan Kata Laluan" />
														<label for="password">Pengesahan Kata Laluan</label>
													</div>
												</div>
												<!--end::Input group=-->
											</div>
										</div>
									</div>
									<!--end::Step 3-->
									<!--begin::Step 4-->
									<div data-kt-stepper-element="content">
										<div class="w-100 text-center">
											<!--begin::Heading-->
											<h1 class="fw-bold text-dark mb-3">Tahniah, Berjaya! 🎉</h1>
											<!--end::Heading-->
											<!--begin::Description-->
											<div class="text-muted fw-semibold fs-4">anda akan menerima kod pengesahan melalui telegram untuk mengaktifkan akaun <?= $system->App->title;?>.</div>
											<!--end::Description-->
											<!--begin::Illustration-->
											<div class="text-center px-4 py-10">
												<img src="assets/media/illustrations/rafiki/done.svg" alt="" class="mw-100 mh-400px" />
											</div>
											<!--end::Illustration-->
										</div>
									</div>
									<!--end::Step 4-->
									<!--begin::Actions-->
									<div class="d-flex flex-stack pt-10">
										<!--begin::Wrapper-->
										<div class="me-2">
											<button type="button" class="btn btn-lg btn-light-primary me-3" data-kt-stepper-action="previous">
											<i class="fad fa-arrow-left ms-0 me-3 fs-6"></i>Kembali</button>
										</div>
										<!--end::Wrapper-->
										<!--begin::Wrapper-->
										<div>
											<button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="submit">
												<span class="indicator-label">Daftar
													<i class="fad fa-arrow-right ms-3 me-0 fs-6"></i>
												</span>
												<span class="indicator-progress">Sila Tunggu...
													<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
											</button>
											<button type="button" class="d-none btn btn-lg btn-primary" data-kt-stepper-action="next">Seterusnya
												<i class="fad fa-arrow-right ms-3 me-0 fs-6"></i>
											</button>
											<button type="button" class="btn btn-lg btn-primary" data-kt-stepper-action="validate">
												<span class="indicator-label">Semak
													<i class="fad fa-arrow-right ms-3 me-0 fs-6"></i>
												</span>
												<span class="indicator-progress">Sila Tunggu...
													<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
											</button>
										</div>
										<!--end::Wrapper-->
									</div>
									<!--end::Actions-->
								</form>
								<!--end::Form-->
							</div>
							<!--end::Content-->

						</div>
						<!--end::Stepper-->
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
		<!--end::Authentication - Sign-up-->
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
	<script src="assets/js/custom/authentication/sign-up/signup.js"></script>
	<!--end::Custom Javascript-->
	<!--end::Javascript-->
</body>
<!--end::Body-->

</html>