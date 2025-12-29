<?php 
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
include "config/autoload.php";

$systemId = $_GET['sid'] ?? null;
$folderExists = false;

if($systemId) {
    $ftp = new FTPConnectionFactory();
    $store = $ftp->createConnection();
    if (!$store) {
        return "Cannot connect to FTP server";
    }
    // FTP directory structure
    $year = date("Y", strtotime("2025-06-15")); // Example date, replace with actual date if needed
    $baseDir = '/ftp';
    
    $directories = [
        $baseDir,
        "$baseDir/Projects",
        "$baseDir/Projects/$year",
        "$baseDir/Projects/$year/Documents",
        "$baseDir/Projects/$year/Documents/Submission",
        "$baseDir/Projects/$year/Documents/Plan",
        "$baseDir/Projects/$year/Geospatial",
        "$baseDir/Projects/$year/Geospatial/Maps",
        "$baseDir/Projects/$year/Geospatial/GISReady",
        "$baseDir/Projects/$year/Reports",
        "$baseDir/Projects/$year/Reports/SiteVisit",
        "$baseDir/Projects/$year/Reports/Survey",
    ];

        foreach ($directories as $dir) {
        
        // Check directory existence
        if (!ftp_chdir($store, $dir)) {
            // Try to create directory
            if (!ftp_mkdir($store, $dir)) {
                error_log("FTP mkdir failed: $dir");
                ftp_close($store);
                return false; // Failed to create directory
            }

            $systemType = ftp_systype($ftp);
            if (stripos($systemType, 'Windows') === false) {
                ftp_chmod($ftp, 0755, $path);
            }
        }else{
            error_log($dir . " does not exist. Creating...\n");
        }
    }

    ftp_close($store);
    $folderExists = true; // All directories exist or created successfully -->
}

echo json_encode(array(
    "success" => true,
    "folderExists" => $folderExists
));

?>


    