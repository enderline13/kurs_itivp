<?php
class DB {
    private static $instance;
    private $mysqli;

    private function __construct() {
        $config = require __DIR__ . '/../config/config.php';
        $c = $config['db'];

        $this->mysqli = new mysqli(
            $c['host'],
            $c['user'],
            $c['pass'],
            $c['name'],
            $c['port']
        );

        if ($this->mysqli->connect_errno) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'DB connection error']);
            exit;
        }
        $this->mysqli->set_charset($c['charset']);
    }

    public static function get() {
        if (!self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance->mysqli;
    }
}
