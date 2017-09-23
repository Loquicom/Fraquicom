<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  debug.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');


if (!function_exists('var_dump_extend')) {

    /**
     * Augmente l'affichage du var_dump au maximum possible
     * @param mixed $data - Données à afficher
     */
    function var_dump_extend($data = null) {
        ini_set('xdebug.var_display_max_depth', -1);
        ini_set('xdebug.var_display_max_children', -1);
        ini_set('xdebug.var_display_max_data', -1);
        if ($data !== null) {
            var_dump($data);
        }
    }

}

if (!function_exists('show_php_error')) {

    /**
     * Affiche toutes les erreurs php
     */
    function show_php_error() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

}

if (!function_exists('mouchard')) {

    /**
     * Envoie un email avec les infos de la page courrante et une variable
     * @global mixed $config
     * @global mixed $_S - La session
     * @param mixed $var - La variable
     */
    function mouchard($var) {
        global $config, $_S;

        if (is_array($var) || is_object($var)) {
            $var = print_r($var, true);
        }
        mail(implode(',', $config["email"]), '[' . $config['appli_name'] . '] Mouchard du ' . date('d/m/Y H\hi'), utf8_encode("MOUCHARD :\n\n" . $var . "\n\nSESSION :\n\n" . print_r($_S, true) . "\n\nREQUEST :\n\n" . print_r($_REQUEST, true) . "\n\nGET :\n\n" . print_r($_GET, true) . "\n\nPOST :\n\n" . print_r($_POST, true) . "\n\nSERVER :\n\n" . print_r($_SERVER, true)), 'From:debug@fraquicom.php');
    }

}