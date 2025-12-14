<!--begin::Card-->
<div class="card">
    <!--begin::Card body-->
    <div class="card-body p-lg-10">
        <!--begin::Header-->
        <div class="mb-6">
            <!--begin::Title-->
            <h1 class="fs-1 text-gray-800 w-bolder mb-6">Senarai Lampiran</h1>
            <!--end::Title-->
            <div class="separator separator-dashed"></div>
        </div>
        <!--end::Header-->

        <?php if (empty($data)) { ?>
            <div class="d-flex flex-column flex-center">
                <img src="assets/media/illustrations/empty/files.svg" class="mw-250px" />
                <div class="fs-2 fw-bolder text-dark">Tiada dokumen ditemui.</div>
            </div>

            <?php } else { ?>
    
            <!--begin::Table-->
            <table id="kt_file_manager_list" data-kt-filemanager-table="folders" class="table align-middle table-row-dashed fs-6 gy-5">
                <!--begin::Table head-->
                <thead>
                    <!--begin::Table row-->
                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-250px">Nama</th>
                        <th class="min-w-10px">Saiz</th>
                        <th class="min-w-125px">Tarikh</th>
                        <th class="w-125px">Tindakan</th>
                    </tr>
                    <!--end::Table row-->
                </thead>
                <!--end::Table head-->
                <!--begin::Table body-->
                <tbody class="fw-semibold text-gray-600">
                    <?php
                        foreach($data as $index => $field) {
                            $created_date = (new DateTime($field->created_date))->format('d-m-Y H:m:s');

                            $bytes = $field->size;

                            if ($bytes >= 1024 * 1024 * 1024) {
                                $size = round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
                            }
                            else if ($bytes >= 1024 * 1024) {
                                $size = round($bytes / (1024 * 1024), 2) . ' MB';
                            } else {
                                $size = round($bytes / 1024, 2) . ' KB';
                            }

                            echo <<<TEMPLATE
                                <tr>
                                    <td class="text-uppercase">
                                        <div class="d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="$field->details">
                                            <i class="fad fa-file-pdf fs-2x me-4 btn-light-primary"></i>
                                            <span class="text-gray-800 fs-6 fw-semibold">$field->name</span>
                                        </div>
                                    </td>
                                    <td><span class="text-gray-800 fs-6 fw-semibold">$size</span></td>
                                    <td><span class="text-gray-800 fs-6 fw-semibold">$created_date</span></td>
                                    <td>
                                        <a href="#pdf-tracker-$index" data-fslightbox="lightbox" data-class="fslightbox-source" class="btn btn-sm btn-icon btn-light btn-active-light-primary fs-5" title="Lihat Dokumen" data-bs-toggle="tooltip" data-bs-placement="top">
                                            <span class="fad fa-eye fs-4 m-0"></span>
                                        </a>
                                    </td>
                                </tr>


                            TEMPLATE;
                        }
                    ?>
                </tbody>
                <!--end::Table body-->
            </table>
            <!--end::Table-->

            <!--start::Attachment-->
            <?php
                foreach($data as $index => $field) {
                    echo <<<TEMPLATE
                        <div style="display: none;">
                            <div id="pdf-tracker-$index">
                                <iframe class="scroll h-800px w-1000px" src="/attachments/view?url=$field->url&mime=$field->mime_type "></iframe>
                            </div>
                        </div>

                    TEMPLATE;
                }
            ?>
            <!--end::Attachment-->

        <?php }?>


    </div>
    <!--end::Card body-->
</div>
<!--begin::Card-->