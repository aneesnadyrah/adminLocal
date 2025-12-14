<?php 
$systemId = $_GET['id'] ?? NULL; 
$data_bkil = Details::getWorkflowDetails($systemId, 'way_leave');
// $count_bkil = Details::getCountWorkflow($systemId, 'way_leave');

$data_permit = Details::getWorkflowDetails($systemId, 'work_permit');
// $count_permit = Details::getCountWorkflow($systemId, 'work_permit');

$data_finish = Details::getWorkflowDetails($systemId, 'work_finish');
// $count_finish = Details::getCountWorkflow($systemId, 'work_finish');

$data_defect = Details::getWorkflowDetails($systemId, 'work_defect');
// $count_defect = Details::getCountWorkflow($systemId, 'work_defect');

?>

<!--begin::Aliran Kerja-->
<!--begin::Row-->
<div class="row g-9">
    <!--begin::col kelulusan izin lalu -->
    <div class="col-md-4 col-lg-12 col-xl-4">
        <!--begin::Col header-->
        <div class="mb-9">
            <div class="d-flex flex-stack">
                <div class="fw-bold fs-4">Kelulusan Izin Lalu</div>
            </div>
            <div class="h-3px w-100 bg-warning"></div>
        </div>
        <!--end::Col header-->

        <!--begin::Card-->
        <div class="card mb-6 mb-xl-9">
            <!--begin::Card body-->
            <div class="card-body">

                <!--begin::Scroll-->
                <div class="tab-content hover-scroll-overlay-y pe-1 me-3 mb-2" style="height: 350px">
                    <!--begin::Table Container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                                <tr>                            
                                    <th class="p-0 min-w-200px"></th>
                                    <th class="p-0 min-w-100px"></th>    
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php 
                                // foreach($data_bkil as $row) { 
                                foreach($data_bkil as $index => $row) { 
                                    if ($row->badge_status === 'Selesai') {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-success">Selesai</span>';
                                    } else {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-danger">Belum Selesai</span>';
                                    } 

                                    if(!empty($row->attachment_date)) {
                                        $date_attach = General::convertDate(date($row->attachment_date));
                                    } else {
                                        $date_attach = '';
                                    }
                                    
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold me-2 fs-6"><?php echo $row->details ?></div>
                                                    <!--end::Info-->
                                                </div>
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-muted me-2 fs-7"><?php echo $date_attach ?></div>
                                                    <span class="text-gray-400 fw-bold fs-7 d-block"><?php echo $badgeClass  ?></span>
                                                </div>
                                            </div>

                                            <?php if(!empty($row->url)) { ?>
                                                <div class="align-items-center border border-dashed border-gray-300 rounded w-300px p-3">
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Item-->
                                                        <div class="d-flex flex-aligns-center">
                                                            <!--begin::Icon-->
                                                            <img alt="" class="w-30px me-3" src="assets/media/files/pdf.svg">
                                                            <!--end::Icon-->
                                                            <!--begin::Info-->
                                                            <div class="ms-1 fs-8 mt-2">
                                                                <!--begin::Desc-->
                                                                <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="lightbox-flw-wy" data-class="fslightbox-source" href="#pdf-flw-wy-<?php echo $index ?>">
                                                                    <div class="text-gray-400"><?php echo $row->name ?></div>
                                                                </a>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--begin::Info-->
                                                        </div>
                                                        <!--end::Item-->
                                                        <!--start::PDF-->
                                                        <div style="display: none;">
                                                            <div id="pdf-flw-wy-<?php echo $index?>">
                                                                <iframe class="scroll h-800px w-1000px" src="/attachments/view?file=<?php echo $row->url ?>&mime=<?php echo $row->mime_type ?> "></iframe>
                                                            </div>
                                                        </div>
                                                        <!--end::PDF-->
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                            <!--end::Table body-->
                    </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table Container-->
                </div>
                <!--end::Scroll-->

            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!-- end::col kelulusan izin lalu -->

    <!--begin::col permit kerja -->
    <div class="col-md-4 col-lg-12 col-xl-4">
        <!--begin::Col header-->
        <div class="mb-9">
            <div class="d-flex flex-stack">
                <div class="fw-bold fs-4">Kelulusan Permit Kerja</div>
            </div>
            <div class="h-3px w-100 bg-warning"></div>
        </div>
        <!--end::Col header-->

        <!--begin::Card-->
        <div class="card mb-6 mb-xl-9">
            <!--begin::Card body-->
            <div class="card-body">

                <!--begin::Scroll-->
                <div class="tab-content hover-scroll-overlay-y pe-1 me-3 mb-2" style="height: 350px">
                    <!--begin::Table Container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                                <tr>                            
                                    <th class="p-0 min-w-200px"></th>
                                    <th class="p-0 min-w-100px"></th>    
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php 
                                foreach($data_permit as $index => $row) { 
                                    if ($row->badge_status === 'Selesai') {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-success">Selesai</span>';
                                    } else {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-danger">Belum Selesai</span>';
                                    } 

                                    if(!empty($row->attachment_date)) {
                                        $date_attach = General::convertDate(date($row->attachment_date));
                                    } else {
                                        $date_attach = '';
                                    }
                                    
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold me-2 fs-6"><?php echo $row->details ?></div>
                                                    <!--end::Info-->
                                                </div>
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-muted me-2 fs-7"><?php echo $date_attach ?></div>
                                                    <span class="text-gray-400 fw-bold fs-7 d-block"><?php echo $badgeClass  ?></span>
                                                </div>
                                            </div>

                                            <?php if(!empty($row->url)) { ?>
                                                <div class="align-items-center border border-dashed border-gray-300 rounded w-300px p-3">
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Item-->
                                                        <div class="d-flex flex-aligns-center">
                                                            <!--begin::Icon-->
                                                            <img alt="" class="w-30px me-3" src="assets/media/files/pdf.svg">
                                                            <!--end::Icon-->
                                                            <!--begin::Info-->
                                                            <div class="ms-1 fs-8 mt-2">
                                                                <!--begin::Desc-->
                                                                <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="lightbox-flw-pk" data-class="fslightbox-source" href="#pdf-flw-pk-<?php echo $index ?>">
                                                                    <div class="text-gray-400"><?php echo $row->name ?></div>
                                                                </a>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--begin::Info-->
                                                        </div>
                                                        <!--end::Item-->
                                                        <!--start::PDF-->
                                                        <div style="display: none;">
                                                            <div id="pdf-flw-pk-<?php echo $index?>">
                                                                <iframe class="scroll h-800px w-1000px" src="/attachments/view?file=<?php echo $row->url ?>&mime=<?php echo $row->mime_type ?> "></iframe>
                                                            </div>
                                                        </div>
                                                        <!--end::PDF-->
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                            <!--end::Table body-->
                    </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table Container-->
                </div>
                <!--end::Scroll-->

            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!-- end::col permit kerja -->

    <!--begin::col siap kerja -->
    <div class="col-md-4 col-lg-12 col-xl-4">
        <!--begin::Col header-->
        <div class="mb-9">
            <div class="d-flex flex-stack">
                <div class="fw-bold fs-4">Kelulusan Siap Kerja</div>
            </div>
            <div class="h-3px w-100 bg-warning"></div>
        </div>
        <!--end::Col header-->

        <!--begin::Card-->
        <div class="card mb-6 mb-xl-9">
            <!--begin::Card body-->
            <div class="card-body">

                <!--begin::Scroll-->
                <div class="tab-content hover-scroll-overlay-y pe-1 me-3 mb-2" style="height: 350px">
                    <!--begin::Table Container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                                <tr>                            
                                    <th class="p-0 min-w-200px"></th>
                                    <th class="p-0 min-w-100px"></th>    
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php 
                                foreach($data_finish as $index => $row) { 
                                    if ($row->badge_status === 'Selesai') {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-success">Selesai</span>';
                                    } else {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-danger">Belum Selesai</span>';
                                    } 

                                    if(!empty($row->attachment_date)) {
                                        $date_attach = General::convertDate(date($row->attachment_date));
                                    } else {
                                        $date_attach = '';
                                    }
                                    
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold me-2 fs-6"><?php echo $row->details ?></div>
                                                    <!--end::Info-->
                                                </div>
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-muted me-2 fs-7"><?php echo $date_attach ?></div>
                                                    <span class="text-gray-400 fw-bold fs-7 d-block"><?php echo $badgeClass  ?></span>
                                                </div>
                                            </div>

                                            <?php if(!empty($row->url)) { ?>
                                                <div class="align-items-center border border-dashed border-gray-300 rounded w-300px p-3">
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Item-->
                                                        <div class="d-flex flex-aligns-center">
                                                            <!--begin::Icon-->
                                                            <img alt="" class="w-30px me-3" src="assets/media/files/pdf.svg">
                                                            <!--end::Icon-->
                                                            <!--begin::Info-->
                                                            <div class="ms-1 fs-8 mt-2">
                                                                <!--begin::Desc-->
                                                                <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="lightbox-flw-sk" data-class="fslightbox-source" href="#pdf-flw-sk-<?php echo $index ?>">
                                                                    <div class="text-gray-400"><?php echo $row->name ?></div>
                                                                </a>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--begin::Info-->
                                                        </div>
                                                        <!--end::Item-->
                                                        <!--start::PDF-->
                                                        <div style="display: none;">
                                                            <div id="pdf-flw-sk-<?php echo $index?>">
                                                                <iframe class="scroll h-800px w-1000px" src="/attachments/view?file=<?php echo $row->url ?>&mime=<?php echo $row->mime_type ?> "></iframe>
                                                            </div>
                                                        </div>
                                                        <!--end::PDF-->
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                            <!--end::Table body-->
                    </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table Container-->
                </div>
                <!--end::Scroll-->

            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!-- end::col siap kerja -->

    <!--begin::col sempurna kerja -->
    <div class="col-md-4 col-lg-12 col-xl-4">
        <!--begin::Col header-->
        <div class="mb-9">
            <div class="d-flex flex-stack">
                <div class="fw-bold fs-4">Kelulusan Sempurna Kerja</div>
            </div>
            <div class="h-3px w-100 bg-warning"></div>
        </div>
        <!--end::Col header-->

        <!--begin::Card-->
        <div class="card mb-6 mb-xl-9">
            <!--begin::Card body-->
            <div class="card-body">

                <!--begin::Scroll-->
                <div class="tab-content hover-scroll-overlay-y pe-1 me-3 mb-2" style="height: 350px">
                    <!--begin::Table Container-->
                    <div class="table-responsive">
                        <!--begin::Table-->
                        <table class="table align-middle gs-0 gy-4">
                            <!--begin::Table head-->
                            <thead>
                                <tr>                            
                                    <th class="p-0 min-w-200px"></th>
                                    <th class="p-0 min-w-100px"></th>    
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody>
                                <?php 
                                foreach($data_defect as $index => $row) { 
                                    if ($row->badge_status === 'Selesai') {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-success">Selesai</span>';
                                    } else {
                                        $badgeClass = '<span data-kt-element="status" class="badge badge-light-danger">Belum Selesai</span>';
                                    } 

                                    if(!empty($row->attachment_date)) {
                                        $date_attach = General::convertDate(date($row->attachment_date));
                                    } else {
                                        $date_attach = '';
                                    }
                                    
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="mb-2">
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold me-2 fs-6"><?php echo $row->details ?></div>
                                                    <!--end::Info-->
                                                </div>
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-muted me-2 fs-7"><?php echo $date_attach ?></div>
                                                    <span class="text-gray-400 fw-bold fs-7 d-block"><?php echo $badgeClass  ?></span>
                                                </div>
                                            </div>

                                            <?php if(!empty($row->url)) { ?>
                                                <div class="align-items-center border border-dashed border-gray-300 rounded w-300px p-3">
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Item-->
                                                        <div class="d-flex flex-aligns-center">
                                                            <!--begin::Icon-->
                                                            <img alt="" class="w-30px me-3" src="assets/media/files/pdf.svg">
                                                            <!--end::Icon-->
                                                            <!--begin::Info-->
                                                            <div class="ms-1 fs-8 mt-2">
                                                                <!--begin::Desc-->
                                                                <a class="text-gray-800 text-hover-primary d-flex flex-column" data-fslightbox="lightbox-flw-d" data-class="fslightbox-source" href="#pdf-flw-d-<?php echo $index ?>">
                                                                    <div class="text-gray-400"><?php echo $row->name ?></div>
                                                                </a>
                                                                <!--end::Desc-->
                                                            </div>
                                                            <!--begin::Info-->
                                                        </div>
                                                        <!--end::Item-->
                                                        <!--start::PDF-->
                                                        <div style="display: none;">
                                                            <div id="pdf-flw-d-<?php echo $index?>">
                                                                <iframe class="scroll h-800px w-1000px" src="/attachments/view?file=<?php echo $row->url ?>&mime=<?php echo $row->mime_type ?> "></iframe>
                                                            </div>
                                                        </div>
                                                        <!--end::PDF-->
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                            <!--end::Table body-->
                    </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table Container-->
                </div>
                <!--end::Scroll-->

            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!-- end::col sempurna kerja -->

</div>
<!--end::Row-->

<!--end::Aliran Kerja-->