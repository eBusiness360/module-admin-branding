<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image;

/**
 * Televersement du favicon de l administration.
 *
 * POURQUOI CETTE CLASSE EXISTE. Le modele du coeur,
 * Magento\Config\Model\Config\Backend\Image\Favicon, fait deux choses qui
 * conviennent au favicon de la BOUTIQUE et pas au notre :
 *
 *   - il ecrase _getUploadDir() par un « favicon » ecrit en dur et IGNORE le
 *     upload_dir declare dans system.xml ;
 *   - il force _addWhetherScopeInfo() a vrai, ce qui ajoute un segment de
 *     portee au chemin.
 *
 * Le fichier partait donc dans pub/media/favicon/default/ pendant que le
 * module le cherchait dans pub/media/maxcode/adminbranding/ : l image du
 * favicon rendait 404, sans aucun message d erreur a l enregistrement.
 * Constate en production le 29/09/2026.
 *
 * Partager pub/media/favicon/ avec le favicon de la boutique serait de toute
 * facon une mauvaise idee : deux reglages distincts ecrivant au meme endroit.
 *
 * On herite donc de Image — dont l ancetre File respecte le upload_dir du
 * system.xml — et on ne redefinit QUE la liste des extensions. Deux tests
 * verrouillent ce choix : ils echouent si quelqu un remet la main sur
 * _getUploadDir() ou _addWhetherScopeInfo().
 */
class Favicon extends Image
{
    /**
     * Reprend la liste du coeur pour les favicons, SANS le SVG : un SVG est un
     * document XML qui peut porter du script, et il serait televersé par un
     * administrateur dans un dossier servi publiquement. Magento l exclut de sa
     * propre liste pour la meme raison.
     */
    public const EXTENSIONS_AUTORISEES = ['ico', 'png', 'gif', 'jpg', 'jpeg', 'apng'];

    /**
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return self::EXTENSIONS_AUTORISEES;
    }
}
