<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

$systemId = $_GET['sid'];
$reportId = $_GET['r'];
$authId = $_GET['a'];

$currentReport = array();
$currentReport = Report::getReportDetails($systemId, $reportId, $authId);

// pin and snap review
$referenceNo = $currentReport['reference_no'];
$refNoCol = General::getAllRefExcept($systemId);

// polygon & polyline review
// TODO: $reviewColId will be use in the future
// $reviewColId = "(" . trim($currentReport['geom_site_visit_id'], "{}") . ")";
$reviewCol = array();
$reviewCol = Report::getTextReview($referenceNo, $reportId, $systemId, $authId);

// marker review
$reviewColPic = array();
$reviewColPic = Report::getImgReview($referenceNo, $reportId, $systemId, $authId);

// var_dump($reviewColPic);
// var_dump(selection('ls_work_methods'));
?>

<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row" data-report-body="container">
    <!-- TODO: new form -->
    <!--begin::Content-->
    <div class="flex-lg-row-fluid me-lg-15 order-2 order-lg-1 mb-10 mb-lg-0">
        <!--begin::Form-->
        <form class="form" action="#" id="reportSummary">
            <!--begin::Customer-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <div class="row">
                            <!-- Provider logo -->
                            <div class="col-3 m-0">
                                <div class="d-flex flex-center flex-shrink-0 rounded w-150px h-150px w-lg-150px me-7 mb-4">
                                    <img class="mw-100 mw-lg-150" src="<?php echo General::getProvider($currentReport['provider_id'])->logo; ?>" alt="image" />
                                </div>
                            </div>
                            <!-- Provider logo -->
                            <!-- Project Title -->
                            <div class="col-9 m-0">
                                <div class="d-flex mb-4">
                                    <h3 class="fw-bold me-3"><a href="/projects/details.php?sid=<?php echo $systemId; ?>" target="_blank" class="fw-bold text-gray-800 text-hover-primary"><?php echo $currentReport['reference_no'] ?></a>
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
                                    foreach ($dist as $row) {
                                        echo '<span class="badge badge-light-' . array_shift($colors) . ' me-2">' . $row . '</span>';
                                    }
                                    ?>
                                </div>
                                <div class="text-gray-500 fw-semibold fs-4 mb-5">
                                    <?php
                                    if (isset($currentReport)) {
                                        echo $currentReport['project_title'];
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- Project Title -->
                        </div>
                    </div>
                    <!--begin::Card title-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Description-->
                    <div class="d-flex flex-wrap justify-content-center">
                        <!--begin::Stats-->
                        <div class="d-flex flex-wrap">
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold"><?php echo $currentReport['provider_name'] ?></div>
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
                                    <div class="fs-4 fw-bold"><?php if (isset($currentReport)) {
                                                                    echo General::convertDate($currentReport['actual_sv_date']);
                                                                }; ?></div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Tarikh</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold"><?php if (isset($currentReport)) {
                                                                    echo General::convertTime($currentReport['actual_sv_date']);
                                                                }; ?></div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-semibold fs-6 text-gray-400">Waktu</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Stat-->
                            <!--begin::Stat-->
                            <div class="rounded min-w-125px py-0 px-4 m-0">
                                <!--begin::Number-->

                                <div class="d-flex align-items-center p-3 mb-2">
                                    <!--begin::Avatar-->
                                    <div class="symbol symbol-60px me-3">
                                        <img alt="Pic" src="assets\media\authorities\<?php if (isset($currentReport)) {
                                                                                            echo $currentReport['authority_logo'];
                                                                                        }; ?>.png" />
                                    </div>
                                    <!--end::Avatar-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Name-->
                                        <a href="#" class="fs-4 fw-bold text-gray-900 me-2"><?php if (isset($currentReport)) {
                                                                                                echo $currentReport['authority_name'];
                                                                                            }; ?></a>
                                        <!--end::Name-->
                                        <!--begin::Email-->
                                        <!-- <a href="#" class="fw-semibold text-gray-600 text-hover-primary">sean@dellito.com</a> -->
                                        <!--end::Email-->
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Number-->
                            </div>
                            <!--end::Stat-->
                        </div>
                        <!--end::Stats-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Customer-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10" id="map" style="width: 100%; height: 500px;"></div>
            <!--begin::Map -->
            <!--end::Map -->
            <!--begin::Road Involved-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2 class="fw-bold">Senarai Jalan Terlibat</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!-- TODO: repeater -->
                    <!--begin::Repeater-->
                    <div id="road-involved">
                        <!--begin::Form group-->
                        <div class="form-group">
                            <!-- begin::road list -->
                            <div data-repeater-list="road-list">
                                <div data-repeater-item>
                                    <div class="form-group row">
                                        <div class="col-md-5 mb-7 fv-row">
                                            <label class="form-label required">Nama
                                                Jalan</label>
                                            <input type="text" name="road-name" class="form-control" placeholder="Nama Jalan" data-report-repeater="road-name" />
                                            <input type="text" name="road-id" class="form-control d-none" data-report-repeater="road-id" />
                                        </div>
                                        <div class="col-md-4 mb-7 fv-row">
                                            <label class="form-label required">Kaedah
                                                Kerja</label>
                                            <select name="road-method" data-report-repeater="road-method" class="form-select method-select" data-placeholder="Kaedah Kerja" multiple>
                                                <?php foreach (Permitting::selection_work_method() as $option) {
                                                    echo '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 mb-7 fv-row">
                                            <label class="form-label required">Jarak
                                                Korekan</label>
                                            <input type="text" name="road-length" class="form-control repeater-value" placeholder="Jarak" data-report-repeater="road-length" />
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" data-repeater-delete class="btn btn-light-danger p-4 mt-8">
                                                <i class="fad fa-trash fs-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end::road list -->
                            <div class="d-flex justify-content-between align-items-center">
                                <!--begin::Form group-->
                                <div class="form-group">
                                    <button type="button" id="btn-create" data-repeater-create class="btn btn-light-primary">
                                        <i class="fad fa-plus"></i>Tambah Jalan
                                    </button>
                                </div>
                                <!--end::Form group-->
                                <div class="d-flex flex-end row">
                                    <div class="col-12 fv-row ">
                                        <label class="form-label">Jumlah Jarak</label>
                                        <input type="text" name="application-length" id="application-length" class="form-control form-control-solid" placeholder="Jarak Permohonan" value="" disabled />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Form group-->
                    </div>
                    <!--end::Repeater-->
                    <!-- TODO: repeater -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Road Involved-->
            <!--begin::Officer Involved-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2 class="fw-bold">Senarai Kehadiran</h2>
                    </div>
                    <!--begin::Card title-->
                    <!--begin::Card toolbar-->
                    <!-- <div class="card-toolbar">
                        <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_product">Tambah Jalan</button>
                    </div> -->
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Repeater-->
                    <div id="guest-involved">
                        <!--begin::Form group-->
                        <div class="form-group">
                            <div data-repeater-list="guest-list">
                                <div data-repeater-item>
                                    <div class="form-group row">
                                        <div class="col-md-4 mb-7 fv-row">
                                            <label class="form-label required">Nama</label>
                                            <input type="text" name="guest-name" class="form-control" placeholder="Nama" data-report-repeater="guest-name" />
                                        </div>
                                        <div class="col-md-3 mb-7 fv-row">
                                            <label class="form-label required">Syarikat</label>
                                            <input type="text" name="guest-company" class="form-control" placeholder="Syarikat" data-report-repeater="guest-company" />
                                        </div>
                                        <div class="col-md-3 mb-7 fv-row">
                                            <label class="form-label required">Jawatan</label>
                                            <input type="text" name="guest-position" class="form-control" placeholder="Jawatan" data-report-repeater="guest-position" />
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" data-report-repeater="signature-modal-button" class="btn btn-light-primary p-4 mt-8">
                                                <i class="fad fa-signature fs-4"></i>
                                            </button>
                                            <input type="text" name="guest-signature" data-report-repeater="guest-signature" class="form-control" value="" hidden />
                                            <input type="text" name="guest-type" data-report-repeater="guest-type" class="form-control" value="" hidden />
                                            <input type="text" name="guest-signature-id" data-report-repeater="guest-signature-id" class="form-control" value="" hidden />
                                            <input type="text" name="guest-contact-id" data-report-repeater="guest-contact-id" class="form-control" value="" hidden />
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" data-repeater-delete class="btn btn-light-danger p-4 mt-8">
                                                <i class="fad fa-trash fs-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <!--begin::Form group-->
                                <div class="form-group">
                                    <button type="button" id="btn-create" data-repeater-create class="btn btn-light-primary">
                                        <i class="fad fa-plus"></i>Tambah Pegawai
                                    </button>
                                </div>
                                <!--end::Form group-->
                            </div>
                        </div>
                        <!--end::Form group-->

                    </div>
                    <!--end::Repeater-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Officer Involved-->
            <!--begin::Ulasan Lawatan-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2 class="fw-bold">Ulasan Lawatan Tapak</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!-- TODO: Ulasan Body -->
                    <!-- <ol class="list-group"> -->
                    <?php foreach ($reviewCol as $textReview) {
                        // echo '<li class="list-group-item">'.$textReview["description"].'</li>';
                        echo '<div class="mb-4">
                            <!--begin::Item-->
                            <div class="d-flex align-items-center ps-10 mb-n1">
                                <!--begin::Bullet-->
                                <span class="bullet me-3"></span>
                                <!--end::Bullet-->
                                <!--begin::Label-->
                                <div class="text-gray-600 fw-semibold fs-6">' . $textReview["description"] . '</div>
                                <!--end::Label-->
                            </div>
                            <!--end::Item-->
                        </div>';
                    } ?>
                    <!-- </ol> -->
                    <!-- TODO: Ulasan Body -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Ulasan Lawatan-->
            <!--begin::Ulasan Lawatan Bergambar-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2 class="fw-bold">Ulasan Bergambar</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!-- TODO: Ulasan Body -->
                    <?php
                    $count = count($reviewColPic);
                    for ($i = 0; $i < $count; $i += 2) {
                        $pic1 = base64_decode($reviewColPic[$i]['url']);
                        $desc1 = $reviewColPic[$i]['description'];

                        // Generate unique class name for the first image
                        $uniqueClass1 = 'report-review-pic-' . ($i + 1);

                        // Check if there is a second image available
                        if (($i + 1) < $count) {
                            $pic2 = base64_decode($reviewColPic[$i + 1]['url']);
                            $desc2 = $reviewColPic[$i + 1]['description'];

                            // Generate unique class name for the second image
                            $uniqueClass2 = 'report-review-pic-' . ($i + 2);
                        }

                        // Add the necessary HTML and unique classes for customization
                        echo '
                        <div class="row mb-3">
                          <div class="col-md-6 d-flex">
                            <div class="row flex-fill">
                              <div class="col-md-12 ' . $uniqueClass1 . ' report-review-pic">
                                <img src="' . $pic1 . '" alt="Image" class="img-fluid">
                                <p class="text-gray-600 fw-semibold fs-6">' . $desc1 . '</p>
                              </div>
                            </div>
                          </div>';

                        if (($i + 1) < $count) {
                            echo '
                          <div class="col-md-6 d-flex">
                            <div class="row flex-fill">
                              <div class="col-md-12 ' . $uniqueClass2 . ' report-review-pic">
                                <img src="' . $pic2 . '" alt="Image" class="img-fluid">
                                <p class="text-gray-600 fw-semibold fs-6">' . $desc2 . '</p>
                              </div>
                            </div>
                          </div>';
                        }

                        echo '</div>'; // Close the row div
                    }
                    ?>
                    <!-- TODO: Ulasan Body -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Ulasan Lawatan Bergambar-->
            <!--begin::Amend Involved-->
            <div id="amend-details-card" class="card card-flush pt-3 mb-5 mb-lg-10 d-none">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2 class="fw-bold">Maklumat Pindaan</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!-- TODO: repeater -->
                    <!--begin::Repeater-->
                    <div id="amend-details">
                        <!--begin::Form group-->
                        <div class="form-group">
                            <!-- begin::road list -->
                            <div data-repeater-list="amend-list">
                                <div data-repeater-item>
                                    <div class="form-group row">
                                        <div class="col-md-5 mb-7 fv-row">
                                            <label class="form-label required">Nama Jalan Terlibat</label>
                                            <input type="text" name="amend-road-name" class="form-control" placeholder="Nama Jalan" data-report-repeater="amend-road-name" />
                                            <input type="text" name="amend-id" class="form-control d-none" data-report-repeater="amend-id" />
                                        </div>
                                        <div class="col-md-3 mb-7 fv-row">
                                            <label class="form-label required">Jarak/Kaedah Asal</label>
                                            <textarea name="amend-original" class="form-control repeater-value" placeholder="Cadangan Asal" data-report-repeater="amend-original" ></textarea>
                                        </div>
                                        <div class="col-md-3 mb-7 fv-row">
                                            <label class="form-label required">Jarak/Kaedah Pindaan</label>
                                            <textarea name="amend-proposed" class="form-control repeater-value" placeholder="Cadangan Pindaan" data-report-repeater="amend-proposed" ></textarea>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" data-repeater-delete class="btn btn-light-danger p-4 mt-8">
                                                <i class="fad fa-trash fs-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end::road list -->
                            <div class="d-flex align-items-start">
                                <!--begin::Form group-->
                                <div class="form-group">
                                    <button type="button" id="btn-create" data-repeater-create class="btn btn-light-primary">
                                        <i class="fad fa-plus"></i>Tambah Maklumat Pindaan
                                    </button>
                                </div>
                                <!--end::Form group-->
                            </div>
                        </div>
                        <!--end::Form group-->
                    </div>
                    <!--end::Repeater-->
                    <!-- TODO: repeater -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Amend Involved-->
            <!--begin::Pengesahan Lawatan-->
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card header-->
                <div class="card-header">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2 class="fw-bold">Pengesahan</h2>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!-- TODO: Pengesahan Body -->
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <label class="form-label required"><strong>Pihak Koridor :</strong></label>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-12 d-flex align-items-center justify-content-center h-150px">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signature-pad-01">
                                        Tandatangan
                                    </button>
                                    <img class="report-approval-sign d-none" data-report-signature="signature-img" src="" alt="Image">
                                    <input type="text" name="signature-img-01" data-report-signature="signature-img-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <hr class="border-4 border-top">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-name">Nama : </span>
                                    <input type="text" name="signature-name-01" data-report-signature="signature-name-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-position">Jawatan : </span>
                                    <input type="text" name="signature-position-01" data-report-signature="signature-position-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-date">Tarikh : </span>
                                    <input type="text" name="signature-date-01" data-report-signature="signature-date-input" class="d-none" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <label class="form-label required"><strong>Pihak Pemohon :</strong></label>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-12 d-flex align-items-center justify-content-center h-150px">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signature-pad-02">
                                        Tandatangan
                                    </button>
                                    <img class="report-approval-sign d-none" data-report-signature="signature-img" src="" alt="Image">
                                    <input type="text" name="signature-img-02" data-report-signature="signature-img-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <hr class="border-4 border-top">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-name">Nama : </span>
                                    <input type="text" name="signature-name-02" data-report-signature="signature-name-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-position">Jawatan : </span>
                                    <input type="text" name="signature-position-02" data-report-signature="signature-position-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-date">Tarikh : </span>
                                    <input type="text" name="signature-date-02" data-report-signature="signature-date-input" class="d-none" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <label class="form-label"><strong>Pihak Berkuasa :</strong></label>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-12 d-flex align-items-center justify-content-center h-150px">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#signature-pad-03">
                                        Tandatangan
                                    </button>
                                    <img class="report-approval-sign d-none" data-report-signature="signature-img" src="" alt="Image">
                                    <input type="text" name="signature-img-03" data-report-signature="signature-img-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <hr class="border-4 border-top">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-name">Nama : </span>
                                    <input type="text" name="signature-name-03" data-report-signature="signature-name-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-position">Jawatan : </span>
                                    <input type="text" name="signature-position-03" data-report-signature="signature-position-input" class="d-none" />
                                </div>
                            </div>
                            <div class="row align-items-start">
                                <div class="col-md-12">
                                    <span class="text-gray-600 fw-semibold fs-6" data-report-signature="signature-date">Tarikh : </span>
                                    <input type="text" name="signature-date-03" data-report-signature="signature-date-input" class="d-none" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- TODO: Pengesahan Body -->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Pengesahan Lawatan-->
            <!-- TODO: -->
        </form>
        <!--end::Form-->
    </div>
    <!--end::Content-->
    <!--begin::Sidebar-->
    <div class="flex-column flex-lg-row-auto w-100 w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
        <!--begin::Card-->
        <div class="card card-flush pt-3 mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary" data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto" data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
            <!--begin::Card header-->
            <div class="card-header">
                <!--begin::Card title-->
                <div class="card-title">
                    <h2><?php echo $currentReport['report_type']; ?></h2>
                </div>
                <!--end::Card title-->
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0 fs-6">
                <form class="form" action="#" id="reportChecklist">
                    <!--begin::Section-->
                    <div class="mb-7">
                        <!--begin::Title-->
                        <h5 class="mb-3">Senarai Semak Laporan</h5>
                        <!--end::Title-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Seperator-->
                    <div class="separator separator-dashed mb-7"></div>
                    <!--end::Seperator-->
                    <!--begin::Section-->
                    <div class="mb-7">
                        <!--begin::Title-->
                        <h5 class="mb-3">Pertindihan & Pindaan</h5>
                        <!--end::Title-->
                        <!--begin::Details-->
                        <div class="mb-0">
                            <div class="mb-2 d-none" id="overlapDetailsContainer">
                                <select class="form-select" data-control="select2" data-placeholder="No Rujukan Permohonan Yang Bertindih" id="overlapDetails" name="overlapDetails">
                                    <option></option>
                                    <?php foreach($refNoCol as $option) {?>
                                    <option value="<?php echo $option['reference_no'] ?>"><?php echo $option['reference_no'] ?></option>
                                    <?php } ?>
                                </select>
                                <!-- <input type="text" class="form-control" id="overlapDetails" name="overlapDetails" placeholder="Select2::No Rujukan Permohonan Yang Bertindih"/> -->
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid mb-2">
                                <input class="form-check-input" type="checkbox" value="1" id="overlapping" name="overlapping" />
                                <label class="form-check-label" for="overlapping">
                                    Pertindihan
                                </label>
                            </div>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="amend-plan" name="amend-plan" />
                                <label class="form-check-label" for="amend-plan">
                                    Pindaan
                                </label>
                            </div>
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Seperator-->
                    <div class="separator separator-dashed mb-7"></div>
                    <!--end::Seperator-->
                    <!--begin::Section-->
                    <div class="mb-7">
                        <!--begin::Title-->
                        <h5 class="mb-3">Melibatkan Jalan Persendirian</h5>
                        <!--end::Title-->
                        <!--begin::Details-->
                        <div class="mb-0">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="private-road" name="private-road" />
                                <label class="form-check-label" for="private-road">
                                    Ya
                                </label>
                            </div>
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Seperator-->
                    <div class="separator separator-dashed mb-7"></div>
                    <!--end::Seperator-->
                    <!--begin::Section-->
                    <div class="mb-7">
                        <!--begin::Title-->
                        <h5 class="mb-3">Pengecualian Izin Lalu</h5>
                        <!--end::Title-->
                        <!--begin::Details-->
                        <div class="mb-0">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="wl-exclusion" name="wl-exclusion" />
                                <label class="form-check-label" for="wl-exclusion">
                                    Pengecualian
                                </label>
                            </div>
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Seperator-->
                    <div class="separator separator-dashed mb-7"></div>
                    <!--end::Seperator-->
                    <!--begin::Section-->
                    <div class="mb-10">
                        <!--begin::Title-->
                        <h5 class="mb-3">Catatan</h5>
                        <!--end::Title-->
                        <!--begin::Details-->
                        <div class="mb-0">
                            <div class="mb-2" id="actionNotes">
                                <textarea name="actionNotes" class="form-control" placeholder="Catatan" data-report-note="action" ></textarea>
                            </div>
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Section-->
                    <!--begin::Actions-->
                    <div class="mb-0 d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary flex-grow-1 me-1" id="report-submit">
                            <!--begin::Indicator label-->
                            <span class="indicator-label">Hantar</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            <!--end::Indicator progress-->
                        </button>
                        <button type="button" class="btn btn-danger" id="report-discard">
                            <!--begin::Indicator label-->
                            <span class="indicator-label">Batal</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            <!--end::Indicator progress-->
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Sidebar-->
    <!-- TODO: new form end -->
    <div class="modal fade" tabindex="-1" id="signature-pad-01">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tandatangan Pihak Koridor</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div name="signature-pad" class="signature-pad m-auto">
                        <div class="signature-pad--body">
                            <canvas></canvas>
                        </div>
                    </div>
                    <div class="row mx-6 mt-8">
                        <div class="col-md-6 mb-6 fv-row">
                            <label class="form-label required">Nama Pegawai</label>
                            <input type="text" name="signature-name-modal" class="form-control" placeholder="Nama Pegawai" data-report-repeater="signature-name-modal" />
                        </div>
                        <div class="col-md-6 mb-6 fv-row">
                            <label class="form-label required">Jawatan</label>
                            <input type="text" name="signature-position-modal" class="form-control" placeholder="Jawatan" data-report-repeater="signature-position-modal" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" data-report-button="saveSignature">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="signature-pad-02">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tandatangan Pihak Pemohon</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div name="signature-pad" class="signature-pad m-auto">
                        <div class="signature-pad--body">
                            <canvas></canvas>
                        </div>
                    </div>
                    <div class="row mx-6 mt-8">
                        <div class="col-md-6 mb-6 fv-row">
                            <label class="form-label required">Nama Pegawai</label>
                            <input type="text" name="signature-name-modal" class="form-control" placeholder="Nama Pegawai" data-report-repeater="signature-name-modal" />
                        </div>
                        <div class="col-md-6 mb-6 fv-row">
                            <label class="form-label required">Jawatan</label>
                            <input type="text" name="signature-position-modal" class="form-control" placeholder="Jawatan" data-report-repeater="signature-position-modal" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" data-report-button="saveSignature">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="signature-pad-03">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tandatangan Pihak Berkuasa</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <div class="modal-body">
                    <div name="signature-pad" class="signature-pad m-auto">
                        <div class="signature-pad--body">
                            <canvas></canvas>
                        </div>
                    </div>
                    <div class="row mx-6 mt-8">
                        <div class="col-md-6 mb-6 fv-row">
                            <label class="form-label required">Nama Pegawai</label>
                            <input type="text" name="signature-name-modal" class="form-control" placeholder="Nama Pegawai" data-report-repeater="signature-name-modal" />
                        </div>
                        <div class="col-md-6 mb-6 fv-row">
                            <label class="form-label required">Jawatan</label>
                            <input type="text" name="signature-position-modal" class="form-control" placeholder="Jawatan" data-report-repeater="signature-position-modal" />
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" data-report-button="saveSignature">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Layout-->