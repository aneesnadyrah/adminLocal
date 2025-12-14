
<!--begin::Col-->
<!-- <div class=" mb-5 mb-xl-10"> -->
<div class="">
    <!--begin::Table widget 6-->
    <div class="card card-flush h-500px">
        <!--begin::Header-->
        <div class="card-header pt-7">
            <!--begin::Title-->
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">Permohonan Mula Kerja Ukur</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6"><?php echo Survey::getTotalSurvey(); ?> Jumlah Tugasan Terkini</span>
            </h3>
            <!--end::Title-->

        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">


                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-300px"></th>
                                    <th class="p-0 min-w-100px"></th>
                                    <th class="p-0 min-w-100px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr> 
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php
                                if (empty(Survey::getTotalSurvey())) {
                                    echo '
                                    <div class="fw-semibold py-10 text-center">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Permohonan Mula Kerja Ukur</div>
                                        <div class="text-muted fs-6">Anda tiada permohonan mula kerja ukur hari ini...</div>
                                    </div>
                                    <img src="assets/media/illustrations/empty/01.svg" alt="" class="w-100 h-200px" />';
                                } else {
                                foreach (Survey::tableWidgetSelection(2, 0) as $data){
                                    echo '
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['providerID'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['refNo'].'</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['district'].'</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold mb-1 fs-6">'.$data['length'].'</span>
                                            <span class="fw-semibold text-gray-400">meter</span>
                                        </td>

                                        <td>
                                            <div class="badge badge-light-primary">'.$data['status'].'</div>
                                        </td>
                                        <td class="text-end">
                                              <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px" data-bs-toggle="modal" data-bs-target="#action-survey-start-work-' . $data['sysID'] . '">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </button>
                                        </td>
                                    </tr>';

                                }
                            }
                                ?>

                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
        </div>
        <!--end: Card Body-->
    </div>
    <!--end::Table widget 6-->
</div>
<!--end::Col-->
