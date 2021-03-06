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
        $this->_default = NULL;
    }

    public function init()
    {
        $this->_routes[] = new View('Home', 'layout');
        $this->_default = new View('Home');
    }

    public function SetRoutes($data)
    {
        $this->_routes = $data;
        $this->_default = $this->_routes[0];
    }

    public function GetPage($key)
    {
        if ($key == '') {
            return $this->_default->IncludeView();
        }
        $found = false;
        foreach ($this->_routes as $val) {
            if (strtolower($val->view) == strtolower($key) && file_exists(ROOT_PATH . '/view/' . $val->view . '.php')) {
                $found = true;
                $val->IncludeView();
            }
        }
        if (!$found) {
            $found404 = false;
            foreach ($this->_routes as $val) {
                if ($val->view == '404') {
                    $found404 = true;
                    $val->IncludeView();
                }
            }
            if (!$found404) {
                $this->_default->IncludeView();
            }
        }
    }

    public function GetRoutes()
    {
        return $this->_routes;
    }
}