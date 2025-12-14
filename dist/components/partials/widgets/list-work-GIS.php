
<!--begin::Col-->
<!-- <div class=" mb-5 mb-xl-10"> -->
<div class="">
    <!--begin::Table widget 6-->
    <div class="card card-flush h-500px">
        <!--begin::Header-->
        <div class="card-header pt-7">
            <!--begin::Title-->
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">Senarai Tugasan Pindaan</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6"><?php echo Dashboard::accountWOWidget(6)[0]['CountWO']; ?> Jumlah Tugasan Terkini</span>
            </h3>
            <!--end::Title-->
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <!--begin::Nav-->
                <ul class="nav nav-pills nav-pills-custom">
                    <!--begin::Item-->
                    <li class="nav-item me-3 me-lg-6">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden active" data-bs-toggle="pill" href="#tab-A1">
                            <!--begin::Title-->
                            <i class="fad fa-map-pin text-grey" style="font-size:20px"></i>
                            <!-- <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">PCL</span> -->
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3 me-lg-6">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-warning flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A2">
                            <!--begin::Title-->
                            <i class="fad fa-map-location-dot text-grey" style="font-size:20px"></i>
                            <!-- <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">PIL</span> -->
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-warning"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3 me-lg-6">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A3"
                        <?php if($_SESSION['roleId'] == 32 || $_SESSION['roleId'] == 33 ){
                            echo 'style="pointer-events: none;   opacity: 0.5;"';
                        } ?> >
                            <!--begin::Title-->
                            <i class="fad fa-globe text-grey" style="font-size:20px"></i>
                            <!-- <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">GISR</span> -->
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Nav-->
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">

            <!--begin::Tab Content-->
            <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade active show" id="tab-A1">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-250px"></th>
                                    <th class="p-0 min-w-125px"></th>
                                    <th class="p-0 min-w-125px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php

                                if (empty(Dashboard::accountWOWidget(7))) {
                                    echo '
                                    <div class="fw-semibold py-10 text-center">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Tugasan Pindaan</div>
                                        <div class="text-muted fs-6">Anda tiada Tugasan Pindaan untuk minggu ini...</div>
                                    </div>
                                    <img src="assets/media/illustrations/empty/01.svg" alt="" class="w-100 h-200px" />';
                                } else{
                                foreach (Dashboard::accountWOWidget(7) as $data){
                                    echo '
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['ProviderID'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['RefNo'].'</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['District'].'</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold mb-1 fs-6">'.$data['Length'].'</span>
                                            <span class="fw-semibold text-gray-400">meter</span>
                                        </td>

                                        <td>
                                            <div class="badge badge-light-primary">Permohonan Baru</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="/tasks/new" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
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
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="tab-A2">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-250px"></th>
                                    <th class="p-0 min-w-125px"></th>
                                    <th class="p-0 min-w-125px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php
                                if (empty(Dashboard::accountWOWidget(8))) {
                                    echo '
                                    <div class="fw-semibold py-10 text-center">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Tugasan Pindaan</div>
                                        <div class="text-muted fs-6">Anda tiada Tugasan Pindaan untuk minggu ini...</div>
                                    </div>
                                    <img src="assets/media/illustrations/empty/01.svg" alt="" class="w-100 h-200px" />';
                                } else{

                                foreach (Dashboard::accountWOWidget(8) as $data){
                                    echo '
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['ProviderID'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['RefNo'].'</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['District'].'</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold mb-1 fs-6">'.$data['Length'].'</span>
                                            <span class="fw-semibold text-gray-400">meter</span>
                                        </td>

                                        <td>
                                            <div class="badge badge-light-warning">Lawatan Tapak Selesai</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="/tasks/new" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
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
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="tab-A3">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-250px"></th>
                                    <th class="p-0 min-w-125px"></th>
                                    <th class="p-0 min-w-125px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php
                                if (empty(Dashboard::accountWOWidget(9))) {
                                    echo '
                                    <div class="fw-semibold py-10 text-center">
                                        <div class="text-gray-600 fs-3 mb-2">Tiada Tugasan Pindaan</div>
                                        <div class="text-muted fs-6">Anda tiada Tugasan Pindaan untuk minggu ini...</div>
                                    </div>
                                    <img src="assets/media/illustrations/empty/01.svg" alt="" class="w-100 h-200px" />';
                                } else{
                                foreach (Dashboard::accountWOWidget(9) as $data){
                                    echo '
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['ProviderID'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['RefNo'].'</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['District'].'</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold mb-1 fs-6">'.$data['Length'].'</span>
                                            <span class="fw-semibold text-gray-400">meter</span>
                                        </td>

                                        <td>
                                            <div class="badge badge-light-danger">Kelulusan Permit Kerja</div>
                                        </td>
                                        <td class="text-end">
                                            <a href="/tasks/new" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
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
                <!--end::Tap pane-->
            </div>
            <!--end::Tab Content-->
        </div>
        <!--end: Card Body-->
    </div>
    <!--end::Table widget 6-->
</div>
<!--end::Col-->