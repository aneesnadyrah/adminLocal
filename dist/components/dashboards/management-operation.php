
<!--begin::Row-->
<div class="row mb-10 g-10">
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--begin::Row-->
        <div class="row g-6">
            <?php $dashboard->smallTaskOverview(0) ?>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xl-8">
        <?php $dashboard->summarySlider() ?>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
<div class="row g-10">
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--begin::Gauge Chart-->
        <?php $dashboard->pieChart() ?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-8">
        <!--begin::Chart Overview-->
        <?php $dashboard->lineChart(0) ?>
        <!--end::Chart Overview-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
<div class="row g-5">
    <!--begin::Col-->
    <div class="col-xl-12">
        <?php $dashboard->tableList(0) ?>
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
