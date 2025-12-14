<?php
// Set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Import connection and API setup
require_once "config/system.php";
require_once "config/DBFactory.php";
require_once "api/functions.php";

// Declare all related classes
$db = new DBConnectionFactory();
$telegram = new Telegram();
$taskAssignment = new TaskAssignments();

// Connect to the database using PDO
$conn = $db->createConnection();

// Get current timestamp
$currentTimestamp = time();

// Function to check permit expiry status
function checkPermitExpiry($conn, $systemId, $authorityId, $currentTimestamp)
{
    // Start expired checking logic
    $expireStatus = false;

    $query = "SELECT work_permit_id FROM public.ctrl_authorities WHERE system_id = :sysId AND authority_id = :authorityId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':sysId', $systemId);
    $stmt->bindParam(':authorityId', $authorityId);
    if ($stmt->execute()) {
        $result = $stmt->fetchColumn();

        $permitIds = [];
        if ($result != null) {
            $trimmedValue = trim($result, '{}');
            $arrayValues = explode(',', $trimmedValue);
            $permitIds = array_map('intval', $arrayValues);

            foreach ($permitIds as $permitId) {
                $query = "SELECT extend_id FROM public.flw_work_permit WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $permitId);
                if ($stmt->execute()) {
                    $result = $stmt->fetchColumn();

                    $extendIds = [];
                    if ($result != null) {
                        $trimmedValue = trim($result, '{}');
                        $arrayValues = explode(',', $trimmedValue);
                        $extendIds = array_map('intval', $arrayValues);

                        $query = "SELECT dt_wday_end, dt_wend_end FROM public.flw_work_permit_extend WHERE id IN (" . implode(',', $extendIds) . ") ORDER BY dt_wday_end DESC LIMIT 1";
                        $stmt = $conn->prepare($query);
                        if ($stmt->execute()) {
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $expireDate = $result;

                            foreach ($expireDate as $expireTime) {
                                $expireTimestamp = strtotime($expireTime);
                                if ($expireTimestamp <= $currentTimestamp) {
                                    $expireStatus = true;
                                }
                            }
                        }
                    } else {
                        $query = "SELECT dt_wday_end, dt_wend_end FROM public.flw_work_permit WHERE id = :id";
                        $stmt = $conn->prepare($query);
                        $stmt->bindParam(':id', $permitId);
                        if ($stmt->execute()) {
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $expireDate = $result;

                            foreach ($expireDate as $expireTime) {
                                $expireTimestamp = strtotime($expireTime);
                                if ($expireTimestamp <= $currentTimestamp) {
                                    $expireStatus = true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $expireStatus;
}

// Function to check if DLP has expired
function isDLPExpired($conn, $systemId, $authorityId, $currentTimestamp)
{
    $query = "SELECT finish.dt_dlp_end FROM public.ctrl_authorities auth LEFT JOIN public.flw_work_finish finish ON finish.id = auth.work_finish_id WHERE auth.system_id = :sysId AND auth.authority_id = :authorityId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':sysId', $systemId);
    $stmt->bindParam(':authorityId', $authorityId);

    if ($stmt->execute()) {
        $expireDate = $stmt->fetchColumn();
        $expireTimestamp = strtotime($expireDate);
        return ($expireTimestamp <= $currentTimestamp);
    }
    return false;
}

// Function to trigger task assignment and notifications
function triggerTaskAssignmentAndNotification($taskAssignment, $telegram, $conn, $systemId, $authorityId, $currentStatus = null)
{
    // Determine $flwStatus based on $currentStatus or set it to a default value
    $flwStatus = ($currentStatus !== null) ? $currentStatus : 90;

    // get general info used in telegram message
    $refNo = Utilities::getRefNo($systemId);
    $authName = Utilities::getAuthorityName($authorityId);

    $authorityAssign = [0, $authorityId];

    foreach ($authorityAssign as $authId) {
        $query = "SELECT * FROM public.flw_task_assignments WHERE system_id = :sysId AND authority = :authorityId AND status_id = :flwStatus AND completed = false ORDER BY id DESC ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':sysId', $systemId);
        $stmt->bindParam(':authorityId', $authId);
        $stmt->bindParam(':flwStatus', $flwStatus);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            if ($currentStatus == 144 && $authId == 0) {
                $taskAssignment->create($systemId, $flwStatus, $authId);
            } else if ($currentStatus == 144 && $authId != 0) {
                $telegramMsg = "<strong>Perhatian !!!</strong>\nTempoh Tanggungan Kecacatan bagi permohonan ini telah <strong>Tamat</strong>. \n\n<strong>🔗 No Rujukan : " . $refNo . "\n 📋 Pihak Berkuasa : " . $authName . "</strong>";

                $telegramResponse = $telegram->sendMessage('department', 'operation', $telegramMsg, 'html');
            } else if ($currentStatus == 139) {
                $taskAssignment->create($systemId, $flwStatus, $authId);

                if ($authId != 0) {
                    $telegramMsg = "<strong>Perhatian !!!</strong>\nTempoh Tanggungan Kecacatan bagi permohonan ini telah <strong>Tamat</strong>. \n\n<strong>🔗 No Rujukan : " . $refNo . "\n 📋 Pihak Berkuasa : " . $authName . "</strong>";

                    $telegramResponse = $telegram->sendMessage('department', 'operation', $telegramMsg, 'html');
                }
            } else if ($flwStatus == 90) {
                $taskAssignment->create($systemId, $flwStatus, $authId);

                if ($authId != 0) {
                    $telegramMsg = "<strong>Perhatian !!!</strong>\nPermit Kerja bagi permohonan ini telah <strong>Tamat Tempoh</strong>. \n\n<strong>🔗 No Rujukan : " . $refNo . "\n 📋 Pihak Berkuasa : " . $authName . "</strong>";
        
                    // Send telegram notification for team operation
                    $telegramResponse = $telegram->sendMessage('department', 'operation', $telegramMsg, 'html');
                }
            }
        }
    }
}

// Check if the X-Server-Request header is present
if (isset($_SERVER['HTTP_X_SERVER_REQUEST']) && $_SERVER['HTTP_X_SERVER_REQUEST'] === 'S8R2D7A1F6G4L5V3W9U6XpMjTbNzKqYv') {
    // The request is from your Interedge system

    // Get all applications in permit state
    $permitList = [];
    $query = "SELECT id, system_id, authority, status_id FROM ctrl_statuses WHERE status_id IN (74, 77, 78, 79, 80, 81, 83, 85, 89, 90) AND authority <> 0 ORDER BY system_id";
    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        $permitList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($permitList as $permit) {
            $systemId = $permit['system_id'];
            $authorityId = $permit['authority'];
            $currentStatus = $permit['status_id'];

            // Check permit expiry status
            $isExpired = checkPermitExpiry($conn, $systemId, $authorityId, $currentTimestamp);

            if ($isExpired) {
                // Permit is expired, trigger task assignment and notifications
                triggerTaskAssignmentAndNotification($taskAssignment, $telegram, $conn, $systemId, $authorityId);
            }
        }
    }

    // Retrieve all applications in DLP period
    $dlpList = [];
    $query = "SELECT id, system_id, authority, status_id FROM ctrl_statuses WHERE status_id IN (144, 139) AND authority <> 0 ORDER BY system_id";
    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        $dlpList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dlpList as $dlp) {
            $systemId = $dlp['system_id'];
            $authorityId = $dlp['authority'];
            $currentStatus = $dlp['status_id'];

            if (isDLPExpired($conn, $systemId, $authorityId, $currentTimestamp)) {
                triggerTaskAssignmentAndNotification($taskAssignment, $telegram, $conn, $systemId, $authorityId, $currentStatus);
            }
        }
    }
} else {
    // The request is not from your Interedge system
    http_response_code(400);
    $result = array(
        "success" => false,
        "message" => "Unauthorized request.",
    );

    echo json_encode($result);
    $conn = null;
}