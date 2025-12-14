
<!--begin::Row-->
<div class="row mb-10 gy-5 g-xl-10">
    <?php $dashboard->mediumTaskOverview() ?>
</div>
<!--end::Row-->

<!--begin::Row-->
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--begin::Chart Overview-->
        <?php $dashboard->barChart() ?>
        <!--end::Chart Overview-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xl-8">
        <!--end::Row-->
        <!--begin::Gauge Chart-->
        <?php $dashboard->lineChart()?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->