<?php 
$systemId = $_GET['id'] ?? NULL;
$roleId = $_SESSION['roleId'];
$List = new Lists();
$details = new projectDetails();
?>

<div class="modal fade" id="modal_jump_step_app" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered ">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Langkau Status Permohonan</h2>
                <!--end::Modal title-->

                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-duotone fa-square-xmark fs-1"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--begin::Form-->
            <form novalidate="novalidate" id="form_jump_step">
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
                    <?php 
                        if($roleId == 2 || $roleId == 14 || $roleId == 18 || $roleId == 19 || $roleId == 20 || $roleId == 31 || $roleId == 32) {
                            $department = "operation";
                        } else if($roleId === 4 || $roleId == 5) {
                            $department = "finance";
                        } else if($roleId == 6 || $roleId == 7 || $roleId == 8 || $roleId == 9 || $roleId == 10 || $roleId == 11) {
                            $department = "geospatial";
                        } else if($roleId == 24 || $roleId == 26 || $roleId == 28) {
                            $department = "mapping";
                        } else {
                            $department = '';
                        }

                        $statuses = $List->getBy('ls_statuses','departments', $department);

                        // value retrieved from the database
                        $selectedValue = $details->getStatus('ctrl_statuses',$department,$systemId);

                        if($roleId == 2 || $roleId == 14 || $roleId == 18 || $roleId == 19 || $roleId == 20 || $roleId == 31 || $roleId == 32 || $roleId === 4 || $roleId == 5 || $roleId == 6 || $roleId == 7 || $roleId == 8 || $roleId == 9 || $roleId == 10 || $roleId == 11 || $roleId == 24 || $roleId == 26 || $roleId == 28) {
                            $selectedValues = $selectedValue;

                        } else {
                            $selectedValues = 0;
                        }

                        $statusName = $details->getStatusName($selectedValues);
                        ?>
                        
                        <!--begin::Input group-->
                        <div class="fv-row mb-10">
                            <!--begin::Label-->
                            <label class="form-label">Status Permohonan</label>
                            <span class="text-danger fw-semibold fs-7">Status semasa permohonan ialah <b><?php echo $statusName ?></b>, sila pilih status dibawah untuk tindakan langkau status yang diinginkan.</span>
                            <!--end::Label-->
                            <!--begin::Select2-->
                            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status" data-table-filter="status" name="jump_status">
                            <?php
                            foreach($statuses as $status) {
                                $selected = $status->id == $selectedValue ? 'selected' : '';
                                echo '<option value="'.$status->id.'" '.$selected.'>'.$status->flow_name.'</option>';
                            }
                            ?>
                            </select>
                            <!--end::Select2-->
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
                            <input type="hidden" id="department" name="department"  value="<?php echo $department ?>" >
                            <input type="hidden" id="current-status" name="current-status"  value="<?php echo $selectedValue ?>" >
                            <input type="hidden" id="system-id" name="system-id"  value="<?php echo $systemId ?>" >
                            <button type="submit" class="btn btn-primary" id="submit_jump_step"
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