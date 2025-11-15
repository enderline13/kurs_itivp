<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Restaurant.php';

if (Helpers::userRole() !== 'admin') {
    Helpers::json(['success' => false, 'message' => 'Unauthorized']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $id = intval($_POST['id'] ?? 0);
    if (!$id) Helpers::json(['success' => false, 'message' => 'ID required']);

    if ($action === 'delete_user') {
        $ok = User::delete($id);
        Helpers::json(['success' => $ok]);
    }

    if ($action === 'delete_restaurant') {
        $ok = Restaurant::delete($id);
        Helpers::json(['success' => $ok]);
    }
    
    Helpers::json(['success' => false, 'message' => 'Unknown action']);
}

$users = User::all();
$restaurants = Restaurant::all();

Helpers::json([
    'users' => $users,
    'restaurants' => $restaurants
]);