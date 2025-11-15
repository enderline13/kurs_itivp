<?php

$file = $_GET['file'] ?? null;
if (!$file) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid API request']);
    exit;
}

$baseDir = __DIR__ . '/../app/';

$allowed = [
    'auth', 
    'booking', 
    'profile', 
    'restaurants', 
    'tables',
    'admin',
    'owner',
    'restaurant_manage',
    'table_manage'
];

if (in_array($file, $allowed)) {
    $path = $baseDir . $file . '.php';

    if (file_exists($path)) {
        require $path;
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
    }
} else {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
}