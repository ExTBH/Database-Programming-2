<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// for local server use this to tunnel the db: `ssh -N -L 3306:localhost:3306 u202203102@108.142.248.238`
// define('PREFIX', '/~u202203102/Project'); // for server
define('PREFIX', ''); // for mac or local server


define('DB_HOST', '127.0.0.1'); // for mac or local server
// define('DB_HOST', 'localhost'); // for server


define('DB_NAME', 'db202203102');
define('DB_USER', 'u202203102');
define('DB_PASS', 'u202203102');


define('USER_SESSION_KEY', 'USER');
