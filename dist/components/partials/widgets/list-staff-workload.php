<!--begin::Table widget 1-->
<div class="card card-flush card-xl-stretch mb-0">
    <!--begin::Header-->
    <div class="card-header border-0 pt-5">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Aktiviti Hari Ini</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Lihat Jumlah Kerja & Status Staf</span>
        </h3>
        <!--end::Title-->
        <!--begin::Toolbar-->
        <div class="card-toolbar">
            <!--begin::Nav-->
            <ul class="nav nav-pills nav-pills-custom">
                <!--begin::Item-->
                <li class="nav-item">
                    <!--begin::Link-->
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-primary flex-column overflow-hidden active" data-bs-toggle="pill" href="#ukur">
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-2">Ukur</span>
                        <!--end::Title-->
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-primary"></span>
                        <!--end::Bullet-->
                    </a>
                    <!--end::Link-->
                </li>
                <!--end::Item-->
                <!--begin::Item-->
                <li class="nav-item">
                    <!--begin::Link-->
                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-warning flex-column overflow-hidden" data-bs-toggle="pill" href="#pelan">
                        <!--begin::Title-->
                        <span class="nav-text text-gray-800 fw-bold fs-6 lh-1 my-2">Pelan</span>
                        <!--end::Title-->
                        <!--begin::Bullet-->
                        <span class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-warning"></span>
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
        <div class="tab-content hover-scroll-overlay-y pe-6 me-n6" style="height: 400px">
            <!--begin::Tap pane-->
            <div class="tab-pane fade show active" id="ukur">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 my-0">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bold text-muted fs-6">
                                <th class="p-0 min-w-130px d-block pt-3">Kumpulan</th>
                                <th class="text-end min-w-130px pt-3">Bil. Ahli</th>
                                <th class="pe-0 text-center min-w-130px pt-3">Status</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                            <?php
                            // Call the function to get the staff overview data
                            $staffOverviewData = Survey::getStaffOverview();

                            // Loop through the data and generate the HTML table rows
                            foreach ($staffOverviewData as $teamData) {
                                $teamName = $teamData['team_name'];
                                $teamProfile = $teamData['profile_picture'];
                                $totalMembers = $teamData['total_members'];
                                $masaMasuk = $teamData['masa_masuk'];
                                $masaKeluar = $teamData['masa_keluar'];
                                $status = $teamData['status'];

                                echo '<tr>';
                                echo '<td>';
                                echo '<div class="symbol symbol-45px me-3">';
                                echo '<img src="assets/media/avatars/' . $teamProfile . '.jpg" class="" alt="" />';
                                echo '</div>';
                                echo '<a href="#" class="fw-semibold text-muted text-hover-primary mb-1 fs-6">' . $teamName . '</a>';
                                echo '</td>';
                                echo '<td class="text-center">';
                                echo '<span class="badge badge-circle badge-outline badge-success">' . $totalMembers . '</span>';
                                echo '</td>';
                                echo '<td class="text-center">';
                                
                                // Set badge color based on status
                                $badgeClass = '';
                                switch ($status) {
                                    case 'Di tapak':
                                        $badgeClass = 'badge-light-primary';
                                        break;
                                    case 'Di pejabat':
                                    $badgeClass = 'badge-light-info';
                                    break;
                                    case 'Telah Dilantik':
                                        $badgeClass = 'badge-light-success';
                                        break;
                                    case 'Tidak dilantik':
                                        $badgeClass = 'badge-light-danger';
                                        break;
                                    default:
                                        $badgeClass = 'badge-light-secondary'; // Default color for unknown statuses
                                        break;
                                }
                                
                                echo '<span class="badge ' . $badgeClass . ' fs-7 fw-bold">' . $status . '</span>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--end::Tap pane-->
            <!--begin::Tap pane-->
            <div class="tab-pane fade" id="pelan">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 my-0">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bold text-muted fs-6">
                                <th class="p-0 min-w-150px d-block pt-3">Nama</th>
                                <th class="pe-0 text-center min-w-120px pt-3">Bilangan</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                            <?php
                            $staffWorkloadData = Survey::getStaffWorkloadData();

                            foreach ($staffWorkloadData as $staffData) {
                                $username = $staffData['username'];
                                $firstName = $staffData['firstName'];
                                $profilePic = $staffData['profilePic'];
                                $totalWorkload = $staffData['totalWorkload'];
                            ?>
                            <tr>
                                <td>
                                    <div class="symbol symbol-45px me-3">
                                        <img src="assets/media/avatars/<?= $profilePic ?>.jpg" class="" alt="" />
                                    </div>
                                    <a href="#" class="fw-semibold text-muted text-hover-primary mb-1 fs-6"><?= $firstName ?></a>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if ($totalWorkload == 0) {
                                        echo '<span class="badge badge-light-danger badge-circle badge-lg">' . $totalWorkload . '</span>';
                                    } elseif ($totalWorkload == 1) {
                                        echo '<span class="badge badge-light-primary badge-circle badge-lg">' . $totalWorkload . '</span>';
                                    } else {
                                        echo '<span class="badge badge-light-success badge-circle badge-lg">' . $totalWorkload . '</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--end::Tap pane-->
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end: Card Body-->
</div>
<!--end::Table widget 1-->
