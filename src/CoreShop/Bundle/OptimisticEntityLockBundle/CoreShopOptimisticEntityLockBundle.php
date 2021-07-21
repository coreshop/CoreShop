<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\OptimisticEntityLockBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class CoreShopOptimisticEntityLockBundle extends AbstractPimcoreBundle
{
    /**
     * @return string
     */
    public function getNiceName()
    {
        return 'CoreShop - Optimistic Entity Lock';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'CoreShop - Optimistic Entity Lock Bundle';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        $bundleName = 'coreshop/optimistic-entity-lock-bundle';

        if (class_exists(InstalledVersions::class)) {
            if (InstalledVersions::isInstalled('coreshop/core-shop')) {
                return InstalledVersions::getVersion('coreshop/core-shop');
            }

            if (InstalledVersions::isInstalled($bundleName)) {
                return InstalledVersions::getVersion($bundleName);
            }
        }

        if (class_exists(Versions::class)) {
            if (isset(Versions::VERSIONS[$bundleName])) {
                return Versions::getVersion($bundleName);
            }

            if (isset(Versions::VERSIONS['coreshop/core-shop'])) {
                return Versions::getVersion('coreshop/core-shop');
            }
        }

        if (class_exists(Version::class)) {
            return Version::getVersion();
        }

        return '';
    }

    public function getJsPaths()
    {
        return [];
    }

    public function getCssPaths()
    {
        return [];
    }

    public function getEditmodeJsPaths()
    {
        return [];
    }

    public function getEditmodeCssPaths()
    {
        return [];
    }
}
