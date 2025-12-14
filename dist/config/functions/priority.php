<?php
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));
require_once 'config/DBFactory.php';
require_once 'roles.php';
require_once 'config/components.php';

class Priority
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

    private function queryTasks()
    {
        $db = $this->dbFactory->createConnection();
        $department = $this->Role->{$this->User}->department;
        $statusId = $this->Role->getAssignment($this->User);

        $query = $db->prepare("
            SELECT 
                authority,
                public.view_survey_priority.system_id,
                quote_approve,
                \"wy_approval_date\",
                \"wy_fb_date\",
                \"payment\",
                public.view_survey_priority.id AS \"ID\",
                status_id,
                \"flow_name\",
                \"project_status\",
                \"status_color\",
                \"status_icon\",
                reference_no,
                application_length,
                \"sv_completed\",
                \"submit_date\",
                \"provider_name\",
                \"provider_id\",
                \"district\"
            FROM public.view_survey_priority
            LEFT JOIN flw_survey_udm AS fas ON public.view_survey_priority.system_id = fas.system_id
            WHERE fas.trigger_priority IS NULL OR fas.trigger_priority = false");


        // Execute the query
        $query->execute();

        // Create an array to hold the query result
        $data = array();
        $uniqueSystemIds = array(); // To track unique system_ids

        // Fetch the rows from the query result as an associative array
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $systemId = $row['system_id'];

            // Check if system_id is already processed
            if (isset($uniqueSystemIds[$systemId])) {
                // Check if the current row has a later dt_appv_ltr/wy_approval_date
                if ($row['wy_approval_date'] > $uniqueSystemIds[$systemId]['wy_approval_date']) {
                    // Update the row with the later dt_appv_ltr/wy_approval_date
                    $uniqueSystemIds[$systemId] = $row;
                }
            } else {
                // Add the row for a new system_id
                $uniqueSystemIds[$systemId] = $row;
            }
        }

        // Determine the priority for each unique row
        foreach ($uniqueSystemIds as $row) {
            //site visit completed
            $svComleted = $row['sv_completed'];
            //sebut harga
            $quoteApprove = $row['quote_approve'];
            //kelulusan izin lalu
            $wyApproval = $row['wy_approval_date'];
            //mbkil
            $wyFeedbackDate = $row['wy_fb_date'];
            //bayaran (invois)
            $payment = $row['payment'];

            // Determine the priority based on the conditions
            $priority = 0;
            if ($svComleted === true) {
                $priority = 5;
                if ($quoteApprove === true) {
                    $priority = 4;
                    if ($wyApproval !== null) {
                        $priority = 3;
                        if ($wyFeedbackDate !== null) {
                            $priority = 2;
                        }
                        if ($payment !== null) {
                            $priority = 1;
                        }
                    }
                }
            }

            // Add the priority to the row
            $row['priority'] = $priority;

            // Add the row to the data array
            $data[] = (object) $row;
        }

        $response = $data;

        return $response;
    }

    private function getTableData()
    {
        $data = $this->queryTasks();


        foreach ($data as $item) {
            switch ($item->status_id) {
                default:
                    $item->route = $item->system_id . '-' . $item->status_id;
                    break;
            }
        }
        return $data;
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
    }
}