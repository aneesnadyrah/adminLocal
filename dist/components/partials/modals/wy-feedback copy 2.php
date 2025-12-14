<?php

$option = '<option></option>';
$option2 = '<option></option>';
$option3 = '<option></option>';

foreach (Wayleave::assignStaff(1) as $row) {
    if($row['ProfilePic'] == null){
        $img = "blank";
    }
    else{
        $img = $row['ProfilePic'];
    };
    $name = $row['FirstName'];
    $id = $row['username'];
    $contact = $row['StaffPhoneNo'];

    $option .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'">'.$name.'</option>';
    $option2 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'">'.$name.'</option>';
}
foreach (Wayleave::assignStaff(2) as $row) {
    if($row['ProfilePic'] == null){
        $img = "blank";
    }
    else{
        $img = $row['ProfilePic'];
    };
    $name = $row['FirstName'].' '.$row['LastName'];
    $id = $row['username'];
    $contact = $row['StaffPhoneNo'];
    $position = $row['Position'];

    $option3 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-position="'.$position.'">'.$name.'</option>';
}

date_default_timezone_set('Asia/Kuala_Lumpur');
$current_date = new DateTime();
$generateDate = $current_date->format('Y-m-d H:i:s');
$letter_date = General::convertDate($generateDate); //date in masihi
$hijri_date = Wayleave::convertToHijri($generateDate); // date in hijri

// var_dump($abc);
if($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT') {
    $wayleave_mkil = Wayleave::wyMKIL($_GET['sid']);

    foreach ($wayleave_mkil as $row) {
        $datepickerId = 'datepicker-' . $row['Status']. $row['StatusID'];
    ?>
        <!--begin::Modal - mkil - Add-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-<?php echo $row['WyStatus']. $row['WyID']?>" >
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::modal-content-->
                <div class="modal-content">
                    <!--begin::modal header-->
                    <div class="modal-header">
                        <h3 class="modal-title">Jana Surat Maklum Balas KIL</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::modal header-->
                    <!--begin::modal body-->
                    <div class="modal-body hover-scroll-y pt-10 pb-15 px-lg-17">
                        <!--begin::Stepper-->
                        <div class="stepper stepper-links" id="kt_stepper_mbkil_<?php echo $row['WyStatus'].$row['WyID'] ?>">
                            <!--begin::Nav-->
                            <div class="stepper-nav flex-center flex-wrap mb-8">
                                <!--begin::Step 1-->
                                <div class="stepper-item current" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Provider</h3>
                                </div>
                                <!--end::Step 1-->
                                <!--begin::Step 2-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Pemohon</h3>
                                </div>
                                <!--end::Step 2-->
                                <!--begin::Step 3-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Projek</h3>
                                </div>
                                <!--end::Step 3-->
                                <!--begin::Step 4-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Surat</h3>
                                </div>
                                <!--end::Step 4-->
                                <!--begin::Step 5-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Salinan Kepada</h3>
                                </div>
                                <!--end::Step 5-->
                            </div>
                            <!--end::Nav-->
                            <!--begin::Form-->
                            <form class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" id="form-<?php echo $row['WyStatus'].$row['WyID'] ?>">
                                <!--begin::Scroll-->
                                <div class="hover-scroll h-300px px-5 mb-10">
                                    <!--begin::Group-->
                                    <div class="mb-5">
                                        <!--begin::Step 1-->
                                        <div class="flex-column current" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">Penyedia Utiliti</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="provider_name" value="<?php echo strtoupper($row['ProviderName']) ?>" readonly/>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="up_provider" />
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Alamat</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="addr_provider_1" placeholder="Alamat Pertama" />
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                                <input type="text" class="form-control form-control-solid" name="addr_provider_2" placeholder="Alamat Kedua" />
                                                <!-- <input class="form-control form-control-lg form-control-solid mb-2" name="addr_provider_3"> -->

                                                <!--begin::Input group-->
                                                <div class="row fv-row fv-plugins-icon-container mt-3">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="postcode" name="postcode" placeholder="Poskod" />
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="city" name="city" placeholder="Bandar" />
                                                    </div>
                                                    <div class="col-4">
                                                    <input type="text" class="form-control form-control-solid" id="state" name="state" placeholder="Negeri" />
                                                    </div>
                                                </div>
                                                <!--end::Input group-->
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 1-->

                                        <!--begin::Step 2-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">Nama Syarikat</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <?php
                                                    $data = explode(",", $row['ContactId']);
                                                    $firstElement = $data[0];
                                                    $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                    $contact = Wayleave::getContacts($contactId);

                                                    foreach ($contact as $row3) {
                                                        echo '<input class="form-control form-control-solid" name="company_name" value="'. strtoupper($row3['CompanyName']) .'" readonly />';
                                                    }
                                                ?>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="up_pemohon" />
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Alamat</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="addr_client_1" placeholder="Alamat Pertama" />
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                                <input type="text" class="form-control form-control-solid" name="addr_client_2" placeholder="Alamat Kedua" />

                                                <!--begin::Input group-->
                                                <div class="row fv-row fv-plugins-icon-container mt-3">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="postcode_client" name="postcode_client" placeholder="Poskod" />
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="city_client" name="city_client" placeholder="Bandar" />
                                                    </div>
                                                    <div class="col-4">
                                                    <input type="text" class="form-control form-control-solid" id="state_client" name="state_client" placeholder="Negeri" />
                                                    </div>
                                                </div>
                                                <!--end::Input group-->
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 2-->

                                        <!--begin::Step 3-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="pe-3 mb-6">
                                                <div class="fs-6 fw-semibold mb-2">Tajuk Projek</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="text-muted me-2 fs-6"><?php echo $row['ProjectTitle'] ?></div>
                                                </div>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <div class="required fs-6 fw-semibold mb-2">Tajuk Kecil</div>
                                                <!--begin::Radio-->
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" name="small_title" value="1" />
                                                    <label class="form-check-label">
                                                        Kelulusan Bersyarat Kebenaran Izin Lalu
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-custom form-check-solid mt-2">
                                                    <input class="form-check-input" type="radio" name="small_title" value="2" />
                                                    <label class="form-check-label">
                                                        Penyediaan Wang Cagaran / Deposit Bagi Permohonan Kebenaran Mula Kerja Bersyarat
                                                    </label>
                                                </div>
                                                <!--end::Radio-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="pe-3 mb-6">
                                            <?php 
                                                $i = 1;
                                                $road = Wayleave::getRoad($row['SysID']);
                                                foreach ($road as $row4) {
                                                    $data = explode(",", $row4['RoadId']);
                                                    ?>
                                                    <div class="fs-5 fw-bold mb-2"><?php echo $i.".  ". $row4['AuthorityName'] ?></div>

                                                    <!--begin::Input group-->
                                                    <div class="pe-3 mb-5">
                                                        <div class="fs-6 fw-semibold mb-1">Jalan Terlibat :</div>
                                                            <?php
                                                            foreach ($data as $rTrim) {
                                                                $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                                $road_name = Wayleave::getRoadName($roadIdTrim);

                                                                foreach ($road_name as $rData) { ?>
                                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                                    <div class="text-muted me-2 fs-6"><?php echo $rData['RoadName'] ?></div>
                                                                </div>
                                                                <?php } 
                                                            } ?>
                                                    </div>
                                                    <!--end::Input group-->

                                                    <!--begin::Repeater-->
                                                    <div id="kwc-involved">
                                                        <!--begin::Form group-->
                                                        <div class="form-group">
                                                            <div data-repeater-list="kwc-list">
                                                                <div data-repeater-item>
                                                                    <div class="form-group row mb-5">
                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <label class="form-label">Tempoh Kelulusan Izin Lalu</label>
                                                                            <input class="form-control form-control-solid" placeholder="Sila Pilih Tarikh" name="period_kil" id="kt_daterangepicker_1"/>
                                                                        </div>
                                                                        <!--begin::Input group-->
                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <div class="fs-6 fw-semibold mb-2">Keterangan Bayaran</div>
                                                                            <!--begin::Radio-->
                                                                            <div class="form-check form-check-custom form-check-solid">
                                                                                <input class="form-check-input" type="radio" name="payment_detail" value="1" />
                                                                                <label class="form-check-label">
                                                                                    Wang Cagaran
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check form-check-custom form-check-solid mt-2">
                                                                                <input class="form-check-input" type="radio" name="payment_detail" value="2" />
                                                                                <label class="form-check-label">
                                                                                    Wang Cagaran Berkelompok
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check form-check-custom form-check-solid mt-2">
                                                                                <input class="form-check-input" type="radio" name="payment_detail" value="3" />
                                                                                <label class="form-check-label">
                                                                                    Born Pelaksanaan
                                                                                </label>
                                                                            </div>
                                                                            <!--end::Radio-->
                                                                        </div>
                                                                        <!--end::Input group-->

                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <label class="form-label">Jumlah (RM)</label>
                                                                            <textarea class="form-control form-control-lg form-control-solid" name="amount"></textarea>
                                                                        </div>
                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <label class="form-label">Nota Khas</label>
                                                                            <textarea class="form-control form-control-lg form-control-solid" name="notes"></textarea>
                                                                        </div>

                                                                        <input type="text" name="authority_id" value="<?php echo $row4['AuthorityId'] ?>" hidden>
                                                                        <div class="separator separator-dashed mt-2"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end::Form group-->
                                                        </div>
                                                        <!--end::Form group-->
                                                    </div>
                                                    <!--end::Repeater-->

                                                    <?php
                                                    $i++;
                                                    echo '<input type="text" name="entry_list_road_id" value="'.$row4['RoadId'].'" hidden>';
                                                }
                                            ?>
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--begin::Step 3-->

                                        <!--begin::Step 4-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <label class="d-flex flex-stack mb-5">
                                                    <!--begin:Label-->
                                                    <span class="d-flex align-items-center me-2">
                                                        <!--begin::Description-->
                                                        <span class="d-flex flex-column">
                                                            <span class="form-label">Tarikh</span>
                                                            <span class="fs-6 text-muted"><?php echo $letter_date ?></span>
                                                        </span>
                                                        <!--end:Description-->
                                                    </span>
                                                    <!--end:Label-->

                                                    <!--begin:Label-->
                                                    <span class="d-flex align-items-center me-2">
                                                        <!--begin::Description-->
                                                        <span class="d-flex flex-column">
                                                            <span class="form-label">No Rujukan Surat</span>
                                                            <?php 
                                                                $ltrRefNo = Wayleave::getLetterRefNo($row['SysID'], $row['RefNo']);
                                                                echo '<span class="fs-6 text-muted">'. $ltrRefNo .'</span>'; 
                                                            ?>
                                                        </span>
                                                        <!--end:Description-->
                                                    </span>
                                                    <!--end:Label-->

                                                </label>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">No Rujukan Tuan</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input class="form-control form-control-solid" name="client_ref_no" >
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Pegawai 1 Untuk Dihubungi</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select id="selection-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="form-control form-control-lg form-control-solid" name="staff_name" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true"><?php echo $option ?></select>

                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">No Telefon Pegawai 1 Untuk Dihubungi</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input id="staff_contact" class="form-control form-control-lg form-control-solid" name="staff_contact" readonly>

                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Pegawai 2 Untuk Dihubungi</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select id="selection2-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="form-control form-control-lg form-control-solid" name="staff_name_2" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true"><?php echo $option2 ?></select>

                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">No Telefon Pegawai 2 Untuk Dihubungi</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input id="staff_contact_2" class="form-control form-control-lg form-control-solid" name="staff_contact_2" readonly>
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Pegawai Melulus</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select id="selection3-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="form-control form-control-lg form-control-solid" name="approval_by" data-placeholder="Sila Pilih Pegawai Melulus" data-allow-clear="true"><?php echo $option3 ?></select>

                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">Jawatan Pegawai Melulus</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input id="approval_position" class="form-control form-control-lg form-control-solid" name="approval_position" readonly>
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 4-->

                                        <!--begin::Step 5-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Repeater-->
                                            <div id="kt_copy_to">
                                                <div data-repeater-list="kt_copy_to">
                                                    <div data-repeater-item class="border border-gray-300 p-5 mb-5">
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama Syarikat:</label>
                                                                <input name="company_name_r" class="form-control mb-2 mb-md-0" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Untuk Perhatian: <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                                <input name="name_r" class="form-control mb-2 mb-md-0" />
                                                            </div>
                                                            <div class="col-md-12 mt-3">
                                                                <label class="form-label">Alamat:</label>
                                                                <input name="addr_1_r" class="form-control mb-2" />
                                                                <input name="addr_2_r" class="form-control mb-2" />
                                                                <!--begin::Input group-->
                                                                <div class="row fv-row fv-plugins-icon-container mt-3">
                                                                    <div class="col-4">
                                                                        <input type="text" class="form-control form-control-solid" id="postcode_r" name="postcode_r" placeholder="Poskod" />
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <input type="text" class="form-control form-control-solid" id="city_r" name="city_r" placeholder="Bandar" />
                                                                    </div>
                                                                    <div class="col-4">
                                                                    <input type="text" class="form-control form-control-solid" id="state_r" name="state_r" placeholder="Negeri" />
                                                                    </div>
                                                                </div>
                                                                <!--end::Input group-->
                                                            </div>
                                                            <div class="col-md-3">
                                                                <button type="button" data-repeater-delete class="btn btn-sm btn-light-danger mt-5 md-8">
                                                                    <i class="fad fa-trash"></i>Padam
                                                                </button>
                                                            </div>
                                                            <!-- <div class="separator separator-dashed mb-8"></div> -->
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--begin::Form group-->
                                                <div class="form-group">
                                                    <button type="button" id="btn-create" data-repeater-create class="btn btn-sm btn-light-primary md-8 mb-8">
                                                        <i class="fad fa-plus"></i>Tambah Salinan Kepada
                                                    </button>
                                                </div>
                                                <!--end::Form group-->
                                            </div>
                                            <!--end::Repeater-->
                                        </div>
                                        <!--end::Step 5-->

                                    </div>
                                    <!--end::Group-->
                                </div>
                                <!--end::Scroll-->

                                <input type="text" name="system_id" value="<?php echo $row['SysID'] ?>" hidden>
                                <input type="text" name="item" value="generate-wyFeedback" hidden>
                                <input type="text" name="inv_date" value="<?php echo $generateDate ?>" hidden>
                                <input type="text" name="letter_date" value="<?php echo $current_date->format('Y-m-d') ?>" hidden>
                                <input type="text" name="letter_ref_no" value="<?php echo $ltrRefNo ?>" hidden>
                                <input type="text" name="item_42" value="wyFeedback-ucidos" hidden>
                                <input type="text" name="wy_status" value="<?php echo $row['WyStatus'] ?>" hidden> 
                                <input type="text" name="wy_id" value="<?php echo $row['WyID'] ?>" hidden>
                                <input type="text" name="authority_id" value="<?php echo $row['AuthorityID'] ?>" hidden>

                                <!--begin::Actions-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Wrapper-->
                                    <div class="me-2">
                                        <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                                            Sebelumnya
                                        </button>
                                    </div>
                                    <!--end::Wrapper-->

                                    <!--begin::Wrapper-->
                                    <div>
                                        <button type="submit" id="submit-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="btn btn-primary" data-kt-stepper-action="submit">
                                            <span class="indicator-label">
                                                Hantar
                                            </span>
                                            <span class="indicator-progress">
                                                Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>

                                        <button type="button" class="btn btn-primary" data-kt-stepper-action="next">
                                            Seterusnya
                                        </button>
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Stepper-->
                    </div>
                    <!--end::modal body-->
                </div>
                <!--end::modal-content-->
            </div>
        </div>
        <!--end::Modal - mkil - Add-->
<?php
    }

} else if($appsTitle == 'KITER' || $appsTitle == 'KUK') {
    foreach ($wayleave_mkil as $row) {
        $datepickerId = 'datepicker-' . $row['Status']. $row['StatusID'];
?>
        <!--begin::Modal - mkil - Add-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-<?php echo $row['WyStatus']. $row['WyID']?>" >
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::modal-content-->
                <div class="modal-content">
                    <!--begin::modal header-->
                    <div class="modal-header">
                        <h3 class="modal-title">Jana Surat Maklum Balas KIL</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::modal header-->
                    <!--begin::modal body-->
                    <div class="modal-body hover-scroll-y pt-10 pb-15 px-lg-17">
                        <!--begin::Stepper-->
                        <div class="stepper stepper-links" id="kt_stepper_mbkil_<?php echo $row['WyStatus'].$row['WyID'] ?>">
                            <!--begin::Nav-->
                            <div class="stepper-nav flex-center flex-wrap mb-8">
                                <!--begin::Step 1-->
                                <div class="stepper-item current" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Provider</h3>
                                </div>
                                <!--end::Step 1-->
                                <!--begin::Step 2-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Pemohon</h3>
                                </div>
                                <!--end::Step 2-->
                                <!--begin::Step 3-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Projek</h3>
                                </div>
                                <!--end::Step 3-->
                                <!--begin::Step 4-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Invois</h3>
                                </div>
                                <!--end::Step 4-->
                                <!--begin::Step 5-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Surat</h3>
                                </div>
                                <!--end::Step 5-->
                                <!--begin::Step 6-->
                                <div class="stepper-item" data-kt-stepper-element="nav">
                                    <h3 class="stepper-title">Salinan Kepada</h3>
                                </div>
                                <!--end::Step 6-->
                            </div>
                            <!--end::Nav-->
                            <!--begin::Form-->
                            <form class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" id="form-<?php echo $row['WyStatus'].$row['WyID'] ?>">
                                <!--begin::Scroll-->
                                <div class="hover-scroll h-300px px-5 mb-10">
                                    <!--begin::Group-->
                                    <div class="mb-5">
                                        <!--begin::Step 1-->
                                        <div class="flex-column current" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">Penyedia Utiliti</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="provider_name" value="<?php echo strtoupper($row['ProviderName']) ?>" readonly/>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="up_provider" />
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Alamat</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="addr_provider_1" placeholder="Alamat Pertama" />
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                                <input type="text" class="form-control form-control-solid" name="addr_provider_2" placeholder="Alamat Kedua" />
                                                <!-- <input class="form-control form-control-lg form-control-solid mb-2" name="addr_provider_3"> -->

                                                <!--begin::Input group-->
                                                <div class="row fv-row fv-plugins-icon-container mt-3">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="postcode" name="postcode" placeholder="Poskod" />
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="city" name="city" placeholder="Bandar" />
                                                    </div>
                                                    <div class="col-4">
                                                    <input type="text" class="form-control form-control-solid" id="state" name="state" placeholder="Negeri" />
                                                    </div>
                                                </div>
                                                <!--end::Input group-->
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 1-->

                                        <!--begin::Step 2-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">Nama Syarikat</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <?php
                                                    $data = explode(",", $row['ContactId']);
                                                    $firstElement = $data[0];
                                                    $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                    $contact = Wayleave::getContacts($contactId);

                                                    foreach ($contact as $row3) {
                                                        echo '<input class="form-control form-control-solid" name="company_name" value="'. strtoupper($row3['CompanyName']) .'" readonly />';
                                                    }
                                                ?>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="up_pemohon" />
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Alamat</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input type="text" class="form-control form-control-solid" name="addr_client_1" placeholder="Alamat Pertama" />
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                                <input type="text" class="form-control form-control-solid" name="addr_client_2" placeholder="Alamat Kedua" />

                                                <!--begin::Input group-->
                                                <div class="row fv-row fv-plugins-icon-container mt-3">
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="postcode_client" name="postcode_client" placeholder="Poskod" />
                                                    </div>
                                                    <div class="col-4">
                                                        <input type="text" class="form-control form-control-solid" id="city_client" name="city_client" placeholder="Bandar" />
                                                    </div>
                                                    <div class="col-4">
                                                    <input type="text" class="form-control form-control-solid" id="state_client" name="state_client" placeholder="Negeri" />
                                                    </div>
                                                </div>
                                                <!--end::Input group-->
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 2-->

                                        <!--begin::Step 3-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="pe-3 mb-6">
                                                <div class="fs-6 fw-semibold mb-2">Tajuk Projek</div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="text-muted me-2 fs-6"><?php echo $row['ProjectTitle'] ?></div>
                                                </div>
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-6 fv-row fv-plugins-icon-container">
                                                <div class="required fs-6 fw-semibold mb-2">Tajuk Kecil</div>
                                                <!--begin::Radio-->
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" name="small_title" value="1" />
                                                    <label class="form-check-label">
                                                        Kelulusan Bersyarat Kebenaran Izin Lalu
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-custom form-check-solid mt-2">
                                                    <input class="form-check-input" type="radio" name="small_title" value="2" />
                                                    <label class="form-check-label">
                                                        Penyediaan Wang Cagaran / Deposit Bagi Permohonan Kebenaran Mula Kerja Bersyarat
                                                    </label>
                                                </div>
                                                <!--end::Radio-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="pe-3 mb-6">
                                            <?php 
                                                $i = 1;
                                                $road = Wayleave::getRoad($row['SysID']);
                                                foreach ($road as $row4) {
                                                    $data = explode(",", $row4['RoadId']);
                                                    ?>
                                                    <div class="fs-5 fw-bold mb-2"><?php echo $i.".  ". $row4['AuthorityName'] ?></div>

                                                    <!--begin::Input group-->
                                                    <div class="pe-3 mb-5">
                                                        <div class="fs-6 fw-semibold mb-1">Jalan Terlibat :</div>
                                                            <?php
                                                            foreach ($data as $rTrim) {
                                                                $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                                $road_name = Wayleave::getRoadName($roadIdTrim);

                                                                foreach ($road_name as $rData) { ?>
                                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                                    <div class="text-muted me-2 fs-6"><?php echo $rData['RoadName'] ?></div>
                                                                </div>
                                                                <?php } 
                                                            } ?>
                                                    </div>
                                                    <!--end::Input group-->

                                                    <!--begin::Repeater-->
                                                    <div id="kwc-involved">
                                                        <!--begin::Form group-->
                                                        <div class="form-group">
                                                            <div data-repeater-list="kwc-list">
                                                                <div data-repeater-item>
                                                                    <div class="form-group row mb-5">
                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <label class="form-label">Tempoh Kelulusan Izin Lalu</label>
                                                                            <input type="text" id="period-kil" name="period_kil" class="form-control form-control-lg form-control-solid">
                                                                        </div>
                                                                        <!--begin::Input group-->
                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <div class="fs-6 fw-semibold mb-2">Keterangan Bayaran</div>
                                                                            <!--begin::Radio-->
                                                                            <div class="form-check form-check-custom form-check-solid">
                                                                                <input class="form-check-input" type="radio" name="payment_detail" value="1" />
                                                                                <label class="form-check-label">
                                                                                    Wang Cagaran
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check form-check-custom form-check-solid mt-2">
                                                                                <input class="form-check-input" type="radio" name="payment_detail" value="2" />
                                                                                <label class="form-check-label">
                                                                                    Wang Cagaran Berkelompok
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check form-check-custom form-check-solid mt-2">
                                                                                <input class="form-check-input" type="radio" name="payment_detail" value="3" />
                                                                                <label class="form-check-label">
                                                                                    Born Pelaksanaan
                                                                                </label>
                                                                            </div>
                                                                            <!--end::Radio-->
                                                                        </div>
                                                                        <!--end::Input group-->

                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <label class="form-label">Jumlah (RM)</label>
                                                                            <textarea class="form-control form-control-lg form-control-solid" name="amount"></textarea>
                                                                        </div>
                                                                        <div class="col-12 col-md-12 mb-5 fv-row">
                                                                            <label class="form-label">Nota Khas</label>
                                                                            <textarea class="form-control form-control-lg form-control-solid" name="notes"></textarea>
                                                                        </div>

                                                                        <input type="text" name="authority_id" value="<?php echo $row4['AuthorityId'] ?>" hidden>
                                                                        <div class="separator separator-dashed mt-2"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!--end::Form group-->
                                                        </div>
                                                        <!--end::Form group-->
                                                    </div>
                                                    <!--end::Repeater-->

                                                    <?php
                                                    $i++;
                                                    echo '<input type="text" name="entry_list_road_id" value="'.$row4['RoadId'].'" hidden>';
                                                }
                                            ?>
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--begin::Step 3-->

                                        <!--begin::Step 4-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row">
                                                <!--begin::Label-->
                                                <label class="form-label required">No Invois</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input class="form-control form-control-lg form-control-solid" name="inv_no">
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Tarikh Invois</label>
                                                <!--end::Label-->
                                                <div class="position-relative d-flex align-items-center">
                                                    <!--begin::Icon-->
                                                    <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                                        <span class="symbol-label bg-secondary">
                                                            <i class="fad fa-calendar"></i>
                                                        </span>
                                                    </div>
                                                    <!--end::Icon-->
                                                    <!--begin::Datepicker-->
                                                    <input id="inv-date-<?php echo $row['WyStatus'].$row['WyID'] ?>" name="inv_date" class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh"/>
                                                <!--end::Datepicker-->
                                                </div>
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 4-->

                                        <!--begin::Step 5-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <label class="d-flex flex-stack mb-5">
                                                    <!--begin:Label-->
                                                    <span class="d-flex align-items-center me-2">
                                                        <!--begin::Description-->
                                                        <span class="d-flex flex-column">
                                                            <span class="form-label">Tarikh</span>
                                                            <span class="fs-6 text-muted"><?php echo $letter_date ?></span>
                                                        </span>
                                                        <!--end:Description-->
                                                    </span>
                                                    <!--end:Label-->
                                                </label>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Tarikh Hijrah</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input class="form-control form-control-lg form-control-solid" name="date_hijri" value="<?php echo $hijri_date ?> ">
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <label class="d-flex flex-stack mb-5">
                                                    <!--begin:Label-->
                                                    <span class="d-flex align-items-center me-2">
                                                        <!--begin::Description-->
                                                        <span class="d-flex flex-column">
                                                            <span class="form-label">No Rujukan Surat</span>

                                                            <?php
                                                                $ltrRefNo = Wayleave::getLetterRefNo($row['SysID'], $row['RefNo']);
                                                                echo '<span class="fs-6 text-muted">'. $ltrRefNo .'</span>';
                                                            ?>
                                                        </span>
                                                        <!--end:Description-->
                                                    </span>
                                                    <!--end:Label-->
                                                </label>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Pegawai Untuk Dihubungi</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select id="selection-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="form-control form-control-lg form-control-solid" name="staff_name" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true"><?php echo $option ?></select>

                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">No Telefon Pegawai Untuk Dihubungi</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input id="staff_contact" class="form-control form-control-lg form-control-solid" name="staff_contact" readonly>
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label required">Pegawai Melulus</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select id="selection3-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="form-control form-control-lg form-control-solid" name="approval_by" data-placeholder="Sila Pilih Pegawai Melulus" data-allow-clear="true"><?php echo $option3 ?></select>

                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="mb-5 fv-row fv-plugins-icon-container">
                                                <!--begin::Label-->
                                                <label class="form-label">Jawatan Pegawai Melulus</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <input id="approval_position" class="form-control form-control-lg form-control-solid" name="approval_position" readonly>
                                                <!--end::Input-->
                                                <div class="fv-plugins-message-container invalid-feedback"></div>
                                            </div>
                                            <!--end::Input group-->
                                        </div>
                                        <!--end::Step 5-->

                                        <!--begin::Step 6-->
                                        <div class="flex-column" data-kt-stepper-element="content">
                                            <!--begin::Repeater-->
                                            <div id="kt_copy_to">
                                                <div data-repeater-list="kt_copy_to">
                                                    <div data-repeater-item class="border border-gray-300 p-5 mb-5">
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Nama Syarikat:</label>
                                                                <input name="company_name_r" class="form-control mb-2 mb-md-0" />
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Untuk Perhatian: <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                                <input name="name_r" class="form-control mb-2 mb-md-0" />
                                                            </div>
                                                            <div class="col-md-12 mt-3">
                                                                <label class="form-label">Alamat:</label>
                                                                <input name="addr_1_r" class="form-control mb-2" />
                                                                <input name="addr_2_r" class="form-control mb-2" />
                                                                <!--begin::Input group-->
                                                                <div class="row fv-row fv-plugins-icon-container mt-3">
                                                                    <div class="col-4">
                                                                        <input type="text" class="form-control form-control-solid" id="postcode_r" name="postcode_r" placeholder="Poskod" />
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <input type="text" class="form-control form-control-solid" id="city_r" name="city_r" placeholder="Bandar" />
                                                                    </div>
                                                                    <div class="col-4">
                                                                    <input type="text" class="form-control form-control-solid" id="state_r" name="state_r" placeholder="Negeri" />
                                                                    </div>
                                                                </div>
                                                                <!--end::Input group-->
                                                            </div>
                                                            <div class="col-md-3">
                                                                <button type="button" data-repeater-delete class="btn btn-sm btn-light-danger mt-5 md-8">
                                                                    <i class="fad fa-trash"></i>Padam
                                                                </button>
                                                            </div>
                                                            <!-- <div class="separator separator-dashed mb-8"></div> -->
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--begin::Form group-->
                                                <div class="form-group">
                                                    <button type="button" id="btn-create" data-repeater-create class="btn btn-sm btn-light-primary md-8 mb-8">
                                                        <i class="fad fa-plus"></i>Tambah Salinan Kepada
                                                    </button>
                                                </div>
                                                <!--end::Form group-->
                                            </div>
                                            <!--end::Repeater-->
                                        </div>
                                        <!--end::Step 6-->

                                    </div>
                                    <!--end::Group-->
                                </div>
                                <!--end::Scroll-->

                                <input type="text" name="system_id" value="<?php echo $row['SysID'] ?>" hidden>
                                <input type="text" name="item" value="generate-wyFeedback" hidden>
                                <input type="text" name="inv_date" value="<?php echo $generateDate ?>" hidden>
                                <input type="text" name="letter_date" value="<?php echo $current_date->format('Y-m-d') ?>" hidden>
                                <input type="text" name="letter_ref_no" value="<?php echo $ltrRefNo ?>" hidden>
                                <input type="text" name="item_42" value="wyFeedback-kiter" hidden>
                                <input type="text" name="wy_status" value="<?php echo $row['WyStatus'] ?>" hidden> 
                                <input type="text" name="wy_id" value="<?php echo $row['WyID'] ?>" hidden>
                                <input type="text" name="authority_id" value="<?php echo $row['AuthorityID'] ?>" hidden>

                                <!--begin::Actions-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Wrapper-->
                                    <div class="me-2">
                                        <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                                            Sebelumnya
                                        </button>
                                    </div>
                                    <!--end::Wrapper-->

                                    <!--begin::Wrapper-->
                                    <div>
                                        <button type="submit" id="submit-<?php echo $row['WyStatus'].$row['WyID'] ?>" class="btn btn-primary" data-kt-stepper-action="submit">
                                            <span class="indicator-label">
                                                Hantar
                                            </span>
                                            <span class="indicator-progress">
                                                Sila Tunggu... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>

                                        <button type="button" class="btn btn-primary" data-kt-stepper-action="next">
                                            Seterusnya
                                        </button>
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Stepper-->
                    </div>
                    <!--end::modal body-->
                </div>
                <!--end::modal-content-->
            </div>
        </div>
        <!--end::Modal - mkil - Add-->
<?php 
    }
}
?>

