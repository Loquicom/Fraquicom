<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  Acl.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

class Acl {

    /**
     * L'instance de la class
     * @var Acl
     */
    private static $instance = null;

    /**
     * Indique si l'on utilise ou non les acl
     * @var boolean
     */
    private $actif;

    /**
     * Tableau des acl (modifiable dans config/acl.php)
     * @var mixed
     */
    private $acl;

    /**
     * Lien vers un sous tableau de la session pour stocker les valeurs utilisé
     * pour le bon fonctionnement de la class
     * @var mixed
     */
    private $data;

    /**
     * Indique si l'on est en mvc ou non
     * @var boolean
     */
    private $mvc;

    /**
     * Constructeur privé
     */
    private function __construct() {
        global $config;
        global $_config;
        //Recup valeur
        $this->actif = $config['acl'];
        $this->acl = $config['acl_profil'];
        $this->data = & $_SESSION['_fc_data_' . $config['appli_name']];
        $this->mvc = ($_config['mode'] == 'mvc');
        //Création du tableau dans data
        if (!isset($this->data['_fc_acl'])) {
            $this->data['_fc_acl'] = array();
        }
    }

    /**
     * Recupere l'instance d'acl
     * @return Acl
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new Acl();
        }
        return self::$instance;
    }

    public function verify($url) {
        //Si les acl ne sont pas actives alors toujours true
        if(!$this->actif){
            return true;
        }
        //Recupere la liste des roles de l'utilisateur
        $user = $this->data['_fc_acl'];
        //On regarde si la page est dans un role
        $retour = true;
        foreach ($this->acl as $role => $acl){
            foreach ($acl as $page){
                $url = strtolower($url);
                $page = strtolower($page);
                //Si l'url est dans la page
                if(strpos($page, $url) !== false){
                    //Si c'est l'url exact
                    if($url == $page || substr($url, 0, strlen($url) - 1) == $page){
                        //On regarde si l'utilisateur à le role
                        if(in_array($role, $user)){
                            return true;
                        } else {
                            $retour = false;
                        }
                    }
                    //Sinon on regarde si la page finis par un /
                    else if($page[strlen($page) - 1] == '/'){
                        //On regarde si l'utilisateur à le role
                        if(in_array($role, $user)){
                            return true;
                        } else {
                            $retour = false;
                        }
                    }
                }
            }
        }
        return $retour;
    }

    /**
     * Ajoute un role à l'utilisateur courrant
     * @param string $role - Le nom du role
     * @return boolean - Réussite
     */
    public function add($role) {
        //Si pas actif on ne peux pas intéragir
        if(!$this->actif){
            return false;
        }
        //On vérifie que le role existe
        if(!$this->find_role($role)){
            return false;
        }
        //On verifie qu'il n'a pas deja le role
        if(!in_array(strtolower($role), $this->data['_fc_acl'])){
            //On ajoute
            $this->data['_fc_acl'][strtolower($role)] = strtolower($role);
        }
        return true;
    }

    /**
     * Retire un role de l'utilisateur
     * @param string $role - Le nom du role
     * @return boolean - Réussite
     */
    public function remove($role) {
        //Si pas actif on ne peux pas intéragir
        if(!$this->actif){
            return false;
        }
        //On verifie que l'utilisateur à le role
        if(in_array(strtolower($role), $this->data['_fc_acl'])){
            //On retire
            unset($this->data['_fc_acl'][strtolower($role)]);
            return true;
        }
        return false;
    }

    /**
     * Indique si l'utilisateur à un role
     * @param string $role - Le role à verifier
     * @return boolean - L'utilisateur à le role
     */
    public function check($role) {
        //Si les acl ne sont pas actives alors toujours true
        if(!$this->actif){
            return true;
        }
        //Verification de la presence du role
        return in_array(strtolower($role), $this->data['_fc_acl']);
    }
    
    private function find_role($role){
        $key = array_keys($this->acl);
        if(empty($key)){
            return false;
        }
        $key = array_map("strtolower", $key);
        return in_array(strtolower($role), $key);
    }

}
