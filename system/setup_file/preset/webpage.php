<?php
/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  webpage.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');
?>

<!DOCTYPE html>
<html>
    <head>
        <!--Import Google Icon Font-->
        <link type="text/css" rel="stylesheet" href="<?= assets_url('css/material-icons.css') ?>"/>
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="<?= assets_url('css/materialize.css') ?>"  media="screen,projection"/>
        <!--Import style.css-->
        <link type="text/css" rel="stylesheet" href="<?= assets_url('css/style.css') ?>"  media="screen,projection"/>
        <!--Import Other CSS-->
        <link type="text/css" rel="stylesheet" href="<?= assets_url('css/padding-margin.css') ?>"  media="screen,projection"/>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <title><?= $title ?></title>
    </head>

    <body>

        <main class="container">
            <?= $body ?>
        </main>

        <!--Import jQuery, materialize.js, and other JS-->
        <script type="text/javascript" src="<?= assets_url('js/jquery-3.1.2.min.js') ?>"></script>
        <script type="text/javascript" src="<?= assets_url('js/materialize.js') ?>"></script>
        <script type="text/javascript" src="<?= assets_url('js/message.js') ?>"></script>
    </body>
</html>
