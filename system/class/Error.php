<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Error.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

class Error {

    private static $instance = null;
    
    private static $exception = false;
    
    private static $custom_handler = null;

    private function __construct() {
        //Changement des fonctions d'erreur/exception
        register_shutdown_function("Error::shutdown");
        set_error_handler("Error::error_handler");
        set_exception_handler("Error::exception_handler");
    }

    public static function get_instance() {
        if (self::$instance === null) {
            return self::$instance = new Error();
        }
        return self::$instance;
    }

    /*
     * Méthode ajout erreur, ...
     * Gestion log depuis ici ?
     */

    /* ----- Méthode parametrage gestion erreur ----- */
    
    public function use_custom_handler($callback){
        self::$custom_handler = $callback;
    }
    
    public function reset_handler(){
        self::$custom_handler = null;
    }
    
    /* ----- Méthode gestion des erreurs ----- */
    
    /**
     * Methode appelé lors de l'arret 
     */
    public static function shutdown() {
        //Si la connexion a été abandonnée par le client
        if(connection_aborted()){
            return;
        }
        //Si l'arret est lié à une erreur
        if(error_get_last() !== null){
            //ToDo Log erreur
            echo 'err';
            return;
        }
        if(self::$exception){
            //ToDo Log exception
            echo 'excpt';
            self::$exception = false;
            return;
        }
        //ToDo si arret quand tous est ok
        echo 'ok';
    }

    /**
     * Methode appelé en cas d'erreur
     * @param int $errno - Le type d'erreur
     * @param string $errstr - Le libelle de l'erreur
     * @param string $errfile - Le fichier de l'erreur
     * @param int $errline - Le ligne de l'erreur
     * @return false - Execute le système de gestion des erreurs de PHP
     */
    public static function error_handler($errno, $errstr, $errfile, $errline) {
        echo 'Erreur';
        return false;
    }

    /**
     * 
     * @param type $exception
     */
    public static function exception_handler($exception) {
        echo 'Exception';
        //Il y a eu une exception
        self::$exception = true;
    }

}
