<?php
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/BookingController.php';
require_once __DIR__ . '/config.php';

session_start();

if (!isset($_SESSION[USER_SESSION_KEY])) {
    header('Location: ' . PREFIX . '/login.php');
    exit();
}

$action = $_GET['action'] ?? 'index';
$controller = new BookingController();

if ($action === 'get_bookings') {
    $controller->getBookings();
} else {
    $controller->index();
}
