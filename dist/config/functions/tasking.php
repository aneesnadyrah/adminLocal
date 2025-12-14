<?php
// STUB: Assign to fakhri
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

class Tasking
{
    // Function for Tasking
    public static function taskModal()
    {
        require "config/system.php";
        include "config/tenant.php";
        $role = $_SESSION['roleId'];
        $app = $appsTitle;
    
        // Connect to the database using PDO
        $conn = General::connectToDatabase();
    
    
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
        FROM view_operation_tasks LEFT JOIN public.view_authorities ON view_authorities.system_id = view_operation_tasks.system_id WHERE view_operation_tasks.status_id  IN " . Tasking::roleAction($role, $app)['projectTask'] . " OR view_authorities.authority_status IN " . Tasking::roleAction($role, $app)['projectTask']);
    
        // fetch current username from session
        $username = $_SESSION['username'];
    
        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':userName', $username);
    
        // Execute the query
        $stmt->execute();
    
        // create an array to hold the query result
        $data = array();
    
        // To keep track of seen SysIDs
        $seenSysIDs = array();
    
        // fetch the rows from the query result as an associative array
        // Convert var2 string to an array
        $roleActionArray = explode(',', trim(Tasking::roleAction($role, $app)['projectTask'], '()'));
        while ($row = $stmt->fetch()) {
            $row['userRole'] = $role;
    
            // filter any status id that need splitted action by authority
            if (in_array($row['AuthorityStatusID'], Tasking::roleAction($role, $app)['authorityTask'])) {
                $row['Status'] = $row['AuthorityStatus'];
                $row['StatusID'] = $row['AuthorityStatusID'];
                $row['StatusColor'] = $row['AuthorityStatusColor'];
                $row['StatusIcon'] = $row['AuthorityStatusIcon'];
    
                // TODO: this is added to remove duplicated application entry on caused by multiple authorities involved with the application
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
    
        // convert the result to a Array
        $modalID = $data;
    
        // Close the database connection
        $conn = null;
    
        return $modalID;
    }
    
    //Function for assign selection
    public static function assignSelect($type)
    {
        require_once "config/system.php";
    
        $role = $_SESSION['roleId'];
    
        if ($role == 31 || $role == 32 || $role == 33 || $role == 34 || $role == 35) {
            $filter = '(33)';
        } elseif ($role == 41 || $role == 42 || $role == 43 || $role == 44 || $role == 73) {
            $filter = '(41,42,43,44)';
        } elseif ($role == 61) {
            $filter = '(64,65,63,66)';
        } elseif ($role == 63 || $role == 66) {
            $filter = '(63,66)';
        } elseif ($role == 62 || $role == 64 || $role == 65) {
            $filter = '(64,65)';
        }
    
        // Connect to the database
        $conn = General::connectToDatabase();
    
        if ($type == '1') {
            // Execute a SELECT query on the database
            $queryAction = "SELECT
                sys_users.username AS \"username\",
                sys_hr_employee.first_name AS \"FirstName\",
                sys_users.profile_pic AS \"ProfilePic\",
                sys_hr_employee.position AS \"Position\",
                sys_hr_employee.phone_no AS \"StaffPhoneNo\"
                FROM sys_hr_employee
                LEFT JOIN sys_users ON sys_users.employee_id = sys_hr_employee.id
                WHERE sys_users.role_id IN $filter
                AND sys_users.activation = 'true'
                ORDER BY sys_hr_employee.first_name ASC";
        } else {
            // Execute a SELECT query on the database
            $queryAction = "SELECT
                sys_users.username AS \"username\",
                INITCAP(sys_hr_employee.first_name) AS \"FirstName\",
                sys_users.profile_pic AS \"ProfilePic\",
                sys_hr_employee.position AS \"Position\",
                sys_hr_employee.phone_no AS \"StaffPhoneNo\"
                FROM sys_hr_employee
                LEFT JOIN sys_users ON sys_users.employee_id = sys_hr_employee.id
                WHERE sys_users.activation = 'true'
                ORDER BY sys_hr_employee.first_name ASC";
        }
    
    
        $stmt = $conn->prepare($queryAction);
    
        $stmt->execute();
    
        // create an array to hold the query result
        $data = array();
    
        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }
    
        // convert the result to a Array
        $list = $data;
    
        // Close the database connection
        $conn = null;
        return $list;
    }
    
    public static function roleAction($role, $app = null)
    {
        if ($app == null) {
            // default role action
            if ($role == 1 || $role == 2) {
                $filter = '(1,3,21,86,88,81,101,103,121,151,85,131)';
            } elseif ($role == 3 || $role == 4 || $role == 5) {
                $filter = '(2,8,9,6,291,32,33,48,51,91,42)';
            } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
                $filter = '(4,5,22,29,31,32,33,34,40,41)';
            } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
                $filter = '(73,116)';
            } elseif ($role == 12 || $role == 13 || $role == 14) {
                $filter = '(41,43,44,46,21,51,82,84,822,93,112,124,129,122,152,154,87,89,891,132,141,144,145)';
            } elseif ($role == 17 || $role == 16) {
                $filter = '(6,10,11,12,13,14,17,24,25,26,27,40,49,45,42,,821,83,871,128,127,123,153,872,88,134,133,142,143)';
            } elseif ($role == 18) {
                $filter = '(15,28,106)';
            } elseif ($role == 15) {
                $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
            } elseif ($role == 28) {
                $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
            } elseif ($role == 24 || $role == 23) {
                $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
            } elseif ($role == 22) {
                $filter = '(42,51,52,53,43,46,47,48,62,63)';
            } elseif ($role == 26 || $role == 25) {
                $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
            } elseif ($role == 21) {
                $filter = '(74,75,76,77,78)';
            } elseif ($role == 19) {
                $filter = '(43)';
            } elseif ($role == 32 || $role == 31) {
                $filter = '(2,28,29,31,43,44,51,52,84)';
            }
            // splitted task by authority
            $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
        } else if ($app == 'UCIDOS') {
            // role action for UCIDOS
            if ($role == 1 || $role == 2) {
                $filter = '(1,3,21,81,86,101,103,121,151,85,131)';
            } elseif ($role == 3 || $role == 4 || $role == 5) {
                $filter = '(2,8,9,29,32,33,48,91,51)';
            } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
                $filter = '(4,5,22,29,31,32,33,34,40)';
            } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
                $filter = '(73,116)';
            } elseif ($role == 12 || $role == 13 || $role == 14) {
                $filter = '(41,44,46,47,21,51,82,42,84,822,93,112,124,129,122,152,154,87,89,891,132,141,144,145)';
            } elseif ($role == 17 || $role == 16) {
                $filter = '(6,11,12,13,14,17,24,25,26,27,45,40,49,104,42,821,83,871,128,127,123,153,872,88,134,133,142,143)';
            } elseif ($role == 18) {
                $filter = '(15,28,106)';
            } elseif ($role == 15) {
                $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
            } elseif ($role == 28) {
                $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
            } elseif ($role == 24 || $role == 23) {
                $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
            } elseif ($role == 22) {
                $filter = '(42,51,52,53,43,46,47,48,62,63)';
            } elseif ($role == 26 || $role == 25) {
                $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
            } elseif ($role == 21) {
                $filter = '(74,75,76,77,78)';
            } elseif ($role == 19) {
                $filter = '(43,40,41,49)';
            } elseif ($role == 32 || $role == 31) {
                $filter = '(2,28,29,31,43,44,51,52,84)';
            }
            // splitted task by authority
            $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
        } else if ($app == 'KITER') {
            // role action for KITER
            if ($role == 1 || $role == 2) {
                $filter = '(1,3,21,81,86,85,121,131,101,103,151)';
            } elseif ($role == 3 || $role == 4 || $role == 5) {
                $filter = '(2,8,9,29,32,33,48,91,42,996,51)';
            } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
                $filter = '(4,5,22,29,31,32,33,34,40)';
            } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
                $filter = '(73,116)';
            } elseif ($role == 12 || $role == 13 || $role == 14) {
                $filter = '(41,44,46,47,21,51,82,84,822,93,112,87,122,124,132,141,144,152,129,154,89,891,132,141,144,145)';
            } elseif ($role == 17 || $role == 16) {
                $filter = '(6,11,12,13,14,17,24,25,26,27,45,40,49,104,42,821,83,871,128,872,88,127,123,134,133,142,143,153)';
            } elseif ($role == 18) {
                $filter = '(15,28,106)';
            } elseif ($role == 15) {
                $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
            } elseif ($role == 28) {
                $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
            } elseif ($role == 24 || $role == 23) {
                $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
            } elseif ($role == 22) {
                $filter = '(42,51,52,53,43,46,47,48,62,63)';
            } elseif ($role == 26 || $role == 25) {
                $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
            } elseif ($role == 21) {
                $filter = '(74,75,76,77,78)';
            } elseif ($role == 19) {
                $filter = '(43,40,41,49)';
            } elseif ($role == 32 || $role == 31) {
                $filter = '(2,28,29,31,43,44,51,52,84)';
            }
            // splitted task by authority
            $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
        } else if ($app == 'KUDRAT') {
            // role action for KUDR
            if ($role == 1 || $role == 2) {
                $filter = '(1,3,21,81,86,88,101,,103,121,151,85,131)';
            } elseif ($role == 3 || $role == 4 || $role == 5) {
                $filter = '(2,8,9,6,29,32,33,48,91,996,51)';
            } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
                $filter = '(4,5,22,29,31,32,33,34,40)';
            } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
                $filter = '(73,116)';
            } elseif ($role == 12 || $role == 13 || $role == 14) {
                $filter = '(41,44,46,47,21,51,82,84,822,93,112,124,122,129,152,154,87,89,891,132,141,144,145)';
            } elseif ($role == 17 || $role == 16) {
                $filter = '(10,11,12,13,14,17,24,25,26,27,45,40,49,104,42,821,83,871,128,127,123,153,872,88,134,133,142,143)';
            } elseif ($role == 18) {
                $filter = '(15,28,106)';
            } elseif ($role == 15) {
                $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
            } elseif ($role == 28) {
                $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
            } elseif ($role == 24 || $role == 23) {
                $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
            } elseif ($role == 22) {
                $filter = '(42,51,52,53,43,46,47,48,62,63)';
            } elseif ($role == 26 || $role == 25) {
                $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
            } elseif ($role == 21) {
                $filter = '(74,75,76,77,78)';
            } elseif ($role == 19) {
                $filter = '(43,40,41,49)';
            } elseif ($role == 32 || $role == 31) {
                $filter = '(2,28,29,31,43,44,51,52,84)';
            }
            // splitted task by authority
            $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
        } else {
            // error control
            echo json_encode(
                array(
                    'message' => 'error',
                    'reason' => 'apps contains wrong value, do check your tenant setup!'
                )
            );
        }
    
        return ["projectTask" => $filter, "authorityTask" => $splitTask];
    }
}

class ModalOld
{

    private $RoleID;

    //constructor
    function __construct($role)
    {
        $this->RoleID = $role;
    }

    // public function get()
    // {
    //     return $this->RoleID;
    // }

    private function listOperation()
    {
        require "config/system.php";
        include "config/tenant.php";
        $role = $this->RoleID;
        $app = $appsTitle;

        $conn = General::connectToDatabase();

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
    FROM view_operation_tasks LEFT JOIN public.view_authorities ON view_authorities.system_id = view_operation_tasks.system_id WHERE view_operation_tasks.status_id  IN " . Tasking::roleAction($role, $app)['projectTask'] . " OR view_authorities.authority_status IN " . Tasking::roleAction($role, $app)['projectTask']);

        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':userName', $username);

        // Execute the query
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // To keep track of seen SysIDs
        $seenSysIDs = array();

        // fetch the rows from the query result as an associative array
        // Convert var2 string to an array
        $roleActionArray = explode(',', trim(Tasking::roleAction($role, $app)['projectTask'], '()'));

        while ($row = $stmt->fetch()) {
            $row['userRole'] = $role;

            // filter any status id that need splitted action by authority
            if (in_array($row['AuthorityStatusID'], Tasking::roleAction($role, $app)['authorityTask'])) {
                $row['Status'] = $row['AuthorityStatus'];
                $row['StatusID'] = $row['AuthorityStatusID'];
                $row['StatusColor'] = $row['AuthorityStatusColor'];
                $row['StatusIcon'] = $row['AuthorityStatusIcon'];

                // TODO: this is added to remove duplicated application entry on caused by multiple authorities involved with the application
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

        // Close the database connection
        $conn = null;

        return $data; // Return the modified data array after the loop has finished
    }

    public function getGeneralModal()
    {
        $rows = $this->listOperation();

        $generalStatusMapping = [
            //1 dropzone 1 date 1 note
            //1 dropzone 2 date 1 note
            '085' => [
                'title' => 'Permohonan Lanjutan Permit Kerja',
                'fileId' => 'SRPLPK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],
            '121' => [
                'title' => 'Permohonan Sijil Siap Kerja',
                'fileId' => 'SRPSSK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],
            '151' => [
                'title' => 'Permohonan Pemulangan Wang Cagaran',
                'fileId' => 'SRPWC',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],
            '131' => [
                'title' => 'Permohonan Sijil Siap Memperbaiki Kecacatan',
                'fileId' => 'SRPSSMK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Permohonan',
                    'dateTitle2' => 'Tarikh Laporan',
                    'dt1' => 'dt-appl',
                    'dt2' => 'dt-report',
                ],
            ],
            //1 dropzone 3 date 1 note
            '101' => [
                'title' => 'Notis Mula Kerja',
                'fileId' => 'NMK',
                'modalType' => 'triple-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dateTitle3' => 'Tarikh Mula Kerja',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                    'dt3' => 'dt-start',
                ],
            ],
            '103' => [
                'title' => 'Notis Siap Kerja',
                'fileId' => 'NSK',
                'modalType' => 'triple-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dateTitle3' => 'Tarikh Akhir Kerja',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                    'dt3' => 'dt-finish',
                ],
            ],
        ];

        foreach ($rows as $row) {
            $customModal = false;
            $generalStatus = $row['StatusID'];

            if (isset($generalStatusMapping[$generalStatus])) {
                $data = $generalStatusMapping[$generalStatus];
                $title = $data['title'];
                $fileId = $data['fileId'];
                $label = $data['label'];
                $modalType = $data['modalType'];
            } else {
                $customModal = true;
            }

            // Add additional data for each element in the result
            if ($customModal) {

                if ($generalStatus == '009') {
                    include "components/partials/modals/upload-workOrder.php";
                }
                //sebut harga
                else if ($generalStatus == '029') {
                    include "components/partials/modals/upload-sebutHarga.php";

                    //icpp
                } else if ($generalStatus == '048') {
                    include "components/partials/modals/upload-icpp.php";

                    //rcpp
                } else if ($generalStatus == '051') {
                    include "components/partials/modals/upload-rcpp.php";
                    //phdd
                } else if ($generalStatus == '128') {
                    include "components/partials/modals/upload-phdd.php";
                }

            } else {
                // Include standard modals
                if ($modalType == 'triple-date') {
                    include "components/partials/modals/upload-task-triple-date.php";
                } elseif ($modalType == 'double-date') {
                    include "components/partials/modals/upload-task-double-date-general.php";
                } elseif ($modalType == 'single-date') {
                    include "components/partials/modals/upload-task-single-date-general.php";
                }

            }
        }
    }
    private function TaskSurvey()
    {
        require "config/system.php";
        include "config/tenant.php";
        $role = $_SESSION['roleId'];
        $app = $appsTitle;

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute query on the database
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

        $firstName = SurveyApi::getSurveyFirstname($username);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            // specific filtering on each needed status (primarily used for specific user assignment)
            if ($row['MappingID'] == 62 || $row['MappingID'] == 63) {
                if ($row['SurveyAssign'] != $firstName) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            } else if ($row['subMappingID'] == 11) {
                if ($row['UDMAssign'] != $username) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            } else if ($row['MappingID'] == 73) {
                if ($row['TMPAssign'] != $username) {
                    unset($row);
                    continue;
                    // process the row normally
                }
            }

            // Format the StatusID field to have leading zeroes if it's less than 100
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

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public function getGeneralSurveyModal()
    {
        $rows = $this->TaskSurvey();

        $surveyStatusMapping = [
            //surat survey
            '061' => [
                'title' => 'Surat Pengesahan Kedudukan Utiliti',
                'fileId' => 'SPKU',
                'modalType' => 'surat-survey',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-ltr',
                ],
            ],
            '071' => [
                'title' => 'Surat Lawatan Tapak Koordinasi',
                'fileId' => 'SLTK',
                'modalType' => 'surat-survey',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-ltr',
                ],
            ],
        ];

        foreach ($rows as $row) {

            $customModal = false;
            $surveyStatus = $row['MappingID'];
            $subSurveyStatus = $row['subMappingID'];

            if (isset($surveyStatusMapping[$surveyStatus])) {
                $data = $surveyStatusMapping[$surveyStatus];
                $title = $data['title'];
                $fileId = $data['fileId'];
                $label = $data['label'];
                $modalType = $data['modalType'];
            } else {
                $customModal = true;
            }

            if ($customModal) {
                // nothing to return

                //psb
                if ($surveyStatus == '114' && $subSurveyStatus == '018') {
                    include "components/partials/modals/upload-psb.php";
                }

            } else {

                if ($modalType == 'surat-survey') {
                    // include "components/partials/modals/upload-surat-ukur-pelan.php";
                } else {

                }

            }
        }
    }

    private function listAuthority()
    {
        require "config/system.php";
        include "config/tenant.php";
        $role = $this->RoleID;
        $app = $appsTitle;

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        $systemId = isset($_GET['sid']) ? $_GET['sid'] : null;

        // var_dump($systemId);

        if ($systemId !== null) {
            // Prepare query on the database
            $stmt = $conn->prepare("SELECT * FROM public.view_authorities WHERE system_id = :systemId AND view_authorities.authority_status IN " . Tasking::roleAction($role, $app)['projectTask'] . " ORDER BY id ASC");
            $stmt->bindParam(':systemId', $systemId);
        } else {
            $stmt = $conn->prepare("SELECT * FROM public.view_authorities ORDER BY id ASC");
        }
        // Execute the query
        $stmt->execute();

        // Fetch all rows from the result set as an associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as &$row) {
            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['status'] < 10) {
                $row['status'] = '00' . $row['status'];
            } else if ($row['status'] < 100) {
                $row['status'] = '0' . $row['status'];
            }

            if ($row['authority_status'] < 10) {
                $row['authority_status'] = '00' . $row['authority_status'];
            } else if ($row['authority_status'] < 100) {
                $row['authority_status'] = '0' . $row['authority_status'];
            }

        }

        $row[] = $data;

        // Close the database connection
        $conn = null;

        return $data; // Return the modified data array after the loop has finished
    }

    //Modal that have devided within authority
    public function getAuthorityModal()
    {

        $rows = $this->listAuthority();

        $authorityStatusMapping = [
            //1 dropzone 1 date 1 note
            '041' => [
                'title' => 'Surat Permohonan Kelulusan Izin Lalu',
                'fileId' => 'SPKIL',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-ltr-created',
                ],
            ],
            '045' => [
                'title' => 'Akuan Serahan Permohonan Kelulusan Izin Lalu',
                'fileId' => 'ASPKIL',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat Dihantar',
                    'dt' => 'dt-auth-send-ltr',
                ],
            ],
            '093' => [
                'title' => 'Surat Makluman Mula Kerja',
                'fileId' => 'SMMK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-ltr-created',
                ],
            ],
            '127' => [
                'title' => 'Akuan Serahan Permohonan Sijil Siap Kerja',
                'fileId' => 'ASPSSK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Hantar',
                    'dt' => 'dt-auth-send',
                ],
            ],
            '132' => [
                'title' => 'Surat Permohonan Sijil Siap Memperbaiki Kecacatan',
                'fileId' => 'SPSSMK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-auth-ltr',
                ],
            ],
            '134' => [
                'title' => 'Akuan Serahan Permohonan Sijil Siap Memperbaiki Kecacatan',
                'fileId' => 'ASPSSMK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Hantar',
                    'dt' => 'dt-auth-send',
                ],
            ],
            '141' => [
                'title' => 'Surat Permohonan Sijil Sempurna Kerja',
                'fileId' => 'SPCCC',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-auth-ltr',
                ],
            ],
            '142' => [
                'title' => 'Akuan Serahan Permohonan Sijil Sempurna Kerja',
                'fileId' => 'ASPCCC',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Hantar',
                    'dt' => 'dt-auth-send',
                ],
            ],
            '152' => [
                'title' => 'Surat Permohonan Pemulangan Wang Cagaran',
                'fileId' => 'SPPWC',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-wc-ltr',
                ],
            ],
            '153' => [
                'title' => 'Akuan Serahan Permohonan Pemulangan Wang Cagaran',
                'fileId' => 'ASPPWC',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Hantar',
                    'dt' => 'dt-wc-send',
                ],
            ],
            '821' => [
                'title' => 'Akuan Serahan Permohonan Kelulusan Permit Kerja',
                'fileId' => 'ASPKPK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat Keluar',
                    'dt' => 'dt-auth-send',
                ],

            ],
            '871' => [
                'title' => 'Akuan Serahan Makluman Mula Kerja',
                'fileId' => 'ASMMK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Hantar',
                    'dt' => 'dt-auth-ltr-send',
                ],

            ],
            '872' => [
                'title' => 'Akuan Serahan Permohonan Lanjutan Permit Kerja',
                'fileId' => 'ASPLPK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat Keluar',
                    'dt' => 'dt-auth-send',
                ],

            ],
            //1 dropzone 2 date 1 note
            '046' => [
                'title' => 'Maklumbalas Kelulusan Izin Lalu',
                'fileId' => 'MKIL',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Hantar',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-send',
                ],
            ],
            '082' => [
                'title' => 'Surat Permohonan Kelulusan Permit Kerja',
                'fileId' => 'SPKPK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-ltr',
                ],
            ],
            '083' => [
                'title' => 'Surat Kelulusan Permit Kerja',
                'fileId' => 'SKPK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],
            '087' => [
                'title' => 'Surat Permohonan Lanjutan Permit Kerja',
                'fileId' => 'SPLPK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Pemohonan',
                    'dateTitle2' => 'Tarikh Surat Keluar',
                    'dt1' => 'dt-appl',
                    'dt2' => 'dt-auth-ltr',
                ],
            ],
            '088' => [
                'title' => 'Surat Kelulusan Lanjutan Permit Kerja',
                'fileId' => 'SKLPK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],

            '122' => [
                'title' => 'Surat Permohonan Sijil Siap Kerja',
                'fileId' => 'SPSSK',
                'modalType' => 'single-date',
                'label' => [
                    'dateTitle' => 'Tarikh Surat',
                    'dt' => 'dt-ltr',
                ],
            ],

            '133' => [
                'title' => 'Ulasan Permohonan Sijil Siap Memperbaiki Kecacatan',
                'fileId' => 'UPSSMK',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Semakan Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-review-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],
            '143' => [
                'title' => 'Surat Kelulusan Permohonan Sijil Sempurna Kerja',
                'fileId' => 'SKPCCC',
                'modalType' => 'double-date',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt1' => 'dt-ltr',
                    'dt2' => 'dt-recv',
                ],
            ],

            // 1 dropzone 1 date 1 input 1 note
            '154' => [
                'title' => 'Baucer Pemulangan Wang Cagaran',
                'fileId' => 'BPWC',
                'modalType' => 'double-date-single-input',
                'label' => [
                    'dateTitle1' => 'Tarikh Surat',
                    'dt1' => 'dt-ltr',
                    'dateTitle2' => 'Tarikh Terima',
                    'dt2' => 'dt-recv',
                    'inputTitle' => 'No Baucer',
                    'typeId' => 'no-wc-voucher',
                    'type' => 'text',
                ],
            ],
            '822' => [
                'title' => 'Akuan Serahan Perakuan Permit Kerja',
                'fileId' => 'ASPPK',
                'modalType' => 'single-input',
                'label' => [
                    'dateTitle' => 'Tarikh Serahan',
                    'dt' => 'dt-send',
                    'inputTitle' => 'Nama Penerima',
                    'typeId' => 'name-recv',
                    'type' => 'text',
                ],
            ],
            '891' => [
                'title' => 'Akuan Serahan Lanjutan Perakuan  Permit Kerja',
                'fileId' => 'ASLPPK',
                'modalType' => 'single-input',
                'label' => [
                    'dateTitle' => 'Tarikh Serahan',
                    'dt' => 'dt-send',
                    'inputTitle' => 'Nama Penerima',
                    'typeId' => 'name-recv',
                    'type' => 'text',
                ],
            ],
            '129' => [
                'title' => 'Akuan Serahan Perakuan Kelulusan Sijil Siap Kerja',
                'fileId' => 'ASPKSSK',
                'modalType' => 'single-input',
                'label' => [
                    'dateTitle' => 'Tarikh Serahan',
                    'dt' => 'dt-send',
                    'inputTitle' => 'Nama Penerima',
                    'typeId' => 'name-recv',
                    'type' => 'text',
                ],
            ],
            '145' => [
                'title' => 'Akuan Serahan Perakuan Kelulusan Sijil Sempurna Kerja',
                'fileId' => 'ASPKCCC',
                'modalType' => 'single-input',
                'label' => [
                    'dateTitle' => 'Tarikh Serahan',
                    'dt' => 'dt-send',
                    'inputTitle' => 'Nama Penerima',
                    'typeId' => 'name-recv',
                    'type' => 'text',
                ],
            ],
            //1 dropzone 2 date 1 inout 1 note
            '089' => [
                'title' => 'Perakuan Lanjutan Permit Kerja',
                'fileId' => 'PLPK',
                'modalType' => 'double-date-single-input',
                'label' => [
                    'dateTitle1' => 'Tarikh Perakuan',
                    'dt1' => 'dt-perakuan',
                    'dateTitle2' => 'Tarikh Makluman',
                    'dt2' => 'dt-makluman',
                    'inputTitle' => 'No Perakuan',
                    'typeId' => 'no-perakuan',
                    'type' => 'text',
                ],
            ],
            '124' => [
                'title' => 'Perakuan Sijil Siap Kerja',
                'fileId' => 'PSSK',
                'modalType' => 'double-date-single-input',
                'label' => [
                    'dateTitle1' => 'Tarikh Perakuan',
                    'dt1' => 'dt-cpc-perakuan',
                    'dateTitle2' => 'Tarikh Makluman',
                    'dt2' => 'dt-cpc-makluman',
                    'inputTitle' => 'No Perakuan',
                    'typeId' => 'no-cpc-perakuan',
                    'type' => 'text',
                ],
            ],
            '144' => [
                'title' => 'Perakuan Sijil Sempurna Kerja',
                'fileId' => 'PCCC',
                'modalType' => 'double-date-single-input',
                'label' => [
                    'dateTitle1' => 'Tarikh Perakuan',
                    'dt1' => 'dt-ccc-perakuan',
                    'dateTitle2' => 'Tarikh Makluman',
                    'dt2' => 'dt-ccc-makluman',
                    'inputTitle' => 'No Perakuan',
                    'typeId' => 'no-ccc-perakuan',
                    'type' => 'text',
                ],
            ],
        ];

        foreach ($rows as $row) {
            $customModal = false;
            $authorityStatus = $row['authority_status'];

            if (isset($authorityStatusMapping[$authorityStatus])) {
                $data = $authorityStatusMapping[$authorityStatus];
                $title = $data['title'];
                $fileId = $data['fileId'];
                $label = $data['label'];
                $modalType = $data['modalType'];
            } else {
                $customModal = true;
            }

            if ($customModal) {
                // Include custom modals
                if ($authorityStatus == '084') {
                    include "components/partials/modals/upload-ppk.php";
                } elseif ($authorityStatus == '123') {
                    include "components/partials/modals/upload-skpssk.php";
                } elseif ($authorityStatus == '040') {
                    include "components/partials/modals/upload-rpkwc.php";
                } else if ($authorityStatus == '042') {
                    include "components/partials/modals/upload-surat-kil.php";
                }
            } else {
                // Include standard modals
                if ($modalType == 'triple-date') {
                    include "components/partials/modals/upload-task-triple-date.php";
                } elseif ($modalType == 'double-date') {
                    include "components/partials/modals/upload-task-double-date-authority.php";
                } elseif ($modalType == 'single-date') {
                    include "components/partials/modals/upload-task-single-date-authority.php";
                } elseif ($modalType == 'single-input') {
                    include "components/partials/modals/upload-task-single-input.php";
                } elseif ($modalType == 'double-date-single-input') {
                    include "components/partials/modals/upload-task-double-date-single-input.php";
                }
            }
        }

    }
}

// * api function
function roleAction($role, $app = null)
{
    if ($app == null) {
        // default role action
        if ($role == 1 || $role == 2) {
            $filter = '(1,3,21,86,88,81,101,103,121,151,85,131)';
        } elseif ($role == 3 || $role == 4 || $role == 5) {
            $filter = '(2,8,9,6,291,32,33,48,51,91,42)';
        } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
            $filter = '(4,5,22,29,31,32,33,34,40,41)';
        } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
            $filter = '(73,116)';
        } elseif ($role == 12 || $role == 13 || $role == 14) {
            $filter = '(41,43,44,46,21,51,82,84,822,93,112,124,129,122,152,154,87,89,891,132,141,144,145)';
        } elseif ($role == 17 || $role == 16) {
            $filter = '(6,10,11,12,13,14,17,24,25,26,27,40,49,45,42,,821,83,871,128,127,123,153,872,88,134,133,142,143)';
        } elseif ($role == 18) {
            $filter = '(15,28,106)';
        } elseif ($role == 15) {
            $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
        } elseif ($role == 28) {
            $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
        } elseif ($role == 24 || $role == 23) {
            $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
        } elseif ($role == 22) {
            $filter = '(42,51,52,53,43,46,47,48,62,63)';
        } elseif ($role == 26 || $role == 25) {
            $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
        } elseif ($role == 21) {
            $filter = '(74,75,76,77,78)';
        } elseif ($role == 19) {
            $filter = '(43)';
        } elseif ($role == 32 || $role == 31) {
            $filter = '(2,28,29,31,43,44,51,52,84)';
        }
        // splitted task by authority
        $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
    } else if ($app == 'UCIDOS') {
        // role action for UCIDOS
        if ($role == 1 || $role == 2) {
            $filter = '(1,3,21,81,86,101,103,121,151,85,131)';
        } elseif ($role == 3 || $role == 4 || $role == 5) {
            $filter = '(2,8,9,29,32,33,48,91,51)';
        } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
            $filter = '(4,5,22,29,31,32,33,34,40)';
        } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
            $filter = '(73,116)';
        } elseif ($role == 12 || $role == 13 || $role == 14) {
            $filter = '(41,44,46,47,21,51,82,42,84,822,93,112,124,129,122,152,154,87,89,891,132,141,144,145)';
        } elseif ($role == 17 || $role == 16) {
            $filter = '(6,11,12,13,14,17,24,25,26,27,45,40,49,104,42,821,83,871,128,127,123,153,872,88,134,133,142,143)';
        } elseif ($role == 18) {
            $filter = '(15,28,106)';
        } elseif ($role == 15) {
            $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
        } elseif ($role == 28) {
            $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
        } elseif ($role == 24 || $role == 23) {
            $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
        } elseif ($role == 22) {
            $filter = '(42,51,52,53,43,46,47,48,62,63)';
        } elseif ($role == 26 || $role == 25) {
            $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
        } elseif ($role == 21) {
            $filter = '(74,75,76,77,78)';
        } elseif ($role == 19) {
            $filter = '(43,40,41,49)';
        } elseif ($role == 32 || $role == 31) {
            $filter = '(2,28,29,31,43,44,51,52,84)';
        }
        // splitted task by authority
        $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
    } else if ($app == 'KITER') {
        // role action for KITER
        if ($role == 1 || $role == 2) {
            $filter = '(1,3,21,81,86,85,121,131,101,103,151)';
        } elseif ($role == 3 || $role == 4 || $role == 5) {
            $filter = '(2,8,9,29,32,33,48,91,42,996,51)';
        } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
            $filter = '(4,5,22,29,31,32,33,34,40)';
        } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
            $filter = '(73,116)';
        } elseif ($role == 12 || $role == 13 || $role == 14) {
            $filter = '(41,44,46,47,21,51,82,84,822,93,112,87,122,124,132,141,144,152,129,154,89,891,132,141,144,145)';
        } elseif ($role == 17 || $role == 16) {
            $filter = '(6,11,12,13,14,17,24,25,26,27,45,40,49,104,42,821,83,871,128,872,88,127,123,134,133,142,143,153)';
        } elseif ($role == 18) {
            $filter = '(15,28,106)';
        } elseif ($role == 15) {
            $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
        } elseif ($role == 28) {
            $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
        } elseif ($role == 24 || $role == 23) {
            $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
        } elseif ($role == 22) {
            $filter = '(42,51,52,53,43,46,47,48,62,63)';
        } elseif ($role == 26 || $role == 25) {
            $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
        } elseif ($role == 21) {
            $filter = '(74,75,76,77,78)';
        } elseif ($role == 19) {
            $filter = '(43,40,41,49)';
        } elseif ($role == 32 || $role == 31) {
            $filter = '(2,28,29,31,43,44,51,52,84)';
        }
        // splitted task by authority
        $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
    } else if ($app == 'KUDRAT') {
        // role action for KUDR
        if ($role == 1 || $role == 2) {
            $filter = '(1,3,21,81,86,88,101,,103,121,151,85,131)';
        } elseif ($role == 3 || $role == 4 || $role == 5) {
            $filter = '(2,8,9,6,29,32,33,48,91,996,51)';
        } elseif ($role == 6 || $role == 7 || $role == 10 || $role == 11) {
            $filter = '(4,5,22,29,31,32,33,34,40)';
        } elseif ($role == 8 || $role == 9 || $role == 10 || $role == 11) {
            $filter = '(73,116)';
        } elseif ($role == 12 || $role == 13 || $role == 14) {
            $filter = '(41,44,46,47,21,51,82,84,822,93,112,124,122,129,152,154,87,89,891,132,141,144,145)';
        } elseif ($role == 17 || $role == 16) {
            $filter = '(10,11,12,13,14,17,24,25,26,27,45,40,49,104,42,821,83,871,128,127,123,153,872,88,134,133,142,143)';
        } elseif ($role == 18) {
            $filter = '(15,28,106)';
        } elseif ($role == 15) {
            $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
        } elseif ($role == 28) {
            $filter = '(42,51,52,53,43,46,47,48,61,71,111)';
        } elseif ($role == 24 || $role == 23) {
            $filter = '(42,51,52,53,43,46,47,48,61,62,63,112)';
        } elseif ($role == 22) {
            $filter = '(42,51,52,53,43,46,47,48,62,63)';
        } elseif ($role == 26 || $role == 25) {
            $filter = '(42,51,52,53,43,46,47,48,71,72,73,74,75,76,79,113,114,115)';
        } elseif ($role == 21) {
            $filter = '(74,75,76,77,78)';
        } elseif ($role == 19) {
            $filter = '(43,40,41,49)';
        } elseif ($role == 32 || $role == 31) {
            $filter = '(2,28,29,31,43,44,51,52,84)';
        }
        // splitted task by authority
        $splitTask = [49, 41, 45, 51, 43, 46, 48, 40, 42, 821, 83, 871, 127, 123, 153, 872, 88, 134, 133, 142, 143, 82, 84, 822, 93, 112, 124, 129, 152, 154, 87, 89, 891, 132, 141, 144, 145];
    } else {
        // error control
        echo json_encode(
            array(
                'message' => 'error',
                'reason' => 'apps contains wrong value, do check your tenant setup!'
            )
        );
    }

    return ["projectTask" => $filter, "authorityTask" => $splitTask];
}

// * api function
function taskTable()
{
    global $role;

    require "api/header.php";
    require "config/system.php";
    include "config/tenant.php";
    $app = $appsTitle;

    // Connect to the database using PDO
    $conn = General::connectToDatabase();

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
    FROM view_operation_tasks LEFT JOIN public.view_authorities ON view_authorities.system_id = view_operation_tasks.system_id WHERE view_operation_tasks.status_id  IN " . Tasking::roleAction($role, $app)['projectTask'] . " OR view_authorities.authority_status IN " . Tasking::roleAction($role, $app)['projectTask']);

    // fetch current username from session
    $username = $_SESSION['username'];

    // bind the parameter to the placeholder using the bindValue method
    // $stmt->bindValue(':userName', $username);

    // Execute the query
    $stmt->execute();

    // create an array to hold the query result
    $data = array();

    // To keep track of seen SysIDs
    $seenSysIDs = array();

    // fetch the rows from the query result as an associative array
    // Convert var2 string to an array
    $roleActionArray = explode(',', trim(Tasking::roleAction($role, $app)['projectTask'], '()'));
    while ($row = $stmt->fetch()) {
        $row['userRole'] = $role;

        // filter any status id that need splitted action by authority
        if (in_array($row['AuthorityStatusID'], Tasking::roleAction($role, $app)['authorityTask'])) {
            $row['Status'] = $row['AuthorityStatus'];
            $row['StatusID'] = $row['AuthorityStatusID'];
            $row['StatusColor'] = $row['AuthorityStatusColor'];
            $row['StatusIcon'] = $row['AuthorityStatusIcon'];

            // TODO: this is added to remove duplicated application entry on caused by multiple authorities involved with the application
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

// * api function
function retrieveAssigneeListAndUpdate($roleGroup, $systemId)
{
    // Connect to the database
    $conn = General::connectToDatabase();

    // Prepare the SQL query to retrieve reference column
    $query = "SELECT reference FROM flw_appl_assigner WHERE assignee_role_group = :roleGroup LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':roleGroup', $roleGroup, PDO::PARAM_INT);
    $stmt->execute();
    $reference = $stmt->fetchColumn();
    $referenceParts = explode('.', $reference);
    // var_dump($referenceParts);

    // Retrieve the referred value using the reference column
    $query = "SELECT " . $referenceParts[1] . " FROM " . $referenceParts[0] . " WHERE system_id = :systemId LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':systemId', $systemId, PDO::PARAM_STR);
    $stmt->execute();
    $referredValue = $stmt->fetchColumn();
    // var_dump($referredValue);

    // Prepare the SQL query to retrieve assignee_list and assignee_column
    $query = "
        SELECT assignee_list, assignee_column
        FROM flw_appl_assigner
        WHERE assignee_role_group = :roleGroup
          AND :referredValue::integer[] && reference_list
    ";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':roleGroup', $roleGroup, PDO::PARAM_INT);
    $stmt->bindParam(':referredValue', $referredValue, PDO::PARAM_STR); // Assuming referredValue is integer

    // Execute the query
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($result);

    if ($result) {
        $assigneeList = $result['assignee_list']; // Convert JSON to array
        $assigneeColumn = $result['assignee_column'];
        $toBeAssignedColumn = explode('.', $assigneeColumn);

        // Prepare the SQL query to update assignee_column with assignee_list values
        $updateQuery = "
            UPDATE " . $toBeAssignedColumn[0] . "
            SET " . $toBeAssignedColumn[1] . " = :assigneeList
            WHERE system_id = :systemId
        ";

        // Prepare the update statement
        $updateStmt = $conn->prepare($updateQuery);

        // Bind parameters for the update statement
        $updateStmt->bindParam(':assigneeList', $assigneeList, PDO::PARAM_STR);
        $updateStmt->bindParam(':systemId', $systemId, PDO::PARAM_INT);

        // Execute the update query
        $updateStmt->execute();

        // Close the database connection
        $conn = null;

        return true; // Update successful
    } else {
        return false; // No matching data found
    }
}