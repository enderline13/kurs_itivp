<?php
// app/booking.php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/DB.php';
require_once __DIR__ . '/models/Booking.php';
require_once __DIR__ . '/models/TableModel.php';
require_once __DIR__ . '/models/OpeningHours.php'; // НОВОЕ ПОДКЛЮЧЕНИЕ

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? null;
$userId = Helpers::userId();

if (!$userId) {
    Helpers::json(['success' => false, 'message' => 'Требуется авторизация']);
}

// --- БЛОК ПРОВЕРКИ ЧАСОВ РАБОТЫ ---
function check_restaurant_hours($tableId, $date, $time) {
    $tableData = TableModel::find(intval($tableId));
    if (!$tableData) {
        return ['valid' => false, 'message' => 'Столик не найден.'];
    }
    
    $restaurantId = $tableData['restaurant_id'];
    $hours = OpeningHours::getForRestaurant($restaurantId);
    
    // PHP date('w') возвращает 0 для Воскресенья, 6 для Субботы.
    $weekdayIndex = date('w', strtotime($date)); 
    
    $dayHours = null;
    foreach ($hours as $h) {
        if ($h['weekday'] == $weekdayIndex) {
            $dayHours = $h;
            break;
        }
    }
    
    if (!$dayHours) {
        return ['valid' => false, 'message' => 'Не удалось найти расписание для этого дня.'];
    }

    if ($dayHours['is_closed'] == 1) {
        return ['valid' => false, 'message' => 'Ресторан закрыт в этот день.'];
    }
    
    // Нормализация времени для сравнения
    $openTime = $dayHours['open_time'];   // e.g., '09:00:00'
    $closeTime = $dayHours['close_time']; // e.g., '22:00:00'
    $requestTime = date('H:i:s', strtotime($time)); 
    
    // Бронирование должно быть строго до времени закрытия
    if ($requestTime < $openTime || $requestTime >= $closeTime) {
        return ['valid' => false, 'message' => "Ресторан работает с {$openTime} до {$closeTime}. Выбранное время вне интервала."];
    }

    return ['valid' => true];
}
// ------------------------------------


// 1. Создание бронирования
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    Helpers::requirePostFields(['table_id', 'date', 'time', 'guests']);
    
    $tableId = intval($_POST['table_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = intval($_POST['guests']);
    $table = TableModel::find($tableId);
    
    if (!$table) {
        Helpers::json(['success' => false, 'message' => 'Столик не найден.']);
    }

    // НОВАЯ ПРОВЕРКА ЧАСОВ
    $hoursCheck = check_restaurant_hours($tableId, $date, $time);
    if (!$hoursCheck['valid']) {
        Helpers::json(['success' => false, 'message' => $hoursCheck['message']]);
    }

    // ПРОВЕРКА НАЛИЧИЯ ДРУГИХ БРОНЕЙ (ОСТАЕТСЯ КАК БЫЛО)
    $isAvailable = Booking::isAvailable($tableId, $date, $time);
    if (!$isAvailable) {
        Helpers::json(['success' => false, 'message' => 'Столик уже занят на это время.']);
    }

    // СОЗДАНИЕ БРОНИРОВАНИЯ
    $ok = Booking::create($userId, $table['restaurant_id'], $tableId, $date, $time, $guests, 'confirmed');
    
    if ($ok) {
        Helpers::json(['success' => true, 'redirect' => 'profile.php']);
    } else {
        Helpers::json(['success' => false, 'message' => 'Не удалось создать бронь']);
    }
}

// 2. Проверка доступности
if ($action === 'check_availability') {
    $tableId = intval($_GET['table_id'] ?? 0);
    $date = $_GET['date'] ?? null;
    $time = $_GET['time'] ?? null;

    if (!$tableId || !$date || !$time) {
        Helpers::json(['available' => false, 'message' => 'Недостаточно данных для проверки']);
    }
    
    // НОВАЯ ПРОВЕРКА ЧАСОВ
    $hoursCheck = check_restaurant_hours($tableId, $date, $time);
    if (!$hoursCheck['valid']) {
        Helpers::json(['available' => false, 'message' => $hoursCheck['message']]);
    }
    
    // ПРОВЕРКА НАЛИЧИЯ ДРУГИХ БРОНЕЙ (ОСТАЕТСЯ КАК БЫЛО)
    $isAvailable = Booking::isAvailable($tableId, $date, $time);
    
    Helpers::json(['available' => $isAvailable]);
}

// 3. Отмена бронирования
if ($action === 'cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $bookingId = intval($data['id'] ?? 0);
    
    $booking = Booking::find($bookingId);
    if (!$booking || intval($booking['user_id']) !== $userId) {
        Helpers::json(['success' => false, 'message' => 'Бронь не найдена или принадлежит другому пользователю.']);
    }
    
    $ok = Booking::updateStatus($bookingId, 'cancelled');
    
    Helpers::json(['success' => $ok]);
}