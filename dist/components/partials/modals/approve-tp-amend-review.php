<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-approve-amend-tp" style="overflow-y: scroll !important; ">
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header py-3 d-flex justify-content-between">
                <!--begin::Modal title-->
                <h2>Pengesahan Semakan Pindaan TP</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fad fa-xmark fs-4"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            
            <form id="form_approval_amend_tp" data-form="form-approval-amend-tp" data-route="approvalTPAmend">
                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-10">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Adakah anda ingin mengesahkan Pindaan Cadangan Teknikal ini?</h4>
                        <div class="mt-5 onboarding-info">Sila masukkan catatan pengesahan semakan.</div>

                        <!--begin::Input group-->
                        <div class="mb-5 mt-5 fv-row">

                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 mt-3">
                                <textarea class="form-control" placeholder="Catatan" name="notes"></textarea>
                            </div>
                            <!--end::Input group-->
                            <!--end::Wrapper-->
                        </div>

                        <input type="text" name="system-id" id="system-id" value="<?php echo $systemId; ?>" hidden>
                    </div>
                </div>
                <!--end::Modal body-->

                <!--begin::Modal footer-->
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="report-review-submit" data-submit="approval-amend-tp" class="btn btn-primary me-3">
                        <!--begin::Indicator label-->
                        <span class="indicator-label">Sahkan</span>
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
        </div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Upgrade plan-->