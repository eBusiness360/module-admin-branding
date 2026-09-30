<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\Model\File;

/**
 * Controle de structure d un fichier ICO.
 *
 * POURQUOI. Magento refuse tout televersement de favicon au format ICO :
 * Magento\MediaStorage\Model\File\Validator\Image reconnait le type MIME
 * image/vnd.microsoft.icon, puis demande a l adaptateur d image configure
 * d OUVRIR le fichier. L adaptateur par defaut est GD, qui ne sait pas lire
 * l ICO et leve « Unsupported image format ». D ou le message trompeur
 * « File validation failed. Check Image Processing Settings in the Store
 * Configuration. » — le reglage est en cause, pas le fichier. Le favicon de
 * la boutique, livre par Magento, souffre du meme defaut.
 *
 * Un favicon n est jamais redimensionne : rien n a besoin de l ouvrir. Il
 * suffit de verifier qu il est bien ce qu il pretend etre, et c est le role de
 * cette classe. Elle lit l ICONDIR, structure documentee et minuscule :
 *
 *   octets 0-1  mot reserve, toujours zero
 *   octets 2-3  type : 1 pour une icone, 2 pour un curseur
 *   octets 4-5  nombre d images contenues
 *   ensuite     une entree de 16 octets par image
 *
 * Classe sans dependance, pour qu elle soit testable a l unite — l uploader
 * qui l utilise, lui, ne s instancie pas hors d une requete.
 */
class IcoValidator
{
    private const TAILLE_ENTETE = 6;

    private const TAILLE_ENTREE = 16;

    private const TYPE_ICONE = 1;

    public function isValid(string $chemin): bool
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction
        if (!is_file($chemin) || !is_readable($chemin)) {
            return false;
        }

        $taille = (int) filesize($chemin);
        if ($taille < self::TAILLE_ENTETE + self::TAILLE_ENTREE) {
            return false;
        }

        $flux = fopen($chemin, 'rb');
        if ($flux === false) {
            return false;
        }

        $entete = (string) fread($flux, self::TAILLE_ENTETE);
        fclose($flux);
        // phpcs:enable Magento2.Functions.DiscouragedFunction

        if (strlen($entete) !== self::TAILLE_ENTETE) {
            return false;
        }

        [1 => $reserve, 2 => $type, 3 => $nombre] = (array) unpack('v3', $entete);

        if ($reserve !== 0 || $type !== self::TYPE_ICONE || $nombre < 1) {
            return false;
        }

        // Le fichier doit au moins porter les entrees qu il annonce.
        return $taille >= self::TAILLE_ENTETE + self::TAILLE_ENTREE * $nombre;
    }
}
