<?php
require_once __DIR__ . '/../DB.php';

class TableModel {

    public static function byRestaurant($restaurantId) {
        $db = DB::get();

        $stmt = $db->prepare("SELECT * FROM restaurant_tables WHERE restaurant_id = ?");
        $stmt->bind_param("i", $restaurantId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public static function find($id) {
        $db = DB::get();
        $stmt = $db->prepare("SELECT * FROM restaurant_tables WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public static function create($restaurantId, $seats) {
        $db = DB::get();
        $stmt = $db->prepare("INSERT INTO restaurant_tables (restaurant_id, seats) VALUES (?, ?)");
        $stmt->bind_param("ii", $restaurantId, $seats);
        return $stmt->execute();
    }

    public static function update($id, $restaurantId, $seats) {
        $db = DB::get();
        $stmt = $db->prepare("UPDATE restaurant_tables SET seats = ? WHERE id = ? AND restaurant_id = ?");
        $stmt->bind_param("iii", $seats, $id, $restaurantId);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }

    public static function delete($id) {
        $db = DB::get();
        
        $stmt1 = $db->prepare("DELETE FROM bookings WHERE table_id = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        
        $stmt = $db->prepare("DELETE FROM restaurant_tables WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return ($stmt->affected_rows > 0);
    }
}