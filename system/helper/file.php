<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  file.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

if(!function_exists('make_dir')){
    
    /**
     * Création d'une arborescence de dossiers si ces derniers n'existe pas
     * @param string $path - Le chemin
     * @return boolean
     */
    function make_dir($path){
        //Si le dossier n'existe pas
        if(!is_dir($path)){
            //Tentative de création
            if(make_dir(dirname($path))){
                @mkdir($path);
            }
            //Erreur lors de la creation
            else {
                return false;
            }
        }
        //Le dossier est la
        return true;
    }
    
}

if(!function_exists('is_empty')){
    
    /**
     * Indique si un dossier est vide
     * @param string $path - Le chemin vers le dossier
     * @param array $ignore - [optional] Fichier à ignorer dans le dossier 
     * ex : array('index.php', '.htaccess')
     * @return boolean
     */
    function is_empty($path, $ignore = null){
        //Verifie si le dossier existe
        if(!file_exists($path)){
            return false;
        }
        //Indique si le dossier est vide
        $tab = array('..', '.');
        if($ignore !== null && is_array($ignore) && !empty($ignore)){
            $tab = array_merge($tab, $ignore);
        }
        return empty(array_diff(scandir($path), $tab));
    }
    
}

if (!function_exists('copy_dir')) {

    /**
     * Copie un dossier et son contenue
     * @param string $src - Dossier soruce
     * @param string $dst - Destination
     */
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

}

if (!function_exists('clear_folder')) {

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

}


if (!function_exists('write_file')) {

    /**
     * Write File
     *
     * Writes data to the file specified in the path.
     * Creates a new file if non-existent.
     *
     * @param	string	$path	File path
     * @param	string	$data	Data to write
     * @param	string	$mode	fopen() mode (default: 'wb')
     * @return	bool
     */
    function write_file($path, $data, $mode = 'wb') {
        if (!$fp = @fopen($path, $mode)) {
            return FALSE;
        }
        flock($fp, LOCK_EX);
        for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr($data, $written))) === FALSE) {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return is_int($result);
    }

}

if (!function_exists('get_filenames')) {

    /**
     * Get Filenames
     *
     * Reads the specified directory and builds an array containing the filenames.
     * Any sub-folders contained within the specified path are read as well.
     *
     * @param	string	path to source
     * @param	bool	whether to include the path as part of the filename
     * @param	bool	internal variable to determine recursion status - do not use in calls
     * @return	array
     */
    function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE) {
        static $_filedata = array();
        if ($fp = @opendir($source_dir)) {
            // reset the array and make sure $source_dir has a trailing slash on the initial call
            if ($_recursion === FALSE) {
                $_filedata = array();
                $source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }
            while (FALSE !== ($file = readdir($fp))) {
                if (is_dir($source_dir . $file) && $file[0] !== '.') {
                    get_filenames($source_dir . $file . DIRECTORY_SEPARATOR, $include_path, TRUE);
                } elseif ($file[0] !== '.') {
                    $_filedata[] = ($include_path === TRUE) ? $source_dir . $file : $file;
                }
            }
            closedir($fp);
            return $_filedata;
        }
        return FALSE;
    }

}

if (!function_exists('directory_map')) {

    /**
     * Create a Directory Map
     *
     * Reads the specified directory and builds an array
     * representation of it. Sub-folders contained with the
     * directory will be mapped as well.
     *
     * @param	string	$source_dir		Path to source
     * @param	int	$directory_depth	Depth of directories to traverse
     * 						(0 = fully recursive, 1 = current dir, etc)
     * @param	bool	$hidden			Whether to show hidden files
     * @return	array
     */
    function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE) {
        if ($fp = @opendir($source_dir)) {
            $filedata = array();
            $new_depth = $directory_depth - 1;
            $source_dir = rtrim($source_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            while (FALSE !== ($file = readdir($fp))) {
                // Remove '.', '..', and hidden files [optional]
                if ($file === '.' OR $file === '..' OR ( $hidden === FALSE && $file[0] === '.')) {
                    continue;
                }
                is_dir($source_dir . $file) && $file .= DIRECTORY_SEPARATOR;
                if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir . $file)) {
                    $filedata[$file] = directory_map($source_dir . $file, $new_depth, $hidden);
                } else {
                    $filedata[] = $file;
                }
            }
            closedir($fp);
            return $filedata;
        }
        return FALSE;
    }

}

if (!function_exists('get_dir_file_info')) {

    /**
     * Get Directory File Information
     *
     * Reads the specified directory and builds an array containing the filenames,
     * filesize, dates, and permissions
     *
     * Any sub-folders contained within the specified path are read as well.
     *
     * @param	string	path to source
     * @param	bool	Look only at the top level directory specified?
     * @param	bool	internal variable to determine recursion status - do not use in calls
     * @return	array
     */
    function get_dir_file_info($source_dir, $top_level_only = TRUE, $_recursion = FALSE) {
        static $_filedata = array();
        $relative_path = $source_dir;
        if ($fp = @opendir($source_dir)) {
            // reset the array and make sure $source_dir has a trailing slash on the initial call
            if ($_recursion === FALSE) {
                $_filedata = array();
                $source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }
            // Used to be foreach (scandir($source_dir, 1) as $file), but scandir() is simply not as fast
            while (FALSE !== ($file = readdir($fp))) {
                if (is_dir($source_dir . $file) && $file[0] !== '.' && $top_level_only === FALSE) {
                    get_dir_file_info($source_dir . $file . DIRECTORY_SEPARATOR, $top_level_only, TRUE);
                } elseif ($file[0] !== '.') {
                    $_filedata[$file] = get_file_info($source_dir . $file);
                    $_filedata[$file]['relative_path'] = $relative_path;
                }
            }
            closedir($fp);
            return $_filedata;
        }
        return FALSE;
    }

}

if (!function_exists('get_file_info')) {

    /**
     * Get File Info
     *
     * Given a file and path, returns the name, path, size, date modified
     * Second parameter allows you to explicitly declare what information you want returned
     * Options are: name, server_path, size, date, readable, writable, executable, fileperms
     * Returns FALSE if the file cannot be found.
     *
     * @param	string	path to file
     * @param	mixed	array or comma separated string of information returned
     * @return	array
     */
    function get_file_info($file, $returned_values = array('name', 'server_path', 'size', 'date')) {
        if (!file_exists($file)) {
            return FALSE;
        }
        if (is_string($returned_values)) {
            $returned_values = explode(',', $returned_values);
        }
        foreach ($returned_values as $key) {
            switch ($key) {
                case 'name':
                    $fileinfo['name'] = basename($file);
                    break;
                case 'server_path':
                    $fileinfo['server_path'] = $file;
                    break;
                case 'size':
                    $fileinfo['size'] = filesize($file);
                    break;
                case 'date':
                    $fileinfo['date'] = filemtime($file);
                    break;
                case 'readable':
                    $fileinfo['readable'] = is_readable($file);
                    break;
                case 'writable':
                    $fileinfo['writable'] = is_really_writable($file);
                    break;
                case 'executable':
                    $fileinfo['executable'] = is_executable($file);
                    break;
                case 'fileperms':
                    $fileinfo['fileperms'] = fileperms($file);
                    break;
            }
        }
        return $fileinfo;
    }

}

if (!function_exists('unzip')) {

    /**
     * Permet de dézipper un ZIP dans une destination donnée et de récupérer un tableau qui liste les fichiers
     * @param string $zipPath Chemin veres le zip
     * @param string $dest Répertoire de destination pour dézipper
     * @return array|false Liste des fichiers dézippés ou false si erreur
     */
    function unzip($zipPath, $dest) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $files = array();
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $nameFile = $zip->getNameIndex($i);
                if (file_exists($dest . $nameFile)) { // Si le fichier exist deje on le suppr avant d'extraire
                    unlink($dest . $nameFile);
                }
                if (!$zip->extractTo($dest, array($zip->getNameIndex($i)))) {
                    return false;
                }
                $files[] = $dest . $zip->getNameIndex($i);
            }
            $zip->close();
            return $files;
        } else {
            return false;
        }
    }

}

if (!function_exists('create_zip')) {

    /**
     * Permet de créer un zip à partir d'une liste de fichier
     * @param string $zipPath Nom de destination du zip
     * @param array $files Liste des path + name (ou liste des paths) des fichiers à zipper
     * @return boolean True si réussi, false sinon
     */
    function create_zip($zipPath, $files) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                if (is_string($file)) {
                    $zip->addFile($file, basename($file));
                } else {
                    $zip->addFile($file['path'], $file['name']);
                }
            }
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('clean_file_name')) {

    /**
     * Permet de retirer tous les caractères problématiques dans un nom de fichier (espaces,accents, etc..)
     * @static
     * @param string $string Nom du fichier
     * @return string Retourne le nom du fichier nettoyé de tous les caractères problématiques (espaces,accents, etc..)
     */
    function clean_file_fame($string) {
        $ext = pathinfo($string, PATHINFO_EXTENSION);
        $string = str_replace($ext, '', $string);
        $string = All_Tools::stripAccents($string);
        $string = preg_replace('/[^\w-_]+/', '', $string);
        $string = preg_replace('/(-)+/', '-', $string);
        $string = preg_replace('/(_)+/', '_', $string);
        return $string . "." . strtolower($ext);
    }

}

if (!function_exists('get_mime_by_extension')) {

    /**
     * Get Mime by Extension
     *
     * Translates a file extension into a mime type based on config/mimes.php.
     * Returns FALSE if it can't determine the type, or open the mime config file
     *
     * Note: this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
     * It should NOT be trusted, and should certainly NOT be used for security
     *
     * @param	string	$filename	File name
     * @return	false|string
     */
    function get_mime_by_extension($filename) {
        //Verifie que le fichier existe
        if(!file_exists($filename)){
            return false;
        }
        //Chargement fichier de config mimes.php
        $fc = get_instance();
        $fc->load->config('mimes');
        //Verification de l'extension
        $extension = strtolower(substr(strrchr($filename, '.'), 1));
        if (isset($fc->config->mimes[$extension])) {
            return is_array($fc->config->mimes[$extension]) ? $fc->config->mimes[$extension][0] : $fc->config->mimes[$extension];
        }
        return false;
    }

}

if(!function_exists('get_mime_by_file')){
    
    /**
     * Retourne le mimetype réel d'un fichier, à partir du fichier magic.mime
     * @param string $filename - Le chemin vers le fichier
     * @return false|string - Le mimetype ou false
     */
    function get_mime_by_file($filename){
        //Verifie que le fichier existe
        if(!file_exists($filename)){
            return false;
        }
        //Verification du mimetype
        return mime_content_type($filename);
    }
    
}

if(!function_exists('get_extension')){
    
    /**
     * Retourne l'extension d'un mimetype
     * @param string $mime - Le mimetype
     * @param string $fileExt [optional] - L'extension d'un fichier pour regarder en priorité si le mimetype correspond
     * (Permet d'eviter les probleme pour les mimetype qui correspondent à plusieurs extension comme text/plain)
     * @return false|string
     */
    function get_extension($mime, $fileExt = null){
        //Chargement du fichier mimes.php
        $fc = get_instance();
        $fc->load->config('mimes');
        $mimes = $fc->config->mimes;
        //Si l'extension du fichier est indiquée
        if($fileExt !== null && trim($fileExt) != ''){
            //Regarde si le mimetype indiqué est dans la clef de l'extension du fichier
            $fileExt = strtolower($fileExt);
            if(isset($mimes[$fileExt])){
                $mimelist = $mimes[$fileExt];
                if(!is_array($mimelist)){
                    $mimelist = array($mimelist);
                }
                //Recherche
                if(in_array($mime, $mimelist)){
                    return $fileExt;
                }
            }
        }
        //Parcours du tableau pour trouver l'extension
        foreach ($mimes as $ext => $mimetype){
            //Si plusieurs mimetype pour une extension
            if(is_array($mimetype)){
                if(in_array($mime, $mimetype)){
                    return $ext;
                }
            }
            //Sinon un seul mimetype
            else if($mime == $mimetype){
                return $ext;
            }
        }
        //Si aucun resultat
        return false;
    }
    
}

if(!function_exists('reverse_mimetype')){
    
    /**
     * Alias de get_extension
     * @see get_extension
     * @param string $mime
     * @param string $fileExt
     * @return false|string
     */
    function reverse_mimetype($mime, $fileExt = null){
        return get_extension($mime, $fileExt);
    }
    
}

if(!function_exists('size_name')){

    /**
     * Calcul la taille du fichier dans l'unité approprié
     * @see filesize
     * @param int $size - La taille en octet
     * @param boolean $unité - Ajout ou non des unités dans le retour (defaut true)
     * @return String|int la taille adapté dans le bon format (Octet, Ko, Mo, Go, To, Po)
     */
    function size_name($size, $unite = true){
        //Array contenant les differents nom unités 
        $name = array('Octet(s)','Ko','Mo','Go', 'To', 'Po');
        //Octect
        if($size < 1000){
            $index = 0;
        } 
        //Ko
        else if($size < 1000000){
            $size = round($size/1024,2);
            $index = 1;
        } 
        //Mo
        else if($size < 1000000000){
            $size = round($size/pow(1024,2),2);
            $index = 2;
        }
        //Go
        else if($size < 1000000000000){
            $size = round($size/pow(1024,3),2);
            $index = 3;
        }
        //To
        else if($size < 1000000000000000){
            $size = round($size/pow(1024,4),2);
            $index = 4;
        } 
        //Po
        else {
            $size = round($size/pow(1024,5),2);
            $index = 5;
        }
        //Retour
        if($unite){
            return $size . " " . $name[$index];
        }
        return $size;
    }

}
