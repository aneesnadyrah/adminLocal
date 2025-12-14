<?php

// Connect to the database
$conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpwd");
// Check for errors in the connection
if (!$conn) {
    die("Error in connection: " . pg_last_error());
}

if ($_SESSION['roleId'] == 2){

    // Execute a SELECT query on the database
    $actionQuery = "SELECT
    flw_appl_entries.reference_no AS \"RefNo\",
    flw_appl_entries.system_id AS \"SysID\",
    ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
           FROM sys_upi
          WHERE sys_upi.state_code::text = flw_appl_entries.state::text AND (sys_upi.district_code::text = ANY (flw_appl_entries.districts::text[]))) as \"District\",
    flw_appl_entries.application_length AS \"Length\",
    flw_appl_entries.utility_provider AS \"ProviderID\",
    ls_provider.name AS \"Provider\",
    ls_appl_status.ms_status AS \"Status\",
    ls_appl_status.color AS \"StatusColor\",
    flw_appl_entries.application_date AS \"SubmitDate\",
    status_appl.id AS \"ID\",
    status_appl.project_status AS \"StatusID\"
    FROM status_appl
    LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = status_appl.system_id
    LEFT JOIN ls_appl_status ON ls_appl_status.id = status_appl.project_status
    LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
    WHERE status_appl.project_status IN (2,28,32,42,51)
    GROUP BY flw_appl_entries.reference_no,
    flw_appl_entries.system_id,
    flw_appl_entries.application_length,
    flw_appl_entries, flw_appl_entries.utility_provider,
    ls_appl_status.ms_status,
    ls_provider.name,
    ls_appl_status.color,
    status_appl.id,
    status_appl.project_status,
    flw_appl_entries.application_date
    ";

    $actionResult = pg_query($conn,  $actionQuery);

    // Check for errors in the query
    if (!$actionResult) {
        die("Error in query: " . pg_last_error());
    }

    $ids = pg_fetch_assoc($actionResult);

    if ($ids !== false) {
        $id = $ids['StatusID'];
        $AIDRows = pg_fetch_all($actionResult);

        if(pg_num_rows($actionResult) > 0  && $id == 2)  {

                foreach ($AIDRows as $AIDRow) {
                    echo '<!--begin::Modal-->
                    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-'. $AIDRow['StatusID']. $AIDRow['ID'] .'">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Muatnaik Arahan Kerja</h3>

                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-light-'. $AIDRow['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                        <i class="fad fa-xmark fs-2"></i>
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <form class="modal-body" novalidate="novalidate" id="wo_action_form" action="#">
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-2">
                                        <!--begin::Dropzone-->
                                        <div class="dropzone border-'. $AIDRow['StatusColor'] . ' bg-light-'. $AIDRow['StatusColor'] . '" id="add-attachment">
                                            <!--begin::Message-->
                                            <div class= "dz-message needsclick">
                                                <!--begin::Icon-->
                                                <i class="fa-duotone fa-file-arrow-up text-'. $AIDRow['StatusColor'] . ' fs-3x"></i>
                                                <!--end::Icon-->
                                                <!--begin::Info-->
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                        Leret ke sini atau klik di sini untuk muatnaik dokumen</h3>
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Arahan Kerja</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <input type="text" name="system-id" value="'. $AIDRow['SysID'] .'" hidden>
                                            <input type="text" name="folder" value="WO" hidden>
                                        </div>
                                        <!--end::Dropzone-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen AK telah disatukan dalam bentuk format pdf</em></div>
                                    <div class="mb-5 fv-row">
                                        <label class="required form-label">Jumlah Amaun Arahan Kerja (RM)</label>
                                        <input type="text" name="total-wo" class="form-control form-control-solid" placeholder="Sila Isi Amaun Arahan Kerja"/>
                                    </div>
                                    <!--end::Description-->
                                    <!--begin::Submit button-->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" id="task_action_submit" class="btn btn-'. $AIDRow['StatusColor'] . '">
                                            <!--begin::Indicator label-->
                                            <span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>
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

        }
    }

} elseif ($_SESSION['roleId'] == 32) {

    // Execute a SELECT query on the database
    $actionQuery = "SELECT
    flw_appl_entries.reference_no AS \"RefNo\",
    flw_appl_entries.system_id AS \"SysID\",
    ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
           FROM sys_upi
          WHERE sys_upi.state_code::text = flw_appl_entries.state::text AND (sys_upi.district_code::text = ANY (flw_appl_entries.districts::text[]))) as \"District\",
    flw_appl_entries.application_length AS \"Length\",
    flw_appl_entries.utility_provider AS \"ProviderID\",
    ls_provider.name AS \"Provider\",
    ls_appl_status.ms_status AS \"Status\",
    ls_appl_status.color AS \"StatusColor\",
    flw_appl_entries.application_date AS \"SubmitDate\",
    status_appl.id AS \"ID\",
    status_appl.project_status AS \"StatusID\"
    FROM status_appl
    LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = status_appl.system_id
    LEFT JOIN ls_appl_status ON ls_appl_status.id = status_appl.project_status
    LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
    WHERE status_appl.project_status IN (4, 5, 21,73,116)
    GROUP BY flw_appl_entries.reference_no,
    flw_appl_entries.system_id,
    flw_appl_entries.application_length,
    flw_appl_entries, flw_appl_entries.utility_provider,
    ls_appl_status.ms_status,
    ls_provider.name,
    ls_appl_status.color,
    status_appl.id,
    status_appl.project_status,
    flw_appl_entries.application_date

    ";

    $actionResult = pg_query($conn,  $actionQuery);

    // Check for errors in the query
    if (!$actionResult) {
        die("Error in query: " . pg_last_error());
    }

    $ids = pg_fetch_assoc($actionResult);
    if ($ids !== false) {
        $id = $ids['StatusID'];
        // fetch the rows from the query result as an associative array
        $AIDRows = pg_fetch_all($actionResult);

        if(pg_num_rows($actionResult) > 0 && $id == 4)  {

            //bella add
            $query = "SELECT
                        sys_users.username,
                        sys_hr_employee.first_name,
                        sys_hr_employee.last_name,
                        sys_users.profile_pic
                        FROM sys_hr_employee
                        LEFT JOIN sys_users ON sys_users.employee_id = sys_hr_employee.id
                        WHERE sys_users.role_id = 33
                        AND sys_users.activation = 'true'
                        ORDER BY sys_hr_employee.first_name ASC
                    ";
            $result = pg_query($conn, $query);
            if (!$result) {
                echo "An error occurred.\n";
                exit;
            }
            $select_options = '<option></option>';
            while ($row = pg_fetch_row($result)){
                if($row[3] == null){
                    $profile_picture = "assets/media/avatars/300-2.jpg";
                }
                else{
                    $profile_picture = $row[3];
                };
                $select_options .= '<option value="' . $row[0] . '" data-icon="'. $profile_picture .'">' . $row[1].' '.$row[2] . '</option>';
            };

            foreach ($AIDRows as $AIDRow) {
                echo '<!--begin::Modal-->
                <div class="modal fade" tabindex="-1" id="action-'. $AIDRow['StatusID']. $AIDRow['ID'] .'" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" id="modal_assign_gis">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Lantik Pelukis Pelan Surihan</h3>

                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-light-'. $AIDRow['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close" data-gis-modal-action="close">
                                    <i class="fad fa-xmark fs-2"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <form class="modal-body" novalidate="novalidate" id="gis_action" action="#" data-kt-redirect="/dashboard/index.php">
                                <div class="mb-10 fv-row">
                                    <select id="select-icon" class="form-select form-select-lg form-select-solid" name="gis-assign" data-placeholder="Sila Pilih Pegawai GIS">' . $select_options . '</select>
                                </div>
                                <input type="text" name="system-id" id="system-id" value="'. $AIDRow['SysID'] .'" hidden>
                                <!--begin::Submit button-->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="submit-button" class="btn btn-'. $AIDRow['StatusColor'] . '">
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

        } else {

        }
    }
} elseif ($_SESSION['roleId'] == 33) {

    // Execute a SELECT query on the database
    $actionQuery = "SELECT
    flw_appl_entries.reference_no AS \"RefNo\",
    flw_appl_entries.system_id AS \"SysID\",
    ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
           FROM sys_upi
          WHERE sys_upi.state_code::text = flw_appl_entries.state::text AND (sys_upi.district_code::text = ANY (flw_appl_entries.districts::text[]))) as \"District\",
    flw_appl_entries.application_length AS \"Length\",
    flw_appl_entries.utility_provider AS \"ProviderID\",
    ls_provider.name AS \"Provider\",
    ls_appl_status.ms_status AS \"Status\",
    ls_appl_status.color AS \"StatusColor\",
    flw_appl_entries.application_date AS \"SubmitDate\",
    status_appl.id AS \"ID\",
    status_appl.project_status AS \"StatusID\"
    FROM status_appl
    LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = status_appl.system_id
    LEFT JOIN ls_appl_status ON ls_appl_status.id = status_appl.project_status
    LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
    WHERE status_appl.project_status IN (4, 5, 21,73,116)
    GROUP BY flw_appl_entries.reference_no,
    flw_appl_entries.system_id,
    flw_appl_entries.application_length,
    flw_appl_entries, flw_appl_entries.utility_provider,
    ls_appl_status.ms_status,
    ls_provider.name,
    ls_appl_status.color,
    status_appl.id,
    status_appl.project_status,
    flw_appl_entries.application_date

    ";

    $actionResult = pg_query($conn,  $actionQuery);

    // Check for errors in the query
    if (!$actionResult) {
        die("Error in query: " . pg_last_error());
    }

    $ids = pg_fetch_assoc($actionResult);
    if ($ids !== false) {
        $id = $ids['StatusID'];
        // fetch the rows from the query result as an associative array
        $AIDRows = pg_fetch_all($actionResult);

        if(pg_num_rows($actionResult) > 0 && $id == 5) {

            foreach ($AIDRows as $AIDRow) {
                echo '<!--begin::Modal-->
                <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-'. $AIDRow['StatusID'] . $AIDRow['ID'] .'">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Serahan Pelan Cadangan Laluan</h3>
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-light-'. $AIDRow['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fad fa-xmark fs-2"></i>
                                </div>
                                <!--end::Close-->
                            </div>
                            <form class="modal-body" novalidate="novalidate" id="gis_action_form" action="#">
                                <!--begin::Input group-->
                                <div class="fv-row mb-5">
                                    <!--begin::Dropzone-->
                                    <div class="dropzone border-'. $AIDRow['StatusColor'] . ' bg-light-'. $AIDRow['StatusColor'] . '" id="add-attachment">
                                        <!--begin::Message-->
                                        <div class= "dz-message needsclick">
                                            <!--begin::Icon-->
                                            <i class="fa-duotone fa-file-arrow-up text-'. $AIDRow['StatusColor'] . ' fs-3x"></i>
                                            <!--end::Icon-->
                                            <!--begin::Info-->
                                            <div class="ms-4">
                                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                    Leret ke sini atau klik di sini untuk muatnaik pelan</h3>
                                                <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Pelan Cadangan Laluan</span>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <input type="text" name="system-id" value="'. $AIDRow['SysID'] .'" hidden>
                                        <input type="text" name="folder" value="PCL" hidden>
                                    </div>
                                    <!--end::Dropzone-->
                                </div>
                                <!--end::Input group-->
                                <!--begin::Submit button-->
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="task_action_submit" class="btn btn-'. $AIDRow['StatusColor'] . '">
                                        <!--begin::Indicator label-->
                                        <span class="indicator-label"><i class="fad fa-paper-plane"></i> Seterusnya</span>
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

        }
    }
}

?>

