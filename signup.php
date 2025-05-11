<?php

require_once __DIR__ . '/controllers/SignupController.php';

$controller = new SignupController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if each POST variable is set and sanitize it
    $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : null;
    $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : null;
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
    $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;
    $is_HomeOwner = isset($_POST['request_homeowner']) ? true : false;

$requestHomeowner = isset($_POST['request_homeowner']); // Check if the checkbox is checked
error_log('Request Homeowner: ' . ($requestHomeowner ? 'Yes' : 'No'));

    // if fields are present...
    if ($first_name && $last_name && $email && $password) {
        if ($is_HomeOwner == true) {
            $controller->createRequest($first_name, $last_name, $email, $password);
        } else {
            $controller->signup($first_name, $last_name, $email, $password);
        }
    } else {
        echo "All fields are required.";
    }
} else {
    $controller->index();
}
