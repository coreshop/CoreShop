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

namespace CoreShop\Bundle\TestBundle;

use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class CoreShopTestBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * {@inheritdoc}
     */
    public function getNiceName(): string
    {
        return 'CoreShop - Test';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'CoreShop - Test Bundle';
    }

    /**
     * @return string
     */
    public function getComposerPackageName(): string
    {
        if (isset(Versions::VERSIONS['coreshop/test-bundle'])) {
            return 'coreshop/test-bundle';
        }

        return 'coreshop/core-shop';
    }

    public function getJsPaths()
    {
        return [
            '/bundles/coreshoptest/pimcore/js/plugin.js',
            '/bundles/coreshoptest/pimcore/js/xpath.js',
        ];
    }
}
