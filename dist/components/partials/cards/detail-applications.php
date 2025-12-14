<?php
/**
 * Unified detail-applications.php
 * Enhanced version with better error handling, V1-specific contacts, and improved empty states
 * Uses ProjectDetails unified class with auto-detection
 */

// Get system ID from URL
$systemId = $_GET['id'] ?? null;

// Use unified ProjectDetails class
$details = new ProjectDetails();

// Get contact data using unified method (auto-detects version)
$contacts = null;
if (!empty($systemId)) {
    try {
        $contacts = $details->getContactDetails($systemId);
    } catch (Exception $e) {
        error_log("Error fetching contact details: " . $e->getMessage());
        $contacts = null;
    }
}

// Get additional contact data for V1 systems only
$gisContact = null;
$pkdContact = null;
$surveyContact = null;


try {
    $gisContact = $details->getGISDetails($systemId);
    $pkdContact = $details->getContactPKDDetails($systemId);
    $surveyContact = $details->getContactSurveyDetails($systemId);
} catch (Exception $e) {
    error_log("Error fetching V1 additional contacts: " . $e->getMessage());
}

// Get road details using unified method
$roads = null;
try {
    if (!empty($systemId)) {
        $roads = $details->getRoadDetails($systemId);
    }
} catch (Exception $e) {
    error_log("Error fetching road details: " . $e->getMessage());
    $roads = null;
}

// Get LTA details using unified method
$lta = null;
try {
    if (!empty($systemId)) {
        $lta = $details->getLTADetails($systemId);
    }
} catch (Exception $e) {
    error_log("Error fetching LTA details: " . $e->getMessage());
    $lta = null;
}

// Get authority details using unified method
$authorities = null;
try {
    if (!empty($systemId)) {
        $authorities = $details->getAuthorityDetails($systemId);
    }
} catch (Exception $e) {
    error_log("Error fetching authority details: " . $e->getMessage());
    $authorities = null;
}

$timelineCache = [];
$statusCache = [];

/**
 * Get timeline phase configuration with caching
 */
function getTimelinePhases() {
    static $phases = null;
    if ($phases === null) {
        $phases = [
            'wayleave' => [
                'id' => 'wl',
                'title' => 'Izin Lalu',
                'icon' => 'fa-road',
                'check_field' => 'application_date',
                'milestones' => [
                    'dokumen_permohonan' => [
                        'title' => ['Dokumen', 'Permohonan'],
                        'fields' => ['application_date', 'application_date'],
                        'labels' => ['Tarikh Mohon', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'lawatan_tapak' => [
                        'title' => ['Lawatan', 'Tapak Awalan'],
                        'fields' => ['calendar_sv_date', 'actual_sv_date'],
                        'labels' => ['Tarikh Jemputan', 'Tarikh Laporan'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_permohonan' => [
                        'title' => ['Surat', 'Permohonan'],
                        'fields' => ['wy_dt_auth_ltr_created', 'wy_dt_auth_ltr_send'],
                        'labels' => ['Tarikh Surat', 'Tarikh Hantar'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_kelulusan' => [
                        'title' => ['Surat', 'Kelulusan'],
                        'fields' => ['wy_dt_appv_ltr', 'wy_dt_appv_ltr_received'],
                        'labels' => ['Tarikh Kelulusan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_maklumbalas' => [
                        'title' => ['Surat', 'Maklumbalas'],
                        'fields' => ['wy_dt_fb_ltr_created', 'wy_dt_fb_ltr_send'],
                        'labels' => ['Tarikh Surat', 'Tarikh Maklum'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ]
                ]
            ],
            'permit' => [
                'id' => 'pk',
                'title' => 'Permit Kerja',
                'icon' => 'fa-file-certificate',
                'check_field' => 'permit_dt_appl_ltr_created',
                'scrollable' => true,
                'milestones' => [
                    'dokumen_permohonan' => [
                        'title' => ['Dokumen', 'Permohonan'],
                        'fields' => ['permit_dt_appl_ltr_created', 'permit_dt_appl_ltr_recieved'],
                        'labels' => ['Tarikh Permohonan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_permohonan' => [
                        'title' => ['Surat', 'Permohonan'],
                        'fields' => ['permit_dt_auth_ltr_created', 'permit_dt_auth_ltr_send'],
                        'labels' => ['Tarikh Surat', 'Tarikh Hantar'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_kelulusan' => [
                        'title' => ['Surat', 'Kelulusan'],
                        'fields' => ['permit_dt_appv_ltr_created', 'permit_dt_appv_received'],
                        'labels' => ['Tarikh Kelulusan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_perakuan' => [
                        'title' => ['Surat', 'Perakuan'],
                        'fields' => ['permit_dt_wp_recog_created', 'permit_dt_wp_recog_notify'],
                        'labels' => ['Tarikh Surat', 'Tarikh Makluman'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'nombor_perakuan' => [
                        'title' => ['Nombor', 'Perakuan'],
                        'fields' => ['permit_no_wp_recog'],
                        'labels' => ['No Perakuan'],
                        'icons' => ['fa-certificate'],
                        'colors' => ['text-warning'],
                        'type' => 'single'
                    ],
                    'serahan_perakuan' => [
                        'title' => ['Serahan', 'Perakuan'],
                        'fields' => ['permit_dt_wp_recog_submit', 'permit_wp_recog_received_by'],
                        'labels' => ['Tarikh Serahan', 'Nama Penerima'],
                        'icons' => ['fa-inbox-out', 'fa-handshake'],
                        'colors' => ['text-danger', 'text-success'],
                        'type' => 'mixed'
                    ],
                    'tempoh_minggu' => [
                        'title' => ['Tempoh Permit', 'Minggu Bekerja'],
                        'fields' => ['permit_dt_wday_strt', 'permit_dt_wday_end'],
                        'labels' => ['Tarikh Mula', 'Tarikh Tamat'],
                        'icons' => ['fa-hourglass-start', 'fa-hourglass-end'],
                        'colors' => ['text-success', 'text-danger']
                    ],
                    'tempoh_hujung_minggu' => [
                        'title' => ['Tempoh Permit', 'Hujung Minggu'],
                        'fields' => ['permit_dt_wend_strt', 'permit_dt_wend_end'],
                        'labels' => ['Tarikh Mula', 'Tarikh Tamat'],
                        'icons' => ['fa-hourglass-start', 'fa-hourglass-end'],
                        'colors' => ['text-success', 'text-danger']
                    ]
                ]
            ],
            'notice' => [
                'id' => 'notice',
                'title' => 'Notis Bekerja',
                'icon' => 'fa-snowplow',
                'check_field' => 'notice_dt_ws_ltr_created',
                'milestones' => [
                    'notis_mula' => [
                        'title' => ['Notis', 'Mula Bekerja'],
                        'fields' => ['notice_dt_ws_ltr_created', 'notice_dt_ws_ltr_received'],
                        'labels' => ['Tarikh Notis', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'notis_pemasangan' => [
                        'title' => ['Notis', 'Pemasangan'],
                        'fields' => ['notice_dt_ws', 'notice_dt_wf'],
                        'labels' => ['Tarikh Mula', 'Tarikh Selesai'],
                        'icons' => ['fa-hourglass-start', 'fa-hourglass-end'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'permakluman' => [
                        'title' => ['Permakluman', 'Pihak Berkuasa'],
                        'fields' => ['notice_dt_ws_auth_ltr_send'],
                        'labels' => ['Tarikh Makluman'],
                        'icons' => ['fa-megaphone'],
                        'colors' => ['text-warning'],
                        'type' => 'single'
                    ],
                    'notis_siap' => [
                        'title' => ['Notis', 'Siap Kerja'],
                        'fields' => ['notice_dt_wf_ltr_created', 'notice_dt_wf_ltr_received'],
                        'labels' => ['Tarikh Surat', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ]
                ]
            ],
            'cpc' => [
                'id' => 'cpc',
                'title' => 'CPC',
                'icon' => 'fa-file-check',
                'check_field' => 'cpc_dt_auth_ltr_created',
                'scrollable' => true,
                'milestones' => [
                    'dokumen_permohonan' => [
                        'title' => ['Dokumen', 'Permohonan'],
                        'fields' => ['cpc_dt_appl_ltr_created', 'cpc_dt_appl_ltr_received'],
                        'labels' => ['Tarikh Permohonan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_permohonan' => [
                        'title' => ['Surat', 'Permohonan'],
                        'fields' => ['cpc_dt_auth_ltr_created', 'cpc_dt_auth_ltr_send'],
                        'labels' => ['Tarikh Surat', 'Tarikh Hantar'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_kelulusan' => [
                        'title' => ['Surat', 'Kelulusan'],
                        'fields' => ['cpc_dt_appv_ltr_created', 'cpc_dt_appv_ltr_received'],
                        'labels' => ['Tarikh Kelulusan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'tempoh_liabiliti' => [
                        'title' => ['Tempoh', 'Kecacatan Liabiliti'],
                        'fields' => ['cpc_dt_dlp_start', 'cpc_dt_dlp_end'],
                        'labels' => ['Tarikh Mula', 'Tarikh Tamat'],
                        'icons' => ['fa-hourglass-start', 'fa-hourglass-end'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_perakuan' => [
                        'title' => ['Surat', 'Perakuan'],
                        'fields' => ['cpc_dt_wf_recog_created', 'cpc_dt_wf_recog_notify'],
                        'labels' => ['Tarikh Surat', 'Tarikh Makluman'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'nombor_perakuan' => [
                        'title' => ['Nombor', 'Perakuan'],
                        'fields' => ['cpc_no_wf_recog'],
                        'labels' => ['No Perakuan'],
                        'icons' => ['fa-certificate'],
                        'colors' => ['text-warning'],
                        'type' => 'single'
                    ]
                ]
            ],
            'ccc' => [
                'id' => 'ccc',
                'title' => 'CCC',
                'icon' => 'fa-file-contract',
                'check_field' => 'dt_cmgd_appl_ltr_created',
                'scrollable' => true,
                'milestones' => [
                    'dokumen_permohonan' => [
                        'title' => ['Dokumen', 'Permohonan'],
                        'fields' => ['dt_cmgd_appl_ltr_created', 'dt_cmgd_appl_ltr_received'],
                        'labels' => ['Tarikh Permohonan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_permohonan' => [
                        'title' => ['Surat', 'Permohonan'],
                        'fields' => ['dt_cmgd_auth_ltr_created', 'dt_cmgd_auth_send'],
                        'labels' => ['Tarikh Surat', 'Tarikh Hantar'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_kelulusan' => [
                        'title' => ['Surat', 'Kelulusan'],
                        'fields' => ['dt_cmgd_appv_ltr_created', 'dt_cmgd_appv_ltr_received'],
                        'labels' => ['Tarikh Kelulusan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_perakuan' => [
                        'title' => ['Surat', 'Perakuan'],
                        'fields' => ['dt_ccc_recog_created', 'dt_ccc_recog_notify'],
                        'labels' => ['Tarikh Surat', 'Tarikh Makluman'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'nombor_perakuan' => [
                        'title' => ['Nombor', 'Perakuan'],
                        'fields' => ['no_ccc_recog'],
                        'labels' => ['No Perakuan'],
                        'icons' => ['fa-certificate'],
                        'colors' => ['text-warning'],
                        'type' => 'single'
                    ]
                ]
            ],
            'deposit_return' => [
                'id' => 'dr',
                'title' => 'Pemulangan Wang Cagaran',
                'icon' => 'fa-money-check-pen',
                'check_field' => 'wc_dt_auth_ltr_created',
                'scrollable' => true,
                'milestones' => [
                    'wang_cagaran' => [
                        'title' => ['Wang', 'Cagaran'],
                        'fields' => ['wc_amount'],
                        'labels' => ['Amaun'],
                        'icons' => ['fa-dollar'],
                        'colors' => ['text-info'],
                        'type' => 'amount'
                    ],
                    'surat_permohonan' => [
                        'title' => ['Surat', 'Permohonan'],
                        'fields' => ['wc_dt_auth_ltr_created', 'wc_dt_auth_ltr_send'],
                        'labels' => ['Tarikh Surat', 'Tarikh Hantar'],
                        'icons' => ['fa-inbox-in', 'fa-inbox-out'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'surat_kelulusan' => [
                        'title' => ['Surat', 'Kelulusan'],
                        'fields' => ['wc_dt_appv_ltr_created', 'wc_dt_appv_ltr_received'],
                        'labels' => ['Tarikh Kelulusan', 'Tarikh Terima'],
                        'icons' => ['fa-inbox-out', 'fa-inbox-in'],
                        'colors' => ['text-danger', 'text-success']
                    ],
                    'nombor_resit' => [
                        'title' => ['Nombor', 'Baucer/Resit'],
                        'fields' => ['wc_no_voucher'],
                        'labels' => ['Nombor Resit'],
                        'icons' => ['fa-receipt'],
                        'colors' => ['text-info'],
                        'type' => 'single'
                    ]
                ]
            ]
        ];
    }
    return $phases;
}

/**
 * Check if phase has data with caching
 */
function phaseHasData($auth, $phaseName, $authorityId) {
    static $cache = [];
    $cacheKey = $authorityId . '_' . $phaseName;
    
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }
    
    $phases = getTimelinePhases();
    $phase = $phases[$phaseName] ?? null;
    
    if (!$phase) {
        $cache[$cacheKey] = false;
        return false;
    }
    
    $checkField = $phase['check_field'] ?? null;
    $hasData = $checkField && isset($auth->{$checkField}) && $auth->{$checkField} !== null;
    
    $cache[$cacheKey] = $hasData;
    return $hasData;
}

/**
 * Get milestone status with caching
 */
function getMilestoneStatus($auth, $milestone, $authorityId, $milestoneName) {
    static $statusCache = [];
    $cacheKey = $authorityId . '_' . $milestoneName;
    
    if (isset($statusCache[$cacheKey])) {
        return $statusCache[$cacheKey];
    }
    
    $hasData = true;
    foreach ($milestone['fields'] as $field) {
        if (!isset($auth->{$field}) || $auth->{$field} === null) {
            $hasData = false;
            break;
        }
    }
    
    $status = $hasData ? ['fa-check', 'success'] : ['fa-xmark', 'danger'];
    $statusCache[$cacheKey] = $status;
    return $status;
}

/**
 * Safe date formatting with error handling
 */
function formatDateSafe($date) {
    if (!$date) return '-';
    
    try {
        return (new DateTime($date))->format('d-m-Y');
    } catch (Exception $e) {
        error_log("Date formatting error for '$date': " . $e->getMessage());
        return '-';
    }
}

/**
 * Updated contact card rendering - showing details instead of tooltips
 */
function renderContactCard($contact, $contactType = null) {
    // Version-aware property access with fallbacks
    $fullName = $contact->full_name ?? ($contact->first_name ?? 'Unknown Contact');
    $email = $contact->email ?? '-';
    $position = $contact->position ?? '-';
    $phoneNo = $contact->phone_no ?? '-';
    
    // Version-specific contact type handling
    if ($contactType) {
        $displayType = $contactType;
    } else {
        $displayType = $contact->name_contact_type ?? 'Maklumat Pemohon';
    }
    
    $fullAddress = trim(
        ($contact->first_address ?? ($contact->address_1 ?? '')) . ', ' . 
        ($contact->second_address ?? ($contact->address_2 ?? '')) . ', ' . 
        ($contact->postcode ?? '')
    , ', ') ?: '-';
    ?>
   <div class="card shadow-none border border-gray-300 border-dashed rounded min-w-125px mb-5 swiper-slide card-stretch">
        <span class="bg-gray-100 fs-lg fw-semibold p-4">Maklumat <?php echo htmlspecialchars($displayType) ?></span>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <!--begin::User-->
                <div class="d-flex align-items-center flex-grow-1">
                    
                    <!--begin::Info - Updated to show details instead of tooltips-->
                    <div class="d-flex flex-column flex-grow-1">
                        <div class="text-gray-800 fw-semibold fs-6 mb-3">
                            <?php echo htmlspecialchars($fullName) ?>
                        </div>
                        
                        <!-- Contact details shown instead of tooltips -->
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center">
                                <i class="fad fa-envelope fs-6 me-2 text-gray-400 w-15px"></i>
                                <span class="text-gray-600 fs-7"><?php echo htmlspecialchars($email) ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fad fa-briefcase fs-6 me-2 text-gray-400 w-15px"></i>
                                <span class="text-gray-600 fs-7"><?php echo htmlspecialchars($position) ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fad fa-mobile-button fs-6 me-2 text-gray-400 w-15px"></i>
                                <span class="text-gray-600 fs-7"><?php echo htmlspecialchars($phoneNo) ?></span>
                            </div>
                            <div class="d-flex align-items-start">
                                <i class="fad fa-building fs-6 me-2 text-gray-400 w-15px mt-1 flex-shrink-0"></i>
                                <span class="text-gray-600 fs-7 lh-sm"><?php echo htmlspecialchars($fullAddress) ?></span>
                            </div>
                        </div>
                    </div>
                    <!--end::Info-->
                </div>
                <!--end::User-->
            </div>
        </div>
    </div>
    <?php
}

/**
 * Redesigned empty contact card - matching dashed border style
 */
function renderEmptyContactCard($contactType) {
    ?>
    <div class="card shadow-none border border-gray-300 border-dashed rounded min-w-125px mb-5 swiper-slide">
        <span class="bg-gray-100 fs-lg fw-semibold p-4"><?= htmlspecialchars($contactType) ?></span>
        <!--begin::Card Body-->
        <div class="card-body">
            <!--begin::Empty State-->
            <div class="text-center py-8">
                <div class="symbol symbol-60px symbol-circle mx-auto mb-4">
                    <div class="symbol-label bg-light-secondary">
                        <i class="fad fa-user-slash fs-2 text-gray-400"></i>
                    </div>
                </div>
                <div class="text-gray-600 fs-5 fw-semibold mb-2">Tiada Maklumat</div>
                <div class="text-muted fs-7">
                    Data <?php echo htmlspecialchars($contactType) ?> tidak tersedia
                </div>
            </div>
            <!--end::Empty State-->
        </div>
        <!--end::Card Body-->
    </div>
    <?php
}

/**
 * Render timeline item content
 */
function renderTimelineItem($label, $value, $icon, $color, $type = 'date') {
    $displayValue = $value;
    
    // Format value based on type
    switch ($type) {
        case 'date':
            $displayValue = formatDateSafe($value);
            break;
        case 'amount':
            $displayValue = 'RM ' . ($value ?: '-');
            break;
        case 'text':
        default:
            $displayValue = $value ?: '-';
            break;
    }
    ?>
    
    <div class="timeline-item align-items-center <?php echo $type !== 'single' ? 'mb-7' : '' ?>">
        <?php if ($type !== 'single'): ?>
            <div class="timeline-line <?php echo $type === 'first' ? 'mt-1 mb-n6 mb-sm-n7' : '' ?>"></div>
        <?php endif; ?>
        
        <div class="timeline-icon">
            <i class="fad <?php echo htmlspecialchars($icon) ?> fs-2 <?php echo htmlspecialchars($color) ?>"></i>
        </div>
        
        <div class="timeline-content m-0">
            <span class="fs-6 text-gray-500 fw-semibold d-block"><?php echo htmlspecialchars($label) ?></span>
            <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($displayValue) ?></span>
        </div>
    </div>
    
    <?php
}

/**
 * Render milestone card
 */
function renderMilestoneCard($auth, $milestoneName, $milestone, $authorityId) {
    list($icon, $color) = getMilestoneStatus($auth, $milestone, $authorityId, $milestoneName);
    list($title, $subtitle) = $milestone['title'];
    ?>
    
    <div class="col-lg-2 col-md-4 col-sm-6 col-12 card card-flush card-dashed p-5">
        <!--begin::Header-->
        <div class="d-flex align-items-sm-center">
            <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-3">
                <div class="flex-grow-1 me-2">
                    <span class="text-gray-500 fs-6 fw-semibold"><?php echo htmlspecialchars($title) ?></span>
                    <span class="text-gray-800 fw-bold d-block fs-4"><?php echo htmlspecialchars($subtitle) ?></span>
                </div>
                
                <?php if (!in_array($milestone['type'] ?? '', ['single', 'amount'])): ?>
                    <span class="badge badge-circle badge-lg badge-light-<?php echo $color ?> my-2">
                        <i class="fad <?php echo $icon ?> fs-4 text-<?php echo $color ?>"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <!--end::Header-->
        
        <!--begin::Timeline-->
        <div class="timeline">
            <?php
            $type = $milestone['type'] ?? 'dual';
            $fields = $milestone['fields'];
            $labels = $milestone['labels'];
            $icons = $milestone['icons'];
            $colors = $milestone['colors'];
            
            foreach ($fields as $index => $field) {
                $value = $auth->{$field} ?? null;
                $label = $labels[$index] ?? '';
                $itemIcon = $icons[$index] ?? 'fa-circle';
                $itemColor = $colors[$index] ?? 'text-muted';
                
                $itemType = 'date';
                if ($type === 'amount' || strpos($field, 'amount') !== false) {
                    $itemType = 'amount';
                } elseif ($type === 'single' || $type === 'mixed') {
                    $itemType = strpos($field, 'dt_') === 0 || strpos($field, 'date') !== false ? 'date' : 'text';
                }
                
                if ($index === 0 && count($fields) > 1) {
                    $renderType = 'first';
                } elseif (count($fields) === 1) {
                    $renderType = 'single';
                } else {
                    $renderType = 'date';
                }
                
                renderTimelineItem($label, $value, $itemIcon, $itemColor, $renderType);
            }
            ?>
        </div>
        <!--end::Timeline-->
    </div>
    
    <?php
}

/**
 * Render empty state
 */
function renderEmptyState($phaseTitle) {
    ?>
    <div class="row g-6 g-xl-9 mb-6 mb-xl-9 text-center">
        <div data-kt-search-element="empty" class="text-center">
            <div class="fw-semibold py-10">
                <div class="text-gray-600 fs-3 mb-2">Tiada Milestone</div>
                <div class="text-muted fs-6"><?php echo htmlspecialchars($phaseTitle) ?>...</div>
            </div>
            <div class="text-center px-5">
                <img src="assets/media/illustrations/empty/nalitics.svg" alt="" class="w-100 h-200px" />
            </div>
        </div>
    </div>
    <?php
}

/**
 * Render phase content
 */
function renderPhaseContent($auth, $phaseName, $authorityId) {
    $phases = getTimelinePhases();
    $phase = $phases[$phaseName];
    $hasData = phaseHasData($auth, $phaseName, $authorityId);
    $activeClass = $phaseName === 'wayleave' ? ' show active' : '';
    $scrollClass = isset($phase['scrollable']) && $phase['scrollable'] ? 
        ' hover-scroll-y px-5" style="height: 227px;"' : '"';
    ?>
    
    <div class="tab-pane fade<?php echo $activeClass ?> me-0" id="tab_<?php echo $phase['id'] ?>_<?php echo $authorityId ?>">
        <?php if ($hasData): ?>
            <div class="row gap-5 justify-content-center<?php echo $scrollClass ?>">
                <?php foreach ($phase['milestones'] as $milestoneName => $milestone): ?>
                    <?php renderMilestoneCard($auth, $milestoneName, $milestone, $authorityId); ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php renderEmptyState($phase['title']); ?>
        <?php endif; ?>
    </div>
    
    <?php
}

/**
 * Main function to render authority timeline
 */
function renderAuthorityTimeline($authority) {
    if (empty($authority) || !is_array($authority)) {
        ?>
        <div class="text-center py-10">
            <div class="text-gray-600 fs-3 mb-2">Tiada Data Pihak Berkuasa</div>
            <div class="text-muted fs-6">Sila isi butiran untuk paparkan timeline...</div>
        </div>
        <?php
        return;
    }
    
    $phases = getTimelinePhases();
    
    foreach ($authority as $auth):
        $authorityId = $auth->authority_id ?? 0;
        $authorityName = htmlspecialchars($auth->authority_name ?? 'Unknown Authority');
        $authorityLogo = htmlspecialchars($auth->authority_logo ?? 'default');
    ?>
        
    <!--begin::Accordion-->
    <div class="accordion pb-4" id="kt_accordion_<?php echo $authorityId ?>">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item" id="kt_accordion_header_<?php echo $authorityId ?>">
                <button class="accordion-button fs-4 fw-semibold" type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#kt_accordion_body_<?php echo $authorityId ?>" 
                        aria-expanded="true" 
                        aria-controls="kt_accordion_body_<?php echo $authorityId ?>">
                    
                    <!--begin::Image-->
                    <div class="d-flex flex-center flex-shrink-0 bg-light rounded w-60px h-60px w-lg-60px me-7">
                        <img class="mw-30px mw-lg-60px" 
                             src="assets/media/authorities/<?php echo $authorityLogo ?>.png" 
                             alt="<?php echo $authorityName ?>"
                             onerror="this.src='assets/media/authorities/default.png'" />
                    </div>
                    <!--end::Image-->
                    
                    <!--begin::Label-->
                    <div class="d-flex flex-center fs-4 fw-bold"><?php echo $authorityName ?></div>
                    <!--end::Label-->
                </button>
            </div>

            <div id="kt_accordion_body_<?php echo $authorityId ?>" 
                 class="accordion-collapse collapse show" 
                 aria-labelledby="kt_accordion_header_<?php echo $authorityId ?>" 
                 data-bs-parent="#kt_accordion_<?php echo $authorityId ?>">
                 
                <!--begin::Nav-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                    <?php 
                    $isFirst = true;
                    foreach ($phases as $phaseName => $phase): 
                        $activeClass = $isFirst ? 'active' : '';
                        $isFirst = false;
                    ?>
                        <li class="nav-item">
                            <a class="nav-link w-100 <?php echo $activeClass ?> btn btn-flex btn-active-light-primary m-3" 
                               data-bs-toggle="tab" href="#tab_<?php echo $phase['id'] ?>_<?php echo $authorityId ?>">
                                <i class="fad <?php echo $phase['icon'] ?> fs-1 me-2"></i>
                                <span class="d-flex flex-column align-items-start">
                                    <span class="fs-7"><?php echo htmlspecialchars($phase['title']) ?></span>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <!--end::Nav-->

                <!--begin::Content-->
                <div class="accordion-body">
                    <div class="tab-content" id="tabContent_<?php echo $authorityId ?>">
                        <?php foreach ($phases as $phaseName => $phase): ?>
                            <?php renderPhaseContent($auth, $phaseName, $authorityId); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!--end::Content-->
            </div>
        </div>
    </div>
    <!--end::Accordion-->
    
    <?php endforeach;
}
?>

<!-- begin::Contact Information Slider -->
<?php if (!empty($contacts) && is_array($contacts)) { ?>
    <div class="swiper mySwiper">
        <div class="card-wrapper swiper-wrapper">
            <?php 
            // Render main contact data
            foreach ($contacts as $contact) { 
                renderContactCard($contact, null);
            }
            
                // GIS Contact
                if (!empty($gisContact)) {
                    renderContactCard($gisContact, 'Pegawai GIS');
                } else {
                    renderEmptyContactCard('Pegawai GIS');
                }
                
                // PKD Contact
                if (!empty($pkdContact)) {
                    renderContactCard($pkdContact, 'PKD');
                } else {
                    renderEmptyContactCard('PKD');
                }
                
                // Survey Contact
                if (!empty($surveyContact)) {
                    renderContactCard($surveyContact, 'Pegawai Ukur');
                } else {
                    renderEmptyContactCard('Pegawai Ukur');
                }
            ?>
        </div>

        <div class="swiper-button-next swiper-navBtn fw-bold"></div>
        <div class="swiper-button-prev swiper-navBtn fw-bold"></div>
        <div class="swiper-pagination"></div>
    </div>
<?php } else { ?>
    <div class="text-center card">
        <!--begin::Message-->
        <div class="fw-semibold py-10">
            <div class="text-gray-600 fs-3 mb-2">Tiada Data Bagi Maklumat Pemohon</div>
            <div class="text-muted fs-6">Sila isi butiran ini untuk paparkan data...</div>
        </div>
        <!--end::Message-->
        <!--begin::Illustration-->
        <div class="text-center px-5">
            <img src="assets/media/illustrations/empty/History.svg" alt="" class="w-100 h-200px" />
        </div>
        <!--end::Illustration-->
    </div>
<?php } ?>
<!-- end::Contact Information Slider -->

<!--begin::Road Information-->
<?php if (!empty($roads) && is_array($roads)) { ?>
    <div class="accordion mt-5" id="kt_accordion_1">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_1_header_1">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_1_body_1" aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                        Maklumat Jalan Permohonan
                    </button>
                </h2>
                <div id="kt_accordion_1_body_1" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#kt_accordion_1">
                    <div class="accordion-body">
                        <!--begin::Table-->
                        <table id="kt_file_manager_list" data-kt-filemanager-table="files" 
                               class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-100px">Nama Jalan</th>
                                    <th class="min-w-10px">Jarak (m)</th>
                                    <th class="min-w-10px">Kaedah</th>
                                    <th class="min-w-100px">Koordinat Awal</th>
                                    <th class="min-w-100px">Koordinat Akhir</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-semibold text-gray-600">
                                <?php foreach ($roads as $row) { 
                                    // Version-aware property access
                                    $roadName = $row->road_name ?? '-';
                                    $roadLength = $row->road_length ?? '-';
                                    $coorStart = $row->coor_start ?? '-';
                                    $coorEnd = $row->coor_end ?? '-';
                                    
                                    // Original has both 'methods' and 'methods_shortform'
                                    $methods = ($row->methods ?? '-') . " (" . ($row->methods_shortform ?? '-') . ")";
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars(strtoupper($roadName)) ?></td>
                                        <td><?php echo htmlspecialchars($roadLength) ?></td>
                                        <td><?php echo htmlspecialchars($methods) ?></td>
                                        <td><?php echo htmlspecialchars($coorStart) ?></td>
                                        <td><?php echo htmlspecialchars($coorEnd) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="accordion mt-5" id="kt_accordion_1">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_1_header_1">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_1_body_1" aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                        Maklumat Jalan
                    </button>
                </h2>
                <div id="kt_accordion_1_body_1" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#kt_accordion_1">
                    <div class="accordion-body">
                        <!--begin::Info-->
                        <a class="fs-6 text-gray-600 fw-semibold text-hover-primary" data-fslightbox="lightbox" 
                           data-class="fslightbox-source" href="#pdf-<?php echo htmlspecialchars($systemId ?? '') ?>">
                           Sila rujuk pada dokumen BKIL ini
                        </a>
                        <!--end::Info-->

                        <!--start::PDF-->
                        <div style="display: none;">
                            <div id="pdf-<?php echo htmlspecialchars($systemId ?? '') ?>" style="height: 100vh; width: 95vw">
                                <iframe class="scroll w-100 h-100" src="/attachments/bkil/<?php echo htmlspecialchars($systemId ?? '') ?>.pdf"></iframe>
                            </div>
                        </div>
                        <!--end::PDF-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!--end::Road Information-->

<!--begin::LTA Information-->
<?php if (!empty($lta) && is_array($lta)) { ?>
    <div class="accordion mt-5" id="kt_accordion_2">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_2_header_1">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_2_body_1" aria-expanded="true" aria-controls="kt_accordion_2_body_1">
                        Maklumat LTA
                    </button>
                </h2>
                <div id="kt_accordion_2_body_1" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_2_header_1" data-bs-parent="#kt_accordion_2">
                    <div class="accordion-body">
                        <!--begin::Table-->
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <!--begin::Table head-->
                            <thead>
                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-100px">Pihak Berkuasa</th>
                                    <th class="min-w-100px">Tarikh</th>
                                    <th class="min-w-100px">Masa</th>
                                    <th class="min-w-100px">Lokasi</th>
                                    <th class="min-w-100px">Pegawai</th>
                                    <th class="min-w-100px">Penerangan</th>
                                </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="fw-semibold text-gray-600">
                                <?php foreach ($lta as $row) { 
                                    $dateFormatted = '';
                                    $start = '';
                                    $end = '';
                                    
                                    try {
                                        if (!empty($row->start)) {
                                            $dateFormatted = (new DateTime($row->start))->format('d/m/Y');
                                            $start = date('H:iA', strtotime($row->start));
                                        }
                                        if (!empty($row->end)) {
                                            $end = date('H:iA', strtotime($row->end));
                                        }
                                    } catch (Exception $e) {
                                        error_log("Date formatting error: " . $e->getMessage());
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <img class="mw-30px mw-lg-35px me-1" 
                                                 src="assets/media/authorities/<?php echo htmlspecialchars($row->logo ?? 'default') ?>.png" alt="image" />
                                            <?php echo htmlspecialchars(strtoupper($row->sort_name ?? '')) ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($dateFormatted) ?></td>
                                        <td><?php echo htmlspecialchars($start . ($end ? ' - ' . $end : '')) ?></td>
                                        <td><?php echo htmlspecialchars(strtoupper($row->location ?? '')) ?></td>
                                        <td><?php echo htmlspecialchars(strtoupper($row->first_name ?? '')) ?></td>
                                        <td><?php echo htmlspecialchars(strtoupper($row->description ?? '')) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!--end::LTA Information-->

<!--begin::Authority Information-->
<?php if (!empty($authorities) && is_array($authorities)) { ?>
    <div class="accordion mt-5" id="kt_accordion_3">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_3_header_1">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_3_body_1" aria-expanded="true" aria-controls="kt_accordion_3_body_1">
                        Batu Penanda Kemajuan
                    </button>
                </h2>
                <div id="kt_accordion_3_body_1" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_3_header_1" data-bs-parent="#kt_accordion_3">
                    <div class="accordion-body">
                        <!--begin::Timeline for each authority-->
                        <?php renderAuthorityTimeline($authorities); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <!--begin::Empty Authority State-->
    <div class="accordion mt-5" id="kt_accordion_3">
        <div class="border border-gray-300 border-dashed rounded min-w-125px mb-5">
            <div class="accordion-item">
                <h2 class="accordion-header" id="kt_accordion_3_header_1">
                    <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#kt_accordion_3_body_1" aria-expanded="true" aria-controls="kt_accordion_3_body_1">
                        Batu Penanda Kemajuan
                    </button>
                </h2>
                <div id="kt_accordion_3_body_1" class="accordion-collapse collapse show" 
                     aria-labelledby="kt_accordion_3_header_1" data-bs-parent="#kt_accordion_3">
                    <div class="accordion-body">
                        <div class="text-center py-10">
                            <div class="text-gray-600 fs-3 mb-2">Tiada Data Pihak Berkuasa</div>
                            <div class="text-muted fs-6">Sila isi butiran untuk paparkan timeline...</div>
                            <div class="text-center px-5 mt-5">
                                <img src="assets/media/illustrations/empty/nalitics.svg" alt="" class="w-100 h-200px" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Empty Authority State-->
<?php } ?>
<!--end::Authority Information-->