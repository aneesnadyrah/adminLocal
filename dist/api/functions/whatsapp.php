<?php

class Whatsapp
{
    private $dbFactory;
    private $system;
    private $token;
    private $Traffic;
    private $Curl;
    private $utilities;
    public function __construct()
    {

        $this->dbFactory = new DBConnectionFactory();
        $this->Traffic = new Traffic();
        $this->Curl = new Curl();
        $this->utilities = new Utilities();
        $this->system = new System;
        $this->token = $this->system->App->botToken;
    }

    private function getAuthorityContact($authority, $incharge_phase)
    {
        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare('SELECT phone_no from ls_authority_officers WHERE authority = :authority AND :incharge_phase = ANY(incharge_phase)');
        $stmt->bindParam(':authority', $authority);
        $stmt->bindParam(':incharge_phase', $incharge_phase);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {

            // Extract phone numbers into an array
            $phoneNo = array_column($result, 'phone_no');

            // // Enclose each phone number in double quotes
            // $quotedPhoneNumbers = array_map(function ($phoneNo) {
            //     return '"' . $phoneNo . '"';
            // }, $phoneNumbers);

            // Convert the array of quoted phone numbers into a comma-separated string
            // $phoneNo = implode(', ', $phoneNumbers);

        } else {
            $phoneNo = [];
        }

        $conn = null;

        return $phoneNo;
    }

    private function getPhase($status)
    {

        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare('SELECT flow_phase from ls_statuses WHERE id = :status');
        $stmt->bindParam(':status', $status);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $phase = $result['flow_phase'];

        $conn = null;

        return $phase;
    }

    //general send whatsapp message
    public function sendMessage($systemId, $whatsappMsg, $contact, $attachment = "")
    {

        $applData = array(
            "secret" => $this->system->App->secret,
            "notification" => array(
                "message" => $whatsappMsg,
                "phones" => [
                    $contact
                ],
                "attachments" => [
                    $attachment
                ]
            )
        );

        // Convert the array to JSON
        $jsonData = json_encode($applData);
        // make api request to extercord
        $apiRequest = $this->Curl->request($this->system->App->ec_url . '/gateway/internal/notify/' . $systemId, $jsonData);

        $trafficReturn = $this->Traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

        // Decode the response JSON
        $curlResult = json_decode($apiRequest['response']);

        $conn = null;

        return $curlResult;

    }

    //authority send whatsapp message
    public function sendMessageAuthority($systemId, $authorityId, $currentStatus, $attachment = "")
    {

        $phase = $this->getPhase($currentStatus);

        $contact = $this->getAuthorityContact($authorityId, $phase);

        $whatsappMsg = $this->getMessageByFlow($currentStatus, $systemId, $authorityId);

        $applData = array(
            "secret" => $this->system->App->secret,
            "notification" => array(
                "message" => $whatsappMsg,
                "phones" => $contact,
                "attachments" => [
                    $attachment
                ]
            )
        );

        // Convert the array to JSON
        $jsonData = json_encode($applData);
        // make api request to extercord
        $apiRequest = $this->Curl->request($this->system->App->ec_url . '/gateway/internal/notify/' . $systemId, $jsonData);

        $trafficReturn = $this->Traffic->requestAPI($apiRequest['traffic'], $apiRequest['url'], $apiRequest['request_method'], $apiRequest['headers'], $apiRequest['body'], $apiRequest['response'], $apiRequest['status']);

        // Decode the response JSON
        $curlResult = json_decode($apiRequest['response']);

        $conn = null;

        return $curlResult;

    }

    private function getAttachment($systemId, $attachmentId)
    {

        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = :attachmentId ORDER BY id DESC LIMIT 1");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':attachmentId', $attachmentId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $encodedUrl = base64_decode($result['url']);

            $linkFile = str_replace(' ', '%20', $encodedUrl);
        } else {
            $linkFile = "";
        }

        $conn = null;

        return $linkFile;
    }

    private function getCtrlAuthority($phaseIdName, $systemId, $authorityId)
    {

        $conn = $this->dbFactory->createConnection();

        $stmt = $conn->prepare("SELECT {$phaseIdName} FROM ctrl_authorities WHERE system_id = :systemId AND authority_id = :authorityId");
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authorityId', $authorityId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {

            if ($phaseIdName == "wayleave_id" || $phaseIdName == "work_permit_id") {

                $value = $result["{$phaseIdName}"];

                $toArray = str_replace(['{', '}', ''], '', $value);
                $Ids = explode(',', $toArray);

                $Id = array_pop($Ids);
            } else {

                $Id = $result["{$phaseIdName}"];

            }

        } else {
            $Id = "";
        }

        $conn = null;

        return $Id;
    }

    public function getMessageByFlow($status, $systemId, $authorityId)
    {

        $conn = $this->dbFactory->createConnection();
        $refNo = $this->utilities->getRefNo($systemId);

        //get provider data
        $stmt2 = $conn->prepare("SELECT utility_provider FROM flw_appl_entries WHERE system_id = :system_id");
        $stmt2->bindParam(':system_id', $systemId);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        $providerId = $result2['utility_provider'];

        $provider = $this->utilities->getProvider($providerId);
        $providerName = $provider->name;

        switch ($status) {
            // penetapan tairkh lawatan tapak
            case 11:
                //get LTA date and location
                $stmt = $conn->prepare("SELECT start, location FROM view_calendar WHERE system_id = :system_id AND authority_id = :authority_id");
                $stmt->bindParam(':system_id', $systemId);
                $stmt->bindParam(':authority_id', $authorityId);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                $dateTimeString = $result['start'];
                $location = $result['location'];

                $dateTime = new DateTime($dateTimeString);
                $date = $dateTime->format('d/m/Y');
                $time = $dateTime->format('g:i a');

                //get attachment Cadangan Teknikal and Gambar Lokasi from applicant
                $cadanganTeknikal = $this->getAttachment($systemId, 3);
                $gambarLokasi = $this->getAttachment($systemId, 4);

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Lawatan Tapak Awalan bagi Permohonan *" . $refNo . "* telah ditetapkan seperti ketetapan berikut :-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "🪧 Lokasi : *" . $location . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . "🕰 Masa : *" . $time . "*" . PHP_EOL . PHP_EOL . "Cadangan Teknikal : " . $cadanganTeknikal . PHP_EOL . PHP_EOL . "Penandaan Lokasi : " . $gambarLokasi;
                break;

            //report LTA
            case 15:
                //get attachment LTA report
                $svReport = $this->getAttachment($systemId, 7);

                $svReportAuth = $svReport . "&a=" . $authorityId;

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Laporan Lawatan Tapak telah dikeluarkan, Tuan/Puan boleh melihat laporan tersebut di pautan ini:- " . PHP_EOL . PHP_EOL . "Laporan Lawatan Tapak : " . $svReportAuth;

                break;

            //penyediaan surat PKIL
            case 31:

                //get attachment SPKIL
                $SPKIL = $this->getAttachment($systemId, 25);

                //date
                $wayleaveId = $this->getCtrlAuthority("wayleave_id", $systemId, $authorityId);

                if ($wayleaveId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_auth_ltr_created FROM flw_wayleave WHERE id = :wayleaveId");
                    $stmt3->bindParam(':wayleaveId', $wayleaveId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_auth_ltr_created'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Kelulusan Izin Lalu bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh Surat : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Kelulusan Izin Lalu : " . $SPKIL;
                break;
            //penyediaan surat PKPK
            case 70:

                //get attachment SPKPK
                $SPKPK = $this->getAttachment($systemId, 38);

                //date
                $workpermitId = $this->getCtrlAuthority("work_permit_id", $systemId, $authorityId);

                if ($workpermitId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_auth_ltr_created FROM flw_work_permit WHERE id = :workpermitId");
                    $stmt3->bindParam(':workpermitId', $workpermitId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_auth_ltr_created'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Kelulusan Permit Kerja bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Kelulusan Permit Kerja : " . $SPKPK;
                break;

            //penyedian surat makluman NMK
            case 80:

                //get attachment NMK
                $NMK = $this->getAttachment($systemId, 47);

                //date
                $workNoticeId = $this->getCtrlAuthority("work_notice_id", $systemId, $authorityId);

                if ($workNoticeId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_ws FROM flw_work_notice WHERE id = :workNoticeId");
                    $stmt3->bindParam(':workNoticeId', $workNoticeId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_ws'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Makluman Notis Mula Kerja bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh Mula Kerja : *" . $date . "*" . PHP_EOL . PHP_EOL . "Makluman Notis Mula Kerja : " . $NMK;
                break;

            //NSK
            case 88:

                //get attachment NSK
                $NSK = $this->getAttachment($systemId, 50);

                //date
                $workNoticeId = $this->getCtrlAuthority("work_notice_id", $systemId, $authorityId);

                if ($workNoticeId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_wf FROM flw_work_notice WHERE id = :workNoticeId");
                    $stmt3->bindParam(':workNoticeId', $workNoticeId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_wf'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Makluman Notis Siap Kerja bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh Siap Kerja : *" . $date . "*" . PHP_EOL . PHP_EOL . "Makluman Notis Siap Kerja : " . $NSK;
                break;

            //penyediaan surat permohonan LPK
            case 92:

                //get attachment SPLPK
                $SPLPK = $this->getAttachment($systemId, 44);

                //date
                $workpermitId = $this->getCtrlAuthority("work_permit_id", $systemId, $authorityId);

                if ($workpermitId != "") {

                    $stmt4 = $conn->prepare("SELECT extend_id FROM flw_work_permit WHERE id = :workpermitId");
                    $stmt4->bindParam(':workpermitId', $workpermitId);
                    $stmt4->execute();
                    $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);

                    if ($result4) {

                        $value = $result4["extend_id"];
                        $toArray = str_replace(['{', '}', ''], '', $value);
                        $workPermitExtendIds = explode(',', $toArray);

                        $workPermitExtendId = array_pop($workPermitExtendIds);
                    } else {
                        $workPermitExtendId = "";
                    }

                    if ($workPermitExtendId != "") {

                        $stmt3 = $conn->prepare("SELECT dt_auth_ltr_created FROM flw_work_permit_extend WHERE id = :workPermitExtendId");
                        $stmt3->bindParam(':workPermitExtendId', $workPermitExtendId);
                        $stmt3->execute();
                        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                        $dateString = $result3['dt_auth_ltr_created'];

                        $dateFormat = new DateTime($dateString);

                        $date = $dateFormat->format('d/m/Y');

                    } else {
                        $date = "";
                    }

                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Lanjutan Permit Kerja bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Lanjutan Permit Kerja : " . $SPLPK;
                break;

            //penyediaan surat permohonan CPC
            case 120:

                //get attachment SPSSK
                $SPSSK = $this->getAttachment($systemId, 56);

                //date
                $workFinishId = $this->getCtrlAuthority("work_finish_id", $systemId, $authorityId);

                if ($workFinishId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_auth_ltr_created FROM flw_work_finish WHERE id = :workFinishId");
                    $stmt3->bindParam(':workFinishId', $workFinishId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_auth_ltr_created'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Sijil Siap Kerja bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Sijil Siap Kerja : " . $SPSSK;
                break;

            //penyediaan surat permohonan CMGD
            case 136:

                //get attachment SPSSMK
                $SPSSMK = $this->getAttachment($systemId, 62);

                //date
                $workDefectId = $this->getCtrlAuthority("work_defects_id", $systemId, $authorityId);

                if ($workDefectId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_cmgd_auth_ltr_created FROM flw_work_defects WHERE id = :workDefectId");
                    $stmt3->bindParam(':workDefectId', $workDefectId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_cmgd_auth_ltr_created'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Sijil Siap Memperbaiki Kecacatan bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Sijil Siap Memperbaiki Kecacatan : " . $SPSSMK;
                break;

            //penyediaan surat permohonan CCC
            case 139:

                //get attachment SPCCC
                $SPCCC = $this->getAttachment($systemId, 65);

                //date
                $workDefectId = $this->getCtrlAuthority("work_defects_id", $systemId, $authorityId);

                if ($workDefectId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_ccc_auth_ltr_created FROM flw_work_defects WHERE id = :workDefectId");
                    $stmt3->bindParam(':workDefectId', $workDefectId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_ccc_auth_ltr_created'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Sijil Sempurna Kerja bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Sijil Sempurna Kerja : " . $SPCCC;
                break;

            //penyediaan surat permohonan PWC
            case 146:

                //get attachment SPPWC
                $SPPWC = $this->getAttachment($systemId, 74);

                //date
                $depositReturnId = $this->getCtrlAuthority("deposit_returns_id", $systemId, $authorityId);

                if ($depositReturnId != "") {
                    $stmt3 = $conn->prepare("SELECT dt_auth_ltr_created FROM flw_deposit_returns WHERE id = :depositReturnId");
                    $stmt3->bindParam(':depositReturnId', $depositReturnId);
                    $stmt3->execute();
                    $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);

                    $dateString = $result3['dt_auth_ltr_created'];

                    $dateFormat = new DateTime($dateString);

                    $date = $dateFormat->format('d/m/Y');
                } else {
                    $date = "";
                }

                $message = "Salam Sejahtera Tuan/Puan," . PHP_EOL . PHP_EOL . "Surat Permohonan Pemulangan Wang Cagaran bagi Permohonan *" . $refNo . "* telah disediakan seperti lampiran berikut:-" . PHP_EOL . PHP_EOL . "🏢 Penyedia Utiliti : *" . $providerName . "*" . PHP_EOL . "📅 Tarikh : *" . $date . "*" . PHP_EOL . PHP_EOL . "Permohonan Pemulangan Wang Cagaran : " . $SPPWC;
                break;
            default:
                $message = "";
                break;
        }

        $conn = null;

        return $message;

    }
}





