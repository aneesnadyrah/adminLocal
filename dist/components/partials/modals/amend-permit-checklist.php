<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-amend-permit">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header py-3 d-flex justify-content-between">
                <!--begin::Modal title-->
                <h2>Pinda Permohonan Permit</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                    <span class="svg-icon svg-icon-1">
                        <img src="assets/media/icons/duotune/arrows/arr061.svg" alt="image" />
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <form id="amend_permit_checklist" action="#" method="POST">
                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-10">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Pinda Permohonan Permit ini?</h4>
                        <div class="onboarding-info">Sila masukkan butiran pembetulan yang lengkap bagi memudahkan
                            pemohon untuk mengemaskini.</div>

                        <!--begin::Input group-->
                        <div class="mb-5 mt-5 fv-row">
                            <div class="d-flex flex-stack">
                                <!--begin::Label-->
                                <div class="fw-semibold me-5">
                                    <label class="form-label">1. Maklumat Pegawai</label>
                                </div>
                                <!--end::Label-->
                            </div>

                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="applicant" />
                                    <span class="form-check-label">Kontraktor</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="officer" />
                                    <span class="form-check-label">Sub-Kontraktor</span>
                                </label>
                                <!--end::Checkbox-->
                            </div>
                            <!--end::Checkboxes-->

                            <div class="d-flex flex-column mb-8 mt-3">
                                <textarea class="form-control" placeholder="Catatan" name="notes_2"></textarea>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="mb-5 mt-5 fv-row">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack">
                                <!--begin::Label-->
                                <div class="fw-semibold me-5">
                                    <label class="form-label">2. Senarai Semak Dokumen Permit</label>
                                </div>
                                <!--end::Label-->
                            </div>

                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="provider" />
                                    <span class="form-check-label">Surat Serahan Permohonan</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="title" />
                                    <span class="form-check-label">Pelan Infrastruktur Utiliti</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="info" />
                                    <span class="form-check-label">Pelan Kawalan Trafik</span>
                                </label>
                                <!--end::Checkbox-->
                            </div>
                            <!--end::Checkboxes-->
                            <!--begin::Checkboxes-->
                            <div class="d-flex align-items-center">
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="applicant" />
                                    <span class="form-check-label">Surat Serahan Permohonan</span>
                                </label>
                                <!--end::Checkbox-->
                                <!--begin::Checkbox-->
                                <label class="form-check form-check-custom form-check-solid me-10">
                                    <input class="form-check-input h-20px w-20px" type="checkbox" name="officer" />
                                    <span class="form-check-label">Sub-Kontraktor</span>
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

                        <input type="text" name="system_id" id="system-id" value="<?php echo $systemId; ?>" hidden>
                    </div>
                </div>
                <!--end::Modal body-->

                <!--begin::Modal footer-->
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="amend_app_submit" class="btn btn-primary me-3">Hantar</button>
                </div>
                <!--end::Modal footer-->
                <input type="text" name="route" value="amend" hidden />
                <input type="text" name="ref_no" value="<?php echo $detail['reference_no']; ?>" hidden />
            </form>
        </div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Upgrade plan-->