<?php
    $detail =  LetterRecord::letterInDetails($_GET['lid'], '1')[0];
    $cronology = LetterRecord::cronologyLetter($detail['unique_id']);

    $option = '<option></option>';
    foreach (Tasking::assignSelect('2') as $row) {
        if($row['ProfilePic'] == null){
            $img = "blank";
        }
        else{
            $img = $row['ProfilePic'];
        };
        $name = $row['FirstName'];
        $id = $row['username'];

        $option .= '<option value="'.$id.'" data-kt-select2-user="'.General::getProfile($img).'.jpg">'.$name.'</option>';
    }
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
                    <h1 class="text-dark fw-bold my-0 fs-2 me-auto">Maklumat Surat</h1>
                    <!--end::Title-->
                    <a href="/letter/in/list" class="btn btn-sm btn-light-primary">
                        <span class="svg-icon svg-icon-3">
                            <i class="fad fa-arrow-left fs-4"></i>
                        </span>Kembali</a>
                </div>
                <!--end::Top-->

                <!--begin::Separator-->
                <div class="separator fw-bold my-5"></div>
                <!--end::Separator-->

                <!--begin::Wrapper-->
                <div class="m-0">
                    <!--begin::Row-->
                    <div class="row g-5 mb-8">
                        <!--end::Col-->
                        <div class="col-sm-12">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Tarikh Surat Diterima:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <?php 
                                $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                shuffle($colors);
                                echo '<span class="badge badge-light-'.array_shift($colors).' fw-bold fs-7">'. General::convertDate($detail['date_receive']) .'</span>';
                            ?>
                            <!--end::Text-->
                            <!--begin::file-->
                            <a data-fslightbox="lightbox" data-class="fslightbox-source" href="#pdf-bkil">
                                Lihat Lampiran
                            </a>

                            <div style="display: none;">
                                <div id="pdf-bkil">
                                    <iframe class="scroll h-700px w-900px px-5"
                                        src="/components/partials/widgets/print.php?f=<?php echo $detail['doc']?>&t=<?php echo $detail['letter_id']?> ">
                                    </iframe>
                                </div>
                            </div>
                            <!--end::file-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row g-5 mb-8">
                        <!--end::Col-->
                        <div class="col-sm-12">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Tajuk Surat:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <div class="fw-bold fs-6 text-gray-800 user-select-all"><?php echo $detail['title'] ?></div>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row g-5 mb-8">
                        <!--end::Col-->
                        <div class="col-sm-6">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">No Rujukan Kami:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <div class="fw-bold fs-6 text-gray-800 user-select-all"><?php echo $detail['no_ref_letter'] ?></div>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                        <!--end::Col-->
                        <div class="col-sm-6">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">No Rujukan Tuan:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <div class="fw-bold fs-6 text-gray-800 user-select-all"><?php echo $detail['no_ref_tuan'] ?></div>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row g-5 mb-8">
                        <!--end::Col-->
                        <div class="col-sm-6">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Pengirim:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <div class="fw-bold fs-6 text-gray-800"><?php echo $detail['sender'] ?></div>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                        <!--end::Col-->
                        <div class="col-sm-6">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Diminit Kepada:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <div class="fw-bold fs-6 text-gray-800"><?php echo ucwords($detail['first_name'] ?? '-'); ?></div>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row g-5 mb-8">
                        <!--end::Col-->
                        <div class="col-sm-6">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Jenis Surat:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <?php 
                                $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                shuffle($colors);
                                echo '<span class="badge badge-light-'.array_shift($colors).' fw-bold fs-7">'. $detail['letter_name'] .'</span>';
                            ?>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                        <!--end::Col-->
                        <div class="col-sm-6">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Melalui:</div>
                            <!--end::Label-->
                            <!--end::Text-->
                            <?php 
                                $colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
                                shuffle($colors);
                                echo '<span class="badge badge-light-'.array_shift($colors).' fw-bold fs-7 text-capitalize">'. $detail['method'] .'</span>';
                            ?>
                            <!--end::Text-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row g-5">
                        <!--end::Col-->
                        <div class="col-sm-12">
                            <!--end::Label-->
                            <div class="fw-semibold fs-6 text-gray-600 mb-1">Catatan:</div>
                            <!--end::Label-->
                            <!--end::Col-->
                            <div class="fw-bold fs-6 text-gray-800"><?php echo $detail['notes'] ?></div>
                            <!--end::Col-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Wrapper-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->

        <!--begin::Card-->
        <div class="card">
            <!--begin::Card body-->
            <div class="card-body p-12">
                <!--begin::Wrapper-->
                <div class="d-flex flex-column align-items-start flex-xxl-row">
                    <!--begin::Title-->
                    <h1 class="text-dark fw-bold my-0 fs-2">Kronologi Surat</h1>
                    <!--end::Title-->
                </div>
                <!--end::Top-->

                <!--begin::Separator-->
                <div class="separator fw-bold my-5"></div>
                <!--end::Separator-->

                <!--begin::cronology-->
                <?php if (count($cronology) > 0) { ?>
                    <!--begin::Timeline-->
                    <?php include "components/partials/widgets/cronology-letter-in.php" ?>
                    <!--end::Timeline-->
                <?php } else { ?>
                    <!--begin::Empty-->
                    <div data-kt-search-element="empty" class="text-center">
                        <!--begin::Message-->
                        <div class="fw-semibold py-10">
                            <div class="text-gray-600 fs-3 mb-2">Tiada Data Bagi Kronologi Surat</div>
                        </div>
                        <!--end::Message-->
                        <!--begin::Illustration-->
                        <div class="text-center px-5">
                            <img src="assets/media/illustrations/empty/06.svg" alt="" class="w-50 h-150px" />
                        </div>
                        <!--end::Illustration-->
                    </div>
                    <!--end::Empty-->
                <?php } ?>
                <!--end::cronology-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Content-->
    
    <!--begin::Sidebar-->
    <div class="flex-lg-row-fluid w-100 mw-lg-300px mw-xxl-350px">
    <!-- <div class="flex-lg-auto min-w-lg-300px"> -->
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card body-->
            <div class="card-body p-10">
                <!--begin::Form-->
                <form class="modal-body" novalidate="novalidate" id="form_action_letter_in">
                    <!--begin::Actions-->
                    <div class="mb-0">
                        <!--begin::Title-->
                        <h6 class="mb-6 fw-bolder text-gray-600 text-hover-primary">TINDAKAN SURAT</h6>
                        <!--end::Title-->
                        <div class="separator mb-6 "></div>

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-semibold mb-2">Status</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select id="selection-status" class="form-select form-select-solid" name="status" data-placeholder="Sila Pilih Status" data-allow-clear="true">
                                <option value="1">Dalam Proses</option>
                                <option value="2">Selesai</option>
                            </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-7" id="myDiv" style="display: none;">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">Diminit Kepada</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select id="selection-staff" class="form-select form-select-solid" name="receiver" data-placeholder="Sila Pilih Minit Surat" data-allow-clear="true">
                                <?php echo $option ?>
                            </select>
                            <!--end::Input-->
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

                        <input type="text" name="unique_id" value=<?php echo $_GET['lid'] ?> hidden/>

                        <!--begin::Submit Button-->
                        <button type="submit" id="submit_action_letter" class="btn btn-primary">
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