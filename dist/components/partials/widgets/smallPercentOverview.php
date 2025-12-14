<?php 

foreach ($data as $task) {
    echo <<<TEMPLATE
        <!--begin::Col-->
        <div class="col-sm-6 col-xl-2">
            <!--begin::Card widget 2-->
            <div class="card h-lg-100 hover-elevate-up parent-hover">
                <!--begin::Body-->
                <div class="card-body d-flex justify-content-between align-items-start flex-column">
                    <!--begin::Statistics-->
                    <div class="m-0">
                        <!--begin::Title-->
                        <span class="fs-2 fw-bold text-gray-800 me-2 lh-1 ls-n2">Kelulusan</span>
                        <!--end::Title-->
                    </div>
                    <!--end::Statistics-->

                    <div class="d-flex align-items-center flex-column my-7 w-100">
                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 opacity-50 w-100 mt-auto">
                            <span>$task->title1</span>
                            <span>$task->percent1%</span>
                        </div>
                        <div class="mx-3 w-100 bg-light-$task->color1 rounded mb-2">
                            <div class="bg-$task->color1 rounded h-10px" role="progressbar" style="width: $task->percent1%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="d-flex justify-content-between fw-bold fs-6 text-gray-800 opacity-50 w-100 mt-auto">
                            <span>$task->title2</span>
                            <span>$task->percent2%</span>
                        </div>
                        <div class="mx-3 w-100 bg-light-$task->color2 rounded mb-2">
                            <div class="bg-$task->color2 rounded h-10px" role="progressbar" style="width: $task->percent2%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Card widget 2-->
        </div>
        <!--end::Col-->

    TEMPLATE;
}

