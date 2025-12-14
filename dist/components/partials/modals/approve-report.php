<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-approve">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <h3 class="modal-title">Pengesahan Laporan</h3>
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fad fa-xmark fs-2"></i>
                </div>
            </div>
            <!--end::Modal header-->

            <form id="report-review">
                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-10">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Majukan Permohonan ini ke :</h4>
                        <label class="form-check form-check-custom form-check-solid me-10 mb-1">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="proceed-amend" />
                            <span class="form-check-label">Pindaan Cadangan Teknikal.</span>
                        </label>
                        <label class="form-check form-check-custom form-check-solid me-10">
                            <input class="form-check-input h-20px w-20px" type="checkbox" name="proceed-wl" />
                            <span class="form-check-label">Penyediaan Sebut Harga & Permohonan Izin Lalu.</span>
                        </label>
                        <div class="mt-5 onboarding-info">Sila masukkan catatan pengesahan laporan.</div>

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
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="report-review-submit" class="btn btn-primary me-3">
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