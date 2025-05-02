<?php

require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/controllers/HomeController.php';

session_start();



$controller = new HomeController();
$controller->index();
