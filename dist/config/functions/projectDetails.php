<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'config/components.php';

class ProjectDetails
{
    protected $db;
    private $conn;
    private $Card;

    public function __construct()
    {
        $this->db = new DBConnectionFactory();
        $this->conn = $this->db->createConnection();
        $this->Card = new Card();
    }

    // =====================================
    // PROJECT DETAILS METHODS
    // =====================================

    /**
     * Get project details - COMPLETE VERSION
     */
    public function getProjectDetails($sysid)
    {
        $query = "SELECT * FROM view_project_details WHERE system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // =====================================
    // CONTACT DETAILS METHODS
    // =====================================

    /**
     * Get contact details - COMPLETE VERSION
     */
    public function getContactDetails($sysid)
{
    $db = General::connectToDatabase();
    
    $query = "SELECT 
        entries.system_id,
        entries.old_system_id,
        entries.contact_id,
        contact_info.id,
        contact_info.full_name,
        contact_info.email,
        contact_info.phone_no,
        contact_info.company_name,
        contact_info.position,
        contact_info.type,
        contact_info.address_1,
        contact_info.address_2,
        contact_info.postcode,
        contact_info.city,
        contact_info.state,
        contact_type.name AS name_contact_type
    FROM flw_appl_entries entries
    LEFT JOIN flw_appl_contacts contact_info ON (
        -- V1: old_system_id matching
        (entries.old_system_id IS NOT NULL AND entries.old_system_id = contact_info.old_system_id)
        OR 
        -- V1: fallback to system_id
        (entries.old_system_id IS NULL AND entries.system_id = contact_info.system_id)
        OR
        -- Original: array-based matching
        (entries.contact_id IS NOT NULL AND contact_info.id::text = ANY(entries.contact_id::text[]))
    )
    LEFT JOIN ls_type_contacts contact_type ON contact_info.type = contact_type.id
    WHERE entries.system_id = :systemid AND contact_info.type IS NOT NULL
      AND contact_info.id IS NOT NULL";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

    /**
     * Get contact GIS details 
     */
    public function getGISDetails($sysid)
    {

        $query = "SELECT verifies.system_id, verifies.gis_assignee, verifies.pil_submitted_by, verifies.pkd_assignee, users.username, users.profile_pic, hr.first_name, 
        hr.position, hr.phone_no, hr.email, hr.first_address, hr.second_address, hr.postcode
        FROM flw_appl_verifies verifies
        LEFT JOIN sys_users users ON verifies.gis_assignee = users.username
        LEFT JOIN sys_hr_employee hr ON users.employee_id = hr.id
        WHERE system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get contact PKD details
     */
    public function getContactPKDDetails($sysid)
    {

        $query = "SELECT report.system_id, report.site_officer, hr.first_name, hr.position, 
                    hr.phone_no, hr.email, hr.first_address, hr.second_address, hr.postcode
                FROM ctrl_site_report report
                LEFT JOIN sys_users users ON report.site_officer = users.username
                LEFT JOIN sys_hr_employee hr ON users.employee_id = hr.id
                WHERE report.system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get contact survey details
     */
    public function getContactSurveyDetails($sysid)
    {

        $query = "SELECT report.system_id, report.created_id, hr.first_name, hr.position, 
                    hr.phone_no, hr.email, hr.first_address, hr.second_address, hr.postcode
                FROM flw_survey_reports_udm report
                LEFT JOIN sys_users users ON report.created_id = users.username
                LEFT JOIN sys_hr_employee hr ON users.employee_id = hr.id
                WHERE report.system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // =====================================
    // ROAD DETAILS METHODS
    // =====================================

    /**
     * Get road details - COMPLETE VERSION
     */
    public function getRoadDetails($sysid)
    {
        $query = "SELECT 
            id, 
            road_name, 
            system_id, 
            road_length, 
            CONCAT(SUBSTRING(latitude_start::text, 1, 10), ', ', SUBSTRING(longitude_start::text, 1, 12)) AS coor_start, 
            CONCAT(SUBSTRING(latitude_end::text, 1, 10), ', ', SUBSTRING(longitude_end::text, 1, 12)) AS coor_end,
            (SELECT string_agg(DISTINCT method.method, ',') 
            FROM ls_work_methods method 
            WHERE method.id::text = ANY(flw_appl_roads.method::text[])) AS methods,
            (SELECT string_agg(DISTINCT method.name, ',') 
            FROM ls_work_methods method 
            WHERE method.id::text = ANY(flw_appl_roads.method::text[])) AS methods_shortform
        FROM flw_appl_roads 
        WHERE system_id = :systemid 
        AND active = true
        ORDER BY road_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // =====================================
    // LTA DETAILS METHODS
    // =====================================

    /**
     * Get LTA details - COMPLETE VERSION
     */
    public function getLTADetails($sysid)
    {

        $query = "SELECT calendar.title, calendar.start, calendar.end, calendar.description, calendar.location, 
                    lsAuthority.sort_name, lsAuthority.logo, employee.first_name
                FROM flw_calendars calendar 
                LEFT JOIN ls_authorities lsAuthority ON calendar.authority_id = lsAuthority.id
                LEFT JOIN sys_users users ON calendar.creator = users.username
                LEFT JOIN sys_hr_employee employee ON users.employee_id = employee.id
                WHERE calendar.system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // =====================================
    // AUTHORITY DETAILS METHODS  
    // =====================================

    /**
     * Get authority details - VERSION SPECIFIC
     */
    public function getAuthorityDetails($sysid)
    {
           $query = "SELECT authority.system_id, authority.old_system_id, lsAuthority.id AS authority_id, lsAuthority.logo AS authority_logo, 
                    lsAuthority.name AS authority_name, entries.created_at AS application_date, entries.submitted_at, permit.dt_wpal_submitted AS permit_dt_wpal_submitted, 
                    permit.dt_wpal_checked AS permit_dt_wpal_checked, permit.dt_appl_ltr_created AS permit_dt_appl_ltr_created, permit.dt_appl_ltr_recieved AS permit_dt_appl_ltr_recieved, 
                    permit.dt_auth_ltr_created AS permit_dt_auth_ltr_created, permit.dt_auth_ltr_send AS permit_dt_auth_ltr_send, permit.dt_appv_ltr_created AS permit_dt_appv_ltr_created, 
                    permit.dt_appv_received AS permit_dt_appv_received, permit.dt_wp_recog_created AS permit_dt_wp_recog_created, permit.no_wp_recog AS permit_no_wp_recog, 
                    permit.dt_wp_recog_notify AS permit_dt_wp_recog_notify, permit.dt_wp_recog_submit AS permit_dt_wp_recog_submit, permit.wp_recog_received_by AS permit_wp_recog_received_by, 
                    permit.dt_wday_strt, permit.dt_wday_end, permit.dt_wend_strt, permit.dt_wend_end, notice.dt_ws_ltr_created AS notice_dt_ws_ltr_created, notice.dt_ws_ltr_received AS notice_dt_ws_ltr_received, 
                    notice.dt_ws AS notice_dt_ws, notice.dt_wf AS notice_dt_wf, notice.dt_wf_ltr_created AS notice_dt_wf_ltr_created, notice.dt_wf_ltr_received AS notice_dt_wf_ltr_received, 
                    notice.dt_ws_auth_ltr_send AS notice_dt_ws_auth_ltr_send, cpc.dt_appl_ltr_created AS cpc_dt_appl_ltr_created, cpc.dt_appl_ltr_received AS cpc_dt_appl_ltr_received, 
                    cpc.dt_auth_ltr_created AS cpc_dt_auth_ltr_created, cpc.dt_auth_ltr_send AS cpc_dt_auth_ltr_send, cpc.dt_dlp_start, cpc.dt_dlp_end, 
              cpc.dt_appv_ltr_created AS cpc_dt_appv_ltr_created, cpc.dt_appv_ltr_received AS cpc_dt_appv_ltr_received, cpc.dt_wf_recog_created AS cpc_dt_wf_recog_created, 
                    cpc.no_wf_recog, cpc.dt_wf_recog_notify, ccc.dt_cmgd_appl_ltr_created, ccc.dt_cmgd_appl_ltr_received, ccc.dt_cmgd_auth_ltr_created, ccc.dt_cmgd_auth_send, 
                    ccc.dt_cmgd_appv_ltr_created, ccc.dt_cmgd_appv_ltr_received, ccc.dt_ccc_auth_ltr_created, ccc.dt_ccc_auth_send, ccc.dt_ccc_appv_ltr_created, ccc.dt_ccc_appv_received, 
                    ccc.no_ccc_recog, ccc.dt_ccc_recog_notify, ccc.dt_ccc_recog_created, wc.dt_auth_ltr_created AS wc_dt_auth_ltr_created, wc.dt_auth_ltr_send AS wc_dt_auth_ltr_send, 
                    wc.dt_appv_ltr_created AS wc_dt_appv_ltr_created, wc.dt_appv_ltr_received AS wc_dt_appv_ltr_received, wc.no_voucher AS wc_no_voucher, wc.amount AS wc_amount, 
                    sv.calendar_sv_date, sv.actual_sv_date, wy.dt_auth_ltr_created AS wy_dt_auth_ltr_created, wy.dt_auth_ltr_send AS wy_dt_auth_ltr_send, wy.dt_appv_ltr AS wy_dt_appv_ltr, 
                    wy.dt_appv_ltr_received AS wy_dt_appv_ltr_received, wy.dt_fb_ltr_created AS wy_dt_fb_ltr_created, wy.dt_fb_ltr_send AS wy_dt_fb_ltr_send
                FROM ctrl_authorities authority
                LEFT JOIN ls_authorities lsAuthority ON authority.authority_id = lsAuthority.id
                LEFT JOIN flw_appl_entries entries ON authority.system_id = entries.system_id
                LEFT JOIN flw_work_permit permit ON permit.id = ANY(ARRAY[authority.work_permit_id]::integer[])
                LEFT JOIN flw_work_notice notice ON authority.work_notice_id = notice.id
                LEFT JOIN flw_work_finish cpc ON authority.work_finish_id = cpc.id
                LEFT JOIN flw_work_defects ccc ON authority.work_defects_id = ccc.id
                LEFT JOIN flw_deposit_returns wc ON authority.deposit_returns_id = wc.id
                LEFT JOIN view_report_sitevisit sv ON authority.system_id = sv.system_id AND authority.authority_id = sv.authority_id
                LEFT JOIN flw_wayleave wy ON wy.id = ANY(ARRAY[authority.wayleave_id]::integer[])
                WHERE authority.system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // =====================================
    // ATTACHMENT METHODS
    // =====================================

    /**
     * Get attachments - COMPLETE VERSION
     */
public function getAttachments($sysid)
{
    $query = "SELECT 
        attachments.id AS attach_id, 
        attachments.system_id, 
        attachments.name, 
        attachments.url, 
        attachments.attachment_date, 
        attachments.attachment_type, 
        attachments.size, 
        attachments.mime_type, 
        attachments.user_added, 
        attachments.authority, 
        attachments.chronology_id, 
        attachments.version,
        COALESCE(attachments.old_system_id, NULL) AS old_system_id, 
        COALESCE(attachments.old_wl_id, NULL) AS old_wl_id, 
        COALESCE(attachments.old_sv_id, NULL) AS old_sv_id,
        ls_attach.details, 
        ls_attach.code_name, 
        ls_attach.status_id,
        sys_hr_employee.first_name AS user_first_name, 
        sys_hr_employee.last_name AS user_last_name,
        -- Use COALESCE for potentially missing fields
        COALESCE(entries.submitted_at, NULL) AS submitted_at, 
        COALESCE(entries.reference_no, NULL) AS reference_no, 
        CASE 
            WHEN entries.submitted_at IS NOT NULL THEN EXTRACT(year FROM entries.submitted_at)
            ELSE NULL 
        END AS year,
        CASE
            WHEN ls_attach.status_id IN (4,6,8,14,24,16,27,28,36,35,33,34,1,9,2,31,30) THEN 'izin lalu'
            WHEN ls_attach.status_id IN (72,70,71,73,74,92,94,95,93,96) THEN 'permit'
            WHEN ls_attach.status_id IN (79,80,81) THEN 'notis'
            WHEN ls_attach.status_id IN (88,120,121,122,123,125) THEN 'cpc'
            WHEN ls_attach.status_id IN (134,136,137,138,139,140,141,142,143) THEN 'ccc'
            WHEN ls_attach.status_id IN (144,146,147,148) THEN 'wc'
            WHEN ls_attach.status_id IN (46,55) THEN 'ukur'
            WHEN ls_attach.status_id IN (102,109) THEN 'pelan'
            ELSE 'unknown'
        END AS phase
    FROM flw_appl_attachments attachments
    LEFT JOIN ls_attachments ls_attach ON attachments.attachment_type = ls_attach.id
    LEFT JOIN sys_users ON attachments.user_added = sys_users.username
    LEFT JOIN sys_hr_employee ON sys_users.employee_id = sys_hr_employee.id
    LEFT JOIN flw_appl_entries entries ON attachments.system_id = entries.system_id
    WHERE attachments.system_id = :systemid
    ORDER BY attachments.attachment_date DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

    // =====================================
    // CHRONOLOGY METHODS
    // =====================================

    /**
     * Get chronology details - COMPLETE VERSION
     */
    public function getChronologyDetails($sysid)
{
    $query = "SELECT 
        chronology.system_id, 
        chronology.old_system_id, 
        chronology.type, 
        chronology.username, 
        chronology.created_at, 
        chronology.authority_id, 
        chronology.status_id, 
        chronology.department, 
        status.id AS status_id, 
        status.flow_name, 
        status.icon, 
        notes.details AS note_details, 
        attach.name as attach_name, 
        attach.url, 
        attach.mime_type, 
        attach.attachment_type,
        attach.size, 
        attach.chronology_id AS chronology_id,
        sys_hr_employee.first_name AS user_name
    FROM ctrl_chronologies chronology
    LEFT JOIN ls_statuses status ON chronology.status_id = status.id 
    LEFT JOIN flw_appl_notes notes ON chronology.id = notes.chronology_id
    LEFT JOIN flw_appl_attachments attach ON chronology.id = attach.chronology_id
    LEFT JOIN sys_users ON chronology.username = sys_users.username
    LEFT JOIN sys_hr_employee ON sys_users.employee_id = sys_hr_employee.id
    WHERE chronology.system_id = :systemid AND status_id != 0 
    ORDER BY chronology.created_at DESC";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

    /**
     * Get chronology auth details
     */
    public function getChronologyAuthDetails($sysid, $authorityid)
    {

        $query = "SELECT chronology.system_id, chronology.type, chronology.username, chronology.created_at, 
                    chronology.authority_id, chronology.status_id, chronology.department, status.id AS status_id, 
                    status.flow_name, status.icon, notes.details AS note_details, attach.name as attach_name, 
                    attach.url, attach.mime_type, attach.size, attach.attachment_type, attach.chronology_id AS chronology_id, 
                    users.name AS user_name
                FROM ctrl_chronologies chronology
                LEFT JOIN ls_statuses status ON chronology.status_id = status.id 
                LEFT JOIN flw_appl_notes notes ON chronology.id = notes.chronology_id
                LEFT JOIN flw_appl_attachments attach ON chronology.id = attach.chronology_id
                LEFT JOIN view_users users ON chronology.username = users.username
                WHERE chronology.system_id = :systemid AND status_id != 0 AND chronology.authority_id = :authorityid
                ORDER BY chronology.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->bindParam(':authorityid', $authorityid, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get authority chronology
     */
    public function getAuthorityChronology($sysid)
    {

        $query = "SELECT authority.system_id, lsAuthority.id AS authority_id, lsAuthority.sort_name
                FROM ctrl_authorities authority
                LEFT JOIN ls_authorities lsAuthority ON authority.authority_id = lsAuthority.id
                WHERE authority.system_id = :systemid";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // =====================================
    // WORKFLOW METHODS
    // =====================================

    /**
     * Get workflow details
     */
    public function getWorkflowDetails($sysid, $phase)
    {

        $query = "SELECT ls_attach.id, ls_attach.details, ls_attach.flow_phase, flw_attach.system_id, 
                    flw_attach.name, flw_attach.url, flw_attach.attachment_date, flw_attach.attachment_type, 
                    flw_attach.user_added, flw_attach.mime_type, flw_attach.size,
                    CASE 
                        WHEN flw_attach.attachment_type IS NOT NULL THEN 'Selesai'
                        ELSE 'Belum Selesai'
                    END AS badge_status
                FROM ls_attachments ls_attach
                LEFT JOIN flw_appl_attachments flw_attach ON ls_attach.id = flw_attach.attachment_type 
                    AND flw_attach.system_id = :systemid
                WHERE ls_attach.flow_phase = :phase
                ORDER BY ls_attach.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':systemid', $sysid, PDO::PARAM_STR);
        $stmt->bindParam(':phase', $phase, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // =====================================
    // UTILITY METHODS
    // =====================================

    /**
     * Get reference number - BOTH VERSIONS
     */
    public function getReferenceNo($systemId)
    {
        $sql = "SELECT reference_no FROM flw_appl_entries WHERE system_id = :systemId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        $stmt->execute();
        $referenceNo = $stmt->fetchColumn();
        return $referenceNo;
    }

    /**
     * Get status with security validation - ENHANCED VERSION
     */
    public function getStatus($dbTable, $department, $systemId)
    {
        // Always use V1 security approach
        $allowedTables = ['ctrl_statuses', 'flw_statuses', 'sys_statuses'];
        if (!in_array($dbTable, $allowedTables)) {
            return false;
        }

        $sql = "SELECT status_id FROM {$dbTable} WHERE department = :department AND system_id = :systemId LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':department', $department, PDO::PARAM_STR);
        $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result;
    }

    /**
     * Get status name - ORIGINAL ONLY
     */
    public function getStatusName($statusId)
    {

        $sql = "SELECT flow_name FROM ls_statuses WHERE id = :statusId";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':statusId', $statusId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        $conn = null;
        return $result;
    }

    // =====================================
    // MAGIC METHOD - AUTO DETECTION
    // =====================================

    /**
     * Magic method - automatically detects version and uses appropriate widget names
     */
    public function __call($method, $args)
    {
        $cardPrefix = 'detail-' . $method;

        switch ($method) {
            case 'details':
                return $this->Card->get('detail-projects');

            case 'attachments':
                return $this->Card->get($cardPrefix);

            case 'chronology':
                return $this->Card->get($cardPrefix);

            case 'workflow':
                return $this->Card->get($cardPrefix);

            case 'applications':
                return $this->Card->get($cardPrefix);

            case 'maps':
                return $this->Card->get($cardPrefix);

            default:
                throw new BadMethodCallException("Method {$method} not found");
        }
    }


    // =====================================
    // DEBUG METHODS
    // =====================================

    /**
     * List all available methods
     */
    public function getAvailableMethods()
    {
        return [
            'main_methods' => ['details', 'attachments', 'chronology', 'workflow', 'applications', 'maps'],
            'project_details' => ['getProjectDetails'],
            'contact_methods' => ['getContactDetails', 'getContactDetails2', 'getContactPKDDetails', 'getContactSurveyDetails'],
            'road_methods' => ['getRoadDetails'],
            'authority_methods' => ['getAuthorityDetails', 'getLTADetails'],
            'chronology_methods' => ['getChronologyDetails', 'getChronologyAuthDetails', 'getAuthorityChronology'],
            'workflow_methods' => ['getWorkflowDetails'],
            'utility_methods' => ['getReferenceNo', 'getStatus', 'getStatusName'],
            'debug_methods' => ['getAvailableMethods']
        ];
    }
}
