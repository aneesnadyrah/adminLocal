<!--begin::Modal-->
<div class="modal fade" tabindex="-1" id="modal-approve-sv-review" style="overflow-y: scroll !important; ">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header py-3 d-flex justify-content-between">
                <!--begin::Modal title-->
                <h2>Sahkan Pindaan Permohonan</h2>
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
            
            <form id="approve_sv_amendReview_form" action="#" method="POST">
                <!--begin::Modal body-->
                <div class="modal-body">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body text-center mb-5">Sahkan Pindaan Permohonan ini?</h4>
                        <!-- <div class="onboarding-info text-center mb-5">Sila masukkan catatan bagi permohonan ini.</div> -->

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <!--begin::Checkboxes-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-custom form-check-solid me-10">
                                            <input class="form-check-input h-20px w-20px" type="checkbox" value="1" name="sitevisit" />
                                            <span class="form-check-label onboarding-info">Adakah perlu lawatan tapak ?</span>
                                        </label>
                                        <!--end::Checkbox-->
                                    </div>
                                    <!--end::Checkboxes-->
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="required form-label">Catatan</label>
                                    <textarea class="form-control" placeholder="Catatan" name="notes" rows="3" required></textarea>
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
                    <button type="submit" id="approve_sv_amendReview_submit" class="btn btn-primary me-3">Hantar</button>
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
