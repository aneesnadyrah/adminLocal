<?php 

foreach ($data as $task) {
    echo <<<TEMPLATE
        <!--begin::Col-->
        <div class="col-xxl-4 mb-5">
            <!--begin::Chart widget-->
            <div class="card card-flush h-lg-60">
                <!--begin::Header-->
                <div class="card-header py-5">
                    <!--begin::Title-->
                    <div class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-2x text-gray-800 lh-1 ls-n2 mb-1">$task->count</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">$task->title</span>
                    </div>
                    <!--end::Title-->
                    <!--begin::Symbol-->
                    <div class="card-title align-items-start flex-column">
                        <i class="fad fa-$task->icon text-gray-500 fs-3x"></i>    
                    </div>
                    <!--end::Symbol--> 
                </div>
                <!--end::Header-->
            </div>
            <!--end::Chart widget-->
        </div>   
        <!--end::Col--> 
    TEMPLATE;
}

