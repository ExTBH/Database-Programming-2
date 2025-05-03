<?php

require_once 'config.php';
require_once 'controllers/AdminController.php';

session_start();

// Initialize controller
$controller = new AdminController();

// Handle the request
$controller->index();
