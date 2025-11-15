<?php
require_once __DIR__ . '/../DB.php';

class OpeningHours {

    public static function getForRestaurant($restaurantId) {
        $db = DB::get();
        $stmt = $db->prepare("SELECT * FROM opening_hours WHERE restaurant_id = ? ORDER BY weekday ASC");
        $stmt->bind_param("i", $restaurantId);
        $stmt->execute();
        $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $hoursByDay = [];
        foreach ($results as $r) {
            $hoursByDay[$r['weekday']] = $r;
        }

        $finalHours = [];
        for ($i = 0; $i < 7; $i++) { 
            if (isset($hoursByDay[$i])) {
                $finalHours[] = $hoursByDay[$i];
            } else {
                $finalHours[] = [
                    'weekday' => $i,
                    'open_time' => '09:00',
                    'close_time' => '17:00',
                    'is_closed' => 1 
                ];
            }
        }
        return $finalHours;
    }

    public static function updateForRestaurant($restaurantId, $hoursData) {
        $db = DB::get();
        
        $stmt = $db->prepare(
            "INSERT INTO opening_hours (restaurant_id, weekday, open_time, close_time, is_closed) 
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE 
             open_time = VALUES(open_time), 
             close_time = VALUES(close_time), 
             is_closed = VALUES(is_closed)"
        );

        foreach ($hoursData as $day) {
            $stmt->bind_param("iissi", 
                $restaurantId, 
                $day['weekday'], 
                $day['open_time'], 
                $day['close_time'], 
                $day['is_closed']
            );
            $stmt->execute();
        }
        return true;
    }
}