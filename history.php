<?php
require_once __DIR__ . '/controllers/HistoryController.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/config.php';
session_start();


if (!isset($_SESSION[USER_SESSION_KEY]) || $_SESSION[USER_SESSION_KEY]->role !== UserRole::USER->value) {
    header('Location: ' . PREFIX . '/login.php');
    exit();
}


$controller = new HistoryController();
$controller->index();
