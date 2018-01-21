<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
route.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

//Raccourcis
$config['route'] = array('redirect' => array());
$route = & $config['route'];
$redirect = & $config['route']['redirect'];

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
 * Empeche aussi de mettre les assets en cache
 * Fonctionne uniquement si routage_asset est activé dans fraquicom.json
 */
$route['asset_security'] = false;

/*
 * Permet de redirigé des pages ou des ensemble de page
 * $redirect['hello_world'] = 'test/hello' Redirige site.com/hello_world (ou site.com/hello_world/) sur l'url site.com/test/hello
 * $redirect['hello_world/'] = 'test/hello' Redirige site.com/hello_world (ou site.com/hello_world/) sur l'url site.com/test/hello
 * $redirect['hello_world/*'] = 'test/hello' Redirige toutes le pages commençant par site.com/hello_world/ sur site.com/test/hello
 * $redirect['hello_world'] = '404' Redirige site.com/hello_world sur la page d'erreur 404
 * $redirect['hello_world'] = '403' Redirige site.com/hello_world sur la page d'erreur 403
 */
//$redirect['hello_world'] = '403';

unset($route);
unset($redirect);