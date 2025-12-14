<?php

foreach (Survey::surveyTaskModal() as $id) {
    if ($id['MappingID'] == 0 || $id['MappingID'] == 75) {
        if ($_SESSION['roleId'] == 67) {
            //tmp_date = 0 
            if ($id['subMappingID'] == 15) {
            echo '<!--begin::Modal-->  
            <div class="modal fade" tabindex="-1" id="action-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Tetapkan Set Tandatangan SR</h3>

                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>

                        <form class="modal-body" novalidate="novalidate" id="formSurvey-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '">
                            <!--begin::Input group-->
                            <div class="mb-10 fv-row">
                                <label class="fs-5 fw-semibold form-label mb-0">Bilangan Set Pelan : </label>
                                <div class="d-flex mt-5">
                                    <div class="form-check me-5">
                                        <input class="form-check-input" type="radio" name="set-sr-sign" id="radio-1-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '" value="1">
                                        <label class="form-check-label" for="radio-1-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '">1</label>
                                    </div>
                                    <div class="form-check me-5">
                                        <input class="form-check-input" type="radio" name="set-sr-sign" id="radio-2-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '" value="2">
                                        <label class="form-check-label" for="radio-2-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '">2</label>
                                    </div>
                                    <div class="form-check me-5">
                                        <input class="form-check-input" type="radio" name="set-sr-sign" id="radio-3-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '" value="3">
                                        <label class="form-check-label" for="radio-3-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '">3</label>
                                    </div>
                                    <div class="form-check me-5">
                                        <input class="form-check-input" type="radio" name="set-sr-sign" id="radio-4-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '" value="4">
                                        <label class="form-check-label" for="radio-4-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '">4</label>
                                    </div>
                                    <div class="form-check me-5">
                                        <input class="form-check-input" type="radio" name="set-sr-sign" id="radio-5-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '" value="5">
                                        <label class="form-check-label" for="radio-5-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '">5</label>
                                    </div>
                                </div>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Submit button-->
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="submit-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="d-none btn btn-' . $id['StatusColor'] . '">
                                <input type="text" id="sid-' . $id['subMappingID'] . $id['MappingID']. $id['ID'] .'" name="system-id" value="'. $id['system_id'] .'" hidden/>
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label"><i class="fas fa-check"></i> Sahkan</span>
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