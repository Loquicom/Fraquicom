<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Database.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

class Database {

    /**
     * array $_driverOptions Options du pilote BD.
     */
    private static $driverOptions = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );

    /**
     * L'instance de pdo
     * @var PDO
     */
    private $pdo = null;

    /**
     * Prefix des tables
     * @var string
     */
    private $prefix = '';

    /**
     * Le fetch mode par defaut
     * array, class
     * @var string
     */
    private $fetch = 'array';

    /**
     * Le where des requetes
     * @var string
     */
    private $where = '';

    /**
     * La requete sql
     * @var string
     */
    private $requete = '';

    /**
     * La requete dans pdo
     * @var PDOStatement
     */
    private $statement = null;

    /**
     * Construit une instance de PDO
     * @global mixed $config
     * @param string $dbName - Le nom de la base secondaire (vide pour la principal)
     * @throws FraquicomException - Impossible de connecter
     */
    public function __construct($dbName = '') {
        global $config;

        //On recupére les données de la bonne base
        $db = null;
        if (trim($dbName) == '') {
            $db = $config['db'];
        } else {
            if (isset($config['db']['other'][$dbName])) {
                $db = $config['db']['other'][$dbName];
            } else {
                throw new FraquicomException('Base de données inexistante : ' . $dbName);
            }
        }

        //Verifie qu'il y a bien une base de donnée parametrée
        if (trim($db['host']) != '' && $db['name'] != '') {
            //MySQL
            if ($db['type'] == 'mysql') {
                try {
                    $host = explode(':', $db['host']);
                    if (count($host) > 1) {
                        $this->pdo = new PDO('mysql:host=' . $host[0] . ';port=' . $host[1] . ';dbname=' . $db['name'] . ';charset=utf8', $db['login'], $db['pass'], self::$driverOptions);
                    } else {
                        $this->pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';charset=utf8', $db['login'], $db['pass'], self::$driverOptions);
                    }
                } catch (Exception $ex) {
                    throw new FraquicomException('Impossible de se connecter à la base : ' . $ex->getMessage());
                }
            }
            //SQLite
            else if ($db['type'] == 'sqlite') {
                try {
                    $this->pdo = new PDO('sqlite:' . $db['host'] . $db['name'], null, null, self::$driverOptions);
                } catch (Exception $ex) {
                    throw new FraquicomException('Impossible de se connecter à la base : ' . $ex->getMessage());
                }
            }
            //Oracle
            else if ($db['type'] == 'oracle') {
                try {
                    $this->pdo = new PDO('ori:dbname=//' . $db['host'] . '/' . $db['name'] . ';charset=utf8', $db['login'], $db['pass'], self::$driverOptions);
                } catch (Exception $ex) {
                    throw new FraquicomException('Impossible de se connecter à la base : ' . $ex->getMessage());
                }
            }
            //PostgreSQL
            else if ($db['type'] == 'postgresql') {
                try {
                    $host = explode(':', $db['host']);
                    if (count($host) > 1) {
                        $this->pdo = new PDO('pgsql:host=' . $host[0] . ';port=' . $host[1] . ';dbname=' . $db['name'] . ';user=' . $db['login'] . ';password=' . $db['pass'], null, null, self::$driverOptions);
                    } else {
                        $this->pdo = new PDO('pgsql:host=' . $db['host'] . ';dbname=' . $db['name'] . ';user=' . $db['login'] . ';password=' . $db['pass'], null, null, self::$driverOptions);
                    }
                } catch (Exception $ex) {
                    throw new FraquicomException('Impossible de se connecter à la base : ' . $ex->getMessage());
                }
            }
            //Si aucun type erreur
            else {
                throw new FraquicomException('Type de base de données incorect');
            }
            //Ajout prefix
            $this->prefix = $db['prefix'];
        } else {
            return null;
        }
    }

    /**
     * Une instance d'un base secondaire
     * @param string $dbName
     * @return boolean|\Database
     */
    public function get_other_db($dbName) {
        if (trim($dbName) != '') {
            return new Database($dbName);
        }
        return false;
    }

    /**
     * Change le fetch mode par defaut
     * @param string $fetchMode - Le fetchmode (array ou class)
     * @return boolean
     */
    public function set_fetch_mode($fetchMode) {
        $fetchMode = strtolower($fetchMode);
        if (in_array($fetchMode, array('array', 'class'))) {
            $this->fetch = $fetchMode;
            return true;
        }
        return false;
    }

    /**
     * Reinitailise la requete à zero
     * (ne change pas le fetch mode)
     */
    public function reset() {
        $this->where = '';
        $this->requete = '';
        $this->statement = null;
    }

    /**
     * Création de la condition where de la requete
     * Trois façon de l'utiliser
     * Passage d'un tableau avec clef = champ et valeur = valeur recherché ex : where(array('id' => '1'))
     * Passage de la clef et de la valeur en parametre ex : where('id', '1')
     * Pasage de la clause where directement (sans le mot clef wehere) ex : where('id = 1 And email is null')
     * @param mixed $data - Les données
     * @param string $val - La valeur
     * @return boolean
     */
    public function where($data, $val = '') {
        //Si on utilise en mode where(champ, valeur)
        if (trim($val) != '' && is_string($data)) {
            $this->where = " Where Upper(" . $data . ") = '" . strtoupper($val) . "'";
            return true;
        }
        //Sinon si on utilise en mode where(array(champ => val, ...))
        else if (is_array($data) && !empty($data)) {
            $first = true;
            foreach ($data as $champ => $valeur) {
                if ($first) {
                    $this->where = " Where Upper(" . $champ . ") = '" . strtoupper($valeur) . "'";
                    $first = false;
                } else {
                    $this->where .= " And Upper(" . $champ . ") = '" . strtoupper($valeur) . "'";
                }
            }
            return true;
        }
        //Sinon si $data est un string c'est une clause where deja ecrite
        else if (is_string($data)) {
            $this->where = " Where " . $data;
        }
        return false;
    }

    /**
     * Retourne tous les champs d'une table avec le where actuel
     * @param string $table - Le nom de la table
     * @param boolean $retour - Retourner le resultat
     * @return mixed
     * @throws FraquicomException - Probléme de requete
     */
    public function get($table, $retour = true) {
        //Creation de la requete
        $this->requete = "Select * From " . $this->prefix . $table;
        $this->requete .= $this->where;
        if ($this->execute() === false) {
            return false;
        }
        //Si on retourne directement
        if ($retour) {
            return $this->result();
        }
        return true;
    }

    /**
     * Retourne tous les champs d'une table avec le where en parametre
     * @see Database::where()
     * @param string $table - Le nom de la table
     * @param string[]|string $where - Les champs/valeur pour le where | La clause where ecrite sans le mot clef where
     * @param boolean $retour - Retourner le resultat
     * @return false|mixed
     */
    public function get_where($table, $where, $retour = true) {
        //Ajout du where
        if ($this->where($where) === false) {
            return false;
        }
        //Création de la requete
        $this->requete = "Select * From " . $this->prefix . $table;
        $this->requete .= $this->where;
        if ($this->execute() === false) {
            return false;
        }
        if ($retour) {
            return $this->result();
        }
        return true;
    }

    /**
     * Insert une ou plusieur ligne dans la base
     * 1 ligne $data = array('champ' => 'val', ...)
     * +1 lignes $data = array(array('champ' => 'val', ...), array(...))
     * @param string $table - Le nom de la table
     * @param mixed $data - Les données à insérer
     * @return false|mixed - False si echec, l'id de la ligne si réussie (sous forme de tableau si plusieur ligne)
     */
    public function insert($table, $data) {
        //Si il y a plusieurs insert à faire
        if (isset($data[0]) && is_array($data[0])) {
            //Tableau avec les resultat pour chaque insert
            $result = array();
            foreach ($data as $tab) {
                $result[] = $this->insert($table, $tab);
            }
            return $result;
        }
        //Sinon si il n'y en a qu'un
        else {
            $champs = '';
            $vals = '';
            foreach ($data as $champ => $val) {
                $champs .= $champ . ",";
                $vals .= "'" . $val . "',";
            }
            $this->requete = 'Insert into ' . $this->prefix . $table . '(' . rtrim($champs, ',') . ') Values (' . rtrim($vals, ',') . ');';
            if ($this->execute() === false) {
                return false;
            }
            $this->reset();
            return $this->pdo->lastInsertId();
        }
    }

    /**
     * Met à jour des champ d'une table
     * 1 ligne $id = array('id' => 'val', ...)
     * +1 lignes $id = array(array('id' => 'val', ...), array(...))
     * @param string $table - Le nom de la table
     * @param mixed $id - Le ou les id de la table
     * @param mixed $data - Les données a modifier array('champ' => 'val', ...)
     * @return boolean|boolean[] true ou false selon la reussite, en tableau si plusieurs update
     */
    public function update($table, $id, $data) {
        //Si il y a plusieurs update a faire
        if (isset($id[0]) && is_array($id[0])) {
            $result = array();
            foreach ($id as $tab) {
                $result[] = $this->update($table, $tab, $data);
            }
            return $result;
        }
        //Sinon si il n'y en a qu'un
        else {
            //Conception du where avec le ou les id
            $where = ' Where';
            foreach ($id as $champ => $val) {
                $where .= " Upper(" . $champ . ") = '" . strtoupper($val) . "' And";
            }
            $where = rtrim($where, 'And');
            //Conception du set
            $set = '';
            foreach ($data as $champ => $val) {
                $set .= $champ . " = '" . $val . "'";
            }
            //Ecriture de la requete
            $this->requete = "Update " . $this->prefix . $table . " Set " . $set . $where;
            $result = $this->execute();
            $this->reset();
            return $result;
        }
    }

    /**
     * Supprime des champ d'une table
     * 1 ligne $id = array('id' => 'val', ...)
     * +1 lignes $id = array(array('id' => 'val', ...), array(...))
     * @param string $table - Le nom de la table
     * @param mixed $id - Le ou les id de la table
     * @return boolean|boolean[] true ou false selon la reussite, en tableau si plusieurs delete
     */
    public function delete($table, $id) {
        //Si il y a plusieurs delete a faire
        if (isset($id[0]) && is_array($id[0])) {
            $result = array();
            foreach ($id as $tab) {
                $result[] = $this->delete($table, $tab);
            }
            return $result;
        }
        //Sinon si il n'y en a qu'un
        else {
            //Conception du where avec le ou les id
            $where = ' Where';
            foreach ($id as $champ => $val) {
                $where .= " Upper(" . $champ . ") = '" . strtoupper($val) . "' And";
            }
            $where = rtrim($where, 'And');
            //Ecriture de la requete
            $this->requete = 'Delete From ' . $this->prefix . $table . $where;
            $result = $this->execute();
            $this->reset();
            return $result;
        }
    }

    /**
     * Retourne une ligne sous la forme du fetch mode par defaut
     * @param string $params - Parametre pour le retour
     * @return mixed
     */
    public function row($params = '') {
        if ($this->fetch == 'array') {
            return $this->row_array();
        } else {
            if (trim($params) == '') {
                $params = 'stdClass';
            }
            return $this->row_class($params);
        }
    }

    /**
     * Retourne tous les resultat dans le fetch mode par defaut
     * @param string $params - Parametre pour le retour
     * @return mixed
     */
    public function result($params = '') {
        if ($this->fetch == 'array') {
            return $this->result_array();
        } else {
            if (trim($params) == '') {
                $params = 'stdClass';
            }
            return $this->result_class($params);
        }
    }

    /**
     * Retourne une ligne sous forme de tableau
     * @return mixed
     */
    public function row_array() {
        if ($this->statement !== null) {
            $result = $this->statement->fetch();
            if ($result === false) {
                //Si il n'y a plus de resultat on reset le requete
                $this->reset();
            } else {
                //Correction resultat doublon
                foreach ($result as $key => $val) {
                    if (is_int($key) && !is_string($key)) {
                        unset($result[$key]);
                    }
                }
            }
            return $result;
        }
        return false;
    }

    /**
     * Retourne tous les resusltats osus forme de tableau de tableau
     * @return mixed
     */
    public function result_array() {
        if ($this->statement !== null) {
            $result = $this->statement->fetchAll();
            if ($result !== false) {
                //Correction resultat doublon
                foreach ($result as $num => $line) {
                    foreach ($line as $key => $val) {
                        if (is_int($key) && !is_string($key)) {
                            unset($result[$num][$key]);
                        }
                    }
                }
            }
            //On reset la requete
            $this->reset();
            return $result;
        }
        return false;
    }

    /**
     * Retourne une ligne de resultat sous forme d'objet
     * @param string $class - Le nom de la class
     * @return mixed
     */
    public function row_class($class = 'stdClass') {
        if ($this->statement !== null) {
            $result = $this->statement->fetchObject($class);
            if ($result === false) {
                //Si il n'y a plus de resultat on reset le requete
                $this->reset();
            }
            return $result;
        }
        return false;
    }

    /**
     * Renvoie tous les resultas sous forme d'un tableau d'objet
     * @param string $class - Le nom de la class
     * @return mixed
     */
    public function result_class($class = 'stdClass') {
        if ($this->statement !== null) {
            $result = $this->statement->fetchAll(PDO::FETCH_CLASS, $class);
            //On reset la requete
            $this->reset();
            return $result;
        }
        return false;
    }

     /**
     * Securise et execute une requete sql
     * @param string $sql - Une requete sql, si vide prend celle de la class
     * @param boolean $excep - Renvoie ou non l'exception si il y en a une (renvoie false sinon)
     * @param boolean $statement - Renvoie le PDOstatement si pas d'erreur (sinon renvoie le resultat de pdostm->execute())
     * @return boolean|string|PDOstatement
     */
    public function execute($sql = '', $excep = false, $statement = false) {
        //Si il y a une requete en parametre on l'execute
        if (trim($sql) != '') {
            try {
                $this->statement = $this->pdo->prepare($sql);
                if($statement){
                    $this->statement->execute();
                    return $this->statement;
                }
                return $this->statement->execute();
            } catch (Exception $ex) {
                if($excep){
                    return $ex->getMessage();
                }
                return false;
            }
        }
        //Sinon on exceute la requete de l'instance
        else if (trim($this->requete) != '') {
            try {
                $this->statement = $this->pdo->prepare($this->requete);
                if($statement){
                    $this->statement->execute();
                    return $this->statement;
                }
                return $this->statement->execute();
            } catch (Exception $ex) {
                if($excep){
                    return $ex->getMessage();
                }
                return false;
            }
        } else {
            return false;
        }
    }

}
