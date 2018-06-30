<html><html>
<head>
    <meta charset="UTF-8">
    <meta name=”viewport” content=”width=device-width, initial-scale=1″ />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="<?= PROJECT_DESCRIPTION; ?>" />
    <title><?= isset($view_title) ? $view_title.' | ' : ''; ?><?= PROJECT_NAME; ?></title>
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
    <link rel="icon" type="image/x-icon" href="/assets/medias/images/favicon.ico" />
</head>
<body>
    <header>
        <?php require 'partial/head.php'; ?>
    </header>
    <br>
    <br>
    <section>
        <?= $view_html; ?>
    </section>
    <br>
    <br>
    <footer>
        <?php require 'partial/foot.php'; ?>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
    <script src="/assets/js/script.js" ></script>
</body>
</html>