<?php
$systemId = $_GET['sid'];
$activeReport = Report::getActiveReport($systemId);
$reportId = $activeReport->reportNo;
$reportCtrl = $activeReport->reportCtrl;

$currentReport = array();
$currentReport = Report::getReportDetails($systemId, $reportId);
$currentApplication = $currentReport[0];
$provider = General::getProvider($currentApplication['provider_id']);
?>
<!--begin::Card-->
<div id="hidden_key" class="w-100"></div>
<div class="card card-flush mb-5 mb-xxl-8" data-kt-sticky="true" data-kt-sticky-name="sticky-summary" data-kt-sticky-width="{target: '#hidden_key'}" data-kt-sticky-offset="{default: false, xl: '200px'}" data-kt-sticky-top="100px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
    <div class="card-header">
        <div class="d-flex flex-stack">
            <div class="symbol symbol-100px me-5">
                <img src="<?php echo $provider->logo; ?>" alt="image" />
            </div>
            <h3 class="card-title d-none d-md-block">
                <?php echo $provider->name; ?>
            </h3>
        </div>
        <div class="card-toolbar">
            <div class="d-flex flex-stack">
                <div class="form-check form-switch form-check-custom form-check-warning form-check-solid me-2">
                    <label class="form-check-label me-2" for="highlighter">
                        Butiran Pindaan
                    </label>
                    <input class="form-check-input" type="checkbox" value="" id="highlighter" disabled checked="checked" />
                </div>
                <?php
                //Array for this Nested
                $colors = ['light-dark', 'dark'];
                $customAttribute = ['data-report-view="print"', 'data-bs-toggle="modal" data-bs-target="#modal-approve"'];
                $icons = ['fa-print', 'fa-file-circle-check'];
                $customClass = ['d-none', ''];
                $items = ['Cetak', 'Sahkan'];

                $array = array_map(function ($color, $customAttribute, $icon, $customClass, $item) {
                    return ['color' => $color, 'customAttribute' => $customAttribute, 'icon' => $icon, 'customClass' => $customClass, 'item' => $item];
                }, $colors, $customAttribute, $icons, $customClass, $items);

                foreach ($array as $button) {
                    echo <<<Action
                                <button type="button" class="btn btn-flex flex-center btn-{$button['color']} me-2 {$button['customClass']}" {$button['customAttribute']}>
                                    <i class="fad {$button['icon']} fs-4"></i>
                                    <span class="d-none d-md-inline">{$button['item']}</span>
                                </button>
                            Action;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!--end::Card-->
<!-- TODO: Report view card -->
<!-- begin::reportCard -->
<?php
foreach ($currentReport as $report) {
?>
<div class="card card-flush pt-3 mb-5 mb-xl-10">

    <?php
    // var_dump($currentProject);

//data ulasan based on geom
$reviewCol = array();
$reviewCol = Report::getTextReview($report['reference_no'], $reportId, $systemId, $report['authority_id']);
// print_r($reviewCol);

$reviewColPic = array();
$reviewColPic = Report::getImgReview($report['reference_no'], $reportId, $systemId, $report['authority_id']);
// print_r($reviewColPic);
// $signType = 3;
// $signatureValidation = array();
$corridorSignature = Report::getSignatureValidation($systemId, $reportId, $report['authority_id'], 1);
$applicantSignature = Report::getSignatureValidation($systemId, $reportId, $report['authority_id'], 2);
$authoritySignature = Report::getSignatureValidation($systemId, $reportId, $report['authority_id'], 3);
$guestSignature = Report::getSignatureValidation($systemId, $reportId, $report['authority_id'], 4);

// print_r($guestSignature);
// $decodedData = base64_decode($signatureValidation[0]['signature']);
// print_r($decodedData);
$roadList = Report::getRoadList($systemId, $reportId, $report['authority_id']);
// print_r($roadList);
    ?>

<!-- TODO: new form -->
    <!--begin::Content-->
    <div class="flex-lg-row-fluid mx-lg-10 order-2 order-lg-1 mb-10 mb-lg-0">
        <!--begin::Form-->
        <form class="form" action="#" id="reportSummary">
            <!--begin::Customer-->
                <!--begin::Card body-->
                <div class="card-body">
                    <!-- header report -->
                    <div style="margin-bottom: 1rem; ">
                        <table style="border-collapse: collapse;">
                            <thead>
                                <tr style="border: 1px solid;">
                                    <td rowspan="2" style="width: 10%; text-align: center; border: 1px solid;">
                                        <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                                            src="assets/media/logos/<?php echo strtolower($system->App->tenant) ?>-default.svg">
                                    </td>
                                    <td>
                                        <h2 style=" text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                            <!-- Koridor Utiliti Teknologi Terengganu Sdn. Bhd -->
                                            <?php echo $system->App->company . ' Sdn. Bhd'?>
                                        </h2>
                                    </td>
                                </tr>
                                <tr style="border: 1px solid;" >
                                    <td>
                                        <h2 style=" text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                            Laporan Lawatan Tapak (LLT)
                                        </h2>
                                    </td>
                                </tr>
                            </thead>
                        </table>

                    </div>
                    <!-- 1.0 Maklumat Asas Permohonan -->
                    <table class="table">
                        <thead style="background-color:lightgrey;">
                            <tr>
                                <td colspan="3" class="py-1 px-2" style="font-weight:600; text-align: start; text-transform: uppercase;">
                                    1.0 Maklumat Asas Permohonan
                                </td>
                            </tr>
                        </thead>
                        </table>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="py-1"style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">Tajuk Projek </p>
                                </td>
                                <td class="py-1"style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted; text-transform: uppercase;"><?php echo $report['project_title']; ?></p>
                                </td>
                            </tr>
                            <tr >
                                <td  class="py-1" style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">No Rujukan </p>
                                </td>
                                <td class="py-1" style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $report['reference_no']; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1" style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">Tarikh Lawatan Tapak </p>
                                </td>
                                <td class="py-1" style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo Report::formatDateAndTimeToMalayDateTimeString($report['actual_sv_date']); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1" style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">Daerah</p>
                                </td>
                                <td class="py-1" style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                                    <?php $districts = explode(',', $report['project_district_name']);

                                    // Format the district names based on count
                                    $count = count($districts);
                                    if ($count == 1) {
                                        $formattedDistricts = $districts[0];
                                    } elseif ($count == 2) {
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
                                </td >
                                <td class="py-1" style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $report['provider_shorthand']; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1" style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">Pemohon</p>
                                </td>
                                <td class="py-1" style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $report['provider_name']; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1" style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">Penyedia Utiliti</p>
                                </td>
                                <td class="py-1"style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $report['provider_name']; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-1" style="width:180px;">
                                    <p style="margin: 0.5rem 0 0 1rem">Pihak Berkuasa</p>
                                </td>
                                <td class="py-1"style="width:1px; ">
                                    <p class="mt-2" style="text-align:center;">:</p>
                                </td>
                                <td class="py-1">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $report['authority_name']; ?></p>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <!-- 2.0 Senarai Semak Laporan -->
                    <table class="table">
                        <thead style="background-color:lightgrey;">
                            <tr>
                                <td colspan="2" class="py-1 px-2" style="font-weight:600; text-align: start; text-transform: uppercase;">
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
                                        <input class="form-check-input" type="checkbox" value=""
                                        <?php if($report['overlap_status'] === 0){
                                            echo '';
                                        } else if($report['overlap_status'] === 1){
                                            echo 'checked';
                                        }; ?> disabled/>
                                    </div>
                                </td>
                                <td class="py-1 pt-2" style="width:30%;">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p>Pertindihan Projek <?php if($report['overlap_status'] === 0){
                                            echo '';
                                        } else if($report['overlap_status'] === 1){
                                            echo ': </br>'.$report['overlap_details'];
                                        }; ?></p>
                                </td>
                            <!-- </tr>
                            <tr> -->
                                <td class="py-1 pt-2 px-2" style="width:1%;">
                                <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="" <?php if($report['wl_exclusion_status'] === 0){
                                            echo '';
                                        } else if($report['wl_exclusion_status'] === 1){
                                            echo 'checked';
                                        }; ?> disabled/>
                                    </div>
                                </td>
                                <td class="py-1 pt-2 " style="width:37%;">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p>Pengecualian Permohonan Izin Lalu</p>
                                </td>
                            <!-- </tr>
                            <tr> -->
                                <td class="py-1 pt-2 px-2" style="width:1%;">
                                <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value=""
                                        <?php if($report['amend_status'] === 0){
                                            echo '';
                                        } else if($report['amend_status'] === 1){
                                            echo 'checked';
                                        }; ?> disabled/>
                                    </div>
                                </td>
                                <td class="py-1 pt-2" style="width:30%;">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p>Pindaan Cadangan Teknikal</p>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                    <!-- 3.0 Semakan Lokasi -->
                    <table class="table">
                        <thead style="background-color:lightgrey;">
                            <tr>
                                <td colspan="2" class="py-1 px-2" style="font-weight:600; text-align: start; text-transform: uppercase;">
                                    3.0 Semakan Lokasi
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <div class="card card-flush pt-3 mb-3 mt-7 " id="map-<?php echo $report['authority_id'];?>" style="width: 100%; height: 500px;"></div>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td colspan="4" class="p-0 pb-2">
                                    <p class="fw-bold" style="margin: 0.5rem 0 0 0.5rem">Nama Jalan Terlibat :</p>
                                </td>
                            </tr>
                        </tbody>
                        <tbody style="border:1px solid black;">
                            <tr >
                                <td class="p-2" style="width:1%;text-align:center;">
                                    Bil.
                                </td>
                                <td class="p-2" style="width:30%;text-align:center;">
                                    Nama Jalan/Sungai
                                </td>
                                <td class="p-2" style="width:44%;text-align:center;">
                                    Kaedah Teknikal
                                </td>
                                <td class="p-2" style="width:25%;text-align:center;">
                                    Jarak/Nos
                                </td>
                            </tr>
                            <?php
                            $totalLength = 0;
                            foreach($roadList as $index => $list){
                                $number = $index + 1;
                                $totalLength = $totalLength + $list['road_length'];

                                $methodNames = array();
                                foreach($list['method'] as $methodName){
                                    $methodNames[] = $methodName['name'];
                                }
                                $methodString = implode(', ', $methodNames);

                                echo'
                                <tr >
                                    <td class="p-2"  style="text-align:center;">
                                    '.$number.'
                                    </td>
                                    <td class="p-2" >
                                    '.$list['road_name'].'
                                    </td>
                                    <td class="p-2" >
                                    '.$methodString.'
                                    </td>
                                    <td class="p-2" style="text-align:center;">
                                    '.$list['road_length'].'
                                    </td>
                                </tr>
                                ';

                            }
                            echo '
                            <tr >
                            <td class="p-2" colspan="3" >
                            '.'Jumlah Jarak Terlibat'.'
                            </td>
                            <td class="p-2" style="text-align:center;">
                            '.$totalLength.'
                            </td>
                            </tr>';
                            ?>

                        </tbody>
                        </table>
                        <table class="table">
                        <tbody>
                            <tr >
                                <td class="p-2 fw-bold">
                                    Ulasan Kaedah Teknikal :
                                </td>
                            </tr>
                            <tr >
                                <td class="p-2">
                                <?php
                                    foreach ($reviewCol as $index => $textReview) {
                                        $number = $index + 1; // Adding 1 to start numbering from 1

                                        echo '<div class="mb-4">
                                            <!--begin::Item-->
                                            <div class="d-flex align-items-start ps-5 mb-n1">
                                                <!--begin::Bullet-->
                                                <div class="fs-6 me-3">' . $number . '.</div>
                                                <!--end::Bullet-->
                                                <!--begin::Label-->
                                                <div class="fs-6'.($textReview["amend"] && isset($_GET['hl']) ? ' bg-warning' : '').'" '.($textReview["amend"] ? ' data-report-review="hightlight"' : '').'>' . $textReview["description"] . '</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Item-->
                                        </div>';
                                    }
                                ?>

                                </td>
                            </tr>
                        </tbody>

                    </table>

                    <!-- 4.0 Rujukan Bergambar -->
                    <table class="table">
                        <thead style="background-color:lightgrey;" >
                            <tr>
                                <td colspan="2" class="py-1 px-2" style="font-weight:600; text-align: start; text-transform: uppercase;">
                                    4.0 Rujukan Bergambar
                                </td>
                            </tr>
                        </thead>
                    </table>
                    <table class="table">
                        <tbody style="border:1px solid black;">

                                <!-- TODO: Ulasan Body -->
                                <?php
                                $count = count($reviewColPic);
                                for ($i = 0; $i < $count; $i += 2) {
                                    $pic1 = base64_decode($reviewColPic[$i]['url']);
                                    $desc1 = $reviewColPic[$i]['description'];
                                    $highlightClass1 = ($reviewColPic[$i]["amend"] && isset($_GET['hl']) ? ' bg-warning' : '');
                                    $highlightAtt1 = ($reviewColPic[$i]["amend"] ? ' data-report-review="hightlight"' : '');

                                    // Generate unique class name for the first image
                                    $uniqueClass1 = 'report-review-pic-' . ($i + 1);

                                    // Check if there is a second image available
                                    if (($i + 1) < $count) {
                                        $pic2 = base64_decode($reviewColPic[$i + 1]['url']);
                                        $desc2 = $reviewColPic[$i + 1]['description'];
                                        $highlightClass2 = ($reviewColPic[$i + 1]["amend"] && isset($_GET['hl']) ? ' bg-warning' : '');
                                        $highlightAtt2 = ($reviewColPic[$i + 1]["amend"] ? ' data-report-review="hightlight"' : '');

                                        // Generate unique class name for the second image
                                        $uniqueClass2 = 'report-review-pic-' . ($i + 2);
                                    }

                                    // Add the necessary HTML and unique classes for customization
                                    echo '
                                    <tr>
                                    <td class="p-2" style="width:50%">
                                    <div class="position-relative d-flex align-items-center" >
                                        <div class="col-md-12 ' . $uniqueClass1 . ' report-review-pic">
                                            <img src="' . $pic1 . '" alt="Image" class="img-fluid">
                                            <p class="text-gray-600 fw-semibold fs-6' . $highlightClass1 . '" ' . $highlightAtt1 . '>' . $desc1 . '</p>
                                        </div>
                                        </div>
                                    </td>';

                                    if (($i + 1) < $count) {
                                        echo '
                                    <td class="p-2" style="width:50%">
                                        <div class="position-relative d-flex align-items-center" >
                                            <div class="col-md-12 ' . $uniqueClass2 . ' report-review-pic">
                                                <img src="' . $pic2 . '" alt="Image" class="img-fluid">
                                                <p class="text-gray-600 fw-semibold fs-6' . $highlightClass2 . '" ' . $highlightAtt2 . '>' . $desc2 . '</p>
                                            </div>
                                        </div>
                                    </td>
                                    </tr>';
                                    }
                                }
                                ?>
                        </tbody>

                    </table>
                    <!-- 5.0 Pengesahan -->
                    <table class="table" >
                        <thead style="background-color:lightgrey;">
                            <tr>
                                <td colspan="3" class="py-1 px-2" style="font-weight:600; text-align: start; text-transform: uppercase;">
                                    5.0 Pengesahan
                                </td>
                            </tr>
                        </thead>
                        </table>
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="p-2" style="width:33.33%">
                                    Pihak Koridor :
                                </td>
                                <td class="p-2" style="width:33.33%">
                                    Pihak Pemohon :
                                </td>
                                <td class="p-2" style="width:33.33%">
                                    Pihak Berkuasa :
                                </td>
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
                                            src="<?php echo $authoritySignature[0]['signature']; ?>" alt="">
                                    <?php } ?>

                                </td>
                            </tr>
                            <tr>
                                <td class="p-2" >
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
                                            <td class="px-2 pb-1"><?php echo Utilities::convertDateToMalay($corridorSignature[0]['signed_timestamp']); ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-2" >
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
                                            <td class="px-2 pb-1"><?php echo Utilities::convertDateToMalay($applicantSignature[0]['signed_timestamp']); ?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="p-2" >
                                <table>
                                        <tr>
                                            <td style="vertical-align: top;width:20%;">Nama</td>
                                            <td style="vertical-align: top;width:1%;">: </td>
                                            <td class="px-2 pb-1"><?php if ($authoritySignature != null) {
                                                echo $authoritySignature[0]['full_name'];
                                            }?></td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: top;width:20%;">Jawatan</td>
                                            <td style="vertical-align: top;width:1%;">: </td>
                                            <td class="px-2 pb-1"><?php if ($authoritySignature != null) {
                                                echo $authoritySignature[0]['position'];
                                            }?></td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align: top;width:20%;">Tarikh</td>
                                            <td style="vertical-align: top;width:1%;">: </td>
                                            <td class="px-2 pb-1"><?php if ($authoritySignature != null) {
                                                echo Utilities::convertDateToMalay($authoritySignature[0]['signed_timestamp']);
                                            }?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>



                        </tbody>

                    </table>


                </div>

                <!--end::Card body-->


            <!--end::Customer-->

        </form>
        <!--end::Form-->
    </div>
    <!--end::Content-->
</div>
<div class="card card-flush pt-3 mb-5 mb-lg-10">
<div class="flex-lg-row-fluid mx-lg-10 order-2 order-lg-1 mb-10 mb-lg-0">
                <!--begin::Card body-->
                <div class="card-body">
                    <div style="margin-bottom: 1rem; ">
                        <table class="table" style="border-collapse: collapse;">
                            <thead>
                                <tr>

                                    <td style="width: 80%;">
                                        <h2 style="text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                            BORANG KEHADIRAN LAWATAN TAPAK BERSAMA
                                        </h2>
                                    </td>
                                    <td style="width: 20%; text-align: center;">
                                        <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                                            src="assets/media/logos/<?php echo strtolower($system->App->tenant) ?>-default.svg">
                                    </td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <table class="table table-bordered" style="border-collapse: collapse;border:1px solid black;">
                        <tbody>
                            <tr>
                                <td  style="width: 25%; text-align: center;">
                                    NAMA PROJEK
                                </td>
                                <td colspan="3" class="ps-2" style="width: 75%; text-align: left;text-transform: uppercase;">
                                <?php echo $report['project_title']; ?>
                                </td>
                            </tr>
                            <tr  >
                                <td style=" text-align: center;">
                                    NO RUJUKAN
                                </td>
                                <td colspan="3" class="ps-2" style="text-align: left;">
                                    <?php echo $report['reference_no']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%; text-align: center;">
                                    TARIKH
                                </td>
                                <td  style="width: 25%; text-align: center;">
                                    <?php echo Utilities::convertDateToMalay($report['actual_sv_date']); ?>
                                </td>
                                <td  style="width: 25%; text-align: center;">
                                    WAKTU
                                </td>
                                <td  style="width: 25%; text-align: center;">
                                    <?php echo Report::formatTimeToMalayTimeString($report['actual_sv_date']); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered" style="border-collapse: collapse;border:1px solid black;">
                        <thead>
                            <tr>
                                <td  style="width: 5%; text-align: center;">
                                    BIL.
                                </td>
                                <td style=" text-align: center;">
                                    NAMA
                                </td>
                                <td style="width: 20%; text-align: center;">
                                    AGENSI/SYARIKAT
                                </td>
                                <td style="width: 20%; text-align: center;">
                                    JAWATAN
                                </td>
                                <td style="width: 20%; text-align: center;">
                                    TANDATANGAN
                                </td>
                            </tr>

                        <?php
                            foreach($guestSignature as $index => $guest){
                                $number = $index + 1;

                                $methodNames = array();
                                foreach($list['method'] as $methodName){
                                    $methodNames[] = $methodName['name'];
                                }
                                $methodString = implode(', ', $methodNames);

                                echo '
                            <tr>
                                <td class="p-2" style="text-align:center;">' . $number . '</td>
                                <td class="p-2" style="text-align:left;">' . $guest['full_name'] . '</td>
                                <td class="p-2" style="text-align:left;">' . $guest['company_name'] . '</td>
                                <td class="p-2" style="text-align:center;">' . $guest['position'] . '</td>
                                <td class="p-2" style="text-align:center;height:25px;overflow: visible;vertical-align: middle;">';

                        // Check if $guest['signature'] is not empty
                        if ($guest['signature'] != '') {
                            echo '<img style="max-height:40px; max-width:80%;" src="' . $guest['signature'] . '" alt="Image">';
                        }

                        echo '
                                    </td>
                                </tr>
                                ';

                            }
                            ?>
                        </thead>
                    </table>
                </div>
                        </div>
            </div>
<?php
}
?>
<!-- end::reportCard -->