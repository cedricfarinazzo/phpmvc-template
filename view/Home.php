<?php
ob_start();
// PHP REQUESTS
$test = "accueil";

$view_requests = ob_get_contents();
ob_clean();

ob_start();
// HTML DISPLAY
?>

    <h1>Home</h1>
    <p><?= $test ?></p>

<?php
$view_html = ob_get_contents();
ob_clean();
?>