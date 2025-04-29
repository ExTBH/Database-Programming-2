<?php

require_once __DIR__ . '/config.php';

session_start();
session_destroy(); // Destroy the session
header('Location: ' . PREFIX . '/index.php'); // Redirect to the homepage