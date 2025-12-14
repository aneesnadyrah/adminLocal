<?php

class Task {

    //FIXME: need to extract the roleAction
    public static function getList(){

        global $role;

        require "api/header.php";
        require "config/system.php";
        include "config/tenant.php";
        include_once "api/functions.php";

        $app = $appsTitle;

        // Connect to the database using PDO
        $conn = Utilities::DBFactory();

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT view_operation_tasks.reference_no AS \"RefNo\",
        view_operation_tasks.submission_code AS \"SubCode\",
        view_operation_tasks.project_title AS \"Title\",
        view_operation_tasks.system_id AS \"SysID\",
        view_operation_tasks.payment_method AS \"PaymentMethod\",
        view_operation_tasks.districts AS \"District\",
        view_operation_tasks.application_length AS \"Length\",
        view_operation_tasks.provider_id AS \"ProviderID\",
        view_operation_tasks.provider_name AS \"Provider\",
        view_operation_tasks.status AS\"Status\",
        view_authorities.status AS \"AuthorityStatus\",
        view_operation_tasks.status_color AS \"StatusColor\",
        view_authorities.status_color AS \"AuthorityStatusColor\",
        view_operation_tasks.status_icon AS \"StatusIcon\",
        view_authorities.status_icon AS \"AuthorityStatusIcon\",
        view_operation_tasks.submit_date AS \"SubmitDate\",
        view_operation_tasks.id AS \"ID\",
        view_operation_tasks.mapping_id AS \"MappingID\",
        view_operation_tasks.status_id AS \"StatusID\",
        view_authorities.authority_status AS \"AuthorityStatusID\",
        view_operation_tasks.report_no AS \"ReportNo\",
        view_operation_tasks.gis_assignee AS \"GISAssign\",
        view_operation_tasks.pil_submitted_by AS \"PILBy\",
        view_operation_tasks.pkd_assignee AS \"PKDAssign\"
        FROM view_operation_tasks LEFT JOIN public.view_authorities ON view_authorities.system_id = view_operation_tasks.system_id WHERE view_operation_tasks.status_id  IN " . roleAction($role, $app)['projectTask'] . " OR view_authorities.authority_status IN " . roleAction($role, $app)['projectTask']);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // To keep track of seen SysIDs
        $seenSysIDs = array();

        // fetch the rows from the query result as an associative array
        // Convert var2 string to an array
        $roleActionArray = explode(',', trim(roleAction($role, $app)['projectTask'], '()'));
        while ($row = $stmt->fetch()) {
            $row['userRole'] = $role;

            // filter any status id that need splitted action by authority
            if (in_array($row['AuthorityStatusID'], roleAction($role, $app)['authorityTask'])) {
                $row['Status'] = $row['AuthorityStatus'];
                $row['StatusID'] = $row['AuthorityStatusID'];
                $row['StatusColor'] = $row['AuthorityStatusColor'];
                $row['StatusIcon'] = $row['AuthorityStatusIcon'];

                // NOTE: this is added to remove duplicated application entry on caused by multiple authorities involved with the application
                // uset row that !in_array($row['AuthorityStatusID'], roleAction($role, $app)['authorityTask']) && with repeated SysID
                // Check if the SysID has been seen before
                if (isset($seenSysIDs[$row['SysID']])) {
                    unset($row); // Remove the duplicate row
                    continue; // Skip to the next row
                } else {
                    $seenSysIDs[$row['SysID']] = true;
                }

            } else {
                // uset row that !in_array($row['AuthorityStatusID'], roleAction($role, $app)['authorityTask']) && with repeated SysID
                // Check if the SysID has been seen before
                if (isset($seenSysIDs[$row['SysID']])) {
                    unset($row); // Remove the duplicate row
                    continue; // Skip to the next row
                } else {
                    $seenSysIDs[$row['SysID']] = true;
                }
            }

            // specific filtering on each needed status (primarily used for specific user assignment)
            if ($row['StatusID'] == 5) {
                if ($row['GISAssign'] != $username) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            }

            // Check if StatusId exists in roleActionList
            if (in_array($row['StatusID'], $roleActionArray)) {
                // role to be check is added here
                if ($role == 52) {
                    // echo 'test'.$row['PKDAssign'];
                    $pkdAssignArray = explode(',', trim($row['PKDAssign'], '{}'));
                    // Check if the username is in the array
                    if (!in_array($username, $pkdAssignArray)) {
                        unset($row);
                        continue;
                        // process the row normally
                    }
                } else if ($role == 33) {
                    $gisAssign = $row['GISAssign'];
                    // Check if the username is same
                    if ($gisAssign != $username || !is_null($row['PILBy'])) {
                        unset($row);
                        continue;
                        // process the row normally
                    }
                }
            }

            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['StatusID'] < 10) {
                $row['StatusID'] = '00' . $row['StatusID'];
            } else if ($row['StatusID'] < 100) {
                $row['StatusID'] = '0' . $row['StatusID'];
            }

            if ($row['MappingID'] < 10) {
                $row['MappingID'] = '00' . $row['MappingID'];
            } else if ($row['MappingID'] < 100) {
                $row['MappingID'] = '0' . $row['MappingID'];
            }
            $data[] = $row;
        }

        // convert the result to a JSON string
        $json = '{"data":' . json_encode($data) . '}';

        // Close the database connection
        $conn = null;

        return $json;
    }

    public static function SurveyTable(){
        global $role;
        // Include the database connection parameters
        global $conn;

        require "api/header.php";
        include "config/tenant.php";
        $app = $appsTitle;

        // Prepare query on the database
        $stmt = $conn->prepare("SELECT * FROM public.view_survey_plan_tasks WHERE \"MappingID\" IN " . SurveyApi::surveyAction($role, $app) . " AND \"subMappingID\" IN " . Survey::subSurveyAction($role, $app));

        // $stmt = $conn->prepare("SELECT * FROM public.view_operation_task WHERE \"StatusID\" IN " . roleAction($role) . " AND (\"GISAssign\" = :userName OR \"StatusID\" <> 5) ORDER BY \"SubmitDate\" DESC ");
        $stmt = $conn->prepare("SELECT
                                reference_no AS \"RefNo\",
                                system_id,
                                \"district\" AS \"District\",
                                \"status_id\" AS \"StatusID\",
                                \"mapping_id\" AS \"MappingID\",
                                \"sub_mapping_id\" AS \"subMappingID\",
                                application_length AS \"Length\",
                                \"provider_id\" AS \"ProviderID\",
                                \"provider_name\" AS \"Provider\",
                                \"project_status\" AS \"ProjectStatus\",
                                \"mapping_status\" AS \"MappingStatus\",
                                \"status_color\" AS \"StatusColor\",
                                \"status_icon\" AS \"StatusIcon\",
                                \"submit_date\" AS \"SubmitDate\",
                                id AS \"ID\",
                                report_no AS \"ReportNo\",
                                \"survey_assign\" AS \"SurveyAssign\",
                                trigger_spku AS \"triggerSPKU\",
                                \"udm_assign\" AS \"UDMAssign\",
                                \"tmp_assign\" AS \"TMPAssign\" 
                                FROM public.view_survey_plan_tasks WHERE \"mapping_id\" IN " . SurveyApi::surveyAction($role, $app) . " AND \"sub_mapping_id\" IN " . Survey::subSurveyAction($role, $app));

        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':userName', $username);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $row['userRole'] = $role;

            if ($row['MappingID'] < 10) {
                $row['MappingID'] = '00' . $row['MappingID'];
            } else if ($row['MappingID'] < 100) {
                $row['MappingID'] = '0' . $row['MappingID'];
            }

            if ($row['subMappingID'] < 10) {
                $row['subMappingID'] = '00' . $row['subMappingID'];
            } else if ($row['subMappingID'] < 100) {
                $row['subMappingID'] = '0' . $row['subMappingID'];
            }

            $data[] = $row;
        }

        // convert the result to a JSON string
        $json = '{"data":' . json_encode($data) . '}';

        // Close the database connection
        $conn = null;

        return $json;
    }


}

