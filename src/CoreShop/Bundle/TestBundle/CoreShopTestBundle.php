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

namespace CoreShop\Bundle\TestBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class CoreShopTestBundle extends AbstractPimcoreBundle
{
    public function getNiceName(): string
    {
        return 'CoreShop - Test';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Test Bundle';
    }

    public function getVersion(): string
    {
        $bundleName = 'coreshop/test-bundle';

        if (class_exists(InstalledVersions::class)) {
            if (InstalledVersions::isInstalled('coreshop/core-shop')) {
                return InstalledVersions::getVersion('coreshop/core-shop');
            }

            if (InstalledVersions::isInstalled($bundleName)) {
                return InstalledVersions::getVersion($bundleName);
            }
        }

        if (class_exists(Version::class)) {
            return Version::getVersion();
        }

        return '';
    }

    public function getJsPaths(): array
    {
        return [
            '/bundles/coreshoptest/pimcore/js/plugin.js',
            '/bundles/coreshoptest/pimcore/js/xpath.js',
        ];
    }
}
