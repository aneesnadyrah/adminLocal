<?php 

foreach ($data as $task) {
    echo <<<TEMPLATE
        <!--begin::Col-->
        <div class="col-6">
            <a href="$task->route" class="card hover-elevate-up parent-hover p-4">
                <div class="card-body d-flex flex-column p-3">
                    <div class="d-flex align-items-center justify-content-center fs-7 fw-bold text-gray-400">
                        <span class="svg-icon svg-icon-6 svg-icon-gray-600 me-3">
                            <i class="fad fa-$task->icon text-$task->color fs-2hx"></i>
                        </span>
                        <span class="fw-bold fs-3x text-dark lh-1 ls-n2">$task->count</span>
                    </div>
                    <div class="my-2 text-center">
                        <span class="fw-semibold text-gray-400 text-hover-$task->color">$task->title</span>
                    </div>
                </div>
            </a>
        </div>
        <!--end::Col-->
    TEMPLATE;
}

