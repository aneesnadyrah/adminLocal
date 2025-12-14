<?php
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

require "api/header.php";
require "config/system.php";
include_once "config/functions.php";
include "config/tenant.php";
include_once "api/functions.php";

$json_data = file_get_contents("php://input");
// Check if JSON data was retrieved successfully
if ($json_data === false) {
    echo 'Error retrieving JSON data';
    exit;
}

$data = json_decode($json_data, true);
// var_dump($data);exit;
// Check if JSON data was parsed successfully
if ($data === null) {
    echo 'Error parsing JSON data';
    exit;
}

// Connect to the database using PDO
$conn = Utilities::DBFactory();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($data["item"] == "generate-wyFeedback") {
        $timestamp = date('Y-m-d H:i:s', time());

        if ($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT') {
            //start insert into table flw_wy_letters
            $stmt = $conn->prepare("INSERT INTO flw_wy_letters (system_id, small_title, letter_date, letter_ref_no, client_ref_no, staff_name, staff_contact, staff_name_2, staff_contact_2, staff_approval, staff_approval_position, entry_list_road, status, created_at) VALUES(:systemId, :smallTitle, :letterDate, :letterRefNo, :clientRefNo, :staffName, :staffContact, :staffName2, :staffContact2, :staffApproval, :staffApprovalPosition, :roadId, 1, :created) RETURNING id");

            // values
            $systemId = $data['system_id'];
            $smallTitle = $data['small_title'];
            $letterDate = $data['letter_date'];
            $letterRefNo = $data['letter_ref_no'];
            $clientRefNo = $data['client_ref_no'];
            $staffName = $data['staff_name'];
            $staffContact = $data['staff_contact'];
            $staffName2 = $data['staff_name_2'];
            $staffContact2 = $data['staff_contact_2'];
            $staffApproval = $data['approval_by'];
            $staffApprovalPosition = $data['approval_position'];
            $roadId = $data['entry_list_road_id'];

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':smallTitle', $smallTitle);
            $stmt->bindParam(':letterDate', $letterDate);
            $stmt->bindParam(':letterRefNo', $letterRefNo);
            $stmt->bindParam(':clientRefNo', $clientRefNo);
            $stmt->bindParam(':staffName', $staffName);
            $stmt->bindParam(':staffContact', $staffContact);
            $stmt->bindParam(':staffName2', $staffName2);
            $stmt->bindParam(':staffContact2', $staffContact2);
            $stmt->bindParam(':staffApproval', $staffApproval);
            $stmt->bindParam(':staffApprovalPosition', $staffApprovalPosition);
            $stmt->bindParam(':roadId', $roadId);
            $stmt->bindParam(':created', $timestamp);
            $stmt->execute();

            // Get the ID of the inserted row
            $ltrwyid = $stmt->fetchColumn();

            //for kudr/ucidos
            $query = "UPDATE flw_wayleave SET status = 4, dt_fb_ltr_created = :created, letter_wy_id = :ltrwyid WHERE system_id = :sid ";
            //for kutt
            // $query = "UPDATE flw_wayleave SET status = 4, dt_fb_ltr_created = :created, letter_wy_id = :ltrwyid WHERE system_id = :sid AND authority = :authorityid ";
            $stmtUpdate = $conn->prepare($query);

            // Bind the parameters
            $stmtUpdate->bindParam(':sid', $systemId);
            // $stmtUpdate->bindParam(':authorityid', $authorityId);
            $stmtUpdate->bindParam(':created', $timestamp);
            $stmtUpdate->bindParam(':ltrwyid', $ltrwyid);
            $stmtUpdate->execute();
            //end insert into table flw_wy_letters


            // Create an array to store contact IDs
            $contactIds = [];

            //start Insert data into table flw_appl_contacts
            $stmt2 = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, type, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, 5, :created) RETURNING id");

            // values
            $name = $data['up_provider'];
            $companyName = $data['provider_name'];
            $address1 = $data['addr_provider_1'];
            $address2 = $data['addr_provider_2'];
            $postcode = $data['postcode'];
            $city = $data['city'];
            $state = $data['state'];

            // Bind the parameters
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':name', $name);
            $stmt2->bindParam(':companyName', $companyName);
            $stmt2->bindParam(':unit', $address1);
            $stmt2->bindParam(':street', $address2);
            $stmt2->bindParam(':postcode', $postcode);
            $stmt2->bindParam(':city', $city);
            $stmt2->bindParam(':state', $state);
            $stmt2->bindParam(':created', $timestamp);
            $stmt2->execute();

            // Retrieve the ID of the last inserted provider contact
            $providerContactId = $stmt2->fetchColumn();

            $stmt3 = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, type, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, 2, :created) RETURNING id");

            // values
            $systemId = $data['system_id'];
            $name = $data['up_pemohon'];
            $companyName = $data['company_name'];
            $address1 = $data['addr_client_1'];
            $address2 = $data['addr_client_2'];
            $postcode = $data['postcode_client'];
            $city = $data['city_client'];
            $state = $data['state_client'];

            // Bind the parameters
            $stmt3->bindParam(':systemId', $systemId);
            $stmt3->bindParam(':name', $name);
            $stmt3->bindParam(':companyName', $companyName);
            $stmt3->bindParam(':unit', $address1);
            $stmt3->bindParam(':street', $address2);
            $stmt3->bindParam(':postcode', $postcode);
            $stmt3->bindParam(':city', $city);
            $stmt3->bindParam(':state', $state);
            $stmt3->bindParam(':created', $timestamp);
            $stmt3->execute();

            // Retrieve the ID of the last inserted client contact
            $clientContactId = $stmt3->fetchColumn();

            // Add contact IDs to the array
            $contactIds[] = $providerContactId;
            $contactIds[] = $clientContactId;

            // Convert the array to a comma-separated string
            $contactId = '{' . implode(",", $contactIds) . '}';

            $updtcontact = $conn->prepare("UPDATE flw_wy_letters SET provider_client_id = :contactId WHERE id = :id ");
            $updtcontact->bindParam(':contactId', $contactId);
            $updtcontact->bindParam(':id', $ltrwyid);
            $updtcontact->execute();
            //end Insert data into table flw_appl_contacts


            // Get the list of data to insert
            $kwcList = $data['kwc-list'];

            //start Insert data into table flw_kwc_wy_letters
            $stmtKwc = $conn->prepare("INSERT INTO flw_kwc_wy_letters (system_id, authority_id, project_date, payment_detail, amount, notes, created_at) VALUES(:systemId, :authorityid, :projectDate, :paymentdetail, :amount, :notes, :created)");

            foreach ($kwcList as $kwc) {
                $authorityId = $kwc['authority_id'];
                $projectDate = $kwc['period_kil'];
                $paymentDetail = $kwc['payment_detail'];
                $amount = $kwc['amount'];
                $notes = $kwc['notes'];

                $dateRangeParts = explode(" - ", $projectDate);
                if (count($dateRangeParts) === 2) {
                    $startDate = date("Y-m-d", strtotime($dateRangeParts[0]));
                    $endDate = date("Y-m-d", strtotime($dateRangeParts[1]));

                    // Format the date range as "[start_date, end_date)"
                    $formattedDateRange = "[" . $startDate . "," . $endDate . ")";
                } else {
                    $formattedDateRange = '';
                }

                // Bind the parameters
                $stmtKwc->bindParam(':systemId', $systemId);
                $stmtKwc->bindParam(':authorityid', $authorityId);
                $stmtKwc->bindParam(':projectDate', $formattedDateRange);
                $stmtKwc->bindParam(':paymentdetail', $paymentDetail);
                $stmtKwc->bindParam(':amount', $amount);
                $stmtKwc->bindParam(':notes', $notes);
                $stmtKwc->bindParam(':created', $timestamp);

                // Execute the SQL query
                $stmtKwc->execute();
            }

            // Prepare a SELECT query to select list road based on system_id
            $idKwcResult = $conn->prepare("SELECT id FROM flw_kwc_wy_letters WHERE system_id = :systemId ");

            // bind the parameter to the placeholder using the bindValue method
            $idKwcResult->bindValue(':systemId', $systemId);

            // Execute the query
            $idKwcResult->execute();

            while ($row = $idKwcResult->fetch()) {
                $idKwcData[] = $row['id'];
            }

            $idKwcResult->closeCursor();

            if (is_array($idKwcData)) {
                $int_array = [];
                foreach ($idKwcData as $value) {
                    $int_array[] = (int) $value;
                }
                $kwcId = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $kwcId = '{' . intval($idKwcData) . '}';
            }

            $updtkwc = $conn->prepare("UPDATE flw_wy_letters SET kwc_id = :kwcId WHERE id = :id ");
            $updtkwc->bindParam(':kwcId', $kwcId);
            $updtkwc->bindParam(':id', $ltrwyid);
            $updtkwc->execute();
            //end Insert data into table flw_kwc_wy_letters


            // Get the list of data to insert
            $salinanList = $data['salinan-list'];

            // Create an array to store copy IDs
            $copyIds = [];

            //start Insert data into table flw_appl_contacts
            $stmtCopy = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, type, added_at) VALUES (:systemId, :nameR, :companyNameR, :addr1R, :addr2R, :postcodeR, :cityR, :stateR, 5, :created) RETURNING id");

            foreach ($salinanList as $copy) {
                $companyNameR = $copy['company_name_r'];
                $nameR = $copy['name_r'];
                $addr1R = $copy['addr_1_r'];
                $addr2R = $copy['addr_2_r'];
                $postcodeR = $copy['postcode_r'];
                $cityR = $copy['city_r'];
                $stateR = $copy['state_r'];

                // Bind the parameters
                $stmtCopy->bindParam(':systemId', $systemId);
                $stmtCopy->bindParam(':companyNameR', $companyNameR);
                $stmtCopy->bindParam(':nameR', $nameR);
                $stmtCopy->bindParam(':addr1R', $addr1R);
                $stmtCopy->bindParam(':addr2R', $addr2R);
                $stmtCopy->bindParam(':postcodeR', $postcodeR);
                $stmtCopy->bindParam(':cityR', $cityR);
                $stmtCopy->bindParam(':stateR', $stateR);
                $stmtCopy->bindParam(':created', $timestamp);

                // Execute the SQL query
                $stmtCopy->execute();

                // Retrieve the ID of the last inserted client contact
                $copyContactId = $stmtCopy->fetchColumn();

                // Add contact IDs to the array
                $copyIds[] = $copyContactId;

            }

            // Convert the array to a comma-separated string
            $copyId = '{' . implode(",", $copyIds) . '}';

            $updtcopy = $conn->prepare("UPDATE flw_wy_letters SET contact_copy_to = :copyId WHERE id = :id ");
            $updtcopy->bindParam(':copyId', $copyId);
            $updtcopy->bindParam(':id', $ltrwyid);
            $updtcopy->execute();
            //end Insert data into table flw_appl_contacts


            $message = "Surat Maklum Balas Izin Lalu Telah Berjaya Dijana 🎉";

            header('HTTP/2 200 OK');
            echo json_encode([
                "message" => $message,
                "status" => 200,
                "sysid" => $systemId,
                "letterRefNo" => $letterRefNo,
                "id" => $ltrwyid
            ]);

            // Close the database connection
            $conn = null;

        } else if ($appsTitle == 'KITER' || $appsTitle == 'KUK') {
            $authorityId = $data['authority_id'];

            //start insert into table flw_wy_letters
            $stmt = $conn->prepare("INSERT INTO flw_wy_letters (system_id, small_title, inv_no, inv_date, letter_hijri_date, letter_date, letter_ref_no, client_ref_no, staff_name, staff_contact, staff_approval, staff_approval_position, entry_list_road, status, created_at) VALUES(:systemId, :smallTitle, :invNo, :invDate, :letterHijriDate, :letterDate, :letterRefNo, :clientRefNo, :staffName, :staffContact, :staffApproval, :staffApprovalPosition, :roadId, 1, :created) RETURNING id");

            // values
            $systemId = $data['system_id'];
            $smallTitle = $data['small_title'];
            $letterDate = $data['letter_date'];
            $letterRefNo = $data['letter_ref_no'];
            $clientRefNo = $data['client_ref_no'];
            $staffName = $data['staff_name'];
            $staffContact = $data['staff_contact'];
            $staffApproval = $data['approval_by'];
            $staffApprovalPosition = $data['approval_position'];
            $roadId = $data['entry_list_road_id'];

            $invNo = $data['inv_no'];
            $invDate = $data['inv_date'];
            $letterHijriDate = $data['date_hijri'];

            // Bind the parameters
            $stmt->bindParam(':systemId', $systemId);
            $stmt->bindParam(':smallTitle', $smallTitle);
            $stmt->bindParam(':letterDate', $letterDate);
            $stmt->bindParam(':letterRefNo', $letterRefNo);
            $stmt->bindParam(':clientRefNo', $clientRefNo);
            $stmt->bindParam(':staffName', $staffName);
            $stmt->bindParam(':staffContact', $staffContact);
            $stmt->bindParam(':staffApproval', $staffApproval);
            $stmt->bindParam(':staffApprovalPosition', $staffApprovalPosition);
            $stmt->bindParam(':roadId', $roadId);
            $stmt->bindParam(':created', $timestamp);
            $stmt->bindParam(':invNo', $invNo);
            $stmt->bindParam(':invDate', $invDate);
            $stmt->bindParam(':letterHijriDate', $letterHijriDate);
            $stmt->execute();

            // Get the ID of the inserted row
            $ltrwyid = $stmt->fetchColumn();

            //for kudr/ucidos
            // $query = "UPDATE flw_wayleave SET status = 4, dt_fb_ltr_created = :created, letter_wy_id = :ltrwyid WHERE system_id = :sid ";
            //for kutt
            $query = "UPDATE flw_wayleave SET status = 4, dt_fb_ltr_created = :created, letter_wy_id = :ltrwyid WHERE system_id = :sid AND authority = :authorityid ";
            $stmtUpdate = $conn->prepare($query);

            // Bind the parameters
            $stmtUpdate->bindParam(':sid', $systemId);
            $stmtUpdate->bindParam(':authorityid', $authorityId);
            $stmtUpdate->bindParam(':created', $timestamp);
            $stmtUpdate->bindParam(':ltrwyid', $ltrwyid);
            $stmtUpdate->execute();
            //end insert into table flw_wy_letters


            // Create an array to store contact IDs
            $contactIds = [];

            //start Insert data into table flw_appl_contacts
            $stmt2 = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, type, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, 5, :created) RETURNING id");

            // values
            $name = $data['up_provider'];
            $companyName = $data['provider_name'];
            $address1 = $data['addr_provider_1'];
            $address2 = $data['addr_provider_2'];
            $postcode = $data['postcode'];
            $city = $data['city'];
            $state = $data['state'];

            // Bind the parameters
            $stmt2->bindParam(':systemId', $systemId);
            $stmt2->bindParam(':name', $name);
            $stmt2->bindParam(':companyName', $companyName);
            $stmt2->bindParam(':unit', $address1);
            $stmt2->bindParam(':street', $address2);
            $stmt2->bindParam(':postcode', $postcode);
            $stmt2->bindParam(':city', $city);
            $stmt2->bindParam(':state', $state);
            $stmt2->bindParam(':created', $timestamp);
            $stmt2->execute();

            // Retrieve the ID of the last inserted provider contact
            $providerContactId = $stmt2->fetchColumn();

            $stmt3 = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, type, added_at) VALUES (:systemId, :name, :companyName, :unit, :street, :postcode, :city, :state, 2, :created) RETURNING id");

            // values
            $systemId = $data['system_id'];
            $name = $data['up_pemohon'];
            $companyName = $data['company_name'];
            $address1 = $data['addr_client_1'];
            $address2 = $data['addr_client_2'];
            $postcode = $data['postcode_client'];
            $city = $data['city_client'];
            $state = $data['state_client'];

            // Bind the parameters
            $stmt3->bindParam(':systemId', $systemId);
            $stmt3->bindParam(':name', $name);
            $stmt3->bindParam(':companyName', $companyName);
            $stmt3->bindParam(':unit', $address1);
            $stmt3->bindParam(':street', $address2);
            $stmt3->bindParam(':postcode', $postcode);
            $stmt3->bindParam(':city', $city);
            $stmt3->bindParam(':state', $state);
            $stmt3->bindParam(':created', $timestamp);
            $stmt3->execute();

            // Retrieve the ID of the last inserted client contact
            $clientContactId = $stmt3->fetchColumn();

            // Add contact IDs to the array
            $contactIds[] = $providerContactId;
            $contactIds[] = $clientContactId;

            // Convert the array to a comma-separated string
            $contactId = '{' . implode(",", $contactIds) . '}';

            $updtcontact = $conn->prepare("UPDATE flw_wy_letters SET provider_client_id = :contactId WHERE id = :id ");
            $updtcontact->bindParam(':contactId', $contactId);
            $updtcontact->bindParam(':id', $ltrwyid);
            $updtcontact->execute();
            //end Insert data into table flw_appl_contacts


            // Get the list of data to insert
            $kwcList = $data['kwc-list'];

            //start Insert data into table flw_kwc_wy_letters
            $stmtKwc = $conn->prepare("INSERT INTO flw_kwc_wy_letters (system_id, authority_id, project_date, payment_detail, amount, notes, created_at) VALUES(:systemId, :authorityid, :projectDate, :paymentdetail, :amount, :notes, :created)");

            foreach ($kwcList as $kwc) {
                $authorityId = $kwc['authority_id'];
                $projectDate = $kwc['period_kil'];
                $paymentDetail = $kwc['payment_detail'];
                $amount = $kwc['amount'];
                $notes = $kwc['notes'];

                $dateRangeParts = explode(" - ", $projectDate);
                if (count($dateRangeParts) === 2) {
                    $startDate = date("Y-m-d", strtotime($dateRangeParts[0]));
                    $endDate = date("Y-m-d", strtotime($dateRangeParts[1]));

                    // Format the date range as "[start_date, end_date)"
                    $formattedDateRange = "[" . $startDate . "," . $endDate . ")";
                } else {
                    $formattedDateRange = '';
                }

                // Bind the parameters
                $stmtKwc->bindParam(':systemId', $systemId);
                $stmtKwc->bindParam(':authorityid', $authorityId);
                $stmtKwc->bindParam(':projectDate', $formattedDateRange);
                $stmtKwc->bindParam(':paymentdetail', $paymentDetail);
                $stmtKwc->bindParam(':amount', $amount);
                $stmtKwc->bindParam(':notes', $notes);
                $stmtKwc->bindParam(':created', $timestamp);

                // Execute the SQL query
                $stmtKwc->execute();
            }

            // Prepare a SELECT query to select list road based on system_id
            $idKwcResult = $conn->prepare("SELECT id FROM flw_kwc_wy_letters WHERE system_id = :systemId ");

            // bind the parameter to the placeholder using the bindValue method
            $idKwcResult->bindValue(':systemId', $systemId);

            // Execute the query
            $idKwcResult->execute();

            while ($row = $idKwcResult->fetch()) {
                $idKwcData[] = $row['id'];
            }

            $idKwcResult->closeCursor();

            if (is_array($idKwcData)) {
                $int_array = [];
                foreach ($idKwcData as $value) {
                    $int_array[] = (int) $value;
                }
                $kwcId = '{' . implode(', ', $int_array) . '}';
            } else {
                // value is a single value
                $kwcId = '{' . intval($idKwcData) . '}';
            }

            $updtkwc = $conn->prepare("UPDATE flw_wy_letters SET kwc_id = :kwcId WHERE id = :id ");
            $updtkwc->bindParam(':kwcId', $kwcId);
            $updtkwc->bindParam(':id', $ltrwyid);
            $updtkwc->execute();
            //end Insert data into table flw_kwc_wy_letters


            // Get the list of data to insert
            $salinanList = $data['salinan-list'];

            // Create an array to store copy IDs
            $copyIds = [];

            //start Insert data into table flw_appl_contacts
            $stmtCopy = $conn->prepare("INSERT INTO flw_appl_contacts (system_id, full_name, company_name, address_1, address_2, postcode, city, state, type, added_at) VALUES (:systemId, :nameR, :companyNameR, :addr1R, :addr2R, :postcodeR, :cityR, :stateR, 5, :created) RETURNING id");

            foreach ($salinanList as $copy) {
                $companyNameR = $copy['company_name_r'];
                $nameR = $copy['name_r'];
                $addr1R = $copy['addr_1_r'];
                $addr2R = $copy['addr_2_r'];
                $postcodeR = $copy['postcode_r'];
                $cityR = $copy['city_r'];
                $stateR = $copy['state_r'];

                // Bind the parameters
                $stmtCopy->bindParam(':systemId', $systemId);
                $stmtCopy->bindParam(':companyNameR', $companyNameR);
                $stmtCopy->bindParam(':nameR', $nameR);
                $stmtCopy->bindParam(':addr1R', $addr1R);
                $stmtCopy->bindParam(':addr2R', $addr2R);
                $stmtCopy->bindParam(':postcodeR', $postcodeR);
                $stmtCopy->bindParam(':cityR', $cityR);
                $stmtCopy->bindParam(':stateR', $stateR);
                $stmtCopy->bindParam(':created', $timestamp);

                // Execute the SQL query
                $stmtCopy->execute();

                // Retrieve the ID of the last inserted client contact
                $copyContactId = $stmtCopy->fetchColumn();

                // Add contact IDs to the array
                $copyIds[] = $copyContactId;

            }

            // Convert the array to a comma-separated string
            $copyId = '{' . implode(",", $copyIds) . '}';

            $updtcopy = $conn->prepare("UPDATE flw_wy_letters SET contact_copy_to = :copyId WHERE id = :id ");
            $updtcopy->bindParam(':copyId', $copyId);
            $updtcopy->bindParam(':id', $ltrwyid);
            $updtcopy->execute();
            //end Insert data into table flw_appl_contacts


            $message = "Surat Maklum Balas Izin Lalu Telah Berjaya Dijana 🎉";

            header('HTTP/2 200 OK');
            echo json_encode([
                "message" => $message,
                "status" => 200,
                "sysid" => $systemId,
                "letterRefNo" => $letterRefNo,
                "id" => $ltrwyid
            ]);

            // Close the database connection
            $conn = null;
        }
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    if ($data["item"] == "send-kil") {
        // Prepare the query for update
        $query = "UPDATE flw_wayleave SET status = :wyStatus, dt_auth_ltr_send = :sendDate WHERE id = :id ";
        $stmt = $conn->prepare($query);

        $created = date('Y-m-d H:i:s', time());
        $wy_id = $data["wy-id"];
        $wy_status = 2;
        $send_date = $data['dt-send-wayleave'];
        $system_id = $data['system-id'];
        $authority_name = $data['authority-name'];
        $authority_id = $data['authority-id'];

        //TODO : change to new function
        $send_dt = convertDate($send_date);

        // Bind the parameters
        $stmt->bindParam(':wyStatus', $wy_status);
        $stmt->bindParam(':id', $wy_id);
        $stmt->bindParam(':sendDate', $send_date);

        // Execute the query and get the ID from the result set
        $stmt->execute();


        $stmt2 = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");

        // bind the parameter to the placeholder using the bindValue method
        $stmt2->bindValue(':systemId', $system_id);

        // Execute the query
        $stmt2->execute();

        // Fetch the result
        $result = $stmt2->fetch(PDO::FETCH_ASSOC);

        $catatan = 'Nota: Surat Permohonan Kelulusan Izin Lalu bagi Pihak Berkuasa ' . $authority_name . ' telah dihantar. ';

        //TODO : change to new function
        // insert to flw_appl_notes
        $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authorityId)");
        $stmtNote->bindParam(':systemId', $system_id);
        $stmtNote->bindParam(':notes', $catatan);
        $stmtNote->bindParam(':created', $created);
        $stmtNote->bindParam(':authorityId', $authority_id);
        $stmtNote->execute();

        // Get the ID of the inserted row
        $notesId = $conn->lastInsertId();

        //TODO : change to new function
        // Update Status Application // (45 Way Leave Submited Application -> 45 Way Leave Submited Application)
        $statusUpdateResult = updateProjectStatus($conn, $system_id, 45, 45);


        // set initial status of changeLogUpdateResult
        $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

        // Update changelog if status update successful
        if ($statusUpdateResult['result'] == true) {
            //TODO : change to new function
            insertSysRecordChangelog($conn, $system_id, 'Surat Permohonan Kelulusan Izin Lalu bagi Pihak Berkuasa ' . $authority_name . ' telah dihantar.', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

            // declare telegram notification string
            $telegramMsg = "Surat Permohonan Kelulusan Izin Lalu telah dihantar. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "\n📍 Pihak Berkuasa: " . $authority_name . "\n📅 Tarikh Hantar: " . $send_dt . "</strong>";
            //TODO : change to new function
            // get chatId for role 11,12
            $telegramId = TelegramApi::getTelegramIdByRole([11, 12]);
            // $telegramId = TelegramApi::getTelegramIdByRole([12]);
            foreach ($telegramId as $chatId) {
                $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
            }
        }

        $message = "Bukti Penghantaran Berjaya Dimuat Naik 🎉";

        header('HTTP/2 200 OK');
        echo json_encode([
            "message" => $message,
            "statusUpdate" => $statusUpdateResult,
            "changeLogUpdate" => $changeLogUpdateResult
        ]);

        // Close the database connection
        $conn = null;

    } else if ($data["item"] == "receive-kil") {
        // Prepare the query for update
        $query = "UPDATE flw_wayleave SET status = :wyStatus, dt_appv_ltr = :approvalDate, dt_appv_ltr_received = :receiveDate WHERE id = :id ";
        $stmt = $conn->prepare($query);

        // Value
        $wy_id = $data["wy-id"];
        $wy_status = 3;
        $approval_date = $data['dt-approval-wayleave'];
        $receive_date = $data['dt-receive-wayleave'];
        $system_id = $data['system-id'];
        $authority_name = $data['authority-name'];
        $authority_id = $data['authority-id'];

        $receive_dt = convertDate($receive_date);
        $created = date('Y-m-d H:i:s', time());

        // Bind the parameters
        $stmt->bindParam(':wyStatus', $wy_status);
        $stmt->bindParam(':id', $wy_id);
        $stmt->bindParam(':approvalDate', $approval_date);
        $stmt->bindParam(':receiveDate', $receive_date);

        // Execute the query and get the ID from the result set
        $stmt->execute();


        $stmt2 = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");

        // bind the parameter to the placeholder using the bindValue method
        $stmt2->bindValue(':systemId', $system_id);
        // Execute the query
        $stmt2->execute();
        // Fetch the result
        $result = $stmt2->fetch(PDO::FETCH_ASSOC);

        // check total count
        $check = $conn->prepare("SELECT COUNT(id) as total FROM view_authorities WHERE system_id = :systemID");
        $check->bindParam(':systemID', $system_id);
        $check->execute();
        $row = $check->fetch(PDO::FETCH_ASSOC);

        $totalRow = $row['total'];

        // check current count
        $checkCurrent = $conn->prepare("SELECT COUNT(id) as totalcurrent FROM view_authorities WHERE system_id = :systemID AND wl_status= :wyStatus ");
        $wy_status_receive = 3;
        $checkCurrent->bindParam(':systemID', $system_id);
        $checkCurrent->bindParam(':wyStatus', $wy_status_receive);
        $checkCurrent->execute();
        $rowCurrent = $checkCurrent->fetch(PDO::FETCH_ASSOC);

        $totalrowCurrent = $rowCurrent['totalcurrent'];

        if ($totalrowCurrent >= $totalRow) {

            $catatan = 'Nota: Surat Kelulusan Izin Lalu bagi Pihak Berkuasa ' . $authority_name . ' telah diterima. ';

            //TODO : change to new function
            // insert to flw_appl_notes
            $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authorityId)");
            $stmtNote->bindParam(':systemId', $system_id);
            $stmtNote->bindParam(':notes', $catatan);
            $stmtNote->bindParam(':created', $created);
            $stmtNote->bindParam(':authorityId', $authority_id);
            $stmtNote->execute();

            // Get the ID of the inserted row
            $notesId = $conn->lastInsertId();

            //TODO : change to new function
            // Update Status Application // (45 Way Leave Submited Application -> 42 Way Leave Approval)
            $statusUpdateResult = updateProjectStatus($conn, $system_id, 45, 42);


            // set initial status of changeLogUpdateResult
            $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

            // Update changelog if status update successful
            if ($statusUpdateResult['result'] == true) {
                //update table ctrl_authorities
                $stmt5 = $conn->prepare("UPDATE ctrl_authorities SET authority_status = 42 WHERE wayleave_id = :wyId");
                $stmt5->bindValue(':wyId', $wy_id);
                $stmt5->execute();

                //TODO : change to new function
                insertSysRecordChangelog($conn, $system_id, 'Surat Kelulusan Izin Lalu bagi Pihak Berkuasa ' . $authority_name . ' telah diterima.', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                // get url of attachment
                $stmtAttach = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 18 ");
                $stmtAttach->bindValue(':systemId', $system_id);
                $stmtAttach->execute();
                $resultAttach = $stmtAttach->fetch(PDO::FETCH_ASSOC);
                $url_attachment = base64_decode($resultAttach['url']);

                // declare telegram notification string
                $telegramMsg = "Surat Kelulusan Izin Lalu telah diterima. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "\n📍 Pihak Berkuasa: " . $authority_name . "\n📅 Tarikh Terima: " . $receive_dt . "</strong>";
                // notify RO, admin permit, account
                $telegramId = getTelegramIdByRole([11, 12, 44, 21, 22, 23]);
                // $telegramId = getTelegramIdByRole([12]);
                foreach ($telegramId as $chatId) {
                    $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                    $telegramResponse2 = telegramSendDocument($chatId, $url_attachment);
                }

                //TODO : change to new function
                // notify ketua survey
                $timeConverted = Utilities::convertDateToMalay($created);

                $telegramMsg2 = "Tugasan Baru : Pembahagian Tugasan Pengukuran" . PHP_EOL . PHP_EOL . "🔗 No Rujukan : " . $result['reference_no'] . PHP_EOL . "📅 Tarikh Tugasan Masuk : " . $timeConverted;

                //TODO : change to new function
                // get chatId for role 61
                $telegramId2 = TelegramApi::getTelegramIdByRole([61]);
                foreach ($telegramId2 as $chatId2) {
                    $telegramResponse = telegramSendMessage($chatId2, $telegramMsg2);
                }
                ;
            }

            // Prepare the query for update mapping status
            $queryM = "UPDATE status_appl SET mapping_status = 61 WHERE system_id = :sysid ";
            $stmtM = $conn->prepare($queryM);

            $stmtM->bindParam(':sysid', $system_id);
            $stmtM->execute();

            // Prepare the query for update sub_mapping_status flw_appl_mapping
            $querySM = "UPDATE flw_appl_mapping SET sub_mapping_status = 2 WHERE system_id = :sysid ";
            $stmtSM = $conn->prepare($querySM);

            $stmtSM->bindParam(':sysid', $system_id);
            $stmtSM->execute();

            $message = "Surat Kelulusan Izin Lalu Berjaya Dimuat Naik 🎉";

            header('HTTP/2 200 OK');
            echo json_encode([
                "message" => $message,
                "url" => $url_attachment,
                "statusUpdate" => $statusUpdateResult,
                "changeLogUpdate" => $changeLogUpdateResult
            ]);

            // Close the database connection
            $conn = null;

        } else {

            // get url of attachment
            $stmtAttach = $conn->prepare("SELECT url FROM flw_appl_attachments WHERE system_id = :systemId AND attachment_type = 18 ");
            $stmtAttach->bindValue(':systemId', $system_id);
            $stmtAttach->execute();
            $resultAttach = $stmtAttach->fetch(PDO::FETCH_ASSOC);
            $url_attachment = base64_decode($resultAttach['url']);

            $catatan = 'Nota: Surat Kelulusan Izin Lalu bagi Pihak Berkuasa ' . $authority_name . ' telah diterima. ';

            //TODO : change to new function
            // insert to flw_appl_notes
            $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authorityId)");
            $stmtNote->bindParam(':systemId', $system_id);
            $stmtNote->bindParam(':notes', $catatan);
            $stmtNote->bindParam(':created', $created);
            $stmtNote->bindParam(':authorityId', $authority_id);
            $stmtNote->execute();

            // Get the ID of the inserted row
            $notesId = $conn->lastInsertId();

            // Update Status Application // (45 Way Leave Submited Application -> 45 Way Leave Submited Application)
            //TODO : change to new function
            $statusUpdateResult = updateProjectStatus($conn, $system_id, 45, 45);

            // set initial status of changeLogUpdateResult
            $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

            // Update changelog if status update successful
            if ($statusUpdateResult['result'] == true) {

                if ($appsTitle === 'UCIDOS' || $appsTitle === 'KUDRAT') {
                    //update table ctrl_authorities
                    $stmt5 = $conn->prepare("UPDATE ctrl_authorities SET authority_status = 45 WHERE wayleave_id = :wyId");
                    $stmt5->bindValue(':wyId', $wy_id);
                    $stmt5->execute();
                } else {
                    //update table ctrl_authorities
                    $stmt5 = $conn->prepare("UPDATE ctrl_authorities SET authority_status = 42 WHERE wayleave_id = :wyId");
                    $stmt5->bindValue(':wyId', $wy_id);
                    $stmt5->execute();
                }
                //TODO : change to new function
                insertSysRecordChangelog($conn, $system_id, 'Surat Kelulusan Izin Lalu bagi Pihak Berkuasa ' . $authority_name . ' telah diterima.', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                // declare telegram notification string
                $telegramMsg = "Surat Kelulusan Izin Lalu telah diterima. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "\n📍 Pihak Berkuasa: " . $authority_name . "\n📅 Tarikh Terima: " . $receive_dt . "</strong>";
                // get chatId for role 11,12,61,44,21,22,23
                $telegramId = getTelegramIdByRole([12]);
                // $telegramId = getTelegramIdByRole([11,12,61,44,21,22,23]);
                foreach ($telegramId as $chatId) {
                    $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                    $telegramResponse2 = telegramSendDocument($chatId, $url_attachment);
                }
            }

            $message = "Surat Kelulusan Izin Lalu Berjaya Dimuat Naik 🎉";

            header('HTTP/2 200 OK');
            echo json_encode([
                "message" => $message,
                "url" => $url_attachment,
                "statusUpdate" => $statusUpdateResult,
                "changeLogUpdate" => $changeLogUpdateResult
            ]);
        }

        // Close the database connection
        $conn = null;

    } else if ($data["item"] == "send-wyFeedback-approval") {
        // Value
        $wyFeedback_id = $data["ltr-wyFeedback-id"];
        $system_id = $data['system-id'];
        $timestamp = date('Y-m-d H:i:s', time());

        //get wy id
        $stmtWy = $conn->prepare("SELECT flw_wayleave.id, public.view_authorities.authority_name AS \"Authority\", public.view_authorities.authority_id AS \"AuthorityID\"
                                    FROM flw_wayleave LEFT JOIN public.view_authorities ON public.view_authorities.\"wl_id\" = flw_wayleave.id
                                    WHERE flw_wayleave.letter_wy_id = :wyFeedbackid");
        $stmtWy->bindValue(':wyFeedbackid', $wyFeedback_id);
        $stmtWy->execute();
        $resultWy = $stmtWy->fetch(PDO::FETCH_ASSOC);
        $wy_id = $resultWy['id'];
        $authority_name = $resultWy['Authority'];
        $authority_id = $resultWy['AuthorityID'];

        // Prepare the query for update
        $query = "UPDATE flw_wayleave_letters SET status = 2 WHERE id = :id AND system_id = :systemId ";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':id', $wyFeedback_id);
        $stmt->bindParam(':systemId', $system_id);
        $stmt->execute();

        // Prepare the query for update
        $queryUpdate = "UPDATE flw_wayleave SET status = 6, dt_fb_ltr_created = :created WHERE system_id = :sid AND letter_wy_id = :wyFeedbackid ";
        $stmtUpdate = $conn->prepare($queryUpdate);

        // Bind the parameters
        $stmtUpdate->bindParam(':sid', $system_id);
        $stmtUpdate->bindValue(':wyFeedbackid', $wyFeedback_id);
        $stmtUpdate->bindParam(':created', $timestamp);
        $stmtUpdate->execute();

        // Prepare the query for update
        $queryAuthorities = "UPDATE ctrl_authorities SET authority_status = 43 WHERE system_id = :sid AND wayleave_id = :wyid ";
        $stmtAuthorities = $conn->prepare($queryAuthorities);

        // Bind the parameters
        $stmtAuthorities->bindParam(':sid', $system_id);
        $stmtAuthorities->bindParam(':wyid', $wy_id);
        $stmtAuthorities->execute();


        // Query message for telegram
        $stmt2 = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");
        $stmt2->bindValue(':systemId', $system_id);
        $stmt2->execute();
        $result = $stmt2->fetch(PDO::FETCH_ASSOC);

        $created = date('Y-m-d H:i:s', time());
        $catatan = 'Nota: Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan';

        //TODO : change to new function
        // insert to flw_appl_notes
        $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
        $stmtNote->bindParam(':systemId', $system_id);
        $stmtNote->bindParam(':notes', $catatan);
        $stmtNote->bindParam(':created', $created);
        $stmtNote->execute();

        // Get the ID of the inserted row
        $notesId = $conn->lastInsertId();



        // check count
        $check = $conn->prepare("SELECT COUNT(id) as total FROM flw_wayleave WHERE system_id = :systemID");
        $check->bindParam(':systemID', $system_id);
        $check->execute();
        $row = $check->fetch(PDO::FETCH_ASSOC);

        $totalRow = $row['total'];

        // check current
        $checkCurrent = $conn->prepare("SELECT COUNT(id) as totalcurrent FROM flw_wayleave WHERE system_id = :systemID AND status= :wyStatus ");
        // value
        $wy_status_receive = 6;
        // bind parameter
        $checkCurrent->bindParam(':systemID', $system_id);
        $checkCurrent->bindParam(':wyStatus', $wy_status_receive);
        $checkCurrent->execute();
        $rowCurrent = $checkCurrent->fetch(PDO::FETCH_ASSOC);

        $totalrowCurrent = $rowCurrent['totalcurrent'];

        if ($totalrowCurrent >= $totalRow) {
            // Update Status Application // (51 Invoice 50% / 100% Submitted -> 43 Way Leave Approval Feedback)
            //TODO : change to new function
            $statusUpdateResult = updateProjectStatus($conn, $system_id, 51, 43);

            // set initial status of changeLogUpdateResult
            $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

            // Update changelog if status update successful
            if ($statusUpdateResult['result'] == true) {
                //TODO : change to new function
                insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                // declare telegram notification string
                $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "\n 📍 Pihak Berkuasa : " . $authority_name . "</strong>";
                // get chatId for role 73
                $telegramId = getTelegramIdByRole([73]);
                foreach ($telegramId as $chatId) {
                    $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                }
            }

        } else {
            //TODO : change to new function
            $statusUpdateResult = updateProjectStatus($conn, $system_id, 51, 51);

            // set initial status of changeLogUpdateResult
            $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

            // Update changelog if status update successful
            if ($statusUpdateResult['result'] == true) {
                //TODO : change to new function
                insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                // declare telegram notification string
                $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "\n 📍 Pihak Berkuasa : " . $authority_name . "</strong>";
                //TODO : change to new function
                // get chatId for role 73
                $telegramId = TelegramApi::getTelegramIdByRole([73]);
                foreach ($telegramId as $chatId) {
                    $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                }
            }
        }
        $message = "Pengesahan Surat Maklum Balas Kelulusan Izin Lalu Berjaya Dihantar 🎉";

        header('HTTP/2 200 OK');
        echo json_encode([
            "message" => $message,
            "sysid" => $system_id
        ]);

        $conn = null;

    } else if ($data["item"] == "wyFeedback-approved") {
        // Prepare the query for update
        $query = "UPDATE flw_wayleave_letters SET status = :status, notes = :notes WHERE id = :id AND system_id = :systemId ";
        $stmt = $conn->prepare($query);

        // Value
        $wyFeedback_id = $data["ltr-wyFeedback-id"];
        $system_id = $data['system-id'];
        $notes = $data['notes'];
        $timestamp = date('Y-m-d H:i:s', time());

        $confirmation = isset($data['confirmation']) ? $data['confirmation'] : 0;

        if ($confirmation == 1) {
            $status = 3; //approve
        } else {
            $status = 4; // not approve
        }

        // Bind the parameters
        $stmt->bindParam(':id', $wyFeedback_id);
        $stmt->bindParam(':systemId', $system_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':notes', $notes);

        // Execute the query and get the ID from the result set
        $stmt->execute();

        // Query message for telegram
        $stmt2 = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");
        $stmt2->bindValue(':systemId', $system_id);
        $stmt2->execute();

        // Fetch the result
        $result = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($confirmation == 1) {
            // Prepare the query for update
            $queryFb = "UPDATE flw_wayleave SET status = 7, dt_fb_ltr_created = :created WHERE system_id = :sid AND letter_wy_id = :wyFeedbackid ";
            $stmtFb = $conn->prepare($queryFb);

            // Bind the parameters
            $stmtFb->bindParam(':sid', $system_id);
            $stmtFb->bindValue(':wyFeedbackid', $wyFeedback_id);
            $stmtFb->bindParam(':created', $timestamp);
            $stmtFb->execute();

            //get wy id
            $stmtWy = $conn->prepare("SELECT flw_wayleave.id, public.view_authorities.authority_name AS \"Authority\", public.view_authorities.authority_id AS \"AuthorityID\"
            FROM flw_wayleave LEFT JOIN public.view_authorities ON public.view_authorities.\"wl_id\" = flw_wayleave.id
            WHERE flw_wayleave.letter_wy_id = :wyFeedbackid");
            $stmtWy->bindValue(':wyFeedbackid', $wyFeedback_id);
            $stmtWy->execute();
            $resultWy = $stmtWy->fetch(PDO::FETCH_ASSOC);
            $wy_id = $resultWy['id'];
            $authority_id = $resultWy['AuthorityID'];

            // Prepare the query for update
            $queryAuthorities = "UPDATE ctrl_authorities SET authority_status = 46 WHERE system_id = :sid AND wayleave_id = :wyid ";
            $stmtAuthorities = $conn->prepare($queryAuthorities);

            // Bind the parameters
            $stmtAuthorities->bindParam(':sid', $system_id);
            $stmtAuthorities->bindParam(':wyid', $wy_id);
            $stmtAuthorities->execute();

            // check count
            $check = $conn->prepare("SELECT COUNT(id) as total FROM flw_wayleave WHERE system_id = :systemID");
            $check->bindParam(':systemID', $system_id);
            $check->execute();
            $row = $check->fetch(PDO::FETCH_ASSOC);

            $totalRow = $row['total'];

            // check current
            $checkCurrent = $conn->prepare("SELECT COUNT(id) as totalcurrent FROM flw_wayleave WHERE system_id = :systemID AND status= :wyStatus ");
            // value
            $wy_status_receive = 7;
            // bind parameter
            $checkCurrent->bindParam(':systemID', $system_id);
            $checkCurrent->bindParam(':wyStatus', $wy_status_receive);
            $checkCurrent->execute();
            $rowCurrent = $checkCurrent->fetch(PDO::FETCH_ASSOC);

            $totalrowCurrent = $rowCurrent['totalcurrent'];

            if ($totalrowCurrent >= $totalRow) {
                $message = "Surat Maklum Balas Kelulusan Izin Lalu Berjaya Disahkan 🎉";
                $catatan = 'Surat Maklum Balas Kelulusan Izin Lalu telah disahkan. Nota: ' . $notes;

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authority_id)");
                $stmtNote->bindParam(':systemId', $system_id);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->bindParam(':authority_id', $authority_id);
                $stmtNote->execute();

                // Get the ID of the inserted row
                $notesId = $conn->lastInsertId();

                // Update Status Application // (43 Way Leave Approval Feedback -> 46 Way Leave Feedback Approved)
                //TODO : change to new function
                $statusUpdateResult = updateProjectStatus($conn, $system_id, 43, 46);

                // TODO: Update changelog table
                // set initial status of changeLogUpdateResult
                $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

                // Update changelog if status update successful
                if ($statusUpdateResult['result'] == true) {
                    //TODO : change to new function
                    insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu telah disahkan', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                    // declare telegram notification string
                    $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu telah <strong>disahkan</strong>. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "</strong>";
                    // get chatId for role 44
                    $telegramId = getTelegramIdByRole([44]);
                    foreach ($telegramId as $chatId) {
                        $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                    }
                }
            } else {
                $message = "Surat Maklum Balas Kelulusan Izin Lalu Berjaya Disahkan 🎉";
                $catatan = 'Surat Maklum Balas Kelulusan Izin Lalu telah disahkan. Nota: ' . $notes;

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authority_id)");
                $stmtNote->bindParam(':systemId', $system_id);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->bindParam(':authority_id', $authority_id);
                $stmtNote->execute();

                // Get the ID of the inserted row
                $notesId = $conn->lastInsertId();

                // Update Status Application // (43 Way Leave Approval Feedback -> 46 Way Leave Feedback Approved)
                //TODO : change to new function
                $statusUpdateResult = updateProjectStatus($conn, $system_id, 43, 43);

                // set initial status of changeLogUpdateResult
                $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

                // Update changelog if status update successful
                if ($statusUpdateResult['result'] == true) {
                    //TODO : change to new function
                    insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu telah disahkan', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                    // declare telegram notification string
                    $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu telah <strong>disahkan</strong>. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "</strong>";
                    // get chatId for role 44
                    $telegramId = getTelegramIdByRole([44]);
                    foreach ($telegramId as $chatId) {
                        $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                    }
                }
            }

        } else {
            // Prepare the query for update
            $queryFb = "UPDATE flw_wayleave SET status = 8, dt_fb_ltr_created = :created WHERE system_id = :sid AND letter_wy_id = :wyFeedbackid ";
            $stmtFb = $conn->prepare($queryFb);

            // Bind the parameters
            $stmtFb->bindParam(':sid', $system_id);
            $stmtFb->bindValue(':wyFeedbackid', $wyFeedback_id);
            $stmtFb->bindParam(':created', $timestamp);
            $stmtFb->execute();

            //get wy id
            $stmtWy = $conn->prepare("SELECT flw_wayleave.id, public.view_authorities.authority_name AS \"Authority\", public.view_authorities.authority_id AS \"AuthorityID\"
            FROM flw_wayleave LEFT JOIN public.view_authorities ON public.view_authorities.\"wl_id\" = flw_wayleave.id
            WHERE flw_wayleave.letter_wy_id = :wyFeedbackid");
            $stmtWy->bindValue(':wyFeedbackid', $wyFeedback_id);
            $stmtWy->execute();
            $resultWy = $stmtWy->fetch(PDO::FETCH_ASSOC);
            $wy_id = $resultWy['id'];
            $authority_id = $resultWy['AuthorityID'];

            // Prepare the query for update
            $queryAuthorities = "UPDATE ctrl_authorities SET authority_status = 47 WHERE system_id = :sid AND wayleave_id = :wyid ";
            $stmtAuthorities = $conn->prepare($queryAuthorities);

            // Bind the parameters
            $stmtAuthorities->bindParam(':sid', $system_id);
            $stmtAuthorities->bindParam(':wyid', $wy_id);
            $stmtAuthorities->execute();

            // check count
            $check = $conn->prepare("SELECT COUNT(id) as total FROM flw_wayleave WHERE system_id = :systemID");
            $check->bindParam(':systemID', $system_id);
            $check->execute();
            $row = $check->fetch(PDO::FETCH_ASSOC);

            $totalRow = $row['total'];

            // check current
            $checkCurrent = $conn->prepare("SELECT COUNT(id) as totalcurrent FROM flw_wayleave WHERE system_id = :systemID AND status= :wyStatus ");
            // value
            $wy_status_receive = 8;
            // bind parameter
            $checkCurrent->bindParam(':systemID', $system_id);
            $checkCurrent->bindParam(':wyStatus', $wy_status_receive);
            $checkCurrent->execute();
            $rowCurrent = $checkCurrent->fetch(PDO::FETCH_ASSOC);

            $totalrowCurrent = $rowCurrent['totalcurrent'];

            if ($totalrowCurrent >= $totalRow) {
                $message = "Surat Maklum Balas Kelulusan Izin Lalu Tidak Disahkan";
                $catatan = 'Surat Maklum Balas Kelulusan Izin Lalu tidak disahkan. Nota: ' . $notes;

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authority_id)");
                $stmtNote->bindParam(':systemId', $system_id);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->bindParam(':authority_id', $authority_id);
                $stmtNote->execute();

                // Get the ID of the inserted row
                $notesId = $conn->lastInsertId();

                // Update Status Application // (43 Way Leave Approval Feedback -> 46 Way Leave Feedback Not Approved)
                //TODO : change to new function
                $statusUpdateResult = updateProjectStatus($conn, $system_id, 43, 47);

                // set initial status of changeLogUpdateResult
                $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

                // Update changelog if status update successful
                if ($statusUpdateResult['result'] == true) {
                    //TODO : change to new function
                    insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu tidak disahkan ', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                    // declare telegram notification string
                    $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu <strong>tidak disahkan</strong>. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "</strong>";
                    // get chatId for role 44
                    $telegramId = getTelegramIdByRole([44]);
                    foreach ($telegramId as $chatId) {
                        $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                    }
                }

            } else {
                $message = "Surat Maklum Balas Kelulusan Izin Lalu Tidak Disahkan";
                $catatan = 'Surat Maklum Balas Kelulusan Izin Lalu tidak disahkan. Nota: ' . $notes;

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at, authority_id) VALUES (:systemId, :notes, :created, :authority_id)");
                $stmtNote->bindParam(':systemId', $system_id);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->bindParam(':authority_id', $authority_id);
                $stmtNote->execute();

                // Get the ID of the inserted row
                $notesId = $conn->lastInsertId();

                // Update Status Application // (43 Way Leave Approval Feedback -> 46 Way Leave Feedback Not Approved)
                //TODO : change to new function
                $statusUpdateResult = updateProjectStatus($conn, $system_id, 43, 43);

                // set initial status of changeLogUpdateResult
                $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

                // Update changelog if status update successful
                if ($statusUpdateResult['result'] == true) {
                    //TODO : change to new function
                    insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu tidak disahkan ', $statusUpdateResult['new_status'], $authority_id, notes_id: $notesId);

                    // declare telegram notification string
                    $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu <strong>tidak disahkan</strong>. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "</strong>";
                    // get chatId for role 44
                    $telegramId = getTelegramIdByRole([44]);
                    foreach ($telegramId as $chatId) {
                        $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
                    }
                }
            }



        }

        // Finally, return a JSON
        echo json_encode(
            array(
                "message" => $message
            )
        );

        // Close the database connection
        $conn = null;

    } else if ($data["item"] == "generate-wyFeedback-edit") {
        $authorityId = $data['authority_id'];
        $addressId = $data['address_id'];

        if ($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT') {
            if ($data["status"] == "51" || $data["status"] == "47") {
                // Prepare the query for update
                $query = "UPDATE flw_wayleave_letters SET up_provider = :upProvider, up_client = :upClient, letter_date = :letterDate, staff_name = :staffName, staff_contact = :staffContact, staff_name_2 = :staffName2, staff_contact_2 = :staffContact2, staff_approval = :staffApproval, staff_approval_position = :staffApprovalPosition, client_ref_no = :clientRefNo WHERE id = :fid ";
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':fid', $wyFeedbackId);
                $stmt->bindParam(':upProvider', $upProvider);
                $stmt->bindParam(':upClient', $upClient);
                $stmt->bindParam(':letterDate', $letterDate);
                $stmt->bindParam(':staffName', $staffName);
                $stmt->bindParam(':staffContact', $staffContact);
                $stmt->bindParam(':staffName2', $staffName2);
                $stmt->bindParam(':staffContact2', $staffContact2);
                $stmt->bindParam(':staffApproval', $staffApproval);
                $stmt->bindParam(':staffApprovalPosition', $staffApprovalPosition);
                $stmt->bindParam(':clientRefNo', $clientRefNo);

                // values
                $systemId = $data['system_id'];
                $upProvider = $data['up_provider'];
                $upClient = $data['up_pemohon'];
                $letterDate = $data['letter_date'];
                $staffName = $data['staff_name'];
                $staffContact = $data['staff_contact'];
                $staffName2 = $data['staff_name_2'];
                $staffContact2 = $data['staff_contact_2'];
                $staffApproval = $data['approval_by'];
                $staffApprovalPosition = $data['approval_position'];
                $clientRefNo = $data['client_ref_no'];

                $letterRefNo = $data['letter_ref_no'];
                $wyFeedbackId = $data['wy_feedback_id'];
                $timestamp = date('Y-m-d H:i:s', time());

                // Execute the query and get the ID from the result set
                $stmt->execute();

                // Prepare the query for update tble address
                $queryAddr = "UPDATE flw_address_wy_letters SET addr_provider_1 = :addrProvider1, addr_provider_2 = :addrProvider2, addr_provider_3 = :addrProvider3, addr_client_1 = :addrClient1, addr_client_2 = :addrClient2, addr_client_3 = :addrClient3 WHERE id = :addressId ";
                $stmtAddr = $conn->prepare($queryAddr);

                $stmtAddr->bindParam(':addressId', $addressId);
                $stmtAddr->bindParam(':addrProvider1', $addrProvider1);
                $stmtAddr->bindParam(':addrProvider2', $addrProvider2);
                $stmtAddr->bindParam(':addrProvider3', $addrProvider3);
                $stmtAddr->bindParam(':addrClient1', $addrClient1);
                $stmtAddr->bindParam(':addrClient2', $addrClient2);
                $stmtAddr->bindParam(':addrClient3', $addrClient3);

                $addrProvider1 = $data['addr_provider_1'];
                $addrProvider2 = $data['addr_provider_2'];
                $addrProvider3 = $data['addr_provider_3'];
                $addrClient1 = $data['addr_client_1'];
                $addrClient2 = $data['addr_client_2'];
                $addrClient3 = $data['addr_client_3'];

                $stmtAddr->execute();

                // Prepare the query for update
                $query = "UPDATE flw_wayleave SET status = 5, dt_fb_ltr_created = :created WHERE system_id = :sid AND authority = :authorityid ";
                $stmtUpdate = $conn->prepare($query);

                // Bind the parameters
                $stmtUpdate->bindParam(':sid', $systemId);
                $stmtUpdate->bindParam(':authorityid', $authorityId);
                $stmtUpdate->bindParam(':created', $timestamp);
                $stmtUpdate->execute();

                $catatan = 'Surat Maklum Balas Izin Lalu Telah Berjaya Dikemaskini';

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
                $stmtNote->bindParam(':systemId', $systemId);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->execute();

                $message = "Surat Maklum Balas Izin Lalu Telah Berjaya Dikemaskini 🎉";

                header('HTTP/2 200 OK');
                echo json_encode([
                    "message" => $message,
                    "status" => 200,
                    "sysid" => $systemId,
                    "letterRefNo" => $letterRefNo,
                    "id" => $wyFeedbackId
                ]);

                // Close the database connection
                $conn = null;

            } else {
                // Prepare the query for update
                $query = "UPDATE flw_wayleave_letters SET up_provider = :upProvider, up_client = :upClient, staff_name = :staffName, staff_contact = :staffContact, staff_name_2 = :staffName2, staff_contact_2 = :staffContact2, staff_approval = :staffApproval, staff_approval_position = :staffApprovalPosition, client_ref_no = :clientRefNo WHERE system_id = :sid AND status = 4 ";
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':sid', $systemId);
                $stmt->bindParam(':upProvider', $upProvider);
                $stmt->bindParam(':upClient', $upClient);
                $stmt->bindParam(':staffName', $staffName);
                $stmt->bindParam(':staffContact', $staffContact);
                $stmt->bindParam(':staffName2', $staffName2);
                $stmt->bindParam(':staffContact2', $staffContact2);
                $stmt->bindParam(':staffApproval', $staffApproval);
                $stmt->bindParam(':staffApprovalPosition', $staffApprovalPosition);
                $stmt->bindParam(':clientRefNo', $clientRefNo);

                // values
                $systemId = $data['system_id'];
                $upProvider = $data['up_provider'];
                $upClient = $data['up_pemohon'];
                $staffName = $data['staff_name'];
                $staffContact = $data['staff_contact'];
                $staffName2 = $data['staff_name_2'];
                $staffContact2 = $data['staff_contact_2'];
                $staffApproval = $data['approval_by'];
                $staffApprovalPosition = $data['approval_position'];
                $clientRefNo = $data['client_ref_no'];

                $letterRefNo = $data['letter_ref_no'];
                $timestamp = date('Y-m-d H:i:s', time());

                // Execute the query and get the ID from the result set
                $stmt->execute();

                // Prepare the query for update tble address
                $queryAddr = "UPDATE flw_address_wy_letters SET addr_provider_1 = :addrProvider1, addr_provider_2 = :addrProvider2, addr_provider_3 = :addrProvider3, addr_client_1 = :addrClient1, addr_client_2 = :addrClient2, addr_client_3 = :addrClient3 WHERE id = :addressId ";
                $stmtAddr = $conn->prepare($queryAddr);

                $stmtAddr->bindParam(':addressId', $addressId);
                $stmtAddr->bindParam(':addrProvider1', $addrProvider1);
                $stmtAddr->bindParam(':addrProvider2', $addrProvider2);
                $stmtAddr->bindParam(':addrProvider3', $addrProvider3);
                $stmtAddr->bindParam(':addrClient1', $addrClient1);
                $stmtAddr->bindParam(':addrClient2', $addrClient2);
                $stmtAddr->bindParam(':addrClient3', $addrClient3);

                $addrProvider1 = $data['addr_provider_1'];
                $addrProvider2 = $data['addr_provider_2'];
                $addrProvider3 = $data['addr_provider_3'];
                $addrClient1 = $data['addr_client_1'];
                $addrClient2 = $data['addr_client_2'];
                $addrClient3 = $data['addr_client_3'];

                $stmtAddr->execute();

                // Prepare the query for update
                $query = "UPDATE flw_wayleave SET status = 8, dt_fb_ltr_created = :created WHERE system_id = :sid AND authority = :authorityid ";
                $stmtUpdate = $conn->prepare($query);

                // Bind the parameters
                $stmtUpdate->bindParam(':sid', $systemId);
                $stmtUpdate->bindParam(':authorityid', $authorityId);
                $stmtUpdate->bindParam(':created', $timestamp);
                $stmtUpdate->execute();

                $catatan = 'Surat Maklum Balas Izin Lalu tidak disahkan';

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
                $stmtNote->bindParam(':systemId', $systemId);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->execute();

                $message = "Surat Maklum Balas Izin Lalu Tidak Disahkan";

                header('HTTP/2 200 OK');
                echo json_encode([
                    "message" => $message,
                    "sysId" => $systemId,
                    "status" => 4
                ]);

                // Close the database connection
                $conn = null;
            }

        } else if ($appsTitle == 'KITER' || $appsTitle == 'KUK') {
            if ($data["status"] == "51" || $data["status"] == "47") {
                // for edit
                // Prepare the query for update
                $query = "UPDATE flw_wayleave_letters SET up_provider = :upProvider, up_client = :upClient, inv_no = :invNo, inv_date = :invDate, letter_date = :letterDate, letter_hijri_date = :letterHijriDate, staff_name = :staffName, staff_contact = :staffContact, staff_approval = :staffApproval, staff_approval_position = :staffApprovalPosition WHERE id = :fid ";
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':fid', $wyFeedbackId);
                $stmt->bindParam(':upProvider', $upProvider);
                $stmt->bindParam(':upClient', $upClient);
                $stmt->bindParam(':invNo', $invNo);
                $stmt->bindParam(':invDate', $invDate);
                $stmt->bindParam(':letterDate', $letterDate);
                $stmt->bindParam(':letterHijriDate', $letterHijriDate);
                $stmt->bindParam(':staffName', $staffName);
                $stmt->bindParam(':staffContact', $staffContact);
                $stmt->bindParam(':staffApproval', $staffApproval);
                $stmt->bindParam(':staffApprovalPosition', $staffApprovalPosition);

                // values
                $systemId = $data['system_id'];
                $upProvider = $data['up_provider'];
                $upClient = $data['up_pemohon'];
                $invNo = $data['inv_no'];
                $invDate = $data['inv_date'];
                $letterRefNo = $data['letter_ref_no'];
                $letterDate = $data['letter_date'];
                $letterHijriDate = $data['date_hijri'];
                $staffName = $data['staff_name'];
                $staffContact = $data['staff_contact'];
                $staffApproval = $data['approval_by'];
                $staffApprovalPosition = $data['approval_position'];

                $wyFeedbackId = $data['wy_feedback_id'];
                $timestamp = date('Y-m-d H:i:s', time());

                // Execute the query and get the ID from the result set
                $stmt->execute();

                // Prepare the query for update tble address
                $queryAddr = "UPDATE flw_address_wy_letters SET addr_provider_1 = :addrProvider1, addr_provider_2 = :addrProvider2, addr_provider_3 = :addrProvider3, addr_client_1 = :addrClient1, addr_client_2 = :addrClient2, addr_client_3 = :addrClient3 WHERE id = :addressId ";
                $stmtAddr = $conn->prepare($queryAddr);

                $stmtAddr->bindParam(':addressId', $addressId);
                $stmtAddr->bindParam(':addrProvider1', $addrProvider1);
                $stmtAddr->bindParam(':addrProvider2', $addrProvider2);
                $stmtAddr->bindParam(':addrProvider3', $addrProvider3);
                $stmtAddr->bindParam(':addrClient1', $addrClient1);
                $stmtAddr->bindParam(':addrClient2', $addrClient2);
                $stmtAddr->bindParam(':addrClient3', $addrClient3);

                $addrProvider1 = $data['addr_provider_1'];
                $addrProvider2 = $data['addr_provider_2'];
                $addrProvider3 = $data['addr_provider_3'];
                $addrClient1 = $data['addr_client_1'];
                $addrClient2 = $data['addr_client_2'];
                $addrClient3 = $data['addr_client_3'];

                $stmtAddr->execute();

                // Prepare the query for update
                $query = "UPDATE flw_wayleave SET status = 5, dt_fb_ltr_created = :created WHERE system_id = :sid AND authority = :authorityid ";
                $stmtUpdate = $conn->prepare($query);

                // Bind the parameters
                $stmtUpdate->bindParam(':sid', $systemId);
                $stmtUpdate->bindParam(':authorityid', $authorityId);
                $stmtUpdate->bindParam(':created', $timestamp);
                $stmtUpdate->execute();

                $catatan = 'Surat Maklum Balas Izin Lalu Telah Berjaya Dikemaskini';

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
                $stmtNote->bindParam(':systemId', $systemId);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->execute();

                $message = "Surat Maklum Balas Izin Lalu Telah Berjaya Dikemaskini 🎉";

                header('HTTP/2 200 OK');
                echo json_encode([
                    "message" => $message,
                    "status" => 200,
                    "sysid" => $systemId,
                    "letterRefNo" => $letterRefNo,
                    "id" => $wyFeedbackId
                ]);

                // Close the database connection
                $conn = null;

            } else {
                // for amend
                // Prepare the query for update
                $query = "UPDATE flw_wayleave_letters SET up_provider = :upProvider, up_client = :upClient, inv_no = :invNo, inv_date = :invDate, staff_name = :staffName, staff_contact = :staffContact, staff_approval = :staffApproval, staff_approval_position = :staffApprovalPosition WHERE system_id = :sid AND status = 4 ";
                $stmt = $conn->prepare($query);

                // Bind the parameters
                $stmt->bindParam(':sid', $systemId);
                $stmt->bindParam(':upProvider', $upProvider);
                $stmt->bindParam(':upClient', $upClient);
                $stmt->bindParam(':invNo', $invNo);
                $stmt->bindParam(':invDate', $invDate);
                $stmt->bindParam(':staffName', $staffName);
                $stmt->bindParam(':staffContact', $staffContact);
                $stmt->bindParam(':staffApproval', $staffApproval);
                $stmt->bindParam(':staffApprovalPosition', $staffApprovalPosition);

                // values
                $systemId = $data['system_id'];
                $upProvider = $data['up_provider'];
                $upClient = $data['up_pemohon'];
                $invNo = $data['inv_no'];
                $invDate = $data['inv_date'];
                $letterRefNo = $data['letter_ref_no'];
                $staffName = $data['staff_name'];
                $staffContact = $data['staff_contact'];
                $staffApproval = $data['approval_by'];
                $staffApprovalPosition = $data['approval_position'];

                $letterRefNo = $data['letter_ref_no'];
                $wyFeedbackId = $data['wy_feedback_id'];
                $timestamp = date('Y-m-d H:i:s', time());

                // Execute the query and get the ID from the result set
                $stmt->execute();

                // Prepare the query for update tble address
                $queryAddr = "UPDATE flw_address_wy_letters SET addr_provider_1 = :addrProvider1, addr_provider_2 = :addrProvider2, addr_provider_3 = :addrProvider3, addr_client_1 = :addrClient1, addr_client_2 = :addrClient2, addr_client_3 = :addrClient3 WHERE id = :addressId ";
                $stmtAddr = $conn->prepare($queryAddr);

                $stmtAddr->bindParam(':addressId', $addressId);
                $stmtAddr->bindParam(':addrProvider1', $addrProvider1);
                $stmtAddr->bindParam(':addrProvider2', $addrProvider2);
                $stmtAddr->bindParam(':addrProvider3', $addrProvider3);
                $stmtAddr->bindParam(':addrClient1', $addrClient1);
                $stmtAddr->bindParam(':addrClient2', $addrClient2);
                $stmtAddr->bindParam(':addrClient3', $addrClient3);

                $addrProvider1 = $data['addr_provider_1'];
                $addrProvider2 = $data['addr_provider_2'];
                $addrProvider3 = $data['addr_provider_3'];
                $addrClient1 = $data['addr_client_1'];
                $addrClient2 = $data['addr_client_2'];
                $addrClient3 = $data['addr_client_3'];

                $stmtAddr->execute();

                // Prepare the query for update
                $query = "UPDATE flw_wayleave SET status = 8, dt_fb_ltr_created = :created WHERE system_id = :sid AND authority = :authorityid ";
                $stmtUpdate = $conn->prepare($query);

                // Bind the parameters
                $stmtUpdate->bindParam(':sid', $systemId);
                $stmtUpdate->bindParam(':authorityid', $authorityId);
                $stmtUpdate->bindParam(':created', $timestamp);
                $stmtUpdate->execute();

                $catatan = 'Surat Maklum Balas Izin Lalu tidak disahkan';

                //TODO : change to new function
                // insert to flw_appl_notes
                $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
                $stmtNote->bindParam(':systemId', $systemId);
                $stmtNote->bindParam(':notes', $catatan);
                $stmtNote->bindParam(':created', $timestamp);
                $stmtNote->execute();

                $message = "Surat Maklum Balas Izin Lalu Tidak Disahkan";

                header('HTTP/2 200 OK');
                echo json_encode([
                    "message" => $message,
                    "sysId" => $systemId,
                    "status" => 4
                ]);

                // Close the database connection
                $conn = null;
            }

        }

    } else if ($data["item"] == "send-wyFeedback-approval-amend") {

        // Prepare the query for update
        $query = "UPDATE flw_wayleave_letters SET status = 2 WHERE id = :id AND system_id = :systemId ";
        $stmt = $conn->prepare($query);

        // Value
        $wyFeedback_id = $data['wyFeedback-id'];
        $system_id = $data['system-id'];

        // Bind the parameters
        $stmt->bindParam(':id', $wyFeedback_id);
        $stmt->bindParam(':systemId', $system_id);

        // Execute the query and get the ID from the result set
        $stmt->execute();


        // Query message for telegram
        $stmt2 = $conn->prepare("SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId");
        $stmt2->bindValue(':systemId', $system_id);

        $stmt2->execute();

        // Fetch the result
        $result = $stmt2->fetch(PDO::FETCH_ASSOC);

        $created = date('Y-m-d H:i:s', time());
        $catatan = 'Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan.';

        //TODO : change to new function
        // insert to flw_appl_notes
        $stmtNote = $conn->prepare("INSERT INTO flw_appl_notes (system_id, notes, created_at) VALUES (:systemId, :notes, :created)");
        $stmtNote->bindParam(':systemId', $system_id);
        $stmtNote->bindParam(':notes', $catatan);
        $stmtNote->bindParam(':created', $created);
        $stmtNote->execute();

        // Get the ID of the inserted row
        $notesId = $conn->lastInsertId();

        // Update Status Application // (47 Way Leave Feedback Not Approve -> 43 Way Leave Approval Feedback)
        //TODO : change to new function
        $statusUpdateResult = updateProjectStatus($conn, $system_id, 47, 43);

        // set initial status of changeLogUpdateResult
        $changeLogUpdateResult = array('result' => false, 'message' => 'Change log update not triggered.');

        // Update changelog if status update successful
        if ($statusUpdateResult['result'] == true) {
            //TODO : change to new function
            insertSysRecordChangelog($conn, $system_id, 'Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan', $statusUpdateResult['new_status'], notes_id: $notesId);

            // declare telegram notification string
            $telegramMsg = "Surat Maklum Balas Kelulusan Izin Lalu perlu disahkan. \n\n<strong>🔗 No Rujukan : " . $result['reference_no'] . "</strong>";
            // get chatId for role 73
            $telegramId = getTelegramIdByRole([73]);
            foreach ($telegramId as $chatId) {
                $telegramResponse = telegramSendMessage($chatId, $telegramMsg, 'html');
            }
        }

        $message = "Pengesahan Surat Maklum Balas Kelulusan Izin Lalu Berjaya Dihantar 🎉";

        header('HTTP/2 200 OK');
        echo json_encode([
            "message" => $message
        ]);

        $conn = null;

    } else {
        echo "NOT AUTHORIZED!!!!!!!!!!!!!!!!!!!!!!!!!!!!";
    }
}



