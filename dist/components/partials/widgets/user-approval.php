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
?>

<!--begin::Slider Widget 7-->
<div id="user-approval" class="card  card-flush carousel carousel-custom carousel-light-dots carousel-stretch slide h-xl-100" data-bs-ride="carousel" data-bs-interval="5000" style="background-color: #afe1a7" data-theme="light">
    <!--begin::Header-->
    <div class="card-header align-items-center pt-7">
        <!--begin::Title-->
        <h4 class="card-label fw-bold text-gray-700 m-0">
            Pengesahan Akaun
        </h4>
        <!--end::Title-->
        <!--begin::Toolbar-->
        <div class="card-toolbar">
            <!--begin::Carousel Indicators-->
            <ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-success">

                <?php
                if(pg_num_rows($result) > 0)  {
                    foreach ($rows as $index => $row) {
                        if ($index == 0) {
                            echo '<li data-bs-target="#user-approval" data-bs-slide-to="'.$index.'" class="active ms-1"></li>';
                        } else {
                            echo '<li data-bs-target="#user-approval" data-bs-slide-to="'.$index.'" class="ms-1"></li>';
                        }
                    }
                }
                ?>

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
            <?php

            // Return the user information to the client
            if(pg_num_rows($result) > 0)  {
                foreach ($rows as $index => $row) {
                    if ($index == 0) {
                        echo '<!--begin::Item-->
                        <div class="carousel-item show active">';

                    } else {
                        echo '<!--begin::Item-->
                        <div class="carousel-item">';
                    }

                    echo '<!--begin::Wrapper-->
                        <div class="d-flex align-items-center justify-content-between">
                                <!--begin::User Img-->
                                <div class="w-80px flex-shrink-0 me-2">
                                    <div class="symbol symbol-75px symbol-circle">
                                        <img src="assets/media/avatars/blank.png" />
                                    </div>
                                </div>

                                <!--end::User Img-->
                                <!--begin::Info-->
                                <div class="m-0">
                                    <!--begin::Subtitle-->
                                    <h5 class="fw-bold text-gray-700 mb-3">
                                    '.$row['full_name'].'
                                    </h5>
                                    <!--end::Subtitle-->
                                    <!--begin::Items-->
                                    <div class="d-flex d-grid gap-5">
                                        <!--begin::Item-->
                                        <div class="d-flex flex-column flex-shrink-0 me-4">
                                            <!--begin::Section-->
                                            <span class="d-flex align-items-center fs-7 text-gray-600 mb-2">
                                            <i class="fa-brands fa-telegram me-3 fs-3"></i>'.$row['telegram_id'].'
                                            </span>
                                            <!--end::Section-->
                                            <!--begin::Section-->
                                            <span class="d-flex align-items-center fs-7 text-gray-600 mb-2">
                                                <i class="fad fa-circle-user me-3 fs-3"></i>'.$row['role'].'
                                            </span>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Info-->
                                <!--begin::Action-->
                                <div class="d-flex d-grid gap-18">
                                    <a href="javascript:void(0)" class="btn btn-icon btn-circle btn-lg btn-success" data-bs-toggle="modal" data-bs-target="#approve-user'.$row['id'].'"><i class="fa-duotone fa-badge-check fs-2"></i></a>
                                </div>
                                <!--end::Action-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Item-->';

                }
            } else {
                echo
                '<!--begin::Item-->
                <div class="carousel-item active show">
                    <div class="d-flex justify-content-center mb-4">
                    <img class="h-85px" src="assets/media/illustrations/empty/chill.svg" />
                    </div>
                    <h4 class="text-center text-gray-700">Tiada Pendaftaran Pengguna Baru</h4>
                </div>
                <!--end::Item-->';
            }

            ?>
        </div>
        <!--end::Carousel-->
    </div>
    <!--end::Body-->
</div>
<!--end::Slider Widget 7-->

