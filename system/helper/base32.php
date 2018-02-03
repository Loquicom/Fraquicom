<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  base32.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

if(!function_exists('base32_table')){

	/**
	 * Table de codage Base 32
	 * @return mixed - La table de codage
	 */
	public function base32_table(){
		return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
            'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
            '=', // padding char
        );
	}

}

if(!function_exists('base32_encode')){

	/**
	 * Encode un string en base 32
	 * @param string $string - Le string à encoder
	 * @return string - Le string encoder
	 */
	public function base32_encode($string){
		//Recup table carac
		$validChars = base32_table();
		//Encodage
		$code = '';
		for ($i = 0; $i < strlen($string); ++$i) {
            $code .= $validChars[ord($string[$i]) & 31];
        }
        //Retour
        return $code;
	}

}

if(!function_exists('base32_decode')){

	/**
	 * Decode un string en base 32
	 * @param string $string - Le string à decoder
	 * @return string - Le string decoder
	 */
	public function base32_decode($string){
		if (empty($string)) {
            return '';
        }
        $base32chars = base32_table();
        $base32charsFlipped = array_flip($base32chars);
        $paddingCharCount = substr_count($string, $base32chars[32]);
        $allowedValues = array(6, 4, 3, 1, 0);
        if (!in_array($paddingCharCount, $allowedValues)) {
            return false;
        }
        for ($i = 0; $i < 4; ++$i) {
            if ($paddingCharCount == $allowedValues[$i] &&
                    substr($string, -($allowedValues[$i])) != str_repeat($base32chars[32], $allowedValues[$i])) {
                return false;
            }
        }
        $string = str_replace('=', '', $string);
        $string = str_split($string);
        $binaryString = '';
        for ($i = 0; $i < count($string); $i = $i + 8) {
            $x = '';
            if (!in_array($string[$i], $base32chars)) {
                return false;
            }
            for ($j = 0; $j < 8; ++$j) {
                $x .= str_pad(base_convert(@$base32charsFlipped[@$string[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for ($z = 0; $z < count($eightBits); ++$z) {
                $binaryString .= (($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48) ? $y : '';
            }
        }
        return $binaryString;
	}

}