<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controllers/RentController.php';

session_start();

if (!isset($_SESSION[USER_SESSION_KEY])) {
    header('Location: login.php');
    exit;
}

$controller = new RentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $charge_point_id = $_POST['charge_point_id'] ?? null;
    $date = $_POST['date'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;

    $controller->add($charge_point_id, $date, $start_time, $end_time);
} else {
    $charge_point_id = $_GET['charger'] ?? null;
    $controller->index($charge_point_id);
}
