<?php 
$systemId = $_GET['sid'];
$dataLTA = wayleaveAmendments::getAttachmentLTA($systemId);
?>

<div class="flex-column flex-lg-row-auto w-lg-250px w-xl-300px mb-10 order-1 order-lg-2">
    <!--begin::Card-->
    <div class="card card-flush mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary"
        data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', xl: '300px'}"
        data-kt-sticky-left="auto" data-kt-sticky-top="100px" data-kt-sticky-animation="false"
        data-kt-sticky-zindex="95" style="">
        <!--begin::Card body-->
        <div class="card-body">
            <!--begin::Form-->
            <form class="modal-body" novalidate="novalidate" id="form_action_permit_checklist" data-route="approvalTPAmend">
                <!--begin::Actions-->
                <div class="mb-0">
                    <!--begin::Title-->
                    <h3 class="read-only text-justify text-gray-900 fs-4">
                        <span>PENGESAHAN SEMAKAN PINDAAN TP</span>
                    </h3>
                    <!--end::Title-->

                    <!--begin::Seperator-->
                    <div class="separator separator-dashed my-7"></div>
                    <!--end::Seperator-->

                    <h4 class="text-center text-gray-800 fs-5">
                        <?= $data->reference ?? "#$systemId" ?>
                    </h4>

                    <!--begin::Provider-->
                    <div class="mt-3">
                        <!--begin::Details-->
                        <div class="text-center">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-100px">
                                <img src="<?= $data->provider->logo ?>" alt="<?= $data->provider->name ?>" />
                            </div>
                            <h5 class="fw-semibold">
                                <?= !empty($data->provider->name) ? $data->provider->name : 'Tidak Diketahui' ?>
                            </h5>
                            <!--end::Avatar-->
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Provider-->

                    <!--begin::Seperator-->
                    <div class="separator separator-dashed my-7"></div>
                    <!--end::Seperator-->

                    <!--begin::Report Site Visit-->
                    <div class="mb-7">
                        <!--begin::Title-->
                        <h5 class="mb-3">Laporan Lawatan Tapak</h5>
                        <!--end::Title-->
                        <!--begin::Details-->
                        <div class="mb-0">
                            <h5 class="fw-semibold">
                                <?php
                                    if (empty($dataLTA)) {
                                        echo <<<HTML
                                            <div class="d-flex flex-column flex-center">
                                                <img src="assets/media/illustrations/empty/files.svg" class="mw-250px" />
                                                <div class="fs-2 fw-bolder text-dark">Tiada dokumen ditemui.</div>
                                            </div>
                                        HTML;
        
                                    } else {

                                        echo <<<HTML
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">
                                        HTML;
                                            $bytes = $dataLTA->size;
                                            if ($bytes >= 1024 * 1024) {
                                                $size = round($bytes / (1024 * 1024), 2) . ' MB';
                                            } else {
                                                $size = round($bytes / 1024, 2) . ' KB';
                                            }
                                                
                                            echo <<<TEMPLATE
                                                <tr>
                                                    <!--begin::Name=-->
                                                    <td>
                                                        <div class="d-flex align-items-center" data-bs-toggle="tooltip" data-bs-boundary="window"
                                                        data-bs-placement="top" title="$dataLTA->details">
                                                            <!--begin::Icon-->
                                                            <i class="fad fa-file-pdf fs-2x me-5"></i>
                                                            <!--end::Icon-->
                                                            <span class="text-gray-800 me-18">Laporan LTA</span>

                                                            <a href="#pdf-bkil-$dataLTA->id" data-fslightbox="lightbox-tp" data-class="fslightbox-source"
                                                            class="btn btn-icon btn-light btn-active-light-primary">
                                                            <span class="fad fa-eye fs-5"></span>
                                                        </a>
                                                        </div>
                                                    </td>
                                                    <!--end::Name=-->
                                                </tr>
                                            TEMPLATE;

                                        echo '</tbody>
                                        <!--end::Table body-->';

                                        echo <<<TEMPLATE
                                            <div style="display: none;">
                                                <div id="pdf-bkil-$dataLTA->id" style="height: 100vh; width: 95vw">
                                                    <iframe class="scroll w-100 h-100" src="/attachments/view?url=$dataLTA->url&mime=$dataLTA->mime_type ">
                                                    </iframe>
                                                </div>
                                            </div>
                                            <!--end::file-->
                                        TEMPLATE;
                                    }
                                ?>
                            </h5>
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Report Site Visit-->

                    <!--begin::Seperator-->
                    <div class="separator separator-dashed my-7"></div>
                    <!--end::Seperator-->

                    <!--begin::Title-->
                    <h5 class="required mb-5">Pengesahan Semakan </h5>
                    <!--end::Title-->
                    <!--begin::Details-->
                    <div class="mb-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-primary flex-grow-1 me-1" data-bs-toggle="modal" data-bs-target="#modal-approve-amend-tp">
                            <i class="fad fa-file-circle-check fs-4"></i>
                            <span class="d-none d-md-inline">Sahkan</span>
                        </button>
                    </div>
                    <!--end::Details-->
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>