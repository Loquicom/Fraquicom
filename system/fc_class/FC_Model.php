<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
FC_CModel.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

Class FC_Model extends Fraquicom{
    
    public function object($name) {
        throw new FraquicomException('Impossible d\'utiliser la methode object en mode mvc');
    }
    
    public function objects_to_attribute() {
        throw new FraquicomException('Impossible d\'utiliser la methode objects_to_attribute en mode mvc');
    }
    
}