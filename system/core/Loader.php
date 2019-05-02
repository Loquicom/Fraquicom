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
     * Dossier de travail actuel
     * @var  string
     */
    protected $working_dir = '';

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

    public function set_working_dir(string $dir) {
        $this->working_dir = ($dir[strlen($dir) - 1] == DIRECTORY_SEPARATOR) ? $dir : $dir . DIRECTORY_SEPARATOR;
    }

    public function reset_working_dir() {
        $this->working_dir = '';
    }

    /* === Chargement === */

    /**
     * Charge un model
     * @param string $name Le nom du model à charger
     * @return boolean Reussite
     */
    public function model(string $name) {

    }

    /**
     * Charge un controller
     * @param string $name Le nom du controller à charger
     * @return boolean Reussite
     */
    public function controller(string $name) {

    }

    /**
     * Charge une vue
     * @param string $name Le nom de la vue à charger
     * @return boolean Reussite
     */
    public function view(string $name) {

    }

    /**
     * Charge un objet
     * @param string $name Le nom de l'objet à charger
     * @return boolean Reussite
     */
    public function object(string $name) {

    }

    /**
     * Charge un fichier
     * @param string $name Le nom du fichier à charger
     * @return boolean Reussite
     */
    public function file(string $name) {

    }

    /**
     * Charge une bibliotheque
     * @param string $name Le nom de la bibliotheque à charger
     * @return boolean Reussite
     */
    public function library(string $name) {

    }

    /**
     * Charge un helper
     * @param string $name Le nom de l'helper à charger
     * @return boolean Reussite
     */
    public function helper(string $name) {

    }

    /**
     * Charge un fichier de config
     * @param string $name Le nom du fichier de config à charger
     * @return boolean Reussite
     */
    public function config(string $name) {

    }

    /**
     * Inclut un fichier
     * @param string $name Le nom du fichier
     * @param string $ext L'extension du fichier (optional)
     * @return boolean Reussite
     */
    public function import(string $name, string $ext = '.php') {

    }

    /**
     * Recupere le contenu d'un fichier
     * @param string $file Le chemin vers le fichier
     * @return false|string false en cas d'erreur, le contenu sinon
     */
    public function content(string $file) {

    }

    /**
     * Charge les dépendances importer via composer
     * @return boolean Reussite
     */
    public function composer() {

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