<?php
// STUB: Assign to atikah
// set the base apps path
set_include_path(realpath($_SERVER['DOCUMENT_ROOT']));

class LetterRecord{

    public static function selectLetter()
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $queryAction = "SELECT * FROM ls_letters WHERE type = :type ORDER BY details ASC";

        $type = '1';
        // Prepare the query statement
        $stmt = $conn->prepare($queryAction);
        $stmt->bindParam(':type', $type);
        $stmt->execute();


        // Fetch the rows from the query result as an associative array
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $list;
    }

    public static function letterInDetails($id, $selection)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();
        
        // Execute a SELECT query on the database
        if ($selection == 1) {
            // for query project details
            $query = "SELECT *, sys_hr_employee.first_name, gen_letters.id AS letter_id, ls_letters.details AS letter_name, gen_letters.document AS doc, gen_letters.unique_id AS unique_id
                FROM public.gen_letters
                LEFT JOIN ls_letters ON gen_letters.type=ls_letters.id
                LEFT JOIN sys_users ON gen_letters.receiver=sys_users.username
                LEFT JOIN sys_hr_employee ON sys_users.employee_id=sys_hr_employee.id
                WHERE gen_letters.unique_id = :id";

            // Prepare the query statement
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
        }

        // Execute the query
        $stmt->execute();

        // Fetch the rows from the query result as an associative array
        $detail = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $detail;
    }

    public static function cronologyLetter($id)
    {
        // Include the database connection parameters
        global $PDO;

        $conn = General::connectToDatabase();

        // Execute a SELECT query on the database
        $query = "SELECT
        gen_letter_cronologies.title,
        gen_letter_cronologies.notes,
        gen_letter_cronologies.created_at,
        gen_letter_cronologies.status,
        sys_users.profile_pic,
        sys_hr_employee.first_name
        FROM gen_letter_cronologies
        LEFT JOIN sys_users ON sys_users.username = gen_letter_cronologies.created_by
        LEFT JOIN sys_hr_employee ON sys_hr_employee.id = sys_users.employee_id
        WHERE gen_letter_cronologies.letter_id = :id
        ORDER BY gen_letter_cronologies.created_at DESC";

        // Prepare the query statement
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);

        // Execute the query
        $stmt->execute();

        // Fetch the rows from the query result as an associative array
        $detail = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $conn = null;

        return $detail;
    }
}
?>