<div class="card card-flush card-stretch">
    <!--begin::Header-->
    <div class="card-header pt-5">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Permohonan Permit</span>
        </h3>
        <!--end::Title-->
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body d-flex pt-3 pb-5">
        <!--begin::Tab Content-->
        <div class="tab-content">
            <!--begin::Tap pane-->
            <div class="tab-pane fade show active" id="kt_chart_widgets_22_tab_content_1">
                <!--begin::Wrapper-->
                <div class="d-flex flex-wrap flex-md-nowrap">
                    <!--begin::Items-->
                    <div class="me-md-5 w-100">
                        <?php
                        foreach ($data as $task) {
                            echo <<<TEMPLATE
                            <!--begin::Item-->
                            <div class="d-flex border border-gray-300 border-dashed rounded p-4 mb-3">
                                <!--begin::Block-->
                                <div class="d-flex align-items-center flex-grow-1 me-2 me-sm-5">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-50px me-4">
                                        <span class="symbol-label">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen013.svg-->
                                            <i class="fa-duotone fa-$task->icon text-$task->color fs-2x"></i>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Section-->
                                    <div class="me-2">
                                        <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">$task->title</a>
                                        <span class="badge badge-lg badge-light-$task->color align-self-center px-2">$task->percent</span>
                                        <span class="text-gray-400 fw-bold d-block fs-7">$task->subtitle</span>
                                    </div>
                                    <!--end::Section-->
                                </div>
                                <!--end::Block-->
                                <!--begin::Info-->
                                <div class="d-flex align-items-center">
                                    <span class="text-dark fw-bolder fs-2x">$task->count</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Item-->
                            TEMPLATE;
                        }
                        ?>
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Tap pane-->
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end: Card Body-->
</div>
<!--end::Chart widget 22-->