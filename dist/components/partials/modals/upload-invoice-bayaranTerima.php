<?php

foreach (Tasking::taskModal() as $id) {
    if ($id['StatusID'] == '048') {
        echo '<!--begin::Modal-->
                    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $id['StatusID']. $id['ID'] . '">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Muatnaik Invois Bayaran Terima</h3>

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
                                                    <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Invois Bayaran Terima</span>
                                                </div>
                                                <!--end::Info-->
                                            </div>
                                            <input type="text" id="sid-' . $id['StatusID']. $id['ID'] . '" name="system-id" value="' . $id['SysID'] . '" hidden>
                                            <input type="text" id="f-' . $id['StatusID']. $id['ID'] . '" name="folder" value="RCPP" hidden>
                                        </div>
                                        <!--end::Dropzone-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Description-->
                                    <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen IBT telah disatukan dalam bentuk format pdf</em></div>
                                    <!--end::Description-->
                                    <div class="col-12 fs-6 fv-row mb-5">
                                        <label class="required form-label mb-2">Kaedah Pembayaran</label>
                                        <select class="form-select form-select-solid fs-6" data-control="select2" data-placeholder="Status" data-hide-search="true" name="payment_method">
                                            <option value="Pemindahan_Bank_Segera" selected="selected">Pemindahan Bank Segera</option>
                                            <option value="Jaminan_bank">Jaminan bank</option>
                                            <option value="Cek">Cek</option>
                                            <option value="Tunai">Tunai</option>
                                        </select>
                                    </div>
                                    <div class="mb-5 fv-row">
                                        <label class="required form-label">Jumlah Amaun Bayaran Invois (RM)</label>
                                        <input type="text" name="total-inv-pay" class="form-control form-control-solid" placeholder="Sila Isi Amaun Bayaran Invois"/>
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
