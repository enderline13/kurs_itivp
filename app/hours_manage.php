<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/OpeningHours.php';
require_once __DIR__ . '/models/Restaurant.php';

if (Helpers::userRole() !== 'owner') {
    Helpers::json(['success' => false, 'message' => 'Unauthorized']);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::json(['success' => false, 'message' => 'Only POST allowed']);
}

$ownerId = Helpers::userId();
$restaurantId = intval($_POST['restaurant_id'] ?? 0);

// Безопасность: Проверяем, что владелец управляет своим рестораном
$r = Restaurant::find($restaurantId);
if (!$r || intval($r['owner_id']) !== $ownerId) {
    Helpers::json(['success' => false, 'message' => 'Доступ запрещен']);
}

$hoursData = [];
for ($i = 0; $i < 7; $i++) {
    $hoursData[] = [
        'weekday' => $i,
        'open_time' => $_POST['open'][$i] ?? '09:00',
        'close_time' => $_POST['close'][$i] ?? '17:00',
        'is_closed' => isset($_POST['closed'][$i]) ? 1 : 0
    ];
}

$ok = OpeningHours::updateForRestaurant($restaurantId, $hoursData);

if ($ok) {
    Helpers::json(['success' => true]);
} else {
    Helpers::json(['success' => false, 'message' => 'Не удалось сохранить часы']);
}