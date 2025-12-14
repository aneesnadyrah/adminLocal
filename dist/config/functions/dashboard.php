<?php

set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/system.php';
require_once 'config/DBFactory.php';
require_once 'config/components.php';
require_once 'config/functions/roles.php';

class Dashboard
{
    protected $db;
    private $conn;
    private $Role;
    private $Widget;
    private $User;

    public function __construct($username)
    {
        $this->db = new DBConnectionFactory();
        $this->conn = $this->db->createConnection();
        $this->Role = new Roles();
        $this->Widget = new Widget();
        $this->User = $username;
    }

    private function formatNumber($number, $type)
    {
        switch ($type) {
            case 'currency':
                $prefix = 'RM ';
                break;
            case 'length':
                $prefix = '';
                break;
            default:
                $prefix = '';
                break;
        }
        $suffix = '';
        $conversion = 0;

        if ($number >= 1000 && $number < 1000000) {
            $conversion = $number / 1000;
            switch ($type) {
                case 'currency':
                    $suffix = 'K';
                    break;
                case 'length':
                    $suffix = 'km';
                    break;
                default:
                    $suffix = 'K';
                    break;
            }
        } elseif ($number >= 1000000) {
            $conversion = $number / 1000000;
            switch ($type) {
                case 'currency':
                    $suffix = 'J';
                    break;
                case 'length':
                    $suffix = 'J km';
                    break;
                default:
                    $suffix = 'J';
                    break;
            }
        }

        $result = (object) [
            "number" => number_format($conversion, 2),
            "prefix" => $prefix,
            "suffix" => $suffix,
        ];

        return $result;
    }

    private function getProvider($id)
    {
        $system = new System();
        $ec_url = $system->App->ec_url;
        $url = $ec_url . '/api/providers/' . $id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if ($result === false) {
            // Handle cURL error, if needed
            $decodedResult = (object) ["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
        } else {
            // Decode the JSON result
            $decodedResult = json_decode($result);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle JSON decoding error, if needed
                $decodedResult = (object) ["id" => "", "name" => "", "logo" => "", "sort_name" => ""];
            }
        }
        curl_close($ch);

        return $decodedResult;
    }

    private function getProvidersList()
    {
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        $list = [];

        switch ($department) {
            case 'board':
                switch ($subDepartment) {
                    case 'director':
                        $list[] = [
                            'title' => 'Jumlah Permohonan',
                            'subtitle' => 'Mengikut Penyedia Utiliti',
                            'data' => $this->getCountProviders()
                        ];
                        break;
                }
                break;
            default:
                $list[] = [
                    'title' => 'Jumlah Permohonan',
                    'subtitle' => 'Mengikut Penyedia Utiliti',
                    'data' => $this->getCountProviders()
                ];
                break;
        }

        return $list;
    }

private function getCountProviders()
{

    $sql = "SELECT COUNT(flw_appl_entries.utility_provider) as count, 
                   flw_appl_entries.utility_provider, 
                   ls_providers.name 
            FROM flw_appl_entries
            LEFT JOIN ls_providers ON flw_appl_entries.utility_provider = ls_providers.id
            GROUP BY flw_appl_entries.utility_provider, ls_providers.name 
            ORDER BY count ASC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $result;
}

    private function getCountTotal($filter = NULL)
    {

        switch ($filter) {
            case 'application':
                $calculate = 'COUNT(id)';
                $table = 'flw_appl_entries';
                $condition = 'WHERE reference_no IS NOT NULL';
                $type = 'count';
                break;
            case 'invoice':
                $calculate = 'SUM(amount_invoices)';
                $table = 'flw_invoices';
                $condition = '';
                $type = 'currency';
                break;
            case 'appl_length':
                $calculate = 'SUM(application_length)';
                $table = 'flw_appl_entries';
                $condition = 'WHERE reference_no IS NOT NULL';
                $type = 'count';
                break;
            case 'survey_length':
                $calculate = 'SUM(survey_length)';
                $table = 'flw_survey_udm';
                $condition = '';
                $type = 'count';
                break;
        }

        $sql = "SELECT $calculate AS counter FROM $table $condition";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rawValue = $stmt->fetch(PDO::FETCH_OBJ)->counter ?? 0;

        // Format the result based on type
        if ($type === 'currency') {
            return 'RM ' . number_format($rawValue, 2, '.', ',');
        } else {
            return number_format($rawValue, 0, '.', ',');
        }
    }

    private function getCountTask($filter = NULL, $statusId = NULL)
    {
        $role = $this->Role->{$this->User};
        $department = $role->department;

        $status = $statusId ?? $this->Role->getAssignment($this->User);

        if ($department == 'management') {
            $set_array = explode(',', str_replace(['{', '}'], '', $status)); // Convert string to array

            if (in_array(158, $set_array)) {
                $department = 'finance';
            } else {
                $department = $role->sub_department;
            }
        } else {
            $department = $role->department;
        }

        switch ($filter) {
            case 'new':
                $condition = "AND tasks.status_id = ANY (:statusId) AND tasks.status_id != 162 AND DATE(assign.created_at) >= CURRENT_DATE - INTERVAL '7 days' AND assign.completed = false ";
                break;
            case 'pending':
                $condition = "AND tasks.status_id = ANY (:statusId) AND tasks.status_id != 162 AND DATE(assign.created_at) < CURRENT_DATE - INTERVAL '7 days' AND assign.completed = false ";
                break;
            case 'done':
                $condition = "AND NOT tasks.status_id = ANY (:statusId) AND assign.completed_at IS NOT NULL AND completed = true";
                break;
            case 'quotation':
                $status = [27, 28, 29];
                $status = '{' . implode(',', $status) . '}';
                $condition = "AND tasks.status_id = ANY (:statusId) AND assign.completed = false";
                break;
            default:
                $condition = "AND tasks.status_id = :statusId AND assign.completed = false ";
                break;
        }

        $query = "SELECT COUNT(tasks.status_id) AS count_task FROM view_tasks tasks LEFT JOIN flw_task_assignments assign ON assign.system_id = tasks.system_id  WHERE flow_department = :department AND username = :username AND authority = 0 {$condition}";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':username', $this->User);
        $stmt->bindParam(':statusId', $status);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count_task;
    }

    private function getCountData($category, $filter = "all")
    {
        switch ($category) {
            case 'length':
                $calculate = 'SUM(application_length)';
                $table = 'flw_appl_entries';
                $recordDate = 'application_date';
                break;
            case 'charges':
                $calculate = 'SUM(amount)';
                $table = 'flw_charges';
                $recordDate = 'paid_at';
                break;
            case 'utilityPayment':
                $calculate = 'SUM(amount_paid)';
                $table = 'flw_invoices';
                $recordDate = 'created_at';
                break;
            case 'paid':
                $calculate = 'SUM(amount_invoices)';
                $table = 'flw_invoices';
                $recordDate = 'created_at';
                break;
            default:
                break;
        }

        switch ($filter) {
            case "overall":
                $condition = "";
                break;
            case "yearly":
                $condition = "WHERE EXTRACT(YEAR FROM {$recordDate}) = EXTRACT(YEAR FROM CURRENT_DATE)";
                break;
            case "monthly":
                $condition = "WHERE EXTRACT(MONTH FROM {$recordDate}) = EXTRACT(MONTH FROM CURRENT_DATE)";
                break;
            case "weekly":
                $condition = "WHERE EXTRACT(WEEK FROM {$recordDate}) = EXTRACT(WEEK FROM CURRENT_DATE)";
                break;
            default:
                $condition = "";
                break;
        }

        $query = "SELECT $calculate AS counter FROM $table $condition";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ)->counter;
        return $result;
    }

    private function queryTableList($category, $filter)
    {

        switch ($category) {
            case 'length':
                $table = 'flw_appl_entries';
                break;
            case 'charges':
                $table = 'flw_charges';
                $condition = "WHERE is_paid = 'false'";
                break;
            case 'unpaid':
                $table = 'flw_invoices';
                $condition = "WHERE is_paid = 'false' AND ";
                $joined = "LEFT JOIN view_tasks ON view_tasks.system_id = flw_invoices.system_id";
                break;
            case 'dlp':
                $table = 'flw_appl_entries';
                $condition = "";
                break;
            default:
                $table = "flw_appl_entries";
                $joined = "";
                $condition = "WHERE ";
                break;
        }

        switch ($filter) {
            case "yearly":
                $date = "flw_invoices.created_at >= DATE_TRUNC('year', CURRENT_DATE)
                       AND flw_invoices.created_at < DATE_TRUNC('year', CURRENT_DATE) + INTERVAL '1 year'";
                break;
            case "monthly":
                $date = "EXTRACT(MONTH FROM $table.created_at) = EXTRACT(MONTH FROM CURRENT_DATE)";
                break;
            case "weekly":
                $date = "EXTRACT(WEEK FROM $table.created_at) = EXTRACT(WEEK FROM CURRENT_DATE)";
                break;
            default:
                $date = "";
                break;
        }

        $query = "SELECT * FROM {$table} {$joined} {$condition} {$date} ORDER BY {$table}.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach ($result as $record) {
            $providerData = $this->getProvider($record->provider_id);
            if (isset($record->provider_id)) {
                $record->provider = $providerData;
            } else {
                $record->provider = $providerData;
            }
        }

        return $result;
    }

    private function getTableList($which = 0)
    {

        $role = $this->Role->{$this->User};
        $department = $role->department;
        $subDepartment = $role->sub_department;
        $zone = $role->zone_districts;
        $tableYearly = $this->queryTableList('unpaid', 'monthly');

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'operation':
                        $list = [
                            0 => (object) [
                                "title" => "Senarai Tempoh Kecacatan Kurang 7 Hari",
                                "subtitle" => "Mengikut Permohonan",
                                "route" => "/tracker/dlp/week",
                                "table_id" => "dlp-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Daerah", "Status", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "districts", "status", "balance"],
                            ]
                        ];
                        break;
                    case 'finance':
                        $list = [
                            0 => (object) [
                                "title" => "Senarai Bayaran Tertunggak",
                                "subtitle" => "Mengikut Permohonan",
                                "route" => "/tracker/payment/unpaid",
                                "table_id" => "unpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Daerah", "Status", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "districts", "status", "balance"],
                            ]
                        ];
                        break;
                    case 'mapping':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Kerja Pengukuran Terkini",
                                "subtitle" => "Jumlah Kerja Ukur Minggu Ini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                }
                break;

            case 'operation':
                switch ($subDepartment) {
                    case 'registration':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Permohonan Tahun Ini",
                                "subtitle" => "Jumlah Tugasan Terkini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;

                    case 'permit':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Permohonan Tahun Ini",
                                "subtitle" => "Jumlah Tugasan Terkini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                }
                break;

            case 'finance':
                switch ($subDepartment) {
                    case 'finance':
                        $list = [
                            0 => (object) [
                                "title" => "Senarai Bayaran Tertunggak",
                                "subtitle" => "Mengikut Permohonan",
                                "route" => "/tracker/payment/unpaid",
                                "table_id" => "unpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Daerah", "Status", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "districts", "status", "balance"],
                            ]
                        ];
                        break;
                }
                break;

            case 'geospatial':
                switch ($subDepartment) {
                    case 'geospatial':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Tugasan Pindaan",
                                "subtitle" => "Jumlah Tugasan Terkini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                    case 'translation':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Tugasan Pindaan",
                                "subtitle" => "Jumlah Tugasan Terkini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                    case 'charting':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Tugasan Pindaan",
                                "subtitle" => "Jumlah Tugasan Terkini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                    default:
                        break;
                }
                break;

            case 'mapping':
                switch ($subDepartment) {
                    case 'survey':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Kerja Pengukuran Terkini",
                                "subtitle" => "Jumlah Kerja Ukur Minggu Ini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                    case 'plan':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Kerja Pelan Terkini",
                                "subtitle" => "Jumlah Kerja Pelan Minggu Ini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                    case 'mapping':
                        $list = [
                            0 => (object) [
                                "count" => $this->getCountTask(statusId: 16),
                                "title" => "Senarai Kerja Pelan Terkini",
                                "subtitle" => "Jumlah Kerja Pelan Minggu Ini",
                                "route" => "/tracker/application",
                                "table_id" => "notpaid-yearly",
                                "records" => $tableYearly,
                                "headers" => ["No Rujukan", "Tarikh Invois", "Status", "Daerah", "Nilai Tunggakan"],
                                "body" => ["reference_no", "created_at", "status", "districts", "balance"],
                            ]
                        ];
                        break;
                }
                break;

            default:
                $list = [0 => []];
                break;
        }

        return $list[$which];
    }

    private function queryRadialData($category, $filter)
    {

        switch ($category) {
            case 'perfrormance':
                $query = "";
                break;
            case 'utilityPayment':
                $query = "SELECT
                category,
                jsonb_agg(provider) AS labels,
                jsonb_agg(ROUND(CAST(amount_paid/amount_invoices*100 AS numeric), 2)) AS series
                FROM (
                SELECT
                    'year' AS category,
                    flw_appl_entries.utility_provider AS provider,
                    SUM(flw_invoices.amount_paid) AS amount_paid,
                    SUM(amount_invoices) AS amount_invoices
                FROM
                    flw_invoices
                LEFT JOIN
                    flw_appl_entries ON flw_appl_entries.system_id = flw_invoices.system_id
                WHERE
                    EXTRACT(YEAR FROM flw_invoices.created_at) = EXTRACT(YEAR FROM NOW())
                GROUP BY flw_appl_entries.utility_provider
                UNION ALL
                SELECT
                    'month' AS category,
                    flw_appl_entries.utility_provider AS provider,
                    SUM(flw_invoices.amount_paid) AS amount_paid,
                    SUM(amount_invoices) AS amount_invoices
                FROM
                    flw_invoices
                LEFT JOIN
                    flw_appl_entries ON flw_appl_entries.system_id = flw_invoices.system_id
                WHERE
                    EXTRACT(MONTH FROM flw_invoices.created_at) = EXTRACT(MONTH FROM NOW())
                GROUP BY flw_appl_entries.utility_provider
                ) AS subquery
                GROUP BY
                category
                ORDER BY
                category DESC";

                $chart = $this->conn->query($query)->fetchAll(PDO::FETCH_OBJ);


                foreach ($chart as $index => $record) {
                    $providerIds = json_decode($record->labels);

                    // Check if json_decode was successful
                    if ($providerIds && is_array($providerIds)) {
                        $providerNames = [];
                        foreach ($providerIds as $id) {
                            // Assuming your API returns an object with a 'sort_name' property
                            $providerData = $this->getProvider($id);

                            // Check if the request was successful and the data is valid
                            if ($providerData && isset($providerData->sort_name)) {
                                $providerNames[] = $providerData->sort_name;
                            }
                        }
                        $record->labels = json_encode($providerNames);
                    } else {
                        // If it's not a valid JSON array, assume it's a single ID
                        $providerData = $this->getProvider($record->provider);

                        // Check if the request was successful and the data is valid
                        $record->labels = $providerData ? json_encode([$providerData->sort_name]) : [];
                    }
                }

                $data = [];
                foreach ($chart as $index => $record) {
                    $data[] = (object) [
                        'tab' => "{$category}_tab_{$index}",
                        'id' => "{$category}_radial_{$index}",
                        'style' => 'radial',
                        'height' => 350,
                        'category' => $record->category,
                        'labels' => $record->labels,
                        'series' => $record->series,
                    ];
                }

                break;
        }

        return $data;
    }

    private function getRadialData()
    {
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        $radial = [];
        switch ($subDepartment) {
            case 'registration':
                $radial[] = (object) [
                    "title" => "Permohonan Diterima",
                    "subtitle" => "Mengikut Kategori Jarak",
                    "charts" => $this->queryRadialData("utilityPayment", "yearly"),

                ];
                break;
            case 'finance':
                $radial[] = (object) [
                    "title" => "Permohonan Diterima",
                    "subtitle" => "Mengikut Kategori Jarak",
                    "charts" => $this->queryRadialData("utilityPayment", "yearly"),

                ];
                break;
            case 'business':
                $radial[] = (object) [
                    "title" => "Permohonan Diterima",
                    "subtitle" => "Mengikut Kategori Jarak",
                    "charts" => $this->queryRadialData("utilityPayment", "yearly"),

                ];
                break;
            default:
                break;
        }

        return $radial;
    }

    private function querySliderData($category, $filter = 'weekly')
    {
        $department = $this->Role->{$this->User}->department;

        switch ($category) {
            case 'new':
                $statusId = "{4,68,118}";
                $date = 'tasks.submitted_date';
                break;
            case 'site_visit':
                $statusId = "{13}";
                $date = 'site.start';
                $filter = 'monthly';
                break;
            case 'wayleave':
                $statusId = "{34}";
                $date = 'tasks.created_date';
                break;
            case 'work_permit':
                $statusId = "{73}";
                $date = 'tasks.created_date';
                break;
            case 'work_notice':
                $statusId = "{77,78,79,80,81}";
                $date = 'tasks.created_date';
                break;
            default:
                $statusId = "{4,68,118}";
                $date = 'tasks.submitted_date';
                break;
        }

        switch ($filter) {
            case 'yearly':
                $sorting = "YEAR";
                break;
            case 'monthly':
                $sorting = "MONTH";
                break;
            case 'weekly':
                $sorting = "WEEK";
                break;
            default:
                $sorting = "WEEK";
                break;
        }


        $condition = '';
        if ($department === 'management') {
            // $condition = "AND username = :username AND flow_department = :department";
        }

        $query = "SELECT * FROM view_tasks tasks
        LEFT JOIN flw_appl_reports report ON report.system_id = tasks.system_id
        LEFT JOIN flw_calendars site ON site.system_id = tasks.system_id
        WHERE EXTRACT($sorting FROM $date) = EXTRACT($sorting FROM CURRENT_DATE)
        AND tasks.status_id = ANY(:statusId)
        $condition LIMIT 7";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statusId', $statusId);
        if ($department === 'management') {
            // $stmt->bindParam(':username', $this->User);
            // $stmt->bindParam(':department', $department);
        }

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_OBJ);

        $response = (object) [
            "data" => $data,
            "count" => count($data),
        ];

        return $response;
    }
    private function getSliderData()
    {

        $role = $this->Role->{$this->User};
        $department = $role->department;
        $subDepartment = $role->sub_department;
        $statusId = $this->Role->getAssignment($this->User);
        $sliders = [];

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'operation':
                        $sliders = [
                            (object) [
                                "title" => "Kelulusan Izin Lalu Bulan Ini",
                                "route" => "/week/application/wayleave",
                                "data" => $this->querySliderData("wayleave", "monthly")->data,
                                "counts" => $this->querySliderData("wayleave", "monthly")->count,
                            ],
                            (object) [
                                "title" => "Kelulusan Permit Kerja Bulan Ini",
                                "route" => "/week/application/permit",
                                "data" => $this->querySliderData("work_permit", "monthly")->data,
                                "counts" => $this->querySliderData("work_permit", "monthly")->count,
                            ],
                            (object) [
                                "title" => "Permohonan Minggu Ini",
                                "route" => "/week/application",
                                "data" => $this->querySliderData("new", "weekly")->data,
                                "counts" => $this->querySliderData("new", "weekly")->count,
                            ],
                            (object) [
                                "title" => "Lawatan Tapak Bulan Ini",
                                "route" => "/details/sitevisit/$department",
                                "data" => $this->querySliderData("site_visit", "monthly")->data,
                                "counts" => $this->querySliderData("site_visit", "monthly")->count,
                            ]
                        ];
                        break;
                    case 'executive':
                        $sliders = [
                            (object) [
                                "title" => "Kelulusan Izin Lalu Tahun Ini",
                                "route" => "/",
                                "data" => $this->querySliderData("wayleave", "yearly")->data,
                                "counts" => $this->querySliderData("wayleave", "yearly")->count,
                                "date_filter" => "created_date",
                            ],
                            (object) [
                                "title" => "Kelulusan Izin Lalu Bulan Ini",
                                "route" => "/",
                                "data" => $this->querySliderData("wayleave", "monthly")->data,
                                "counts" => $this->querySliderData("wayleave", "monthly")->count,
                                "date_filter" => "created_date",
                            ],
                            (object) [
                                "title" => "Kelulusan Permit Kerja Tahun Ini",
                                "route" => "/",
                                "data" => $this->querySliderData("work_permit", "yearly")->data,
                                "counts" => $this->querySliderData("work_permit", "yearly")->count,
                                "date_filter" => "created_date",
                            ],
                            (object) [
                                "title" => "Kelulusan Permit Kerja Bulan Ini",
                                "route" => "/",
                                "data" => $this->querySliderData("work_permit", "monthly")->data,
                                "counts" => $this->querySliderData("work_permit", "monthly")->count,
                                "date_filter" => "created_date"
                            ],
                            (object) [
                                "title" => "Kelulusan Notis Kerja Tahun Ini",
                                "route" => "/",
                                "data" => $this->querySliderData("work_notice", "yearly")->data,
                                "counts" => $this->querySliderData("work_notice", "yearly")->count,
                                "date_filter" => "created_date",
                            ],
                            (object) [
                                "title" => "Kelulusan Notis Kerja Bulan Ini",
                                "route" => "/",
                                "data" => $this->querySliderData("work_notice", "monthly")->data,
                                "counts" => $this->querySliderData("work_notice", "monthly")->count,
                                "date_filter" => "created_date"
                            ]
                        ];
                        break;
                }
                break;
            default:
                switch ($subDepartment) {
                    case 'mapping':
                    case 'survey':
                    case 'plan':
                        $slider1 = $this->querySliderData("new");
                        $slider2 = $this->querySliderData("wayleave");
                        $sliders = [
                            (object) [
                                "title" => "Permohonan Minggu Ini",
                                "date_filter" => "submitted_date",
                                "data" => $slider1->data,
                                "counts" => $slider1->count,
                            ],
                            (object) [
                                "title" => "Kelulusan Izin Lalu",
                                "date_filter" => "submitted_date",
                                "data" => $slider2->data,
                                "counts" => $slider2->count,
                            ]
                        ];
                    break;
                    case 'registration':
                        $sliderNew = $this->querySliderData("new");
                        $sliderSV = $this->querySliderData("site_visit");
                        $sliders = [
                            (object) [
                                "title" => "Permohonan Minggu Ini",
                                "route" => "/week/application",
                                "date_filter" => "submitted_date",
                                "data" => $sliderNew->data,
                                "counts" => $sliderNew->count,
                            ],
                            (object) [
                                "title" => "Lawatan Tapak",
                                "route" => "/details/sitevisit/$department",
                                "date_filter" => "submitted_date",
                                "data" => $sliderSV->data,
                                "counts" => $sliderSV->count,
                            ]
                        ];
                        break;

                    case 'geospatial':
                        $sliders = [
                            (object) [
                                "title" => "Lawatan Tapak Minggu Ini",
                                "route" => "/details/sitevisit/$department",
                                "date_filter" => "sv_date_done",
                                "data" => $this->querySliderData("site_visit")->data,
                                "counts" => $this->querySliderData("site_visit")->count,
                            ]
                        ];
                        break;

                    case 'translation':
                        $sliders = [
                            (object) [
                                "title" => "Lawatan Tapak Minggu Ini",
                                "route" => "/details/sitevisit/$department",
                                "date_filter" => "sv_date_done",
                                "data" => $this->querySliderData("site_visit")->data,
                                "counts" => $this->querySliderData("site_visit")->count,
                            ]
                        ];
                        break;

                    case 'charting':
                        $sliders = [
                            (object) [
                                "title" => "Lawatan Tapak Minggu Ini",
                                "route" => "/details/sitevisit/$department",
                                "date_filter" => "sv_date_done",
                                "data" => $this->querySliderData("site_visit")->data,
                                "counts" => $this->querySliderData("site_visit")->count,
                            ]
                        ];
                        break;

                    case 'project':
                        // Handle project case if needed
                        break;
                    case 'business':
                        $sliders = [
                            (object) [
                                "title" => "Permohonan Minggu Ini",
                                "route" => "/week/application",
                                "date_filter" => "submitted_date",
                                "data" => $this->querySliderData("new")->data,
                                "counts" => $this->querySliderData("new")->count,
                            ],
                            (object) [
                                "title" => "Lawatan Tapak",
                                "route" => "/details/sitevisit/$department",
                                "date_filter" => "submitted_date",
                                "data" => $this->querySliderData("site_visit")->data,
                                "counts" => $this->querySliderData("site_visit")->count,
                            ]
                        ];
                        break;
                    case 'hr':
                        $sliders = [
                            (object) [
                                "title" => "Permohonan Cuti Minggu Ini",
                                "route" => "/week/application",
                                "date_filter" => "submitted_date"
                            ],
                            (object) [
                                "title" => "Mesyuarat Minggu Ini",
                                "route" => "/week/sitevisit",
                                "date_filter" => "submitted_date"
                            ]
                        ];
                        break;
                    default:
                        break;
                }
                break;
        }

        return $sliders;
    }

    private function getOverviewData($which = 0)
    {
        $subDepartment = $this->Role->{$this->User}->sub_department;
        $department = $this->Role->{$this->User}->department;

        $tasks = new stdClass();

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'operation':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 152),
                                    'title' => 'Kebenaran Khas',
                                    'icon' => 'file-certificate ',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/approvepiu/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 153),
                                    'title' => 'Permohonan KIV',
                                    'icon' => 'hourglass-clock',
                                    'color' => 'dark',
                                    'route' => '/operation/general/tasks/approvepiu/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => "Tunggakan",
                                    'icon' => 'circle-exclamation',
                                    'color' => 'danger',
                                    'route' => '/operation/general/tasks/pending'
                                ],
                            ],
                            1 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 152),
                                    'title' => 'Kebenaran Khas',
                                    'icon' => 'file-certificate ',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/approvepiu/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 153),
                                    'title' => 'Permohonan KIV',
                                    'icon' => 'hourglass-clock',
                                    'color' => 'dark',
                                    'route' => '/operation/general/tasks/approvepiu/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => "Tunggakan",
                                    'icon' => 'circle-exclamation',
                                    'color' => 'danger',
                                    'route' => '/operation/general/tasks/pending'
                                ],
                            ],
                        ];
                        break;

                    case 'finance':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 2),
                                    'title' => 'Caj Pendaftaran',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/regcharges'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 6),
                                    'title' => 'Arahan Kerja',
                                    'icon' => 'file-upload',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/wo'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('quotation'),
                                    'title' => 'Sebut Harga',
                                    'icon' => 'file-invoice',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/quotation'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 37),
                                    'title' => 'Semakan Invois',
                                    'icon' => 'money-check-dollar',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/invoice'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/finance/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    case 'business':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'file-lines',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass',
                                    'color' => 'warning',
                                    'route' => '/operation/general/tasks/pending'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 143),
                                    'title' => 'Serahan CCC',
                                    'icon' => 'handshake',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 149),
                                    'title' => 'Permohonan Selesai',
                                    'icon' => 'file-circle-check',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ]
                            ]
                        ];
                        break;

                    case 'mapping':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 63),
                                    'title' => 'Serahan Data Lot',
                                    'icon' => 'check-to-slot',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/datalot'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 48),
                                    'title' => 'Pengesahan Kerja',
                                    'icon' => 'file-circle-check ',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/approvepiu'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/mapping/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    case 'project':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask(statusId: 15),
                                    'title' => 'Laporan LTA',
                                    'icon' => 'files',
                                    'color' => 'primary',
                                    'percent' => '75%',
                                    'route' => '/project/general/tasks/approvereport',
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 82),
                                    'title' => 'Mula Kerja',
                                    'icon' => 'circle-exclamation',
                                    'color' => 'danger',
                                    'percent' => '88%',
                                    'route' => '/project/general/tasks/datalot/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 121),
                                    'title' => 'Permohonan CPC',
                                    'icon' => 'file-certificate ',
                                    'color' => 'primary',
                                    'percent' => '60%',
                                    'route' => '/project/general/tasks/approvepiu/new'
                                ],
                            ]
                        ];
                        break;

                    case 'executive':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'percent' => '75%',
                                    'route' => '/management/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'circle-exclamation',
                                    'color' => 'danger',
                                    'percent' => '88%',
                                    'route' => '/management/general/tasks/pending'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 152),
                                    'title' => 'Pembatalan Permohonan',
                                    'icon' => 'file-certificate ',
                                    'color' => 'primary',
                                    'percent' => '60%',
                                    'route' => '/management/general/tasks/cancel/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 153),
                                    'title' => 'Penangguhan Permohonan',
                                    'icon' => 'file-certificate ',
                                    'color' => 'primary',
                                    'percent' => '60%',
                                    'route' => '/management/general/tasks/delay/new'
                                ],
                            ]
                        ];
                        break;

                    case 'permit':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/permit/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'warning',
                                    'route' => '/permit/general/tasks/pending'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 28),
                                    'title' => 'Sebut Harga Tidak Disahkan',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'danger',
                                    'route' => '/permit/general/tasks/quotation'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 55),
                                    'title' => 'PIU Dikeluarkan',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'primary',
                                    'route' => '/permit/general/tasks/uploadpiu'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 144),
                                    'title' => 'Kiraan Wang Cagaran',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'primary',
                                    'route' => '/permit/general/tasks/new'
                                ],
                            ]
                        ];
                        break;
                }
                break;

            case 'finance':
                switch ($subDepartment) {
                    case 'finance':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 2),
                                    'title' => 'Caj Pendaftaran',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/regcharges'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 6),
                                    'title' => 'Arahan Kerja',
                                    'icon' => 'file-upload',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/wo'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('quotation'),
                                    'title' => 'Sebut Harga',
                                    'icon' => 'file-invoice',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/quotation'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 37),
                                    'title' => 'Semakan Invois',
                                    'icon' => 'money-check-dollar',
                                    'color' => 'primary',
                                    'route' => '/finance/general/tasks/invoice'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/finance/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;
                }
                break;

            case 'operation':
                switch ($subDepartment) {
                    case 'registration':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'file-lines',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tugasan Tertunda',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/operation/general/tasks/pending',
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 68),
                                    'title' => 'Permohonan Permit',
                                    'icon' => 'memo-circle-check',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/permit/new',
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 118),
                                    'title' => 'Permohonan CPC',
                                    'icon' => 'flag-checkered',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/cpc/new',
                                ]
                            ]
                        ];
                        break;

                    case 'permit':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 31),
                                    'title' => 'Permohonan KIL',
                                    'icon' => 'file-lines',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/pkil'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 70),
                                    'title' => 'Permohonan KPK',
                                    'icon' => 'memo',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/pkpk'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 73),
                                    'title' => 'Perakuan Permit Kerja',
                                    'icon' => 'memo-circle-check',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/ppk'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'money-from-bracket',
                                    'color' => 'warning',
                                    'route' => '/operation/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    case 'project':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 13),
                                    'title' => 'Laporan LTA',
                                    'icon' => 'files',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/reportlta',
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 17),
                                    'title' => 'Pindaan Pelan TP',
                                    'icon' => 'file-pen',
                                    'color' => 'primary',
                                    'route' => '/operation/general/tasks/amendtp',
                                    // ],
                                    // (object) [
                                    //     'count' => $this->getCountTask(statusId: 25),
                                    //     'title' => 'Semakan PIL',
                                    //     'icon' => 'hourglass-clock fa-fade',
                                    //     'color' => 'warning',
                                    //     'route' => '/operation/general/tasks/checkpil',
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tertunggak',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/operation/general/tasks/pending',
                                ],
                            ]
                        ];
                        break;

                    default:
                        $tasks = [];
                        break;
                }
                break;

            case 'geospatial':
                switch ($subDepartment) {
                    case 'geospatial':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 8),
                                    'title' => 'Pelan Cdg. Laluan',
                                    'icon' => 'map-pin',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/pcl'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 24),
                                    'title' => 'Pelan Izin Lalu',
                                    'icon' => 'map-location-dot',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/pil'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 61),
                                    'title' => 'GIS Ready',
                                    'icon' => 'globe',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/gisReady/new'
                                ]
                            ]
                        ];
                        break;

                    case 'translation':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 61),
                                    'title' => 'GIS Ready',
                                    'icon' => 'globe',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/gisReady/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/geospatial/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    case 'charting':
                        // Handle GIS tasks
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 8),
                                    'title' => 'PCL',
                                    'icon' => 'map-pin',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/pcl',
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 24),
                                    'title' => 'PIL',
                                    'icon' => 'map-location-dot',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/pil'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/geospatial/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    default:
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 8),
                                    'title' => 'Pelan Cdg. Laluan',
                                    'icon' => 'map-pin',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/pcl'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 24),
                                    'title' => 'Pelan Izin Lalu',
                                    'icon' => 'map-location-dot',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/pil'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 61),
                                    'title' => 'GIS Ready',
                                    'icon' => 'globe',
                                    'color' => 'primary',
                                    'route' => '/geospatial/general/tasks/gisReady/new'
                                ]
                            ]
                        ];
                        break;
                }
                break;

            case 'hr':
                $tasks = [
                    0 => [
                        (object) [
                            'count' => $this->getCountTask(statusId: 144),
                            'title' => 'Penyediaan Dokumen Permohonan PWC',
                            'icon' => 'file-lines',
                            'color' => 'primary',
                            'route' => '/operation/general/tasks/new'
                        ],
                        (object) [
                            'count' => $this->getCountTask(statusId: 145),
                            'title' => 'Semakan Dokumen Permohonan PWC',
                            'icon' => 'file-lines',
                            'color' => 'primary',
                            'route' => '/operation/general/tasks/new'
                        ]
                    ]
                ];
                // $tasks = [
                //     (object) [
                //         'count' => $this->getCountTask(statusId: 144),
                //         'title' => 'Penyediaan Dokumen Permohonan PWC',
                //         'icon' => 'file-lines',
                //         'color' => 'primary',
                //         'route' => '/operation/general/tasks/new'
                //     ],
                //     (object) [
                //         'count' => $this->getCountTask(statusId: 145),
                //         'title' => 'Semakan Dokumen Permohonan PWC',
                //         'icon' => 'file-lines',
                //         'color' => 'primary',
                //         'route' => '/operation/general/tasks/new'
                //     ]
                // ];
                break;

            case 'mapping':
                switch ($subDepartment) {
                    case 'survey':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 63),
                                    'title' => 'Serahan Data Lot',
                                    'icon' => 'check-to-slot',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/datalot'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 48),
                                    'title' => 'Pengesahan Kerja',
                                    'icon' => 'file-circle-check ',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/approvepiu'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/mapping/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    case 'plan':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 63),
                                    'title' => 'Serahan Data Lot',
                                    'icon' => 'check-to-slot',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/datalot'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 48),
                                    'title' => 'Pengesahan Kerja',
                                    'icon' => 'file-circle-check ',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/approvepiu'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/mapping/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;

                    case 'mapping':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTask('new'),
                                    'title' => 'Tugasan Baru',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/new'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 63),
                                    'title' => 'Serahan Data Lot',
                                    'icon' => 'check-to-slot',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/datalot'
                                ],
                                (object) [
                                    'count' => $this->getCountTask(statusId: 48),
                                    'title' => 'Pengesahan Kerja',
                                    'icon' => 'file-circle-check ',
                                    'color' => 'primary',
                                    'route' => '/mapping/general/tasks/approvepiu'
                                ],
                                (object) [
                                    'count' => $this->getCountTask('pending'),
                                    'title' => 'Tunggakan',
                                    'icon' => 'hourglass-clock fa-fade',
                                    'color' => 'warning',
                                    'route' => '/mapping/general/tasks/pending'
                                ]
                            ]
                        ];
                        break;
                }
                break;

            case 'board':
                switch ($subDepartment) {
                    case 'director':
                        $tasks = [
                            0 => [
                                (object) [
                                    'count' => $this->getCountTotal('application'),
                                    'title' => 'Jumlah Permohonan',
                                    'icon' => 'briefcase',
                                    'color' => 'primary',
                                    'route' => ''
                                ],
                                (object) [
                                    'count' => $this->getCountTotal('invoice'),
                                    'title' => 'Jumlah Invoice',
                                    'icon' => 'file-invoice-dollar',
                                    'color' => 'primary',
                                    'route' => ''
                                ],
                                (object) [
                                    'count' => $this->getCountTotal('appl_length'),
                                    'title' => 'Jarak Permohonan',
                                    'icon' => 'map-location-dot',
                                    'color' => 'primary',
                                    'route' => ''
                                ],
                                (object) [
                                    'count' => $this->getCountTotal('survey_length'),
                                    'title' => 'Jarak Ukur',
                                    'icon' => 'angle',
                                    'color' => 'primary',
                                    'route' => ''
                                ]
                            ]
                        ];
                        break;
                }
                break;

            default:
                $tasks = [0 => []];
                break;
        }

        return $tasks[$which];
    }

    private function queryBarData($category, $type, $filter)
    {
        if ($type === 'overview') {
            switch ($category) {
                case 'utilityPayment':
                    $query = "SELECT
                    'year' AS category,
                    SUM(amount_invoices) - SUM(amount_paid) AS balance,
                    SUM(amount_paid) AS paid,
                    flw_appl_entries.utility_provider AS provider
                    FROM
                    flw_invoices
                    LEFT JOIN
                    flw_appl_entries ON flw_appl_entries.system_id = flw_invoices.system_id
                    WHERE
                    EXTRACT(YEAR FROM flw_invoices.created_at) = EXTRACT(YEAR FROM NOW())
                    GROUP BY
                    flw_appl_entries.utility_provider
                    UNION ALL
                    SELECT
                    'month' AS category,
                    SUM(amount_invoices) - SUM(amount_paid) AS balance,
                    SUM(amount_paid) AS paid,
                    flw_appl_entries.utility_provider AS provider
                    FROM
                    flw_invoices
                    LEFT JOIN
                    flw_appl_entries ON flw_appl_entries.system_id = flw_invoices.system_id
                    WHERE
                    EXTRACT(MONTH FROM flw_invoices.created_at) = EXTRACT(MONTH FROM NOW())
                    GROUP BY
                    flw_appl_entries.utility_provider
                    ORDER BY
                    balance DESC
                    LIMIT 5";

                    $list = $this->conn->query($query)->fetchAll(PDO::FETCH_OBJ);

                    $query = "SELECT
                        category,
                        jsonb_agg(provider) AS provider,
                        jsonb_agg(amount_paid) AS paid,
                        jsonb_agg(balance) AS balance
                        FROM (
                        SELECT
                            'year' AS category,
                            flw_appl_entries.utility_provider AS provider,
                        SUM(flw_invoices.amount_paid) AS amount_paid,
                            SUM(flw_invoices.amount_invoices - flw_invoices.amount_paid) AS balance
                        FROM
                            flw_invoices
                        LEFT JOIN
                            flw_appl_entries ON flw_appl_entries.system_id = flw_invoices.system_id
                        WHERE
                            EXTRACT(YEAR FROM flw_invoices.created_at) = EXTRACT(YEAR FROM NOW())
                        GROUP BY flw_appl_entries.utility_provider
                        UNION ALL
                        SELECT
                            'month' AS category,
                            flw_appl_entries.utility_provider AS provider,
                            SUM(flw_invoices.amount_paid) AS amount_paid,
                            SUM(flw_invoices.amount_invoices - flw_invoices.amount_paid) AS balance
                        FROM
                            flw_invoices
                        LEFT JOIN
                            flw_appl_entries ON flw_appl_entries.system_id = flw_invoices.system_id
                        WHERE
                            EXTRACT(MONTH FROM flw_invoices.created_at) = EXTRACT(MONTH FROM NOW())
                        GROUP BY flw_appl_entries.utility_provider
                        ) AS subquery
                        GROUP BY
                        category
                        ORDER BY
                        category DESC
                        LIMIT 5";

                    $chart = $this->conn->query($query)->fetchAll(PDO::FETCH_OBJ);

                    $listing = [];
                    foreach ($list as $index => $record) {
                        $categoryIndex = ($record->category === 'year') ? 0 : 1;

                        $listing[$categoryIndex][] = (object) [
                            'balance' => $this->formatNumber($record->balance, 'currency')->number,
                            'prefix' => $this->formatNumber($record->balance, 'currency')->prefix,
                            'suffix' => $this->formatNumber($record->balance, 'currency')->suffix,
                            'paid' => $this->formatNumber($record->paid, 'currency')->number,
                            'decimal' => 2,
                            'provider' => $this->getProvider($record->provider),
                        ];
                    }

                    foreach ($chart as $index => $record) {
                        $providerIds = json_decode($record->provider);

                        // Check if json_decode was successful
                        if ($providerIds && is_array($providerIds)) {
                            $providerNames = [];
                            foreach ($providerIds as $id) {
                                // Assuming your API returns an object with a 'sort_name' property
                                $providerData = $this->getProvider($id);

                                // Check if the request was successful and the data is valid
                                if ($providerData && isset($providerData->sort_name)) {
                                    $providerNames[] = $providerData->sort_name;
                                }
                            }
                            $record->provider = json_encode($providerNames);
                        } else {
                            // If it's not a valid JSON array, assume it's a single ID
                            $providerData = $this->getProvider($record->provider);

                            // Check if the request was successful and the data is valid
                            $record->provider = $providerData ? json_encode([$providerData->sort_name]) : [];
                        }
                    }

                    $charting = [];
                    foreach ($chart as $index => $record) {
                        $charting[] = (object) [
                            'tab' => "{$category}_tab_{$index}",
                            'id' => "{$category}_bar_{$index}",
                            'type' => 'stacked',
                            'style' => 'column',
                            'height' => 250,
                            'category' => $record->category,
                            'labels' => $record->provider,
                            'series' => '[' . $record->paid . ', ' . $record->balance . ']',
                            'name' => '["Bayaran","Tunggakan"]'
                        ];
                    }
                    $data = (object) [
                        "counter" => [
                            0 => (object) [
                                "title" => "Tahunan",
                                "counter" => $this->formatNumber($this->getCountData("utilityPayment", "yearly"), 'currency')->number,
                                "prefix" => $this->formatNumber($this->getCountData("utilityPayment", "monthly"), 'currency')->prefix,
                                "suffix" => $this->formatNumber($this->getCountData("utilityPayment", "yearly"), 'currency')->suffix,
                                "decimal" => 2
                            ],
                            1 => (object) [
                                "title" => "Bulanan",
                                "counter" => $this->formatNumber($this->getCountData("utilityPayment", "monthly"), 'currency')->number,
                                "prefix" => $this->formatNumber($this->getCountData("utilityPayment", "monthly"), 'currency')->prefix,
                                "suffix" => $this->formatNumber($this->getCountData("utilityPayment", "yearly"), 'currency')->suffix,
                                "decimal" => 2
                            ],
                        ],
                        'lists' => array_values($listing),
                        'data' => $charting
                    ];

                    break;
                default:
                    break;
            }
        }

        return $data;
    }

    private function getBarData()
    {
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;
        $paymentYearly = $this->queryBarData("utilityPayment", 'overview', "yearly");

        $bars = [];

        switch ($department) {
            case 'finance':
                switch ($subDepartment) {
                    case 'finance':
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;
                }
                break;

            case 'management':
                switch ($subDepartment) {
                    case 'business':
                        // Handle Business tasks
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    case 'project':
                        // Handle Business tasks
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    case 'permit':
                        // Handle Business tasks
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    case 'finance':
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    case 'executive':
                        // Handle Business tasks
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    default:
                        $bars[] = [];
                        break;
                }
                break;

            case 'operation':
                switch ($subDepartment) {
                    case 'registration':
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    case 'permit':
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;

                    default:
                        $bars[] = [];
                        break;
                }
                break;

            case 'geospatial':
                switch ($subDepartment) {
                    case 'geospatial':
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;
                }
                break;
            case 'board':
                switch ($subDepartment) {
                    case 'director':
                        $bars[] = (object) [
                            "title" => "Kutipan Penyedia Utiliti",
                            "subtitle" => "Mengikut 5 Teratas",
                            "category" => "utilityPayment",
                            "charts" => $paymentYearly,
                        ];
                        break;
                }
                break;
        }

        return $bars;
    }
    private function getMapData()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;
        $list = new stdClass();

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'project':
                        $list = (object) [
                            "count" => "",
                            "title" => "Senarai Kerja Pengukuran Terkini",
                            "subtitle" => "Jumlah Kerja Ukur Minggu Ini",
                            "route" => "/tracker/application"
                        ];
                        break;

                    case 'geospatial':
                        $list = (object) [
                            "count" => "",
                            "title" => "Senarai Kerja Pelan Terkini",
                            "subtitle" => "Jumlah Kerja Pelan Minggu Ini",
                            "route" => "/tracker/application"
                        ];
                        break;
                }
                break;

            case 'operation':
                switch ($subDepartment) {
                    case 'project':
                        $list = (object) [
                            "count" => "",
                            "title" => "Senarai Kerja Pengukuran Terkini",
                            "subtitle" => "Jumlah Kerja Ukur Minggu Ini",
                            "route" => "/tracker/application"
                        ];
                        break;
                }
                break;

            case 'geospatial':
                switch ($subDepartment) {
                    case 'charting':
                        $list = (object) [
                            "count" => "",
                            "title" => "Senarai Kerja Pelan Terkini",
                            "subtitle" => "Jumlah Kerja Pelan Minggu Ini",
                            "route" => "/tracker/application"
                        ];
                        break;
                }
                break;

            default:
                break;
        }

        return $list;
    }

    private function getWorkloadData()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        switch ($subDepartment) {
            case 'registration':
                break;
            case 'finance':
                break;
            case 'geospatial':
                $workload = (object) [
                    "title" => "Bilangan Kerja Staf",
                    "route" => "/tracker/application"
                ];
                break;
            case 'translation':
                $workload = (object) [
                    "title" => "Bilangan Kerja Staf",
                    "route" => "/tracker/application"
                ];
                break;
            case 'charting':
                $workload = (object) [
                    "title" => "Bilangan Kerja Staf",
                    "route" => "/tracker/application"
                ];
                break;
            case 'project':
                break;
            default:
                break;
        }

        return $workload;
    }

    private function queryPieData($category, $type, $filter = 'yearly')
    {
        
        switch ($category) {
            case 'lengthCode':
                $query = "SELECT
                'year' AS category,
                ARRAY(SELECT SUM(1) FROM flw_appl_entries WHERE EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) GROUP BY length_code) AS series,
                ARRAY(SELECT DISTINCT length_code FROM flw_appl_entries WHERE EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) GROUP BY length_code) AS labels
				UNION
                SELECT
                    'week' AS category,
                    ARRAY(SELECT SUM(1) FROM flw_appl_entries WHERE EXTRACT(WEEK FROM created_at) = EXTRACT(WEEK FROM CURRENT_DATE) AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) GROUP BY length_code) AS series,
                    ARRAY(SELECT DISTINCT length_code FROM flw_appl_entries WHERE EXTRACT(WEEK FROM created_at) = EXTRACT(WEEK FROM CURRENT_DATE) AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) GROUP BY length_code) AS labels
                UNION
				SELECT
                    'month' AS category,
                    ARRAY(SELECT SUM(1) FROM flw_appl_entries WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) GROUP BY length_code) AS series,
                    ARRAY(SELECT DISTINCT length_code FROM flw_appl_entries WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE) AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE) GROUP BY length_code) AS labels
                ORDER BY category DESC";
            case 'payment':
                break;
            default:
                break;
        }


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $data = [];
        foreach ($result as $index => $row) {
            if ($filter === 'yearly') {
                if ($row->category === 'year' || $row->category === 'month') {
                    $label = explode(",", trim($row->labels, '{}'));
                    $labels = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $label);
                    $data[] = (object) [
                        "type" => $type,
                        "tab" => "tab_{$category}_{$index}",
                        "id" => "pie_{$category}_{$index}",
                        "category" => $row->category,
                        "labels" => "[" . implode(", ", $labels) . "]",
                        "series" => str_replace(['{', '}'], ['[', ']'], $row->series),
                    ];
                }
            } else if ($filter === 'monthly') {
                if ($row->category === 'month' || $row->category === 'year') {
                    $label = explode(",", trim($row->labels, '{}'));
                    $labels = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $label);
                    $data[] = (object) [
                        "type" => $type,
                        "tab" => "tab_{$category}_{$index}",
                        "id" => "pie_{$category}_{$index}",
                        "category" => $row->category,
                        "labels" => "[" . implode(", ", $labels) . "]",
                        "series" => str_replace(['{', '}'], ['[', ']'], $row->series),
                    ];
                }
            } else {
                $label = explode(",", trim($row->labels, '{}'));
                $labels = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $label);
                $data[] = (object) [
                    "type" => $type,
                    "tab" => "tab_{$category}_{$index}",
                    "id" => "pie_{$category}_{$index}",
                    "category" => $row->category,
                    "labels" => "[" . implode(", ", $labels) . "]",
                    "series" => str_replace(['{', '}'], ['[', ']'], $row->series),
                ];
            }
        }
        return $data;
    }

    private function getPieData()
    {


        $pie[] = (object) [
            "title" => "Permohonan Diterima",
            "subtitle" => "Mengikut Kategori Jarak",
            "category" => "lengthCode",
            "charts" => $this->queryPieData("lengthCode", "donut", "yearly"),

        ];

        return $pie;
    }

    private function querylineAllData($category, $filter = NULL)
    {
        

        switch ($category) {
            case 'applications':
                $style = 'smooth';
                $query = "SELECT
                EXTRACT(YEAR FROM application_date)::TEXT AS year,
                jsonb_build_array(
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 1 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 2 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 3 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 4 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 5 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 6 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 7 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 8 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 9 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 10 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 11 THEN id END),
                  COUNT(CASE WHEN EXTRACT(MONTH FROM application_date) = 12 THEN id END)
                ) AS series,
                jsonb_build_array('Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogs', 'Sep', 'Okt', 'Nov', 'Dis') AS labels
              FROM flw_appl_entries
              GROUP BY EXTRACT(YEAR FROM application_date)
              ORDER BY EXTRACT(YEAR FROM application_date)";
                break;
            case 'paid':
                $style = 'stepline';
                $query = "SELECT
                EXTRACT(YEAR FROM created_at)::TEXT AS year,
                jsonb_build_array(
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 1 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 2 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 3 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 4 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 5 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 6 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 7 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 8 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 9 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 10 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 11 THEN amount_paid END), 0),
                    COALESCE(SUM(CASE WHEN EXTRACT(MONTH FROM created_at) = 12 THEN amount_paid END), 0)
                ) AS series,
                jsonb_build_array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec') AS labels
            FROM flw_invoices
            GROUP BY EXTRACT(YEAR FROM created_at)
            ORDER BY EXTRACT(YEAR FROM created_at)";
                break;
            default:
                break;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $year = [];
        $series = [];

        foreach ($result as $data) {
            $year[] = $data->year;
            $series[] = $data->series;
        }
        $name = array_map(function ($element) {
            return '"' . $element . '"';
        }, $year);

        $data = (object) [
            "id" => "line_all_{$category}",
            "labels" => $data->labels,
            "name" => "[" . implode(', ', $name) . "]",
            "series" => "[" . implode(', ', $series) . "]",
            "style" => $style
        ];

        return $data;
    }

    private function getLineData($which = 0)
    {
        $department = $this->Role->{$this->User}->department;

        $subDepartment = $this->Role->{$this->User}->sub_department;
        $overall = $this->formatNumber($this->getCountData("length", "overall"), "length");
        $yearly = $this->formatNumber($this->getCountData("length", "yearly"), "length");
        $monthly = $this->formatNumber($this->getCountData("length", "monthly"), "length");
        $weekly = $this->formatNumber($this->getCountData("length", "weekly"), "length");

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'operation':
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Permohonan Terkini",
                                "subtitle" => "Mengikut Jarak & Tahun Semasa",
                                "chart" => $this->querylineAllData("applications", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Keseluruhan",
                                        "counter" => $overall->number,
                                        "prefix" => $overall->prefix,
                                        "suffix" => $overall->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $yearly->number,
                                        "prefix" => $yearly->prefix,
                                        "suffix" => $yearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $monthly->number,
                                        "prefix" => $monthly->prefix,
                                        "suffix" => $monthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "3" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $weekly->number,
                                        "prefix" => $weekly->prefix,
                                        "suffix" => $weekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case 'finance':
                        $countYearly = $this->formatNumber($this->getCountData("paid", "yearly"), "currency");
                        $countMonthly = $this->formatNumber($this->getCountData("paid", "monthly"), "currency");
                        $countWeekly = $this->formatNumber($this->getCountData("paid", "weekly"), "currency");
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Bayaran Caj Perkhidmatan Terkini",
                                "subtitle" => "Mengikut Tahun Semasa",
                                "chart" => $this->querylineAllData("paid", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $countYearly->number,
                                        "prefix" => $countYearly->prefix,
                                        "suffix" => $countYearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $countMonthly->number,
                                        "prefix" => $countYearly->prefix,
                                        "suffix" => $countMonthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $countWeekly->number,
                                        "prefix" => $countYearly->prefix,
                                        "suffix" => $countWeekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ]
                        ];
                        break;
                    case 'business':
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Permohonan Terkini",
                                "subtitle" => "Mengikut Jarak & Tahun Semasa",
                                "chart" => $this->querylineAllData("applications", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Keseluruhan",
                                        "counter" => $overall->number,
                                        "prefix" => $overall->prefix,
                                        "suffix" => $overall->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $yearly->number,
                                        "prefix" => $yearly->prefix,
                                        "suffix" => $yearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $monthly->number,
                                        "prefix" => $monthly->prefix,
                                        "suffix" => $monthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "3" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $weekly->number,
                                        "prefix" => $weekly->prefix,
                                        "suffix" => $weekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case 'executive':
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Permohonan Terkini",
                                "subtitle" => "Mengikut Jarak & Tahun Semasa",
                                "chart" => $this->querylineAllData("applications", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Keseluruhan",
                                        "counter" => $overall->number,
                                        "prefix" => $overall->prefix,
                                        "suffix" => $overall->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $yearly->number,
                                        "prefix" => $yearly->prefix,
                                        "suffix" => $yearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $monthly->number,
                                        "prefix" => $monthly->prefix,
                                        "suffix" => $monthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "3" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $weekly->number,
                                        "prefix" => $weekly->prefix,
                                        "suffix" => $weekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ],
                        ];
                        break;
                        case 'mapping':
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Permohonan Terkini",
                                "subtitle" => "Mengikut Jarak & Tahun Semasa",
                                "chart" => $this->querylineAllData("applications", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Keseluruhan",
                                        "counter" => $overall->number,
                                        "prefix" => $overall->prefix,
                                        "suffix" => $overall->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $yearly->number,
                                        "prefix" => $yearly->prefix,
                                        "suffix" => $yearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $monthly->number,
                                        "prefix" => $monthly->prefix,
                                        "suffix" => $monthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "3" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $weekly->number,
                                        "prefix" => $weekly->prefix,
                                        "suffix" => $weekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ],
                        ];
                        break;
                }
                break;
            case 'board':
                switch ($subDepartment) {
                    case 'director':
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Permohonan Terkini",
                                "subtitle" => "Mengikut Jarak & Tahun Semasa",
                                "chart" => $this->querylineAllData("applications", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Keseluruhan",
                                        "counter" => $overall->number,
                                        "prefix" => $overall->prefix,
                                        "suffix" => $overall->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $yearly->number,
                                        "prefix" => $yearly->prefix,
                                        "suffix" => $yearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $monthly->number,
                                        "prefix" => $monthly->prefix,
                                        "suffix" => $monthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "3" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $weekly->number,
                                        "prefix" => $weekly->prefix,
                                        "suffix" => $weekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ],
                        ];
                        break;
                }
                break;
            default:
                switch ($department) {
                    case 'operation':
                    case 'permit':
                    case 'survey':
                    case 'mapping':
                    case 'plan':
                    case 'registration':
                    case 'geospatial':
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Permohonan Terkini",
                                "subtitle" => "Mengikut Jarak & Tahun Semasa",
                                "chart" => $this->querylineAllData("applications", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Keseluruhan",
                                        "counter" => $overall->number,
                                        "prefix" => $overall->prefix,
                                        "suffix" => $overall->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $yearly->number,
                                        "prefix" => $yearly->prefix,
                                        "suffix" => $yearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $monthly->number,
                                        "prefix" => $monthly->prefix,
                                        "suffix" => $monthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "3" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $weekly->number,
                                        "prefix" => $weekly->prefix,
                                        "suffix" => $weekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case 'finance':
                        $countYearly = $this->formatNumber($this->getCountData("paid", "yearly"), "currency");
                        $countMonthly = $this->formatNumber($this->getCountData("paid", "monthly"), "currency");
                        $countWeekly = $this->formatNumber($this->getCountData("paid", "weekly"), "currency");
                        $line = [
                            0 => (object) [
                                "title" => "Jumlah Bayaran Caj Perkhidmatan Terkini",
                                "subtitle" => "Mengikut Tahun Semasa",
                                "chart" => $this->querylineAllData("paid", "yearly"),
                                "counter" => (object) [
                                    "0" => (object) [
                                        "title" => "Tahunan",
                                        "counter" => $countYearly->number,
                                        "prefix" => $countYearly->prefix,
                                        "suffix" => $countYearly->suffix,
                                        "decimal" => 2
                                    ],
                                    "1" => (object) [
                                        "title" => "Bulanan",
                                        "counter" => $countMonthly->number,
                                        "prefix" => $countYearly->prefix,
                                        "suffix" => $countMonthly->suffix,
                                        "decimal" => 2
                                    ],
                                    "2" => (object) [
                                        "title" => "Mingguan",
                                        "counter" => $countWeekly->number,
                                        "prefix" => $countYearly->prefix,
                                        "suffix" => $countWeekly->suffix,
                                        "decimal" => 2
                                    ],
                                ],
                            ]
                        ];
                        break;
                    default:
                        $line = [0 => []];
                        break;
                }
                break;
        }

        return $line[$which];
    }

    private function getStaffAttendance()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'mapping':
                        $activities = (object) [
                            "title" => "Aktiviti Hari Ini",
                            "subtitle" => "Semak aktiviti staf",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    default:
                        break;
                }
                break;

            case 'mapping':
                switch ($subDepartment) {
                    case 'plan':
                        $activities = (object) [
                            "title" => "Aktiviti Hari Ini",
                            "subtitle" => "Daftar Masuk & Keluar",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    case 'survey':
                        $activities = (object) [
                            "title" => "Aktiviti Hari Ini",
                            "subtitle" => "Daftar Masuk & Keluar",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    case 'mapping':
                        $activities = (object) [
                            "title" => "Aktiviti Hari Ini",
                            "subtitle" => "Daftar Masuk & Keluar",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    default:
                        break;
                }
                break;

            case 'geospatial':
                switch ($subDepartment) {
                    case 'geospatial':
                        $activities = (object) [
                            "title" => "Bilangan Kerja Staf",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    case 'translation':
                        $activities = (object) [
                            "title" => "Bilangan Kerja Staf",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    case 'charting':
                        $activities = (object) [
                            "title" => "Bilangan Kerja Staf",
                            "clockIn" => "10.30am",
                            "clockOut" => "2.00pm",
                            "count" => $this->getCountTask(statusId: 74),
                            "status" => "Berjaya",
                            "route" => "/tracker/application"
                        ];
                        break;
                    case 'project':
                        break;
                    default:
                        break;
                }
                break;

            default:
                break;
        }

        return $activities;
    }

    private function getDistanceOverview()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        $distance = new stdClass();

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'mapping':
                        $distance = [
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Mohon',
                                'icon' => 'files',
                                'color' => 'info',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Ukur',
                                'icon' => 'angle',
                                'color' => 'success',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Beza Jarak',
                                'icon' => 'file-plus-minus',
                                'color' => 'warning',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak GIS',
                                'icon' => 'globe',
                                'color' => 'danger',
                            ]
                        ];
                        break;
                    default:
                        break;
                }
                break;

            case 'mapping':
                switch ($subDepartment) {
                    case 'plan':
                        $distance = [
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Mohon',
                                'icon' => 'files',
                                'color' => 'info',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Ukur',
                                'icon' => 'angle',
                                'color' => 'success',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Beza Jarak',
                                'icon' => 'file-plus-minus',
                                'color' => 'warning',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak GIS',
                                'icon' => 'globe',
                                'color' => 'danger',
                            ]
                        ];
                        break;
                    case 'survey':
                        $distance = [
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Mohon',
                                'icon' => 'files',
                                'color' => 'info',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Ukur',
                                'icon' => 'angle',
                                'color' => 'success',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Beza Jarak',
                                'icon' => 'file-plus-minus',
                                'color' => 'warning',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak GIS',
                                'icon' => 'globe',
                                'color' => 'danger',
                            ]
                        ];
                        break;
                    case 'mapping':
                        $distance = [
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Mohon',
                                'icon' => 'files',
                                'color' => 'info',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak Ukur',
                                'icon' => 'angle',
                                'color' => 'success',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Beza Jarak',
                                'icon' => 'file-plus-minus',
                                'color' => 'warning',
                            ],
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => '2790',
                                'title' => 'Jarak GIS',
                                'icon' => 'globe',
                                'color' => 'danger',
                            ]
                        ];
                        break;
                    default:
                        break;
                }
                break;

            default:
                $distance = [];
                break;
        }

        return $distance;
    }

    private function getTaskWeekly()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        $distance = new stdClass();

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'permit':
                        $distance = [
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => $this->getCountTask(statusId: 32),
                                'title' => 'Permohonan IL Dalam Proses',
                                'subtitle' => 'Permohonan IL yang perlu disemak',
                                'icon' => 'angle',
                                'color' => 'primary',
                                'percent' => '85%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 34),
                                'title' => 'Kelulusan Izin Lalu',
                                'subtitle' => 'Peratusan IL yang lulus',
                                'icon' => 'pen',
                                'color' => 'primary',
                                'percent' => '60%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 73),
                                'title' => 'Kelulusan Permit',
                                'subtitle' => 'Peratusan permit yang lulus',
                                'icon' => 'user',
                                'color' => 'primary',
                                'percent' => '85%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 93),
                                'title' => 'Permohonan Permit Tertunggak',
                                'subtitle' => 'Permohonan permit yang belum lengkap',
                                'icon' => 'business-time',
                                'color' => 'warning',
                                'percent' => '15%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 93),
                                'title' => 'Permohonan Permit Tertunggak',
                                'subtitle' => 'Permohonan permit yang belum lengkap',
                                'icon' => 'business-time',
                                'color' => 'warning',
                                'percent' => '15%',
                            ]
                        ];
                        break;

                    default:
                        break;
                }
                break;

            case 'operation':
                switch ($subDepartment) {
                    case 'permit':
                        $distance = [
                            (object) [
                                // 'count' => $this->getCountTask(statusId: 2),
                                'count' => $this->getCountTask(statusId: 32),
                                'title' => 'Permohonan IL Dalam Proses',
                                'subtitle' => 'Permohonan IL yang perlu disemak',
                                'icon' => 'angle',
                                'color' => 'primary',
                                'percent' => '85%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 34),
                                'title' => 'Kelulusan Izin Lalu',
                                'subtitle' => 'Peratusan IL yang lulus',
                                'icon' => 'pen',
                                'color' => 'primary',
                                'percent' => '60%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 73),
                                'title' => 'Kelulusan Permit',
                                'subtitle' => 'Peratusan permit yang lulus',
                                'icon' => 'user',
                                'color' => 'primary',
                                'percent' => '85%',
                            ],
                            (object) [
                                'count' => $this->getCountTask(statusId: 93),
                                'title' => 'Permohonan Permit Tertunggak',
                                'subtitle' => 'Permohonan permit yang belum lengkap',
                                'icon' => 'business-time',
                                'color' => 'warning',
                                'percent' => '15%',
                            ]
                        ];
                        break;

                    default:
                        break;
                }
                break;

            default:
                $distance = [];
                break;
        }

        return $distance;
    }

    private function getPercentOverview()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        $data = new stdClass();

        switch ($department) {
            case 'operation':
                switch ($subDepartment) {
                    case 'permit':
                        $data =
                            [
                                (object) [
                                    'title1' => 'Izin Lalu',
                                    'title2' => 'Permit',
                                    'percent1' => $this->getCountTask(statusId: 34),
                                    'percent2' => $this->getCountTask(statusId: 73),
                                    'color1' => 'primary',
                                    'color2' => 'primary',
                                ]
                            ];
                        break;

                    default:
                        break;
                }
                break;

            case 'management':
                switch ($subDepartment) {
                    case 'permit':
                        $data =
                            [
                                (object) [
                                    'title1' => 'Izin Lalu',
                                    'title2' => 'Permit',
                                    'percent1' => $this->getCountTask(statusId: 34),
                                    'percent2' => $this->getCountTask(statusId: 73),
                                    'color1' => 'primary',
                                    'color2' => 'primary',
                                ]
                            ];
                        break;

                    case 'project':
                        $data =
                            [
                                (object) [
                                    'title1' => 'CPC',
                                    'title2' => 'CCC/CMGD/PWC',
                                    'percent1' => $this->getCountTask(statusId: 121),
                                    'percent2' => $this->getCountTask(statusId: 143),
                                    'color1' => 'primary',
                                    'color2' => 'primary',
                                ]
                            ];
                        break;

                    default:
                        break;
                }
                break;

            default:
                $data = [];
                break;
        }

        return $data;
    }

    private function getListPriority()
    {
        
        $department = $this->Role->{$this->User}->department;
        $subDepartment = $this->Role->{$this->User}->sub_department;

        switch ($department) {
            case 'management':
                switch ($subDepartment) {
                    case 'project':
                        $list = [
                            (object) [
                                "color" => "info",
                                "refNo" => "KUP/DIGI/A3/12/23/0016",
                                "timeDate" => "10:00 am - 06 Jan 23",
                            ],
                            (object) [
                                "color" => "warning",
                                "refNo" => "KUP/CLM/A3/12/23/0018",
                                "timeDate" => "2:00 pm - 14 Feb 23",
                            ],
                            (object) [
                                "color" => "success",
                                "refNo" => "KUP/TTDC/A3/12/23/0014",
                                "timeDate" => "3:00 pm - 04 Apr 23",
                            ],
                            (object) [
                                "color" => "danger",
                                "refNo" => "KUP/FGV/A3/12/23/0019",
                                "timeDate" => "9:00 am - 30 Mei 23",
                            ],
                            (object) [
                                "color" => "primary",
                                "refNo" => "KUP/TNB/A3/12/23/0022",
                                "timeDate" => "12:00 pm - 01 Jun 23",
                            ]
                        ];
                        break;
                    default:
                        break;
                }
                break;

            case 'operation':
                switch ($subDepartment) {
                    case 'project':
                        $list = [
                            (object) [
                                "color" => "info",
                                "refNo" => "KUP/DIGI/A3/12/23/0016",
                                "timeDate" => "10:00 am - 06 Jan 23",
                            ],
                            (object) [
                                "color" => "warning",
                                "refNo" => "KUP/CLM/A3/12/23/0018",
                                "timeDate" => "2:00 pm - 14 Feb 23",
                            ],
                            (object) [
                                "color" => "success",
                                "refNo" => "KUP/TTDC/A3/12/23/0014",
                                "timeDate" => "3:00 pm - 04 Apr 23",
                            ],
                            (object) [
                                "color" => "danger",
                                "refNo" => "KUP/FGV/A3/12/23/0019",
                                "timeDate" => "9:00 am - 30 Mei 23",
                            ],
                            (object) [
                                "color" => "primary",
                                "refNo" => "KUP/TNB/A3/12/23/0022",
                                "timeDate" => "12:00 pm - 01 Jun 23",
                            ]
                        ];
                        break;
                    default:
                        break;
                }
                break;

            default:
                break;
        }

        return $list;
    }

    public function __call($method, $args)
    {

        $which = isset($args[0]) ? $args[0] : 0;

        switch ($method) {
            case 'smallTaskOverview':
                $getData = $this->getOverviewData($which);
                break;
            case 'mediumTaskOverview':
                $getData = $this->getOverviewData();
                break;
            case 'bigTaskOverview':
                $getData = $this->getOverviewData();
                break;
            case 'summarySlider':
                $getData = $this->getSliderData();
                break;
            case 'gaugeChart':
                $getData = $this->getGaugeData();
                break;
            case 'barChart':
                $getData = $this->getBarData();
                break;
            case 'tableList':
                $getData = $this->getTableList($which);
                break;
            case 'smallMapData':
                $getData = $this->getMapData();
                break;
            case 'bigMapData':
                $getData = $this->getMapData();
                break;
            case 'workloadData':
                $getData = $this->getWorkloadData();
                break;
            case 'pieChart':
                $getData = $this->getPieData();
                break;
            case 'radialChart':
                $getData = $this->getRadialData();
                break;
            case 'lineChart':
                $getData = $this->getLineData($which);
                break;
            case 'staffAttendance':
                $getData = $this->getStaffAttendance();
                break;
            case 'distanceOverview':
                $getData = $this->getDistanceOverview();
                break;
            case 'taskWeekly':
                $getData = $this->getTaskWeekly();
                break;
            case 'smallPercentOverview':
                $getData = $this->getPercentOverview();
                break;
            case 'bigPercentOverview':
                $getData = $this->getPercentOverview();
                break;
            case 'listPriority':
                $getData = $this->getListPriority();
                break;
            case 'listProvider':
                $getData = $this->getProvidersList();
                break;
            default:
                $getData = [];
                break;
        }

        return $this->Widget->get($method, $getData);
    }
}
