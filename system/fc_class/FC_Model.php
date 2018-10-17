<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
FC_Model.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

Class FC_Model extends Fraquicom{
    
    /**
     * Le nom de la table lié dans le BDD
     * @var null|string - Null si non definit, sinon un string avec le nom
     */
    protected static $table_name = null;
    
    /**
     * Le nom de la base qui contient la table
     * @var null|string - Null si base par defaut sinon un string avec le nom
     */
    protected static $db_name = null;

    /**
     * Instance de Database à utiliser
     * @var Database
     */
    protected $db;
    
    /**
     * Prefixe des tables dans la base
     * @var string
     */
    protected $prefix;

    /**
     * Constructeur
     * Charge le gestionnaire de BDD si besoins
     */
    public function __construct() {
        parent::__construct();
        //Si gestion d'une table
        if(static::$table_name !== null){
            //Charge la librairie de gestion de BDD
            $this->load->library('database');
            //Charge la bonne base et le prefixe
            global $config;
            if(static::$db_name !== null){
                $this->db = $this->database->get_other_db(static::$db_name);
                $this->prefix = $config['db']['other'][static::$db_name]['prefix'];
            } else {
                $this->db = $this->database;
                $this->prefix = $config['db']['prefix'];
            }
            //Verif que la table existe
            if(!$this->db->get(static::$table_name)){
                throw new FraquicomException('La table ' . self::$table_name . 'n\'existe pas');
            }
        }
    }

    /* === Méthodes héritées === */
    
    public function object($name) {
        throw new FraquicomException('Impossible d\'utiliser la methode object en mode MVC');
    }
       
}