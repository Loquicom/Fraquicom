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

/* --- Declaration variable globale --- */
global $config; //Config application
global $_S; //Session
global $fc; //Instance du Fraquicom
global $logger; //Instance du logger
global $error; //Intance du gestionnaire d'erreur

/* --- Chargement Core --- */
require SYSTEM . 'Core.php';
$core = Core::create();

/* --- Setup --- */
if ($core->need_setup()) {
    $core->setup();
    echo 'test';
}

/* --- Initialisation --- */
$core->ini();