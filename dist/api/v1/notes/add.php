<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require_once "api/header.php";
require_once "config/system.php";
require_once "api/functions.php";
require_once "config/DBFactory.php";

// Connect to the database using PDO
$db = new DBConnectionFactory();
$chronology = new Chronology();
$flow = new FlowStatuses($username);
$changelog = new Changelog($username);
$System   = new System;
$tenant   = $System->App->title;
$ftpHost  = $System->FTPConnection->host;
$ftpPath  = $System->FTPConnection->path;
$ftp      = new FTPConnectionFactory();
$ftpConn  = $ftp->createConnection();
$conn = $db->createConnection();

$data = file_get_contents("php://input");
$POST = json_decode($data, true);

if (isset($_GET['type'])) {

    if($_GET['type'] == "write"){

        $systemId = $POST['system-id'];
        $details = $POST['notes'];
        $timestamp = date('Y-m-d H:i:s', time());
        $authorities = isset($POST['authorities']) ? $POST['authorities'] : null;

            $note = 'Catatan Tambahan : '. $details;
            $stmt = $conn->prepare("SELECT department FROM ls_user_roles WHERE id = :roleId");
            $stmt->bindParam(':roleId', $roleId);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $department = $result["department"];

            $currentStatus = $flow->getCurrentFlow($systemId, $department);

            if($authorities === null){
                $chronoId = $chronology->create($username, $systemId, $currentStatus->status, 'notes');
            } else {
                $chronoId = $chronology->create($username, $systemId, $currentStatus->status, 'notes', $authorities);
            }

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id) VALUES (:systemId, :details, :created,:authorities, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $note);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':authorities', $authorities);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Menambah Catatan bagi projek ' . $systemId;
            $pages = 'modal_catatan';
            $changelog->userActivity($text, $pages);

            $changelog->projectActivity($systemId, $currentStatus->status, $note);


            $message = 'Catatan Bertulis Berjaya Ditambah!🎉';

            http_response_code(200);
            echo json_encode(
                array(
                    "type" => 'bertulis',
                    "message" => $message,
                    "status" => 200,
                )
            );



    } else if($_GET['type'] == "attachment"){
        $systemId = $POST['system-id'];
        $details = $POST['notes'];
        $timestamp = date('Y-m-d H:i:s', time());
        $authorities = isset($POST['authorities']) ? $POST['authorities'] : null;
        $attachment_date = isset($POST['date_attachment']) ? $POST['date_attachment'] : null;
        $type_attachment = isset($POST['type_attachment']) ? $POST['type_attachment'] : null;

            $note = 'Catatan Tambahan : '. $details;
            $stmt = $conn->prepare("SELECT department FROM ls_user_roles WHERE id = :roleId");
            $stmt->bindParam(':roleId', $roleId);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $department = $result["department"];

            $currentStatus = $flow->getCurrentFlow($systemId, $department);

            if($authorities === null){
                $chronoId = $chronology->create($username, $systemId, $currentStatus->status, 'file');
            } else {
                $chronoId = $chronology->create($username, $systemId, $currentStatus->status, 'file', $authorities);
            }

            $query = "INSERT INTO flw_appl_notes (system_id, details, created_at, authority_id, username, chronology_id) VALUES (:systemId, :details, :created,:authorities, :username, :chronology_id)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':details', $note);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':authorities', $authorities);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':chronology_id', $chronoId);
            $stmt->execute();

            if($type_attachment == "TL"){
                $attachment_type = 77;
            } else if ($type_attachment == "SRT"){
                $type_letter = isset($POST['type_letter']) ? $POST['type_letter'] : null;
                $stmt = $conn->prepare("SELECT id FROM ls_attachments WHERE code_name = :type_letter");
                $stmt->bindParam(':type_letter', $type_letter);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                $attachment_type = $result["id"];

            } else {
                $attachment_type = 85;
            }



            $query2 = "UPDATE flw_appl_attachments SET attachment_date = :attachment_date, chronology_id = :chronology_id WHERE system_id = :systemId AND attachment_type = :attachment_type AND id = (SELECT id FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachment_type ORDER BY id DESC LIMIT 1)";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':attachment_date', $attachment_date);
            $stmt2->bindParam(':chronology_id', $chronoId);
            $stmt2->bindParam(':attachment_type', $attachment_type);
            $stmt2->execute();

            //Record Activity
            $text = 'Pengguna ' . $username . ' telah Menambah Catatan bagi projek ' . $systemId;
            $pages = 'modal_catatan';
            $changelog->userActivity($text, $pages);

            $changelog->projectActivity($systemId, $currentStatus->status, $note);


            $message = 'Catatan Berlampiran Berjaya Ditambah!🎉';

            http_response_code(200);
            echo json_encode(
                array(
                    "type" => 'berlampiran',
                    "message" => $message,
                    "status" => 200,
                )
            );



    } else if($_GET['type'] == "upload"){

        // var_dump($_POST);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['folder'] == 'TL-OF') {
            $type       = 77;
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['folder'] == 'SJLT') {
            $type       = 24;
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['folder'] == 'SPKU') {
            $type       = 36;
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['folder'] == 'SLTK') {
            $type       = 37;
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['folder'] == 'LL-OF') {
            $type       = 85;
        }


        // get the uploaded file
        $systemId       = $_POST['systemId'];
        $file           = $_FILES['file'];
        $fileName       = $file['name'];
        $folder         = $_POST['folder'];
        $authorityId    = isset($_POST['authorityId']) ? $_POST['authorityId'] : null;
        $extension      = pathinfo($fileName)['extension'];
        $newFileName    = Utilities::extractSystemId($systemId, 'digits') . '-' . $folder;

        $folderPath   = '/Projects/' . date('Y') . '/' . $systemId . '/Documents/Submission/';
        $newFileName  = Utilities::extractSystemId($systemId, 'digits'). '-CT-' . $folder;


        // Enable passive mode
        ftp_pasv($ftpConn, true);

        // Change directory to projects
        if (!ftp_chdir($ftpConn, "Projects")) {
            if (ftp_mkdir($ftpConn, "Projects")) {
                ftp_chdir($ftpConn, "Projects");
                // Set permissions for the newly created directory
                ftp_chmod($ftpConn, 0755, "Projects");
            } else {
                die("Failed to create projects directory on FTP server");
            }
        }

        // Check if the year directory exists, create if not
        $yearDirectory = date('Y');
        if (!ftp_chdir($ftpConn, $yearDirectory)) {
            if (ftp_mkdir($ftpConn, $yearDirectory)) {
                ftp_chdir($ftpConn, $yearDirectory);
                // Set permissions for the newly created directory
                ftp_chmod($ftpConn, 0755, $yearDirectory);
            } else {
                die("Failed to create year directory on FTP server");
            }
        }

        // Check if the system ID directory exists, create if not
        if (!ftp_chdir($ftpConn, $systemId)) {
            if (ftp_mkdir($ftpConn, $systemId)) {
                ftp_chdir($ftpConn, $systemId);
                // Set permissions for the newly created directory
                ftp_chmod($ftpConn, 0755, $systemId);
            } else {
                die("Failed to create system ID directory on FTP server");
            }
        }

        $remoteFilePath = $folderPath . $newFileName . '.' . $extension;

        if (ftp_put($ftpConn, $remoteFilePath, $file['tmp_name'], FTP_BINARY)) {

            // Set permissions for the uploaded file
            ftp_chmod($ftpConn, 0755, $remoteFilePath);
            $encodedUrl = base64_encode('https://' . $ftpHost . $remoteFilePath);

        } else {
            die(var_dump(error_get_last()));
        }

        // Retrieve the MIME type of the uploaded file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // Retrieve the file size in bytes
        $fileSize = filesize($file['tmp_name']);

        // Close the FTP connection
        ftp_close($ftpConn);

        // Connect to the database using PDO
        $db = new DBConnectionFactory();
        $conn = $db->createConnection();

        $attachResult = "SELECT * FROM ls_attachments WHERE code_name = :folder";
        $stmt = $conn->prepare($attachResult);

        // bind parameter
        $stmt->bindParam(':folder', $folder);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $attachId = $row['id'];
        $attachDetails = $row['details'];
        $created = date('Y-m-d H:i:s', time());
        $url = $encodedUrl;
        $attachmentName = isset($_POST['other-letter-name']) ? $_POST['other-letter-name'] : $fileName;

        $query = "INSERT INTO flw_appl_attachments (system_id, name, url, user_added, created_date, attachment_type, mime_type, size, authority) VALUES
        (:systemId, :attachDetails, :encodeUrl, :user, :created, :attachId, :mimeType, :fileSize, :authorityId)";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':systemId', $systemId); // Assuming $systemId is defined somewhere

           $stmt->bindParam(':attachDetails', $attachmentName);


        $stmt->bindParam(':encodeUrl', $url);
        $stmt->bindParam(':user', $username);
        $stmt->bindParam(':created', $created);
        $stmt->bindParam(':attachId', $attachId);
        $stmt->bindParam(':mimeType', $mimeType);
        $stmt->bindParam(':fileSize', $fileSize);
        $stmt->bindParam(':authorityId', $authorityId);

        $result = $stmt->execute();

        $message = 'Lampiran Catatan Ditambah! 🎉';

        http_response_code(200);
        echo json_encode([
            "systemID" => $systemId,
            "message" => $message,
            "attachDetails" => $attachmentName,
            "url" => $url,
            "mimeType" => $mimeType,
            "fileSize" => $fileSize,
            "status" => 200,
        ]);
    } else if ($_GET['type'] == "getListLetter"){

    // Prepare an SQL statement for querying data
    $stmt = $conn->prepare("SELECT * FROM ls_attachments WHERE status_id IS NULL AND flow_phase <> 'general'");
    // $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();

    // Fetch the results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results in the desired JSON format
    header('HTTP/2 200 OK');
    echo json_encode($results);
    }

} else if (isset($_GET['sysId'])) {
    // Get the systemId from the GET parameter
    $systemId = $_GET['sysId'];

    // Prepare an SQL statement for querying data
    $stmt = $conn->prepare("
        SELECT
            ctrl_authorities.id,
            ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ',') AS array_to_string
        FROM sys_upi
        WHERE sys_upi.state_code = ls_authorities.state_code AND (sys_upi.district_code = ls_authorities.district_code)) AS district,
            ls_authorities.logo,
            ls_authorities.sort_name
        FROM ctrl_authorities
        LEFT JOIN ls_authorities ON ls_authorities.id = ctrl_authorities.authority_id
        WHERE ctrl_authorities.system_id = :systemId
    ");
    $stmt->bindParam(':systemId', $systemId);
    $stmt->execute();

    // Fetch the results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // // Modify the data to create the "authorityLogo" column
    // foreach ($results as &$result) {
    //     // Pad the "logo" column with leading zeros
    //     $result['image'] = 22;
    //     unset($result['district']); // Remove the original "district" and "logo" columns
    //     unset($result['logo']);
    // }

    // Return the results in the desired JSON format
    header('HTTP/2 200 OK');
    echo json_encode($results);
}


$conn = null;
