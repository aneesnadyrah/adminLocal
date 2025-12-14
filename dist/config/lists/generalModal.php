<?php
/**
 * NOTE - Example Usage of mapping
 * Input available upload, date, textarea, checkbox, select, text
 *'1' => (object)[
 *    'title' => 'Muatnaik Invois Caj Pendaftaran',
 *    'aside' => (object) [
 *            "url" => $statusId === 1  ? General::getAttachment('SRIL')->url:null,
 *            "mime" => $statusId === 1  ? General::getAttachment('SRIL')->mime_type:null,
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


switch ($statusId) {
    case 1:
        $mapping = (object) [
            '1' => (object) [
                'title' => 'Muatnaik Invois Caj Pendaftaran',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Invois Caj Pendaftaran',
                            'folder' => 'ICP',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Invois',
                            'name' => 'dt_inv',
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
            ]
        ];
        break;
    case 2:
        $paymentMethod = General::getPaymentMethod($systemId);

        if ($paymentMethod['payment_method'] == 4) {
            $mapping = (object) [

                '2' => (object) [
                    'title' => 'Semakan Caj Pendaftaran ToyyibPay',
                    'input' => (object) [
                        'checkbox' => (object) [
                            0 => (object) [
                                'label' => 'Terima Caj Pendaftaran?',
                                'name' => 'verify_rcp',
                                'value' => 1,
                                'type' => 'switch'
                            ],
                        ],
                        'textarea' => (object) [
                            0 => (object) [
                                'label' => 'Catatan',
                                'name' => 'notes'
                            ]
                        ],
    
                    ]
                ]
            ];

        } else{
            $mapping = (object) [

                '2' => (object) [
                    'title' => 'Semakan Invois Caj Pendaftaran',
                    'aside' => (object) [
                        "url" => $statusId === 2 ? General::getAttachment($systemId, 'RCP')->url : null,
                        "mime" => $statusId === 2 ? General::getAttachment($systemId, 'RCP')->mime_type : null,
                        "type" => 'attachment',
                    ],
                    'input' => (object) [
                        'checkbox' => (object) [
                            0 => (object) [
                                'label' => 'Terima Caj Pendaftaran?',
                                'name' => 'verify_rcp',
                                'value' => 1,
                                'type' => 'switch'
                            ],
                        ],
                        'textarea' => (object) [
                            0 => (object) [
                                'label' => 'Catatan',
                                'name' => 'notes'
                            ]
                        ],
    
                    ]
                ]
            ];
        }
        break;
    case 6:
        $mapping = (object) [
            '6' => (object) [
                'title' => 'Muat Naik Arahan Kerja PIL',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Arahan Kerja PIL',
                            'folder' => 'AKP',
                            'type' => '.pdf'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Amaun Arahan Kerja (RM)',
                            'name' => 'total_wop',
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
            ]
        ];
        break;
    case 7:
        $mapping = (object) [
            '7' => (object) [
                'title' => 'Lantikan Pelukis Pelan Surihan',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Pegawai GIS',
                            'placeholder' => 'Sila Pilih Pegawai GIS',
                            'name' => 'gis_assign',
                            'type' => 'images',
                            'option' => $statusId === 7 ? $this->selectUsers('charting') : null,
                            'required' => true
                        ],
                    ],
                    // 'text' => (object) [
                    //     0 => (object) [
                    //         'label' => 'Tempoh Masa Perlu Disiapkan',
                    //         'name' => 'delivery_date',
                    //         'style' => 'transparent',
                    //         'readonly' => true,
                    //         'attribute' => 'data-estimated'
                    //     ]
                    // ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 8:
        $mapping = (object) [
            '8' => (object) [
                'title' => 'Muat Naik Pelan Cadangan Laluan',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pelan Cadangan Laluan',
                            'folder' => 'PCL',
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
            ]
        ];
        break;
    case 9:
        $mapping = (object) [
            '9' => (object) [
                'title' => 'Muat Naik Arahan Kerja Operasi',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Arahan Kerja Operasi',
                            'folder' => 'AKO',
                            'type' => '.pdf'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Amaun Arahan Kerja (RM)',
                            'name' => 'total_woo',
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
            ]
        ];
        break;
    case 24:
        $mapping = (object) [
            '24' => (object) [
                'title' => 'Muat Naik Pelan Izin Lalu',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pelan Izin Lalu',
                            'folder' => 'PIL',
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
            ]
        ];
        break;
    case 27:
        $mapping = (object) [
            '27' => (object) [
                'title' => 'Muat Naik Sebut Harga',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Sebut Harga',
                            'folder' => 'SH',
                            'type' => '.pdf'
                        ],
                    ],
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Plan UDM',
                            'name' => 'check_udm',
                            'value' => 1,
                            'type' => 'checkbox'
                        ],
                        1 => (object) [
                            'label' => 'Plan TMP',
                            'name' => 'check_tmp',
                            'value' => 1,
                            'type' => 'checkbox'
                        ],
                        2 => (object) [
                            'label' => 'Plan As Built',
                            'name' => 'check_asbuilt',
                            'value' => 1,
                            'type' => 'checkbox'
                        ],
                    ],
                    'checkbox_title' => 'Sila tanda caj perkhidmatan yang berkaitan',
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Amaun Sebut Harga (RM)',
                            'name' => 'total_quote',
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
            ]
        ];
        break;
    /* The above code is a switch statement in PHP. It is checking if the value of the variable
    `` is equal to 27. If it is, then the code inside the case block will be executed. */
    case 28:
        $mapping = (object) [
            '28' => (object) [
                'title' => 'Semakan Sebut Harga Disetujui',
                'aside' => (object) [
                    "url" => $statusId === 28 ? General::getAttachment($systemId, 'SHD')->url : null,
                    "mime" => $statusId === 28 ? General::getAttachment($systemId, 'SHD')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Sebut Harga?',
                            'name' => 'attach_agreed',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 29:
        $quoteApproval = Permitting::getQuoteApproval($systemId);
        if ($quoteApproval['quote_services_included_amended'] !== null) {
            $servicesArray = explode(',', trim($quoteApproval['quote_services_included_amended'], '{}'));
        } else {
            $servicesArray = [];
        }
        $mapping = (object) [
            '29' => (object) [
                'title' => 'Muat Naik Pindaan Sebut Harga',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pindaan Sebut Harga',
                            'folder' => 'SH',
                            'type' => '.pdf'
                        ],
                    ],
                    'note' => (object) [
                        0 => (object) [
                            'label' => 'Nota dari pemohon',
                            'note' => $quoteApproval['quote_approve_notes'],
                            'services' => $quoteApproval['quote_services_included_amended'],
                        ],
                    ],
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Plan UDM',
                            'name' => 'check_udm',
                            'value' => 1,
                            'type' => 'checkbox',
                            'checked' => (!empty ($servicesArray[0]) == 1 ? 'checked' : '')
                        ],
                        1 => (object) [
                            'label' => 'Plan TMP',
                            'name' => 'check_tmp',
                            'value' => 1,
                            'type' => 'checkbox',
                            'checked' => (!empty ($servicesArray[1]) == 1 ? 'checked' : '')
                        ],
                        2 => (object) [
                            'label' => 'Plan As Built',
                            'name' => 'check_asbuilt',
                            'value' => 1,
                            'type' => 'checkbox',
                            'checked' => (!empty ($servicesArray[2]) == 1 ? 'checked' : '')
                        ],
                    ],
                    'checkbox_title' => 'Sila tanda caj perkhidmatan yang berkaitan',
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Amaun Sebut Harga (RM)',
                            'name' => 'total_quote',
                            'value' => $quoteApproval['quotation_total'],
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
            ]
        ];
        break;
    case 35:
        $mapping = (object) [
            '35' => (object) [
                'title' => 'Muat Naik Bukti Pembayaran Invois',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Bukti Pembayaran Invois',
                            'folder' => 'RCPP',
                            'type' => '.pdf'
                        ],
                    ],
                    // 'checkbox' => (object) [
                    //     0 => (object) [
                    //         'label' => 'Upload Invois Seterusnya?',
                    //         'name' => 'proceed_next_invoice',
                    //         'value' => 1,
                    //         'type' => 'switch'
                    //     ],
                    // ],
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Kategori Invois',
                            'placeholder' => 'Sila Pilih Kategori Invois',
                            'name' => 'invoice_category',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Perkhidmatan',
                                    "name" => 'Perkhidmatan',
                                ],
                                // "1" => (object) [
                                //     "value" => 'GIS_Ready',
                                //     "name" => 'GIS_Ready',
                                // ],
                            ],
                            'required' => true
                        ],
                        // 1 => (object) [
                        //     'label' => 'Peratusan Tuntutan',
                        //     'placeholder' => 'Sila Pilih Peratusan Tuntutan',
                        //     'name' => 'invoice_division',
                        //     'type' => 'default',
                        //     'option' => [
                        //         "0" => (object) [
                        //             "value" => '10',
                        //             "name" => '10%',
                        //         ],
                        //         "1" => (object) [
                        //             "value" => '40',
                        //             "name" => '40%',
                        //         ],
                        //         "2" => (object) [
                        //             "value" => '50',
                        //             "name" => '50%',
                        //         ],
                        //         "3" => (object) [
                        //             "value" => '100',
                        //             "name" => '100%',
                        //         ],
                        //     ]
                        // ],
                        1 => (object) [
                            'label' => 'Kaedah Pembayaran',
                            'placeholder' => 'Sila Pilih Kaedah Pembayaran',
                            'name' => 'payment_method',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Pemindahan_Bank_Segera',
                                    "name" => 'Pemindahan Bank Segera',
                                ],
                                "1" => (object) [
                                    "value" => 'Jaminan_bank',
                                    "name" => 'Jaminan Bank',
                                ],
                                "2" => (object) [
                                    "value" => 'Cek',
                                    "name" => 'Cek',
                                ],
                                "3" => (object) [
                                    "value" => 'Tunai',
                                    "name" => 'Tunai',
                                ],
                            ],
                            'required' => true
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Tuntutan Semasa (%)',
                            'value' => $this->getInvPercent($systemId)->percent,
                            'name' => 'current_invoice_division',
                            'readonly' => 'readonly'
                        ],
                        1 => (object) [
                            'label' => 'Jumlah Baki Invois (RM)',
                            'value' => $this->getInvPercent($systemId)->balance,
                            'name' => 'balance_invoice',
                            'readonly' => 'readonly'
                        ],
                        2 => (object) [
                            'label' => 'Jumlah Bayaran (RM)',
                            'placeholder' => 'Sila Isi Jumlah Bayaran',
                            'name' => 'total_rcpp',
                            'required' => true
                        ],
                        3 => (object) [
                            'label' => 'Nama Pembayar',
                            'placeholder' => 'Sila Isi Nama Pembayar',
                            'name' => 'payee',
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
            ]
        ];
        break;
    case 36:
        $mapping = (object) [
            '36' => (object) [
                'title' => 'Muat Naik Invois Caj Perkhidmatan',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Invois Caj Perkhidmatan',
                            'folder' => 'ICPP',
                            'type' => '.pdf'
                        ],
                    ],
                    // 'select' => (object) [
                    //     0 => (object) [
                    //         'label' => 'Peratusan Tuntutan',
                    //         'placeholder' => 'Sila Pilih Peratusan Tuntutan',
                    //         'name' => 'invoice_division',
                    //         'type' => 'default',
                    //         'option' =>[
                    //             "0" => (object) [
                    //                 "value" => '10',
                    //                 "name" => '10%',
                    //             ],
                    //             "1" => (object) [
                    //                 "value" => '40',
                    //                 "name" => '40%',
                    //             ],
                    //             "2" => (object) [
                    //                 "value" => '50',
                    //                 "name" => '50%',
                    //             ],
                    //             "3" => (object) [
                    //                 "value" => '100',
                    //                 "name" => '100%',
                    //             ],
                    //         ]
                    //     ],

                    // ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Invois',
                            'name' => 'dt_inv',
                            'type' => 'date',
                            'required' => true
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Tuntutan Semasa (%)',
                            'value' => $this->getInvPercent($systemId)->percent,
                            'name' => 'current_invoice_division',
                            'readonly' => 'readonly'
                        ],
                        1 => (object) [
                            'label' => 'Peratusan Tuntutan (%)',
                            'placeholder' => 'Sila Isi Peratusan Tuntutan',
                            'name' => 'invoice_division',
                            'required' => true
                        ],
                        2 => (object) [
                            'label' => 'Amaun Invois (RM)',
                            'placeholder' => 'Sila Isi Amaun Invois',
                            'name' => 'total_icpp',
                            'required' => true
                        ],
                        3 => (object) [
                            'label' => 'No Invois',
                            'placeholder' => 'Sila Isi No Invois',
                            'name' => 'no_icpp',
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 37:
        $mapping = (object) [
            '37' => (object) [
                'title' => 'Semakan Bayaran Invois Caj Perkhidmatan',
                'aside' => (object) [
                    "url" => $statusId === 37 ? General::getAttachment($systemId, 'RCPP')->url : null,
                    "mime" => $statusId === 37 ? General::getAttachment($systemId, 'RCPP')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Bayaran Invois?',
                            'name' => 'verify_rcpp',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    // case 41:
    //     $mapping = (object) [
    //         '41' => (object) [
    //             'title' => 'Lantikan Kumpulan Ukur PIU',
    //             'input' => (object) [
    //                 'select' => (object) [
    //                     0 => (object) [
    //                         'label' => 'Kategori',
    //                         'placeholder' => 'Sila Pilih Kategori',
    //                         'name' => 'survey-provider',
    //                         'type' => 'default',
    //                         'option' => [
    //                             "0"  => (object) [
    //                                 "value" => 'Inhouse',
    //                                 "name" => 'Inhouse',
    //                             ],
    //                             // "1" => (object) [
    //                             //     "value" => 'Outsource',
    //                             //     "name" => 'Outsource',
    //                             // ],
    //                         ],
    //                     ],
    //                     1 => (object) [
    //                         'label' => 'Kumpulan Ukur',
    //                         'placeholder' => 'Sila Pilih Kumpulan Ukur',
    //                         'name' => 'survey-team-assign',
    //                         'type' => 'images',
    //                         'option' => $statusId === 41  ? $this->selectUsers('team_survey'):null,
    //                     ],
    //                 ],
    //                 'date' => (object) [
    //                     0 => (object) [
    //                         'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
    //                         'name' => 'modal-date-range',
    //                         'type' => 'daterange'
    //                     ],
    //                 ],
    //                 // 'checkbox' => (object) [
    //                 //     0 => (object) [
    //                 //         'label' => 'Tanda Jika Perlu Surat Pengesahan Kedudukan Utiliti',
    //                 //         'name' => 'verify',
    //                 //         'value' => 1,
    //                 //         'type' => 'switch'
    //                 //     ],
    //                 // ],
    //                 'textarea' => (object) [
    //                     0 => (object) [
    //                         'label' => 'Catatan',
    //                         'name' => 'notes'
    //                     ]
    //                 ],
    //             ]
    //         ]
    //     ];
    //     break;
    // case 41:
    //     $mapping = (object) [
    //         '41' => (object) [
    //             'title' => 'Lantikan Tugasan Ukur PIU',
    //             'input' => (object) [
    //                 'select' => (object) [
    //                     0 => (object) [
    //                         'label' => 'Kategori',
    //                         'placeholder' => 'Sila Pilih Kategori',
    //                         'name' => 'survey-provider',
    //                         'type' => 'default',
    //                         'option' => [
    //                             "0" => (object) [
    //                                 "value" => 'Inhouse',
    //                                 "name" => 'Inhouse',
    //                             ],
    //                             "1" => (object) [
    //                                 "value" => 'Outsource',
    //                                 "name" => 'Outsource',
    //                             ],
    //                         ],
    //                     ],
    //                     1 => (object) [
    //                         'label' => 'Kumpulan Ukur',
    //                         'placeholder' => 'Sila Pilih Kumpulan Ukur',
    //                         'name' => 'survey-team-assign',
    //                         'type' => 'images',
    //                         'option' => $statusId === 41  ? $this->selectUsers('team_survey'):null,
    //                     ],
    //                     // 1 => (object) [
    //                     //     'label' => 'Nama Panel Ukur',
    //                     //     'placeholder' => 'Nama Panel Ukur',
    //                     //     'name' => 'survey-surveyor',
    //                     //     'type' => 'default',
    //                     //     'option' => $statusId === 41 ? $this->selectUsers('surveyor') : null,
    //                     // ],
    //                 ],
    //                 'date' => (object) [
    //                     0 => (object) [
    //                         'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
    //                         'name' => 'modal-date-range',
    //                         'type' => 'daterange'
    //                     ],
    //                 ],
    //                 // 'checkbox' => (object) [
    //                 //     0 => (object) [
    //                 //         'label' => 'Tanda Jika Perlu Surat Pengesahan Kedudukan Utiliti',
    //                 //         'name' => 'verify',
    //                 //         'value' => 1,
    //                 //         'type' => 'switch'
    //                 //     ],
    //                 // ],
    //                 'textarea' => (object) [
    //                     0 => (object) [
    //                         'label' => 'Catatan',
    //                         'name' => 'notes'
    //                     ]
    //                 ],
    //             ]
    //         ]
    //     ];
    //     break;
    case 42:
        $mapping = (object) [
            '42' => (object) [
                'title' => 'Pengesahan Kehadiran Kerja Lapangan (PIU)',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Status Cuaca',
                            'placeholder' => 'Sila Pilih Status Cuaca',
                            'name' => 'weather-status',
                            'type' => 'images',
                            'option' => (object) [
                                "0" => (object) [
                                    "value" => 'panas',
                                    "name" => 'Panas',
                                ],
                                "1" => (object) [
                                    "value" => 'hujan',
                                    "name" => 'Hujan',
                                ],
                            ],
                            'required' => true
                        ]
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Masa',
                            'name' => 'clock-in',
                            'type' => 'time',
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
            ]
        ];
        break;
    case 43:
        $mapping = (object) [
            '43' => (object) [
                'title' => 'Pengesahan Waktu Masuk Kerja Lapangan (PIU)',
                'aside' => (object) [
                    "url" => $this->system->App->url . '/attendance/survey/' . $systemId,
                    "type" => 'qrcode',
                ],
                'input' => (object) [
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 44:
        $mapping = (object) [
            '44' => (object) [
                'title' => 'Pengesahan Waktu Keluar Kerja Lapangan (PIU)',
                'input' => (object) [
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Masa Keluar',
                            'name' => 'clock-out',
                            'type' => 'time',
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
            ]
        ];
        break;
    case 46:
        $mapping = (object) [
            '46' => (object) [
                'title' => 'Serahan Data Pengesanan Data Utiliti (PIU)',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Kerja Lapangan',
                            'folder' => 'RDPIU',
                            'type' => '.pdf,.dwg, .dxf'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 49:
        $mapping = (object) [
            '49' => (object) [
                'title' => 'Perlantikan Pelukis PIU',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Pelukis Pelan',
                            'placeholder' => 'Sila Pilih Pelukis Pelan',
                            'name' => 'plan-assign-udm',
                            'type' => 'images',
                            'option' => $statusId === 49 ? $this->selectUsers('plan') : null,
                            'required' => true
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
                            'name' => 'modal-date-range',
                            'type' => 'daterange',
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
            ]
        ];
        break;
    case 50:
        $mapping = (object) [
            '50' => (object) [
                'title' => 'Kemaskini Kemajuan PIU Harian',
                'input' => (object) [
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jarak Ukur (meter)',
                            'placeholder' => intval(Survey::currentProgressUdm($systemId)),
                            'name' => 'distance-survey',
                            'readonly' => 'readonly'
                        ],
                        1 => (object) [
                            'label' => 'Progress Terkini (meter)',
                            'placeholder' => intval(Survey::getProgressUDM($systemId)),
                            'name' => 'current-progress-udm',
                            'readonly' => 'readonly'
                        ],
                        2 => (object) [
                            'label' => 'Progress Hari Ini (meter)',
                            'name' => 'progress-daily-udm',
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
            ]
        ];
        break;
    case 51:
        $mapping = (object) [
            '51' => (object) [
                'title' => 'Perlantikan Pelukis PPT',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Pelukis Pelan',
                            'placeholder' => 'Sila Pilih Pelukis Pelan',
                            'name' => 'plan-assign-tmp',
                            'type' => 'images',
                            'option' => $statusId === 51 ? $this->selectUsers('plan') : null,
                            'required' => true
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
                            'name' => 'modal-date-range',
                            'type' => 'daterange',
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
            ]
        ];
        break;
    case 52:
        $mapping = (object) [
            '52' => (object) [
                'title' => 'Kemaskini Kemajuan PPT Harian',
                'input' => (object) [
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jarak Ukur',
                            'placeholder' => intval(Survey::currentProgressUdm($systemId)),
                            'name' => 'distance-survey',
                            'readonly' => 'readonly'
                        ],
                        1 => (object) [
                            'label' => 'Progress Terkini',
                            'placeholder' => intval(Survey::getProgressTMP($systemId)),
                            'name' => 'current-progress-tmp',
                            'readonly' => 'readonly'
                        ],
                        2 => (object) [
                            'label' => 'Progress Hari Ini',
                            'name' => 'progress-daily-tmp',
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
            ]
        ];
        break;
    case 53:
        $mapping = (object) [
            '53' => (object) [
                'title' => 'Penetapan Tarikh Endorsement',
                'input' => (object) [
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh',
                            'name' => 'sr-arrival-date',
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
            ]
        ];
        break;
    case 54:
        $mapping = (object) [
            '54' => (object) [
                'title' => 'Pengesahan Cop Bilangan Endorsement',
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => '1',
                            'name' => 'set-sr-sign',
                            'value' => 1,
                            'type' => 'radio'
                        ],
                        1 => (object) [
                            'label' => '2',
                            'name' => 'set-sr-sign',
                            'value' => 2,
                            'type' => 'radio'
                        ],
                        2 => (object) [
                            'label' => '3',
                            'name' => 'set-sr-sign',
                            'value' => 3,
                            'type' => 'radio'
                        ],
                        3 => (object) [
                            'label' => '4',
                            'name' => 'set-sr-sign',
                            'value' => 4,
                            'type' => 'radio'
                        ],
                        4 => (object) [
                            'label' => '5',
                            'name' => 'set-sr-sign',
                            'value' => 5,
                            'type' => 'radio'
                        ],
                    ],
                    'checkbox_title' => 'Bilangan Set Pelan',
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 55:
        $surveyCategory = Survey::getCategorySvyUdm($systemId);

        if ($surveyCategory == 2) {
            $mapping = (object) [
                '55' => (object) [
                    'title' => 'Muatnaik PIU',
                    'input' => (object) [
                        'upload' => (object) [
                            0 => (object) [
                                'label' => 'Pelan Infrastruktur Utiliti',
                                'folder' => 'PIU',
                                'type' => '.pdf'
                            ],
                            1 => (object) [
                                'label' => 'Pelan Infrastruktur Utiliti',
                                'folder' => 'PIU',
                                'type' => '.zip'
                            ],
                        ],
                        'date' => (object) [
                            0 => (object) [
                                'label' => 'Tarikh Endorsement',
                                'name' => 'sr-arrival-date',
                                'type' => 'date',
                                'required' => true
                            ],
                        ],
                        'text' => (object) [
                            0 => (object) [
                                'label' => 'Nama Panel',
                                'name' => 'survey-panel-name',
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
                ]
            ];
        } else {
            $mapping = (object) [
                '55' => (object) [
                    'title' => 'Muatnaik PIU',
                    'input' => (object) [
                        'upload' => (object) [
                            0 => (object) [
                                'label' => 'Pelan Infrastruktur Utiliti',
                                'folder' => 'PIU',
                                'type' => '.pdf'
                            ],
                            1 => (object) [
                                'label' => 'Pelan Infrastruktur Utiliti',
                                'folder' => 'PIU',
                                'type' => '.zip'
                            ],
                        ],
                        'textarea' => (object) [
                            0 => (object) [
                                'label' => 'Catatan',
                                'name' => 'notes'
                            ]
                        ],
                    ]
                ]
            ];
        }
        break;
    case 155:
        $mapping = (object) [
            '155' => (object) [
                'title' => 'Muatnaik PPT',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pelan Kawalan Trafik',
                            'folder' => 'PPT',
                            'type' => '.pdf'
                        ],
                        1 => (object) [
                            'label' => 'Pelan Kawalan Trafik',
                            'folder' => 'PPT',
                            'type' => '.zip'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 56:
        $mapping = (object) [
            '56' => (object) [
                'title' => 'Serahan PIU & PPT',
                'input' => (object) [
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh',
                            'name' => 'handover',
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
            ]
        ];
        break;
    case 58:
        $mapping = (object) [
            '58' => (object) [
                'title' => 'Muatnaik Pembetulan PIU',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pelan Infrastruktur Utiliti',
                            'folder' => 'PIU',
                            'type' => '.pdf'
                        ],
                        1 => (object) [
                            'label' => 'Pelan Infrastruktur Utiliti',
                            'folder' => 'PIU',
                            'type' => '.zip'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Catatan Pegawai Pelan',
                            'placeholder' => Survey::getRemarkUDM($systemId),
                            'name' => 'plan_remark',
                            'readonly' => 'readonly'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 60:
        $mapping = (object) [
            '60' => (object) [
                'title' => 'Muatnaik Pembetulan PPT',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pelan Kawalan Trafik',
                            'folder' => 'PPT',
                            'type' => '.pdf'
                        ],
                        1 => (object) [
                            'label' => 'Pelan Kawalan Trafik',
                            'folder' => 'PPT',
                            'type' => '.zip'
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Catatan Pegawai Pelan',
                            'placeholder' => Survey::getRemarkTMP($systemId),
                            'name' => 'plan_remark',
                            'readonly' => 'readonly'
                        ]
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 65:
        $mapping = (object) [
            '65' => (object) [
                'title' => 'Muat Naik Bukti Pembayaran Invois 50% (GIS Ready)',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Bukti Pembayaran Invois',
                            'folder' => 'RCPP',
                            'type' => '.pdf'
                        ],
                    ],
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Kaedah Pembayaran',
                            'placeholder' => 'Sila Pilih Kaedah Pembayaran',
                            'name' => 'payment-method-inv',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Pemindahan_Bank_Segera',
                                    "name" => 'Pemindahan Bank Segera',
                                ],
                                "1" => (object) [
                                    "value" => 'Jaminan_bank',
                                    "name" => 'Jaminan Bank',
                                ],
                                "2" => (object) [
                                    "value" => 'Cek',
                                    "name" => 'Cek',
                                ],
                                "3" => (object) [
                                    "value" => 'Tunai',
                                    "name" => 'Tunai',
                                ],
                            ],
                            'required' => true
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Bayaran (RM)',
                            'name' => 'total-rcpp',
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
            ]
        ];
        break;
    case 66:
        $mapping = (object) [
            '66' => (object) [
                'title' => 'Muat Naik Bukti Pembayaran Invois 40% (GIS Ready)',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Bukti Pembayaran Invois',
                            'folder' => 'RCPP',
                            'type' => '.pdf'
                        ],
                    ],
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Kaedah Pembayaran',
                            'placeholder' => 'Sila Pilih Kaedah Pembayaran',
                            'name' => 'payment-method-inv',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Pemindahan_Bank_Segera',
                                    "name" => 'Pemindahan Bank Segera',
                                ],
                                "1" => (object) [
                                    "value" => 'Jaminan_bank',
                                    "name" => 'Jaminan Bank',
                                ],
                                "2" => (object) [
                                    "value" => 'Cek',
                                    "name" => 'Cek',
                                ],
                                "3" => (object) [
                                    "value" => 'Tunai',
                                    "name" => 'Tunai',
                                ],
                            ],
                            'required' => true
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Bayaran (RM)',
                            'name' => 'total-rcpp',
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
            ]
        ];
        break;
    case 75:
        $mapping = (object) [
            '75' => (object) [
                'title' => 'Semakan Bayaran Invois Caj Perkhidmatan 40%',
                'aside' => (object) [
                    "url" => $statusId === 75 ? General::getAttachment($systemId, 'RCPP')->url : null,
                    "mime" => $statusId === 75 ? General::getAttachment($systemId, 'RCPP')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Bayaran Invois?',
                            'name' => 'verify',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 67:
        $mapping = (object) [
            '67' => (object) [
                'title' => 'Muat Naik Dokumen Permohonan Permit Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Dokumen Permohonan Permit Kerja',
                            'folder' => 'SRPKPK',
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
            ]
        ];
        break;
    case 77:
        $mapping = (object) [
            '77' => (object) [
                'title' => 'Semakan Notis Mula Kerja',
                'aside' => (object) [
                    "url" => $statusId === 77 ? General::getAttachment($systemId, 'NMK')->url : null,
                    "mime" => $statusId === 77 ? General::getAttachment($systemId, 'NMK')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Notis Mula Kerja?',
                            'name' => 'verify',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 79:
        $mapping = (object) [
            '79' => (object) [
                'title' => 'Muat Naik Notis Mula Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Notis Mula Kerja',
                            'folder' => 'NMK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_ws_ltr_created',
                            'type' => 'date',
                            'required' => true
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_ws_ltr_received',
                            'type' => 'date',
                            'required' => true
                        ],
                        2 => (object) [
                            'label' => 'Tarikh Mula Kerja',
                            'name' => 'dt_ws',
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
            ]
        ];
        break;
    case 86:
        $mapping = (object) [
            '86' => (object) [
                'title' => 'Semakan Notis Siap Kerja',
                'aside' => (object) [
                    "url" => $statusId === 86 ? General::getAttachment($systemId, 'NSK')->url : null,
                    "mime" => $statusId === 86 ? General::getAttachment($systemId, 'NSK')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Notis Siap Kerja?',
                            'name' => 'verify',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 88:
        $mapping = (object) [
            '88' => (object) [
                'title' => 'Muat Naik Permohonan Notis Siap Kerja',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Notis Siap Kerja',
                            'folder' => 'NSK',
                            'type' => '.pdf'
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh Surat',
                            'name' => 'dt_wf_ltr_created',
                            'type' => 'date',
                            'required' => true
                        ],
                        1 => (object) [
                            'label' => 'Tarikh Terima',
                            'name' => 'dt_wf_ltr_received',
                            'type' => 'date',
                            'required' => true
                        ],
                        2 => (object) [
                            'label' => 'Tarikh Akhir Kerja',
                            'name' => 'dt_wf',
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
            ]
        ];
        break;
    case 89:
        $mapping = (object) [
            '89' => (object) [
                'title' => 'Semakan Permohonan Lanjutan Permit Kerja',
                'aside' => (object) [
                    "url" => $statusId === 89 ? General::getAttachment($systemId, 'PLPK')->url : null,
                    "mime" => $statusId === 89 ? General::getAttachment($systemId, 'PLPK')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Permohonan Lanjutan Permit Kerja?',
                            'name' => 'verify',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 97:
        $mapping = (object) [
            '97' => (object) [
                'title' => 'Lantikan Kumpulan Ukur PSB',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Kategori',
                            'placeholder' => 'Sila Pilih Kategori',
                            'name' => 'survey-provider',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Inhouse',
                                    "name" => 'Inhouse',
                                ],
                                // "1" => (object) [
                                //     "value" => 'Outsource',
                                //     "name" => 'Outsource',
                                // ],
                            ],
                            'required' => true
                        ],
                        1 => (object) [
                            'label' => 'Kumpulan Ukur',
                            'placeholder' => 'Sila Pilih Kumpulan Ukur',
                            'name' => 'survey-team-assign',
                            'type' => 'images',
                            'option' => $statusId === 97 ? $this->selectUsers('team_survey') : null,
                            'required' => true
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
                            'name' => 'modal-date-range',
                            'type' => 'daterange',
                            'required' => true
                        ],
                    ],
                    // 'checkbox' => (object) [
                    //     0 => (object) [
                    //         'label' => 'Tanda Jika Perlu Surat Pengesahan Kedudukan Utiliti',
                    //         'name' => 'verify',
                    //         'value' => 1,
                    //         'type' => 'switch'
                    //     ],
                    // ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 98:
        $mapping = (object) [
            '98' => (object) [
                'title' => 'Pengesahan Kehadiran Kerja Lapangan (PSB)',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Status Cuaca',
                            'placeholder' => 'Sila Pilih Status Cuaca',
                            'name' => 'weather-status',
                            'type' => 'images',
                            'option' => (object) [
                                "0" => (object) [
                                    "value" => 'panas',
                                    "name" => 'Panas',
                                ],
                                "1" => (object) [
                                    "value" => 'hujan',
                                    "name" => 'Hujan',
                                ],
                            ],
                            'required' => true
                        ],
                        1 => (object) [
                            'label' => 'Bilangan Ahli Kumpulan',
                            'placeholder' => 'Sila Pilih Bilangan Ahli Kumpulan',
                            'name' => 'number-team',
                            'type' => 'default',
                            'option' => (object) [
                                "0" => (object) [
                                    "value" => '3',
                                    "name" => '3',
                                ],
                                "1" => (object) [
                                    "value" => '4',
                                    "name" => '4',
                                ],
                            ],
                            'required' => true
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Masa',
                            'name' => 'clock-in',
                            'type' => 'time',
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
            ]
        ];
        break;
    case 99:
        $mapping = (object) [
            '99' => (object) [
                'title' => 'Pengesahan Waktu Masuk Kerja Lapangan (PSB)',
                'aside' => (object) [
                    "url" => $this->system->App->url . '/attendance/survey/' . $systemId,
                    "type" => 'qrcode',
                ],
                'input' => (object) [
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 100:
        $mapping = (object) [
            '100' => (object) [
                'title' => 'Pengesahan Waktu Keluar Kerja Lapangan (PSB)',
                'input' => (object) [
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Masa Keluar',
                            'name' => 'clock-out',
                            'type' => 'time',
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
            ]
        ];
        break;
    case 102:
        $mapping = (object) [
            '102' => (object) [
                'title' => 'Serahan Data Pengesanan Data Utiliti (PSB)',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Kerja Lapangan',
                            'folder' => 'RDPSB',
                            'type' => '.pdf,.dwg, .dxf'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 105:
        $mapping = (object) [
            '105' => (object) [
                'title' => 'Perlantikan Pelukis PSB',
                'input' => (object) [
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Pelukis Pelan',
                            'placeholder' => 'Sila Pilih Pelukis Pelan',
                            'name' => 'plan-assign-asb',
                            'type' => 'images',
                            'option' => $statusId === 105 ? $this->selectUsers('plan') : null,
                            'required' => true
                        ],
                    ],
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
                            'name' => 'modal-date-range',
                            'type' => 'daterange',
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
            ]
        ];
        break;
    case 106:
        $mapping = (object) [
            '106' => (object) [
                'title' => 'Kemaskini Kemajuan PSB Harian',
                'input' => (object) [
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jarak Ukur (meter)',
                            'placeholder' => intval(Survey::currentProgressASB($systemId)),
                            'name' => 'distance-survey',
                            'readonly' => 'readonly'
                        ],
                        1 => (object) [
                            'label' => 'Progress Terkini (meter)',
                            'placeholder' => intval(Survey::getProgressASB($systemId)),
                            'name' => 'current-progress-asb',
                            'readonly' => 'readonly'
                        ],
                        2 => (object) [
                            'label' => 'Progress Hari Ini (meter)',
                            'name' => 'progress-daily-asb',
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
            ]
        ];
        break;
    case 107:
        $mapping = (object) [
            '107' => (object) [
                'title' => 'Penetapan Tarikh Endorsement',
                'input' => (object) [
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh',
                            'name' => 'sr-arrival-date',
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
            ]
        ];
        break;
    case 108:
        $mapping = (object) [
            '108' => (object) [
                'title' => 'Pengesahan Cop Bilangan Endorsement (PSB)',
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => '1',
                            'name' => 'set-sr-sign',
                            'value' => 1,
                            'type' => 'radio'
                        ],
                        1 => (object) [
                            'label' => '2',
                            'name' => 'set-sr-sign',
                            'value' => 2,
                            'type' => 'radio'
                        ],
                        2 => (object) [
                            'label' => '3',
                            'name' => 'set-sr-sign',
                            'value' => 3,
                            'type' => 'radio'
                        ],
                        3 => (object) [
                            'label' => '4',
                            'name' => 'set-sr-sign',
                            'value' => 4,
                            'type' => 'radio'
                        ],
                        4 => (object) [
                            'label' => '5',
                            'name' => 'set-sr-sign',
                            'value' => 5,
                            'type' => 'radio'
                        ],
                    ],
                    'checkbox_title' => 'Bilangan Set Pelan',
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 109:
        $mapping = (object) [
            '109' => (object) [
                'title' => 'Muat Naik Pelan Siap Bina',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Pelan Siap Bina',
                            'folder' => 'PSB',
                            'type' => '.pdf'
                        ],
                        1 => (object) [
                            'label' => 'Pelan Siap Bina',
                            'folder' => 'PSB',
                            'type' => '.dwg'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],
                ]
            ]
        ];
        break;
    case 110:
        $mapping = (object) [
            '110' => (object) [
                'title' => 'Serahan PSB',
                'input' => (object) [
                    'date' => (object) [
                        0 => (object) [
                            'label' => 'Tarikh',
                            'name' => 'handover',
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
            ]
        ];
        break;
    case 117:
        $mapping = (object) [
            '117' => (object) [
                'title' => 'Muat Naik Bukti Pembayaran Invois 10% (GIS Ready)',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Bukti Pembayaran Invois',
                            'folder' => 'RCPP',
                            'type' => '.pdf'
                        ],
                    ],
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Kaedah Pembayaran',
                            'placeholder' => 'Sila Pilih Kaedah Pembayaran',
                            'name' => 'payment-method-inv',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Pemindahan_Bank_Segera',
                                    "name" => 'Pemindahan Bank Segera',
                                ],
                                "1" => (object) [
                                    "value" => 'Jaminan_bank',
                                    "name" => 'Jaminan Bank',
                                ],
                                "2" => (object) [
                                    "value" => 'Cek',
                                    "name" => 'Cek',
                                ],
                                "3" => (object) [
                                    "value" => 'Tunai',
                                    "name" => 'Tunai',
                                ],
                            ],
                            'required' => true
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Bayaran (RM)',
                            'name' => 'total-rcpp',
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
            ]
        ];
        break;
    case 118:
        $mapping = (object) [
            '118' => (object) [
                'title' => 'Muat Naik Dokumen Permohonan Sijil Siap Kerja',
                'input' => (object) [
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
                            'name' => 'notes'
                        ]
                    ],
                ]
            ],
        ];
        break;
    case 124:
        $mapping = (object) [
            '124' => (object) [
                'title' => 'Muat Naik Bukti Pembayaran Invois Caj Perkhidmatan 10%',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Bukti Pembayaran Invois',
                            'folder' => 'RCPP',
                            'type' => '.pdf'
                        ],
                    ],
                    'select' => (object) [
                        0 => (object) [
                            'label' => 'Kaedah Pembayaran',
                            'placeholder' => 'Sila Pilih Kaedah Pembayaran',
                            'name' => 'payment-method-inv',
                            'type' => 'default',
                            'option' => [
                                "0" => (object) [
                                    "value" => 'Pemindahan_Bank_Segera',
                                    "name" => 'Pemindahan Bank Segera',
                                ],
                                "1" => (object) [
                                    "value" => 'Jaminan_bank',
                                    "name" => 'Jaminan Bank',
                                ],
                                "2" => (object) [
                                    "value" => 'Cek',
                                    "name" => 'Cek',
                                ],
                                "3" => (object) [
                                    "value" => 'Tunai',
                                    "name" => 'Tunai',
                                ],
                            ],
                            'required' => true
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Jumlah Bayaran (RM)',
                            'name' => 'total-rcpp',
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
            ]
        ];
        break;
    case 135:
        $mapping = (object) [
            '135' => (object) [
                'title' => 'Semakan Dokumen Permohonan CMGD',
                'aside' => (object) [
                    "url" => $statusId === 135 ? General::getAttachment($systemId, 'SRPSSMK')->url : null,
                    "mime" => $statusId === 135 ? General::getAttachment($systemId, 'SRPSSMK')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Dokumen Permohonan CMGD?',
                            'name' => 'verify',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],

                ]
            ]
        ];
        break;
    case 144:
        $mapping = (object) [
            '144' => (object) [
                'title' => 'Muat Naik Dokumen Permohonan PWC',
                'input' => (object) [
                    'upload' => (object) [
                        0 => (object) [
                            'label' => 'Dokumen Permohonan PWC',
                            'folder' => 'SRPWC',
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
            ]
        ];
        break;
    case 145:
        $mapping = (object) [
            '145' => (object) [
                'title' => 'Semakan Dokumen Permohonan PWC',
                'aside' => (object) [
                    "url" => $statusId === 145 ? General::getAttachment($systemId, 'SRPWC')->url : null,
                    "mime" => $statusId === 145 ? General::getAttachment($systemId, 'SRPWC')->mime_type : null,
                    "type" => 'attachment',
                ],
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Sahkan Dokumen Permohonan PWC?',
                            'name' => 'verify',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],

                ]
            ]
        ];
        break;
    case 158:
        $mapping = (object) [
            '158' => (object) [
                'title' => 'Pengesahan Pengecualian Caj Perkhidmatan',
                'input' => (object) [
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Adakah Permohonan ini Dikecualikan Caj Perkhidmatan?',
                            'name' => 'verify_service_charge',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],

                ]
            ]
        ];
        break;
    case 151:
        $notesCancel = Permitting::getCancellationNotes($systemId);
        $mapping = (object) [
            '151' => (object) [
                'title' => 'Pengesahan Pembatalan Permohonan',
                'input' => (object) [
                    'note' => (object) [
                        0 => (object) [
                            'label' => 'Nota Pembatalan Permohonan',
                            'note' => $notesCancel['cancellation_notes'],
                        ],
                    ],
                    'checkbox' => (object) [
                        0 => (object) [
                            'label' => 'Adakah permohonan ini boleh dibatalkan?',
                            'name' => 'verify_cancel_application',
                            'value' => 1,
                            'type' => 'switch'
                        ],
                    ],
                    'textarea' => (object) [
                        0 => (object) [
                            'label' => 'Catatan',
                            'name' => 'notes'
                        ]
                    ],

                ]
            ]
        ];
        break;
    case 160:
        $notesCancel = Permitting::getCancellationNotes($systemId);
        $mapping = (object) [
            '160' => (object) [
                'title' => 'Pengesahan No Rujukan',
                'input' => (object) [
                    'note' => (object) [
                        0 => (object) [
                            'label' => 'Tajuk Permohonan',
                            'note' => $notesCancel['project_title'],
                        ],
                    ],
                    'text' => (object) [
                        0 => (object) [
                            'label' => 'Cadangan No Rujukan',
                            'name' => 'no_ref',
                            'value' => $notesCancel['reference_no'],
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
            ]
        ];
        break;
    default:
        $mapping = (object) [

        ];
        break;
}
;

// $mapping = (object) [
//     '1' => (object) [
//         'title' => 'Muatnaik Invois Caj Pendaftaran',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Invois Caj Pendaftaran',
//                     'folder' => 'ICP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Invois',
//                     'name' => 'dt_inv',
//                     'type' => 'date',
//                     'required' => true
//                 ],

//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '2' => (object) [
//         'title' => 'Semakan Invois Caj Pendaftaran',
//         'aside' => (object) [
//             "url" => $statusId === 2  ? General::getAttachment($systemId, 'RCP')->url : null,
//             "mime" => $statusId === 2 ? General::getAttachment($systemId, 'RCP')->mime_type : null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Terima Caj Pendaftaran?',
//                     'name' => 'verify_rcp',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],

//         ]
//     ],
//     '6' => (object) [
//         'title' => 'Muat Naik Arahan Kerja PIL',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Arahan Kerja PIL',
//                     'folder' => 'AKP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Amaun Arahan Kerja (RM)',
//                     'name' => 'total_wop'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '7' => (object) [
//         'title' => 'Lantikan Pelukis Pelan Surihan',
//         'input' => (object) [
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Pegawai GIS',
//                     'placeholder' => 'Sila Pilih Pegawai GIS',
//                     'name' => 'gis_assign',
//                     'type' => 'images',
//                     'option' => $statusId === 7  ? $this->selectUsers('charting'):null,
//                 ],
//             ],
//             // 'text' => (object) [
//             //     0 => (object) [
//             //         'label' => 'Tempoh Masa Perlu Disiapkan',
//             //         'name' => 'delivery_date',
//             //         'style' => 'transparent',
//             //         'readonly' => true,
//             //         'attribute' => 'data-estimated'
//             //     ]
//             // ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '8' => (object) [
//         'title' => 'Muat Naik Pelan Cadangan Laluan',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Pelan Cadangan Laluan',
//                     'folder' => 'PCL',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '9' => (object) [
//         'title' => 'Muat Naik Arahan Kerja Operasi',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Arahan Kerja Operasi',
//                     'folder' => 'AKO',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Amaun Arahan Kerja (RM)',
//                     'name' => 'total_woo'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '24' => (object) [
//         'title' => 'Muat Naik Pelan Izin Lalu',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Pelan Izin Lalu',
//                     'folder' => 'PIL',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '27' => (object) [
//         'title' => 'Muat Naik Sebut Harga',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Sebut Harga',
//                     'folder' => 'SH',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Plan UDM',
//                     'name' => 'check_udm',
//                     'value' => 1,
//                     'type' => 'checkbox'
//                 ],
//                 1 => (object) [
//                     'label' => 'Plan TMP',
//                     'name' => 'check_tmp',
//                     'value' => 1,
//                     'type' => 'checkbox'
//                 ],
//                 2 => (object) [
//                     'label' => 'Plan As Built',
//                     'name' => 'check_asbuilt',
//                     'value' => 1,
//                     'type' => 'checkbox'
//                 ],
//             ],
//             'checkbox_title' => 'Sila tanda caj perkhidmatan yang berkaitan',
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Amaun Sebut Harga (RM)',
//                     'name' => 'total_quote'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '28' => (object) [
//         'title' => 'Semakan Sebut Harga Disetujui',
//         'aside' => (object) [
//             "url" => $statusId === 28  ? General::getAttachment($systemId, 'SHD')->url:null,
//             "mime" => $statusId === 28  ? General::getAttachment($systemId, 'SHD')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Sebut Harga?',
//                     'name' => 'attach_agreed',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '35' => (object)[
//         'title' => 'Muat Naik Bukti Pembayaran Invois',
//         'input' => (object)[
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Bukti Pembayaran Invois',
//                     'folder' => 'RCPP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Kategori Invois',
//                     'placeholder' => 'Sila Pilih Kategori Invois',
//                     'name' => 'invoice_category',
//                     'type' => 'default',
//                     'option' =>[
//                         "0" => (object) [
//                             "value" => 'Perkhidmatan',
//                             "name" => 'Perkhidmatan',
//                         ],
//                         "1" => (object) [
//                             "value" => 'GIS_Ready',
//                             "name" => 'GIS_Ready',
//                         ],
//                     ]
//                 ],
//                 1 => (object) [
//                     'label' => 'Peratusan Tuntutan',
//                     'placeholder' => 'Sila Pilih Peratusan Tuntutan',
//                     'name' => 'invoice_division',
//                     'type' => 'default',
//                     'option' =>[
//                         "0" => (object) [
//                             "value" => '10',
//                             "name" => '10%',
//                         ],
//                         "1" => (object) [
//                             "value" => '40',
//                             "name" => '40%',
//                         ],
//                         "2" => (object) [
//                             "value" => '50',
//                             "name" => '50%',
//                         ],
//                         "3" => (object) [
//                             "value" => '100',
//                             "name" => '100%',
//                         ],
//                     ]
//                 ],
//                 2 => (object) [
//                     'label' => 'Kaedah Pembayaran',
//                     'placeholder' => 'Sila Pilih Kaedah Pembayaran',
//                     'name' => 'payment_method',
//                     'type' => 'default',
//                     'option' =>[
//                         "0" => (object) [
//                             "value" => 'Pemindahan_Bank_Segera',
//                             "name" => 'Pemindahan Bank Segera',
//                         ],
//                         "1" => (object) [
//                             "value" => 'Jaminan_bank',
//                             "name" => 'Jaminan Bank',
//                         ],
//                         "2" => (object) [
//                             "value" => 'Cek',
//                             "name" => 'Cek',
//                         ],
//                         "3" => (object) [
//                             "value" => 'Tunai',
//                             "name" => 'Tunai',
//                         ],
//                     ]
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Bayaran (RM)',
//                     'name' =>  'total_rcpp'
//                 ],
//                 1 => (object) [
//                     'label' => 'Nama Pembayar',
//                     'name' =>  'payee'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' =>  'notes'
//                 ]
//             ],
//         ]
//     ],
//     '36' => (object)[
//         'title' => 'Muat Naik Invois Caj Perkhidmatan',
//         'input' => (object)[
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Invois Caj Perkhidmatan',
//                     'folder' => 'ICPP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Peratusan Tuntutan',
//                     'placeholder' => 'Sila Pilih Peratusan Tuntutan',
//                     'name' => 'invoice_division',
//                     'type' => 'default',
//                     'option' =>[
//                         "0" => (object) [
//                             "value" => '10',
//                             "name" => '10%',
//                         ],
//                         "1" => (object) [
//                             "value" => '40',
//                             "name" => '40%',
//                         ],
//                         "2" => (object) [
//                             "value" => '50',
//                             "name" => '50%',
//                         ],
//                         "3" => (object) [
//                             "value" => '100',
//                             "name" => '100%',
//                         ],
//                     ]
//                 ],

//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Invois',
//                     'name' => 'dt_inv',
//                     'type' => 'date',
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Amaun Invois (RM)',
//                     'name' =>  'total_icpp'
//                 ],
//                 1 => (object) [
//                     'label' => 'No Invois',
//                     'name' =>  'no_icpp'
//                 ],

//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' =>  'notes'
//                 ]
//             ],
//         ]
//     ],
//     '37' => (object)[
//         'title' => 'Semakan Bayaran Invois Caj Perkhidmatan',
//         'aside' => (object) [
//             "url" => $statusId === 37  ? General::getAttachment($systemId, 'RCPP')->url:null,
//             "mime" => $statusId === 37  ? General::getAttachment($systemId, 'RCPP')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object)[
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Bayaran Invois?',
//                     'name' => 'verify_rcpp',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' =>  'notes'
//                 ]
//             ],
//         ]
//     ],
//     '41' => (object) [
//         'title' => 'Lantikan Kumpulan Ukur PIU',
//         'input' => (object) [
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Kumpulan Ukur',
//                     'placeholder' => 'Sila Pilih Kumpulan Ukur',
//                     'name' => 'survey-team-assign',
//                     'type' => 'images',
//                     'option' => $statusId === 41  ? $this->selectUsers('team_survey'):null,
//                 ],
//                 // 1 => (object) [
//                 //     'label' => 'Ketua Kumpulan Ukur',
//                 //     'placeholder' => 'Sila Pilih Ketua Kumpulan Ukur',
//                 //     'name' => 'survey-leader-assign',
//                 //     'type' => 'images',
//                 //     'option' => $this->selectUsers('survey'),
//                 // ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
//                     'name' => 'modal-date-range',
//                     'type' => 'daterange'
//                 ],
//             ],
//             // 'checkbox' => (object) [
//             //     0 => (object) [
//             //         'label' => 'Tanda Jika Perlu Surat Pengesahan Kedudukan Utiliti',
//             //         'name' => 'verify',
//             //         'value' => 1,
//             //         'type' => 'switch'
//             //     ],
//             // ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '42' => (object) [
//         'title' => 'Pengesahan Kehadiran Kerja Lapangan (PIU)',
//         'input' => (object) [
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Status Cuaca',
//                     'placeholder' => 'Sila Pilih Status Cuaca',
//                     'name' => 'weather-status',
//                     'type' => 'images',
//                     'option' => (object) [
//                         "0" => (object) [
//                             "value" => 'panas',
//                             "name" => 'Panas',
//                         ],
//                         "1" => (object) [
//                             "value" => 'sejuk',
//                             "name" => 'Sejuk',
//                         ],
//                     ]
//                 ],
//                 1 => (object) [
//                     'label' => 'Bilangan Ahli Kumpulan',
//                     'placeholder' => 'Sila Pilih Bilangan Ahli Kumpulan',
//                     'name' => 'number-team',
//                     'type' => 'default',
//                     'option' => (object) [
//                         "0" => (object) [
//                             "value" => '3',
//                             "name" => '3',
//                         ],
//                         "1" => (object) [
//                             "value" => '4',
//                             "name" => '4',
//                         ],
//                     ]
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Masa',
//                     'name' => 'clock-in',
//                     'type' => 'time'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '43' => (object) [
//         'title' => 'Pengesahan Waktu Masuk Kerja Lapangan (PIU)',
//         'aside' => (object) [
//             "url" => 'https://ucidos.test/attendance/survey/' . $systemId,
//             "type" => 'qrcode',
//         ],
//         'input' => (object) [
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '44' => (object) [
//         'title' => 'Pengesahan Waktu Keluar Kerja Lapangan (PIU)',
//         'input' => (object) [
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Masa Keluar',
//                     'name' => 'clock-out',
//                     'type' => 'time'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '46' => (object) [
//         'title' => 'Serahan Data Pengesanan Data Utiliti (PIU)',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Kerja Lapangan',
//                     'folder' => 'RDPIU',
//                     'type' => '.pdf,.dwg'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '49' => (object) [
//         'title' => 'Perlantikan Pelukis PIU',
//         'input' => (object) [
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Pelukis Pelan',
//                     'placeholder' => 'Sila Pilih Pelukis Pelan',
//                     'name' => 'plan-assign-udm',
//                     'type' => 'images',
//                     'option' => $statusId === 49  ? $this->selectUsers('plan'):null,
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
//                     'name' => 'modal-date-range',
//                     'type' => 'daterange'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '50' => (object) [
//         'title' => 'Kemaskini Kemajuan PIU Harian',
//         'input' => (object) [
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jarak Ukur',
//                     'placeholder' => intval(Survey::currentProgressUdm($systemId)),
//                     'name' => 'distance-survey',
//                     'readonly' => 'readonly'
//                 ],
//                 1 => (object) [
//                     'label' => 'Progress Terkini',
//                     'placeholder' => intval(Survey::getProgressUDM($systemId)),
//                     'name' => 'current-progress-udm',
//                     'readonly' => 'readonly'
//                 ],
//                 2 => (object) [
//                     'label' => 'Progress Hari Ini',
//                     'name' => 'progress-daily-udm'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '51' => (object) [
//         'title' => 'Perlantikan Pelukis PPT',
//         'input' => (object) [
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Pelukis Pelan',
//                     'placeholder' => 'Sila Pilih Pelukis Pelan',
//                     'name' => 'plan-assign-tmp',
//                     'type' => 'images',
//                     'option' => $statusId === 51  ? $this->selectUsers('plan'):null,
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Julat Tarikh Jangkaan Mula - Tamat',
//                     'name' => 'modal-date-range',
//                     'type' => 'daterange'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '52' => (object) [
//         'title' => 'Kemaskini Kemajuan PPT Harian',
//         'input' => (object) [
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jarak Ukur',
//                     'placeholder' => intval(Survey::currentProgressUdm($systemId)),
//                     'name' => 'distance-survey',
//                     'readonly' => 'readonly'
//                 ],
//                 1 => (object) [
//                     'label' => 'Progress Terkini',
//                     'placeholder' => intval(Survey::getProgressTMP($systemId)),
//                     'name' => 'current-progress-tmp',
//                     'readonly' => 'readonly'
//                 ],
//                 2 => (object) [
//                     'label' => 'Progress Hari Ini',
//                     'name' => 'progress-daily-tmp'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '53' => (object) [
//         'title' => 'Penetapan Tarikh Endorsement',
//         'input' => (object) [
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh',
//                     'name' => 'sr-arrival-date',
//                     'type' => 'date'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],

//         ]
//     ],
//     '54' => (object) [
//         'title' => 'Pengesahan Cop Bilangan Endorsement',
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => '1',
//                     'name' => 'set-sr-sign',
//                     'value' => 1,
//                     'type' => 'radio'
//                 ],
//                 1 => (object) [
//                     'label' => '2',
//                     'name' => 'set-sr-sign',
//                     'value' => 2,
//                     'type' => 'radio'
//                 ],
//                 2 => (object) [
//                     'label' => '3',
//                     'name' => 'set-sr-sign',
//                     'value' => 3,
//                     'type' => 'radio'
//                 ],
//                 3 => (object) [
//                     'label' => '4',
//                     'name' => 'set-sr-sign',
//                     'value' => 4,
//                     'type' => 'radio'
//                 ],
//                 4 => (object) [
//                     'label' => '5',
//                     'name' => 'set-sr-sign',
//                     'value' => 5,
//                     'type' => 'radio'
//                 ],
//             ],
//             'checkbox_title' => 'Bilangan Set Pelan',
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '55' => (object) [
//         'title' => 'Muatnaik PIU & PPT',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Pelan Infrastruktur Utiliti',
//                     'folder' => 'PIU',
//                     'type' => '.pdf'
//                 ],
//                 1 => (object) [
//                     'label' => 'Pelan Infrastruktur Utiliti',
//                     'folder' => 'PIU',
//                     'type' => '.dwg'
//                 ],
//                 2 => (object) [
//                     'label' => 'Pelan Kawalan Trafik',
//                     'folder' => 'PPT',
//                     'type' => '.pdf'
//                 ],
//                 3 => (object) [
//                     'label' => 'Pelan Kawalan Trafik',
//                     'folder' => 'PPT',
//                     'type' => '.dwg'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '56' => (object) [
//         'title' => 'Serahan PIU & PPT',
//         'input' => (object) [
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh',
//                     'name' => 'handover',
//                     'type' => 'date'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '65' => (object) [
//         'title' => 'Muat Naik Bukti Pembayaran Invois 50% (GIS Ready)',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Bukti Pembayaran Invois',
//                     'folder' => 'RCPP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Kaedah Pembayaran',
//                     'placeholder' => 'Sila Pilih Kaedah Pembayaran',
//                     'name' => 'payment-method-inv',
//                     'type' => 'default',
//                     'option' => [
//                         "0" => (object) [
//                             "value" => 'Pemindahan_Bank_Segera',
//                             "name" => 'Pemindahan Bank Segera',
//                         ],
//                         "1" => (object) [
//                             "value" => 'Jaminan_bank',
//                             "name" => 'Jaminan Bank',
//                         ],
//                         "2" => (object) [
//                             "value" => 'Cek',
//                             "name" => 'Cek',
//                         ],
//                         "3" => (object) [
//                             "value" => 'Tunai',
//                             "name" => 'Tunai',
//                         ],
//                     ]
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Bayaran (RM)',
//                     'name' => 'total-rcpp'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '66' => (object) [
//         'title' => 'Muat Naik Bukti Pembayaran Invois 40% (GIS Ready)',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Bukti Pembayaran Invois',
//                     'folder' => 'RCPP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Kaedah Pembayaran',
//                     'placeholder' => 'Sila Pilih Kaedah Pembayaran',
//                     'name' => 'payment-method-inv',
//                     'type' => 'default',
//                     'option' => [
//                         "0" => (object) [
//                             "value" => 'Pemindahan_Bank_Segera',
//                             "name" => 'Pemindahan Bank Segera',
//                         ],
//                         "1" => (object) [
//                             "value" => 'Jaminan_bank',
//                             "name" => 'Jaminan Bank',
//                         ],
//                         "2" => (object) [
//                             "value" => 'Cek',
//                             "name" => 'Cek',
//                         ],
//                         "3" => (object) [
//                             "value" => 'Tunai',
//                             "name" => 'Tunai',
//                         ],
//                     ]
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Bayaran (RM)',
//                     'name' => 'total-rcpp'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '75' => (object) [
//         'title' => 'Semakan Bayaran Invois Caj Perkhidmatan 40%',
//         'aside' => (object) [
//             "url" => $statusId === 75  ? General::getAttachment($systemId, 'RCPP')->url:null,
//             "mime" => $statusId === 75  ? General::getAttachment($systemId, 'RCPP')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Bayaran Invois?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '77' => (object) [
//         'title' => 'Semakan Notis Mula Kerja',
//         'aside' => (object) [
//             "url" => $statusId === 77  ? General::getAttachment($systemId, 'NMK')->url:null,
//             "mime" => $statusId === 77  ? General::getAttachment($systemId, 'NMK')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Notis Mula Kerja?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '79' => (object) [
//         'title' => 'Muat Naik Notis Mula Kerja',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Notis Mula Kerja',
//                     'folder' => 'NMK',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Surat',
//                     'name' => 'dt_ws_ltr_created',
//                     'type' => 'date'
//                 ],
//                 1 => (object) [
//                     'label' => 'Tarikh Terima',
//                     'name' => 'dt_ws_ltr_received',
//                     'type' => 'date'
//                 ],
//                 2 => (object) [
//                     'label' => 'Tarikh Mula Kerja',
//                     'name' => 'dt_ws',
//                     'type' => 'date'
//                 ],

//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '86' => (object) [
//         'title' => 'Semakan Notis Siap Kerja',
//         'aside' => (object) [
//             "url" => $statusId === 86  ? General::getAttachment($systemId, 'NSK')->url:null,
//             "mime" => $statusId === 86  ? General::getAttachment($systemId, 'NSK')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Notis Siap Kerja?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '88' => (object) [
//         'title' => 'Muat Naik Permohonan Notis Siap Kerja',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Notis Siap Kerja',
//                     'folder' => 'NSK',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Surat',
//                     'name' => 'dt_wf_ltr_created',
//                     'type' => 'date'
//                 ],
//                 1 => (object) [
//                     'label' => 'Tarikh Terima',
//                     'name' => 'dt_wf_ltr_received',
//                     'type' => 'date'
//                 ],
//                 2 => (object) [
//                     'label' => 'Tarikh Akhir Kerja',
//                     'name' => 'dt_wf',
//                     'type' => 'date'
//                 ],

//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '89' => (object) [
//         'title' => 'Semakan Permohonan Lanjutan Permit Kerja',
//         'aside' => (object) [
//             "url" => $statusId === 89  ? General::getAttachment($systemId, 'PLPK')->url:null,
//             "mime" => $statusId === 89  ? General::getAttachment($systemId, 'PLPK')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Permohonan Lanjutan Permit Kerja?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '90' => (object) [
//         'title' => 'Muat Naik Dokumen Permohonan Lanjutan Permit Kerja',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Dokumen Permohonan Lanjutan Permit Kerja',
//                     'folder' => 'SRPLPK',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Surat',
//                     'name' => 'dt_ltr',
//                     'type' => 'date'
//                 ],
//                 1 => (object) [
//                     'label' => 'Tarikh Terima',
//                     'name' => 'dt_recv',
//                     'type' => 'date'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '109' => (object) [
//         'title' => 'Muat Naik Pelan Siap Bina',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Pelan Siap Bina',
//                     'folder' => 'PSB',
//                     'type' => '.pdf'
//                 ],
//                 1 => (object) [
//                     'label' => 'Pelan Siap Bina',
//                     'folder' => 'PSB',
//                     'type' => '.dwg'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '117' => (object) [
//         'title' => 'Muat Naik Bukti Pembayaran Invois 10% (GIS Ready)',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Bukti Pembayaran Invois',
//                     'folder' => 'RCPP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Kaedah Pembayaran',
//                     'placeholder' => 'Sila Pilih Kaedah Pembayaran',
//                     'name' => 'payment-method-inv',
//                     'type' => 'default',
//                     'option' => [
//                         "0" => (object) [
//                             "value" => 'Pemindahan_Bank_Segera',
//                             "name" => 'Pemindahan Bank Segera',
//                         ],
//                         "1" => (object) [
//                             "value" => 'Jaminan_bank',
//                             "name" => 'Jaminan Bank',
//                         ],
//                         "2" => (object) [
//                             "value" => 'Cek',
//                             "name" => 'Cek',
//                         ],
//                         "3" => (object) [
//                             "value" => 'Tunai',
//                             "name" => 'Tunai',
//                         ],
//                     ]
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Bayaran (RM)',
//                     'name' => 'total-rcpp'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '118' => (object) [
//         'title' => 'Semakan Dokumen Permohonan CPC',
//         'aside' => (object) [
//             "url" => $statusId === 118  ? General::getAttachment($systemId, 'SRPSSK')->url:null,
//             "mime" => $statusId === 118  ? General::getAttachment($systemId, 'SRPSSK')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Dokumen Permohonan CPC?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '124' => (object) [
//         'title' => 'Muat Naik Bukti Pembayaran Invois Caj Perkhidmatan 10%',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Bukti Pembayaran Invois',
//                     'folder' => 'RCPP',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'select' => (object) [
//                 0 => (object) [
//                     'label' => 'Kaedah Pembayaran',
//                     'placeholder' => 'Sila Pilih Kaedah Pembayaran',
//                     'name' => 'payment-method-inv',
//                     'type' => 'default',
//                     'option' => [
//                         "0" => (object) [
//                             "value" => 'Pemindahan_Bank_Segera',
//                             "name" => 'Pemindahan Bank Segera',
//                         ],
//                         "1" => (object) [
//                             "value" => 'Jaminan_bank',
//                             "name" => 'Jaminan Bank',
//                         ],
//                         "2" => (object) [
//                             "value" => 'Cek',
//                             "name" => 'Cek',
//                         ],
//                         "3" => (object) [
//                             "value" => 'Tunai',
//                             "name" => 'Tunai',
//                         ],
//                     ]
//                 ],
//             ],
//             'text' => (object) [
//                 0 => (object) [
//                     'label' => 'Jumlah Bayaran (RM)',
//                     'name' => 'total-rcpp'
//                 ]
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '134' => (object) [
//         'title' => 'Muat Naik Dokumen Permohonan CMGD',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Dokumen Permohonan CMGD',
//                     'folder' => 'SRPSSMK',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Permohonan',
//                     'name' => 'dt_appl',
//                     'type' => 'date'
//                 ],
//                 1 => (object) [
//                     'label' => 'Tarikh Laporan',
//                     'name' => 'dt_report',
//                     'type' => 'date'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '135' => (object) [
//         'title' => 'Semakan Dokumen Permohonan CMGD',
//         'aside' => (object) [
//             "url" => $statusId === 135  ? General::getAttachment($systemId, 'SRPSSMK')->url:null,
//             "mime" => $statusId === 135  ? General::getAttachment($systemId, 'SRPSSMK')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Dokumen Permohonan CMGD?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],

//         ]
//     ],
//     '144' => (object) [
//         'title' => 'Muat Naik Dokumen Permohonan PWC',
//         'input' => (object) [
//             'upload' => (object) [
//                 0 => (object) [
//                     'label' => 'Dokumen Permohonan PWC',
//                     'folder' => 'SRPWC',
//                     'type' => '.pdf'
//                 ],
//             ],
//             'date' => (object) [
//                 0 => (object) [
//                     'label' => 'Tarikh Surat',
//                     'name' => 'dt_appl_ltr_created',
//                     'type' => 'date'
//                 ],
//                 1 => (object) [
//                     'label' => 'Tarikh Terima',
//                     'name' => 'dt_appl_ltr_received',
//                     'type' => 'date'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],
//         ]
//     ],
//     '145' => (object) [
//         'title' => 'Semakan Dokumen Permohonan PWC',
//         'aside' => (object) [
//             "url" => $statusId === 145  ? General::getAttachment($systemId, 'SRPWC')->url:null,
//             "mime" => $statusId === 145  ? General::getAttachment($systemId, 'SRPWC')->mime_type:null,
//             "type" => 'attachment',
//         ],
//         'input' => (object) [
//             'checkbox' => (object) [
//                 0 => (object) [
//                     'label' => 'Sahkan Dokumen Permohonan PWC?',
//                     'name' => 'verify',
//                     'value' => 1,
//                     'type' => 'switch'
//                 ],
//             ],
//             'textarea' => (object) [
//                 0 => (object) [
//                     'label' => 'Catatan',
//                     'name' => 'notes'
//                 ]
//             ],

//         ]
//     ],

// ];