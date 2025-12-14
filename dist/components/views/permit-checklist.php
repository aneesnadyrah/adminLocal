<?php
    if (!empty($_GET['sid'])) {
        $detail = ApplicationEntry::getPermitChecklist($_GET['sid']);
        $attach1 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 1);
        $attach2 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 2);
        $attach3 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 3);
        $attach4 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 4);
        $attach5 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 5);
        $attach6 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 6);
        $attach7 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 7);
        $attach8 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 8);
        $attach9 = ApplicationEntry::checkPermitAttachment($_GET['sid'], 9);

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
            <div class="card-header d-flex align-items-center justify-content-between">
                <h1 class="card-title fw-bold text-dark fs-2">Senarai Semak Kelulusan Permit Kerja <?php echo '- '.$detail['reference_no'] ?></h1>

                <a href="/tasks/new" class="btn btn-sm btn-light-primary">
                    <span class="svg-icon svg-icon-3">
                        <i class="fad fa-arrow-left fs-4"></i>
                    </span>
                    Kembali
                </a>
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
        "num" => 1
    ),
    array(
        "attach" => $attach2,
        "received" => $received,
        "num" => 2
    ),
    array(
        "attach" => $attach3,
        "received" => $received,
        "num" => 3
    ),
    array(
        "attach" => $attach4,
        "received" => $received,
        "num" => 4
    ),
    array(
        "attach" => $attach5,
        "received" => $received,
        "num" => 5
    ),
    array(
        "attach" => $attach6,
        "received" => $received,
        "num" => 6
    ),
    array(
        "attach" => $attach7,
        "received" => $received,
        "num" => 7
    ),
    array(
        "attach" => $attach8,
        "received" => $received,
        "num" => 8
    ),
    array(
        "attach" => $attach9,
        "received" => $received,
        "num" => 9
    ),
    // Add more sets here...
);

foreach ($sets as $set) {
    $attach = $set["attach"];
    $received = $set["received"];
    $num = $set["num"];
?>

<div class="d-flex align-items-center mb-8">
    <!--begin::Bullet-->
    <span class="bullet bullet-vertical h-40px bg-primary"></span>
    <!--end::Bullet-->
    <!--begin::Checkbox-->
    <div class="form-check form-check-custom form-check-solid mx-5">
        <input class="form-check-input" type="checkbox" value="" disabled <?php echo isset($attach['checked']) ? 'checked' : ''; ?> />
    </div>
    <!--end::Checkbox-->
    <!--begin::Description-->
    <div class="flex-grow-1 me-10">
        <?php
        $link = '<a href="#pdf-attach'.$num.'" class="text-gray-800 text-hover-primary fw-bold fs-6" data-fslightbox="lightbox" data-class="fslightbox-source">SURAT SERAHAN PERMOHONAN KELULUSAN PERMIT KERJA</a>';
        $notlink = '<span class="text-gray-800 fw-bold fs-6">SURAT SERAHAN PERMOHONAN KELULUSAN PERMIT KERJA</span>';
        echo !empty($attach) ? $link : $notlink;
        ?>
        <span class="text-muted fw-semibold d-block"><?php echo !empty($attach) ? General::convertDate($attach['attachment_date']) : '-'; ?></span>
    </div>
    <!--end::Description-->
    <?php echo !empty($attach) ? $received : $not_received; ?>
    <!--begin::Submit Button-->
    <button id="btn_check_<?php echo $num; ?>" class="btn btn-primary btn-sm mx-1" data-toggle="modal" data-target="#modal_<?php echo $num; ?>">Semak</button>

    <div class="modal fade" id="modal_<?php echo $num; ?>" tabindex="-1" role="dialog" aria-labelledby="label_modal_<?php echo $num; ?>">
        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="label_modal_<?php echo $num; ?>">Tindakan</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <!--begin::Form-->
                                    <form novalidate="novalidate" id="form_modal_<?php echo $num; ?>">
                                        <!--begin::Actions-->
                                        <div class="mb-0">
                                            <!--begin::Input group-->
                                            <div class="form-check form-switch form-check-custom form-check-primary form-check-solid fv-row d-flex justify-content-between mb-5">
                                                <label class="form-label fs-6 fw-semibold" >
                                                    Adakah dokumen bagi permohonan permit lengkap?
                                                </label>
                                                <input class="form-check-input toggle-checkbox" type="checkbox" value="1" name="chk_modal_<?php echo $num; ?>" id="toggleComplete"/>
                                            </div>
                                            <!--end::Input group-->

                                            <!--begin::Input group-->
                                            <div class="fv-row">
                                                <!--begin::Label-->
                                                <label class="required fs-6 fw-semibold mb-2">Catatan</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <textarea class="form-control form-control-solid" rows="4" name="note_modal_<?php echo $num; ?>" placeholder="Catatan"></textarea>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->

                                        </div>
                                        <!--end::Actions-->
                                    </form>
                                    <!--end::Form-->

                                    <input type="text" name="system-id" value=<?php echo $_GET['sid'] ?> hidden/>
                                    
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                    <button type="submit" id="submit_modal_<?php echo $num; ?>" class="btn btn-primary">
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

<script>
    document.getElementById('btn_check_'+<?php echo $num; ?>).addEventListener('click', function() {
        $('#modal_'+<?php echo $num; ?>).modal('show');
    });
</script>

<?php
}
?>

            </div>
            
            <div style="display: none;">
                <div id="pdf-attach1">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach1['url']?>/1 ">
                    </iframe>
                </div>
                <div id="pdf-attach2">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach2['url']?>/2 ">
                    </iframe>
                </div>
                <div id="pdf-attach3">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach3['url']?>/3 ">
                    </iframe>
                </div>
                <div id="pdf-attach4">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach4['url']?>/4 ">
                    </iframe>
                </div>
                <div id="pdf-attach5">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach5['url']?>/5 ">
                    </iframe>
                </div>
                <div id="pdf-attach6">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach6['url']?>/6 ">
                    </iframe>
                </div>
                <div id="pdf-attach7">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach7['url']?>/7 ">
                    </iframe>
                </div>
                <div id="pdf-attach8">
                    <iframe class="scroll h-700px w-900px px-5"
                        src="/components/files/<?php echo $attach8['url']?>/8 ">
                    </iframe>
                </div>
                <div id="pdf-attach9">
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
    <div class="flex-lg-row-fluid w-100 mw-lg-300px mw-xxl-350px">
    <!-- <div class="flex-lg-auto min-w-lg-300px"> -->
        <!--begin::Card-->
        <div class="card">
            <!--begin::Header-->
            <div class="card-header d-flex align-items-center">
                <h1 class="card-title fw-bold text-dark fs-2">Tindakan</h1>
            </div>
            <!--end::Header-->

            <!--begin::Card body-->
            <div class="card-body p-10">
                <!--begin::Form-->
                <form class="modal-body" novalidate="novalidate" id="form_action_permit_checklist">
                    <!--begin::Actions-->
                    <div class="mb-0">
                        <!--begin::Input group-->
                        <div class="form-check form-switch form-check-custom form-check-primary form-check-solid fv-row d-flex justify-content-between mb-5">
                            <label class="form-label fs-6 fw-semibold" >
                                Adakah dokumen bagi permohonan permit lengkap?
                            </label>
                            <input class="form-check-input toggle-checkbox" type="checkbox" value="1" name="c" id="toggleComplete"/>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-15">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Catatan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="4" name="notes" placeholder="Catatan"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <input type="text" name="system-id" value=<?php echo $_GET['sid'] ?> hidden/>

                        <!--begin::Submit Button-->
                        <button type="submit" id="submit_action_permit_checklist" class="btn btn-primary">
                            <!--begin::Indicator label-->
                            <span class="indicator-label"><i class="fad fa-user-pen"></i> Hantar</span>
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

