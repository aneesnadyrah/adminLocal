<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-amend-survey-asb" style="overflow-y: scroll !important; ">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header d-flex justify-content-between">
                <h3>Pindaan Laporan Kerja Lapangan</h3>
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fad fa-xmark fs-4"></i>
                </div>
            </div>
            <!--end::Modal header-->
            
            <form id='form_amend_report_asb'>
                <input type="text" name="amend-survey-report-asb" value="1" hidden />
                <input type="text" name="system-id" value="<?php echo $p[0]['SysID']; ?>" hidden />
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body text-center">Adakah anda ingin pinda Laporan Kerja Lapangan ini?</h4>
                        <div class="onboarding-info text-center mb-5">Sila masukkan catatan bagi pindaan ini.</div>

                        <div class="fv-row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="required form-label">Catatan</label>
                                    <textarea class="form-control" placeholder="Catatan" name="notes" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
                
                <!--begin::Modal footer-->
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="amend_report_asb_submit">
                        <span class="indicator-label">
                            Hantar
                        </span>
                        <span class="indicator-progress">
                            Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
                <!--end::Modal footer-->
            </form>
        </div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Upgrade plan-->