<?php
/**
 * Created by PhpStorm.
 * User: lecodeur
 * Date: 6/30/18
 * Time: 7:37 PM
 */

class LangManager
{
    private $_applang = "en";
    private $_appdata = array();

    private $_langavailable = array();
    private $_langdirectory = ROOT_PATH.'/src/lang/';

    public function __construct()
    {
        $data = array_slice(scandir($this->_langdirectory), 2);
        foreach ($data as $val)
        {
            $this->_langavailable[] = (explode('.lang.', $val))[0];
        }
        if (isset($_COOKIE["app_lang"]))
        {
            $this->load($_COOKIE["app_lang"]);
        }
        else
        {
            $this->load('en');
        }
    }

    public function load($lang)
    {
        if(in_array($lang, $this->_langavailable))
        {
            require $this->_langdirectory.$lang.'.lang.php';
        }
        else
        {
            require $this->_langdirectory.'en.lang.php';
        }
        $this->_appdata = $app_lang;
    }

    public function set($lang)
    {
        if (in_array($lang, $this->_langavailable)) {
            setcookie('app_lang', $lang, time() + (60 * 60 * 24 * 365), '/', null, false, true);
            $this->load($lang);
        }
    }

    public function AppData()
    {
        return $this->_appdata;
    }

    public function Data()
    {
        return $this->_appdata['data'];
    }
}