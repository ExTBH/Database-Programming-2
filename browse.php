<?php
require_once __DIR__ . '/controllers/BrowseController.php';
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION[USER_SESSION_KEY])) {
    header('Location: ' . PREFIX . '/login.php');
    exit();
}


$controller = new BrowseController();

// if searching return json
if (isset($_GET['search'])) {
    $controller->search($_GET['search']);
    exit();
}

$controller->index();
