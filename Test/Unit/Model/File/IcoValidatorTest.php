<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\Test\Unit\Model\File;

use Maxcode\AdminBranding\Model\File\IcoValidator;
use PHPUnit\Framework\TestCase;

class IcoValidatorTest extends TestCase
{
    private IcoValidator $validateur;

    /** @var string[] */
    private array $fichiersTemporaires = [];

    protected function setUp(): void
    {
        $this->validateur = new IcoValidator();
    }

    protected function tearDown(): void
    {
        foreach ($this->fichiersTemporaires as $chemin) {
            if (is_file($chemin)) {
                unlink($chemin);
            }
        }
    }

    /**
     * Un ICONDIR : reserve (0), type, nombre d images. Suivi d autant
     * d entrees de 16 octets que le nombre annonce.
     */
    private function fichier(string $octets): string
    {
        $chemin = tempnam(sys_get_temp_dir(), 'maxcode-ico-');
        self::assertNotFalse($chemin);
        $this->fichiersTemporaires[] = $chemin;
        file_put_contents($chemin, $octets);

        return $chemin;
    }

    private function icone(int $reserve = 0, int $type = 1, int $nombre = 1, ?int $entrees = null): string
    {
        return pack('vvv', $reserve, $type, $nombre) . str_repeat("\x00", 16 * ($entrees ?? $nombre));
    }

    public function testItAcceptsAnIcoFile(): void
    {
        self::assertTrue($this->validateur->isValid($this->fichier($this->icone())));
    }

    public function testItAcceptsAnIcoHoldingSeveralSizes(): void
    {
        self::assertTrue($this->validateur->isValid($this->fichier($this->icone(0, 1, 4))));
    }

    /**
     * Un curseur (type 2) a exactement la meme structure qu une icone. Le
     * navigateur ne l affichera pas : on le refuse ici plutot que de laisser
     * un favicon muet en production.
     */
    public function testItRefusesACursorFile(): void
    {
        self::assertFalse($this->validateur->isValid($this->fichier($this->icone(0, 2))));
    }

    public function testItRefusesAHeaderWhoseReservedWordIsNotZero(): void
    {
        self::assertFalse($this->validateur->isValid($this->fichier($this->icone(1))));
    }

    public function testItRefusesAnIcoAnnouncingNoImage(): void
    {
        self::assertFalse($this->validateur->isValid($this->fichier($this->icone(0, 1, 0, 1))));
    }

    /**
     * LA RAISON D ETRE DE CE VALIDATEUR. On remplace le controle du coeur, qui
     * demande a GD d ouvrir le fichier — GD ne sait pas lire l ICO. Il faut
     * donc que le notre rejette bel et bien ce qu un simple nom en .ico
     * pourrait masquer : du PHP, un PNG renomme, un fichier tronque.
     */
    public function testItRefusesAFileThatOnlyPretendsToBeAnIco(): void
    {
        $leurres = [
            'php' => "<?php echo 'salut';",
            'png' => "\x89PNG\x0d\x0a\x1a\x0a" . str_repeat("\x00", 64),
            'html' => '<html lang="fr"><body>rien</body></html>',
            'vide' => '',
            'tronque' => "\x00\x00\x01",
        ];

        foreach ($leurres as $etiquette => $contenu) {
            self::assertFalse($this->validateur->isValid($this->fichier($contenu)), $etiquette);
        }
    }

    /**
     * Un en-tete valide suivi de rien : le fichier annonce une image de plus
     * qu il n en porte. Le controle de taille est ce qui distingue ce
     * validateur d un simple coup d oeil aux quatre premiers octets.
     */
    public function testItRefusesAnIcoTruncatedAfterItsHeader(): void
    {
        self::assertFalse($this->validateur->isValid($this->fichier($this->icone(0, 1, 2, 1))));
    }

    public function testItRefusesAMissingFile(): void
    {
        self::assertFalse($this->validateur->isValid(sys_get_temp_dir() . '/maxcode-absent-' . uniqid() . '.ico'));
    }
}
