<?php 
$systemId = $_GET['id'] ?? NULL;
?>

<div class="modal fade" id="modal_cancel_app" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered ">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Pembatalan Permohonan</h2>
                <!--end::Modal title-->

                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-duotone fa-square-xmark fs-1"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--begin::Form-->
            <form novalidate="novalidate" id="form_cancel_application">
                <!--begin::Modal body-->
                <div class="modal-body py-lg-10 px-lg-10">
                    <?php 
                    $appno = $details->getReferenceNo($systemId);
                    if(!empty($appno)) {
                        $app_no = $appno;
                    } else {
                        $app_no = $systemId;
                    }
                    ?>

                    <div>
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="form-label">No Permohonan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <span class="form-control form-control-solid"><?php echo $app_no ?></span>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>

                    <div>
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="form-label required">Catatan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="3" name="notes"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>

                    <!--begin::Actions-->
                    <div class="d-flex flex-end mt-5">
                        <!--begin::Wrapper-->
                        <div class="me-2">
                            <button type="button" class="btn btn-light btn-active-light-primary" data-bs-toggle="modal" data-modal-action="back">
                                Kembali
                            </button>
                        </div>
                        <!--end::Wrapper-->

                        <!--begin::Wrapper-->
                        <div>
                            <input type="hidden" id="system-id" name="system-id"  value="<?php echo $systemId ?>" >
                            <button type="submit" class="btn btn-primary" id="submit_cancel_application"
                                data-kt-stepper-action="submit">
                                <span class="indicator-label">
                                    Hantar
                                </span>
                                <span class="indicator-progress">
                                    Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Actions-->

                </div>
                <!--end::Modal body-->

            </form>
            <!--end::Form-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>