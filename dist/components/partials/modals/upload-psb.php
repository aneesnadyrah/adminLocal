<?php

// foreach (taskModal() as $row) {
    echo '<!--begin::Modal-->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Pelan Seni Bina</h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-' . $row['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form class="modal-body" novalidate="novalidate" id="formSurvey-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'">
                    <!--begin::Dropzone-->
                        <div class="dropzone border-primary bg-light-primary mb-7 pdf-dropzone">
                            <!--begin::Message-->
                            <div class= "dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-file-arrow-up text-primary fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .pdf sahaja</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <input type="text" id="sid-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'" name="system-id" value="'. $row['system_id'] .'" hidden/>
                            <input type="text" id="f1-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'" name="folder" value="PSB" hidden>

                        </div>
                        <!--end::Dropzone-->
                        <!--begin::Dropzone-->
                        <div class="dropzone border-success bg-light-success mb-7 dwg-dropzone">
                            <!--begin::Message-->
                            <div class= "dz-message needsclick">
                                <!--begin::Icon-->
                                <i class="fa-duotone fa-compass-drafting text-success fs-3x"></i>
                                <!--end::Icon-->
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">
                                        Leret ke sini atau klik di sini untuk muatnaik fail</h3>
                                    <span class="fs-7 fw-semibold text-gray-400">Sila pilih fail berformat .dwg sahaja</span>
                                </div>
                                <!--end::Info-->
                            </div>
                            <input type="text" id="sid-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'" name="system-id" value="'. $row['system_id'] .'" hidden/>
                            <input type="text" id="f2-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'" name="folder" value="CDPSB" hidden>

                        </div>
                        <!--end::Dropzone-->

                        <div class="mb-5 fv-row">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control form-control-solid" name="notes-CDPSB" placeholder="Catatan"></textarea>
                        </div>

                    <!--begin::Submit button-->
                    <div class="d-flex justify-content-end">
                        <button type="submit" id="submit-' . $row['subMappingID'] . $row['MappingID']. $row['ID'] .'"  class="d-none btn btn-' . $row['StatusColor'] . '">
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
