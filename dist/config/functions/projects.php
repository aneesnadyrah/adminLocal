<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';

Class Project {
    private $dbFactory;
    private $Role;
    private $User;
    private $Table;
    private $Modal;

    public function __construct($username) {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->Table = new Table();
        $this->Modal = new Modal();
        $this->User = $username ?? $_SESSION['username'];
    }

    private function queryTasks($systemId) {
        $db = $this->dbFactory->createConnection();
        $department = $this->Role->{$this->User}->department;
        if ($department == 'management') {
            $department = $this->Role->{$this->User}->sub_department;
        } else {
            $department = $this->Role->{$this->User}->department;
        }
        $statusId = $this->Role->getAssignment($this->User);

        $query = "SELECT
        id AS \"ID\",
        report_trigger_id AS \"TriggerID\",
        report_submit AS  \"ReportStat\",
        submit_date AS  \"SubmitDate\",
        reference_no,
        report_no AS \"ReportNo\",
        system_id,
        authority_id AS \"AuthID\",
        authority_name,
        authority_logo,
        authority_district AS \"AuthDistrict\",
        calendar_sv_date,
        entry_appl_length AS \"Length\",
        report_type AS \"ReportType\",
        status_id,
        flow_department,
		flow_action,
		flow_phase,
		status,
		status_color,
		status_icon
        FROM view_report_sitevisit
        WHERE system_id = :systemId
        ORDER BY id DESC";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;

    }

    private function getAttachment($systemId, $code) {
        $db = $this->dbFactory->createConnection();
        $query = "SELECT flw_appl_attachments.url, flw_appl_attachments.mime_type FROM flw_appl_attachments LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type WHERE ls_attachments.code_name = :code AND flw_appl_attachments.system_id = :systemId ORDER BY flw_appl_attachments.id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        $data = new stdClass();
        if($stmt->rowCount() == 0) {
            $data =  (object) [
                'url' => NULL,
                'mime_type' => NULL,
            ];
        } else {
            $data->url = $row->url;
            $data->mime_type = $row->mime_type;
        }

        return $data;


    }

    private function selectUsers($subDepartment) {
        $db = $this->dbFactory->createConnection();

        switch($subDepartment) {
            case 'survey':
                $query = "SELECT survey_team AS name, team_profile AS url, survey_team_id AS value FROM view_users GROUP BY survey_team, team_profile, survey_team_id";
            break;
            default:
                $query = "SELECT name, profile_pic AS url, username AS value, position AS description FROM view_users WHERE sub_department = :sub";
            break;
        }

        $stmt = $db->prepare($query);
        if($subDepartment !== 'survey') {
            $stmt->bindParam(':sub', $subDepartment);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $data;
    }

    private function mappingModal($systemId,$statusId){
        $mapping = new stdClass();

        /**
        * NOTE - Example Usage of mapping
        * Input available upload, date, textarea, checkbox, select, text
        *'1' => (object)[
        *    'title' => 'Muatnaik Invois Caj Pendaftaran',
        *    'aside' => (object) [
        *            "url" => $this->getAttachment('SRIL')->url,
        *            "mime" => $this->getAttachment('SRIL')->mime_type,
        *            "type" => 'attachment',
        *        ],
        *    'input' => (object)[
        *        'upload' => (object) [
        *            0 => (object) [
        *                'label' => 'Invois Caj Pendaftaran',
        *                'folder' => 'ICP',
        *                'type' => 'dropzone'
        *            ]
        *        ],
        *        'date' => (object) [
        *            0 => (object) [
        *                'label' => 'Tarikh Invois',
        *                'name' => 'date',
        *                'type' => 'daterange' | 'time' | 'datetime' | 'multiple' | 'date'
        *            ],
        *        ],
        *        'text' => (object) [
        *            0 => (object) [
        *                'label' => 'Jumlah Invois',
        *                'name' => 'amount',
        *                ]
        *        ],
        *        'textarea' => (object) [
        *            0 => (object) [
        *                'label' => 'Catatan',
        *                'name' =>  'notes'
        *           ]
        *        ],
        *        'checkbox' => (object) [
        *            0 => (object) [
        *                'label' => 'Cetak Invois',
        *                'name' => 'print',
        *                'value' => 1,
        *                'type' => 'checkbox' | 'radio' | 'switch'
        *            ],
        *        ],
        *        'select' => (object) [
        *            0 => (object) [
        *                'label' => 'Jenis Invois',
        *                'placeholder' => 'Sila Pilih Jenis Invois',
        *               'name' => 'invois',
        *                'type' => 'images' | 'default',
        *                'option' => $select ----> get from selectUsers
        *                           "0" => (object) [
        *                              "value" => 'Caj Pendaftaran',
        *                              "name" => 'Caj Perkhidmatan',
        *                            ],
        *            ],
        *        ],
        *       'checkbox_title' => 'Sila Tandakan item yang terlibat' ---> must insert if selected checkbox
        *    ]
        * ]
        */

        $mapping = (object)[
            '11' => (object)[
                'title' => 'Sahkan Tarikh Lawatan Tapak',
                'input' => (object)[
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' =>  'notes'
                        ]
                    ],
                ]
            ],

        ];



        return $mapping->$statusId ?? null;
    }

    private function getTableData($systemId) {
        $data = $this->queryTasks($systemId);

        foreach ($data AS $item) {
            switch ($item->status_id) {
                case 10:
                    $item->route = "calendar/{$item->system_id}/1";
                    break;
                case 13:
                    $item->route = "reports/site/{$item->system_id}?ref={$item->ReportNo}&auth={$item->AuthID}";
                    break;
                // case 30:
                //     $item->route = "operation/authority/tasks/new/{$item->system_id}";
                //     break;
                default:
                    $item->route = $item->system_id .'-'. $item->status_id.'-'. $item->AuthID;
                break;
            }
        }
        return $data;
    }

    private function getModalData($systemId) {
        $data = $this->queryTasks($systemId);

        $combinedData = [];

        foreach ($data as $item) {
            $id = $item->status_id;
            $modalData = $this->mappingModal($item->system_id, $id);

            $needed = (object)[
                'system_id' => $item->system_id,
                'status_id' => $item->status_id,
                'authority_id' => $item->AuthID,
                'color' => $item->status_color,
                'id' => $item->system_id .'-'. $item->status_id.'-'. $item->AuthID,
                'authority_logo' => $item->authority_logo,
                'authority_name' => $item->authority_name,
                'ReportNo' => $item-> ReportNo,
                'calendar_sv_date' => $item->calendar_sv_date,
            ];

            if ($modalData !== null) {
                // Merge the data from queryTasks and mappingModal
                $combinedItem = (object) array_merge((array) $needed, (array) $modalData);

                $combinedData[] = $combinedItem;
            }
        }

        return $combinedData;
    }

    public function getJSData($method) {
        $Role = new Roles();
        $department = $Role->{$this->User}->department;
    }

    public function __call($method, $args) {
        $section = $args[0];
        $systemId = $args[1];
        // $phasing = $args[2];

        // switch($phasing){
        //     case 'wayleave':
        //         $phase = 'way_leave';
        //     break;
        //     case 'permit':
        //         $phase = 'work_permit';
        //     break;
        //     case 'cpc':
        //         $phase = 'work_finish';
        //     break;
        //     case 'cmgd':
        //         $phase = 'work_defect';
        //     break;
        //     case 'ccc':
        //         $phase = 'work_complete';
        //     break;
        //     case 'deposit':
        //         $phase = 'deposit_return';
        //     break;
        //     default:
        //         $phase = NULL;
        //     break;
        // }

        if($method === 'table') {
            $getData = $this->getTableData($systemId);
            return $this->Table->render($section, $getData);
        }

        if($method === 'modal') {
            $getData = $this->getModalData($systemId);
            return $this->Modal->render($section, $getData);
        }

        // if ($method === 'javascript') {
        //     $getData = $this->getJSData($section, $roleId);
        //     return  $this->Pages[$subDepartment]->Javascript->get($section, $getData);

        // }
    }
}