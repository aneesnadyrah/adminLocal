<?php

$option = '<option></option>';
$option2 = '<option></option>';

echo '<!--begin::Modal - Customers - Add-->
<div class="modal fade" id="modal_letter_in" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Surat Masuk</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fad fa-xmark fs-2"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Form-->
            <form class="modal-body" novalidate="novalidate" id="form_add_letter_in">
                <!--begin::Modal body-->
                <div class="modal-body">
                    <!--begin::Scroll-->
                    <div class="scroll-y me-n7 pe-7" id="kt_modal_add_customer_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_customer_header" data-kt-scroll-wrappers="#kt_modal_add_customer_scroll" data-kt-scroll-offset="300px">
                    <!--begin::Dropzone-->
                        <div class="dropzone border-primary bg-light-primary mb-5" id="dropzone_letter_in">
                            <!--begin::Message-->
                            <div class= "dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik dokumen surat</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf sahaja</span>
                                </div>
                                <!--end::Info-->
                            </div>
                        </div>
                        <!--end::Dropzone-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Tajuk Surat</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="4" name="title" placeholder="Tajuk Surat"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">No Rujukan Kami</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" placeholder="No Rujukan Surat" name="no_ref_letter" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">No Rujukan Tuan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" placeholder="No Rujukan Tuan" name="no_ref_tuan" />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row g-9 mb-7">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-semibold mb-2">Pengirim</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" placeholder="" name="sender" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-semibold mb-2">Diminit Kepada</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select id="selection-staff" class="form-select form-select-solid" name="receiver" data-placeholder="Sila Pilih Minit Surat" data-allow-clear="true">' . $option . '</select>
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row g-9 mb-7">
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-semibold mb-2">Tarikh Surat Diterima</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="position-relative d-flex align-items-center">
                                    <!--begin::Icon-->
                                    <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                        <span class="symbol-label bg-secondary">
                                            <i class="fad fa-calendar"></i>
                                        </span>
                                    </div>
                                    <!--end::Icon-->
                                    <!--begin::Datepicker-->
                                    <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila pIlih Tarikh" name="date_receive" type="text" readonly="readonly" fdprocessedid="0bowoa" id="single-date-picker">
                                    <!--end::Datepicker-->
                                </div>
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-6 fw-semibold mb-2">Melalui</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <select id="selection-by" class="form-select form-select-solid" name="method" data-placeholder="Sila Pilih Melalui" data-allow-clear="true">
                                    <option value="emel" data-icon="fa-envelope fs-5">Emel</option>
                                    <option value="fax" data-icon="fa-fax fs-5">Fax</option>
                                    <option value="pos" data-icon="fa-mailbox-flag-up fs-5">Pos</option>
                                    <option value="byHand" data-icon="fa-hand-holding fs-5">Serahan Tangan</option>
                                </select>
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Jenis Surat</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select id="selection-type" class="form-select form-select-solid" name="type" data-placeholder="Sila Pilih Jenis Surat" data-allow-clear="true">' . $option2 . '</select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Catatan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="4" name="notes" placeholder="Catatan"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Scroll-->
                </div>
                <!--end::Modal body-->
                
                <!--begin::Modal footer-->
                <div class="d-flex justify-content-end">
                    <button type="submit" id="submit_add_letter" class="btn btn-primary">
                        <!--begin::Indicator label-->
                        <span class="indicator-label"><i class="fad fa-user-pen"></i> Hantar</span>
                        <!--end::Indicator label-->
                        <!--begin::Indicator progress-->
                        <span class="indicator-progress">Sila Tunggu...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                        <!--end::Indicator progress-->
                    </button>
                </div>
                <!--end::Modal footer-->
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
<!--end::Modal - Customers - Add-->';

?>