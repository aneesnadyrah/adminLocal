<div class="card card-flush pt-3 mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header d-flex justify-content-between align-items-center">
        <!--begin::Card title-->
        <div class="card-title">
            <h2 class="fw-bold">Butiran Jalan</h2>
        </div>
        <!--begin::Card title-->
        <?php if($_SESSION["roleId"] == 1) : ?>
        <div class="d-flex justify-content-center">
            <button class="btn btn-flex flex-center btn-light me-2" data-road="button" data-bs-toggle="modal"
                data-bs-target="#kt_roads_modal">
                <i class="fa-solid fa-pen-to-square fs-3"></i>
            </button>
        </div>
        <?php endif; ?>
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
                <tbody class="fw-semibold text-gray-800" data-road="table-body">
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
            <!--end::Table-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::jalan-->
</div>