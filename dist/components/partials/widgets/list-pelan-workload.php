<!--begin::Table widget 1-->
<div class="card card-flush card-xl-stretch mb-0">
    <!--begin::Header-->
    <div class="card-header border-0 pt-5">
        <!--begin::Title-->
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Bilangan Kerja Staf</span>
            <span class="text-muted mt-1 fw-semibold fs-7">Lihat Jumlah Kerja Staf</span>
        </h3>
        <!--end::Title-->
    </div>
    <!--end::Header-->
    <!--begin::Body-->
    <div class="card-body">
        <!--begin::Tab Content-->
        <div class="tab-content">
            <!--begin::Tap pane-->
            <div class="tab-pane fade show active">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 my-0 mb-5">
                        <!--begin::Table head-->
                        <thead>
                            <tr class="fw-bold text-muted fs-6">
                                <th class="p-0 min-w-150px d-block pt-0">Nama</th>
                                <th class="pe-0 text-center min-w-120px pt-0">Bilangan</th>
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