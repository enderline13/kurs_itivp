<?php
require_once __DIR__ . '/../DB.php';

class Booking {

    public static function create($userId, $restaurantId, $tableId, $startTime, $endTime, $guests, $notes = null) {
        $db = DB::get();

        $stmt = $db->prepare(
            "INSERT INTO bookings (user_id, restaurant_id, table_id, guests, start_time, end_time, status)
             VALUES (?, ?, ?, ?, ?, ?, 'confirmed')"
        );
        $stmt->bind_param("iiisss", $userId, $restaurantId, $tableId, $guests, $startTime, $endTime);
        if (!$stmt->execute()) return false;

        $id = $db->insert_id;
        
        return $id;
    }

    public static function forUser($userId) {
        $db = DB::get();

        $stmt = $db->prepare("
            SELECT 
                b.id, b.start_time, b.end_time, b.guests, b.status, b.table_id,
                r.name AS restaurant_name
            FROM bookings b
            JOIN restaurants r ON b.restaurant_id = r.id
            WHERE b.user_id = ?
            ORDER BY b.start_time DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($rows as &$r) {
            $dt = new DateTime($r['start_time']);
            $r['date'] = $dt->format('Y-m-d');
            $r['time'] = $dt->format('H:i');
        }
        return $rows;
    }

    public static function forOwner($ownerId) {
        $db = DB::get();

        $stmt = $db->prepare("
            SELECT 
                b.id, b.start_time, b.guests, b.status, b.table_id,
                r.name AS restaurant_name,
                u.full_name AS user_name
            FROM bookings b
            JOIN restaurants r ON b.restaurant_id = r.id
            JOIN users u ON b.user_id = u.id
            WHERE r.owner_id = ?
            ORDER BY b.start_time DESC
        ");
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($rows as &$r) {
            $dt = new DateTime($r['start_time']);
            $r['date'] = $dt->format('Y-m-d');
            $r['time'] = $dt->format('H:i');
        }
        return $rows;
    }

    public static function cancel($id, $userId = null) {
        $db = DB::get();
        
        if ($userId) {
            $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $id, $userId);
        } else {
            $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $stmt->bind_param("i", $id);
        }
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    public static function isTableAvailable($tableId, $startTime, $endTime) {
        $db = DB::get();
        $stmt = $db->prepare("
            SELECT COUNT(*) AS cnt FROM bookings
            WHERE table_id = ? AND status IN ('pending','confirmed')
            AND NOT (end_time <= ? OR start_time >= ?)
        ");
        $stmt->bind_param("iss", $tableId, $startTime, $endTime);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        return intval($r['cnt']) === 0;
    }

    public static function find($id) {
        $db = DB::get();
        $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}