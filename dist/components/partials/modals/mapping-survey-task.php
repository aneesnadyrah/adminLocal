
<?php
include "config/tenant.php";
$forecast = Survey::getForecast();
$optionWeather = '';

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
    // $selected = ($weather == $forecast) ? 'selected' : '';
    // $placeholder = '<i class="fas fa-' . $weatherIcons[$forecast] . '"></i>' . $forecast;
    // $placeholder = $weatherIcons[$forecast] . $forecast;
    $optionWeather .= '<option value="' . $weather . '" data-icon-class="fas fa-' . $icon . '"><span class="fas fa-' . $icon . '"></span> ' . $weather . '</option>';
}

$optionGroup = '<option></option>';
foreach (Tasking::assignSelect(1) as $row) {
    if ($row['ProfilePic'] == null) {
        $img = "blank";
    } else {
        $img = $row['ProfilePic'];
    };
    $name = $row['FirstName'];
    $id = $row['username'];

    $optionGroup .= '<option value="' . $id . '" data-profile-picture="' . General::getProfile($img) . '.jpg">' . $name . '</option>';
}

foreach ($data as $item) {
    $details = Survey::currentProgressUdm($id->system_id);
    $bit64 = Survey::generateUniqueId();
    $visible = isset($item->input->upload) ? 'd-none' : '';
    // var_dump($bit64);
    // mapping status = 43 for qr code or attandance validation (clock in)
    //mapping status = 44 for attandance validation (clock out)
    //mapping status = 46 for serahan data kerja ukur (PIU)
    if ($id->status_id == 43) {
        if (Survey::getAttand($id->system_id)) {
            echo '
            <!--begin::Modal-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '">
                <!--begin::Modal dialog-->
                <div class="modal-dialog modal-dialog-centered">
                    <!--begin::Modal content-->
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <!--begin::Modal title-->
                            <h2>Daftar Masuk</h2>
                            <!--end::Modal title-->
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-' . $item->color . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>
                        <!--end::Modal header-->
                        
                        <form class="modal-body pt-0" data-form="form-' . $item->id . '">
                            <div class="d-flex justify-content-center mt-5">
                                <!-- QR code container -->
                                    <iframe src="https://' . $_SERVER['HTTP_HOST'] . '/attendance/survey/' . $id->system_id . '" style="width: 100%; height: 370px; text-align: center; vertical-align: middle;"></iframe>
                            </div>
                            <input type="text" name="system-id"  value="' . $id->system_id . '" hidden>
                            <input type="text" id="progress-' . $item->id . '" name="progress-status" value="2" hidden/>
                            <input type="text" class="form-control form-control-solid" id="action" name="action" hidden/>
                                <!--begin::Submit button-->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" data-submit="submit-' . $item->id . '" class="d-none btn btn-' . $item->color . '">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label">Seterusnya <i class="fad fa-circle-arrow-right"></i></span>
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
                            <!-- Add this div to hold the auto-generated button -->
                            <div id="autoButtonContainer"></div>
                        </div>
                        <!--end::Modal content-->
                    </div>
                    <!--end::Modal dialog-->
                </div>
                <!--end::Modal-->';
        } else {
            echo '<!--begin::Modal-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Pengesahan Kehadiran</h3>
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-' . $item->color . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>
                        <form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '">
                            <!--begin::Input group-->
                            <div class="form-floating mb-7 fv-row">
                                <input class="form-control form-control-solid" id="time-picker-' . $item->id . '" name="clock-in" placeholder="Masa" />
                                <label for="time">Masa</label>
                                <input type="text" id="progress-' . $item->id . '" name="progress-status" value="1" hidden/>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="form-floating form-control-solid-bg rounded mb-7 fv-row">
                                <select id="selection-' . $item->id . '" class="form-select form-select-transparent rounded rounded-end-0 mt-2 mb-1 ms-1" data-placeholder="' . $forecast . '" name="weather-status">' . $optionWeather . '</select>
                                <label for="selection-' . $item->id . '" class="fs-5 fw-semibold form-label mb-0">Status Cuaca</label>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="form-floating form-control-solid-bg rounded mb-10 fv-row">
                                <select id="selection2-' . $item->id . '" class="form-select form-select-transparent rounded rounded-end-0 mt-1 mb-1 ms-1" name="number-team">
                                    <option selected>3</option>
                                    <option>4</option>
                                </select>
                                <label for="selection2-' . $item->id . '" class="fs-5 fw-semibold form-label mb-0">Bilangan Ahli Kumpulan</label>
                            </div>
                            <!--end::Input group-->
                            <input type="text" id="bit64" name="bit64" value = "' . $bit64 . '" hidden>
                            <!--begin::Submit button-->
                            <div class="d-flex justify-content-end">
                                <button type="submit" data-submit="submit-' . $item->id . '" class="btn btn-' . $item->color . '">
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
    } else if ($id->status_id == 44) {
        echo '<!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Pengesahan Tamat Kerja</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-' . $item->color . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '">
                        <!--begin::Input group-->
                        <div class="form-floating mb-7 fv-row">
                            <input class="form-control form-control-solid" id="time-picker3-' . $item->id . '" name="clock-out" placeholder="Masa" />
                            <label for="time">Masa</label>
                        </div>
                        <!--end::Input group-->
                        <input type="text" id="progress-' . $item->id . '" name="progress-status" value="3" hidden/>
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" data-submit="submit-' . $item->id . '" class="d-none btn btn-' . $item->color . '">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fas fa-check"></i> Sahkan</span>
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
    } else if ($id->status_id == 46) {  
        echo '<!--begin::Modal-->  
        <div class="modal fade" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Serahan Data Kerja Ukur</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-' . $item->color . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '">

                        <!--begin::Dropzone-->
                        <div class="dropzone border-' . $item->color . ' bg-light-' . $item->color . ' mb-7">
                            <!--begin::Message-->
                            <div class= "dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-file-arrow-up text-' . $item->color . ' fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .dwg atau .pdf sahaja</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <input type="text" id="sid-' . $item->id . '" name="system-id" value="' . $item->system_id . '" hidden/>
                            <input type="text" id="progress-' . $item->id . '" name="progress-status" value="4" hidden/>
                            <input type="text" id="f-' . $item->id . '" name="folder" value="RDPIU" hidden>
                            <input type="text" class="form-control form-control-solid" id="kerja-lapangan" name="kerja-lapangan" hidden/>
                        </div>
                        <!--end::Dropzone-->
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="form-floating mb-7 fv-row">
                            <input type="text" class="form-control form-control-solid" id="upload-remark" name="upload-remark" placeholder="Catatan"/>
                            <label for="upload-remark">Catatan</label>
                            <input type="text" id="sid-' . $item->id . '" name="system-id" value="' . $item->system_id . '" hidden/>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end my-5">
                            <button type="submit" data-submit="submit-' . $item->id . '" class="w-100 btn btn-' . $item->color . ' ' . $visible . '">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fad fa-arrow-up-from-bracket"></i> Muatnaik</span>
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
