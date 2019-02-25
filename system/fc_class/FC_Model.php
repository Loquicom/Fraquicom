<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
FC_Model.php
==============================================================================*/
defined('FC_INI') or exit('Acces Denied');

Class FC_Model extends Fraquicom{
    
    /**
     * Le nom de la table lié dans la BDD
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
    
    /* === Gestion BDD === */
    
    /**
     * Change le fetch mode par defaut
     * @param string $fetchMode - Le fetchmode (array ou class)
     * @return boolean
     * @throws FraquicomException Aucune table
     */
    public function db_set_fetch_mode($fetchMode) {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->set_fetch_mode($fetchMode);
    }

    /**
     * Reinitailise la requete à zero
     * (ne change pas le fetch mode)
     * @throws FraquicomException Aucune table
     */
    public function db_reset() {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        $this->db->reset();
    }

    /**
     * Création de la condition where de la requete
     * Trois façon de l'utiliser
     * Passage d'un tableau avec clef = champ et valeur = valeur recherché ex : where(array('id' => '1'))
     * Passage de la clef et de la valeur en parametre ex : where('id', '1')
     * Passage de la clause where directement (sans le mot clef where) ex : where('id = 1 And email is null')
     * @param mixed $data - Les données
     * @param string $val - La valeur
     * @return boolean
     * @throws FraquicomException Aucune table
     */
    public function db_where($data, $val = '') {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->where($data, $val);
    }

    /**
     * Retourne tous les champs d'une table avec le where actuel
     * @param boolean $retour - Retourner le resultat (optional)
     * @return mixed
     * @throws FraquicomException Aucune table ou Probléme de requete
     */
    public function db_get($retour = true) {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->get(static::$table_name, $retour);
    }

    /**
     * Retourne tous les champs d'une table avec le where en parametre
     * @see Database::where()
     * @param string[]|string $where - Les champs/valeur pour le where | La 
     * clause where ecrite sans le mot clef where
     * @param boolean $retour - Retourner le resultat (optional)
     * @return false|mixed
     * @throws FraquicomException Aucune table
     */
    public function db_get_where($where, $retour = true) {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->get_where(static::$table_name, $where, $retour);
    }

    /**
     * Insert une ou plusieur ligne dans la base
     * 1 ligne $data = array('champ' => 'val', ...)
     * +1 lignes $data = array(array('champ' => 'val', ...), array(...))
     * @param mixed $data - Les données à insérer
     * @return false|mixed - False si echec, l'id de la ligne si réussie (sous forme de tableau si plusieur ligne)
     * @throws FraquicomException Aucune table
     */
    public function db_insert($data) {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->insert(static::$table_name, $data);
    }

    /**
     * Met à jour des champ d'une table
     * 1 ligne $id = array('id' => 'val', ...)
     * +1 lignes $id = array(array('id' => 'val', ...), array(...))
     * @param mixed $id - Le ou les id de la table
     * @param mixed $data - Les données a modifier array('champ' => 'val', ...)
     * @return boolean|boolean[] true ou false selon la reussite, en tableau si plusieurs update
     * @throws FraquicomException Aucune table
     */
    public function db_update($id, $data) {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->update(static::$table_name, $id, $data);
    }

    /**
     * Supprime des champ d'une table
     * 1 ligne $id = array('id' => 'val', ...)
     * +1 lignes $id = array(array('id' => 'val', ...), array(...))
     * @param mixed $id - Le ou les id de la table
     * @return boolean|boolean[] true ou false selon la reussite, en tableau si plusieurs delete
     * @throws FraquicomException Aucune table
     */
    public function db_delete($id) {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->delete(static::$table_name, $id);
    }

    /**
     * Retourne une ligne sous la forme du fetch mode par defaut
     * @param string $params - Parametre pour le retour
     * @return mixed
     * @throws FraquicomException Aucune table
     */
    public function db_row($params = '') {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->row($params);
    }

    /**
     * Retourne tous les resultat dans le fetch mode par defaut
     * @param string $params - Parametre pour le retour
     * @return mixed
     * @throws FraquicomException Aucune table
     */
    public function db_result($params = '') {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->result($params);
    }

    /**
     * Retourne une ligne sous forme de tableau
     * @return mixed
     * @throws FraquicomException Aucune table
     */
    public function db_row_array() {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        $this->db->row_array();
    }

    /**
     * Retourne tous les resusltats osus forme de tableau de tableau
     * @return mixed
     * @throws FraquicomException Aucune table
     */
    public function db_result_array() {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->result_array();
    }

    /**
     * Retourne une ligne de resultat sous forme d'objet
     * @param string $class - Le nom de la class
     * @return mixed
     * @throws FraquicomException Aucune table
     */
    public function db_row_class($class = 'stdClass') {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->row_class($class);
    }

    /**
     * Renvoie tous les resultas sous forme d'un tableau d'objet
     * @param string $class - Le nom de la class
     * @return mixed
     * @throws FraquicomException Aucune table
     */
    public function result_class($class = 'stdClass') {
        if(static::$table_name === null){
            throw new FraquicomException('Aucune table n\'est reliée au Model');
        }
        return $this->db->result_class($class);
    }

    /* === Méthodes héritées === */
    
    public function object($name) {
        throw new FraquicomException('Impossible d\'utiliser la methode object en mode MVC');
    }
       
}