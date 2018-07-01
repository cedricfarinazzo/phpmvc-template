<?php

class View
{
    public $view = '';
    public $layout = 'layout';

    public function __construct($view, $layout = 'layout')
    {
        $this->view = $view;
        $this->layout = $layout;
    }

    public function IncludeView()
    {
        global $langmanager;
        global $connected;
        require ROOT_PATH . '/view/' . $this->view . '.php';
        global $view_title;
        $view_title = $this->view;
        require ROOT_PATH . '/_shared/' . $this->layout . '.php';
    }
}