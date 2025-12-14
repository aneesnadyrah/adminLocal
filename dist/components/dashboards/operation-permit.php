<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
?>

<div class="row mb-10 gy-5 g-xl-10">
    <?php $dashboard->smallPercentOverview() ?>
    <?php $dashboard->mediumTaskOverview() ?>
</div>

<!--begin::Row-->
<div class="row gx-xl-10">
<!-- <div class="row g-xl-8"> -->
    <!--begin::Col-->
    <div class="col-xxl-5">
        <!--begin::Latest Application-->
        <?php // $dashboard->taskWeekly() ?>
        <?php $dashboard->pieChart() ?>
        <!--end::Latest Application-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-7">
        <!--begin::Task Overview-->
        <?php // $dashboard->barChart() ?>
        <?php // include General::getWidget("task-permit-chart") ?>
        <!--end::Task Overview-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->