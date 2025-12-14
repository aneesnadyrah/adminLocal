<?php
/**
 * File Upload Handler
 * Optimized version with improved structure and error handling
 */

// Set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

// Load dependencies
require "api/header.php";
require "config/system.php";
include_once "config/functions.php";
include_once "api/functions.php";
require_once "config/DBFactory.php";

// Initialize system
$System   = new System;
$tenant   = $System->App->title;
$ftpHost  = $System->FTPConnection->host;
$ftpPath  = $System->FTPConnection->path;

// ============================================================================
// CONFIGURATION ARRAYS
// ============================================================================

/**
 * Folder configuration mapping
 * Maps folder codes to their type IDs and success messages
 */
$folderConfig = [
    'SRIL'    => ['type' => 1],
    'PP'      => ['type' => 2],
    'PCT'     => ['type' => 3],
    'GL'      => ['type' => 4],
    'AKP'     => ['type' => 5, 'message' => 'Fail Arahan Kerja PIL Berjaya Dimuat Naik 🎉'],
    'PCL'     => ['type' => 6, 'message' => 'Fail Pelan Cadangan Laluan Berjaya Dimuat Naik 🎉'],
    'LLTA'    => ['type' => 7],
    'PIL'     => ['type' => 8, 'message' => 'Fail Pelan Izin Lalu Berjaya Dimuat Naik 🎉'],
    'PPCT'    => ['type' => 9],
    'SH'      => ['type' => 10, 'message' => 'Sebut Harga Berjaya Dimuat Naik 🎉'],
    'SHD'     => ['type' => 11],
    'ICPP'    => ['type' => 12, 'message' => 'Invois Caj Pendaftaran Berjaya Dimuat Naik 🎉'],
    'RCPP'    => ['type' => 13, 'message' => 'Invois Bayaran Terima Berjaya Dimuat Naik 🎉'],
    'PIU'     => ['type' => 15, 'message' => 'Fail PIU Berjaya Dimuat Naik 🎉'],
    'PPT'     => ['type' => 16, 'message' => 'Fail PPT Berjaya Dimuat Naik 🎉'],
    'SKU'     => ['type' => 17],
    'SKIL'    => ['type' => 18, 'message' => 'Surat Kelulusan Izin Lalu Berjaya Dimuat Naik 🎉'],
    'MKIL'    => ['type' => 19, 'message' => 'Surat Maklum Balas Kelulusan Izin Lalu Berjaya Dimuat Naik 🎉'],
    'ASPKIL'  => ['type' => 20, 'message' => 'Bukti Penghantaran Surat Permohonan KIL Berjaya Dimuat Naik 🎉'],
    'ICP'     => ['type' => 21, 'message' => 'Invois Caj Pendaftaran Berjaya Dimuat Naik 🎉'],
    'AKO'     => ['type' => 22, 'message' => 'Fail Arahan Kerja Operasi Berjaya Dimuat Naik 🎉'],
    'RCP'     => ['type' => 23, 'message' => 'Resit Bayaran Caj Pendaftaran Berjaya Dimuat Naik 🎉'],
    'SPKIL'   => ['type' => 25, 'message' => 'Surat Permohonan Kelulusan Izin Lalu Berjaya Dimuat Naik 🎉'],
    'RP'      => ['type' => 26, 'message' => 'Ringkasan Projek dan Wang Cagaran Berjaya Dimuat Naik 🎉'],
    'KWC'     => ['type' => 27, 'message' => 'Kiraan Wang Cagaran Berjaya Dimuat Naik 🎉'],
    'RDPIU'   => ['type' => 33],
    'SPKPK'   => ['type' => 38, 'message' => 'Surat Permohonan Kelulusan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'ASPKPK'  => ['type' => 39, 'message' => 'Akuan Serahan Permohonan Kelulusan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'SKPK'    => ['type' => 40, 'message' => 'Surat Kelulusan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'PPK'     => ['type' => 41, 'message' => 'Perakuan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'ASPPK'   => ['type' => 42, 'message' => 'Akuan Serahan Perakuan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'SRPLPK'  => ['type' => 43, 'message' => 'Permohonan Lanjutan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'SPLPK'   => ['type' => 44, 'message' => 'Surat Permohonan Lanjutan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'SKLPK'   => ['type' => 45, 'message' => 'Surat Kelulusan Lanjutan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'PLPK'    => ['type' => 46, 'message' => 'Perakuan Lanjutan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'NMK'     => ['type' => 47, 'message' => 'Notis Mula Kerja Berjaya Dimuat Naik 🎉'],
    'SMMK'    => ['type' => 48, 'message' => 'Surat Makluman Mula Kerja Berjaya Dimuat Naik 🎉'],
    'ASMMK'   => ['type' => 49, 'message' => 'Akuan Serahan Makluman Mula Kerja Berjaya Dimuat Naik 🎉'],
    'NSK'     => ['type' => 50, 'message' => 'Notis Siap Kerja Berjaya Dimuat Naik 🎉'],
    'SRPSSK'  => ['type' => 51, 'message' => 'Permohonan Sijil Siap Kerja Berjaya Dimuat Naik 🎉'],
    'PHDD'    => ['type' => 55, 'message' => 'Profil HDD Berjaya Dimuat Naik 🎉'],
    'SPSSK'   => ['type' => 56, 'message' => 'Surat Permohonan Sijil Siap Kerja Berjaya Dimuat Naik 🎉'],
    'ASPSSK'  => ['type' => 57, 'message' => 'Akuan Serahan Permohonan Sijil Siap Kerja Berjaya Dimuat Naik 🎉'],
    'ASPKSSK' => ['type' => 57, 'message' => 'Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja Berjaya Dimuat Naik 🎉'],
    'SKPSSK'  => ['type' => 58, 'message' => 'Surat Kelulusan Permohonan Sijil Siap Kerja Berjaya Dimuat Naik 🎉'],
    'PSSK'    => ['type' => 59, 'message' => 'Perakuan Sijil Siap Kerja Berjaya Dimuat Naik 🎉'],
    'SRPSSMK' => ['type' => 60, 'message' => 'Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik 🎉'],
    'SPSSMK'  => ['type' => 62, 'message' => 'Surat Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik 🎉'],
    'ASPSSMK' => ['type' => 63, 'message' => 'Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik 🎉'],
    'UPSSMK'  => ['type' => 64, 'message' => 'Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan Berjaya Dimuat Naik 🎉'],
    'SPCCC'   => ['type' => 65, 'message' => 'Surat Permohonan Sijil Sempurna Kerja Berjaya Dimuat Naik 🎉'],
    'ASPCCC'  => ['type' => 66, 'message' => 'Akuan Serahan Permohonan Sijil Sempurna Kerja Berjaya Dimuat Naik 🎉'],
    'SKPCCC'  => ['type' => 67, 'message' => 'Surat Kelulusan Permohonan Sijil Sempurna Kerja Berjaya Dimuat Naik 🎉'],
    'PCCC'    => ['type' => 68, 'message' => 'Perakuan Sijil Sempurna Kerja Berjaya Dimuat Naik 🎉'],
    'SRPWC'   => ['type' => 69, 'message' => 'Permohonan Kelulusan Pemulangan Wang Cagaran Berjaya Dimuat Naik 🎉'],
    'SPPWC'   => ['type' => 74, 'message' => 'Surat Permohonan Pemulangan Wang Cagaran Berjaya Dimuat Naik 🎉'],
    'ASPPWC'  => ['type' => 75, 'message' => 'Akuan Serahan Permohonan Pemulangan Wang Cagaran Berjaya Dimuat Naik 🎉'],
    'BPWC'    => ['type' => 76, 'message' => 'Baucer Pemulangan Wang Cagaran Berjaya Dimuat Naik 🎉'],
    'ASPLPK'  => ['type' => 78, 'message' => 'Akuan Serahan Permohonan Lanjutan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'ASLPPK'  => ['type' => 79, 'message' => 'Akuan Serahahan Lanjutan Perakuan Permit Kerja Berjaya Dimuat Naik 🎉'],
    'ASPKCCC' => ['type' => 81, 'message' => 'Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja Berjaya Dimuat Naik 🎉'],
    'CDPSB'   => ['type' => 83, 'message' => 'Pelan Siap Bina Berjaya Dimuat Naik 🎉'],
    'PSB'     => ['type' => 86, 'message' => 'Pelan Siap Bina Berjaya Dimuat Naik 🎉'],
    'DLOT'    => ['type' => 87, 'message' => 'Fail Data Lot Berjaya Dimuat Naik 🎉'],
];

/**
 * Folder path configuration
 * Defines which folders go to which paths and whether they need authority ID
 */
$folderPathConfig = [
    // Submission folders (without authority)
    'submission' => [
        'folders' => ['AKP', 'ICPP', 'RCPP', 'NMK', 'SRPLPK', 'NSK', 'SRPSSK', 'PSB', 'PHDD', 'SRPSSMK', 'SRPWC'],
        'path' => 'Documents/Submission/',
        'useAuthority' => false
    ],
    // Plan folders
    'plan' => [
        'folders' => ['PCL', 'PIL', 'DLOT'],
        'path' => 'Documents/Plan/',
        'useAuthority' => false
    ],
    // Submission folders (with authority)
    'submissionWithAuthority' => [
        'folders' => [
            'SPKIL', 'RP', 'KWC', 'ASPKIL', 'SKIL', 'MKIL', 'SPKPK', 'ASPKPK', 'SKPK', 'PPK', 'ASPPK',
            'SMMK', 'ASMMK', 'SPLPK', 'ASPLPK', 'SKLPK', 'PLPK', 'ASLPPK', 'SPSSK', 'ASPSSK',
            'SKPSSK', 'PSSK', 'ASPKSSK', 'SPSSMK', 'ASPSSMK', 'UPSSMK', 'SPCCC', 'ASPCCC',
            'SKPCCC', 'PCCC', 'ASPKCCC', 'SPPWC', 'ASPPWC', 'BPWC'
        ],
        'path' => 'Documents/Submission/',
        'useAuthority' => true
    ],
    // Special folders with custom paths
    'piu_ppt' => [
        'folders' => ['PIU', 'PPT'],
        'path' => 'Documents/Plan/',
        'useAuthority' => false
    ],
    'rdpiu' => [
        'folders' => ['RDPIU'],
        'path' => 'Reports/Survey/',
        'useAuthority' => false
    ]
];

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

/**
 * Send JSON response and exit
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function sendError($message, $statusCode = 400) {
    sendJsonResponse(['error' => $message], $statusCode);
}

/**
 * Get folder path configuration
 */
function getFolderPathConfig($folder, $folderPathConfig) {
    foreach ($folderPathConfig as $config) {
        if (in_array($folder, $config['folders'])) {
            return $config;
        }
    }
    // Default configuration
    return ['path' => '', 'useAuthority' => false];
}

/**
 * Create FTP directory if it doesn't exist
 */
function ensureFtpDirectory($ftpConn, $directory) {
    if (!ftp_chdir($ftpConn, $directory)) {
        if (ftp_mkdir($ftpConn, $directory)) {
            ftp_chdir($ftpConn, $directory);
            ftp_chmod($ftpConn, 0755, $directory);
            return true;
        }
        return false;
    }
    return true;
}

// ============================================================================
// MAIN PROCESSING
// ============================================================================

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Invalid request method');
}

// Validate required POST data
$systemId = $_POST['systemId'] ?? null;
$folder = $_POST['folder'] ?? null;
$authorityId = $_POST['authorityId'] ?? null;

// Validate uploaded file
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    sendError('File upload error');
}

$file = $_FILES['file'];
$fileName = $file['name'];
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// *** ADD THIS: Skip .gpkg files ***
if ($extension === 'gpkg') {
    sendJsonResponse([
        'success' => true,
        'skipped' => true,
        'message' => 'GPKG files are skipped',
        'fileName' => $fileName
    ]);
}

// Validate folder configuration
if (!isset($folderConfig[$folder])) {
    sendError('Invalid folder type');
}

if (!$systemId || !$folder) {
    sendError('Missing required parameters: systemId or folder');
}

$type = $folderConfig[$folder]['type'];
$message = $folderConfig[$folder]['message'] ?? null;

// ============================================================================
// DATABASE OPERATIONS
// ============================================================================

try {
    // Connect to database
    $db = new DBConnectionFactory();
    $conn = $db->createConnection();
    
    // Get application date
    $stmt = $conn->prepare("SELECT application_date FROM flw_appl_entries WHERE system_id = :systemId");
    $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        sendError('System ID not found in database');
    }
    
    $date = $row['application_date'];
    $year = substr($date, 0, 4);
    
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
}

// ============================================================================
// FILE PATH CONFIGURATION
// ============================================================================

// Get folder path configuration
$pathConfig = getFolderPathConfig($folder, $folderPathConfig);
$basePath = '/projects/' . $year . '/' . $systemId . '/';
$folderPath = $basePath . $pathConfig['path'];

// Build filename
$baseFileName = Utilities::extractSystemId($systemId, 'digits') . '-' . $folder;
$newFileName = $baseFileName;

if ($pathConfig['useAuthority'] && $authorityId) {
    $newFileName .= '-' . $authorityId;
}

$remoteFilePath = $folderPath . $newFileName . '.' . $extension;

// ============================================================================
// FTP UPLOAD
// ============================================================================

try {
    // Create FTP connection
    $ftp = new FTPConnectionFactory();
    $ftpConn = $ftp->createConnection();
    
    if (!$ftpConn) {
        throw new Exception('Failed to connect to FTP server');
    }
    
    // Enable passive mode
    ftp_pasv($ftpConn, true);
    
    // Create directory structure
    if (!ensureFtpDirectory($ftpConn, 'Projects')) {
        throw new Exception('Failed to create Projects directory');
    }
    
    if (!ensureFtpDirectory($ftpConn, $year)) {
        throw new Exception('Failed to create year directory');
    }
    
    if (!ensureFtpDirectory($ftpConn, $systemId)) {
        throw new Exception('Failed to create system ID directory');
    }
    
    // Upload file
    if (!ftp_put($ftpConn, $remoteFilePath, $file['tmp_name'], FTP_BINARY)) {
        throw new Exception('Failed to upload file to FTP server');
    }
    
    // Set file permissions
    ftp_chmod($ftpConn, 0755, $remoteFilePath);
    
    // Generate encoded URL
    $encodedUrl = base64_encode('https://' . $ftpHost . $remoteFilePath);
    
    // Close FTP connection
    ftp_close($ftpConn);
    
} catch (Exception $e) {
    sendError('FTP error: ' . $e->getMessage(), 500);
}

// ============================================================================
// FILE METADATA
// ============================================================================

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

// Get file size
$fileSize = filesize($file['tmp_name']);

// ============================================================================
// SAVE TO DATABASE
// ============================================================================

try {
    // Get attachment details
    $stmt = $conn->prepare("SELECT * FROM ls_attachments WHERE code_name = :folder");
    $stmt->bindParam(':folder', $folder);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$row) {
        throw new Exception('Attachment type not found');
    }
    
    $attachId = $row['id'];
    $attachDetails = $row['details'];
    
    // Prepare insert data
    $created = date('Y-m-d H:i:s');
    $attachDate = date('Y-m-d');
    $user = $_SESSION['username'] ?? 'system';
    
    // Insert attachment record
    $query = "INSERT INTO flw_appl_attachments 
              (system_id, name, url, attachment_date, user_added, created_date, attachment_type, mime_type, size, authority) 
              VALUES (:systemId, :fileName, :url, :attachDate, :user, :created, :attachId, :mimeType, :fileSize, :authorityId)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId);
    $stmt->bindParam(':fileName', $fileName);
    $stmt->bindParam(':url', $encodedUrl);
    $stmt->bindParam(':attachDate', $attachDate);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':created', $created);
    $stmt->bindParam(':attachId', $attachId);
    $stmt->bindParam(':mimeType', $mimeType);
    $stmt->bindParam(':fileSize', $fileSize);
    $stmt->bindParam(':authorityId', $authorityId);
    
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception('Failed to save attachment to database');
    }
    
    // Close database connection
    $conn = null;
    
} catch (PDOException $e) {
    sendError('Database error: ' . $e->getMessage(), 500);
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}

// ============================================================================
// SEND SUCCESS RESPONSE
// ============================================================================

sendJsonResponse([
    'success' => true,
    'systemID' => $systemId,
    'message' => $message ?? 'File uploaded successfully',
    'attachDetails' => $fileName,
    'url' => $encodedUrl,
    'mimeType' => $mimeType,
    'fileSize' => $fileSize
]);