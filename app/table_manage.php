<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/TableModel.php';
require_once __DIR__ . '/models/Restaurant.php';

if (Helpers::userRole() !== 'owner') {
    Helpers::json(['success' => false, 'message' => 'Unauthorized']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Helpers::json(['success' => false, 'message' => 'Only POST allowed']);
}

$ownerId = Helpers::userId();
$action = $_POST['action'] ?? null;

if ($action === 'delete') {
    $id = $_POST['id'] ?? 0;
    $ok = TableModel::delete(intval($id));
    Helpers::json(['success' => $ok]);
}


Helpers::requirePostFields(['restaurant_id', 'seats']);
$id = $_POST['id'] ?? null;
$restaurantId = intval($_POST['restaurant_id']);
$seats = intval($_POST['seats']);

$r = Restaurant::find($restaurantId);
if (!$r || intval($r['owner_id']) !== $ownerId) {
    Helpers::json(['success' => false, 'message' => 'Доступ запрещен']);
}

if ($id) {
    $ok = TableModel::update(intval($id), $restaurantId, $seats);
} else {
    $ok = TableModel::create($restaurantId, $seats);
}

if ($ok) {
    Helpers::json(['success' => true]);
} else {
    Helpers::json(['success' => false, 'message' => 'Не удалось сохранить столик']);
}