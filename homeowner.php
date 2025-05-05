<?php

require_once 'config.php';
require_once 'controllers/HomeOwnerController.php';
require_once 'models/User.php';


session_start();

// Initialize controller
$controller = new HomeOwnerController();

$controller->index();
