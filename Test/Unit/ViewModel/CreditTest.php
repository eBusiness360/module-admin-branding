<?php
/**
 * @author    eBusiness360 – Maxime LESGUILLIER aka Maxcode
 * @copyright Copyright © 2026 eBusiness360 – Maxime LESGUILLIER aka Maxcode. Tous droits réservés.
 * @license   Licence propriétaire — voir le fichier LICENSE
 * @package   Maxcode_AdminBranding
 */
declare(strict_types=1);

namespace Maxcode\AdminBranding\Test\Unit\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Maxcode\AdminBranding\ViewModel\Credit;
use PHPUnit\Framework\TestCase;

class CreditTest extends TestCase
{
    private function makeViewModel(bool $hidden): Credit
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturn($hidden);

        return new Credit($scopeConfig);
    }

    public function testIsVisibleWhenNoModuleHidesIt(): void
    {
        self::assertTrue($this->makeViewModel(false)->isVisible());
    }

    public function testIsHiddenWhenTheFlagIsSet(): void
    {
        self::assertFalse($this->makeViewModel(true)->isVisible());
    }

    public function testExposesTheSiteUrl(): void
    {
        self::assertSame('https://ebusiness360.fr', $this->makeViewModel(false)->getSiteUrl());
    }

    public function testExposesTheSiteLabel(): void
    {
        self::assertSame('ebusiness360.fr', $this->makeViewModel(false)->getSiteLabel());
    }
}
