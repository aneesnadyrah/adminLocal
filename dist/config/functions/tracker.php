<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'config/components.php';

Class Tracker {
    private $dbFactory;

    private $Table;

    private $Widget;

    private $Card;

    public function __construct() {
        $this->dbFactory = new DBConnectionFactory();
        $this->Table = new Table();
        $this->Widget = new Widget();
        $this->Card = new Card();
    }

    private function getTableData() {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT tasks.provider_id, tasks.districts, tasks.roads, tasks.created_date, tasks.reference_no, tasks.system_id, tasks.application_length, tasks.status_color, tasks.status, entries.link_id, entries.site_start, entries.site_end, tasks.length_code, tasks.districts, tasks.status, tasks.project_title, tasks.submitted_date, tasks.application_date, wayleaves.dt_auth_ltr_created AS wayleave_dt_auth_ltr_created, wayleaves.dt_auth_ltr_send AS wayleaves_dt_auth_ltr_send, wayleaves.dt_appv_ltr, wayleaves.dt_appv_ltr_received AS wayleaves_dt_appv_ltr_received, wayleaves.dt_fb_ltr_send, permits.dt_auth_ltr_created AS permit_dt_auth_ltr_created, permits.dt_auth_ltr_send AS permit_dt_auth_ltr_send, permits.dt_appv_ltr_created AS permit_dt_appv_ltr_created, permits.dt_appv_received, permits.dt_wp_recog_created, permits.no_wp_recog, permits.dt_wday_strt, permits.dt_wday_end, permits.dt_wend_strt, permits.dt_wend_end, notices.dt_ws_ltr_created, notices.dt_ws_ltr_received, notices.dt_wf_ltr_created, notices.dt_wf_ltr_received, finishes.dt_wfal_submitted, finishes.dt_wfal_checked, finishes.dt_auth_ltr_created AS finish_dt_auth_ltr_created, finishes.dt_auth_ltr_send AS finish_dt_auth_ltr_send, finishes.dt_appv_ltr_created AS finish_dt_appv_ltr_created, finishes.dt_appv_ltr_received AS finish_dt_appv_ltr_received, finishes.dt_wf_recog_created, finishes.no_wf_recog, finishes.dt_dlp_start, finishes.dt_dlp_end,defects.dt_cmgd_auth_ltr_created, defects.dt_cmgd_auth_send, defects.dt_cmgd_appv_ltr_created, defects.dt_cmgd_appv_ltr_received, defects.dt_ccc_recog_created, defects.no_ccc_recog, reports.sv_date_done
        FROM public.view_tasks tasks
        LEFT JOIN flw_appl_entries entries ON entries.system_id = tasks.system_id
        LEFT JOIN flw_wayleave wayleaves ON wayleaves.system_id = tasks.system_id
        LEFT JOIN flw_work_permit permits ON permits.system_id = tasks.system_id
        LEFT JOIN flw_work_notice notices ON notices.system_id = tasks.system_id
        LEFT JOIN flw_work_finish finishes ON finishes.system_id = tasks.system_id
        LEFT JOIN flw_work_defects defects ON defects.system_id = tasks.system_id
        LEFT JOIN flw_deposit_returns kwc ON kwc.system_id = tasks.system_id
        LEFT JOIN flw_appl_reports reports ON reports.system_id = tasks.system_id
        -- WHERE tasks.status_id = 2
        ORDER BY created_date ASC " ;
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    private function getTableDataV3()
    {
        $db = $this->dbFactory->createConnection();

        // Fetch data from view_tracker_general
        $query = "SELECT * FROM view_tracker_general";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $general = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Fetch data from view_tracker_authorities
        $query2 = "SELECT * FROM view_tracker_authorities";
        $stmt2 = $db->prepare($query2);
        $stmt2->execute();
        $authority = $stmt2->fetchAll(PDO::FETCH_OBJ);

        // Create a map of authorities by system_id
        $authorityMap = [];
        foreach ($authority as $auth) {
            $systemId = $auth->auth_system_id;
            if (!isset($authorityMap[$systemId])) {
                $authorityMap[$systemId] = [];
            }
            // Include all attributes in the authority array
            $authorityMap[$systemId][] = $auth;
        }

        // Merge the authorities into the general array
        foreach ($general as &$gen) {
            $systemId = $gen->system_id;
            $gen->authority = isset($authorityMap[$systemId]) ? $authorityMap[$systemId] : [];
        }

        // Return the combined result as an array of standard objects
        return $general;
    }

    private function getCountTask($filter = NULL) {
        $db = $this->dbFactory->createConnection();

        switch($filter) {
            case 'application':
                $query = "SELECT COUNT(tasks.status_id) AS count_task FROM view_tasks tasks";
            break;
            case 'length':
                $query = "SELECT TO_CHAR(SUM(tasks.application_length), 'FM999,999,999') AS count_task FROM view_tasks tasks";
            break;
            case 'deposit':
                $query = "SELECT COUNT(tasks.application_length) AS count_task FROM view_tasks tasks";
            break;
        }
        $stmt = $db->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $result->count_task;
    }

    private function smallTaskOverview() {

        $tasks = new stdClass();

        $tasks = [
            (object) [
                'count' => $this->getCountTask('application'),
                'title' => 'Jumlah Permohonan',
                'icon' => 'briefcase'
            ],
            (object) [
                'count' => $this->getCountTask('length') .' meter',
                'title' => 'Jumlah Jarak',
                'icon' => 'pen-ruler'
            ],
            (object) [
                // 'count' => 'RM '.$this->getCountTask('deposit'),
                'count' => 'RM 0',
                'title' => 'Jumlah Wang Cagaran',
                'icon' => 'money-check-dollar-pen'
            ]
        ];

        return $tasks;

    }

    private function getTrackerNewData() {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT tasks.reference_no, tasks.system_id, tasks.application_length, verifies.gis_length, tasks.districts, tasks.provider_id, tasks.status_id, tasks.flow_action,  tasks.status, tasks.status_color, tasks.status_icon, tasks.flow_department, tasks.project_title, tasks.approved_date, tasks.submitted_date, tasks.application_date, tasks.created_date, status.authority, ls_authorities.sort_name, ls_authorities.logo, survey.current_progress, ctrlauthorities.wayleave_id, wayleave.dt_appv_ltr
        FROM public.view_tasks tasks
        LEFT JOIN flw_appl_verifies verifies ON tasks.system_id = verifies.system_id
        LEFT JOIN ctrl_statuses status ON status.system_id = tasks.system_id AND status.status_id = tasks.status_id AND status.authority !=0
        LEFT JOIN ls_authorities ON ls_authorities.id = status.authority
        LEFT JOIN flw_survey_reports_udm survey ON tasks.system_id = survey.system_id
            AND survey.created_timestamp = (
                SELECT MAX(created_timestamp)
                FROM flw_survey_reports_udm
                WHERE system_id = tasks.system_id
            )
        LEFT JOIN ctrl_authorities ctrlauthorities ON tasks.system_id = ctrlauthorities.system_id AND status.authority = ctrlauthorities.authority_id
		LEFT JOIN flw_wayleave wayleave ON wayleave.id = ANY(ctrlauthorities.wayleave_id)
        -- WHERE tasks.status_id = 30
        ORDER BY created_date ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    private function getAttachments($sysid) {
        $db = $this->dbFactory->createConnection();

        $query = "SELECT attachments.name, attachments.size, attachments.created_date, attachments.url, attachments.mime_type, list_attach.details
        FROM public.flw_appl_attachments attachments
        LEFT JOIN public.ls_attachments list_attach ON attachments.attachment_type = list_attach.id
        WHERE attachments.system_id = :systemid
        ORDER BY created_date ASC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':systemid', $sysid);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    public function __call($method, $args) {
        $section = $args[0];

        if($method === 'table') {
            $getData = $this->getTableData();
            return $this->Table->get($section, $getData);
        }
        
        if ($method === 'tableV3') {
            $getData = $this->getTableDataV3();
            return $this->Table->get($section, $getData);
        }

        if($method === 'widget') {
            $getData = $this->smallTaskOverview();
            return $this->Widget->get($section, $getData);
        }

        if($method === 'trackerNew') {
            $getData = $this->getTrackerNewData();
            return $this->Table->get($section, $getData);
        }

        if($method === 'attachment') {
            $sysid = $args[0];
            $getData = $this->getAttachments($sysid);
            return $this->Card->get('tracker-attachment', $getData);
        }

    }

}