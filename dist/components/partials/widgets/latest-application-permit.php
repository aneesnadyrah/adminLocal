<!--begin::Table widget 7-->
<div class="card card-flush h-550px mb-7">
    <!--begin::Header-->
    <div class="card-header pt-7">
        <!--begin::Title-->
        <h4 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800">Senarai Tugasan Terkini</span>
            <span class="text-gray-400 mt-1 fw-semibold fs-7">Dikemaskini 37 minit yang lalu</span>
        </h4>
        <!--end::Title-->
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <!--begin::Nav-->
                <ul class="nav nav-pills nav-pills-custom">
                    <!--begin::Item-->
                    <li class="nav-item me-3">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden active" data-bs-toggle="pill" href="#tab-A1">
                            <!--begin::Title-->
                            <!-- <i class="fad fa-map-pin text-grey" style="font-size:20px"></i> -->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">14 Hari IL</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-success"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-warning flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A2">
                            <!--begin::Title-->
                            <!-- <i class="fad fa-map-location-dot text-grey" style="font-size:20px"></i> -->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">7 Hari IL</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-warning"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A3">
                            <!--begin::Title-->
                            <!-- <i class="fad fa-globe text-grey" style="font-size:20px"></i> -->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">IL Tamat</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A4">
                            <!--begin::Title-->
                            <!-- <i class="fad fa-globe text-grey" style="font-size:20px"></i> -->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">14 Hari PK</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-success"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A5">
                            <!--begin::Title-->
                            <!-- <i class="fad fa-globe text-grey" style="font-size:20px"></i> -->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">7 Hari PK</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-warning"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A6"
                        <?php if($_SESSION['roleId'] == 32 || $_SESSION['roleId'] == 41 ){
                            echo 'style="pointer-events: none;   opacity: 0.5;"';
                        } ?> >
                            <!--begin::Title-->
                            <!-- <i class="fad fa-globe text-grey" style="font-size:20px"></i> -->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">PK Tamat</span>
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
    <div class="card-body py-3">
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
                            <tr class="border-bottom-0">
                                <th class="p-0 w-50px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-50px"></th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        
                        <!--begin::Table body-->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-info">
                                            <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/2.webp" alt="" />
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/DIGI/03/23/04/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1085m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-warning fs-base">KT</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">23 Jan, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-success">
                                            <!--begin::Svg Icon | path: icons/duotune/medicine/med005.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/3.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TIME/03/23/06/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">101m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-success fs-base">CH</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">04 Feb, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-danger">
                                            <!--begin::Svg Icon | path: icons/duotune/abstract/abs036.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-danger">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/7.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TMB/1/23/101/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">500m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-primary fs-base">CH</span>
                                    <span class="fw-bold badge badge-light-danger fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">18 Feb, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-primary">
                                            <!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-primary">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/5.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TNBB/1/23/63/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">13000m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-dark fs-base">JT</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">01 Apr, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-warning">
                                            <!--begin::Svg Icon | path: icons/duotune/technology/teh008.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-warning">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/8.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/GAS/03/23/05/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1407m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-info fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">26 May, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
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
                            <tr class="border-bottom-0">
                                <th class="p-0 w-50px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-50px"></th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        
                        <!--begin::Table body-->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-primary">
                                            <!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-primary">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/5.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TNBB/1/23/63/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">13000m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-danger fs-base">JT</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">01 Apr, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-warning">
                                            <!--begin::Svg Icon | path: icons/duotune/technology/teh008.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-warning">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/8.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/GAS/03/23/06/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1407m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-info fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">26 May, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
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
                            <tr class="border-bottom-0">
                                <th class="p-0 w-50px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-50px"></th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        
                        <!--begin::Table body-->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-info">
                                            <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/2.webp" alt="" />
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/DIGI/03/23/04/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1085m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-success fs-base">KT</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">23 Jan, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-success">
                                            <!--begin::Svg Icon | path: icons/duotune/medicine/med005.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/7.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TMB/1/23/101/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">500m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-danger fs-base">CH</span>
                                    <span class="fw-bold badge badge-light-warning fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">04 Feb, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--end::Tap pane-->
            
            <!--begin::Tap pane-->
            <div class="tab-pane fade" id="tab-A4">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="border-bottom-0">
                                <th class="p-0 w-50px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-50px"></th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        
                        <!--begin::Table body-->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-primary">
                                            <!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-primary">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/5.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TNBB/1/23/63/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">13000</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-warning fs-base">JT</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">01 Apr, 22</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-success">
                                            <!--begin::Svg Icon | path: icons/duotune/medicine/med005.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/7.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TMB/1/23/101/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">500m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-info fs-base">CH</span>
                                    <span class="fw-bold badge badge-light-primary fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">04 Feb, 22</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-info">
                                            <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/2.webp" alt="" />
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/DIGI/03/23/04/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1085m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-dark fs-base">KT</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">23 Jan, 22</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--end::Tap pane-->

            <!--begin::Tap pane-->
            <div class="tab-pane fade" id="tab-A5">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="border-bottom-0">
                                <th class="p-0 w-50px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-175px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-150px"></th>
                                <th class="p-0 min-w-50px"></th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        
                        <!--begin::Table body-->
                        <tbody>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-warning">
                                            <!--begin::Svg Icon | path: icons/duotune/technology/teh008.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-warning">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/8.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/GAS/03/23/06/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1407m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-success fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">26 May, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-info">
                                            <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/2.webp" alt="" />
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/DIGI/03/23/04/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">1085m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-warning fs-base">KT</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">23 Jan, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="symbol symbol-40px">
                                        <span class="symbol-label bg-light-success">
                                            <!--begin::Svg Icon | path: icons/duotune/medicine/med005.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-success">
                                                <img class="symbol-label bg-light-default" id="provider-img" src="assets/media/provider/7.webp" alt="" />
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                </td>
                                <td class="ps-0">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">KUK/TMB/1/23/101/A3</a>
                                    <span class="text-muted fw-semibold d-block fs-7">500m</span>
                                </td>
                                <td>
                                    <span class="fw-bold badge badge-light-danger fs-base">CH</span>
                                    <span class="fw-bold badge badge-light-primary fs-base">LP</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">04 Feb, 22</span>
                                </td>
                                <td>
                                    <span class="fw-bold d-block fs-6">Status</span>
                                </td>
                                <td class="text-end">
                                    <a href="#" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--end::Tap pane-->
            
        </div>
        <!--end::Tab Content-->
    </div>
    <!--begin::Body-->
</div>
<!--end::Table widget 7-->