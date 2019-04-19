<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
FC_Object.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

class FC_Object extends Fraquicom{
    
    public function model($name) {
        throw new FraquicomException('Impossible d\'utiliser la methode model en mode no_mvc');
    }
    
    public function controller($name) {
        throw new FraquicomException('Impossible d\'utiliser la methode controller en mode no_mvc');
    }
    
}