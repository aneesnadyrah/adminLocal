<?php

foreach ($data as $item) {
    // var_dump($item->id);
    if ($item->status_id === 59) {
        echo '<!--begin::Modal-->
    <div class="modal fade" data-bs-backdrop="static" tabindex="-1" id="modal-review-' . $item->id . '" data-review-tmp="modal-review-' . $item->id . '" data-system-id="' . $item->system_id . '" data-id="' . $item->id . '">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Semakan Pelan Kawalan Trafik</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fad fa-xmark fs-2"></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form class="modal-body" novalidate="novalidate" id="form-review-tmp-' . $item->id . '">
                    <div class="w-100">
                        <!--begin::Input group-->
                        <div class="form mb-7 fv-row">
                            <div data-upload="SSPPT-' . $item->id . '" class="dropzone border-' . $item->color . ' bg-light-' . $item->color . '" data-type=".pdf">
                                <div class="dz-message needsclick"><i class="fad fa-file-arrow-up text-' . $item->color . ' fs-3x"></i>
                                    <div class="ms-4">
                                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">Leret ke sini atau klik di sini untuk muatnaik dokumen</h3>
                                        <span class="fs-7 fw-semibold text-gray-400">Sila Pilih Dokumen Senarai Semak Pelan Kawalan Trafik</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted fs-7 required mb-3"><em>Pastikan dokumen telah disatukan dalam bentuk format .pdf</em></div>
                        </div>
                        <!--end::Input group-->
                        <div class="form mb-7 fv-row fv-plugins-icon-container">
                            <div class="mb-5 col-md-12">
                                <label class="form-label required">Catatan</label>
                                <textarea class="form-control form-control-solid" name="review-tmp-remark"></textarea>
                            </div>
                        </div>
                        <!--begin::Submit button-->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-light btn-active-light-' . $item->color . ' me-3" id="submit-no-review-tmp-' . $item->id . '" >
                                <!--begin::Indicator label-->
                                <span class="indicator-label">Tidak</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">Sila Tunggu...
                                    <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>
                            <button type="submit" id="submit-confirm-review-tmp-' . $item->id . '" class="btn btn-' . $item->color . '">
                                <!--begin::Indicator label-->
                                <span class="indicator-label">Terima</span>
                                <!--end::Indicator label-->
                                <!--begin::Indicator progress-->
                                <span class="indicator-progress">Sila Tunggu...
                                    <span class="spinner-border sp inner-border-sm align-middle ms-2"></span>
                                </span>
                                <!--end::Indicator progress-->
                            </button>
                        </div>
                        <!--end::Submit button-->
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Modal-->';

    }
}
?>