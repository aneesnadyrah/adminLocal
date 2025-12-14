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
                <button class="btn btn-icon btn-light" id="date-range-clear">
                    <i class="fad fa-xmark fs-4"></i>
                </button>
            </div>
            <!--end::Flatpickr-->
            <div class="w-100 mw-150px">
                <!--begin::Select2-->
                <?php
                // Define the API endpoint
                $url = 'https://' . $_SERVER['HTTP_HOST'] . '/api/status/lists/status';

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
                // echo $status;
                
                echo '<select class="form-select form-select-solid" data-control="select2" data-placeholder="Status" data-table-filter="status">';
                echo '<option value="all">Semua</option>';

                // Loop through the data and build the select options
                // foreach ($data as $row) {
                //     echo '<option value="' . $row['flow_name'] . '">' . $row['flow_name'] . '</option>';
                // }
                foreach ($data as $row) {
                    $selected = $row['flow_name'] == base64_decode($status) ? 'selected' : '';
                    echo '<option value="' . $row['flow_name'] . '"' . $selected . '>' . $row['flow_name'] . '</option>';
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
        <!--begin::Table-->
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="project-task">
            <!--begin::Table head-->
            <thead>
                <!--begin::Table row-->
                <tr class="text-center text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                    <th>No Rujukan</th>
                    <th class="min-w-150px">Penyedia Utiliti</th>
                    <th>Daerah</th>
                    <th>Jarak</th>
                    <th>Status</th>
                    <th></th>
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