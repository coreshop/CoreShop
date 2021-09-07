<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\TrackingBundle\Resolver;

use Pimcore\Analytics\SiteId\SiteIdProvider;
use Pimcore\Analytics\Google\Config\ConfigProvider as GoogleConfigProvider;
use Pimcore\Config\Config as ConfigObject;

class ConfigResolver implements ConfigResolverInterface
{
    private SiteIdProvider $siteIdProvider;
    private GoogleConfigProvider $goggleConfigProvider;
    private ?ConfigObject $googleConfig;

    public function __construct(
        SiteIdProvider $siteIdProvider,
        GoogleConfigProvider $goggleConfigProvider
    ) {
        $this->siteIdProvider = $siteIdProvider;
        $this->goggleConfigProvider = $goggleConfigProvider;
    }

    public function getGoogleConfig(): ?ConfigObject
    {
        if (null !== $this->googleConfig) {
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
