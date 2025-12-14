<!--begin::Col-->
<!-- <div class="col-xl-4"> -->
<!--begin::Chart widget 19-->
<div class="card card-flush mb-5 mb-xl-10">
    <!--begin::Header-->
    <div class="card-header pt-7">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Kutipan Penyedia Utiliti</span>
            <span class="text-gray-400 pt-2 fw-semibold fs-6">Penyedia Utiliti Utama </span>
        </h3>
        <!--end::Title-->
        <!--begin::Toolbar-->
        <div class="card-toolbar">
            <!--begin::Nav-->
            <ul class="nav" id="gauge_tabs">
                <li class="nav-item">
                    <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-light active fw-bold px-4 me-1"
                        data-bs-toggle="tab" id="years_tab" href="#years_content">Tahun</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-light fw-bold px-4"
                        data-bs-toggle="tab" id="months_tab" href="#months_content">Bulan</a>
                </li>
            </ul>
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body pt-0">
        <!--begin::Tab Content-->
        <div class="tab-content">
            <!--begin::Tap pane-->
            <div class="tab-pane fade show active" id="years_content">
                <!--begin::Chart container-->
                <div id="yearly-collections" class="w-100 h-400px mb-5 mt-n4"></div>
                <!--end::Chart container-->
                <!--begin::Items-->
                <div class="m-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-6 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-450px">Penyedia Utiliti</th>
                                    <th class="p-0 w-125px">Kutipan</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php
                                foreach (Dashboard::accountWOWidget(4) as $data) {
                                    echo '
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-stack my-2">
                                                <!--begin::Section-->
                                                    <div class="d-flex align-items-center me-5">
                                                        <!--begin::Flag-->
                                                            <div class="symbol symbol-40px me-3">
                                                                <img src="assets/media/provider/' . $data['uti_pro'] . '.webp" class="" alt="" />
                                                            </div>
                                                        <!--end::Flag-->
                                                        <!--begin::Content-->
                                                            <div class="me-5">
                                                                <!--begin::Title-->
                                                                    <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">' . $data['uti_name'] . '</a>
                                                                <!--end::Title-->

                                                            </div>
                                                        <!--end::Content-->
                                                    </div>
                                                <!--end::Section-->
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-end">
                                                <!--begin::Number-->
                                                <span class="text-gray-800 fw-bold fs-6 me-3">RM ' . Dashboard::formatCurrency($data['app_leng']) . '</span>
                                                <!--end::Number-->
                                                <!--begin::Info-->
                                                <div class="m-0">

                                                </div>
                                                <!--end::Info-->
                                            </div>
                                        </td>
                                    </tr>';
                                }

                                ?>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                </div>
                <!--end::Items-->
            </div>
            <!--end::Tap pane-->
            <!--begin::Tap pane-->
            <div class="tab-pane fade" id="months_content">
                <!--begin::Chart container-->
                <div id="monthly-collections" class="w-100 h-400px mb-5 mt-n4"></div>
                <!--end::Chart container-->
                <!--begin::Items-->
                <div class="m-0">
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-450px">Penyedia Utiliti</th>
                                    <th class="p-0 w-125px">Kutipan</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php
                                foreach (Dashboard::accountWOWidget(5) as $data) {
                                    echo '
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-stack my-2">
                                                    <!--begin::Section-->
                                                        <div class="d-flex align-items-center me-5">
                                                            <!--begin::Flag-->
                                                                <div class="symbol symbol-40px me-3">
                                                                    <img src="assets/media/provider/' . $data['uti_pro'] . '.webp" class="" alt="" />
                                                                </div>
                                                            <!--end::Flag-->
                                                            <!--begin::Content-->
                                                                <div class="me-5">
                                                                    <!--begin::Title-->
                                                                        <a href="#" class="text-gray-800 fw-bold text-hover-primary fs-6">' . $data['uti_name'] . '</a>
                                                                    <!--end::Title-->

                                                                </div>
                                                            <!--end::Content-->
                                                        </div>
                                                    <!--end::Section-->
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-end">
                                                    <!--begin::Number-->
                                                    <span class="text-gray-800 fw-bold fs-6 me-3">RM ' . Dashboard::formatCurrency($data['app_leng']) . '</span>
                                                    <!--end::Number-->
                                                    <!--begin::Info-->
                                                    <div class="m-0">

                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                            </td>
                                        </tr>';
                                }
                                ?>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                </div>
                <!--end::Items-->
            </div>
            <!--end::Tap pane-->
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end::Body-->
</div>
<!--end::Chart widget 19-->
<!-- </div> -->
<!--end::Col-->