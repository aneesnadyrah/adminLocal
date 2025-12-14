<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
?>
<!--begin::Row-->
<div class="row gy-5 g-xl-10 mb-10 mb-xl-0">
    <!--begin::Col-->
    <!--begin::Mixed Widget 12-->
    <div class="col-xxl-4">
        <?php include General::getWidget("task-overview") ?>
        <!--end::Mixed Widget 12-->
    </div>
    <!--end::Col-->
    <div class="col-xxl-8">
        <div class=" card card-xl-stretch ">
            <div class="row g-xl-5 px-5">
                <!--begin::Col-->
                <div class="col-xxl-9 ">
                    <div class="my-5">
                    <!--begin::Tables Widget 9-->
                    <?php include General::getWidget("survey-map") ?>
                    <!--end::Tables Widget 9-->
                    </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-xxl-3 pt-4 px-5">
                    <!--begin::Tables Widget 9-->
                    <?php include General::getWidget("distance-overview") ?>
                    <!--end::Tables Widget 9-->
                </div>
            </div>
        </div>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
<!--begin::Row-->
<div class="row gy-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-xxl-8">
        <!--begin::Tables Widget 9-->
        <?php include General::getWidget("list-survey-progress") ?>
        <!--end::Tables Widget 9-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-4">
        <?php include General::getWidget("list-staff-workload") ?>
        <!--end::Mixed Widget 12-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->