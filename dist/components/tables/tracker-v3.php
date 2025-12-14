<?php
$List = new Lists();
$tracker = new Tracker();
$system = new System;
$ec_url = $system->App->ec_url;

?>

<!--begin::Card Total-->
<div class="row gx-10 mb-4">
    <?php // $tracker->widget('tracker-tasks'); ?>
</div>
<!--end::Card Total-->

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
                <button type="button" class="btn btn-secondary" data-kt-menu-trigger="click"
                    data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Jana Rumusan
                </button>
                <!--begin::Menu-->
                <div id="kt_datatable_example_export_menu"
                    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                    data-kt-menu="true">
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
            <div id="kt_datatable_example_buttons" class="d-none"></div>
            <!--end::Hide default export buttons-->

            <div class="w-100 mw-150px">
                <!--begin::Select2-->
                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status"
                    data-table-filter="status">
                    <option value="all">Semua</option>
                    <?php
                    $statuses = $List->get('ls_statuses');

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
        <div class="table-responsive">
            <table class="table table-striped table-rounded table-bordered text-nowrap display compact gs-3"
                id="tracker-v3" style="width:100%">
                <!--begin::Table head-->
                <thead class="bg-gray-300 border-white">
                    <tr class="align-middle text-center fw-bold text-uppercase">
                        <th rowspan="3" class="min-w-225px bg-light-dark">No Permohonan</th>
                        <th rowspan="3" class="min-w-75px bg-light-dark">Kod Jarak</th>
                        <th rowspan="3" class="min-w-125px bg-light-dark">Penyedia Utiliti</th>
                        <th rowspan="3" class="min-w-100px mw-150px bg-light-dark">Link ID</th>
                        <th rowspan="3" class="min-w-100px bg-light-dark">Tapak A</th>
                        <th rowspan="3" class="min-w-100px bg-light-dark">Tapak B</th>
                        <th rowspan="3" class="min-w-100px bg-light-dark">Daerah</th>
                        <th rowspan="3" class="min-w-250px bg-light-dark">Jalan Terlibat</th>
                        <th rowspan="2" colspan="4" class="min-w-100px bg-light-dark">Jarak</th>
                        <th rowspan="2" colspan="4" class="text-wrap min-w-400px bg-light-dark">Status</th>
                        <th rowspan="3" class="text-wrap min-w-450px bg-light-dark">Tajuk</th>
                        <th rowspan="3" class="min-w-200px bg-light-dark">Pihak Berkuasa</th>
                        <th colspan="8" rowspan="1" data-dt-order="disable" class="bg-light-primary">Kelulusan Izin Lalu
                        </th>
                        <th colspan="8" rowspan="1" style="background-color:#F5EDE0">Pemetaan</th>
                        <th colspan="14" rowspan="1" class="bg-light-danger">Kelulusan Permit Kerja</th>
                        <th colspan="6" rowspan="1" class="bg-light-success">Notis Bekerja</th>
                        <th colspan="11" rowspan="1" class="bg-light-info">Sijil Siap Kerja</th>
                        <th colspan="8" rowspan="1" style="background-color:#ECF8FF">Sijil Perakuan Siap Memperbaiki
                            Kecacatan</th>
                        <th colspan="4" rowspan="1" style="background-color:#FFEBD9">Notis Pemulangan Wang Cagaran</th>
                        <th rowspan="3" class="bg-light-dark">Butiran Permohonan</th>
                    </tr>
                    <tr class="text-center fw-bold text-uppercase">
                        <th rowspan="2" rowspan="1" class="min-w-100px text-wrap bg-light-primary">Tarikh Permohonan
                        </th>
                        <th colspan="2" rowspan="1" class="bg-light-primary">Lawatan Tapak Awalan</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-primary">Surat Mohon kepada PBM</th>
                        <th colspan="2" rowspan="1" class="bg-light-primary">Surat Kelulusan Izin Lalu</th>
                        <th rowspan="2" class="min-w-100px text-wrap bg-light-primary">Surat Maklum Balas</th>


                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#F5EDE0">Ukur</th>
                        <th colspan="3" rowspan="1" class="text-wrap" style="background-color:#F5EDE0">Pelan PIU</th>
                        <th colspan="3" rowspan="1" class="text-wrap" style="background-color:#F5EDE0">Pelan PPT</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-danger">Surat Serahan Dokumen</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-danger">Surat Mohon kepada PBM</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-danger">Surat Kelulusan Permit Kerja</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-danger">Surat Perakuan Permit</th>
                        <th colspan="3" rowspan="1" class="text-wrap bg-light-danger">Tempoh Sah Permit (Hari Bekerja)
                        </th>
                        <th colspan="3" rowspan="1" class="text-wrap bg-light-danger">Tempoh Sah Permit (Hari Minggu)
                        </th>

                        <th colspan="3" rowspan="1" class="text-wrap bg-light-success">Notis Mula Kerja</th>
                        <th colspan="3" rowspan="1" class="text-wrap bg-light-success">Notis Siap Kerja</th>

                        <th colspan="2" rowspan="1" class="text-wrap bg-light-info">Surat Serahan Dokumen</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-info">Surat Mohon kepada PBM</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-info">Surat Kelulusan Siap Kerja</th>
                        <th colspan="2" rowspan="1" class="text-wrap bg-light-info">Surat Perakuan Siap Kerja</th>
                        <th colspan="3" rowspan="1" class="text-wrap bg-light-info">Tempoh Liabiliti Kecacatan</th>

                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#ECF8FF">Surat Serahan
                            Dokumen</th>
                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#ECF8FF">Surat Mohon
                            kepada PBM</th>
                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#ECF8FF">Surat Kelulusan
                            CMGD</th>
                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#ECF8FF">Surat Perakuan
                            CCC</th>

                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#FFEBD9">Surat Mohon
                            kepada PBM</th>
                        <th colspan="2" rowspan="1" class="text-wrap" style="background-color:#FFEBD9">Resit Pemulangan
                            Wang Cagaran</th>
                    </tr>
                    <tr class="text-center fw-bold text-uppercase">
                        <th class="text-wrap min-w-100px bg-light-dark">Mohon</th>
                        <th class="text-wrap min-w-100px bg-light-dark">GIS</th>
                        <th class="text-wrap min-w-100px bg-light-dark">PKD</th>
                        <th class="text-wrap min-w-100px bg-light-dark">Pelan</th>
                        <th class="text-wrap min-w-100px bg-light-dark">Operasi</th>
                        <th class="text-wrap min-w-100px bg-light-dark">Akaun</th>
                        <th class="text-wrap min-w-100px bg-light-dark">Geospatial</th>
                        <th class="text-wrap min-w-100px bg-light-dark">Pemetaan</th>
                        <!-- Izin Lalu -->


                        <th class="min-w-100px text-wrap bg-light-primary">Tarikh Lawatan</th>
                        <th class="min-w-100px text-wrap bg-light-primary">Tarikh Laporan</th>

                        <th class="min-w-100px text-wrap bg-light-primary">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap bg-light-primary">Tarikh Hantar</th>

                        <th class="min-w-100px text-wrap bg-light-primary">Tarikh Kelulusan</th>
                        <th class="min-w-100px text-wrap bg-light-primary">Tarikh Terima</th>

                        <!-- Ukur dan Pelan -->
                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Ukur</th>
                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Siap Ukur</th>

                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Penyediaan</th>
                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Siap</th>
                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Cop Sah</th>

                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Penyediaan</th>
                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Siap</th>
                        <th class="min-w-100px text-wrap" style="background-color:#F5EDE0">Tarikh Cop Sah</th>

                        <!-- Permit Kerja -->
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Terima</th>

                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Hantar</th>

                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Kelulusan</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Terima</th>

                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Perakuan</th>
                        <th class="min-w-100px text-wrap bg-light-danger">No Perakuan</th>

                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Mula</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Tamat</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tempoh</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Mula</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tarikh Tamat</th>
                        <th class="min-w-100px text-wrap bg-light-danger">Tempoh</th>

                        <!-- Notis Bekerja -->

                        <th class="min-w-100px text-wrap bg-light-success">Tarikh Notis</th>
                        <th class="min-w-100px text-wrap bg-light-success">Tarikh Terima</th>
                        <th class="min-w-100px text-wrap bg-light-success">Tarikh Mula</th>

                        <th class="min-w-100px text-wrap bg-light-success">Tarikh Notis</th>
                        <th class="min-w-100px text-wrap bg-light-success">Tarikh Terima</th>
                        <th class="min-w-100px text-wrap bg-light-success">Tarikh Siap</th>

                        <!-- Siap Kerja -->
                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Terima</th>

                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Hantar</th>

                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Kelulusan</th>
                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Terima</th>

                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Perakuan</th>
                        <th class="min-w-100px text-wrap bg-light-info">No Perakuan</th>

                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Mula</th>
                        <th class="min-w-100px text-wrap bg-light-info">Tarikh Tamat</th>
                        <th class="min-w-100px text-wrap bg-light-info">Tempoh</th>

                        <!-- Pembaikan Kecacatan -->
                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Terima</th>

                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Hantar</th>

                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Kelulusan</th>
                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Terima</th>

                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">Tarikh Perakuan</th>
                        <th class="min-w-100px text-wrap" style="background-color:#ECF8FF">No Perakuan</th>

                        <!-- Wang Cagaran -->
                        <th class="min-w-100px text-wrap" style="background-color:#FFEBD9">Tarikh Surat</th>
                        <th class="min-w-100px text-wrap" style="background-color:#FFEBD9">Tarikh Hantar</th>

                        <th class="min-w-100px text-wrap" style="background-color:#FFEBD9">Tarikh Kelulusan</th>
                        <th class="min-w-100px text-wrap" style="background-color:#FFEBD9">No Resit</th>
                    </tr>
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="text-gray-800 text-center">
                    <?php

                    foreach ($data as $field) {
                        // $provider = General::getProvider($field->utility_provider);
                        // $date = new DateTime($field->created_date);

                        echo '<tr>';
                        // NOTE - Reference Number
                        echo $field->reference_no === NULL ? '<td class="fw-bold"  >#' . $field->system_id . '</td>' : '<td class="fw-bold"  data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="' . $field->system_id . '" data-bs-delay-hide="1000">' . $field->reference_no . '</td>';
                        // NOTE - Length Code
                        echo empty($field->length_code) ? '<td >-</td>' : '<td >' . $field->length_code . '</td>';
                        // NOTE - Provider
                        echo empty($field->provider_name) ? '<td class="min-w-125px">-</td>' : '<td class="min-w-125px">
                        <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $field->provider_name . '">
                        <div class="symbol symbol-50px">
                        <img src="' . $ec_url . '/providers/logo/' . $field->provider_logo . '" alt="' . $field->provider_name . '"  />
                        </div>
                        <span class="d-none">' . $field->provider_name . '</span></div>
                        </td>';
                        // NOTE - Link ID
                        echo empty($field->link_id) ? '<td class="min-w-100px">-</td>' : '<td class="text-wrap min-w-100px mw-150px">' . $field->link_id . '</td>';
                        // NOTE - Site Start
                        echo empty($field->site_start) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . $field->site_start . '</td>';
                        // NOTE - Site End
                        echo empty($field->site_end) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . $field->site_end . '</td>';
                        // NOTE - District
                        echo '<td class="min-w-100px">';
                        if (!empty($field->districts)) {
                            $districts = explode(",", $field->districts);
                            foreach ($districts as $index => $district) {
                                if ($index === 0) {
                                    echo '<span class="badge badge-secondary me-2 text-wrap">' . $district . '</span>';
                                } else {
                                    echo '<br/>';
                                    echo '<span class="badge badge-secondary me-2 text-wrap">' . $district . '</span>';
                                }

                            }
                        } else {
                            echo '-';
                        }
                        echo '</td>';
                        // NOTE - Road
                        echo '<td class="min-w-250px">';
                        if (!empty($field->roads)) {
                            $roads = explode(",", $field->roads);
                            foreach ($roads as $index => $road) {
                                if ($index === 0) {
                                    echo '<span class="badge badge-secondary me-2 text-capitalize text-wrap">' . $road . '</span>';
                                } else {
                                    echo '<br/>';
                                    echo '<span class="badge badge-secondary me-2 text-capitalize text-wrap">' . $road . '</span>';
                                }

                            }
                        } else {
                            echo '-';
                        }
                        echo '</td>';
                        // NOTE - Application Distance
                        echo empty($field->application_length) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . $field->application_length . ' m</td>';
                        // NOTE - GIS Distance
                        echo empty($field->gis_length) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . $field->gis_length . ' m</td>';
                        // NOTE - PKD Distance
                        echo empty($field->pkd_length) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . $field->pkd_length . ' m</td>';
                        // NOTE - Survey Distance
                        echo empty($field->survey_length) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . $field->survey_length . ' m</td>';
                        // NOTE - Status
                        // operation status
                        echo empty($field->op_status_id) ? '<td class="text-wrap min-w-100px">-</td>' : '<td class="min-w-100px"><span class="text-wrap badge badge-light-' . $field->op_color . ' me-2">' . $field->op_flow_name . '</span></td>';
                        // finance status
                        echo empty($field->acc_status_id) ? '<td class="text-wrap min-w-100px">-</td>' : '<td class="min-w-100px"><span class="text-wrap badge badge-light-' . $field->acc_color . ' me-2">' . $field->acc_flow_name . '</span></td>';
                        // gis status
                        echo empty($field->gis_status_id) ? '<td class="text-wrap min-w-100px">-</td>' : '<td class="min-w-100px"><span class="text-wrap badge badge-light-' . $field->gis_color . ' me-2">' . $field->gis_flow_name . '</span></td>';
                        // survey status
                        echo empty($field->sur_status_id) ? '<td class="text-wrap min-w-100px">-</td>' : '<td class="min-w-100px"><span class="text-wrap badge badge-light-' . $field->sur_color . ' me-2">' . $field->sur_flow_name . '</span></td>';

                        // NOTE - Project Title
                        echo empty($field->project_title) ? '<td class="text-wrap min-w-450px" >-</td>' : '<td class="text-wrap mw-450px">' . $field->project_title . '</td>';
                        // NOTE - Authority
                        if ($field->authority != null) {
                            echo '<td class="text-start min-w-200px">';
                            foreach ($field->authority as $index => $authority) {
                                // if($index === 0){

                                if ($index === 0) {
                                    echo empty($authority->name) ? '-' : '<div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $authority->name . '">
                                    <div class="symbol symbol-40px mx-2">
                                    <img src="assets/media/authorities/' . $authority->logo . '.png" alt="' . $authority->sort_name . '"  />
                                    </div>
                                    <span class="">' . $authority->sort_name . '</span></div>';
                                } else {
                                    echo '<br/>';
                                    echo empty($authority->name) ? '-' : '<div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $authority->name . '">
                                    <div class="symbol symbol-40px mx-2">
                                    <img src="assets/media/authorities/' . $authority->logo . '.png" alt="' . $authority->sort_name . '"  />
                                    </div>
                                    <span class="">' . $authority->sort_name . '</span></div>';
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td >-</td>';
                        }
                        // NOTE - Tarikh Surat Permohonan
                        echo empty($field->application_date) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->application_date))->format('d/m/Y') . '</td>';

                        // NOTE - Tarikh Lawatan Tapak
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->actual_sv_date) ? '-' : (new DateTime($authority->actual_sv_date))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->actual_sv_date) ? '-' : (new DateTime($authority->actual_sv_date))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Laporan
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->report_date) ? '-' : (new DateTime($authority->report_date))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->report_date) ? '-' : (new DateTime($authority->report_date))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Surat Kpd PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wl_auth_created) ? '-' : (new DateTime($authority->wl_auth_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wl_auth_created) ? '-' : (new DateTime($authority->wl_auth_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh PBM Hantar
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wl_auth_send) ? '-' : (new DateTime($authority->wl_auth_send))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wl_auth_send) ? '-' : (new DateTime($authority->wl_auth_send))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh KIL
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wl_appv) ? '-' : (new DateTime($authority->wl_appv))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wl_appv) ? '-' : (new DateTime($authority->wl_appv))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima KIL
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wl_appv_received) ? '-' : (new DateTime($authority->wl_appv_received))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wl_appv_received) ? '-' : (new DateTime($authority->wl_appv_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh MKIL
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wl_fb_created) ? '-' : (new DateTime($authority->wl_fb_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wl_fb_created) ? '-' : (new DateTime($authority->wl_fb_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }


                        // NOTE - Tarikh Ukur Pelan PIU & PPT
                        echo empty($field->start_survey) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->start_survey))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Siap Ukur Pelan PIU & PPT
                        echo empty($field->end_survey) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->end_survey))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Penyediaan PIU
                        echo empty($field->start_udm) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->start_udm))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Siap PIU
                        echo empty($field->end_udm) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->end_udm))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Cop Sah PIU
                        echo empty($field->submitted_udm) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->submitted_udm))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Penyediaan PPT
                        echo empty($field->start_tmp) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->start_tmp))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Siap PPT
                        echo empty($field->end_tmp) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->end_tmp))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Cop Sah PPT
                        echo empty($field->submitted_tmp) ? '<td class="min-w-100px">-</td>' : '<td class="min-w-100px">' . (new DateTime($field->submitted_tmp))->format('d/m/Y') . '</td>';
                        // NOTE - Tarikh Surat Dokumen Permit
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_appl_created) ? '-' : (new DateTime($authority->wp_appl_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Dokumen Permit
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_appl_received) ? '-' : (new DateTime($authority->wp_appl_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Surat Kpd PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_auth_created) ? '-' : (new DateTime($authority->wp_auth_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_auth_created) ? '-' : (new DateTime($authority->wp_auth_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Hantar PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_auth_send) ? '-' : (new DateTime($authority->wp_auth_send))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_auth_send) ? '-' : (new DateTime($authority->wp_auth_send))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Kelulusan Permit Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_appv_created) ? '-' : (new DateTime($authority->wp_appv_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_appv_created) ? '-' : (new DateTime($authority->wp_appv_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Kelulusan Permit Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_appv_received) ? '-' : (new DateTime($authority->wp_appv_received))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_appv_received) ? '-' : (new DateTime($authority->wp_appv_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Perakuan Permit Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_recog_created) ? '-' : (new DateTime($authority->wp_recog_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_recog_created) ? '-' : (new DateTime($authority->wp_recog_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - No Perakuan Permit Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->no_wp_recog) ? '-' : $authority->no_wp_recog;
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->no_wp_recog) ? '-' : $authority->no_wp_recog;
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Mula Permit Hari Bekerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_wday_strt) ? '-' : (new DateTime($authority->wp_wday_strt))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_wday_strt) ? '-' : (new DateTime($authority->wp_wday_strt))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Tamat Permit Hari Bekerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_wday_end) ? '-' : (new DateTime($authority->wp_wday_end))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_wday_end) ? '-' : (new DateTime($authority->wp_wday_end))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tempoh Permit Hari Bekerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo ($authority->wp_wday_period === null) ? '-' : $authority->wp_wday_period . ' Hari';
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo ($authority->wp_wday_period === null) ? '-' : $authority->wp_wday_period . ' Hari';
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Mula Permit Hari Minggu
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_wend_strt) ? '-' : (new DateTime($authority->wp_wend_strt))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_wend_strt) ? '-' : (new DateTime($authority->wp_wend_strt))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Tamat Permit Hari Minggu
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wp_wend_end) ? '-' : (new DateTime($authority->wp_wend_end))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wp_wend_end) ? '-' : (new DateTime($authority->wp_wend_end))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tempoh Permit Hari Minggu
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo ($authority->wp_wend_period == null) ? '-' : $authority->wp_wend_period . ' Hari';
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo ($authority->wp_wend_period == null) ? '-' : $authority->wp_wend_period . ' Hari';
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Notis Mula Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->nt_ws_created) ? '-' : (new DateTime($authority->nt_ws_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Notis Mula Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->nt_ws_received) ? '-' : (new DateTime($authority->nt_ws_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh  Mula Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->notice_start) ? '-' : (new DateTime($authority->notice_start))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Notis Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->nt_wf_created) ? '-' : (new DateTime($authority->nt_wf_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Notis Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->nt_wf_received) ? '-' : (new DateTime($authority->nt_wf_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->notice_finish) ? '-' : (new DateTime($authority->notice_finish))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Surat Serahan Dokumen Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_appl_created) ? '-' : (new DateTime($authority->wf_appl_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Dokumen Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_appl_received) ? '-' : (new DateTime($authority->wf_appl_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Surat Sijil Siap Kerja ke PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_auth_created) ? '-' : (new DateTime($authority->wf_auth_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wf_auth_created) ? '-' : (new DateTime($authority->wf_auth_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh PBM Hantar Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_auth_send) ? '-' : (new DateTime($authority->wf_auth_send))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wf_auth_send) ? '-' : (new DateTime($authority->wf_auth_send))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Kelulusan Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_appv_created) ? '-' : (new DateTime($authority->wf_appv_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wf_appv_created) ? '-' : (new DateTime($authority->wf_appv_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Kelulusan Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_appv_received) ? '-' : (new DateTime($authority->wf_appv_received))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wf_appv_received) ? '-' : (new DateTime($authority->wf_appv_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Perakuan Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->wf_recog_created) ? '-' : (new DateTime($authority->wf_recog_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->wf_recog_created) ? '-' : (new DateTime($authority->wf_recog_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - No Perakuan Sijil Siap Kerja
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->no_wf_recog) ? '-' : $authority->no_wf_recog;
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->no_wf_recog) ? '-' : $authority->no_wf_recog;
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Mula Tempoh Liabilti Kecacatan
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->dt_dlp_start) ? '-' : (new DateTime($authority->dt_dlp_start))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->dt_dlp_start) ? '-' : (new DateTime($authority->dt_dlp_start))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Tamat Tempoh Liabilti Kecacatan
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->dt_dlp_end) ? '-' : (new DateTime($authority->dt_dlp_end))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->dt_dlp_end) ? '-' : (new DateTime($authority->dt_dlp_end))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tempoh Liabilti Kecacatan
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo ($authority->wf_dlp_period == null) ? '-' : $authority->wf_dlp_period . ' Hari';
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo ($authority->wf_dlp_period == null) ? '-' : $authority->wf_dlp_period . ' Hari';
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh serahan dokumen CMGD
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->cmgd_appl_created) ? '-' : (new DateTime($authority->cmgd_appl_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->cmgd_appl_created) ? '-' : (new DateTime($authority->cmgd_appl_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh terima serahan dokumen CMGD
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->cmgd_appl_received) ? '-' : (new DateTime($authority->cmgd_appl_received))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->cmgd_appl_received) ? '-' : (new DateTime($authority->cmgd_appl_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh surat CMGD ke PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->cmgd_auth_created) ? '-' : (new DateTime($authority->cmgd_auth_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->cmgd_auth_created) ? '-' : (new DateTime($authority->cmgd_auth_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Hantar CMGD PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->cmgd_auth_send) ? '-' : (new DateTime($authority->cmgd_auth_send))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->cmgd_auth_send) ? '-' : (new DateTime($authority->cmgd_auth_send))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Kelulusan CMGD
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->cmgd_appv_created) ? '-' : (new DateTime($authority->cmgd_appv_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->cmgd_appv_created) ? '-' : (new DateTime($authority->cmgd_appv_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Terima Kelulusan CMGD
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->cmgd_appv_received) ? '-' : (new DateTime($authority->cmgd_appv_received))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->cmgd_appv_received) ? '-' : (new DateTime($authority->cmgd_appv_received))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Perakuan CCC
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->ccc_recog_created) ? '-' : (new DateTime($authority->ccc_recog_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->ccc_recog_created) ? '-' : (new DateTime($authority->ccc_recog_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - No Perakuan CCC
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->no_ccc_recog) ? '-' : $authority->no_ccc_recog;
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->no_ccc_recog) ? '-' : $authority->no_ccc_recog;
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Surat ke PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->dr_auth_created) ? '-' : (new DateTime($authority->dr_auth_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->dr_auth_created) ? '-' : (new DateTime($authority->dr_auth_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Tarikh Hantar PBM
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->dr_auth_send) ? '-' : (new DateTime($authority->dr_auth_send))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->dr_auth_send) ? '-' : (new DateTime($authority->dr_auth_send))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - Kelulusan Pemulangan Wang Cagaran
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->dr_appv_created) ? '-' : (new DateTime($authority->dr_appv_created))->format('d/m/Y');
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->dr_appv_created) ? '-' : (new DateTime($authority->dr_appv_created))->format('d/m/Y');
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // NOTE - No Resit Wang Cagaran
                        if ($field->authority != null) {
                            echo '<td class="min-w-100px">';
                            foreach ($field->authority as $index => $authority) {
                                if ($index === 0) {
                                    echo empty($authority->dr_no_voucher) ? '-' : $authority->dr_no_voucher;
                                } else {
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo '<br/>';
                                    echo empty($authority->dr_no_voucher) ? '-' : $authority->dr_no_voucher;
                                }
                            }
                            echo '</td>';
                        } else {
                            echo '<td>-</td>';
                        }
                        // Redirect to a page
                        echo empty($field->system_id) ? '<td>-</td>' : '<td>
                            <a href="projects/details/' . $field->system_id . '" class="btn btn-icon btn-light-' . (empty($field->op_color) ? 'secondary' : $field->op_color) . ' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Butiran Permohonan" target="_blank" ><i class="fad fa-file-lines fs-4"></i></a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
                <!--end::Table body-->
            </table>
        </div>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Table Tracker-->