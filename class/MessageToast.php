<?php

class MessageToast
{

    protected $message = array("Vous êtes désormais membre du site !", "Vous êtes connecté !", "Vous êtes déconnecté !", "Nous avons renouvelé votre connexion.", 'Bienvenue sur le site du groupee ACCEr !', 'Votre compte a bien été supprimé.');
    protected $style = array("success", "success", "noerror", 'success', 'success', 'success');

    protected $id_message;

    public function __construct($id_message)
    {
        $this->id_message = $id_message;
    }

    public function GetMessage()
    {
        if (0 <= $this->id_message && $this->id_message < count($this->message)) {
            $color = $this->style[$this->id_message] == "success" ? "success green darken-2" : "error red darken-1";
            $message = $this->message[(int)$this->id_message];
            return '<span class="' . $color . '" style="padding: 7px; box-shadow: 6px 6px 0px black; border-radius: 9px;" >' . $message . '</span>';
        }
        return false;
    }
}