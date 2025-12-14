 <!--begin::Products-->
 <div class="card card-flush">
     <!--begin::Card header-->
     <div class="card-header align-items-center py-5 gap-2 gap-md-5">
         <!--begin::Card title-->
         <div class="card-title">
             <!--begin::Search-->
             <div class="d-flex align-items-center position-relative my-1">
                 <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                 <span class="svg-icon svg-icon-1 position-absolute ms-4">
                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                         <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                     </svg>
                 </span>
                 <!--end::Svg Icon-->
                 <input type="text" data-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Carian Permohonan..." />
             </div>
             <!--end::Search-->
         </div>
         <!--end::Card title-->
         <!--begin::Card toolbar-->
         <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
             <!--begin::Flatpickr-->
             <div class="input-group w-250px">
                 <input class="form-control form-control-solid rounded rounded-end-0" placeholder="Pilih Julat Tarikh" id="table-date-range" />
                 <button class="btn btn-icon btn-light" id="date-range-clear">
                     <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                     <span class="svg-icon svg-icon-2">
                         <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                             <rect opacity="0.5" x="7.05025" y="15.5356" width="12" height="2" rx="1" transform="rotate(-45 7.05025 15.5356)" fill="currentColor" />
                             <rect x="8.46447" y="7.05029" width="12" height="2" rx="1" transform="rotate(45 8.46447 7.05029)" fill="currentColor" />
                         </svg>
                     </span>
                     <!--end::Svg Icon-->
                 </button>
             </div>
             <!--end::Flatpickr-->
             <div class="w-100 mw-150px">
                 <!--begin::Select2-->
                 <?php

                    $hostName = $_SERVER['HTTP_HOST'];

                    // Define the API endpoint
                    $url = 'https://'.$hostName.'/api/lists/statusLetter';

                    // Make the API request
                    $response = file_get_contents($url, false);

                    // Check for errors
                    if ($response === false) {
                        // Handle errors
                        echo 'Error: ' . error_get_last()['message'];
                        exit;
                    }

                    // Decode the JSON response
                    $data = json_decode($response, true);

                    // Check for errors
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        // Handle errors
                        echo 'Error: ' . json_last_error_msg();
                        exit;
                    }

                    // Retrieve the status value from the URL query parameters
                    $status = isset($_GET['statId']) ? $_GET['statId'] : '';

                    echo '<select class="form-select form-select-solid" data-control="select2" data-placeholder="Status" data-table-filter="status">';
                    echo '<option value="all">Semua</option>';

                    // Loop through the data and build the select options
                    // foreach ($data as $row) {
                    //     echo '<option value="' . $row['id'] . '">' . $row['ms_status'] . '</option>';
                    // }
                    foreach ($data as $row) {
                        $selected = $row['ms_status'] == base64_decode($status) ? 'selected' : '';
                        echo '<option value="'. $row['ms_status'] .'"' . $selected . '>' . $row['ms_status'] . '</option>';
                    }

                    echo '</select>';

                    ?>
                 <!--end::Select2-->
             </div>
         </div>
         <!--end::Card toolbar-->
     </div>
     <!--end::Card header-->
     <!--begin::Card body-->
     <div class="card-body pt-0">
         <?php
            if ($_SESSION['roleId'] == 32 || $_SESSION['roleId'] == 33 || $_SESSION['roleId'] == 34 || $_SESSION['roleId'] == 35) {
                $tarikh = "Tarikh Mohon";
            } else if ($_SESSION['roleId'] == 41 || $_SESSION['roleId'] == 42 || $_SESSION['roleId'] == 43 || $_SESSION['roleId'] == 44) {
                $tarikh = "Tarikh Surat";
            } else {
                $tarikh = "Tarikh";
            }

            ?>
         <!--begin::Table-->
         <table class="table align-middle table-row-dashed fs-6 gy-5" id="list-letter">
             <!--begin::Table head-->
             <thead>
                 <!--begin::Table row-->
                 <tr class="text-center text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                     <th>No Rujukan</th>
                     <th>Penyedia Utiliti</th>
                     <th>Daerah</th>
                     <th>Jarak</th>
                     <th>Status</th>
                     <th ><?php echo $tarikh ?></th>
                     <th>Tindakan</th>
                 </tr>
                 <!--end::Table row-->
             </thead>
             <!--end::Table head-->
             <!--begin::Table body-->
             <tbody class="fw-semibold text-gray-700"></tbody>
             <!--end::Table body-->
         </table>
         <!--end::Table-->
     </div>
     <!--end::Card body-->
 </div>
 <!--end::Products-->