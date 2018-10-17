<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
FC_Controller.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

Class FC_Controller extends Fraquicom{
    
    public function object($name) {
        throw new FraquicomException('Impossible d\'utiliser la methode object en mode mvc');
    }
    
    public function index(){
        echo 'Fonction par defaut du controller';
    }
    
}