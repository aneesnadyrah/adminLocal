<?php
$Role = new Roles();
$List = new Lists();
$username = $_SESSION['username'];
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
                    <th>No Rujukan</th>
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
                foreach (Survey::tableWidgetSelection(5, 0) as $data) {
                    $provider = General::getProvider($data['providerID']);
                    echo '
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex justify-content-center flex-column">
                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['refNo'].'</a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $provider->name . '">
                                <div class="symbol symbol-50px">
                                    <img src="' . $provider->logo . '" alt="' . $provider->name . '"  />
                                </div>
                            <span class="d-none">' . $provider->name . '</span>
                        </td>
                        <td>
                            <span class="badge badge-secondary me-2">' . $data['district'] . '</span>
                        </td>
                        <td>
                            <span class="text-gray-800 fw-bold mb-1 fs-6">'.$data['length'].'</span>
                            <span class="fw-semibold text-gray-400">meter</span>
                        </td>

                        <td>
                            <div class="badge badge-light-primary">'.$data['status'].'</div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-light-' . $data['status_color'] . ' m-0" data-bs-toggle="modal" data-bs-target="#action-survey-start-work-' . $data['sysID'] . '" data-bs-toggle="tooltip" data-bs-placement="top">
                                <i class="fad fa-file-pen fs-4"></i>
                            </button>
                        </td>
                    </tr>';

                }
                ?>

            </tbody>
            <!--end::Table body-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>