<?php
// $option = '';
// foreach (General::selection('ls_authorities') as $row) {
//     $option .= '<option value="'. $row['id'] .'">' . $row['name'] . '</option>';
// }

foreach ($data as $item) {
    $timestamp = strtotime($item->calendar_sv_date);
    $formattedDate = date('d M Y', $timestamp);
    $formattedTime = date('h:iA', $timestamp);
        echo '<!--begin::Modal-->
        <div class="modal fade" tabindex="-1" id="modal-' . $item->id . '" data-modal="' . $item->id . '" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">'.$item->title .'</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-'. $item->color  . ' ms-auto" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fad fa-xmark fs-2"></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form class="modal-body" novalidate="novalidate"data-form="form-' . $item->id . '">
                        <div class="mb-5">
                            <!--begin::List-->
                            <div name="calendar-sv-list" class="mh-375px scroll-y me-n7 pe-7">

                            <div class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                    <div class="d-flex align-items-center me-auto">
                                        <div class="symbol symbol-35px">
                                        <img alt="Pic" src="assets/media/authorities/' . $item->authority_logo . '.png">
                                        </div>
                                        <div class="ms-6">
                                        <a class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">' . $item->authority_name . '
                                            <!--
                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Art Director</span>
                                            -->
                                        </a>
                                        <div class="fw-semibold text-muted">' . $item->ReportNo . '</div>
                                        </div>
                                    </div>
                                    <div class="d-flex ms-auto">
                                        <div class="text-end">
                                        <div class="fs-5 fw-bold text-dark">' . $formattedDate . '</div>
                                        <div class="fs-7 text-dark">' . $formattedTime . '</div>
                                        </div>
                                    </div>
                                    </div>
                                    <!-- TODO::List convert this according to new structure-->
                                    <input type="text" hidden name="systemId" value="'.$item->system_id.'"/>
                                    <input type="text" hidden name="authorityId" value="'.$item->authority_id.'"/>
                                    <input type="text" hidden name="authorityName" value="'.$item->authority_name.'"/>
                                    <input type="text" hidden name="notes" value="Tetapan Tarikh Lawatan Tapak disahkan."/>

    </div>
                            <!--end::List-->
                        </div>
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" data-submit="submit-'. $item->id .'"  class="btn btn-'. $item->color  . '">
                                <!--begin::Indicator label-->
                                <span class="indicator-label"><i class="fad fa-calendar-check"></i> Sahkan</span>
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

?>