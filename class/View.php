<?php

class View
{
    public $view = '';
    public $layout = 'layout';

    public function __construct($view, $layout = 'layout')
    {
        $this->view = $view;
    }

    public function IncludeView()
    {
        require ROOT_PATH . '/view/' . $this->view . '.php';
        echo $view_requets;
        require ROOT_PATH . '/_shared/' . $this->layout . '.php';
    }
}