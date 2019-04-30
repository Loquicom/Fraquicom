<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Loader.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class FC_Loader {

    /**
     * Liste des noms de variables qu'il est interdit d'utiliser
     * @var array
     */
    protected static $forbidden = [
        'config', 
        'fc', 
        'fraquicom', 
        'logger', 
        'error', 
        'router', 
        'loader', 
        'acl', 
        '_S', 
        '_SESSION', 
        '_SERVER', 
        '_POST',
        '_GET',
        '_REQUEST',
        '_COOKIE'
    ];

    /**
     * Instance du Loader pour le singleton
     * @var FC_Loader
     */
    protected static $instance = null;

    /**
     * Liste des alias sous la forme [nom concret => [tableau des alias]]
     * @var array
     */
    protected $alias = [
        'database' => ['db']
    ];

    /* === Instanciation === */

    public static function get_instance() {
        if(static::$instance === null) {
            static::$instance = new FC_Loader();
        }
        return static::$instance;
    }

}