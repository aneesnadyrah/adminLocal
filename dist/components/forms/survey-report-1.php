<?php
$systemId = $_GET['sid'];
$p = Survey::listProvider($systemId);
$d = Survey::surveyLogBookUdm($systemId);
$surveyImageData = Survey::getSurveyImageData($systemId);
$surveyLength = Survey::getSurveyLength($systemId);
$start = Survey::getStartCoordinates($systemId);
$end = Survey::getEndCoordinates($systemId);
$dateRange = Survey::getStartAndEndDates($systemId);

// Access the earliest and latest dates
$earliestDate = $dateRange['earliest_date'];
$latestDate = $dateRange['latest_date'];

$surveyWorkData = Survey::getAllSurveyWorkData($systemId);
$equipmentData = Survey::getAllEquipmentData($systemId);

if (isset($d[0]['survey_date'])) {
    $surveyDate = $d[0]['survey_date'];
    $dateTime = !empty($surveyDate) ? new DateTime($surveyDate) : null;
    $date = $dateTime !== null ? Survey::formatMalayDate($dateTime->format('d M Y')) : '';
} else {
    $date = ''; // Set a default value if the key is not present
}
?>

<div class="letter">
<section class="sheet padding-10mm">
    <article>
        <!--begin::Form-->
        <form action="" id="form-report-review" class="print-content-only">
            <!--begin::Wrapper-->
            <div class="d-flex justify-content-center">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column align-items-start flex-xxl-row mt-10 mb-20 ms-10 me-5">
                    <!--begin::Input group-->
                    <div class="d-flex align-items-center justify-content-start flex-equal order-3 fw-row mt-10 ms-10">
                        <!--begin::Input-->
                        <div class="position-relative d-flex align-items-center w-250px h-100px">
                            <img class="mw-300px" src="assets/media/logos/KUP-default.svg" alt="image" />
                        </div>
                        <!--end::Input-->

                        <div style="width: 180px;"></div>
                        <!-- Add a div with a specific width to create space -->

                        <!--begin::Input-->
                        <div class="position-relative d-flex align-items-center w-200px me-10">
                            <img class="mw-150px" src="assets/media/provider/<?php echo $p[0]['ProviderID']; ?>.webp" />
                        </div>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Separator-->
            <!-- <div class="separator border-dark fw-bold my-5"></div> -->
            <!--end::Separator-->

            <!--begin::Wrapper-->
            <div class="d-flex justify-content-center align-items-center mb-15">
                <!--begin::Wrapper-->
                <div class="mb-0">
                    <!--begin::Wrapper-->
                    <div class="d-flex flex-stack flex-column justify-content-center align-items-center flex-xxl-row mb-10 fw-row">
                        <!--begin::Section-->
                        <div class="mw-500px">
                            <!--begin::Item-->
                            <div class="d-flex flex-stack mb-10 justify-content-center">
                                <div class="fw-bold fs-1">LAPORAN KERJA LAPANGAN
                                </div>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex flex-stack mb-1  justify-content-center text-center">
                                <div class="fs-5 ">NO. RUJUKAN</div>
                                <div class="fs-5 px-5">:</div>
                                <div class="position-relative d-flex align-items-center fs-5">
                                    <?php echo $p[0]['RefNo']; ?>
                                </div>
                            </div>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <div class="d-flex flex-stack  justify-content-center text-center">
                                <div class="fs-5 ">TARIKH DIUKUR</div>
                                <div class="fs-5 px-5">:</div>
                                <div class="position-relative d-flex align-items-center fs-5" style="text-transform: uppercase;">
                                    <?php echo $date; ?>
                                </div>
                            </div>
                            <!--end::Item-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Wrapper-->
            <!-- <div class="d-flex flex-column justify-content-center align-items-center flex-xxl-row mb-20">
                <div class="table-responsive">
                <table class="table table-bordered border-gray-800">
                    <tr>
                        <td class="d-flex align-items-center justify-content-center" style="width: 500px;">
                            <div class="position-relative d-flex align-items-center">
                                <img class="mw-450px h-350px" src="assets/media/logos/defaultt.svg" alt="image" />
                            </div>
                        </td>
                    </tr>
                </table>
                </div>
            </div> -->
            <!--end::Wrapper-->

            <div class="table-responsive mt-10 mb-10">
                <table class="table table-bordered border-gray-800 gy-4 gs-4">
                    <tbody>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">TAJUK PERMOHONAN</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                <?php echo $p[0]['project_title']; ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">JALAN TERLIBAT</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                <?php
                                $roadNames = array();
                                foreach ($p as $item) {
                                    $roadNames[] = $item['road_name'];
                                }

                                // Print all road names
                                $count = count($roadNames);
                                for ($i = 0; $i < $count; $i++) {
                                    echo $roadNames[$i];
                                    if ($i < $count - 1) {
                                        echo " - ";
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">TARIKH MULA/TARIKH TAMAT</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                MULA:
                                <?php echo $earliestDate; ?><br>

                                <?php
                                // Printing the value of $jarakUkur
                                echo "TAMAT: " . $latestDate . "<br>";
                                ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">JARAK MOHON/JARAK DIUKUR</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                JARAK MOHON:
                                <?php echo $p[0]['application_length']; ?> M<br>

                                <?php
                                // Printing the value of $jarakUkur
                                echo "JARAK DIUKUR: " . $surveyLength . " M<br>";
                                ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">KOORDINAT
                                MULA/KOORDINAT AKHIR</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                KOORDINAT MULA:
                                <?php echo $start['latitude_start']; ?>,
                                <?php echo $start['longitude_start']; ?> <br>
                                KOORDINAT AKHIR:
                                <?php echo $end['latitude_end']; ?>,
                                <?php echo $end['longitude_end']; ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">KERJA-KERJA YANG
                                DILAKUKAN DI LAPANGAN</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                <?php
                                if (is_array($surveyWorkData)) {
                                    echo implode('<br>', $surveyWorkData);
                                } else {
                                    echo $surveyWorkData;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">PERALATAN KERJA YANG DIGUNAKAN</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                <?php
                                if (is_array($equipmentData)) {
                                    echo implode('<br>', $equipmentData);http://127.0.0.1:51353/browser/#
                                } else {
                                    echo $equipmentData;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="fw-semibold fs-6 text-start ms-2">
                            <td style="text-transform: uppercase;">SENARAI ANGGOTA YANG
                                TERLIBAT</td>
                            <td style="text-transform: uppercase; width: 70%;">
                                AHLI KUMPULAN
                                <?php echo $d[0]['survey_team']; ?><br><br>
                                <?php echo Survey::generateNumberedNames($d[0]['team_members'] ?? null); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
        <!--end::Form-->
    </article>                                
</section>

<section class="sheet padding-10mm">
    <article>
        <!--begin::Form-->
        <form action="" id="form-report-review" class="print-content-only">
            <!--begin::title-->
            <?php
            // Filter the survey image data for category = 1
            $filteredImageData = array_filter($surveyImageData, function ($imageData) {
                return $imageData['category'] == 1;
            });

            $filteredImageData = array_values($filteredImageData);

            // Count the number of filtered rows
            $rowCount = count($filteredImageData);

            if (!empty($filteredImageData)) : ?>
                <div class="row gx-20 mb-5">
                    <span class="fw-bold fs-4 mb-5" style="text-transform:uppercase;">Kerja-kerja yang dilakukan di lapangan</span>
                </div>
            <?php endif; ?>
            <!--end::title-->

            <!--begin::content-->
            <div class="table-responsive mb-10">
                <table class="table table-bordered border-gray-800 gy-4 gs-4">
                    <tbody>
                    <?php
                    // Filter the survey image data for category = 1
                    $filteredImageData = array_filter($surveyImageData, function ($imageData) {
                        return $imageData['category'] == 1;
                    });

                    $filteredImageData = array_values($filteredImageData);

                    // Count the number of filtered rows
                    $rowCount = count($filteredImageData);

                    // Loop through the survey image data
                    for ($i = 0; $i < $rowCount; $i += 2) :
                        $imageData1 = $filteredImageData[$i];
                        $imageData2 = isset($filteredImageData[$i + 1]) ? $filteredImageData[$i + 1] : null;
                    ?>
                        <tr class="fw-semibold fs-6">
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Center the content -->
                                    <!--begin::Input group-->
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!--begin::Input-->
                                        <div class="position-relative d-flex align-items-center" style="height: 300px; width: 330px;">
                                            <img class="image-fit" src="<?php echo $imageData1['url']; ?>" alt="image" style="width: 100%; height: 100%; object-fit: fill;" />
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Center the content -->
                                    <!--begin::Input group-->
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!--begin::Input-->
                                        <div class="position-relative d-flex align-items-center" style="height: 300px; width: 330px;">
                                            <?php if ($imageData2) : ?>
                                                <img class="image-fit" src="<?php echo $imageData2['url']; ?>" alt="image" style="width: 100%; height: 100%; object-fit: fill;" />
                                            <?php endif; ?>
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><?php echo $imageData1['description']; ?></td>
                            <td class="text-center"><?php echo $imageData2 ? $imageData2['description'] : ''; ?></td>
                        </tr>
                    <?php
                    endfor;
                    ?>   
                    </tbody>
                </table>
            </div>
            <!--end::content-->
        </form>
        <!--end::Form-->
    </article>                                
</section>

<section class="sheet padding-10mm">
    <article>
        <!--begin::Form-->
        <form action="" id="form-report-review" class="print-content-only">
            <!--begin::title-->
            <?php 
            // Filter the survey image data for category = 1
            $filteredImageData2 = array_filter($surveyImageData, function ($imageData22) {
                return $imageData22['category'] == 2;
            });

            $filteredImageData2 = array_values($filteredImageData2);

            // Count the number of filtered rows
            $rowCount = count($filteredImageData2);
            
            if (!empty($filteredImageData2)) : ?>
                <div class="row gx-20 mb-5">
                    <span class="fw-bold fs-4 mb-5" style="text-transform:uppercase;">Kerja-kerja pengukuran dan penandaan chainage sedang dijalankan</span>
                </div>
            <?php endif; ?>
            <!--end::title-->

            <!--begin::content-->
            <div class="table-responsive mb-10">
                <table class="table table-bordered border-gray-800 gy-4 gs-4">
                    <tbody>
                    <?php
                    // Filter the survey image data for category = 1
                    $filteredImageData2 = array_filter($surveyImageData, function ($imageData22) {
                        return $imageData22['category'] == 2;
                    });

                    $filteredImageData2 = array_values($filteredImageData2);

                    // Count the number of filtered rows
                    $rowCount = count($filteredImageData2);

                    // Loop through the survey image data
                    for ($i = 0; $i < $rowCount; $i += 2) :
                        $imageData3 = $filteredImageData2[$i];
                        $imageData4 = isset($filteredImageData2[$i + 1]) ? $filteredImageData2[$i + 1] : null;
                    ?>
                        <tr class="fw-semibold fs-6">
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Center the content -->
                                    <!--begin::Input group-->
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!--begin::Input-->
                                        <div class="position-relative d-flex align-items-center" style="height: 300px; width: 330px;">
                                            <img class="image-fit" src="<?php echo $imageData3['url']; ?>" alt="image" style="width: 100%; height: 100%; object-fit: fill;" />
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Center the content -->
                                    <!--begin::Input group-->
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!--begin::Input-->
                                        <div class="position-relative d-flex align-items-center" style="height: 300px; width: 330px;">
                                            <?php if ($imageData4) : ?>
                                                <img class="image-fit" src="<?php echo $imageData4['url']; ?>" alt="image" style="width: 100%; height: 100%; object-fit: fill;" />
                                            <?php endif; ?>
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center"><?php echo $imageData3['description']; ?></td>
                            <td class="text-center"><?php echo $imageData4 ? $imageData4['description'] : ''; ?></td>
                        </tr>
                    <?php
                    endfor;
                    ?>   
                    </tbody>
                </table>
            </div>
            <!--end::content-->
        </form>
        <!--end::Form-->
    </article>                                
</section>

<section class="sheet padding-10mm">
    <article>
        <!--begin::Form-->
        <form action="" id="form-report-review" class="print-content-only">
            <!--begin::title-->
            <?php 
            // Filter the survey image data for category = 1
            $filteredImageData3 = array_filter($surveyImageData, function ($imageData33) {
                return $imageData33['category'] == 3;
            });

            $filteredImageData3 = array_values($filteredImageData3);

            // Count the number of filtered rows
            $rowCount = count($filteredImageData3);
            
            if (!empty($filteredImageData3)) : ?>
            <div class="row gx-20 mb-5">
                <span class="fw-bold fs-4 mb-5" style="text-transform:uppercase;">Kerja-kerja penandaan pegging
                    'right of way' row(diwarnakan <span class="text-danger">merah</span>) dan pegging propose
                    (diwarnakan <span class="text-primary">biru</span>). paip pegging
                    diletakkan selari dengan chainage yang ditanda.</span>
            </div>
            <?php endif; ?>
            <!--end::title-->

            <!--begin::content-->
            <div class="table-responsive mb-10">
                <table class="table table-bordered border-gray-800 gy-4 gs-4">
                    <tbody>
                    <?php
                    // Filter the survey image data for category = 1
                    $filteredImageData3 = array_filter($surveyImageData, function ($imageData33) {
                        return $imageData33['category'] == 3;
                    });

                    $filteredImageData3 = array_values($filteredImageData3);

                    // Count the number of filtered rows
                    $rowCount = count($filteredImageData3);

                    // Loop through the survey image data
                    for ($i = 0; $i < $rowCount; $i += 2) :
                        $imageData5 = $filteredImageData3[$i];
                        $imageData6 = isset($filteredImageData3[$i + 1]) ? $filteredImageData3[$i + 1] : null;
                    ?>
                        <tr class="fw-semibold fs-6">
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Center the content -->
                                    <!--begin::Input group-->
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!--begin::Input-->
                                        <div class="position-relative d-flex align-items-center" style="height: 300px; width: 330px;">
                                            <img class="image-fit" src="<?php echo $imageData5['url']; ?>" alt="image" style="width: 100%; height: 100%; object-fit: fill;" />
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Center the content -->
                                    <!--begin::Input group-->
                                    <div class="d-flex align-items-center justify-content-center">
                                        <!--begin::Input-->
                                        <div class="position-relative d-flex align-items-center" style="height: 300px; width: 330px;">
                                            <?php if ($imageData6) : ?>
                                                <img class="image-fit" src="<?php echo $imageData6['url']; ?>" alt="image" style="width: 100%; height: 100%; object-fit: fill;" />
                                            <?php endif; ?>
                                        </div>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                            </td>
                        </tr>
                    <?php
                    endfor;
                    ?>   
                    </tbody>
                </table>
            </div>
            <!--end::content-->

            <div class="table-responsive mb-10">
                <table class="table table-rounded table-row-bordered table-striped border-gray-800 gy-4 gs-4">
                    <tbody class="table-striped">
                        <tr class="fw-semibold fs-6">
                            <td style="text-transform: uppercase; width: 50%;">
                                disediakan oleh</td>
                            <td style="text-transform: uppercase; width: 50%;">
                                disemak oleh</td>
                        </tr>
                        <tr class="fw-semibold fs-6">
                            <td style="text-transform: uppercase; width: 50%;"></td>
                            <td style="text-transform: uppercase; width: 50%;"></td>
                        </tr>
                        <tr class="fw-semibold fs-6">
                            <td style="text-transform: uppercase; width: 50%;"></td>
                            <td style="text-transform: uppercase; width: 50%;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <input type="text" id="system-id" value="<?php echo $systemId ?>" hidden>
            <!--end::Wrapper-->
        </form>
        <!--end::Form-->
    </article>                                
</section>
</div>

