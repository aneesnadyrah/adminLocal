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

// Execute a SELECT query on the database
$query = "SELECT 
status_appl.last_status_date AS old_date, 
status_appl.updated_status_date AS new_date,
ls1.ms_status AS old_status,
ls2.ms_status AS new_status,
flw_appl_entries.reference_no
FROM status_appl
LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = status_appl.system_id
LEFT JOIN ls_appl_status ls1 ON ls1.id = status_appl.last_project_status 
LEFT JOIN ls_appl_status ls2 ON ls2.id = status_appl.project_status
LIMIT 10";
$result = pg_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Error in query: " . pg_last_error());
}

// fetch the rows from the query result as an associative array
$rows = pg_fetch_all($result);

?>



<!--begin::Col-->
<div class="col-xl-4 mb-xl-10">
    <!--begin::List widget 16-->
    <div class="card card-flush h-xl-100">
        <!--begin::Header-->
        <div class="card-header pt-7">
            <!--begin::Title-->
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">Log Perubahan Rekod</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6">Rekod Terkini Berubah</span>
            </h3>
            <!--end::Title-->
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div class="card-body pt-4 px-0">
            <!--begin::Tab Content-->
            <div class="px-9 hover-scroll-overlay-y pe-7 me-3 mb-2" style="height: 450px">
                <!--begin::Tab pane-->
                <div>
                    <!--begin::Item-->
                    <div class="m-0">
                        <?php
                        if(pg_num_rows($result) > 0)  {
                            foreach ($rows as $data) {
                                echo '<!--begin::Timeline-->
                                <div class="timeline ms-n1">
                                    <!--begin::Title-->
                                    <a href="#" class="fs-6 text-gray-800 fw-bold d-block text-hover-primary">'.$data['reference_no'].'</a>
                                    <!--end::Title-->
                                    <!--begin::Timeline item-->
                                    <div class="timeline-item align-items-center mb-4">
                                        <!--begin::Timeline line-->
                                        <div class="timeline-line w-20px mt-6 mb-n8"></div>
                                        <!--end::Timeline line-->
                                        <!--begin::Timeline icon-->
                                        <div class="timeline-icon pt-1" style="margin-left: 0.7px">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen015.svg-->
                                            <span class="svg-icon svg-icon-2 svg-icon-success">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor" />
                                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Timeline icon-->
                                        <!--begin::Timeline content-->
                                        <div class="timeline-content m-0">
                                            <!--begin::Label-->
                                            <span class="fs-8 fw-bolder text-success text-uppercase">'.$data['old_status'].'</span>
                                            <!--begin::Label-->
                                            <br />
                                            <!--begin::Title-->
                                            <span class="fw-semibold text-gray-400">';
                                            $date = new DateTime($data['new_date']);
                                            echo $date->format('Y-m-d');
                                            echo '</span>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Timeline content-->
                                    </div>
                                    <!--end::Timeline item-->
                                    <!--begin::Timeline item-->
                                    <div class="timeline-item align-items-center">
                                        <!--begin::Timeline line-->
                                        <div class="timeline-line w-20px"></div>
                                        <!--end::Timeline line-->
                                        <!--begin::Timeline icon-->
                                        <div class="timeline-icon pt-1" style="margin-left: 0.5px">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen018.svg-->
                                            <span class="svg-icon svg-icon-2 svg-icon-info">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10ZM6.39999 9.89999C6.99999 8.19999 8.40001 6.9 10.1 6.4C10.6 6.2 10.9 5.7 10.7 5.1C10.5 4.6 9.99999 4.3 9.39999 4.5C7.09999 5.3 5.29999 7 4.39999 9.2C4.19999 9.7 4.5 10.3 5 10.5C5.1 10.5 5.19999 10.6 5.39999 10.6C5.89999 10.5 6.19999 10.2 6.39999 9.89999ZM14.8 19.5C17 18.7 18.8 16.9 19.6 14.7C19.8 14.2 19.5 13.6 19 13.4C18.5 13.2 17.9 13.5 17.7 14C17.1 15.7 15.8 17 14.1 17.6C13.6 17.8 13.3 18.4 13.5 18.9C13.6 19.3 14 19.6 14.4 19.6C14.5 19.6 14.6 19.6 14.8 19.5Z" fill="currentColor" />
                                                    <path d="M16 12C16 14.2 14.2 16 12 16C9.8 16 8 14.2 8 12C8 9.8 9.8 8 12 8C14.2 8 16 9.8 16 12ZM12 10C10.9 10 10 10.9 10 12C10 13.1 10.9 14 12 14C13.1 14 14 13.1 14 12C14 10.9 13.1 10 12 10Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </div>
                                        <!--end::Timeline icon-->
                                        <!--begin::Timeline content-->
                                        <div class="timeline-content m-0">
                                            <!--begin::Label-->
                                            <span class="fs-8 fw-bolder text-info text-uppercase">'.$data['new_status'].'</span>
                                            <!--begin::Label-->
                                            <br />
                                            <!--begin::Title-->
                                            <span class="fw-semibold text-gray-400">';
                                            $date = new DateTime($data['new_date']);
                                            echo $date->format('Y-m-d');
                                            echo '</span>
                                            <!--end::Title-->
                                        </div>
                                        <!--end::Timeline content-->
                                    </div>
                                    <!--end::Timeline item-->
                                </div>
                                <!--end::Timeline-->
                                <!--begin::Separator-->
                                <div class="separator separator-dashed mt-5 mb-4"></div>
                                <!--end::Separator-->';
                            }
                        } else {

                            echo '<div class="d-flex justify-content-center mt-20 mb-4">
                            <img class="h-200px" src="assets/media/illustrations/empty/You’re Lost!.svg" />
                            </div>
                            <h4 class="text-center text-gray-500">Tiada rekod berubah</h4>';

                        }
                    ?>
                    </div>
                    <!--end::Item-->
                </div>
                <!--end::Tab pane-->
            </div>
            <!--end::Tab Content-->
        </div>
        <!--end: Card Body-->
    </div>
    <!--end::List widget 16-->
</div>
<!--end::Col-->