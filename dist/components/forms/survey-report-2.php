
<?php
$systemId = $_GET['sid'];
$p = Survey::listProvider($systemId);
$d = Survey::surveyLogBookUdm($systemId);
$surveyImageData = Survey::getSurveyImageData($systemId);
$start = Survey::getStartCoordinates($systemId);
$end = Survey::getEndCoordinates($systemId);
$dateRange = Survey::getStartAndEndDates($systemId);

// Access the earliest and latest dates
$earliestDate = $dateRange['earliest_date'];
$latestDate = $dateRange['latest_date'];

// Assuming you have called the function to retrieve all rows with the given system_id
$surveyWorkDataArray = Survey::getAllSurveyWorkData($systemId);

// Combine the survey work data into a single string with line breaks
$combinedSurveyWork = implode("\n", $surveyWorkDataArray);

// Assuming you have called the function to retrieve all rows with the given system_id
$equipmentDataArray = Survey::getAllEquipmentData($systemId);

// Combine the equipment data into a single string with line breaks
$combinedEquipment = implode("\n", $equipmentDataArray);

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
        <!-- title -->
        <div style="margin-bottom: 1rem; ">
            <table style="border-collapse: collapse;">
                <thead>
                    <tr style="border: 1px solid;">
                        <td rowspan="2" style="width: 10%; text-align: center; border: 1px solid;">
                            <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                                src="assets/media/logos/<?php echo $tenant ?>-default.svg">
                        </td>
                        <td>
                            <h2 style="font-weight: 700; text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                <!-- Koridor Utiliti Teknologi Terengganu Sdn. Bhd -->
                                <?php echo $tenant . ' Sdn. Bhd'?>
                            </h2>
                        </td>
                    </tr>
                    <tr style="border: 1px solid;" >
                        <td>
                            <h2 style="font-weight: 700; text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                Laporan Kerja Lapangan
                            </h2>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>

        <table class="table">
            <thead style="background-color:lightgrey;">
                <tr>
                    <th class="p-1" style="vertical-align: top; width:1px;"><h5 style="font-weight:600; margin: 0.5rem 0 0.5rem 0.5rem; text-transform: uppercase;">1.0</h5></th>
                    <th class="p-1">
                        <h5 style="font-weight:600; text-align: start; margin: 0.5rem 0.5rem 0.5rem 0; text-transform: uppercase;">Butiran Projek</h5>
                    </th>
                </tr>
            </thead>       
        </table>
        <table class="table">
            <tbody>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1"style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">Nombor Rujukan</p>
                    </td>
                    <td class="py-1"style="width:1px; ">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $p[0]['RefNo']; ?></p>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1"style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">TARIKH DIUKUR</p>
                    </td>
                    <td class="py-1"style="width:1px; ">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $date; ?></p>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1"style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">TAJUK PERMOHONAN</p>
                    </td>
                    <td class="py-1"style="width:1px; ">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $p[0]['project_title']; ?></p>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1"style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">JALAN TERLIBAT</p>
                    </td>
                    <td class="py-1"style="width:1px; ">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
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
                        </p>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1" style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">TARIKH MULA/TARIKH TAMAT</p>
                    </td>
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
                    <td class="py-1" style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">JARAK MOHON/JARAK DIUKUR</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 0;">
                            <p style="border-bottom: 2px dotted;">JARAK MOHON: <?php echo $p[0]['application_length']; ?> M</p>
                            <?php
                            // Printing the value of $jarakMohon
                            $jarakMohon = floatval($p[0]['application_length']); // Convert to float

                            // Printing the value of $progressPending
                            $progressPending = isset($d[0]['progress_pending']) ? floatval($d[0]['progress_pending']) : 0.0; // Convert to float

                            // Calculate $jarakUkur
                            $jarakUkur = intval($jarakMohon - $progressPending); // Convert the result back to integer

                            // Printing the value of $jarakUkur
                            echo '<p class="py-1" style="border-bottom: 2px dotted;">JARAK DIUKUR: ' . $jarakUkur . ' M</p>';
                            ?>
                        </p>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1" style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">KOORDINAT MULA/KOORDINAT AKHIR</p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p class="py-1" style="border-bottom: 2px dotted;">
                                KOORDINAT MULA:
                                <?php echo $start['latitude_start']; ?>,
                                <?php echo $start['longitude_start']; ?>
                        </p>
                        <p class="py-1" style="border-bottom: 2px dotted;">
                                KOORDINAT AKHIR:
                                <?php echo $end['latitude_end']; ?>,
                                <?php echo $end['longitude_end']; ?>
                        </p>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1" style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">
                            KERJA-KERJA YANG DILAKUKAN DI LAPANGAN
                        </p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <?php
                        $surveyWorkLines = explode("\n", $combinedSurveyWork);
                        foreach ($surveyWorkLines as $line) {
                            if (!empty($line)) {
                                echo '<p class="py-1" style="border-bottom: 2px dotted">' . $line . '</p>';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1" style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">
                            PERALATAN KERJA YANG DIGUNAKAN
                        </p>
                    </td>
                    <td class="py-1" style="width:1px;">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <?php
                        $equipmentLines = explode("\n", $combinedEquipment);
                        foreach ($equipmentLines as $line) {
                            if (!empty($line)) {
                                echo '<p class="py-1" style="border-bottom: 2px dotted">' . $line . '</p>';
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr class="fw-semibold fs-6 text-start ms-2">
                    <td class="py-1"style="width:250px; text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 1rem">
                            SENARAI ANGGOTA YANG TERLIBAT
                        </p>
                    </td>
                    <td class="py-1"style="width:1px; ">
                        <p class="mt-2" style="text-align:center;">:</p>
                    </td>
                    <td class="py-1" style="text-transform: uppercase;">
                        <p style="margin: 0.5rem 0 0 0;">
                            AHLI KUMPULAN
                            <?php echo $d[0]['survey_team']; ?><br><br>
                            <?php echo Survey::generateNumberedNames($d[0]['team_members'] ?? null); ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="margin-top: 1rem;"></div>
    </article>
</section>

<section class="sheet padding-10mm">
    <article>
        <!--begin::Form-->
        <form action="" id="form-report-review">
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
                <table class="my-5" style="margin-bottom: 1rem; ">
                    <thead style="background-color:lightgrey;">
                        <tr>
                            <th class="p-1" style="vertical-align: top; width:1px;"><h5 style="font-weight:600; margin: 0.5rem 0 0.5rem 0.5rem; text-transform: uppercase;">2.0</h5></th>
                            <th class="p-1">
                                <h5 style="font-weight:600; text-align: start; margin: 0.5rem 0.5rem 0.5rem 0; text-transform: uppercase;">Kerja-kerja yang dilakukan di lapangan</h5>
                            </th>
                        </tr>
                    </thead>       
                </table>
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
                                <div class="d-flex justify-content-center image-container">
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
                                <div class="d-flex justify-content-center image-container">
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
        <form action="" id="form-report-review">
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
                <table class="my-5" style="margin-bottom: 1rem; ">
                    <thead style="background-color:lightgrey;">
                        <tr>
                            <th class="p-1" style="vertical-align: top; width:1px;"><h5 style="font-weight:600; margin: 0.5rem 0 0.5rem 0.5rem; text-transform: uppercase;">3.0</h5></th>
                            <th class="p-1">
                                <h5 style="font-weight:600; text-align: start; margin: 0.5rem 0.5rem 0.5rem 0; text-transform: uppercase;">Kerja-kerja Pengukuran dan Penandaan Chainage sedang Dijalankan</h5>
                            </th>
                        </tr>
                    </thead>       
                </table>
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
                                <div class="d-flex justify-content-center image-container">
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
                                <div class="d-flex justify-content-center image-container">
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
        <form action="" id="form-report-review">
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
                <table class="my-5" style="margin-bottom: 1rem; ">
                    <thead style="background-color:lightgrey;">
                        <tr>
                            <th class="p-1" style="vertical-align: top; width:1px;"><h5 style="font-weight:600; margin: 0.5rem 0 0.5rem 0.5rem; text-transform: uppercase;">4.0</h5></th>
                            <th class="p-1">
                                <h5 style="font-weight:600; text-align: start; margin: 0.5rem 0.5rem 0.5rem 0; text-transform: uppercase;">
                                    Kerja-kerja penandaan pegging
                                    'right of way' row(diwarnakan <span style="font-weight:600; text-transform: uppercase; color:red;font-size: 14px;">merah</span>) dan pegging propose
                                    (diwarnakan <span style="font-weight:600; text-transform: uppercase; color:blue;font-size: 14px;">biru</span>). paip pegging
                                    diletakkan selari dengan chainage yang ditanda.
                                </h5>
                            </th>
                        </tr>
                    </thead>       
                </table>
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
                                <div class="d-flex justify-content-center image-container">
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
                                <div class="d-flex justify-content-center image-container">
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

<script>
    // Function to add page breaks after every 4 images
    function addPageBreaks() {
        const images = document.querySelectorAll('.image-container');
        let imageCount = 0;

        images.forEach((image, index) => {
            imageCount++;
            if (imageCount % 4 === 0 && index !== images.length - 1) {
                const pageBreak = document.createElement('tr');
                pageBreak.classList.add('page-break');
                image.parentNode.insertBefore(pageBreak, image.parentNode.childNodes[index + 1]);
            }
        });
    }

    // Call the function when the page loads
    window.addEventListener('load', addPageBreaks);
</script>
