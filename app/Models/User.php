<?php
require_once __DIR__ . '/../DB.php';

class User {

    public static function create($roleId, $fullName, $email, $passwordHash, $phone = null) {
        $db = DB::get();

        $stmt = $db->prepare(
            "INSERT INTO users (role_id, full_name, email, password, phone) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("issss", $roleId, $fullName, $email, $passwordHash, $phone);
        return $stmt->execute();
    }

    public static function findByEmail($email) {
        $db = DB::get();

        $stmt = $db->prepare("SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public static function findById($id) {
        $db = DB::get();

        $stmt = $db->prepare("SELECT u.*, r.name AS role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public static function all() {
        $db = DB::get();
        $res = $db->query("SELECT u.id, u.full_name, u.email, u.phone, r.name AS role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.created_at DESC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    public static function findByRole($roleName) {
        $db = DB::get();
        $stmt = $db->prepare("SELECT u.id, u.full_name, u.email FROM users u JOIN roles r ON u.role_id = r.id WHERE r.name = ? ORDER BY u.full_name ASC");
        $stmt->bind_param("s", $roleName);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public static function delete($id) {
        $db = DB::get();
        
        $user = self::findById($id);
        if ($user['role_name'] === 'owner') {
            $r = $db->query("SELECT id FROM restaurants WHERE owner_id = $id LIMIT 1");
            if ($r->num_rows > 0) {
                return false; 
            }
        }
        
        $db->begin_transaction();
        try {
            $stmt1 = $db->prepare("DELETE FROM bookings WHERE user_id = ?");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();

            $stmt2 = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
}