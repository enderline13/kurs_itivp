<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/User.php';

$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'login')) {
    Helpers::requirePostFields(['email', 'password']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $user = User::findByEmail($email);
    if (!$user) {
        Helpers::json(['success' => false, 'message' => 'Пользователь не найден']);
    }
    if (!password_verify($password, $user['password'])) {
        Helpers::json(['success' => false, 'message' => 'Неверный пароль']);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role_name'] ?? null;

    $redirect = '/profile.php';
    if ($_SESSION['role'] === 'admin') $redirect = '/admin_dashboard.php';
    if ($_SESSION['role'] === 'owner') $redirect = '/owner_dashboard.php';

    Helpers::json(['success' => true, 'redirect' => $redirect]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'register')) {
    Helpers::requirePostFields(['full_name', 'email', 'password', 'role']);
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = $_POST['phone'] ?? null;
    $roleName = $_POST['role'];

    $db = DB::get();
    
    $stmt = $db->prepare("SELECT id FROM roles WHERE name = ?");
    $stmt->bind_param("s", $roleName);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if (!$r) Helpers::json(['success' => false, 'message' => 'Неверная роль пользователя']);

    $roleId = intval($r['id']);

    if (User::findByEmail($email)) {
        Helpers::json(['success' => false, 'message' => 'Email уже занят']);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $ok = User::create($roleId, $fullName, $email, $hash, $phone);
    if ($ok) {
        Helpers::json(['success' => true, 'redirect' => '/login.php']);
    } else {
        Helpers::json(['success' => false, 'message' => 'Не удалось создать пользователя']);
    }
}

if ($action === 'logout') {
    session_destroy();
    Helpers::json(['success' => true]);
}

if ($action === 'check') {
    if (Helpers::isLogged()) {
        Helpers::json([
            'loggedIn' => true,
            'user_id' => Helpers::userId(),
            'role' => Helpers::userRole()
        ]);
    } else {
        Helpers::json(['loggedIn' => false]);
    }
}

Helpers::json(['success' => false, 'message' => 'Unknown action']);