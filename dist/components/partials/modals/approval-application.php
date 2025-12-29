<!--begin::Modal-->
<div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-approve">
    <div class="modal-dialog modal-dialog-centered">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h3 class="modal-title">Sahkan Permohonan</h3>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="fad fa-xmark fs-4"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <form id='form_approval' class="modal-body" novalidate="novalidate" data-form="form-approval" data-route="approval">
                <h4 class="fw-semibold mb-5">Adakah anda ingin mengesahkan permohonan ini?</h4>
                <?php
                    echo <<<TEMPLATE
                        <div class="fv-row mb-8">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Jenis Utiliti</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Sila pilih jenis utiliti" name="utility_type" required>
                                <option></option>
                                <option value="PWR">Tenaga</option>
                                <option value="TEL">Telekomunikasi</option>
                                <option value="WTR">Air</option>
                                <option value="SWR">Kumbahan</option>
                                <option value="GAS">Minyak & Gas</option>
                                <option value="OTR">Lain-lain</option>
                            </select>
                            <!--end::Input-->
                        </div>
                    TEMPLATE;
                ?>

                <?php
                    $systemId = $_GET['id'];
                    $general = new General();
                    $dataEntry = $general->getDataEntry($systemId);
                    $providerId = $dataEntry['utility_provider'];
                ?>
                <input type="hidden" name="state" value="<?php echo $dataEntry['state']; ?>" />
                <input type="hidden" name="utility_provider" value="<?php echo $dataEntry['utility_provider']; ?>" />
                <!-- If Utility is TNB or TM, then enable this -->
                <?php if ($providerId == 5 || $providerId == 7) { ?>
                    <div class="fv-row mb-8">
                        <label class="required fs-6 fw-semibold mb-2">Label Permohonan</label>
                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Sila pilih label permohonan" name="appl_label" required>
                            <option></option>
                            <option value="Sistem">Sistem</option>
                            <option value="Bekalan">Bekalan</option>
                        </select>
                    </div>
                <?php } ?>

                <div class="fv-row mb-8">
                    <label class="required fs-6 fw-semibold mb-2">Jenis Caj Pendaftaran</label>
                    <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Sila pilih jenis caj pendaftaran" name="fee_label" required>
                        <option></option>
                        <option value="4">Toyyibpay</option>
                        <option value="2">Invois</option>
                    </select>
                </div>

                <div class="mb-10">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control form-control-solid" placeholder="Sila isi catatan jika ada..." name="notes" rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" data-submit="approval" class="btn btn-primary">
                        <span class="indicator-label">
                            Hantar
                        </span>
                        <span class="indicator-progress">
                            Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!--end::Modal content-->
</div>
<!--end::Modal - Upgrade plan-->