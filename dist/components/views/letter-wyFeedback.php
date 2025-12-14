<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

if (!empty($_GET['sid']) && !empty($_GET['id'])) {
    $sysID = $_GET['sid'];
    $letterWyFeedback_id = $_GET['id'];
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$current_date = new DateTime();
$generateDate = $current_date->format('Y-m-d H:i:s');
$letter_date = General::convertDate($generateDate);
$hijri_date = Wayleave::convertToHijri($generateDate); // date in hijri
?>

<!--begin::Stepper-->
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid mb-10 mb-lg-0 d-print-none">
        <!--begin::Card-->
        <div class="card mb-8">
            <!--begin::Card body-->
            <div class="card-body p-12">
                <!--begin::Group-->
                <div class="mb-5">
                    <div class="flex-column current" data-kt-stepper-element="content">
                        <!--begin::Title-->
                        <div class="d-flex flex-row align-items-center flex-xxl-row">
                            <!--begin::Title-->
                            <h1 class="text-dark fw-bold my-0 fs-2 me-auto">Surat Maklum Balas Izin Lalu</h1>
                            <!--end::Title-->
                            <!-- begin::print-->
                            <button type="button" class="btn btn-sm btn-light-success" id="print_feedback"><i class="fad fa-print fs-4"></i>Cetak</button>
                            <!-- end::print-->
                        </div>
                        <!--end::Title-->
                        <!--begin::Separator-->
                        <div class="separator separator-dashed border-2 my-5"></div>
                        <!--end::Separator-->
                        <!--begin::Content-->
                        <div id="print_content">
                            <?php 
                            $data_wy = Wayleave::getGenerateWyFeedback($letterWyFeedback_id, 1);

                            if($appsTitle == 'UCIDOS') {
                                foreach ($data_wy as $data) {
                                    $ltr_date = $current_date->format('dmY');
                                    $authority = Wayleave::getAuthority($data['SysID']);

                                    echo' 
                                    <!--begin::Top-->
                                    <div class="print-end">
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
                                    <div class="row mb-0">
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
                                    <!--end::content-->

                                    <!--begin::Footer-->
                                    <footer>
                                        <div class="footer-content d-none d-print-block pt-10">
                                            <div class="fw-row text-center">
                                                <span class="fw-bold">Koridor Utiliti Pahang Sdn Bhd</span>
                                                <span class="fs-8">(1132210-W)</span>
                                            </div>
                                            <div>A31, Jalan IM 7/3, Bandar Indera Mahkota, 25200 Kuantan, Pahang Darul Makmur</div>
                                            <div>Ph +609-572 9666 Fax +609 573 9555</div>
                                        </div>
                                    </footer>
                                    <!--end::Footer-->
                                    ';
                                }
                            } else if($appsTitle == 'KITER' || $appsTitle == 'KUDRAT') {
                                foreach ($data_wy as $data) {
                                    $InvDate = $data['InvDate'];
                                    $inv_date = General::convertDate($InvDate);
                                    $ltr_date = $current_date->format('dmY');

                                    $authority = Wayleave::getAuthority($data['SysID']);

                                    echo' 
                                    <div class="kutt_print">
                                        <!--begin::No Rujukan-->
                                        <div class="kutt_print d-flex justify-content-end flex-column flex-sm-row mb-10">
                                            <!--begin::Section-->
                                            <div class="print-end">
                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack">
                                                    <div class="fs-5 pe-4">Ruj. Kami</div>
                                                    <div class="fs-5 px-5">:</div>
                                                    <div class="position-relative d-flex align-items-center w-250px fs-5">'.$data['LtrRefNo'].'</div>
                                                </div>
                                                <!--end::Item-->
                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack">
                                                    <div class="fs-5 pe-10">Tarikh</div>
                                                    <div class="fs-5 px-5">:</div>
                                                    <div class="position-relative d-flex align-items-center w-250px fs-5">'.$letter_date.'</div>
                                                </div>
                                                <!--end::Item-->
                                                <!--begin::Item-->
                                                <div class="d-flex flex-stack">
                                                    <div class="fs-5">Bersamaan</div>
                                                    <div class="fs-5 px-5">:</div>
                                                    <div class="position-relative d-flex align-items-center w-250px fs-5">'.$hijri_date.'</div>
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
                                                if ($row2['Type'] == 3) {
                                                    $providerData[] = $row2; // Add Type == 3 data to the Type == 3 array
                                                } elseif ($row2['Type'] == 1) {
                                                    $clientData[] = $row2; // Add Type == 1 data to the Type == 1 array
                                                }
                                            }
                                        }

                                        // Display Type == 3 data first
                                        foreach ($providerData as $row2) {
                                            echo '<div class="flex-root d-flex flex-column mb-6">';
                                            echo '<span class="fw-bold fs-5">'. strtoupper($data['ProviderName']) .'</span>';
                                            echo '<span class="fs-5">' . $row2['Address1'] . ', ' . $row2['Address2'] . ', ' . '</span>';
                                            echo '<span class="fs-5">' . $row2['Postcode'] . $row2['City'] . ', ' . '</span>';
                                            echo '<span class="fs-5">' . $row2['State'] . '.' . '</span>';
                                            echo '<span class="fs-5">(U.P.:' . $data['ProviderUP'] . ')</span>';
                                            echo '</div>';
                                        }

                                        // Display Type == 1 data
                                        foreach ($clientData as $row2) {
                                            echo '<div class="flex-root d-flex flex-column mb-6">';
                                            echo '<span class="fw-bold fs-5">'. (isset($row2['CompanyName']) ? strtoupper($row2['CompanyName']) : 'TIADA NAMA SYARIKAT') .'</span>';
                                            echo '<span class="fs-5">' . $row2['Address1'] . ', ' . $row2['Address2'] . ', ' . '</span>';
                                            echo '<span class="fs-5">' . $row2['Postcode'] . $row2['City'] . ', ' . '</span>';
                                            echo '<span class="fs-5">' . $row2['State'] . '.' . '</span>';
                                            echo '<span class="fs-5">(U.P.:' . $data['ClientUP'] . ')</span>';
                                            echo '</div>';
                                        }

                                        echo'
                                        <!--begin::content-->
                                        <div class="row mb-0">
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
                                                            <div class="fs-5 pe-4">Tarikh</div>
                                                            <div class="fs-5 px-5">:</div> 
                                                            <div class="position-relative d-flex align-items-center w-250px fs-5">'.$letter_date.'</div>
                                                        </div>
                                                        <!--end::Item-->
                                                    </div>
                                                    <!--end::Section-->
                                                </div>
                                                <!--end::No Rujukan-->
                                                <!--begin::Separator-->
                                                <div class="separator border-dark mb-6"></div>
                                                <!--end::Separator -->
                                            </div>
                                            <!--end::Page Break-->

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
                                                        <span class="fs-5 pe-10">10.</span>
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
                                            <div class="fw-row">
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
                                    </div>
                                    ';
                                }
                            }
                            ?>
                        </div>
                        <!--end::Content-->
                    </div>

                </div>
                <!--end::Group-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Content-->
</div>
<!--end::Stepper-->

