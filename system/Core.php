<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Core.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

final class Core {

    /**
     * Instance du Core
     * @var Core
     */
    private static $instance = null;

    /**
     * Contenu par defaut du fichier de config fraquicom.json
     * @var array
     */
    private static $default_json_config = [
        'require' => [
            'appli_name' => 'Fraquicom',
            'data_path' => 'data',
            'tmp_path' => 'tmp',
            'MVC' => true
        ],
        'system' => [
            'setup' => true
        ]
    ];

    /**
     * Instance du Fraquicom
     * @var Fraquicom
     */
    private $fraquicom;

    /**
     * Instance du gestionnaire d'erreur
     * @var FC_Error
     */
    private $error;

    /**
     * Instance du gestionnaire de logs
     * @var Logger
     */
    private $logger;

    /**
     * Instance du gestionnaire de routage
     * @var FC_Router
     */
    private $router;

    /**
     * Instance du gestionnaire de chargement de fichiers
     * @var FC_Loader
     */
    private $loader;

    /**
     * Instance du gestionnaire d'accès
     * @var FC_Acl
     */
    private $acl;

    /**
     * Contenu du fichier de config fraquicom.json
     * @var array
     */
    private $json_config;

    /**
     * Contenu des fichiers de config
     * @var array
     */
    private $config;

    /* === Instanciation === */

    private function __construct() {
        //Verif que le fichier de config json existe
        if (!file_exists('fraquicom.json')) {
            //Si le fichier est absent creation
            if (!$this->generate_config_json()) {
                throw new FraquicomException("Impossible de generer le fichier fraquicom.json");
            }
        }
        //Charge le fichier de config json
        $json = file_get_contents('fraquicom.json');
        if ($json === false) {
            throw new FraquicomException("Impossible de charger le fichier fraquicom.json");
        }
        //Parse le fichier
        $json_config = json_decode($json);
        if ($json_config === false) {
            throw new FraquicomException("Impossible de parser le fichier fraquicom.json");
        }
        //Sauvegarde la valeur
        $this->json_config = $json_config;
        //Ajout / si besoins chemin
        $this->json_config->require->data_path .= ($this->json_config->require->data_path[strlen($this->json_config->require->data_path) - 1] == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
        $this->json_config->require->tmp_path .= ($this->json_config->require->tmp_path[strlen($this->json_config->require->tmp_path) - 1] == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
        //Creation constante MVC
        define('MVC', $this->json_config->require->MVC);
        //Creation dossier data et tmp
        if(!$this->make_dir($this->json_config->require->data_path)){
            throw new FraquicomException("Impossible de créer le dossier data : " . $this->json_config->require->data_path);
        }
        if(!$this->make_dir($this->json_config->require->tmp_path . 'log')){
            throw new FraquicomException("Impossible de créer le dossier temporaire : " . $this->json_config->require->tmp_path);
        }
        //Charge la class d'erreur et de log
        require SYSTEM . 'core' . DIRECTORY_SEPARATOR . 'Logger.php';
        $this->logger = new Logger(LOG_SYSTEM, $this->json_config->require->tmp_path . 'log' . DIRECTORY_SEPARATOR . 'fraquicom_' . date('Y-m-d'));
        require SYSTEM . 'core' . DIRECTORY_SEPARATOR . 'Error.php';
        FC_Error::set_logger($this->logger);
        $this->error = FC_Error::get_instance();
    }

    /**
     * Creation d'une instance de Core
     * @return boolean|\Core
     */
    public static function create() {
        if (self::$instance === null) {
            self::$instance = new Core();
            return self::$instance;
        }
        return false;
    }

    /* === Setup === */

    public function need_setup() {
        return $this->json_config->system->setup;
    }

    /**
     * Installe le Fraquicom
     * @throws FraquicomException - Erreur lors de l'installation
     */
    public function setup() {
        //Regarde le mode de setup
        if ($this->json_config->require->MVC) {
            //Creation des fichier avec archiecture MVC
            $this->generate_application_mvc();
        } else {
            //Creation des fichier avec archiecture standerd
            $this->generate_application_no_mvc();
        }
        //Parametrage du fichier de config
        $config_content = file_get_contents(APPLICATION . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        if ($config_content === false) {
            throw new FraquicomException("Impossible de lire le fichier de config config.php : " . APPLICATION . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        }
        $tab = str_replace('\\', '/', [$this->json_config->require->data_path, $this->json_config->require->tmp_path]);
        $config_content = str_replace(['%APPLI%', '%DATA%', '%TMP%'], [$this->json_config->require->appli_name, $tab[0], $tab[1]], $config_content);
        if (file_put_contents(APPLICATION . 'config' . DIRECTORY_SEPARATOR . 'config.php', $config_content) === false) {
            throw new FraquicomException("Impossible d'écrire dans le fichier de config config.php : " . APPLICATION . 'config' . DIRECTORY_SEPARATOR . 'config.php');
        }
        //Recriture fichier de config json
        $json = self::$default_json_config;
        $json['system']['setup'] = false;
        $json['require']['MVC'] = MVC;
        if (!$this->generate_config_json($json)) {
            throw new FraquicomException("Impossible de regenerer le fichier fraquicom.json");
        }
    }

    /* === Méthodes d'initialisation === */

    public function ini() {
        //Chargement fichiers de configs
        $this->load_config_file();
        //Chargement des class principales
        $this->load_core_file();
        //Chargement session
        $this->load_session();
    }

    private function load_config_file() {
        //Verif fichier de config existe
        if (!file_exists(APPLICATION . 'config/')) {
            throw new FraquicomException("Impossible de trouver le dossier de config : " . BASE_PATH . APPLICATION . 'config' . DIRECTORY_SEPARATOR);
        }
        //Initialisation variable $config
        $config = [];
        //Recup tous les fichiers de config pour les charger
        $config_files = array_diff(scandir(APPLICATION . 'config' . DIRECTORY_SEPARATOR), ['.', '..', 'index.html', '.htaccess']);
        foreach ($config_files as $config_file) {
            require APPLICATION . 'config' . DIRECTORY_SEPARATOR . $config_file;
        }
        //Recupération en attribut
        $this->config = &$config;
    }

    private function load_core_file() {
        //Class d'encapsulation du fichier de config
        require SYSTEM . 'core' . DIRECTORY_SEPARATOR . 'Config.php';
    }
    
    private function load_session() {
        
    }

    /**
     * Chargement des fichiers en fonction de la configuration de l'utilisateur
     */
    public function load() {

    }

    /* === Methodes generation === */

    /**
     * Genere le fichier de config fraquicom.json
     * @param array $json_config - Le contenu du fichier json à créer
     * @return boolean
     */
    private function generate_config_json($json_config = null) {
        //Si aucun parametre par defaut
        if ($json_config === null) {
            $json_config = self::$default_json_config;
        }
        //Encode en JSON
        $json = json_encode($json_config, JSON_PRETTY_PRINT);
        if ($json === false) {
            return false;
        }
        //Creation du fichier
        $res = file_put_contents('fraquicom.json', $json);
        if ($res === false) {
            return false;
        }
        return true;
    }

    private function generate_application_no_mvc() {
        //Backup avant supression si il y a qqchose dans le dossier application
        if (file_exists(APPLICATION) && count(array_diff(scandir(APPLICATION), array('..', '.', '.htaccess', 'index.html'))) > 0) {
            //Creation d'un fichier backup si besoins
            if (!file_exists('_backup' . DIRECTORY_SEPARATOR)) {
                if (mkdir('_backup' . DIRECTORY_SEPARATOR) === false) {
                    throw new FraquicomException("Impossible de créer le fichier de backup : " . BASE_PATH . '_backup' . DIRECTORY_SEPARATOR);
                }
            }
            //Si il existe deja on le vide
            else {
                $this->clear_folder('_backup' . DIRECTORY_SEPARATOR, true);
            }
            //Copie le ocntenu d'application dans _backup
            $this->copy_dir(APPLICATION, '_backup' . DIRECTORY_SEPARATOR);
            //Vide le dossier application et remet les elements de sécurité
            $this->clear_folder(APPLICATION, true);
            $this->copy_dir(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'security' . DIRECTORY_SEPARATOR, APPLICATION);
        }
        //Création des sous dossiers
        if (!$this->create_dir(APPLICATION, 'class')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'ajax')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'helper')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'library')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        //Ajout fichier config
        $this->copy_dir(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR, APPLICATION . 'config' . DIRECTORY_SEPARATOR);
        //Ajout de l'exemple
        copy(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'preset' . DIRECTORY_SEPARATOR . 'hello_world.php', APPLICATION . 'hello_world.php');
    }
    
    private function generate_application_mvc() {
        //Backup avant supression si il y a qqchose dans le dossier application
        if (file_exists(APPLICATION) && count(array_diff(scandir(APPLICATION), array('..', '.', '.htaccess', 'index.html'))) > 0) {
            //Creation d'un fichier backup si besoins
            if (!file_exists('_backup' . DIRECTORY_SEPARATOR)) {
                if (mkdir('_backup' . DIRECTORY_SEPARATOR) === false) {
                    throw new FraquicomException("Impossible de créer le fichier de backup : " . BASE_PATH . '_backup' . DIRECTORY_SEPARATOR);
                }
            }
            //Si il existe deja on le vide
            else {
                $this->clear_folder('_backup' . DIRECTORY_SEPARATOR, true);
            }
            //Copie le ocntenu d'application dans _backup
            $this->copy_dir(APPLICATION, '_backup' . DIRECTORY_SEPARATOR);
            //Vide le dossier application et remet les elements de sécurité
            $this->clear_folder(APPLICATION, true);
            $this->copy_dir(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'security' . DIRECTORY_SEPARATOR, APPLICATION);
        }
        //Création des sous dossiers
        if (!$this->create_dir(APPLICATION, 'model')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'controller')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'view')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'helper')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        if (!$this->create_dir(APPLICATION, 'library')) {
            throw new FraquicomException("Impossible de créer le dossier " . APPLICATION);
        }
        //Ajout fichier config
        $this->copy_dir(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR, APPLICATION . 'config' . DIRECTORY_SEPARATOR);
        //Ajout de l'exemple
        copy(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'preset' . DIRECTORY_SEPARATOR . 'hello_world.php', APPLICATION . 'view' . DIRECTORY_SEPARATOR . 'hello_world.php');
        copy(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'preset' . DIRECTORY_SEPARATOR . 'hello_world.controller.php', APPLICATION . 'controller' . DIRECTORY_SEPARATOR . 'hello_world.php');
    }

    /* === Getter === */

    /**
     * Retourne l'instance du gestionnaire d'erreur
     * @return FC_Error
     */
    public function get_error() {
        return $this->error;
    }

    /**
     * Retourne l'instance du gestionnaire de logs
     * @return Logger
     */
    public function get_logger() {
        return $this->logger;
    }

    /**
     * Retourne l'instance du Fraquicom
     * @return Fraquicom
     */
    public function get_fraquicom() {
        return $this->fraquicom;
    }

    /**
     * Retourne l'instance du gestionnaire de routage
     * @return FC_Router
     */
    public function get_router() {
        return $this->router;
    }

    /**
     * Retourne l'instance du gestionnaire de chargement de fichiers 
     * @return FC_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retourne l'instance du gestionnaire d'accès
     * @return FC_Acl
     */
    public function get_acl() {
        return $this->acl;
    }

    /**
     * Retourne les valeurs des fichiers de config
     * @return array
     */
    public function get_config() {
        return new FC_Config($this->config);
    }

    /* === Méthode utilitaire === */
    
    /**
     * Creation d'un dossier si il n'existe pas
     * @param qtring $dir - Le chemin du dossier
     * @return boolean
     */
    private function make_dir($dir){
        //Si le dossier existe deja
        if(file_exists($dir)){
            return true;
        }
        //Sinon creation du dossier
        return mkdir($dir, 0777, true);
    }

    /**
     * Vide un dossier
     * @param string $folderPath - Le chemin du fichier
     * @param boolean $subfolder - Supprimer aussi les sous dossier [optional]
     * @param boolean $delete - Supprimer le dossier courant [optional]
     */
    private function clear_folder($folderPath, $subfolder = false, $delete = false) {
        //On verifie que c'est un fichier
        if (is_dir($folderPath)) {
            //On ajoute un slash a lafin si il n'y en a pas
            if ($folderPath[strlen($folderPath) - 1] != DIRECTORY_SEPARATOR) {
                $folderPath .= DIRECTORY_SEPARATOR;
            }
            //Recup tous les fichiers
            $files = array_diff(scandir($folderPath), array('..', '.'));
            //Parcours des fichiers
            foreach ($files as $file) {
                //Si ce sont des fichiers
                if (is_file($folderPath . $file)) {
                    unlink($folderPath . $file);
                }
                //Sinon ce sont des dossier et supprime seulement si subFolder = true
                else if ($subfolder) {
                    //On rapelle cette fontion pour vider le dossier
                    $this->clear_folder($folderPath . $file, true, true);
                }
            }
            //Si $delete on supprime aussi le fichier actuel
            if ($delete) {
                @rmdir($folderPath);
            }
        }
    }

    /**
     * Copie un dossier et son contenue
     * @param string $src - Dossier soruce
     * @param string $dst - Destination
     */
    private function copy_dir($src, $dst) {
        //Ajoute un un / final si besoins
        $src = $src[strlen($src) - 1] == DIRECTORY_SEPARATOR ? $src : $src . DIRECTORY_SEPARATOR;
        $dst = $dst[strlen($dst) - 1] == DIRECTORY_SEPARATOR ? $dst : $dst . DIRECTORY_SEPARATOR;
        //Copie fichier du dossier
        $dir = opendir($src);
        if(!file_exists($dst)) {
            @mkdir($dst);
        }
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    $this->copy_dir($src . $file . DIRECTORY_SEPARATOR, $dst . $file . DIRECTORY_SEPARATOR);
                } else {
                    copy($src . $file, $dst . $file);
                }
            }
        }
        closedir($dir);
    }

    private function create_dir($dirpath, $dirname) {
        //Creation du dossier
        if (mkdir($dirpath . $dirname) === false) {
            return false;
        }
        //Ajout fichier securite
        $this->copy_dir(SYSTEM . 'setup_file' . DIRECTORY_SEPARATOR . 'security' . DIRECTORY_SEPARATOR, $dirpath . $dirname);
        //Retour
        return true;
    }

}

/* --- Class Exception Fraquicom --- */

class FraquicomException extends Exception {
    
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

class LoaderException extends Exception {

    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}
