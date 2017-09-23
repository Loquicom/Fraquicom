<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  ip.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

if (!function_exists('get_ip')) {


    /**
     * Donne l'ip de l'utilisateur actuel
     * @param string $method - Façon de récupérer l'ip (env ou server)
     * @return false|string - L'ip
     */
    function get_ip($method = 'env') {

        //Utilise les variables d'environnement
        if ($method == 'env') {
            $ip = ($ip = getenv('HTTP_FORWARDED_FOR')) ? $ip :
                    ($ip = getenv('HTTP_X_FORWARDED_FOR')) ? $ip :
                    ($ip = getenv('HTTP_X_COMING_FROM')) ? $ip :
                    ($ip = getenv('HTTP_VIA')) ? $ip :
                    ($ip = getenv('HTTP_XROXY_CONNECTION')) ? $ip :
                    ($ip = getenv('HTTP_CLIENT_IP')) ? $ip :
                    ($ip = getenv('REMOTE_ADDR')) ? $ip :
                    false;
            return $ip;
        }
        //Utilise la variable $_SERVER
        else if ($method == 'server') {
            return (isset($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : false;
        }

        return false;
    }

}