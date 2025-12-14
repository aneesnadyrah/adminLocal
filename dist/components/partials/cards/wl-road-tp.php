<?php 
$systemId = $_GET['sid'];
$dataRoad = wayleaveAmendments::getDataRoadTP($systemId);
?>

<!--begin::road info-->
<div class="accordion pt-1 mb-3 mb-xl-8" id="accordionExample">
    <div class="card">
        <div class="card-header pt-6" id="headingOne" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            <!-- <div class="mt-2"> -->
            <h2 class="fw-bold">Butiran Jalan Awal Pemohon</h2>
            <span class="icon">&#9654;</span> <!-- Arrow icon -->
            <!-- </div> -->
        </div>
        <div class="separator separator-dashed"></div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
            <div class="card-body">
                <!--begin::Table-->
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr
                                class="border-bottom border-gray-200 text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-150px">Nama Jalan</th>
                                <th class="min-w-125px">Kaedah Korekan</th>
                                <th class="min-w-125px">Koordinat Mula</th>
                                <th class="min-w-125px">Koordinat Akhir</th>
                                <th class="min-w-125px">Jarak (m)</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-800">
                            <?php
                            foreach ($data as $road) {
                                ?>
                                <tr>
                                    <td>
                                        <label class="w-150px">
                                            <?= $road->name; ?>
                                        </label>
                                    </td>
                                    <td>
                                        <?php
                                        $method = explode(",", $road->methods);
                                        $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                        shuffle($colors);
                                        foreach ($method as $row) {
                                            echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= $road->start_coordinates ?>
                                    </td>
                                    <td>
                                        <?= $road->end_coordinates ?>
                                    </td>
                                    <td>
                                        <?= $road->distance ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                </div>
                <!--end::Table-->
            </div>
        </div>
    </div>
</div>
<!--end::road info-->

<div class="card card-flush pt-3 mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Card title-->
        <div class="card-title">
            <h2 class="fw-bold">Butiran Jalan Selepas Pindaan</h2>
        </div>
        <!--begin::Card title-->
    </div>
    <!--end::Card header-->
    <div class="separator separator-dashed"></div>
    <!--begin::Card body-->
    <div class="card-body">
        <!--begin::Product table-->
        <div class="table-responsive">
            <!--begin::Table-->
            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr
                        class="border-bottom border-gray-200 text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-150px">Nama Jalan</th>
                        <th class="min-w-125px">Kaedah Korekan</th>
                        <th class="min-w-125px">Koordinat Mula</th>
                        <th class="min-w-125px">Koordinat Akhir</th>
                        <th class="min-w-125px">Jarak (m)</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-semibold text-gray-800">
                    <?php
                    foreach ($dataRoad as $road) {
                        ?>
                        <tr>
                            <td>
                                <label class="w-150px">
                                    <?= $road->name; ?>
                                </label>
                            </td>
                            <td>
                                <?php
                                $method = explode(",", $road->methods);
                                $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                shuffle($colors);
                                foreach ($method as $row) {
                                    echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?= $road->start_coordinates ?>
                            </td>
                            <td>
                                <?= $road->end_coordinates ?>
                            </td>
                            <td>
                                <?= $road->distance ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::jalan-->
</div>



