<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Loader.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class FC_Loader {

    /**
     * Liste des noms de variables qu'il est interdit d'utiliser
     * @var array
     */
    protected static $forbidden = [
        'config', 
        'fc', 
        'fraquicom', 
        'logger', 
        'error', 
        'router', 
        'loader', 
        'acl', 
        '_S', 
        '_SESSION', 
        '_SERVER', 
        '_POST',
        '_GET',
        '_REQUEST',
        '_COOKIE'
    ];

    /**
     * Instance du Loader pour le singleton
     * @var FC_Loader
     */
    protected static $instance = null;

    /**
     * Liste des alias sous la forme [nom concret => [tableau des alias]]
     * @var array
     */
    protected $alias = [
        'database' => ['db']
    ];

    /* --- Liste des fichiers déjà chargés --- */

    /**
     * Liste des controllers instanciés
     * @var array
     */
    protected $controller = [];

    /**
     * Liste des models instanciés
     * @var array
     */
    protected $model = [];

    /**
     * Liste des objets instanciés
     * @var array
     */
    protected $object = [];

    /**
     * Liste des bibliothèques instanciés
     * @var array
     */
    protected $library = [];

    /* === Instanciation === */

    public static function get_instance() {
        if(static::$instance === null) {
            static::$instance = new FC_Loader();
        }
        return static::$instance;
    }

    private function __construct() {
        //Private pour le singleton
    }

    /* === Paramétrage === */

    /**
     * Ajoute un alias
     * @param string $default_name Le nom sans alias
     * @param string|array $alias Le ou les alias
     * @return boolean Reussite
     */
    public function add_alias(string $default_name, $alias) {
        if(static::is_forbidden($default_name)) {
            return false;
        }
        //Si besoin transforme en tableau
        if(!is_array($alias)) {
            $alias = [$alias];
        }
        //Ajoute les alias
        if(array_key_exists($default_name, $this->alias)) {
            $this->alias[$default_name] = array_merge($this->alias[$default_name], $alias);
        } else {
            $this->alias[$default_name] = $alias;
        }
        return true;
    }

    /**
     * Supprime les alias d'un nom
     * @param string $default_name Le nom dont il faut supprimer les alias
     */
    public function remove_alias(string $default_name) {
        unset($this->alias[$default_name]);
    }

    /* === Utilitaire === */

    /**
     * Indique si un nom de variable est interdit
     * @param string $name Le nom à tester
     * @return boolean Interdit ou non
     */
    protected static function is_forbidden(string $name) {
        return in_array($name, static::$forbidden);
    }

}