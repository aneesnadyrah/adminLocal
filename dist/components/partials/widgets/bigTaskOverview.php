<?php 

foreach ($data as $task) {
    echo <<<TEMPLATE
        <!--begin::Col-->
        <div class="col-3">
            <!--begin::Card widget 2-->
            <a href="$task->route" class="card h-lg-100 hover-elevate-up parent-hover">

                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <!--begin::Section-->
                    <div class="d-flex flex-column my-7">
                        <!--begin::Number-->
                        <span class="fw-semibold fs-2x text-gray-800 lh-1 ls-n2">
                            <i class="fad fa-$task->icon text-$task->color fs-2x"></i>
                            <span>$task->count</span>
                        </span>
                        <!--end::Number-->
                        <!--begin::Follower-->
                        <div class="mt-2">
                            <span class="fw-semibold fs-4 text-gray-400">$task->title</span>
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
    TEMPLATE;
}

?>