<div class="modal fade" id="modal-pin-project" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-600px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal body-->
            <div class="modal-body py-lg-10 px-lg-10">
                <!--begin::Search-->
                <div id="modal-pin-project-handler" data-kt-search-keypress="true" data-kt-search-min-length="4"
                    data-kt-search-enter="enter" data-kt-search-layout="inline">
                    <!--begin::Form-->
                    <form data-kt-search-element="form" class="w-100 position-relative mb-5" autocomplete="off">
                        <!--begin::Hidden input(Added to disable form autocomplete)-->
                        <input type="hidden" />
                        <!--end::Hidden input-->
                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span
                            class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M21.7,18.9l-3.1-3.1c-0.7,1.1-1.7,2.1-2.8,2.8l3.1,3.1c0.4,0.4,1,0.4,1.4,0l1.4-1.4C22.1,19.9,22.1,19.3,21.7,18.9z"
                                    fill="currentColor" />
                                <path class="st0"
                                    d="M11,20c-5,0-9-4-9-9s4-9,9-9s9,4,9,9S16,20,11,20z M11,4c-3.9,0-7,3.1-7,7s3.1,7,7,7s7-3.1,7-7S14.9,4,11,4z
                                    M11.5,6.9L12.7,9l2.4,0.6c0.4,0.1,0.6,0.6,0.3,0.9l-1.7,1.8l0.2,2.5c0,0.4-0.4,0.7-0.8,0.6l-2.3-1l-2.3,1c-0.4,0.2-0.8-0.1-0.8-0.6
                                    l0.2-2.5l-1.7-1.8c-0.3-0.3-0.1-0.8,0.3-0.9L9.3,9l1.2-2.1C10.7,6.5,11.2,6.5,11.5,6.9z"
                                    fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Input-->
                        <input type="text" class="form-control form-control-lg form-control-solid px-15"
                            style="height:50px;" name="pin-search" value=""
                            placeholder="Cari Nombor Rujukan Permohonan..." data-kt-search-element="input" />
                        <!--end::Input-->
                        <!--begin::Spinner-->
                        <span class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5"
                        data-kt-search-element="spinner">
                            <span class="spinner-border h-15px w-15px align-middle text-muted"></span>
                        </span>
                        <!--end::Spinner-->
                        <!--begin::Reset-->
                        <span
                            class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 me-5 d-none"
                            data-kt-search-element="clear">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                        transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                        <!--end::Reset-->
                    </form>
                    <!--end::Form-->
                    <!--begin::Wrapper-->
                    <div class="separator separator-dashed"></div>
                    <div class="py-5">
                        <!--begin::Pinned Project-->
                        <div data-kt-search-element="pinned">
                            <!--begin::Users-->
                            <div class="mh-250px scroll-y me-n7 pe-7">
                                <div id="pinned-selection">
                                    <!--begin::Pinned Projects-->
                                    <div class="app-navbar-item-container"></div>
                                    <!--end::Pinned Projects-->
                                </div>
                            </div>
                            <!--end::Users-->
                        </div>
                        <!--end::Pinned Project-->
                        <!--begin::Results(add d-none to below element to hide the users list by default)-->
                        <div data-kt-search-element="results" class="d-none"></div>
                        <!--end::Results-->
                        <!--begin::Empty-->
                        <div data-kt-search-element="empty" class="text-center d-none">
                            <!--begin::Message-->
                            <div class="fw-semibold py-10">
                                <div class="text-gray-600 fs-3 mb-2">Nombor Rujukan Tidak Dijumpai</div>
                                <div class="text-muted fs-6">Cuba cari dengan Nombor Rujukan Permohonan Penuh...</div>
                            </div>
                            <!--end::Message-->
                            <!--begin::Illustration-->
                            <div class="text-center px-5">
                                <img src="assets/media/illustrations/empty/spaceShip.svg" alt="" class="w-100 h-200px" />
                            </div>
                            <!--end::Illustration-->
                        </div>
                        <!--end::Empty-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Search-->
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
</div>