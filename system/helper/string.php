<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  string.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

if (!function_exists('random_string')) {

    /**
     * Create a "Random" String
     *
     * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, md5 and sha1
     * @param	int	number of characters
     * @return	string
     */
    function random_string($len = 8, $type = 'alnum') {
        switch ($type) {
            case 'basic':
                return mt_rand();
            case 'alnum':
            case 'numeric':
            case 'nozero':
            case 'alpha':
                switch ($type) {
                    case 'alpha':
                        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum':
                        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric':
                        $pool = '0123456789';
                        break;
                    case 'nozero':
                        $pool = '123456789';
                        break;
                }
                return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
            case 'md5':
                return md5(uniqid(mt_rand()));
            case 'sha1':
                return sha1(uniqid(mt_rand(), TRUE));
        }
    }

}

if (!function_exists('get_unique_hash')) {

    /**
     * Création d'un hash unique
     * @param string $str - String à ajouter
     * @return string
     */
    function get_unique_hash($str = '') {
        return md5(uniqid(md5(mt_rand(1, 999999999)) . $str, true));
    }

}

if (!function_exists('compare_string')) {

    /**
     * Permet de retourner le pourcentage de ressemblance entre les 2 chaines passées en parametre
     * @param string $str1
     * @param string $str2
     * @return float retourne le pourcentage de correspondance
     */
    function compare_string($str1, $str2) {
        $strlen1 = strlen($str1);
        $strlen2 = strlen($str2);
        $max = max($strlen1, $strlen2);
        $splitSize = 250;
        if ($max > $splitSize) {
            $lev = 0;
            for ($cont = 0; $cont < $max; $cont += $splitSize) {
                if ($strlen1 <= $cont || $strlen2 <= $cont) {
                    $lev = $lev / ($max / min($strlen1, $strlen2));
                    break;
                }
                $lev += levenshtein(substr($str1, $cont, $splitSize), substr($str2, $cont, $splitSize));
            }
        } else {
            $lev = levenshtein($str1, $str2);
        }
        $porcentage = -100 * $lev / $max + 100;
        if ($porcentage > 75) {
            similar_text($str1, $str2, $porcentage);
        }
        return $porcentage / 100;
    }

}

if (!function_exists('str_trunc')) {

    /**
     * Permet de tronquer une chaine de caractères
     * @param string $string Chaine à tronquer
     * @param int $longueur Longueur max de la chaine
     * @return string Retourne la chaine tronquée si besoin
     */
    function str_trunc($string, $longueur = 300) {
        if ($longueur <= 0)
            return $string;
        if (strlen($string) > $longueur) {
            return mb_substr($string, 0, ($longueur - 3)) . '...';
        }
        return $string;
    }

}

if (!function_exists('first_letter_to_upper')) {

    /**
     * Permet de passer la première lettre de chaque mot de la chaine en capitale
     * @static
     * @param string $string Chaine de caractère
     * @return string Retourne la chaine une majuscule au début de chaque mot
     */
    function first_letter_to_upper($string) {
        $myString = '';
        $tab = explode(' ', $string);
        if (count($tab)) {
            foreach ($tab as $key => $val) {
                if (strlen($myString)) {
                    $myString .= ' ';
                }
                $myString .= ucfirst(strtolower($val));
            }
        }
        return $myString;
    }

}

if (!function_exists('increment_string')) {

    /**
     * Add's _1 to a string or increment the ending number to allow _2, _3, etc
     *
     * @param	string	required
     * @param	string	What should the duplicate number be appended with
     * @param	string	Which number should be used for the first dupe increment
     * @return	string
     */
    function increment_string($str, $separator = '_', $first = 1) {
        preg_match('/(.+)' . preg_quote($separator, '/') . '([0-9]+)$/', $str, $match);
        return isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $str . $separator . $first;
    }

}

if(!function_exists('protect_string')){
    
    /**
     * Protege un string
     * @param string $string - Le string
     * @param boolean $delTag - [optional] Supprime ou non les balises (defaut non)
     * @return string
     */
    function protect_string($string, $delTag = false){
        //Si on supprime les balises
        if($delTag){
            return strip_tags($string);
        } else {
            return htmlentities($string, ENT_QUOTES);
        }
    }
    
}

if(!function_exists('unprotect_string')){
    
    /**
     * Retire la protection d'un strig
     * @param string $string - Le string
     * @return string
     */
    function unprotect_string($string){
        return html_entity_decode($string, ENT_QUOTES);
    }
    
}

if(!function_exists('url_safe_encode')){
    
    /**
     * Encode un string pour pouvoir l'utiliser dans une url
     * @return string
     */
    function url_safe_encode(){
       $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', '.'), $data);
        return $data; 
    }
    
}

if(!function_exists('url_safe_decode')){
    
    /**
     * Decode un string encoder par url_safe_encode
     * @return string
     */
    function url_safe_decode($string){
        $data = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
    
}

if(!function_exists('crypte')){
    
    /**
     * Chiffre un string
     * @param string $string - Le string
     * @param string $clef - La clef pour chiffrer (longueur 16 ou 32)
     * @param type $methode - le Cipher
     * @param type $mode - Le mode
     * @return string
     */
    function crypte($string, $clef, $methode = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
        //Calcul iv
        $iv_size = mcrypt_get_iv_size($methode, $mode);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        //Crypte
        $data = $iv . mcrypt_encrypt($methode, $clef, $string, $mode, $iv);
        return url_safe_encode($data);
    }
    
}

if(!function_exists('decrypte')){
    
    /**
     * Dechiffre un string
     * @param string $string - Le string
     * @param string $clef - La clef pour chiffrer (longueur 16 ou 32)
     * @param type $methode - le Cipher
     * @param type $mode - Le mode
     * @return string
     */
    function decrypte($string, $clef, $methode = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
        $tmp = $this->urlsafe_b64decode($string);
        if (strlen($tmp) < 16) {
            return false;
        }
        $iv = substr($tmp, 0, 16);
        $data = substr($tmp, 16);
        return rtrim(mcrypt_decrypt($methode, $clef, $data, $mode, $iv), "\0");
    }
    
}