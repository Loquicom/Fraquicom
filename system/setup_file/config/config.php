<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
config.php.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

/*
 * Quand debug est activé toutes les erreurs peuvent être affichées, sinon en 
 * cas d'erreur le site ne charge pas (Erreur 503)
 */
$config['debug'] = true;

/*
 * Affiche ou non les erreurs 
 */
$config['show_error'] = true;

/*
 * La liste des emails des developpeurs
 */
$config['email'] = array();

/*
 * La version de l'application
 */
$config['version'] = '1.0.0';

/*
 * Le nom de la session
 * Vide pour ne pas utiliser
 */
$config['session'] = 'Fraquicom';

/*
 * Le nom de l'appli
 * Obligatoire
 */
$config['appli_name'] = '%APPLI%';

/**
 * Indique si le site est en maintenance
 * Si c'est la cas bloque l'accès
 */
$config['maintenance'] = false;