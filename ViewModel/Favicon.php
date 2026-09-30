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

class Favicon implements ArgumentInterface
{
    private const XML_PATH_FAVICON = 'maxcode_adminbranding/logo/favicon';
    private const DIR = 'maxcode/adminbranding/';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    public function getFaviconUrl(): ?string
    {
        $stored = ltrim(trim((string) $this->scopeConfig->getValue(self::XML_PATH_FAVICON)), '/');

        if ($stored === '') {
            return null;
        }

        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
            . self::DIR . $stored;
    }
}
