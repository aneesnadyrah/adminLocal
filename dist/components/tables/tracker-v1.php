<?php
$List = new Lists();
$tracker = new Tracker();
?>

<!--begin::Card Total-->
<div class="row gx-10 mb-4">  
    <?php $tracker->widget('tracker-tasks'); ?>
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
                <button type="button" class="btn btn-secondary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span class="path2"></span></i>
                    Jana Rumusan
                </button>
                <!--begin::Menu-->
                <div id="kt_datatable_example_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
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
                <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status" data-table-filter="status">
                <option value="all">Semua</option>
                <?php
                $statuses = $List->get('ls_statuses');

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
        <table class="table table-striped table-rounded table-bordered text-nowrap display compact gs-3" id="tracker-v1" style="width:100%">
            <!--begin::Table head-->
            <thead class="bg-gray-300 border-white">
                <tr class="align-middle text-center fw-bold text-uppercase">
                    <th rowspan="3">No Permohonan</th>
                    <th rowspan="3">Kod Jarak</th>
                    <th rowspan="3">Penyedia Utiliti</th>
                    <th rowspan="3">Link ID</th>
                    <th rowspan="3">Tapak A</th>
                    <th rowspan="3">Tapak B</th>
                    <th rowspan="3">Daerah</th>
                    <th rowspan="3">Jalan Terlibat</th>
                    <th rowspan="3">Jarak</th>
                    <th rowspan="3">Status</th>
                    <th rowspan="3" class="text-nowrap">Tajuk</th>
                    <th colspan="9" rowspan="1">Kelulusan Izin Lalu</th>
                    <th colspan="22" rowspan="1">Kelulusan Permit Kerja</th>
                    <th colspan="4" rowspan="1">Notis Bekerja</th>
                    <th colspan="11" rowspan="1">Sijil Siap Kerja</th>
                    <th colspan="8" rowspan="1">Sijil Perakuan Siap Memperbaiki Kecacatan</th>
                    <th colspan="5" rowspan="1">Notis Pemulangan Wang Cagaran</th>
                    <th rowspan="3">Lampiran</th>
                </tr>
                <tr class="text-center fw-bold text-uppercase">
                    <th colspan="2" rowspan="1">Surat Masuk Permohonan</th>
                    <th colspan="2" rowspan="1">Lawatan Tapak Awalan</th>
                    <th colspan="2" rowspan="1">Surat Keluar kepada PBM</th>
                    <th colspan="2" rowspan="1">Surat Kelulusan Izin Lalu</th>
                    <th rowspan="2">Surat Maklum Balas</th>

                    <th colspan="2" rowspan="1">Surat Serahan Dokumen</th>
                    <th colspan="8" rowspan="1">Pelan PIU dan PPT</th>
                    <th colspan="2" rowspan="1">Surat Keluar kepada PBM</th>
                    <th colspan="2" rowspan="1">Surat Kelulusan Permit Kerja</th>
                    <th colspan="2" rowspan="1">Surat Perakuan Permit</th>
                    <th colspan="6" rowspan="1">Tempoh Sah Permit</th>

                    <th colspan="2" rowspan="1">Notis Mula Kerja</th>
                    <th colspan="2" rowspan="1">Notis Siap Kerja</th>

                    <th colspan="2" rowspan="1">Surat Serahan Dokumen</th>
                    <th colspan="2" rowspan="1">Surat Keluar kepada PBM</th>
                    <th colspan="2" rowspan="1">Surat Kelulusan Siap Kerja</th>
                    <th colspan="2" rowspan="1">Surat Perakuan Siap Kerja</th>
                    <th colspan="3" rowspan="1">Tempoh Liabiliti Kecacatan</th>

                    <th colspan="2" rowspan="1">Lawatan Tapak</th>
                    <th colspan="2" rowspan="1">Surat Keluar kepada PBM</th>
                    <th colspan="2" rowspan="1">Surat Kelulusan CMGD</th>
                    <th colspan="2" rowspan="1">Surat Perakuan CMGD</th>

                    <th rowspan="2" class="text-center">Nilai Wang Cagaran</th>
                    <th colspan="2" rowspan="1">Surat Keluar kepada PBM</th>
                    <th colspan="2" rowspan="1">Resit Pemulangan Wang Cagaran</th>
                </tr>
                <tr class="text-center fw-bold text-uppercase">
                    <!-- Izin Lalu -->
                    <th>Tarikh Surat Permohonan</th>
                    <th>Tarikh Permohonan Diterima</th>

                    <th>Tarikh Lawatan Tapak</th>
                    <th>Tarikh Laporan</th>

                    <th>Tarikh Keluar</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Kelulusan</th>
                    <th>Tarikh Terima</th>

                    <!-- Permit Kerja -->
                    <th>Tarikh Serahan</th>
                    <th>Tarikh Lengkap</th>

                    <th>Tarikh Ukur</th>
                    <th>Tarikh Siap Ukur</th>

                    <th>Tarikh Penyediaan PIU</th>
                    <th>Tarikh Siap PIU</th>
                    <th>Tarikh Cop Sah PIU</th>

                    <th>Tarikh Penyediaan PPT</th>
                    <th>Tarikh Siap PPT</th>
                    <th>Tarikh Cop Sah PPT</th>

                    <th>Tarikh Keluar</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Kelulusan</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Perakuan</th>
                    <th>No Perakuan</th>

                    <th>Tarikh Mula (Hari Bekerja)</th>
                    <th>Tarikh Tamat (Hari Bekerja)</th>
                    <th>Tempoh (Hari Bekerja)</th>
                    <th>Tarikh Mula (Hari Minggu)</th>
                    <th>Tarikh Tamat (Hari Minggu)</th>
                    <th>Tempoh (Hari Minggu)</th>

                    <!-- Notis Bekerja -->

                    <th>Tarikh Notis</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Notis</th>
                    <th>Tarikh Terima</th>

                    <!-- Siap Kerja -->
                    <th>Tarikh Serahan</th>
                    <th>Tarikh Lengkap</th>

                    <th>Tarikh Keluar</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Kelulusan</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Perakuan</th>
                    <th>No Perakuan</th>

                    <th>Tarikh Mula</th>
                    <th>Tarikh Tamat</th>
                    <th>Tempoh</th>

                    <!-- Pembaikan Kecacatan -->
                    <th>Tarikh Lawatan Tapak</th>
                    <th>Tarikh Laporan</th>

                    <th>Tarikh Keluar</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Kelulusan</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Perakuan</th>
                    <th>No Perakuan</th>


                    <!-- Wang Cagaran -->
                    <th>Tarikh Keluar</th>
                    <th>Tarikh Terima</th>

                    <th>Tarikh Resit</th>
                    <th>No Resit</th>
                </tr>
            </thead>
            <!--end::Table head-->
            <!--begin::Table body-->
            <tbody class="text-gray-800 text-center">
                <?php
                    foreach($data AS $field) {
                        $provider = General::getProvider($field->provider_id);
                        // $date = new DateTime($field->created_date);

                        echo '<tr>';
                        // NOTE - Reference Number
                        echo $field->reference_no === NULL ? '<td class="fw-bold">#'. $field->system_id .'</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="'. $field->system_id .'" data-bs-delay-hide="1000">'. $field->reference_no .'</td>';
                        // NOTE - Length Code
                        echo empty($field->length_code) ? '<td>-</td>' : '<td>'. $field->length_code .'</td>';
                        // NOTE - Provider
                        echo empty($provider->name) ? '<td>-</td>' : '<td>
                                    <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $provider->name .'">
                                    <div class="symbol symbol-50px">
                                    <img src="'. $provider->logo .'" alt="'. $provider->name .'"  />
                                    </div>
                                    <span class="d-none">'. $provider->name  .'</span></div>
                                    </td>' ;
                        // NOTE - Link ID
                        echo empty($field->link_id) ? '<td>-</td>' : '<td>'. $field->link_id .'</td>';
                        // NOTE - Site Start
                        echo empty($field->site_start) ? '<td>-</td>' : '<td>'. $field->site_start .'</td>';
                        // NOTE - Site End
                        echo empty($field->site_end) ? '<td>-</td>' : '<td>'. $field->site_end .'</td>';
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
                        // NOTE - Road
                        echo '<td>';
                        if(!empty($field->roads)) {
                            $roads = explode(",", $field->roads);
                            foreach ($roads as $road) {
                                echo '<span class="badge badge-secondary me-2 text-capitalize">'. $road .'</span>';
                            } 
                        } else {
                            echo '-';
                        }
                        echo '</td>';
                        // NOTE - Distance
                        echo empty($field->application_length) ? '<td>-</td>' : '<td>'. $field->application_length .' m</td>';
                        // NOTE - Status
                        echo '<td>
                        <span class="badge badge-light-'. $field->status_color .' me-2">'. $field->status .'</span>
                        </td>';
                        // NOTE - Project Title
                        echo empty($field->project_title) ? '<td>-</td>' : '<td>'. $field->project_title .'</td>';       
                        // NOTE - Tarikh Surat Permohonan 
                        echo empty($field->application_date) ? '<td>-</td>' : '<td>'. (new DateTime($field->application_date))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Permohonan Diterima 
                        echo empty($field->created_date) ? '<td>-</td>' : '<td>'. (new DateTime($field->created_date))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Lawatan Tapak  
                        echo empty($field->sv_date_done) ? '<td>-</td>' : '<td>'. (new DateTime($field->sv_date_done))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Laporan 
                        echo '<td></td>';
                        // NOTE - Tarikh Keluar Kpd PBM 
                        echo empty($field->wayleave_dt_auth_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->wayleave_dt_auth_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh PBM Terima 
                        echo empty($field->wayleaves_dt_auth_ltr_send) ? '<td>-</td>' : '<td>'. (new DateTime($field->wayleaves_dt_auth_ltr_send))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh KIL 
                        echo empty($field->dt_appv_ltr) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_appv_ltr))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Terima KIL
                        echo empty($field->wayleaves_dt_appv_ltr_received) ? '<td>-</td>' : '<td>'. (new DateTime($field->wayleaves_dt_appv_ltr_received))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh MKIL
                        echo empty($field->dt_fb_ltr_send) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_fb_ltr_send))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Serahan Dokumen
                        echo '<td>-</td>';
                        // NOTE - Tarikh Lengkap Serahan Dokumen
                        echo '<td>-</td>';
                        // NOTE - Tarikh Ukur Pelan PIU & PPT
                        echo '<td>-</td>';
                        // NOTE - Tarikh Siap Ukur Pelan PIU & PPT
                        echo '<td>-</td>';
                        // NOTE - Tarikh Penyediaan PIU
                        echo '<td>-</td>';
                        // NOTE - Tarikh Siap PIU
                        echo '<td>-</td>';
                        // NOTE - Tarikh Cop Sah PIU
                        echo '<td>-</td>';
                        // NOTE - Tarikh Penyediaan PPT
                        echo '<td>-</td>';
                        // NOTE - Tarikh Siap PPT
                        echo '<td>-</td>';
                        // NOTE - Tarikh Cop Sah PPT
                        echo '<td>-</td>';
                        // NOTE - Tarikh Keluar Kpd PBM
                        echo empty($field->permit_dt_auth_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->permit_dt_auth_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh PBM Terima
                        echo empty($field->permit_dt_auth_ltr_send) ? '<td>-</td>' : '<td>'. (new DateTime($field->permit_dt_auth_ltr_send))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Kelulusan Permit Kerja
                        echo empty($field->permit_dt_appv_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->permit_dt_appv_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Terima Kelulusan Permit Kerja
                        echo empty($field->dt_appv_received) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_appv_received))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Perakuan Permit Kerja
                        echo empty($field->dt_wp_recog_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wp_recog_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - No Perakuan Permit Kerja
                        echo empty($field->no_wp_recog) ? '<td>-</td>' : '<td>'. $field->no_wp_recog .'</td>' ;
                        // NOTE - Tarikh Mula Permit Hari Bekerja
                        echo empty($field->dt_wday_strt) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wday_strt))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Tamat Permit Hari Bekerja
                        echo empty($field->dt_wday_end) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wday_end))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tempoh Permit Hari Bekerja
                        echo '<td>-</td>';
                        // NOTE - Tarikh Mula Permit Hari Minggu
                        echo empty($field->dt_wend_strt) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wend_strt))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Tamat Permit Hari Minggu
                        echo empty($field->dt_wend_end) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wend_end))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tempoh Permit Hari Minggu
                        echo '<td>-</td>';
                        // NOTE - Tarikh Notis Mula Kerja
                        echo empty($field->dt_ws_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_ws_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Terima Notis Mula Kerja
                        echo empty($field->dt_ws_ltr_received) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_ws_ltr_received))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Notis Siap Kerja
                        echo empty($field->dt_wf_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wf_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Terima Notis Siap Kerja
                        echo empty($field->dt_wf_ltr_received) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wf_ltr_received))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Serahan Dokumen Sijil Siap Kerja
                        echo empty($field->dt_wfal_submitted) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wfal_submitted))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Lengkap Dokumen Sijil Siap Kerja
                        echo empty($field->dt_wfal_checked) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wfal_checked))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Keluar Sijil Siap Kerja ke PBM
                        echo empty($field->finish_dt_auth_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->finish_dt_auth_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh PBM Terima Sijil Siap Kerja
                        echo empty($field->finish_dt_auth_ltr_send) ? '<td>-</td>' : '<td>'. (new DateTime($field->finish_dt_auth_ltr_send))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Kelulusan Sijil Siap Kerja
                        echo empty($field->finish_dt_appv_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->finish_dt_appv_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Terima Kelulusan Sijil Siap Kerja
                        echo empty($field->finish_dt_appv_ltr_received) ? '<td>-</td>' : '<td>'. (new DateTime($field->finish_dt_appv_ltr_received))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Perakuan Sijil Siap Kerja
                        echo empty($field->dt_wf_recog_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_wf_recog_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - No Perakuan Sijil Siap Kerja
                        echo empty($field->no_wf_recog) ? '<td>-</td>' : '<td>'. $field->no_wf_recog .'</td>' ;
                        // NOTE - Tarikh Mula Tempoh Liabilti Kecacatan
                        echo empty($field->dt_dlp_start) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_dlp_start))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Tamat Tempoh Liabilti Kecacatan
                        echo empty($field->dt_dlp_end) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_dlp_end))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tempoh Liabilti Kecacatan
                        echo '<td>-</td>';
                        // NOTE - Tarikh LTA
                        echo '<td>-</td>';
                        // NOTE - Tarikh Laporan LTA
                        echo '<td>-</td>';
                        // NOTE - Tarikh CMGD ke PBM
                        echo empty($field->dt_cmgd_auth_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_cmgd_auth_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh PBM Terima CMGD
                        echo empty($field->dt_cmgd_auth_send) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_cmgd_auth_send))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Kelulusan CMGD
                        echo empty($field->dt_cmgd_appv_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_cmgd_appv_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Terima Kelulusan CMGD
                        echo empty($field->dt_cmgd_appv_ltr_received) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_cmgd_appv_ltr_received))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh Perakuan CCC
                        echo empty($field->dt_ccc_recog_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_ccc_recog_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - No Perakuan CCC
                        echo empty($field->no_ccc_recog) ? '<td>-</td>' : '<td>'. $field->no_ccc_recog .'</td>' ;
                        // NOTE - Nilai Wang Cagaran
                        echo '<td>-</td>';
                        // NOTE - Tarikh Keluar ke PBM
                        echo empty($field->dt_auth_ltr_created) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_auth_ltr_created))->format('d/m/Y') .'</td>' ;
                        // NOTE - Tarikh PBM Terima
                        echo empty($field->dt_auth_ltr_send) ? '<td>-</td>' : '<td>'. (new DateTime($field->dt_auth_ltr_send))->format('d/m/Y') .'</td>' ;
                        // NOTE - Resit Pemulangan Wang Cagaran
                        echo '<td>-</td>';
                        // NOTE - No Resit Wang Cagaran
                        echo '<td>-</td>';
                        // Redirect to a page
                        echo empty($field->system_id) ? '<td>dd-</td>' : '<td>
                        <a href="tracker/attachments/list/'.$field->system_id.'" class="btn btn-icon btn-light-' . $field->status_color . ' m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Lampiran" target="_blank"><i class="fad fa-file-lines fs-4"></i></a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
            <!--end::Table body-->

            <!--start::Table footer-->
            <!-- <tfoot>
                <tr>
                    <th colspan="68" style="text-align:right" id="lengthFooter"></th>
                    <th></th>
                </tr>
            </tfoot> -->
            <!--end::Table footer-->
        </table>
        <!--end::Table-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Table Tracker-->

