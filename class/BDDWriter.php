<?php

class BDDWriter implements SplObserver
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function update(SplSubject $obj)
    {
        $q = $this->db->prepare('INSERT INTO error SET erreur = :erreur');
        $q->bindValue(':error', $obj->getFormatedError());
        $q->execute();
    }
}