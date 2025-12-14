<?php
    echo <<<TEMPLATE
        <div class="card card-flush card-stretch">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">$data->title</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">$data->subtitle</span>
                </h3>
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body d-flex justify-content-between flex-column pb-0 px-0 pt-1">
                <!--begin::Items-->
                <div class="d-flex flex-wrap d-grid gap-5 px-9 mt-5 mb-3">
        TEMPLATE;
        if(isset($data->counter)) {
            foreach($data->counter AS $statistic) {
                    echo <<<TEMPLATE
                    <!--begin::Item-->
                    <div class="me-md-2">
                        <!--begin::Statistics-->
                        <span class="fs-6 fw-semibold text-gray-400">$statistic->title</span>
                        <div class="d-flex mb-2">
                            <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2" data-kt-countup="true" data-kt-countup-decimal-places="$statistic->decimal" data-kt-countup-value="$statistic->counter" data-kt-countup-prefix="$statistic->prefix" data-kt-countup-suffix="$statistic->suffix">0</span>
                        </div>
                        <!--end::Statistics-->
                    </div>
                    <!--end::Item-->
                    TEMPLATE;
                }
            }
            $chart = $data->chart;
            echo <<<TEMPLATE
                    </div>
                    <!--end::Items-->
                    <!--begin::Chart-->
                    <div data-line-chart="$chart->id" data-line-series="$chart->series" data-line-name='$chart->name' data-line-labels='$chart->labels' data-line-style='$chart->style' class="min-h-350px p-5"></div>
                    <!--end::Chart-->
                </div>
                <!--end::Card body-->
            </div>
        TEMPLATE;