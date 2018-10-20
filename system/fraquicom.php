<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Fraquicom.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

/* --- Declaration variable globale --- */
global $config; //Config application
global $_S; //Session
global $fc; //Instance du Fraquicom

/* --- Chargement Core --- */
require SYSTEM . 'Core.php';
$core = Core::create();

/* --- Setup --- */
if($core->need_setup()){
    $core->setup();
    echo 'test';
}

/* --- Initialisation --- */
$core->ini();