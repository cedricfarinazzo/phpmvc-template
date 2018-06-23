<?php

class Factory
{
    public static function autoload_starter()
    {
        require ROOT_PATH . '/class/Autoloader.php';
        Autoloader::register();
    }

    public static function error_handler($db)
    {
        $o = new ErrorHandler;
        $o->attach(new BDDWriter($db));
        set_error_handler([$o, 'error']);
    }

    public static function Session_init($db)
    {
        $session = new MySessionHandler($db);
        ini_set('session.save_handler', 'users');
        session_set_save_handler(array($session, 'open'),
            array($session, 'close'),
            array($session, 'read'),
            array($session, 'write'),
            array($session, 'destroy'),
            array($session, 'gc'),
            array($session, 'create_sid'));
        register_shutdown_function('session_write_close');
    }

    public static function getReCaptcha()
    {
        global $reCaptcha_keypublic;
        global $reCaptcha_keyprivate;
        return new reCaptcha(array('publicKey' => $reCaptcha_keypublic, 'privateKey' => $reCaptcha_keyprivate));
    }
}