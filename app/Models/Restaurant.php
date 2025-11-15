<?php
require_once __DIR__ . '/../DB.php';

class Restaurant {

    public static function all($filters = []) {
        $db = DB::get();
        $sql = "SELECT * FROM restaurants WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filters['city'])) {
            $sql .= " AND city LIKE ?";
            $params[] = '%' . $filters['city'] . '%';
            $types .= 's';
        }
        if (!empty($filters['min_seats'])) {
            
            $sql = "SELECT DISTINCT r.* FROM restaurants r JOIN restaurant_tables t ON t.restaurant_id = r.id WHERE t.seats >= ?";
            $params = [ intval($filters['min_seats']) ];
            $types = 'i';
        }

        $sql .= " ORDER BY created_at DESC";

        if ($types) {
            $stmt = $db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $res = $db->query($sql);
            return $res->fetch_all(MYSQLI_ASSOC);
        }
    }

    public static function find($id) {
        $db = DB::get();

        $stmt = $db->prepare("SELECT * FROM restaurants WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public static function forOwner($ownerId) {
        $db = DB::get();
        $stmt = $db->prepare("SELECT * FROM restaurants WHERE owner_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $ownerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function create($ownerId, $name, $description, $address, $city = null) {
        $db = DB::get();

        $stmt = $db->prepare(
            "INSERT INTO restaurants (owner_id, name, description, address, city) VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->bind_param("issss", $ownerId, $name, $description, $address, $city);
        return $stmt->execute();
    }

    public static function update($id, $ownerId, $name, $description, $address, $city) {
        $db = DB::get();
        $stmt = $db->prepare(
            "UPDATE restaurants 
             SET name = ?, description = ?, address = ?, city = ? 
             WHERE id = ? AND owner_id = ?"
        );
        $stmt->bind_param("ssssii", $name, $description, $address, $city, $id, $ownerId);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    public static function delete($id) {
        $db = DB::get();
        
        $db->begin_transaction();
        try {
            $stmt1 = $db->prepare("DELETE FROM bookings WHERE restaurant_id = ?");
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            
            $stmt2 = $db->prepare("DELETE FROM restaurant_tables WHERE restaurant_id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();

            $stmt3 = $db->prepare("DELETE FROM restaurants WHERE id = ?");
            $stmt3->bind_param("i", $id);
            $stmt3->execute();

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
}