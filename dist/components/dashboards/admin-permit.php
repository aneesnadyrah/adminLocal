<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
?>
<!--begin::Task Overview-->
<?php include General::getWidget("task-permit-horizontal") ?>
<!--end::Task Overview-->

<!--begin::Row-->
<div class="row g-xl-8">
    <!--begin::Col-->
    <div class="col-xxl-6 gy-5">
        <!--begin::Latest Application-->
        <?php include General::getWidget("task-weekly") ?>
        <!--end::Latest Application-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-6 gy-5">
        <!--begin::Task Overview-->
        <?php include General::getWidget("task-permit-chart") ?>
        <!--end::Task Overview-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->