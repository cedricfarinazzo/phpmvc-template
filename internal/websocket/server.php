<?php
require dirname(realpath(__FILE__), 3).'/config/config.php';
require ROOT_PATH.'/config/recaptchaConfig.php';
require ROOT_PATH.'/config/dbKey.php';
require ROOT_PATH.'/config/project.php';

require ROOT_PATH . '/class/Factory.php';
Factory::autoload_starter();
$db = PDOFactory::getMysqlConnexion($db_name, $host_db, $login_db, $password_db);
global $langmanager;
$langmanager = new LangManager();
Factory::error_handler($db);

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require ROOT_PATH . '/vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebsocketTemplate()
        )
    ),
    8080
);

$server->run();