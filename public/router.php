<?php
$request = $_SERVER["REQUEST_URI"];
if (strlen($request) > 0) {
    $request = substr($request, 1);
}

$request = explode('?', $request)[0];

if (strlen($request) > 0) {
    while ($request[strlen($request) - 1] == '/') {
        $request = substr($request, 0, strlen($request) - 1);
    }

}

require dirname(realpath(__FILE__), 2) . '/config/init.php';

$router = new Router();
$router->init();
$router->SetRoutes($CustomRoutes);

$router->GetPage(!empty($request) && $request != "/" ? $request : '');