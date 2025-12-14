<?php

// foreach (taskModal() as $row) {
    echo '<!--begin::Modal-->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $row['StatusID']. $row['ID'] . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Bukti Pembayaran Caj Perkhidmatan</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-' . $row['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form class="modal-body" novalidate="novalidate" id="form-' . $row['StatusID']. $row['ID'] . '">
                    <!--begin::Input group-->
                    <div class="fv-row mb-2">
                        <!--begin::Dropzone-->
                        <div class="dropzone border-' . $row['StatusColor'] . ' bg-light-' . $row['StatusColor'] . '" id="add-attachment-' . $row['StatusID']. $row['ID'] . '">
                            <!--begin::Message-->
                            <div class= "dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-file-arrow-up text-' . $row['StatusColor'] . ' fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik dokumen</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <input type="text" id="sid-' . $row['StatusID']. $row['ID'] . '" name="system-id" value="' . $row['SysID'] . '" hidden>
                            <input type="text" id="f-' . $row['StatusID']. $row['ID'] . '" name="folder" value="RCPP" hidden>
                        </div>
                        <!--end::Dropzone-->
                    </div>
                    <!--end::Input group-->
                    <!--begin::Description-->
                    <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen dalam bentuk format pdf</em></div>

                    <div class="col-12 fs-6 fv-row mb-5">
                        <label class="required form-label mb-2">Kaedah Pembayaran</label>
                        <select class="form-select form-select-solid fs-6" data-control="select2" data-placeholder="Status" data-hide-search="true" name="payment-method-inv">
                            <option value="Pemindahan_Bank_Segera" selected="selected">Pemindahan Bank Segera</option>
                            <option value="Jaminan_bank">Jaminan bank</option>
                            <option value="Cek">Cek</option>
                            <option value="Tunai">Tunai</option>
                        </select>
                    </div>
                    <div class="row">

                        <div class="col">
                            <div class="mb-5 fv-row">
                                <label class="required form-label">Jumlah Bayaran (RM)</label>
                                <input type="text" class="form-control form-control-solid" name="total-rcpp" placeholder="Sila isi jumlah bayaran"></input>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5 fv-row">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control form-control-solid" name="notes-icpp" placeholder="Catatan"></textarea>
                    </div>
                    <!--end::Description-->
                    <!--begin::Submit button-->
                    <div class="d-flex justify-content-end">
                        <button type="submit" id="submit-' . $row['StatusID']. $row['ID'] . '"  class="d-none btn btn-' . $row['StatusColor'] . '">
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
    <!--end::Modal-->';

// }
