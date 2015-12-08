<!DOCTYPE html>
<html>
    <head>
        <title>Lietuvos skautijos pažymėjimas <?php if(isset($page_title)) echo "| " . $page_title  ?></title>
        <link href="public/assets/img/favicon.ico" rel="icon" />
        <meta charset="utf-8" />
        <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta content="ALL" name="robots" />
        <meta content="index,follow" name="GOOGLEBOT" />
        <meta content="Anketa Lietuvos Skautijos nario pažymėjimui." name="description" />
        <meta content="meniu, anketa, LS, Lietuvos Skautija, skautai, pažymėjimas" name="keywords" />
        
        <!-- Bootstrap, svarbu, kad veiktų tik atidarius -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" href="/public/assets/css/main.css" />
        <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    </head>
    <body class="<?php echo $page_body ?>">
        <div class="container">
            <?php if(isset($view_filename)) require $view_filename ?>
        </div>
    </body>
</html>
