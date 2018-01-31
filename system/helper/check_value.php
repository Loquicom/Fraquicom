<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  check_value.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

if (!function_exists('check_integer')) {

    /**
     * Permet de vérifier que le paramètre passé est un ENTIER (sous forme de 
     * int ou de string)
     * Plus d'infos : http://php.net/manual/fr/function.is-int.php#82857
     * @param string $nb - Valeur à vérifier
     * @return boolean - True si la valeur passée est un entier (int), false sinon.
     */
    function check_integer($nb) {
        return(ctype_digit(strval($nb)));
    }

}

if(!function_exists('check_float')){
    
    /**
     * Permet de vérifier que le paramètre passé est un FLOAT (sous forme de 
     * float ou de string)
     * Plus d'infos : http://php.net/manual/fr/function.is-int.php#82857
     * @param string $nb - Valeur à vérifier
     * @return boolean - True si la valeur passée est un entier (int), false sinon.
     */
    function check_float($nb){
        return is_float($nb + 0);
    }
    
}

if(!function_exists('check_double')){
    
    /**
     * Alias of check_float
     */
    function check_double($nb){
        return check_float($nb);
    }
    
}

if (!function_exists('check_email')) {

    /**
     * Permet de vérifier que le paramètre passé est une adresse mail au format valide
     * @param string $email - Valeur à vérifier
     * @return boolean - True si la valeur passée est une adresse mail au bon format, false sinon
     */
    function check_email($email) {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

}

if (!function_exists('is_email')) {

    /**
     * Alias of check_email
     */
    function is_email($email) {
        return check_email($email);
    }

}

if (!function_exists('check_url')) {

    /**
     * Permet de vérifier que le paramètre passé est au format URL
     * @param string - $url Valeur à vérifier
     * @return boolean - True si format URL ok, false sinon
     */
    function check_url($url) {
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }

}

if (!function_exists('is_url')) {

    /**
     * Alias of check_url
     */
    function is_url($url) {
        return check_url($url);
    }

}

if (!function_exists('check_date')) {

    /**
     * Permet de vérifier que le paramètre passé est une date FR ou US 
     * @param string $date - Valeur à vérifier
     * @return boolean
     */
    function check_date($date) {
        //Découpe la date
        if (strpos($date, '/') !== false) {
            $date = explode('/', $date);
        } else if (strpos($date, '.') !== false) {
            $date = explode('.', $date);
        } else if (strpos($date, '-') !== false) {
            $date = explode('-', $date);
        } else {
            return false;
        }
        //Si c'est une date europeen
        if (strlen($date[0]) == 2 && count($date) == 3) {
            return ($date[0] > 0 && $date[0] < 32) && ($date[1] > 0 && $date[1] < 13) && (strlen($date[2]) > 0);
        }
        //Si c'est une date us
        else if (strlen($date[0]) == 3 && count($date) == 3) {
            return ($date[2] > 0 && $date[2] < 32) && ($date[1] > 0 && $date[1] < 13) && (strlen($date[0]) > 0);
        }
        //Sinon false
        return false;
    }

}

if (!function_exists('is_date')) {

    /**
     * Alias of check_date
     */
    function is_date($date) {
        return check_date($date);
    }

}

if (!function_exists('check_phone_number')) {

    /**
     * Permet de verifier si la paramètre passé est un numéro de téléphone
     * @param type $number - Valeur à vérifier
     * @return boolean
     */
    function check_phone_number($number) {
        return (preg_match('#^(0[1-589])(?:[ _.-]?(\d{2})){4}$#', $number) || preg_match('#^0[6-7]([-._ ]?[0-9]{2}){4}$#', $number));
    }

}

if (!function_exists('is_phone_number')) {

    /**
     * Alias of check_phone_number
     */
    function is_phone_number($number) {
        return check_phone_number($number);
    }

}