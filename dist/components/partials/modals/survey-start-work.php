<?php

foreach (Survey::SurveyNotesModal() as $id) {
    // $note = getSurveyNotes($id['system_id']);
    // echo ($id['system_id']);
    echo '<!--begin::Modal-->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="action-survey-start-work-' . $id['system_id'] . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Semakan Kelulusan Mula Kerja Ukur</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form class="modal-body" novalidate="novalidate" id="form-survey-start-work-' . $id['id'] . '">
                    <!--begin::Input group-->
                    <div class="form mb-7 fv-row">
                        <p>Catatan daripada pengawai ukur untuk memulakan kerja :</p>
                        <div class="note-description" style="padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9; border-radius: 5px;">' . Survey::getSurveyNotes($id['system_id']) . '</div>
                    </div>
                    <!--end::Input group-->
                    <div class="form mb-7 fv-row fv-plugins-icon-container">
                            <label class="form-label">Catatan Kelulusan Mula Kerja</label>
                            <textarea type="text" name="finance-remark" id="finance-remark" class="form-control" placeholder="Sila Isi Catatan Disini..." rows="2"></textarea>
                            <input type="text" name="system-id" id="system-id" value="' . $id['system_id'] . '" hidden>
                            <input type="text" name="id" id="id" value="' . $id['id'] . '" hidden>
                        </div>
                    <!--begin::Submit button-->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-light btn-active-light-success me-3" id="submit-no-start-survey-' . $id['id'] . '" >
                            <!--begin::Indicator label-->
                            <span class="indicator-label">Tidak</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
                            </span>
                            <!--end::Indicator progress-->
                        </button>
                        <button type="submit" id="submit-confirm-start-survey-' . $id['id'] . '" class="btn btn-success">
                            <!--begin::Indicator label-->
                            <span class="indicator-label">Ya</span>
                            <!--end::Indicator label-->
                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">Sila Tunggu...
                                <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
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
?>
