<?php

foreach (Tasking::taskModal() as $id) {
    if ($id['StatusID'] == '031') {
        echo '<!--begin::Modal-->
                    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $id['StatusID']. $id['ID'] . '">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Muatnaik Sebut Harga Disetujui</h3>

                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                        <i class="fad fa-xmark fs-2"></i>
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <form class="modal-body" novalidate="novalidate" id="form-' . $id['StatusID']. $id['ID'] . '">
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-2">
                                        <!--begin::Dropzone-->
                                        <div class="dropzone border-' . $id['StatusColor'] . ' bg-light-' . $id['StatusColor'] . '" id="add-attachment-' . $id['StatusID']. $id['ID'] . '">
                                            <!--begin::Message-->
                                            <div class= "dz-message needsclick">
                                                <!--begin::Icon-->
                                                <i class="fa-duotone fa-file-arrow-up text-' . $id['StatusColor'] . ' fs-3x"></i>
                                                <!--end::Icon-->
                                                <!--begin::Info-->
                                                <div class="ms-4">
                                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                                        Leret ke sini atau klik di sini untuk muatnaik dokumen</h3>
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Sebut Harga Disetujui</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <input type="text" id="sid-' . $id['StatusID']. $id['ID'] . '" name="system-id" value="' . $id['SysID'] . '" hidden>
                                            <input type="text" id="f-' . $id['StatusID']. $id['ID'] . '" name="folder" value="SHD" hidden>
                                        </div>
                                        <!--end::Dropzone-->
                                    </div>
                                    <!--end::Input group-->

                                    <!--begin::Description-->
                                    <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen SH Disetujui telah disatukan dalam bentuk format pdf</em></div>
                                    <!--end::Description-->
                                    <div class="form-check form-switch form-check-custom form-check-warning form-check-solid fv-row d-flex justify-content-between">
                                        <label class="form-check-label" for="agree">
                                            Andakah anda mengsahkan sebut harga ini?
                                        </label>
                                        <input class="form-check-input " type="checkbox" value="on" name="quote-agreed" id="agree"/>

                                    </div>
                                    <div class="my-5 fv-row">
                                        <label class="required form-label">Catatan</label>
                                        <textarea type="text" name="quote-verify-notes" class="form-control form-control-solid" placeholder="Catatan"></textarea>
                                    </div>
                                    <!--begin::Submit button-->
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" id="submit-' . $id['StatusID']. $id['ID'] . '"  class="d-none btn btn-' . $id['StatusColor'] . '">
                                            <!--begin::Indicator label-->
                                            <span class="indicator-label"><i class="fad fa-paper-plane"></i> Muat Naik</span>
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
    }
}
