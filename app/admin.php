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
    
    if ($action === 'delete_user') {
        if (!$id) Helpers::json(['success' => false, 'message' => 'ID required']);
        $ok = User::delete($id);
        Helpers::json(['success' => $ok]);
    }

    if ($action === 'delete_restaurant') {
        if (!$id) Helpers::json(['success' => false, 'message' => 'ID required']);
        $ok = Restaurant::delete($id);
        Helpers::json(['success' => $ok]);
    }
    
    if ($action === 'add_user') {
        Helpers::requirePostFields(['full_name', 'email', 'password', 'role']);
        
        $db = DB::get();
        $stmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
        $stmt->bind_param("s", $_POST['role']);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if (!$r) Helpers::json(['success' => false, 'message' => 'Неверная роль']);
        
        $roleId = intval($r['id']);
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $ok = User::create($roleId, $_POST['full_name'], $_POST['email'], $hash, null);
        Helpers::json(['success' => $ok]);
    }
    
    if ($action === 'change_owner') {
        $ownerId = intval($_POST['owner_id'] ?? 0);
        if (!$id || !$ownerId) Helpers::json(['success' => false, 'message' => 'ID required']);
        
        $ok = Restaurant::updateOwner($id, $ownerId);
        Helpers::json(['success' => $ok]);
    }
    
    Helpers::json(['success' => false, 'message' => 'Unknown POST action']);
}

$action = $_GET['action'] ?? null; 

if ($action === 'get_owners') {
    $owners = User::findByRole('owner');
    Helpers::json($owners);
} elseif ($action === 'get_data') {
    $users = User::all();
    $restaurants = Restaurant::all();

    Helpers::json([
        'users' => $users,
        'restaurants' => $restaurants
    ]);
} else {
    Helpers::json(['success' => false, 'message' => 'Неизвестное GET действие.']);
}