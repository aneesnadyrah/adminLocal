<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-approve-tp" style="overflow-y: scroll !important; ">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header py-3 d-flex justify-content-between">
                <!--begin::Modal title-->
                <h2>Semak Pindaan Cadangan Teknikal</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fad fa-xmark fs-4"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            
            <form id='form_approval_tp' class="modal-body" novalidate="novalidate" data-form="form-approval-tp" data-route="approval">
            <!-- <form id="amend_sv_amendReview_form" action="#" method="POST"> -->
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body text-center">Adakah anda ingin mengesahkan Pindaan Cadangan Teknikal ini?</h4>
                        <div class="onboarding-info text-center mb-5">Sila masukkan catatan bagi permohonan ini.</div>

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
                    <button type="submit" data-submit="approval-tp" class="btn btn-primary">
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