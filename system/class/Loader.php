<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Loader.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class Loader {

    /**
     * Instance du loader
     * @var Loader
     */
    private static $instance = null;

    /**
     * Nom de variable interdit pour les vues et les fichiers
     * @var string[] 
     */
    private static $varNameForbidden = array('_config', 'config', '_S', '_setup', 'fc', 'fraquicom');

    /**
     * Le mode mvc ou non
     * @var string 
     */
    private $mode = '';

    /**
     * Tableau de tous les Model instacié
     * @var mixed 
     */
    private $models = array();

    /**
     * Tableau de tous les Controller instacié
     * @var mixed 
     */
    private $controllers = array();

    /**
     * Tableau de tous les Objets instacié
     * @var mixed 
     */
    private $objects = array();

    /**
     * Tableau de tous les Bibliotheque instacié
     * @var mixed 
     */
    private $libraries = array();

    /**
     * Constructeur privé du loader
     * @global mixed $_config
     */
    private function __construct() {
        global $_config;
        $this->mode = $_config['mode'];
    }

    /**
     * Retourne l'instance du loader
     * @return Loader
     */
    public static function getLoader() {
        if (self::$instance === null) {
            self::$instance = new Loader();
        }
        return self::$instance;
    }

    /**
     * Change le mode du Loader entre mvc ou no_mvc
     * @param string $mode
     */
    public function set_mode($mode) {
        $this->mode = $mode;
    }

    /**
     * Retourne le model dans le tableau
     * @param string $name - Le nom du model
     * @return false|mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_model($name) {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        $name = strtolower($name);
        if (isset($this->models[$name])) {
            return $this->models[$name];
        }
        return false;
    }

    /**
     * Retourne le tableaux avec tous les models
     * @return mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_all_models() {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        return $this->models;
    }

    /**
     * Retourne le controller dans le tableau
     * @param string $name - Le nom du controller
     * @return false|mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_controller($name) {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        $name = strtolower($name);
        if (isset($this->controllers[$name])) {
            return $this->controllers[$name];
        }
        return false;
    }

    /**
     * Retourne le tableaux avec tous les controllers
     * @return mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_all_controllers() {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        return $this->controllers;
    }

    /**
     * Retourne l'objet dans le tableau
     * @param string $name - Le nom de l'objet
     * @return false|mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_object($name) {
        if ($this->mode != 'no_mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        $name = strtolower($name);
        if (isset($this->objects[$name])) {
            return $this->objects[$name];
        }
        return false;
    }

    /**
     * Retourne le tableaux avec tous les objets
     * @return mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_all_objects() {
        if ($this->mode != 'no_mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        return $this->objects;
    }

    /**
     * Retourne la bibliotheque dans le tableau
     * @param string $name - Le nom de la bibliotheque
     * @return false|mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_library($name) {
        $name = strtolower($name);
        if (isset($this->libraries[$name])) {
            return $this->libraries[$name];
        }
        return false;
    }

    /**
     * Retourne le tableaux avec tous les bibliotheques
     * @return mixed
     * @throws LoaderException - Mode incorrectt
     */
    public function get_all_libraries() {
        return $this->libraries;
    }

    /**
     * Charge un model
     * @param string $name - Le nom du model
     * @return boolean
     * @throws LoaderException - Mode incorrect|Erreur pendant le chargement
     */
    public function model($name) {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        //On regarde si le fichier existe
        if (file_exists('./application/model/' . $name . '.php')) {
            $minusName = strtolower($name);
            try {
                require './application/model/' . $name . '.php';
                $this->models[$minusName] = new $name();
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement du model : ' . $ex->getMessage());
            }
            return true;
        }
        return false;
    }

    /**
     * Charge un controller
     * @param string $name - Le nom du controller
     * @return boolean
     * @throws LoaderException - Mode incorrect|Erreur pendant le chargement
     */
    public function controller($name) {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        //On regarde si le fichier existe
        if (file_exists('./application/controller/' . $name . '.php')) {
            $minusName = strtolower($name);
            try {
                require './application/controller/' . $name . '.php';
                $this->controllers[$minusName] = new $name();
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement du controller : ' . $ex->getMessage());
            }
            return true;
        }
        return false;
    }

    /**
     * Charge une vue
     * @param string $name - Le nom de la vue
     * @param mixed $params - Les parametres de la vue
     * @param boolean $return - Retour ou affichage de la vue
     * @return boolean
     * @throws LoaderException - Mode incorrect|Erreur pendant le chargmement
     */
    public function view($name, $params = array(), $return = false) {
        if ($this->mode != 'mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        //Si le fichier existe
        if (file_exists('./application/view/' . $name . '.php')) {
            //Si on retourne
            if ($return) {
                try {
                    $content = $this->execute_php('./application/view/' . $name, $params);
                } catch (Exception $ex) {
                    throw new LoaderException('Erreur pendant le chargement de la vue : ' . $ex->getMessage());
                }
                return $content;
            }
            //Si on affiche
            else {
                //Chargement des variables et du fichier
                if (is_array($params) && !empty($params)) {
                    foreach ($params as $key => $val) {
                        if (!in_array($key, self::$varNameForbidden)) {
                            $$key = $val;
                        }
                    }
                }
                try {
                    require './application/view/' . $name . '.php';
                } catch (Exception $ex) {
                    throw new LoaderException('Erreur pendant le chargement de la vue : ' . $ex->getMessage());
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Charge un objet
     * @param string $name - Le nom de l'objet
     * @return boolean
     * @throws LoaderException - Mode incorrect|Erreur pendant le chargement
     */
    public function object($name) {
        if ($this->mode != 'no_mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        //On regarde si le fichier existe
        if (file_exists('./application/class/' . $name . '.php')) {
            if (class_exists($name, false)) {
                return true;
            }
            $minusName = strtolower($name);
            try {
                require './application/class/' . $name . '.php';
                $this->objects[$minusName] = new $name();
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement de l\'objet : ' . $ex->getMessage());
            }
            return true;
        }
        return false;
    }

    /**
     * Charge un fichier
     * @param string $name - Le nom du fichier
     * @param mixed $params - Les parametres du fichier
     * @param boolean $return - Retour ou affichage du fichier
     * @return boolean
     * @throws LoaderException - Mode incorrect|Erreur pendant le chargmement
     */
    public function file($name, $params = null, $return = false) {
        if ($this->mode != 'no_mvc') {
            throw new LoaderException('Mode incompatible avec la methode');
        }
        //Si le fichier existe
        if (file_exists('./application/' . $name . '.php')) {
            //Si on retourne
            if ($return) {
                try {
                    $content = $this->execute_php('./application/' . $name, $params);
                } catch (Exception $ex) {
                    throw new LoaderException('Erreur pendant le chargement du fichier : ' . $ex->getMessage());
                }
                return $content;
            }
            //Si on affiche
            else {
                //Chargement des variables et du fichier
                if (is_array($params) && !empty($params)) {
                    foreach ($params as $key => $val) {
                        if (!in_array($key, self::$varNameForbidden)) {
                            $$key = $val;
                        }
                    }
                }
                try {
                    require './application/' . $name . '.php';
                } catch (Exception $ex) {
                    throw new LoaderException('Erreur pendant le chargement du fichier : ' . $ex->getMessage());
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Charge un helper
     * @param string $name - Le nom de l'helper
     * @return boolean
     * @throws LoaderException - Erreur pendant le chargement
     */
    public function helper($name) {
        $return = false;
        //Chargement de l'helper de l'utilisateur
        if (file_exists('./application/helper/' . $name . '.php')) {
            try {
                require './application/helper/' . $name . '.php';
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement de l\'helper : ' . $ex->getMessage());
            }
            $return = true;
        }
        //Chargement de l'helper de Fraquicom
        if (file_exists('./system/helper/' . $name . '.php')) {
            try {
                require './system/helper/' . $name . '.php';
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement de l\'helper : ' . $ex->getMessage());
            }
            $return = true;
        }
        return $return;
    }

    /**
     * Charge une bibliotheque
     * @param string $name - Le nom de la bibliotheque
     * @return boolean
     * @throws LoaderException - Erreur pendant le chargement
     */
    public function library($name) {
        $minusName = strtolower($name);
        //Chargement de la bibliothéque de l'utilisateur de l'utilisateur
        if (file_exists('./application/library/' . $name . '.php')) {
            if (class_exists($name, false)) {
                return true;
            }
            try {
                require './application/library/' . $name . '.php';
                $this->libraries[$minusName] = new $name();
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement de la bibliotheque : ' . $ex->getMessage());
            }
            return true;
        }
        //Chargement de la bibliothéque de Fraquicom
        if (file_exists('./system/library/' . $name . '.php')) {
            if (class_exists($name, false)) {
                return true;
            }
            try {
                require './system/library/' . $name . '.php';
                $this->libraries[$minusName] = new $name();
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement de la bibliotheque : ' . $ex->getMessage());
            }
            return true;
        }
        return false;
    }
    
    /**
     * Charge un fichier de config
     * @param string $name - Le nom de la bibliotheque
     * @return boolean
     * @throws LoaderException - Erreur pendant le chargement
     */
    public function config($name){
        if(file_exists('./application/config/' . $name . '.php')){
            try{
                require './application/config/' . $name . '.php';
            } catch (Exception $ex) {
                throw new LoaderException('Erreur pendant le chargement du fichier de config : ' . $ex->getMessage());
            }
        }
        return false;
    }

    /**
     * Execute du code php et renvoie le resultat
     * @param string $filename - Le chemin du fichier php
     * @param mixed $data - Les parametres pour le fichier
     * @return boolean|mixed
     */
    private function execute_php($filename, $data = array()) {
        if (file_exists($filename . '.php')) {
            ob_start();
            //Création des variables de la vue
            if (is_array($data) && !empty($data)) {
                foreach ($data as $key => $val) {
                    if (!in_array($key, self::$varNameForbidden)) {
                        $$key = $val;
                    }
                }
            }
            //Recuperation de la vue
            require $filename . '.php';
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        return false;
    }

}
