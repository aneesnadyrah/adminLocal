<?php
$List = new Lists();
$tracker = new Tracker();
$system = new System();
?>

<!--begin::Table Tracker-->
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
            <!--begin::Export dropdown-->
            <div>
                <button type="button" class="btn btn-secondary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Jana Rumusan
                </button>
                <!--begin::Menu-->
                <div id="jana_rumusan_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-export="copy">
                            Salin ke Papan Keratan
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-export="excel">
                            Jana ke Excel
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-export="csv">
                            Jana ke CSV
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-export="pdf">
                            Jana ke PDF
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Export dropdown-->

            <!--begin::Hide default export buttons-->
            <div id="jana_rumusan" class="d-none"></div>
            <!--end::Hide default export buttons-->

            <div class="w-200px">
                <!--begin::Select2-->
                <?php 
                $state_code = $system->App->state;
                $option = '<option></option>';
                $authorities = $List->getBy('ls_authorities','state_code',$state_code);
                
                foreach ($authorities as $authority) {
                    $option .= '<option value="'.$authority->sort_name.'" data-authority="assets/media/authorities/'.$authority->logo.'.png">'.$authority->sort_name.'</option>';
                }
                ?>

                <select id="kt_data_authority" class="form-select form-select-solid" data-control="select2" data-placeholder="Pihak Berkuasa" data-table-filter="authority" data-allow-clear="true">
                    <?php echo $option ?>
                </select>
                <!--end::Select2-->
            </div>
            
            <div class="w-100 mw-150px">
                <!--begin::Select2-->
                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status" data-table-filter="status">
                <option value="all">Semua</option>
                <?php
                $statuses = $List->get('ls_statuses');

                foreach( $statuses as $status ){
                    // echo '<option value="'.$status->status.'">'.$status->status.'</option>';
                    echo '<option value="'.$status->flow_name.'">'.$status->flow_name.'</option>';
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
        <table class="table table-striped table-rounded table-bordered text-nowrap display compact gs-3" id="tracker-v2" style="width:100%">
            <!--begin::Table head-->
            <thead class="bg-gray-300 border-white">
                <tr class="text-center fw-bold text-uppercase">
                    <th>No Permohonan</th>
                    <th>Jarak Permohonan</th>
                    <th>Jarak GIS</th>
                    <th>Jarak Ukur</th>
                    <th>Daerah</th>
                    <th>Pihak Berkuasa</th>
                    <th>Penyedia Utiliti</th>
                    <th>Status</th>
                    <th>Bahagian</th>
                    <th>Tarikh Kelulusan</th>
                    <th>No Permit</th>
                    <th>Tempoh DLP</th>
                    <th>Nilai Wang Cagaran</th>
                    <th>Lampiran</th>
                </tr>
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-800 text-center">
                <?php
                    foreach($data AS $field) {
                        $provider = General::getProvider($field->provider_id);

                        if($field->flow_department == 'finance') {
                            $department = "Kewangan";
                        } else if($field->flow_department == 'geospatial') {
                            $department = "Geospatial";
                        } else if($field->flow_department == 'hr') {
                            $department = "Sumber Manusia";
                        } else if($field->flow_department == 'mapping') {
                            $department = "Ukur dan Pelan";
                        } else if($field->flow_department == 'management') {
                            $department = "Pengurusan";
                        } else if($field->flow_department == 'operation') {
                            $department = "Operasi";
                        }

                        echo '<tr>';
                            // NOTE - Reference Number
                            echo $field->reference_no === NULL ? '<td class="fw-bold">#'. $field->system_id .'</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="'. $field->system_id .'" data-bs-delay-hide="1000">'. $field->reference_no .'</td>';
                            // NOTE - Distance Application
                            echo empty($field->application_length) ? '<td>-</td>' : '<td>'. $field->application_length .' m</td>';
                            // NOTE - Distance GIS
                            echo empty($field->gis_length) ? '<td>-</td>' : '<td>'. $field->gis_length .' m</td>';
                            // NOTE - Distance Survey
                            echo empty($field->current_progress) ? '<td>-</td>' : '<td>'. $field->current_progress .' m</td>';
                            // NOTE - District
                            echo '<td>';
                            if(!empty($field->districts)) {
                                $districts = explode(",", $field->districts);
                                foreach ($districts as $district) {
                                    echo '<span class="badge badge-secondary me-2">'. $district .'</span>';
                                } 
                            } else {
                                echo '-';
                            }
                            echo '</td>';
                            // NOTE - Authority
                            echo empty($field->sort_name) ? '<td>-</td>' : '<td>
                                    <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->sort_name .'">
                                    <div class="symbol symbol-50px">
                                    <img src="assets/media/authorities/'. $field->logo .'.png" alt="'. $field->sort_name .'"  />
                                    </div>
                                    <span class="d-none">'. $field->sort_name  .'</span></div>
                                    </td>' ;
                            // NOTE - Provider
                            echo empty($provider->name) ? '<td>-</td>' : '<td>
                                    <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $provider->name .'">
                                    <div class="symbol symbol-50px">
                                    <img src="'. $provider->logo .'" alt="'. $provider->name .'"  />
                                    </div>
                                    <span class="d-none">'. $provider->name  .'</span></div>
                                    </td>' ;
                            // NOTE - Status
                            echo '<td><span class="badge badge-light-'. $field->status_color .' me-2">'. $field->flow_action .'</span></td>';
                            // echo '<td><span class="badge badge-light-'. $field->status_color .' me-2">'. $field->status .'</span></td>';
                            // NOTE - Flow Department
                            echo '<td><span class="badge badge-light-info me-2">'. $department .'</span></td>';
                            // NOTE - Tarikh Terima KIL
                            echo empty($field->dt_appv_ltr) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_appv_ltr))->format('d/m/Y') .'</td>' ;
                            echo '<td>-</td>';
                            echo '<td>-</td>';
                            echo '<td>-</td>';
                            // Redirect to a page
                            echo empty($field->system_id) ? '<td>-</td>' : '<td>
                            <a href="tracker/attachments/list/'.$field->system_id.'" class="btn btn-icon btn-light-' . $field->status_color . ' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Lampiran" target="_blank"><i class="fad fa-file-lines fs-4"></i></a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Table Tracker-->

