<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 * Originally derived from pimcore/google-marketing-bundle (POCL).
 */

namespace CoreShop\Bundle\TrackingBundle\GoogleMarketing\Config;

class Config
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @param array<string, mixed> $reportConfig
     */
    public static function fromReportConfig(array $reportConfig): self
    {
        return new self($reportConfig['analytics'] ?? []);
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function isSiteConfigured(string $configKey): bool
    {
        $config = $this->getConfigForSite($configKey);

        if (null === $config) {
            return false;
        }

        return null !== $this->normalizeStringValue($config['trackid'] ?? null);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getConfigForSite(string $configKey): ?array
    {
        return $this->config['sites'][$configKey] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfiguredSites(): array
    {
        $sites = $this->config['sites'] ?? [];

        return is_array($sites) ? $sites : [];
    }

    private function normalizeStringValue(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
