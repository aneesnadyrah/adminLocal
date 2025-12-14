<!-- <div class="container"> -->


<div class="row g-5 g-xl-5 mb-xl-10">
    <div class="col-6 col-sm-3 col-xxl-6">
        <a href="/tasks/new" class="card card-flush hover-elevate-up ">
                <!--begin::Card body-->
                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <i class="<?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 1)['iconClass']; ?>" style="font-size:30px"></i>
                    </div>
                    <!--end::Icon-->
                    <!--begin::Section-->
                    <div class="d-flex flex-column my-5">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2"><?php if(Dashboard::GISWidget($_SESSION['roleId'],1)[0]['Task'] !== NULL){ echo Dashboard::GISWidget($_SESSION['roleId'], 1)[0]['Task']; } else { echo 0;} ?></span>
                        <!--end::Number-->
                        <!--begin::Follower-->
                        <div class="mt-2">
                            <span class="fw-semibold fs-6 text-gray-400 mt-0"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 1)['content']; ?></span>
                            <!-- <span class="fw-semibold fs-6 text-gray-400"></span> -->
                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Card body-->
            <!--end::Card widget 4-->
        </a>
    </div>
    <div class="col-6 col-sm-3 col-xxl-6">
        <a href="/tasks/new" class="card card-flush hover-elevate-up"
        <?php if($_SESSION['roleId'] == 6 || $_SESSION['roleId'] == 7 || $_SESSION['roleId'] == 8 || $_SESSION['roleId'] == 9 ){
            echo 'style="pointer-events: none;   opacity: 0.5;"';
        } ?> >
                <!--begin::Card body-->
                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <!--begin::Icon-->
                    <div class="m-0">
                        <i class="<?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 2)['iconClass']; ?>" style="font-size:30px"></i>
                    </div>
                    <!--end::Icon-->
                    <!--begin::Section-->
                    <div class="d-flex flex-column my-5">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">9</span>
                        <!--end::Number-->
                        <!--begin::Follower-->
                        <div class="mt-2">
                            <span class="fw-semibold fs-6 text-gray-400 mt-0" href="/tasks/new"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 2)['content']; ?></span>
                            <!-- <span class="fw-semibold fs-6 text-gray-400"></span> -->
                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->

                </div>

                <!--end::Card body-->
            <!--end::Card widget 6-->
        </a>
    </div>

    <!-- Force next columns to break to new line -->
    <!-- <div class="w-100"></div> -->

    <div class="col-6 col-sm-3 col-xxl-6">
            <!--begin::Card widget 5-->
                    <a href="/tasks/new" class="card card-flush hover-elevate-up" >
                <!--begin::Card body-->
                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column" >
                    <!--begin::Icon-->
                    <div class="m-0">
                        <i class="<?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 3)['iconClass']; ?>" style="font-size:30px"></i>
                    </div>
                    <!--end::Icon-->
                    <!--begin::Section-->
                    <div class="d-flex flex-column my-5">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">8</span>
                        <!--end::Number-->
                        <!--begin::Follower-->
                        <div class="mt-2">
                            <span class="fw-semibold fs-6 text-gray-400 mt-0" href="/tasks/new"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 3)['content']; ?></span>
                            <!-- <span class="fw-semibold fs-6 text-gray-400"></span> -->
                        </div>
                        <!--end::Follower-->
                    </div>
                    <!--end::Section-->

                </div>

                <!--end::Card body-->

            <!--end::Card widget 5-->
            </a>
    </div>
    <div class="col-6 col-sm-3 col-xxl-6">
        <!--begin::Card widget 7-->
        <a href="/tasks/new" class="card card-flush hover-elevate-up ">
            <!--begin::Card body-->
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <i class="<?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 4)['iconClass']; ?>" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-5">
                    <!--begin::Number-->
                    <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">1</span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400 mt-0" href="/tasks/new"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 4)['content']; ?></span>
                        <!-- <span class="fw-semibold fs-6 text-gray-400"></span> -->
                    </div>
                    <!--end::Follower-->
                </div>
                <!--end::Section-->

            </div>

            <!--end::Card body-->
        </a>
        <!--end::Card widget 7-->
    </div>
</div>
<!-- </div> -->