<?php

$authority = (object)[
    '30' => (object)[
        'title' => 'Muat Naik Ringkasan Projek dan Kiraan Wang Cagaran',
        'input' => (object)[
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
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '31' => (object)[
        'title' => 'Muat Naik Dokumen Permohonan Kelulusan Izin Lalu',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '32' => (object)[
        'title' => 'Muat Naik Akuan Serahan Permohonan Kelulusan Izin Lalu',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '33' => (object)[
        'title' => 'Muat Naik Kelulusan Izin Lalu',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Surat Terima',
                    'name' => 'dt_appv_ltr_received',
                    'type' => 'date',
                    'required' => true
                ],
                2 => (object) [
                    'label' => 'Tarikh Mula',
                    'name' => 'dt_start',
                    'type' => 'date'
                ],
                3 => (object) [
                    'label' => 'Tarikh Tamat',
                    'name' => 'dt_end',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '34' => (object)[
        'title' => 'Muat Naik Maklumbalas Kelulusan Izin Lalu',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Hantar',
                    'name' => 'dt_fb_ltr_send',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '70' => (object)[
        'title' => 'Muat Naik Dokumen Permohonan Kelulusan Permit Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '71' => (object)[
        'title' => 'Muat Naik Akuan Serahan Permohonan Kelulusan Permit Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '72' => (object)[
        'title' => 'Muat Naik Kelulusan Permit Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_appv_received',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '73' => (object)[
        'title' => 'Muat Naik Perakuan Permit Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Makluman',
                    'name' => 'dt_wp_recog_notify',
                    'type' => 'date',
                    'required' => true
                ],
                2 => (object) [
                    'label' => 'Tarikh Mula Hari Bekerja',
                    'name' => 'dt_wday_strt',
                    'type' => 'date',
                    'required' => true
                ],
                3 => (object) [
                    'label' => 'Tarikh Tamat Hari Bekerja',
                    'name' => 'dt_wday_end',
                    'type' => 'date',
                    'required' => true
                ],
                4 => (object) [
                    'label' => 'Tarikh Mula Hari Minggu',
                    'name' => 'dt_wend_strt',
                    'type' => 'date',
                ],
                5 => (object) [
                    'label' => 'Tarikh Tamat Hari Minggu',
                    'name' => 'dt_wend_end',
                    'type' => 'date',
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'No Perakuan',
                    'name' =>  'no_wp_recog',
                    'required' => true
                ]
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '74' => (object)[
        'title' => 'Muat Naik Akuan Serahan Perakuan Permit Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'Nama Penerima',
                    'name' =>  'wp_recog_received_by',
                    'required' => true
                ]
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],

    '80' => (object)[
        'title' => 'Muat Naik Surat Makluman Notis Mula Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '81' => (object)[
        'title' => 'Muat Naik Akuan Serahan Makluman Notis Mula Kerja',
        'input' => (object)[
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
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '90' => (object) [
        'title' => 'Muat Naik Dokumen Permohonan Lanjutan Permit Kerja',
        'input' => (object) [
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Dokumen Permohonan Lanjutan Permit Kerja',
                    'folder' => 'SRPLPK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Surat',
                    'name' => 'dt_appl_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_appl_ltr_received',
                    'type' => 'date',
                    'required' => true
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
    '92' => (object)[
        'title' => 'Muat Naik Surat Permohonan Lanjutan Permit Kerja',
        'input' => (object)[
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Surat Permohonan Lanjutan Permit Kerja',
                    'folder' => 'SPLPK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Surat',
                    'name' => 'dt_auth_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '93' => (object)[
        'title' => 'Muat Naik Akuan Serahan Permohonan Lanjutan Permit Kerja',
        'input' => (object)[
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
                    'name' => 'dt_auth_ltr_send',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '94' => (object)[
        'title' => 'Muat Naik Surat Kelulusan Lanjutan Permit Kerja',
        'input' => (object)[
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Kelulusan Lanjutan Permit Kerja',
                    'folder' => 'SKLPK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Surat',
                    'name' => 'dt_appv_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_appv_received',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '95' => (object)[
        'title' => 'Muat Naik Perakuan Lanjutan Permit Kerja',
        'input' => (object)[
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
                    'name' => 'dt_wp_recog_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Makluman',
                    'name' => 'dt_wp_recog_notify',
                    'type' => 'date',
                    'required' => true
                ],
                2 => (object) [
                    'label' => 'Tarikh Mula Hari Bekerja',
                    'name' => 'dt_wday_strt',
                    'type' => 'date',
                    'required' => true
                ],
                3 => (object) [
                    'label' => 'Tarikh Tamat Hari Bekerja',
                    'name' => 'dt_wday_end',
                    'type' => 'date',
                    'required' => true
                ],
                4 => (object) [
                    'label' => 'Tarikh Mula Hari Minggu',
                    'name' => 'dt_wend_strt',
                    'type' => 'date',
                    'required' => true
                ],
                5 => (object) [
                    'label' => 'Tarikh Tamat Hari Minggu',
                    'name' => 'dt_wend_end',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'No Perakuan',
                    'name' =>  'no_wp_recog',
                    'required' => true
                ]
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '96' => (object)[
        'title' => 'Muat Naik Akuan Serahan Perakuan Lanjutan Permit Kerja',
        'input' => (object)[
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Akuan Serahan Perakuan Lanjutan Permit Kerja',
                    'folder' => 'ASPLPK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Serahan',
                    'name' => 'dt_wp_recog_handover',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'Nama Penerima',
                    'name' =>  'wp_recog_received_by',
                    'required' => true
                ]
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '118' => (object)[
        'title' => 'Muat Naik Dokumen Permohonan Sijil Siap Kerja',
        'input' => (object)[
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Dokumen Permohonan Sijil Siap Kerja',
                    'folder' => 'SRPSSK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Surat',
                    'name' => 'dt_appl_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_appl_ltr_received',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '120' => (object)[
        'title' => 'Muat Naik Surat Permohonan Sijil Siap Kerja',
        'input' => (object)[
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
                    'name' => 'dt_auth_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '121' => (object)[
        'title' => 'Muat Naik Akuan Serahan Permohonan Sijil Siap Kerja',
        'input' => (object)[
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
                    'name' => 'dt_auth_ltr_send',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '122' => (object)[
        'title' => 'Muat Naik Kelulusan Permohonan Sijil Siap Kerja',
        'input' => (object)[
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Kelulusan Permohonan Sijil Siap Kerja',
                    'folder' => 'SKPSSK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Mula DLP',
                    'name' => 'dt_dlp_start',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Tamat DLP',
                    'name' => 'dlpEnd',
                    'type' => 'date',
                    'required' => true
                ],
                2 => (object) [
                    'label' => 'Tarikh Kelulusan',
                    'name' => 'dt_appv_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                3 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_appv_ltr_received',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
                ]
            ],
        ]
    ],
    '123' => (object)[
        'title' => 'Muat Naik Perakuan Sijil Siap Kerja',
        'input' => (object)[
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
                    'name' => 'dt_wf_recog_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Makluman',
                    'name' => 'dt_wf_recog_notify',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'No Perakuan',
                    'name' =>  'no_wf_recog',
                    'required' => true
                ]
            ],
            'textarea' => (object) [
                0 => (object) [
                    'label' => 'Catatan',
                    'name' =>  'notes'
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
                    'name' => 'dt_wf_recog_submit',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'Nama Penerima',
                    'name' => 'wf_recog_received_by',
                    'required' => true
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
    '134' => (object) [
        'title' => 'Muat Naik Dokumen Permohonan CMGD',
        'input' => (object) [
            'upload' => (object) [
                0 => (object) [
                    'label' => 'Dokumen Permohonan CMGD',
                    'folder' => 'SRPSSMK',
                    'type' => '.pdf'
                ],
            ],
            'date' => (object) [
                0 => (object) [
                    'label' => 'Tarikh Surat',
                    'name' => 'dt_cmgd_appl_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_cmgd_appl_ltr_received',
                    'type' => 'date',
                    'required' => true
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
                    'name' => 'dt_cmgd_auth_ltr_created',
                    'type' => 'date',
                    'required' => true
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
                    'name' => 'dt_cmgd_auth_send',
                    'type' => 'date',
                    'required' => true
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
                    'label' => 'Tarikh Surat',
                    'name' => 'dt_cmgd_appv_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_cmgd_appv_ltr_received',
                    'type' => 'date',
                    'required' => true
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
                    'name' => 'dt_ccc_auth_ltr_created',
                    'type' => 'date',
                    'required' => true
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
                    'name' => 'dt_ccc_auth_send',
                    'type' => 'date',
                    'required' => true
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
                    'name' => 'dt_ccc_appv_ltr_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_ccc_appv_received',
                    'type' => 'date',
                    'required' => true
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
                    'name' => 'dt_ccc_recog_created',
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Makluman',
                    'name' => 'dt_ccc_recog_notify',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'No Perakuan',
                    'name' => 'no_ccc_recog',
                    'required' => true
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
                    'name' => 'dt_ccc_recog_submit',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'Nama Penerima',
                    'name' => 'ccc_recog_received_by',
                    'required' => true
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
                    'type' => 'date',
                    'required' => true
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
                    'type' => 'date',
                    'required' => true
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
                    'type' => 'date',
                    'required' => true
                ],
                1 => (object) [
                    'label' => 'Tarikh Terima',
                    'name' => 'dt_appv_ltr_received',
                    'type' => 'date',
                    'required' => true
                ],
            ],
            'text' => (object) [
                0 => (object) [
                    'label' => 'No Baucer',
                    'name' => 'no_voucher',
                    'required' => true
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