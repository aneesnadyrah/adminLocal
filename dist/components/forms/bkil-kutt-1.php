<?php
$systemId = $_GET['sid'] ?? NULL;
$detail = new projectDetails();
$data = $detail->getProjectDetails($systemId);
$provider = General::getProvider($data->provider_id);
$road = $detail->getRoadDetails($systemId);

$cleanRange = trim($data->project_date, '[]()');
$project_date = explode(',',$cleanRange);
$project_dateStr = new DateTime($project_date[0]);
$project_dateStrFmt = $project_dateStr->format('d / m / Y');
$project_dateEnd = new DateTime($project_date[1]);
$project_dateEndFmt = $project_dateEnd->format('d / m / Y');

$diff = $project_dateStr->diff($project_dateEnd);
$days = $diff->days;
?>

<!-- Each sheet element should have the class "sheet" -->
<!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
<section class="sheet padding-10mm">

    <!-- Write HTML just like a web page -->
    <article>

        <div style="margin-bottom: 1rem; ">
            <table style="border-collapse: collapse;">
                <thead>
                    <tr style="border: 1px solid;">
                        <td rowspan="2" style="width: 10%; text-align: center; border: 1px solid;">
                            <img style="display: block; width: 150px; margin: auto; padding: 5px;"
                                src="assets/media/logos/<?= strtolower($system->App->tenant) ?>-default.svg">
                        </td>
                        <td>
                            <h2
                                style="font-weight: 700; text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                <!-- Koridor Utiliti Teknologi Terengganu Sdn. Bhd -->
                                <?= $system->App->company . ' Sdn. Bhd' ?>
                            </h2>
                        </td>
                    </tr>
                    <tr style="border: 1px solid;">
                        <td>
                            <h2
                                style="font-weight: 700; text-align: center; margin: 0.75rem 0; text-transform: uppercase;">
                                Borang Kelulusan Izin Lalu (BKIL)
                            </h2>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>


        <table>
            <thead style="background-color:lightgrey;">
                <tr>
                    <th colspan="2">
                        <h5 style="font-weight:600; text-align: start; margin: 0.5rem">1.0 Butiran Projek</h5>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:120px;">
                        <p style="margin: 0.5rem 0 0 1rem">1.1 Tajuk Projek :</p>
                    </td>
                    <td>
                        <!-- <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">TRY MOHON TAJUK PERMOHONAN YANG MEMERLUKAN ITU</p> -->
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $data->project_title ?>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td style="width:300px;">
                        <p style="margin: 0.5rem 0 0 1rem">1.2 Tempoh Kerja Korekgali Pemasangan Utiliti :</p>
                    </td>
                    <td>
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $days.' hari';?></p>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>
        <table style="padding-left:1.5rem">
            <tbody>
                <tr>
                    <td style="width:200px;">
                        <p style="margin: 0.5rem 0 0 1rem">i. Jangkaan Tarikh Mula Kerja :</p>
                    </td>
                    <td style="width:250px">
                        <p style="margin: 0.5rem 0 0 1.5rem; border-bottom: 2px dotted"><?php echo $project_dateStrFmt; ?></p>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td style="width:200px;">
                        <p style="margin: 0.5rem 0 0 1rem">ii. Jangkaan Tarikh Tamat Kerja :</p>
                    </td>
                    <td style="width:250px">
                        <p style="margin: 0.5rem 0 0 1.5rem; border-bottom: 2px dotted"><?php echo $project_dateEndFmt; ?></p>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>
        <table>
            <tbody>
                <tr>
                    <td style="width:250px;">
                        <p style="margin: 0.5rem 0 0 1rem">1.3 Kos Projek (Ringgit Malaysia) :</p>
                    </td>
                    <td>
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted">
                            <?php echo 'RM ' . number_format($data->project_costs, 2, '.', ',') ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width:250px;">
                        <p style="margin: 0.5rem 0 0 1rem">1.4 Penyedia Utiliti :</p>
                    </td>
                    <td>
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $provider->name ?></p>
                    </td>
                </tr>
                <tr>
                    <td style="width:250px;">
                        <p style="margin: 0.5rem 0 0 1rem">1.5 Jenis Permohonan :</p>
                    </td>
                    <td>
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $data->application_code ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="width:250px;">
                        <p style="margin: 0.5rem 0 0 1rem">1.6 No Rujukan <?= $system->App->tenant ?> :</p>
                    </td>
                    <td>
                        <p style="margin: 0.5rem 0 0 0; border-bottom: 2px dotted"><?php echo $data->reference_no ?></p>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top:1rem;"></div>

        <table>
            <thead style="background-color:lightgrey;">
                <tr>
                    <th colspan="2">
                        <h5 style="font-weight:600; text-align: start; margin: 0.5rem">2.0 Butiran Jalan Terlibat</h5>
                    </th>
                </tr>
            </thead>
        </table>

        <div style="margin-top:0.5rem;"></div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Jalan</th>
                    <th>Kaedah</th>
                    <th>Koordinat Mula</th>
                    <th>Koordinat Akhir</th>
                    <th>Jarak (m)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $limit = 20;
                $count = 0;
		$sum = 0;
                foreach ($road as $row) {
                    if ($count >= $limit)
                        break; // Stop the loop if limit is reached
                    $count++;
			$sum += $row->road_length; 
                    ?>
                    <tr>
                        <td><?php echo strtoupper($row->road_name) ?></td>
                        <td><?php echo $row->methods ?></td>
                        <td><?php echo $row->coor_start ?></td>
                        <td><?php echo $row->coor_end ?></td>
                        <td><?php echo $row->road_length ?></td>
                    </tr>

                <?php } ?>
            </tbody>
            <?php if ($count >= $limit): ?>
            <?php else: ?>
                <tfoot>
                    <tr>
                        <td colspan="4" style="padding-left:1rem; text-align:left;">
                            <p>Jumlah Jarak Permohonan (Meter)</p>
                        </td>
                        <td style="text-align:center;">
                            <p><?= $sum ?></p>
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>

    </article>
</section>

<?php if (count($road) > 20): ?>
    <section class="sheet padding-10mm">
        <!-- Write HTML just like a web page -->
        <article>
            <div div style="margin-top:1rem;"></div>

            <table>
                <thead style="background-color:lightgrey;">
                    <tr>
                        <th colspan="2">
                            <h5 style="font-weight:600; text-align: start; margin: 0.5rem">2.0 Butiran Jalan Terlibat</h5>
                        </th>
                    </tr>
                </thead>
            </table>

            <div style="margin-top:0.5rem;"></div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Jalan</th>
                        <th>Kaedah</th>
                        <th>Koordinat Mula</th>
                        <th>Koordinat Akhir</th>
                        <th>Jarak (m)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 20;
                    $offset = 20; // This is the number of records to skip
                    $count = 0;
                    $displayCount = 0;

                    foreach ($road as $row) {
                        if ($count < $offset) {
                            $count++;
                            continue; // Skip the records until the offset is reached
                        }
                        if ($displayCount >= $limit) break; // Stop the loop if limit is reached
                        $displayCount++;
                    ?>
                        <tr>
                            <td><?php echo strtoupper($row->road_name) ?></td>
                            <td><?php echo $row->methods ?></td>
                            <td><?php echo $row->coor_start ?></td>
                            <td><?php echo $row->coor_end ?></td>
                            <td><?php echo $row->road_length ?></td>
                        </tr>

                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="padding-left:1rem; text-align:left;">
                            <p>Jumlah Jarak Permohonan (Meter)</p>
                        </td>
                        <td style="text-align:center;">
                            <p><?php echo $data->application_length ?></p>
                        </td>
                    </tr>
                </tfoot>
            </table>

        </article>
    </section>
<?php endif; ?>
