<!--begin::Modal-->
<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-amend">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h3 class="modal-title">Pinda Permohonan</h3>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fad fa-xmark fs-4"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            

            <form class="modal-body" novalidate="novalidate" data-form="form-amend" data-route="amendment">
                <!--begin::Modal body-->
                
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body">Pinda Permohonan ini?</h4>
                    <div class="onboarding-info">Sila masukkan butiran pembetulan yang lengkap bagi memudahkan
                        pemohon untuk mengemaskini.</div>

                    <!--begin::Input group-->
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

                    <!--begin::Input group-->
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

                    <!--begin::Input group-->
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
                </div>
   
                <!--end::Modal body-->

                <!--begin::Modal footer-->
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="amend_app_submit" data-submit="amend" class="btn btn-primary me-3">Hantar</button>
                </div>
                <!--end::Modal footer-->
            </form>
        </div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Upgrade plan-->