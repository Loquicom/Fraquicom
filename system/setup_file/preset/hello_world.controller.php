<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
hello_world.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

class hello_world extends FC_Controller{
    
    public function index(){
        $this->load->view('hello_world');
    }
    
}