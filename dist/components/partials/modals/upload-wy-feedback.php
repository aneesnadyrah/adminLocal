<?php
// if($appsTitle == 'UCIDOS') {
foreach ($wayleave_mkil as $row) {
    // if($row['StatusID'] == 46) {
        echo '<!--begin::Modal-->  
        <div class="modal fade" tabindex="-1" id="uploadMkil-'. $row['WyStatus']. $row['WyID'] .'" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Muat Naik Surat Maklum Balas</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-warning ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <form class="modal-body" novalidate="novalidate" id="form-'. $row['WyStatus']. $row['WyID'] .'">

                        <!--begin::Dropzone-->
                        <div class="dropzone border-warning bg-light-warning mb-15" id="dropzone_upload_mkil">
                            <!--begin::Message-->
                            <div class= "dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-file-arrow-up text-warning fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik dokumen surat</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf sahaja</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <input type="text" id="sid-'. $row['WyStatus']. $row['WyID'] .'" name="system-id" value="'. $row['SysID'] .'" hidden/>
                            <input type="text" id="f-'. $row['WyStatus']. $row['WyID'] .'" name="folder" value="MKIL" hidden>
                            <input type="text" name="upload-mbkil" value="upload-mbkil" hidden>
                        </div>
                        <!--end::Dropzone-->
                        
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end my-5">
                            <button type="submit" id="submit-'. $row['WyStatus'] . $row['WyID'] .'" class="btn btn-warning">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fad fa-arrow-up-from-bracket"></i> Muatnaik</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">Sila Tunggu...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>
                        </div>
                        <!--end::Submit button-->
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->';
    // }
}
// }

?>