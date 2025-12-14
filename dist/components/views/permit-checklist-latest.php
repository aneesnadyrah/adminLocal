<?php
    if (!empty($_GET['sid'])) {
        $appEntry = new permitEntry();
        $projectDetails = new Details();
        $detail = $appEntry->getPermitChecklist($_GET['sid']);
        $attach1 = $appEntry->checkPermitAttachment($_GET['sid'], 1);
        $attach2 = $appEntry->checkPermitAttachment($_GET['sid'], 2);
        $attach3 = $appEntry->checkPermitAttachment($_GET['sid'], 3);
        $attach4 = $appEntry->checkPermitAttachment($_GET['sid'], 4);
        $attach5 = $appEntry->checkPermitAttachment($_GET['sid'], 5);
        $attach6 = $appEntry->checkPermitAttachment($_GET['sid'], 6);
        $attach7 = $appEntry->checkPermitAttachment($_GET['sid'], 7);
        $attach8 = $appEntry->checkPermitAttachment($_GET['sid'], 8);
        $attach9 = $appEntry->checkPermitAttachment($_GET['sid'], 9);

        $e = $projectDetails->getProjectDetails($systemId);
        $officers = $appEntry->getPermitOfficers($systemId);
        
        $not_received = '<span class="badge badge-light-warning fs-8 fw-bold">Belum Diterima</span>';
        $received = '<span class="badge badge-light-success fs-8 fw-bold">Sudah Diterima</span>';
    }
?>

<!--begin::Layout-->
<div class="d-flex flex-column flex-lg-row">
    <!--begin::Content-->
    <div class="flex-lg-row-fluid mb-10 mb-lg-0 me-lg-7 me-xl-10">
        <!--begin::Permit Checklist-->
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <!--begin::Header-->
            
            <!--begin::Header-->
            <div class="card-header d-flex align-items-center justify-content-between">
                
                <h1 class="card-title fw-bold text-dark fs-2">Maklumat Kelulusan Permit Kerja <?php echo '- ' . isset($detail['reference_no']) ?></h1>

                <a href="/tasks/new" class="btn btn-sm btn-light-primary">
                    <span class="svg-icon svg-icon-3">
                        <i class="fad fa-arrow-left fs-4"></i>
                    </span>
                    Kembali
                </a>
            </div>
            <!--end::Header-->
            
            <div class="card-body p-10">
                <!--begin::Product table-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr
                                class="border-bottom border-gray-200 text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="">Nama</th>
                                <th class="">Email</th>
                                <th class="">No Telefon</th>
                                <th class="">Nama Syarikat</th>
                                <th class="">Alamat</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-800">
                            <?php
                            foreach ($officers as $row) {
                                if(isset($row['Type'])) {
                                    $type = $row['Type'];
                                    switch ($type) {
                                        case 1:
                                            $typeOfficer = 'Kontraktor Utiliti';
                                            break;
                                        case 2:
                                            $typeOfficer = 'Sub-Kontraktor Utiliti';
                                            break;
                                        default:
                                            $typeOfficer = '';
                                            break;
                                    }
                                } else {
                                    $typeOfficer = '';
                                }

                                $template = <<<HTML
                                    <tr>
                                        <td>
                                            <label class="w-150px">$row[FullName]</label>
                                            <div class="fw-normal text-gray-600">$typeOfficer</div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-danger">$row[Email]</span>
                                        </td>
                                        <td>$row[PhoneNo]</td>
                                        <td>
                                            $row[CompanyName]
                                            <div class="fw-normal text-gray-600">$row[Position]</div>
                                        </td>
                                        <td>
                                        $row[Address1], 
                                        $row[Address2], <br/>
                                        $row[Postcode], 
                                        $row[City], 
                                        $row[State]
                                        </td>
                                    </tr>
                                    HTML;

                                if (!empty($row['FullName'])) {
                                    echo $template;
                                }
                            }
                            ?>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Product table-->
            </div>
                                    
        </div>


        <!--begin::Permit Checklist-->
        <div class="card card-xl-stretch mb-5 mb-xl-8">
            <!--begin::Header-->
            <div class="card-header d-flex align-items-center justify-content-between">
                
                <h1 class="card-title fw-bold text-dark fs-2">Senarai Semak Kelulusan Permit Kerja</h1>

                <!-- <a href="/tasks/new" class="btn btn-sm btn-light-primary">
                    <span class="svg-icon svg-icon-3">
                        <i class="fad fa-arrow-left fs-4"></i>
                    </span>
                    Kembali
                </a> -->
            </div>
            <!--end::Header-->

            <!--begin::Body-->
            

            <div class="card-body p-10">
            <?php
            // Example data for each set
            $sets = array(
                array(
                    "attach" => $attach1,
                    "received" => $received,
                    "name" => "SURAT SERAHAN PERMOHONAN KELULUSAN PERMIT KERJA",
                    "num" => 1
                ),
                // array(
                //     "attach" => $attach2,
                //     "received" => $received,
                //     "name" => "BORANG PERMOHONAN KELULUSAN PERMIT KERJA (BKPK)",
                //     "num" => 2
                // ),
                array(
                    "attach" => $attach3,
                    "received" => $received,
                    "name" => "PELAN INFRASTRUKTUR UTILITI (UDM)",
                    "num" => 2
                ),
                array(
                    "attach" => $attach4,
                    "received" => $received,
                    "name" => "PELAN KAWALAN TRAFIK (TCP) OLEH JURUTERA PROFESIONAL DENGAN PERAKUAN AMALAN (PEPC)",
                    "num" => 3
                ),
                array(
                    "attach" => $attach5,
                    "received" => $received,
                    "name" => "KAEDAH KERJA (Pengesahan Oleh Jurutera Bertauliah - PEPC) (Berwarna)",
                    "num" => 4
                ),
                array(
                    "attach" => $attach6,
                    "received" => $received,
                    "name" => "BORANG B (Majlis Bandaraya Kuantan)",
                    "num" => 5
                ),
                array(
                    "attach" => $attach7,
                    "received" => $received,
                    "name" => "INSURAN",
                    "num" => 6
                ),
                array(
                    "attach" => $attach8,
                    "received" => $received,
                    "name" => "BORANG JKR",
                    "num" => 7
                ),
                array(
                    "attach" => $attach9,
                    "received" => $received,
                    "name" => "CAJ WANG CAGARAN (Deposit)",
                    "num" => 8
                ),
                // Add more sets here...
            );

            foreach ($sets as $set) {
                $attach = $set["attach"];
                $name = $set["name"];
                $num = $set["num"];
            ?>

            <div class="d-flex align-items-center mb-8">
                <!--begin::Bullet-->
                <!-- <span class="bullet bullet-vertical h-40px bg-primary"></span> -->
                <!--end::Bullet-->

                <!--begin::Checkbox-->
                <div class="form-check form-check-custom form-check-solid me-5">
                    <input class="form-check-input" type="checkbox" value="" disabled <?php echo isset($attach['checked']) && $attach['checked'] == true ? 'checked' : ''; ?> />
                </div>
                <!--end::Checkbox-->

                <!--begin::Description-->
                <div class="flex-grow-1 me-10">
                    <?php
                    // $link = '<a href="#pdf-attach-'.$num.'" class="text-gray-800 text-hover-primary fw-bold fs-6" data-fslightbox="lightbox" data-class="fslightbox-source">'.$name.'</a>';
                    $link = '<span class="text-gray-800 fw-bold fs-6" data-fslightbox="lightbox" data-class="fslightbox-source">'.$name.'</span>';
                    $notlink = '<span class="text-gray-800 fw-bold fs-6">'.$name.'</span>';
                    $attachmentExists = !empty($attach);
                    echo $attachmentExists ? $link : $notlink;
                    ?>
                    <span class="text-muted fw-semibold d-block">
                        <?php
                        if ($attachmentExists) {
                            echo $received . ' ';
                            echo General::convertDate($attach['attachment_date']);
                        } else {
                            echo $not_received;
                        }
                        ?>
                    </span>
                </div>
                <!--end::Description-->
                <!--begin::Button-->
                <div class="d-flex align-items-center gap-2"> <!-- Added gap-2 class for spacing -->
                    <a href="#pdf-attach-<?php echo $num; ?>" class="btn btn-icon btn-light-dark btn-sm <?php if (!$attachmentExists) echo 'd-none'; ?>" <?php if ($attachmentExists) echo 'data-fslightbox="lightbox" data-class="fslightbox-source"'; ?>>
                        <i class="fad fa-eye fs-4"></i>
                    </a>
                    <button id="btn_check_<?php echo $num; ?>" class="btn btn-icon btn-light-primary btn-sm <?php if (!$attachmentExists) echo 'd-none'; ?>" <?php if ($attachmentExists) echo 'data-toggle="modal" data-target="#modal_'.$num.'"'; ?>>
                        <i class="fad fa-file-pen fs-4"></i>
                    </button>
                </div>
                <!--end::Button-->
                
                <div class="modal fade chk-modal" id="modal_<?php echo $num; ?>" tabindex="-1" role="dialog" aria-labelledby="label_modal_<?php echo $num; ?>">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="label_modal_<?php echo $num; ?>">Tindakan</h4>
                                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fad fa-xmark fs-2"></i>
                                </div>
                            </div>
                            <div class="modal-body">
                                <!--begin::Form-->
                                <form novalidate="novalidate" id="form_modal_<?php echo $num; ?>">
                                    <!--begin::Actions-->
                                    <div class="mb-0">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-5">
                                            <label class="form-label fs-6 fw-semibold" >
                                                Adakah dokumen <b><?php echo $name; ?></b> ini lengkap?
                                            </label>

                                            <div class="d-flex align-items-center me-2">
												<!--begin::Radio-->
												<div class="form-check form-check-custom form-check flex-shrink-0 me-6">
                                                    <input class="form-check-input check-modal" type="radio" id="complete_doc_<?php echo $num; ?>" name="check_modal_<?php echo $num; ?>" value="1"/>
                                                    <label class="form-check-label" for="complete_doc_<?php echo $num; ?>">
                                                        Ya
                                                    </label>
												</div>
												<!--end::Radio-->
												<!--begin::Radio-->
												<div class="form-check form-check-custom form-check flex-shrink-0 me-6">
                                                    <input class="form-check-input check-modal" type="radio" id="incomplete_doc_<?php echo $num; ?>" name="check_modal_<?php echo $num; ?>" value="0"/>
                                                    <label class="form-check-label" for="incomplete_doc_<?php echo $num; ?>">
                                                        Tidak
                                                    </label>
												</div>
												<!--end::Radio-->
											</div>
                                        </div>
                                        <!--end::Input group-->

                                        <!--begin::Input group-->
                                        <div class="fv-row">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-semibold mb-2">Catatan</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <textarea class="form-control note-modal" rows="4" name="note_modal_<?php echo $num; ?>" placeholder="Catatan"></textarea>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->

                                    </div>
                                    <!--end::Actions-->
                                </form>
                                <!--end::Form-->

                                <span id="phpValue" class="phpValue" data-num="<?php echo $num; ?>"></span>
                                
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                <button type="submit" id="submit_modal_<?php echo $num; ?>" class="btn btn-primary submit-button">
                                    <span class="indicator-label"><i class="fad fa-user-pen"></i> Hantar</span>
                                    <span class="indicator-progress">Sila Tunggu...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php
            }
            ?>

            </div>
            
            <div style="display: none;">
                <div id="pdf-attach-1">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach1['url']?>/1 ">
                    </iframe>
                </div>
                <div id="pdf-attach-2">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach2['url']?>/2 ">
                    </iframe>
                </div>
                <div id="pdf-attach-3">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach3['url']?>/3 ">
                    </iframe>
                </div>
                <div id="pdf-attach-4">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach4['url']?>/4 ">
                    </iframe>
                </div>
                <div id="pdf-attach-5">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach5['url']?>/5 ">
                    </iframe>
                </div>
                <div id="pdf-attach-6">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach6['url']?>/6 ">
                    </iframe>
                </div>
                <div id="pdf-attach-7">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach7['url']?>/7 ">
                    </iframe>
                </div>
                <div id="pdf-attach-8">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach8['url']?>/8 ">
                    </iframe>
                </div>
                <div id="pdf-attach-9">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach9['url']?>/9 ">
                    </iframe>
                </div>
            </div>
            <!--end::file-->
            <!--end::Body-->
        </div>
        <!--end:Permit Checklist-->
    </div>
    <!--end::Content-->
    
    <!--begin::Sidebar-->
    <?php include "components/views/permit-sidebar-1.php" ?>
    <!--end::Sidebar-->
</div>
<!--end::Layout-->

