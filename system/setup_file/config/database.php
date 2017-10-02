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
 *      - sqlite (SQLite 3)
 *      - oracle
 *      - postgresql
 * 
 * Pour fonctiooner correctement assurer vous que PDO est bien parametré
 */
$database['type'] = 'mysql';

/*
 * L'hote de la base de données
 * 
 * mysql : L'hote de la base avec ou sans port (ex : localhost ou localhost:3307)
 * sqlite : Le dossier de la base avec un / (ex : db/sqlite/)
 * oracle : L'hote de la base : le port (ex : localhost:1521)
 * postgresql : L'hote de la base avec ou sans port (ex : localhost ou localhost:5432)
 */
$database['host'] = '';

/*
 * Le nom de la base
 * 
 * mysql : Le nom de la base
 * sqlite : Le nom du fichier avec l'extension
 * oracle : Le nom de la base
 * postgresql : Le nom de la base
 */
$database['name'] = '';

/*
 * Le login de la base
 * Null si aucun login
 * 
 * mysql : Le login
 * sqlite : Ne pas modifier
 * oracle : Le login
 * postgresql : Le login
 */
$database['login'] = null;

/*
 * Le mot de passe de la base
 * Null si aucun mot de passe
 * 
 * mysql : Le mot de passe
 * sqlite : Ne pas modifier
 * oracle : Le mot de passe
 * postgresql : Le mot de passe
 */
$database['pass'] = null;

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
    'login' => null,
    'pass' => null,
    'prefix' => ''
);
//*/

unset($database);
unset($otherDB);
