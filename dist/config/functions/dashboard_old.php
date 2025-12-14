<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
class DashboardOld{
    public static function accountWOWidget($selection)
    {
        // Selection details
        // 0 =
        // 1 =
        // 2 =
        // 3 =
        // 4 =
        // 5 =
        // 6 =
        // 7 =
        // 8 =
        // 9 =

        // Include the database connection parameters
        global $PDO;
        include "config/tenant.php";

        $conn = General::connectToDatabase();

        $month = date('m');
        $year = date('Y');
        // Prepare the query based on the selection
        if ($selection == 0) {
            //Account Work Order Total
            if ($appsTitle === 'KUDRAT') {
                $query = "SELECT SUM(COALESCE(wop_total, 0) + COALESCE(woo_total, 0)) AS \"WOTotal\" FROM flw_appl_verifies";

            } else {
                $query = "SELECT SUM(COALESCE(wop_total, 0)) AS \"WOTotal\" FROM flw_appl_verifies";
            }

        } else if ($selection == 1) {
            //Arahan Kerja Dikeluarkan
            if ($appsTitle === 'KUDRAT') {
                $query = "SELECT
        SUM(CASE WHEN wop_total IS NOT NULL THEN 1 ELSE 0 END) +
        SUM(CASE WHEN woo_total IS NOT NULL THEN 1 ELSE 0 END) AS \"WONumber\"
    FROM flw_appl_verifies
    WHERE EXTRACT(year FROM wop_submit_date) = :year";

            } else {
                $query = "SELECT COUNT(wop_total) AS \"WONumber\" FROM flw_appl_verifies WHERE EXTRACT(year FROM wop_submit_date) = :year AND wop_total IS NOT NULL";
            }

        } else if ($selection == 2) {
            //Tunggakan Kerja Akaun
            if ($appsTitle === 'KUDRAT') {
                $week_ago = date('Y-m-d', strtotime('-1 week'));
                $query = "SELECT COUNT(wop_total) AS \"NotSubmitWO\" FROM flw_appl_verifies WHERE EXTRACT(year FROM wop_submit_date) = :year AND wop_submit_date >= :week_ago AND wop_total IS NULL";

            } else {
                $week_ago = date('Y-m-d', strtotime('-1 week'));
                $query = "SELECT COUNT(wop_total) AS \"NotSubmitWO\" FROM flw_appl_verifies WHERE EXTRACT(year FROM wop_submit_date) = :year AND wop_submit_date >= :week_ago AND wop_total IS NULL";
            }

        } else if ($selection == 4) {
            $query = "SELECT flw_appl_entries.utility_provider AS uti_pro,
        ls_provider.name AS uti_name,
        SUM(flw_appl_entries.application_length) AS app_leng
        FROM flw_appl_entries
        JOIN ls_provider ON flw_appl_entries.utility_provider = ls_provider.id
        WHERE EXTRACT(year FROM flw_appl_entries.application_date) = :year
        GROUP BY flw_appl_entries.utility_provider, ls_provider.name
        ORDER BY app_leng DESC
        LIMIT 5";
        } else if ($selection == 5) {
            $query = "SELECT flw_appl_entries.utility_provider AS uti_pro,
        ls_provider.name AS uti_name,
        SUM(flw_appl_entries.application_length) AS app_leng
        FROM flw_appl_entries
        JOIN ls_provider ON flw_appl_entries.utility_provider = ls_provider.id
        WHERE EXTRACT(year FROM flw_appl_entries.application_date) = :year
        AND EXTRACT(month FROM flw_appl_entries.application_date) = :month
        GROUP BY flw_appl_entries.utility_provider, ls_provider.name
        ORDER BY app_leng DESC
        LIMIT 5";
        } else if ($selection == 6) {
            $query = "SELECT COUNT(*) AS \"CountWO\" FROM view_operation_tasks WHERE \"status_id\" = 2";
        } else if ($selection == 7) {
            $query = "SELECT reference_no AS \"RefNo\",
                    submission_code AS \"SubCode\",
                    project_title AS \"Title\",
                    system_id AS \"SysID\",
                    payment_method  AS \"PaymentMethod\",
                    \"districts\" AS \"District\",
                    application_length AS \"Length\",
                    \"provider_id\" AS \"ProviderID\",
                    \"provider_name\" AS \"Provider\",
                    \"provider_logo\" AS \"ProviderLogo\",
                    \"status\" AS \"Status\",
                    \"status_color\" AS \"StatusColor\",
                    \"status_icon\" AS \"StatusIcon\",
                    \"submit_date\" AS \"SubmitDate\",
                    id AS \"ID\",
                    \"mapping_id\" AS \"MappingID\",
                    \"status_id\" AS \"StatusID\",
                    report_no AS \"ReportNo\",
                    gis_assignee AS \"GISAssign\",
                    pil_submitted_by AS \"PILBy\",
                    pkd_assignee AS \"PKDAssign\"
                    FROM view_operation_tasks WHERE \"status_id\" = 2 LIMIT 5";
                        } else if ($selection == 8) {
                            $query = "SELECT reference_no AS \"RefNo\",
                    submission_code AS \"SubCode\",
                    project_title AS \"Title\",
                    system_id AS \"SysID\",
                    payment_method  AS \"PaymentMethod\",
                    \"districts\" AS \"District\",
                    application_length AS \"Length\",
                    \"provider_id\" AS \"ProviderID\",
                    \"provider_name\" AS \"Provider\",
                    \"provider_logo\" AS \"ProviderLogo\",
                    \"status\" AS \"Status\",
                    \"status_color\" AS \"StatusColor\",
                    \"status_icon\" AS \"StatusIcon\",
                    \"submit_date\" AS \"SubmitDate\",
                    id AS \"ID\",
                    \"mapping_id\" AS \"MappingID\",
                    \"status_id\" AS \"StatusID\",
                    report_no AS \"ReportNo\",
                    gis_assignee AS \"GISAssign\",
                    pil_submitted_by AS \"PILBy\",
                    pkd_assignee AS \"PKDAssign\" FROM view_operation_tasks WHERE \"status_id\" = 29 LIMIT 5";
                        } else if ($selection == 9) {
                            $query = "SELECT
                    reference_no AS \"RefNo\",
                    submission_code AS \"SubCode\",
                    project_title AS \"Title\",
                    system_id AS \"SysID\",
                    payment_method  AS \"PaymentMethod\",
                    \"districts\" AS \"District\",
                    application_length AS \"Length\",
                    \"provider_id\" AS \"ProviderID\",
                    \"provider_name\" AS \"Provider\",
                    \"provider_logo\" AS \"ProviderLogo\",
                    \"status\" AS \"Status\",
                    \"status_color\" AS \"StatusColor\",
                    \"status_icon\" AS \"StatusIcon\",
                    \"submit_date\" AS \"SubmitDate\",
                    id AS \"ID\",
                    \"mapping_id\" AS \"MappingID\",
                    \"status_id\" AS \"StatusID\",
                    report_no AS \"ReportNo\",
                    gis_assignee AS \"GISAssign\",
                    pil_submitted_by AS \"PILBy\",
                    pkd_assignee AS \"PKDAssign\"
                FROM view_operation_tasks WHERE \"status_id\" = 83 LIMIT 5";
        } else if ($selection == 10) {
            //Account Task Count
            if ($appsTitle === 'KUDRAT') {
                $query = "SELECT COUNT(status_id) AS \"Task\" FROM view_operation_tasks WHERE \"status_id\" = ANY(ARRAY[2, 9, 6, 29, 31, 32, 33, 48, 51, 83, 91])";
            } else {
                $query = "SELECT COUNT(status_id) AS \"Task\" FROM view_operation_tasks WHERE \"status_id\" = ANY(ARRAY[2, 9, 29, 31, 32, 33, 48, 51, 83, 91])";
            }

        }

        // Prepare the query statement
        $stmt = $conn->prepare($query);

        // Bind parameters if necessary
        if ($selection == 1 || $selection == 2 || $selection == 4 || $selection == 5) {
            $stmt->bindParam(':year', $year);
        }
        if ($selection == 2) {
            $stmt->bindParam(':week_ago', $week_ago);
        }
        if ($selection == 5) {
            $stmt->bindParam(':month', $month);
        }

        // Execute the query
        $stmt->execute();

        // Fetch the rows from the result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert the result to an array
        $detail = $data;

        // Close the database connection
        $conn = null;

        return $detail;
    }

    public static function formatCurrency($number)
    {
        if ($number >= 1000000000) {
            return number_format($number / 1000000000, 1) . "B";
        } else if ($number >= 1000000) {
            return number_format($number / 1000000, 1) . "J";
        } else if ($number >= 1000) {
            return number_format($number / 1000, 1) . "K";
        } else {
            return number_format($number);
        }
    }

    public static function taskOverview($role, $mapping)
    {
        // Include the database connection parameters
        global $PDO;

        include "config/tenant.php";
        $app = $appsTitle;

        $conn = General::connectToDatabase();

        // Prepare the query
        if ($role == 61 || $role == 62 || $role == 63 || $role == 64 || $role == 65 || $role == 66 || $role == 67) {
            $query = "SELECT COALESCE(COUNT(id), 0) AS total FROM public.view_survey_plan_tasks WHERE \"sub_mapping_id\" = :mapping AND \"mapping_id\" IN " . SurveyApi::surveyAction($role, $app);
            // $query = "SELECT COALESCE(COUNT(\"ID\"), 0) AS total FROM public.view_survey_plan_tasks";
        }

        // Prepare the query statement
        $stmt = $conn->prepare($query);

        // Bind parameters if necessary
        if ($role == 61 || $role == 62 || $role == 63 || $role == 64 || $role == 65 || $role == 66 || $role == 67) {
            $stmt->bindParam(':mapping', $mapping);
        }

        // Execute the query
        $stmt->execute();

        // Fetch the row from the result as an associative array
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if $row is a valid array
        if (is_array($row)) {
            $list = $row['total'];
        } else {
            $list = 0;
        }

        // Close the database connection
        $conn = null;

    return $list;
    }

    public static function taskOverviewLabel($role, $label)
    {
        $iconClass = '';
        $content = '';

        if ($role == 6 || $role == 7 || $role == 8 || $role == 9 || $role == 10 || $role == 11) {
            if ($label == 1) {
                $iconClass = 'fad fa-map-pin text-primary';
                $content = "Pelan Cdg. Laluan";
            } else if ($label == 2) {
                $iconClass = 'fad fa-globe text-primary';
                $content = "GIS Ready";
            } else if ($label == 3) {
                $iconClass = 'fad fa-map-location-dot text-primary';
                $content = "Pelan Izin Lalu";
            } else if ($label == 4) {
                $iconClass = 'fad fa-file-exclamation text-danger';
                $content = "Tertunggak";
            }
        } else if ($role == 28) {
            if ($label == 1) {
                $iconClass = 'fad fa-person-circle-plus text-primary';
                $content = "Tugasan";
            } else if ($label == 2) {
                $iconClass = 'fad fa-circle-exclamation text-danger';
                $content = "Tertunggak";
            } else if ($label == 3) {
                $iconClass = 'fad fa-check-to-slot text-primary';
                $content = "Serahan Data Lot";
            } else if ($label == 4) {
                $iconClass = 'fad fa-file-circle-check text-primary';
                $content = "Pengesahan Kerja";
            }
        } else if ($role == 24 || $role == 23 || $role == 22) {
            if ($label == 1) {
                $iconClass = 'fad fa-person-circle-plus text-primary';
                $content = "Tugasan";
            } else if ($label == 2) {
                $iconClass = 'fad fa-circle-exclamation text-danger';
                $content = "Tertunggak";
            } else if ($label == 3) {
                $iconClass = 'fad fa-check-to-slot text-primary';
                $content = "Serahan Data Lot";
            } else if ($label == 4) {
                $iconClass = 'fad fa-file-circle-check text-primary';
                $content = "Pengesahan Kerja";
            }
        } else if ($role == 26 || $role == 25 || $role == 21) {
            if ($label == 1) {
                $iconClass = 'fad fa-person-circle-plus text-primary';
                $content = "Tugasan";
            } else if ($label == 2) {
                $iconClass = 'fad fa-circle-exclamation text-danger';
                $content = "Tertunggak";
            } else if ($label == 3) {
                $iconClass = 'fad fa-map-location-dot text-primary';
                $content = "Penyediaan Pelan";
            } else if ($label == 4) {
                $iconClass = 'fad fa-check-to-slot text-primary';
                $content = "Rekod Pelan";
            }
        } else {
            if ($label == 1) {
                $iconClass = 'fad fa-person-circle-plus text-primary';
                $content = "Tiada";
            } else if ($label == 2) {
                $iconClass = 'fad fa-circle-exclamation text-danger';
                $content = "Tiada";
            } else if ($label == 3) {
                $iconClass = 'fad fa-check-to-slot text-primary';
                $content = "Tiada";
            } else if ($label == 4) {
                $iconClass = 'fad fa-file-circle-check text-primary';
                $content = "Tiada";
            }
        }
        return array('iconClass' => $iconClass, 'content' => $content);
    }

    public static function GISWidget($role, $selection)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // $day =  date('d');
        // $week =  date('');
        $month = date('m');
        $year = date('Y');

        if ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
            if ($selection == 1) {
                $query = "SELECT COUNT(status_id) AS \"Task\" FROM view_operation_tasks WHERE status_id = ANY(ARRAY[4])";
            }
        } else if ($role == 8 || $role == 9) {
            if ($selection == 1) {
                $query = "SELECT COUNT(status_id) AS \"Task\" FROM view_operation_tasks WHERE status_id = ANY(ARRAY[4,5])";
            }
        }



        // Prepare the query statement
        $stmt = $conn->prepare($query);

        // Bind parameters if necessary
        // if ($selection == 1) {
        //     $stmt->bindParam(':year', $year);
        //     $stmt->bindParam(':week_ago', $week_ago);
        // }

        // Execute the query
        $stmt->execute();

        // Fetch the rows from the result as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert the result to an array
        $detail = $data;

        // Close the database connection
        $conn = null;

        return $detail;
    }

    public static function countTask($status)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT COUNT(*) AS \"countTask\" FROM view_operation_tasks WHERE status_id IN ($status) ");

        // Execute the query
        $stmt->execute();

        // Fetch the count result
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retrieve the count value
        $count = $row['countTask'];

        // Close the database connection
        $conn = null;

        return $count;
    }

    public static function eventNow()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
            flw_appl_entries.length_code,
            flw_appl_entries.application_length,
            flw_appl_entries.system_id,
            -- flw_appl_entries.districts,
            flw_appl_entries.utility_provider,
            ls_provider.name,
            ( SELECT array_to_string(array_agg(DISTINCT sys_upi.district_name), ','::text) AS array_to_string
                FROM sys_upi
                WHERE sys_upi.state_code::text = flw_appl_entries.state::text AND (sys_upi.district_code::text = ANY (flw_appl_entries.districts::text[]))) AS district
            FROM flw_appl_entries
            LEFT JOIN ls_provider ON flw_appl_entries.utility_provider = ls_provider.id
            WHERE DATE(flw_appl_entries.created_at) = :date_now
            GROUP BY flw_appl_entries.length_code, flw_appl_entries.application_length, flw_appl_entries.system_id, flw_appl_entries.state, flw_appl_entries.districts, flw_appl_entries.utility_provider, ls_provider.name
            LIMIT 5"; // Specify the limit as 10 rows

        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Bind the parameter
        $date_now = date("Y-m-d"); // Assuming $date_now is the current date
        $stmt->bindParam(":date_now", $date_now);

        // Execute the query
        $stmt->execute();

        // Fetch the rows as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $data;
    }
}
?>
