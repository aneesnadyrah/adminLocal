<?php   $username = $_SESSION['username']; 
        $s = Survey::getSurveyStaffData($username);
?>
<!--begin::Slider Widget 2-->
<div class="card card-flush card-stretch slide mb-10" data-bs-interval="5500">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <h4 class="card-title d-flex align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Aktiviti Hari Ini</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Daftar Masuk & Keluar</span>
        </h4>
        <!--end::Title-->
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body py-4">
        <div class="mb-15" >
        <!--begin::Wrapper-->
        <div class="d-flex align-items-center mb-10">
            <!--begin::Symbol-->
            <div class="symbol symbol-70px symbol-circle me-5">
                <img src="assets/media/avatars/<?= $s['profile_picture'] ?>.jpg" class="" alt="" />
            </div>
            <!--end::Symbol-->
            <!--begin::Info-->
            <div class="m-0">
                <!--begin::Subtitle-->
                <h4 class="fw-bold text-gray-800 mb-4">Kumpulan <?= $s['team_name'] ?></h4>
                <!--end::Subtitle-->

            </div>
            <!--end::Info-->
        </div>
        <!--end::Wrapper-->
        <!--begin::Item-->
        <div class="d-flex flex-stack ">
            <!--begin::Symbol-->
            <span class="symbol symbol-20px me-5">
                <span class="symbol-label bg-light-dark">
                    <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                    <span class="svg-icon svg-icon-2 svg-icon-gray">
                        <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                    </span>
                    <!--end::Svg Icon-->
                </span>
            </span>
            <!--end::Symbol-->
            <!--begin::Section-->
            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                <!--begin:Author-->
                <div class="flex-grow-1 me-2">
                    <span class="text-gray-600 fs-6 fw-semibold">Masa Daftar Masuk</span>
                    <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                </div>
                <!--end:Author-->
                <!--begin::Actions-->
                <div class="flex-grow-1 me-2">
                    <span class="d-flex align-items-end ms-10"><?= $s['clock_in'] ?></span>
                </div>
                <!--begin::Actions-->
            </div>
            <!--end::Section-->
        </div>
        <!--end::Item-->
        <!--begin::Separator-->
        <div class="separator separator-dashed my-5"></div>
        <!--end::Separator-->
        <!--begin::Item-->
        <div class="d-flex flex-stack ">
            <!--begin::Symbol-->
            <span class="symbol symbol-20px me-5">
                <span class="symbol-label bg-light-dark">
                    <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                    <span class="svg-icon svg-icon-2 svg-icon-gray">
                        <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                    </span>
                    <!--end::Svg Icon-->
                </span>
            </span>
            <!--end::Symbol-->
            <!--begin::Section-->
            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                <!--begin:Author-->
                <div class="flex-grow-1 me-2">
                    <span class="text-gray-600 fs-6 fw-semibold">Masa Daftar Keluar</span>
                    <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                </div>
                <!--end:Author-->
                <!--begin::Actions-->
                <div class="flex-grow-1 me-2">
                    <span class="d-flex align-items-end ms-10"><?= $s['clock_out'] ?></span>
                </div>
                <!--begin::Actions-->
            </div>
            <!--end::Section-->
        </div>
        <!--end::Item-->
        <!--begin::Separator-->
        <div class="separator separator-dashed my-5"></div>
        <!--end::Separator-->
        <!--begin::Item-->
        <div class="d-flex flex-stack ">
            <!--begin::Symbol-->
            <span class="symbol symbol-20px me-5">
                <span class="symbol-label bg-light-dark">
                    <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                    <span class="svg-icon svg-icon-2 svg-icon-gray">
                        <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                    </span>
                    <!--end::Svg Icon-->
                </span>
            </span>
            <!--end::Symbol-->
            <!--begin::Section-->
            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                <!--begin:Author-->
                <div class="flex-grow-1 me-2">
                    <span class="text-gray-600 fs-6 fw-semibold">Bilangan</span>
                    <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                </div>
                <!--end:Author-->
                <!--begin::Actions-->
                <div class="flex-grow-1 me-2">
                    <span class="d-flex fw-bold align-items-end ms-15"><?= $s['total_members'] ?></span>
                </div>
                <!--begin::Actions-->
            </div>
            <!--end::Section-->
        </div>
        <!--end::Item-->
        <!--begin::Separator-->
        <div class="separator separator-dashed my-5"></div>
        <!--end::Separator-->
        <!--begin::Item-->
        <div class="d-flex flex-stack ">
            <!--begin::Symbol-->
            <span class="symbol symbol-20px me-5">
                <span class="symbol-label bg-light-dark">
                    <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                    <span class="svg-icon svg-icon-2 svg-icon-gray">
                        <i class="fad fa-angle-right text-dark" style="font-size:10px"></i>
                    </span>
                    <!--end::Svg Icon-->
                </span>
            </span>
            <!--end::Symbol-->
            <!--begin::Section-->
            <div class="d-flex flex-row-fluid flex-wrap">
                <!--begin:Author-->
                <div class="flex-grow-1 me-10">
                    <span class="text-gray-600 fs-6 fw-semibold">Status</span>
                    <!-- <span class="text-muted fw-semibold d-block fs-7">40+ Courses</span> -->
                </div>
                <!--end:Author-->
                <!--begin::Actions-->
                <div class="flex-grow-1 me-0">
                    <span class="badge badge-light-success align-items-end ms-20"><?= $s['status'] ?></span>
                </div>
                <!--begin::Actions-->
            </div>
            <!--end::Section-->
        </div>
        <!--end::Item-->
    </div>
        <!--begin::Action-->
        <!-- <div class="m-0">
            <a href="#" class="btn btn-sm btn-primary me-2 mb-2">Daftar Masuk</a>
            <a href="/components/partials/modals/survey-progress.php" class="btn btn-sm btn-light-primary mb-2">Daftar Keluar</a>
        </div> -->
        <!--end::Action-->
    </div>
    <!--end::Body-->
</div>
<!--end::Slider Widget 2-->