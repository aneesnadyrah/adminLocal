<div class="modal fade" id="modal_note_ref" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal body-->
            <div class="modal-body py-lg-10 px-lg-10">
                <!--begin::Search-->
                <div id="modal_note_handler" data-kt-search-keypress="true" data-kt-search-min-length="4"
                    data-kt-search-enter="enter" data-kt-search-layout="inline">
                    <!--begin::Form-->
                    <form data-kt-search-element="form" class="w-100 position-relative mb-5" autocomplete="off" data-form="reference">
                        <!--begin::Hidden input(Added to disable form autocomplete)-->
                        <input type="hidden" />
                        <!--end::Hidden input-->
                        <!--begin::Icon-->
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <i
                            class="far fa-search text-gray-500 fs-3 position-absolute top-50 ms-5 translate-middle-y"></i>
                        <!--end::Svg Icon-->
                        <!--end::Icon-->
                        <!--begin::Input-->
                        <input type="text" class="form-control form-control-lg form-control-solid px-15"
                            style="height:50px;" name="project_note" value=""
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
                            <span class="far fa-xmark fs-3 me-0"></span>
                            <!--end::Svg Icon-->
                        </span>
                        <!--end::Reset-->
                    </form>
                    <!--end::Form-->
                    <!--begin::Wrapper-->
                    <div class="separator separator-dashed"></div>
                    <div class="py-5">
                        <!--begin::Results(add d-none to below element to hide the users list by default)-->
                        <form data-form="reference_no">
                            <div data-kt-search-element="results" class="d-none"></div>
                        </form>
                        <!--end::Results-->
                        <!--begin::Empty-->
                        <div data-kt-search-element="empty" class="text-center d-none">
                            <!--begin::Message-->
                            <div class="fw-semibold py-10">
                                <div class="text-gray-600 fs-3 mb-2">Nombor Rujukan Tidak Dijumpai</div>
                                <div class="text-muted fs-6">Cuba cari dengan Nombor Rujukan Permohonan Penuh...</div>
                            </div>
                            <!--end::Message-->
                        </div>
                        <!--end::Empty-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Search-->
                <div data-form-view="authority" class="mb-3">

                    <form data-form="authority">
                        <div class="mb-5" data-view="authorityTitle"></div>
                        <style>
                            .form-check-image .form-check-wrapper {
                                border: 0px solid transparent !important;
                            }

                            .form-check-image.active:not(.form-check-success):not(.form-check-danger) .form-check-wrapper {
                                border: 0px solid transparent !important;
                            }
                        </style>
                        <!--begin::Authority-->
                        <div class="d-flex justify-content-center align-items-center fv-row mb-5 flex-wrap" data-view="authority"
                            data-kt-buttons="true" data-kt-buttons-target=".form-check-image, .form-check-input"></div>
                        <!--end::Authority-->
                    </form>
                </div>
                <!--end-->
                <!--begin::Actions-->
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary" data-modal-action="continue"

                        >Seterusnya</button>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Stepper-->
    </div>
    <!--end::Modal dialog-->
</div>

<div class="modal fade" id="modal_note_item" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-550px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            <div class="modal-body px-lg-10">
                <form data-form="notes">
                    <!--begin::Row-->
                    <div class="row mb-5 fv-row" data-kt-buttons="true"
                        data-kt-buttons-target=".form-check-image, .form-check-input">
                        <label class="form-label required mb-3">Pilih Jenis Catatan</label>
                        <!--begin::Col-->
                        <div class="col-6 me-0">
                            <!--begin::Option-->
                            <input type="radio" class="btn-check" name="type_note" value="write" id="type-1" />
                            <!-- <label class="form-check-image p-2"  for="radio-add-notes-1"> -->
                            <label
                                class="btn btn-outline btn-outline-dashed btn-active-light-primary p-2 d-flex align-items-center mb-5"
                                for="type-1">
                                <div class="form-check-wrapper">
                                    <div class="d-flex flex-stack opacity-75-hover">
                                        <div class="btn btn-icon btn-light-primary me-2">
                                            <i class="fad fa-pencil-mechanical fs-2x text-primary"></i>
                                        </div>
                                        <span class="fw-semibold">Catatan Bertulis</span>
                                    </div>
                                </div>
                            </label>
                            <!--end::Option-->
                        </div>
                        <!--end::Col-->

                        <!--begin::Col-->
                        <div class="col-6 ms-0">
                            <!--begin::Option-->
                            <input type="radio" class="btn-check" name="type_note" value="attach" id="type-2" />
                            <label
                                class="btn btn-outline btn-outline-dashed btn-active-light-primary p-2 d-flex align-items-center"
                                for="type-2">
                                <div class="form-check-wrapper">
                                    <div class="d-flex flex-stack opacity-75-hover">
                                        <div class="btn btn-icon btn-light-primary me-2">
                                            <i class="fad fa-file-alt fs-2x text-primary"></i>
                                        </div>
                                        <span class="fw-semibold">Catatan Berlampiran</span>
                                    </div>
                                </div>
                            </label>
                            <!--end::Option-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->

                    <div class="d-none" data-form-view="attachment">
                        <!--begin::Input group-->
                        <div class="row">
                            <div class="col-md-6">
                                <!--begin::Input group for dropdown-->
                                <div class="mb-7 fv-row">
                                    <label class="form-label required" for="select_lampiran">Pilih Jenis
                                        Lampiran</label>
                                    <select class="form-select" data-control="select2"
                                        data-dropdown-parent="#modal_note_item"
                                        data-placeholder="Sila Pilih Jenis Lampiran" id="select_lampiran" name="select_lampiran">
                                        <option></option>
                                        <option value="TL">Tangkap Layar (Screenshot)</option>
                                        <option value="SRT">Surat</option>
                                    </select>
                                </div>
                                <!--end::Input group for dropdown-->
                            </div>

                            <div class="col-md-6">
                                <!--begin::Input group for "Tarikh" input-->
                                <div class="mb-7 fv-row">
                                    <label class="required form-label">Tarikh</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                            <span class="symbol-label bg-transparent">
                                                <i class="fad fa-calendar fs-5"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Datepicker-->
                                        <input class="form-control form-control ps-12 flatpickr-input"
                                            placeholder="Sila Pilih Tarikh" name="date_attachment" id="date_attachment" type="text"
                                            data-control="flatpickr">
                                        <!--end::Datepicker-->
                                    </div>
                                </div>
                                <!--end::Input group for "Tarikh" input-->
                            </div>
                        </div>
                        <!--end::Input group-->

                        <div class="d-none" data-form-view="letter">
                            <!--begin::Input group-->
                            <div class="mb-7 fv-row">
                                <label class="form-label required" for="letter_selection">Pilih Jenis Surat</label>
                                <select class="form-select" data-control="select2" data-dropdown-parent="#modal_note_item"
                                    data-placeholder="Sila Pilih Jenis Surat" id="letter_selection" name="type_letter">
                                    <!-- <option></option> -->
                                    <!-- <option value="A">Surat A</option>
                                    <option value="B">Surat B</option>
                                    <option value="C">Surat C</option>
                                    <option value="LL-OF">Lain-lain</option> -->
                                </select>
                            </div>

                            <div class="d-none" data-form-view="other-letter">
                                <!-- Input field to capture user input -->
                                <div class="mb-10 fv-row">
                                    <label class="form-label required" for="other-letter-name">Nama Surat</label>
                                    <input type="text" class="form-control" id="other-letter-name" name="other-letter-name"
                                        placeholder="Sila Masukkan Nama Surat">
                                </div>
                                <!--end::Input group-->
                            </div>
                        </div>

                        <!--begin::Dropzone-->
                        <div class="dropzone border-primary bg-light-primary mb-7 pdf-dropzone" id="dropzone-notes">
                            <!--begin::Message-->
                            <div class="dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf atau .png
                                        sahaja</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <!-- <input type="text" id="sid" name="system-id" value="1" hidden/> -->
                            <input type="text" name="folder" value="folder-add-notes" id="folder" hidden>
                        </div>
                        <!--end::Dropzone-->

                    </div>

                    <div class="d-none" data-form-view="notes">
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="form-label">Catatan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="3" name="notes"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--begin::Actions-->
                    <div class="d-flex flex-stack">
                        <!--begin::Wrapper-->
                        <div class="me-2">
                            <button type="button" class="btn btn-light btn-active-light-primary" data-bs-toggle="modal" data-modal-action="back"
                                data-bs-target="#modal_note_ref">
                                Kembali
                            </button>
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Wrapper-->
                        <div>
                            <button type="submit" class="btn btn-primary" id="submit-add-notes"
                                data-kt-stepper-action="submit">
                                <span class="indicator-label">
                                    Hantar
                                </span>
                                <span class="indicator-progress">
                                    Sila Tunggu... <span
                                        class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Actions-->
                </form>
            </div>
        </div>
    </div>
    <!--end::Modal dialog-->
</div>