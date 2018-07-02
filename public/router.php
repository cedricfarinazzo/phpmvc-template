<?php
$request = $_SERVER["REQUEST_URI"];

if (file_exists(dirname(realpath(__FILE__), 2).'/public'.$request) &&
    $request != '' &&
    $request != '/' &&
    !is_dir(dirname(realpath(__FILE__), 2).'/public'.$request)) {

    $path = dirname(realpath(__FILE__), 2).'/public'.$request;
    $extension = pathinfo($path)["extension"];
    $extension = strtolower($extension);
    if (in_array($extension, array('jpg, jpeg, png', 'gif', 'svg', 'js', 'css', 'xml', 'text')))
    {
        switch ($extension)
        {
            case 'jpg':
                header("Content-Type: image/jpeg");
                break;

            case 'jpeg':
                header("Content-Type: image/jpeg");
                break;

            case 'png':
                header("Content-Type: image/png");
                break;

            case 'gif':
                header("Content-Type: image/gif");
                break;

            case 'svg':
                header("Content-Type: image/svg+xml");
                break;

            case 'js':
                header("Content-Type: application/javascript");
                break;

            case 'css':
                header("Content-Type: text/css");
                break;

            case 'xml':
                header("Content-Type: application/xml");
                break;

            default:
                header("Content-Type: text/plain");
                break;
        }
        readfile($path);
        exit();
    }
}




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