<?php


foreach ($data as $item) {
    $surveyApi = new SurveyApi($username);
    $progress = intval(Survey::getProgressUDM($id->system_id));
    $surveyLength = $surveyApi->getSurveyLength($systemId);
    $progressUDM = intval(($progress / $surveyLength) * 100);
    
    echo '<!--begin::Modal-->
        <div class="modal fade" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">' . $item->title . '</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-' . $item->color . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>

                    <form class="modal-body" novalidate="novalidate" data-form="form-' . $item->id . '">
                        <!--begin::Progress-->
                        <div class="d-flex flex-column w-100 me-2 mb-7">
                            <div class="d-flex align-items-between mb-2">
                                <span class="text-muted me-20 fs-7 fw-bold">Progress Terkini ' . $progress . ' / ' . $surveyLength . ' m</span>
                                <span class="text-muted me-20 fs-7 fw-bold">' . $progressUDM . '%</span>
                            </div>
                            <div class="progress w-100" style="height: 10px;">
                                <div class="progress-bar ' . ($progressUDM <= 25 ? 'bg-danger' : ($progressUDM <= 50 ? 'bg-warning' : ($progressUDM <= 75 ? 'bg-info' : 'bg-success'))) . ' progress-bar-striped progress-bar-animated" role="progressbar" style="width: ' . $progressUDM . '%" aria-valuenow="' . $progressUDM . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!--end::Progress-->

                        <!--begin::Input group-->
                        <div class="input-group mb-7 fv-row">
                            <span class="input-group-text">Progress Hari Ini</span>
                            <input type="text" class="form-control" id="progress-daily-udm"  name="progress-daily-udm" aria-describedby="progress-daily-udm"/>
                            <input type="text" name="progress-udm" value="' . $progress . '" hidden />
                            <input type="text" name="length"  value="'. $surveyLength .'" hidden>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" data-submit="submit-' . $item->id . '" class="btn btn-' . $item->color . '">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fas fa-check"></i> Sahkan</span>
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
    <!--end::Modal-->'
    ; 
    
}

?>