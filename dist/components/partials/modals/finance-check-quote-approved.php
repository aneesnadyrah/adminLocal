<?php

foreach (Tasking::taskModal() as $id) {
    if ($id['StatusID'] == '032') {
        echo '<!--begin::Modal-->
            <div class="modal fade" tabindex="-1" id="action-' . $id['StatusID']. $id['ID'] . '">
                <div class="modal-dialog  mw-900px modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Semak Sebut Harga Disetujui</h3>

                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-' . $id['StatusColor'] . ' ms-2" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fad fa-xmark fs-2"></i>
                            </div>
                            <!--end::Close-->
                        </div>

                        <div class="modal-body">
                            <form class="modal-body" novalidate="novalidate" id="form-' . $id['StatusID']. $id['ID'] . '">';
                                $attach = Permitting::getQuoteApproval($id['SysID']);
                                // var_dump($attach);
                                foreach ($attach as $data2) {
                                    // Convert the string to a DateTime object
                                    $dateTime = new DateTime($data2['quote_approve_date']);
                                    $paid_at = $dateTime->format('j F Y');
                                echo'
                                <!--start::d-flex-->
                                <div class="d-flex flex-column flex-lg-row">
                                    <div class="flex-lg-row-fluid me-lg-7 me-xl-10">
                                        <iframe class="scroll h-400px w-450px rounded" src="/components/partials/widgets/print.php?f='.$data2['quote_approve_url'].' "></iframe>
                                    </div>
                                    <div class="flex-lg-auto min-w-lg-300px">
                                        <h6 class="mb-3 fw-bolder text-gray-600">BUTIRAN SEBUT HARGA</h6>

                                        <div class="mb-6">
                                            <div class="fw-semibold text-gray-600 fs-7">Tarikh Setuju :</div>
                                            <div class="fw-bold text-gray-800 fs-6">'.$paid_at.'</div>
                                        </div>';

                                }
                                echo'
                                        <div class="separator separator-dashed mb-7"></div>

                                        <h6 class="mb-3 fw-bolder text-gray-600">TINDAKAN</h6>

                                        <div class="form-check form-switch form-check-custom form-check-' . $id['StatusColor'] . ' form-check-solid fv-row d-flex justify-content-between mb-5">
                                            <label class="form-label fs-6" for="agree">
                                                Sahkan Sebut Harga?
                                            </label>
                                            <input class="form-check-input" type="checkbox" value="1" name="attach-agreed" id="toggleAgree"/>
                                        </div>

                                        <!--begin::Input group-->
                                        <div class="fv-row">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-semibold mb-2">Catatan</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <textarea class="form-control form-control-solid" rows="4" name="notes" placeholder="Catatan"></textarea>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                    </div>

                                </div>
                                <!--end::d-flex-->

                                <input type="text" id="system-id" name="system-id" value="' . $id['SysID'] . '" hidden>
                                <input type="text" name="quote-check" value="quote-check" hidden>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kembali</button>
                            <button type="submit" id="submit-' . $id['StatusID']. $id['ID'] . '"  class="btn btn-' . $id['StatusColor'] . '">
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

                    </div>
                </div>
            </div>
        <!--end::Modal-->';
    }



}
