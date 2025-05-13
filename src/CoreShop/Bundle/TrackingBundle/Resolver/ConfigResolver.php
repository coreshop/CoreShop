<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\TrackingBundle\Resolver;

use Pimcore\Bundle\GoogleMarketingBundle\Config\ConfigProvider;
use Pimcore\Bundle\GoogleMarketingBundle\SiteId\SiteIdProvider;

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
