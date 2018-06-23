<?php

require dirname(realpath(__FILE__), 2) . '/config/init.php';

$router = new Router();
$router->init();

$router->GetPage(isset($_GET["page"]) ? $_GET["page"] : '');