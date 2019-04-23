<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Fraquicom.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

/* --- Verification du droit d'écriture */
if (!is_writable('./')) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'The application environment is not set correctly.';
    exit(1);
}

/* --- Chargement Core --- */
require SYSTEM . 'Core.php';
$core = Core::create();

/* --- Setup --- */
if ($core->need_setup()) {
    $core->setup();
}

/* --- Initialisation --- */
$core->ini();
$core->load();

/* --- Declaration et initialisation variable globale --- */
global $config; //Config application
global $fc; //Instance du Fraquicom
global $logger; //Instance du logger
global $error; //Intance du gestionnaire d'erreur
global $router; //Intance du gestionnaire de routage
global $loader; //Instance du gestionnaire de chargement des fichiers
global $acl; //Instance du gestionnaire d'accès
global $_S; //Session

$config = $core->get_config();
$fc = $core->get_fraquicom();
$logger = $core->get_logger();
$error = $core->get_error();
$router = $core->get_router();
$loader = $core->get_loader();
$acl = $core->get_acl();
