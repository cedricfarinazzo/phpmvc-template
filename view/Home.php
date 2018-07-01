<?php
ob_start();
// PHP REQUESTS
$test = "accueil";


$view_requests = ob_get_contents();
ob_clean();
echo $view_requests;
ob_start();
// HTML DISPLAY
?>

    <h1><?= $langmanager->Data()['Home'] ?></h1>
    <p><?= $test ?></p>

<?php
$view_html = ob_get_contents();
ob_clean();
?>