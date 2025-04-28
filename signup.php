<?php
$prefix = "/~u202203102/Project";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once __DIR__ . '/controllers/SignupController.php';

$controller = new SignupController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if each POST variable is set and sanitize it
    $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : null;
    $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;

    // if fields are present...
    if ($first_name && $last_name && $email && $password) {
        $controller->signup($first_name, $last_name, $email, $password);
    } else {
        echo "All fields are required.";
    }
} else {
    $controller->index();
}
