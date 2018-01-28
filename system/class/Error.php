<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Error.php
  ============================================================================ */
defined('FC_INI') or exit('Acces Denied');

define("E_EXCEPTION", 0);

class Error {

    /**
     * L'instance de Error
     * @var Error
     */
    private static $instance = null;

    /**
     * L'arret vient t'il d'une exception
     * @var boolean
     */
    private static $exception = false;

    /**
     * Trace de la derniere exception
     * @var array
     */
    private static $excpt_trace = null;

    /**
     * Fonction d'erreur de l'utilisateur
     * @var function
     */
    private static $custom_handler = null;
    
    /**
     * Fonction a appeler en cas d'erreur
     * @var function
     */
    private static $action_error = null;
    
    /**
     * Fonction a appeler en cas d'exception
     * @var function
     */
    private static $action_exception = null;
    
    /**
     * Fonction a appeler en cas de connexion abandonnée
     * @var function
     */
    private static $action_aborted = null;
    
    /**
     * Fonction a appeler quand il n'y a aucun probleme
     * @var function
     */
    private static $action_ok = null;

    /**
     * Utilisez ou non le gestionnaire d'erreur de php
     * @var boolean
     */
    private static $use_php_error = false;

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

    /**
     * Lance une erreur
     * @param string $msg - Le message de l'erreur
     * @param string $file - Le fichier (utiliser __FILE__)
     * @param string $line - La ligne (utiliser __LINE__)
     * @param boolean $isWarning - Si c'est un warning ou une error
     */
    public function trigger($msg, $file = 'Unknow file', $line = 'Unknow line', $isWarning = false) {
        //Selectionne le bon type d'erreur
        $no = ($isWarning) ? E_USER_WARNING : E_USER_ERROR;
        //Si on utilise la gestion d'erreur du Fraquicom
        if (!self::$use_php_error) {
            //Lance l'erreur via le gestionnaire
            self::error_handler($no, $msg, $file, $line);
        } else {
            //On lance une erreur php
            trigger_error($msg, $no);
        }
    }

    /**
     * Ajoute une erreur dans les log
     * @param int $type - Le type d'erreur à ajouter (utiliser les constantes E_)
     * @param string $msg - Le message de l'erreur
     * @param string $file - Le fichier (utiliser __FILE__)
     * @param string $line - La ligne (utiliser __LINE__)
     * @param array $trace - Trace de l'éxecution, tableau de tableau. Chaque 
     * sous tableau doit contenir un champ function, si les champs class, type,
     * file et line existe ils sont pris en compte (pour plus d'info voir
     * debug_backtrace)
     * @return false
     */
    public function add($type, $msg, $file = 'Unknow file', $line = 'Unknow line', $trace = array()) {
        //Debut log
        $fc = get_instance();
        $fc->log->startLog(date('H:i:s') . '(' . time() . ')');
        $fc->log->addLine('Error add by $this->error->add()', 'info');
        $fc->log->addLine(self::get_type_error($type) . " : " . $msg . " (" . $file . ", line " . $line . ")", ($type == E_ERROR || $type == E_USER_ERROR || $type == E_EXCEPTION) ? 'err' : 'warn');
        //Ajout trace
        if (!empty($trace)) {
            $i = 1;
            foreach ($trace as $t) {
                //Recup nom de la fonction
                $fonction = $t['function'] . '()';
                if (isset($t['class'])) {
                    $fonction = $t['class'] . $t['type'] . $fonction;
                }
                //Autre info
                $file = (isset($t['file'])) ? $t['file'] : 'Unknown file';
                $line = (isset($t['line'])) ? $t['line'] : 'Unknown line';
                //Ajout ligne log
                $fc->log->addLine("Trace #" . $i++ . " : " . $fonction . " (" . $file . ", line " . $line . ")");
            }
        }
        $fc->log->endLog();
        return false;
    }

    /* ----- Méthode parametrage gestion erreur ----- */

    /**
     * Permet d'utiliser un handler personalisé pour les erreurs
     * Les parametre envoyé à la fonction sont :
     *   - Le numero de l'erreur
     *   - Le libelle de l'erreur
     *   - Le fichier de l'erreur
     *   - La ligne de l'erreur
     *   - La trace jusqu'a l'erreur
     * /!\ En cas d'utilisation d'un handler personalisé, les messages
     * d'erreurs version Fraquicom ne s'afficherons plus
     * @param function $callback - La fonction à utiliser
     */
    public function use_custom_handler($callback) {
        self::$custom_handler = $callback;
    }

    /**
     * Reset le handler par defaut (celui du Fraquicom)
     */
    public function reset_handler() {
        self::$custom_handler = null;
    }

    /**
     * Indique si lon utilise le systeme de gestion d'erreur de php ou non
     * @param boolean $bool
     */
    public function use_php_error($bool) {
        self::$use_php_error = (boolean) $bool;
    }
    
    /**
     * Change l'action à faire en cas d'erreur
     * @param function $callback - La fonction à appeler (laisse vide pour retirer)
     */
    public function set_action_error($callback = null){
        self::$action_error = $callback;
    }
    
    /**
     * Change l'action à faire en cas d'exception
     * @param function $callback - La fonction à appeler (laisse vide pour retirer)
     */
    public function set_action_exception($callback = null){
        self::$action_exception = $callback;
    }
    
    /**
     * Change l'action à faire en cas d'abandon de connexion
     * @param function $callback - La fonction à appeler (laisse vide pour retirer)
     */
    public function set_action_aborted($callback = null){
        self::$action_aborted = $callback;
    }
    
    /**
     * Change l'action à faire quand il n'y a aucun probleme
     * @param function $callback - La fonction à appeler (laisse vide pour retirer)
     */
    public function set_action_ok($callback = null){
        self::$action_ok = $callback;
    }

    /* ----- Méthode gestion des erreurs ----- */

    /**
     * Methode appelé lors de l'arret 
     */
    public static function shutdown() {
        //Recuperation fraquicom
        $fc = get_instance();
        //Si la connexion a été abandonnée par le client
        if (connection_aborted()) {
            //Si une action à faire
            if(self::$action_aborted !== null){
                $function = self::$action_aborted;
                $function();
            }
        }
        //Si arret exception
        else if (self::$exception) {
            self::$exception = false;
            //Si une action à faire
            if(self::$action_exception !== null){
                $function = self::$action_exception;
                $function();
            }
        }
        //Si l'arret est lié à une erreur
        else if (error_get_last() !== null) {
            //Si une action à faire
            if(self::$action_error !== null){
                $function = self::$action_error;
                $function();
            }
        }
        //Si arret quand tous est ok
        else {
            //Si une action à faire
            if(self::$action_ok !== null){
                $function = self::$action_ok;
                $function();
            }
        }
        //On fini le log
        $fc->log->writeLog();
    }

    /**
     * Methode appelé en cas d'erreur
     * @param int $errno - Le type d'erreur
     * @param string $errstr - Le libelle de l'erreur
     * @param string $errfile - Le fichier de l'erreur
     * @param int $errline - Le ligne de l'erreur
     * @return boolean - Pour executer ou non le système de gestion des erreurs
     *  de PHP
     */
    public static function error_handler($errno, $errstr, $errfile, $errline) {
        //Récupération de la trace
        $trace = array();
        if (self::$exception) {
            //Si l'appel viens d'une exception on regarde si il à fournis sa trace
            if (self::$excpt_trace !== null) {
                $trace = self::$excpt_trace;
                self::$excpt_trace = null;
            }
        } else {
            //Sinon recup de la trace
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            array_shift($trace);
        }
        //Si on apelle une fonction custom
        if (self::$custom_handler !== null) {
            $handler = self::$custom_handler;
            $handler($errno, $errstr, $errfile, $errline, $trace);
        } else if (!self::$use_php_error) {
            //Affiche uniquement si l'on utilise pas la gestion d'erreur php
            echo self::html_error(self::get_type_error($errno), $errstr, $errfile, $errline, $trace);
        }
        //Ajoute dans le log l'erreur
        $fc = get_instance();
        $fc->log->startLog(date('H:i:s') . '(' . time() . ')');
        $fc->log->addLine(self::get_type_error($errno) . " : " . $errstr . " (" . $errfile . ", line " . $errline . ")", ($errno == E_ERROR || $errno == E_USER_ERROR || $errno == E_EXCEPTION) ? 'err' : 'warn');
        $i = 1;
        foreach ($trace as $t) {
            //Recup nom de la fonction
            $fonction = $t['function'] . '()';
            if (isset($t['class'])) {
                $fonction = $t['class'] . $t['type'] . $fonction;
            }
            //Autre info
            $file = (isset($t['file'])) ? $t['file'] : 'Unknown file';
            $line = (isset($t['line'])) ? $t['line'] : 'Unknown line';
            //Ajout ligne log
            $fc->log->addLine("Trace #" . $i++ . " : " . $fonction . " (" . $file . ", line " . $line . ")");
        }
        $fc->log->endLog();
        //Retour
        return !self::$use_php_error;
    }

    /**
     * Methode appelé en cas d'exception
     * @param Exception $exception - L'exception
     */
    public static function exception_handler($exception) {
        //Il y a eu une exception
        self::$exception = true;
        self::$excpt_trace = $exception->getTrace();
        //On lance un erreur
        if (!self::$use_php_error) {
            //On lance l'erreur par la fonction du fraquicom
            self::error_handler(E_EXCEPTION, $exception->getMessage(), $exception->getFile(), $exception->getLine());
        } else {
            //On trigger l'erreur
            trigger_error($exception->getMessage(), E_USER_ERROR);
        }
    }

    public static function get_type_error($no) {
        switch ($no) {
            case E_EXCEPTION:
                $liberr = "EXCEPTION";
                break;
            case E_NOTICE:
                $liberr = "NOTICE";
                break;
            case E_STRICT:
                $liberr = "STRICT";
                break;
            case E_USER_NOTICE:
                $liberr = "USER_NOTICE";
                break;
            case E_WARNING:
                $liberr = "WARNING";
                break;
            case E_USER_WARNING:
                $liberr = "USER_WARNING";
                break;
            case E_DEPRECATED:
                $liberr = "DEPRECATED";
                break;
            case E_USER_DEPRECATED:
                $liberr = "USER_DEPRECATED";
                break;
            case E_ERROR:
                $liberr = "ERROR";
                break;
            case E_USER_ERROR:
                $liberr = "USER_ERROR";
                break;
            default:
                $liberr = "NUMERO" . $no;
                break;
        }
        return $liberr;
    }

    private static function html_error($errlib, $errstr, $errfile, $errline, $trace) {
        $html = <<<HTML
<div style="max-width: 80vw; margin: auto">
    <div style="border: black dashed 2px; box-shadow: 0 6px 10px 0 rgba(0,0,0,0.14), 0 1px 18px 0 rgba(0,0,0,0.12), 0 3px 5px -1px rgba(0,0,0,0.3);">
        <div style="padding-left: 1em; border-bottom: black dashed 2px; background-color: #ffecb3">
            <img src="http://img.loquicom.fr/fraquicom.svg" width="40" height="40" alt="Logo Fraquicom" style="display: inline-block">
            <div style="display: inline-block; position: relative"><strong style="position: absolute; top: -25px">Fraquicom</strong></div>
        </div>
        <div style="background-color: #fff8e1">
            <div style="line-height: 0.5em; padding-top: 1em; padding-bottom: 1em;">
                <div style="padding-left: 1em;">
                    <h3 style="display: inline-block">{$errlib} :</h3> <br> <span style="padding-left: 2em">{$errstr}</span>
                    <br>
                    <h3 style="display: inline-block">File :</h3> <br> <span style="padding-left: 2em">{$errfile}</span>
                    <br>
                    <h3 style="display: inline-block">Line :</h3> <br> <span style="padding-left: 2em">{$errline}</span>
                </div>
            </div>
            <div style="border-top: black dashed 2px; padding-bottom: 1em">
                <h3 style="padding-left: 1em">Call stack :</h3>
                <table style="width: 90%; margin: auto; border-spacing: 0px">
                    <thead>
                        <tr>
                            <th style="border-bottom: black 1px solid">#</th>
                            <th style="border-bottom: black 1px solid">Function</th>
                            <th style="border-bottom: black 1px solid">File</th>
                            <th style="border-bottom: black 1px solid">Line</th>
                        </tr>
                    </thead>
                    <tbody style="text-align: center;">
HTML;
        //Generation de la trace
        $style = "";
        $i = 1;
        foreach ($trace as $t) {
            //Recup nom de la fonction
            $fonction = $t['function'] . '()';
            if (isset($t['class'])) {
                $fonction = $t['class'] . $t['type'] . $fonction;
            }
            //Autre info
            $file = (isset($t['file'])) ? $t['file'] : 'Unknown file';
            $line = (isset($t['line'])) ? $t['line'] : 'Unknown line';
            //Code html
            $html .= <<<HTML
                        <tr{$style}>
                            <td>{$i}</td>
                            <td>{$fonction}</td>
                            <td>{$file}</td>
                            <td>{$line}</td>
                        </tr>
HTML;
            //Changement ou nom de couleur de ligne
            if (trim($style) == "") {
                $style = ' style="background-color: #ffecb3"';
            } else {
                $style = '';
            }
            //Incrementation i
            $i++;
        }
        //Si pas de trace
        if (empty($trace)) {
            $html .= <<<HTML
                    <tr><td colspan="100%"><h3>Empty</h3></td></tr>
HTML;
        }
        $html .= <<<HTML
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>           
HTML;
        //Retour
        return $html;
    }

}
