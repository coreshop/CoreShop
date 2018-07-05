<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\TrackingBundle\Resolver;

use Pimcore\Analytics\SiteId\SiteIdProvider;
use Pimcore\Analytics\Google\Config\ConfigProvider as GoogleConfigProvider;
use Pimcore\Config\Config as ConfigObject;

class ConfigResolver
{
    /**
     * @var null|bool|ConfigObject
     */
    private $googleConfig = null;

    /**
     * @var SiteIdProvider
     */
    private $siteIdProvider;

    /**
     * @var GoogleConfigProvider
     */
    private $goggleConfigProvider;

    /**
     * @param SiteIdProvider       $siteIdProvider
     * @param GoogleConfigProvider $goggleConfigProvider
     */
    public function __construct(
        SiteIdProvider $siteIdProvider,
        GoogleConfigProvider $goggleConfigProvider
    ) {
        $this->siteIdProvider = $siteIdProvider;
        $this->goggleConfigProvider = $goggleConfigProvider;
    }

    /**
     * @return bool|ConfigObject
     */
    public function getGoogleConfig()
    {
        if (!is_null($this->googleConfig)) {
            return $this->googleConfig;
        }

        $config = $this->goggleConfigProvider->getConfig();
        $siteId = $this->siteIdProvider->getForRequest();

        $configKey = $siteId->getConfigKey();
        if (!$config->isSiteConfigured($configKey)) {
            return false;
        }

        $siteConfig = $config->getConfigForSite($configKey);

        $this->googleConfig = $siteConfig;

        return $this->googleConfig;
    }
}
