<?php

require 'config.php';
require 'recaptchaConfig.php';
require 'dbKey.php';

if ($_SERVER["SERVER_NAME"] == "localhost" OR $_SERVER["SERVER_NAME"] == "0.0.0.0" OR $_SERVER["SERVER_NAME"] == "127.0.0.1") {
    $production = false;
} else {
    $production = true;
}

error_reporting(0);
if (!$production) {
    error_reporting(E_ALL);
}


require ROOT_PATH . '/class/Factory.php';
Factory::autoload_starter();
$db = PDOFactory::getMysqlConnexion($db_name, $host_db, $login_db, $password_db);
Factory::error_handler($db);

require 'routesConfig.php';

session_start();
//user connect