<!--begin::Card Jalan-->
<div class="card card-flush pt-3 mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header">
        <!--begin::Card title-->
        <div class="card-title">
            <h2 class="fw-bold">Lampiran</h2>
        </div>
        <!--begin::Card title-->
    </div>
    <!--end::Card header-->
    <div class="separator separator-dashed"></div>
    <!--begin::Card body-->
    <div class="card-body">
        <!--begin::Section-->
        <div class="mb-0 table-responsive">
            <!--begin::Table-->
            <table id="kt_file_manager_list" data-kt-filemanager-table="files"
                class="table align-middle table-row-dashed fs-6 gy-5">
                <?php
                    if (empty($data)) {
                        echo <<<HTML
                            <div class="d-flex flex-column flex-center">
                                <img src="assets/media/illustrations/empty/files.svg" class="mw-250px" />
                                <div class="fs-2 fw-bolder text-dark">Tiada dokumen ditemui.</div>
                                <div class="fs-6">Kembalikan kepada pemohon jika fail dokumen tidak ditemui.
                                </div>
                            </div>
                        HTML;

                    } else {

                    echo <<<HTML
                    <!--begin::Table head-->
                    <thead>
                        <!--begin::Table row-->
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-250px">Nama</th>
                            <th class="min-w-10px">Saiz</th>
                            <th class="min-w-125px">Tarikh Dokumen</th>
                            <th>Lihat</th>
                        </tr>
                        <!--end::Table row-->
                    </thead>
                    <!--end::Table head-->
                    <!--begin::Table body-->
                    <tbody class="fw-semibold text-gray-600">
                    HTML;
                        foreach ($data as $index => $attachment) {
                        $bytes = $attachment->size;
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
                                    data-bs-placement="top" title="$attachment->details">
                                        <!--begin::Icon-->
                                        <i class="fad fa-file-pdf fs-2x me-5"></i>
                                        <!--end::Icon-->
                                        <span class="text-gray-800">
                                            $attachment->name
                                        </span>
                                    </div>
                                </td>
                                <!--end::Name=-->
                                <!--begin::Size-->
                                <td>
                                    <span class="text-gray-800">$size</span>
                                </td>
                                <!--end::Size-->
                                <!--begin::Last modified-->
                                <td>
                                    $attachment->created_date
                                </td>
                                <!--end::Last modified-->
                                <!--begin::Actions-->
                                <td class="text-end">
                                    <a href="#pdf-bkil-$index" data-fslightbox="lightbox" data-class="fslightbox-source"
                                        class="btn btn-icon btn-light btn-active-light-primary">
                                        <span class="fad fa-eye fs-5"></span>
                                    </a>
                                </td>
                                <!--end::Actions-->
                            </tr>
                        TEMPLATE;
                    }
                    echo '</tbody>
                    <!--end::Table body-->';
                }
                ?>
            </table>
            <!--end::Table-->
        </div>
        <!--end::Section-->

        <!--begin::file-->

        <?php
        foreach ($data as $index => $attachment) {
            echo <<<TEMPLATE
            <div style="display: none;">
                <div id="pdf-bkil-$index" style="height: 100vh; width: 95vw">
                    <iframe class="scroll w-100 h-100" src="/attachments/view?url=$attachment->url&mime=$attachment->mime_type ">
                    </iframe>
                </div>
            </div>
            <!--end::file-->
            TEMPLATE;
        }
        ?>
    </div>
</div>
<!--end::Card body-->