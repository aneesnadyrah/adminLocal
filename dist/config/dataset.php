<?php
class Dataset {

    public static function RoleAssignments($roleId) {
        require "config/system.php";
        $conn = new PDO($PDO);

        $stmt = $conn->prepare("SELECT department FROM ls_user_roles WHERE id = :roleId");
        $stmt->bindParam(':roleId', $roleId);
        if($stmt->execute()) {
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                $flowGroup = $row['department'];

                $stmt = $conn->prepare("SELECT id FROM ls_statuses WHERE flow_group = :flowGroup");
                $stmt->bindParam(':flowGroup', $flowGroup);
                $stmt->execute();

                return $stmt->fetchAll();
            } else if ($flowGroup === "management") {

                if($roleId == 5) {
                    $statusId = [1,2,3,4,5,6];
                } else if($roleId == 6) {
                    $statusId = [1,2,3,4,5,6];
                }

                return $statusId;

            }
        };

    }

}