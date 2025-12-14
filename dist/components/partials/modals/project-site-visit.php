<?php
$option = '';
foreach (General::selection('ls_authorities') as $row) {
    $option .= '<option value="'. $row['id'] .'">' . $row['name'] . '</option>';
}

foreach (Tasking::taskModal() as $AIDRow) {
    if ($appsTitle === 'KUDRAT'){
        if($AIDRow['StatusID'] == 10){
            echo '<!--begin::Modal-->
            <div class="modal fade" tabindex="-1" id="action-'. $AIDRow['StatusID']. $AIDRow['ID'] .'" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Cipta Rekod Lawatan Tapak</h3>

                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-'. $AIDRow['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>
                        <form class="modal-body" novalidate="novalidate" id="form-'. $AIDRow['StatusID']. $AIDRow['ID'] .'">
                            <div class="mb-5 fv-row">
                                <label class="required form-label">Jenis Lawatan Tapak</label>
                                <select id="selection-'. $AIDRow['StatusID']. $AIDRow['ID'] .'" class="form-select form-select-solid" name="report-type" disabled>
                                    <option></option>
                                    <option value="1" selected>Lawatan Tapak Awalan</option>
                                    <option value="2">Lawatan Tapak Bersama</option>
                                    <option value="3">Lawatan Tapak Berkala</option>
                                </select>
                                <div class="fw-semibold text-gray-400 mb-6 ps-1 fst-italic">*Hanya pilihan ini tersedia buat masa ini.</div>
                            </div>
                            <input type="text" name="system-id"  value="'. $AIDRow['SysID'] .'" hidden>
                            <!--begin::Submit button-->
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="submit-'. $AIDRow['StatusID'] . $AIDRow['ID'] .'"  class="d-none btn btn-'. $AIDRow['StatusColor'] . '">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label"><i class="fad fa-file-circle-plus"></i> Cipta</span>
                                    <!--end::Indicator label-->
                                    <!--begin::Indicator progress-->
                                    <span class="indicator-progress">Sila Tunggu...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                    <!--end::Indicator progress-->
                                </button>
                            </div>
                            <!--end::Submit button-->
                        </form>
                    </div>
                </div>
            </div>
            <!--end::Modal-->';
        }
    } else {
        if($AIDRow['StatusID'] == 6){
        echo '<!--begin::Modal-->
        <div class="modal fade" tabindex="-1" id="action-'. $AIDRow['StatusID']. $AIDRow['ID'] .'" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Cipta Rekod Lawatan Tapak</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-'. $AIDRow['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" id="form-'. $AIDRow['StatusID']. $AIDRow['ID'] .'">
                        <div class="mb-5 fv-row">
                            <label class="required form-label">Jenis Lawatan Tapak</label>
                            <select id="selection-'. $AIDRow['StatusID']. $AIDRow['ID'] .'" class="form-select form-select-solid" name="report-type" disabled>
                                <option></option>
                                <option value="1" selected>Lawatan Tapak Awalan</option>
                                <option value="2">Lawatan Tapak Bersama</option>
                                <option value="3">Lawatan Tapak Berkala</option>
                            </select>
                            <div class="fw-semibold text-gray-400 mb-6 ps-1 fst-italic">*Hanya pilihan ini tersedia buat masa ini.</div>
                        </div>
                        <input type="text" name="system-id"  value="'. $AIDRow['SysID'] .'" hidden>
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="submit-'. $AIDRow['StatusID'] . $AIDRow['ID'] .'"  class="d-none btn btn-'. $AIDRow['StatusColor'] . '">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fad fa-file-circle-plus"></i> Cipta</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">Sila Tunggu...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>
                        </div>
                        <!--end::Submit button-->
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->';
    }
    }


}

?>