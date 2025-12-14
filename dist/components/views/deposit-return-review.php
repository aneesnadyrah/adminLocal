<?php
$systemId = $_GET['sid'];

$flwAuthId = $_GET['aid'];
// var_dump($flwAuthId);
$authId = General::getAuthorityId($flwAuthId);


$authorityIds = array();
$totalDist = array();
$authDist = array();
foreach(Permitting::authorityWc($systemId,$authId) as $items){
    // var_dump($items['authority_id']);
    $authorityIds[] = $items['authority_id'];

    $totalDist[] = $items['total_distance'];

    $authDist[$items['authority_id']] = $items['total_distance'];



}

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
$uniqueTotalDistance = array_values(array_unique($totalDist));
$authTotalDist = array_sum($uniqueTotalDistance);

// var_dump($authTotalDist);

// $details = [];
// foreach (authorityModal() as $item) {

//     if($item['system_id'] == $systemId){
//         $details[] = $item;

//     }

// }
$detail = Permitting::authorityData($systemId,$authId);
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


$depositListing = Permitting::DepositListing($systemId,$authId);
$listData = reset($depositListing);
// var_dump($depositListing);
$pageId = $listData['deposit_status'].$listData['id'];

// var_dump($listData);
?>

<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">

    <form action="" id="form-deposit-return-<?php echo $pageId; ?>">
        <!--begin::Card-->


        <?php

            foreach($uniqueAuthorityIds as $no => $id){
            $data = Permitting::dataWc($systemId,$id);

        ?>
        <!--end::Card-->
        <div class="card mb-5 print-content-only">
            <!--begin::Card body-->
            <div class="card-body py-12 px-20">
                <!--begin::Form-->
                    <div class="fv-row my-5 ">
                        <div class="d-flex flex-column justify-content-center">

                            <table class="table table-bordered"
                                style="border:1px solid black;border-collapse:collapse;width: 100%;">
                                <!-- <thead style="visibility: collapse">
                                    <tr>
                                        <td style="width:6.25%;"></td>
                                        <td style="width:25.75%;"></td>
                                        <td style="width:9%;"></td>
                                        <td style="width:9%;"></td>
                                        <td style="width:13%;"></td>
                                        <td style="width:10%;"></td>
                                        <td style="width:13.5%;"></td>
                                        <td style="width:13.5%;"></td>
                                    </tr>
                                </thead> -->

                                <?php
                                 if($appsTitle == 'KITER'|| $appsTitle == 'KUDRAT'){
                                    echo '
                                    <thead style="visibility: collapse">
                                    <tr>
                                        <td style="width:6.25%;"></td>
                                        <td style="width:25.75%;"></td>
                                        <td style="width:9%;"></td>
                                        <td style="width:9%;"></td>
                                        <td style="width:13%;"></td>
                                        <td style="width:10%;"></td>
                                        <td style="width:13.5%;"></td>
                                        <td style="width:13.5%;"></td>
                                    </tr>
                                </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center align-items-center fs-7" style="width:30%;vertical-align:middle;">
                                            <img class="w-150px"
                                                src="assets/media/logos/'.$tenant.'-default.svg" alt="image" />
                                        </td>
                                        <td colspan="5" class="text-center align-items-center fs-7 py-10 fw-bold"
                                            style="text-decoration:underline;width:70%;">KIRAAN WANG CAGARAN</td>
                                    </tr>
                                </tbody>';

                                 } else if ($appsTitle == 'UCIDOS' ){
                                     echo '
                                     <thead style="visibility: collapse;border:1px solid white;">
                                    <tr>
                                        <td style="width:6.25%;border:1px solid white;"></td>
                                        <td style="width:25.75%;border:1px solid white;"></td>
                                        <td style="width:9%;border:1px solid white;"></td>
                                        <td style="width:9%;border:1px solid white;"></td>
                                        <td style="width:13%;border:1px solid white;"></td>
                                        <td style="width:10%;border:1px solid white;"></td>
                                        <td style="width:13.5%;border:1px solid white;"></td>
                                        <td style="width:13.5%;border:1px solid white;"></td>
                                    </tr>
                                </thead>

                                     <tbody>
                                <tr>
                                        <td colspan="8" class="text-center align-items-center fs-7 py-5 fw-bold"
                                            style="border:1px solid white;border-bottom: 1px solid black;width:70%;">DEPOSIT KESELAMATAN DAN JADUAL KADAR KAEDAH PEMASANAGAN MERUJUK KEPADA PROSEDUR PERMOHONAN PEMASANGAN UTILITI JKR 2022 (Ruj.: Borang JKR/P-02/2022)</td>
                                    </tr>
                                </tbody>';
                                 }
                                ?>


                                <tbody>
                                    <tr>
                                        <td colspan="8" class="text-center align-items-center fs-7 p-0 fw-bold"
                                            style="width:100%;background-color:lightgrey;">BUTIRAN PROJEK</td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">1
                                        </td>
                                        <td class="fs-7 p-1 align-items-center" style="width:25%;">Tajuk Projek</td>
                                        <td colspan="6" class="fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['project_title'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">2
                                        </td>
                                        <td class="fs-7 p-1 align-items-center" style="width:25%;">No. Ruj. <?php echo $tenant;?></td>
                                        <td colspan="6" class="fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['reference_no'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">3
                                        </td>
                                        <td class="fs-7 p-1 align-items-center" style="width:25%;">Lokasi</td>
                                        <td colspan="6" class="fs-7 p-1  align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php
                                            $roadInvolved = Permitting::getRoadInvolved($systemId,$id);
                                            $nameRoad = [];
                                            foreach($roadInvolved as $rowing){
                                                $nameRoad[] = $rowing['road_name'];
                                            }

                                            $uniqueNameRoad = array_values(array_unique($nameRoad));
                                            $nameRoadString = implode(', ', $uniqueNameRoad);

                                            echo $nameRoadString;
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">4
                                        </td>
                                        <td class="fs-7 p-1 align-items-center" style="width:25%;">Jarak</td>
                                        <td colspan="6" class="fs-7 p-1  align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['involved_appl_length'].' METER';?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">5
                                        </td>
                                        <td class="fs-7 p-1 align-items-center" style="width:25%;">Penyedia Utiliti</td>
                                        <td colspan="6" class="fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['provider_name'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">6
                                        </td>
                                        <td class="fs-7 p-1 align-items-center" style="width:25%;"><?php

                                        $authorityGroup = "Lain-Lain"; // Default value if none of the conditions are met

                                        if ($detail[$no]['authority_group'] === 1) {
                                            $authorityGroup = "JKR";
                                        } elseif ($detail[$no]['authority_group'] === 2) {
                                            $authorityGroup = "MD";
                                        } elseif ($detail[$no]['authority_group'] === 3) {
                                            $authorityGroup = "JPS";
                                        }

                                        echo 'Pihak Berkuasa ';
                                        ?></td>
                                        <td colspan="6" class="fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[$no]['authority_name'];?></td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="8" class="text-center align-items-center fs-7 p-0 fw-bold"
                                            style="width:100%;background-color:lightgrey;">KADAR DEPOSIT KESELAMATAN
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <th class="fs-7 text-center align-items-center fs-7 fw-bold"
                                            style="width:6.25%;">BIL</th>
                                        <th class="fs-7 text-center align-items-center fw-bold" colspan="3">KAEDAH
                                            PEMASANGAN</th>
                                        <th class="fs-7 text-center align-items-center fw-bold">KUANTITI</th>
                                        <th class="fs-7 text-center align-items-center fw-bold">UNIT</th>
                                        <th class="fs-7 text-center p-0 align-items-center fw-bold">KADAR<br>(RM)</th>
                                        <th class="fs-7 text-center p-0 align-items-center fw-bold">BAYARAN<br>(RM)</th>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="fs-7 py-10 text-center align-items-center fs-7"
                                            style="width:6.25%;">1</td>
                                        <td colspan="3" class="fs-7 ps-1 p-0 align-items-center ">Horizontal Directional
                                            Drilling (HDD)</td>
                                        <td colspan="4" class="fs-7 p-0 text-center align-items-center "></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 ps-1 p-0 align-items-center " colspan="3">(i) Jalan (Lebar
                                            carriageaway) - Merentangi jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $hdd_road_crossing = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'HDD'){
                                                $hdd_road_crossing += $datas['road_crossing'];

                                            }

                                        }

                                        if($hdd_road_crossing !== 0){
                                            echo $hdd_road_crossing;
                                        } else {
                                            echo '';
                                        }

                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold">
                                            <?php $total_hdd_road_crossing = Permitting::formulaWC('Kiraan HDD Road Crossing', $hdd_road_crossing); echo $total_hdd_road_crossing ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 ps-1 align-items-center " colspan="3">(ii) Sepanjang Bahu
                                            Jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $hdd_road_shoulder = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'HDD'){
                                                $hdd_road_shoulder += $datas['road_shoulder'];

                                            }

                                        }

                                        if($hdd_road_shoulder !== 0){
                                            echo $hdd_road_shoulder;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">0.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_hdd_road_shoulder = Permitting::formulaWC('Kiraan HDD Road Shoulder', $hdd_road_shoulder); echo $total_hdd_road_shoulder; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 ps-1 align-items-center " colspan="3">(iii) Pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $hdd_pit = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'HDD'){
                                                $hdd_pit += $datas['pit'];
                                            }

                                        }

                                        if($hdd_pit !== 0){
                                            echo $hdd_pit;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">8,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_hdd_pit = Permitting::formulaWC('Kiraan HDD Pit', $hdd_pit); echo $total_hdd_pit ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="fs-7 py-10 text-center align-items-center fs-7 "
                                            style="width:6.25%;">2</td>
                                        <td colspan="3" class="fs-7 ps-1 p-0 align-items-center ">Micro Tunnelling/Pipe
                                            Jacking/Thrust Boring</td>
                                        <td colspan="4" class="fs-7 p-0 text-center align-items-center "></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 ps-1 p-0 align-items-center " colspan="3">(i) Jalan (Lebar
                                            carriageaway) - Merentangi jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $mt_road_crossing = 0;
                                        $pj_road_crossing = 0;
                                        $tb_road_crossing = 0;

                                        foreach($data as $datas){

                                            if($datas['method'] === 'MT'){
                                                $mt_road_crossing += $datas['road_crossing'];

                                            } else if($datas['method'] === 'PJ'){
                                                $pj_road_crossing += $datas['road_crossing'];

                                            } else if($datas['method'] === 'TB'){
                                                $tb_road_crossing += $datas['road_crossing'];
                                            }
                                        }

                                        $mt_pj_tb_road_crossing = $mt_road_crossing + $pj_road_crossing + $tb_road_crossing;

                                        if($mt_pj_tb_road_crossing !== 0){
                                            echo $mt_pj_tb_road_crossing;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,800.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_mt_pj_tb_road_crossing = Permitting::formulaWC('Kiraan MT/PJ/TB Road Crossing', $mt_pj_tb_road_crossing); echo $total_mt_pj_tb_road_crossing; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 ps-1 align-items-center " colspan="3">(ii) Sepanjang Bahu
                                            Jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $mt_road_shoulder = 0;
                                        $pj_road_shoulder = 0;
                                        $tb_road_shoulder = 0;

                                        foreach($data as $datas){

                                            if($datas['method'] === 'MT'){
                                                $mt_road_shoulder += $datas['road_shoulder'];

                                            } else if($datas['method'] === 'PJ'){
                                                $pj_road_shoulder += $datas['road_shoulder'];

                                            } else if($datas['method'] === 'TB'){
                                                $tb_road_shoulder += $datas['road_shoulder'];
                                            }
                                        }

                                        $mt_pj_tb_road_shoulder = $mt_road_shoulder + $pj_road_shoulder + $tb_road_shoulder;

                                        if($mt_pj_tb_road_shoulder !== 0){
                                            echo $mt_pj_tb_road_shoulder;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">0.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_mt_pj_tb_road_shoulder = Permitting::formulaWC('Kiraan MT/PJ/TB Road Shoulder', $mt_pj_tb_road_shoulder); echo $total_mt_pj_tb_road_shoulder; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">(iii) Pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $mt_pit = 0;
                                        $pj_pit = 0;
                                        $tb_pit = 0;

                                        foreach($data as $datas){

                                            if($datas['method'] === 'MT'){
                                                $mt_pit += $datas['pit'];

                                            } else if($datas['method'] === 'PJ'){
                                                $pj_pit += $datas['pit'];

                                            } else if($datas['method'] === 'TB'){
                                                $tb_pit += $datas['pit'];
                                            }
                                        }

                                        $mt_pj_tb_pit = $mt_pit + $pj_pit + $tb_pit;

                                        if($mt_pj_tb_pit !== 0){
                                            echo $mt_pj_tb_pit;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">12,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_mt_pj_tb_pit = Permitting::formulaWC('Kiraan MT/PJ/TB Pit', $mt_pj_tb_pit); echo $total_mt_pj_tb_pit; ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2" class="fs-7 py-10 text-center align-items-center fs-7 "
                                            style="width:6.25%;">3</td>
                                        <td colspan="3" class="fs-7 ps-1 p-0 align-items-center ">i.Diameter Utiliti <
                                                500mm <br>Korekan Bahu Jalan (Kawasan Unpaved)</td>
                                        <td class="fs-7 text-center align-items-center fw-bold"><?php
                                        $gv_diameter_less_500 = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'GV'){
                                                $gv_diameter_less_500 += $datas['diameter_less_500'];
                                            }

                                        }

                                        if($gv_diameter_less_500 !== 0){
                                            echo $gv_diameter_less_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 text-center align-items-center ">meter</td>
                                        <td class="fs-7 text-center align-items-center ">200.00</td>
                                        <td class="fs-7 pe-1 text-end align-items-center fw-bold"><?php $total_gv_diameter_less_500 = Permitting::formulaWC('Kiraan GV diameter less', $gv_diameter_less_500); echo $total_gv_diameter_less_500; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">ii.Diameter Utiliti >
                                            500mm <br>Korekan Bahu Jalan (Kawasan Unpaved)</td>
                                        <td class="fs-7 text-center align-items-center fw-bold"><?php
                                        $gv_diameter_more_500 = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'GV'){
                                                $gv_diameter_more_500 += $datas['diameter_more_500'];

                                            }

                                        }

                                        if($gv_diameter_more_500 !== 0){
                                            echo $gv_diameter_more_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 text-center align-items-center ">meter</td>
                                        <td class="fs-7 text-center align-items-center ">330.00</td>
                                        <td class="fs-7 pe-1 text-end align-items-center fw-bold"><?php $total_gv_diameter_more_500 = Permitting::formulaWC('Kiraan GV diameter more', $gv_diameter_more_500);echo $total_gv_diameter_more_500;?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 text-center align-items-center fs-7 " style="width:6.25%;">4
                                        </td>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">Tambatan/ sokongan
                                            paip air expose - hanya dibenarkan di Kawasan cerun (Kawasan potongan atau
                                            tambakan)</td>
                                        <td class="fs-7 text-center align-items-center fw-bold"><?php
                                        $tambatan = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'MS'){
                                                $tambatan += $datas['quantity'];

                                            }

                                        }

                                        if($tambatan !== 0){
                                            echo $tambatan;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 text-center align-items-center ">nos</td>
                                        <td class="fs-7 text-center align-items-center ">1,000.00</td>
                                        <td class="fs-7 pe-1 text-end align-items-center fw-bold"><?php $total_tambatan = Permitting::formulaWC('Kiraan Tambatan/ sokongan paip air expose', $tambatan); echo $total_tambatan; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 text-center align-items-center fs-7 " style="width:6.25%;">5
                                        </td>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">Pemasangan Tiang</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $penanaman = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'OH'){
                                                $penanaman += $datas['quantity'];

                                            }

                                        }

                                        if($penanaman !== 0){
                                            echo $penanaman;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">nos</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">500.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_penanaman = Permitting::formulaWC('Kiraan Pemasangan Tiang', $penanaman); echo $total_penanaman; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 text-center align-items-center fs-7 " style="width:6.25%;">6
                                        </td>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">Pembersihan Tapak</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $bersih = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'Permbersihan Tapak'){
                                                $bersih += $datas['quantity'];

                                            }

                                        }

                                        if($bersih !== 0){
                                            echo $bersih;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">pukal</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">5,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_bersih = Permitting::formulaWC('Kiraan Pembersihan Tapak', $bersih); echo $total_bersih; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 text-center align-items-center fs-7 " style="width:6.25%;">7
                                        </td>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">Pelan Kawalan Trafik
                                            (TCP)</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $pelantcp = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'Pelan Kawalan Trafik (TCP)'){
                                                $pelantcp += $datas['quantity'];

                                            }

                                        }

                                        if($pelantcp !== 0){
                                            echo $pelantcp;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">hari/lokasi</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,700.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_pelantcp = Permitting::formulaWC('Kiraan Pelan TCP', $pelantcp); echo $total_pelantcp; ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="fs-7 py-10 text-center align-items-center fs-7 "
                                            style="width:6.25%;">8</td>
                                        <td colspan="3" class="fs-7 ps-1 p-0 align-items-center ">Di kawasan
                                            carriageway/permukaan berturap (berdasarkan justifikasi di tapak) :</td>
                                        <td colspan="4" class="fs-7 p-0 text-center align-items-center "></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fs-7 ps-1 p-0 align-items-center ">i.Diameter Utiliti <
                                                500mm</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $cw_diameter_less_500 = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'CW'){
                                                $cw_diameter_less_500 += $datas['diameter_less_500'];
                                            }

                                        }

                                        if($cw_diameter_less_500 !== 0){
                                            echo $cw_diameter_less_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter/lane</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,800.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_cw_diameter_less_500 = Permitting::formulaWC('Kiraan CW diameter less', $cw_diameter_less_500); echo $total_cw_diameter_less_500;?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">ii.Diameter Utiliti >
                                            500mm</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $cw_diameter_more_500 = 0;
                                        foreach($data as $datas){

                                            if($datas['method'] === 'CW'){
                                                $cw_diameter_more_500 += $datas['diameter_more_500'];
                                            }

                                        }

                                        if($cw_diameter_more_500 !== 0){
                                            echo $cw_diameter_more_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter/lane</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">2,400.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_cw_diameter_more_500 = Permitting::formulaWC('Kiraan CW diameter more', $cw_diameter_more_500); echo $total_cw_diameter_more_500; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="fs-7 p-0 ps-1 align-items-center ">iii.Pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $cw_pit = 0;
                                        foreach($data as $datas){
                                            if($datas['method'] === 'CW'){
                                                $cw_pit += $datas['pit'];
                                            }
                                        }

                                        if($cw_pit !== 0){
                                            echo $cw_pit;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">nos</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">25,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_cw_pit = Permitting::formulaWC('Kiraan CW Pit', $cw_pit); echo $total_cw_pit; ?></td>
                                    </tr>

                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="text-end align-items-center fs-7 p-1 fw-bold">JUMLAH KOS
                                            (RM)</td>
                                        <td class="text-end align-items-center fs-7 p-1 fw-bold"><?php
                                        $total_hdd_road_crossing = floatval(str_replace(',', '', $total_hdd_road_crossing));
                                        $total_hdd_road_shoulder = floatval(str_replace(',', '', $total_hdd_road_shoulder));
                                        $total_hdd_pit = floatval(str_replace(',', '', $total_hdd_pit));
                                        $total_mt_pj_tb_road_crossing = floatval(str_replace(',', '', $total_mt_pj_tb_road_crossing));
                                        $total_mt_pj_tb_road_shoulder = floatval(str_replace(',', '', $total_mt_pj_tb_road_shoulder));
                                        $total_mt_pj_tb_pit = floatval(str_replace(',', '', $total_mt_pj_tb_pit));
                                        $total_gv_diameter_less_500 = floatval(str_replace(',', '', $total_gv_diameter_less_500));
                                        $total_gv_diameter_more_500 = floatval(str_replace(',', '', $total_gv_diameter_more_500));
                                        $total_tambatan = floatval(str_replace(',', '', $total_tambatan));
                                        $total_penanaman = floatval(str_replace(',', '', $total_penanaman));
                                        $total_bersih = floatval(str_replace(',', '', $total_bersih));
                                        $total_pelantcp = floatval(str_replace(',', '', $total_pelantcp));
                                        $total_cw_diameter_less_500 = floatval(str_replace(',', '', $total_cw_diameter_less_500));
                                        $total_cw_diameter_more_500 = floatval(str_replace(',', '', $total_cw_diameter_more_500));
                                        $total_cw_pit = floatval(str_replace(',', '', $total_cw_pit));

                                        $overalltotal = $total_hdd_road_crossing + $total_hdd_road_shoulder + $total_hdd_pit + $total_mt_pj_tb_road_crossing + $total_mt_pj_tb_road_shoulder + $total_mt_pj_tb_pit + $total_gv_diameter_less_500 + $total_gv_diameter_more_500 + $total_tambatan + $total_penanaman + $total_bersih + $total_pelantcp + $total_cw_diameter_less_500 + $total_cw_diameter_more_500 + $total_cw_pit;
                                        // print_r($overalltotal);
                                        echo number_format($overalltotal, 2, '.', ','); ?></td>
                                    </tr>
                                </tbody>

                            </table>

                            <div>
                                <p class="fs-9"><em>*Berdasarkan jadual Kadar Wang Cagaran pada Borang JKR/P-02/2022,
                                        PROSEDUR PERMOHONAN PEMASANGAN UTILITI DI JALAN PERSEKUTUAN TAHUN 2022</em></p>
                            </div>
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
                                </div>
                                <div class=" d-flex flex-column w-300px">
                                    <span>Disemak oleh :</span>
                                    <!-- <canvas id="signature" name="signature"></canvas> -->
                                    <span class="my-10"></span>
                                    <span>Nama :</span>
                                    <span>Jawatan :</span>
                                    <span>Tarikh :</span>
                                </div>
                                <div class=" d-flex flex-column w-300px">
                                    <span>Disahkan oleh :</span>
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
                    <input type="text" name="authority-id" id="authority-id" value="<?php echo $authId ?>" hidden />
                    <input type="text" name="flw-auth-id" id="flw-auth-id" value="<?php echo $flwAuthId ?>" hidden />

                <!--end::Form-->
            </div>
            <!--end::Card body-->
        </div>
        <?php

            }
            ?>
            </form>
    </div>

    <!--end::Content-->
    <!--begin::Sidebar-->
    <div class="flex-lg-auto min-w-lg-300px">
		<!--begin::Card-->
		<?php

		if($listData['deposit_status'] == '1'){

            if($role === 52){
                echo '<div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
                <!--begin::Card body-->
                <div class="card-body p-10">
                    <!--begin::Actions-->
                    <div class="mb-0">
                        <button type="submit" href="#" class="btn btn-light-primary btn-active-primary w-100 mb-6" id="send-button-'.$pageId.'">
                            <span class="svg-icon svg-icon-3">
                                <i class="fad fa-file-circle-check fs-2"></i>
                            </span>
                            <span class="indicator-label">Hantar</span>
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <button type="button" class="btn btn-light btn-active-primary w-100" id="edit_button"  data-bs-toggle="modal" data-bs-target="#update-rpwc-'.$pageId.'">
                            <span class="svg-icon svg-icon-3">
                                <i class="fad fa-file-pen fs-2"></i>
                            </span>Kemaskini
                        </button>
                    </div>

                    <!--end::Actions-->
                </div>
                <!--end::Card body-->
            </div>';
            } else {
                echo '<div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
                                        <!--begin::Card body-->
                                        <div class="card-body p-10">
                                            <!--begin::Actions-->
                                            <div class="mb-0">
                                            <a href="wayleave/deposit/list" class="btn btn-light-primary btn-active-primary w-100 mb-6">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fad fa-file-circle-check fs-2"></i>
                                            </span>
                                            <span class="indicator-label">Kembali</span>
                                            <span class="indicator-progress">Sila Tunggu...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </a>
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>';
            }

		}else if($listData['deposit_status'] == '2'){

            if($role === 73){
                echo '
                <div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
                <!--begin::Card body-->
                <div class="card-body p-10">
                    <!--begin::Actions-->
                    <div class="mb-0">
                        <button type="submit" href="#" class="btn btn-light-primary btn-active-primary w-100 mb-6" id="approve-button-'.$pageId.'">
                            <span class="svg-icon svg-icon-3">
                                <i class="fad fa-file-circle-check fs-2"></i>
                            </span>
                            <span class="indicator-label">Sahkan</span>
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                        <button type="button" class="btn btn-light btn-active-primary w-100" id="comment_button"  data-bs-toggle="modal" data-bs-target="#comment-'.$pageId.'">
                            <span class="svg-icon svg-icon-3">
                                <i class="fad fa-file-pen fs-2"></i>
                            </span>Pinda
                        </button>
                    </div>

                    <!--end::Actions-->
                </div>
                <!--end::Card body-->
            </div>';
             }else {
                echo '<div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
                                        <!--begin::Card body-->
                                        <div class="card-body p-10">
                                            <!--begin::Actions-->
                                            <div class="mb-0">
                                            <a href="wayleave/deposit/list" class="btn btn-light-primary btn-active-primary w-100 mb-6">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fad fa-file-circle-check fs-2"></i>
                                            </span>
                                            <span class="indicator-label">Kembali</span>
                                            <span class="indicator-progress">Sila Tunggu...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </a>
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>';
            }

		} else if($listData['deposit_status'] == '3'){
			echo '
			<div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
			<!--begin::Card body-->
			<div class="card-body p-10 d-print-none">
				<!--begin::Actions-->
				<div class="mb-0">
					<button type="submit" href="#" class="btn btn-light-primary btn-active-primary w-100 mb-6"  onclick="window.location.href=`/projects/prints/WC/'.$systemId.'?aid='.$flwAuthId.'`" data-kt-scrolltop="true">
						<span class="svg-icon svg-icon-3">
							<i class="fad fa-file-circle-check fs-2"></i>
						</span>
						<span class="indicator-label">Cetak</span>
						<span class="indicator-progress">Sila Tunggu...
							<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
						</span>
					</button>
				</div>

				<!--end::Actions-->
			</div>
			<!--end::Card body-->
		</div>';
		} else if($listData['deposit_status'] == '4'){
if($role === 52){
		echo '<div class="card mb-5" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
			<!--begin::Card body-->
			<div class="card-body p-10">
				<!--begin::Actions-->
				<div class="mb-0">
					<button type="submit" href="#" class="btn btn-light-primary btn-active-primary w-100 mb-6" id="send-button-'.$pageId.'">
						<span class="svg-icon svg-icon-3">
							<i class="fad fa-file-circle-check fs-2"></i>
						</span>
						<span class="indicator-label">Hantar</span>
						<span class="indicator-progress">Sila Tunggu...
							<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
						</span>
					</button>
					<button type="button" class="btn btn-light btn-active-primary w-100" id="edit_button"  data-bs-toggle="modal" data-bs-target="#update-letter-'.$pageId.'">
						<span class="svg-icon svg-icon-3">
							<i class="fad fa-file-pen fs-2"></i>
						</span>Kemaskini
					</button>
				</div>

				<!--end::Actions-->
			</div>
			<!--end::Card body-->
		</div>';

    }else {
        echo '<div class="card" data-kt-sticky="true" data-kt-sticky-name="invoice" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="75px" data-kt-sticky-animation="true" data-kt-sticky-zindex="95">
                                <!--begin::Card body-->
                                <div class="card-body p-10">
                                    <!--begin::Actions-->
                                    <div class="mb-0">
                                    <a href="wayleave/deposit/list" class="btn btn-light-primary btn-active-primary w-100 mb-6">
                                    <span class="svg-icon svg-icon-3">
                                        <i class="fad fa-file-circle-check fs-2"></i>
                                    </span>
                                    <span class="indicator-label">Kembali</span>
                                    <span class="indicator-progress">Sila Tunggu...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </a>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Card body-->
                            </div>';
    }

		echo '<div class="card mb-5" data-kt-sticky="true" data-kt-sticky-name="catatan" data-kt-sticky-offset="{default: false, lg: \'200px\'}" data-kt-sticky-width="{lg: \'250px\', lg: \'300px\'}" data-kt-sticky-left="auto" data-kt-sticky-top="265px" data-kt-sticky-animation="true" data-kt-sticky-zindex="96">
			<!--begin::Card body-->
			<div class="card-body p-10">
				<!--begin::Actions-->
				<div class="mb-5">
					<div class="d-flex flex-column">
						<!--begin::Label-->
						<label class="d-flex d-inline-block position-relative justify-content-center align-items-center fs-5 fw-bold mb-3">

							<span class="d-inline-block mb-2">Catatan</span>

							<span class="d-inline-block position-absolute h-2px bottom-0 end-0 start-0 bg-danger translate rounded"></span>

						</label>
						<!--end::Label-->
						<div class="fv-row">
							<span type="text" class="fs-6 text-gray-800" placeholder="Catatan..." name="letter-comment">comment</span>
						</div>
					</div>
				</div>

				<!--end::Actions-->
			</div>
			<!--end::Card body-->
		</div>';
		}
		?>


		<!--end::Card-->
    </div>
    <!--end::Sidebar-->
</div>
<!--end::Layout-->