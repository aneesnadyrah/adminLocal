<?php

$approvalName = '';
$jawatanBP = '';

foreach (Tasking::assignSelect(1) as $row) {

    $name = $row['FirstName'];
    $id = $row['username'];
    $position = $row['Position'];
    $approvalName .= '<option value="'.$name.'" data-position="'.$position.'">'.$name.'</option>';
    $jawatanBP .= '<option value="'.$position.'">'.$position.'</option>';
}

$staffContact1 = '';
$staffContact2 = '';

foreach (Tasking::assignSelect(1) as $row) {

    $name = $row['FirstName'];
    $id = $row['username'];
    $contact = $row['StaffPhoneNo'];

    $staffContact1 .= '<option value="'.$name.'" data-contact="'.$contact.'">'.$name.'</option>';
    $staffContact2 .= '<option value="'.$name.'" data-contact="'.$contact.'">'.$name.'</option>';
}

$systemId = isset($_GET['sid']) ? $_GET['sid'] : '';

foreach (Permitting::authorityModal() as $AIDRow) {



    if ($AIDRow['authority_status'] == '041') {

        // var_dump(workMethodRP($AIDRow['system_id'],$AIDRow['authority_id']));

        // $detail =  letterModal($AIDRow['SysID'])[0];
        // $details = selectCalendar($AIDRow['SysID']);
        $authorityInfo  = selectAuthority($AIDRow['system_id'],$AIDRow['authority_id'])[0];
        // var_dump($authorityInfo);
        // $authorityNames = [];
        // $authorityAddresss = [];
        // $authorityPICs = [];


        $authorityName =  $authorityInfo['AuthorityName'];

        $authorityAddress =  $authorityInfo['AuthorityAddress1'].' '.$authorityInfo['AuthorityAddress2'].', '.$authorityInfo['AuthorityPostcode'].', '.$authorityInfo['DistrictName'].', '.$authorityInfo['AuthorityState'];

        $authorityPIC =  $authorityInfo['AuthorityPIC'];

        // $authorityNames[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authorityName.'</div></div>';

        // $authorityAddresss[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authorityAddress.'</div></div>';

        // $authorityPICs[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authorityPIC.'</div></div>';



        $reportInfo = Permitting::reportInfo($AIDRow['system_id'], $authorityInfo['AuthorityID']);
        // print_r($reportInfo);


        $roadInvolved = Permitting::getRoadInvolved($AIDRow['system_id'],$AIDRow['authority_id']);

        // print_r($roadInvolved);

        // $roadIds = [];
        $roadNames = [];
        $workMethods = [];


        foreach($roadInvolved as $rowing){

            // $roadId = $rowing['EntryRoadID'];
            // print_r($roadId.', ');
            // $workMethodCode = $rowing['method'];

            //kena da [0] sebab amik sekali dengan workmethod, setiap jalan ada banyak workMethod
            $roadName = $rowing['road_name'];
            // $workMethod = $rowing['name'];

            // $roadIds[] = $roadId;

            $roadNames[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$roadName.'</div></div>';

            // $workMethods[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$workMethod.'</div></div>';



        // foreach($rowing['method'] as $rowingz){

        //     // $roadId = $rowing['EntryRoadID'];
        //     // print_r($roadId.', ');
        //     $workMethodCode = $rowingz['method'];

        //     //kena da [0] sebab amik sekali dengan workmethod, setiap jalan ada banyak workMethod
        //     // $roadName = $rowing['name'];
        //     $workMethod = $rowingz['name'];

        //     // $roadIds[] = $roadId;

        //     // $roadNames[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$roadName.'</div></div>';

        //     $workMethods[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$workMethod.'</div></div>';

        // }
    }

    $methodStateApproval = [];
    $methodNormal = [];



    //data work method from RP to display in letter
    foreach(Permitting::workMethodRP($AIDRow['system_id'],$AIDRow['authority_id']) as $item){
        if($item['method'] === 'CW'){
            $methodStateApproval[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].'</div></div>';
        } else if(!empty($item['road_crossing'])){
            if($item['method'] === 'OH'){
                $methodStateApproval[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].' Merentangi Jalan'.'</div></div>';
            } else {
                $methodNormal[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].' Merentangi Jalan'.'</div></div>';
            }
        } else {
            $methodNormal[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].'</div></div>';
        }
    }

        $workMethods = array_merge($methodNormal,$methodStateApproval);

        // $authorityNamesString = implode('', $authorityNames);
        // $districtNamesString = implode('', $authorityAddresss);
        // $uniqueauthorityPICs = array_unique($authorityPICs);
        // $authorityPICString = implode('', $uniqueauthorityPICs);

        // $uniqueroadIds = array_unique($roadIds);

        $uniqueroadNames = array_values(array_unique($roadNames));
        $roadNamesString = implode('', $uniqueroadNames);

        $uniqueworkMethods = array_values(array_unique($workMethods));
        $workMethodsString = implode('', $uniqueworkMethods);

        // print_r($uniqueroadIds);
        // print_r($roadNames);
        // print_r($uniqueroadNames);
        // print_r($authorityAddresss);

        $projectTitle = Permitting::titleLetter($AIDRow['system_id']);

        // print_r($projectTitle);

        date_default_timezone_set('Asia/Kuala_Lumpur');
        $current_date = new DateTime();
        $generateDate = $current_date->format('Y-m-d H:i:s');
        $letter_date = General::convertDate($generateDate); // format the date as desired

        //Modal Jana Surat PIL
        echo '
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" role="dialog" id="action-'. $AIDRow['authority_status']. $AIDRow['id'] .'">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-800px">

                <!--begin::Modal content-->
                <div class="modal-content rounded">

                    <!--begin::Modal header-->
                    <div class="modal-header mb-8">
                        <h3 class="modal-title">Maklumat Surat Permohonan Izin Lalu</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close" >
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--end::Modal header-->

                    <!--begin::Modal body-->
                    <div class="modal-body px-10 px-lg-10 pt-0 pb-5">

                        <!--begin::Stepper-->
                        <div class="stepper stepper-pills stepper-column d-flex flex-column flex-lg-row" id="kt_stepper_generate_letter_PIL_'. $AIDRow['authority_status']. $AIDRow['id'] .'">
                            <!--begin::Nav-->

                            <div class="d-flex flex-row-auto w-100 w-250px  py-lg-5">
                                <div class="stepper-nav mb-5">

                                    <!--begin::Step 1-->
                                    <div class="stepper-item mx-4 my-4 current" data-kt-stepper-element="nav">
                                        <!--begin::Wrapper-->
                                        <div class="stepper-wrapper d-flex align-items-center">

                                            <!--begin::Icon-->
                                            <div class="stepper-icon w-40px h-40px">
                                                <i class="stepper-check fas fa-check"></i>
                                                <span class="stepper-number">1</span>
                                            </div>
                                            <!--end::Icon-->

                                            <!--begin::Label-->
                                            <div class="stepper-label">
                                                <h3 class="stepper-title">
                                                    Penerima
                                                </h3>
                                                <div class="stepper-desc">
                                                    Maklumat Penerima
                                                </div>
                                            </div>
                                            <!--end::Label-->

                                        </div>
                                        <!--end::Wrapper-->

                                        <!--begin::Line-->
                                        <div class="stepper-line h-40px"></div>
                                        <!--end::Line-->

                                    </div>
                                    <!--end::Step 1-->

                                    <!--begin::Step 2-->
                                    <div class="stepper-item mx-4 my-4" data-kt-stepper-element="nav">
                                        <!--begin::Wrapper-->
                                        <div class="stepper-wrapper d-flex align-items-center">
                                            <!--begin::Icon-->
                                            <div class="stepper-icon w-40px h-40px">
                                                <i class="stepper-check fas fa-check"></i>
                                                <span class="stepper-number">2</span>
                                            </div>
                                            <!--begin::Icon-->

                                            <!--begin::Label-->
                                            <div class="stepper-label">
                                                <h3 class="stepper-title">
                                                    Maklumat Surat
                                                </h3>

                                                <div class="stepper-desc">
                                                    Maklumat kepada Penerima
                                                </div>
                                            </div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Wrapper-->

                                        <!--begin::Line-->
                                        <div class="stepper-line h-40px"></div>
                                        <!--end::Line-->

                                    </div>
                                    <!--end::Step 2-->

                                    <!--begin::Step 3-->
                                    <div class="stepper-item mx-4 my-4" data-kt-stepper-element="nav">
                                        <!--begin::Wrapper-->
                                        <div class="stepper-wrapper d-flex align-items-center">
                                            <!--begin::Icon-->
                                            <div class="stepper-icon w-40px h-40px">
                                                <i class="stepper-check fas fa-check"></i>
                                                <span class="stepper-number">3</span>
                                            </div>
                                            <!--begin::Icon-->

                                            <!--begin::Label-->
                                            <div class="stepper-label">
                                                <h3 class="stepper-title">
                                                    Pengirim
                                                </h3>

                                                <div class="stepper-desc">
                                                    Maklumat Pengirim
                                                </div>
                                            </div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Step 3-->

                                </div>
                            </div>
                            <!--end::Nav-->
                            <div class="d-flex flex-row-fluid py-lg-5">
                                <!--begin::Form-->
                                <form class="form w-lg-500px mx-auto" novalidate="novalidate" id="form-'. $AIDRow['authority_status']. $AIDRow['id'] .'"  action="components/views/letter-reviews-PIL.php" method="POST">

                                    <!--begin::Group-->
                                    <div class="mb-10">

                                        <!--begin::Step 1-->
                                        <div class="flex-column current" data-kt-stepper-element="content">
                                            <div class="d-flex flex-row align-items-center mb-4 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">PBM/PBT</label>
                                                <div class="col-8">
                                                    <span id="authority-name-'. $AIDRow['authority_status']. $AIDRow['id'] .'" type="text" class="fw-bold fs-6 text-gray-800" name="authority-name"><div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authorityName.'</div></div></span>
                                                </div>
                                            </div>

                                            <input type="hidden" name="authority-id" id="aid-'. $AIDRow['authority_status']. $AIDRow['id'] .'"   value="'. $authorityInfo['AuthorityID'] .'" >

                                            <!--begin::Input group-->
                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">Alamat PBM/PBT</label>
                                                <div class="col-8">
                                                    <span type="text" name="authority-address" class="fw-bold fs-6 text-gray-800">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <span class="col-2 bullet bullet-vertical bg-primary me-5"></span>
                                                            <div class="col-10">'.$authorityAddress.'</div>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold">Untuk Perhatian</label>
                                                <div class="col-8">
                                                    <span id="authority-attention-'. $AIDRow['authority_status']. $AIDRow['id'] .'" type="text" class="fw-bold fs-6 text-gray-800" ><div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authorityPIC.'</div></div></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Step 1-->

                                        <!--begin::Step 2-->
                                        <div class="flex-column" data-kt-stepper-element="content">

                                            <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">No Rujukan Surat</label>
                                                <div class="col-8">
                                                    <input type="text" id="letter-no-'. $AIDRow['authority_status'] . $AIDRow['id'] .'" name="letter-no" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="" readonly/>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">Tarikh Surat</label>
                                                <div class="col-8">
                                                    <input type="text" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" placeholder="'. $letter_date .'" name="date-generated" value="'. $letter_date .'" readonly/>
                                                </div>
                                            </div>

                                            <input type="hidden" name="today-date"  value="'. $generateDate .'" >

                                            <div class=" d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">Tajuk Projek</label>
                                                <div class="col-8">
                                                    <span id="project-title-'. $AIDRow['authority_status']. $AIDRow['id'] .'" type="text" name="project-title" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" style="text-align: justify;">'.$projectTitle[0]['ProjectTitle'].'</span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">Penyedia Utiliti</label>
                                                <div class="col-8">
                                                    <input  type="text" name="provider-name" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="'. $AIDRow['provider_name'] .'" readonly/>
                                                </div>
                                            </div>

                                            <input  type="hidden" name="provider-id" value="'. $AIDRow['provider_id'] .'" />

                                            <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted">Jarak Projek</label>
                                                <div class="col-8">
                                                    <input  type="text" name="distance" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="'. $AIDRow['involved_appl_length'] .' meter" readonly/>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-start fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted pt-4">Nama Jalan Terlibat</label>
                                                <div class="col-8">
                                                    <span  type="text" name="road-name" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="">'. ($roadNamesString ?? '') .'</span>
                                                </div>
                                            </div>

                                            <input  type="hidden" name="road-id" value="" />

                                            <div class="d-flex flex-row align-items-start mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold text-muted pt-4">Kaedah K.Pemasangan</label>
                                                <div class="col-8">
                                                    <span  type="text" name="work-method" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="">'. ($workMethodsString ?? '') .'</span>
                                                </div>
                                            </div>

                                            <input  type="hidden" name="work-method" value="" />

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold form-label required">Tarikh Jangka Mula</label>
                                                <div class="col-8 ps-3">
                                                    <input id="date-start-'. $AIDRow['authority_status']. $AIDRow['id'] .'" name="date-start" class="form-control fw-bold fs-6 text-gray-800" placeholder="Sila Pilih Tarikh" value="" />
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold form-label required">Tarikh Jangka Siap</label>
                                                <div class="col-8 ps-3">
                                                    <input id="date-finish-'. $AIDRow['authority_status']. $AIDRow['id'] .'" type="date" name="date-finish" class="form-control fw-bold fs-6 text-gray-800" placeholder="Sila Pilih Tarikh" value="" />
                                                </div>
                                            </div>

                                        </div>
                                        <!--end::Step 2-->

                                        <!--begin::Step 3-->
                                        <div class="flex-column" data-kt-stepper-element="content">

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold required">Pegawai Berhubung 1</label>
                                                <div class="col-8 ps-3">
                                                    <select id="officer-name1-'. $AIDRow['authority_status']. $AIDRow['id'] .'" class="form-select fw-bold fs-6 text-gray-800" data-control="select2" name="officer-name1" data-allow-clear="true" data-dropdown-parent="#kt_stepper_generate_letter_PIL_'. $AIDRow['authority_status']. $AIDRow['id'] .'" data-hide-search="true" data-placeholder="Sila Pilih Pegawai"><option><option>'. $staffContact1 .'</select>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold">No Tel P.Berhubung 1</label>
                                                <div class="col-8 ps-3">
                                                    <input id="officer-contact1-'. $AIDRow['authority_status']. $AIDRow['id'] .'" class="form-control form-control-solid fw-bold fs-6 text-gray-800" name="officer-contact1" value="" placeholder="" readonly/>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold required">Pegawai Berhubung 2</label>
                                                <div class="col-8 ps-3">
                                                    <select id="officer-name2-'. $AIDRow['authority_status']. $AIDRow['id'] .'" class="form-select fw-bold fs-6 text-gray-800" data-control="select2" name="officer-name2" data-allow-clear="true"data-hide-search="true" data-dropdown-parent="#kt_stepper_generate_letter_PIL_'. $AIDRow['authority_status']. $AIDRow['id'] .'" data-placeholder="Sila Pilih Pegawai"><option><option>'. $staffContact2 .'</select>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold">No Tel P.Berhubung 2</label>
                                                <div class="col-8 ps-3">
                                                    <input id="officer-contact2-'. $AIDRow['authority_status']. $AIDRow['id'] .'" class="form-control form-control-solid fw-bold fs-6 text-gray-800" name="officer-contact2" value="" placeholder="" readonly/>
                                                </div>
                                            </div>

                                            <div class=" d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold required">Tanda Tangan Pegawai</label>
                                                <div class="col-8 ps-3">
                                                    <select id="approval-name-'. $AIDRow['authority_status']. $AIDRow['id'] .'" class="form-select fw-bold fs-6 text-gray-800" name="approval-name" data-allow-clear="true" data-control="select2" data-dropdown-parent="#kt_stepper_generate_letter_PIL_'. $AIDRow['authority_status']. $AIDRow['id'] .'" data-hide-search="true" data-placeholder="Sila Pilih Pegawai"><option><option>'. $approvalName .'</select>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
                                                <label class="col-4 fw-semibold">Jawatan Pegawai</label>
                                                <div class="col-8 ps-3">
                                                    <input id="approval-position-'. $AIDRow['authority_status']. $AIDRow['id'] .'" class="form-control form-control-solid fw-bold fs-6 text-gray-800" name="approval-position" value="" placeholder="" readonly/>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column mb-5 fv-row fv-plugins-icon-container">
                                                <div class="form-check form-switch form-check-custom form-check-solid d-flex justify-content-between">
                                                    <label class="fw-semibold text-muted">
                                                        Sila tanda jika tandatangan bagi pihak pegawai melulus
                                                    </label>
                                                    <input class="form-check-input" type="checkbox" value="true" id="tanda-bagiPihak-'. $AIDRow['authority_status']. $AIDRow['id'] .'" name="bagi-pihak" />
                                                </div>
                                            </div>

                                            <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container d-none" id="bp-position-'. $AIDRow['authority_status']. $AIDRow['id'] .'" >
                                                <label class="col-4 fw-semibold required">Jawatan Bagi Pihak</label>
                                                <div class="col-8 ps-3">
                                                    <select class="form-select fw-bold fs-6 text-gray-800" name="bp-position" data-allow-clear="true" data-control="select2" data-dropdown-parent="#kt_stepper_generate_letter_PIL_'. $AIDRow['authority_status']. $AIDRow['id'] .'" data-hide-search="true" data-placeholder="Sila Pilih Pegawai"><option><option>'. $jawatanBP .'</select>
                                                </div>
                                            </div>

                                            <input type="hidden" id="system-id-'. $AIDRow['authority_status']. $AIDRow['id'] .'" name="system-id"  value="'. $AIDRow['system_id'] .'" >
                                            <input type="hidden" id="flw-auth-id-'. $AIDRow['authority_status']. $AIDRow['id'] .'" name="flw-auth-id"  value="'. $AIDRow['flw_authorities_id'] .'" >

                                            <input type="hidden" id="ref-no-'. $AIDRow['authority_status']. $AIDRow['id'] .'" name="ref-no"  value="'. $AIDRow['reference_no'] .'">

                                            <input type="hidden" name="letter-type"  value="3">
                                            <!--end::Actions-->
                                        </div>
                                        <!--begin::Step 3-->

                                    </div>
                                    <!--end::Group-->

                                    <!--begin::Actions-->
                                    <div class="d-flex flex-stack justify-content-center">
                                        <!--begin::Wrapper-->
                                        <div class="me-2">
                                            <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
                                                Kembali
                                            </button>
                                        </div>
                                        <!--end::Wrapper-->

                                        <!--begin::Wrapper-->
                                        <div>
                                            <button type="submit" id="submit-'. $AIDRow['authority_status'] . $AIDRow['id'] .'"  class="btn btn-primary" data-kt-stepper-action="submit">
                                                <!--begin::Indicator label-->
                                                <span class="indicator-label">Jana</span>
                                                <!--end::Indicator label-->
                                                <!--begin::Indicator progress-->
                                                <span class="indicator-progress">Sila Tunggu...
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                </span>
                                                <!--end::Indicator progress-->
                                            </button>

                                            <button type="button" class="btn btn-light-primary btn-active-primary" data-kt-stepper-action="next">
                                                Seterusnya
                                            </button>
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Actions-->
                                </form>
                                <!--end::Form-->
                            </div>
                        </div>
                        <!--end::Stepper-->

                    </div>
                    <!--end::Modal body-->

                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>';
}


}

// foreach (letterModal() as $row) {

//         $modalId = $row['StatusID'].$row['ID'].$row['LetterStatusID'].$row['LetterID'];
//         // print_r($modalId);

//         $generateInfo = generateLetter($row['SysID'],$row['flw_generated_letter_id'],3)[0];

//         $authorityData = authorityData($row['SysID'],$row['AuthorityId'])[0];

//     //  var_dump($authorityData);

//         $authInfo  = selectAuthority($row['SysID'],$row['AuthorityId'])[0];

//         $authAddress =  $authInfo['AuthorityAddress1'].' '.$authInfo['AuthorityAddress2'].', '.$authInfo['AuthorityPostcode'].', '.$authInfo['DistrictName'].', '.$authInfo['AuthorityState'];

//         $staffContact1s = '';
//         $staffContact2s = '';

//         $approvalNames = '';
//         $jawatanBPs = '';

//         $toogle = ($generateInfo['on_behalf'] == 't') ? 'checked="checked"' : '';

//         foreach (assignSelect(1) as $rowz) {

//             $name = $rowz['FirstName'];
//             $id = $rowz['username'];
//             $position = $rowz['Position'];

//             $isSelected = ($name === $generateInfo['approval_name']) ? 'selected' : '';
//             $isSelecteds = ($position === $generateInfo['on_behalf_position']) ? 'selected' : '';

//             $approvalNames .= '<option value="'.$name.'" data-position="'.$position.'" '.$isSelected.'>'.$name.'</option>';
//             $jawatanBPs .= '<option value="'.$position.'" '.$isSelecteds.'>'.$position.'</option>';
//         }

//         foreach (assignSelect(1) as $rows) {

//             $name = $rows['FirstName'];
//             $id = $rows['username'];
//             $contact = $rows['StaffPhoneNo'];

//             $isSelected1 = ($name === $generateInfo['officer_name_1']) ? 'selected' : '';
//             $isSelected2 = ($name === $generateInfo['officer_name_2']) ? 'selected' : '';

//             $staffContact1s .= '<option value="'.$name.'" data-contact="'.$contact.'" '.$isSelected1.'>'.$name.'</option>';
//             $staffContact2s .= '<option value="'.$name.'" data-contact="'.$contact.'" '.$isSelected2.'>'.$name.'</option>';
//         }


//         $roadInvolved2 = getRoadInvolved($row['SysID'],$row['AuthorityId']);

//         $roadNames2 = [];
//         $workMethods2 = [];


//         foreach($roadInvolved2 as $rowing2){

//             $roadName2 = $rowing2['road_name'];
//             $roadNames2[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$roadName2.'</div></div>';
//         }

//         $methodStateApproval2 = [];
//         $methodNormal2 = [];



//     //data work method from RP to display in letter
//     foreach(workMethodRP($row['SysID'],$row['AuthorityId']) as $item2){
//         if($item2['method'] === 'CW'){
//             $methodStateApproval2[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].'</div></div>';
//         } else if(!empty($item2['road_crossing'])){
//             if($item2['method'] === 'OH'){
//                 $methodStateApproval2[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].' Merentangi Jalan'.'</div></div>';
//             } else {
//                 $methodNormal2[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].' Merentangi Jalan'.'</div></div>';
//             }
//         } else {
//             $methodNormal2[] = '<div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$item['name'].'</div></div>';
//         }
//     }

//         $workMethods2 = array_merge($methodNormal2,$methodStateApproval2);

//         $uniqueroadNames2 = array_values(array_unique($roadNames2));
//         $roadNamesString2 = implode('', $uniqueroadNames2);

//         $uniqueworkMethods2 = array_values(array_unique($workMethods2));
//         $workMethodsString2 = implode('', $uniqueworkMethods2);


//         // print_r($modalId);

//         //Modal Pindaan Surat PIL
//         if($row['LetterStatusID'] == 2){
//         echo'
//             <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="comment-'. $modalId .'" aria-hidden="true">
//                     <div class="modal-dialog modal-dialog-centered">
//                         <div class="modal-content">
//                             <div class="modal-header">
//                                 <h3 class="modal-title">Pindaan Surat</h3>

//                                 <!--begin::Close-->
//                                 <div id="close-button" class="btn btn-icon btn-sm btn-active-light-'. $row['LetterStatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close" >
//                                     <i class="fad fa-xmark fs-2"></i>
//                                 </div>
//                                 <!--end::Close-->
//                             </div>
//                             <form class="modal-body" novalidate="novalidate" id="form-comment-letter-'. $modalId .'" method="POST">
//                                 <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
//                                         <!--begin::Label-->
//                                         <label class="required d-flex align-items-center fs-6 fw-bold mb-2" for="letter-comment-'. $modalId .'">
//                                             <span>Catatan</span>
//                                         </label>
//                                         <!--end::Label-->
//                                         <div class="fv-row">
//                                             <textarea type="text" class="form-control" id="letter-comment-'. $modalId .'" placeholder="Catatan..." name="letter-comment"></textarea>
//                                         </div>
//                                     </div>


//                                 <input type="hidden" name="system-id"  value="'. $row['SysID'] .'" >
//                                 <input type="hidden" name="letter-no"  value="'. $row['LetterNo'].'" >
//                                 <input type="hidden" name="letter-date"  value="'. $row['LetterDate'].'" >

//                                 <!--begin::Submit button-->
//                                 <div class="d-flex justify-content-end">
//                                     <button type="button" class="btn btn-light-'. $row['LetterStatusColor'] . '" id="comment-letter-button-'. $modalId .'">
//                                         <!--begin::Indicator label-->
//                                         <span class="indicator-label"><i class="fad fa-file-circle-plus"></i> Hantar</span>
//                                         <!--end::Indicator label-->
//                                         <!--begin::Indicator progress-->
//                                         <span class="indicator-progress">Sila Tunggu...
//                                             <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
//                                         </span>
//                                         <!--end::Indicator progress-->
//                                     </button>
//                                 </div>
//                                 <!--end::Submit button-->
//                             </form>
//                         </div>
//                     </div>
//                 </div>
//                 <!--end::Modal-->
//             ';
//         }

//         if($row['LetterStatusID'] == 1 || $row['LetterStatusID'] == 4){
//         //Modal Kemaskini PIL
//         echo '
//         <div class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-hidden="true" role="dialog" id="update-letter-'. $modalId .'">
//             <!--begin::Modal dialog-->
//             <div class="modal-dialog modal-dialog-centered mw-800px">

//                 <!--begin::Modal content-->
//                 <div class="modal-content rounded">

//                     <!--begin::Modal header-->
//                     <div class="modal-header mb-8">
//                         <h3 class="modal-title">Kemaskini Surat Permohonan Izin Lalu</h3>
//                         <!--begin::Close-->
//                         <div class="btn btn-icon btn-sm btn-active-light-'. $row['LetterStatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close" >
//                             <i class="fad fa-xmark fs-2"></i>
//                         </div>
//                         <!--end::Close-->
//                     </div>
//                     <!--end::Modal header-->

//                     <!--begin::Modal body-->
//                     <div class="modal-body px-10 px-lg-10 pt-0 pb-5">

//                         <!--begin::Stepper-->
//                         <div class="stepper stepper-pills stepper-column d-flex flex-column flex-lg-row" id="kt_stepper_update_letter_PIL_'. $modalId .'">
//                             <!--begin::Nav-->

//                             <div class="col-4 d-flex flex-row-auto w-100 w-250px  py-lg-5">
//                                 <div class="stepper-nav mb-5">

//                                     <!--begin::Step 1-->
//                                     <div class="stepper-item mx-4 my-4 current" data-kt-stepper-element="nav">
//                                         <!--begin::Wrapper-->
//                                         <div class="stepper-wrapper d-flex align-items-center">

//                                             <!--begin::Icon-->
//                                             <div class="stepper-icon w-40px h-40px">
//                                                 <i class="stepper-check fas fa-check"></i>
//                                                 <span class="stepper-number">1</span>
//                                             </div>
//                                             <!--end::Icon-->

//                                             <!--begin::Label-->
//                                             <div class="stepper-label">
//                                                 <h3 class="stepper-title">
//                                                     Penerima
//                                                 </h3>
//                                                 <div class="stepper-desc">
//                                                     Maklumat Penerima
//                                                 </div>
//                                             </div>
//                                             <!--end::Label-->

//                                         </div>
//                                         <!--end::Wrapper-->

//                                         <!--begin::Line-->
//                                         <div class="stepper-line h-40px"></div>
//                                         <!--end::Line-->

//                                     </div>
//                                     <!--end::Step 1-->

//                                     <!--begin::Step 2-->
//                                     <div class="stepper-item mx-4 my-4" data-kt-stepper-element="nav">
//                                         <!--begin::Wrapper-->
//                                         <div class="stepper-wrapper d-flex align-items-center">
//                                             <!--begin::Icon-->
//                                             <div class="stepper-icon w-40px h-40px">
//                                                 <i class="stepper-check fas fa-check"></i>
//                                                 <span class="stepper-number">2</span>
//                                             </div>
//                                             <!--begin::Icon-->

//                                             <!--begin::Label-->
//                                             <div class="stepper-label">
//                                                 <h3 class="stepper-title">
//                                                     Maklumat Surat
//                                                 </h3>

//                                                 <div class="stepper-desc">
//                                                     Maklumat kepada Penerima
//                                                 </div>
//                                             </div>
//                                             <!--end::Label-->
//                                         </div>
//                                         <!--end::Wrapper-->

//                                         <!--begin::Line-->
//                                         <div class="stepper-line h-40px"></div>
//                                         <!--end::Line-->

//                                     </div>
//                                     <!--end::Step 2-->

//                                     <!--begin::Step 3-->
//                                     <div class="stepper-item mx-4 my-4" data-kt-stepper-element="nav">
//                                         <!--begin::Wrapper-->
//                                         <div class="stepper-wrapper d-flex align-items-center">
//                                             <!--begin::Icon-->
//                                             <div class="stepper-icon w-40px h-40px">
//                                                 <i class="stepper-check fas fa-check"></i>
//                                                 <span class="stepper-number">3</span>
//                                             </div>
//                                             <!--begin::Icon-->

//                                             <!--begin::Label-->
//                                             <div class="stepper-label">
//                                                 <h3 class="stepper-title">
//                                                     Pengirim
//                                                 </h3>

//                                                 <div class="stepper-desc">
//                                                     Maklumat Pengirim
//                                                 </div>
//                                             </div>
//                                             <!--end::Label-->
//                                         </div>
//                                         <!--end::Wrapper-->
//                                     </div>
//                                     <!--end::Step 3-->

//                                 </div>
//                             </div>
//                             <!--end::Nav-->
//                             <div class="col-8 d-flex flex-row-fluid py-lg-5">
//                                 <!--begin::Form-->
//                                 <form class="form w-lg-500px mx-auto" novalidate="novalidate" id="form-update-'. $modalId .'"  action="components/views/letter-reviews-PIL.php" method="POST">

//                                     <!--begin::Group-->
//                                     <div class="mb-10">

//                                         <!--begin::Step 1-->
//                                         <div class="flex-column current" data-kt-stepper-element="content">
//                                             <div class="d-flex flex-row align-items-center mb-4 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">PBM/PBT</label>
//                                                 <div class="col-8">
//                                                     <span id="authority-name-'. $modalId .'" type="text" class="fw-bold fs-6 text-gray-800" name="authority-name"><div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authInfo['AuthorityName'].'</div></div></span>
//                                                 </div>
//                                             </div>

//                                             <input type="hidden" name="authority-id" id="aid-'. $modalId .'" value="'. $authInfo['AuthorityID'] .'" >

//                                             <!--begin::Input group-->
//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">Alamat PBM/PBT</label>
//                                                 <div class="col-8">
//                                                     <span type="text" name="authority-address" class="fw-bold fs-6 text-gray-800"><div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authAddress.'</div></div></span>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold">Untuk Perhatian</label>
//                                                 <div class="col-8">
//                                                     <span id="authority-attention-'. $modalId .'" type="text" class="fw-bold fs-6 text-gray-800" ><div class="d-flex flex-row align-items-center"><span class="col-2 bullet bullet-vertical bg-primary me-5"></span><div class="col-10">'.$authInfo['AuthorityPIC'].'</div></div></span>
//                                                 </div>
//                                             </div>
//                                         </div>
//                                         <!--end::Step 1-->

//                                         <!--begin::Step 2-->
//                                         <div class="flex-column" data-kt-stepper-element="content">

//                                             <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">No Rujukan Surat</label>
//                                                 <div class="col-8">
//                                                     <input type="text" id="letter-no-'. $modalId .'" name="letter-no" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="'.$row['LetterNo'].'" readonly/>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">Tarikh Surat</label>
//                                                 <div class="col-8">
//                                                     <input type="text" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" placeholder="'. $letter_date .'" name="date-generated" value="'. $letter_date .'" readonly/>
//                                                 </div>
//                                             </div>

//                                             <input type="hidden" name="today-date"  value="'. $generateDate .'" >

//                                             <div class=" d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">Tajuk Projek</label>
//                                                 <div class="col-8">
//                                                     <span id="project-title-'. $modalId .'" type="text" name="project-title" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" style="text-align: justify;">'.$authorityData['project_title'].'</span>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">Penyedia Utiliti</label>
//                                                 <div class="col-8">
//                                                     <input  type="text" name="provider-name" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="'. $row['Provider'] .'" readonly/>
//                                                 </div>
//                                             </div>

//                                             <input  type="hidden" name="provider-id" value="'. $row['ProviderID'] .'" />

//                                             <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted">Jarak Projek</label>
//                                                 <div class="col-8">
//                                                     <input  type="text" name="distance" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="'. $authorityData['involved_appl_length'] .' meter" readonly/>
//                                                 </div>
//                                             </div>


//                                             <div class="d-flex flex-row align-items-start fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted pt-4">Nama Jalan Terlibat</label>
//                                                 <div class="col-8">
//                                                     <span  type="text" name="road-name" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="">'. ($roadNamesString2 ?? '') .'</span>
//                                                 </div>
//                                             </div>

//                                             <input  type="hidden" name="road-id" value="" />

//                                             <div class="d-flex flex-row align-items-start mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold text-muted pt4">Kaedah K.Pemasangan</label>
//                                                 <div class="col-8">
//                                                     <span  type="text" name="work-method" class="form-control form-control-transparent fw-bold fs-6 text-gray-800" value="">'. ($workMethodsString2 ?? '') .'</span>
//                                                 </div>
//                                             </div>

//                                             <input  type="hidden" name="work-method" value="" />

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold form-label required">Tarikh Jangka Mula</label>
//                                                 <div class="col-8 ps-3">
//                                                     <input id="date-start-'. $modalId .'" name="date-start" class="form-control fw-bold fs-6 text-gray-800" placeholder="Sila Pilih Tarikh" value="'.$generateInfo['date_start'].'" />
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold form-label required">Tarikh Jangka Siap</label>
//                                                 <div class="col-8 ps-3">
//                                                     <input id="date-finish-'. $modalId .'" type="date" name="date-finish" class="form-control fw-bold fs-6 text-gray-800" placeholder="Sila Pilih Tarikh" value="'.$generateInfo['date_finish'].'" />
//                                                 </div>
//                                             </div>

//                                         </div>
//                                         <!--end::Step 2-->

//                                         <!--begin::Step 3-->
//                                         <div class="flex-column" data-kt-stepper-element="content">

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold required">Pegawai Berhubung 1</label>
//                                                 <div class="col-8 ps-3">
//                                                     <select id="officer-name1-'. $modalId .'" class="form-select fw-bold fs-6 text-gray-800" data-control="select2" name="officer-name1" data-allow-clear="true" data-dropdown-parent="#kt_stepper_update_letter_PIL_'. $modalId .'" data-hide-search="true" data-placeholder="Sila Pilih Pegawai"><option><option>'. $staffContact1s .'</select>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold">No Tel P.Berhubung 1</label>
//                                                 <div class="col-8 ps-3">
//                                                     <input id="officer-contact1-'. $modalId .'" class="form-control form-control-solid fw-bold fs-6 text-gray-800" name="officer-contact1" value="'.$generateInfo['officer_contact_1'].'" placeholder="" readonly/>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold required">Pegawai Berhubung 2</label>
//                                                 <div class="col-8 ps-3">
//                                                     <select id="officer-name2-'. $modalId .'" class="form-select fw-bold fs-6 text-gray-800" data-control="select2" name="officer-name2" data-allow-clear="true"data-hide-search="true" data-dropdown-parent="#kt_stepper_update_letter_PIL_'. $modalId .'" data-placeholder="Sila Pilih Pegawai"><option><option>'. $staffContact2s .'</select>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold">No Tel P.Berhubung 2</label>
//                                                 <div class="col-8 ps-3">
//                                                     <input id="officer-contact2-'. $modalId .'" class="form-control form-control-solid fw-bold fs-6 text-gray-800" name="officer-contact2" value="'.$generateInfo['officer_contact_2'].'" placeholder="" readonly/>
//                                                 </div>
//                                             </div>

//                                             <div class=" d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold required">Tanda Tangan Pegawai</label>
//                                                 <div class="col-8 ps-3">
//                                                     <select id="approval-name-'. $modalId .'" class="form-select fw-bold fs-6 text-gray-800" name="approval-name" data-allow-clear="true" data-control="select2" data-dropdown-parent="#kt_stepper_update_letter_PIL_'. $modalId .'"data-hide-search="true" data-placeholder="Sila Pilih Pegawai"><option><option>'. $approvalNames .'</select>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container">
//                                                 <label class="col-4 fw-semibold">Jawatan Pegawai</label>
//                                                 <div class="col-8 ps-3">
//                                                     <input id="approval-position-'. $modalId .'" class="form-control form-control-solid fw-bold fs-6 text-gray-800" name="approval-position" value="'.$generateInfo['approval_position'].'" placeholder="" readonly/>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-column mb-5 fv-row fv-plugins-icon-container">
//                                                 <div class="form-check form-switch form-check-custom form-check-solid d-flex justify-content-between">
//                                                     <label class="fw-semibold text-muted">
//                                                         Sila tanda jika tandatangan bagi pihak pegawai melulus
//                                                     </label>
//                                                     <input class="form-check-input" type="checkbox" value="true" id="tanda-bagiPihak-'. $modalId.'" name="bagi-pihak" '.$toogle .'/>
//                                                 </div>
//                                             </div>

//                                             <div class="d-flex flex-row align-items-center mb-5 fv-row fv-plugins-icon-container" id="bp-position-'. $modalId.'" >
//                                                 <label class="col-4 fw-semibold required">Jawatan Bagi Pihak</label>
//                                                 <div class="col-8 ps-3">
//                                                     <select class="form-select fw-bold fs-6 text-gray-800" name="bp-position" data-allow-clear="true" data-control="select2" data-dropdown-parent="#kt_stepper_update_letter_PIL_'. $modalId.'" data-hide-search="true" data-placeholder="Sila Pilih Pegawai"><option><option>'. $jawatanBPs .'</select>
//                                                 </div>
//                                             </div>

//                                             <input type="hidden" id="system-id-'. $modalId .'" name="system-id"  value="'. $row['SysID'] .'" >
//                                             <input type="hidden" id="flw-auth-id-'. $authorityData['authority_status']. $authorityData['id'] .'" name="flw-auth-id"  value="'. $authorityData['flw_authorities_id'] .'" >

//                                             <input type="hidden" id="ref-no-'. $modalId .'" name="ref-no"  value="'. $row['RefNo'] .'">

//                                             <input type="hidden" name="letter-type"  value="3">
//                                             <!--end::Actions-->
//                                         </div>
//                                         <!--begin::Step 3-->

//                                     </div>
//                                     <!--end::Group-->

//                                     <!--begin::Actions-->
//                                     <div class="d-flex flex-stack justify-content-center">
//                                         <!--begin::Wrapper-->
//                                         <div class="me-2">
//                                             <button type="button" class="btn btn-light btn-active-light-primary" data-kt-stepper-action="previous">
//                                                 Kembali
//                                             </button>
//                                         </div>
//                                         <!--end::Wrapper-->

//                                         <!--begin::Wrapper-->
//                                         <div>
//                                             <button type="submit" id="submit-update-'. $modalId .'"  class="btn btn-primary" data-kt-stepper-action="submit">
//                                                 <!--begin::Indicator label-->
//                                                 <span class="indicator-label">Kemaskini</span>
//                                                 <!--end::Indicator label-->
//                                                 <!--begin::Indicator progress-->
//                                                 <span class="indicator-progress">Sila Tunggu...
//                                                     <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
//                                                 </span>
//                                                 <!--end::Indicator progress-->
//                                             </button>

//                                             <button type="button" class="btn btn-light-primary btn-active-primary" data-kt-stepper-action="next">
//                                                 Seterusnya
//                                             </button>
//                                         </div>
//                                         <!--end::Wrapper-->
//                                     </div>
//                                     <!--end::Actions-->
//                                 </form>
//                                 <!--end::Form-->
//                             </div>
//                         </div>
//                         <!--end::Stepper-->

//                     </div>
//                     <!--end::Modal body-->

//                 </div>
//                 <!--end::Modal content-->
//             </div>
//             <!--end::Modal dialog-->
//         </div>';
//         }

// }



?>

