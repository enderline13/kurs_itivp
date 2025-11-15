<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/Restaurant.php';
require_once __DIR__ . '/models/TableModel.php';

header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? null;
$city = $_GET['city'] ?? null;
$seats = $_GET['seats'] ?? null;

if ($id) {
    $r = Restaurant::find(intval($id));
    if (!$r) {
        Helpers::json(['success' => false, 'message' => 'Restaurant not found']);
    }
    $r['tables'] = TableModel::byRestaurant($r['id']);
    Helpers::json($r);
} else {
    $filters = [];
    if ($city) $filters['city'] = $city;
    if ($seats) $filters['min_seats'] = intval($seats);
    $list = Restaurant::all($filters);
    Helpers::json($list);
}
