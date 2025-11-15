<?php
session_start();

class Helpers {

    public static function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public static function requirePostFields(array $fields) {
        foreach ($fields as $f) {
            if (!isset($_POST[$f]) || $_POST[$f] === '') {
                self::json(['success' => false, 'message' => "Missing field: $f"]);
            }
        }
    }

    public static function userId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function userRole() {
        return $_SESSION['role'] ?? null;
    }

    public static function isLogged() {
        return isset($_SESSION['user_id']);
    }
}
