<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
hello_world.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

$fc->load->file('header', array('title' => 'Hello World'));
?>

<h1>Hello World</h1>

<?php
$fc->load->file('footer');