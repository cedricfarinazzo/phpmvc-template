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
        $q = $this->db->prepare('INSERT INTO erreurs SET erreur = :erreur');
        $q->bindValue(':erreur', $obj->getFormatedError());
        $q->execute();
    }
}