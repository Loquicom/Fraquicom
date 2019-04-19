<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
config.php.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

/*
 * Le nom de l'appli
 * Obligatoire
 */
$config['appli_name'] = '%APPLI%';

/*
 * Chemin vers le dossier des stockages des fichiers 
 * /!\ Chemin en absolue et fini par un /
 */
$config['data_path'] = '%DATA%';

/*
 * Chemin vers le dossier des stockages des fichiers termporaires
 * /!\ Chemin en absolue et fini par un /
 */
$config['tmp_path'] = '%TMP%';

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
 * Active ou desactive les acl 
 */
$config['acl'] = true;

/**
 * Si vrai on affiche une erreur 403 à l'utilisateur lorsqu'il n'a pas accès à
 * une page, sinon on le redirige sur la page d'accueil du site
 */
$config['acl_403'] = false;

/**
 * Indique si le site est en maintenance
 * Si c'est la cas bloque l'accès
 */
$config['maintenance'] = false;