<?php 
$system_id = $_GET['sid']; 
$project = new ProjectDetails();
$data = $project->getProjectDetails($system_id);
?>

<!--begin::Card-->
<div class="card mb-6 mb-xl-8">
    <!--begin::Card Body-->
    <div class="card-body pt-9 pb-0">
        <?php $provider = General::getProvider($data->provider_id); ?>

        <!--begin::Details-->
        <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
            <!--begin::Image-->
            <div class="d-flex flex-center flex-shrink-0 bg-light rounded w-150px h-150px w-lg-150px me-7 mb-4">
                <img class="mw-100px mw-lg-150px" src="<?php echo $provider->logo ?>" alt="<?php echo $provider->name ?>" />
            </div>
            <!--end::Image-->

            <div class="flex-grow-1">
                <!--begin::Head-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::Details-->
                    <div class="d-flex flex-column">
                        <!--begin::Status-->
                        <div class="d-flex flex-wrap align-items-center mb-1">
                            <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3"><?php echo $data->reference_no ?></a>
                            <span class="badge badge-light-<?php echo $data->status_color ?> me-auto"><?php echo $data->status ?></span>
                        </div>
                        <!--end::Status-->
                        <!--start::District-->
                        <div class="d-flex align-items-center mb-1">
                            <?php $dist = explode(",", $data->districts);
                            $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                            shuffle($colors);
                            foreach ($dist as $row) {
                                echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                            }
                            ?>
                        </div>
                        <!--end::District-->
                        <!--begin::Description-->
                        <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400"><?php echo $data->project_title ?></div>
                        <!--end::Description-->
                    </div>
                    <!--end::Details-->
                    <!--begin::Actions-->
                    <!-- <div class="d-flex mb-4">
                        <button type="button" class="btn btn-icon btn-sm btn-bg-light btn-active-color-warning me-3"><i class="fad fa-star"></i></button>
                        <div class="me-0">
                            <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="bi bi-three-dots fs-3"></i>
                            </button>
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link  px-3"><i class="menu-icon fad fa-edit me-2 fs-4"></i>Kemaskini</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3"><i class=" menu-icon fad fa-file-chart-pie me-2 fs-4"></i>Ringkasan</a>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!--end::Actions-->
                </div>
                <!--end::Head-->
                <!--begin::Info-->
                <div class="d-flex flex-wrap justify-content-start">
                    <!--begin::Stats-->
                    <div class="d-flex flex-wrap">
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo !empty($provider->name) ? $provider->name : '-'; ?></div>
                            </div>
                            <!--end::Number-->
                            <!--begin::Label-->
                            <div class="fw-semibold fs-6 text-gray-400">Penyedia Utiliti</div>
                            <!--end::Label-->
                        </div>
                        <!--end::Stat-->
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo !empty($data->submitted_date) ? (new DateTime($data->submitted_date))->format('d M Y') : '-'; ?></div>
                            </div>
                            <!--end::Number-->
                            <!--begin::Label-->
                            <div class="fw-semibold fs-6 text-gray-400">Tarikh Permohonan</div>  
                            <!--end::Label-->
                        </div>
                        <!--end::Stat-->
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo !empty($data->application_length) ? $data->application_length : '-'; ?></div>
                            </div>
                            <!--end::Number-->
                            <!--begin::Label-->
                            <div class="fw-semibold fs-6 text-gray-400">Jarak Permohonan</div>
                            <!--end::Label-->
                        </div>
                        <!--end::Stat-->
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo !empty($data->sitevisit_length) ? $data->sitevisit_length : '-'; ?></div>
                            </div>
                            <!--end::Number-->
                            <!--begin::Label-->
                            <div class="fw-semibold fs-6 text-gray-400">Jarak Site Visit</div>
                            <!--end::Label-->
                        </div>
                        <!--end::Stat-->
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold"><?php echo !empty($data->sitevisit_length) ? $data->sitevisit_length : '-'; ?></div>
                            </div>
                            <!--end::Number-->
                            <!--begin::Label-->
                            <div class="fw-semibold fs-6 text-gray-400">Jarak Akhir</div>
                            <!--end::Label-->
                        </div>
                        <!--end::Stat-->
                        <!--begin::Stat-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <!--begin::Number-->
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold">RM - </div>
                            </div>
                            <!--end::Number-->
                            <!--begin::Label-->
                            <div class="fw-semibold fs-6 text-gray-400">Wang Cagaran</div>
                            <!--end::Label-->
                        </div>
                        <!--end::Stat-->

                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Details-->
    </div>
    <!--end::Card Body-->
</div>
<!--end::Card-->

<div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Title-->
        <div class="card-title">
            <h3>Muat Naik Dokumen PIL</h3>
        </div>
        <!--end::Title-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body">
        <!--begin::row-->
        <div class="row gx-9 gy-6">
            <!--begin::Col-->
            <div class="col-xl-3">
                <h4 class="mb-3">Pelan Izin Lalu</h4>

                <div class="fv-row mb-5">
                    <div data-upload="PIL-<?php echo $system_id; ?>-24" class="dropzone border-primary bg-light-primary dz-clickable p-5" data-type=".pdf">
                        <div class="dz-message needsclick">
                            <i class="fad fa-map-location-dot text-primary fs-3x"></i>
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muat naik PIL</h3>
                                <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen PIL</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted fs-7 required mb-3"><em>Pastikan dokumen telah disatukan dalam bentuk format .pdf</em></div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-3">
                <h4 class="mb-3">Data Lot</h4>

                <div class="fv-row mb-5">
                    <div data-upload="DLOT-<?php echo $system_id; ?>-24" class="dropzone border-primary bg-light-primary dz-clickable p-5" data-type=".zip">
                        <div class="dz-message needsclick">
                            <i class="fad fa-file-arrow-up text-primary fs-3x"></i>
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muat naik dokumen</h3>
                                <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Data Lot</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted fs-7 required mb-3"><em>Pastikan dokumen telah disatukan dalam bentuk format .zip</em></div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-3">
                <h4 class="mb-3">LINE</h4>

                <div class="fv-row mb-5">
                    <div data-upload="LINE-<?php echo $system_id; ?>-24" class="dropzone border-primary bg-light-primary dz-clickable p-5" data-type=".gpkg">
                        <!-- <input type="file" name="file" id="file" accept=".gpkg" onchange="loadGeoPackage(this.files)" /> -->
                        <div class="dz-message needsclick">
                            <i class="fad fa-route text-primary fs-3x"></i>
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muat naik LINE</h3>
                                <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Fail<em>'LINE.gpkg'</em> sahaja</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted fs-7 required mb-3"><em>Pastikan dokumen telah disatukan dalam bentuk format .gpkg</em></div>
                </div>
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-3">
                <h4 class="mb-3">POINT</h4>

                <div class="fv-row mb-5">
                    <div data-upload="POINT-<?php echo $system_id; ?>-24" class="dropzone border-primary bg-light-primary dz-clickable p-5" data-type=".gpkg">
                        <div class="dz-message needsclick">
                            <i class="fad fa-location-dot text-primary fs-3x"></i>
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muat naik POINT</h3>
                                <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Fail<em>'POINT.gpkg'</em> sahaja</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-muted fs-7 required mb-3"><em>Pastikan dokumen telah disatukan dalam bentuk format .gpkg</em></div>
                </div>
            </div>
            <!--end::Col-->
            <!-- <div id="feature-tables"></div> -->
        </div>
        <!--end::row-->

        <!--begin::Road Info-->
        <div class="d-flex flex-end">
            <button id="road-info" class="btn btn-primary flex-shrink-0" data-road="roadInfo-<?php echo $system_id?>" style="display: none;">
            <!-- <button id="road-info" class="btn btn-primary flex-shrink-0" data-road="roadInfo-<?php echo $system_id?>"> -->
                <span class="indicator-label">Butiran Laluan</span>
                <span class="indicator-progress">
                    <span data-kt-translate="general-progress">Sila Tunggu...</span>
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
        <!--end::Road Info-->
    </div>
    <!--end::Card body-->
</div>

<div class="modal fade" id="gis-pil-route-modal" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Butiran Laluan</h2>
                <!--end::Modal title-->
            </div>
            <!--end::Modal header-->

            <!--start::Form-->
            <form novalidate="novalidate" id="form-gis-plan-modal">
                <!--begin::Modal body-->
                <div class="modal-body py-lg-10 px-lg-10">

                    <div class="h-350px w-100 m-0 p-0 mb-5 rounded rounded-3 overflow-hidden" id="map-modal"></div>

                    <!--begin::Repeater-->
                    <div id="gis_route_repeater">
                        <!--begin::Form group-->
                        <div data-repeater-list="gis_route_list">
                            <div data-repeater-item>
                                <div class="border border-gray-300 rounded p-5 my-2">
                                    <div class="form-group row fv-row">
                                        <div class="col-md-12 row mb-2">

                                            <div class="col-md-5">
                                                <label class="form-label">Koordinat Awal:</label>
                                                <input type="text" name="coor_start" data-route-list="coor-start"
                                                    class="form-control mb-2 mb-md-0"
                                                    placeholder="5.33010, 103.14785" />
                                            </div>

                                            <div class="col-md-6 pe-7">
                                                <label class="form-label">Jalan Terlibat:</label>
                                                <input type="text" name="road_name" class="form-control mb-2 mb-md-0"
                                                    placeholder="Sila isi nama jalan" />
                                                    <input type="text" name="road-id" class="form-control d-none" data-report-repeater="road-id" />
                                            </div>
                                            <div class="col-md-1 ms-n4">
                                                <button type="button" data-repeater-delete
                                                    class="btn btn-sm btn-light-danger py-4 mt-8"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Klik untuk hapuskan daripada senarai.">
                                                    <i class="fad fa-trash fs-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-12 row">
                                            <div class="col-md-5">
                                                <label class="form-label">Koordinat Akhir:</label>
                                                <input type="text" name="coor_end" data-route-list="coor-end"
                                                    class="form-control mb-2 mb-md-0"
                                                    placeholder="5.33010, 103.14785" />
                                            </div>





                                            <!-- <div class="col-md-3">
                                                <label class="form-label">Kaedah:</label> -->
                                            <!--begin::Select2-->
                                            <!-- <select class="form-select mb-2" data-kt-repeater="select2"
                                                    data-placeholder="Sila pilih kaedah" tabindex="0" aria-hidden="true"
                                                    name="method">
                                                    <option></option>
                                                    <?php
                                                    foreach (General::selection('ls_work_methods') as $row) {
                                                        $name = $row['name'];
                                                        $id = $row['id'];

                                                        echo '<option value="' . $id . '">' . $name . '</option>';
                                                    }
                                                    ?>
                                                </select> -->
                                            <!--end::Select2-->
                                            <!-- </div> -->


                                            <div class="col-md-4">
                                                <label class="form-label">Pihak Berkuasa Terlibat:</label>
                                                <!--begin::Select2-->
                                                <select class="form-select mb-2" data-kt-repeater="select2"
                                                    data-placeholder="Sila pilih Pihak Berkuasa terlibat" tabindex="0"
                                                    aria-hidden="true" name="authority">
                                                    <option></option>
                                                    <?php
                                                    foreach (General::authoritySelection($system->App->state) as $row) {
                                                        $sort_name = $row['sort_name'];
                                                        $id = $row['id'];

                                                        echo '<option value="' . $id . '">' . $sort_name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <!--end::Select2-->
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Daerah Terlibat:</label>
                                                <!--begin::Select2-->
                                                <select class="form-select mb-2" data-kt-repeater="select2"
                                                    data-placeholder="Sila pilih Daerah" tabindex="0" aria-hidden="true"
                                                    name="district">
                                                    <option></option>
                                                    <?php
                                                    // FIXME: changing districts by using UPI standard
                                                    // REVIEW: $state must be declare by apps

                                                    foreach (General::UPIselection($system->App->state) as $row) {
                                                        $name = $row['district_name'];
                                                        $id = $row['district_code'];

                                                        echo '<option value="' . $id . '">' . $name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <!--end::Select2-->
                                            </div>
                                            <input type="text" name="route-length" data-route-list="length"
                                                class="form-control mb-2 mb-md-0"
                                                placeholder="Sila masukkan jarak laluan." value="0" hidden />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Form group-->

                        <!--begin::Form group-->
                        <div class="form-group row mt-5 d-flex justify-content-between">
                            <div class="col-md-3">
                                <button type="button" data-repeater-create class="btn btn-light-primary md-8 mb-8">
                                    <i class="fad fa-plus"></i>Tambah Jalan
                                </button>
                            </div>
                        </div>
                        <!--end::Form group-->
                    </div>
                    <!--end::Repeater-->
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="form-label">Catatan:</label>
                            <textarea class="form-control" placeholder="Catatan" name="notes"></textarea>
                        </div>
                    </div>

                    <input type="text" name="sysId" value="<?php echo $system_id ?>" hidden />
                    <!--begin::Submit-->
                    <div class="d-flex flex-end mt-5">
                        <button id="submit-gis-pil" class="btn btn-primary me-2 flex-shrink-0">
                            <!--begin::Indicator label-->
                            <span class="indicator-label">Hantar</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">
                                <span data-kt-translate="general-progress">Sila Tunggu...</span>
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                            <!--end::Indicator progress-->
                        </button>
                    </div>
                    <!--end::Submit-->

                </div>
                <!--end::Modal body-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>