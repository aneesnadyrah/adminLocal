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
                    placeholder="Carian Permohonan..." />
            </div>
            <!--end::Search-->
        </div>
        <!--end::Card title-->
        <!--begin::Card toolbar-->
        <div class="w-100 mw-150px">
            <!--begin::Select2-->
            <select class="form-select form-select-solid" data-control="select2" data-placeholder="Status"
                data-table-filter="status">
                <option value="all">Semua</option>
                <?php
                    echo '<option value="Pembayaran Caj Pendaftaran">Pembayaran Caj Pendaftaran</option>';
                    echo '<option value="Pindaan Lawatan Tapak">Pindaan Lawatan Tapak</option>'; 
                    echo '<option value="Pindaan Lawatan Tapak">Sebut Harga Dikeluarkan</option>';
                ?>
            </select>
            <!--end::Select2-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
     <!--begin::Card body-->
     <div class="card-body pt-0">
         <!--begin::Table-->
         <table class="table table-row-dashed align-middle fs-6 gy-5" id="client-status">
             <!--begin::Table head-->
             <thead>
                 <!--begin::Table row-->
                 <tr class="text-gray-400 fw-bold fs-7 text-uppercase">
                     <th class="text-center">No Permohonan</th>
                     <th class="text-center">Tarikh Mohon</th>
                     <th class="text-center">Penyedia Utiliti</th>
                     <th class="text-center">Daerah</th>
                     <th class="text-center">Jarak (m)</th>
                     <th class="text-center">Status</th>
                 </tr>
                 <!--end::Table row-->
             </thead>
             <!--end::Table head-->
             <!--begin::Table body-->
             <tbody class="text-gray-700 text-center">
                <?php 
                    $data = General::getClientStatus();

                    foreach ($data as $fee) {
                        $date = new DateTime($fee->created_at);

                        echo '<tr>';
                            echo $fee->reference_no === NULL ? '<td class="fw-bold">#' . $fee->sub_id . '</td>' : '<td class="fw-bold" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="top" title="' . $fee->sub_id . '" data-bs-delay-hide="1000">' . $fee->reference_no . '</td>';
                            echo '<td>' . $date->format('d/m/Y') . '</td>';
                            echo '<td>
                            <div class="" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $fee->provider . '">
                            <div class="symbol symbol-50px">
                            <img src="' . $fee->provider_img . '" alt="' . $fee->provider . '"  />
                            </div>
                            <span class="d-none">' . $fee->provider . '</span>
                            </td>';
                            echo '<td><span class="badge badge-secondary me-2">'. $fee->district .'</span></td>';
                            echo '<td>'. $fee->length .'</span></td>';
                            echo '<td><span class="badge badge-light-primary me-2">'. $fee->status .'</span></td>';
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
 <!--end::Products-->