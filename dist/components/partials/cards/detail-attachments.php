<?php
/**
 * KITER Attachment Details Page - Simple Download Implementation
 * 
 * FEATURES:
 * 1. Direct URL handling without complex encoding
 * 2. Simple download.php handler for forced downloads
 * 3. Clean, maintainable code structure
 * 4. Enhanced user experience with loading states
 */

// Use unified ProjectDetails class
$details = new ProjectDetails();
$systemId = $_GET['id'] ?? null;

$attachments = [];
if (!empty($systemId)) {
    try {
        $attachments = $details->getAttachments($systemId);
    } catch (Exception $e) {
        error_log("Error fetching attachments: " . $e->getMessage());
        $attachments = [];
    }
}

// Define tab configurations - enhanced for both versions
$tabConfigs = [
    'wl' => ['id' => 'tab-wl', 'phase' => 'izin lalu', 'title' => 'Izin Lalu'],
    'permit' => ['id' => 'tab-permit', 'phase' => 'permit', 'title' => 'Permit Kerja'],
    'notice' => ['id' => 'tab-notice', 'phase' => 'notis', 'title' => 'Notis Kerja'],
    'cpc' => ['id' => 'tab-cpc', 'phase' => 'cpc', 'title' => 'Sijil Siap Kerja'],
    'ccc' => ['id' => 'tab-ccc', 'phase' => 'ccc', 'title' => 'Sijil Sempurna Kerja'],
    'wc' => ['id' => 'tab-wc', 'phase' => 'wc', 'title' => 'Pemulangan Wang Cagaran'],
    'udm' => ['id' => 'tab-udm', 'phase' => 'ukur', 'title' => 'UDM'],
    'tmp' => ['id' => 'tab-tmp', 'phase' => 'ukur', 'title' => 'TMP'],
    'ab' => ['id' => 'tab-ab', 'phase' => 'ukur', 'title' => 'As-Built'],
    'plan' => ['id' => 'tab-plan', 'phase' => 'pelan', 'title' => 'Pelan']
];

/**
 * Format file size with version-aware logic
 */
function formatFileSize($bytes) {
    if (!is_numeric($bytes) || $bytes <= 0) return '0 bytes';
    
    // Enhanced V1 formatting
    if ($bytes >= 1024 * 1024 * 1024) {
        return round($bytes / (1024 * 1024 * 1024), 2) . ' GB';
    } elseif ($bytes >= 1024 * 1024) {
        return round($bytes / (1024 * 1024), 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }

}


/**
 * Get file type icon based on mime type
 */
function getFileIcon($mimeType) {
    $mimeType = strtolower($mimeType ?? '');
    
    if (strpos($mimeType, 'pdf') !== false) {
        return 'pdf';
    } elseif (strpos($mimeType, 'image') !== false) {
        return 'image';
    } elseif (strpos($mimeType, 'word') !== false || strpos($mimeType, 'document') !== false) {
        return 'doc';
    } elseif (strpos($mimeType, 'excel') !== false || strpos($mimeType, 'spreadsheet') !== false) {
        return 'xls';
    } elseif (strpos($mimeType, 'zip') !== false || strpos($mimeType, 'archive') !== false) {
        return 'zip';
    } else {
        return 'doc';
    }
}

/**
 * Get view URL for attachment
 */
function getViewUrl($attach) {
    $attachmentType = $attach->attachment_type ?? 0;
    $attachmentCode = $attach->code_name ?? '';
    $mimeType = $attach->mime_type ?? 'application/pdf';
    $url = $attach->url ?? '';
    
    if ($attachmentCode === 'LTA' || $attachmentCode === 'LLTA' || $attachmentCode === 'RCP') {
        // External link - decode if needed, but skip decoding for toyyibpay URLs
        return (strpos($url, 'https://toyyibpay') !== false) ? $url : base64_decode($url);
    } else {
        // Direct URL for viewing
        return "/attachments/view?url=" . $url . "&mime=" . urlencode($mimeType);
    }
}

/**
 * Get download URL for attachment
 */
function getDownloadUrl($attach) {
    $url = $attach->url ?? '';
    $mimeType = $attach->mime_type ?? 'application/pdf';
        // Route through our simple download handler
        return "/attachments/view?url=" . $url . "&mime=" . urlencode($mimeType) . "&download=1";
}

/**
 * Render attachment item in list view (table row format)
 */
function renderAttachmentItem($attach, $config, $key, $index) {
    // Safe property access
    $details = $attach->details ?? 'Dokumen';
    $version = $attach->version ?? '';
    $attachmentType = $attach->attachment_type ?? 0;
    $size = $attach->size ?? 0;
    $attachmentDate = $attach->attachment_date ?? '';
    $mimeType = $attach->mime_type ?? 'application/pdf';
    $userName = $attach->user_first_name ?? $attach->user_added ?? 'System';
    
    // Format data
    $sizeFormatted = formatFileSize($size);
    $viewUrl = getViewUrl($attach);
    $downloadUrl = getDownloadUrl($attach);
    $titleText = $version ? htmlspecialchars($details) . ' (v' . htmlspecialchars($version) . ')' : htmlspecialchars($details);
    
    // Get file icon
    $fileIcon = getFileIcon($mimeType);
    
    // Format date for list view - date only
    $formattedDate = '';
    if (!empty($attachmentDate)) {
        try {
            $formattedDate = date('d/m/Y', strtotime($attachmentDate));
        } catch (Exception $e) {
            $formattedDate = htmlspecialchars($attachmentDate);
        }
    }
    
    echo '<tr class="attachment-item">';
    
    // File icon and name
    echo '<td class="d-flex align-items-center">';
    echo '<i class="fad fa-file-' . $fileIcon . ' fs-1 text-muted me-2"></i>';
    echo '<div class="d-flex flex-column">';
    echo '<a href="' . htmlspecialchars($viewUrl) . '" target="_blank" class="text-gray-800 text-hover-primary fs-6 fw-bold">' . $titleText . '</a>';
    echo '<span class="text-muted fs-7">' . htmlspecialchars($mimeType) . '</span>';
    echo '</div>';
    echo '</td>';
    
    // Size
    echo '<td class="text-muted fs-7">' . htmlspecialchars($sizeFormatted) . '</td>';
    
    // Upload date
    echo '<td class="text-muted fs-7">' . htmlspecialchars($formattedDate) . '</td>';
    
    // Uploaded by
    echo '<td class="text-muted fs-7">' . htmlspecialchars($userName) . '</td>';
    
    // Actions
    echo '<td>';
    echo '<div class="d-flex align-items-center gap-2">';
    
    // View button - opens file for viewing (inline)
    if($attachmentType !== 87) {
        echo '<a href="' . htmlspecialchars($viewUrl) . '" target="_blank" class="btn btn-sm btn-icon btn-primary" title="Lihat">';
        echo '<i class="fad fa-eye fs-5"></i>';
        echo '</a>';
    }
    
    // Download button for downloadable files
    if ($attachmentType !== 7 && $attachmentType !== 23) { // Not external link
        echo '<a href="' . htmlspecialchars($downloadUrl) . '" class="btn btn-sm btn-icon btn-secondary download-btn" title="Muat Turun">';
        echo '<i class="fad fa-download fs-5"></i>';
        echo '</a>';
    }
    
    echo '</div>';
    echo '</td>';
    
    echo '</tr>';
}

/**
 * Render attachments in list view format
 */
function renderAttachmentsContainer($matchingAttachments, $config, $key) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-hover align-middle gs-0 gy-3">';
    echo '<thead class="border-bottom border-gray-200">';
    echo '<tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">';
    echo '<th class="min-w-250px">Nama Fail</th>';
    echo '<th class="min-w-100px">Saiz</th>';
    echo '<th class="min-w-150px">Tarikh Muat Naik</th>';
    echo '<th class="min-w-100px">Dimuat Naik Oleh</th>';
    echo '<th class="min-w-100px text-end">Tindakan</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($matchingAttachments as $index => $attach) {
        renderAttachmentItem($attach, $config, $key, $index);
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

?>

<!--begin::Attachments Layout-->
<div class="form d-flex flex-column flex-lg-row">
    <!--begin::File Structure Sidebar-->
    <div class="d-flex flex-column flex-lg-row-auto w-lg-300px mb-7 me-7 me-lg-10">
        <div class="card shadow-none border border-gray-300 border-dashed rounded">
           <span class="bg-gray-100 fs-lg fw-semibold p-4">Navigasi Fail</span>
            <!--begin::Card body-->
            <div class="card-body pt-0">
                <div class="d-flex flex-column gap-10">
                    <div id="jstree">
                        <ul>
                            <li data-tab="tab-wl" data-jstree='{ "opened" : true }'>
                                Projek
                                <ul>
                                    <li data-tab="tab-wl" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        Izin Lalu
                                    </li>
                                    <li data-tab="tab-permit" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        Permit Kerja
                                    </li>
                                    <li data-tab="tab-notice" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        Notis 
                                    </li>
                                    <li data-tab="tab-cpc" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        CPC
                                    </li>
                                    <li data-tab="tab-ccc" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        CCC
                                    </li>
                                    <li data-tab="tab-wc" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        Pemulangan Wang Cagaran
                                    </li>
                                </ul>
                            </li>
                            <li data-tab="tab-udm">
                                Ukur
                                <ul>
                                    <li data-tab="tab-udm" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        UDM
                                    </li>
                                    <li data-tab="tab-tmp" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        TMP
                                    </li>
                                    <li data-tab="tab-ab" data-jstree='{ "icon" : "fad fa-file text-gray-500 fs-2" }'>
                                        As Built
                                    </li>
                                </ul>
                            </li>
                            <li data-tab="tab-plan">
                                Pelan
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
    </div>
    <!--end::File Structure Sidebar-->

    <!--begin::Content Area-->
    <div class="d-flex flex-column flex-lg-row-fluid gap-7 gap-lg-10">
        <div id="myTabContent">
            <?php 
            foreach ($tabConfigs as $key => $config) {
                $matchingRowsFound = false;
                $matchingAttachments = [];
                
                // Check for matching attachments
                if (!empty($attachments) && is_array($attachments)) {
                    foreach ($attachments as $attach) {
                        $attachPhase = $attach->phase ?? '';
                        if ($attachPhase === $config['phase']) {
                            $matchingRowsFound = true;
                            $matchingAttachments[] = $attach;
                        }
                    }
                }
                
                // Set current class for first tab (Izin Lalu/Wayleave)
                $currentClass = ($key === 'wl') ? ' current' : '';
                ?>
                
                <!--begin::Tab Content-->
                <div id="<?php echo $config['id'] ?>" class="doc-content<?php echo $currentClass ?>">
                    <div class="card shadow-none border border-gray-300 border-dashed rounded">
                        <?php if ($matchingRowsFound) { ?>
                            <!--begin::Header-->
                            <div class="bg-gray-100 p-4 d-flex justify-content-between align-items-center">
                                <span class="fs-lg fw-semibold">
                                    Senarai Lampiran (<?php echo htmlspecialchars($config['title']) ?>) 
                                    <span class="badge badge-light-primary ms-2"><?php echo count($matchingAttachments) ?></span>
                                </span>
                            </div>
                            <!--end::Header-->
                            
                            <div class="separator mb-5"></div>

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <?php renderAttachmentsContainer($matchingAttachments, $config, $key); ?>
                            </div>
                            <!--end::Card body-->
                        <?php } else { ?>
                            <!--begin::Empty State-->
                            <div class="card-body pt-0">
                                <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
                                    <div data-kt-search-element="empty" class="text-center">
                                        <div class="fw-semibold py-10">
                                            <div class="text-gray-600 fs-3 mb-2">Tiada Dokumen</div>
                                            <div class="text-muted fs-6">
                                                <?php echo htmlspecialchars($config['title']) ?>...
                                            </div>
                                        </div>
                                        <div class="text-center px-5">
                                            <img src="assets/media/illustrations/empty/Files.svg" alt="" class="w-100 h-200px" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Empty State-->
                        <?php } ?>
                    </div>
                </div>
                <!--end::Tab Content-->
            <?php } ?>
        </div>
    </div>
    <!--end::Content Area-->
</div>
<!--end::Attachments Layout-->

<!-- Success Message Modal -->
<div class="modal fade" id="downloadSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <i class="fad fa-check-circle text-success fs-1 mb-3"></i>
                <h4 class="mb-2">Muat Turun Dimulakan</h4>
                <p class="text-muted">Fail anda sedang dimuat turun...</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for attachment list */
.attachment-item:hover {
    background-color: var(--bs-light) !important;
}

/* Table enhancements */
.table td {
    vertical-align: middle;
    border-top: 1px solid var(--bs-gray-200);
    padding: 1rem 0.75rem;
}

.table thead th {
    border-bottom: 2px solid var(--bs-gray-200);
    font-weight: 600;
}

/* Button hover effects */
.btn-icon:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

/* Loading state for download buttons */
.download-btn.loading {
    pointer-events: none;
    opacity: 0.7;
}

.download-btn.loading i::before {
    content: '\f110'; /* fa-spinner */
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// Enhanced jstree functionality and download handling
$(function () {
    // Initialize JSTree
    $('#jstree').jstree({
        "types" : {
            "default" : {
                "icon" : "fa fa-folder fs-2"
            }
        },
        "plugins": ["types"]
    }).on("select_node.jstree", function(event, node) {
        var tab_id = $(node.event.currentTarget).parent().attr('data-tab');
        if (tab_id) {
            $('.doc-content').removeClass('current');
            $("#" + tab_id).addClass('current');
        }
    });
    
    // Enhanced download button handling
    $('.download-btn').on('click', function(e) {
        var $btn = $(this);
        
        // Add loading state
        $btn.addClass('loading');
        
        // Show success modal
        setTimeout(function() {
            $('#downloadSuccessModal').modal('show');
        }, 500);
        
        // Reset button state after 3 seconds
        setTimeout(function() {
            $btn.removeClass('loading');
        }, 3000);
        
        // Auto-hide modal after 2 seconds
        setTimeout(function() {
            $('#downloadSuccessModal').modal('hide');
        }, 2500);
    });
    
    // Add tooltips for better UX
    $('[title]').tooltip();
});

// Error handling for failed downloads
$(document).on('click', 'a[href*="download.php"]', function(e) {
    var link = this;
    
    // Check if the download link is working
    fetch(link.href, { method: 'HEAD' })
        .catch(function() {
            e.preventDefault();
            alert('Ralat: Gagal memuat turun fail. Sila cuba lagi.');
        });
});
</script>