
<?php
// How to use this widget must declare 5 mandatory variables $head_title, $head_total, $count_success, $fIcon, $progressColor
// $head_title = 'Lawatan Tapak';
// $head_total = 27;
// $count_success = 15;
// $fIcon = 'fa-map-location-dot';
// $progressColor = 'success';
// include getWidget("project-head-task-overview.php");
?>

<div class="card card-flush h-md-50 mb-5 mb-xl-10 hover-elevate-up cursor-pointer">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <div class="card-title d-flex flex-column w-100">
            <!--begin::Info-->
            <div class="d-flex align-items-center w-100">
                <!--begin::Currency-->
                <span class="fad <?php echo $fIcon; ?> text-primary fs-3x me-4"></span>
                <!--end::Currency-->
                <!--begin::Amount test::25-->
                <span name="head-total" class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2"><?php echo $head_total; ?></span>
                <!--end::Amount-->
                <!--begin::Badge-->
                <span name="head-percent" class="badge badge-light-primary fs-base ms-auto">0%</span>
                <!--end::Badge-->
            </div>
            <!--end::Info-->
            <!--begin::Subtitle test::Lawatan Tapak-->
            <span name="head-title" class="text-gray-700 pt-1 fw-semibold fs-3 pt-3"><?php echo $head_title; ?></span>
            <!--end::Subtitle-->
        </div>
        <!--end::Title-->
    </div>
    <!--end::Header-->
    <!--begin::Card body-->
    <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
        <!--begin::Chart-->
        <div class="d-flex flex-center me-5 pt-2">
            <div name="task-overview-head" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11" data-kt-progress-color="<?php if (isset($progressColor)){echo $progressColor;} ?>"></div>
        </div>
        <!--end::Chart-->
        <!--begin::Labels-->
        <div class="d-flex flex-column content-justify-center flex-row-fluid">
            <!--begin::Label-->
            <div class="d-flex fw-semibold align-items-center">
                <!--begin::Bullet-->
                <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                <!--end::Bullet-->
                <!--begin::Label-->
                <div class="text-gray-500 flex-grow-1 me-4">Baharu</div>
                <!--end::Label-->
                <!--begin::Stats-->
                <div name="count-success" class="fw-bolder text-gray-700 text-xxl-end"><?php echo $count_success; ?></div>
                <!--end::Stats-->
            </div>
            <!--end::Label-->
            <!--begin::Label-->
            <div class="d-flex fw-semibold align-items-center">
                <!--begin::Bullet-->
                <div class="bullet w-8px h-3px rounded-2 bg-gray me-3"></div>
                <!--end::Bullet-->
                <!--begin::Label-->
                <div class="text-gray-500 flex-grow-1 me-4">Tertunggak</div>
                <!--end::Label-->
                <!--begin::Stats-->
                <div name="count-open" class="fw-bolder text-gray-700 text-xxl-end"></div>
                <!--end::Stats-->
            </div>
            <!--end::Label-->
        </div>
        <!--end::Labels-->
    </div>
    <!--end::Card body-->
</div>