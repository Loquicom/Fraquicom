<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
route.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

//Raccourcis
$config['route'] = array();
$route = & $config['route'];

/*
 * Page à charger par defaut
 * En mode mvc le nom du controller[/methode]
 * En mode no_mvc le nom du fichier sans le .php
 */
$route['index'] = 'hello_world';

/*
 * Page en cas d'erreur 404
 * En mode mvc le nom du controller[/methode]
 * En mode no_mvc le nom du fichier sans le .php
 */
$route['404'] = '';

/*
 * Si activé l'utilisateur obtient un lien non partageable pour accèder aux
 * assets de l'application
 */
$route['asset_security'] = false;

unset($route);