<?php
require_once __DIR__ . '/controllers/ProfileController.php';
require_once __DIR__ . '/config.php';

session_start();


if (!isset($_SESSION[USER_SESSION_KEY])) {
    header('Location: ' . PREFIX . '/login.php');
    exit();
}


$controller = new ProfileController();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? null;
    $last_name = $_POST['last_name'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($email && $password) {
        $controller->update($first_name, $last_name, $email, $password);
    } else {
        echo "Email and password are required.";
    }
} else {
    $controller->index();
}
