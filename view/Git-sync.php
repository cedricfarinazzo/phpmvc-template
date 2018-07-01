<?php
ob_start();
// PHP REQUESTS

// The commands
set_time_limit(0);
$commands = array(
    'echo $PWD',
    'whoami' ,
    'git add .',
    'git stash',
    'git reset --hard HEAD',
    'git pull',
    'git status',
    'git submodule sync',
    'git submodule update',
    'git submodule status',
    'cd .. && composer install',
    'cd .. && composer update'
);
// Run the commands for output
$output = '';
foreach($commands AS $command){
    // Run it
    $tmp = shell_exec($command);
    // Output
    $output .= "<span style=\"color: #6BE234;\">\$</span> <span style=\"color: #729FCF;\">{$command}\n</span>";
    $output .= htmlentities(trim($tmp)) . "\n";
}

$view_requests = ob_get_contents();
ob_clean();
echo $view_requests;
ob_start();
// HTML DISPLAY
?>

<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>GIT DEPLOYMENT SCRIPT</title>
    </head>
    <body style="background-color: #000000; color: #FFFFFF; font-weight: bold; padding: 0 10px;">
        <pre>
             ____________________________
            |                            |
            |   Git Deployment Script    |
            |                            |
            |____________________________|

<?php echo $output; ?>
        </pre>
    </body>
</html>

<?php
$view_html = ob_get_contents();
ob_clean();
?>