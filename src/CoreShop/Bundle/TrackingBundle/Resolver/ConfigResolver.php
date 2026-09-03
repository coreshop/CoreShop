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
 */

namespace CoreShop\Bundle\TrackingBundle\Resolver;

use CoreShop\Bundle\TrackingBundle\GoogleMarketing\Config\ConfigProvider;
use CoreShop\Bundle\TrackingBundle\GoogleMarketing\SiteId\SiteIdProvider;

class ConfigResolver implements ConfigResolverInterface
{
    private array $googleConfig;

    public function __construct(
        private SiteIdProvider $siteIdProvider,
        private ConfigProvider $goggleConfigProvider,
    ) {
    }

    /**
     * @psalm-suppress DeprecatedClass
     */
    public function getGoogleConfig(): ?array
    {
        $config = $this->goggleConfigProvider->getConfig();
        $siteId = $this->siteIdProvider->getForRequest();

        $configKey = $siteId->getConfigKey();

        if (!$config->isSiteConfigured($configKey)) {
            return [];
        }

        $siteConfig = $config->getConfigForSite($configKey);

        $this->googleConfig = $siteConfig;

        return $this->googleConfig;
    }
}
