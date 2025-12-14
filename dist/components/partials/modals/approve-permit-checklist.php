<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-approve" style="overflow-y: scroll !important; ">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header py-3 d-flex justify-content-between">
                <h2>Sahkan Permohonan</h2>
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
            
            <form id="approve_app_form" action="#" method="POST">
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body text-center mb-8">Sahkan Permohonan Permit ini?</h4>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Catatan</label>
                                    <textarea class="form-control" placeholder="Catatan" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <input type="text" name="system-id" id="system-id" value="<?php echo $systemId; ?>" hidden>
                    </div>
                </div>
                <!--end::Modal body-->
                
                <!--begin::Modal footer-->
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>

                    <button type="submit" id="approve_app_submit" class="btn btn-primary me-3">
                        <span class="indicator-label">
                            Hantar
                        </span>
                        <span class="indicator-progress">
                            Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
                <!--end::Modal footer-->
                <input type="text" name="route" value="approve" hidden />
                <input type="text" name="ref-no" value="<?php echo $detail['reference_no']; ?>" hidden />
            </form>
        </div>
        <!--end::Modal content-->
    </div>
</div>
<!--end::Modal - Upgrade plan-->
