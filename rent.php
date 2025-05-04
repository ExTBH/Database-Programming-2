<?php
require_once __DIR__ . '/controllers/RentController.php';

session_start();

$controller = new RentController();

$controller->index();
