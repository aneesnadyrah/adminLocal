<?php
foreach ($data as $index => $item) {
    echo <<<TEMPLATE
    <div class="card card-stretch card-flush">
        <!--begin::Header-->
        <div class="card-header pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-dark">$item->title</span>
                <span class="text-gray-400 pt-2 fw-semibold fs-6">$item->subtitle</span>
            </h3>
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <ul class="nav">
    TEMPLATE;

    foreach ($item->charts AS $index => $chart) {
        switch ($chart->category) {
            case 'year' :
                $chart->category = 'Tahun';
                break;
            case 'month' :
                $chart->category = 'Bulan';
                break;
            case 'week' :
                $chart->category = 'Minggu';
                break;
            case 'day' :
                $chart->category = 'Hari';
                break;
            default :
                $chart->category;
                break;
        }
        $isActive = $index === 0 ? 'btn-active active' : '';
        echo <<<TEMPLATE
                    <li class="nav-item">
                        <a class="nav-link btn btn-color-muted btn-active-light $isActive fw-bold px-4 me-1" data-bs-toggle="tab" href="#$chart->tab">$chart->category</a>
                    </li>
    TEMPLATE;
    }

    echo <<<TEMPLATE
                </ul>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pt-0">
            <!--begin::Tab Content-->
            <div class="tab-content">
    TEMPLATE;

    foreach ($item->charts AS $index => $chart) {
        $isActive = $index === 0 ? ' show active' : '';
        echo <<<TEMPLATE
                <div class="tab-pane fade $isActive" id="$chart->tab" role="tabpanel">
                    <div class="d-flex justify-content-center w-100 p-5" data-pie-chart="$chart->id" data-pie-labels='$chart->labels' data-pie-series="$chart->series" data-pie-type="$chart->type"></div>
                </div>
    TEMPLATE;
    }

    echo <<<TEMPLATE
            </div>
            <!--end::Tab Content-->
        </div>
        <!--end::Body-->
    </div>
    TEMPLATE;
}
?>
