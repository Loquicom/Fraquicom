<?php
/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>
  
  GPL-3.0
  message.php
  ============================================================================== */

defined('FC_INI') or exit('Acces Denied');

if (!function_exists('message_erreur')) {

    /**
     * Genere un message d'erreur
     * @param string $msg - Le message à afficher
     * @param boolean $echo - echo le message sinon le return, false par defaut
     */
    function message_err($msg, $echo = false) {
        $html = '';
        $html .= '<div class="hide-on-small-only">' . "\n";
        $html .= '   <div class="row red-text red lighten-4" style="height: 60px; border: dashed;">' . "\n";
        $html .= '       <div class="col s2">' . "\n";
        $html .= '           <i class="material-icons" style="font-size: 3em; line-height: 125%">error</i>' . "\n";
        $html .= '       </div>' . "\n";
        $html .= '       <div class="col s10 truncate " style="font-size: 1.5em; line-height: 250%">' . "\n";
        $html .= '           ' . $msg . "\n";
        $html .= '       </div>' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        $html .= '<div class="hide-on-med-and-up">' . "\n";
        $html .= '   <div class="red lighten-4 red-text center" style="border: dashed">' . "\n";
        $html .= '       <br>' . $msg . '<br>&nbsp;' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        
        if($echo){
            echo $html;
            exit;
        } else {
            return $html;
        }
    }

}

if (!function_exists('message_info')) {

    /**
     * Genere un message d'information
     * @param string $msg - Le message à afficher
     * @param boolean $echo - echo le message sinon le return, false par defaut
     */
    function message_info($msg, $echo = false) {
        $html = '';
        $html .= '<div class="hide-on-small-only">' . "\n";
        $html .= '   <div class="row blue-text blue lighten-4" style="height: 60px; border: dashed;">' . "\n";
        $html .= '       <div class="col s2">' . "\n";
        $html .= '           <i class="material-icons" style="font-size: 3em; line-height: 125%">info</i>' . "\n";
        $html .= '       </div>' . "\n";
        $html .= '       <div class="col s10 truncate " style="font-size: 1.5em; line-height: 250%">' . "\n";
        $html .= '           ' . $msg . "\n";
        $html .= '       </div>' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        $html .= '<div class="hide-on-med-and-up">' . "\n";
        $html .= '   <div class="blue lighten-4 blue-text center" style="border: dashed">' . "\n";
        $html .= '       <br>' . $msg . '<br>&nbsp;' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        
        if($echo){
            echo $html;
            exit;
        } else {
            return $html;
        }
    }

}

if (!function_exists('message_warn')) {

    /**
     * Genere un message d'attention
     * @param string $msg - Le message à afficher
     * @param boolean $echo - echo le message sinon le return, false par defaut
     */
    function message_warn($msg, $echo = false) {
        $html = '';
        $html .= '<div class="hide-on-small-only">' . "\n";
        $html .= '   <div class="row amber-text yellow lighten-4" style="height: 60px; border: dashed;">' . "\n";
        $html .= '       <div class="col s2">' . "\n";
        $html .= '           <i class="material-icons" style="font-size: 3em; line-height: 125%">warning</i>' . "\n";
        $html .= '       </div>' . "\n";
        $html .= '       <div class="col s10 truncate " style="font-size: 1.5em; line-height: 250%">' . "\n";
        $html .= '           ' . $msg . "\n";
        $html .= '       </div>' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        $html .= '<div class="hide-on-med-and-up">' . "\n";
        $html .= '   <div class="yellow lighten-4 amber-text center" style="border: dashed">' . "\n";
        $html .= '       <br>' . $msg . '<br>&nbsp;' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        
        if($echo){
            echo $html;
            exit;
        } else {
            return $html;
        }
    }

}

if (!function_exists('message_conf')) {

    /**
     * Genere un message de confirmation
     * @param string $msg - Le message à afficher
     * @param boolean $echo - echo le message sinon le return, false par defaut
     */
    function message_conf($msg, $echo = false) {
        $html = '';
        $html .= '<div class="hide-on-small-only">' . "\n";
        $html .= '   <div class="row green-text green lighten-4" style="height: 60px; border: dashed;">' . "\n";
        $html .= '       <div class="col s2">' . "\n";
        $html .= '           <i class="material-icons" style="font-size: 3em; line-height: 125%">check_circle</i>' . "\n";
        $html .= '       </div>' . "\n";
        $html .= '       <div class="col s10 truncate " style="font-size: 1.5em; line-height: 250%">' . "\n";
        $html .= '           ' . $msg . "\n";
        $html .= '       </div>' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        $html .= '<div class="hide-on-med-and-up">' . "\n";
        $html .= '   <div class="green lighten-4 green-text center" style="border: dashed">' . "\n";
        $html .= '       <br>' . $msg . '<br>&nbsp;' . "\n";
        $html .= '   </div>' . "\n";
        $html .= '</div>' . "\n";
        
        if($echo){
            echo $html;
            exit;
        } else {
            return $html;
        }
    }

}
