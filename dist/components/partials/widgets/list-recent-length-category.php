<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Connect to the database
$conn = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpwd");
// Check for errors in the connection
if (!$conn) {
    die("Error in connection: " . pg_last_error());
}

$currentYear =  date('Y');

// Execute a SELECT query on the database
$query = "SELECT COUNT(id) AS total_application FROM flw_appl_entries WHERE EXTRACT(year FROM application_date) = '$currentYear'";
$result = pg_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Error in query: " . pg_last_error());
}

$rows = pg_fetch_assoc($result);

$total = $rows['total_application']
?>


<!--begin::Col-->
<!-- <div class=" mb-5 mb-xl-10"> -->
<div class="">
    <!--begin::Table widget 6-->
    <div class="card card-flush h-md-100">
        <!--begin::Header-->
        <div class="card-header pt-7">
            <!--begin::Title-->
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">Permohonan Terkini Mengikut Kategori Jarak</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6"><?php echo $total; ?> Jumlah Permohonan Tahun Ini</span>
            </h3>
            <!--end::Title-->
            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <!--begin::Nav-->
                <ul class="nav nav-pills nav-pills-custom">
                    <!--begin::Item-->
                    <li class="nav-item me-3 me-lg-6">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden active" data-bs-toggle="pill" href="#tab-A1">
                            <!--begin::Title-->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">A1</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3 me-lg-6">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-warning flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A2">
                            <!--begin::Title-->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">A2</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-warning"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="nav-item mb-3 me-3 me-lg-6">
                        <!--begin::Link-->
                        <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden" data-bs-toggle="pill" href="#tab-A3">
                            <!--begin::Title-->
                            <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-4">A3</span>
                            <!--end::Title-->
                            <!--begin::Bullet-->
                            <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                            <!--end::Bullet-->
                        </a>
                        <!--end::Link-->
                    </li>
                    <!--end::Item-->
                </ul>
                <!--end::Nav-->
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body">

            <!--begin::Tab Content-->
            <div class="tab-content">
                <!--begin::Tap pane-->
                <div class="tab-pane fade active show" id="tab-A1">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-450px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php

                                // Execute a SELECT query on the database
                                $query = "SELECT * FROM flw_appl_entries WHERE length_code = 'A1' ORDER BY id DESC LIMIT 5";
                                $result = pg_query($conn, $query);

                                // Check for errors in the query
                                if (!$result) {
                                    die("Error in query: " . pg_last_error());
                                }

                                // fetch the rows from the query result as an associative array
                                $rows = pg_fetch_all($result);

                                 if(pg_num_rows($result) > 0)  {
                                    foreach ($rows as $data) {
                                        echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['utility_provider'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['reference_no'].'</a>
                                                    <span class="text-muted fw-semibold d-block fs-7">'.$data['utility_provider'].'</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['application_length'].'</span>
                                            <span class="fw-semibold text-gray-400 d-block">meter</span>
                                        </td>
                                        <td>
                                            <a href="#" class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">'.$data['district'].'</a>
                                            <span class="text-muted fw-semibold d-block fs-7">'.$data['project_region'].'</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="/projects/details.php?sid=s?sid='.$data['system_id'].'" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </td>
                                    </tr>';
                                    }
                                } else {

                                }

                                ?>

                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="tab-A2">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-450px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                            <?php

                                // Execute a SELECT query on the database
                                $query = "SELECT * FROM flw_appl_entries WHERE length_code = 'A2' ORDER BY id DESC LIMIT 5";
                                $result = pg_query($conn, $query);

                                // Check for errors in the query
                                if (!$result) {
                                    die("Error in query: " . pg_last_error());
                                }

                                // fetch the rows from the query result as an associative array
                                $rows = pg_fetch_all($result);

                                 if(pg_num_rows($result) > 0)  {
                                    foreach ($rows as $data) {
                                        echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['utility_provider'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['reference_no'].'</a>
                                                    <span class="text-muted fw-semibold d-block fs-7">'.$data['utility_provider'].'</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['application_length'].'</span>
                                            <span class="fw-semibold text-gray-400 d-block">meter</span>
                                        </td>
                                        <td>
                                            <a href="#" class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">'.$data['district'].'</a>
                                            <span class="text-muted fw-semibold d-block fs-7">'.$data['project_region'].'</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="/projects/details.php?sid=s?sid='.$data['system_id'].'" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </td>
                                    </tr>';
                                    }
                                } else {

                                }

                                ?>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Tap pane-->
                <!--begin::Tap pane-->
                <div class="tab-pane fade" id="tab-A3">
                    <!--begin::Table container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table table-row-dashed align-middle gs-0 gy-4 my-0">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                    <th class="p-0 w-200px w-xxl-450px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 min-w-150px"></th>
                                    <th class="p-0 w-50px"></th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                            <?php

                                // Execute a SELECT query on the database
                                $query = "SELECT * FROM flw_appl_entries WHERE length_code = 'A3' ORDER BY id DESC LIMIT 5";
                                $result = pg_query($conn, $query);

                                // Check for errors in the query
                                if (!$result) {
                                    die("Error in query: " . pg_last_error());
                                }

                                // fetch the rows from the query result as an associative array
                                $rows = pg_fetch_all($result);

                                 if(pg_num_rows($result) > 0)  {
                                    foreach ($rows as $data) {
                                        echo '<tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    <img src="assets/media/provider/'. $data['utility_provider'] .'.webp" class="" alt="" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <a href="#" class="text-dark fw-bold text-hover-primary mb-1 fs-6">'.$data['reference_no'].'</a>
                                                    <span class="text-muted fw-semibold d-block fs-7">'.$data['utility_provider'].'</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-bold d-block mb-1 fs-6">'.$data['application_length'].'</span>
                                            <span class="fw-semibold text-gray-400 d-block">meter</span>
                                        </td>
                                        <td>
                                            <a href="#" class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">'.$data['district'].'</a>
                                            <span class="text-muted fw-semibold d-block fs-7">'.$data['project_region'].'</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="/projects/details.php?sid=s?sid='.$data['system_id'].'" class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary w-30px h-30px">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr001.svg-->
                                                <span class="svg-icon svg-icon-5 svg-icon-gray-700">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M14.4 11H3C2.4 11 2 11.4 2 12C2 12.6 2.4 13 3 13H14.4V11Z" fill="currentColor" />
                                                        <path opacity="0.3" d="M14.4 20V4L21.7 11.3C22.1 11.7 22.1 12.3 21.7 12.7L14.4 20Z" fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </td>
                                    </tr>';
                                    }
                                } else {

                                }

                                ?>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                    </div>
                    <!--end::Table-->
                </div>
                <!--end::Tap pane-->
            </div>
            <!--end::Tab Content-->
        </div>
        <!--end: Card Body-->
    </div>
    <!--end::Table widget 6-->
</div>
<!--end::Col-->