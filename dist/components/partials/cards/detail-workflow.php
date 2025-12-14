<?php 
/**
 * Unified Project Details Workflow
 * Uses the new unified ProjectDetails class with auto-detection
 * Updated to remove fslightbox and iframe usage - all attachments open in new tabs
 */

$systemId = $_GET['id'] ?? null;
$debugMode = isset($_GET['debug']) && $_GET['debug'] == '1';

// Initialize the unified ProjectDetails class
$projectDetails = new ProjectDetails();

// Initialize data arrays with error handling
$data_bkil = [];
$data_permit = [];
$data_finish = [];
$data_defect = [];

try {
    if (!empty($systemId)) {
        // Use the unified ProjectDetails class methods
        $data_bkil = $projectDetails->getWorkflowDetails($systemId, 'way_leave') ?: [];
        $data_permit = $projectDetails->getWorkflowDetails($systemId, 'work_permit') ?: [];
        $data_finish = $projectDetails->getWorkflowDetails($systemId, 'work_finish') ?: [];
        $data_defect = $projectDetails->getWorkflowDetails($systemId, 'work_defect') ?: [];
    }
} catch (Exception $e) {
    error_log("Error fetching workflow details: " . $e->getMessage());
    if ($debugMode) {
        echo "<div class='alert alert-danger'>Error loading workflow data: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

/**
 * Unified function to render workflow column - Updated to remove fslightbox and iframe
 * Works with the unified ProjectDetails class
 */
function renderWorkflowColumn($data, $title) {
    ?>
    <div class="col-md-4 col-lg-12 col-xl-4">
        <!--begin::Col header-->
        <div class="mb-9">
            <div class="d-flex flex-stack">
                <div class="fw-bold fs-4"><?php echo htmlspecialchars($title) ?></div>
            </div>
            <div class="h-3px w-100 bg-warning"></div>
        </div>
        <!--end::Col header-->

        <!--begin::Card-->
        <div class="card mb-6 mb-xl-9 shadow-none border border-gray-300 border-dashed rounded">
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
                                if (!empty($data) && is_array($data)) {
                                    foreach($data as $index => $row) { 
                                        // Safe property access with fallbacks
                                        $badgeStatus = $row->badge_status ?? 'Belum Selesai';
                                        $details = $row->details ?? 'Dokumen';
                                        $attachmentDate = $row->attachment_date ?? '';
                                        $url = $row->url ?? '';
                                        $name = $row->name ?? '';
                                        $mimeType = $row->mime_type ?? 'application/pdf';
                                        
                                        // Safe badge generation
                                        if ($badgeStatus === 'Selesai') {
                                            $badgeClass = '<span data-kt-element="status" class="badge badge-light-success">Selesai</span>';
                                        } else {
                                            $badgeClass = '<span data-kt-element="status" class="badge badge-light-danger">Belum Selesai</span>';
                                        } 

                                        // Safe date formatting
                                        $dateAttach = '';
                                        if (!empty($attachmentDate)) {
                                            try {
                                                if (class_exists('General') && method_exists('General', 'convertDate')) {
                                                    $dateAttach = General::convertDate(date($attachmentDate));
                                                } else {
                                                    $dateAttach = date('d/m/Y', strtotime($attachmentDate));
                                                }
                                            } catch (Exception $e) {
                                                error_log("Date conversion error: " . $e->getMessage());
                                                $dateAttach = $attachmentDate;
                                            }
                                        }

                                        // Prepare attachment URL for new tab opening
                                        $attachmentUrl = '';
                                        if (!empty($url)) {
                                            $attachmentUrl = "/attachments/view?url=" . urlencode($url) . "&mime=" . urlencode($mimeType);
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Info-->
                                                    <div class="fw-semibold me-2 fs-6"><?php echo htmlspecialchars($details) ?></div>
                                                    <!--end::Info-->
                                                </div>
                                                <div class="d-flex align-items-center fs-6">
                                                    <div class="text-muted me-2 fs-7"><?php echo htmlspecialchars($dateAttach) ?></div>
                                                    <span class="text-gray-400 fw-bold fs-7 d-block"><?php echo $badgeClass ?></span>
                                                </div>
                                                
                                                <?php if (!empty($attachmentUrl)) { ?>
                                                    <div class="align-items-center border border-dashed border-gray-300 rounded w-300px p-3">
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Item-->
                                                            <div class="d-flex flex-aligns-center">
                                                                <!--begin::Icon-->
                                                                <img alt="" class="w-30px me-3" src="assets/media/files/pdf.svg">
                                                                <!--end::Icon-->
                                                                <!--begin::Info-->
                                                                <div class="ms-1 fs-8 mt-2">
                                                                    <!--begin::Desc - Updated to open in new tab-->
                                                                    <a class="text-gray-800 text-hover-primary d-flex flex-column" 
                                                                       href="<?php echo htmlspecialchars($attachmentUrl) ?>" 
                                                                       target="_blank">
                                                                        <div class="text-gray-400"><?php echo htmlspecialchars($name) ?></div>
                                                                    </a>
                                                                    <!--end::Desc-->
                                                                </div>
                                                                <!--end::Info-->
                                                            </div>
                                                            <!--end::Item-->
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } 
                                } else { ?>
                                    <tr>
                                        <td class="text-center text-muted">Tiada data <?php echo htmlspecialchars(strtolower($title)) ?></td>
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
    <?php
}
?>

<!--begin::Aliran Kerja-->
<!--begin::Row-->
<div class="row g-9">
    <?php
    // Render all workflow columns using the unified class data
    renderWorkflowColumn($data_bkil, 'Kelulusan Izin Lalu');
    renderWorkflowColumn($data_permit, 'Kelulusan Permit Kerja');
    renderWorkflowColumn($data_finish, 'Kelulusan Siap Kerja');
    renderWorkflowColumn($data_defect, 'Kelulusan Sempurna Kerja');
    ?>
</div>
<!--end::Row-->
<!--end::Aliran Kerja-->