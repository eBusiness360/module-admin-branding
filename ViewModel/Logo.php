<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Logo implements ArgumentInterface
{
    private const XML_PATH_IMAGE = 'maxcode_adminbranding/logo/image';
    private const XML_PATH_ALT = 'maxcode_adminbranding/logo/alt';
    private const XML_PATH_STOREFRONT_LOGO = 'design/header/logo_src';

    private const DIR_OWN = 'maxcode/adminbranding/';
    private const DIR_STOREFRONT = 'logo/';

    private const DEFAULT_ALT = 'Administration';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * URL du logo à afficher, ou null si aucun n'est disponible.
     */
    public function getLogoUrl(): ?string
    {
        $own = $this->readPath(self::XML_PATH_IMAGE);
        if ($own !== null) {
            return $this->mediaBaseUrl() . self::DIR_OWN . $own;
        }

        $storefront = $this->readPath(self::XML_PATH_STOREFRONT_LOGO);
        if ($storefront !== null) {
            return $this->mediaBaseUrl() . self::DIR_STOREFRONT . $storefront;
        }

        return null;
    }

    public function getLogoAlt(): string
    {
        $alt = trim((string) $this->scopeConfig->getValue(self::XML_PATH_ALT));

        // Le repli passe par __() : le module est distribue, l administrateur
        // n est pas forcement francophone.
        return $alt !== '' ? $alt : (string) __(self::DEFAULT_ALT);
    }

    private function readPath(string $configPath): ?string
    {
        $value = ltrim(trim((string) $this->scopeConfig->getValue($configPath)), '/');

        return $value !== '' ? $value : null;
    }

    /**
     * Même construction que le cœur pour les champs image de configuration
     * (voir Magento\Config\Block\System\Config\Form\Field\Image::_getUrl()).
     *
     * On passe par UrlInterface et non par StoreManagerInterface : en zone
     * d'administration, getStore() renvoie le magasin admin, et surtout
     * Magento\Store\Api\Data\StoreInterface ne déclare pas getBaseUrl() —
     * un test qui la mocke échoue avant même d'avoir rien vérifié.
     */
    private function mediaBaseUrl(): string
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }
}
