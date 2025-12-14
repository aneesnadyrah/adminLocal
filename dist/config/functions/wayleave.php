<?php
// STUB: Assign to fakhri
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

class Wayleave
{
    public static function checkingModel($systemId)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $systemId = $sid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT flw_appl_entries.reference_no AS \"RefNo\", ls_authorities.name AS \"Authority\", ls_authorities.logo AS \"Logo\", flw_wayleave.dt_appl_ltr_created AS \"LetterDate\", flw_wayleave.auth_road_length AS \"Length\", flw_wayleave.status AS \"WyStatus\", flw_wayleave.id AS \"ID\", flw_wayleave.system_id AS \"SysID\", flw_wayleave.authority AS \"AuthorityID\", status_appl.project_status AS \"Status\"
    FROM flw_wayleave
    LEFT JOIN ls_authorities ON flw_wayleave.authority = ls_authorities.id
    LEFT JOIN flw_appl_entries ON flw_wayleave.system_id = flw_appl_entries.system_id
    LEFT JOIN status_appl ON flw_wayleave.system_id = status_appl.system_id
    WHERE flw_wayleave.system_id = :systemId ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;

    }

    public static function wyMKIL($sid)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $systemId = $sid;

        $stmt = $conn->prepare("SELECT public.view_authorities.reference_no AS \"RefNo\", public.view_authorities.authority_name AS \"Authority\", public.view_authorities.authority_logo AS \"Logo\", public.view_authorities.wl_appl_letter_date AS \"LetterDate\", public.view_authorities.wl_status AS \"WyStatus\", public.view_authorities.wl_id AS \"WyID\", public.view_authorities.system_id AS \"SysID\", public.view_authorities.authority_id AS \"AuthorityID\", flw_appl_entries.project_title AS \"ProjectTitle\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", status_appl.project_status AS \"Status\", status_appl.id AS \"StatusID\", flw_wayleave.letter_wy_id AS \"LtrWyID\", public.view_authorities.provider_shorthand AS \"Provider\", flw_appl_entries.tags AS \"Tags\"
        FROM public.view_authorities
        LEFT JOIN flw_wayleave ON public.view_authorities.\"wl_id\" = flw_wayleave.id
        LEFT JOIN flw_appl_entries ON public.view_authorities.\"system_id\" = flw_appl_entries.system_id
        LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
        LEFT JOIN status_appl ON public.view_authorities.\"system_id\" = status_appl.system_id
        WHERE public.view_authorities.\"system_id\" = :systemId ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;

    }

    public static function getContacts($id)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT flw_appl_contacts.full_name AS \"ContactName\", flw_appl_contacts.company_name AS \"CompanyName\", flw_appl_contacts.type AS \"Type\", flw_appl_contacts.address_1 AS \"Address1\", flw_appl_contacts.address_2 AS \"Address2\", flw_appl_contacts.postcode AS \"Postcode\", flw_appl_contacts.city AS \"City\", flw_appl_contacts.state AS \"State\" FROM flw_appl_contacts
    WHERE id = :id");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':id', $id);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function getRoad($sid)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $systemId = $sid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT view_authorities.authority_id AS \"AuthorityId\", authority_name AS \"AuthorityName\", road_involved_id AS \"RoadId\", flw_kwc_wy_letters.period_kil AS \"PeriodKil\", flw_kwc_wy_letters.payment_detail AS \"PaymentDetail\", flw_kwc_wy_letters.amount AS \"Amount\", flw_kwc_wy_letters.notes AS \"NotesKwc\" FROM view_authorities LEFT JOIN flw_kwc_wy_letters ON view_authorities.authority_id = flw_kwc_wy_letters.authority_id
    WHERE view_authorities.system_id = :systemId ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    // REVIEW - no reference to this function
    public static function wyFeedbackModel($sid)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $systemId = $sid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT flw_appl_entries.system_id AS \"SysID\", flw_appl_entries.project_title AS \"ProjectTitle\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\"
    FROM flw_appl_entries
    LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
    WHERE flw_appl_entries.system_id = :systemId ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function wyFeedbackModelEdit($sid, $fid)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $systemId = $sid;
        $feedbackId = $fid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT public.view_authorities.reference_no AS \"RefNo\", public.view_authorities.authority_name AS \"Authority\", public.view_authorities.authority_logo AS \"Logo\", public.view_authorities.wl_appl_letter_date AS \"LetterDate\", public.view_authorities.wl_status AS \"WyStatus\", public.view_authorities.wl_id AS \"WyID\", public.view_authorities.system_id AS \"SysID\", public.view_authorities.authority_id AS \"AuthorityID\", road_involved_id AS \"RoadId\", flw_appl_entries.project_title AS \"ProjectTitle\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", status_appl.project_status AS \"Status\", status_appl.id AS \"StatusID\", flw_wayleave.letter_wy_id AS \"LtrWyID\", flw_wayleave_letters.up_provider AS \"UpProvider\", flw_wayleave_letters.up_client AS \"UpClient\", flw_wayleave_letters.client_ref_no AS \"ClientRefNo\", flw_wayleave_letters.staff_name AS \"StaffName\", flw_wayleave_letters.staff_contact AS \"StaffContact\", flw_wayleave_letters.staff_name_2 AS \"StaffName2\", flw_wayleave_letters.staff_contact_2 AS \"StaffContact2\", flw_wayleave_letters.staff_approval AS \"StaffApproval\", flw_wayleave_letters.staff_approval_position AS \"StaffApprovalPosition\", flw_wayleave_letters.inv_no AS \"InvNo\", flw_wayleave_letters.inv_date AS \"InvDate\", flw_wayleave_letters.id AS \"WyFeedbackId\", flw_wayleave_letters.letter_ref_no AS \"LetterRefNo\", flw_wayleave_letters.address_id AS \"AddressID\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
        FROM public.view_authorities
        LEFT JOIN flw_wayleave ON public.view_authorities.\"wl_id\" = flw_wayleave.id
        LEFT JOIN flw_wayleave_letters ON flw_wayleave_letters.id = flw_wayleave.letter_wy_id
        LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
        LEFT JOIN flw_appl_entries ON public.view_authorities.\"system_id\" = flw_appl_entries.system_id
        LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
        LEFT JOIN status_appl ON public.view_authorities.\"system_id\" = status_appl.system_id
        WHERE public.view_authorities.\"system_id\" = :systemId AND flw_wayleave.letter_wy_id = :feedbackId ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);
        $stmt->bindValue(':feedbackId', $feedbackId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function wyFeedbackModelAmend($sid)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $systemId = $sid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT flw_appl_entries.system_id AS \"SysID\", flw_appl_entries.project_title AS \"ProjectTitle\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", flw_wayleave_letters.up_provider AS \"UpProvider\", flw_wayleave_letters.up_client AS \"UpClient\", flw_wayleave_letters.client_ref_no AS \"ClientRefNo\", flw_wayleave_letters.staff_name AS \"StaffName\", flw_wayleave_letters.staff_contact AS \"StaffContact\", flw_wayleave_letters.staff_name_2 AS \"StaffName2\", flw_wayleave_letters.staff_contact_2 AS \"StaffContact2\", flw_wayleave_letters.staff_approval AS \"StaffApproval\", flw_wayleave_letters.staff_approval_position AS \"StaffApprovalPosition\", flw_wayleave_letters.inv_no AS \"InvNo\", flw_wayleave_letters.inv_date AS \"InvDate\", flw_wayleave_letters.id AS \"WyFeedbackId\", flw_wayleave_letters.letter_ref_no AS \"LetterRefNo\", flw_wayleave_letters.letter_date AS \"LetterDate\", status_appl.id AS \"ID\", status_appl.project_status AS \"StatusID\"
    FROM flw_appl_entries
    LEFT JOIN status_appl ON status_appl.system_id = flw_appl_entries.system_id
    LEFT JOIN flw_wayleave_letters ON flw_wayleave_letters.system_id = flw_appl_entries.system_id
    LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
    WHERE flw_appl_entries.system_id = :systemId AND flw_wayleave_letters.status = 4 ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function getRoadName($rid)
    {
        require "config/system.php";

        // Connect to the database using PDO
        try {
            $conn = new PDO($PDO);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error in connection: " . $e->getMessage());
        }

        // value
        $roadid = $rid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT road_name AS \"RoadName\" FROM flw_appl_roads
    WHERE id = :roadid ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':roadid', $roadid);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function assignStaff($type)
    {
        require "config/system.php";

        // Connect to the database

        $conn = General::connectToDatabase();

        if ($type == 1) {
            $filter = '(11,12,41,42,43,44)';
        } else {
            $filter = '(71,72,73)';
        }

        $queryAction = "SELECT
                sys_users.username AS username,
                sys_hr_employee.first_name AS FirstName,
                sys_hr_employee.last_name AS LastName,
                sys_users.profile_pic AS ProfilePic,
                sys_hr_employee.position AS Position,
                sys_hr_employee.phone_no AS StaffPhoneNo
                FROM sys_hr_employee
                LEFT JOIN sys_users ON sys_users.employee_id = sys_hr_employee.id
                WHERE sys_users.role_id IN :filter
                AND sys_users.activation = 'true'
                ORDER BY sys_hr_employee.first_name ASC";

        $stmt = $conn->query($queryAction);
        $stmt->bindParam(':filter', $filter);
        $stmt->execute();

        // Fetch the result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $data;

    }

    public static function convertToHijri($date)
    {
        // REVIEW - fromDateTime() expecting 2 arguments, only first argument provided, temporary pass default 2nd argument which is null refered to it library
        $intlCalendar = IntlCalendar::fromDateTime($date, null);
        $islamicCalendar = IntlCalendar::createInstance('Asia/Kuala_Lumpur', 'ms_MY@calendar=islamic-civil');

        $islamicCalendar->set(
            IntlCalendar::FIELD_HOUR_OF_DAY,
            $intlCalendar->get(IntlCalendar::FIELD_HOUR_OF_DAY)
        );
        $islamicCalendar->set(
            IntlCalendar::FIELD_MINUTE,
            $intlCalendar->get(IntlCalendar::FIELD_MINUTE)
        );

        $hijriYear = $islamicCalendar->get(IntlCalendar::FIELD_YEAR);
        $hijriMonth = $islamicCalendar->get(IntlCalendar::FIELD_MONTH) + 1; // Adjust month by adding 1
        $hijriDay = $islamicCalendar->get(IntlCalendar::FIELD_DAY_OF_MONTH);

        $hijriDate = sprintf('%02d %s %04d H', $hijriDay, Wayleave::getHijriMonthName($hijriMonth), $hijriYear);

        return $hijriDate;
    }

    public static function getHijriMonthName($monthNumber)
    {
        $monthNames = [
            1 => 'Muharram',
            2 => 'Safar',
            3 => 'Rabiulawal',
            4 => 'Rabiulakhir',
            5 => 'Jamadilawal',
            6 => 'Jamadilakhir',
            7 => 'Rejab',
            8 => 'Syaaban',
            9 => 'Ramadhan',
            10 => 'Syawal',
            11 => 'Zulkaedah',
            12 => 'Zulhijjah',
        ];

        return $monthNames[$monthNumber] ?? '';
    }

    public static function getLetterRefNo($sid, $refNo)
    {
        require "config/system.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // value
        $systemId = $sid;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT COUNT(letter_ref_no) AS total FROM flw_wayleave_letters
    WHERE system_id = :systemId ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // Fetch all rows from the result set as an array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // check if reference_no already exists or not
        if ($result == 0) {
            $ref_no = $refNo . "/BF";
        } else if ($result == 1) {
            $log = 1;
            $ref_no = $refNo . "/BF (" . $log . ")";
        } else {
            $log = $result['total'] + 1;
            $ref_no = $refNo . "/BF (" . $log . ")";
        }

        $LtrRefNo = $ref_no;

        // Close the database connection
        $conn = null;

        return $LtrRefNo;
    }

    public static function getGenerateWyFeedback($variables, $type)
    {
        require "config/system.php";
        include "config/tenant.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // value
        $id = $variables;

        if ($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT') {
            if ($type == 1) {
                // FOR VIEW SURAT MAKLUM BALAS
                // Prepare query on the database
                $stmt = $conn->prepare("SELECT flw_wy_letters.system_id AS \"SysID\", flw_wy_letters.letter_ref_no AS \"LtrRefNo\", flw_wy_letters.client_ref_no AS \"ClientRefNo\",flw_wy_letters.provider_client_id AS \"ProviderClientId\", flw_wy_letters.contact_copy_to AS \"CopyId\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.tags AS \"Tags\", flw_wy_letters.small_title AS \"SmallTitle\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", tb_staff_contact_2.first_name AS \"FirstNameContact2\", tb_staff_contact_2.last_name AS \"LastNameContact2\", flw_wy_letters.staff_approval_position AS \"staffApprovePosition\"
                FROM flw_wy_letters
                LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id = flw_wy_letters.system_id
                LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wy_letters.staff_approval
                LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wy_letters.staff_name
                LEFT JOIN sys_users AS staff_name_users_2 ON staff_name_users_2.username = flw_wy_letters.staff_name_2
                LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
                LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
                LEFT JOIN sys_hr_employee AS tb_staff_contact_2 ON tb_staff_contact_2.id=staff_name_users_2.employee_id
                WHERE flw_wy_letters.id = :id ");

                $stmt2 = $conn->prepare("SELECT flw_wayleave_letters.id AS \"WyFeedbackID\", flw_wayleave_letters.system_id AS \"SysID\", flw_wayleave_letters.letter_ref_no AS \"LtrRefNo\", flw_wayleave_letters.client_ref_no AS \"ClientRefNo\", flw_wayleave_letters.up_provider AS \"ProviderUP\", flw_wayleave_letters.up_client AS \"ClientUP\", flw_wayleave_letters.staff_name AS \"staffName\", flw_wayleave_letters.staff_contact AS \"staffContact\", flw_wayleave_letters.staff_approval AS \"staffApproval\", flw_wayleave_letters.staff_approval_position AS \"staffApprovePosition\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", tb_staff_contact_2.first_name AS \"FirstNameContact2\", tb_staff_contact_2.last_name AS \"LastNameContact2\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
            FROM flw_wayleave_letters
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id=flw_wayleave_letters.system_id
            LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wayleave_letters.staff_approval
            LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wayleave_letters.staff_name
            LEFT JOIN sys_users AS staff_name_users_2 ON staff_name_users_2.username = flw_wayleave_letters.staff_name_2
            LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact_2 ON tb_staff_contact_2.id=staff_name_users_2.employee_id
            LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
            LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
            WHERE flw_wayleave_letters.id = :id ");

            } else if ($type == 2) {
                // FOR APPROVAL SURAT MAKLUM BALAS
                // Prepare query on the database
                $stmt = $conn->prepare("SELECT flw_wayleave_letters.id AS \"WyFeedbackID\", flw_wayleave_letters.system_id AS \"SysID\", flw_wayleave_letters.letter_ref_no AS \"LtrRefNo\", flw_wayleave_letters.client_ref_no AS \"ClientRefNo\", flw_wayleave_letters.up_provider AS \"ProviderUP\", flw_wayleave_letters.up_client AS \"ClientUP\", flw_wayleave_letters.staff_name AS \"staffName\", flw_wayleave_letters.staff_contact AS \"staffContact\", flw_wayleave_letters.staff_approval AS \"staffApproval\", flw_wayleave_letters.staff_approval_position AS \"staffApprovePosition\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", tb_staff_contact_2.first_name AS \"FirstNameContact2\", tb_staff_contact_2.last_name AS \"LastNameContact2\", flw_wayleave_letters.letter_date AS \"LetterDate\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
            FROM flw_wayleave_letters
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id=flw_wayleave_letters.system_id
            LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wayleave_letters.staff_approval
            LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wayleave_letters.staff_name
            LEFT JOIN sys_users AS staff_name_users_2 ON staff_name_users_2.username = flw_wayleave_letters.staff_name_2
            LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact_2 ON tb_staff_contact_2.id=staff_name_users_2.employee_id
            LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
            LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
            WHERE flw_wayleave_letters.system_id = :id AND flw_wayleave_letters.status = 2 ");

            } else {
                // FOR AMEND SURAT MAKLUM BALAS
                // Prepare query on the database
                $stmt = $conn->prepare("SELECT flw_wayleave_letters.id AS \"WyFeedbackID\", flw_wayleave_letters.system_id AS \"SysID\", flw_wayleave_letters.letter_ref_no AS \"LtrRefNo\", flw_wayleave_letters.client_ref_no AS \"ClientRefNo\", flw_wayleave_letters.up_provider AS \"ProviderUP\", flw_wayleave_letters.up_client AS \"ClientUP\", flw_wayleave_letters.staff_name AS \"staffName\", flw_wayleave_letters.staff_contact AS \"staffContact\", flw_wayleave_letters.staff_approval AS \"staffApproval\", flw_wayleave_letters.staff_approval_position AS \"staffApprovePosition\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", tb_staff_contact_2.first_name AS \"FirstNameContact2\", tb_staff_contact_2.last_name AS \"LastNameContact2\", flw_wayleave_letters.letter_date AS \"LetterDate\", flw_wayleave_letters.notes AS \"Notes\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
            FROM flw_wayleave_letters
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id=flw_wayleave_letters.system_id
            LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wayleave_letters.staff_approval
            LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wayleave_letters.staff_name
            LEFT JOIN sys_users AS staff_name_users_2 ON staff_name_users_2.username = flw_wayleave_letters.staff_name_2
            LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact_2 ON tb_staff_contact_2.id=staff_name_users_2.employee_id
            LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
            LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
            WHERE flw_wayleave_letters.system_id = :id AND flw_wayleave_letters.status = 4 ");

            }

        } else if ($appsTitle == 'KITER' || $appsTitle == 'KUK') {
            if ($type == 1) {
                // FOR VIEW SURAT MAKLUM BALAS
                // Prepare query on the database
                $stmt = $conn->prepare("SELECT flw_wayleave_letters.id AS \"WyFeedbackID\", flw_wayleave_letters.system_id AS \"SysID\", flw_wayleave_letters.letter_ref_no AS \"LtrRefNo\", flw_wayleave_letters.up_provider AS \"ProviderUP\", flw_wayleave_letters.up_client AS \"ClientUP\", flw_wayleave_letters.inv_no AS \"InvNo\", flw_wayleave_letters.inv_date AS \"InvDate\", flw_wayleave_letters.staff_name AS \"staffName\", flw_wayleave_letters.staff_contact AS \"staffContact\", flw_wayleave_letters.staff_approval_position AS \"staffApprovePosition\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
            FROM flw_wayleave_letters
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id=flw_wayleave_letters.system_id
            LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wayleave_letters.staff_approval
            LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wayleave_letters.staff_name
            LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
            LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
            LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
            WHERE flw_wayleave_letters.id = :id ");

            } else if ($type == 2) {
                // FOR APPROVAL SURAT MAKLUM BALAS
                // Prepare query on the database
                $stmt = $conn->prepare("SELECT flw_wayleave_letters.id AS \"WyFeedbackID\", flw_wayleave_letters.system_id AS \"SysID\", flw_wayleave_letters.letter_ref_no AS \"LtrRefNo\", flw_wayleave_letters.up_provider AS \"ProviderUP\", flw_wayleave_letters.up_client AS \"ClientUP\", flw_wayleave_letters.inv_no AS \"InvNo\", flw_wayleave_letters.inv_date AS \"InvDate\", flw_wayleave_letters.staff_name AS \"staffName\", flw_wayleave_letters.staff_contact AS \"staffContact\", flw_wayleave_letters.staff_approval_position AS \"staffApprovePosition\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", flw_wayleave_letters.letter_date AS \"LetterDate\", flw_wayleave_letters.letter_hijri_date AS \"LetterHijriDate\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
            FROM flw_wayleave_letters
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id=flw_wayleave_letters.system_id
            LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wayleave_letters.staff_approval
            LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wayleave_letters.staff_name
            LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
            LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
            LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
            WHERE flw_wayleave_letters.system_id = :id AND flw_wayleave_letters.status = 2 ");

            } else {
                // FOR AMEND SURAT MAKLUM BALAS
                // Prepare query on the database
                $stmt = $conn->prepare("SELECT flw_wayleave_letters.id AS \"WyFeedbackID\", flw_wayleave_letters.system_id AS \"SysID\", flw_wayleave_letters.letter_ref_no AS \"LtrRefNo\", flw_wayleave_letters.up_provider AS \"ProviderUP\", flw_wayleave_letters.up_client AS \"ClientUP\", flw_wayleave_letters.inv_no AS \"InvNo\", flw_wayleave_letters.inv_date AS \"InvDate\", flw_wayleave_letters.staff_name AS \"staffName\", flw_wayleave_letters.staff_contact AS \"staffContact\", flw_wayleave_letters.staff_approval_position AS \"staffApprovePosition\", flw_appl_entries.project_title AS \"Title\", flw_appl_entries.reference_no AS \"RefNo\", flw_appl_entries.contact_id AS \"ContactId\", ls_provider.name AS \"ProviderName\", tb_staff_approval.first_name AS \"FirstNameApproval\", tb_staff_approval.last_name AS \"LastNameApproval\", tb_staff_contact.first_name AS \"FirstNameContact\", tb_staff_contact.last_name AS \"LastNameContact\", flw_wayleave_letters.letter_date AS \"LetterDate\", flw_wayleave_letters.letter_hijri_date AS \"LetterHijriDate\", flw_wayleave_letters.notes AS \"Notes\", flw_address_wy_letters.addr_provider_1 AS \"AddrProvider1\", flw_address_wy_letters.addr_provider_2 AS \"AddrProvider2\", flw_address_wy_letters.addr_provider_3 AS \"AddrProvider3\", flw_address_wy_letters.addr_client_1 AS \"AddrClient1\", flw_address_wy_letters.addr_client_2 AS \"AddrClient2\", flw_address_wy_letters.addr_client_3 AS \"AddrClient3\"
            FROM flw_wayleave_letters
            LEFT JOIN flw_appl_entries ON flw_appl_entries.system_id=flw_wayleave_letters.system_id
            LEFT JOIN sys_users AS staff_approval_users ON staff_approval_users.username = flw_wayleave_letters.staff_approval
            LEFT JOIN sys_users AS staff_name_users ON staff_name_users.username = flw_wayleave_letters.staff_name
            LEFT JOIN sys_hr_employee AS tb_staff_approval ON tb_staff_approval.id=staff_approval_users.employee_id
            LEFT JOIN sys_hr_employee AS tb_staff_contact ON tb_staff_contact.id=staff_name_users.employee_id
            LEFT JOIN ls_provider ON ls_provider.id = flw_appl_entries.utility_provider
            LEFT JOIN flw_address_wy_letters ON flw_wayleave_letters.address_id = flw_address_wy_letters.id
            WHERE flw_wayleave_letters.system_id = :id AND flw_wayleave_letters.status = 4 ");

            }

        }

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':id', $id);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function getAuthority($sid)
    {
        require "config/system.php";
        include "config/tenant.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        if ($appsTitle == 'UCIDOS' || $appsTitle == 'KUDRAT') {
            $stmt = $conn->prepare("SELECT authority AS \"Authority\", ls_authorities.name AS \"AuthorityName\", ls_authorities.fullname AS \"AuthorityFullName\", ls_authorities.id AS \"AuthorityId\"
        FROM flw_wayleave
        LEFT JOIN ls_authorities ON ls_authorities.id = flw_wayleave.authority
        WHERE system_id = :systemid ");
        } else if ($appsTitle == 'KITER') {
            $stmt = $conn->prepare("SELECT authority AS \"Authority\", ls_authorities.name AS \"AuthorityName\", ls_authorities.id AS \"AuthorityId\"
        FROM flw_wayleave
        LEFT JOIN ls_authorities ON ls_authorities.id = flw_wayleave.authority
        WHERE system_id = :systemid ");
        }

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemid', $sid);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function getAttachment($sid, $type)
    {
        require "config/system.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT url AS \"AttachUrl\" FROM flw_appl_attachments WHERE system_id = :systemid AND attachment_type = :type ");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemid', $sid);
        $stmt->bindValue(':type', $type);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function integerToRoman($num)
    {
        $romanSymbols = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'iv' => 4,
            'i' => 1
        );

        $romanValue = '';

        foreach ($romanSymbols as $symbol => $value) {
            while ($num >= $value) {
                $romanValue .= $symbol;
                $num -= $value;
            }
        }

        return $romanValue;
    }

    public static function countWy($sid, $type)
    {
        require "config/system.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // value
        $systemId = $sid;

        // get total wy
        if ($type = '1') {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM public.view_authorities
                    WHERE public.view_authorities.\"system_id\" = :systemId ");

        } else {
            //total wy = 3
            $stmt = $conn->prepare("SELECT COUNT(*) FROM public.view_authorities
                    WHERE public.view_authorities.\"system_id\" = :systemId AND public.view_authorities.\"wl_status\" = '3' ");

        }

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;

    }

}