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

namespace CoreShop\Bundle\PimcoreBundle;

use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopPimcoreBundle extends AbstractPimcoreBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    /**
     * @return string
     */
    public function getNiceName()
    {
        return 'CoreShop - Pimcore';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'CoreShop - Pimcore Bundle';
    }

    /**
     * @return string
     */
    public function getComposerPackageName()
    {
        if (isset(Versions::VERSIONS['coreshop/pimcore-bundle'])) {
            return 'coreshop/pimcore-bundle';
        }

        return 'coreshop/core-shop';
    }
}
