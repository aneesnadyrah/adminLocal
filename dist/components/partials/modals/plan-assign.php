<?php
$option = '<option></option>';
foreach (Tasking::assignSelect('1') as $row) {
    if ($row['ProfilePic'] == null) {
        $img = "blank";
    } else {
        $img = $row['ProfilePic'];
    };
    $name = $row['FirstName'];
    $id = $row['username'];

    $option .= '<option value="' . $id . '" data-profile-picture="' . General::getProfile($img) . '.jpg" >' . $name . '</option>';
}

foreach (Survey::surveyTaskModal() as $id) {
    if ($id['MappingID'] == '071' || $id['MappingID'] == '072') {

        if ($_SESSION['roleId'] == 61 || $_SESSION['roleId'] == 63 || $_SESSION['roleId'] == 66) {

            if ($id['subMappingID'] == '010') {         
            echo '<!--begin::Modal-->
                <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $id['subMappingID'] . $id['MappingID'] . $id['ID'] . '">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Lantik Pelukis Pelan Infrastruktur Utiliti</h3>

                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fad fa-xmark fs-2"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <form class="modal-body" novalidate="novalidate" id="formSurvey-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '">
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <select id="selection-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3 mb-2" name="plan-assign-udm">' . $option . '</select>
                                    <label for="selection-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Pelukis Pelan</label>
                                </div><!--begin::Input group-->
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <input class="form-control form-control-transparent rounded rounded-end-0 pt-10" id="modal-date-range-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" name="modal-date-range"/>
                                    <label for="floatingInput" class="fs-5 fw-semibold form-label mb-0">Julat Tarikh Jangkaan Mula - Tamat</label>
                                </div>
                                <!--end::Input group-->  
                                <input type="text" name="system-id"  value="'. $id['system_id'] .'" hidden>
                                <input type="text" id="plan-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" name="plan_assignee" value="1" hidden/>
                                <!--begin::Submit button-->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="submit-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" class="btn btn-' . $id['StatusColor'] . '">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label"><i class="fad fa-user-pen"></i> Lantik</span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">Sila Tunggu...
                                            <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
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
            } else if ($id['subMappingID'] == '012') {
            echo '<!--begin::Modal-->
                <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Lantik Pelukis Pelan Kawalan Trafik</h3>

                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fad fa-xmark fs-2"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <form class="modal-body" novalidate="novalidate" id="formSurvey-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '">
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <select id="selection-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" class="form-select form-select-transparent rounded rounded-end-0 mt-3 mb-2" name="plan-assign-tmp">' . $option . '</select>
                                    <label for="selection-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" class="fs-5 fw-semibold form-label mb-0">Sila Pilih Pelukis Pelan</label>
                                </div><!--begin::Input group-->
                                <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                    <input class="form-control form-control-transparent rounded rounded-end-0 pt-10" id="modal-date-range2-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" name="modal-date-range2"/>
                                    <label for="floatingInput" class="fs-5 fw-semibold form-label mb-0">Julat Tarikh Jangkaan Mula - Tamat</label>
                                </div>
                                <!--end::Input group-->  
                                <input type="text" name="system-id"  value="'. $id['system_id'] .'" hidden>
                                <input type="text" id="plan-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" name="plan_assignee" value="2" hidden/>
                                <!--begin::Submit button-->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="submit-' . $id['subMappingID']   . $id['MappingID'] . $id['ID'] . '" class="btn btn-' . $id['StatusColor'] . '">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label"><i class="fad fa-user-pen"></i> Lantik</span>
                                        <!--end::Indicator label-->
                                        <!--begin::Indicator progress-->
                                        <span class="indicator-progress">Sila Tunggu...
                                            <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
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

