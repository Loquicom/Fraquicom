<?php

/**
 * ============================================================================
 * Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>
 *
 * GPL-3.0
 * index.php
 * ============================================================================
 * 
 * @author Loquicom <contact@loquicom.fr>
 * @link loquicom.fr
 */
/*
 * ----------------------------------------------------------------------------
 *  Modication possible
 * ---------------------------------------------------------------------------- 
 */

/* --- Environnement --- */
$default_env = 'development';
define('ENVIRONMENT', isset($_SERVER['FC_ENV']) ? $_SERVER['FC_ENV'] : $default_env);

/* --- HTTPS --- */
$_https_only = false;

/* --- Chemin système --- */
$_system_path = 'system';

/* --- Chemin application --- */
$_application_path = 'application';

/* --- Chemin assets --- */
$_assets_path = 'assets';

/* --- Chemin absolue framework --- */
//Vide pour calcul automatique
$_absolute_path = '';

/* --- Base url --- */
//Vide pour calcul automatique
$_base_url = '';

/*
 * ----------------------------------------------------------------------------
 *  Fin modification
 * ---------------------------------------------------------------------------- 
 */

/* --- Mode CLI ou Web --- */
$_php_mode = 'web';
if (substr(php_sapi_name(), 0, 3) == 'cli') {
    $_php_mode = 'cli';
}

/* --- Environnement --- */
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;
    case 'testing':
    case 'production':
        error_reporting(0);
        ini_set('display_errors', 0);
        break;
    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

/* --- HTTPS --- */
if($_https_only && $_php_mode != 'cli'){
    if(!(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')){
        header('Location: https://' .  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        exit(1);
    }
}

/* --- Calcul chemin --- */
//Calcul du chemin absolu jusqu'au dossier source du framework
if ($_absolute_path == '') {
    $_absolute_path = __DIR__;
}
//Calcul l'url du site (si en mode web)
if ($_base_url == '' && $_php_mode == 'web') {
    $_base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
}

/* --- Constantes --- */
define('FC_INI', true);
/**
 * Dossier systeme du Fraquicom
 */
define('SYSTEM', $_system_path[strlen($_system_path) - 1] == DIRECTORY_SEPARATOR ? $_system_path : $_system_path . DIRECTORY_SEPARATOR);
/**
 * Dossier application du Fraquicom
 */
define('APPLICATION', $_application_path[strlen($_application_path) - 1] == DIRECTORY_SEPARATOR ? $_application_path : $_application_path . DIRECTORY_SEPARATOR);
/**
 * Dossier assets du Fraquicom
 */
define('ASSETS', $_assets_path[strlen($_assets_path) - 1] == DIRECTORY_SEPARATOR ? $_assets_path : $_assets_path . DIRECTORY_SEPARATOR);
/**
 * Le mode d'utilisation de PHP : web ou cli
 */
define('PHP_MODE', $_php_mode);
/**
 * Chemin vers le framework
 */
define('BASE_PATH', $_absolute_path[strlen($_absolute_path) - 1] == DIRECTORY_SEPARATOR ? $_absolute_path : $_absolute_path . DIRECTORY_SEPARATOR);
/**
 * URL du site
 */
define('BASE_URL', $_base_url);
/**
 * Nom du log system
 */
define('LOG_SYSTEM', 'Fraquicom System Log');


/* --- Lancement Fraquicom --- */
require SYSTEM . 'fraquicom.php';
