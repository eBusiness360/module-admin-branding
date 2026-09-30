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
use Magento\Framework\UrlInterface;
use Maxcode\AdminBranding\ViewModel\Favicon;
use PHPUnit\Framework\TestCase;

class FaviconTest extends TestCase
{
    private const MEDIA = 'https://exemple.test/media/';

    private function makeViewModel(?string $stored): Favicon
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($stored);

        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->method('getBaseUrl')->willReturn(self::MEDIA);

        return new Favicon($scopeConfig, $urlBuilder);
    }

    public function testReturnsNullWhenNothingIsConfigured(): void
    {
        self::assertNull($this->makeViewModel(null)->getFaviconUrl());
    }

    public function testReturnsNullWhenTheValueIsBlank(): void
    {
        self::assertNull($this->makeViewModel('  ')->getFaviconUrl());
    }

    public function testBuildsTheMediaUrl(): void
    {
        self::assertSame(
            self::MEDIA . 'maxcode/adminbranding/favicon.ico',
            $this->makeViewModel('favicon.ico')->getFaviconUrl()
        );
    }
}
