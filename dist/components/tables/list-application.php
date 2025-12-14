<?php
$Role = new Roles();
$List = new Lists();
$username = $_SESSION['username'];
$roleId = $_SESSION['roleId'];

require_once 'config/functions/general.php';
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
		    <th></th>
                    <th class="text-center">Tarikh Permohonan</th>
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
                    $provider = General::getAllProvider();
                    foreach ($data as $field) {
                        // $provider = General::getProvider($field->utility_provider);
                        $date = isset($field->created_at)? new DateTime($field->created_at):'';
                        $date = isset($field->created_at)? $date->format('d/m/Y'):'';
                        foreach ($provider as $prov) {
                            if($field->utility_provider == $prov->id) {
                                $field->utility_provider = $prov->name;
                                $logo = $prov->logo;
                            }
                        }

                        echo $field->reference_no === NULL ? '<td class="fw-bold">#' . $field->system_id . '</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="' . $field->system_id . '" data-bs-delay-hide="1000">' . $field->reference_no . '</td>';
			// NOTE - Hidden Title
                        echo '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="' . $field->project_title . '" data-bs-delay-hide="10"><i class="fa-light fa-circle-info"></i><span class="d-none">' . $field->project_title . '</span></td>';
                        echo '<td>' . $date . '</td>';
                        echo '<td>
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $field->utility_provider . '">
                        <div class="symbol symbol-50px">
                        <img src="' . $logo . '" alt="' . $field->utility_provider . '"  />
                        </div>
                        <span class="d-none">' . $field->utility_provider . '</span>
                        </td>';
                        echo '<td><span class="badge badge-secondary me-2">'. $field->districts .'</span></td>';
                        // NOTE - Distance
                        echo '<td>'. $field->application_length .' m</td>';
                        // NOTE - Status
                        echo '<td>
                        <span class="badge badge-light-'. $field->color .' me-2">'. $field->flow_name .'</span>
                        </td>';
                        echo '<td class="text-center"><a href="projects/details/' . $field->system_id .'" class="btn btn-icon btn-light-primary m-0" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Semak Permohonan" data-bs-original-title="Semak Permohonan" data-kt-initialized="1">
                                <i class="fad fa-arrow-right fs-4"></i>
                                </a></td>';
                        echo '</tr>';
                    }
                
                ?>            </tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>