<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';

class TeamSurvey
{
    private $dbFactory;
    private $Role;
    private $User;
    private $Table;
    private $Modal;
    private $system;

    public function __construct($username)
    {
        $this->dbFactory = new DBConnectionFactory();
        $this->Role = new Roles();
        $this->Table = new Table();
        $this->Modal = new Modal();
        $this->User = $username ?? $_SESSION['username'];
        $this->system = new System;
    }

    private function queryTasks()
    {
        $db = $this->dbFactory->createConnection();
        $state = $this->system->App->state;

        $query = $db->prepare("SELECT * FROM flw_survey_team WHERE state = :state AND is_active = true ORDER BY id ASC");
        
        // Bind the parameter
        $query->bindParam(':state', $state, PDO::PARAM_INT);
        // Execute the query
        $query->execute();

        $data = $query->fetchAll(PDO::FETCH_OBJ);

        return $data;

    }

    private function getAttachment($systemId, $code)
    {
        $db = $this->dbFactory->createConnection();
        $query = "SELECT flw_appl_attachments.url, flw_appl_attachments.mime_type FROM flw_appl_attachments LEFT JOIN ls_attachments ON ls_attachments.id = flw_appl_attachments.attachment_type WHERE ls_attachments.code_name = :code AND flw_appl_attachments.system_id = :systemId ORDER BY flw_appl_attachments.id DESC LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);

        $data = new stdClass();
        if ($stmt->rowCount() == 0) {
            $data = (object) [
                'url' => NULL,
                'mime_type' => NULL,
            ];
        } else {
            $data->url = $row->url;
            $data->mime_type = $row->mime_type;
        }

        return $data;


    }

    private function selectUsers($subDepartment)
    {
        $db = $this->dbFactory->createConnection();

        switch ($subDepartment) {
            case 'survey':
                $query = "SELECT survey_team AS name, team_profile AS url, survey_team_id AS value FROM view_users GROUP BY survey_team, team_profile, survey_team_id";
                break;
            default:
                $query = "SELECT name, profile_pic AS url, username AS value, position AS description FROM view_users WHERE sub_department = :sub";
                break;
        }

        $stmt = $db->prepare($query);
        if ($subDepartment !== 'survey') {
            $stmt->bindParam(':sub', $subDepartment);
        }
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

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
        ];

        $authority = (object) [
            '30' => (object) [
                'title' => 'Muat Naik Ringkasan Projek dan Kiraan Wang Cagaran',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Ringkasan Projek',
                            'folder' => 'RP',
                            'type' => '.pdf'
                        ],
                        1 => (object) [
                            'label' => 'Kiraan Wang Cagaran',
                            'folder' => 'KWC',
                            'type' => '.pdf'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '31' => (object) [
                'title' => 'Muat Naik Dokumen Permohonan Kelulusan Izin Lalu',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Dokumen Permohonan Kelulusan Izin Lalu',
                            'folder' => 'SPKIL',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_auth_ltr_created',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '32' => (object) [
                'title' => 'Muat Naik Akuan Serahan Permohonan Kelulusan Izin Lalu',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Kelulusan Izin Lalu',
                            'folder' => 'ASPKIL',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat Dihantar',
                            'name' => 'dt_auth_ltr_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '33' => (object) [
                'title' => 'Muat Naik Kelulusan Izin Lalu',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Kelulusan Izin Lalu',
                            'folder' => 'SKIL',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_appv_ltr',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Surat Terima',
                            'name' => 'dt_appv_ltr_received',
                            'type' => 'date'
                        ],
                        2 => (object) [
                            'label' => 'Tarikh Mula',
                            'name' => 'dt_start',
                            'type' => 'date'
                        ],
                        3 => (object) [
                            'label' => 'Tarikh Tamat',
                            'name' => 'dt_end',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '34' => (object) [
                'title' => 'Muat Naik Maklumbalas Kelulusan Izin Lalu',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Maklumbalas Kelulusan Izin Lalu',
                            'folder' => 'MKIL',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_fb_ltr_created',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Hantar',
                            'name' => 'dt_fb_ltr_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '70' => (object) [
                'title' => 'Muat Naik Dokumen Permohonan Kelulusan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Dokumen Permohonan Kelulusan Permit Kerja',
                            'folder' => 'SPKPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_auth_ltr_created',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '71' => (object) [
                'title' => 'Muat Naik Akuan Serahan Permohonan Kelulusan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Kelulusan Permit Kerja',
                            'folder' => 'ASPKPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat Keluar',
                            'name' => 'dt_auth_ltr_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '72' => (object) [
                'title' => 'Muat Naik Kelulusan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Kelulusan Permit Kerja',
                            'folder' => 'SKPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_appv_ltr_created',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_appv_received',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '73' => (object) [
                'title' => 'Muat Naik Perakuan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Perakuan Permit Kerja',
                            'folder' => 'PPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Perakuan',
                            'name' => 'dt_wp_recog_created',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Makluman',
                            'name' => 'dt_wp_recog_notify',
                            'type' => 'date'
                        ],
                        2 => (object) [
                            'label' => 'Tarikh Mula Hari Bekerja',
                            'name' => 'dt_wday_strt',
                            'type' => 'date'
                        ],
                        3 => (object) [
                            'label' => 'Tarikh Tamat Hari Bekerja',
                            'name' => 'dt_wday_end',
                            'type' => 'date'
                        ],
                        4 => (object) [
                            'label' => 'Tarikh Mula Hari Minggu',
                            'name' => 'dt_wend_strt',
                            'type' => 'date'
                        ],
                        5 => (object) [
                            'label' => 'Tarikh Tamat Hari Minggu',
                            'name' => 'dt_wend_end',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'No Perakuan',
                            'name' => 'no_wp_recog'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '74' => (object) [
                'title' => 'Muat Naik Akuan Serahan Perakuan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Perakuan Permit Kerja',
                            'folder' => 'ASPPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Serahan',
                            'name' => 'dt_wp_recog_submit',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Nama Penerima',
                            'name' => 'wp_recog_received_by'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '80' => (object) [
                'title' => 'Muat Naik Surat Makluman Notis Mula Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Surat Makluman Notis Mula Kerja',
                            'folder' => 'SMMK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_ws_auth_ltr_created',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '81' => (object) [
                'title' => 'Muat Naik Akuan Serahan Makluman Notis Mula Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Makluman Notis Mula Kerja',
                            'folder' => 'ASMMK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Hantar',
                            'name' => 'dt_ws_auth_ltr_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '92' => (object) [
                'title' => 'Muat Naik Dokumen Permohonan Lanjutan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Dokumen Permohonan Lanjutan Permit Kerja',
                            'folder' => 'SPLPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Pemohonan',
                            'name' => 'dt_appl',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Surat Keluar',
                            'name' => 'dt_auth_ltr',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '93' => (object) [
                'title' => 'Muat Naik Akuan Serahan Permohonan Lanjutan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Lanjutan Permit Kerja',
                            'folder' => 'ASPLPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat Keluar',
                            'name' => 'dt_auth_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '94' => (object) [
                'title' => 'Muat Naik Surat Kelulusan Lanjutan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Lanjutan Permit Kerja',
                            'folder' => 'ASPLPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat Keluar',
                            'name' => 'dt_auth_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '95' => (object) [
                'title' => 'Muat Naik Perakuan Lanjutan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Perakuan Lanjutan Permit Kerja',
                            'folder' => 'PLPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Perakuan',
                            'name' => 'dt_perakuan',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Makluman',
                            'name' => 'dt_makluman',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'No Perakuan',
                            'name' => 'no_perakuan'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '96' => (object) [
                'title' => 'Muat Naik Akuan Serahan Perakuan Lanjutan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Perakuan Lanjutan Permit Kerja',
                            'folder' => 'ASPLPK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat Keluar',
                            'name' => 'dt_auth_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '120' => (object) [
                'title' => 'Muat Naik Surat Permohonan Sijil Siap Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Surat Permohonan Sijil Siap Kerja',
                            'folder' => 'SPSSK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_ltr',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '121' => (object) [
                'title' => 'Muat Naik Akuan Serahan Permohonan Sijil Siap Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Sijil Siap Kerja',
                            'folder' => 'ASPSSK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Hantar',
                            'name' => 'dt_auth_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '122' => (object) [
                'title' => 'Muat Naik Kelulusan Permohonan Sijil Siap Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Kelulusan Permohonan Sijil Siap Kerja',
                            'folder' => 'SKPSSK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Mula',
                            'name' => 'dt_cpc_dlp_start',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Tamat',
                            'name' => 'dt_cpc_dlp_end',
                            'type' => 'date'
                        ],
                        2 => (object) [
                            'label' => 'Tarikh Kelulusan',
                            'name' => 'dt_cpc_appv_ltr',
                            'type' => 'date'
                        ],
                        3 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_cpc_appc_recv',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '123' => (object) [
                'title' => 'Muat Naik Perakuan Sijil Siap Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Perakuan Sijil Siap Kerja',
                            'folder' => 'PSSK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Perakuan',
                            'name' => 'dt_cpc_perakuan',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Makluman',
                            'name' => 'dt_cpc_makluman',
                            'type' => 'date'
                        ],
                        2 => (object) [
                            'label' => 'Tarikh Kelulusan',
                            'name' => 'dt_cpc_appv_ltr',
                            'type' => 'date'
                        ],
                        3 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_cpc_appc_recv',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '125' => (object) [
                'title' => 'Muat Naik Akuan Serahan Perakuan Sijil Siap Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Perakuan Sijil Siap Kerja',
                            'folder' => 'ASPKSSK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Serahan',
                            'name' => 'dt_send',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Nama Penerima',
                            'name' => 'name_recv'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '136' => (object) [
                'title' => 'Muat Naik Surat Permohonan Sijil Siap Memperbaiki Kecacatan',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Surat Permohonan Sijil Siap Memperbaiki Kecacatan',
                            'folder' => 'SPSSMK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_auth_ltr',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '137' => (object) [
                'title' => 'Muat Naik Akuan Serahan  Permohonan Sijil Siap Memperbaiki Kecacatan',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan  Permohonan Sijil Siap Memperbaiki Kecacatan',
                            'folder' => 'ASPSSMK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Hantar',
                            'name' => 'dt_auth_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '138' => (object) [
                'title' => 'Muat Naik Ulasan Laporan Sijil Siap Memperbaiki Kecacatan',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Ulasan Laporan Sijil Siap Memperbaiki Kecacatan',
                            'folder' => 'UPSSMK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Semakan Surat',
                            'name' => 'dt_review_ltr',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_recv',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '139' => (object) [
                'title' => 'Muat Naik Surat Permohonan Sijil Sempurna Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Surat Permohonan Sijil Sempurna Kerja',
                            'folder' => 'SPCCC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_auth_ltr',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '140' => (object) [
                'title' => 'Muat Naik Akuan Serahan Permohonan Sijil Sempurna Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Sijil Sempurna Kerja',
                            'folder' => 'ASPCCC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Hantar',
                            'name' => 'dt_auth_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '141' => (object) [
                'title' => 'Muat Naik Surat Kelulusan Permohonan Sijil Sempurna Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Surat Kelulusan Permohonan Sijil Sempurna Kerja',
                            'folder' => 'SKPCCC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_ltr',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_recv',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '142' => (object) [
                'title' => 'Muat Naik Perakuan Sijil Sempurna Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Perakuan Sijil Sempurna Kerja',
                            'folder' => 'PCCC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Perakuan',
                            'name' => 'dt_ccc_perakuan',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Makluman',
                            'name' => 'dt_ccc_makluman',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'No Perakuan',
                            'name' => 'no_ccc_perakuan'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '143' => (object) [
                'title' => 'Muat Naik Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja',
                            'folder' => 'ASPKCCC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Serahan',
                            'name' => 'dt_send',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Nama Penerima',
                            'name' => 'name_recv'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '146' => (object) [
                'title' => 'Muat Naik Surat Permohonan Pemulangan Wang Cagaran',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Surat Permohonan Pemulangan Wang Cagaran',
                            'folder' => 'SPPWC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_auth_ltr_created',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '147' => (object) [
                'title' => 'Muat Naik Akuan Serahan Permohonan Pemulangan Wang Cagaran',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Akuan Serahan Permohonan Pemulangan Wang Cagaran',
                            'folder' => 'ASPPWC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Hantar',
                            'name' => 'dt_auth_ltr_send',
                            'type' => 'date'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
            '148' => (object) [
                'title' => 'Muat Naik Baucer Pemulangan Wang Cagaran',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Baucer Pemulangan Wang Cagaran',
                            'folder' => 'BPWC',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_appv_ltr_created',
                            'type' => 'date'
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_appv_ltr_received',
                            'type' => 'date'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'No Baucer',
                            'name' => 'no_wc_voucher'
                        ]
                    ],
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

    private function getTableData()
    {
        $data = $this->queryTasks();


        // foreach ($data as $item) {
        //     // var_dump($item);
        //     switch ($item->id) {
        //         default:
        //             $item->route = $item->system_id . '-' . $item->status_id;
        //             break;
        //     }
        // }
        return $data;
    }

    private function getModalData()
    {
        $data = $this->queryTasks();

        $combinedData = [];

        foreach ($data as $item) {
            $id = $item->status_id;
            $modalData = $this->mappingModal($item->system_id, $id);

            $needed = (object) [
                'system_id' => $item->system_id,
                'status_id' => $item->status_id,
                'color' => $item->status_color,
                'id' => $item->system_id . '-' . $item->status_id,
            ];

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

        if ($method === 'table') {
            $getData = $this->getTableData();
            return $this->Table->render($section, $getData);

        }

        if ($method === 'modal') {
            $getData = $this->getModalData();
            return $this->Modal->render($section, $getData);
        }

        // if ($method === 'javascript') {
        //     $getData = $this->getJSData($section, $roleId);
        //     return  $this->Pages[$subDepartment]->Javascript->get($section, $getData);

        // }
    }
}