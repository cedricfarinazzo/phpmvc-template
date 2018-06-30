<?php
global $CustomRoutes;
$CustomRoutes = array();
$CustomRoutes[] = new View('Home', 'layout');
$CustomRoutes[] = new View('Git-sync', 'empty');
$CustomRoutes[] = new View('sitemap.xml', 'sitemap');
$CustomRoutes[] = new View('401');
$CustomRoutes[] = new View('404');
$CustomRoutes[] = new View('500');