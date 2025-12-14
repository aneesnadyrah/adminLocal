<?php 

foreach ($data as $task) {
    echo <<<TEMPLATE
        <!--begin::Col-->
        <div class="col-6 col-xl-2 ">
            <a href="$task->route" class="card h-lg-100 hover-elevate-up parent-hover">
                <div class="card-body d-flex align-items-center justify-content-center flex-column">
                    <div class="m-0">
                        <i class="fad fa-$task->icon text-$task->color" style="font-size:30px"></i>
                    </div>
                    <div class="d-flex flex-column my-7">
                        <span class="fw-bold fs-3x text-dark lh-1 ls-n2 text-center">$task->count</span>
                        <div class="mt-2 text-center">
                            <span class="fw-semibold fs-6 text-gray-400 text-hover-$task->color">$task->title</span>
                        </div>
                    </div>
                </div>
                
            </a>
        </div>
        <!--end::Col-->
        

    TEMPLATE;
}

