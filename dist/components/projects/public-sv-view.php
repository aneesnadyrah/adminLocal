<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
$currentReports = array();
$currentReports = Report::getReportDetails($systemId, $reportId);
$currentProject = $currentReports[0];
$authValue = isset($_GET['a']) ? $_GET['a'] : '';

// Function to get image orientation
function getImageOrientation($base64Image) {
    $imageData = base64_decode($base64Image);
    $image = @imagecreatefromstring($imageData);
    
    if ($image === false) {
        return 'landscape'; // default fallback
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    imagedestroy($image);
    
    return ($height > $width) ? 'portrait' : 'landscape';
}

// Function to render Section 4.0 with dynamic pages
function renderSection4($reviewColPic) {
    if (empty($reviewColPic)) {
        return; // No images to display
    }

    $totalImages = count($reviewColPic);
    $imageNumber = 1;
    $currentPage = 0;
    $imagesPerPage = 4; // Always 2x2 grid (4 images per page)

    $i = 0;
    
    while ($i < $totalImages) {
        $isFirstPage = ($currentPage === 0);
        ?>
        
        <!-- 4.0 Rujukan Bergambar -->
        <table class="table" data-section-title="LLT">
            <thead style="background-color:lightgrey;">
                <tr>
                    <td colspan="2" class="py-1 px-2"
                        style="font-weight:600; text-align: start; text-transform: uppercase;">
                        4.0 Rujukan Bergambar <?php echo !$isFirstPage ? '(Sambungan)' : ''; ?>
                    </td>
                </tr>
            </thead>
        </table>
        
        <table class="table">
            <tbody style="border:1px solid black;">
            <?php
            $imagesOnCurrentPage = 0;
            
            // Process images in pairs (2 per row)
            while ($i < $totalImages && $imagesOnCurrentPage < $imagesPerPage) {
                echo '<tr>';
                
                // First image in row
                if ($i < $totalImages) {
                    $currentImage = $reviewColPic[$i];
                    $pic1 = base64_decode($currentImage['url']);
                    $desc1 = $currentImage['description'];
                    $highlightClass1 = ($currentImage["amend"] && isset($_GET['hl']) ? ' bg-warning' : '');
                    $uniqueClass1 = 'report-review-pic-' . ($i + 1);
                    
                    echo '
                    <td class="p-2" style="width:50%; vertical-align:top;">
                        <div class="position-relative d-flex align-items-center">
                            <div class="col-md-12 ' . $uniqueClass1 . ' report-review-pic">
                                <div class="position-absolute" style="top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 2px 8px; border-radius: 15px; font-size: 12px; font-weight: bold; z-index: 10;">
                                    ' . $imageNumber . '
                                </div>
                                <div class="image-container">
                                    <img src="' . $pic1 . '" alt="Image">
                                </div>
                                <p class="text-gray-600 fw-semibold fs-6' . $highlightClass1 . '" style="text-align: justify;text-align-last: center;padding-left: 5px;padding-right: 5px;padding-top:5px;">
                                    ' . $imageNumber . '. ' . $desc1 . '
                                </p>
                            </div>
                        </div>
                    </td>';
                    
                    $imageNumber++;
                    $i++;
                    $imagesOnCurrentPage++;
                } else {
                    // Empty cell if no more images
                    echo '<td class="p-2" style="width:50%;"></td>';
                }
                
                // Second image in row
                if ($i < $totalImages && $imagesOnCurrentPage < $imagesPerPage) {
                    $currentImage = $reviewColPic[$i];
                    $pic2 = base64_decode($currentImage['url']);
                    $desc2 = $currentImage['description'];
                    $highlightClass2 = ($currentImage["amend"] && isset($_GET['hl']) ? ' bg-warning' : '');
                    $uniqueClass2 = 'report-review-pic-' . ($i + 1);
                    
                    echo '
                    <td class="p-2" style="width:50%; vertical-align:top;">
                        <div class="position-relative d-flex align-items-center">
                            <div class="col-md-12 ' . $uniqueClass2 . ' report-review-pic">
                                <div class="position-absolute" style="top: 10px; left: 10px; background: rgba(0,0,0,0.7); color: white; padding: 2px 8px; border-radius: 15px; font-size: 12px; font-weight: bold; z-index: 10;">
                                    ' . $imageNumber . '
                                </div>
                                <div class="image-container">
                                    <img src="' . $pic2 . '" alt="Image">
                                </div>
                                <p class="text-gray-600 fw-semibold fs-6' . $highlightClass2 . '" style="text-align: justify;text-align-last: center;padding-left: 5px;padding-right: 5px;padding-top:5px;">
                                    ' . $imageNumber . '. ' . $desc2 . '
                                </p>
                            </div>
                        </div>
                    </td>';
                    
                    $imageNumber++;
                    $i++;
                    $imagesOnCurrentPage++;
                } else {
                    // Empty cell if odd number of images
                    echo '<td class="p-2" style="width:50%;"></td>';
                }
                
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        
        <?php
        $currentPage++;
        
        // IMPORTANT: Only add page break if there are MORE images to display
        if ($i < $totalImages) {
            echo '<div style="page-break-after: always;"></div>';
        }
    }
}

// NEW CONSOLIDATED FUNCTION to render a single report
function renderReportSection($currentReport, $systemId, $reportId, $tenant, $company) {
    // Data retrieval
    $referenceNo = $currentReport['reference_no'];
    $reviewCol = Report::getTextReview($referenceNo, $reportId, $systemId, $currentReport['authority_id']);
    $reviewColPic = Report::getImgReview($referenceNo, $reportId, $systemId, $currentReport['authority_id']);
    $corridorSignature = Report::getSignatureValidation($systemId, $reportId, $currentReport['authority_id'], 1);
    $applicantSignature = Report::getSignatureValidation($systemId, $reportId, $currentReport['authority_id'], 2);
    $authoritySignature = Report::getSignatureValidation($systemId, $reportId, $currentReport['authority_id'], 3);
    $guestSignature = Report::getSignatureValidation($systemId, $reportId, $currentReport['authority_id'], 4);
    $roadList = Report::getRoadList($systemId, $reportId, $currentReport['authority_id']);
    ?>
    <section class="sheet padding-15mm main-section" id="mainSection1">
        <!-- Header -->
        <div style="margin-bottom: 1rem;">
            <table style="border-collapse: collapse;">
                <thead>
                    <tr style="border: 1px solid;">
                        <td rowspan="2" style="width: 10%; text-align: center; border: 1px solid;">
                            <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                                src="assets/media/logos/<?php echo strtolower($tenant) ?>-default.svg">
                        </td>
                        <td>
                            <h2 style="text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                <?php echo $company . ' Sdn. Bhd' ?>
                            </h2>
                        </td>
                    </tr>
                    <tr style="border: 1px solid;">
                        <td>
                            <h2 style="text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                Laporan Lawatan Tapak (LLT)
                            </h2>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- 1.0 Maklumat Asas Permohonan -->
        <table class="table" data-section-title="LLT">
            <thead style="background-color:lightgrey;">
                <tr>
                    <td colspan="3" class="py-1 px-2"
                        style="font-weight:600; text-align: start; text-transform: uppercase;">
                        1.0 Maklumat Asas Permohonan
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table">
            <tbody>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Tajuk Projek </p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted;text-transform: uppercase;">
                            <?php echo $currentReport['project_title']; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">No Rujukan </p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo $currentReport['reference_no']; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Tarikh Lawatan Tapak </p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo Report::formatDateAndTimeToMalayDateTimeString($currentReport['actual_sv_date']); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Daerah</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php 
                            $districts = explode(',', $currentReport['project_district_name']);
                            $districtCount = count($districts);
                            if ($districtCount == 1) {
                                $formattedDistricts = $districts[0];
                            } elseif ($districtCount == 2) {
                                $formattedDistricts = implode(' dan ', $districts);
                            } else {
                                $lastDistrict = array_pop($districts);
                                $formattedDistricts = implode(', ', $districts) . ' dan ' . $lastDistrict;
                            }
                            echo $formattedDistricts;
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Jenis Utiliti</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo $currentReport['provider_shorthand']; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Pemohon</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo $currentReport['provider_name']; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Penyedia Utiliti</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo $currentReport['provider_name']; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td class="py-1" style="width:180px;">
                        <p style="margin: 0.5rem 0 0 1rem">Pihak Berkuasa</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo $currentReport['authority_name']; ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 2.0 Senarai Semak Laporan -->
        <table class="table" data-section-title="LLT">
            <thead style="background-color:lightgrey;">
                <tr>
                    <td colspan="2" class="py-1 px-2"
                        style="font-weight:600; text-align: start; text-transform: uppercase;">
                        2.0 Senarai Semak Laporan
                    </td>
                </tr>
            </thead>
        </table>
        <table class="table">
            <tbody>
                <tr>
                    <td class="py-1 pt-2 px-2" style="width:1%;">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="" <?php echo ($currentReport['overlap_status'] === 1) ? 'checked' : ''; ?> disabled />
                        </div>
                    </td>
                    <td class="py-1 pt-2" style="width:30%;">
                        <p>Pertindihan Projek
                            <?php 
                            if ($currentReport['overlap_status'] === 1) {
                                echo ': </br>' . $currentReport['overlap_details'];
                            }
                            ?>
                        </p>
                    </td>
                    <td class="py-1 pt-2 px-2" style="width:1%;">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="" <?php echo ($currentReport['wl_exclusion_status'] === 1) ? 'checked' : ''; ?> disabled />
                        </div>
                    </td>
                    <td class="py-1 pt-2" style="width:37%;">
                        <p>Pengecualian Permohonan Izin Lalu</p>
                    </td>
                    <td class="py-1 pt-2 px-2" style="width:1%;">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="" <?php echo ($currentReport['amend_status'] === 1) ? 'checked' : ''; ?> disabled />
                        </div>
                    </td>
                    <td class="py-1 pt-2" style="width:30%;">
                        <p>Pindaan Cadangan Teknikal</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- 3.0 Semakan Lokasi -->
        <table class="table" data-section-title="LLT">
            <thead style="background-color:lightgrey;">
                <tr>
                    <td colspan="2" class="py-1 px-2"
                        style="font-weight:600; text-align: start; text-transform: uppercase;">
                        3.0 Semakan Lokasi
                    </td>
                </tr>
            </thead>
        </table>
        <div id="map-<?php echo $currentReport['authority_id']; ?>" style="width: 100%; height: 380px;"></div>

        <table class="table">
            <thead>
                <tr>
                    <td colspan="4" class="p-0 pb-2">
                        <p class="fw-bold" style="margin: 0.5rem 0 0 0.5rem">Nama Jalan Terlibat :</p>
                    </td>
                </tr>
            </thead>
            <tbody style="border:1px solid black;">
                <tr>
                    <td class="p-2" style="width:1%;text-align:center;">Bil.</td>
                    <td class="p-2" style="width:30%;text-align:center;">Nama Jalan/Sungai</td>
                    <td class="p-2" style="width:44%;text-align:center;">Kaedah Teknikal</td>
                    <td class="p-2" style="width:25%;text-align:center;">Jarak/Nos</td>
                </tr>
                <?php
                $totalLength = 0;
                foreach ($roadList as $index => $list) {
                    $number = $index + 1;
                    $totalLength += $list['road_length'];
                    $methodNames = array();
                    foreach ($list['method'] as $methodName) {
                        $methodNames[] = $methodName['name'];
                    }
                    $methodString = implode(', ', $methodNames);
                    echo '
                    <tr>
                        <td class="p-2" style="text-align:center;">' . $number . '</td>
                        <td class="p-2">' . $list['road_name'] . '</td>
                        <td class="p-2">' . $methodString . '</td>
                        <td class="p-2" style="text-align:center;">' . $list['road_length'] . '</td>
                    </tr>';
                }
                echo '
                <tr>
                    <td class="p-2" colspan="3">Jumlah Jarak Terlibat</td>
                    <td class="p-2" style="text-align:center;">' . $totalLength . '</td>
                </tr>';
                ?>
            </tbody>
        </table>

        <table class="table">
            <tbody>
                <tr>
                    <td class="p-2 fw-bold">Ulasan Kaedah Teknikal :</td>
                </tr>
                <tr>
                    <td class="p-2">
                        <?php
                        foreach ($reviewCol as $index => $textReview) {
                            $number = $index + 1;
                            echo '<div class="mb-4">
                                <div class="d-flex align-items-start ps-5 mb-n1">
                                    <div class="fs-6 me-3">' . $number . '.</div>
                                    <div class="fs-6' . ($textReview["amend"] && isset($_GET['hl']) ? ' bg-warning' : '') . '">' . $textReview["description"] . '</div>
                                </div>
                            </div>';
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>

    <?php
    // Render Section 4.0 with dynamic layout
    renderSection4($reviewColPic);
    ?>

    <!-- 5.0 Pengesahan -->
    <table class="table" data-section-title="LLT">
        <thead style="background-color:lightgrey;">
            <tr>
                <td colspan="3" class="py-1 px-2"
                    style="font-weight:600; text-align: start; text-transform: uppercase;">
                    5.0 Pengesahan
                </td>
            </tr>
        </thead>
    </table>
    <table class="table">
        <tbody>
            <tr>
                <td class="p-2" style="width:33.33%">Pihak Koridor :</td>
                <td class="p-2" style="width:33.33%">Pihak Pemohon :</td>
                <td class="p-2" style="width:33.33%">Pihak Berkuasa :</td>
            </tr>
            <tr>
                <td style="height:120px;text-align: center;vertical-align: middle;">
                    <?php if ($corridorSignature[0]['signature'] != '') { ?>
                        <img class="report-approval-sign" style="max-height:120px; max-width:80%;"
                            data-report-signature="signature-img"
                            src="<?php echo $corridorSignature[0]['signature']; ?>" alt="Image">
                    <?php } ?>
                </td>
                <td style="height:120px;text-align: center;vertical-align: middle;">
                    <?php if ($applicantSignature[0]['signature'] != '') { ?>
                        <img class="report-approval-sign" style="max-height:120px; max-width:80%;"
                            data-report-signature="signature-img"
                            src="<?php echo $applicantSignature[0]['signature']; ?>" alt="Image">
                    <?php } ?>
                </td>
                <td style="height:120px;text-align: center;vertical-align: middle;">
                    <?php if ($authoritySignature != null) { ?>
                        <img class="report-approval-sign" style="max-height:120px; max-width:80%;"
                            data-report-signature="signature-img"
                            src="<?php echo $authoritySignature[0]['signature']; ?>" alt="Image">
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td class="p-2">
                    <table>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Nama</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1"><?php echo $corridorSignature[0]['full_name']; ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Jawatan</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1"><?php echo $corridorSignature[0]['position']; ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Tarikh</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1"><?php echo Report::convertDateToMalay($corridorSignature[0]['signed_timestamp']); ?></td>
                        </tr>
                    </table>
                </td>
                <td class="p-2">
                    <table>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Nama</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1"><?php echo $applicantSignature[0]['full_name']; ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Jawatan</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1"><?php echo $applicantSignature[0]['position']; ?></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Tarikh</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1"><?php echo Report::convertDateToMalay($applicantSignature[0]['signed_timestamp']); ?></td>
                        </tr>
                    </table>
                </td>
                <td class="p-2">
                    <table>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Nama</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1">
                                <?php if ($authoritySignature != null) { echo $authoritySignature[0]['full_name']; } ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Jawatan</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1">
                                <?php if ($authoritySignature != null) { echo $authoritySignature[0]['position']; } ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;width:20%;">Tarikh</td>
                            <td style="vertical-align: top;width:1%;">: </td>
                            <td class="px-2 pb-1">
                                <?php if ($authoritySignature != null) { echo Report::convertDateToMalay($authoritySignature[0]['signed_timestamp']); } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</section>

<!-- Attendance Section -->
<section class="sheet padding-15mm main-section" id="mainSection2">
    <div style="margin-bottom: 1rem;">
        <table style="border-collapse: collapse;">
            <thead>
                <tr>
                    <td style="width: 80%;">
                        <h2 style="text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                            BORANG KEHADIRAN LAWATAN TAPAK BERSAMA
                        </h2>
                    </td>
                    <td style="width: 20%; text-align: center;">
                        <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                            src="assets/media/logos/<?php echo strtolower($tenant) ?>-default.svg">
                    </td>
                </tr>
            </thead>
        </table>
    </div>
    <table class="table table-bordered" style="border-collapse: collapse;border:1px solid black;">
        <tbody>
            <tr>
                <td style="width: 25%; text-align: center;">NAMA PROJEK</td>
                <td colspan="3" class="ps-2" style="width: 75%; text-align: left; text-transform: uppercase;">
                    <?php echo $currentReport['project_title']; ?>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">NO RUJUKAN</td>
                <td colspan="3" class="ps-2" style="text-align: left;">
                    <?php echo $currentReport['reference_no']; ?>
                </td>
            </tr>
            <tr>
                <td style="width: 25%; text-align: center;">TARIKH</td>
                <td style="width: 25%; text-align: center;">
                    <?php echo Report::convertDateToMalay($currentReport['actual_sv_date']); ?>
                </td>
                <td style="width: 25%; text-align: center;">WAKTU</td>
                <td style="width: 25%; text-align: center;">
                    <?php echo Report::formatTimeToMalayTimeString($currentReport['actual_sv_date']); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered" style="border-collapse: collapse;border:1px solid black;">
        <thead>
            <tr>
                <td style="width: 5%; text-align: center;">BIL.</td>
                <td style="text-align: center;">NAMA</td>
                <td style="width: 20%; text-align: center;">AGENSI/SYARIKAT</td>
                <td style="width: 20%; text-align: center;">JAWATAN</td>
                <td style="width: 20%; text-align: center;">TANDATANGAN</td>
            </tr>
            <?php
            foreach ($guestSignature as $index => $guest) {
                $number = $index + 1;
                echo '
                <tr>
                    <td class="p-2" style="text-align:center;">' . $number . '</td>
                    <td class="p-2" style="text-align:left;">' . $guest['full_name'] . '</td>
                    <td class="p-2" style="text-align:left;">' . $guest['company_name'] . '</td>
                    <td class="p-2" style="text-align:center;">' . $guest['position'] . '</td>
                    <td class="p-2" style="text-align:center;height:25px;overflow: visible;vertical-align: middle;">';
                if ($guest['signature'] != '') {
                    echo '<img style="max-height:40px; max-width:80%;" src="' . $guest['signature'] . '" alt="Image">';
                }
                echo '</td>
                </tr>';
            }
            ?>
        </thead>
    </table>
</section>
<?php
}
?>

<style>
    .image-container {
        width: 100%;
        height: 250px; /* FIXED HEIGHT - same for all images */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px;
    }
    
    /* All images use the same constraints - portrait or landscape */
    .image-container img {
        max-width: 100%;
        max-height: 250px; /* FIXED maximum height */
        width: auto;
        height: auto;
        object-fit: contain; /* Maintain aspect ratio */
        display: block;
    }
    
    .report-review-pic {
        width: 100%;
        position: relative;
    }
    
</style>

<div>
    <?php
    if ($authValue) {
        // WITH SPECIFIC AUTHORITY FILTER
        foreach ($currentReports as $currentReport) {
            if ($currentReport['authority_id'] == $_GET['a']) {
                renderReportSection($currentReport, $systemId, $reportId, $tenant, $company);
            }
        }
    } else {
        // WITHOUT AUTHORITY FILTER - SHOW ALL AUTHORITIES
        foreach ($currentReports as $currentReport) {
            renderReportSection($currentReport, $systemId, $reportId, $tenant, $company);
        }
    }
    ?>
</div>