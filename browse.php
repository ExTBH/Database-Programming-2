<?php
require_once __DIR__ . '/controllers/BrowseController.php';
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION[USER_SESSION_KEY])) {
    header('Location: ' . PREFIX . '/login.php');
    exit();
}


$controller = new BrowseController();

if (
    isset($_GET['search']) ||
    isset($_GET['min_price']) ||
    isset($_GET['max_price']) ||
    isset($_GET['availability'])
) {
    $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
    $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
    $controller->search(
        $_GET['search'],
        $min_price,
        $max_price,
        $_GET['availability'] ?? null
    );
    exit();
}

$controller->index();
