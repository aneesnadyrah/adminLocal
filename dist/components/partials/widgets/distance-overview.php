<!-- <div class="container"> -->
<!--begin::Row-->
<div class="row g-xl-5">
    <!--begin::Col-->
    <div class="d-flex flex-wrap col-12  col-xl-12 p-0">
        <!--begin::Card widget 2-->
        <div class="col-6 col-xl-12 card bg-transparent shadow-none hover-elevate-up">
                <div class="card-body d-flex flex-stack flex-grow-1 p-6 py-5">
                    <span class="symbol symbol-50px me-1">
                        <span class="symbol-label bg-light-info">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2x svg-icon-info">
                            <i class="fad fa-files text-info" style="font-size:25px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <div class="d-flex flex-column text-end">
                         <?php
                            $metrics = Survey::getMetrics(); // Assuming $data contains the correct data
                            echo '<span class="text-dark fw-bold fs-3">' . $metrics['jarak_mohon'] . '</span>';
                        ?>
                        <span class="text-muted fw-semibold mt-1">Jarak Mohon</span>
                    </div>
                </div>
        </div>
        <!--end::Card widget 2-->
        <!--begin::Card widget 2-->
        <div class="col-6 col-xl-12  card bg-transparent shadow-none hover-elevate-up">
                <div class="card-body d-flex flex-stack flex-grow-1 p-6 py-5">
                    <span class="symbol symbol-50px me-1">
                        <span class="symbol-label bg-light-success">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2x svg-icon-success">
                            <i class="fad fa-angle text-success" style="font-size:25px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <div class="d-flex flex-column text-end">
                         <?php
                            $metrics = Survey::getMetrics(); // Assuming $data contains the correct data
                            echo '<span class="text-dark fw-bold fs-3">' . $metrics['jarak_ukur'] . '</span>';
                        ?>
                        <span class="text-muted fw-semibold mt-2">Jarak Ukur</span>
                    </div>
                </div>
        </div>
        <!--end::Card widget 2-->
        <!--begin::Card widget 2-->
        <div class="col-6 col-xl-12  card bg-transparent shadow-none hover-elevate-up">
                <div class="card-body d-flex flex-stack flex-grow-1 p-6 py-5">
                    <span class="symbol symbol-50px me-1">
                        <span class="symbol-label bg-light-warning">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2x svg-icon-warning">
                            <i class="fad fa-file-plus-minus text-warning" style="font-size:25px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <div class="d-flex flex-column text-end">
                         <?php
                            $metrics = Survey::getMetrics(); // Assuming $data contains the correct data
                            echo '<span class="text-dark fw-bold fs-3">' . $metrics['beza_jarak'] . '</span>';
                        ?>
                        <span class="text-muted fw-semibold mt-1">Beza Jarak</span>
                    </div>
                </div>
        </div>
        <!--end::Card widget 2-->
        <!--begin::Card widget 2-->
        <div class="col-6 col-xl-12  card bg-transparent shadow-none hover-elevate-up">
                <div class="card-body d-flex flex-stack flex-grow-1 p-6 py-5">
                    <span class="symbol symbol-50px me-1">
                        <span class="symbol-label bg-light-danger">
                            <!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
                            <span class="svg-icon svg-icon-2x svg-icon-danger">
                            <i class="fad fa-globe text-danger" style="font-size:25px"></i>
                            </span>
                            <!--end::Svg Icon-->
                        </span>
                    </span>
                    <div class="d-flex flex-column text-end">
                         <?php
                            $metrics = Survey::getMetrics(); // Assuming $data contains the correct data
                            echo '<span class="text-dark fw-bold fs-3">' . $metrics['jarak_gis'] . '</span>';
                        ?>
                        <span class="text-muted fw-semibold mt-1">Jarak GIS</span>
                    </div>
                </div>
        </div>
        <!--end::Card widget 2-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->