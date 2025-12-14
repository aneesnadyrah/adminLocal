
<?php

include "config/tenant.php";
// include "config/autoload.php";

if (!empty($_GET['sid']) && !empty($_GET['id'])) {
    $sysID = $_GET['sid'];
    $letterWyFeedback_id = $_GET['id'];
}

$option = '<option></option>';
$option2 = '<option></option>';
$option3 = '<option></option>';

date_default_timezone_set('Asia/Kuala_Lumpur');
$current_date = new DateTime();
$generateDate = $current_date->format('Y-m-d H:i:s');
$letter_date = General::convertDate($generateDate); //date in masihi
$hijri_date = Wayleave::convertToHijri($generateDate); // date in hijri

if($appsTitle == 'UCIDOS') {
    if($letterWyFeedback_id == 4) {
        $wy_feedback_kil = Wayleave::wyFeedbackModelAmend($_GET['sid']); //for amend based on status = 4 (tak sah)
    } else {
        $wy_feedback_kil = Wayleave::wyFeedbackModelEdit($sysID,$letterWyFeedback_id); //for edit based on letterWyFeedback_id
    }

    foreach ($wy_feedback_kil as $row) {
        if($row['StatusID'] == 51 || $row['StatusID'] == 47) {
            $datepickerId = 'datepicker-' . $row['StatusID']. $row['ID'];
            $selectedValue = $row['StaffApproval'];
            $selectedName = $row['StaffName'];
            $selectedName2 = $row['StaffName2'];

            foreach (Wayleave::assignStaff(1) as $data2) {
                if($data2['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data2['ProfilePic'];
                };
                $name = $data2['FirstName'].' '.$data2['LastName'];
                $id = $data2['username'];
                $contact = $data2['StaffPhoneNo'];
            
                $option .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'"';
                if ($id == $selectedName) {
                    $option .= ' selected';
                }
                $option .= '>'.$name.'</option>';

                $option2 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'"';
                if ($id == $selectedName2) {
                    $option2 .= ' selected';
                }
                $option2 .= '>'.$name.'</option>';

            }

            foreach (Wayleave::assignStaff(2) as $data) {
                if($data['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data['ProfilePic'];
                };
                $name = $data['FirstName'].' '.$data['LastName'];
                $id = $data['username'];
                $contact = $data['StaffPhoneNo'];
                $position = $data['Position'];
            
                $option3 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-position="'.$position.'"';
                if ($id == $selectedValue) {
                    $option3 .= ' selected';
                }
                $option3 .= '>'.$name.'</option>';
            }

            echo '<!--begin::Modal - mkil - Add-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="edit-feedback-generate" >
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::modal-content-->
                    <div class="modal-content">
                        <!--begin::modal header-->
                        <div class="modal-header">
                            <h3 class="modal-title">Kemaskini Surat Maklum Balas Kelulusan Izin Lalu</h3>

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
                            <div class="stepper stepper-links" id="kt_stepper_mbkil_edit">
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
                                </div>
                                <!--end::Nav-->
                                <!--begin::Form-->
                                <form class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" id="form-mbkil-edit">

                                    <!--begin::Scroll-->
                                    <div class="hover-scroll h-300px px-5 mb-10">
                                        <!--begin::Group-->
                                        <div class="mb-5">
                                            <!--begin::Step 1-->
                                            <div class="flex-column current" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Penyedia Utiliti</span>
                                                                <span class="fs-6 text-muted">'. $row['ProviderName'] .'</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[1];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_provider" value="'.$row['UpProvider'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 1-->

                                            <!--begin::Step 2-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Nama Syarikat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['CompanyName'] . '</span>';
                                                                }

                                                                echo '
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_pemohon" value="'.$row['UpClient'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--begin::Step 2-->

                                            <!--begin::Step 3-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Tajuk Projek</span>
                                                                <span class="fs-6 text-muted">' . $row['ProjectTitle'] . '</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::table-->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td class="form-label">NAMA JALAN</td>
                                                                <td class="form-label">PBM / PBT</td>
                                                                
                                                            </tr>
                                                            ';
                                                            $road = Wayleave::getRoad($row['SysID']);
                                                            foreach ($road as $row4) {
                                                                $data = explode(",", $row4['RoadId']);
                                                                echo '<tr>';
                                                                    echo '<td class="fs-6 text-muted">';
                                                                    echo '<table class="table table-row-bordered table-row">'; 
                                                                    // Start the nested table
                                                                    foreach ($data as $rTrim) {
                                                                        $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                                        $road_name = Wayleave::getRoadName($roadIdTrim);
                                                                        echo '<tr>';
                                                                        foreach ($road_name as $rData) {
                                                                            echo '<td class="fs-6 text-muted text-center">' . $rData['RoadName'] . '</td>';
                                                                        }
                                                                        echo '</tr>';
                                                                    }
                                                                    echo '</table>'; 
                                                                    // End the nested table
                                                                    echo '</td>';
                                                                    echo '<td class="fs-6 text-muted text-center">' . $row4['AuthorityName'] . '</td>';
                                                                    // echo '<td class="fs-6 text-muted text-center"></td>';
                                                                echo '</tr>';

                                                                echo '<input type="text" name="entry_list_road_id" value="'.$row4['RoadId'].'" hidden>';

                                                            }
                                                            echo '
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::table-->';

                                                // $total_authority = Wayleave::getRoad($row['SysID']);

                                                // // Define an array to store the input values
                                                // // $inputValues = array();

                                                // foreach ($total_authority as $data) {
                                                //     $authorityId = $data['AuthorityId'];

                                                //     echo'
                                                //     <!--begin::Input group-->
                                                //     <div class="mb-5 fv-row fv-plugins-icon-container">
                                                //         <!--begin::Label-->
                                                //         <label class="form-label required">Jumlah Bayaran bagi '. $data['AuthorityName'] .'</label>
                                                //         <!--end::Label-->
                                                //         <!--begin::Input-->
                                                //         <textarea class="form-control form-control-lg form-control-solid" rows="4" name="jumlah" placeholder="Cth: (Deposit Keselamatan 100% Dalam Bentuk Jaminan Bank)"></textarea>
                                                //         <!--end::Input-->
                                                //         <div class="fv-plugins-message-container invalid-feedback"></div>
                                                //     </div>
                                                //     <!--end::Input group-->';

                                                //     // Store the input value in the array
                                                //     // $inputValues[$authorityId] = $_POST['jumlah'][$authorityId] ?? '';
                                                // }
                                                // Use $inputValues array to access the stored values
                                                // print_r($inputValues);

                                                echo'
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
                                                                <span class="form-label">Tarikh</span>';
                                                                if($row['StatusID'] == 47) {
                                                                    echo '<span class="fs-6 text-muted">'. General::convertDate($row['LetterDate']) .'</span>';
                                                                } else {
                                                                    echo '<span class="fs-6 text-muted">'. $letter_date .'</span>';
                                                                }

                                                            echo'
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->

                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">No Rujukan Surat</span>
                                                                <span class="fs-6 text-muted">'.$row['LetterRefNo'].'</span>
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
                                                    <label class="form-label required">No Rujukan Tuan</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="client_ref_no" value="'.$row['ClientRefNo'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <!--begin::Label-->
                                                    <label class="form-label required">Pegawai 1 Untuk Dihubungi</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select id="selection-staff" class="form-control form-control-lg form-control-solid" name="staff_name" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true">'. $option .'</select>
                                                    
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
                                                    <input id="staff_contact" class="form-control form-control-lg form-control-solid" name="staff_contact" readonly value="'.$row['StaffContact'].'">
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
                                                    <select id="selection-staff2" class="form-control form-control-lg form-control-solid" name="staff_name_2" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true">'. $option2 .'</select>
                                                    
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
                                                    <input id="staff_contact_2" class="form-control form-control-lg form-control-solid" name="staff_contact_2" readonly value="'.$row['StaffContact2'].'">                                                       
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
                                                    <select id="selection-approval" class="form-control form-control-lg form-control-solid" name="approval_by" data-placeholder="Sila Pilih Pegawai Melulus" data-allow-clear="true">'. $option3 .'</select>
                                                    
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
                                                    <input id="approval_position" class="form-control form-control-lg form-control-solid" name="approval_position" readonly value="'.$row['StaffApprovalPosition'].'">                                                       
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 4-->
                                        </div>
                                        <!--end::Group-->
                                    </div>
                                    <!--end::Scroll-->

                                    <input type="text" name="system_id"  value="'. $row['SysID'] .'" hidden>
                                    <input type="text" name="item" value="generate-wyFeedback-edit" hidden>
                                    <input type="text" name="letter_date" value="'.$current_date->format('Y-m-d').'" hidden>
                                    <input type="text" name="letter_ref_no" value="'.$row['LetterRefNo'].'" hidden>
                                    <input type="text" name="item_42_edit" value="wyFeedback-edit-ucidos" hidden>
                                    <input type="text" name="wy_feedback_id" value="'.$row['WyFeedbackId'].'" hidden>
                                    <input type="text" name="status" value="'.$row['StatusID'].'" hidden>

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
                                            <button type="submit" id="wyFeedback-edit-submit" class="btn btn-primary" data-kt-stepper-action="submit">
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
            </div><!--end::Modal - mkil - Add-->';
        }
    }

} else if($appsTitle == 'KITER') {
    if($letterWyFeedback_id == 4) {
        $wy_feedback_kil = Wayleave::wyFeedbackModelAmend($_GET['sid']); //for amend based on status = 4 (tak sah)
    } else {
        $wy_feedback_kil = Wayleave::wyFeedbackModelEdit($sysID,$letterWyFeedback_id); //for edit based on letterWyFeedback_id
    }

    foreach ($wy_feedback_kil as $row) {
        if($row['StatusID'] == 51 || $row['StatusID'] == 47) {
            $datepickerId = 'datepicker-' . $row['StatusID']. $row['ID'];
            $selectedValue = $row['StaffApproval'];
            $selectedName = $row['StaffName'];

            foreach (Wayleave::assignStaff(1) as $data2) {
                if($data2['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data2['ProfilePic'];
                };
                $name = $data2['FirstName'].' '.$data2['LastName'];
                $id = $data2['username'];
                $contact = $data2['StaffPhoneNo'];
            
                $option .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'"';
                if ($id == $selectedName) {
                    $option .= ' selected';
                }
                $option .= '>'.$name.'</option>';

            }

            foreach (Wayleave::assignStaff(2) as $data) {
                if($data['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data['ProfilePic'];
                };
                $name = $data['FirstName'].' '.$data['LastName'];
                $id = $data['username'];
                $contact = $data['StaffPhoneNo'];
                $position = $data['Position'];
            
                $option3 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-position="'.$position.'"';
                if ($id == $selectedValue) {
                    $option3 .= ' selected';
                }
                $option3 .= '>'.$name.'</option>';
            }

            echo '<!--begin::Modal - mkil - Add-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="edit-feedback-generate">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::modal-content-->
                    <div class="modal-content">
                        <!--begin::modal header-->
                        <div class="modal-header">
                            <h3 class="modal-title">Kemaskini Surat Maklum Balas Kelulusan Izin Lalu</h3>

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
                            <div class="stepper stepper-links" id="kt_stepper_mbkil_edit">
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
                                </div>
                                <!--end::Nav-->
                                <!--begin::Form-->
                                <form class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" id="form-mbkil-edit">

                                    <!--begin::Scroll-->
                                    <div class="hover-scroll h-300px px-5 mb-10">
                                        <!--begin::Group-->
                                        <div class="mb-5">
                                            <!--begin::Step 1-->
                                            <div class="flex-column current" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Penyedia Utiliti</span>
                                                                <span class="fs-6 text-muted">'. $row['ProviderName'] .'</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[1];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_provider" value="'.$row['UpProvider'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 1-->

                                            <!--begin::Step 2-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Nama Syarikat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['CompanyName'] . '</span>';
                                                                }

                                                                echo '
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_pemohon" value="'.$row['UpClient'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--begin::Step 2-->

                                            <!--begin::Step 3-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Tajuk Projek</span>
                                                                <span class="fs-6 text-muted">' . $row['ProjectTitle'] . '</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::table-->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td class="form-label">NAMA JALAN</td>
                                                                <td class="form-label">PBM / PBT</td>
                                                                
                                                            </tr>
                                                            ';
                                                            $road = Wayleave::getRoad($row['SysID']);
                                                            foreach ($road as $row4) {
                                                                $data = explode(",", $row4['RoadId']);
                                                                echo '<tr>';
                                                                    echo '<td class="fs-6 text-muted">';
                                                                    echo '<table class="table table-row-bordered table-row">'; 
                                                                    // Start the nested table
                                                                    foreach ($data as $rTrim) {
                                                                        $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                                        $road_name = Wayleave::getRoadName($roadIdTrim);
                                                                        echo '<tr>';
                                                                        foreach ($road_name as $rData) {
                                                                            echo '<td class="fs-6 text-muted text-center">' . $rData['RoadName'] . '</td>';
                                                                        }
                                                                        echo '</tr>';
                                                                    }
                                                                    echo '</table>'; 
                                                                    // End the nested table
                                                                    echo '</td>';
                                                                    echo '<td class="fs-6 text-muted text-center">' . $row4['AuthorityName'] . '</td>';
                                                                    // echo '<td class="fs-6 text-muted text-center"></td>';
                                                                echo '</tr>';

                                                                echo '<input type="text" name="entry_list_road_id" value="'.$row4['RoadId'].'" hidden>';

                                                            }
                                                            echo '
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::table-->
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
                                                    <input class="form-control form-control-lg form-control-solid" name="inv_no" value="'.$row['InvNo'].'">
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
                                                        <input id="inv-date" name="inv_date" class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh"/>
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
                                                                <span class="form-label">Tarikh</span>';
                                                                if($row['StatusID'] == 47) {
                                                                    echo '<span class="fs-6 text-muted">'. General::convertDate($row['LetterDate']) .'</span>';
                                                                } else {
                                                                    echo '<span class="fs-6 text-muted">'. $letter_date .'</span>';
                                                                }
                                                            echo'
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
                                                    <input class="form-control form-control-lg form-control-solid" name="date_hijri" value="' . $hijri_date .'">
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
                                                                <span class="fs-6 text-muted">'.$row['LetterRefNo'].'</span>
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
                                                    <select id="selection-staff" class="form-control form-control-lg form-control-solid" name="staff_name" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true">'. $option .'</select>
                                                    
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
                                                    <input id="staff_contact" class="form-control form-control-lg form-control-solid" name="staff_contact" readonly value="'.$row['StaffContact'].'">
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
                                                    <select id="selection-approval" class="form-control form-control-lg form-control-solid" name="approval_by" data-placeholder="Sila Pilih Pegawai Melulus" data-allow-clear="true">'. $option3 .'</select>
                                                    
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
                                                    <input id="approval_position" class="form-control form-control-lg form-control-solid" name="approval_position" readonly value="'.$row['StaffApprovalPosition'].'">                                                       
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 5-->
                                        </div>
                                        <!--end::Group-->
                                    </div>
                                    <!--end::Scroll-->

                                    <input type="text" name="system_id"  value="'. $row['SysID'] .'" hidden>
                                    <input type="text" name="item" value="generate-wyFeedback-edit" hidden>
                                    <input type="text" name="inv_date" value="'.$generateDate.'" hidden>
                                    <input type="text" name="letter_date" value="'.$current_date->format('Y-m-d').'" hidden>
                                    <input type="text" name="letter_ref_no" value="'.$row['LetterRefNo'].'" hidden>
                                    <input type="text" name="item_42_edit" value="wyFeedback-edit-kiter" hidden>
                                    <input type="text" name="wy_feedback_id" value="'.$row['WyFeedbackId'].'" hidden>
                                    <input type="text" name="status" value="'.$row['StatusID'].'" hidden>

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
                                            <button type="submit" id="wyFeedback-edit-submit" class="btn btn-primary" data-kt-stepper-action="submit">
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
            </div><!--end::Modal - mkil - Add-->';
        }
    }

} else if($appsTitle == 'KUDRAT') {
    if($letterWyFeedback_id == 4) {
        $wy_feedback_kil = Wayleave::wyFeedbackModelAmend($_GET['sid']); //for amend based on status = 4 (tak sah)
    } else {
        $wy_feedback_kil = Wayleave::wyFeedbackModelEdit($sysID,$letterWyFeedback_id); //for edit based on letterWyFeedback_id
    }

    foreach ($wy_feedback_kil as $row) {
        if($row['StatusID'] == 51 || $row['StatusID'] == 47) {
            $datepickerId = 'datepicker-' . $row['StatusID']. $row['ID'];
            $selectedValue = $row['StaffApproval'];
            $selectedName = $row['StaffName'];

            foreach (Wayleave::assignStaff(1) as $data2) {
                if($data2['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data2['ProfilePic'];
                };
                $name = $data2['FirstName'].' '.$data2['LastName'];
                $id = $data2['username'];
                $contact = $data2['StaffPhoneNo'];
            
                $option .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'"';
                if ($id == $selectedName) {
                    $option .= ' selected';
                }
                $option .= '>'.$name.'</option>';

            }

            foreach (Wayleave::assignStaff(2) as $data) {
                if($data['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data['ProfilePic'];
                };
                $name = $data['FirstName'].' '.$data['LastName'];
                $id = $data['username'];
                $contact = $data['StaffPhoneNo'];
                $position = $data['Position'];
            
                $option3 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-position="'.$position.'"';
                if ($id == $selectedValue) {
                    $option3 .= ' selected';
                }
                $option3 .= '>'.$name.'</option>';
            }

            echo '<!--begin::Modal - mkil - Add-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="edit-feedback-generate">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::modal-content-->
                    <div class="modal-content">
                        <!--begin::modal header-->
                        <div class="modal-header">
                            <h3 class="modal-title">Kemaskini Surat Maklum Balas Kelulusan Izin Lalu</h3>

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
                            <div class="stepper stepper-links" id="kt_stepper_mbkil_edit">
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
                                </div>
                                <!--end::Nav-->
                                <!--begin::Form-->
                                <form class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" id="form-mbkil-edit">

                                    <!--begin::Scroll-->
                                    <div class="hover-scroll h-300px px-5 mb-10">
                                        <!--begin::Group-->
                                        <div class="mb-5">
                                            <!--begin::Step 1-->
                                            <div class="flex-column current" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Penyedia Utiliti</span>
                                                                <span class="fs-6 text-muted">'. $row['ProviderName'] .'</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[1];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_provider" value="'.$row['UpProvider'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 1-->

                                            <!--begin::Step 2-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Nama Syarikat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['CompanyName'] . '</span>';
                                                                }

                                                                echo '
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_pemohon" value="'.$row['UpClient'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--begin::Step 2-->

                                            <!--begin::Step 3-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Tajuk Projek</span>
                                                                <span class="fs-6 text-muted">' . $row['ProjectTitle'] . '</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::table-->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td class="form-label">NAMA JALAN</td>
                                                                <td class="form-label">PBM / PBT</td>
                                                                
                                                            </tr>
                                                            ';
                                                            $road = Wayleave::getRoad($row['SysID']);
                                                            foreach ($road as $row4) {
                                                                $data = explode(",", $row4['RoadId']);
                                                                echo '<tr>';
                                                                    echo '<td class="fs-6 text-muted">';
                                                                    echo '<table class="table table-row-bordered table-row">'; 
                                                                    // Start the nested table
                                                                    foreach ($data as $rTrim) {
                                                                        $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                                        $road_name = Wayleave::getRoadName($roadIdTrim);
                                                                        echo '<tr>';
                                                                        foreach ($road_name as $rData) {
                                                                            echo '<td class="fs-6 text-muted text-center">' . $rData['RoadName'] . '</td>';
                                                                        }
                                                                        echo '</tr>';
                                                                    }
                                                                    echo '</table>'; 
                                                                    // End the nested table
                                                                    echo '</td>';
                                                                    echo '<td class="fs-6 text-muted text-center">' . $row4['AuthorityName'] . '</td>';
                                                                    // echo '<td class="fs-6 text-muted text-center"></td>';
                                                                echo '</tr>';

                                                                echo '<input type="text" name="entry_list_road_id" value="'.$row4['RoadId'].'" hidden>';

                                                            }
                                                            echo '
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::table-->';

                                                // $total_authority = Wayleave::getRoad($row['SysID']);

                                                // // Define an array to store the input values
                                                // // $inputValues = array();

                                                // foreach ($total_authority as $data) {
                                                //     $authorityId = $data['AuthorityId'];

                                                //     echo'
                                                //     <!--begin::Input group-->
                                                //     <div class="mb-5 fv-row fv-plugins-icon-container">
                                                //         <!--begin::Label-->
                                                //         <label class="form-label required">Jumlah Bayaran bagi '. $data['AuthorityName'] .'</label>
                                                //         <!--end::Label-->
                                                //         <!--begin::Input-->
                                                //         <textarea class="form-control form-control-lg form-control-solid" rows="4" name="jumlah" placeholder="Cth: (Deposit Keselamatan 100% Dalam Bentuk Jaminan Bank)"></textarea>
                                                //         <!--end::Input-->
                                                //         <div class="fv-plugins-message-container invalid-feedback"></div>
                                                //     </div>
                                                //     <!--end::Input group-->';

                                                //     // Store the input value in the array
                                                //     // $inputValues[$authorityId] = $_POST['jumlah'][$authorityId] ?? '';
                                                // }
                                                // Use $inputValues array to access the stored values
                                                // print_r($inputValues);

                                                echo'
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
                                                    <input class="form-control form-control-lg form-control-solid" name="inv_no" value="'.$row['InvNo'].'">
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
                                                        <input id="inv-date" name="inv_date" class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh"/>
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
                                                                <span class="form-label">Tarikh</span>';
                                                                if($row['StatusID'] == 47) {
                                                                    echo '<span class="fs-6 text-muted">'. General::convertDate($row['LetterDate']) .'</span>';
                                                                } else {
                                                                    echo '<span class="fs-6 text-muted">'. $letter_date .'</span>';
                                                                }
                                                            echo'
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
                                                    <input class="form-control form-control-lg form-control-solid" name="date_hijri" value="' . $hijri_date .'">
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
                                                                <span class="fs-6 text-muted">'.$row['LetterRefNo'].'</span>
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
                                                    <select id="selection-staff" class="form-control form-control-lg form-control-solid" name="staff_name" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true">'. $option .'</select>
                                                    
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
                                                    <input id="staff_contact" class="form-control form-control-lg form-control-solid" name="staff_contact" readonly value="'.$row['StaffContact'].'">
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
                                                    <select id="selection-approval" class="form-control form-control-lg form-control-solid" name="approval_by" data-placeholder="Sila Pilih Pegawai Melulus" data-allow-clear="true">'. $option3 .'</select>
                                                    
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
                                                    <input id="approval_position" class="form-control form-control-lg form-control-solid" name="approval_position" readonly value="'.$row['StaffApprovalPosition'].'">                                                       
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 5-->
                                        </div>
                                        <!--end::Group-->
                                    </div>
                                    <!--end::Scroll-->

                                    <input type="text" name="system_id"  value="'. $row['SysID'] .'" hidden>
                                    <input type="text" name="item" value="generate-wyFeedback-edit" hidden>
                                    <input type="text" name="inv_date" value="'.$generateDate.'" hidden>
                                    <input type="text" name="letter_date" value="'.$current_date->format('Y-m-d').'" hidden>
                                    <input type="text" name="letter_ref_no" value="'.$row['LetterRefNo'].'" hidden>
                                    <input type="text" name="item_42_edit" value="wyFeedback-edit-kudr" hidden>
                                    <input type="text" name="wy_feedback_id" value="'.$row['WyFeedbackId'].'" hidden>
                                    <input type="text" name="status" value="'.$row['StatusID'].'" hidden>

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
                                            <button type="submit" id="wyFeedback-edit-submit" class="btn btn-primary" data-kt-stepper-action="submit">
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
            </div><!--end::Modal - mkil - Add-->';
        }
    }

} else if($appsTitle == 'KUK') {
    if($letterWyFeedback_id == 4) {
        $wy_feedback_kil = Wayleave::wyFeedbackModelAmend($_GET['sid']); //for amend based on status = 4 (tak sah)
    } else {
        $wy_feedback_kil = Wayleave::wyFeedbackModelEdit($sysID,$letterWyFeedback_id); //for edit based on letterWyFeedback_id
    }

    foreach ($wy_feedback_kil as $row) {
        if($row['StatusID'] == 51 || $row['StatusID'] == 47) {
            $datepickerId = 'datepicker-' . $row['StatusID']. $row['ID'];
            $selectedValue = $row['StaffApproval'];
            $selectedName = $row['StaffName'];

            foreach (Wayleave::assignStaff(1) as $data2) {
                if($data2['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data2['ProfilePic'];
                };
                $name = $data2['FirstName'].' '.$data2['LastName'];
                $id = $data2['username'];
                $contact = $data2['StaffPhoneNo'];
            
                $option .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-contact="'.$contact.'"';
                if ($id == $selectedName) {
                    $option .= ' selected';
                }
                $option .= '>'.$name.'</option>';

            }

            foreach (Wayleave::assignStaff(2) as $data) {
                if($data['ProfilePic'] == null){
                    $img = "blank";
                }
                else{
                    $img = $data['ProfilePic'];
                };
                $name = $data['FirstName'].' '.$data['LastName'];
                $id = $data['username'];
                $contact = $data['StaffPhoneNo'];
                $position = $data['Position'];
            
                $option3 .= '<option value="'.$id.'" data-staff="'.General::getProfile($img).'.jpg" data-position="'.$position.'"';
                if ($id == $selectedValue) {
                    $option3 .= ' selected';
                }
                $option3 .= '>'.$name.'</option>';
            }

            echo '<!--begin::Modal - mkil - Add-->
            <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="edit-feedback-generate">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <!--begin::modal-content-->
                    <div class="modal-content">
                        <!--begin::modal header-->
                        <div class="modal-header">
                            <h3 class="modal-title">Kemaskini Surat Maklum Balas Kelulusan Izin Lalu</h3>

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
                            <div class="stepper stepper-links" id="kt_stepper_mbkil_edit">
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
                                </div>
                                <!--end::Nav-->
                                <!--begin::Form-->
                                <form class="form fv-plugins-bootstrap5 fv-plugins-framework" novalidate="novalidate" id="form-mbkil-edit">

                                    <!--begin::Scroll-->
                                    <div class="hover-scroll h-300px px-5 mb-10">
                                        <!--begin::Group-->
                                        <div class="mb-5">
                                            <!--begin::Step 1-->
                                            <div class="flex-column current" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Penyedia Utiliti</span>
                                                                <span class="fs-6 text-muted">'. $row['ProviderName'] .'</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[1];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_provider" value="'.$row['UpProvider'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 1-->

                                            <!--begin::Step 2-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Nama Syarikat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['CompanyName'] . '</span>';
                                                                }

                                                                echo '
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Alamat</span>
                                                                ';

                                                                $data = explode(",", $row['ContactId']);
                                                                $firstElement = $data[0];
                                                                $contactId = trim($firstElement, '{}'); // Remove curly braces
                                                                $contact = Wayleave::getContacts($contactId);

                                                                foreach ($contact as $row3) {
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Address1'] . ', ' . $row3['Address2'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['Postcode'] . $row3['City'] . ', ' . '</span>';
                                                                    echo '<span class="fs-6 text-muted">' . $row3['State'] . '.' . '</span>';
                                                                }

                                                                echo '
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
                                                    <label class="form-label required">Untuk Perhatian<i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" aria-label="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-bs-original-title="Merujuk kepada Untuk perhatian surat cth: (U.P.: Ahmad Hafiz)" data-kt-initialized="1"></i></label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-lg form-control-solid" name="up_pemohon" value="'.$row['UpClient'].'">
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--begin::Step 2-->

                                            <!--begin::Step 3-->
                                            <div class="flex-column" data-kt-stepper-element="content">
                                                <!--begin::Input group-->
                                                <div class="mb-5 fv-row fv-plugins-icon-container">
                                                    <label class="d-flex flex-stack mb-5">
                                                        <!--begin:Label-->
                                                        <span class="d-flex align-items-center me-2">
                                                            <!--begin::Description-->
                                                            <span class="d-flex flex-column">
                                                                <span class="form-label">Tajuk Projek</span>
                                                                <span class="fs-6 text-muted">' . $row['ProjectTitle'] . '</span>
                                                            </span>
                                                            <!--end:Description-->
                                                        </span>
                                                        <!--end:Label-->
                                                    </label>
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::table-->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td class="form-label">NAMA JALAN</td>
                                                                <td class="form-label">PBM / PBT</td>
                                                                
                                                            </tr>
                                                            ';
                                                            $road = Wayleave::getRoad($row['SysID']);
                                                            foreach ($road as $row4) {
                                                                $data = explode(",", $row4['RoadId']);
                                                                echo '<tr>';
                                                                    echo '<td class="fs-6 text-muted">';
                                                                    echo '<table class="table table-row-bordered table-row">'; 
                                                                    // Start the nested table
                                                                    foreach ($data as $rTrim) {
                                                                        $roadIdTrim = trim($rTrim, '{}'); // Remove curly braces
                                                                        $road_name = Wayleave::getRoadName($roadIdTrim);
                                                                        echo '<tr>';
                                                                        foreach ($road_name as $rData) {
                                                                            echo '<td class="fs-6 text-muted text-center">' . $rData['RoadName'] . '</td>';
                                                                        }
                                                                        echo '</tr>';
                                                                    }
                                                                    echo '</table>'; 
                                                                    // End the nested table
                                                                    echo '</td>';
                                                                    echo '<td class="fs-6 text-muted text-center">' . $row4['AuthorityName'] . '</td>';
                                                                    // echo '<td class="fs-6 text-muted text-center"></td>';
                                                                echo '</tr>';

                                                                echo '<input type="text" name="entry_list_road_id" value="'.$row4['RoadId'].'" hidden>';

                                                            }
                                                            echo '
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::table-->';

                                                // $total_authority = Wayleave::getRoad($row['SysID']);

                                                // // Define an array to store the input values
                                                // // $inputValues = array();

                                                // foreach ($total_authority as $data) {
                                                //     $authorityId = $data['AuthorityId'];

                                                //     echo'
                                                //     <!--begin::Input group-->
                                                //     <div class="mb-5 fv-row fv-plugins-icon-container">
                                                //         <!--begin::Label-->
                                                //         <label class="form-label required">Jumlah Bayaran bagi '. $data['AuthorityName'] .'</label>
                                                //         <!--end::Label-->
                                                //         <!--begin::Input-->
                                                //         <textarea class="form-control form-control-lg form-control-solid" rows="4" name="jumlah" placeholder="Cth: (Deposit Keselamatan 100% Dalam Bentuk Jaminan Bank)"></textarea>
                                                //         <!--end::Input-->
                                                //         <div class="fv-plugins-message-container invalid-feedback"></div>
                                                //     </div>
                                                //     <!--end::Input group-->';

                                                //     // Store the input value in the array
                                                //     // $inputValues[$authorityId] = $_POST['jumlah'][$authorityId] ?? '';
                                                // }
                                                // Use $inputValues array to access the stored values
                                                // print_r($inputValues);

                                                echo'
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
                                                    <input class="form-control form-control-lg form-control-solid" name="inv_no" value="'.$row['InvNo'].'">
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
                                                        <input id="inv-date" name="inv_date" class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh"/>
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
                                                                <span class="form-label">Tarikh</span>';
                                                                if($row['StatusID'] == 47) {
                                                                    echo '<span class="fs-6 text-muted">'. General::convertDate($row['LetterDate']) .'</span>';
                                                                } else {
                                                                    echo '<span class="fs-6 text-muted">'. $letter_date .'</span>';
                                                                }
                                                            echo'
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
                                                    <input class="form-control form-control-lg form-control-solid" name="date_hijri" value="' . $hijri_date .'">
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
                                                                <span class="fs-6 text-muted">'.$row['LetterRefNo'].'</span>
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
                                                    <select id="selection-staff" class="form-control form-control-lg form-control-solid" name="staff_name" data-placeholder="Sila Pilih Pegawai" data-allow-clear="true">'. $option .'</select>
                                                    
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
                                                    <input id="staff_contact" class="form-control form-control-lg form-control-solid" name="staff_contact" readonly value="'.$row['StaffContact'].'">
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
                                                    <select id="selection-approval" class="form-control form-control-lg form-control-solid" name="approval_by" data-placeholder="Sila Pilih Pegawai Melulus" data-allow-clear="true">'. $option3 .'</select>
                                                    
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
                                                    <input id="approval_position" class="form-control form-control-lg form-control-solid" name="approval_position" readonly value="'.$row['StaffApprovalPosition'].'">                                                       
                                                    <!--end::Input-->
                                                    <div class="fv-plugins-message-container invalid-feedback"></div>
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Step 5-->
                                        </div>
                                        <!--end::Group-->
                                    </div>
                                    <!--end::Scroll-->

                                    <input type="text" name="system_id"  value="'. $row['SysID'] .'" hidden>
                                    <input type="text" name="item" value="generate-wyFeedback-edit" hidden>
                                    <input type="text" name="inv_date" value="'.$generateDate.'" hidden>
                                    <input type="text" name="letter_date" value="'.$current_date->format('Y-m-d').'" hidden>
                                    <input type="text" name="letter_ref_no" value="'.$row['LetterRefNo'].'" hidden>
                                    <input type="text" name="item_42_edit" value="wyFeedback-edit-kudr" hidden>
                                    <input type="text" name="wy_feedback_id" value="'.$row['WyFeedbackId'].'" hidden>
                                    <input type="text" name="status" value="'.$row['StatusID'].'" hidden>

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
                                            <button type="submit" id="wyFeedback-edit-submit" class="btn btn-primary" data-kt-stepper-action="submit">
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
            </div><!--end::Modal - mkil - Add-->';
        }
    }

}

?>