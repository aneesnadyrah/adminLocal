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
        <table class="table table-row-dashed align-middle fs-6 gy-5" id="<?= $Role->$username->sub_department."-task" ?>">

            <!--begin::Table head-->
            <thead class="text-gray-400 fw-bold fs-7 text-uppercase">
                <!--begin::Table row-->
                <tr>
                    <th class="text-center">No Permohonan</th>
                    <th class="text-center">Tarikh Tugasan</th>
                    <th class="text-center">Penyedia Utiliti</th>
                    <th class="text-center">Daerah</th>
                    <th class="text-center">Jarak</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Tindakan</th>
                </tr>
                <!--end::Table row-->
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-700 text-center">
                <?php
                foreach ($data as $field) {
                    $provider = General::getProvider($field->provider_id);
                    $districts = explode(",", $field->districts);
                    $date = new DateTime($field->created_at);

                    echo '<tr>';
                    // NOTE - Reference Number
                    echo $field->reference_no === NULL ? '<td class="fw-bold">#' . $field->system_id . '</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="' . $field->system_id . '" data-bs-delay-hide="1000">' . $field->reference_no . '</td>';
                    // NOTE - Date
                    echo '<td>' . $date->format('d/m/Y') . '</td>';
                    // NOTE - Provider
                    echo '<td>
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $provider->name . '">
                        <div class="symbol symbol-50px">
                        <img src="' . $provider->logo . '" alt="' . $provider->name . '"  />
                        </div>
                        <span class="d-none">' . $provider->name . '</span>
                        </td>';
                        // NOTE - District
                        echo '<td>';
                        foreach ($districts as $district) {
                            echo '<span class="badge badge-secondary me-2">'. $district .'</span>';
                        }
                        echo '</td>';
                        // NOTE - Distance
                        echo '<td>'. $field->application_length .' m</td>';
                        // NOTE - Status
                        echo '<td>
                        <span class="badge badge-light-'. $field->status_color .' me-2">'. $field->flow_action .'</span>
                        </td>';
                    // NOTE - Action
                    //NOTE -  SystemId-StatusId
                    switch ($field->status_icon) {
                        case 'arrow-right':
                            // Redirect to a page
                            echo '<td>
                                <a href="' . $field->route . '" class="btn btn-icon btn-light-' . $field->status_color . ' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $field->flow_action . '">
                                <i class="fad fa-' . $field->status_icon . ' fs-4"></i>
                                </a></td>';
                            break;
                        default:
                            echo '<td>';

                            if ($field->status_id == 55 || $field->status_id == 109 || $field->status_id == 155) {
                                //Download QR Code Pelan
                                echo '
                                    <button type="button" 
                                            class="btn btn-icon btn-light-dark m-0 me-2" 
                                            data-system-id="' . $field->system_id . '" 
                                            onclick="generateQR(\'' . $field->system_id . '\')"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="Muatnaik QR Code">
                                        <div id="qrcodePlan-' . $field->system_id . '" hidden></div>
                                        <a id="downloadLink-' . $field->system_id . '" download="qr_code.png" style="display: none;"></a>
                                        <i class="fad fa-qrcode fs-4"></i>
                                    </button>
                                    ';
                            } else if($field->status_id == 35){

                                if($field->percent < 100){
                                    echo '
                                <button type="button" class="btn btn-icon btn-light-dark m-0 me-2" data-invois="invois-' . $field->system_id . '" data-system-id="'.$field->system_id.'"  data-bs-toggle="tooltip" data-bs-placement="top" title="Muat naik Invois">
                                <i class="fad fa-file-circle-plus fs-4"></i>
                                </button>
                                    ';
                                }

                            } else if($field->status_id == 7 && ($roleId == 10 || $roleId == 11)) {
                                echo '
                                <a href="#work-order-'.$field->system_id.'" data-fslightbox="lightbox-basic" data-class="fslightbox-source" class="btn btn-icon btn-light-dark m-0 me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Work Order">
                                    <span class="fad fa-eye fs-4"></span>
                                </a>';

                            } else if ($field->status_id == 57) {
                                //Download Pelan PIU .pdf / .zip
                                echo '
                                    <!--begin::Export dropdown-->
                                    <button type="button" class="btn btn-icon btn-light-dark m-0 me-2" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <i class="fad fa-download fs-4"></i>
                                    </button>
                                    <!--begin::Menu-->
                                    <div id="download-plan-piu" data-download-piu="piu-' . $field->system_id . '" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true" data-system-id="' . $field->system_id . '">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-file="pdf">
                                            <i class="fad fa-file-pdf fs-4 me-3"></i>
                                            Salinan Pelan (.pdf)
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-file="zip">
                                            <i class="fad fa-file-zip fs-4 me-3"></i>
                                            Lukisan Pelan (.zip)
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    ';
                            } else if ($field->status_id == 59) {
                                //Download Pelan PIU .pdf / .zip
                                echo '
                                    <!--begin::Export dropdown-->
                                    <button type="button" class="btn btn-icon btn-light-dark m-0 me-2" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <i class="fad fa-download fs-4"></i>
                                    </button>
                                    <!--begin::Menu-->
                                    <div id="download-plan-ppt" data-download-ppt="ppt-' . $field->system_id . '" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true" data-system-id="' . $field->system_id . '">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-file="pdf">
                                            <i class="fad fa-file-pdf fs-4 me-3"></i>
                                            Salinan Pelan (.pdf)
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-file="zip">
                                            <i class="fad fa-file-zip fs-4 me-3"></i>
                                            Lukisan Pelan (.zip)
                                            </a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    ';
                            }

                            echo '<button type="button" class="btn btn-icon btn-light-' . $field->status_color . ' m-0" data-bs-toggle="modal" data-bs-target="#modal-' . $field->route . '" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $field->flow_action . '">
                                <i class="fad fa-' . $field->status_icon . ' fs-4"></i>
                                </button>
                                </tr>
                                </td>';
                        break;
                    }

                        if($field->status_id == 7 && ($roleId == 10 || $roleId == 11)) {
                            $attach = General::getAttachment($field->system_id, 'AKP');
                        echo '
                            <div style="display: none;">
                                <div id="work-order-'.$field->system_id.'">
                                    <iframe src="/attachments/view?url='.$attach->url.'&mime='.$attach->mime_type.'" width="855px" height="650px"></iframe>
                                </div>
                            </div>'; 
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