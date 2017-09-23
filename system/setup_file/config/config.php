<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
config.php.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

/*
 * Quand debug est activé toutes les erreurs sont affichées, sinon aucune erreur
 * ne s'affiche
 */
$config['debug'] = true;

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
 */
$config['appli_name'] = '';