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
            <a href="/calendar/<?php echo $data[0]->system_id; ?>/1" type="button" class="btn btn-flex flex-center btn-secondary w-40px w-md-auto h-40px px-0 px-md-6">
                <i class="fad fa-circle-plus fs-4"></i>
                <span class="d-none d-md-inline">Lawatan Tapak</span>
            </a>
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
                    <th class="text-center">No Rujukan</th>
                    <th class="text-center">Pihak Berkuasa Melulus</th>
                    <th class="text-center">Tarikh Lawatan Tapak</th>
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
                        // $provider = json_decode(file_get_contents("https://app.kudr.my/gateway/internal/provider/$field->provider_id"));
                        // $districts = explode(",", $field->districts);
                        $date = new DateTime($field->calendar_sv_date);

                        echo '<tr>';
                        // NOTE - Reference Number
                        echo $field->reference_no === NULL ? '<td class="fw-bold">#'. $field->system_id .'</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="'. $field->system_id .'" data-bs-delay-hide="1000">'. $field->reference_no .'</td>';
                        // NOTE - Provider
                        echo '<td>
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->authority_name .'">
                        <div class="symbol symbol-50px">
                        <img src="assets/media/authorities/'. $field->authority_logo .'.png" alt="'. $field->authority_name .'"  />
                        </div>
                        <span class="d-none">'. $field->authority_name .'</span>
                        </td>';
                        // NOTE - Date
                        echo '<td>'. $date->format('d/m/Y') .'</td>';
                        // // NOTE - District
                        // echo '<td>';
                        // foreach ($districts as $district) {
                        //     echo '<span class="badge badge-secondary me-2">'. $district .'</span>';
                        // }
                        // echo '</td>';
                        // NOTE - Distance
                        echo '<td>'. $field->Length .' m</td>';
                        //NOTE -  SystemId-StatusId
                        switch ($field->status_id) {
                            case 11:
                                // Pengesahan Tarikh Lawatan Tapak
                                // TODO - open confirmation modal
                                echo '<td>
                                <span class="badge badge-light-'. $field->status_color .' me-2">'. $field->flow_action .'</span>
                                </td>
                                <td>
                                <button type="button" class="btn btn-icon btn-light-'. $field->status_color .' m-0" data-bs-toggle="modal" data-bs-target="#modal-'. $field->route .'" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->flow_action .'">
                                <i class="fad fa-calendar-check fs-4"></i>
                                </button>
                                </td>';
                                echo "</tr>";
                            break;
                            case 12:
                                // Pindaan Tarikh Lawatan Tapak
                                // TODO - open confirmation modal
                                echo '<td>
                                <span class="badge badge-light-'. $field->status_color .' me-2">'. $field->flow_action .'</span>
                                </td>
                                <td>
                                <a href="'. $field->route .'" class="btn btn-icon btn-light-'. $field->status_color .' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->flow_action .'">
                                <i class="fad fa-calendar-check fs-4"></i>
                                </a></td>';
                            break;
                            case 13:
                                // Lawatan Tapak Awalan
                                // TODO - link ke page pin n snap
                                echo '<td>
                                <span class="badge badge-light-'. $field->status_color .' me-2">'. $field->flow_action .'</span>
                                </td>
                                <td>
                                <a href="'. $field->route .'" class="btn btn-icon btn-light-'. $field->status_color .' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->flow_action .'">
                                <i class="fad fa-map-location-dot fs-4"></i>
                                </a></td>';
                            break;
                            case 14:
                                // Penyediaan Laporan LTA
                                // TODO - link ke page pin n snap
                                echo '<td>
                                <span class="badge badge-light-'. $field->status_color .' me-2">'. $field->flow_action .'</span>
                                </td>
                                <td>
                                <a href="'. $field->route .'" class="btn btn-icon btn-light-'. $field->status_color .' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $field->flow_action .'">
                                <i class="fad fa-calendar-check fs-4"></i>
                                </a></td>';
                            break;
                            case 15:
                                // Pengesahan Laporan LTA
                                echo '<td>
                                <span class="badge badge-light-success me-2">Pengesahan Laporan</span>
                                </td>
                                <td>
                                <button type="button" class="btn m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Pengesahan Laporan">
                                <i class="fad fa-circle-check text-success fs-2"></i>
                                </button>
                                </td>';
                                echo "</tr>";
                            break;
                            default:
                                echo '<td>
                                <span class="badge badge-light-success me-2">Lawatan Tapak Awalan Selesai</span>
                                </td>
                                <td>
                                <button type="button" class="btn m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Lawatan Tapak Awalan Selesai">
                                <i class="fad fa-circle-check text-success fs-2"></i>
                                </button>
                                </td>';
                                echo "</tr>";
                            break;
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

