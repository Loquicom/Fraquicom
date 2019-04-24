<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Config.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class FC_Config {

    /**
     * Les données de config
     * @var array
    */
    protected $config = [];

    /**
     * Construit un une instance d'encapsulation de données de configuration pour faciliter l'accès
     */
    public function __construct() {
        if(func_num_args() > 0) {
            $this->config = call_user_func_array([$this, 'merge_array'], func_get_args());
        }
    }

    /**
     * Accède à une valeur du tableau config
     * Autant de parametre que de clef pour accéder à la valeur ou un tableau avec toutes les clefs
     * Aucun parametre pour retourner le tableau complet
     * @return false|mixed
     */
    public function get() {
        //Si aucun parametre
        if (func_num_args() == 0) {
            return $this->config;
        } 
        //Si 1 parametre
        else if (func_num_args() == 1) {
            //Si c'est un tableau de parametre on appel la fonction avec la bonne forme
            if(is_array(func_get_arg(0))){
                return call_user_func_array([$this, 'get'], func_get_arg(0));
            }
            //Si la clef existe
            else if (isset($this->config[func_get_arg(0)])) {
                return $this->config[func_get_arg(0)];
            } else {
                return false;
            }
        } 
        //Si +1 parametres
        else {
            $config = $this->config;
            $args = func_get_args();
            foreach ($args as $arg) {
                if (isset($config[$arg])) {
                    $config = $config[$arg];
                } else {
                    return false;
                }
            }
            return $config;
        }
    }

    /**
     * Ajoute un element dans le tableau de config
     * @param string $key La clef à ajouter dans le tableau
     * @param mixed $val La valeur à ajouter 
     */
    public function add($key, $val) {
        $this->config[$key] = $val;
    }

    /**
     * Accède sous forma d'attribut à une valeur de config
     * @param string $key La clef du tableau à accèder
     * @return false|FC_Config|mixed false si la clef est introuvable,
     * FC_Config si la valeur retourné est un tableau (pour enchainer),
     * mixed si la valeur n'est pas un tableau (retourne directement la valeur)
     */
    public function __get($key) {
        $result = $this->get($key);
        //Si le resultat est un tableau on l'encapsule dans un config pour enchainer
        if(is_array($result)) {
            return new FC_Config($result);
        }
        return $result;
    }

    /**
     * Ajoute un element dans le tableau de config
     * @param string $key La clef à ajouter dans le tableau
     * @param mixed $val La valeur à ajouter 
     */
    public function __set($key, $val) {
        $this->add($key, $val);
    }

    /**
     * Fusionne des tableaux et des valeurs en un seul tableau
     * @return array Le resultat de la fusion
     */
    protected function merge_array() {
        $result = [];
        switch(func_num_args()) {
            case 0:
                break;
            case 1:
                if(is_array(func_get_arg(0))) {
                    $result = func_get_arg(0);
                } else {
                    $result[] = func_get_arg(0);
                }
                break;
            default:
                foreach(func_get_args() as $arg) {
                    if(!is_array($arg)) {
                        $result[] = $arg;
                    } else {
                        foreach($arg as $key => $val) {
                            $result[$key] = $val;
                        }
                    }
                }
        }
        return $result;
    }

}