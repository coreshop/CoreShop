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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\TrackingBundle\Resolver;

use Pimcore\Analytics\Google\Config\ConfigProvider as GoogleConfigProvider;
use Pimcore\Analytics\SiteId\SiteIdProvider;
use Pimcore\Config\Config as ConfigObject;

class ConfigResolver implements ConfigResolverInterface
{
    /**
     * @psalm-suppress DeprecatedClass
     */
    private ?ConfigObject $googleConfig;

    public function __construct(
        private SiteIdProvider $siteIdProvider,
        private GoogleConfigProvider $goggleConfigProvider,
    ) {
    }

    /**
     * @psalm-suppress DeprecatedClass
     */
    public function getGoogleConfig(): ?ConfigObject
    {
        if (isset($this->googleConfig)) {
            return $this->googleConfig;
        }

        $config = $this->goggleConfigProvider->getConfig();
        $siteId = $this->siteIdProvider->getForRequest();

        $configKey = $siteId->getConfigKey();

        if (!$config->isSiteConfigured($configKey)) {
            return null;
        }

        $siteConfig = $config->getConfigForSite($configKey);

        $this->googleConfig = $siteConfig;

        return $this->googleConfig;
    }
}
