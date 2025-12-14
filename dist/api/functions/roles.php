<?php

class Roles {

    private static function getDepartmentRoleId($department) {
        global $conn;
        $stmt = $conn->prepare('SELECT id FROM ls_user_roles WHERE department = :department');
        $stmt->bindParam(':department', $department);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getDepartment($roleId) {
        global $conn;
        $stmt = $conn->prepare("SELECT department FROM ls_user_roles WHERE id = :roleId");
        $stmt->bindParam(':roleId', $roleId);
        $stmt->execute();

        $id = $stmt->fetchColumn();

        return Roles::getDepartmentRoleId($id);
    }

    public static function getRole($roleId) {
        global $conn;

        $stmt = $conn->prepare("SELECT role FROM ls_user_roles WHERE id = :roleId");
        $stmt->bindParam(':roleId', $roleId);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}