
<!--begin::Row-->
<div class="row mb-10 g-5 g-xl-10">
    <?php //$dashboard->bigTaskOverview() ?>
</div>
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--end::Row-->
        <!--begin::Gauge Chart-->
        <?php // $dashboard->radialChart() ?>
        <?php $dashboard->barChart() ?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-8">
        <!--begin::Chart Overview-->
        <?php // $dashboard->areaChart(2) ?>
        <?php $dashboard->lineChart() ?>
        <!--end::Chart Overview-->
    </div>
    <!--end::Col-->
</div>
<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--end::Row-->
        <!--begin::Gauge Chart-->
        <?php $dashboard->pieChart() ?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
    <!--begin::Col-->
    <div class="col-xxl-8">
        <!--begin::Gauge Chart-->
        <?php 
            // this summary contains 6 gauges
            $dashboard->summarySlider() 
        ?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->