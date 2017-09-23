<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  _ini.php
  ============================================================================ */

//Définition constante
define('FC_INI', true);

//Chargement class loader
require './system/class/Loader.php';

//Chargement des fichiers de ocnfig utilisateurs
try {
    require './application/config/config.php';
    require './application/config/database.php';
    require './application/config/loader.php';
    require './application/config/route.php';
} catch (Exception $ex) {
    throw new FraquicomException('Impossible de charger les fichiers de config : ' . $ex->getMessage());
}

//Adaptation du niveau d'erreur
if ($config['debug']) {
    error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    //ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
} else {
    error_reporting(0);
    //ini_set('error_reporting', 0);
}

//Démarrage de la session
if (trim(session_id()) === '') {
    session_start();
    if (trim($config['session']) != '') {
        if (!isset($_SESSION[$config['session']])) {
            $_SESSION[$config['session']] = array();
        }
        $_S = & $_SESSION[$config['session']];
    } else {
        //Création d'un raccourci pour la session
        $_S = & $_SESSION;
    }
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

//Chargement des class Fraquicom
if ($_config['mode'] == 'mvc') {
    try {
        require './system/fc_class/Fraquicom.php';
        require './system/fc_class/FC_Controller.php';
        //require './system/fc_class/FC_Model.php';
    } catch (Exception $ex) {
        throw new FraquicomException('Impossible de charger les class Fraquicom : ' . $ex->getMessage());
    }
} else {
    try {
        require './system/fc_class/Fraquicom.php';
        //require './system/fc_class/Fc_Object.php';
    } catch (Exception $ex) {
        throw new FraquicomException('Impossible de charger les class Fraquicom : ' . $ex->getMessage());
    }
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
