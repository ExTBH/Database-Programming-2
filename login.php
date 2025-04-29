<?php
require_once __DIR__ . '/controllers/LoginController.php';

$controller = new LoginController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($email && $password) {
        $controller->login($email, $password);
    } else {
        echo "Email and password are required.";
    }
} else {
    $controller->index();
}
