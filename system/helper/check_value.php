<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  check_value.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

if (!function_exists('is_integer')) {

    /**
     * Permet de vérifier que le paramètre passé est un ENTIER (int) et non pas une chaine de caractère content un entier
     * Plus d'infos : http://php.net/manual/fr/function.is-int.php#82857
     * @param string $nb - Valeur à vérifier
     * @return boolean - True si la valeur passée est un entier (int), false sinon.
     */
    function is_integer($nb) {
        return(ctype_digit(strval($nb)));
    }

}

if(!function_exists('is_email')){
    
    /**
     * Permet de vérifier que le paramètre passé est une adresse mail au format valide
     * @static
     * @param string $email - Valeur à vérifier
     * @return boolean - True si la valeur passée est une adresse mail au bon format, false sinon
     */
    function is_email($email){
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
}

if(!function_exists('is_url')){
    
    /**
     * Permet de vérifier que le paramètre passé est au format URL
     * @static
     * @param string - $url Valeur à vérifier
     * @return boolean - True si format URL ok, false sinon
     */
    function is_url($url){
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }
    
}

if(!function_exists('is_date')){
    
    /**
     * Permet de vérifier que le paramètre passé est une date FR ou US 
     * @param string $date - Valeur à vérifier
     * @return boolean
     */
    function is_date($date){
        $date = explode('/', explode('.', explode('-', $date)));
        //Si c'est une date europeen
        if(strlen($date[0]) == 2 && count($date) == 3){
            return ($date[0] > 0 && $date[0] < 32) && ($date[1] > 0 && $date[1] < 13) && (strlen($date[2]) > 0);
        }
        //Si c'est une date us
        else if(strlen($date[0]) == 3 && count($date) == 3){
            return ($date[2] > 0 && $date[2] < 32) && ($date[1] > 0 && $date[1] < 13) && (strlen($date[0]) > 0);
        }
        //Sinon false
        return false;
    }
    
}

if(!function_exists('is_phone_number')){
    
    /**
     * Permet de verifier si la paramètre passé est un numéro de téléphone
     * @param type $number - Valeur à vérifier
     * @return boolean
     */
    function is_phone_number($number){
        return (preg_match('#^(0[1-589])(?:[ _.-]?(\d{2})){4}$#', $number) || preg_match('#^0[6-7]([-. ]?[0-9]{2}){4}$#', $number));
    }
    
}