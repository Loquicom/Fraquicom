<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  database.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

//Raccourcis
$config['db'] = array('other' => array());
$database = & $config['db'];
$otherDB = & $config['db']['other'];

/*
 * Le type de base données utilisé
 * Les types supporté sont :
 *      - mysql
 */
$database['type'] = 'mysql';

/*
 * L'hote de la base de données
 */
$database['host'] = '';

/*
 * Le nom de la base
 */
$database['name'] = '';

/*
 * Le login de la base
 */
$database['login'] = '';

/*
 * Le mot de passe de la base
 */
$database['pass'] = '';

/*
 * Prefix des tables de la base
 */
$database['prefix'] = '';

/* --- Pour ajouter une autre base de données decommentez lz code suivant --- */
/*
$otherDB['db_name'] = array(
    'type' => 'mysql',
    'host' => '',
    'name' => '',
    'login' => '',
    'pass' => '',
    'prefix' => ''
);
//*/

unset($database);
unset($otherDB);
