
<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (!empty($_GET['sid'])) {
    $sysID = $_GET['sid'];
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$data_wy = Wayleave::getGenerateWyFeedback($sysID, 2); 

?>

<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
        <!--begin::Card-->
        <div class="card mb-8">
            <!--begin::Card body-->
            <div class="card-body p-12">
                <!--begin::Wrapper-->
                <div class="d-flex flex-row align-items-start flex-xxl-row">
                    <!--begin::Title-->
                    <h1 class="text-dark fw-bold my-0 fs-2 me-auto">Pengesahan Surat Maklum Balas Kelulusan Izin Lalu</h1>
                    <!--end::Title-->
                </div>
                <!--end::Top-->
                <!--begin::Separator-->
                <div class="separator separator-dashed border-2 my-5"></div>
                <!--end::Separator-->

                <?php 
                    $wyLtrId = '';
                    if($appsTitle == 'UCIDOS') {
                        foreach ($data_wy as $data) {
                            $ltr_date = date('dmY', strtotime($data['LetterDate']));
                            $letter_date = General::convertDate($data['LetterDate']);
                            $authority = Wayleave::getAuthority($data['SysID']);
        
                            echo' 
                            <!--begin::Top-->
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
        
                            <!--begin::No Rujukan-->
                            <div class="d-flex justify-content-end flex-column flex-sm-row mb-10">
                                <!--begin::Section-->
                                <div class="print-end">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack">
                                        <div class="fs-5 pe-4 text-end">Ruj. Kami</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-250px fs-5">'.$data['LtrRefNo'].'</div>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack">
                                        <div class="fs-5 pe-4">Ruj. Tuan</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-250px fs-5">'.$data['ClientRefNo'].'</div>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack">
                                        <div class="fs-5 pe-11">Tarikh</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-250px fs-5">'.$letter_date.'</div>
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::No Rujukan-->';
        
                            $contactId = explode(",", trim($data['ContactId'], "{}"));
    
                            $providerData = array(); // Intermediate array to store Type == 3 data
                            $clientData = array(); // Intermediate array to store Type == 1 data

                            foreach ($contactId as $rowCtc) {
                                $contact = Wayleave::getContacts($rowCtc);
                                
                                foreach ($contact as $row2) {
                                    if ($row2['Type'] == 1 || $row2['Type'] == 2) {
                                        $clientData[] = $row2; // Add Type == 1 data to the Type == 1 array
                                    }
                                }
                            }
        
                            echo '<div class="flex-root d-flex flex-column mb-6">';
                            // Display Type == 1 data
                            foreach ($clientData as $row2) {
                                echo '<span class="fw-bold fs-5">'. (isset($row2['CompanyName']) ? strtoupper($row2['CompanyName']) : 'TIADA NAMA SYARIKAT') .'</span>';
                            }
                                echo '<span class="fs-5">' . $data['AddrClient1'] . ',</span>';
                                echo '<span class="fs-5">' . $data['AddrClient2'] . ',</span>';
                                echo '<span class="fs-5">' . $data['AddrClient3'] . '</span>';
                                echo '<span class="fs-5"><b>(u.p.:' . $data['ClientUP'] . ')</b></span>';
                            echo '</div>';

                            $i = 0;
                            $totalAuthority = count($authority); 
        
                            echo'
                            <!--begin::content-->
                            <div class="row mw-850px mb-0">
                                <span class="fs-5 mb-6">Tuan,</span>
                                <span class="fw-bold fs-5">'. $data['Title'] .'</span>
                                <span class="fs-5 mb-1">- Kelulusan Bersyarat Kebenaran Izin Lalu</span>
                                <!--begin::Separator-->
                                <div class="separator border-dark mb-6"></div>
                                <!--end::Separator -->
                                <span class="fs-5 mb-6">Dengan hormatnya perkara di atas adalah dirujuk.</span>';

                                if($totalAuthority == 1) {
                                    foreach ($authority as $row2) {
                                        echo'
                                        <div class="fw-row mb-6" style="text-align: justify;">
                                            <span class="fs-5 pe-10">2.</span>
                                            <span class="fs-5">Sukacita dimaklumkan bahawa <b>permohonan kebenaran izin lalu</b> tuan sebagaimana projek di atas telah mendapat <b>kelulusan</b> daripada <b>'.$row2['AuthorityFullName'].'</b>. Sehubungan dengan itu, pihak tuan perlu mematuhi syarat-syarat yang terkandung dalam surat kelulusan izin lalu daripada <b>'.$row2['AuthorityName'].'</b>. Bagi proses kelulusan permit korekan jalan, pihak tuan dikehendaki melengkapkan dokumen seperti yang dinyatakan di <b>Lampiran A</b> dan kemukakan kepada pihak Koridor Utiliti Pahang (KUP) untuk tindakan seterusnya.</span>
                                        </div>';
                                        $i++;
                                    }
                                } else {
                                    echo'
                                        <div class="fw-row mb-6" style="text-align: justify;">
                                            <span class="fs-5 pe-10">2.</span>
                                            <span class="fs-5">Sukacita dimaklumkan bahawa <b>permohonan kebenaran izin lalu</b> tuan sebagaimana projek di atas telah mendapat <b>kelulusan</b> daripada ';
                                            if($totalAuthority == 2) {
                                                $counter = 1;
                                                foreach ($authority as $row2) {
                                                    echo '<b>'.$row2['AuthorityFullName'].'</b>';
                                                    if ($counter < $totalAuthority) {
                                                        echo ' dan ';
                                                    }
                                                    $counter++;
                                                }
                                            } else {
                                                $counter = 1;
                                                foreach ($authority as $row2) {
                                                    if ($counter < $totalAuthority) {
                                                        echo '<b>'.$row2['AuthorityFullName'].'</b>' . ', ';
                                                    } elseif ($counter == $totalAuthority) {
                                                        echo ' dan '.'<b>'.$row2['AuthorityFullName'].'</b>';
                                                    }
                                                    $counter++;
                                                }
                                            }
                                            echo'
                                            . Sehubungan dengan itu, pihak tuan perlu mematuhi syarat-syarat yang terkandung dalam surat kelulusan izin lalu daripada '; 
                                            if($totalAuthority == 2) {
                                                $counter = 1;
                                                foreach ($authority as $row2) {
                                                    echo '<b>'.$row2['AuthorityName'].'</b>';
                                                    if ($counter < $totalAuthority) {
                                                        echo ' dan ';
                                                    }
                                                    $counter++;
                                                }
                                            } else {
                                                $counter = 1;
                                                foreach ($authority as $row2) {
                                                    if ($counter < $totalAuthority) {
                                                        echo '<b>'.$row2['AuthorityName'].'</b>' . ', ';
                                                    } elseif ($counter == $totalAuthority) {
                                                        echo ' dan '.'<b>'.$row2['AuthorityName'].'</b>';
                                                    }
                                                    $counter++;
                                                }
                                            }
                                            echo'. Bagi proses kelulusan permit korekan jalan, pihak tuan dikehendaki melengkapkan dokumen seperti yang dinyatakan di <b>Lampiran A</b> dan kemukakan kepada pihak Koridor Utiliti Pahang (KUP) untuk tindakan seterusnya.</span>
                                        </div>';
                                }

                                echo'
                                <div class="fw-row mb-6" style="text-align: justify;">
                                    <span class="fs-5 pe-10">3.</span>
                                    <span class="fs-5">Berikut adalah makluman deposit keselamatan/wang cagaran bagi projek di atas :-</span>
                                </div>

                                <!--start::Page Break-->
                                <div class="page-break"></div>

                                <div class="d-none d-print-block">
                                    <!--begin::No Rujukan-->
                                    <div class="d-flex justify-content-start flex-column flex-sm-row mb-5 mt-10">
                                        <!--begin::Section-->
                                        <div class="print-start">
                                            <!--begin::Item-->
                                            <div class="d-flex flex-stack">
                                                <div class="fs-5 pe-4">Ruj. Kami</div>
                                                <div class="fs-5 px-5">:</div>
                                                <div class="position-relative d-flex align-items-center w-250px fs-5">'.$data['LtrRefNo'].'</div>
                                            </div>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <div class="d-flex flex-stack">
                                                <div class="fs-5">Muka Surat</div>
                                                <div class="fs-5 px-5">:</div>
                                                <div id="page-number" class="page-number position-relative d-flex align-items-center w-250px fs-5"></div>


                                            </div>
                                            <!--end::Item-->
                                        </div>
                                        <!--end::Section-->
                                    </div>
                                    <!--end::No Rujukan-->
                                    <div class="fw-bold fs-5">'. $data['Title'] .'</div>
                                    <div class="fs-5 mb-1">- Kelulusan Bersyarat Kebenaran Izin Lalu</div>
                                    <!--begin::Separator-->
                                    <div class="separator border-dark mb-6"></div>
                                    <!--end::Separator -->
                                </div>
                                <!--end::Page Break-->

                                <!--begin::table-->';
                                $road = Wayleave::getRoad($data['SysID']);
                                $bil = 1;

                                foreach ($road as $row) {
                                    $dataRoad = explode(",", $row['RoadId']);
                                    $romanNumeral = Wayleave::integerToRoman($bil);

                                echo'
                                <div class="fw-row fw-bold mb-6" style="text-align: justify;">
                                    <span class="fs-5 pe-15"></span>
                                    <span class="fs-5 pe-10">'. $romanNumeral .'.</span>
                                    <span class="fs-5">'. $row['AuthorityName'] . '</span>
                                </div>

                                <div class="table-responsive mb-5">
                                    <table class="table table-bordered border-dark">
                                        <tbody>
                                            <tr class="text-center fw-bold fs-5">
                                                <td class="p-1">Bil.</td>
                                                <td class="p-1">Jalan Terlibat</td>
                                                <td class="p-1">Tempoh Kelulusan Izin Lalu</td>
                                                <td class="p-1">Keterangan Bayaran</td>
                                                <td class="p-1">Jumlah (RM)</td>
                                            </tr>';

                                            echo '<tr>';
                                                echo '<td class="fs-5 text-center p-1">'. $romanNumeral .'.</td>';
                                                echo '<td class="fs-5 p-1">';
                                                // Start the nested table
                                                echo '<table class="table table-row-bordered table-row">'; 
                                                foreach ($dataRoad as $rTrim) {
                                                    $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                    $road_name = Wayleave::getRoadName($roadIdTrim);
                                                    echo '<tr>';
                                                    foreach ($road_name as $rData) {
                                                        echo '<td class="fs-5 text-center p-1">' . $rData['RoadName'] . '</td>';
                                                    }
                                                    echo '</tr>';
                                                }
                                                echo '</table>'; 
                                                // End the nested table
                                                echo '</td>';
                                                echo '<td class="fs-5 text-center p-1">'.$row['PeriodKil'].'</td>';
                                                echo '<td class="fs-5 text-center p-1">'.$row['PaymentDetail'].'</td>';
                                                echo '<td class="fs-5 text-center p-1">'.$row['Amount'].'</td>';
                                            echo '</tr>';

                                            echo'
                                        </tbody>
                                    </table>
                                </div>';
                                $bil++;
                                }
                                echo'
                                <!--end::table-->

                                <div class="fw-row mb-6 justify-text">
                                    <span class="fs-5 pe-10">4.</span>
                                    <span class="fs-5">Adalah diingatkan bahawa pihak tuan boleh didakwa mengikut Akta 133 (Akta Jalan, Parit dan Bangunan 1974), Seksyen 40 dan di bawah Seksyen 85 Undang-Undang Malaysia Akta 333 (Akta Pengangkutan Jalan 1987) sekiranya kerja-kerja tersebut dilakukan tanpa kelulusan Pihak Berkuasa Jalan.</span>
                                </div>
                                <div class="fw-row mb-6 justify-text">
                                    <span class="fs-5">Kerjasama, perhatian dan tindakan tuan berhubung perkara ini amatlah kami hargai dan diucapkan ribuan terima kasih.</span>
                                </div>
                                <div class="fw-row mb-6">
                                    <span class="fs-5">Sekian.</span>
                                </div>
                                
                                <div class="fw-row">
                                    <span class="fs-5">Dengan hormatnya,</span>
                                </div>
                                <div class="fw-row mb-20">
                                    <span class="fs-5 fw-bold">KORIDOR UTILITI PAHANG</span>
                                </div>
                                <div class="fw-row">
                                    <span class="fs-5 fw-bold">('.strtoupper($data['FirstNameApproval']).' '.strtoupper($data['LastNameApproval']).')</span>
                                </div>
                                <div class="fw-row">
                                    <span class="fs-5">'.$data['staffApprovePosition'].'</span>
                                </div>
                                <div class="fw-row mb-5">
                                    <span class="fs-10">DF'.$ltr_date.'-KBKIL</span>
                                </div>

                                <span class="fs-5 mb-5">Salinan Kepada:</span>

                                <div class="flex-root d-flex flex-column mb-6">
                                    <span class="fw-bold fs-5">'. strtoupper($data['ProviderName']) .'</span>
                                    <span class="fs-5">' . $data['AddrProvider1'] . ',</span>
                                    <span class="fs-5">' . $data['AddrProvider2'] . ',</span>
                                    <span class="fs-5">' . $data['AddrProvider3'] . '.' . '</span>
                                    <span class="fs-5"><b>(u.p.:' . $data['ProviderUP'] . ')</b></span>
                                </div>

                            </div>
                            <!--end::content-->';
                            $wyLtrId = $data['WyFeedbackID'];
        
                        }

                    } else if($appsTitle == 'KITER' || $appsTitle == 'KUDRAT') {
                        foreach ($data_wy as $data) {
                            $InvDate = $data['InvDate'];
                            $inv_date = General::convertDate($InvDate);
                            $ltr_date = date('dmY', strtotime($data['LetterDate']));
                            $letter_date = General::convertDate($data['LetterDate']);
                            $authority = Wayleave::getAuthority($data['SysID']);
        
                            echo' 
                            <!--begin::No Rujukan-->
                            <div class="d-flex justify-content-end flex-column flex-sm-row mb-10">
                                <!--begin::Section-->
                                <div class="text-sm-end">
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-1">
                                        <div class="fs-5 pe-4">Ruj. Kami</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-250px fs-5">'.$data['LtrRefNo'].'</div>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-1">
                                        <div class="fs-5 pe-10">Tarikh</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-250px fs-5">'.$letter_date.'</div>
                                    </div>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <div class="d-flex flex-stack mb-1">
                                        <div class="fs-5">Bersamaan</div>
                                        <div class="fs-5 px-5">:</div>
                                        <div class="position-relative d-flex align-items-center w-250px fs-5">'.$data['LetterHijriDate'].'</div>
                                    </div>
                                    <!--end::Item-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::No Rujukan-->';
                            $ctc = explode(",", $data['ContactId']);
                            foreach ($ctc as $row) {
                                $contactId = trim($row, '{}'); // Remove curly braces
                                $contact = Wayleave::getContacts($contactId);
                                foreach ($contact as $row2) {
                                    if($row2['Type'] == 3) {
                                        echo '<div class="flex-root d-flex flex-column mb-6">';
                                        echo '<span class="fw-bold fs-5 mb-1">'. strtoupper($data['ProviderName']) .'</span>';
                                        echo '<span class="fs-5 mb-1">' . $row2['Address1'] . ', ' . $row2['Address2'] . ', ' . '</span>';
                                        echo '<span class="fs-5 mb-1">' . $row2['Postcode'] . $row2['City'] . ', ' . '</span>';
                                        echo '<span class="fs-5 mb-1">' . $row2['State'] . '.' . '</span>';
                                        echo '<span class="fs-5 mb-1">(U.P.:' . $data['ProviderUP'] . ')</span>';
                                        echo '</div>';
                                    } 
        
                                    if($row2['Type'] == 1) {
                                        echo '<div class="flex-root d-flex flex-column mb-6">';
                                        echo '<span class="fw-bold fs-5 mb-1">'. strtoupper($row2['CompanyName']) .'</span>';
                                        echo '<span class="fs-5 mb-1">' . $row2['Address1'] . ', ' . $row2['Address2'] . ', ' . '</span>';
                                        echo '<span class="fs-5 mb-1">' . $row2['Postcode'] . $row2['City'] . ', ' . '</span>';
                                        echo '<span class="fs-5 mb-1">' . $row2['State'] . '.' . '</span>';
                                        echo '<span class="fs-5 mb-1">(U.P.:' . $data['ClientUP'] . ')</span>';
                                        echo '</div>';
                                    }
                                }
                            }
        
                            echo'
                            <!--begin::content-->
                            <div class="row mw-850px mb-0">
                                <span class="fs-5 mb-6">Tuan,</span>
                                <span class="fw-bold fs-5 mb-1">'. $data['Title'] .'</span>
                                <div class="fv-row">
                                    <span class="fw-bold fs-5 mb-1">PER:</span>
                                    <span class="fs-5 mb-1">Kelulusan Izin Lalu</span>
                                </div>
                                <!--begin::Separator-->
                                <div class="separator border-dark mb-6"></div>
                                <!--end::Separator -->
                                <span class="fs-5 mb-6">Dengan segala hormatnya merujuk perkara di atas.</span>
                                <div class="fw-row mb-6" style="text-align: justify;">
                                    <span class="fs-5 pe-10">2.</span>
                                    <span class="fs-5">Sukacita dimaklumkan bahawa <b>permohonan kebenaran laluan (Izin Lalu)</b> pihak tuan telah mendapat <b>kelulusan</b> sebagaimana berikut:</span>
                                </div>
        
                                <!--begin::table-->
                                <div class="table-responsive mb-5">
                                    <table class="table table-bordered border-dark">
                                        <tbody>
                                            <tr class="text-center fw-bold fs-5">
                                                <td class="p-1">BIL</td>
                                                <td class="p-1">NAMA JALAN</td>
                                                <td class="p-1">PBM/PBT</td>
                                                <td class="p-1">JUMLAH (RM)</td>
                                            </tr>';
                                            $road = Wayleave::getRoad($data['SysID']);
        
                                            $bil = 1;
        
                                            foreach ($road as $row) {
                                                $dataRoad = explode(",", $row['RoadId']);
        
                                                echo '<tr>';
                                                    echo '<td class="fs-5 text-center p-1">'. $bil .'.</td>';
                                                    echo '<td class="fs-5 p-1">';
                                                    // Start the nested table
                                                    echo '<table class="table table-row-bordered table-row">'; 
                                                    foreach ($dataRoad as $rTrim) {
                                                        $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                        $road_name = Wayleave::getRoadName($roadIdTrim);
                                                        echo '<tr>';
                                                        foreach ($road_name as $rData) {
                                                            echo '<td class="fs-5 text-center p-1">' . $rData['RoadName'] . '</td>';
                                                        }
                                                        echo '</tr>';
                                                    }
                                                    echo '</table>'; 
                                                    // End the nested table
                                                    echo '</td>';
                                                    echo '<td class="fs-5 text-center p-1">' . $row['AuthorityName'] . '</td>';
                                                    echo '<td class="fs-5 text-center p-1"></td>';
                                                echo '</tr>';
        
                                                $bil++;
                                            }
        
                                            echo'
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::table-->
        
                                <div class="fw-row mb-6 justify-text">
                                    <span class="fs-5 pe-10">3.</span>
                                    <span class="fs-5">Sehubungan itu, bagi proses kelulusan permit kerja, pihak tuan dikehendaki mematuhi dan melengkapkan syarat-syarat seperti yang dinyatakan di <b>Lampiran</b> serta mengemukakannya kepada pihak kami untuk tindakan seterusnya.</span>
                                </div>
                                <div class="fw-row mb-6 justify-text">
                                    <span class="fs-5 pe-10">4.</span>
                                    <span class="fs-5">Pihak tuan diminta untuk mengemukakan insurans <b>"Contractor s All Risk"</b> yang meliputi insurans kerja dan polisi tanggungan awam sepertimana yang dinyatakan di <b>Jadual 2</b> untuk menanggung rugi (indemnity) di mana <b>tempoh sahlakunya bermula dari Tarikh Milik Tapak sehingga tamat Tempoh Tanggungan Kecacatan/ "Defected Liability Period" (DLP).</b> Insurans tersebut hendaklah juga melindungi (Insured) <b>Kerajaan Persekutuan / Negeri, Jurutera Daerah JKR dan JPS berkenaan serta wakilnya yang terlibat secara langsung dengan kerja tersebut.</b></span>
                                </div>
                                <div class="fw-row mb-6 justify-text">';
        
                                    $i = 0;
                                    $totalAuthority = count($authority);
        
                                    if($totalAuthority == 1) {
                                        foreach ($authority as $row2) {
                                            echo'
                                            <span class="fs-5 pe-10">5.</span>
                                            <span class="fs-5">Bagi perlaksanaan kerja-kerja di tapak, pihak tuan adalah <b>DIWAJIBKAN</b> untuk mematuhi Garis Panduan Laluan Kemudahan Utiliti Negeri Terengganu dan juga syarat-syarat yang dinyatakan oleh '.$row2['AuthorityName'].' seperti di lampiran.</span>';
                                            $i++;
                                        }
                                    } else {
                                        echo '<span class="fs-5 pe-10">5.</span>
                                        <span class="fs-5">Bagi perlaksanaan kerja-kerja di tapak, pihak tuan adalah <b>DIWAJIBKAN</b> untuk mematuhi Garis Panduan Laluan Kemudahan Utiliti Negeri Terengganu dan juga syarat-syarat yang dinyatakan oleh ';
        
                                        if($totalAuthority == 2) {
                                            $counter = 1;
                                            foreach ($authority as $row2) {
                                                echo $row2['AuthorityName'];
                                                if ($counter < $totalAuthority) {
                                                    echo ' serta ';
                                                }
                                                $counter++;
                                            }
        
                                            echo ' seperti di lampiran.</span>';
                                        } else {
                                            $counter = 1;
                                            foreach ($authority as $row2) {
                                                if ($counter < $totalAuthority) {
                                                    echo $row2['AuthorityName'] . ', ';
                                                } elseif ($counter == $totalAuthority) {
                                                    echo ' serta '.$row2['AuthorityName'];
                                                }
                                                $counter++;
                                            }
        
                                            echo ' seperti di lampiran.</span>';
                                        }
                                    }
        
                                    echo'
                                </div>';
                                $showAir = false; // Flag to track if "show_air" div has been displayed
                                $showJKR = false;
        
                                $abc = '';
        
                                foreach ($authority as $row3) {
                                    if ($row3['AuthorityId'] == 17 || $row3['AuthorityId'] == 18 || $row3['AuthorityId'] == 19 || $row3['AuthorityId'] == 20) {
                                        $showAir = true;
                                        $abc = $row3['AuthorityName'];
                                        break;
                                    }
                                }
        
                                if ($showAir) {
                                    echo '
                                    <div id="show_air">
                                        <div class="fw-row mb-6 justify-text">
                                            <span class="fs-5 pe-10">6.</span>
                                            <span class="fs-5">Antara syarat-syarat yang dikemukakan oleh pihak '.$abc.' ialah:</span>
                                        </div>
                                        <div class="mb-6">
                                            <div class="fw-row mb-5" style="padding-left: 80px;">
                                                <span class="fs-5 pe-10">i.</span>
                                                <span class="fs-5">Kedalaman minima antara aras sungai sediada dengan lapisan teratas saluran paip utiliti mestilah tidak kurang dari tiga (3) meter;</span>
                                            </div>
                                            <div class="fw-row mb-5" style="padding-left: 80px;">
                                                <span class="fs-5 pe-9">ii.</span>
                                                <span class="fs-5">Panjang bagi saluran paip utiliti mengufuk yang merentangi sungai sekurang-kurangnya mestilah sama dengan lebar dasar sungai sediada;</span>
                                            </div>
                                            <div class="fw-row mb-5" style="padding-left: 80px;">
                                                <span class="fs-5 pe-8">iii.</span>
                                                <span class="fs-5">Kecerunan saluran paip utiliti mestilah tidak melebihi 3 (mengufuk) : 1 (menegak);dan</span>
                                            </div>
                                            <div class="fw-row" style="padding-left: 80px;">
                                                <span class="fs-5 pe-7">iv.</span>
                                                <span class="fs-5">Jajaran saluran paip utiliti hendaklah berada sekurang-kurangnya 10 meter dari tebing sungai.</span>
                                            </div>
                                        </div>
        
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">7.</span>
                                            <span class="fs-5">Pihak tuan juga diminta untuk menjelaskan bayaran caj perkhidmatan bagi membolehkan permohonan kelulusan permit pihak tuan diproses. Bersama-sama ini disertakan <b>Invois No.: '. $data['InvNo'] .'</b> Bertarikh <b>'. $inv_date .'</b> untuk tindakan selanjutnya pihak tuan.</span>
                                        </div>
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">8.</span>
                                            <span class="fs-5">Untuk makluman pihak tuan, kelulusan izin lalu ini hanya sah dalam tempoh <b>6 bulan (180 hari) sahaja</b> dan pihak tuan/puan perlu melengkapkan syarat-syarat seperti yang dinyatakan di atas dalam tempoh tersebut. Sekiranya pihak tuan/puan gagal berbuat demikian, permohonan pihak tuan/puan akan <b>DIBATALKAN</b> dan pihak tuan/puan perlu membuat permohonan yang baru kepada pihak kami.</span>
                                        </div>
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">9.</span>
                                            <span class="fs-5">Adalah diingatkan bahawa pihak tuan boleh didakwa mengikut Akta 333 (Akta Pengangkutan Jalan 1987), Seksyen 40 dan di bawah Seksyen 85 Undang-Undang Malaysia sekiranya kerja-kerja tersebut dilakukan tanpa kelulusan Pihak Berkuasa Jalan.</span>
                                        </div>
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">9.</span>
                                            <span class="fs-5">Sekiranya dokumen sebagaimana dilampirkan telah lengkap, pihak tuan diminta untuk menghubungi pegawai kami <b>'.strtoupper($data['FirstNameContact']).' '.strtoupper($data['LastNameContact']).'</b> di talian <b>'. $data['staffContact'] .'</b> bagi urusan penyerahan tersebut. Hanya senarai semak yang telah dilengkapkan sahaja akan diterima ketika penyerahan tersebut.</span>
                                        </div>
                                    </div>';
                                } else {
                                    echo '
                                    <div id="show_jkr">
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">6.</span>
                                            <span class="fs-5">Pihak tuan juga diminta untuk menjelaskan bayaran caj perkhidmatan bagi membolehkan permohonan kelulusan permit pihak tuan diproses. Bersama-sama ini disertakan <b>Invois No.: '. $data['InvNo'] .'</b> Bertarikh <b>'. $inv_date .'</b> untuk tindakan selanjutnya pihak tuan.</span>
                                        </div>
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">7.</span>
                                            <span class="fs-5">Untuk makluman pihak tuan, kelulusan izin lalu ini hanya sah dalam tempoh <b>6 bulan (180 hari) sahaja</b> dan pihak tuan/puan perlu melengkapkan syarat-syarat seperti yang dinyatakan di atas dalam tempoh tersebut. Sekiranya pihak tuan/puan gagal berbuat demikian, permohonan pihak tuan/puan akan <b>DIBATALKAN</b> dan pihak tuan/puan perlu membuat permohonan yang baru kepada pihak kami.</span>
                                        </div>
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">8.</span>
                                            <span class="fs-5">Adalah diingatkan bahawa pihak tuan boleh didakwa mengikut Akta 333 (Akta Pengangkutan Jalan 1987), Seksyen 40 dan di bawah Seksyen 85 Undang-Undang Malaysia sekiranya kerja-kerja tersebut dilakukan tanpa kelulusan Pihak Berkuasa Jalan.</span>
                                        </div>
                                        <div class="fw-row mb-6 justify-text" >
                                            <span class="fs-5 pe-10">9.</span>
                                            <span class="fs-5">Sekiranya dokumen sebagaimana dilampirkan telah lengkap, pihak tuan diminta untuk menghubungi pegawai kami <b>'.strtoupper($data['FirstNameContact']).' '.strtoupper($data['LastNameContact']).'</b> di talian <b>'. $data['staffContact'] .'</b> bagi urusan penyerahan tersebut. Hanya senarai semak yang telah dilengkapkan sahaja akan diterima ketika penyerahan tersebut.</span>
                                        </div>
                                    </div>';
        
                                }
        
        
                                echo'
        
                                <div class="fw-row mb-12 justify-text" >
                                    <span class="fs-5">Kerjasama tuan dalam perkara ini, didahului dengan ucapan ribuan terima kasih.</span>
                                </div>
                                <div class="fw-row mb-6 justify-text" >
                                    <span class="fs-5">Sekian.</span>
                                </div>
                                <div class="fw-row mb-6 justify-text" >
                                    <span class="fs-5 fw-bold">"TERENGGANU MAJU, BERKAT DAN SEJAHTERA"</span>
                                </div>
                                <div class="fw-row mb-20 justify-text" >
                                    <span class="fs-5">Dengan hormatnya,</span>
                                </div>
                                <div class="fw-row justify-text" >
                                    <span class="fs-5 fw-bold">('.strtoupper($data['FirstNameApproval']).' '.strtoupper($data['LastNameApproval']).')</span>
                                </div>
                                <div class="fw-row mb-5 justify-text" >
                                    <span class="fs-5">'.$data['staffApprovePosition'].'</span>
                                </div>
                                <div class="fw-row mb-5 justify-text" >
                                    <span class="fs-10">NABH/'.$ltr_date.'/KIL</span>
                                </div>
        
                            </div>
                            <!--end::content-->
                            ';
                            $wyLtrId = $data['WyFeedbackID'];
        
                        }
                        
                    }

                ?>


            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->

    </div>
    <!--end::Content-->

    <!--begin::Sidebar-->
    <div class="flex-lg-row-fluid w-100 mw-lg-300px mw-xxl-350px">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card body-->
            <div class="card-body p-10">
                <!--begin::Form-->
                <form class="modal-body" novalidate="novalidate" id="form_wayFeedback_approval">
                    <!--begin::Actions-->
                    <div class="mb-0">
                        <!--begin::Title-->
                        <h6 class="mb-6 fw-bolder text-gray-600 text-hover-primary">TINDAKAN PENGESAHAN</h6>
                        <!--end::Title-->
                        <div class="separator mb-6 "></div>

                        <div class="fv-row mb-10 form-check form-switch form-check-custom form-check-solid">
                            <label class="fs-6 fw-semibold pe-4" for="flexSwitchDefault">
                                Adakah anda sahkan surat maklum balas ini?
                            </label>
                            <input class="form-check-input" type="checkbox" value="1" id="flexSwitchDefault" name="confirmation"/>
                        </div>

                        <!--begin::Input group-->
                        <div class="fv-row mb-15">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">Catatan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="4" name="notes" placeholder="Catatan"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <input type="text" name="system-id" value="<?php echo $sysID ?>" hidden>
                        <input type="text" name="ltr-wyFeedback-id" value="<?php echo $wyLtrId ?>" hidden>
                        <input type="text" name="item" value="wyFeedback-approved" hidden>

                        <!--begin::Submit Button-->
                        <button type="submit" id="submit_wayFeedback_approval" class="btn btn-primary">
                            <!--begin::Indicator label-->
                            <span class="indicator-label"><i class="fad fa-file-circle-check fs-4 pe-1"></i> Hantar</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                            <!--end::Indicator progress-->
                        </button>
                        <!--end::Submit Button-->
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end::Form-->

            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Sidebar-->
</div>
<!--end::Layout-->