<?php
//FIXME - which one is correct, 3 parameters?
// $detail =  ProjectDetails::projectDetailsNew($_GET['sid'])[0];
$detail =  ProjectDetails::projectDetails($_GET['sid'], '1', '0')[0];
// var_dump($AIDRow);
// $attach_wayleave = ProjectDetails::projectDetailsNew($detail['system_id']);
$attach_wayleave = ProjectDetails::projectDetails($detail['system_id'], '2', '1');
 
?>


<div class="d-flex flex-row-auto w-100">
    <div class="card w-100 me-10">
        <div class="card-header">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bold m-0">Kronologi</h3>
            </div>
            <!--end::Card title-->
        </div>
        <!--begin::Card body-->
        <div class="card-body ms-n5 me-n3">
            <div class="tab-content hover-scroll-overlay-y mb-2" style="height: 455px">
                <!--begin::Item BKIL-->
                <div class="m-0">

                    <!--begin::Timeline-->
                    <div class="timeline">
                        <?php
                        $aliranKerja = ProjectDetails::aliranKerjaData($detail['system_id'], '1,2,3,7,8,9,4,5,6,11,12,13,14,15,16,17,18,21,22,23,24,25,26,27,28,29,31,32,33,40,49,41,42,45,51,52,53,43,46,47,48');
                        foreach ($aliranKerja as $row) {
                            $time = date('h:i A', strtotime($row['NewTimestamp']));

                            if(!empty($row['Notes'])) {
                                $catatan = '<span class="fs-8 text-gray-600">'.$row['Notes'].'</span>';
                            } else {
                                $catatan = '';
                            }

                            if($row['ProfilePic'] == null){
                                $img = "blank";
                            }
                            else{
                                $img = $row['ProfilePic'];
                            };

                            if($row['Role'] == 11 || $row['Role'] == 12) {
                                $department = "Pendaftaran";
                            } else if($row['Role'] == 21 || $row['Role'] == 22 || $row['Role'] == 23) {
                                $department = "Akaun";
                            } else if($row['Role'] == 31 || $row['Role'] == 32 || $row['Role'] == 33 || $row['Role'] == 34 || $row['Role'] == 35) {
                                $department = "GIS";
                            } else if($row['Role'] == 41 || $row['Role'] == 42 || $row['Role'] == 43 || $row['Role'] == 44 || $row['Role'] == 45) {
                                $department = "Permit";
                            } else if($row['Role'] == 51 || $row['Role'] == 52 || $row['Role'] == 53 || $row['Role'] == 54) {
                                $department = "PKD";
                            } else if($row['Role'] == 61 || $row['Role'] == 62 || $row['Role'] == 63 || $row['Role'] == 64 || $row['Role'] == 65) {
                                $department = "Ukur & Pelan";
                            } else if($row['Role'] == 71 || $row['Role'] == 72 || $row['Role'] == 73) {
                                $department = "Admin Sistem";
                            }

                            if(!empty($row['FirstName'])) {
                                $firstName = ucwords($row['FirstName']);
                                $role = '<span class="text-primary fw-bold fs-9" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="'.$firstName.'">Pegawai '.ucwords($department).'</span>';
                                $color_dot = 'success';
                            } else {
                                $firstName = $row['Username'];
                                $role = '<span class="text-info fw-bold fs-9" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="'.$firstName.'">Pemohon</span>';
                                $color_dot = 'success';
                                // badge badge-lg badge-light
                            }
                            // var_dump($row);
                            if(!empty($row['AuthorityName'])){

                                $authority = '<span class="fs-9 text-gray-800">Pihak Berkuasa : ' . $row['AuthorityName'] . '</span>';
                            } else {
                                $authority = '';
                            }


                        ?>
                            <!--begin::Timeline item-->
                            <div class="timeline-item align-items-start mb-3">
                                <!--begin::Timeline line-->
                                <div class="timeline-line w-35px mt-6 mb-n12"></div>
                                <!--end::Timeline line-->

                                <!--begin::Timeline icon-->
                                <div class="timeline-icon" style="margin-left: 11px">
                                    <i class="fad fa-circle-dot text-<?php echo $color_dot ?> fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </div>
                                <!--end::Timeline icon-->

                                <!--begin::Timeline content-->
                                <div class="timeline-content m-0">
                                    <!--begin::Title-->
                                    <span class="fs-8 fw-bold text-gray-800 text-uppercase d-block"><?php echo $row['StatusName']; ?> </span>
                                    <!--end::Title-->

                                    <!--begin::Title-->
                                    <span class="fs-9 text-gray-800 d-block"><?php echo General::convertDate(date($row['NewTimestamp'])) ?> - <?php echo $time ?></span>

                                    <?php echo $authority ?>
                                    <!--end::Title-->

                                    <!--begin::User-->
                                    <div class="mb-3">
                                        <?php echo $role ?>
                                    </div>
                                    <!--end::User-->

                                    <!--begin::Notes-->
                                    <!-- <span class="fs-7 text-gray-400 fw-semibold d-block mt-5"><?php echo $catatan ?></span> -->

                                    <!--begin::Row-->
                                    <!-- <div class="row g-6 g-xl-9 mb-6 mb-xl-9"> -->
                                        <?php
                                        foreach ($attach_wayleave as $rows) {
                                            $date = $rows['AttacmenthDate'];
                                            $timestamp = strtotime($date);
                                            $current_timestamp = time();
                                            $elapsed_seconds = $current_timestamp - $timestamp;

                                            if ($elapsed_seconds < 60) {
                                                $time_ago = "Just Now";
                                            } else if ($elapsed_seconds < 3600) {
                                                $elapsed_minutes = floor($elapsed_seconds / 60);
                                                $time_ago = $elapsed_minutes . " Minit Lepas";
                                            } else if ($elapsed_seconds < 86400) {
                                                $elapsed_hours = floor($elapsed_seconds / 3600);
                                                $time_ago = $elapsed_hours . " Jam Lepas";
                                            } else if ($elapsed_seconds < 2592000) {
                                                $elapsed_hours = floor($elapsed_seconds / 86400);
                                                $time_ago = $elapsed_hours . " Hari Lepas";
                                            } else if ($elapsed_seconds < 31104000) {
                                                $elapsed_hours = floor($elapsed_seconds / 2592000);
                                                $time_ago = $elapsed_hours . " Bulan Lepas";
                                            } else {
                                                $elapsed_days = floor($elapsed_seconds / 31104000);
                                                $time_ago = $elapsed_days . " Tahun Lepas";
                                            }

                                                    // print_r('att =' . $rows['AttachmentStatus']);
                                                    // print_r('old =' . $row['OldStatus']);
                                                    if ($rows['AttachmentStatus'] != null) {
                                                if ($rows['AttachmentStatus'] == $row['OldStatus']) {
                                                    // if ($rows['AttachmentStatus'] != null && $row['OldStatus'] != null) {
                                                    // if($row['NewStatus']){
                                                    // print_r(base64_decode($rows['AttachmentUrl']));

                                                    ?>
                                            <!--begin::Col-->
                                            <!-- <div class="col-md-6 col-lg-4 col-xl-3"> -->
                                            <div class="w-100 w-175px mx-1 mb-2">
                                                <!--begin::Card-->
                                                <div class="card h-100 card-bordered rounded hover-elevate-up ms-2">
                                                    <!--begin::Card body-->
                                                    <div class="card-body d-flex justify-content-center align-items-center flex-column p-2">
                                                        <!--begin::Overlay-->
                                                        <a class="text-gray-800 text-hover-primary d-flex flex-column w-100" data-fslightbox="pdf" href="#pdf-<?php echo $rows['AttachmentID'] ?>">
                                                            <!--begin::Image-->
                                                            <div class="overlay-wrapper d-flex flex-row align-items-center justify-content-start">
                                                                <div class=" d-flex flex-column me-2">
                                                                    <!-- <img src="assets/media/files/pdf.svg" class="theme-light-show" alt="" /> -->
                                                                    <!-- <img src="assets/media/files/pdf-dark.svg" class="theme-dark-show" alt="" /> -->
                                                                    <i class="fas fa-file-pdf fs-6"></i>
                                                                </div>
                                                            <!--end::Image-->
                                                            <!--begin::Title-->
                                                                <div class="d-flex flex-column container-attachment">
                                                                    <div class="fs-9 fw-bold attachment-details"><?php echo $rows['AttachmentDetails'] ?></div>
                                                                    <!--end::Title-->

                                                                    <!--begin::Description-->
                                                                    <!-- <div class="fs-9 fw-semibold text-gray-400"><?php echo $time_ago ?></div> -->
                                                                </div>
                                                            <!--end::Description-->
                                                            </div>
                                                            <!--begin::Action-->
                                                            <!-- <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow min-h-170px">
                                                                <i class="fad fa-print text-white fs-2x"></i>
                                                            </div> -->
                                                            <!--end::Action-->
                                                        </a>
                                                        <!--end::Overlay-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Card-->
                                            </div>
                                            <!--end::Col-->

                                        <div style="display: none;">
                                                <div id="pdf-<?php echo $rows['AttachmentID'] ?>">
                                                    <iframe
                                                        class="embed-responsive-item card-rounded"
                                                        src="/components/files/<?php echo $rows['AttachmentUrl'] ?>/<?php echo $rows['AttachmentType'] ?> "
                                                        width="940"
                                                        height="680"
                                                        frameBorder="0"
                                                        allow="autoplay; fullscreen"
                                                        allowfullscreen="allowfullscreen"
                                                    ></iframe>
                                                </div>
                                            </div>

                                        <?php
                                                    } else {

                                                    }
                                                }




                                            ?>


                                            <?php
                                        }

                                        if ($row['OldStatus'] == 14) {

                                            $reportData = ProjectDetails::dataLLT($detail['system_id'], $row['AuthorityID'])[0];
                                            // print_r($reportData);

                                            if (!empty($reportData['ReportURL'])) {



                                                $url = base64_decode($reportData['ReportURL']);



                                                ?>
                                                <div class="w-100 w-175px mx-1 mb-2">
                                                    <!--begin::Card-->
                                                    <div class="card h-100 card-bordered rounded hover-elevate-up ms-2">
                                                        <!--begin::Card body-->
                                                        <div class="card-body d-flex justify-content-center align-items-center flex-column p-2">
                                                            <!--begin::Overlay-->
                                                            <a class="text-gray-800 text-hover-primary d-flex flex-column w-100" data-fslightbox="pdf" href="#pdf-<?php echo $reportData['ReportId']; ?>">
                                                                <!--begin::Image-->
                                                                <div class="overlay-wrapper d-flex flex-row align-items-center justify-content-start">
                                                                    <div class=" d-flex flex-column me-2">
                                                                        <i class="fas fa-file-pdf fs-6"></i>
                                                                    </div>
                                                                <!--end::Image-->
                                                                <!--begin::Title-->
                                                                    <div class="d-flex flex-column container-attachment">
                                                                        <div class="fs-9 fw-bold attachment-details">Laporan Lawatan Tapak</div>
                                                                        <!--end::Title-->
                                                                    </div>
                                                                <!--end::Description-->
                                                                </div>
                                                            </a>
                                                            <!--end::Overlay-->
                                                        </div>
                                                        <!--end::Card body-->
                                                    </div>
                                                    <!--end::Card-->
                                                </div>
                                                <!--end::Col-->

                                            <div style="display: none;">
                                                    <div id="pdf-<?php echo $reportData['ReportId']; ?>">
                                                        <iframe
                                                            class="embed-responsive-item card-rounded"
                                                            src="<?php echo $url; ?>"
                                                            width="940"
                                                            height="680"
                                                            frameBorder="0"
                                                            allow="autoplay; fullscreen"
                                                            allowfullscreen="allowfullscreen"
                                                        ></iframe>
                                                    </div>
                                                </div>

                                            <?php
                                            }
                                        }

                                        if ($row['OldStatus'] == 40) {

                                            // $reportData = ProjectDetails::dataLLT($detail['system_id'], $row['AuthorityID'])[0];
                                            // print_r($reportData);

                                            // if (!empty($reportData['ReportURL'])) {



                                                // $url = base64_decode($reportData['ReportURL']);



                                                ?>
                                                <div class="w-100 w-175px mx-1 mb-2">
                                                    <!--begin::Card-->
                                                    <div class="card h-100 card-bordered rounded hover-elevate-up ms-2">
                                                        <!--begin::Card body-->
                                                        <div class="card-body d-flex justify-content-center align-items-center flex-column p-2">
                                                            <!--begin::Overlay-->
                                                            <a class="text-gray-800 text-hover-primary d-flex flex-column w-100" data-fslightbox="pdf" href="#pdf-RP-<?php echo $detail['system_id']; ?>">
                                                                <!--begin::Image-->
                                                                <div class="overlay-wrapper d-flex flex-row align-items-center justify-content-start">
                                                                    <div class=" d-flex flex-column me-2">
                                                                        <i class="fas fa-file-pdf fs-6"></i>
                                                                    </div>
                                                                <!--end::Image-->
                                                                <!--begin::Title-->
                                                                    <div class="d-flex flex-column container-attachment">
                                                                        <div class="fs-9 fw-bold attachment-details">Ringkasan Projek</div>
                                                                        <!--end::Title-->
                                                                    </div>
                                                                <!--end::Description-->
                                                                </div>
                                                            </a>
                                                            <!--end::Overlay-->
                                                        </div>
                                                        <!--end::Card body-->
                                                    </div>
                                                    <!--end::Card-->
                                                </div>
                                                <!--end::Col-->

                                            <div style="display: none;">
                                                    <div id="pdf-RP-<?php echo $detail['system_id']; ?>">
                                                        <iframe
                                                            class="embed-responsive-item card-rounded"
                                                            src="/projects/prints/RP/<?php echo $detail['system_id']; ?>"
                                                            width="940"
                                                            height="680"
                                                            frameBorder="0"
                                                            allow="autoplay; fullscreen"
                                                            allowfullscreen="allowfullscreen"
                                                        ></iframe>
                                                    </div>
                                                </div>

                                            <?php
                                            // }
                                        }
                                        ?>
                                    <!-- </div> -->
                                    <!--end:Row-->
                                    <!--end::Notes-->
                                </div>
                                <!--end::Timeline content-->
                            </div>
                            <!--end::Timeline item-->

                        <?php } ?>
                    </div>
                    <!--end::Timeline-->
                </div>
                <!--end::Item BKIL-->
            </div>
        </div>
    </div>
</div>