<?php

/* ==============================================================================
  Fraquicom [PHP Framework] by Loquicom <contact@loquicom.fr>

  GPL-3.0
  image.php
  ============================================================================ */

if (!function_exists('redim_image')) {

    /**
     * Redimensionne une image
     * 
     * @param string $img_src - Le chemin de l'image
     * @param int $dst_w - Largeur max en px
     * @param int $dst_h - Hauteur max en px
     * @param boolean $onlyWH - Seleument la ligne pour l'image
     * @return boolean | array('width', 'height', 'wh');
     */
    function redim_image($img_src, $dst_w, $dst_h, $onlyWH = false) {
        // Lit les dimensions de l'image
        // controle ici si l'image existe attention si chemin http faire attention !! // TODO
        $size = GetImageSize($img_src);
        if (isset($size) && $size !== false && is_array($size)) {
            $src_w = $size[0];
            $src_h = $size[1];
            // Teste les dimensions tenant dans la zone
            $test_h = round(($dst_w / $src_w) * $src_h);
            $test_w = round(($dst_h / $src_h) * $src_w);
            // Si Height final non précisé (0)
            if (!$dst_h)
                $dst_h = $test_h;
            // Sinon si Width final non précisé (0)
            elseif (!$dst_w)
                $dst_w = $test_w;
            // Sinon teste quel redimensionnement tient dans la zone
            elseif ($test_h > $dst_h)
                $dst_w = $test_w;
            else
                $dst_h = $test_h;

            // Affiche les dimensions optimales
            if ($onlyWH) {
                return 'width="' . $dst_w . '" height="' . $dst_h . '"';
            } else {
                return array(
                    'width' => $dst_w,
                    'height' => $dst_h,
                    'wh' => 'width="' . $dst_w . '" height="' . $dst_h . '"'
                );
            }
        } else {
            return false;
        }
    }

}

if (!function_exists('image_html')) {

    /**
     * Création d'une balise html img adapté à l'image
     * @param string $src - Le chemin de l'image (attribut src de la balise img) dans le dossier assets
     * @param string $alt - Description de l'image
     * @param string $title - Titre de l'image
     * @param string $class - Class à mettre sur l'image séparés d'un espace
     * @param int $width - Largeur de l'image
     * @param int $height - Hauteur de l'image
     * @return false|string La balise ou false si l'image est introuvable
     */
    function image_html($src, $alt = '', $title = '', $class = '', $width = 0, $height = 0) {
        if (file_exists('./assets/' . $src)) {
            $wh['wh'] = '';
            if ($width > 0 && $height > 0) {
                $wh = redim_image('./assets/' . $src, 120, 58);
            }
            return '<img src="' . assets_url($src) . '" alt="' . $alt . '" title="' . $title . '" ' . $wh['wh'] . ' class="' . $class . '">';
        } else {
            return false;
        }
    }

}