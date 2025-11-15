<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/Booking.php';
require_once __DIR__ . '/models/TableModel.php';
require_once __DIR__ . '/models/Restaurant.php';
require_once __DIR__ . '/models/User.php';

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    if (!Helpers::isLogged()) Helpers::json(['success' => false, 'message' => 'Unauthorized']);

    Helpers::requirePostFields(['restaurant_id', 'table_id', 'date', 'time', 'guests']);

    $userId = Helpers::userId();
    $restaurantId = intval($_POST['restaurant_id']);
    $tableId = intval($_POST['table_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];
    $guests = intval($_POST['guests']);
    $notes = $_POST['notes'] ?? null;

    $table = TableModel::find($tableId);
    if (!$table || intval($table['restaurant_id']) !== $restaurantId) {
        Helpers::json(['success' => false, 'message' => 'Table not found in restaurant']);
    }

    try {
        $dt = new DateTime($date . ' ' . $time);
    } catch (Exception $e) {
        Helpers::json(['success' => false, 'message' => 'Invalid date/time']);
    }
    $start = $dt->format('Y-m-d H:i:s');
    $dt->modify('+2 hours');
    $end = $dt->format('Y-m-d H:i:s');

    if (!Booking::isTableAvailable($tableId, $start, $end)) {
        Helpers::json(['success' => false, 'message' => 'Столик недоступен на выбранное время']);
    }

    $id = Booking::create($userId, $restaurantId, $tableId, $start, $end, $guests, $notes);
    if ($id) {
        Helpers::json(['success' => true, 'redirect' => '/profile.php']);
    } else {
        Helpers::json(['success' => false, 'message' => 'Не удалось создать бронь']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'cancel') {
    $payload = json_decode(file_get_contents('php://input'), true);
    $id = intval($payload['id'] ?? 0);
    if (!$id) Helpers::json(['success' => false, 'message' => 'id required']);
    if (!Helpers::isLogged()) Helpers::json(['success' => false, 'message' => 'Unauthorized']);

    $userId = Helpers::userId();
    $b = Booking::find($id);
    if (!$b) Helpers::json(['success' => false, 'message' => 'Booking not found']);
    
    if (intval($b['user_id']) !== $userId) Helpers::json(['success' => false, 'message' => 'No permission']);

    $ok = Booking::cancel($id, $userId);
    Helpers::json(['success' => $ok]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'check_availability') {
    $tableId = intval($_GET['table_id'] ?? 0);
    $date = $_GET['date'] ?? null;
    $time = $_GET['time'] ?? null;

    if (!$tableId || !$date || !$time) {
        Helpers::json(['available' => false, 'message' => 'Missing params']);
    }

    try {
        $dt = new DateTime($date . ' ' . $time);
    } catch (Exception $e) {
        Helpers::json(['available' => false, 'message' => 'Invalid date/time']);
    }
    $start = $dt->format('Y-m-d H:i:s');
    $dt->modify('+2 hours');
    $end = $dt->format('Y-m-d H:i:s');

    $isAvailable = Booking::isTableAvailable($tableId, $start, $end);
    
    Helpers::json(['available' => $isAvailable]);
}


Helpers::json(['success' => false, 'message' => 'Unknown action']);