
<!--begin::Row-->
<div class="row mb-10 g-5 g-xl-10">
    <?php $dashboard->bigPercentOverview() ?>
    <?php $dashboard->bigTaskOverview() ?>
</div>
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-7">
        <!--end::Row-->
        <!--begin::Gauge Chart-->
        <?php //$dashboard->gaugeChart(2)?>
        <?php $dashboard->bigMapData() ?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-5">
        <!--begin::Chart Overview-->
        <?php // $dashboard->areaChart(2) ?>
        <?php $dashboard->listPriority() ?>
        <?php // include General::getWidget("listPriority") ?>
        <!--end::Chart Overview-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->

