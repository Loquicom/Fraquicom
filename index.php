<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  index.php
  ============================================================================ */

//Création des variables global
global $_config; //Config system
global $config; //Config application
global $_S; //Session
global $_setup; //Indique si il faut setup le dossier d'aplication
$_setup = true;
//Chargement valeur $_config
if (file_exists('./system/config/local.php')) {
    require './system/config/local.php';
}

//Si aucun fichier de config locale création et de htaccess
if (!(file_exists('./system/config/local.php') && file_exists('./.htaccess') && file_exists('./fraquicom.ini'))) {
    //Si application est deja rempli on recré uniquement les dossier manquants
    if (count(array_diff(scandir('./application/'), array('..', '.', '.htaccess', 'index.html'))) > 0) {
        $_setup = false;
    }
    require './system/_setup.php';
    //On relance se script
    header('Location: ./');
    exit;
}
//Sinon on verifie que le fichier fraquicom.ini n'a pas changé
else {
    if (md5_file('./fraquicom.ini') != $_config['md5']) {
        //On lance le script de setup
        require './system/_setup.php';
        //On relance se script
        header('Location: ./');
        exit;
    }
}

//Chargement du fichier d'initialisation
require './system/_ini.php';

//Récupération d'une instance de Fraquicom
$fraquicom = get_instance();

//Recupéaration de l'url
if (isset($_GET['_fc_r'])) {
    $url = $_GET['_fc_r'];
    unset($_GET['_fc_r']);
} else {
    $url = '';
}
//Si il n'y a aucune url
if (trim($url) == '') {
    //Routage vers la methode par defaut
    $url = $config['route']['index'];
}
//Ajout des parametre get du l'url dans $_GET
$getParams = explode('?' ,$_SERVER['REQUEST_URI']);
if(isset($getParams[1])){
    //Si il y a des parametres get
    $getParams = explode('&', $getParams[1]);
    foreach ($getParams as $getParam){
        $getParam = explode('=', $getParam);
        $_GET[$getParam[0]] = $getParam[1];
    }
}

//Ajout dans la variable $_config du script appelé
$_config['current_script'] = $url;
//En mvc
if ($_config['mode'] == 'mvc') {
    //Découpage de l'url
    $url = explode('/', $url);
    for ($i = 0; $i < count($url); $i++) {
        if (trim($url[$i]) == '') {
            unset($url[$i]);
        }
    }
    //Si il n'y a qu'un element c'est la controller, on appel sa methode index
    if (count($url) == 1) {
        if ($fraquicom->load->controller($url[0]) === false) {
            //Si le controller n'existe pas
            if (trim($config['route']['404']) != '') {
                //Si il y a une page 404 indiqué dans le fichier de config on l'utilise
                exit($fraquicom->load->view($config['route']['404'], null, true));
            } else {
                //Sinon on prend celle par defaut
                exit(file_get_contents('./system/file/404.html'));
            }
        }
        $fraquicom->controller($url[0])->index();
    }
    //Si il y a plus c'est la méthode et ses parametres
    else {
        //Chargement du controller
        if ($fraquicom->load->controller($url[0]) === false) {
            //Si le controller n'existe pas
            if (trim($config['route']['404']) != '') {
                //Si il y a une page 404 indiqué dans le fichier de config on l'utilise
                exit($fraquicom->load->view($config['route']['404'], null, true));
            } else {
                //Sinon on prend celle par defaut
                exit(file_get_contents('./system/file/404.html'));
            }
        }
        //On verifie que la méthode existe
        if (!method_exists($fraquicom->controller($url[0]), $url[1])) {
            //Si la méthode n'existe pas
            if (trim($config['route']['404']) != '') {
                //Si il y a une page 404 indiqué dans le fichier de config on l'utilise
                exit($fraquicom->load->view($config['route']['404'], null, true));
            } else {
                //Sinon on prend celle par defaut
                exit(file_get_contents('./system/file/404.html'));
            }
        }
        //Recupération des parametres
        $params = $url;
        unset($params[0]);
        unset($params[1]);
        //On verifie que le nombre de parametre correspond à celui de la methode appelé
        $methodParams = new ReflectionMethod($fraquicom->controller($url[0]), $url[1]);
        if ($methodParams->getNumberOfRequiredParameters() > count($params)) {
            //Pas assez de parametre donc redirection 404
            if (trim($config['route']['404']) != '') {
                //Si il y a une page 404 indiqué dans le fichier de config on l'utilise
                exit($fraquicom->load->view($config['route']['404'], null, true));
            } else {
                //Sinon on prend celle par defaut
                exit(file_get_contents('./system/file/404.html'));
            }
        }
        //Sinon appel de la méthode
        call_user_func_array(array($fraquicom->controller($url[0]), $url[1]), $params);
    }
}
//En non mvc
else {
    //Création d'un variable courte avec une instance de Fraquicom
    $fc = & $fraquicom;
    //Si le chemin se termine par un slash on cherche index.php
    if ($url[strlen($url) - 1] == '/') {
        $url .= 'index';
    }
    //Si le fichier existe
    if (file_exists('./application/' . $url . '.php')) {
        require './application/' . $url . '.php';
    } else {
        if (trim($config['route']['404']) != '') {
                //Si il y a une page 404 indiqué dans le fichier de config on l'utilise
                exit($fraquicom->load->file($config['route']['404'], null, true));
            } else {
                //Sinon on prend celle par defaut
                exit(file_get_contents('./system/file/404.html'));
            }
    }
}