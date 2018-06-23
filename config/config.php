<?php
date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, "fr_FR");
session_cache_expire(60);

define("ROOT_PATH", dirname(realpath(__FILE__), 2));