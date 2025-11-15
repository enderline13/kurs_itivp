<?php
require_once __DIR__ . '/Helpers.php';
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
    $id = intval($_POST['id'] ?? 0);
    $r = Restaurant::find($id);
    if (!$r || intval($r['owner_id']) !== $ownerId) {
        Helpers::json(['success' => false, 'message' => 'Доступ запрещен']);
    }
    $ok = Restaurant::delete($id);
    Helpers::json(['success' => $ok]);
}


Helpers::requirePostFields(['name', 'address', 'city']);
$id = $_POST['id'] ?? null;
$name = $_POST['name'];
$desc = $_POST['description'] ?? null;
$addr = $_POST['address'];
$city = $_POST['city'];

if ($id) {
    $r = Restaurant::find(intval($id));
    if (!$r || intval($r['owner_id']) !== $ownerId) {
        Helpers::json(['success' => false, 'message' => 'Доступ запрещен']);
    }
    $ok = Restaurant::update(intval($id), $ownerId, $name, $desc, $addr, $city);
} else {
    $ok = Restaurant::create($ownerId, $name, $desc, $addr, $city);
}

if ($ok) {
    Helpers::json(['success' => true]);
} else {
    Helpers::json(['success' => false, 'message' => 'Не удалось сохранить ресторан']);
}