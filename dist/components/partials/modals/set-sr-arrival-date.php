<?php

foreach (Survey::surveyTaskModal() as $id) {
    if ($id['MappingID'] == 0 || $id['MappingID'] == 74) {
        if ($_SESSION['roleId'] == 67) {
            //tmp_date = 0 
            if ($id['subMappingID'] == 14) {
            echo '<!--begin::Modal-->  
            <div class="modal fade" tabindex="-1" id="action-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Tetapkan Tarikh Pengesahan Juru Ukur</h3>

                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>

                        <form class="modal-body" novalidate="novalidate" id="formSurvey-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '">
                            <!--begin::Input group-->
                            <div class="form-floating mb-7 fv-row">
                                <input class="form-control form-control-solid" id="sr-arrival-date-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" name="sr-arrival-date" placeholder="Tarikh"/>
                                <label for="date">Tarikh</label>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="form-floating mb-7 fv-row">
                                <input type="text" class="form-control form-control-solid" id="sr_arrival_remark" name="sr-arrival-remark" placeholder="Catatan"/>
                                <label for="sr_arrival_remark">Catatan</label>
                                <input type="text" id="sid-' . $id['subMappingID'] . $id['MappingID']. $id['ID'] .'" name="system-id" value="'. $id['system_id'] .'" hidden/>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Submit button-->
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="submit-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="d-none btn btn-' . $id['StatusColor'] . '">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label"><i class="fas fa-check"></i> Tetapkan</span>
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
}

?>