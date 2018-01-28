<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  _ini.php
  ============================================================================ */

//Chargement des fichiers de ocnfig utilisateurs
try {
    require './application/config/config.php';
    require './application/config/database.php';
    require './application/config/loader.php';
    require './application/config/route.php';
} catch (Exception $ex) {
    throw new FraquicomException('Impossible de charger les fichiers de config : ' . $ex->getMessage());
}

//Chargement des fichiers de configaration de l'utilisateur
if ($config['loader']['all']['config']) {
    //Scan des fichiers dans config
    $configFiles = array_diff(scandir('./application/config/'), array('..', '.', 'config.php', 'loader.php', 'database.php', 'route.php'));
    //Import si il y en a
    if (!empty($configFiles)) {
        foreach ($configFiles as $configFile) {
            try {
                require './application/config/' . $configFile . '.php';
            } catch (Exception $ex) {
                throw new FraquicomException('Impossible de charger le fichier de config ' . $configFile . ' : ' . $ex->getMessage());
            }
        }
    }
} else if (!empty($config['loader']['config'])) {
    foreach ($config['loader']['config'] as $configFile) {
        if (file_exists('./application/config/' . $configFile . '.php')) {
            try {
                require './application/config/' . $configFile . '.php';
            } catch (Exception $ex) {
                throw new FraquicomException('Impossible de charger le fichier de config ' . $configFile . ' : ' . $ex->getMessage());
            }
        } else {
            throw new FraquicomException('Impossible de trouver le fichier ' . $configFile . ' dans \'./application/config/' . $configFile . '.php\'');
        }
    }
}

//Adaptation du niveau d'erreur
if ($config['show_error']) {
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    //ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
} else {
    error_reporting(0);
    //ini_set('error_reporting', 0);
}

//Démarrage de la session
if (trim(session_id()) === '') {
    if (trim($config['session']) != '') {
        //Parametrage du nom de la session
        session_name($config['session']);
    }
    session_start();
    //Si l'appli à un nom création d'un sous tableau pour son usage
    if (trim($config['appli_name']) != '') {
        if (!isset($_SESSION[$config['appli_name']])) {
            $_SESSION[$config['appli_name']] = array();
        }
        $_S = & $_SESSION[$config['appli_name']];
    } else {
        //Erreur le nom de l'appli n'est pas renseigné
        exit("Aucun nom d'application, veuillez en saisir un dans le fichier config.php");
    }
}
//Création de la clef de sécurité de la session
if (!isset($_S['_fc_id'])) {
    $_S['_fc_id'] = str_replace('=', '-equ-', base64_encode(uniqid(mt_rand(0, 999999))));
}

//Chargement de la class log
require './system/class/Log.php';
//Chargement de la class error
require './system/class/Error.php';
//Chargement de la class config
require './system/class/Config.php';
//Chargement de la class loader
require './system/class/Loader.php';

//Chargement des class Fraquicom
if ($_config['mode'] == 'mvc') {
    try {
        require './system/fc_class/Fraquicom.php';
        require './system/fc_class/FC_Controller.php';
        require './system/fc_class/FC_Model.php';
    } catch (Exception $ex) {
        throw new FraquicomException('Impossible de charger les class Fraquicom : ' . $ex->getMessage());
    }
} else {
    try {
        require './system/fc_class/Fraquicom.php';
        require './system/fc_class/Fc_Object.php';
    } catch (Exception $ex) {
        throw new FraquicomException('Impossible de charger les class Fraquicom : ' . $ex->getMessage());
    }
}

//Verifie que data_path et tmp_path ne sont pas vide
if(trim($config['data_path']) == '' || trim($config['tmp_path']) == ''){
    throw new FraquicomException('Les chemins data_path et tmp_path ne sont pas renseigné');
}

//Création du dossier data et tmp si besoins
if (!(file_exists($config['data_path'] . 'log/') && file_exists($config['tmp_path']))) {
    _ini_dir($config['data_path'] . 'log/');
    _ini_dir($config['tmp_path']);
}

/* --- Fonction _ini.php --- */

function _ini_dir($path) {
    //Si le dossier n'existe pas
    if (!is_dir($path)) {
        //Tentative de création
        if (_ini_dir(dirname($path))) {
            @mkdir($path);
        }
        //Erreur lors de la creation
        else {
            return false;
        }
    }
    //Le dossier est la
    return true;
}

/* --- Fonction Fraquicom --- */

function get_instance() {
    return Fraquicom::get_instance();
}

/* --- Class Exception Fraquicom --- */

class FraquicomException extends Exception {
    
}

class LoaderException extends Exception {
    
}
