<?php

require_once 'config.php';
require_once 'controllers/AdminController.php';
require_once 'models/User.php';


session_start();

// Initialize controller
$controller = new AdminController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if each POST variable is set and sanitize it
    $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : null;
    $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;
    $role = isset($_POST['role']) ? htmlspecialchars($_POST['role']) : null;
    $userRole = UserRole::from($role); // Converts to UserRole enum

    // if fields are present...
    if ($first_name && $last_name && $email && $password && $role) {
        User::manageUser("add", null,  $first_name, $last_name, $email, $userRole, $password);
        $controller->index();
    } else {
        echo "All fields are required.";
    }
} else {
    $controller->index();
}
