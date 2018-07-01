<?php

class reCaptcha
{
    protected $privateKey;
    protected $publicKey;
    protected $api_url = "http://www.google.com/recaptcha/api/siteverify";
    protected $response = false;

    public function __construct($data)
    {
        $this->hydrate($data);
    }

    public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getRecaptcha()
    {
        return '<div class="g-recaptcha" data-sitekey="' . $this->publicKey . '"></div>
      ';
    }

    public function verify()
    {
        if (isset($_POST['g-recaptcha-response']) AND !empty($_POST['g-recaptcha-response'])) {
            $response = $_POST['g-recaptcha-response'];
            $url = $this->api_url . '?secret=' . $this->privateKey . '&response=' . $response . '&remoteip=' . $_SERVER['REMOTE_ADDR'];
            $decode = json_decode(file_get_contents($url), true);
            $this->response = $decode['success'];
        } else {
            $this->response = false;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setPrivateKey($key)
    {
        if (is_string($key)) {
            $this->privateKey = $key;
        }
    }

    public function setPublicKey($key)
    {
        if (is_string($key)) {
            $this->publicKey = $key;
        }
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }
}