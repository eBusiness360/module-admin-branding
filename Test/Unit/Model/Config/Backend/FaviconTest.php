<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\Test\Unit\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\File;
use Maxcode\AdminBranding\Model\Config\Backend\Favicon;
use Maxcode\AdminBranding\Model\File\FaviconUploader;
use PHPUnit\Framework\TestCase;

/**
 * Ces modeles ne s instancient pas en test unitaire : leurs ancetres resolvent
 * leurs dependances par l Object Manager. On verifie donc ce qui se verifie
 * sans instance — et surtout la CONFIGURATION, puisque c est la que le defaut
 * vivait : le system.xml designait des classes qui ignorent son propre
 * upload_dir.
 */
class FaviconTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function champsDeFichier(): array
    {
        return ['logo' => ['image'], 'favicon' => ['favicon']];
    }

    // Test/Unit/Model/Config/Backend -> racine du module : cinq niveaux.
    private const RACINE_MODULE = __DIR__ . '/../../../../../';

    private function modeleDeBackend(string $idChamp): string
    {
        $chemin = self::RACINE_MODULE . 'etc/adminhtml/system.xml';
        $xml = simplexml_load_file($chemin);
        self::assertNotFalse($xml, 'system.xml illisible');

        $trouve = $xml->xpath(
            sprintf('//section[@id="maxcode_adminbranding"]//field[@id="%s"]/backend_model', $idChamp)
        );
        self::assertNotEmpty($trouve, 'Champ ' . $idChamp . ' sans backend_model');

        return (string) $trouve[0];
    }

    private function classeDeclarant(string $classe, string $methode): string
    {
        $reflexion = new \ReflectionClass($classe);

        while (!$reflexion->hasMethod($methode)) {
            $reflexion = $reflexion->getParentClass();
        }

        return $reflexion->getMethod($methode)->getDeclaringClass()->getName();
    }

    /**
     * LE DEFAUT REPARE. Les modeles du coeur Image\Logo et Image\Favicon
     * ecrasent _getUploadDir() par un dossier ecrit en dur — « logo » et
     * « favicon » — et IGNORENT le upload_dir declare dans system.xml. Les
     * fichiers partaient donc dans les dossiers de Magento pendant que les
     * ViewModels du module les cherchaient dans le sien : l image rendait 404,
     * sans aucun message d erreur a l enregistrement.
     *
     * Constate en production le 29/09/2026 :
     *   fichier reel   pub/media/favicon/default/favicon_1.ico
     *   valeur en base default/favicon_1.ico
     *   URL construite media/maxcode/adminbranding/default/favicon_1.ico -> 404
     *
     * @dataProvider champsDeFichier
     */
    public function testTheBackendModelHonoursTheUploadDirOfSystemXml(string $idChamp): void
    {
        $classe = $this->modeleDeBackend($idChamp);

        self::assertSame(
            File::class,
            $this->classeDeclarant($classe, '_getUploadDir'),
            $classe . ' redefinit le dossier de televersement et ignore le upload_dir du system.xml.'
        );
    }

    /**
     * Meme famille : _addWhetherScopeInfo() force l ajout d un segment de
     * portee dans les sous-classes du coeur, d ou le « default/ » observe en
     * production. La version de File le laisse decider par le system.xml.
     *
     * @dataProvider champsDeFichier
     */
    public function testTheBackendModelDoesNotForceTheScopeSegment(string $idChamp): void
    {
        $classe = $this->modeleDeBackend($idChamp);

        self::assertSame(
            File::class,
            $this->classeDeclarant($classe, '_addWhetherScopeInfo')
        );
    }

    /**
     * L AUTRE MOITIE DU DEFAUT. Le chemin repare, le televersement d un .ico
     * echouait encore : « File validation failed. Check Image Processing
     * Settings in the Store Configuration. » L uploader du coeur fait ouvrir
     * toute image reconnue par l adaptateur configure, et GD ne lit pas l ICO.
     * Le champ recoit donc sa propre fabrique d uploader — par type virtuel,
     * pour ne rien changer aux autres televersements du site.
     *
     * Ce test verrouille la chaine complete : si l argument disparait du
     * di.xml, ou si le type virtuel cesse de designer notre classe, le format
     * ICO redevient silencieusement irrecevable.
     */
    public function testTheFaviconFieldGetsItsOwnUploader(): void
    {
        $xml = simplexml_load_file(self::RACINE_MODULE . 'etc/di.xml');
        self::assertNotFalse($xml, 'di.xml illisible');

        $fabrique = $xml->xpath(
            sprintf(
                '//type[@name="%s"]/arguments/argument[@name="uploaderFactory"]',
                Favicon::class
            )
        );
        self::assertNotEmpty($fabrique, 'Le champ favicon n a pas sa propre fabrique d uploader');

        $typeVirtuel = $xml->xpath(
            sprintf('//virtualType[@name="%s"]/arguments/argument[@name="instanceName"]', trim((string) $fabrique[0]))
        );
        self::assertNotEmpty($typeVirtuel, 'Type virtuel de fabrique introuvable');

        self::assertSame(FaviconUploader::class, trim((string) $typeVirtuel[0]));
    }

    public function testTheFaviconAcceptsTheUsualFormats(): void
    {
        foreach (['ico', 'png', 'gif', 'jpg', 'jpeg', 'apng'] as $extension) {
            self::assertContains($extension, Favicon::EXTENSIONS_AUTORISEES, $extension);
        }
    }

    /**
     * Pas de SVG, malgre sa prise en charge par les navigateurs : un SVG est un
     * document XML qui peut porter du script, et il serait televersé par un
     * administrateur dans un dossier servi publiquement. Magento l exclut de sa
     * propre liste pour la meme raison.
     */
    public function testTheFaviconRefusesSvgAndAnythingExecutable(): void
    {
        foreach (['svg', 'php', 'phtml', 'html', 'js'] as $extension) {
            self::assertNotContains($extension, Favicon::EXTENSIONS_AUTORISEES, $extension);
        }
    }
}
