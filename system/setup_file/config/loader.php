<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
loader.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

//Raccourcis
$config['loader'] = array();
$loader = & $config['loader'];

/*
 * Indique si on charge par defaut tous les elements d'une catégorie
 */
$loader['all'] = array(
    'config' => false,
    'helper' => false,
    'library' => false,
    'class' => false
);

/*
 * Indique quel fichier de config doivent etre chargé en permanence
 * Les fichier config.php, database.php, loader.php, acl.php et route.php sont 
 * toujours chargé
 */
$loader['config'] = array();

/*
 * Indique quel fichier d'helper doivent etre chargé en permanence
 */
$loader['helper'] = array('url');

/*
 * Indique quelle bibliothéque doivent etre chargé en permanence
 */
$loader['library'] = array('Session', 'Database');

/*
 * Indique quelle class doivent etre chargé en permanence
 * Uniquement utile dans le mode no_mvc
 */
$loader['class'] = array();

/*
 *  Indique si il faut charger l'autoload de composer
 */
$loader['composer'] = false;

unset($loader);