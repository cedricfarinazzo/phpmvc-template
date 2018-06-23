<?php

class VisitedManager
{

    protected $need = false;

    public function __construct()
    {
        $this->need = !isset($_COOKIE["visited"]);
        if ($this->need) {
            $this->visite();
        }
    }

    private function visite()
    {
        setcookie('visited', 1, time() + (60 * 60 * 24 * 365), '/', null, false, true);
    }

    public function needMessage()
    {
        return $this->need;
    }

}