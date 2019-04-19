<?php

/* =============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  url.php
  ============================================================================== */
defined('FC_INI') or exit('Acces Denied');

if (!function_exists('base_url')) {

    function base_url() {
        global $_config;
        return $_config['web_root'];
    }

}

if (!function_exists('current_url')) {

    function current_url() {
        global $_config;
        return $_config['web_root'] . $_config['current_script'];
    }

}

if (!function_exists('redirect_url')) {

    /**
     * Redirect url
     *
     * Créer une url pour la redirection
     *
     * @param	string	$uri
     * @param   boolean $abs - Chemin en absolue
     * @return	string
     */
    function redirect_url($uri = '', $abs = false) {
        if ($abs) {
            global $_config;
            return $_config['web_root'] . $uri;
        } else {
            //Recupere l'url courrant sans la base
            $url = substr(current_url(), strlen(base_url()));
            //Recupere le nombre de sous chemin present
            $subUrl = count(explode('/', $url));
            $return = '';
            for ($i = 1; $i < $subUrl; $i++) {
                $return .= '../';
            }
            $return .= $uri;
            return $return;
        }
    }

}

if (!function_exists('assets_url')) {

    /**
     * Créer une url pour l'accès aux données de assets
     *
     * @param	string	$uri
     * @param   boolean $racine - Indique si le chemin pars de la racine ou non
     * @return	string
     */
    function assets_url($uri = '') {
        global $_S;
        $fc = get_instance();
        if ($fc->config->get('route', 'asset_security') && $fc->config->get('routage_asset')) {
            return redirect_url('assets/' . str_replace('=', '-equ-', base64_encode(uniqid() . '|=|' . $uri . '|=|' . mt_rand(1000, 9999))) .'/' . $_S['_fc_id']);
        } else {
            return redirect_url('assets/' . $uri);
        }
    }

}

if (!function_exists('redirect')) {

    /**
     * Header Redirect
     *
     * Header redirect in two flavors
     * For very fine grained control over headers, you could use the Output
     * Library's set_header() function.
     *
     * @param	string	$uri	URL
     * @param	string	$method	Redirect method
     * 			'auto', 'location' or 'refresh'
     * @param	int	$code	HTTP Response status code
     * @return	void
     */
    function redirect($uri = '', $method = 'auto', $code = NULL) {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = redirect_url($uri);
        }
        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 /* reference: http://en.wikipedia.org/wiki/Post/Redirect/Get */ : 307;
            } else {
                $code = 302;
            }
        }
        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, TRUE, $code);
                break;
        }
        exit;
    }

}

if (!function_exists('url_get_contents')) {

    /**
     * Permet de faire un file_get_contents sur une url distante
     * @static
     * @uses All_Curl Utilise la class All_Curl afin d'effectuer une requète curl sur l'url distante
     * @param string $url Url à recupérer
     * @param array $post Tableau de données POST à passer à l'url (facultatif)
     * @param string $session Token de session (facultatif)
     * @return array Retourne un tableau avec la clé "état" valant 'ko' si échec et 'ok' si réussi
     */
    function url_get_contents($url, $post = array(), $session = '') {
        if (!(isset($url) && trim($url) != '')) {
            return array(
                'etat' => 'ko',
                'httpCode' => '',
                'erreur' => 'Url invalide'
            );
        }
        $curl = new All_Curl($session);
        $get = $curl->get($url, $post);
        $httpCode = $curl->getHttpCode();
        $listErreurHttp = array(
            '400' => "Mauvaise requête",
            '401' => "Accès refusé (demande d'authentification HTTP)",
            '403' => "Accès refusé (sans demande d'authentification)",
            '404' => "Page introuvable",
            '405' => "Méthode de requête incorrect",
            '407' => "Authentification proxy nécessaire",
            '408' => "Requête en timeout",
            '500' => "Erreur interne du serveur (peut résulter d'un plantage site/serveur)",
            '503' => "Service temporairement indisponible",
            '509' => "Bande passante insuffisante"
        );
        if (array_key_exists($httpCode, $listErreurHttp)) {
            return array(
                'etat' => 'ko',
                'httpCode' => $httpCode,
                'erreur' => $listErreurHttp[$httpCode]
            );
        }
        return array(
            'etat' => 'ok',
            'httpCode' => $httpCode,
            'content' => $get
        );
    }

}
