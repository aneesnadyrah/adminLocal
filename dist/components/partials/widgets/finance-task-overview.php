
<!--begin::Row-->
<div class="row gy-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-6 col-xl-2 ">
        <!--begin::Card widget 2-->
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">


            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <i class="fad fa-briefcase text-primary" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"><?php echo Dashboard::accountWOWidget(10)[0]['Task']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">

                        <span class="fw-semibold fs-6 text-gray-400">Tugasan</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
            </div>
            <!--end::Body-->

        </a>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-6 col-xl-2 ">
        <!--begin::Card widget 2-->
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">

            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <i class="fad fa-circle-exclamation text-danger" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <div>
                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"><?php echo Dashboard::accountWOWidget(2)[0]['NotSubmitWO']; ?></span>
                    </div>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400">Tunggakan Kerja</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
            </div>
            <!--end::Body-->

        </a>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-6 col-xl-2 ">
        <!--begin::Card widget 2-->
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <i class="fad fa-file-export text-primary" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"><?php echo Dashboard::accountWOWidget(1)[0]['WONumber']; ?></span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400">AK Dikeluarkan</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
            </div>
            <!--end::Body-->

            <!--end::Card widget 2-->
        </a>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-6 col-xl-2 ">
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <i class="fad fa-money-from-bracket text-primary" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number--><div>
                    <span class="fs-4 fw-semibold text-gray-400 me-1">RM</span>
                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"><?php $value = Dashboard::accountWOWidget(0)[0]['WOTotal']; if($value !== NULL){ echo Dashboard::formatCurrency($value);} else { echo 0;}  ?></span>
                    </div>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">

                        <span class="fw-semibold fs-6 text-gray-400">Jumlah AK</span>

                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
            </div>
            <!--end::Body-->

        </a>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-6 col-xl-2">
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                <i class="fad fa-file-invoice text-primary" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2">0</span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400">INV Dikeluarkan</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>
            <!--end::Body-->

        </a>
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-6 col-xl-2">
        <a href="/tasks/new" class="card h-lg-100 hover-elevate-up parent-hover">
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                <i class="fad fa-money-check-dollar text-primary" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-7">
                    <!--begin::Number-->
                                        <div>
                    <span class="fs-4 fw-semibold text-gray-400 me-1">RM</span>
                    <span class="fw-semibold fs-3x text-gray-800 lh-1 ls-n2"><?php echo Dashboard::formatCurrency(0);  ?></span>
                </div>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400">Pembayaran INV</span>
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->
            </div>
            <!--end::Body-->

        </a>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->