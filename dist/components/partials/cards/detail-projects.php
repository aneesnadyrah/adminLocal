<?php

/**
 * Unified project-details.php
 * Combines project-details.php and project-details-v1.php with auto-detection
 * Uses ProjectDetails unified class
 */
// Use unified ProjectDetails class
$details = new ProjectDetails();
$systemId = $_GET['id'] ?? null;
$roleId = $_SESSION["roleId"];
// Get project data using unified method (auto-detects version)
if (!empty($systemId)) {
    $data = $details->getProjectDetails($systemId);
}


// Get provider information safely
$provider = null;
if ($data && isset($data->provider_id)) {
    try {
        if (class_exists('General') && method_exists('General', 'getProvider')) {
            $provider = General::getProvider($data->provider_id);
        }
    } catch (Exception $e) {
        error_log("Error fetching provider: " . $e->getMessage());
    }
}

// Color array for district badges
$colors = array('info', 'success');
shuffle($colors);

/**
 * Format currency safely
 */
function formatCurrency($amount)
{
    if (!is_numeric($amount) || $amount <= 0) return 'RM -';
    return 'RM ' . number_format($amount, 2);
}

/**
 * Format distance safely  
 */
function formatDistance($distance, $unit = 'm')
{
    if (!is_numeric($distance) || $distance <= 0) return '0 ' . $unit;
    return number_format($distance, 0) . ' ' . $unit;
}

/**
 * Parse districts safely
 */
function parseDistricts($districts)
{
    if (empty($districts)) return [];

    if (is_string($districts)) {
        // Handle comma-separated string
        return array_filter(array_map('trim', explode(',', $districts)));
    } elseif (is_array($districts)) {
        return array_filter($districts);
    }

    return [];
}

/**
 * Check if user can jump steps based on role and version
 */

function canUserJumpStep($roleId, $systemId = null)
{
    // Cache the authorized roles to avoid repeated array creation
    static $authorizedRoles = null;
    if ($authorizedRoles === null) {
        $authorizedRoles = [2, 4, 5, 6, 7, 8, 9, 10, 11, 14, 18, 19, 20, 24, 26, 28, 31, 32];
    }

    // Validate inputs
    if (!is_numeric($roleId) || $roleId < 1) {
        return false;
    }


    // Check system ID requirement
    if (empty($systemId)) {
        return false;
    }

    // Check role authorization
    return in_array((int)$roleId, $authorizedRoles, true);
}
?>

<!--begin::Project Details Card-->
<div class="card mb-6 mb-xl-8 shadow-none border border-gray-300 border-dashed rounded">
    <!--begin::Card Body-->
    <div class="card-body pt-9 pb-0">
        <!--begin::Details Section-->
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <!--begin::Provider Logo-->
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <?php if ($provider && isset($provider->logo) && isset($provider->name)) { ?>
                        <img class="mw-100px mw-lg-150px"
                            src="<?= htmlspecialchars($provider->logo) ?>"
                            alt="<?= htmlspecialchars($provider->name) ?>" />
                    <?php } else { ?>
                        <div class="text-muted fs-4">
                            <i class="fad fa-building fs-2x"></i><br>
                            <small>No Logo</small>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!--end::Provider Logo-->

            <!--begin::Info Section-->
            <div class="flex-grow-1">
                <!--begin::Head-->
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <!--begin::Details-->
                    <div class="d-flex flex-column">
                        <!--begin::Reference Number-->
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-2">
                                <span class="text-gray-900 fs-2 fw-bold me-3">
                                    <?= htmlspecialchars($data->reference_no ?? '-') ?>
                                </span>
                                <?php
                                $districts = parseDistricts($data->districts ?? '');
                                if (!empty($districts)) {
                                    foreach ($districts as $district) {
                                        if (!empty(trim($district))) {
                                            $color = $colors[array_rand($colors)];
                                            echo '<span class="badge badge-light-' . htmlspecialchars($color) . ' me-auto">' .
                                                htmlspecialchars(trim($district)) . '</span>';
                                        }
                                    }
                                } else {
                                    echo '<span class="badge badge-light-secondary me-2">No Districts</span>';
                                }
                                ?>
                            </div>
                        </div>
                        <!--end::Reference Number-->
                        <!--begin::Status-->
                        <div class="d-flex flex-wrap fw-semibold fs-3 mb-4 pe-2">
                            <!--begin::Status Badge-->
                            <?php
                            $operationStatus = $data->status ?? 'Unknown';
                            $operationStatusColor = $data->status_color ?? 'info';
                            $operationStatusIndex = $data->flow_index ?? 0;

                            $financeStatus = $data->finance_status ?? 'Unknown';
                            $financeStatusColor = $data->finance_status_color ?? 'info';
                            $financeStatusIndex = $data->finance_flow_index ?? 0;

                            // Check if both invoice data exist
                            if (!isset($data->invoice_balance) || !isset($data->invoice_amount)) {
                                $financeInvoicesCompleted = false;
                            } else {
                                $balance = (float) $data->invoice_balance;
                                $amount = (float) $data->invoice_amount;
                                // Completed if balance is less than amount (payment has been made)
                                $financeInvoicesCompleted = ($balance < $amount);
                            }

                            $mappingStatus = $data->mapping_status ?? 'Unknown';
                            $mappingStatusColor = $data->mapping_status_color ?? 'info';
                            $mappingStatusIndex = $data->mapping_flow_index ?? 0;

                            $geospatialStatus = $data->geospatial_status ?? 'Unknown';
                            $geospatialStatusColor = $data->geospatial_status_color ?? 'info';
                            $geospatialStatusIndex = $data->geospatial_flow_index ?? 0;
                            $geospatialSubmit = $data->pil_submitted_at ?? NULL;
                            ?>

                            <?php if ($operationStatusIndex !== 0) : ?>
                                <span class="badge badge-light-<?= htmlspecialchars($operationStatusColor) ?> me-2">
                                    <?= htmlspecialchars($operationStatus) ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($geospatialSubmit === NULL) : ?>
                                <span class="badge badge-light-<?= htmlspecialchars($geospatialStatusColor) ?> me-2">
                                    <?= htmlspecialchars($geospatialStatus) ?>
                                </span>
                            <?php else : ?>
                                <span class="badge badge-light-dark me-2">
                                    <i class="fad fa-check-circle me-1 text-success"></i>
                                    Pelan Izin Lalu Telah Hantar
                                </span>
                            <?php endif; ?>

                            <?php if ($financeStatusIndex !== 0 && $financeStatus !== 'Permohonan Baru' && !$financeInvoicesCompleted) : ?>
                                <span class="badge badge-light-<?= htmlspecialchars($financeStatusColor) ?> me-2">
                                    <?= htmlspecialchars($financeStatus) ?>
                                </span>
                            <?php elseif ($financeInvoicesCompleted) : ?>
                                <span class="badge badge-light-dark me-2">
                                    <i class="fad fa-check-circle me-1 text-success"></i>
                                    Pembayaran Invois Selesai
                                </span>
                            <?php endif; ?>

                            <?php if ($mappingStatusIndex !== 0) : ?>
                                <span class="badge badge-light-<?= htmlspecialchars($mappingStatusColor) ?> me-auto">
                                    <?= htmlspecialchars($mappingStatus) ?>
                                </span>
                            <?php endif; ?>
                            <!--end::Status Badge-->
                        </div>
                        <!--end::Status-->
                    </div>

                    <!--begin::Actions-->
                    <div class="d-flex my-4">
                        <!--begin::Cancel Button-->
                        <button type="button" class="btn btn-flex flex-center btn-secondary me-2"
                            data-bs-toggle="modal" data-bs-target="#modal_cancel_app">
                            <i class="fad fa-archive fs-4"></i>
                            <span class="d-none d-md-inline">Batal</span>
                        </button>
                        <!--end::Cancel Button-->

                        <?php if (empty($data->old_system_id)) : ?>
                            <a href="/projects/prints/BKIL/<?= $systemId ?>" class="btn btn-sm btn-primary me-2">
                                <span class="svg-icon svg-icon-3">
                                    <i class="fad fa-file fs-2 me-2"></i>
                                </span>
                                <span>Borang KIL</span>
                            </a>
                        <?php endif; ?>
                        <!--begin::Jump Step Button (V1 only)-->
                        <?php if (canUserJumpStep($roleId, $systemId ?? null)) { ?>
                            <button type="button"
                                class="btn btn-sm btn-primary jump-step-btn me-2"
                                data-bs-toggle="modal"
                                data-bs-target="#modal_jump_step_app"
                                data-system-id="<?= htmlspecialchars($systemId, ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fad fa-forward fs-4" aria-hidden="true"></i>
                                <span class="d-none d-md-inline">Langkau</span>
                            </button>
                        <?php } ?>
                        <!--end::Jump Step Button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--begin::Project Title-->
                <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">
                    <?= htmlspecialchars($data->project_title ?? 'No Project Title') ?>
                </div>
                <!--end::Project Title-->
                <!--end::Head-->

                <!--begin::Info Stats-->
                <div class="d-flex flex-wrap justify-content-start">
                    <!--begin::Stats-->
                    <div class="d-flex flex-wrap">
                        <!--begin::Application Date-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold">
                                    <?php
                                    if (!empty($data->application_date)) {
                                        try {
                                            echo date('d/m/Y', strtotime($data->application_date));
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($data->application_date);
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-400">Tarikh Mohon</div>
                        </div>
                        <!--end::Application Date-->

                        <!--begin::Submitted Date (V1 only)-->
                        <?php if (!empty($data->submitted_date)) { ?>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">
                                        <?php
                                        try {
                                            echo date('d/m/Y', strtotime($data->submitted_date));
                                        } catch (Exception $e) {
                                            echo htmlspecialchars($data->submitted_date);
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">Tarikh Proses</div>
                            </div>
                        <?php } ?>
                        <!--end::Submitted Date-->

                        <!--begin::Project Length-->
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold">
                                    <?= formatDistance($data->application_length ?? 0); ?>
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-400">
                                Jarak Permohonan
                            </div>
                        </div>
                        <!--end::Project Length-->

                        <?php if ($operationStatusIndex > 3) : ?>
                            <!--begin::Project Length-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">
                                        <?php
                                        if ($operationStatusIndex <= 3) {
                                            $ltaLength = 0;
                                        } else {
                                            $ltaLength = $data->lta_length ?? 0;
                                        }
                                        ?>
                                        <?= formatDistance($ltaLength); ?>
                                    </div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">
                                    Jarak LTA
                                </div>
                            </div>
                            <!--end::Project Length-->
                        <?php endif; ?>

                        <?php if ($mappingStatusIndex > 3) : ?>
                            <!--begin::Project Length-->
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">
                                        <?php
                                        $surveyLength = 0;
                                        ?>
                                        <?= formatDistance($surveyLength); ?>
                                    </div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">
                                    Jarak Ukur
                                </div>
                            </div>
                            <!--end::Project Length-->
                        <?php endif; ?>

                        <!--begin::Project Cost (V1 only)-->
                        <?php if ($data->project_costs !== null) { ?>
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="fs-4 fw-bold">
                                        <?php echo formatCurrency($data->project_costs ?? 0) ?>
                                    </div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-400">Kos Projek</div>
                            </div>
                        <?php } ?>
                        <!--end::Project Cost / Security Deposit-->
                    </div>
                    <!--end::Stats-->
                </div>
                <!--end::Info Stats-->
            </div>
            <!--end::Info Section-->
        </div>
        <!--end::Details Section-->

        <div class="separator"></div>

        <!--begin::Navigation Tabs-->
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <!--begin::Applications Tab-->
            <li class="nav-item me-6">
                <div class="d-flex flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="fad fa-rectangle-history-circle-user fs-1 me-2"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab" href="#tab_1">
                            Butiran Permohonan
                        </a>
                    </div>
                </div>
            </li>
            <!--end::Applications Tab-->

            <!--begin::Maps & GIS Tab-->
            <li class="nav-item me-6">
                <div class="d-flex flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="fad fa-map-location fs-1 me-2"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_2">
                            Butiran GIS & Jalan
                        </a>
                    </div>
                </div>
            </li>
            <!--end::Maps & GIS Tab-->

            <!--begin::Attachments Tab-->
            <li class="nav-item me-6">
                <div class="d-flex flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="fad fa-folder-open fs-1 me-2"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_3">
                            Senarai Lampiran
                        </a>
                    </div>
                </div>
            </li>
            <!--end::Attachments Tab-->

            <!--begin::Chronology Tab-->
            <li class="nav-item me-6">
                <div class="d-flex flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="fad fa-timeline fs-1 me-2"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_4">
                            Kronologi Permohonan
                        </a>
                    </div>
                </div>
            </li>
            <!--end::Chronology Tab-->

            <!--begin::Workflow Tab-->
            <li class="nav-item me-6">
                <div class="d-flex flex-wrap">
                    <div class="d-flex align-items-center">
                        <i class="fad fa-chart-gantt fs-1 me-2"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <a class="nav-link text-active-primary py-5 me-6" data-bs-toggle="tab" href="#tab_5">
                            Aliran Kerja
                        </a>
                    </div>
                </div>
            </li>
            <!--end::Workflow Tab-->
        </ul>
        <!--end::Navigation Tabs-->
    </div>
    <!--end::Card Body-->
</div>
<!--end::Project Details Card-->

<!--begin::Tab Content-->
<div class="tab-content" id="myTabContent">
    <!--begin::Applications Tab-->
    <div class="tab-pane fade show active" id="tab_1">
        <?php
        if ($data && isset($systemId)) {
            $details->applications($systemId);
        } else { ?>
            <div data-kt-search-element="empty" class="text-center card">
                <div class="fw-semibold py-10">
                    <div class="text-gray-600 fs-3 mb-2">Tiada Data Bagi Butiran Permohonan</div>
                    <div class="text-muted fs-6">Sila isi butiran ini untuk paparkan data...</div>
                </div>
                <div class="text-center px-5">
                    <img src="assets/media/illustrations/empty/Error.svg" alt="" class="w-100 h-200px" />
                </div>
            </div>
        <?php } ?>
    </div>
    <!--end::Applications Tab-->

    <!--begin::Maps & GIS Tab-->
    <div class="tab-pane fade" id="tab_2">
        <?php
        if (!empty($systemId)) {
            $details->maps($systemId);
        } else {
            echo '<!-- ERROR: System ID not available for maps -->';
            echo '<div class="alert alert-warning">System ID tidak tersedia untuk paparan peta.</div>';
        }
        ?>
    </div>
    <!--end::Maps & GIS Tab-->

    <!--begin::Attachments Tab-->
    <div class="tab-pane fade" id="tab_3">
        <?php
        if ($data && isset($systemId)) {
            $details->attachments($systemId);
        } else { ?>
            <div data-kt-search-element="empty" class="text-center card">
                <div class="fw-semibold py-10">
                    <div class="text-gray-600 fs-3 mb-2">Tiada Data Bagi Senarai Lampiran</div>
                    <div class="text-muted fs-6">Sila isi butiran ini untuk paparkan data...</div>
                </div>
                <div class="text-center px-5">
                    <img src="assets/media/illustrations/empty/Files.svg" alt="" class="w-100 h-200px" />
                </div>
            </div>
        <?php } ?>
    </div>
    <!--end::Attachments Tab-->

    <!--begin::Chronology Tab-->
    <div class="tab-pane fade" id="tab_4">
        <?php
        if (isset($systemId)) {
            $details->chronology($systemId);
        } else { ?>
            <div data-kt-search-element="empty" class="text-center card">
                <div class="fw-semibold py-10">
                    <div class="text-gray-600 fs-3 mb-2">Tiada Data Bagi Kronologi Permohonan</div>
                    <div class="text-muted fs-6">Sila isi butiran ini untuk paparkan data...</div>
                </div>
                <div class="text-center px-5">
                    <img src="assets/media/illustrations/empty/History.svg" alt="" class="w-100 h-200px" />
                </div>
            </div>
        <?php } ?>
    </div>
    <!--end::Chronology Tab-->

    <!--begin::Workflow Tab-->
    <div class="tab-pane fade" id="tab_5">
        <?php
        if (isset($systemId)) {
            $details->workflow($systemId);
        } else { ?>
            <div data-kt-search-element="empty" class="text-center card">
                <div class="fw-semibold py-10">
                    <div class="text-gray-600 fs-3 mb-2">Tiada Data Bagi Aliran Kerja</div>
                    <div class="text-muted fs-6">Sila isi butiran ini untuk paparkan data...</div>
                </div>
                <div class="text-center px-5">
                    <img src="assets/media/illustrations/empty/Validation.svg" alt="" class="w-100 h-200px" />
                </div>
            </div>
        <?php } ?>
    </div>
    <!--end::Workflow Tab-->
</div>
<!--end::Tab Content-->