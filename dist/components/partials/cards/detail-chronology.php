<?php 
/**
 * Unified Project Details Chronology
 * Uses the new unified ProjectDetails class with auto-detection
 * Updated to remove fslightbox and iframe usage - all attachments open in new tabs
 */

$systemId = $_GET['id'] ?? null;

// Initialize the unified ProjectDetails class
$projectDetails = new ProjectDetails();
$chronologies = $projectDetails->getChronologyDetails($systemId) ?: [];
$authority = $projectDetails->getAuthorityChronology($systemId) ?: [];

// Color array for styling
$colors = array('primary', 'info', 'success', 'danger', 'warning', 'dark');
shuffle($colors);

/**
 * Unified function to render timeline item - Updated to remove fslightbox and iframe
 * Works with the unified ProjectDetails class
 */
function renderChronologyItem($chronology, $index, $lightboxPrefix = 'lightbox-cr') {
    // Safe property access with fallbacks
    $createdAt = $chronology->created_at ?? '';
    $icon = $chronology->icon ?? 'clock';
    $flowName = $chronology->flow_name ?? 'Unknown Activity';
    $noteDetails = $chronology->note_details ?? '';
    $attachName = $chronology->attach_name ?? '';
    $attachmentType = $chronology->attachment_type ?? '';
    $url = $chronology->url ?? '';
    $mimeType = $chronology->mime_type ?? 'application/pdf';
    $size = $chronology->size ?? 0;
    $oldSystemId = $chronology->old_system_id ?? '';
    
    // Handle different user name fields (unified class handles this automatically)
    $userName = $chronology->username ?? 'Unknown User';
    
    // Safe time formatting
    $time = '';
    if (!empty($createdAt)) {
        try {
            $time = date('h:i A', strtotime($createdAt));
        } catch (Exception $e) {
            error_log("Time formatting error: " . $e->getMessage());
            $time = '';
        }
    }
    
    // Safe date formatting
    $dateFormatted = '';
    if (!empty($createdAt)) {
        try {
            if (class_exists('General') && method_exists('General', 'convertDate')) {
                $dateFormatted = General::convertDate(date($createdAt));
            } else {
                $dateFormatted = date('d/m/Y', strtotime($createdAt));
            }
        } catch (Exception $e) {
            error_log("Date formatting error: " . $e->getMessage());
            $dateFormatted = date('d/m/Y', strtotime($createdAt));
        }
    }
    
    // Safe size formatting
    $sizeFormatted = '';
    if (!empty($size) && is_numeric($size)) {
        if ($size >= 1024 * 1024 * 1024) {
            $sizeFormatted = round($size / (1024 * 1024 * 1024), 2) . ' GB';
        } else if ($size >= 1024 * 1024) {
            $sizeFormatted = round($size / (1024 * 1024), 2) . ' MB';
        } else if ($size >= 1024) {
            $sizeFormatted = round($size / 1024, 2) . ' KB';
        } else {
            $sizeFormatted = $size . ' bytes';
        }
    }
    
    // Prepare attachment URL for new tab opening
    $attachmentUrl = '';
    if (!empty($url)) {
        $attachmentUrl = "/attachments/view?url=" . urlencode($url) . "&mime=" . urlencode($mimeType);
    } else if(!empty($url) && $attachmentType === 23) {
        $attachmentUrl = $url;
    }
    ?>
    <!--begin::Timeline item-->
    <div class="timeline-item">
        <!--begin::Timeline line-->
        <div class="timeline-line w-40px"></div>
        <!--end::Timeline line-->
        <!--begin::Timeline icon-->
        <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
            <div class="symbol-label bg-light">
                <i class="fad fa-<?php echo htmlspecialchars($icon) ?> fs-2"></i>
            </div>
        </div>
        <!--end::Timeline icon-->
        <!--begin::Timeline content-->
        <div class="timeline-content mb-10 mt-n1">
            <!--begin::Timeline heading-->
            <div class="pe-3 mb-3">
                <!--begin::Title-->
                <div class="d-flex align-items-center fs-6">
                    <!--begin::Info-->
                    <div class="fw-semibold me-2 fs-5"><?php echo htmlspecialchars($flowName) ?></div>
                    <!--end::Info-->
                </div>
                <!--end::Title-->
                <!--begin::Description-->
                <div class="d-flex align-items-center fs-6">
                    <!--begin::Info-->
                    <div class="text-muted me-2 fs-7">
                        Pada <?php echo htmlspecialchars($dateFormatted) ?><?php echo !empty($time) ? ' - ' . htmlspecialchars($time) : '' ?> oleh
                    </div>
                    <!--end::Info-->
                    <!--begin::User-->
                    <div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="right" title="<?php echo htmlspecialchars($userName) ?>">
                        <img src="assets/media/avatars/blank.jpg" alt="">
                    </div>
                    <!--end::User-->
                </div>
                <!--end::Description-->
            </div>
            <!--end::Timeline heading-->
            <!--begin::Timeline details-->
            <div class="overflow-auto">
                <!--begin::Record-->
                <div class="align-items-center border border-dashed border-gray-300 rounded min-w-750px px-7 py-3">
                    <!--begin::Title-->
                    <div class="d-flex fs-6 text-gray-700 pe-7"><?= $noteDetails ?></div>
                    <!--end::Title-->

                    <?php if (!empty($attachName) && !empty($attachmentUrl)) { ?>
                        <!--begin::Attachment Section-->
                        <div class="d-flex align-items-center mt-5">
                            <!--begin::File info-->
                            <div class="d-flex align-items-center flex-row-fluid">
                                <!--begin::Pic-->
                                <div class="symbol symbol-45px me-5">
                                    <img alt="" class="h-30px" src="assets/media/files/pdf.svg">
                                </div>
                                <!--end::Pic-->
                                <!--begin::Details-->
                                <div class="d-flex flex-column text-gray-600">
                                    <a class="text-gray-800 text-hover-primary fs-6 fw-bold" 
                                       href="<?php echo htmlspecialchars($attachmentUrl) ?>" 
                                       target="_blank">
                                        <?php echo htmlspecialchars($attachName) ?>
                                    </a>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($sizeFormatted) ?></div>
                                </div>
                                <!--end::Details-->
                            </div>
                            <!--end::File info-->
                        </div>
                        <!--end::Attachment Section-->
                    <?php } ?>
                </div>
                <!--end::Record-->
            </div>
            <!--end::Timeline details-->
        </div>
        <!--end::Timeline content-->
    </div>
    <!--end::Timeline item-->
    <?php
}

/**
 * Unified function to render authority tab content
 * Uses the unified ProjectDetails class
 */
function renderAuthorityChronology($projectDetails, $systemId, $authorityId) {
    $authorities = [];
    try {
        if (!empty($systemId) && !empty($authorityId)) {
            $authorities = $projectDetails->getChronologyAuthDetails($systemId, $authorityId) ?: [];
        }
    } catch (Exception $e) {
        error_log("Error fetching authority cronology: " . $e->getMessage());
    }

    if (!empty($authorities) && is_array($authorities)) {
        foreach ($authorities as $index => $chronologyAuthority) {
            renderChronologyItem($chronologyAuthority, 'auth-' . $authorityId . '-' . $index, 'lightbox-cr-auth');
        }
    } else { ?>
        <div class="text-center py-10">
            <div class="text-gray-600 fs-3 mb-2">Tiada Kronologi</div>
            <div class="text-muted fs-6">Belum ada aktiviti yang direkodkan untuk autoriti ini...</div>
        </div>
    <?php }
}
?>

<!--begin::Kronologi-->
<div class="text-center card shadow-none border border-gray-300 border-dashed rounded">
    <!--begin::Card body-->
    <div class="card-body pt-0">
        <!--begin::Nav-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-10">
            <!--begin::Nav item-->
            <li class="nav-item">
                <a class="nav-link w-100 active btn btn-flex btn-active-light-primary m-3" data-bs-toggle="tab" href="#tab_all">
                    <i class="fad fa-files fs-1 me-2"></i>
                    <span class="d-flex flex-column align-items-start">
                        <span class="fs-7">Semua</span>
                    </span>
                </a>
            </li>
            <!--end::Nav item-->

            <?php 
            if (!empty($authority) && is_array($authority)) {
                foreach($authority as $row) { 
                    $authorityId = $row->authority_id ?? 0;
                    $sortName = $row->sort_name ?? 'Unknown';
                    ?>
                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link w-100 btn btn-flex btn-active-light-primary m-3" data-bs-toggle="tab" href="#tabAuth_<?php echo $authorityId ?>">
                            <i class="fad fa-file fs-1 me-2"></i>
                            <span class="d-flex flex-column align-items-start">
                                <span class="fs-7"><?php echo htmlspecialchars($sortName) ?></span>
                            </span>
                        </a>
                    </li>
                    <!--end::Nav item-->
                <?php } 
            } ?>
        </ul>
        <!--end::Nav-->

        <!--begin::Tab Content-->
        <div class="tab-content px-6" id="myTabContent">
            <!--begin::Tab View All-->
            <div class="tab-pane fade show active me-0" id="tab_all">
                <!--begin::Timeline-->
                <div class="timeline">
                    <?php 
                    if (!empty($chronologies) && is_array($chronologies)) {
                        foreach($chronologies as $index2 => $chronology) {
                            renderChronologyItem($chronology, $index2, 'lightbox-cr-all');
                        }
                    } else { ?>
                        <div class="text-center py-10">
                            <div class="text-gray-600 fs-3 mb-2">Tiada Kronologi</div>
                            <div class="text-muted fs-6">Belum ada aktiviti yang direkodkan...</div>
                        </div>
                    <?php } ?>
                </div>
                <!--end::Timeline-->
            </div>
            <!--end::Tab View All-->

            <?php 
            // Render Authority Tabs
            if (!empty($authority) && is_array($authority)) {
                foreach($authority as $row) { 
                    $authorityId = $row->authority_id ?? 0;
                    ?>
                    <!--begin::Tab View Authority-->
                    <div class="tab-pane fade" id="tabAuth_<?php echo $authorityId ?>">
                        <!--begin::Timeline-->
                        <div class="timeline">
                            <?php renderAuthorityChronology($projectDetails, $systemId, $authorityId); ?>
                        </div>
                        <!--end::Timeline-->
                    </div>
                    <!--end::Tab View Authority-->
                <?php } 
            } ?>
        </div>
        <!--end::Tab Content-->
    </div>
    <!--end::Card body-->
</div>
<!--end::Kronologi-->