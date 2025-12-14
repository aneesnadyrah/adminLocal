<?php
// STUB: Assign to fakhri
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

class Permitting
{
    //data  for provider address
    public static function letterAddress($systemId)
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT view_operation_tasks.*, CONCAT(ls_provider.address_1, ' ', ls_provider.address_2) AS \"Address\", ls_provider.postcode AS \"Postcode\",ls_provider.city AS \"City\", ls_provider.state AS \"States\"  FROM view_operation_tasks LEFT JOIN ls_provider ON provider_id = ls_provider.id WHERE status_id IN ('21', '41') AND system_id = :systemId";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['StatusID'] < 10) {
                $row['StatusID'] = '00' . $row['StatusID'];
            } else if ($row['StatusID'] < 100) {
                $row['StatusID'] = '0' . $row['StatusID'];
            }

            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;
        return $modalID;
    }

    //data from public.calender_view
    public static function selectCalendar($systemId)
    {
        require_once "config/system.php";

        global $role;

        // Connect to the database
        $conn = General::connectToDatabase();

        $query = "SELECT * FROM public.calendar_view WHERE \"SysID\" = :systemId";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a JSON string
        // $json = '{"data":' . json_encode($data) . '}';

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;
        // return $json;
        return $modalID;
    }

    public static function generateLetter($systemId, $id, $letterType)
    {
        require_once "config/system.php";

        global $role;

        // Connect to the database
        $conn = General::connectToDatabase();

        $query = "SELECT * FROM flw_generated_letter WHERE type = :letterType AND system_id = :systemId AND id = :id";
        // $result = pg_query($conn,  $query);
        $stmt = $conn->prepare($query);

        $stmt->bindValue(':letterType', $letterType);
        $stmt->bindValue(':systemId', $systemId);
        $stmt->bindValue(':id', $id);

        $stmt->execute();
        // Check for errors in the query
        // if (!$result) {
        //     die("Error in query: " . pg_last_error());
        // }

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a JSON string
        // $json = '{"data":' . json_encode($data) . '}';

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;
        return $modalID;

    }

    public static function letterStatus($letterNo)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT * FROM ctrl_letter WHERE letter_no = :letterNo";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':letterNo', $letterNo);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $entry = $data;

        // Close the database connection
        $conn = null;

        return $entry;
    }

    // used in this file
    public static function letterAction($role)
    {
        if ($role == 11 || $role == 12) {
            $filter = '(0)';
        } elseif ($role == 21 || $role == 22) {
            $filter = '(0)';
        } elseif ($role == 31 || $role == 32 || $role == 33) {
            $filter = '(0)';
        } elseif ($role == 31 || $role == 34 || $role == 35) {
            $filter = '(0)';
        } elseif ($role == 41 || $role == 42 || $role == 43 || $role == 44) {
            $filter = '(1,2,3,4)';
        } elseif ($role == 52) {
            $filter = '(0)';
        } elseif ($role == 51) {
            $filter = '(1)';
        } elseif ($role == 53) {
            $filter = '(0)';
        } elseif ($role == 61) {
            $filter = '(0)';
        } elseif ($role == 62) {
            $filter = '(0)';
        } elseif ($role == 63) {
            $filter = '(0)';
        } elseif ($role == 64) {
            $filter = '(0)';
        } elseif ($role == 73) {
            $filter = '(1,2,3,4)';
        } elseif ($role == 90) {
            $filter = '(0)';
        }

        return $filter;
    }

    public static function letterRole($role, $app = null)
    {
        if ($role == 11 || $role == 12) {
            $filter = '(1,3,21,83,86,88)';
        } elseif ($role == 21 || $role == 22) {
            $filter = '(2,29,31,33,42,51,83,91)';
        } elseif ($role == 31 || $role == 32 || $role == 33) {
            $filter = '(4,5,22)';
        } elseif ($role == 31 || $role == 34 || $role == 35) {
            $filter = '(73,116)';
        } elseif ($role == 41 || $role == 42 || $role == 43 || $role == 44) {
            $filter = '(21,41)';
        } elseif ($role == 52) {
            $filter = '(6,11,12,13,14,24,25,26,27,103,104)';
        } elseif ($role == 51) {
            $filter = '(21,15,28,106)';
        } elseif ($role == 53) {
            $filter = '(93,101,102,103,119,121,122,123,124,126,131,132,133,134)';
        } elseif ($role == 61) {
            $filter = '(61,63,111)';
        } elseif ($role == 62) {
            $filter = '(61,62,112)';
        } elseif ($role == 63) {
            $filter = '(62,113,114,115)';
        } elseif ($role == 64) {
            $filter = '(63,71,74,75)';
        } elseif ($role == 90) {
            $filter = '(2,28,29,31,43,44,51,52,84)';
        }

        return $filter;
    }

    public static function letterModal()
    {
        require_once "config/system.php";
        $role = $_SESSION['roleId'];

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute query on the database
        $stmt = $conn->prepare("SELECT * FROM public.letter_listing WHERE \"LetterStatusID\" IN " . Permitting::letterAction($role) . "");

        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':userName', $username);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['StatusID'] < 10) {
                $row['StatusID'] = '00' . $row['StatusID'];
            } else if ($row['StatusID'] < 100) {
                $row['StatusID'] = '0' . $row['StatusID'];
            }
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    // FIXME: modify public static function based on UPI standard
    public static function selectAuthority($systemId, $authorityId)
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT authority.authority_id AS \"AuthorityID\", authority.authority_name AS \"AuthorityName\", COALESCE(ls_authorities.address_1,'') AS \"AuthorityAddress1\", COALESCE(ls_authorities.address_2,'') AS \"AuthorityAddress2\", COALESCE(ls_authorities.postcode,'') AS \"AuthorityPostcode\",  COALESCE(ls_district.name,'') AS \"DistrictName\", COALESCE(ls_authorities.state,'') AS \"AuthorityState\", COALESCE(ls_authorities.pic,'') AS \"AuthorityPIC\" FROM public.entry_on_authority authority LEFT JOIN ls_authorities ON ls_authorities.id = authority.authority_id LEFT JOIN ls_district ON ls_district.id = authority.authority_district WHERE authority.system_id = :systemId AND authority.authority_id = :authorityId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authorityId', $authorityId);

        $stmt->execute();
        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $authority = $data;

        // Close the database connection
        $conn = null;
        return $authority;
    }

    public static function titleLetter($systemId)
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT public.master_view.project_title AS \"ProjectTitle\" FROM public.master_view WHERE public.master_view.system_id = :systemId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $authority = $data;

        // Close the database connection
        $conn = null;
        return $authority;
    }

    public static function reviewLetter($systemId, $letterId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // $systemId = $_POST['system-id'];

        // Execute a SELECT query on the database
        $query = "SELECT *, public.letter_listing.\"AuthorityId\" AS \"AuthorityId\"
    FROM public.letter_listing
    WHERE public.letter_listing.\"SysID\" = :systemId AND public.letter_listing.flw_generated_letter_id = :letterId";

        // $result = pg_query($conn,  $query);

        // // Check for errors in the query
        // if (!$result) {
        //     die("Error in query: " . pg_last_error());
        // }
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':letterId', $letterId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            if ($row['StatusID'] < 10) {
                $row['StatusID'] = '00' . $row['StatusID'];
            } else if ($row['StatusID'] < 100) {
                $row['StatusID'] = '0' . $row['StatusID'];
            }

            $data[] = $row;
        }

        // convert the result to a Array
        $authority = $data;

        // Close the database connection
        $conn = null;
        return $authority;
    }


    // FIXME: modify authorities based on UPI standard
    public static function filterAuthority($authorityId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT *,COALESCE(ls_authorities.name,'') AS \"AuthorityName\", COALESCE(ls_districts.name,'') AS \"DistrictName\"
    FROM ls_authorities
    LEFT JOIN ls_districts ON ls_districts.id = ls_authorities.district
    WHERE ls_authorities.id = :authorityId";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':authorityId', $authorityId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $authority = $data;

        // Close the database connection
        $conn = null;
        return $authority;
    }

    public static function reportInfo($systemId, $authorityId)
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        //data for workMethod and workMethodCode
        // $authorityId = trim($authorityId, '{}');
        // $authorityIds = array_map('intval', explode(',', $authorityId));
        // $authorityArray = '{' . implode(',', $authorityIds) . '}';
        // Execute a SELECT query on the database
        $query = "SELECT view_report_sitevisit.entry_list_road_id AS \"EntryRoadID\",
                 flw_appl_roads.road_name AS \"RoadName\",
                 ls_work_methods.name AS \"workMethod\",
                 ls_work_methods.method AS \"workMethodCode\"
          FROM view_report_sitevisit
          LEFT JOIN flw_appl_roads ON flw_appl_roads.id = ANY(view_report_sitevisit.entry_list_road_id)
          LEFT JOIN ls_work_methods ON ls_work_methods.id = ANY(flw_appl_roads.method)
          WHERE view_report_sitevisit.system_id = :systemId AND view_report_sitevisit.authority_id = :authorityId";

        // $result = pg_query($conn,  $query);

        // // Check for errors in the query
        // if (!$result) {
        //     die("Error in query: " . pg_last_error());
        // }
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindValue(':authorityId', $authorityId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        $query2 = "SELECT view_report_sitevisit.entry_list_road_id AS \"EntryRoadID\",
                 flw_appl_roads.road_name AS \"RoadName\"
          FROM view_report_sitevisit
          LEFT JOIN flw_appl_roads ON flw_appl_roads.id = ANY(view_report_sitevisit.entry_list_road_id)
          WHERE view_report_sitevisit.system_id = :systemId AND view_report_sitevisit.authority_id = :authorityId";

        // $result2 = pg_query($conn,  $query2);

        // // Check for errors in the query
        // if (!$result2) {
        //     die("Error in query: " . pg_last_error());
        // }
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':systemId', $systemId);
        $stmt2->bindValue(':authorityId', $authorityId);

        $stmt2->execute();

        // create an array to hold the query result
        $data2 = array();

        // fetch the rows from the query result as an associative array
        while ($row2 = $stmt2->fetch()) {
            $data2[] = $row2;
        }

        // convert the result to a Array
        $authority = array(
            'methodData' => $data,
            'roadData' => $data2
        );

        // Close the database connection
        $conn = null;
        return $authority;
    }

    public static function formulaWC($name, $quantity)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT formula FROM ls_deposit_formula WHERE name = :name";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':name', $name);

        $stmt->execute();

        $formula = $stmt->fetchColumn();

        $formula = str_replace('kuantiti', $quantity, $formula);

        $result = eval("return $formula;");

        // Round up the result to two decimal places
        $roundedResult = round($result, 2);

        // Format the rounded result with comma separators for thousands, millions, and billions
        $formattedResult = number_format($roundedResult, 2, '.', ',');

        // Close the database connection
        $conn = null;

        return $formattedResult;
    }

    public static function selection_work_method()
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT * FROM ls_work_methods";


        $stmt = $conn->prepare($query);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function selection_rp_item()
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT * FROM ls_appl_summaries";


        $stmt = $conn->prepare($query);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function taskId($systemId)
    {
        require_once "config/system.php";
        include "config/tenant.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute query on the database
        $stmt = $conn->prepare("SELECT * FROM view_operation_tasks WHERE \"SysID\" = :systemId");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {

            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['StatusID'] < 10) {
                $row['StatusID'] = '00' . $row['StatusID'];
            } else if ($row['StatusID'] < 100) {
                $row['StatusID'] = '0' . $row['StatusID'];
            }
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function authorityRp($systemId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        // $query = "SELECT * FROM flw_appl_summary WHERE system_id = :systemId";
        $query = "SELECT flw_appl_summary.*
    FROM flw_appl_summary
    LEFT JOIN ctrl_project_summary ON ctrl_project_summary.id = flw_appl_summary.ctrl_summary_id
    WHERE flw_appl_summary.system_id = :systemId
    AND (
      ctrl_project_summary.system_id, ctrl_project_summary.revision
    ) = (
      SELECT cp.system_id, max(cp.revision) AS max_revision
      FROM ctrl_project_summary cp
      WHERE cp.system_id = :systemId
      GROUP BY cp.system_id
    )";


        $stmt = $conn->prepare($query);
        $stmt->bindValue(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function authorityWc($systemId, $authId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        // $query = "SELECT * FROM flw_appl_deposit WHERE system_id = :systemId";
        $query = "SELECT flw_appl_deposit.* FROM flw_appl_deposit LEFT JOIN ctrl_deposit ON ctrl_deposit.id = flw_appl_deposit.ctrl_deposit_id WHERE flw_appl_deposit.system_id = :systemId AND flw_appl_deposit.authority_id = :authId  AND (
        ctrl_deposit.system_id,ctrl_deposit.authority_id, ctrl_deposit.revision
      ) = (
        SELECT cd.system_id, cd.authority_id, max(cd.revision) AS max_revision
        FROM ctrl_deposit cd
        WHERE cd.system_id = :systemId AND cd.authority_id = :authId
        GROUP BY cd.system_id, cd.authority_id
      )";


        $stmt = $conn->prepare($query);
        $stmt->bindValue(':systemId', $systemId);
        $stmt->bindValue(':authId', $authId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function dataWc($systemId, $authority)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT flw_appl_deposit.* FROM flw_appl_deposit LEFT JOIN ctrl_deposit ON ctrl_deposit.id = flw_appl_deposit.ctrl_deposit_id WHERE flw_appl_deposit.system_id = :systemId AND flw_appl_deposit.authority_id = :authority AND (
        ctrl_deposit.system_id,ctrl_deposit.authority_id, ctrl_deposit.revision
      ) = (
        SELECT cd.system_id, cd.authority_id, max(cd.revision) AS max_revision
        FROM ctrl_deposit cd
        WHERE cd.system_id = :systemId AND cd.authority_id = :authority
        GROUP BY cd.system_id, cd.authority_id
      )";


        $stmt = $conn->prepare($query);
        $stmt->bindValue(':systemId', $systemId);
        $stmt->bindValue(':authority', $authority);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function dataRp($systemId, $authority)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT flw_appl_summary.* FROM flw_appl_summary LEFT JOIN ctrl_project_summary ON ctrl_project_summary.id = flw_appl_summary.ctrl_summary_id WHERE flw_appl_summary.system_id = :systemId AND flw_appl_summary.authority_id = :authority AND (
        ctrl_project_summary.system_id, ctrl_project_summary.revision
      ) = (
        SELECT cp.system_id, max(cp.revision) AS max_revision
        FROM ctrl_project_summary cp
        WHERE cp.system_id = :systemId
        GROUP BY cp.system_id
      )";


        $stmt = $conn->prepare($query);
        $stmt->bindValue(':systemId', $systemId);
        $stmt->bindValue(':authority', $authority);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // Close the database connection
        $conn = null;

        return $data;
    }

    public static function getLetterNo($letterId)
    {
        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        $query = "SELECT letter.letter_ref_no, letter.authority_id, auth.group FROM flw_generated_letter letter LEFT JOIN ls_authorities auth ON auth.id = letter.authority_id WHERE letter.id = :letterId";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':letterId', $letterId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        $row = $stmt->fetch();

        // Close the database connection
        $conn = null;

        return $row;

    }

    public static function authorityModal()
    {
        require_once "config/system.php";
        include "config/tenant.php";
        $role = $_SESSION['roleId'];
        $app = $appsTitle;

        // Connect to the database using PDO
        $conn = General::connectToDatabase();


        $stmt = $conn->prepare("SELECT * FROM public.view_authorities");


        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {

            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['project_status'] < 10) {
                $row['project_status'] = '00' . $row['project_status'];
            } else if ($row['project_status'] < 100) {
                $row['project_status'] = '0' . $row['project_status'];
            }

            if ($row['authority_status'] < 10) {
                $row['authority_status'] = '00' . $row['authority_status'];
            } else if ($row['authority_status'] < 100) {
                $row['authority_status'] = '0' . $row['authority_status'];
            }

            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function authorityData($systemId, $authId)
    {
        require_once "config/system.php";
        include "config/tenant.php";
        $role = $_SESSION['roleId'];
        $app = $appsTitle;

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare("SELECT * FROM public.view_authorities WHERE system_id = :systemId AND authority_id = :authId ORDER BY authority_group");

        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authId', $authId);


        // fetch current username from session
        $username = $_SESSION['username'];

        // bind the parameter to the placeholder using the bindValue method
        // $stmt->bindValue(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {

            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['project_status'] < 10) {
                $row['project_status'] = '00' . $row['project_status'];
            } else if ($row['project_status'] < 100) {
                $row['project_status'] = '0' . $row['project_status'];
            }

            if ($row['authority_status'] < 10) {
                $row['authority_status'] = '00' . $row['authority_status'];
            } else if ($row['authority_status'] < 100) {
                $row['authority_status'] = '0' . $row['authority_status'];
            }

            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }


    public static function getRoadInvolved($systemId, $authId)
    {

        require_once "config/system.php";
        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        // Get the contact signature list
        $query = "SELECT authority.road_involved_id
    FROM public.view_authorities authority
    WHERE authority.system_id = :systemId AND authority.authority_id = :authId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authId', $authId);
        $stmt->execute();
        $roadIdStr = $stmt->fetchColumn();

        // convert into array
        $roadIdArray = array_map('intval', explode(',', str_replace(array('{', '}'), '', $roadIdStr)));

        // var_dump($roadIdArray);

        // get the contact details
        $roadDetails = [];
        foreach ($roadIdArray as $roadId) {
            $query = "SELECT * FROM flw_appl_roads WHERE id = :roadId";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':roadId', $roadId, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $roadDetails[] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }

        foreach ($roadDetails as &$roadDetail) {
            // Extract the integers from the "method" value
            if (isset($roadDetail['method'])) {
                $methodStr = $roadDetail['method'];
                $methodStr = str_replace(array('{', '}'), '', $methodStr); // Remove curly braces from the string
                $methodArray = array_map('intval', explode(',', $methodStr));
                $methodName = [];

                foreach ($methodArray as $method) {
                    $query = "SELECT * FROM ls_work_methods WHERE id = :roadId";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':roadId', $method, PDO::PARAM_STR);
                    $stmt->execute();
                    $methodName[] = $stmt->fetch(PDO::FETCH_ASSOC);
                }



                // Replace the "method" value with the array of integers
                $roadDetail['method'] = $methodName;
            }
        }

        // Close the database connection
        $conn = null;

        // Return the JSON string to the client
        return $roadDetails;
    }


    public static function dataMasterView($systemId)
    {
        require_once "config/system.php";
        include "config/tenant.php";

        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        // Execute query on the database
        $stmt = $conn->prepare("SELECT * FROM public.master_view WHERE system_id = :systemId");

        // bind the parameter to the placeholder using the bindValue method
        $stmt->bindValue(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {

            // Format the StatusID field to have leading zeroes if it's less than 100
            if ($row['status_id'] < 10) {
                $row['status_id'] = '00' . $row['status_id'];
            } else if ($row['status_id'] < 100) {
                $row['status_id'] = '0' . $row['status_id'];
            }
            $data[] = $row;
        }

        // convert the result to a Array
        $modalID = $data;

        // Close the database connection
        $conn = null;

        return $modalID;
    }

    public static function SummaryListing($systemId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
    *
    FROM public.summary_listing WHERE system_id = :systemId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $entry = $data;

        // Close the database connection
        // pg_close($conn);
        $conn = null;

        return $entry;
    }

    public static function DepositListing($systemId, $authId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
    *
    FROM public.deposit_listing WHERE system_id = :systemId AND authority_id = :authId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);
        $stmt->bindParam(':authId', $authId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $entry = $data;

        // Close the database connection
        // pg_close($conn);
        $conn = null;

        return $entry;
    }

    public static function getQuoteApproval($id)
    {
        require_once "config/system.php";
        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT * FROM flw_appl_verifies
                WHERE system_id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result;
    }


    public static function contactRPClient($systemId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT entries.contact_id, contact.full_name, contact.company_name, contact.phone_no, contact.position, types.name FROM flw_appl_entries entries LEFT JOIN flw_appl_contacts contact ON contact.id = any(entries.contact_id) LEFT JOIN ls_type_contacts types ON types.id = contact.type WHERE entries.system_id = :systemId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }

        // convert the result to a Array
        $entry = $data;

        // Close the database connection
        // pg_close($conn);
        $conn = null;

        return $entry;
    }

    public static function contactRPKUN($systemId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT entryAuth.pkd_assignee, sys_users.employee_id, sys_hr_employee.first_name, sys_hr_employee.last_name, sys_hr_employee.position, sys_hr_employee.phone_no FROM public.view_authorities entryAuth LEFT JOIN sys_users ON sys_users.username = any(entryAuth.pkd_assignee) LEFT JOIN sys_hr_employee ON sys_hr_employee.id = sys_users.employee_id WHERE entryAuth.system_id = :systemId";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Create an array to hold unique values of pkd_assignee
        $uniqueValues = array();

        // create an array to hold the query result
        $data = array();

        // fetch the rows from the query result as an associative array
        while ($row = $stmt->fetch()) {
            // Check if pkd_assignee is not in the uniqueValues array
            if (!in_array($row['pkd_assignee'], $uniqueValues)) {
                // Add the row to the data array
                $data[] = $row;

                // Add pkd_assignee to the uniqueValues array
                $uniqueValues[] = $row['pkd_assignee'];
            }
        }

        // convert the result to a Array
        $entry = $data;

        // Close the database connection
        // pg_close($conn);
        $conn = null;

        return $entry;
    }

    public static function workMethodRP($systemId, $authId)
    {

        require_once "config/system.php";

        // Connect to the database
        $conn = General::connectToDatabase();

        // Execute the first query
        $query = "SELECT id
FROM ctrl_project_summary
WHERE system_id = :systemId
  AND revision = (SELECT MAX(revision) FROM ctrl_project_summary WHERE system_id = :systemId);";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':systemId', $systemId);

        $stmt->execute();

        // Initialize $ctrl_summary_id to a default value or null
        $ctrl_summary_id = null;

        // Check if a row was returned in the first query
        if ($row = $stmt->fetch()) {
            $ctrl_summary_id = $row['id'];

            // Execute the second query only if $ctrl_summary_id has a value
            $query2 = "SELECT flw_appl_summary.*, ls_work_methods.* FROM flw_appl_summary INNER JOIN ls_work_methods ON flw_appl_summary.method = ls_work_methods.method WHERE ctrl_summary_id = :ctrl_summary_id AND authority_id = :authId";

            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':ctrl_summary_id', $ctrl_summary_id);
            $stmt2->bindParam(':authId', $authId);

            $stmt2->execute();

            // create an array to hold the query result
            $data = array();

            // fetch the rows from the query result as an associative array
            while ($row2 = $stmt2->fetch()) {
                $data[] = $row2;
            }


        } else {
            // Handle the case where no data was found in the first query
            $data = array();

        }

        // convert the result to a Array
        $entry = $data;



        // Close the database connection
        // pg_close($conn);
        $conn = null;

        return $entry;

    }

    public static function getCancellationNotes($id)
    {
        require_once "config/system.php";
        // Connect to the database using PDO
        $conn = General::connectToDatabase();

        $stmt = $conn->prepare('SELECT cancellation_notes, reference_no, project_title FROM flw_appl_entries
                WHERE system_id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $result;
    }
}

// * api function
function summaryAction($role)
{
    if ($role == 11 || $role == 12) {
        $filter = '(0)';
    } elseif ($role == 21 || $role == 22) {
        $filter = '(0)';
    } elseif ($role == 31 || $role == 32 || $role == 33) {
        $filter = '(0)';
    } elseif ($role == 31 || $role == 34 || $role == 35) {
        $filter = '(0)';
    } elseif ($role == 41) {
        $filter = '(0)';
    } elseif ($role == 42 || $role == 43 || $role == 44) {
        $filter = '(0)';
    } elseif ($role == 52) {
        $filter = '(1,2,3,4)';
    } elseif ($role == 51) {
        $filter = '(1)';
    } elseif ($role == 53) {
        $filter = '(0)';
    } elseif ($role == 61) {
        $filter = '(0)';
    } elseif ($role == 62) {
        $filter = '(0)';
    } elseif ($role == 63) {
        $filter = '(0)';
    } elseif ($role == 64) {
        $filter = '(0)';
    } elseif ($role == 73) {
        $filter = '(1,2,3,4)';
    } elseif ($role == 90) {
        $filter = '(0)';
    }

    return $filter;
}

// * api function
function depositAction($role)
{
    if ($role == 11 || $role == 12) {
        $filter = '(0)';
    } elseif ($role == 21 || $role == 22) {
        $filter = '(0)';
    } elseif ($role == 31 || $role == 32 || $role == 33) {
        $filter = '(0)';
    } elseif ($role == 31 || $role == 34 || $role == 35) {
        $filter = '(0)';
    } elseif ($role == 41) {
        $filter = '(0)';
    } elseif ($role == 42 || $role == 43 || $role == 44) {
        $filter = '(0)';
    } elseif ($role == 52) {
        $filter = '(1,3,4)';
    } elseif ($role == 51) {
        $filter = '(1)';
    } elseif ($role == 53) {
        $filter = '(0)';
    } elseif ($role == 61) {
        $filter = '(0)';
    } elseif ($role == 62) {
        $filter = '(0)';
    } elseif ($role == 63) {
        $filter = '(0)';
    } elseif ($role == 64) {
        $filter = '(0)';
    } elseif ($role == 73) {
        $filter = '(1,2,3,4)';
    } elseif ($role == 90) {
        $filter = '(0)';
    }

    return $filter;
}

// * api function
function letterTable()
{
    global $role;
    // // Check for errors in the connection
    // if (!$conn) {
    //     die("Error in connection: " . pg_last_error());
    // }
    require "api/header.php";
    require_once "config/system.php";

    // Connect to the database
    $conn = General::connectToDatabase();

    // Execute a SELECT query on the database
    $query = "SELECT * FROM public.letter_listing WHERE \"LetterStatusID\" IN " . Permitting::letterAction($role) . "";

    $stmt = $conn->prepare($query);
    // Execute the query
    $stmt->execute();

    // $result = pg_query($conn, $query);
    $username = $_SESSION['username'];

    $data = array();

    while ($row = $stmt->fetch()) {
        // specific filtering on each needed status (primarily used for specific user assignment)
        if ($row['StatusID'] == 5) {
            if ($row['GISAssign'] != $username) {
                unset($row);
                continue;
                // process the row normally
            }
        }

        // Format the StatusID field to have leading zeroes if it's less than 100
        if ($row['StatusID'] < 10) {
            $row['StatusID'] = '00' . $row['StatusID'];
        } else if ($row['StatusID'] < 100) {
            $row['StatusID'] = '0' . $row['StatusID'];
        }
        $data[] = $row;
    }

    // $result = pg_query($conn, $query);

    // Check for errors in the query
    // if (!$result) {
    //     die("Error in query: " . pg_last_error());
    // }

    // create an array to hold the query result
    // $data = array();

    // fetch the rows from the query result as an associative array
    // while ($row = pg_fetch_assoc($result)) {
    //     // Format the StatusID field to have leading zeroes if it's less than 100
    //     // if ($row['StatusID'] < 10) {
    //     //     $row['StatusID'] = '00' . $row['StatusID'];
    //     // } else if ($row['StatusID'] < 100) {
    //     //     $row['StatusID'] = '0' . $row['StatusID'];
    //     // }

    //     $data[] = $row;
    // }

    // convert the result to a JSON string
    $json = '{"data":' . json_encode($data) . '}';

    // Write the JSON string to a file

    // Close the database connection
    // pg_close($conn);
    $conn = null;

    return $json;
}