<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Session.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

class Session {

    /**
     * Ajoute les champs pour la class si besoin et verifie les variables temporaire
     * @global array $_S - La session
     */
    public function __construct() {
        global $_S;
        //Zone information pour les variables flash
        if (!isset($_S['_fc_flash'])) {
            $_S['_fc_flash'] = array();
        }
        //Zone information pour les variables temporaire
        if (!isset($_S['_fc_temp'])) {
            $_S['_fc_temp'] = array();
        }
        //Mise à jour des varaibles temporaire
        $this->verify_temp();
    }

    /**
     * Renvoie la valeur associé à la clef dans la session
     * @global array $_S - La session
     * @param string $clef
     * @return false|mixed
     */
    public function get($clef) {
        global $_S;
        //Mise à jour des varaibles temporaire
        $this->verify_temp();
        if (isset($_S[$clef])) {
            //On regarde si c'est une variable flash
            if (array_key_exists($clef, $_S['_fc_flash'])) {
                //On recupere la valeur
                $val = $_S[$clef];
                //On retire une utilisation
                $_S['_fc_flash'][$clef] --;
                //Si il n'y a plus d'utilisation on supprime
                if ($_S['_fc_flash'][$clef] <= 0) {
                    unset($_S['_fc_flash'][$clef]);
                    unset($_S[$clef]);
                }
                //Renvoie de la valeur
                return $val;
            } else {
                return $_S[$clef];
            }
        }
        return false;
    }

    /**
     * Methode magique recupére une valeur de la session
     * $this->session->clef
     * @see Session::get()
     * @param string $clef
     * @return false|mixed
     */
    public function __get($clef) {
        return $this->get($clef);
    }

    /**
     * Ajoute une ou des valeurs dans la session
     * 1 valeur : add(clef, val)
     * +1 valeurs : add(array(clef => val, ...))
     * @global array $_S - La session
     * @param mixed $data
     * @param string $val
     * @return boolean
     */
    public function add($data, $val = '') {
        global $_S;
        //Si val n'est pas vide, on ne set qu'une donnée
        if (trim($val) != '' && is_string($data)) {
            $_S[$data] = $val;
            return true;
        }
        //Sinon on set plusieurs dans data
        else if (is_array($data)) {
            foreach ($data as $key => $val) {
                $_S[$key] = $val;
            }
            return true;
        }
        //Sinon cas imprevue
        else {
            return false;
        }
    }

    /**
     * Ajoute une ou des valeurs dans la session avec un nombre d'utilistion
     * 1 valeur : add(clef, val)
     * +1 valeurs : add(array(clef => val, ...))
     * @global array $_S - La session
     * @param mixed $data
     * @param string $val
     * @param int $nbUtilisation - Le nombre d'utilisation
     * @return boolean
     */
    public function add_flash($data, $val = '', $nbUtilisation = 1) {
        global $_S;
        //Si val n'est pas vide, on ne set qu'une donnée
        if (trim($val) != '' && is_string($data)) {
            $_S[$data] = $val;
            $_S['_fc_flash'][$data] = $nbUtilisation;
            return true;
        }
        //Sinon on set plusieurs dans data
        else if (is_array($data)) {
            foreach ($data as $key => $val) {
                $_S[$key] = $val;
                $_S['_fc_flash'][$key] = $nbUtilisation;
            }
            return true;
        }
        //Sinon cas imprevue
        else {
            return false;
        }
    }

    /**
     * Ajoute une ou des valeurs dans la session valide pendant un temps determiner
     * 1 valeur : add(clef, val)
     * +1 valeurs : add(array(clef => val, ...))
     * @global array $_S - La session
     * @param mixed $data
     * @param string $val
     * @param int $temps - Le temps en seconde
     * @return boolean
     */
    public function add_temp($data, $val = '', $temps = 300) {
        global $_S;
        //Si val n'est pas vide, on ne set qu'une donnée
        if (trim($val) != '' && is_string($data)) {
            $_S[$data] = $val;
            $_S['_fc_temp'][$data] = time() + $temps;
            return true;
        }
        //Sinon on set plusieurs dans data
        else if (is_array($data)) {
            foreach ($data as $key => $val) {
                $_S[$key] = $val;
                $_S['_fc_temp'][$key] = time() + $temps;
            }
            return true;
        }
        //Sinon cas imprevue
        else {
            return false;
        }
    }

    /**
     * Méthode magique ajoute une valeur dans la session
     * $this->session->clef = valeur
     * @see Session::add()
     * @param string $clef
     * @param mixed $val
     * @return boolean
     */
    public function __set($clef, $val) {
        return $this->add($clef, $val);
    }

    /**
     * Supprime une claf et sa valeur
     * @global array $_S - La session
     * @param string $clef
     * @return boolean
     */
    public function remove($clef) {
        global $_S;
        unset($_S[$clef]);
        return true;
    }

    /**
     * Vide la session
     * @global array $_S - La session
     * @return boolean
     */
    public function clear() {
        global $_S;
        //On vide la session
        foreach ($_S as $key => $val) {
            unset($_S[$key]);
        }
        //Recreation zone flash et temporaire
        $_S['_fc_flash'] = array();
        $_S['_fc_temp'] = array();
        return true;
    }

    /**
     * Verifie et supprime les variable temporaire expirer
     * @global array $_S - La session
     */
    private function verify_temp() {
        global $_S;
        //On parcours toutes les variables temporaire
        if (!empty($_S['_fc_temp'])) {
            foreach ($_S['_fc_temp'] as $key => $tmpFin) {
                //Si le timestamp actuel est plus grand que celui de fin on supprime
                if ($tmpFin < time()) {
                    unset($_S['_fc_temp'][$key]);
                    unset($_S[$key]);
                }
            }
        }
    }

}
