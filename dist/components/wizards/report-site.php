<div class="card-body pt-5 card-rounded" id="map"></div>
<?php
$reportId = isset($_GET['ref']) ? $_GET['ref'] : '';
$authId = isset($_GET['auth']) ? $_GET['auth'] : '';
$currentReport = Report::getReportDetails($systemId, $reportId, $authId);
?>
<div class="modal fade" id="site-visit-init-modal" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="fad fa-xmark fs-1 d-none"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y pt-0 pb-15">
                <!--begin::Wrapper-->
                <div class="mw-lg-600px mx-auto">
                    <!--begin::Heading-->
                    <div class="mb-10 text-center" data-sv-init="head-1">
                        <!--begin::Title-->
                        <h1 class="mb-3" data-sv-init="title">Tetapan Awal Lawatan Tapak</h1>
                        <!--end::Title-->
                        <!--begin::Description-->
                        <div class="text-muted fw-semibold fs-5" data-sv-init="subtitle">Sila Tekan Pada Butang Mula
                            untuk Memulakan Lawatan Tapak.
                        </div>
                        <!--end::Description-->
                    </div>
                    <div class="mb-13 text-center d-none" data-sv-init="head-2">
                        <div class="d-flex justify-content-between">
                            <h1 class="d-flex flex-column-reverse" data-sv-init="title">Kehadiran Lawatan Tapak</h1>
                            <button type="button" id="guest-btn-create" class="btn btn-light-primary"
                                data-sv-init="guest-repeater-add">
                                <i class="fad fa-plus"></i>Tambah Pegawai
                            </button>
                        </div>
                    </div>
                    <!--end::Heading-->
                    <!-- TODO: Start::Report Info -->
                    <div class="card card-dashed bg-light-dark p-5 mb-5" data-sv-init="appl-info">
                        <div class="row">
                            <!-- Provider logo -->
                            <div class="col-2">
                                <div class="d-flex justify-content-center align-items-center rounded"
                                    style="width: 100px; height: 100px;">
                                    <!-- TODO : Intergrate With Extercord API (using dev link) -->
                                    <?php
                                    $data = General::getProvider($currentReport['provider_id']);
                                    ?>
                                    <img class="img-fluid" src="<?= $data->logo; ?>" alt="<?= $data->name ?>" />
                                </div>
                            </div>
                            <!-- Provider logo -->
                            <!-- Project Title -->
                            <div class="col-10">
                                <div class="d-flex justify-content-between">
                                    <h3 class="fw-bold me-3">
                                        <a href="/projects/details.php?sid=<?php echo $systemId; ?>" target="_blank"
                                            class="fw-bold text-gray-800 text-hover-primary">
                                            <?= $currentReport['reference_no'] ?>
                                        </a>
                                        <?php
                                        if (isset($reportId)) {
                                            echo ' - ' . $reportId;
                                        }
                                        ?>
                                    </h3>
                                    <?php
                                    $dist = explode(",", $currentReport['project_district_name']);
                                    $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                    shuffle($colors);
                                    echo '<div>';
                                    foreach ($dist as $row) {
                                        echo '<span class="badge badge-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                    }
                                    echo '</div>';
                                    ?>
                                </div>
                                <p class="text-gray-600 fw-semibold fs-5" style="text-align: justify;">
                                    <?php
                                    if (isset($currentReport)) {
                                        echo $currentReport['project_title'];
                                    }
                                    ?>
                                </p>
                            </div>
                            <!-- Project Title -->
                        </div>

                    </div>
                    <!-- TODO: End::Report Info -->
                    <!--begin::Input group-->
                    <div class="mb-10 d-none" data-sv-init="guest-repeater">
                        <div id="guest-involved">
                            <!--begin::Form group-->
                            <div class="form-group">
                                <div data-repeater-list="guest-list">
                                    <div data-repeater-item>
                                        <div class="form-group row">
                                            <div class="col-md-4 mb-7 fv-row">
                                                <label class="form-label required">Nama</label>
                                                <input type="text" name="guest-name" class="form-control"
                                                    placeholder="Nama" data-report-repeater="guest-name" />
                                            </div>
                                            <div class="col-md-3 mb-7 fv-row">
                                                <label class="form-label required">Syarikat</label>
                                                <input type="text" name="guest-company" class="form-control"
                                                    placeholder="Syarikat" data-report-repeater="guest-company" />
                                            </div>
                                            <div class="col-md-3 mb-7 fv-row">
                                                <label class="form-label required">Jawatan</label>
                                                <input type="text" name="guest-position" class="form-control"
                                                    placeholder="Jawatan" data-report-repeater="guest-position" />
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" data-report-repeater="signature-modal-button"
                                                    class="btn btn-light-primary p-4 mt-8">
                                                    <i class="fad fa-signature fs-4"></i>
                                                </button>
                                                <input type="text" name="guest-signature"
                                                    data-report-repeater="guest-signature" class="form-control" value=""
                                                    hidden />
                                                <input type="text" name="guest-type" data-report-repeater="guest-type"
                                                    class="form-control" value="" hidden />
                                                <input type="text" name="guest-signature-id"
                                                    data-report-repeater="guest-signature-id" class="form-control"
                                                    value="" hidden />
                                                <input type="text" name="guest-contact-id"
                                                    data-report-repeater="guest-contact-id" class="form-control"
                                                    value="" hidden />
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" data-repeater-delete
                                                    class="btn btn-light-danger p-4 mt-8">
                                                    <i class="fad fa-trash fs-4"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start">
                                    <!--begin::Form group-->
                                    <div class="form-group d-none">
                                        <button type="button" id="btn-create" data-repeater-create
                                            class="btn btn-light-primary">
                                            <i class="fad fa-plus"></i>Tambah Pegawai
                                        </button>
                                    </div>
                                    <!--end::Form group-->
                                </div>
                            </div>
                            <!--end::Form group-->

                        </div>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex align-items-center mt-10">
                        <div class="position-relative ps-4 py-6 me-4">
                            <div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-primary">
                            </div>
                        </div>
                        <!--begin::Label-->
                        <div class="flex-grow-1">
                            <span class="fs-3 fw-bold text-primary d-block" data-sv-init="time">Memuat Maklumat Masa
                                daripada Pangkalan Data...</span>
                            <span class="fs-7 fw-semibold text-muted" data-sv-init="time-desc">Sistem Akan Menyimpan
                                Tarikh ini sebagai Tarikh Sebenar Lawatan Tapak</span>
                        </div>
                        <!--end::Label-->
                        <!--begin::Switch-->
                        <button type="button" id="sv-init-start" data-sv-init="start" class="btn btn-light-primary">
                            <i class="fad fa-location-pen"></i>Mula
                        </button>
                        <button type="button" id="sv-init-hide" data-sv-init="hide" class="btn btn-secondary d-none">
                            <i class="fad fa-location-pen"></i>Tutup
                        </button>
                        <!--end::Switch-->
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>