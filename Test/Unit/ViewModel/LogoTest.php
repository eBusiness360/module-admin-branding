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
use Maxcode\AdminBranding\ViewModel\Logo;
use PHPUnit\Framework\TestCase;

class LogoTest extends TestCase
{
    private const MEDIA = 'https://exemple.test/media/';

    private function makeViewModel(array $config): Logo
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')
            ->willReturnCallback(static fn (string $path) => $config[$path] ?? null);

        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->method('getBaseUrl')->willReturn(self::MEDIA);

        return new Logo($scopeConfig, $urlBuilder);
    }

    public function testReturnsNullWhenNothingIsConfigured(): void
    {
        self::assertNull($this->makeViewModel([])->getLogoUrl());
    }

    public function testUsesTheDedicatedLogoWhenConfigured(): void
    {
        $viewModel = $this->makeViewModel([
            'maxcode_adminbranding/logo/image' => 'mon-logo.png',
        ]);

        self::assertSame(
            self::MEDIA . 'maxcode/adminbranding/mon-logo.png',
            $viewModel->getLogoUrl()
        );
    }

    public function testFallsBackToTheStorefrontLogo(): void
    {
        $viewModel = $this->makeViewModel([
            'design/header/logo_src' => 'default/logo-site.png',
        ]);

        self::assertSame(
            self::MEDIA . 'logo/default/logo-site.png',
            $viewModel->getLogoUrl()
        );
    }

    public function testPrefersTheDedicatedLogoOverTheStorefrontOne(): void
    {
        $viewModel = $this->makeViewModel([
            'maxcode_adminbranding/logo/image' => 'mon-logo.png',
            'design/header/logo_src'           => 'default/logo-site.png',
        ]);

        self::assertSame(
            self::MEDIA . 'maxcode/adminbranding/mon-logo.png',
            $viewModel->getLogoUrl()
        );
    }

    public function testIgnoresAValueMadeOnlyOfSpaces(): void
    {
        self::assertNull($this->makeViewModel([
            'maxcode_adminbranding/logo/image' => '   ',
        ])->getLogoUrl());
    }

    public function testAValueMadeOnlyOfSpacesFallsBackToTheStorefrontLogo(): void
    {
        $viewModel = $this->makeViewModel([
            'maxcode_adminbranding/logo/image' => '   ',
            'design/header/logo_src'           => 'default/logo-site.png',
        ]);

        self::assertSame(
            self::MEDIA . 'logo/default/logo-site.png',
            $viewModel->getLogoUrl()
        );
    }

    public function testStripsALeadingSlashFromTheStoredPath(): void
    {
        $viewModel = $this->makeViewModel([
            'maxcode_adminbranding/logo/image' => '/mon-logo.png',
        ]);

        self::assertSame(
            self::MEDIA . 'maxcode/adminbranding/mon-logo.png',
            $viewModel->getLogoUrl()
        );
    }

    public function testReturnsTheConfiguredAlternativeText(): void
    {
        $viewModel = $this->makeViewModel([
            'maxcode_adminbranding/logo/alt' => 'Administration Untel',
        ]);

        self::assertSame('Administration Untel', $viewModel->getLogoAlt());
    }

    public function testFallsBackToAGenericAlternativeText(): void
    {
        self::assertSame('Administration', $this->makeViewModel([])->getLogoAlt());
    }
}
