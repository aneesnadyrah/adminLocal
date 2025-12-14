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
$query = "SELECT sys_users.created_at, sys_users.id, sys_users.full_name, sys_users.telegram_id, sys_users.profile_pic, ls_user_role.role AS role
FROM sys_users LEFT JOIN ls_user_role ON ls_user_role.id = sys_users.role_id  WHERE activation = 0";
$result = pg_query($conn, $query);

// Check for errors in the query
if (!$result) {
    die("Error in query: " . pg_last_error());
}

// fetch the rows from the query result as an associative array
$rows = pg_fetch_all($result);

 if(pg_num_rows($result) > 0)  {
    foreach ($rows as $data) {

    }
}
?>


<!--begin::Slider Widget 7-->
<div id="recent-application" class="card card-flush carousel carousel-custom carousel-light-dots carousel-stretch slide h-xl-100" data-bs-ride="carousel" data-bs-interval="5000" style="background-color: #ffe896" data-theme="light">
    <!--begin::Header-->
    <div class="card-header align-items-center pt-7">
        <!--begin::Title-->
        <h4 class="card-label fw-bold text-gray-800 m-0">
            Recent Applications
        </h4>
        <!--end::Title-->
        <!--begin::Toolbar-->
        <div class="card-toolbar">
            <!--begin::Carousel Indicators-->
            <ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-dark">
                <li data-bs-target="#recent-application" data-bs-slide-to="0" class="active ms-1">
                </li>
                <li data-bs-target="#recent-application" data-bs-slide-to="1" class="ms-1"></li>
                <li data-bs-target="#recent-application" data-bs-slide-to="2" class="ms-1"></li>
                <li data-bs-target="#recent-application" data-bs-slide-to="3" class="ms-1"></li>
                <li data-bs-target="#recent-application" data-bs-slide-to="4" class="ms-1"></li>
            </ol>
            <!--end::Carousel Indicators-->
        </div>
        <!--end::Toolbar-->
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body pt-3">
        <!--begin::Carousel-->
        <div class="carousel-inner">
            <!--begin::Item-->

            <div class="carousel-item active show">
                <!--begin::Wrapper-->
                <div class="d-flex align-items-center">
                    <!--begin::Provider Img-->
                    <div class="w-80px flex-shrink-0 me-2">
                        <img class="min-h-auto" src="assets/media/features-logos/datatables.png" />
                    </div>
                    <!--end::Provider Img-->
                    <!--begin::Info-->
                    <div class="m-0">
                        <!--begin::Subtitle-->
                        <h5 class="fw-bold text-gray-800 mb-3">
                            PWR/A3/11/2022/0345
                        </h5>
                        <!--end::Subtitle-->
                        <!--begin::Items-->
                        <div class="d-flex d-grid gap-5">
                            <!--begin::Item-->
                            <div class="d-flex flex-column flex-shrink-0 me-4">
                                <!--begin::Section-->
                                <span class="d-flex align-items-center fs-7 fw-bold text-gray-600 mb-2">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen057.svg-->
                                    <span class="svg-icon svg-icon-6 svg-icon-gray-600 me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                            <path d="M11.9343 12.5657L9.53696 14.963C9.22669 15.2733 9.18488 15.7619 9.43792 16.1204C9.7616 16.5789 10.4211 16.6334 10.8156 16.2342L14.3054 12.7029C14.6903 12.3134 14.6903 11.6866 14.3054 11.2971L10.8156 7.76582C10.4211 7.3666 9.7616 7.42107 9.43792 7.87962C9.18488 8.23809 9.22669 8.72669 9.53696 9.03696L11.9343 11.4343C12.2467 11.7467 12.2467 12.2533 11.9343 12.5657Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->Tenaga Nasional
                                </span>
                                <!--end::Section-->
                                <!--begin::Section-->
                                <span class="d-flex align-items-center fs-7 fw-bold text-gray-600 mb-2">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen057.svg-->
                                    <span class="svg-icon svg-icon-6 svg-icon-gray-600 me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                            <path d="M11.9343 12.5657L9.53696 14.963C9.22669 15.2733 9.18488 15.7619 9.43792 16.1204C9.7616 16.5789 10.4211 16.6334 10.8156 16.2342L14.3054 12.7029C14.6903 12.3134 14.6903 11.6866 14.3054 11.2971L10.8156 7.76582C10.4211 7.3666 9.7616 7.42107 9.43792 7.87962C9.18488 8.23809 9.22669 8.72669 9.53696 9.03696L11.9343 11.4343C12.2467 11.7467 12.2467 12.2533 11.9343 12.5657Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->Kuantan
                                </span>
                                <!--end::Section-->
                                <!--begin::Section-->
                                <span class="d-flex align-items-center text-gray-600 fw-bold fs-7">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen057.svg-->
                                    <span class="svg-icon svg-icon-6 svg-icon-gray-600 me-2">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                            <path d="M11.9343 12.5657L9.53696 14.963C9.22669 15.2733 9.18488 15.7619 9.43792 16.1204C9.7616 16.5789 10.4211 16.6334 10.8156 16.2342L14.3054 12.7029C14.6903 12.3134 14.6903 11.6866 14.3054 11.2971L10.8156 7.76582C10.4211 7.3666 9.7616 7.42107 9.43792 7.87962C9.18488 8.23809 9.22669 8.72669 9.53696 9.03696L11.9343 11.4343C12.2467 11.7467 12.2467 12.2533 11.9343 12.5657Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->3,500 m
                                </span>
                                <!--end::Section-->
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Items-->
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Item-->
        </div>
        <!--end::Carousel-->
    </div>
    <!--end::Body-->
</div>
<!--end::Slider Widget 7-->