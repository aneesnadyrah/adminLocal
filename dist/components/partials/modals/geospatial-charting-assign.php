<?php
$option = '<option></option>';
foreach (Tasking::assignSelect('1') as $row) {
    if($row['ProfilePic'] == null){
        $img = "blank";
    }
    else{
        $img = $row['ProfilePic'];
    };
    $name = $row['FirstName'];
    $id = $row['username'];

    $option .= '<option value="'.$id.'" data-profile-picture="'.General::getProfile($img).'.jpg">'.$name.'</option>';
}

foreach ($data as $item) {
    $size = isset($item->aside)? "modal-lg" : "";
    $visible = isset($item->input->upload)? 'd-none' : '';
    // $range = projectDetails($AIDRow['SysID'], '1', '0')[0];
    // $range = projectDetails($AIDRow['SysID'])[0];

    // set timezone
    date_default_timezone_set('Asia/Kuala_Lumpur');

    // set public holidays
    $public_holidays = array(
        '2023-01-01', '2023-01-02', '2023-01-22', '2023-01-23', '2023-01-24', '2023-04-08', '2023-04-22', '2023-04-23', '2023-04-24', '2023-05-01', '2023-05-04', '2023-05-22', '2023-06-05', '2023-06-29', '2023-07-19', '2023-07-30', '2023-07-31', '2023-08-31', '2023-09-16', '2023-09-28', '2023-11-12', '2023-11-13', '2023-12-25'
    );

    $length = $AIDRow['LengthCode'];
    $working_days_to_add = 0;

    if ($length == 'A1' || $length == 'B') {
        $working_days_to_add = 1; // if code is "A1" or "B", add 1 day
    } else if ($length == 'A2') {
        $working_days_to_add = 2; // if code is "A2", add 2 day
    } else if ($length == 'A3' || $length == 'A') {
        $working_days_to_add = 3; // if code is "A3" or "A", add 3 days
    }

    $current_date = new DateTime(); // create a DateTime object with the current date

    // add 1 day if the current day is Saturday or Sunday
    if ($current_date->format('w') == 0) {
        $current_date->modify('+1 day');
    } else if ($current_date->format('w') == 6) {
        $current_date->modify('+2 days');
    }

    // add working days
    $working_days = 0;
    while ($working_days < $working_days_to_add) {
        $current_date->modify('+1 day');

        // add 1 day if the current day is Saturday or Sunday
        if ($current_date->format('w') == 0) {
            $current_date->modify('+1 day');
        } else if ($current_date->format('w') == 6) {
            $current_date->modify('+2 days');
        }

        // skip public holidays
        if (in_array($current_date->format('Y-m-d'), $public_holidays)) {
            $current_date->modify('+1 day');
        }

        $working_days++;
    }
    $new_date = $current_date->format('j F Y'); // format the date as desired
    $delivery_date = $current_date->format('Y-m-d H:i:s');


    echo '<!--begin::Modal-->  
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-'. $item->id .'" data-modal="'. $item->id .'">
        <div class="modal-dialog modal-dialog-centered '. $size .'">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">' . $item->title . '</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-secondary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form class="modal-body" novalidate="novalidate" data-form="form-'. $item->id .'">
                    <div class="form-floating mb-10 fv-row">
                        <select id="selection-'. $item->id .'" class="form-select form-select-lg form-select-solid" name="gis-assign" data-placeholder="Sila Pilih Pegawai GIS">'. $option .'</select>
                        <label for="selection-'. $item->id .'">Sila Pilih Pegawai GIS</label>
                    </div>
                    <!--begin::Description-->
                    <div class="mb-5 fv-row">
                        <label class="form-label text-gray-600">Tarikh Hantar PCL
                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" aria-label="Phone number must be active" data-bs-original-title="Tempoh Penyediaan PCL(Hari Bekerja)" data-kt-initialized="1"></i>
                        </label>
                        <div class="mb-8">
                            <span class="badge badge-light-warning py-3 px-7">'.$new_date.'</span>
                        </div>
                    </div>
                    <!--end::Description-->
                    <input type="text" name="delivery-date"  value="'. $delivery_date .'" hidden>
                    <!--begin::Submit button-->
                    <div class="d-flex justify-content-end">
                        <button type="submit" data-submit="submit-'. $item->id .'" class="w-100 btn btn-' . $item->color .' '. $visible .'">
                            <!--begin::Indicator label-->
                            <span class="indicator-label"><i class="fad fa-user-pen"></i> Lantik</span>
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

?>