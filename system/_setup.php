<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  _setup.php
  ============================================================================ */

//Verifie qu'il est possible d'ecrire
if (!is_writable('./')) {
    //Erreur impossible d'écrire
    exit("Impossible de créer les fichiers de configurations");
}

//Verification que le fichier json existe
if (!file_exists('./fraquicom.json')) {
    //Création du fichier
    $file = fopen('./fraquicom.json', 'w');
    if ($file === false) {
        //Erreur impossible d'écrire
        exit("Impossible de créer les fichiers de configurations");
    }
    fwrite($file, json_encode(array(
        'config' => array(
            'appli_name' => 'Fraquicom',
            'data_path' => '',
            'tmp_path' => ''
        ),
        'mode' => array(
            'mvc' => 'on'
        ),
        'route' => array(
            'routage_asset' => 'on'
        ),
        'root' => array(
            'fileroot' => '',
            'webroot' => ''
        )
    ), JSON_PRETTY_PRINT));
    fclose($file);
}

//Chargement du fichier json
$data = json_decode(file_get_contents('./fraquicom.json'), JSON_OBJECT_AS_ARRAY);

//Si on setup le dossier application
if ($_setup) {
//Préparation en fonction du mode choisie
    if ($data['mode']['mvc'] == 'on') {
        //Backup avant supression si il y a qqchose a backup
        if (!file_exists('./_backup/') && count(array_diff(scandir('./application/'), array('..', '.', '.htaccess', 'index.html'))) > 0) {
            @mkdir('./_backup/');
        } else {
            clear_folder('./_backup', true);
        }
        if (count(array_diff(scandir('./application/'), array('..', '.', '.htaccess', 'index.html'))) > 0) {
            copy_dir('./application/', './_backup/');
        }
        //Suppr
        clear_folder('./application', true);
        //Création du contenue du fichier application
        copy_dir('./system/setup_file/security/', './application/');
        @mkdir('./application/model');
        copy_dir('./system/setup_file/security/', './application/model/');
        @mkdir('./application/view');
        copy_dir('./system/setup_file/security/', './application/view/');
        @mkdir('./application/controller');
        copy_dir('./system/setup_file/security/', './application/controller/');
        @mkdir('./application/helper');
        copy_dir('./system/setup_file/security/', './application/helper/');
        @mkdir('./application/library');
        copy_dir('./system/setup_file/security/', './application/library/');
        copy_dir('./system/setup_file/config/', './application/config/');
        //Ajout de l'exemple
        copy('./system/setup_file/preset/hello_world.php', './application/view/hello_world.php');
        copy('./system/setup_file/preset/hello_world.controller.php', './application/controller/hello_world.php');
    } else {
        //Backup avant supression
        if (!file_exists('./_backup/') && count(array_diff(scandir('./application/'), array('..', '.', '.htaccess', 'index.html'))) > 0) {
            @mkdir('./_backup/');
        } else {
            clear_folder('./_backup', true);
        }
        if (count(array_diff(scandir('./application/'), array('..', '.', '.htaccess', 'index.html'))) > 0) {
            copy_dir('./application/', './_backup/');
        }
        //Suppr
        clear_folder('./application', true);
        //Création du contenue du fichier application
        copy_dir('./system/setup_file/security/', './application/');
        @mkdir('./application/ajax');
        copy_dir('./system/setup_file/security/', './application/ajax/');
        @mkdir('./application/class');
        copy_dir('./system/setup_file/security/', './application/class/');
        @mkdir('./application/helper');
        copy_dir('./system/setup_file/security/', './application/helper/');
        @mkdir('./application/library');
        copy_dir('./system/setup_file/security/', './application/library/');
        copy_dir('./system/setup_file/config/', './application/config/');
        //Ajout de l'exemple
        copy('./system/setup_file/preset/hello_world.php', './application/hello_world.php');
    }
}
//Ajout des infos dans le fichier de config
file_put_contents("./application/config/config.php", 
        str_replace("%APPLI%", $data['config']['appli_name'], 
                str_replace("%DATA%", str_replace("\\", "\\\\", $data['config']['data_path']), 
                        str_replace("%TMP%", str_replace("\\", "\\\\", $data['config']['tmp_path']), 
                                file_get_contents("./application/config/config.php")))));

//Création de l'htacces de routage en fonction de l'ini
$htaccess = fopen('./.htaccess', 'w');
//Verifie la bonne création du fichier
if ($htaccess === false) {
    //Erreur impossible d'écrire
    exit("Impossible de créer les fichiers de configurations");
}
$code = 'Options +FollowSymLinks' . "\r\n\r\n";
$code .= 'RewriteEngine On' . "\r\n\r\n";
$code .= 'RewriteBase /' . "\r\n\r\n";
$code .= 'RewriteCond $1 !^(index\.php|robots\.txt' . (($data['route']['routage_asset'] == 'on') ? '' : '|assets') . ')' . "\r\n\r\n";
$code .= 'RewriteRule ^(.*)$ ' . substr($_SERVER['SCRIPT_NAME'], 1) . '?_fc_r=$1 [L]';
fwrite($htaccess, $code);
fclose($htaccess);

//Création du fichir de config local
if (!file_exists('./system/config/')) {
    @mkdir('./system/config');
}
$root = ($_SERVER['REQUEST_URI'] == '/') ? './' : $_SERVER['REQUEST_URI'];
$root = ((trim($data['root']['fileroot']) != '') ? $data['root']['fileroot'] : $root);
$webroot = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$file_root = mb_substr(str_replace("\\", "/", __FILE__), 0, strpos(str_replace("\\", "/", __FILE__) , $root) + strlen($root));
$local = fopen('./system/config/local.php', 'w');
$code = '<?php' . "\r\n\r\n";
$code .= '$_config[\'root\'] = "' . $root . '";' . "\r\n";
$code .= '$_config[\'routage_asset\'] = ' . (($data['route']['routage_asset'] == 'on') ? 'true' : 'false') . ';' . "\r\n";
$code .= '$_config[\'file_root\'] = "' . $file_root . '";' . "\r\n";
$code .= '$_config[\'web_root\'] = "' . ((trim($data['root']['webroot']) != '') ? $data['root']['webroot'] : $webroot) . '";' . "\r\n";
$code .= '$_config[\'mode\'] = "' . (($data['mode']['mvc'] == 'on') ? 'mvc' : 'no_mvc') . '";' . "\r\n";
$code .= '$_config[\'md5\'] = "' . md5_file('./fraquicom.json') . '";' . "\r\n";
fwrite($local, $code);
fclose($local);

/* ===== Fonction ===== */

function copy_dir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                copy_dir($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

/**
 * Vide un dossier
 * @author Loquicom
 * @param string $folderPath - Le chemin du fichier
 * @param boolean $subfolder - Supprimer aussi les sous dossier
 * @param boolean $delete - Supprimer le dossier courant
 */
function clear_folder($folderPath, $subfolder = false, $delete = false) {
    //On verifie que c'est un fichier
    if (is_dir($folderPath)) {
        //On ajoute un slash a lafin si il n'y en a pas
        if ($folderPath[strlen($folderPath) - 1] != '/') {
            $folderPath .= '/';
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
                clear_folder($folderPath . $file, true, true);
            }
        }
        //Si $delete on supprime aussi le fichier actuel
        if ($delete) {
            @rmdir($folderPath);
        }
    }
}
