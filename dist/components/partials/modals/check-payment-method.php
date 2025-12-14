<?php

foreach (Tasking::taskModal() as $id) {
    if ($id['StatusID'] == '002') { ?>
        <!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-<?php echo $id['StatusID'].''.$id['ID'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Muat Naik Arahan Kerja PIL</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-<?php echo $id['StatusColor'] ?> ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" id="form-<?php echo $id['StatusID'].''.$id['ID'] ?>">
                        <!--begin::Input group-->
                        <div class="fv-row mb-2">
                            <!--begin::Dropzone-->
                            <div class="dropzone border-<?php echo $id['StatusColor'] ?> bg-light-<?php echo $id['StatusColor'] ?>" id="add-attachment-<?php echo $id['StatusID'].''.$id['ID'] ?>">
                                <!--begin::Message-->
                                <div class= "dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="fa-duotone fa-file-arrow-up text-<?php echo $id['StatusColor'] ?> fs-3x"></i>
                                    <!--end::Icon-->
                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                            Leret ke sini atau klik di sini untuk muat naik dokumen</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Arahan Kerja</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <input type="text" id="sid-<?php echo $id['StatusID'].''.$id['ID'] ?>" name="system-id" value="<?php echo $id['SysID'] ?>" hidden>
                                <input type="text" id="f-<?php echo $id['StatusID'].''.$id['ID'] ?>" name="folder" value="AKP" hidden>
                                <input type="text" name="payment_method" value="1" hidden>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen AK telah disatukan dalam bentuk format pdf</em></div>
                        <div class="mb-5 fv-row">
                            <label class="required form-label">Jumlah Nilai Arahan Kerja (RM)</label>
                            <input type="text" name="total-wop" class="form-control form-control-solid" placeholder="Sila Isi Nilai Arahan Kerja"/>
                        </div>
                        <!--end::Description-->
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="submit-<?php echo $id['StatusID'].''.$id['ID'] ?>"  class="d-none btn btn-<?php echo $id['StatusColor'] ?>">
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
    } else if (($id['StatusID'] == '996') && $id['PaymentMethod'] == '2') {
        ?>
        <!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-<?php echo $id['StatusID'].''.$id['ID'] ?>">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Muat Naik Invois Caj Pendaftaran</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-<?php echo $id['StatusColor'] ?> ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" id="form-<?php echo $id['StatusID'].''.$id['ID'] ?>">
                        <!--begin::Input group-->
                        <div class="fv-row mb-2">
                            <!--begin::Dropzone-->
                            <div class="dropzone border-<?php echo $id['StatusColor'] ?> bg-light-<?php echo $id['StatusColor'] ?>" id="add-attachment-<?php echo $id['StatusID'].''.$id['ID'] ?>">
                                <!--begin::Message-->
                                <div class= "dz-message needsclick">
                                    <!--begin::Icon-->
                                    <i class="fa-duotone fa-file-arrow-up text-<?php echo $id['StatusColor'] ?> fs-3x"></i>
                                    <!--end::Icon-->
                                    <!--begin::Info-->
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                            Leret ke sini atau klik di sini untuk muat naik dokumen</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf sahaja</span>
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <input type="text" id="sid-<?php echo $id['StatusID'].''.$id['ID'] ?>" name="system-id" value="<?php echo $id['SysID'] ?>" hidden/>
                                <input type="text" id="f-<?php echo $id['StatusID'].''.$id['ID'] ?>" name="folder" value="ICP" hidden>
                                <input type="text" name="payment_method" value="2" hidden>
                                <input type="text" name="item-upload" value="action-upload-invoice" hidden>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 required mb-2"><em>Pastikan dokumen invois telah disatukan dalam bentuk format pdf</em></div>
                        <!--end::Description-->

                        <!--begin::Input group-->
                        <div class="fv-row mt-7 mb-7">
                            <!--begin::Label-->
                            <label class="fs-6 fw-semibold mb-2">Catatan</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" rows="4" name="notes" placeholder="Catatan"></textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <input type="text" id="system-id" name="system-id" value="<?php echo $id['SysID'] ?>" hidden>
                        <input type="text" name="payment-method" value="payment-method" hidden>

                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="submit-<?php echo $id['StatusID'].''.$id['ID'] ?>"  class="d-none btn btn-<?php echo $id['StatusColor'] ?>">
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
