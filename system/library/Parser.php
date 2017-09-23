<?php

/*=============================================================================
Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

GPL-3.0
Parser.php
==============================================================================*/

if(!@class_exists('LcParser', false)){
    require './system/class/LcParser.php';
}

class Parser {
    
    /**
     * Transforme un document en tableau de données
     * @param int|string $type - Le type de document (Utiliser constante : INI, JSON, XML, CSV) 
     * @param string $content - Chemin vers le document ou le document
     * @return mixed
     */
    public function parse($type, $content){
        $parser = new LcParser($type);
        $parser->parse($content);
        return $parser->getData();
    }
    
    /**
     * Transforme un tableau de données en un document du type
     * @param int|string $type - Le type de document (Utiliser constante : INI, JSON, XML, CSV) 
     * @param mixed $tab - Tableau de donnée
     * @return string
     */
    public function transform($type, $tab){
        $parser = new LcParser($type);
        $parser->transform($tab);
        return $parser->getFileContent();
    }
    
}