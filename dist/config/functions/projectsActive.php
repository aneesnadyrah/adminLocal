<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';

class ProjectActive
{
    private $dbFactory;
    private $Role;
    private $User;
    private $Table;
    private $Modal;

    public function __construct($username)
    {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->Table = new Table();
        $this->Modal = new Modal();
        $this->User = $username ?? $_SESSION['username'];
    }

    private function queryTasks($phase)
    {
        $db = $this->dbFactory->createConnection();

        // Get the current timestamp
        $currentTimestamp = time();

        if ($phase == 'permit') {
            $statuses = '74, 77, 78, 79, 80, 81, 83, 85, 89, 90, 91, 92, 93, 94, 95, 96';

            $query = "SELECT *
            FROM view_tasks
            WHERE (status_id = ANY (ARRAY[" . $statuses . "]))
            ORDER BY system_id;";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach ($data as $item) {
                $query = "SELECT permit.id as p_id, permit.dt_wday_strt, permit.dt_wday_end, extend.id as e_id, extend.dt_wday_strt as e_dt_wday_strt, extend.dt_wday_end as e_dt_wday_end, auth.authority_id
                FROM ctrl_authorities auth
                LEFT JOIN LATERAL unnest(auth.work_permit_id) p_id ON TRUE
                LEFT JOIN flw_work_permit permit ON permit.id = p_id
                LEFT JOIN LATERAL unnest(permit.extend_id) e_id ON TRUE
                LEFT JOIN flw_work_permit_extend extend ON extend.id = e_id
                WHERE auth.system_id = :systemId;";

                $stmt = $db->prepare($query);
                $stmt->execute(['systemId' => $item->system_id]);
                $results = $stmt->fetchAll(PDO::FETCH_OBJ);

                // Initialize variables to store the nearest dates
                $nearestDate = null;
                $nearestEndDate = null;

                // Iterate over the result set
                foreach ($results as $result) {
                    // Convert date strings to timestamps
                    if ($result->dt_wday_end !== null) {
                        $endDateTimestamp = strtotime($result->dt_wday_end);
                    } else {
                        $endDateTimestamp = null;
                    }

                    if ($result->e_dt_wday_end !== null) {
                        $eEndDateTimestamp = strtotime($result->e_dt_wday_end);
                    } else {
                        $eEndDateTimestamp = null;
                    }

                    // Check if the date is in the future
                    if ($endDateTimestamp !== null && $endDateTimestamp > $currentTimestamp) {
                        // Check if this date is the nearest one found so far
                        if ($nearestEndDate === null || $endDateTimestamp < $nearestEndDate) {
                            $nearestEndDate = $endDateTimestamp;
                        }
                    }

                    if ($eEndDateTimestamp !== null && $eEndDateTimestamp > $currentTimestamp) {
                        if ($nearestEndDate === null || $eEndDateTimestamp < $nearestEndDate) {
                            $nearestEndDate = $eEndDateTimestamp;
                        }
                    }
                }

                // Convert nearest end date timestamp to a formatted date string
                if ($nearestEndDate != null) {
                    $nearestDate = date('Y-m-d H:i:s', $nearestEndDate);
                }

                $item->nearest_expiry = $nearestDate;
                $item->custom_action = "Muat Naik Notis Siap Kerja";
                $item->phase = $phase;
            }
        } else if ($phase == 'liability') {
            $query = "SELECT dlp.nearest_expiry, tasks.* FROM (SELECT finish.system_id, auth.id AS ctrl_authorities_id, auth.authority_id, finish.dt_dlp_end as nearest_expiry
            FROM flw_work_finish finish
            LEFT JOIN ctrl_authorities auth ON auth.work_finish_id = finish.id
            WHERE CURRENT_DATE BETWEEN finish.dt_dlp_start AND finish.dt_dlp_end) dlp
            LEFT JOIN (SELECT tasks.*, status.authority, ls_authorities.sort_name, ls_authorities.logo
            FROM public.view_tasks tasks
            LEFT JOIN ctrl_statuses status ON status.system_id = tasks.system_id AND status.status_id = tasks.status_id
            LEFT JOIN ls_authorities ON ls_authorities.id = status.authority
            WHERE status.authority != 0
            AND flow_department = 'operation'
            GROUP BY status.authority, tasks.reference_no,tasks.system_id,tasks.project_title,tasks.payment_method,tasks.length_code,tasks.application_length, tasks.approved_date, tasks.submitted_date, tasks.created_date,tasks.districts, tasks.provider_id, tasks.status_id, tasks.flow_action,tasks.flow_phase, tasks.status, tasks.status_color, tasks.status_icon, tasks.flow_department, tasks.gis_assignee, tasks.gis_assignee_date, tasks.pil_submitted_by, tasks.pkd_assignee, tasks.pkd_assignee_date, tasks.roads, tasks.authority_icon, tasks.application_date
            ,ls_authorities.sort_name,ls_authorities.logo
            ) tasks ON (tasks.system_id = dlp.system_id AND tasks.authority = dlp.authority_id)";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach ($data as $item) {
                $item->custom_action = "Muat Naik Surat Permohonan Sijil Siap Membaiki Kecatatan";
                $item->phase = $phase;
            }
        }

        return $data;
    }

    private function mappingModal($systemId, $statusId)
    {
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

        $mapping = (object) [
            'permit' => (object) [
                'title' => 'Majukan ke Tindakan Notis Siap Kerja',
                'input' => (object) [
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            'liability' => (object) [
                'title' => 'Majukan ke Tindakan Permohonan Sijil Siap Membaiki Kecacatan',
                'input' => (object) [
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],

        ];



        return $mapping->$statusId ?? null;
    }

    private function getTableData($phase)
    {
        $data = $this->queryTasks($phase);

        foreach ($data as $item) {
            if ($item->phase == 'permit') {
                $item->route = $item->system_id . '-' . $item->phase;
            } else if ($item->phase == 'liability') {
                $item->route = $item->system_id . '-' . $item->phase . '-' .$item->authority;
            }
        }
        return $data;
    }

    private function getModalData($systemId)
    {
        $data = $this->queryTasks($systemId);

        $combinedData = [];

        foreach ($data as $item) {
            $id = $item->phase;
            $modalData = $this->mappingModal($item->system_id, $id);

            if ($item->phase == 'permit') {
                $needed = (object) [
                    'system_id' => $item->system_id,
                    'status_id' => $item->phase,
                    'color' => $item->status_color,
                    'id' => $item->system_id . '-' . $item->phase,
                    'action' => $item->custom_action,
                ];
            } else if ($item->phase == 'liability') {
                $needed = (object) [
                    'system_id' => $item->system_id,
                    'status_id' => $item->phase,
                    'color' => $item->status_color,
                    'id' => $item->system_id . '-' . $item->phase . '-' .$item->authority,
                    'action' => $item->custom_action,
                ];
            }

            if ($modalData !== null) {
                // Merge the data from queryTasks and mappingModal
                $combinedItem = (object) array_merge((array) $needed, (array) $modalData);

                $combinedData[] = $combinedItem;
            }
        }

        return $combinedData;
    }

    public function getJSData($method)
    {
        $Role = new Roles();
        $department = $Role->{$this->User}->department;
    }

    public function __call($method, $args)
    {
        $section = $args[0];
        $filter = $args[1];

        if ($method === 'table') {
            $getData = $this->getTableData($filter);
            return $this->Table->render($section, $getData);
        }

        if ($method === 'modal') {
            $getData = $this->getModalData($filter);
            return $this->Modal->render($section, $getData);
        }
    }
}