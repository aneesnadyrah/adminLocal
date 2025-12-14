<!-- <div class="container"> -->


<div class="row g-5 g-xl-5 mb-xl-10">
    <div class="col-6 col-sm-3 col-xxl-6">
        <a href="/surveys/tasks/new" class="card card-flush hover-elevate-up ">
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
                    <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">
                        <?php
                        if ($_SESSION['roleId'] == 61) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 2) + Dashboard::taskOverview($_SESSION['roleId'], 9) + Dashboard::taskOverview($_SESSION['roleId'], 10) + Dashboard::taskOverview($_SESSION['roleId'], 12);
                        } else if ($_SESSION['roleId'] == 62 || $_SESSION['roleId'] == 64) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 3) + Dashboard::taskOverview($_SESSION['roleId'], 4) + Dashboard::taskOverview($_SESSION['roleId'], 5) + Dashboard::taskOverview($_SESSION['roleId'], 6) + Dashboard::taskOverview($_SESSION['roleId'], 7) + Dashboard::taskOverview($_SESSION['roleId'], 8);
                        } else if ($_SESSION['roleId'] == 65) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 3) + Dashboard::taskOverview($_SESSION['roleId'], 4) + Dashboard::taskOverview($_SESSION['roleId'], 5) + Dashboard::taskOverview($_SESSION['roleId'], 6) + Dashboard::taskOverview($_SESSION['roleId'], 7) + Dashboard::taskOverview($_SESSION['roleId'], 8);
                        } else if ($_SESSION['roleId'] == 63 || $_SESSION['roleId'] == 66) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 10) + Dashboard::taskOverview($_SESSION['roleId'], 11) + Dashboard::taskOverview($_SESSION['roleId'], 12) + Dashboard::taskOverview($_SESSION['roleId'], 13);
                        } else if ($_SESSION['roleId'] == 67) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 14) + Dashboard::taskOverview($_SESSION['roleId'], 15) + Dashboard::taskOverview($_SESSION['roleId'], 16) + Dashboard::taskOverview($_SESSION['roleId'], 17);
                        } else {
                            echo '0';
                        };
                        ?>
                    </span>
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
        <a href="/surveys/tasks/new" class="card card-flush hover-elevate-up">
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
                    <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">
                        0
                    </span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400 mt-0" href="/surveys/tasks/new"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 2)['content']; ?></span>
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
        <a href="/surveys/tasks/new" class="card card-flush hover-elevate-up">
            <!--begin::Card body-->
            <!--begin::Body-->
            <div class="card-body d-flex justify-content-between align-items-start flex-column">
                <!--begin::Icon-->
                <div class="m-0">
                    <i class="<?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 3)['iconClass']; ?>" style="font-size:30px"></i>
                </div>
                <!--end::Icon-->
                <!--begin::Section-->
                <div class="d-flex flex-column my-5">
                    <!--begin::Number-->
                    <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">
                        <?php
                        if ($_SESSION['roleId'] == 61) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 2) + Dashboard::taskOverview($_SESSION['roleId'], 9) + Dashboard::taskOverview($_SESSION['roleId'], 10) + Dashboard::taskOverview($_SESSION['roleId'], 12);
                        } else if ($_SESSION['roleId'] == 62 || $_SESSION['roleId'] == 64) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 3) + Dashboard::taskOverview($_SESSION['roleId'], 4) + Dashboard::taskOverview($_SESSION['roleId'], 5) + Dashboard::taskOverview($_SESSION['roleId'], 6) + Dashboard::taskOverview($_SESSION['roleId'], 7) + Dashboard::taskOverview($_SESSION['roleId'], 8);
                        } else if ($_SESSION['roleId'] == 65) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 3) + Dashboard::taskOverview($_SESSION['roleId'], 4) + Dashboard::taskOverview($_SESSION['roleId'], 5) + Dashboard::taskOverview($_SESSION['roleId'], 6) + Dashboard::taskOverview($_SESSION['roleId'], 7) + Dashboard::taskOverview($_SESSION['roleId'], 8);
                        } else if ($_SESSION['roleId'] == 63 || $_SESSION['roleId'] == 66) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 10) + Dashboard::taskOverview($_SESSION['roleId'], 11) + Dashboard::taskOverview($_SESSION['roleId'], 12) + Dashboard::taskOverview($_SESSION['roleId'], 13);
                        } else if ($_SESSION['roleId'] == 67) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 14) + Dashboard::taskOverview($_SESSION['roleId'], 15) + Dashboard::taskOverview($_SESSION['roleId'], 16) + Dashboard::taskOverview($_SESSION['roleId'], 17);
                        } else {
                            echo '0';
                        };
                        ?>
                    </span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400 mt-0" href="/surveys/tasks/new"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 3)['content']; ?></span>
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
        <a class="card card-flush hover-elevate-up ">
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
                    <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2 my-2">
                        <?php
                        if ($_SESSION['roleId'] == 61) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 2) + Dashboard::taskOverview($_SESSION['roleId'], 9) + Dashboard::taskOverview($_SESSION['roleId'], 10) + Dashboard::taskOverview($_SESSION['roleId'], 12);
                        } else if ($_SESSION['roleId'] == 62 || $_SESSION['roleId'] == 64) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 3) + Dashboard::taskOverview($_SESSION['roleId'], 4) + Dashboard::taskOverview($_SESSION['roleId'], 5) + Dashboard::taskOverview($_SESSION['roleId'], 6) + Dashboard::taskOverview($_SESSION['roleId'], 7) + Dashboard::taskOverview($_SESSION['roleId'], 8);
                        } else if ($_SESSION['roleId'] == 65) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 3) + Dashboard::taskOverview($_SESSION['roleId'], 4) + Dashboard::taskOverview($_SESSION['roleId'], 5) + Dashboard::taskOverview($_SESSION['roleId'], 6) + Dashboard::taskOverview($_SESSION['roleId'], 7) + Dashboard::taskOverview($_SESSION['roleId'], 8);
                        } else if ($_SESSION['roleId'] == 63 || $_SESSION['roleId'] == 66) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 10) + Dashboard::taskOverview($_SESSION['roleId'], 11) + Dashboard::taskOverview($_SESSION['roleId'], 12) + Dashboard::taskOverview($_SESSION['roleId'], 13);
                        } else if ($_SESSION['roleId'] == 67) {
                            echo Dashboard::taskOverview($_SESSION['roleId'], 18);
                        } else {
                            echo '0';
                        };
                        ?>
                    </span>
                    <!--end::Number-->
                    <!--begin::Follower-->
                    <div class="mt-2">
                        <span class="fw-semibold fs-6 text-gray-400 mt-0"><?php echo Dashboard::taskOverviewLabel($_SESSION['roleId'], 4)['content']; ?></span>
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