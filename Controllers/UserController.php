<?php
namespace Controllers;
use Database\Database;

class UserControllers extends Database {
    public function getIdByToken($token) {
        $sql = "SELECT id FROM users WHERE token = ?";
        $paramse = [
            $token
        ];
        $stmt = $this->executeStatement($sql, $paramse);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['id'];
    }
}