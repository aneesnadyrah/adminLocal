<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "config/system.php";
require_once "config/functions.php";

$system = new System;

// Function to extract and validate UUID from URL
function extractUUID()
{
    $url = $_SERVER['REQUEST_URI'];
    $segments = explode('/', trim($url, '/'));
    $lastSegment = end($segments);

    // Validate UUID format (8-4-4-4-12 pattern)
    if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $lastSegment)) {
        return $lastSegment;
    }

    return null;
}

function getSystemId($uuid)
{
    $conn = General::connectToDatabase();

    $query = "SELECT system_id FROM flw_appl_plan WHERE sharing_code = :uuid";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn();
}

function formatCurrency($amount)
{
    if (!is_numeric($amount) || $amount <= 0) return 'RM -';
    return 'RM ' . number_format($amount, 2);
}

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
 * Format distance safely  
 */
function formatDistance($distance, $unit = 'm')
{
    if (!is_numeric($distance) || $distance <= 0) return '0 ' . $unit;
    return number_format($distance, 0) . ' ' . $unit;
}

function getFileSize($url) {

    // Get headers
    $headers = get_headers($url, 1);

    if (isset($headers['Content-Length'])) {
        $size = $headers['Content-Length']; // in bytes

        return round($size / 1048576, 2) . " MB\n";
    } else {
        return "0.00 MB\n";
    }
}

// Function to get attachments by sharing code
function getAttachmentsBySharingCode($uuid)
{

    $conn = General::connectToDatabase();
    // Single optimized query with JOIN instead of UNION
    $query = "SELECT a.url, a.attachment_type, a.mime_type, a.created_date, a.name FROM flw_appl_plan p LEFT JOIN flw_appl_attachments a ON p.system_id = a.system_id
    WHERE p.sharing_code = :uuid AND a.attachment_type IN (15, 16, 86) AND a.mime_type = 'application/pdf' ORDER BY a.attachment_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();

    $attachments = [];
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        // Decode URL inline during fetch
        $decodedUrl = base64_decode($row->url, true);

        if ($decodedUrl !== false) {
            $attachments[] = [
                'url' => $decodedUrl,
                'type' => $row->attachment_type,
                'name' => $row->name,
                'created_at' => $row->created_date,
                'mime' => $row->mime_type
            ];
        }
    }

    return $attachments;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/media/logos/<?= strtolower($system->App->title) ?>-small.svg" />
    <title><?= $system->App->title ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Dark mode configuration for Tailwind
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            position: relative;
        }

        /* Grain texture overlay - FIXED */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            opacity: 0.08;
            pointer-events: none;
            z-index: 1;
        }

        /* Dark mode grain texture */
        .dark body::before {
            opacity: 0.05;
        }

        /* Container with proper z-index */
        .content-wrapper {
            position: relative;
            z-index: 2;
        }

        /* Glassmorphism blur effect for cards */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Dark mode glass card */
        .dark .glass-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(71, 85, 105, 0.3);
        }

        /* Subtle gradient overlay */
        .gradient-overlay {
            position: relative;
            overflow: hidden;
        }

        .gradient-overlay::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(22, 186, 137, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Hatching pattern - increased width */
        .hatching-pattern {
            background-image:
                repeating-linear-gradient(45deg,
                    transparent,
                    transparent 8px,
                    rgba(22, 186, 137, 0.04) 8px,
                    rgba(22, 186, 137, 0.04) 10px);
        }

        /* Dark mode hatching pattern */
        .dark .hatching-pattern {
            background-image:
                repeating-linear-gradient(45deg,
                    transparent,
                    transparent 8px,
                    rgba(22, 186, 137, 0.06) 8px,
                    rgba(22, 186, 137, 0.06) 10px);
        }

        /* Grain texture for specific elements - using ::before */
        .grain-texture {
            position: relative;
        }

        .grain-texture::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='grainFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.95' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23grainFilter)'/%3E%3C/svg%3E");
            opacity: 0.12;
            pointer-events: none;
            border-radius: inherit;
            z-index: 1;
        }

        /* Dark mode grain texture */
        .dark .grain-texture::before {
            opacity: 0.08;
        }

        /* Ensure card content is above grain */
        .grain-texture>* {
            position: relative;
            z-index: 2;
        }

        /* Blur effect on hover with smooth transition */
        .hover-blur {
            transition: backdrop-filter 0.3s ease, transform 0.3s ease;
        }

        .hover-blur:hover {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transform: translateY(-1px);
        }

        /* Smooth transitions for buttons */
        button {
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease, opacity 0.3s ease;
        }

        /* Smooth transitions for theme toggle */
        body,
        .glass-card,
        .grain-texture {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        /* Mouse glow effect - ONLY on border, NOT inside */
        .glow-card {
            position: relative;
        }

        .glow-card::after {
            content: '';
            position: absolute;
            inset: -4px;
            background: radial-gradient(400px circle at var(--mouse-x, 50%) var(--mouse-y, 30%),
                    rgba(22, 186, 137, 1),
                    transparent 40%);
            border-radius: 1rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
            pointer-events: none;
            filter: blur(15px);
            mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            mask-composite: exclude;
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            padding: 4px;
        }

        .glow-card:hover::after {
            opacity: 0.8;
        }

        /* Dark mode glow effect - stronger */
        .dark .glow-card::after {
            background: radial-gradient(600px circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
                    rgba(22, 186, 137, 1),
                    transparent 40%);
            filter: blur(20px);
        }

        /* Ensure grain texture doesn't hide glow */
        .grain-texture {
            position: relative;
        }

        /* Custom brand color utilities */
        .bg-brand {
            background-color: #16ba89;
        }

        .bg-brand-dark {
            background-color: #0d9470;
        }

        .text-brand {
            color: #16ba89;
        }

        .border-brand {
            border-color: #16ba89;
        }

        .hover\:bg-brand:hover {
            background-color: #16ba89;
        }

        .hover\:bg-brand-dark:hover {
            background-color: #0d9470;
        }

        /* Dark mode toggle button */
        .dark-mode-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 50;
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dark .dark-mode-toggle {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(71, 85, 105, 0.3);
        }

        .dark-mode-toggle:hover {
            transform: scale(1.1);
        }
    </style>
</head>

<?php

// Main execution
$uuid = extractUUID();
$systemId = getSystemId($uuid);

$attachments = getAttachmentsBySharingCode($uuid);

$detail =  new ProjectDetails();
$project = $detail->getProjectDetails($systemId);

// $contact = Survey::projectDetails($detail[0]['system_id'], '3', '0');
// $attach_udm = Survey::projectDetails($detail[0]['system_id'], '2', '1');
// $attach_tpm = Survey::projectDetails($detail[0]['system_id'], '2', '2');

$provider = General::getProvider($project->provider_id);

?>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">

    <!-- Background effects container -->
    <div class="fixed inset-0 gradient-overlay hatching-pattern pointer-events-none" style="z-index: 0;"></div>

    <!-- Dark Mode Toggle Button -->
    <button id="darkModeToggle" class="dark-mode-toggle" aria-label="Toggle dark mode">
        <svg id="sunIcon" class="w-5 h-5 text-gray-800 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <svg id="moonIcon" class="w-5 h-5 text-gray-300 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </button>

    <div class="content-wrapper max-w-6xl mx-auto px-4 py-12 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="glass-card hover-blur glow-card rounded-2xl mb-8 grain-texture">
            <div class="flex items-center gap-5 p-8">
                <div class="w-30px h-30px rounded-xl bg-slate-100 flex items-center justify-center">
                    <img src="<?= $provider->logo ?>" alt="<?= $provider->name ?>" width="200px" height="200px"/>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100"><?= $project->reference_no ?></h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 my-2"><?= $project->project_title ?></p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-brand/10 text-brand border border-brand/20"><?= $project->status ?></span>
                </div>
            </div>
        </div>

        <?php
        if (!empty($project->application_date)) {
            $date = date('d/m/Y', strtotime($project->application_date));
        } else {
            $date = '-';
        }
        ?>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <div class="glass-card hover-blur glow-card p-6 rounded-xl grain-texture">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100"><?= $date ?></div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider mt-2">Tarikh Mohon</div>
            </div>
            <div class="glass-card hover-blur glow-card p-6 rounded-xl grain-texture">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalSize"><?= formatDistance($project->application_length ?? 0); ?></div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider mt-2">Jarak Permohonan</div>
            </div>
            <div class="glass-card hover-blur glow-card p-6 rounded-xl grain-texture">
                <div class="text-3xl font-bold text-brand" id="price"><?= formatDistance($project->lta_length ?? 0); ?></div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider mt-2">Jarak LTA</div>
            </div>
            <div class="glass-card hover-blur glow-card p-6 rounded-xl grain-texture">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100"><?= formatCurrency($project->project_costs ?? 0)  ?></div>
                <div class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider mt-2">Kos Projek</div>
            </div>
        </div>


        <!-- Files List -->
        <div class="glass-card glow-card rounded-2xl grain-texture">
            <div class="px-8 py-5 border-b border-gray-200/50 dark:border-gray-700/50">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Senarai Perkongsian Dokumen</h2>
            </div>
            <div class="divide-y divide-gray-200/50 dark:divide-gray-700/50">

                <?php foreach($attachments as $row) :
                    // Determine file display name
                    if ($row['type'] == '15') {
                        $fileDisplay = 'Pelan Infrastruktur Utiliti';
                        $iconColor = 'primary';
                    } else if ($row['type'] == '16') {
                        $fileDisplay = 'Pelan Kawalan Trafik';
                        $iconColor = 'success';
                    } else if ($row['type'] == '86') {
                        $fileDisplay = 'Pelan Siap Bina';
                        $iconColor = 'info';
                    } else {
                        $fileDisplay = 'Tidak Diketahui';
                        $iconColor = 'secondary';
                    }

                    if($row['mime'] == 'application/pdf') {
                        $fileExtension = 'PDF';
                    } else if ($row['mime'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                        $fileExtension = 'DOCX';
                    } else if ($row['mime'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        $fileExtension = 'XLSX';
                    } else if ($row['mime'] == 'application/zip') {
                        $fileExtension = 'ZIP';
                    } else if ($row['mime'] == 'image/jpeg') {
                        $fileExtension = 'JPG';
                    } else if ($row['mime'] == 'image/png') {
                        $fileExtension = 'PNG';
                    } else {
                        $fileExtension = 'DAT';
                    }
                ?>

                <!--begin::Hidden iframe-->
                <div style="display: none;">
                    <div id="<?= $row['type']; ?>">
                        <iframe
                            class="scroll w-100 h-100 rounded-xl"
                            src="<?=$row['url']; ?>"
                            width="940"
                            height="680"
                            frameBorder="0"
                            allow="autoplay; fullscreen"
                            allowfullscreen="allowfullscreen"
                        ></iframe>
                    </div>
                </div>
                <!--end::Hidden iframe-->

                <div class="flex items-center justify-between p-6 hover:bg-white/30 dark:hover:bg-gray-800/30 transition-all">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-12 h-12 rounded-lg bg-gray-100/80 dark:bg-gray-800/80 text-gray-700 dark:text-gray-300 flex items-center justify-center text-xs font-bold flex-shrink-0 border border-gray-200/50 dark:border-gray-700/50 backdrop-blur-sm">
                            <?= $fileExtension ?>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate"><?= $fileDisplay ?></div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1"><?= getFileSize($row['url']) ?> • <?= date('d/m/Y', strtotime($row['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="<?= $row['url'] ?>" class="px-4 py-2 text-xs font-medium text-white bg-brand hover:bg-brand-dark rounded-lg transition-colors">
                            Lihat
                        </a>
                    </div>
                </div>

                <?php endforeach; ?>


            </div>
        </div>

        <!-- Footer -->
        <div class="text-center py-5 text-sm text-gray-500 dark:text-gray-400">
            <p>&copy; <?= date('Y') .' '. $system->App->title ?></p>
        </div>

    </div>

    <script src="<?= $system->App->url ?>/assets/plugins/custom/fslightbox/fslightbox.bundle.js"></script>
    <script>
        // Mouse glow effect tracking
        document.addEventListener('mousemove', (e) => {
            const cards = document.querySelectorAll('.glow-card');

            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        // Dark mode functionality with auto-detection
        const darkModeToggle = document.getElementById('darkModeToggle');
        const htmlElement = document.documentElement;

        // Function to set dark mode
        function setDarkMode(isDark) {
            if (isDark) {
                htmlElement.classList.add('dark');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                htmlElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        // Check for saved preference or system preference
        function initializeDarkMode() {
            const savedMode = localStorage.getItem('darkMode');

            if (savedMode === 'enabled') {
                setDarkMode(true);
            } else if (savedMode === 'disabled') {
                setDarkMode(false);
            } else {
                // Auto-detect system preference
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                setDarkMode(prefersDark);
            }
        }

        // Toggle dark mode
        darkModeToggle.addEventListener('click', () => {
            const isDark = htmlElement.classList.contains('dark');
            setDarkMode(!isDark);
        });

        // Listen for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            // Only auto-switch if user hasn't manually set a preference
            if (!localStorage.getItem('darkMode')) {
                setDarkMode(e.matches);
            }
        });

        // Initialize dark mode on page load
        initializeDarkMode();
    </script>
</body>

</html>