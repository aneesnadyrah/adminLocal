<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Sidebar-->
    <div class="flex-column flex-lg-row-auto w-100 mw-lg-350px mb-10 mb-lg-0">
        <!--begin::Contacts-->
        <div class="card card-flush">

            <!--begin::Card body-->
            <div class="card-body pt-5" id="upload-block">
                <!--begin::Input group-->
                <div class="fv-row mt-5">
                    <div class="dropzone dz-clickable" id="dropzone">
                        <input type="file" name="file" id="file" accept=".gpkg" onchange="loadGeoPackage(this.files)" />
                        <div class="dz-message needsclick">
                            <!--begin::Icon-->
                            <i class="fad fa-route text-primary fs-3x"></i>
                            <!--end::Icon-->

                            <!--begin::Info-->
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Klik untuk memuat naik fail surihan</h3>
                                <span class="fs-7 fw-semibold text-primary opacity-75">Sila pilih fail
                                    <em>'LINE.gpkg'</em> sahaja</span>
                            </div>
                            <!--end::Info-->
                        </div>
                    </div>

                    <div id="dropzoneIndicator" class="d-none mt-n3 fw-bold">
                        Sila tunggu...
                        <span class="spinner-border spinner-border-sm align-middle ms-2 "></span>
                    </div>
                </div>




                <div id="information">
                    <div class="mt-5" data-separator="view"></div>
                    <div class="tableGroup">
                        <div id="feature-tables"></div>
                    </div>
                </div>
                <div class="d-none" data-form="metadata" novalidate="novalidate">
                    <form novalidate="novalidate" id="form-gis-plan">
                        <div class="row fv-row mt-5">
                            <div class="col-6">
                                <!--begin::Input group-->
                                <!--begin::Label-->
                                <label class="form-label">Jarak Surihan</label>
                                <!--end::Label-->
                                <input class="form-control form-control-solid" name="gpkg-length" id="gpkg-length"
                                    placeholder="Jarak Surihan" readonly />
                                <!--end::Input group-->
                            </div>
                            <div class="col-6">
                                <!--begin::Input group-->
                                <!--begin::Label-->
                                <label class="form-label">Pindaan</label>
                                <!--end::Label-->
                                <!--begin::Select2-->
                                <input class="form-control form-control-solid" id="data-revision"
                                    name="data-revision" />
                                <!--end::Select2-->
                                <!--end::Input group-->
                            </div>
                        </div>


                        <div class="row fv-row mt-5">
                            <div class="col-md-12">
                                <label class="form-label">Kepadatan Utiliti:</label>
                                <!--begin::Select2-->
                                <select class="form-select mb-2" data-control="select2"
                                    data-placeholder="Sila pilih Kepadatan" tabindex="0" aria-hidden="true"
                                    name="density" id="density-select">
                                    <option></option>
                                    <option value="1">Rendah</option>
                                    <option value="2">Sederhana</option>
                                    <option value="3">Tinggi</option>
                                </select>
                                <!--end::Select2-->
                            </div>

                        </div>

                        <input type="text" name="sysId" value="<?php echo $systemId ?>" hidden />
                        <!--begin::Actions-->
                        <div class="d-flex flex-end mt-5">
                            <!-- begin::modalButton -->
                            <button id="route-details-btn" class="btn btn-primary flex-shrink-0">
                                <!--begin::Indicator label-->
                                <span class="indicator-label">Butiran Laluan</span>
                                <!--end::Indicator label-->
                            </button>
                            <!-- end::modalButton -->
                        </div>
                    </form>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::card body-->
        </div>
        <!--end::Contacts-->
    </div>
    <!--end::Sidebar-->
    <!--begin::Content-->
    <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
        <!--begin::Messenger-->
        <div class="card card-flush">
            <!--begin::Card body-->
            <div class="card-body pt-5 card-rounded" id="map"></div>
            <!--end::Card body-->
        </div>
        <!--end::Messenger-->
    </div>
    <!--end::Content-->
</div>
<!--end::Layout-->


<div class="modal fade" id="gis-plan-route-modal" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <!--begin::Modal title-->
                <h2>Butiran Laluan</h2>
                <!--end::Modal title-->

                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fa-duotone fa-square-xmark fs-1"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--start::Form-->
            <form novalidate="novalidate" id="form-gis-plan-modal">
                <!--begin::Modal body-->
                <div class="modal-body py-lg-10 px-lg-10">

                    <div class="h-300px w-100 m-0 p-0 mb-5 rounded rounded-3 overflow-hidden" id="map-modal"></div>

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

                                            <!-- <div class="col-md-3">
                                                <label class="form-label">Kepadatan Utiliti:</label> -->
                                            <!--begin::Select2-->
                                            <!-- <select class="form-select mb-2" data-kt-repeater="select2"
                                                    data-placeholder="Sila pilih Kepadatan" tabindex="0"
                                                    aria-hidden="true" name="density">
                                                    <option></option>
                                                    <option value="1">Rendah</option>
                                                    <option value="2">Sederhana</option>
                                                    <option value="3">Tinggi</option>
                                                </select> -->
                                            <!--end::Select2-->
                                            <!-- </div> -->
                                            <!-- <div class="col-md-3">
                                                <label class="form-label">Jarak (m):</label> -->
                                            <input type="text" name="route-length" data-route-list="length"
                                                class="form-control mb-2 mb-md-0"
                                                placeholder="Sila masukkan jarak laluan." value="0" hidden />
                                            <!-- </div> -->
                                        </div>

                                        <!-- Create a new column for the delete button -->
                                        <!-- <div class="col-md-1"> -->
                                        <!-- <button type="button" data-repeater-delete
                                                class="btn btn-sm btn-light-danger float-end mb-4"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Klik untuk hapuskan daripada senarai.">
                                                <i class="fad fa-trash"></i>
                                            </button> -->
                                        <!-- <button type="button" data-route-list="coor-swap"
                                                class="btn btn-sm btn-light-primary float-end mb-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Klik untuk tukar di antara koordinat mula dan akhir.">
                                                <i class="fad fa-arrow-right-arrow-left"></i>
                                            </button>
                                            <button type="button" data-route-list="move-up"
                                                class="btn btn-sm btn-light-primary float-end mb-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Klik untuk pindah ke atas.">
                                                <i class="fad fa-arrow-up"></i>
                                            </button>
                                            <button type="button" data-route-list="move-down"
                                                class="btn btn-sm btn-light-primary float-end mb-1"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="Klik untuk pindah ke bawah.">
                                                <i class="fad fa-arrow-down"></i>
                                            </button> -->
                                        <!-- </div> -->
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
                            <!-- <div class="col-md-3">
                                <input type="text" class="form-control form-control-solid" id="route-length-total"
                                    name="route-length-total" value="0" />
                            </div> -->
                        </div>
                        <!--end::Form group-->
                    </div>
                    <!--end::Repeater-->
                    <!-- <div class="form-group row">
                        <div class="col-md-3">
                            <label class="form-label">Jarak Surihan:</label>
                            <input class="form-control form-control-solid" name="gpkg-length" id="gpkg-length"
                                placeholder="Jarak Surihan" readonly />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pindaan:</label>
                            <input class="form-control form-control-solid" id="data-revision" name="data-revision" />
                            <input type="text" name="sysId" value="<?php echo $systemId ?>" hidden />
                        </div>
                    </div> -->
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label class="form-label">Catatan:</label>
                            <textarea class="form-control" placeholder="Catatan" name="notes"></textarea>
                        </div>
                    </div>

                    <!--begin::Submit-->
                    <div class="d-flex flex-end mt-5">
                        <button id="submit-gis-plan" class="btn btn-primary me-2 flex-shrink-0">
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