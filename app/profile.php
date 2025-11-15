<?php
require_once __DIR__ . '/Helpers.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Booking.php';

if (!Helpers::isLogged()) {
    Helpers::json(['user' => null]);
}

$user = User::findById(Helpers::userId());
$bookings = Booking::forUser(Helpers::userId());

Helpers::json(['user' => $user, 'bookings' => $bookings]);
