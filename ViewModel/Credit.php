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
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Credit implements ArgumentInterface
{
    /**
     * Chemin écrit par un module complémentaire. Absent, il vaut false :
     * le crédit reste affiché. Ce module ne connaît pas qui l'écrit.
     */
    private const XML_PATH_HIDE = 'maxcode_adminbranding/footer/hide_credit';

    private const SITE_URL = 'https://ebusiness360.fr';
    private const SITE_LABEL = 'ebusiness360.fr';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isVisible(): bool
    {
        return !$this->scopeConfig->isSetFlag(self::XML_PATH_HIDE);
    }

    public function getSiteUrl(): string
    {
        return self::SITE_URL;
    }

    public function getSiteLabel(): string
    {
        return self::SITE_LABEL;
    }
}
