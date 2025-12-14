
<!--begin::Row-->
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-sm-6 col-xl-6 mb-xl-10">
        <!--begin::Card widget 2-->
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-light-primary">
                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                            <i class="fad fa-file-lines text-primary fs-2hx"></i>
                            <!--end::Svg Icon-->
                        </span>
                    </div>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-3x text-dark lh-1 ls-n2">12</span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-semibold fs-4 text-gray-400">Permohonan Baru</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
                <!--begin::Badge-->
                <span class="badge badge-light-success fs-base">50%</span>
                <!--end::Badge-->
            </div>
            <!--end::Body-->
        </a>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-sm-6 col-xl-6 mb-xl-10">
        <!--begin::Card widget 2-->
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-light-danger">
                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                            <i class="fad fa-ban text-danger fs-2hx"></i>
                            <!--end::Svg Icon-->
                        </span>
                    </div>
                    <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                    <!-- <i class="fad fa-ban text-danger fs-2hx"></i> -->
                    <!--end::Svg Icon-->
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-3x text-dark lh-1 ls-n2">9</span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="m-0">
                        <span class="fw-semibold fs-4 text-gray-400">Permohonan Batal</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
                <!--begin::Badge-->
                <span class="badge badge-light-success fs-base">31%</span>
                <!--end::Badge-->
            </div>
            <!--end::Body-->
        </a>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
<!--begin::Row-->
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-sm-6 col-xl-6 mb-xl-10">
        <!--begin::Card widget 2-->
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-light-primary">
                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                            <i class="fad fa-file-export text-primary fs-2hx"></i>
                            <!--end::Svg Icon-->
                        </span>
                    </div>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-bold fs-3x text-dark lh-1 ls-n2"><?php echo Dashboard::accountWOWidget(1)[0]['WONumber']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-4 text-gray-400">AK Dikeluarkan</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
                <!--begin::Badge-->
                <span class="badge badge-light-success fs-base">55%</span>
                <!--end::Badge-->
            </div>
            <!--end::Body-->

            <!--end::Card widget 2-->
        </a>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-sm-6 col-xl-6 mb-xl-10">
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <div class="symbol symbol-45px w-40px me-5">
                        <span class="symbol-label bg-light-primary">
                            <!--begin::Svg Icon | path: icons/duotune/maps/map004.svg-->
                            <i class="fad fa-money-from-bracket text-primary fs-2hx"></i>
                            <!--end::Svg Icon-->
                        </span>
                    </div>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number--><div>
                    <span class="fw-bold fs-3x text-dark lh-1 ls-n2"><?php $value = Dashboard::accountWOWidget(0)[0]['WOTotal']; echo Dashboard::formatCurrency($value);  ?></span>
                    </div>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">

                        <span class="fw-semibold fs-4 text-gray-400">Jumlah AK (RM)</span>

                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
                <!--begin::Badge-->
                <span class="badge badge-light-success fs-base">70%</span>
                <!--end::Badge-->
            </div>
            <!--end::Body-->

        </a>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->