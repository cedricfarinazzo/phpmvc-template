<?php
ob_start();
// PHP REQUESTS

header('Content-Type: text/xml');
$main_url = $_SERVER["HTTP_HOST"];
$main_url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443 ?
        'https' :
        'http').
    '://'.$main_url.'/';

require ROOT_PATH.'/config/routesConfig.php';

$datenow = new DateTime('now');

$view_requests = ob_get_contents();
ob_clean();
echo $view_requests;
ob_start();
// HTML DISPLAY
?>

<?php foreach ($CustomRoutes as $v) { ?>
<url>
    <loc><?= $main_url.$v->view ?></loc>
    <lastmod><?= $datenow->format('Y-m-d'); ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>1</priority>
</url>
<?php } ?>

<?php
$view_html = ob_get_contents();
ob_clean();
?>