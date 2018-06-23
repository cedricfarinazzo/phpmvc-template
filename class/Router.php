<?php
/**
 * Created by PhpStorm.
 * User: lecodeur
 * Date: 6/23/18
 * Time: 4:30 PM
 */

class Router
{
    private $_routes;
    private $_default;

    public function __construct()
    {
        $this->_routes = array();
        $this->_default = new View('Home');
    }

    public function init()
    {
        $this->_routes[] = new View('Home', 'layout');
    }

    public function GetPage($key)
    {
        $found = false;
        foreach ($this->_routes as $val) {
            if ($val->view == $key) {
                $found = true;
                $val->IncludeView();
            }
        }
        if (!$found) {
            $this->_default->IncludeView();
        }
    }

    public function GetRoutes()
    {
        return $this->_routes;
    }
}