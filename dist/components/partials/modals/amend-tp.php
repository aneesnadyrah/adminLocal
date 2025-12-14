<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-amend-tp" style="overflow-y: scroll !important; ">
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header py-3 d-flex justify-content-between">
                <!--begin::Modal title-->
                <h2>Butiran Maklumat Pindaan</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fad fa-xmark fs-4"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            
            <form id='form_amend_tp' data-form="form-amend-tp" data-route="amend">
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="onboarding-content mb-0">
                        <div class="onboarding-info">Sila masukkan butiran pembetulan yang lengkap bagi memudahkan pemohon untuk mengemaskini.</div>

                        <!--begin::Input group Maklumat Permohonan-->
                        <div class="mb-5 mt-5 fv-row">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack">
                                <!--begin::Label-->
                                <div class="fw-semibold me-5">
                                    <label class="form-label">1. Maklumat Permohonan</label>
                                </div>
                                <!--end::Label-->
                            </div>

                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="provider" />
                                    <span class="form-check-label">Penyedia Utiliti</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="title" />
                                    <span class="form-check-label">Tajuk</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="info" />
                                    <span class="form-check-label">Maklumat</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="appl_letter" />
                                    <span class="form-check-label">Surat Permohonan</span>
                                </label>
                                <!--end::Checkbox-->
                            </div>
                            <!--end::Checkboxes-->

                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 mt-3">
                                <textarea class="form-control" placeholder="Catatan" name="notes_1"></textarea>
                            </div>
                            <!--end::Input group-->
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Input group Maklumat Permohonan-->

                        <!--begin::Input group Maklumat Pegawai-->
                        <div class="mb-5 mt-5 fv-row">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack">
                                <!--begin::Label-->
                                <div class="fw-semibold me-5">
                                    <label class="form-label">2. Maklumat Pegawai</label>
                                </div>
                                <!--end::Label-->
                            </div>

                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="applicant" />
                                    <span class="form-check-label">Pemohon</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="officer" />
                                    <span class="form-check-label">Pegawai</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="ack_letter" />
                                    <span class="form-check-label">Surat Perakuan</span>
                                </label>
                                <!--end::Checkbox-->
                            </div>
                            <!--end::Checkboxes-->

                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 mt-3">
                                <textarea class="form-control" placeholder="Catatan" name="notes_2"></textarea>
                            </div>
                            <!--end::Input group-->
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Input group Maklumat Pegawai-->

                        <!--begin::Input group Maklumat Jalan-->
                        <div class="mt-5 fv-row">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack">
                                <!--begin::Label-->
                                <div class="fw-semibold me-5">
                                    <label class="fs-6">3. Maklumat Jalan</label>
                                </div>
                                <!--end::Label-->
                            </div>

                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="district" />
                                    <span class="form-check-label fw-semibold">Daerah</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox"
                                        name="road" />
                                    <span class="form-check-label fw-semibold">Jalan</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox"
                                        name="doc_technical" />
                                    <span class="form-check-label fw-semibold">Dokumen Teknikal</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox"
                                        name="plan_location" />
                                    <span class="form-check-label fw-semibold">Gambar Lokasi</span>
                                </label>
                                <!--end::Checkbox-->
                            </div>
                            <!--end::Checkboxes-->

                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-3 mt-3">
                                <textarea class="form-control" placeholder="Catatan" name="notes_3"></textarea>
                            </div>
                            <!--end::Input group-->
                            <!--end::Wrapper-->
                        </div>

                        <!--end::Input group Maklumat Jalan-->
                    </div>
                </div>
                <!--end::Modal body-->
                
                <!--begin::Modal footer-->
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal" id="amendTPCancelBtn">Batal</button>
                    <button type="submit" data-submit="amend-tp" class="btn btn-primary">
                        <span class="indicator-label">
                            Simpan
                        </span>
                        <span class="indicator-progress">
                            Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Upgrade plan-->