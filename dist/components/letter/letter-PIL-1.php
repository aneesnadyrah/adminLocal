<div class="letter">


<?php

$systemId = $_GET['sid'];
$letterId = $_GET['lid'];

$getletterNo = Permitting::getLetterNo($letterId);
$letterNo = $getletterNo['letter_ref_no'];
$authId = $getletterNo['authority_id'];
$authGroup = $getletterNo['group'];
// var_dump($authGroup);

//data from flw_generated_letter
$generateInfo = Permitting::generateLetter($systemId,$letterId,3)[0];
$projectTitle = Permitting::titleLetter($systemId);

if ($generateInfo['on_behalf'] == 't') {
    $bagiPihakPosition = '<span class="fs-5 lh-base">b.p. ' . ucwords(strtolower($generateInfo['on_behalf_position'])) .'</span>';
} else if ($generateInfo['on_behalf'] == 'f') {
    $bagiPihakPosition = '';
}

//data from public.letter_listing
$review = Permitting::reviewLetter($systemId,$letterId);
$modalId = $review[0]['StatusID'].$review[0]['ID'].$review[0]['LetterStatusID'].$review[0]['LetterID'];
$authorityId = $review[0]['AuthorityId'];
$flw_authority_id = $review[0]['flw_authority_id'];
// print_r($authorityId);
// $authorityIdsArray = explode(",", trim($authorityIds, "{}"));

    //Start to convert date into hijri date
    $formattedDate = date("d-m-Y", strtotime($review[0]['LetterDate']));

    // make api request to chagne date into hijri date
    $apiRequest = ExternalApi::makeApiRequest( 'https://api.aladhan.com/v1/gToH/'.$formattedDate, null,  3, 19,'GET');

    $hijriDate = '';

    if($apiRequest['error'] === "none"){
        // Decode the response JSON
        $hijriResult = json_decode($apiRequest['response'], true);

        $hijriMonthNumber = $hijriResult['data']['hijri']['month']['number'];

        $hijriMonth = '';

        if($hijriMonthNumber === 1){
            $hijriMonth = 'Muharam';
        } else if ($hijriMonthNumber === 2){
            $hijriMonth = 'Safar';
        } else if ($hijriMonthNumber === 3){
            $hijriMonth = 'Rabiulawal';
        } else if ($hijriMonthNumber === 4){
            $hijriMonth = 'Rabiulakhir';
        } else if ($hijriMonthNumber === 5){
            $hijriMonth = 'Jamadilawal';
        } else if ($hijriMonthNumber === 6){
            $hijriMonth = 'Jamadilakhri';
        } else if ($hijriMonthNumber === 7){
            $hijriMonth = 'Rejab';
        } else if ($hijriMonthNumber === 8){
            $hijriMonth = 'Syaaban';
        } else if ($hijriMonthNumber === 9){
            $hijriMonth = 'Ramadan';
        } else if ($hijriMonthNumber === 10){
            $hijriMonth = 'Syawal';
        } else if ($hijriMonthNumber === 11){
            $hijriMonth = 'Zulkaedah';
        } else if ($hijriMonthNumber === 12){
            $hijriMonth = 'Zulhijah';
        } else {
            $hijriMonth = '';
        }

        $hijriDate = $hijriResult['data']['hijri']['day'] . ' ' . $hijriMonth . ' ' . $hijriResult['data']['hijri']['year'] . 'H';

    } else {
        $hijriDate = '';
    }



// foreach($authorityIdsArray as $row){
    $roadNameTitle = [];
    // $roadNameList = [];
    // $workMethodList = [];
    $authorityDetails = Permitting::filterAuthority($authorityId);

    $reportInfo = Permitting::getRoadInvolved($systemId,$authorityId);

    // var_dump($reportInfo);

    $eachRoadAndDist = [];
    $categoryRoad = [];

    foreach($reportInfo as $key => $rowing){
        $roadName = $rowing['road_name'];
        $roadLength = $rowing['road_length'];
        $roadCategory = $rowing['owner_category'];

        $roadNameTitle[] = $roadName;

        $entry = [
            'name' => $roadName,
            'length' => $roadLength
        ];

        $eachRoadAndDist[] = $entry;

        $categoryRoad[] = $roadCategory;

    }

    // var_dump($eachRoadAndDist);


    //string of road name
    $uniqueroadNameTitle = array_values(array_unique($roadNameTitle));
    $roadNameTitleString = implode(', ', $uniqueroadNameTitle);

    $methodStateApproval = [];
    $methodNormal = [];

    //data work method from RP to display in letter
    foreach(Permitting::workMethodRP($systemId,$authId) as $item){
        if($item['method'] === 'CW'){
            $methodStateApproval[] = $item['name'];
        } else if(!empty($item['road_crossing'])){
            if($item['method'] === 'OH'){
                $methodStateApproval[] = $item['name'].' Merentangi Jalan';
            } else {
                $methodNormal[] = $item['name'].' Merentangi Jalan';
            }
        } else {
            $methodNormal[] = $item['name'];
        }
    }

    // var_dump($methodStateApproval);
    // var_dump('<br>');
    // var_dump($methodNormal);

    //all workmethod without spliting with state method
    $workMethodList = array_merge($methodNormal,$methodStateApproval);

    $uniqueworkMethodList = array_values(array_unique($workMethodList));

    // var_dump($uniqueworkMethodList);

    $workMethodString = '';

    $counting = count($uniqueworkMethodList);
    if ($counting == 1) {
        $workMethodString = $uniqueworkMethodList[0];
    } elseif ($counting == 2) {
        $workMethodString = implode(' dan ', $uniqueworkMethodList);
    } else {
        $lastworkMethodList = array_pop($uniqueworkMethodList);
        $workMethodString = implode(', ', $uniqueworkMethodList) . ' dan ' . $lastworkMethodList;
    }

    // $workMethodString = implode(', ', $uniqueworkMethodList);

    // $roadNameList = '<div ><span class="fs-5 ps-10 pe-10 w-100px">i.</span></div><div>
    // 	<span class="fs-5" >'.$roadNameTitleString .' : </span><span class="fs-5" ><b>'.$workMethodString.'</b></span><span class="fs-5" ><b> ('.$review[0]['Length'].' meter)</b></span>
    // 	</div>';
    $roadNameList = '';




    $TitleSlot = '';
    $SloganSlot = '';
    $SecondHeader = '';
    $authorityState = '';
    $letterLine1 = '';
    $letterLine2 = '';
    $letterLine3 = '';
    $letterDateTable3 = '';
    $letterLine4 = '';
    $standardMethod = [];
    $stateMethod = [];
    $countMethod = 0;
    $salinanKepadaJKRNegeri = '';
    $beforeSign = '';
    $nameKUNbefore = '';
    $nameKUNafter = '';
    $endingLine = '';
    $columnHijraDate = '';



    if ($appsTitle == 'UCIDOS'){

        $TitleSlot = '<span class="fw-bold fs-5 mb-0" style="text-align: justify;">'.strtoupper(strtolower($projectTitle[0]['ProjectTitle'])).'</span>
        <span class="fs-5 mb-0">Permohonan Izin Lalu ('.$roadNameTitleString.')</span>';
        $SloganSlot = '';
        $endingLine = 'Kerjasama, perhatian serta kelulusan dari pihak tuan amatlah kami hargai dan diucapkan ribuan terima kasih.';
        $beforeSign = 'Dengan hormatnya,';
        $SecondHeader = '<div class="d-flex justify-content-start mb-5 d-none d-print-block">
                                <!--begin::Section-->
                                <div class="mw-450px">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-0 lh-sm">
                                        <div class="fs-5 pe-4">Ruj. Kami</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-300px fs-5">
                                            <!-- KUP/OPSBF_MNG/Bil.(29)2023 -->
                                            '.$letterNo.'</div>
                                    </div>
                                    <div class="d-flex flex-stack mb-0 lh-sm">
                                        <div class="fs-5 pe-4">Muka Surat</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-300px fs-5">
                                        2/2
                                        </div>
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <span class="fw-bold fs-5 mb-0 d-none d-print-block lh-sm" style="text-align: justify;">'.strtoupper(strtolower($projectTitle[0]['ProjectTitle'])).'</span>
                            <span class="fs-5 mb-0 d-none d-print-block "lh-sm>Permohonan Izin Lalu ('.$roadNameTitleString.')</span>

                            <!--begin::Separator-->
                            <div class="separator border-dark fw-bold mt-1 mb-5 d-none d-print-block"></div>';

        //list road method by format letter PIL
        foreach($workMethodList as $work){
            if($work === 'Penanaman Tiang Merentangi Jalan' || $work === 'Korekan Jalan Berturap'){
                $stateMethod[] = $work;
            } else {
                $standardMethod[] = $work;
            }
        }

        // Format the work methods names based on count
        $count = count($standardMethod);
        if ($count == 1) {
            $standardMethodString = $standardMethod[0];
        } elseif ($count == 2) {
            $standardMethodString = implode(' dan ', $standardMethod);
        } else {
            $lastStandardMethod = array_pop($standardMethod);
            $standardMethodString = implode(', ', $standardMethod) . ' dan ' . $lastStandardMethod;
        }

        $count = count($stateMethod);
        if ($count == 1) {
            $stateMethodString = $stateMethod[0];
        } elseif ($count == 2) {
            $stateMethodString = implode(' dan ', $stateMethod);
        } else {
            $lastStateMethod = array_pop($stateMethod);
            $stateMethodString = implode(', ', $stateMethod) . ' dan ' . $lastStateMethod;
        }

        // $authorityState = Permitting::filterAuthority(59);
        $authorityState = (Permitting::filterAuthority(59)) ? Permitting::filterAuthority(59) : ' ';

        $nameKUNbefore = '<span class="fw-bold fs-5 lh-base">' . strtoupper(strtolower($tenant)) . '</span>';
        $nameKUNafter = '';

        //determine wether Majlis Daerah or not
        if($authGroup === 2){

            $letterLine1 = 'Dengan segala hormatnya merujuk kepada perkara di atas.';

            $letterLine2 = '<span class="fs-5 pe-10 lh-sm">2.</span>
            <span class="fs-5 lh-sm" >Untuk makluman tuan, pihak kami ingin memohon kebenaran izin lalu bagi kerja-kerja pemasangan dan penyambungan kemudahan utiliti <b>'.$review[0]['Provider'].'</b> serta pengorekan yang melibatkan jarak laluan sepanjang <b>'.$review[0]['Length'].' meter</b> sahaja. Cadangan laluan yang terlibat adalah seperti berikut:-</span>';

            $letterLine3 = '<span class="fs-5 pe-10 lh-sm">3.</span>
            <span class="fs-5 lh-sm" >Bersama-sama ini disertakan pelan cadangan jajaran laluan sebagai rujukan pihak tuan dan kelulusan tuan. Justeru itu, kami memohon pihak tuan untuk memberikan ulasan dan wang cagaran yang perlu dibayar oleh pemohon tersebut untuk tindakan kami selanjutnya.</span>';

            $letterLine4 = '<div class="fw-row mb-5 " style="text-align: justify;"><span class="fs-5 pe-10 lh-sm">4.</span>
            <span class="fs-5 lh-sm" >Sekiranya terdapat sebarang pertanyaan berhubung perkara ini, sila hubungi pegawai kami <b>' . ucwords(strtolower($generateInfo['officer_name_1'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_1'] . ')</b> atau <b>' . ucwords(strtolower($generateInfo['officer_name_2'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_2'] . ')</b> untuk keterangan lanjut.</span></div>';

            $roadNameList = '
                <div class="fw-row mb-7 mt-2 d-flex lh-sm">
                    <div class="col-1 d-flex justify-content-center">
                        <div class="col-5"></div>
                        <span class="fs-5 col-2 d-flex"></span>
                        <div class="col-5"></div>
                    </div>
                    <div class="col-11 ms-n2">
                        <span class="fs-5" >'.ucwords(strtolower($roadNameTitleString)) .' : </span>
                        <span class="fs-5" ><b>'.$review[0]['Length'].' meter ('.$workMethodString.')</b></span>
                    </div>
                </div>';

        } else {

            $letterLine1 = 'Dengan segala hormatnya merujuk kepada perkara di atas.';

            $letterLine2 = '<span class="fs-5 pe-10 lh-sm">2.</span>
            <span class="fs-5 lh-sm" >Dimaklumkan bahawa <b>'.$review[0]['Provider'].'</b> akan menjalankan kerja pemasangan utiliti bagi projek diatas adalah seperti berikut:</span>';

            $letterLine3 = '<span class="fs-5 pe-10 lh-sm">3.</span>
            <span class="fs-5 lh-sm" >Sehubungan dengan itu, pihak kami ingin membuat Permohonan lzin Lalu bagi melaksanakan kerja ukur tanah (sempadan ROW), cerapan data bagi Pemetaan Pengesanan Utiliti (UDM) dan penandaan jajaran utiliti di kawasan terlibat. Bersama-sama ini dikemukakan dokumen-dokumen sebagaimana berikut:</span>';

            $salinanKepadaJKRNegeri = '
            <div>
                <span class="fs-5 fw-bold w-150px pe-5 lh-sm">s.k :</span>
            </div>
            <div class="d-flex flex-column ps-5">
                <span class="fw-bold fs-5 mb-0 lh-sm" style="text-transform:uppercase;">' . (isset($authorityState[0]['AuthorityName']) ? $authorityState[0]['AuthorityName'] : ' ') . '</span>
                <span class="fs-5 mb-0 lh-sm" >' . (isset($authorityState[0]['address_1']) ? $authorityState[0]['address_1'] : ' ') . ',</span>
                ' . (isset($authorityState[0]['address_2']) ? '<span class="fs-5 mb-0 lh-sm" >' . $authorityState[0]['address_2'] . ',</span>' : '') . '
                <span class="fs-5 mb-0 lh-sm" >' . (isset($authorityState[0]['postcode']) ? $authorityState[0]['postcode'] : ' ') . ', ' . (isset($authorityState[0]['DistrictName']) ? $authorityState[0]['DistrictName'] : ' ') . '</span>
                <span class="fs-5 mb-0 lh-sm" >' . (isset($authorityState[0]['state']) ? $authorityState[0]['state'] : ' ') . '</span>
                ' . (isset($authorityState[0]['pic']) ? '<span class="fs-5 fw-bold mb-5 lh-sm" >(u.p. : ' . ucwords($authorityState[0]['pic']) . ')</span>' : '') . '

            </div>';

            $letterDateTable3 = '<div class="fw-row mb-5 ">
                <div class="d-flex justify-content-center">
                    <table class="table table-bordered lh-sm" style="border: 1px solid black;">
                        <thead class="text-center">
                            <tr>
                            <th scope="col">BIL.</th>
                            <th scope="col" >PERKARA</th>
                            <th scope="col">KUANTITI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td scope="row" class="text-center">i.</td>
                            <td class="text-start">Ringkasan Projek</td>
                            <td class="text-center">1 Salinan</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center">ii.</td>
                            <td class="text-start">Jadual Kiraan Kadar Kaedah Pemasangan dan Deposit Keselamatan</td>
                            <td class="text-center">1 Salinan</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center">iii.</td>
                            <td class="text-start">Pelan Cadangan Laluan Utiliti beserta Kaedah Pemasangan Utiliti</td>
                            <td class="text-center">1 Salinan</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center">iv.</td>
                            <td class="text-start">Gambar-Gambar Lokasi</td>
                            <td class="text-center">1 Salinan</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center">v.</td>
                            <td class="text-start">Laporan Lawatan Tapak</td>
                            <td class="text-center">1 Salinan</td>
                            </tr>
                            <tr>
                            <td scope="row"  colspan="3" class="text-start">
                                <span class="text-decoration-underline lh-base">Kerja Ukur, Cerapan Data Udm dan Penandaan</span>
                                <span class ="d-block lh-base"><b>Tarikh Jangka Mula: ' . General::convertDate($generateInfo['date_start']) . '</b></span>
                                <span class ="d-block lh-base"><b>Tarikh Jangka Siap: ' . General::convertDate($generateInfo['date_finish']) . '</b></span>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>';

            //determine wether the method need approval from state or not
            if(!empty($standardMethod) && !empty($stateMethod)){

                $roadNameList = '
                <div class="fw-row mb-5 d-flex lh-sm">
                    <div class="d-flex flex-column">
                        <div class="d-flex flex-row">
                            <div class="col-1 d-flex justify-content-center">
                                <div class="col-5"></div>
                                <span class="fs-5 col-2 d-flex">i.</span>
                                <div class="col-5"></div>
                            </div>
                            <div class="col-11 ps-5">
                                <span class="fs-5" >'.$roadNameTitleString .' : </span>
                                <span class="fs-5" ><b>'.$standardMethodString.'</b></span>
                            </div>
                        </div>

                        <div class="d-flex flex-row">
                            <div class="col-1 d-flex justify-content-center">
                                <div class="col-5"></div>
                                <span class="fs-5 col-2 d-flex">ii.</span>
                                <div class="col-5"></div>
                            </div>
                            <div class="col-11 ps-5">
                                <span class="fs-5" >'.$roadNameTitleString .' : </span>
                                <span class="fs-5" ><b>'.$stateMethodString.'</b></span><span class="fs-5" > (Kelulusan adalah dibawah JKR Negeri '.$state.')</span>
                            </div>
                        </div>
                    </div>
                </div>';

                $letterLine4 = '<div class="fw-row mb-5 " style="text-align: justify;">
                <span class="fs-5 pe-10 lh-sm">4.</span>
                <span class="fs-5 lh-sm" >Seperti pihak tuan sedia maklum, pemohonan bagi <b>Kaedah '.$stateMethodString.'</b> akan dimohon kepada Jabatan Kerja Raya (JKR) Negeri '.$state.' untuk kelulusan atau ulasan selanjutnya.</span>
                </div>

                <div class="fw-row mb-5 " style="text-align: justify;">
                <span class="fs-5 pe-10 lh-sm">5.</span>
                <span class="fs-5 lh-sm" >Sekiranya terdapat sebarang pertanyaan berhubung perkara ini, sila hubungi pegawai kami <b>' . ucwords(strtolower($generateInfo['officer_name_1'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_1'] . ')</b> atau <b>' . ucwords(strtolower($generateInfo['officer_name_2'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_2'] . ')</b> untuk keterangan lanjut.</span>
                </div>';


            } else if (empty($standardMethod) && !empty($stateMethod)){

                $roadNameList = '
                <div class="fw-row mb-5 d-flex lh-sm">
                    <div class="col-1 d-flex justify-content-center">
                        <div class="col-5"></div>
                        <span class="fs-5 col-2 d-flex">i.</span>
                        <div class="col-5"></div>
                    </div>
                    <div class="col-11 ps-5">
                        <span class="fs-5" >'.$roadNameTitleString .' : </span>
                        <span class="fs-5" ><b>'.$stateMethodString.'</b></span><span class="fs-5" > (Kelulusan adalah dibawah JKR Negeri '.$state.')</span>
                    </div>
                </div>';

                $letterLine4 = '<div class="fw-row mb-5 " style="text-align: justify;">
                <span class="fs-5 pe-10 lh-sm">4.</span>
                <span class="fs-5 lh-sm" >Seperti pihak tuan sedia maklum, pemohonan bagi <b>kaedah '.$stateMethodString.'</b> akan dimohon kepada Jabatan Kerja Raya (JKR) Negeri '.$state.' untuk kelulusan atau ulasan selanjutnya.</span>
                </div>

                <div class="fw-row mb-5 " style="text-align: justify;">
                <span class="fs-5 pe-10 lh-sm">5.</span>
                <span class="fs-5 lh-sm" >Sekiranya terdapat sebarang pertanyaan berhubung perkara ini, sila hubungi pegawai kami <b>' . ucwords(strtolower($generateInfo['officer_name_1'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_1'] . ')</b> atau <b>' . ucwords(strtolower($generateInfo['officer_name_2'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_2'] . ')</b> untuk keterangan lanjut.</span>
                </div>';





            } else if (!empty($standardMethod) && empty($stateMethod)){

                $roadNameList = '
                <div class="fw-row mb-5 d-flex lh-sm">
                    <div class="col-1 d-flex justify-content-center">
                        <div class="col-5"></div>
                        <span class="fs-5 col-2 d-flex">i.</span>
                        <div class="col-5"></div>
                    </div>
                    <div class="col-11 ps-5">
                        <span class="fs-5" >'.$roadNameTitleString .' : </span>
                        <span class="fs-5" ><b>'.$standardMethodString.'</b></span>
                    </div>
                </div>';




                $letterLine4 = '<div class="fw-row mb-5 " style="text-align: justify;"><span class="fs-5 pe-10 lh-sm">4.</span>
                <span class="fs-5 lh-sm" >Sekiranya terdapat sebarang pertanyaan berhubung perkara ini, sila hubungi pegawai kami <b>' . ucwords(strtolower($generateInfo['officer_name_1'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_1'] . ')</b> atau <b>' . ucwords(strtolower($generateInfo['officer_name_2'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_2'] . ')</b> untuk keterangan lanjut.</span></div>';

            }

        }

    } else if ($appsTitle == 'KITER'){

        $kuttTitleRoad = [];
        $kuttListRoad = [];
        foreach($eachRoadAndDist as $numbering => $roadData){

            $numbers = $numbering + 1;
            $kuttTitleRoad[] = '<span class="fw-semibold fs-5 mb-0 lh-sm" style="text-align: justify;">'.$numbers.') ' . $roadData['name'] . '</span><br>';

            $kuttListRoad[] = '<tbody>
                    <tr>
                    <td scope="row" class="text-center p-1">'.$numbers.'.</td>
                    <td class="text-start p-1 ps-3">' . ucwords(strtolower($roadData['name'])) . '</td>
                    <td class="text-center p-1">' . $roadData['length'] . '</td>
                    </tr>
                </tbody>';
        }

        $kuttTitleRoadString = implode('', $kuttTitleRoad);
        $kuttListRoadString = implode('', $kuttListRoad);

        $TitleSlot = '
        <div class="row">
        <div class="col-2">
            <span class="d-flex fw-bold fs-5 mb-0 lh-sm">PROJEK/KERJA</span>

        </div>
        <div class="col-1">
        <span class="d-flex justify-content-center fw-bold fs-5 mb-0 lh-sm ">:</span>
        </div>
        <div class="col-9 ms-n10">
            <span class="fw-bold fs-5 mb-0 lh-sm" style="text-align: justify;">'.strtoupper(strtolower($projectTitle[0]['ProjectTitle'])).'</span>
        </div>
        <div class="col-2">
            <span class="d-flex fw-bold fs-5 mb-0 lh-sm">UTILITI</span>

        </div>
        <div class="col-1">
        <span class="d-flex justify-content-center fw-bold fs-5 mb-0 lh-sm ">:</span>
        </div>
        <div class="col-9 ms-n10">
            <span class="fw-bold fs-5 mb-0 lh-sm" style="text-align: justify;">'.strtoupper(strtolower($review[0]['Provider'])).'</span>
        </div>
        <div class="col-2">
            <span class="d-flex fw-bold fs-5 mb-0 lh-sm">PERKARA</span>

        </div>
        <div class="col-1">
        <span class="d-flex justify-content-center fw-bold fs-5 mb-0 lh-sm">:</span>
        </div>
        <div class="col-9 ms-n10">
            <span class="fw-bold fs-5 mb-0 lh-sm" style="text-align: justify;">PERMOHONAN IZIN LALU</span>
            <br>
            '.$kuttTitleRoadString.'
        </div>
        </div>';
        $SloganSlot = '<span class="fw-bold fs-5 mb-5 lh-base">"TERENGGANU MAJU, BERKAT DAN SEJAHTERA"</span>';
        $endingLine = 'Kerjasama serta perhatian daripada pihak tuan amatlah kami hargai dan diucapkan ribuan terima kasih.';
        $beforeSign = 'Dengan hormatnya,';
        $SecondHeader = '<div class="d-flex justify-content-start mb-5 d-none d-print-block">
                                <!--begin::Section-->
                                <div class="mw-450px">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-0">
                                        <div class="fs-5 pe-4 lh-sm">Ruj. Kami</div>
                                        <div class="fs-5 px-5 lh-sm">:</div>
                                        <div class="position-relative d-flex align-items-center w-300px fs-5 lh-sm">
                                            <!-- KUP/OPSBF_MNG/Bil.(29)2023 -->
                                            '.$letterNo.'</div>
                                    </div>
                                    <div class="d-flex flex-stack mb-0">
                                        <div class="fs-5 pe-11 lh-sm">Tarikh</div>
                                        <div class="fs-5 px-5 lh-sm">:</div>
                                        <div class="position-relative d-flex align-items-center w-300px fs-5 lh-sm">
                                        '.General::convertDate($review[0]['LetterDate']).'
                                        </div>
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>

                            <!--begin::Separator-->
                            <div class="separator border-dark fw-bold mt-1 mb-5 d-none d-print-block"></div>';



        // $roadNameList = '<div ><span class="fs-5 ps-10 pe-10 w-100px">i.</span></div><div>
        //         <span class="fs-5" >' . $roadNameTitleString . ' : </span><span class="fs-5" ><b>' . $workMethodString . '</b></span><span class="fs-5" ><b> (' . $review[0]['Length'] . ' meter)</b></span>
        //         </div>';
        $roadNameList = '
            <div class="fw-row d-flex flex-center ">
                <div class="d-flex justify-content-center w-450px">
                    <table class="table table-bordered lh-sm" style="border: 1px solid black;">
                        <thead class="text-center">
                            <tr>
                            <th scope="col" class="p-1">BIL.</th>
                            <th scope="col" class="p-1">NAMA JALAN</th>
                            <th scope="col" class="p-1">JARAK(M)</th>
                            </tr>
                        </thead>
                        '.$kuttListRoadString.'
                    </table>
                </div>
            </div>
                    ';

        $letterLine1 = 'Dengan segala hormatnya, saya merujuk kepada perkara di atas.';

        $roadCategory = '';

        if(!empty($categoryRoad)){
            $categoryRoadValue = array_values(array_unique($categoryRoad));

            // Check the count of unique values
            $count = count($categoryRoadValue);

            if ($count == 1 && in_array(1, $categoryRoadValue)) {
                $roadCategory = 'di Jalan Negeri';
            } elseif ($count == 1 && in_array(2, $categoryRoadValue)) {
                $roadCategory = 'di Jalan Persekutuan';
            } elseif ($count == 2) {
                $roadCategory = 'di Jalan Persekutuan dan Jalan Negeri';
            } else {
                // Handle other cases or conditions here
                // For example, if $count is 0, or if the values are not 1 or 2
                $roadCategory = '';
            }
        } else {
            $roadCategory = '';
        }


        $letterLine2 = '
        <span class="fs-5 pe-10 lh-sm">2.</span>
        <span class="fs-5 lh-sm" >Dimaklumkan bahawa <b>'.$review[0]['Provider'].'</b> akan menjalankan kerja pemasangan utiliti bagi <b>'.ucwords(strtolower($projectTitle[0]['ProjectTitle'])).'</b> '.$roadCategory.' sepanjang <b>'.$review[0]['Length'].' meter</b> bagi cadangan laluan yang terlibat seperti berikut : - </span>';

        $letterLine3 = '<span class="fs-5 pe-10 lh-sm">3.</span>
        <span class="fs-5 lh-sm" >Sehubungan dengan itu, pihak kami ingin membuat Permohonan lzin Lalu bagi melaksanakan kerja ukur tanah (sempadan ROW), cerapan data bagi Pemetaan Pengesanan Utiliti (UDM) dan penandaan jajaran utiliti di kawasan terlibat. Bersama-sama ini dikemukakan dokumen-dokumen sebagaimana berikut:</span>';

        $pointV = '<tr>
        <td scope="row" class="text-start p-1 fs-5">(v)</td>
        <td class="p-1 ps-3 fs-5 text-start">Pengiraan deposit keselamatan</td>
        </tr>';

        $pointV = '';

        if($authGroup === 4){
            $pointV = '';
        } else {
            $pointV = '<tr>
                <td scope="row" class="text-start p-1 fs-5">(v)</td>
                <td class="p-1 ps-3 fs-5 text-start">Pengiraan deposit keselamatan</td>
                </tr>';
        }

        $letterDateTable3 = '<div class="fw-row mb-5 d-flex flex-center">
            <div class="d-flex justify-content-center w-400px">
                <table class="table lh-sm" style="border: 1px solid white;">
                    <tbody>
                        <tr>
                        <td scope="row" class="text-start p-1 fs-5">(i)</td>
                        <td class="p-1 ps-3 fs-5 text-start">Pelan cadangan laluan utiliti</td>
                        </tr>
                        <tr>
                        <td scope="row" class="text-start p-1 fs-5">(ii)</td>
                        <td class="p-1 ps-3 fs-5 text-start">Kaedah pemasangan utiliti</td>
                        </tr>
                        <tr>
                        <td scope="row" class="text-start p-1 fs-5">(iii)</td>
                        <td class="p-1 ps-3 fs-5 text-start">Gambar-gambar lokasi</td>
                        </tr>
                        <tr>
                        <td scope="row" class="text-start p-1 fs-5">(iv)</td>
                        <td class="p-1 ps-3 fs-5 text-start">Tarikh jangka mula pada <b>' . General::convertDate($generateInfo['date_start']) . '</b> dan <br> jangka siap pada <b>' . General::convertDate($generateInfo['date_finish']) . '</b></td>
                        </tr>
                        '.$pointV.'
                        <tr>
                        <td scope="row" class="text-start p-1 fs-5">(vi)</td>
                        <td class="p-1 ps-3 fs-5 text-start">Borang laporan lawatan tapak</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>';

            $letterLine4 = '<div class="fw-row mb-5 " style="text-align: justify;"><span class="fs-5 pe-10 lh-sm">4.</span>
            <span class="fs-5 lh-sm" >Sekiranya terdapat sebarang kemusykilan berhubung perkara ini, sila hubungi pegawai kami, <b>' . ucwords(strtolower($generateInfo['officer_name_1'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_1'] . ')</b> atau <b>' . ucwords(strtolower($generateInfo['officer_name_2'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_2'] . ')</b> untuk keterangan lanjut.</span></div>';


            // $authorityState = Permitting::filterAuthority(40);
            $authorityState = (Permitting::filterAuthority(40)) ? Permitting::filterAuthority(40) : ' ';

            $salinanKepadaJKRNegeri = '';

            $nameKUNbefore = '<span class="fw-bold fs-5 lh-base">' . strtoupper(strtolower($tenant)) . '</span>';
            $nameKUNafter = '';

            if($hijriDate !== ''){
                $columnHijraDate = '<div class="d-flex flex-stack">
                    <div class="fs-5 lh-sm">Bersamaan</div>
                    <div class="fs-5 px-5 lh-sm">:</div>
                    <div class="position-relative d-flex align-items-center w-300px fs-5 lh-sm">'.$hijriDate.'</div>
                </div>';
            } else {
                $columnHijraDate = '';
            }

    } else if($appsTitle == 'KUDRAT'){
        $TitleSlot = '
        <div class="row">
        <div class="col-2">
            <span class="d-flex fw-bold fs-5 mb-0 lh-sm">PROJEK/KERJA</span>

        </div>
        <div class="col-1">
        <span class="d-flex justify-content-center fw-bold fs-5 mb-0 lh-sm ">:</span>
        </div>
        <div class="col-9 ms-n5">
            <span class="fw-bold fs-5 mb-0 lh-sm" style="text-align: justify;">'.strtoupper(strtolower($projectTitle[0]['ProjectTitle'])).'</span>
        </div>
        <div class="col-2">
            <span class="d-flex fw-bold fs-5 mb-0 lh-sm">PERKARA</span>

        </div>
        <div class="col-1">
        <span class="d-flex justify-content-center fw-bold fs-5 mb-0 lh-sm">:</span>
        </div>
        <div class="col-9 ms-n5">
            <span class="fw-bold fs-5 mb-0 lh-sm" style="text-align: justify;">PERMOHONAN IZIN LALU</span>
        </div>
        </div>';
        $SloganSlot = '<span class="fw-bold fs-5 lh-base">"WAWASAN KEMAKMURAN BERSAMA 2030"</span>
        <span class="fw-bold fs-5 mb-5 lh-base">"BERKHIDMAT UNTUK NEGARA"</span>';
        $endingLine = 'Kerjasama serta perhatian daripada pihak tuan amatlah kami hargai dan diucapkan ribuan terima kasih.';
        $beforeSign = 'Saya yang menjalankan amanah,';
        $SecondHeader = '<div class="d-flex justify-content-start mb-5 d-none d-print-block">
                                <!--begin::Section-->
                                <div class="mw-450px">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-0 lh-sm">
                                        <div class="fs-5 pe-4">Ruj. Kami</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-300px fs-5">
                                            <!-- KUP/OPSBF_MNG/Bil.(29)2023 -->
                                            '.$letterNo.'</div>
                                    </div>
                                    <div class="d-flex flex-stack mb-0 lh-sm">
                                        <div class="fs-5 pe-4">Muka Surat</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-300px fs-5">
                                        2/2
                                        </div>
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <span class="fw-bold fs-5 mb-0 d-none d-print-block lh-sm" style="text-align: justify;">'.strtoupper(strtolower($projectTitle[0]['ProjectTitle'])).'</span>
                            <span class="fs-5 mb-0 d-none d-print-block "lh-sm>Permohonan Izin Lalu ('.$roadNameTitleString.')</span>

                            <!--begin::Separator-->
                            <div class="separator border-dark fw-bold mt-1 mb-5 d-none d-print-block"></div>';


    //     $roadNameList = '
    //         <div class="col-1 d-flex justify-content-center">
    //             <div class="col-5"></div>
    //             <span class="fs-5 col-2 d-flex">i.</span>
    //             <div class="col-5"></div>
    //         </div>
    //         <div class="col-11 ps-5">
    //             <span class="fs-5" >'.$roadNameTitleString .' : </span>
    //             <span class="fs-5" ><b>'.$workMethodString.'</b></span>
    //             <span class="fs-5" ><b> (' . $review[0]['Length'] . ' meter)</b></span>
    //         </div>
    //    ';

        $letterLine1 = 'Dengan segala hormatnya, saya merujuk kepada perkara di atas.';

        $roadCategory = '';

        if(!empty($categoryRoad)){

            $categoryRoadValue = array_values(array_unique($categoryRoad));

            // Check the count of unique values
            $count = count($categoryRoadValue);

            if ($count == 1 && in_array(1, $categoryRoadValue)) {
                $roadCategory = 'Jalan Jabatan Negeri';
            } elseif ($count == 1 && in_array(2, $categoryRoadValue)) {
                $roadCategory = 'Jalan Jabatan Persekutuan';
            } elseif ($count == 2) {
                $roadCategory = 'Jalan Jabatan Persekutuan dan Jalan Jabatan Negeri';
            } else {
                // Handle other cases or conditions here
                // For example, if $count is 0, or if the values are not 1 or 2
                $roadCategory = '';
            }

        } else {
            $roadCategory = '';
        }

        $letterLine2 = '<span class="fs-5 pe-10 lh-sm">2.</span>
        <span class="fs-5 lh-sm" >Dimaklumkan bahawa <b>'.$review[0]['Provider'].'</b> akan menjalankan kerja pemasangan utiliti bagi <b>Projek '.ucwords(strtolower($projectTitle[0]['ProjectTitle'])).'</b> di '.$roadCategory.' <b>'.ucwords(strtolower($roadNameTitleString)).' </b> sepanjang <b>'.$review[0]['Length'].' meter</b>.</span>';

        $letterLine3 = '<span class="fs-5 pe-10 lh-sm">3.</span>
        <span class="fs-5 lh-sm" >Sehubungan dengan itu, pihak kami ingin membuat Permohonan lzin Lalu. Bersama-sama ini dikemukakan dokumen-dokumen sebagaimana berikut:</span>';

        $letterDateTable3 = '<div class="fw-row mb-5 ">
                <div class="d-flex justify-content-center px-10">
                    <table class="table table-bordered lh-sm" style="border: 1px solid black;">
                        <thead class="text-center">
                            <tr>
                            <th scope="col" class="p-1">BIL.</th>
                            <th scope="col" class="p-1">PERKARA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td scope="row" class="text-center p-1 ">1.</td>
                            <td class="p-1 ps-3 text-start">Pelan Cadangan Laluan</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center p-1 ">2.</td>
                            <td class="p-1 ps-3 text-start">Kaedah Pemasangan Utiliti</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center p-1 ">3.</td>
                            <td class="p-1 ps-3 text-start">Gambar-Gambar Lokasi</td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center p-1 ">4.</td>
                            <td class="p-1 ps-3 text-start">Tarikh jangka mula pada <b>' . General::convertDate($generateInfo['date_start']) . '</b> dan jangka siap <b>' . General::convertDate($generateInfo['date_finish']) . '</b></td>
                            </tr>
                            <tr>
                            <td scope="row" class="text-center p-1 ">5.</td>
                            <td class="p-1 ps-3 text-start">Laporan Lawatan Tapak</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>';

            $letterLine4 = '<div class="fw-row mb-5 " style="text-align: justify;"><span class="fs-5 pe-10 lh-sm">4.</span>
            <span class="fs-5 lh-sm" >Sekiranya terdapat sebarang kemusykilan berhubung perkara di atas, sila hubungi pegawai kami, <b>' . ucwords(strtolower($generateInfo['officer_name_1'])) . '</b> di talian <b>(' . $generateInfo['officer_contact_1'] . ')</b> atau <b>' . ucwords(strtolower($generateInfo['officer_name_2'])) . '</b> ditalian <b>(' . $generateInfo['officer_contact_2'] . ')</b> untuk keterangan lanjut.</span></div>';


            // $authorityState = Permitting::filterAuthority(59);
            $authorityState = (Permitting::filterAuthority(59)) ? Permitting::filterAuthority(59) : ' ';

            $salinanKepadaJKRNegeri = '';
            $nameKUNbefore = '';
            $nameKUNafter = '<span class="fw-bold fs-5 lh-base">' . strtoupper(strtolower($tenant)) . '</span>';

            if($hijriDate !== ''){
                $columnHijraDate = '<div class="d-flex flex-stack">
                    <div class="fs-5 lh-sm">Bersamaan</div>
                    <div class="fs-5 px-5 lh-sm">:</div>
                    <div class="position-relative d-flex align-items-center w-300px fs-5 lh-sm">'.$hijriDate.'</div>
                </div>';
            } else {
                $columnHijraDate = '';
            }
    };

    echo '

            <section class="sheet padding-10mm main-section" >
                <!--begin::Wrapper-->
                <div class="d-flex flex-column align-items-start flex-sm-row">
                    <!--begin::Input group-->
                    <div class="d-flex align-items-center justify-content-end flex-equal order-3 fw-row">
                        <!--begin::Input-->
                        <div class="position-relative d-flex align-items-center w-200px">
                            <img class="mw-200px" src="assets/media/logos/'.$tenant.'-default.svg" alt="image" />
                        </div>
                        <!--end::Input-->
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Top-->

                <!--begin::Separator-->
                <div class="separator border-dark fw-bold my-5"></div>
                <!--end::Separator-->

                <!--begin::Wrapper-->

                <!--begin::Wrapper-->
                <div class="d-flex justify-content-end mb-5 lh-sm">
                    <!--begin::Section-->
                    <div class="mw-450px">
                        <!--begin::Item-->
                        <div class="d-flex flex-stack mb-0">
                            <div class="fs-5 pe-4 lh-sm">Ruj. Kami</div>
                            <div class="fs-5 px-5 lh-sm">:</div>
                            <div class="position-relative d-flex align-items-center w-300px fs-5 lh-sm">
                                <!-- KUP/OPSBF_MNG/Bil.(29)2023 -->
                                '.$letterNo.'</div>
                        </div>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <div class="d-flex flex-stack">
                            <div class="fs-5 pe-11 lh-sm">Tarikh</div>
                            <div class="fs-5 px-5 lh-sm">:</div>
                            <div class="position-relative d-flex align-items-center w-300px fs-5 lh-sm">'.General::convertDate($review[0]['LetterDate']).'</div>
                        </div>
                        <!--end::Item-->
                        '.$columnHijraDate.'
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Wrapper-->

                <!--begin::content letter-->
                <!--<div class="row gt-20 mb-0">-->

                <div class ="d-flex flex-column">
                    <span class="fw-bold fs-5 mb-0 lh-sm" style="text-transform:uppercase;">'.$authorityDetails[0]['AuthorityName'].'</span>
                    <span class="fs-5 mb-0 lh-sm" >'.$authorityDetails[0]['address_1'].',</span>
                    '.(($authorityDetails[0]['address_2']) ? '<span class="fs-5 mb-0  lh-sm " >'.$authorityDetails[0]['address_2'].',</span>' : '') .'
                    <span class="fs-5 mb-0 lh-sm" >'.$authorityDetails[0]['postcode'].', '.$authorityDetails[0]['DistrictName'].'</span>
                    <span class="fs-5 mb-0 lh-sm" >'.$authorityDetails[0]['state'].'</span>
                    <span class="fs-5 fw-bold mb-5 lh-sm" >(u.p. : '.(($authorityDetails[0]['pic']) ? ucwords($authorityDetails[0]['pic']) : '').')</span>
                </div>

                    <div class ="d-flex flex-column">
                    <span class="fs-5 mb-5 lh-sm">Tuan,</span>
                    '.$TitleSlot.'
                    <!--begin::Separator-->
                    <div class="separator border-dark fw-bold mt-1 mb-5"></div>
                    <!--end::Separator-->
                    </div>
                    <div class ="d-flex flex-column">
                    <span class="fs-5 mb-5 lh-sm" style="text-align: justify;">'.$letterLine1.'</span>
                    </div>

                    <div class="fw-row mb-5" style="text-align: justify;">
                    '.$letterLine2.'
                    </div>


                    '.$roadNameList.'


                    <div class="fw-row mb-5" style="text-align: justify;">
                        '.$letterLine3.'
                    </div>

                    '.$letterDateTable3.'
                <!--</div>-->

                <!--<div class="row gb-20 mb-5">-->


                    '.$letterLine4.'

                    <div class="fw-row mb-5 d-flex flex-column" style="text-align: justify;">
                    <span class="fs-5 lh-sm" style="text-align: justify;">'.$endingLine.'</span>
                    </div>

                    <div class="fw-row mb-5 d-flex flex-column" style="text-align: justify;">
                    <span class="fs-5 mb-5 lh-sm">Sekian.</span>
                    '.$SloganSlot.'


                    <span class="fs-5 lh-base">'.$beforeSign.'</span>
                    '.$nameKUNbefore.'
                    <!-- <canvas id="signature" name="signature"></canvas> -->
                    <span class="my-8 mt-20"></span>
                    <span class="fw-bold fs-5 lh-base">('.strtoupper(strtolower($generateInfo['approval_name'])).')</span>
                    <span class="fs-5 lh-base">'.ucwords(strtolower($generateInfo['approval_position'])).'</span>


                    '.$nameKUNafter.'

                    '.($bagiPihakPosition ?? '').'
                    </div>
                    <div class="d-flex flex-row mt-15">
                        '.$salinanKepadaJKRNegeri.'
                    </div>



                    <!--end::content letter-->

                    <input type="text" name="system-id"  value="'.$systemId.'" hidden>
                    <input type="text" name="letter-no"  value="'.$letterNo.'" hidden>
                    <input type="text" name="authority-id"  value="'.$authorityId.'" hidden>
                    <input type="text" name="appl-date"  value="'.$review[0]['ApplDate'].'" hidden>
                    <input type="text" name="letter-date"  value="'.$review[0]['LetterDate'].'" hidden>
                    <input type="text" name="road-length"  value="'.$review[0]['Length'].'" hidden>
                <!--</div>-->
            </section>
    ';



?>

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
<section class="sheet padding-10mm secondary-section" >
                    <div class="d-flex flex-column align-items-start flex-sm-row mb-5">
                        <!--begin::Input group-->
                        <div class="d-flex align-items-center justify-content-end flex-equal order-3 fw-row">
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center justify-content-end w-200px">
                                <img class="mw-175px" src="assets/media/logos/<?php echo $tenant; ?>-default.svg"
                                    alt="image" />
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>

                    <h2 class="text-center text-decoration-underline">RINGKASAN PROJEK</h2>

                    <!-- <div class="fv-row mb-5 "> -->
                        <!-- <div class="d-flex flex-column justify-content-center"> -->
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

                        <!-- </div> -->
                    <!-- </div> -->




                                <input type="text" name="reference-no" id="reference-no" value="<?php echo $detail[0]['reference_no']; ?>" hidden />
                    <input type="text" name="system-id" id="system-id" value="<?php echo $systemId ?>" hidden />
                                    </section>
<?php
$systemId = $_GET['sid'];

// $flwAuthId = $_GET['aid'];

//get from letter review function
$flwAuthId = strval($flw_authority_id);
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

// function addDataToRpData(&$rpData, $authorityId, $code, $method, $value, $unit) {

//     $rpData[] = array(
//         'authority_id' => $authorityId,
//         'shortMethod' => $code,
//         'method' => $method,
//         'value' => $value,
//         'unit' => $unit,
//     );

// }


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
if ($depositListing) {
    $pageId = $listData['deposit_status'] . $listData['id'];

    // var_dump($listData);
    ?>

<section class="sheet padding-10mm">
        <?php

        foreach ($uniqueAuthorityIds as $no => $id) {
            $data = Permitting::dataWc($systemId, $id);

            ?>

                    <div class="fv-row my-5 ">
                        <div class="d-flex flex-column justify-content-center">

                            <table class="table table-bordered"
                                style="border:1px solid black;border-collapse:collapse;width: 100%;">
                                <!-- <thead style="visibility: collapse;border:1px solid white;">
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
                                </thead> -->

                                <?php
                                if ($appsTitle == 'KITER' || $appsTitle == 'KUDRAT') {
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
                                                src="assets/media/logos/' . $tenant . '-default.svg" alt="image" />
                                        </td>
                                        <td colspan="5" class="text-center align-items-center fs-7 py-10 fw-bold"
                                            style="text-decoration:underline;width:70%;">KIRAAN WANG CAGARAN</td>
                                    </tr>
                                </tbody>';

                                } else if ($appsTitle == 'UCIDOS') {
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
                                        <td class="text-start fs-7 p-1 align-items-center" style="width:25%;">Tajuk Projek</td>
                                        <td colspan="6" class="text-start fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['project_title']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">2
                                        </td>
                                        <td class="text-start fs-7 p-1 align-items-center" style="width:25%;">No. Ruj. <?php echo $tenant; ?></td>
                                        <td colspan="6" class="text-start fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['reference_no']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">3
                                        </td>
                                        <td class="text-start fs-7 p-1 align-items-center" style="width:25%;">Lokasi</td>
                                        <td colspan="6" class="text-start fs-7 p-1  align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php
                                            $roadInvolved = Permitting::getRoadInvolved($systemId, $id);
                                            $nameRoad = [];
                                            foreach ($roadInvolved as $rowing) {
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
                                        <td class="text-start fs-7 p-1 align-items-center" style="width:25%;">Jarak</td>
                                        <td colspan="6" class="text-start fs-7 p-1  align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['involved_appl_length'] . ' METER'; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">5
                                        </td>
                                        <td class="text-start fs-7 p-1 align-items-center" style="width:25%;">Penyedia Utiliti</td>
                                        <td colspan="6" class="text-start fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[0]['provider_name']; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-1 text-center align-items-center fs-7" style="width:6.25%;">6
                                        </td>
                                        <td class="text-start fs-7 p-1 align-items-center" style="width:25%;"><?php

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
                                        <td colspan="6" class="text-start fs-7 p-1 align-items-center fw-bold"
                                            style="width:68.75;text-transform: uppercase;"><?php echo $detail[$no]['authority_name']; ?></td>
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
                                        <td colspan="3" class="text-start fs-7 ps-1 p-0 align-items-center ">Horizontal Directional
                                            Drilling (HDD)</td>
                                        <td colspan="4" class="fs-7 p-0 text-center align-items-center "></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 ps-1 p-0 align-items-center " colspan="3">(i) Jalan (Lebar
                                            carriageaway) - Merentangi jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $hdd_road_crossing = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'HDD') {
                                                $hdd_road_crossing += $datas['road_crossing'];

                                            }

                                        }

                                        if ($hdd_road_crossing !== 0) {
                                            echo $hdd_road_crossing;
                                        } else {
                                            echo '';
                                        }

                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold">
                                            <?php $total_hdd_road_crossing = Permitting::formulaWC('Kiraan HDD Road Crossing', $hdd_road_crossing);
                                            echo $total_hdd_road_crossing ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 p-0 ps-1 align-items-center " colspan="3">(ii) Sepanjang Bahu
                                            Jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $hdd_road_shoulder = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'HDD') {
                                                $hdd_road_shoulder += $datas['road_shoulder'];

                                            }

                                        }

                                        if ($hdd_road_shoulder !== 0) {
                                            echo $hdd_road_shoulder;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">0.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_hdd_road_shoulder = Permitting::formulaWC('Kiraan HDD Road Shoulder', $hdd_road_shoulder);
                                        echo $total_hdd_road_shoulder; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 p-0 ps-1 align-items-center " colspan="3">(iii) Pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $hdd_pit = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'HDD') {
                                                $hdd_pit += $datas['pit'];
                                            }

                                        }

                                        if ($hdd_pit !== 0) {
                                            echo $hdd_pit;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">8,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_hdd_pit = Permitting::formulaWC('Kiraan HDD Pit', $hdd_pit);
                                        echo $total_hdd_pit ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="fs-7 py-10 text-center align-items-center fs-7 "
                                            style="width:6.25%;">2</td>
                                        <td colspan="3" class="text-start fs-7 ps-1 p-0 align-items-center ">Micro Tunnelling/Pipe
                                            Jacking/Thrust Boring</td>
                                        <td colspan="4" class="fs-7 p-0 text-center align-items-center "></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 ps-1 p-0 align-items-center " colspan="3">(i) Jalan (Lebar
                                            carriageaway) - Merentangi jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $mt_road_crossing = 0;
                                        $pj_road_crossing = 0;
                                        $tb_road_crossing = 0;

                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'MT') {
                                                $mt_road_crossing += $datas['road_crossing'];

                                            } else if ($datas['method'] === 'PJ') {
                                                $pj_road_crossing += $datas['road_crossing'];

                                            } else if ($datas['method'] === 'TB') {
                                                $tb_road_crossing += $datas['road_crossing'];
                                            }
                                        }

                                        $mt_pj_tb_road_crossing = $mt_road_crossing + $pj_road_crossing + $tb_road_crossing;

                                        if ($mt_pj_tb_road_crossing !== 0) {
                                            echo $mt_pj_tb_road_crossing;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,800.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_mt_pj_tb_road_crossing = Permitting::formulaWC('Kiraan MT/PJ/TB Road Crossing', $mt_pj_tb_road_crossing);
                                        echo $total_mt_pj_tb_road_crossing; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fs-7 p-0 ps-1 align-items-center " colspan="3">(ii) Sepanjang Bahu
                                            Jalan</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $mt_road_shoulder = 0;
                                        $pj_road_shoulder = 0;
                                        $tb_road_shoulder = 0;

                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'MT') {
                                                $mt_road_shoulder += $datas['road_shoulder'];

                                            } else if ($datas['method'] === 'PJ') {
                                                $pj_road_shoulder += $datas['road_shoulder'];

                                            } else if ($datas['method'] === 'TB') {
                                                $tb_road_shoulder += $datas['road_shoulder'];
                                            }
                                        }

                                        $mt_pj_tb_road_shoulder = $mt_road_shoulder + $pj_road_shoulder + $tb_road_shoulder;

                                        if ($mt_pj_tb_road_shoulder !== 0) {
                                            echo $mt_pj_tb_road_shoulder;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">0.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_mt_pj_tb_road_shoulder = Permitting::formulaWC('Kiraan MT/PJ/TB Road Shoulder', $mt_pj_tb_road_shoulder);
                                        echo $total_mt_pj_tb_road_shoulder; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">(iii) Pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $mt_pit = 0;
                                        $pj_pit = 0;
                                        $tb_pit = 0;

                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'MT') {
                                                $mt_pit += $datas['pit'];

                                            } else if ($datas['method'] === 'PJ') {
                                                $pj_pit += $datas['pit'];

                                            } else if ($datas['method'] === 'TB') {
                                                $tb_pit += $datas['pit'];
                                            }
                                        }

                                        $mt_pj_tb_pit = $mt_pit + $pj_pit + $tb_pit;

                                        if ($mt_pj_tb_pit !== 0) {
                                            echo $mt_pj_tb_pit;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">12,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_mt_pj_tb_pit = Permitting::formulaWC('Kiraan MT/PJ/TB Pit', $mt_pj_tb_pit);
                                        echo $total_mt_pj_tb_pit; ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2" class="fs-7 py-10 text-center align-items-center fs-7 "
                                            style="width:6.25%;">3</td>
                                        <td colspan="3" class="text-start fs-7 ps-1 p-0 align-items-center ">i.Diameter Utiliti <
                                                500mm <br>Korekan Bahu Jalan (Kawasan Unpaved)</td>
                                        <td class="fs-7 text-center align-items-center fw-bold"><?php
                                        $gv_diameter_less_500 = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'GV') {
                                                $gv_diameter_less_500 += $datas['diameter_less_500'];
                                            }

                                        }

                                        if ($gv_diameter_less_500 !== 0) {
                                            echo $gv_diameter_less_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 text-center align-items-center ">meter</td>
                                        <td class="fs-7 text-center align-items-center ">200.00</td>
                                        <td class="fs-7 pe-1 text-end align-items-center fw-bold"><?php $total_gv_diameter_less_500 = Permitting::formulaWC('Kiraan GV diameter less', $gv_diameter_less_500);
                                        echo $total_gv_diameter_less_500; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">ii.Diameter Utiliti >
                                            500mm <br>Korekan Bahu Jalan (Kawasan Unpaved)</td>
                                        <td class="fs-7 text-center align-items-center fw-bold"><?php
                                        $gv_diameter_more_500 = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'GV') {
                                                $gv_diameter_more_500 += $datas['diameter_more_500'];

                                            }

                                        }

                                        if ($gv_diameter_more_500 !== 0) {
                                            echo $gv_diameter_more_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 text-center align-items-center ">meter</td>
                                        <td class="fs-7 text-center align-items-center ">330.00</td>
                                        <td class="fs-7 pe-1 text-end align-items-center fw-bold"><?php $total_gv_diameter_more_500 = Permitting::formulaWC('Kiraan GV diameter more', $gv_diameter_more_500);
                                        echo $total_gv_diameter_more_500; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 text-center align-items-center fs-7 " style="width:6.25%;">4
                                        </td>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">Tambatan/ sokongan
                                            paip air expose - hanya dibenarkan di Kawasan cerun (Kawasan potongan atau
                                            tambakan)</td>
                                        <td class="fs-7 text-center align-items-center fw-bold"><?php
                                        $tambatan = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'MS') {
                                                $tambatan += $datas['quantity'];

                                            }

                                        }

                                        if ($tambatan !== 0) {
                                            echo $tambatan;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 text-center align-items-center ">nos</td>
                                        <td class="fs-7 text-center align-items-center ">1,000.00</td>
                                        <td class="fs-7 pe-1 text-end align-items-center fw-bold"><?php $total_tambatan = Permitting::formulaWC('Kiraan Tambatan/ sokongan paip air expose', $tambatan);
                                        echo $total_tambatan; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 text-center align-items-center fs-7 " style="width:6.25%;">5
                                        </td>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">Pemasangan Tiang</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $penanaman = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'OH') {
                                                $penanaman += $datas['quantity'];

                                            }

                                        }

                                        if ($penanaman !== 0) {
                                            echo $penanaman;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">nos</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">500.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_penanaman = Permitting::formulaWC('Kiraan Pemasangan Tiang', $penanaman);
                                        echo $total_penanaman; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 text-center align-items-center fs-7 " style="width:6.25%;">6
                                        </td>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">Pembersihan Tapak</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $bersih = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'Permbersihan Tapak') {
                                                $bersih += $datas['quantity'];

                                            }

                                        }

                                        if ($bersih !== 0) {
                                            echo $bersih;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">pukal</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">5,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_bersih = Permitting::formulaWC('Kiraan Pembersihan Tapak', $bersih);
                                        echo $total_bersih; ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fs-7 p-0 text-center align-items-center fs-7 " style="width:6.25%;">7
                                        </td>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">Pelan Kawalan Trafik
                                            (TCP)</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $pelantcp = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'Pelan Kawalan Trafik (TCP)') {
                                                $pelantcp += $datas['quantity'];

                                            }

                                        }

                                        if ($pelantcp !== 0) {
                                            echo $pelantcp;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">hari/lokasi</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,700.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_pelantcp = Permitting::formulaWC('Kiraan Pelan TCP', $pelantcp);
                                        echo $total_pelantcp; ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="fs-7 py-10 text-center align-items-center fs-7 "
                                            style="width:6.25%;">8</td>
                                        <td colspan="3" class="text-start fs-7 ps-1 p-0 align-items-center ">Di kawasan
                                            carriageway/permukaan berturap (berdasarkan justifikasi di tapak) :</td>
                                        <td colspan="4" class="fs-7 p-0 text-center align-items-center "></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-start fs-7 ps-1 p-0 align-items-center ">i.Diameter Utiliti <
                                                500mm</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $cw_diameter_less_500 = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'CW') {
                                                $cw_diameter_less_500 += $datas['diameter_less_500'];
                                            }

                                        }

                                        if ($cw_diameter_less_500 !== 0) {
                                            echo $cw_diameter_less_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter/lane</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">1,800.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_cw_diameter_less_500 = Permitting::formulaWC('Kiraan CW diameter less', $cw_diameter_less_500);
                                        echo $total_cw_diameter_less_500; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">ii.Diameter Utiliti >
                                            500mm</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $cw_diameter_more_500 = 0;
                                        foreach ($data as $datas) {

                                            if ($datas['method'] === 'CW') {
                                                $cw_diameter_more_500 += $datas['diameter_more_500'];
                                            }

                                        }

                                        if ($cw_diameter_more_500 !== 0) {
                                            echo $cw_diameter_more_500;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">meter/lane</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">2,400.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_cw_diameter_more_500 = Permitting::formulaWC('Kiraan CW diameter more', $cw_diameter_more_500);
                                        echo $total_cw_diameter_more_500; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-start fs-7 p-0 ps-1 align-items-center ">iii.Pit</td>
                                        <td class="fs-7 p-0 text-center align-items-center fw-bold"><?php
                                        $cw_pit = 0;
                                        foreach ($data as $datas) {
                                            if ($datas['method'] === 'CW') {
                                                $cw_pit += $datas['pit'];
                                            }
                                        }

                                        if ($cw_pit !== 0) {
                                            echo $cw_pit;
                                        } else {
                                            echo '';
                                        }
                                        ?></td>
                                        <td class="fs-7 p-0 text-center align-items-center ">nos</td>
                                        <td class="fs-7 p-0 text-center align-items-center ">25,000.00</td>
                                        <td class="fs-7 p-0 pe-1 text-end align-items-center fw-bold"><?php $total_cw_pit = Permitting::formulaWC('Kiraan CW Pit', $cw_pit);
                                        echo $total_cw_pit; ?></td>
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

                            if ($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT') {
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

        <?php

        }
        ?>
</section>

<?php } ?>
</div>