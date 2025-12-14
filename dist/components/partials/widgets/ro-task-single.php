<!--begin::Col-->
<div class="col-sm-6 col-xl-6 mb-xl-10">
    <!--begin::Card widget 2-->
    <!-- /tasks/new -->
    <a href="<?php echo $link; ?>" class="card h-lg-100 hover-elevate-up parent-hover">
        <!--begin::Body-->
        <div class="card-body d-flex justify-content-between align-items-start flex-column">
            <!--begin::Icon-->
            <div class="m-0">
                <div class="symbol symbol-45px w-40px me-5">
                    <span class="symbol-label bg-light-<?php echo $color; ?>">
                        <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                        <i class="fad <?php echo $icon; ?> text-<?php echo $color; ?> fs-2hx"></i>
                        <!--end::Svg Icon-->
                    </span>
                </div>
            </div>
            <!--end::Icon-->
            <!--begin::Section-->
            <div class="d-flex flex-column my-7">
                <!--begin::Number-->
                <span class="fw-bold fs-3x text-dark lh-1 ls-n2"><?php echo $count; ?></span>
                <!--end::Number-->
                <!--begin::Follower-->
                <div class="m-0">
                    <span class="fw-semibold fs-4 text-gray-400 text-hover-<?php echo $color; ?>"><?php echo $title; ?></span>
                </div>
                <!--end::Follower-->
            </div>
            <!--end::Section-->
            <!--begin::Badge-->
            <span class="badge badge-light-success fs-base"><?php echo $percent; ?>%</span>
            <!--end::Badge-->
        </div>
        <!--end::Body-->
    </a>
    <!--end::Card widget 2-->
</div>
<!--end::Col-->