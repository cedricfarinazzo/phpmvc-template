<?php
ob_start();
// PHP REQUESTS



$view_requests = ob_get_contents();
ob_clean();
echo $view_requests;
ob_start();
// HTML DISPLAY
?>

            

<?php
$view_html = ob_get_contents();
ob_clean();
?>