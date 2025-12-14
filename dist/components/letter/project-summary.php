<?php
$systemId = $_GET['sid'];

$authorityIds = array();
$totalDist = array();
$authDist = array();
foreach(Permitting::authorityRp($systemId) as $items){
    // var_dump($items['authority_id']);
    $authorityIds[] = $items['authority_id'];

    $totalDist[] = $items['total_distance'];

    $authDist[$items['authority_id']] = $items['total_distance'];



}
// var_dump($authDist);
function addDataToRpData(&$rpData, $authorityId, $code, $method, $value, $unit) {

    $rpData[] = array(
        'authority_id' => $authorityId,
        'shortMethod' => $code,
        'method' => $method,
        'value' => $value,
        'unit' => $unit,
    );

}


$uniqueAuthorityIds = array_values(array_unique($authorityIds));
// var_dump($uniqueAuthorityIds);
// $uniqueTotalDistance = array_values(array_unique($totalDist));
$authTotalDist = array_sum($authDist);
// var_dump($authTotalDist);
// var_dump($totalDist);

$details = [];
foreach (Permitting::authorityModal() as $item) {

    if($item['system_id'] == $systemId){
        $details[] = $item;

    }

}
$detail = $details;
// var_dump($detail);


$rpDistrict = [];
$roadNames = [];
$workMethods = [];

foreach($uniqueAuthorityIds as $key => $id ){
    foreach ($detail as $item) {
        if ($item['authority_id'] == $id) {
            $rpDistrict[] = $item['authority_district_name'];
        }
    }

    $roadInvolved = Permitting::getRoadInvolved($systemId,$id);

    // $roadIds = [];



    foreach($roadInvolved as $rowing){

        $roadNames[] = $rowing['road_name'];

        foreach($rowing['method'] as $rowingz){

            $workMethodCode = $rowingz['method'];
            $workMethod = $rowingz['name'];

            $workMethods[] = $workMethod;

        }
    }


}

$uniqueroadNames = array_values(array_unique($roadNames));
$roadNamesString = implode(', ', $uniqueroadNames);

$uniqueworkMethods = array_values(array_unique($workMethods));
$workMethodsString = implode(', ', $uniqueworkMethods);
// var_dump($rpDistrict);
$uniqueworkRpDistrict = array_values(array_unique($rpDistrict));
$rpDistrictString = implode(', ',$uniqueworkRpDistrict);


$summaryListing = Permitting::SummaryListing($systemId);
$listData = reset($summaryListing);
// var_dump($listData);
$pageId = $listData['summary_status'].$listData['id'];

// var_dump($pageId);
?>


<div class="letter align-items-center">
<section class="sheet padding-10mm main-section" >
                    <div class="d-flex flex-column align-items-start flex-sm-row mb-5">
                        <!--begin::Input group-->
                        <div class="d-flex align-items-center justify-content-end flex-equal order-3 fw-row">
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center justify-content-end  w-200px">
                                <img class="mw-175px" src="assets/media/logos/<?php echo $tenant; ?>-default.svg"
                                    alt="image" />
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>

                    <h2 class="text-center text-decoration-underline">RINGKASAN PROJEK</h2>

                    <div class="fv-row mb-5 ">
                        <div class="d-flex flex-column justify-content-center">
                            <table class="table table-bordered" style="border: 1px solid black;">
                                <tbody>
                                    <tr>
                                        <td class="text-center fs-7 p-0" style="width:25%;">No. Ruj. :</td>
                                        <td class="text-center fs-7 p-0 fw-bold" style="width:37.5%;">
                                            <?php echo $detail[0]['reference_no'];?></td>
                                        <td class="text-center fs-7 p-0" style="width:12.5%;">Daerah :</td>
                                        <td class="text-center fs-7 p-0 fw-bold" style="width:25%;"><?php echo $rpDistrictString;?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center fs-7 p-2">Nama Jalan/Lokasi Terlibat :</td>
                                        <td class="text-center fs-7 p-2 fw-bold" colspan="3" style="text-transform: capitalize;"><?php echo $roadNamesString;?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered" style="border: 1px solid black;">
                                <tbody>
                                    <tr>
                                        <td class="text-start fs-7 p-0 ps-1" style="width:75%;">Pemohon (Syarikat Utiliti/Pemaju)
                                        </td>
                                        <td class="text-center fs-7 p-0 fw-bold" colspan="2" style="width:25%;"><?php echo $detail[0]['provider_name'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 p-0 ps-1" style="width:75%;">Jumlah Jarak Keseluruhan Yang
                                            Terlibat</td>
                                        <td class="text-center fs-7 p-0 fw-bold" style="width:12.5%;"><?php echo $authTotalDist ?></td>
                                        <td class="text-center fs-7 p-0" style="width:12.5%;">meter</td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                                foreach($uniqueAuthorityIds as $index => $id){


                            ?>
                            <table class="table table-bordered" style="border: 1px solid black;">
                                <tbody>
                                    <tr>
                                        <td class="text-center fs-7 p-0" style="width:75%;" colspan="2">Pihak Berkuasa : <b><?php
                                        if (isset($detail[$index]['authority_name'])) {
                                            // Access the value here
                                            echo $detail[$index]['authority_name'];
                                        } else {
                                            // Handle the case where the key doesn't exist
                                            echo '';
                                        }
                                        ?></b></td>
                                        <td class="text-center fs-7 p-0" colspan="2"
                                            style="width:25%; border-top: 1px solid white; border-right: 1px solid white;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 p-2" style="width:75%" colspan="2" style="text-transform: capitalize;">Nama Jalan : <b style="text-transform: capitalize;">
                                            <?php     $roadInvolved = Permitting::getRoadInvolved($systemId,$id);
                                            $nameRoad = [];
                                            foreach($roadInvolved as $rowing){
                                                $nameRoad[] = $rowing['road_name'];
                                            }

                                            $uniqueNameRoad = array_values(array_unique($nameRoad));
                                            $nameRoadString = implode(', ', $uniqueNameRoad);

                                            echo $nameRoadString;
                                            ?></b></td>
                                        <td class="text-center fs-7 p-2 fw-bold" style="width:12.5%;"><?php echo $authDist[$id]; ?></td>
                                        <td class="text-center fs-7 p-2" style="width:12.5%;">meter</td>
                                    </tr>
                                    <?php
                                        $alphabets = range('a', 'z');
                                        $counter = 0;



                                        $rpData = array();

                                        foreach(Permitting::dataRp($systemId,$id) as $key => $data){
                                            // var_dump($data);

                                            if ($data['method'] === "HDD") {
                                                if ($data['road_crossing'] !== null) {
                                                addDataToRpData($rpData, $id, $data['method'], 'Horizontal Directional Drilling (HDD) Road Crossing', $data['road_crossing'], 'meter');
                                                }

                                                if ($data['road_shoulder'] !== null) {
                                                addDataToRpData($rpData, $id, $data['method'], 'Horizontal Directional Drilling (HDD) Road Shoulder', $data['road_shoulder'], 'meter');
                                                }
                                            } else if ($data['method'] === "GV") {
                                                if ($data['diameter_more_500'] !== null) {
                                                addDataToRpData($rpData, $id, $data['method'], 'Korekan Jalan Tidak Berturap (Diameter > 500)', $data['diameter_more_500'], 'meter');
                                                }
                                                if ($data['diameter_less_500'] !== null) {
                                                addDataToRpData($rpData, $id, $data['method'], 'Korekan Jalan Tidak Berturap (Diameter < 500)', $data['diameter_less_500'], 'meter');
                                                }
                                            } else if ($data['method'] === "CW") {
                                                if ($data['diameter_more_500'] !== null) {
                                                addDataToRpData($rpData, $id, $data['method'], 'Korekan Jalan Berturap (Diameter > 500)' , $data['diameter_more_500'], 'meter');
                                                }
                                                if ($data['diameter_less_500'] !== null) {
                                                addDataToRpData($rpData, $id, $data['method'], 'Korekan Jalan Berturap (Diameter < 500)', $data['diameter_less_500'], 'meter');
                                                }
                                            } else if ($data['method'] === "OH") {
                                                if ($data['quantity'] !== null) {
                                                    $methodName = 'Penanaman Tiang ('.$data['distance'].' meter)';
                                                addDataToRpData($rpData, $id, $data['method'], $methodName , $data['quantity'], 'nos');
                                                }

                                            } else if ($data['method'] === "MT") {
                                                if ($data['road_crossing'] !== null) {
                                                    addDataToRpData($rpData, $id, $data['method'], 'Micro Tunnelling Road Crossing', $data['road_crossing'], 'meter');
                                                    }

                                                    if ($data['road_shoulder'] !== null) {
                                                    addDataToRpData($rpData, $id, $data['method'], 'Micro Tunnelling Road Shoulder', $data['road_shoulder'], 'meter');
                                                    }

                                            } else if ($data['method'] === "TB") {
                                                if ($data['road_crossing'] !== null) {
                                                    addDataToRpData($rpData, $id, $data['method'], 'Thrust Boring Road Crossing', $data['road_crossing'], 'meter');
                                                    }

                                                    if ($data['road_shoulder'] !== null) {
                                                    addDataToRpData($rpData, $id, $data['method'], 'Thrust Boring Road Shoulder', $data['road_shoulder'], 'meter');
                                                    }

                                            } else if ($data['method'] === "PJ") {
                                                if ($data['road_crossing'] !== null) {
                                                    addDataToRpData($rpData, $id, $data['method'], 'Pipe Jacking Road Crossing', $data['road_crossing'], 'meter');
                                                    }

                                                    if ($data['road_shoulder'] !== null) {
                                                    addDataToRpData($rpData, $id, $data['method'], 'Pipe Jacking Road Shoulder', $data['road_shoulder'], 'meter');
                                                    }

                                            } else if ($data['method'] === "CP") {

                                                addDataToRpData($rpData, $id, $data['method'], 'Penarikan Kabel', $data['distance'], 'meter');

                                            } else if ($data['method'] === "ID") {

                                                addDataToRpData($rpData, $id, $data['method'], 'Dalam Longkang', $data['distance'], 'meter');

                                            } else if ($data['method'] === "ED") {

                                                addDataToRpData($rpData, $id, $data['method'], 'Tepi Longkang', $data['distance'], 'meter');

                                            } else if ($data['method'] === "GI") {

                                                addDataToRpData($rpData, $id, $data['method'], 'Besi Bergalvani', $data['distance'], 'meter');

                                            } else if ($data['method'] === "MS") {

                                                addDataToRpData($rpData, $id, $data['method'], 'Tambatan/ sokongan paip air expose', $data['quantity'], 'nos');

                                            } else if ($data['method'] === "Jangkaan Tempoh Kerja di Tapak") {

                                                addDataToRpData($rpData, $id, $data['method'], 'Jangkaan Tempoh Kerja di Tapak', $data['quantity'], 'hari');

                                            } else {
                                                addDataToRpData($rpData, $id, $data['method'], $data['method'], $data['quantity'], 'nos');
                                            }

                                        }


                                            // var_dump($rpData);



                                            foreach($rpData as $key => $data){



                                    ?>

                                    <tr>
                                        <td class="fs-7 p-0 text-center" style="width:6.25%">
                                            <?php echo $alphabets[$counter].'.';
                                          ?></td>
                                        <td class="text-start fs-7 p-0 ps-1" style="width:68.75%"><?php
                                               echo $data['method'];

                                         ?>

                                        </td>
                                        <td class="text-center fs-7 p-0 fw-bold" style="width:12.5%;"><?php
                                                echo $data['value'];

                                        ?></td>
                                        <td class="text-center fs-7 p-0" style="width:12.5%;"><?php
                                                echo $data['unit'];


                                        ?></td>
                                    </tr>
                                    <?php
                                        $counter++;
                                        }
                                    ?>

                                </tbody>
                            </table>
                            <?php
                                }
                            ?>



                            <!-- <table class="table table-bordered" style="border: 1px solid black;">
                                <tr>
                                    <td class="text-start fs-7 ps-2 p-1" style="width:75%" colspan="2">Jangkaan Tempoh Kerja di
                                        Tapak :</td>
                                    <td class="text-center fs-7 p-1 fw-bold" style="width:12.5%;"></td>
                                    <td class="text-center fs-7 p-1" style="width:12.5%;">hari</td>
                                </tr>
                            </table> -->

                            <table class="table table-bordered" style="border: 1px solid black;">
                                <tbody>
                                    <tr>
                                        <th class="text-center fs-7 p-0" style="width:100%;" colspan="3">Pihak Berkuasa
                                            Melulus Yang Terlibat :</th>

                                    </tr>
                                    <?php
                                    foreach($uniqueAuthorityIds as $keyz => $id){

                                     ?>
                                    <tr>
                                        <td class="fs-7 p-0 text-center" style="width:6.25%"><?php echo $alphabets[$key].'.'; ?></td>
                                        <td class="fs-7 p-0 text-center" style="width:25%"><?php

                                        $authorityGroup = "Lain-Lain"; // Default value if none of the conditions are met

                                        if ($detail[$keyz]['authority_group'] === 1) {
                                            $authorityGroup = "JKR";
                                        } elseif ($detail[$keyz]['authority_group'] === 2) {
                                            $authorityGroup = "MD";
                                        } elseif ($detail[$keyz]['authority_group'] === 3) {
                                            $authorityGroup = "JPS";
                                        }
                                        echo $authorityGroup;
                                        ?></td>
                                        <td class="text-center fs-7 p-0" style="width:68.75%;"><?php echo $detail[$keyz]['authority_name'];?></td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>

                            <span class="fw-bold fs-7">Pegawai Bertanggungjawab : </span>


<!-- <tr>
    <th class="fs-7 p-0 text-center" style="width:6.25%">Bil.</th>
    <th class="fs-7 p-0 text-center" style="width:25%">Syarikat</th>
    <th class="fs-7 p-0 text-center" style="width:31.25%;">Nama</th>
    <th class="text-center fs-7 p-0" style="width:22%;">Jawatan</th>
    <th class="text-center fs-7 p-0" style="width:15.5%;">No Tel</th>
</tr> -->
<!-- <tr>
    <td class="fs-7 p-0 text-center">a.</td>
    <td class="fs-7 p-0 ps-1">KORIDOR UTILITI PAHANG</td>
    <td class=" fs-7 p-0 ps-1"></td>
    <td class="text-center fs-7 p-0">Pengurus Operasi</td>
    <td class="text-center fs-7 p-0"></td>
</tr> -->
<?php
    // var_dump(contactRP($systemId));

    $contactClient = Permitting::contactRPClient($systemId);
    $contactKUN = Permitting::contactRPKUN($systemId);
    $alphabetz = range('a', 'z');
    $counterz = 0;

    echo '<table class="table table-bordered" style="border: 1px solid black;">
    <tbody>
    <tr>
        <td colspan="5" class="text-center fs-7 p-0 fw-bold">Agensi Penyelaras Utiliti</td>
    </tr>';

    foreach($contactKUN as $officer){



    echo '<tr>
        <td class="fs-7 p-1 text-center" style="width:6.25%">'.$alphabetz[$counterz].'.</td>
        <td class="fs-7 p-1 ps-1 text-start" style="width:25%">'.$tenant.'</td>
        <td class=" fs-7 p-1 ps-1 text-start" style="width:31.25%;">'.$officer['first_name'].' '.$officer['last_name'].'</td>
        <td class="text-center fs-7 p-1" style="width:22%;">'.$officer['position'].'</td>
        <td class="text-center fs-7 p-1" style="width:15.5%;">'.$officer['phone_no'].'</td>
    </tr>';

        $counterz++;
    }

    echo '</tbody>
        </table>';

    foreach($contactClient as $contact){

        echo '<table class="table table-bordered" style="border: 1px solid black;">
        <tbody>
        <tr>
            <td colspan="5" class="text-center fs-7 p-0 fw-bold">' . $contact['name'] . '</td>
        </tr>';

        foreach ($contactClient as $contacts) {

            if($contact['name'] === $contacts['name']){
                echo '<tr>
                    <td class="fs-7 p-1 text-center" style="width:6.25%">' . $alphabetz[$counterz] . '.</td>
                    <td class="fs-7 p-1 ps-1 text-start" style="width:25%">' . $contacts['company_name'] . '</td>
                    <td class=" fs-7 p-1 ps-1 text-start" style="width:31.25%;">' . $contacts['full_name'] . '</td>
                    <td class="text-center fs-7 p-1" style="width:22%;">' . $contacts['position'] . '</td>
                    <td class="text-center fs-7 p-1" style="width:15.5%;">' . $contacts['phone_no'] . '</td>
                </tr>';

                $counterz++;
            }

        }

        echo '</tbody>
        </table>';
    }
?>

                            <div class="d-flex flex-row justify-content-between">
                            <?php

if($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT'){
    echo '<div class=" d-flex flex-column w-300px">
        <span>Disediakan oleh :</span>
        <!-- <canvas id="signature" name="signature"></canvas> -->
        <span class="my-10"></span>
        <span>Nama :</span>
        <span>Jawatan :</span>
        <span>Tarikh :</span>
    </div>
    <div class="d-flex flex-column w-300px">
        <span>Disemak oleh :</span>
        <!-- <canvas id="signature" name="signature"></canvas> -->
        <span class="my-10"></span>
        <span>Nama :</span>
        <span>Jawatan :</span>
        <span>Tarikh :</span>
    </div>';

} else if ($appsTitle == 'KITER') {
    echo '<div class=" d-flex flex-column w-300px">
        <span>Disediakan oleh :</span>
        <!-- <canvas id="signature" name="signature"></canvas> -->
        <span class="my-10"></span>
        <span>Nama :</span>
        <span>Jawatan :</span>
        <span>Tarikh :</span>
    </div>
    <div class="d-flex flex-column w-300px">
        <span>Disemak oleh :</span>
        <!-- <canvas id="signature" name="signature"></canvas> -->
        <span class="my-10"></span>
        <span>Nama :</span>
        <span>Jawatan :</span>
        <span>Tarikh :</span>
    </div>
    <div class="d-flex flex-column w-300px">
        <span>Disahkan oleh :</span>
        <!-- <canvas id="signature" name="signature"></canvas> -->
        <span class="my-10"></span>
        <span>Nama :</span>
        <span>Jawatan :</span>
        <span>Tarikh :</span>
    </div>';

}
?>


                            </div>

                        </div>
                    </div>




                                <input type="text" name="reference-no" id="reference-no" value="<?php echo $detail[0]['reference_no']; ?>" hidden />
                    <input type="text" name="system-id" id="system-id" value="<?php echo $systemId ?>" hidden />
                                    </section>
                                    </div>
