<!-- survey-signin.php -->
<!--begin::Body-->
<div class="py-20">
    <!--begin::Logo-->
    <div class="d-flex d-md-block py-10 justify-content-center">
        <img alt="" src="assets/media/logos/<?php echo $appsTitle ?>-default.svg" class="theme-light-show h-45px" />
        <img alt="" src="assets/media/logos/<?php echo $appsTitle ?>-dark.svg" class="theme-dark-show h-45px" />
    </div>
    <!--end::Logo-->
    <!--begin::Form-->
    <form class="form w-100" novalidate="novalidate" id="sign_in_form">
        <!--begin::Body-->
        <div class="card-body">

            <!--begin::Heading-->
            <div class="text-start mb-10">
                <!--begin::Title-->
                <h1 class="text-dark mb-3 fs-2x" data-kt-translate="sign-in-title">Daftar Masuk</h1>
                <!--end::Title-->
                <!--begin::Text-->
                <div class="text-gray-400 fw-semibold fs-6" data-kt-translate="general-desc">Daftar Masuk Kumpulan Ukur</div>
                <!--end::Link-->
            </div>
            <!--begin::Heading-->
            <!--begin::Input group=-->
            <div class="fv-row mb-8">
                <!--begin::Email-->
                <input type="text" placeholder="Nama Pengguna" name="username" autocomplete="off" data-kt-translate="sign-in-input-email" class="form-control form-control-solid" />
                <input type="hidden"  name="systemId" value="<?php $systemId = $_GET['sid']; echo $systemId;  ?>" />
                <!--end::Email-->
            </div>
            <!--end::Input group=-->
            <div class="fv-row mb-7">
                <!--begin::Password-->
                <input type="password" placeholder="Kata Laluan" name="password" autocomplete="off" data-kt-translate="sign-in-input-password" class="form-control form-control-solid" />
                <!--end::Password-->
            </div>
            <!--end::Input group=-->

            <!--begin::Actions-->
            <div class="d-flex flex-stack">
                <!--begin::Submit-->
                <button id="sign_in_submit" class="btn btn-primary me-2 flex-shrink-0">
                    <!--begin::Indicator label-->
                    <span class="indicator-label" data-kt-translate="sign-in-submit">Daftar Masuk</span>
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
                <!-- <div class="fw-semibold"> -->
                    <!-- <div></div> -->
                    <!--begin::Link-->
                    <!-- <a href="authentication/reset-password.php" class="link-primary" data-kt-translate="sign-in-forgot-password">Terlupa Kata Laluan?</a> -->
                    <!--end::Link-->
                <!-- </div> -->
                <!--end::Wrapper-->
            </div>
            <!--end::Actions-->
        </div>
        <!--begin::Body-->
    </form>
    <!--end::Form-->
</div>
<!--end::Body-->