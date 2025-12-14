<!--end::View component-->
<!--begin::Card-->
<div class="card app-calendar-wrapper">
    <div class="row g-0">
        <!-- Calendar Sidebar -->
        <div class="col-3 app-calendar-sidebar" id="app-calendar-sidebar">
            <div class="border-bottom p-4 my-sm-0 mb-3">
                <div class="d-grid">
                <button
                    class="btn btn-primary btn-toggle-sidebar"
                    data-kt-calendar="add"
                >
                    <i class="ti ti-plus me-1"></i>
                    <span class="align-middle indicator-label" data-kt-calendar="button">Tambah Agenda</span>
                    <span class="indicator-progress">Sila Tunggu...<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
                </div>
            </div>
            <div class="p-3">
                <!-- inline calendar (flatpicker) -->
                <div class="inline-calendar"></div>

                <hr class="container-m-nx mb-4 mt-3" />

                <!-- Filter -->
                <div class="mb-3 ms-3">
                <small class="text-small text-muted text-uppercase align-middle">Tapisan Aktiviti</small>
                </div>

                <div class="form-check form-check-info mb-2 ms-3">
                    <input
                        class="form-check-input select-all"
                        type="checkbox"
                        id="selectAll"
                        data-value="all"
                        checked
                    />
                    <label class="form-check-label" for="selectAll">Lihat Semua</label>
                </div>

                <div class="app-calendar-events-filter ms-3">
                </div>
            </div>
        </div>
        <!-- /Calendar Sidebar -->

        <!-- Calendar & Modal -->
        <div class="col-9 app-calendar-content">
            <!--begin::Card header-->
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body">
                <!--begin::Calendar-->
                <div id="kt_calendar_app"></div>
                <!--end::Calendar-->
            </div>
            <!--end::Card body-->
            <!-- TODO: figure out what app-overlay used for -->
            <div class="app-overlay"></div>
        </div>
    </div>
</div>
<!--end::Card-->

<!--begin::Offcanvas - New Product-->
<div id="kt_drawer_add_event" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"  data-kt-drawer-width="{default:'300px', 'md': '500px'}">
    <div class="card rounded-0 w-100 h-100" style="box-shadow: none;">
        <!-- Begin::Form -->
        <form class="form" action="#" id="kt_modal_add_event_form">
            <div class="card-header">
                <h2 class="card-title fw-bold" data-kt-calendar="title">Tambah Agenda</h2>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light" id="kt_modal_add_event_close">
                        <i class="far fa-close fs-4"></i>
                    </button>
                </div>
            </div>
            <div class="card-body hover-scroll-overlay-y">
                <!--begin::Input group-->
                <div class="fv-row mb-9" name="form-event-title">
                    <!--begin::Label-->
                    <label class="fs-6 fw-semibold required mb-2">Tajuk Agenda</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" class="form-control form-control-solid" placeholder="Nyatakan tajuk agenda" name="calendar_event_name" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-9" name="form-event-category">
                    <!--begin::Label-->
                    <label class="fs-6 fw-semibold required mb-2">Kategori</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <select class="form-select form-select-solid" data-control="select2" id="calendar_group_id" name="calendar_group_id" data-placeholder="Pilih kategori agenda" data-hide-search="true">
                        <!-- <option></option> -->
                        <!-- <option data-label="primary" value="1">Company</option> -->
                    </select>
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-9" name="form-event-guest">
                    <!--begin::Label-->
                    <label class="fs-6 fw-semibold required mb-2">Tetamu</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <select class="form-select form-select-solid" data-control="select2" id="calendar_event_guest" name="calendar_event_guest"  data-close-on-select="false" data-placeholder="Pilih tetamu agenda" data-allow-clear="true" data-hide-selected="true" multiple="multiple">
                        <!-- <option></option> -->
                    </select>
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-9" name="form-event-desc">
                    <!--begin::Label-->
                    <label class="fs-6 fw-semibold mb-2">Penerangan Agenda</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" class="form-control form-control-solid" placeholder="Nyatakan penerangan agenda" name="calendar_event_description" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-9" name="form-event-loc">
                    <!--begin::Label-->
                    <label class="fs-6 fw-semibold mb-2">Lokasi Agenda</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input type="text" class="form-control form-control-solid" placeholder="Nyatakan lokasi agenda" name="calendar_event_location" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="fv-row mb-9">
                    <!--begin::Checkbox-->
                    <label class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="" id="kt_calendar_datepicker_allday" />
                        <span class="form-check-label fw-semibold" for="kt_calendar_datepicker_allday">Sepanjang Hari</span>
                    </label>
                    <!--end::Checkbox-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="row row-cols-lg-2 g-10">
                    <div class="col">
                        <div class="fv-row mb-9">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2 required">Tarikh Mula Agenda</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control form-control-solid" name="calendar_event_start_date" placeholder="Pilih tarikh mula" id="kt_calendar_datepicker_start_date" />
                            <!--end::Input-->
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-9">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2 required">Tarikh Tamat Agenda</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control form-control-solid" name="calendar_event_end_date" placeholder="Pilih tarikh tamat" id="kt_calendar_datepicker_end_date" />
                            <!--end::Input-->
                        </div>
                    </div>
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="row row-cols-lg-2 g-10">
                    <div class="col" data-kt-calendar="datepicker">
                        <div class="fv-row mb-9">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">Waktu Mula Agenda</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control form-control-solid" name="calendar_event_start_time" placeholder="Pilih waktu mula" id="kt_calendar_datepicker_start_time" />
                            <!--end::Input-->
                        </div>
                    </div>
                    <div class="col" data-kt-calendar="datepicker">
                        <div class="fv-row mb-9">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">Waktu Tamat Agenda</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input class="form-control form-control-solid" name="calendar_event_end_time" placeholder="Pilih waktu tamat" id="kt_calendar_datepicker_end_time" />
                            <!--end::Input-->
                        </div>
                    </div>
                </div>
                <!--end::Input group-->
            </div>
            <div class="card-footer d-flex justify-content-left">
                <!--begin::Button-->
                <button type="reset" id="kt_modal_add_event_cancel" class="btn btn-light me-3">Batal</button>
                <!--end::Button-->
                <!--begin::Button-->
                <button type="submit" id="kt_modal_add_event_submit" class="btn btn-primary btn-add-event">
                    <span class="indicator-label">Tambah</span>
                    <span class="indicator-progress">Sila tunggu...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
                <!--end::Button-->
            </div>
        </form>
        <!-- End::Form -->
    </div>
</div>
<!--end::Offcanvas - New Product-->

<!--begin::Modals-->
<!--begin::Modal - New Product-->
<div class="modal fade" id="kt_modal_view_event" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header justify-content-between">
            <h3 class="modal-title">Maklumat Agenda</h3>


            <div class="d-flex">
                <!--begin::Edit-->
                <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-primary me-2" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Kemaskini Agenda" id="kt_modal_view_event_edit">
                    <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                    <span class="svg-icon svg-icon-2">
                        <span class="far fa-pen-to-square" style="font-size: 18px;"></span>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Edit-->
                <!--begin::Edit-->
                <div class="btn btn-icon btn-sm btn-color-gray-400 btn-active-icon-danger me-2" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Hapuskan Agenda" id="kt_modal_view_event_delete">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                    <span class="svg-icon svg-icon-2">
                        <span class="far fa-trash" style="font-size: 18px;"></span>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Edit-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-color-gray-500 btn-active-icon-primary" data-bs-toggle="tooltip" title="Tutup Agenda" data-bs-dismiss="modal">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1">
                        <span class="far fa-xmark" style="font-size: 18px;"></span>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body pt-0 pb-8 px-lg-15">
                <!--begin::Row-->
                <div class="d-flex justify-content-between ms-n4 my-5">
                    <div class="position-relative ps-4 py-1">
                        <div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-primary">
                        </div>
                        <span class="d-flex align-items-center fs-5 fw-semibold me-3" data-kt-calendar="event_name"></span>
                    </div>
                            <span class="d-flex align-items-center badge badge-light-primary my-2" data-kt-calendar="event_view_group"></span>
                        <!--end::Event name-->
                </div>
                <!--end::Row-->
                <!-- begin::Row -->
                <div class="d-flex align-items-center mb-5 d-none" data-kt-calendar="event_auth">
                        <div class="symbol symbol-35px d-flex align-items-center">
                        <img src="assets/media/authorities/JKR-000000.png" alt="" data-kt-calendar="event_auth_logo">
                        </div>
                        <div class="ms-5 d-flex flex-column align-items-start">
                            <span class="text-dark fw-bold text-hover-primary fs-5 me-4 accordion-icon-on" data-kt-calendar="event_auth_name"></span>
                        </div>
                </div>
                <!-- end::Row -->
                <!--begin::Row-->
                <div class="d-flex justify-content-between align-items-center ">

                    <div class="d-flex flex-column align-items-start">
                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs050.svg-->

                        <div class="d-flex align-items-center mb-3">
                        <i class="fad fa-calendar-clock text-muted fs-5 me-3"></i>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Event start date/time-->
                        <div class="fs-6 me-3 fw-light">
                            <span data-kt-calendar="event_start_date"></span>
                        </div>
                        </div>
                        <!--end::Event start date/time-->
                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs050.svg-->
                        <div class="d-flex align-items-center mb-3">
                        <i class="fad fa-calendar-check text-muted fs-5 me-3"></i>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Event end date/time-->
                        <div class="fs-6 fw-light">
                            <span data-kt-calendar="event_end_date"></span>
                        </div>
                    </div>
                        <!--end::Event end date/time-->
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-light-success" data-kt-calendar="all_day"></span>
                    </div>
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="d-flex align-items-center mb-3">
                    <!--begin::Icon-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen018.svg-->
                    <i class="fad fa-location-dot text-muted fs-5 me-5"></i>
                    <!--end::Svg Icon-->
                    <!--end::Icon-->
                    <!--begin::Event location-->
                    <div class="fs-6 fw-light" data-kt-calendar="event_location"></div>
                    <!--end::Event location-->
                </div>
                <!--end::Row-->
                <div class="d-flex align-items-start">
                    <!--begin::Icon-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                    <i class="far fa-calendar-lines-pen text-muted fs-5 me-3 mt-1"></i>
                    <!--end::Svg Icon-->
                    <!--end::Icon-->
                    <div class="mb-5">
                        <!--begin::Event description-->
                        <div class="fs-6 fw-light" data-kt-calendar="event_description"></div>
                        <!--end::Event description-->
                    </div>
                </div>
            </div>
            <!--end::Modal body-->
        </div>
    </div>
</div>
<!--end::Modal - New Product-->
<!--end::Modals-->