<?php
// var_dump($abc);
foreach ($wayleave_kil as $index => $row) {
    if (isset($row['WyStatus']) && $row['WyStatus'] == 1) { 
        $datepickerId = 'datepicker-' . $row['WyStatus'] . $row['ID']; ?>

        <!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-<?php echo $row['WyStatus']. $row['ID'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Muat Naik Bukti Penghantaran</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" id="form-<?php echo $row['WyStatus']. $row['ID'] ?>">
                        <!--begin::Input group-->
                        <div class="fv-row mb-2">
                            <!--begin::Dropzone-->
                            <div class="dropzone border-primary bg-light-primary" id="add-attachment-<?php echo $row['WyStatus']. $row['ID'] ?>">
                                <!--begin::Message-->
                                <div class= "dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                    <!--end::Icon-->
                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                            Leret ke sini atau klik di sini untuk muat naik dokumen</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Fail</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <input type="text" id="sid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="system-id" value="<?php echo $row['SysID'] ?>" hidden>
                                <input type="text" id="wyid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="wy-id" value="<?php echo $row['ID'] ?>" hidden>
                                <input type="text" id="authorityname-<?php echo $row['WyStatus']. $row['ID'] ?>" name="authority-name" value="<?php echo $row['Authority'] ?>" hidden>
                                <input type="text" id="authorityid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="authority-id" value="<?php echo $row['AuthorityID'] ?>" hidden>
                                <input type="text" id="fid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="folder" value="ASPKIL" hidden>
                                <input type="text" name="item" value="send-kil" hidden>
                                <input type="text" name="statusId" value="<?php echo $row['Status'] ?>" hidden>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen telah disatukan dalam bentuk format pdf</em></div>
                        <div class="mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Tarikh Hantar Surat Kelulusan Izin Lalu</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                    <span class="symbol-label bg-secondary">
                                        <i class="fad fa-calendar"></i>
                                    </span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Datepicker-->

                                <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh" name="dt-send-wayleave" type="text" readonly="readonly" fdprocessedid="0bowoa" data-datepicker-id="<?php echo $datepickerId ?>">
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Description-->
                        
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="submit-<?php echo $row['WyStatus']. $row['ID'] ?>"  class="d-none btn btn-primary">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>
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
        <!--end::Modal-->
    <?php
    } else if (isset($row['WyStatus']) && $row['WyStatus'] == 2) {
        $datepickerId = 'datepicker-' . $row['WyStatus'] . $row['ID']; ?>

        <!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-<?php echo $row['WyStatus']. $row['ID'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Muat Naik Surat Kelulusan Izin Lalu</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-info ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" id="form-<?php echo $row['WyStatus']. $row['ID'] ?>">
                        <!--begin::Input group-->
                        <div class="fv-row mb-2">
                            <!--begin::Dropzone-->
                            <div class="dropzone border-info bg-light-info" id="add-attachment-<?php echo $row['WyStatus']. $row['ID'] ?>">
                                <!--begin::Message-->
                                <div class= "dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="fa-duotone fa-file-arrow-up text-info fs-3x"></i>
                                    <!--end::Icon-->
                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                            Leret ke sini atau klik di sini untuk muat naik dokumen</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Surat Kelulusan Izin Lalu</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <input type="text" id="sid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="system-id" value="<?php echo $row['SysID'] ?>" hidden>
                                <input type="text" id="wyid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="wy-id" value="<?php echo $row['ID'] ?>" hidden>
                                <input type="text" id="authorityname-<?php echo $row['WyStatus']. $row['ID'] ?>" name="authority-name" value="<?php echo $row['Authority'] ?>" hidden>
                                <input type="text" id="authorityid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="authority-id" value="<?php echo $row['AuthorityID'] ?>" hidden>
                                <input type="text" id="fid-<?php echo $row['WyStatus']. $row['ID'] ?>" name="folder" value="SKIL" hidden>
                                <input type="text" name="item" value="receive-kil" hidden>
                                <input type="text" name="statusId" value="<?php echo $row['Status'] ?>" hidden>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen telah disatukan dalam bentuk format pdf</em></div>
                        <div class="mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Tarikh Surat Kelulusan Izin Lalu</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                    <span class="symbol-label bg-secondary">
                                        <i class="fad fa-calendar"></i>
                                    </span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Datepicker-->

                                <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh" name="dt-approval-wayleave" type="text" readonly="readonly" fdprocessedid="0bowoa" data-datepicker-id="<?php echo $datepickerId ?>">
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <div class="mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">Tarikh Terima Surat Diterima</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                    <span class="symbol-label bg-secondary">
                                        <i class="fad fa-calendar"></i>
                                    </span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Datepicker-->

                                <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh" name="dt-receive-wayleave" type="text" readonly="readonly" fdprocessedid="0bowoa" data-datepicker-id="<?php echo $datepickerId ?>">
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Description-->
                        
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="submit-<?php echo $row['WyStatus']. $row['ID'] ?>"  class="d-none btn btn-info">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fad fa-paper-plane"></i> Hantar</span>
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
        <!--end::Modal-->
<?php
    } 
}
?>