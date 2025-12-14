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
                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status" data-table-filter="status">
                <option value="all">Semua</option>
                <?php
                $statuses = $List->getBy('ls_statuses','flow_role_id', $username);

                foreach( $statuses as $status ){
                    echo '<option value="'.$status->status.'">'.$status->status.'</option>';
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
                    <th class="text-center">Tarikh Tamat</th>
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
                    foreach($data AS $field) {
                        $provider = General::getProvider($field->provider_id);
                        $districts = explode(",", $field->districts);
                        $date = $field->nearest_expiry === NULL ? NULL :new DateTime($field->nearest_expiry);

                        echo '<tr>';
                        // NOTE - Reference Number
                        echo $field->reference_no === NULL ? '<td class="fw-bold">#'. $field->system_id .'</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="'. $field->system_id .'" data-bs-delay-hide="1000">'. $field->reference_no .'</td>';
                        // NOTE - Date
                        echo $field->nearest_expiry === NULL ? '<td><span class="badge badge-light-danger me-2">Telah Tamat</span></td>' : '<td>'. $date->format('d/m/Y') .'</td>';
                        // NOTE - Provider
                        echo '<td>
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $provider->name .'">
                        <div class="symbol symbol-50px">
                        <img src="'. $provider->logo .'" alt="'. $provider->name .'"  />
                        </div>
                        <span class="d-none">'. $provider->name  .'</span>
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
                        echo '<td>';
                        echo'<button type="button" class="btn btn-icon btn-light-'. $field->status_color .' m-0" data-bs-toggle="modal" data-bs-target="#modal-'. $field->route .'" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->custom_action .'">
                        <i class="fad fa-'. $field->status_icon .' fs-4"></i>
                        </button>
                        </tr>
                        </td>';
                    }
                ?>
            </tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>

