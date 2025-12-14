<?php

// $RoleId = $_SESSION['roleId'];
// $Modal= new Modal($RoleId);

// var_dump($Modal->TaskTripleDate());

// foreach ($Modal->TaskTripleDate() as $row) {

        echo '<!--begin::Modal-->
        <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $row['StatusID'] . $row['ID'] . '">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">' . $title . '</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-' . $row['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate" id="form-' . $row['StatusID'] . $row['ID'] . '">
                        <!--begin::Input group-->
                        <div class="fv-row mb-2">
                            <!--begin::Dropzone-->
                            <div class="dropzone border-' . $row['StatusColor'] . ' bg-light-' . $row['StatusColor'] . '" id="add-attachment-' . $row['StatusID'] . $row['ID'] . '">
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
                                <input type="text" id="sid-' . $row['StatusID'] . $row['ID'] . '" name="system-id" value="' . $row['SysID'] . '" hidden>
                                <input type="text" id="f-' . $row['StatusID'] . $row['ID'] . '" name="folder" value="' . $fileId . '" hidden>
                                <input type="text" id="label-' . $row['StatusID'] . $row['ID'] . '" name="label-input" value="' . $label['dt1'] . '" hidden>
                                <input type="text" id="label2-' . $row['StatusID'] . $row['ID'] . '" name="label-input2" value="' . $label['dt2'] . '" hidden>
                                <input type="text" id="label3-' . $row['StatusID'] . $row['ID'] . '" name="label-input3" value="' . $label['dt3'] . '" hidden>
                            </div>
                            <!--end::Dropzone-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Description-->
                        <div class="text-muted fs-7 required mb-5"><em>Pastikan dokumen dalam bentuk format pdf</em></div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-5 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">' . $label['dateTitle1'] . '</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <div class="position-relative d-flex align-items-center">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                            <span class="symbol-label bg-transparent">
                                                <i class="fad fa-calendar fs-5"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Datepicker-->
                                        <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh" name="' . $label['dt1'] . '-' . $fileId . '" type="text" data-control="flatpickr">
                                        <!--end::Datepicker-->
                                    </div>
                                    <!--end::Input-->
                                </div>
                            </div>

                            <div class="col">
                                <div class="mb-5 fv-row">
                                    <!--begin::Label-->
                                    <label class="required form-label">' . $label['dateTitle2'] . '</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <div class="position-relative d-flex align-items-center">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                            <span class="symbol-label bg-transparent">
                                                <i class="fad fa-calendar fs-5"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Datepicker-->
                                        <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh" name="' . $label['dt2'] . '-' . $fileId . '2" type="text" data-control="flatpickr">
                                        <!--end::Datepicker-->
                                    </div>
                                    <!--end::Input-->
                                </div>
                            </div>
                        </div>

                        <div class="mb-5 fv-row">
                            <!--begin::Label-->
                            <label class="required form-label">' . $label['dateTitle3'] . '</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <div class="symbol symbol-20px me-4 position-absolute ms-4">
                                    <span class="symbol-label bg-transparent">
                                        <i class="fad fa-calendar fs-5"></i>
                                    </span>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Datepicker-->

                                <input class="form-control form-control-solid ps-12 flatpickr-input" placeholder="Sila Pilih Tarikh" name="' . $label['dt3'] . '-' . $fileId . '3" type="text" data-control="flatpickr"">
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>

                        <div class="mb-5 fv-row">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control form-control-solid" name="notes-' . $fileId . '" placeholder="Catatan"></textarea>
                        </div>
                        <!--end::Description-->
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" id="submit-' . $row['StatusID'] . $row['ID'] . '"  class="d-none btn btn-' . $row['StatusColor'] . '">
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
