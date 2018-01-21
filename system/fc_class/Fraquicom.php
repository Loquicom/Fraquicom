<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Fraquicom.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class Fraquicom {

    /**
     * Gestion des erreurs du framework
     * @var Error
     */
    public $error = null;
    
    /**
     * Instance de Config qui permet d'acceder au valeurs des fichiers de
     * configuration
     * @var Config
     */
    public $config = null;
    
    /**
     * Le loader du framework
     * @var Loader
     */
    public $load = null;

    /**
     * Retourne une instance de Fraquicom
     * @return \Fraquicom
     */
    public static function get_instance() {
        return new Fraquicom();
    }

    /**
     * Charge le loader et load les preset du fichier de config
     * @global type $config
     * @global type $_config
     * @throws FraquicomException
     */
    public function __construct() {
        //Chargement gestion des erreurs
        $this->error = Error::get_instance();
        //Chargement fichier de config
        $this->config = Config::get_config();
        //Chargement de la class loader
        $this->load = Loader::getLoader();
        //Chargement des fichiers demandé par l'utilisateur
        //Chargment Helper
        if ($this->config->get('loader', 'all', 'helper')) {
            $helperFiles = array_merge(array_diff(scandir('./application/helper/'), array('..', '.')), array_diff(scandir('./system/helper/'), array('..', '.')));
            foreach ($helperFiles as $helperFile) {
                $helperFile = str_replace('.php', '', $helperFile);
                if ($this->load->helper($helperFile) === false) {
                    throw new FraquicomException('Impossible de charger le fichier ' . $helperFile . '.php');
                }
            }
        } else if (!empty($this->config->get('loader', 'helper'))) {
            foreach ($this->config->get('loader', 'helper') as $helperFile) {
                if ($this->load->helper($helperFile) === false) {
                    throw new FraquicomException('Impossible de charger le fichier ' . $helperFile . '.php');
                }
            }
        }
        //Chargement bibliotheque
        if ($this->config->get('loader', 'all', 'library')) {
            $librayFiles = array_merge(array_diff(scandir('./application/library/'), array('..', '.')), array_diff(scandir('./system/library/'), array('..', '.')));
            foreach ($librayFiles as $libraryFile) {
                $libraryFile = str_replace('.php', '', $libraryFile);
                if ($this->load->library($libraryFile) === false) {
                    throw new FraquicomException('Impossible de charger le fichier ' . $libraryFile . '.php');
                }
            }
        } else if (!empty($this->config->get('loader', 'library'))) {
            foreach ($this->config->get('loader', 'library') as $libraryFile) {
                if ($this->load->library($libraryFile) === false) {
                    throw new FraquicomException('Impossible de charger le fichier ' . $libraryFile . '.php');
                }
            }
        }
        //Chargement class
        if ($this->config->get('mode') == 'no_mvc' && $this->config->get('loader', 'all', 'class')) {
            $classFiles = array_diff(scandir('./application/class/'), array('..', '.'));
            foreach ($classFiles as $classFile) {
                $classFile = str_replace('.php', '', $classFile);
                if ($this->load->library($classFile) === false) {
                    throw new FraquicomException('Impossible de charger le fichier ' . $classFile . '.php');
                }
            }
        } else if ($this->config->get('mode') == 'no_mvc' && !empty($this->config->get('loader', 'class'))) {
            foreach ($this->config->get('loader', 'class') as $classFile) {
                if ($this->load->object($classFile) === false) {
                    throw new FraquicomException('Impossible de charger le fichier ' . $classFile . '.php');
                }
            }
        }
    }

    /**
     * Tente d'acceder à un model chargé
     * @param string $name - Le nom
     * @return false|mixed
     */
    public function model($name) {
        return $this->load->get_model($name);
    }

    /**
     * Tente d'acceder à un controller chargé
     * @param string $name - Le nom
     * @return false|mixed
     */
    public function controller($name) {
        return $this->load->get_controller($name);
    }

    /**
     * Tente d'acceder à un objet chargé
     * @param string $name - Le nom
     * @return false|mixed
     */
    public function object($name) {
        return $this->load->get_object($name);
    }

    /**
     * Tente d'acceder à une bibliotheque chargé
     * @param string $name - Le nom
     * @return false|mixed
     */
    public function library($name) {
        if (strtolower($name) == 'db') {
            $name = 'database';
        }
        return $this->load->get_library($name);
    }

    /**
     * Méthode magique qui permet d'acceder aux bibilothéque/model/objet
     * Si une bibliotheque chargée porte le même nom qu' un model ou un objet,
     * elle est renvoyé en priorité
     * $this->name;
     * @param string $name - Le nom
     * @return boolean|mixed
     */
    public function __get($name) {
        if (strtolower($name) == 'db') {
            $name = 'database';
        }
        //On tente d'abord de recupérer une bibliothéque
        if ($this->load->get_library($name) !== false) {
            return $this->load->get_library($name);
        }
        //Sinon un objet ou un model selon le mode
        if ($this->config->mode == 'mvc') {
            if ($this->load->get_model($name) !== false) {
                return $this->load->get_model($name);
            }
            return false;
        } else {
            if ($this->load->get_object($name) !== false) {
                return $this->load->get_object($name);
            }
            return false;
        }
    }

}
