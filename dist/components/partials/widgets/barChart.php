<?php
foreach ($data as $item) {
    echo <<<TEMPLATE
        <div class="card card-flush card-stretch mb-5">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">$item->title</span>
                    <span class="text-gray-500 pt-2 fw-semibold fs-6">$item->subtitle</span>
                </h3>
                <!--end::Title-->

                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <!--begin::Nav-->
                    <ul class="nav">
    TEMPLATE;

    foreach ($item->charts->data as $index => $chart) {
        switch ($chart->category) {
            case 'year':
                $chart->category = 'Tahun';
                break;
            case 'month':
                $chart->category = 'Bulan';
                break;
            case 'week':
                $chart->category = 'Minggu';
                break;
            case 'day':
                $chart->category = 'Hari';
                break;
            default:
                $chart->category;
                break;
        }
        $isActive = $index === 0 ? 'btn-active active' : '';
        echo <<<TEMPLATE
                        <li class="nav-item">
                            <a class="nav-link btn btn-color-muted btn-active-light $isActive fw-bold px-4 me-1" data-bs-toggle="tab" data-kt-countup-tabs="true" href="#$chart->tab">$chart->category</a>
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
            <div class="card-body d-flex justify-content-between flex-column pt-5">
                <!--begin::Tab Content-->
                <div class="tab-content">
    TEMPLATE;

    if (isset($item->charts)) {
        foreach ($item->charts->data as $index => $chart) {
            $isActive = $index === 0 ? ' show active' : '';
            echo <<<TEMPLATE
                    <div class="tab-pane fade $isActive" id="$chart->tab" role="tabpanel">
            TEMPLATE;

            if (isset($item->charts->counter)) {
                echo <<<TEMPLATE
                        <!--begin::Items-->
                        <div class="d-flex flex-wrap d-grid gap-5">
                            <!--begin::Item-->
                            <div class="me-md-2">
                                <!--begin::Statistics-->
                                <span class="fs-6 fw-semibold text-gray-400">{$item->charts->counter[$index]->title}</span>
                                <div class="d-flex mb-2">
                                    <span class="fs-2qx fw-bold text-gray-800 me-2 lh-1 ls-n2" data-kt-countup="true" data-kt-countup-decimal-places="{$item->charts->counter[$index]->decimal}" data-kt-countup-value="{$item->charts->counter[$index]->counter}" data-kt-countup-prefix="{$item->charts->counter[$index]->prefix}" data-kt-countup-suffix="{$item->charts->counter[$index]->suffix}">0</span>
                                </div>
                                <!--end::Statistics-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
            TEMPLATE;
            }

            echo <<<TEMPLATE
                        <div class="d-flex justify-content-center w-100" data-bar-chart="$chart->id" data-bar-labels='$chart->labels' data-bar-series='$chart->series' data-bar-height="$chart->height" data-bar-name='$chart->name' data-bar-type="$chart->type" data-bar-style="$chart->style"></div>
            TEMPLATE;

            if (isset($item->charts->lists[$index])) {
                $listItem = $item->charts->lists[$index];

                foreach ($listItem as $index => $listData) {
                    echo <<<TEMPLATE
                            <!--begin::Items-->
                            <div class="m-0">                               
                                <!--begin::Item-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center me-5">
                                        <!--begin::Flag-->                    
                                        <img src="{$listData->provider->logo}" class="me-4 w-30px" style="border-radius: 4px" alt=""/>
                                        <!--end::Flag-->

                                        <!--begin::Content-->
                                        <div class="me-5">
                                            <!--begin::Title-->
                                            <a class="text-gray-800 fw-bold text-hover-primary fs-6">{$listData->provider->name}</a>
                                            <!--end::Title-->                                  
                                        </div>
                                        <!--end::Content-->                                       
                                    </div>
                                    <!--end::Section-->  

                                    <!--begin::Wrapper-->
                                    <div class="d-flex align-items-center"> 
                                        <!--begin::Number-->           
                                        <span class="text-gray-800 fw-bold fs-4 me-3" data-kt-countup="true" data-kt-countup-decimal-places="{$listData->decimal}" data-kt-countup-value="{$listData->paid}" data-kt-countup-prefix="{$listData->prefix}" data-kt-countup-suffix="{$listData->suffix}">0</span> 
                                        <!--end::Number-->                             
                                    </div>
                                    <!--end::Wrapper-->                
                                </div>
                                <!--end::Item-->
                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-4"></div>
                                <!--end::Separator-->
                            </div>
                    TEMPLATE;
                }
            }
            echo <<<TEMPLATE
                    </div>
                    <!--end::Tap pane-->
            TEMPLATE;
        }

    } else {
        echo <<<TEMPLATE
                    <!--begin::Empty-->
                    <div class="text-center pt-3">
                        <!--begin::Illustration-->
                        <div class="text-center px-5">
                            <img src="assets/media/illustrations/empty/lostConnection.svg" alt="" class="w-350px" />
                        </div>
                        <!--end::Illustration-->
                        <!--begin::Message-->
                        <h4 class="text-gray-600 fs-4 fw-bold mb-10">Tiada Data $item->title</h4>
                        <!--end::Message-->
                    </div>
                    <!--end::Empty-->
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
