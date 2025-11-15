<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/Booking.php';
require_once __DIR__ . '/models/Restaurant.php';

if (Helpers::userRole() !== 'owner') {
    Helpers::json(['success' => false, 'message' => 'Unauthorized']);
}

$ownerId = Helpers::userId();

$restaurants = Restaurant::forOwner($ownerId);
$bookings = Booking::forOwner($ownerId);

Helpers::json([
    'restaurants' => $restaurants,
    'bookings' => $bookings
]);