<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// TODO: change all these into functions after this !!!
include "config/system.php";

$systemId = $_GET['sid'];
$reportId = $_GET['r'];
$authId = $_GET['a'];

//data dari entry on report
$currentReport = array();
$currentReport = Report::getReportDetails($systemId, $reportId, $authId);

// print_r($currentReport);

$referenceNo = $currentReport['reference_no'];

$details = new ProjectDetails();
$currentProject = $details->details($systemId);

if (count($currentProject) > 0) {
    $currentProject = $currentProject[0];
}

// var_dump($currentProject);

//data ulasan based on geom
$reviewCol = array();
$reviewCol = Report::getTextReview($referenceNo, $reportId, $systemId, $authId);

// print_r($reviewCol);

$reviewColPic = array();
$reviewColPic = Report::getImgReview($referenceNo, $reportId, $systemId, $authId);
// print_r($reviewColPic);
// $signType = 3;
// $signatureValidation = array();
$corridorSignature = Report::getSignatureValidation($systemId, $reportId, $authId, 1);
$applicantSignature = Report::getSignatureValidation($systemId, $reportId, $authId, 2);
$authoritySignature = Report::getSignatureValidation($systemId, $reportId, $authId, 3);
$guestSignature = Report::getSignatureValidation($systemId, $reportId, $authId, 4);

// print_r($guestSignature);
// $decodedData = base64_decode($signatureValidation[0]['signature']);
// print_r($decodedData);
$roadList = Report::getRoadList($systemId, $reportId, $authId);
// print_r($roadList);

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
                <!--begin::Card body-->
                <div class="card-body">
                    <!-- header report -->
                    <div style="margin-bottom: 1rem; ">
                        <table style="border-collapse: collapse;">
                            <thead>
                                <tr style="border: 1px solid;">
                                    <td rowspan="2" style="width: 10%; text-align: center; border: 1px solid;">
                                        <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                                            src="assets/media/logos/<?php echo $tenant ?>-default.svg">
                                    </td>
                                    <td>
                                        <h2 style=" text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                            <!-- Koridor Utiliti Teknologi Terengganu Sdn. Bhd -->
                                            <?php echo $tenant . ' Sdn. Bhd'?>
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
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted; text-transform: uppercase;"><?php echo $currentReport['project_title']; ?></p>
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
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $currentReport['reference_no']; ?></p>
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
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo Report::formatDateAndTimeToMalayDateTimeString($currentReport['actual_sv_date']); ?></p>
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
                                    <?php $districts = explode(',', $currentReport['project_district_name']);

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
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $currentReport['provider_shorthand']; ?></p>
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
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $currentReport['provider_name']; ?></p>
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
                                    <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $currentReport['provider_name']; ?></p>
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
                                        <?php if($currentReport['overlap_status'] === 0){
                                            echo '';
                                        } else if($currentReport['overlap_status'] === 1){
                                            echo 'checked';
                                        }; ?> disabled/>
                                    </div>
                                </td>
                                <td class="py-1 pt-2" style="width:30%;">
                                    <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                                    <p>Pertindihan Projek <?php if($currentReport['overlap_status'] === 0){
                                            echo '';
                                        } else if($currentReport['overlap_status'] === 1){
                                            echo ': </br>'.$currentReport['overlap_details'];
                                        }; ?></p>
                                </td>
                            <!-- </tr>
                            <tr> -->
                                <td class="py-1 pt-2 px-2" style="width:1%;">
                                <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="" <?php if($currentReport['wl_exclusion_status'] === 0){
                                            echo '';
                                        } else if($currentReport['wl_exclusion_status'] === 1){
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
                                        <?php if($currentReport['amend_status'] === 0){
                                            echo '';
                                        } else if($currentReport['amend_status'] === 1){
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
                    <div class="card card-flush pt-3 mb-3 mt-7 " id="map" style="width: 100%; height: 500px;"></div>
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
                            foreach($roadList as $index => $list){
                                $number = $index + 1;

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
                                                <div class="fs-6">' . $textReview["description"] . '</div>
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
                                    <tr>
                                    <td class="p-2" style="width:50%">
                                    <div class="position-relative d-flex align-items-center" >
                                        <div class="col-md-12 ' . $uniqueClass1 . ' report-review-pic">
                                            <img src="' . $pic1 . '" alt="Image" class="img-fluid">
                                            <p class="text-gray-600 fw-semibold fs-6">' . $desc1 . '</p>
                                        </div>
                                        </div>
                                    </td>';

                                    if (($i + 1) < $count) {
                                        echo '
                                    <td class="p-2" style="width:50%">
                                        <div class="position-relative d-flex align-items-center" >
                                            <div class="col-md-12 ' . $uniqueClass2 . ' report-review-pic">
                                                <img src="' . $pic2 . '" alt="Image" class="img-fluid">
                                                <p class="text-gray-600 fw-semibold fs-6">' . $desc2 . '</p>
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
                                <img class="report-approval-sign" style="max-height:120px; max-width:80%;" data-report-signature="signature-img" src="<?php echo $corridorSignature[0]['signature']; ?>" alt="Image">

                                </td >
                                <td style="height:120px;text-align: center;vertical-align: middle;">
                                <img class="report-approval-sign" style="max-height:120px; max-width:80%;" data-report-signature="signature-img" src="<?php echo $applicantSignature[0]['signature']; ?>" alt="Image">

                                </td>
                                <td style="height:120px;text-align: center;vertical-align: middle;">
                                    <?php if ($authoritySignature != null) { ?>
                                <img class="report-approval-sign" style="max-height:120px; max-width:80%;" data-report-signature="signature-img" src="<?php echo $authoritySignature[0]['signature']; ?>" alt="">
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
                                            <td class="px-2 pb-1"><?php echo Report::convertDateToMalay($corridorSignature[0]['signed_timestamp']); ?></td>
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
                                            <td class="px-2 pb-1"><?php echo Report::convertDateToMalay($applicantSignature[0]['signed_timestamp']); ?></td>
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
                                                echo Report::convertDateToMalay($authoritySignature[0]['signed_timestamp']);
                                            }?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>



                        </tbody>

                    </table>


                </div>

                <!--end::Card body-->
            </div>
            <div class="card card-flush pt-3 mb-5 mb-lg-10">
                <!--begin::Card body-->
                <div class="card-body">
                    <div style="margin-bottom: 1rem; ">
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
                                            src="assets/media/logos/<?php echo $tenant ?>-default.svg">
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
                                <?php echo $currentReport['project_title']; ?>
                                </td>
                            </tr>
                            <tr  >
                                <td style=" text-align: center;">
                                    NO RUJUKAN
                                </td>
                                <td colspan="3" class="ps-2" style="text-align: left;">
                                    <?php echo $currentReport['reference_no']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%; text-align: center;">
                                    TARIKH
                                </td>
                                <td  style="width: 25%; text-align: center;">
                                    <?php echo Report::convertDateToMalay($currentReport['actual_sv_date']); ?>
                                </td>
                                <td  style="width: 25%; text-align: center;">
                                    WAKTU
                                </td>
                                <td  style="width: 25%; text-align: center;">
                                    <?php echo Report::formatTimeToMalayTimeString($currentReport['actual_sv_date']); ?>
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

                                echo'
                                <tr >
                                    <td class="p-2"  style="text-align:center;">
                                    '.$number.'
                                    </td>
                                    <td class="p-2" >
                                    '.$guest['full_name'].'
                                    </td>
                                    <td class="p-2" >
                                    '.$guest['company_name'].'
                                    </td>
                                    <td class="p-2" style="text-align:center;">
                                    '.$guest['position'].'
                                    </td>
                                    <td class="p-2" style="text-align:center;height:25px;overflow: visible;">
                                    <img style="height:40px;margin: -15px;" src="'.$guest['signature'].'" alt="Image">
                                    </td>
                                </tr>
                                ';

                            }
                            ?>
                        </thead>
                    </table>
                </div>
            </div>
            <!--end::Customer-->
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
                <h3 class="card-title fw-bold"><?php echo $currentReport['report_type']; ?></h3>
                <div class="card-toolbar">
                <a href="/projects/prints/LLT/<?php echo $systemId.'?r='.$reportId.'&a='.$authId; ?>" class="btn btn-icon btn-sm btn-light">
                    <i class="fad fa-print fs-3"></i>
                </a>
                </div>
            </div>
            <!--end::Card header-->
            <!--begin::Card body-->
            <div class="card-body pt-0 fs-6">
                <!--begin::Section-->
                <div class="mb-7">
                    <!--begin::Title-->
                    <h5>No Rujukan Projek</h5>
                    <!--end::Title-->
                    <!--begin::Details-->
                    <div class="d-flex align-items-center mb-1">
                        <!--begin::Name-->
                        <a href="/projects/details.php?sid=<?php echo $systemId; ?>" target="_blank" class="fw-bold text-gray-800 text-hover-primary me-2"><?php echo $currentReport['reference_no'] ?></a>
                        <!--end::Name-->
                        <!--begin::Status-->
                        <!--end::Status-->
                    </div>
                    <!--end::Details-->
                    <!--begin::Email-->
                    <span class="badge badge-light-<?php echo $currentProject['status_color'] ?>"><?php echo $currentProject['status'] ?></span>
                    <!--end::Email-->
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
                            <input class="form-check-input" type="checkbox" value="" id="wl-exclusion" />
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
                    <h5 class="mb-3">Pertindihan & Pindaan</h5>
                    <!--end::Title-->
                    <!--begin::Details-->
                    <div class="mb-0">
                        <div class="form-check form-switch form-check-custom form-check-solid mb-2">
                            <input class="form-check-input" type="checkbox" value="" id="overlapping" />
                            <label class="form-check-label" for="overlapping">
                                Pertindihan
                            </label>
                        </div>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="" id="amend-plan" />
                            <label class="form-check-label" for="amend-plan">
                                Pindaan
                            </label>
                        </div>
                    </div>
                    <!--end::Details-->
                </div>
                <!--end::Section-->
                <!--begin::Actions-->
                <button type="submit" class="btn btn-block btn-primary mb-3 w-100" id="report-submit">
                    <!--begin::Indicator label-->
                    <span class="indicator-label">Pengesahan Laporan</span>
                    <!--end::Indicator label-->
                    <!--begin::Indicator progress-->
                    <span class="indicator-progress">Sila Tunggu...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    <!--end::Indicator progress-->
                </button>
                <a href="/reports/site/view/<?php echo $systemId.'?r='.$reportId.'&a='.$authId; ?>" class="btn btn-block btn-secondary w-100" id="report-review">
                    <!--begin::Indicator label-->
                    <span class="indicator-label">Lihat Laporan</span>
                    <!--end::Indicator label-->
                </a>
                <!--end::Actions-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Sidebar-->

</div>
<!--end::Layout-->