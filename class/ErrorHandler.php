<?php

class ErrorHandler implements SplSubject
{
    protected $observers = [];
    protected $formatedError;

    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
        return $this;
    }

    public function detach(SplObserver $observer)
    {
        if (is_int($key = array_search($observer, $this->observers, true))) {
            unset($this->observers[$key]);
        }
    }

    public function getFormatedError()
    {
        return $this->formatedError . '  at ' . time();
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function error($errno, $errstr, $errfile, $errline)
    {
        global $production;
        $this->formatedError = '[' . $errno . '] ' . $errstr . "\n" . 'Fichier : ' . $errfile . ' (ligne ' . $errline . ')';
        $this->notify();
        if (!$production) {
            echo '<br/><pre style="border:1px solid;"><br/><b>Erreur</b><br/>' . $this->getFormatedError() . '</pre><br/>';
        }
    }
}