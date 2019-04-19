<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  acl.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

$config['acl_profil'] = array();
$acl = & $config['acl_profil'];

/*
 * Indique si la gestion des ACL est en mode stricte
 * En mode stricte si une page n'est dans aucun role elle est inaccessible
 * En mode stricte selle est accessible par tous le monde
 */
$config['acl_strict'] = false;

/*
 * Pour definir un role simplement faire :
 * $acl['role'] = array(...)
 * 
 * Dans l'array indiquer les différentes pages autorisé pour le role
 * il faut indiquer l'url, si l'url finis par un / on accepte toutes les url
 * commençant comme indiquer, sinon on accepte que la page.
 * Pour rappel les url sont composées de la façon suivante :
 * mvc : Site/Controller/Methode/Param1/Param2/.../ParamN
 * non-mvc Site/Dossier1/Dossier2/.../DossierN/Fichier
 * 
 * Si une page n'apparait pas, elle est accessible pour tous le monde.
 * Pour donner un role à un utilisateur utiliser $this->error->add('role)
 */


unset($acl);