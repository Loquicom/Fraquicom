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
    protected $working_dir = APPLICATION;

    /**
     * Liste des alias sous la forme [nom concret => [tableau des alias]]
     * @var array
     */
    protected $alias = [
        'database' => ['db']
    ];

    /* --- Liste des fichiers déjà chargés --- */

    /**
     * Liste des noms des fichiers déjà chargés
     * @var string[]
     */
    protected $loaded = [];

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
     * Liste des fichiers instanciés
     * @var array
     */
    protected $file = [];

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
        $this->working_dir = APPLICATION;
    }

    /* === Chargement === */

    /**
     * Charge un model
     * @param string $name Le nom du model à charger
     * @return boolean true Reussite, false le fichier n'existe pas
     * @throws FcLoaderException Erreur ou Exception déclanchée lors du chargement du fichier
     */
    public function model(string $name) {
        $result = $this->load($name, 'model');
        if($result === false) {
            return false;
        }
        $this->model[strtolower($name)] = $result;
        return true;
    }

    /**
     * Charge un controller
     * @param string $name Le nom du controller à charger
     * @return boolean true Reussite, false le fichier n'existe pas
     * @throws FcLoaderException Erreur ou Exception déclanchée lors du chargement du fichier
     */
    public function controller(string $name) {
        $result = $this->load($name, 'controller');
        if($result === false) {
            return false;
        }
        $this->controller[strtolower($name)] = $result;
        return true;
    }

    /**
     * Charge une vue
     * @param string $name Le nom de la vue à charger
     * @param array $params Les variables à passer à la vue [varName => varVal, ...]
     * @param bool $return Retourner ou afficher la vue
     * @return boolean Reussite
     * @throws FcLoaderException
     */
    public function view(string $name, array $params = [], bool $return = false) {
        return $this->file('view' . DIRECTORY_SEPARATOR . $name, $params, $return);
    }

    /**
     * Charge un objet
     * @param string $name Le nom de l'objet à charger
     * @return boolean Reussite
     * @throws FcLoaderException
     */
    public function object(string $name) {
        $result = $this->load($name, 'class');
        if($result === false) {
            return false;
        }
        $this->object[strtolower($name)] = $result;
        return true;
    }

    /**
     * Charge un fichier
     * @param string $name Le nom du fichier à charger
     * @param array $params Les variables à passer au fichier [varName => varVal, ...]
     * @param bool $return Retourner ou afficher le fichier
     * @return boolean Reussite
     * @throws FcLoaderException
     */
    public function file(string $name, array $params = [], bool $return = false) {
        $lower_name = strtolower($name);
        //Chargement dans un composant
        if(strpos($name, ':') !== false) {
            list($component, $name) = explode(':', $lower_name, 2);
            $component_dir = $this->working_dir . 'component' . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR;
            if(!file_exists($component_dir . $name . '.php')) {
                return false;
            }
            //Tentative de chargement du fichier
            try {
                require $component_dir . $name . '.php';
                $this->set_working_dir($component_dir);
                $this->file[$name] = new FC_Component($this, new $name(), $component_dir);
                $this->reset_working_dir();
            } catch (Exception $ex) {
                throw new FcLoaderException("Impossible de charger le controller " . $lower_name, 1, $ex);
            }
        } 
        //Chargement dans l'application principale
        else {
            if(!file_exists($this->working_dir . $name . '.php')) {
                return false;
            }
            //Tentative de chargement du fichier
            try {
                require $this->working_dir . $name . '.php';
                //Si on charge qqchose dans un composant
                if($this->working_dir === APPLICATION) {
                    $this->file[$lower_name] = new $name();
                } else {
                    $this->file[$lower_name] = new FC_Component($this, new $name(), $this->working_dir);
                }
            } catch (Exception $ex) {
                throw new FcLoaderException("Impossible de charger le controller " . $lower_name, 1, $ex);
            }
        }
        return true;
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

    /* === Recupère un objet === */

    public function get_model(string $name) {
        $name = strtolower($name);
        if(!array_key_exists($name, $this->model)) {
            return false;
        }
        return $this->model[$name];
    }

    public function get(string $name) {

    }

    public function __get(string $name) {

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

    protected function load(string $name, string $type) {
        $return;
        //Recupere le nom de class et le nom en minuscule
        $lower_name = strtolower($name);
        $class_name = explode('/', str_replace('\\', '/', $lower_name));
        $class_name = $class_name[count($class_name) - 1];
        //Chargement dans un composant
        if(strpos($name, ':') !== false) {
            //Recupere le nom du composant
            list($component, $name) = explode(':', $name, 2);
            $class_name = substr($class_name, strlen($component) + 1);
            //Regarde si il est déjà chargé
            if(in_array($class_name, $this->loaded)) {
                return false;
            }
            //Verif que le fichier existe
            $component_dir = $this->working_dir . 'component' . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR;
            if(!file_exists($component_dir . $type . DIRECTORY_SEPARATOR . $name . '.php')) {
                return false;
            }
            //Tentative de chargement du fichier
            try {
                require $component_dir . $type . DIRECTORY_SEPARATOR . $name . '.php';
                $this->set_working_dir($component_dir);
                $return = new FC_Component($this, new $class_name(), $component_dir);
                $this->reset_working_dir();
            } catch (Exception $ex) {
                throw new FcLoaderException("Impossible de charger " . $lower_name, 1, $ex);
            }
        } 
        //Chargement dans l'application principale
        else {
            //Regarde si il est déjà chargé
            if(in_array($class_name, $this->loaded)) {
                return false;
            }
            //Verif que le fichier existe
            if(!file_exists($this->working_dir . $type . DIRECTORY_SEPARATOR . $name . '.php')) {
                return false;
            }
            //Tentative de chargement du fichier
            try {
                require $this->working_dir . $type . DIRECTORY_SEPARATOR . $name . '.php';
                //Si on charge qqchose dans un composant
                if($this->working_dir === APPLICATION) {
                    $return = new $class_name();
                } else {
                    $return = new FC_Component($this, new $class_name(), $this->working_dir);
                }
            } catch (Exception $ex) {
                throw new FcLoaderException("Impossible de charger " . $lower_name, 1, $ex);
            }
        }
        $this->loaded[] = $class_name;
        return $return;
    }

    /**
     * Execute du code php et renvoie le resultat
     * @param string $filename - Le chemin du fichier php sans l'extension
     * @param array $data - Les parametres pour le fichier [varName => varVal, ...]
     * @return false|mixed
     */
    protected function execute(string $filename,array $data = []) {
        $content;
        //Chargement dans un composant
        if(strpos($name, ':') !== false) {
            //Recupere le nom du composant
            list($component, $name) = explode(':', $name, 2);
            //Verif que le fichier existe
            $component_dir = $this->working_dir . 'component' . DIRECTORY_SEPARATOR . $component . DIRECTORY_SEPARATOR;
            if(!file_exists($component_dir . $filename . '.php')) {
                return false;
            }
            //Tentative de chargement du fichier
            try {
                ob_start();
                //Création des variables
                if (is_array($data) && !empty($data)) {
                    foreach ($data as $key => $val) {
                        if (!in_array($key, static::$forbidden)) {
                            $$key = $val;
                        }
                    }
                }
                //Recuperation
                require $component_dir . $filename . '.php';
                $content = ob_get_contents();
                ob_end_clean();
            } catch (Exception $ex) {
                throw new FcLoaderException("Impossible de charger " . $filename, 1, $ex);
            }
        } 
        //Chargement dans l'application principale
        else {
            //Verif que le fichier existe
            if(!file_exists($this->working_dir . $filename . '.php')) {
                return false;
            }
            //Tentative de chargement du fichier
            try {
                ob_start();
                //Création des variables
                if (is_array($data) && !empty($data)) {
                    foreach ($data as $key => $val) {
                        if (!in_array($key, static::$forbidden)) {
                            $$key = $val;
                        }
                    }
                }
                //Recuperation
                require $this->working_dir . $filename . '.php';
                $content = ob_get_contents();
                ob_end_clean();
            } catch (Exception $ex) {
                throw new FcLoaderException("Impossible de charger " . $filename, 1, $ex);
            }
        }
        return $content;
    }

}

/**
 * Proxy pour le loader de fichiers des composants
 */
class FC_Component {

    /**
     * Le loader à utiliser
     * @var FC_Loader
     */
    protected $loader;

    /**
     * L'objet instancié dans le composant
     * @var Object
     */
    protected $object;

    /**
     * Dossier du composant
     * @var string
     */
    protected $component_dir;

    public function __construct(FC_Loader $loader, $object, string $dir) {
        $this->loader = $loader;
        $this->object = $object;
        $this->component_dir = $dir;
    }

    public function __call(string $name , array $arguments) {
        if(!method_exists($this->object, $name)) {
            throw new FraquicomException('Call to undefined method ' . get_class($this->object) . '::' . $name . '()');
        }
        $this->loader->set_working_dir($this->component_dir);
        call_user_func_array([$this->object, $name], $arguments);
        $this->loader->reset_working_dir();
    }

}