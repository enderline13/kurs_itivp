<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/TableModel.php';

$restaurantId = $_GET['restaurant_id'] ?? null;
$id = $_GET['id'] ?? null;

if ($id) {
    $table = TableModel::find(intval($id));
    Helpers::json($table);
}

if (!$restaurantId) {
    Helpers::json(['success' => false, 'message' => 'restaurant_id required']);
}

$tables = TableModel::byRestaurant(intval($restaurantId));
Helpers::json($tables);