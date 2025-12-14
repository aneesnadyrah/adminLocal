<!--begin::Products-->
 <div class="card card-flush">
     <!--begin::Card header-->
     <div class="card-header align-items-center py-5 gap-2 gap-md-5">
         <!--begin::Card title-->
         <div class="card-title">
             <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                <i class="fad fa-search position-absolute ms-4"></i>
                <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14"
                    placeholder="Carian Laporan..." />
            </div>
            <!--end::Search-->
         </div>
         <!--end::Card title-->
         <!--begin::Card toolbar-->
         <!-- <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
             <button type="button" href="#" class="btn btn-flex flex-center btn-primary w-40px w-md-auto h-40px px-0 px-md-6" data-bs-toggle="modal" data-bs-target="#survey-add-team">
                 <i class="fad fa-circle-plus fs-4"></i>
                 <span class="d-none d-md-inline">Tambah</span>
             </button>
         </div> -->
         <!--end::Card toolbar-->
     </div>
     <!--end::Card header-->
     <!--begin::Card body-->
     <div class="card-body pt-0">
         <!--begin::Table-->
         <table class="table align-middle table-row-dashed fs-6 gy-5" id="survey-report-list">
             <!--begin::Table head-->
             <thead>
                 <!--begin::Table row-->
                 <tr class="text-center text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                     <th class="text-center">No Permohonan</th>
                     <th class="text-center">Tarikh Laporan</th>
                     <th class="text-center">Penyedia Utiliti</th>
                     <th class="text-center">Daerah</th>
                     <th class="text-center">Jarak</th>
                     <th class="text-center">Kumpulan</th>
                     <th class="text-center">Tindakan</th>
                 </tr>
                 <!--end::Table row-->
             </thead>
             <!--end::Table head-->
             <!--begin::Table body-->
             <tbody class="fw-semibold text-gray-700">
            <?php 
            $i = 0; $systemId = ''; $sysId=[];
            foreach ($data as $field) : 
                if($i == 0 || $systemId != $field->system_id) {
                    $provider = General::getProvider($field->utility_provider);
            ?>
            <tr class="text-center">
                <td>
                    <a href="surveys/site/viewReport/<?= $field->system_id; ?>" class="text-gray-800 text-hover-primary mb-1"><?= $field->reference_no; ?></a>
                </td>
                <td><?= isset($field->survey_date) ? date("d/m/Y", strtotime($field->survey_date)):''; ?></td>
                <td><div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $provider->name; ?>">
                        <div class="symbol symbol-50px">
                        <img src="<?= $provider->logo; ?>" alt="<?= $provider->name; ?>"  />
                        </div>
                        <span class="d-none"><?= $provider->name; ?></span></td>
                <td><span class="badge badge-secondary me-2"><?= $field->districts; ?></span></td>
                <td><?= $field->survey_length; ?> km</td>
                <td><?= $field->survey_team; ?></td>
                <td>
                    <a href="surveys/site/viewReport/<?= $field->system_id; ?>" class="btn btn-icon btn-light-primary btn-active-color-light btn-sm me-1" title="Lihat Laporan">
                        <i class="fad fa-regular fa-file fs-2"></i>
                    </a>        
                </td>
            </tr>
            <?php
                }
                $systemId = $field->system_id;
                $i++;
            ?>
                
                <?php endforeach; ?>
             </tbody>
             <!--end::Table body-->
         </table>
         <!--end::Table-->
     </div>
     <!--end::Card body-->
 </div>
 <!--end::Products-->