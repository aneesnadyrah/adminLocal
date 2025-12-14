
<!--begin::Row-->
<div class="row mb-6 g-5 g-xl-10"> 
    <!--begin::Col-->
    <div class="col-xl-4">
        <!--begin::Row-->
        <div class="row g-6">
            <?php $dashboard->smallTaskOverview() ?>
        </div>
        <!--end::Row-->
    </div>
    <!--begin::Col-->
    <div class="col-xl-8">
        <?php $dashboard->summarySlider() ?>
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
        <!--begin::Chart Overview-->
        <?php $dashboard->lineChart() ?>
        <!--end::Chart Overview-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->