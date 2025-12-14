<?php

class Calendar {

    public static function fetchCalendarEvent(){
        global $conn;

        // Prepare the SELECT query
        $query = "SELECT id, group_id as \"groupId\", title, flw_calendars.start, flw_calendars.end, description, location, CASE WHEN all_day THEN true ELSE false END as \"allDay\", creator, guests, system_id, authority_id, created_at, report_type FROM flw_calendars ORDER BY id ASC";
        $stmt = $conn->prepare($query);

        // Execute the query
        if (!$stmt->execute()) {
            echo "An error occurred.\n";
            exit;
        }

        // Fetch the rows from the query result as an associative array
        $dataEvent = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($dataEvent);exit;

        // Convert string of allDay to boolean && string of id to integer
        $processed_dataEvent = array_map(function ($row) {
            $row['allDay'] = ($row['allDay']) ? true : false;
            $row['id'] = intval($row['id']); // convert id to integer
            $row['groupId'] = intval($row['groupId']); // convert groupId to integer
            return $row;
        }, $dataEvent);

        // Return the processed data
        return $processed_dataEvent;
    }

    // Function to check if exits and assign new key as a marker
    public static function CheckAndAssign(&$arrayToBeCheckAndAssigned, $arrayToBeCheckWith, $arrayToBeCheckAndAssignedKey, $arrayToBeCheckWithKey, $arrayToBeCheckAndAssignedNewKey, $valueToBeAssignedIfExistFromArrayToBeCheckWithKey, $valueToBeAssignedIfNotExist)
    {
        foreach ($arrayToBeCheckAndAssigned as &$element1) {
            $element1[$arrayToBeCheckAndAssignedNewKey] = $valueToBeAssignedIfNotExist; // Initialize assigned key to $valueToBeAssignedIfNotExist by default

            foreach ($arrayToBeCheckWith as $element2) {
                if ($element1[$arrayToBeCheckAndAssignedKey] == $element2[$arrayToBeCheckWithKey]) {
                    $element1[$arrayToBeCheckAndAssignedNewKey] = $element2[$valueToBeAssignedIfExistFromArrayToBeCheckWithKey]; // Set assigned key to $valueToBeAssignedIfExist if arrayToBeCheckAndAssignedKey is found
                    break; // Exit inner loop since we found a match
                }
            }
        }
    }
}
?>