<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Router.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class FC_Router {

    /**
     * L'instance de Router
     * @var FC_Router
     */
    private static $instance = null;

    private function __construct() {
        //Constructeur privé pour le singleton
    }

    public static function get_instance() {
        if(self::$instance === null) {
            self::$instance = new FC_Router();
        }
        return self::$instance;
    }

}