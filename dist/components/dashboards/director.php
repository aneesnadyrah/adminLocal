<div class="row mb-10 gy-5 g-xl-10">
    <?php $dashboard->bigTaskOverview() ?>
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
        <!--begin::Chart Overview-->
        <?php $dashboard->lineChart() ?>
        <?php //$dashboard->barChart() 
        ?>
        <!--end::Chart Overview-->
    </div>
    <!--end::Col-->
</div>

<div class="row g-5 g-xl-10">
    <!--begin::Col-->
    <div class="col-xl-8">
        <!--end::Row-->
        <!--begin::Gauge Chart-->
        <?php  $dashboard->listProvider()?>
        <!--end::Gauge Chart-->
    </div>
    <div class="col-xl-4">
        <!--end::Row-->
        <!--begin::Gauge Chart-->
        <?php $dashboard->barChart() ?>
        <!--end::Gauge Chart-->
    </div>
    <!--end::Col-->
</div>

<!--end::Row-->