<?php
require_once __DIR__ . '/controllers/ContactController.php';
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION[USER_SESSION_KEY])) {
    header('Location: ' . PREFIX . '/login.php');
    exit();
}

$controller = new ContactController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $controller->sendMessage($_POST);
} else {
    // check if homeowner param is empty
    if (empty($_GET['homeowner'])) {
        header('Location: ' . PREFIX . '/index.php');
        exit();
    }

    // Show contact form
    $controller->index();
}
