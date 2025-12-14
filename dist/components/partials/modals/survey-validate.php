<?php
$forecast = Survey::getForecast();
$option = '<option selected>' . $forecast . '</option>';

// Define the array of weather options
$weatherOptions = array($forecast, 'Cerah', 'Hujan', 'Mendung', 'Berangin', 'Bersalji');

$weatherIcons = array(
    $forecast => 'cloud',
    'Cerah' => 'sun',
    'Hujan' => 'cloud-rain',
    'Mendung' => 'clouds',
    'Berangin' => 'wind',
    'Bersalji' => 'snowflake'
);

foreach ($weatherOptions as $weather) {
    $icon = isset($weatherIcons[$weather]) ? $weatherIcons[$weather] : 'unknown';
    $selected = ($weather == $forecast) ? 'selected' : '';
    $option .= '<option value="' . $weather . '" data-icon-class="fas fa-' . $icon . '"><span class="fas fa-' . $icon . '"></span> ' . $weather . '</option>';
}

foreach (Survey::surveyTaskModal() as $id) {
    if ($id['MappingID'] == 0 || $id['MappingID'] == 62) {
        // if ($_SESSION['roleId'] == 61 || $_SESSION['roleId'] == 62 || $_SESSION['roleId'] == 64) {
            if ($id['subMappingID'] == 3) {   
            $bit64 = Survey::generateUniqueId();    
            echo '<!--begin::Modal-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Pengesahan Kehadiran</h3>
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>
                        <form class="modal-body" novalidate="novalidate" id="formSurvey-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '">
                            <!--begin::Input group-->
                            <div class="form-floating mb-7 fv-row">
                                <input class="form-control form-control-solid" id="time-picker-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" name="clock-in" placeholder="Masa" />
                                <label for="time">Masa</label>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="form-floating form-control-solid-bg rounded mb-7 fv-row">
                                <select id="selection-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="form-select form-select-transparent rounded rounded-end-0 mt-2 mb-1 ms-1" data-placeholder="' . $forecast . '" name="weather-status">' . $option . '</select>
                                <label for="selection-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="fs-5 fw-semibold form-label mb-0">Status Cuaca</label>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                <select id="selection2-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="form-select form-select-transparent rounded rounded-end-0 mt-1 mb-1 ms-1" name="number-team">
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                                <label for="selection2-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="fs-5 fw-semibold form-label mb-0">Bilangan Ahli Kumpulan</label>
                            </div>
                            <!--end::Input group-->
                            <input type="text" name="system-id"  value="' . $id['system_id'] . '" hidden>
                            <input type="text" id="bit64" name="bit64" value = "' . $bit64 . '" hidden>
                            <!--begin::Submit button-->
                            <div class="d-flex justify-content-end">
                                <button type="submit" id="submit-' . $id['subMappingID']  . $id['MappingID'] . $id['ID'] . '" class="d-none btn btn-' . $id['StatusColor'] . '">
                                    <!--begin::Indicator label-->
                                    <span class="indicator-label"><i class="fad fa-qrcode"></i> Jana</span>
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
        // }
    }
}

