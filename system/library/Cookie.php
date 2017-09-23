<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Cookie.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

class Cookie {

    /**
     * Recupere la valeur d'un cookie
     * @param string $clef
     * @return boolean
     */
    public function get($clef) {
        if (isset($_COOKIE[$clef])) {
            return $_COOKIE[$clef];
        }
        return false;
    }

    /**
     * Méthode magique pour recuperer la valeur d'un cookie
     * $this->cookie->clef
     * @see Cookie::get()
     * @param string $clef
     * @return boolean
     */
    public function __get($clef) {
        return $this->get($clef);
    }

    /**
     * Création d'un ou plusieurs cookie
     * 1 cookie : add(clef, val, temps = 30)
     * +1 cookies : add(array(clef => val, ...), temps = 30)
     * @param mixed $data - Tableau avec les cookie ou le nom du cookie
     * @param mixed $val - La valeur ou le temps
     * @param int $temps - Le temps de validité en jour
     * @return boolean
     */
    public function add($data, $val = '', $temps = 30) {
        //Si plusieurs cookie
        if (is_array($data)) {
            //On regarde si le temps à été mis dans val
            if (trim($val) != '') {
                $temps = $val;
            }
            foreach ($data as $nom => $valeur) {
                setcookie($nom, $valeur, time() + (60 * 60 * 24 * $temps), null, null, false, true);
            }
            return true;
        }
        //Si un seul
        else if (is_string($data)) {
            setcookie($data, $val, time() + (60 * 60 * 24 * $temps), null, null, false, true);
            return true;
        }
        //Sinon cas imprevue
        else {
            return false;
        }
    }

    /**
     * Méthode magique pour ajouter ou supprimer un cookie
     * Pour supprimer donner la valeur null
     * $this->cookie->clef = valeur
     * @param string $clef
     * @param mixed $val
     */
    public function __set($clef, $val) {
        //Si val est null on supprime le cookie
        if ($val === null) {
            $this->remove($clef);
        }
        //Sinon on l'ajoute
        else {
            $this->add($clef, $val);
        }
    }

    /**
     * Supprime un cookie
     * @param string $clef
     * @return boolean
     */
    public function remove($clef) {
        //Si le cookie existe
        if (isset($_COOKIE[$clef])) {
            //Si c'est un tableau de clef
            if (is_array($clef)) {
                foreach ($clef as $cookie) {
                    setcookie($cookie, false, 0, null, null, false, true);
                }
            }
            //Sinon si c'est directement la clef
            else {
                setcookie($clef, false, 0, null, null, false, true);
            }
            return true;
        } 
        //Sinon false
        else {
            return false;
        }
    }

}
