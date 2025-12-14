<?php
$Role = new Roles();
$List = new Lists();
$username = $_SESSION['username'];
$roleId = $_SESSION['roleId'];
?>

<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header align-items-center py-5 gap-2 gap-md-5">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <i class="fad fa-search position-absolute ms-4"></i>
                <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14"
                    placeholder="Carian Permohonan..." />
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
            <!--begin::Flatpickr-->
            <div class="input-group w-250px">
                <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pilih Julat Tarikh"
                    id="table-date-range" />
                <button class="btn btn-icon btn-light" date-range-clear>
                    <i class="fad fa-xmark fs-4"></i>
                </button>
            </div>
            <!--end::Flatpickr-->
            <div class="w-100 mw-150px">
                <!--begin::Select2-->
                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status"
                    data-table-filter="status">
                    <option value="all">Semua</option>
                    <?php
                    $statuses = $List->getBy('ls_statuses', 'flow_role_id', $username);

                    foreach ($statuses as $status) {
                        echo '<option value="' . $status->status . '">' . $status->status . '</option>';
                    }

                    ?>
                </select>
                <!--end::Select2-->
            </div>
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Table-->
        <table class="table table-row-dashed align-middle fs-6 gy-5"
            id="<?= $Role->$username->sub_department . "-task" ?>">

            <!--begin::Table head-->
            <thead class="text-gray-400 fw-bold fs-7 text-uppercase">
                <!--begin::Table row-->
                <tr>
                    <th class="text-center">No Rujukan</th>
                    <th class="text-center">Penyedia Utiliti</th>
                    <th class="text-center">Jarak</th>
                    <th class="text-center">Senarai Semak</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Keutamaan</th>
                    <th class="text-center">Tindakan</th>
                </tr>
                </tr>
                <!--end::Table row-->
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-700 text-center">
                <?php
                foreach ($data as $field) {
                    $provider = General::getProvider($field->provider_id);
                    // $provider = (object) [
                    //     'name' => 'test',
                    //     'logo' => 'test'
                    // ];
                    // // $districts = explode(",", $field->district);
                    if ($field->submit_date !== null) {
                        $date = new DateTime($field->submit_date);
                        // Now you can use $date as needed
                    } else {
                        // Handle the case when $field->created_at is null
                        $date = new DateTime(); // This sets $date to the current date and time
                    }

                    echo '<tr>';
                    // NOTE - Reference Number
                    echo $field->reference_no === NULL ? '<td class="fw-bold">#' . $field->system_id . '</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="' . $field->system_id . '" data-bs-delay-hide="1000">' . $field->reference_no . '</td>';
                    // NOTE - Provider
                    echo '<td>
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $provider->name . '">
                        <div class="symbol symbol-50px">
                        <img src="' . $provider->logo . '" alt="' . $provider->name . '"  />
                        </div>
                        <span class="d-none">' . $provider->name . '</span>
                        </td>';
                    // NOTE - Distance
                    echo '<td>' . $field->application_length . ' m</td>';
                    // NOTE - Senarai Semak
                    // echo '<td>';
                    // foreach ($districts as $district) {
                    //     echo '<span class="badge badge-secondary me-2">'. $district .'</span>';
                    // } 
                    //Note: Senarai semak
                    echo '<td>';
                    // Assuming $priority is the priority value
                    $priority = $field->priority;

                    $badgeClass1 = "badge-secondary";
                    $badgeClass2 = "badge-secondary";
                    $badgeClass3 = "badge-secondary";
                    $badgeClass4 = "badge-secondary";
                    $badgeClass5 = "badge-secondary";

                    if ($priority === 1) {
                        $badgeClass1 = "badge-success";
                        $badgeClass2 = "badge-success";
                        $badgeClass3 = "badge-success";
                        $badgeClass4 = "badge-success";
                        $badgeClass5 = "badge-success";
                    } elseif ($priority === 2) {
                        $badgeClass4 = "badge-success";
                        $badgeClass3 = "badge-success";
                        $badgeClass2 = "badge-success";
                        $badgeClass1 = "badge-success";
                    } elseif ($priority === 3) {
                        $badgeClass3 = "badge-success";
                        $badgeClass2 = "badge-success";
                        $badgeClass1 = "badge-success";
                    } elseif ($priority === 4) {
                        $badgeClass2 = "badge-success";
                        $badgeClass1 = "badge-success";
                    } elseif ($priority === 5) {
                        $badgeClass1 = "badge-success";
                    }

                    // Render the badges
                    echo '<div class="text-center">';
                    echo '<div class="badge badge-white">';
                    echo '<span class="badge badge-circle ' . $badgeClass1 . ' me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="LTA Selesai" style="color: white;">5</span>';
                    echo '<span class="badge badge-circle ' . $badgeClass2 . ' me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Sebut Harga Disahkan" style="color: white;">4</span>';
                    echo '<span class="badge badge-circle ' . $badgeClass3 . ' me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Kelulusan Izin Lalu" style="color: white;">3</span>';
                    echo '<span class="badge badge-circle ' . $badgeClass4 . ' me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="MBKIL" style="color: white;">2</span>';
                    echo '<span class="badge badge-circle ' . $badgeClass5 . ' me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Bayaran (Invois)" style="color: white;">1</span>';
                   
                    echo '</div>';
                    echo '</div>';
                    echo '</td>';
                    // NOTE - Status
                    echo '<td>
                            <span class="badge badge-light-' . $field->status_color . ' me-2">' . $field->project_status . '</span>
                            </td>';
                    // NOTE - Priority
                    echo '<td>';
                    // Assuming $priority is the priority value
                    $priority = $field->priority;
                    $colorStatus = "";

                    // Set the colorStatus based on the priority value
                    if ($priority === 5) {
                        $colorStatus = "secondary";
                    } elseif ($priority === 4) {
                        $colorStatus = "success";
                    } elseif ($priority === 3) {
                        $colorStatus = "info";
                    } elseif ($priority === 2) {
                        $colorStatus = "warning";
                    } elseif ($priority === 1) {
                        $colorStatus = "danger";
                    } else {
                        $colorStatus = "white";
                    }

                    // Render the icon
                    $icon = '<i class="fa-solid fa-flag text-' . $colorStatus . '"></i>';

                    // Render the HTML
                    echo '<div class="text-center">';
                    echo $icon;
                    echo '</div>';
                    // NOTE - Action
                    // switch ($field->status_icon) {
                    //     case 'arrow-right':
                    //         // Redirect to a page
                    //         echo '<td>
                    //         <a href="'. $field->route .'" class="btn btn-icon btn-light-' . $field->status_color . ' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->flow_name .'">
                    //         <i class="fad fa-'. $field->status_icon .' fs-4"></i>
                    //         </a></td>';
                    //     break;
                    //     default:
                    //         echo '<td>
                    //         <button type="button" class="btn btn-icon btn-light-'. $field->status_color .' m-0" data-bs-toggle="modal" data-bs-target="#modal-'. $field->route .'" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->flow_name .'">
                    //         <i class="fad fa-'. $field->status_icon .' fs-4"></i>
                    //         </button>
                    //         </td>';
                    //         echo "</tr>";
                    //     break;
                    // }
                    if ($field->status_id == 39) {
                        echo '<td><div class="text-center">';
                        echo '<button type="button" id="confirm-alert" class="btn btn-icon btn-light-dark confirm-alert-btn" data-row-id="#confirm-' . $field->ID . '" data-system-id="' . $field->system_id . '">';
                        echo '<i class="fad fa-hourglass-start fs-2"></i>';
                        echo '</button>';
                        echo '</div></td>';
                    }
                }
                ?>
            </tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>