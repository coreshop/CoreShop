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

namespace CoreShop\Bundle\OptimisticEntityLockBundle;

use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class CoreShopOptimisticEntityLockBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

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
    public function getComposerPackageName()
    {
        if (isset(Versions::VERSIONS['coreshop/optimistic-entity-lock-bundle'])) {
            return 'coreshop/optimistic-entity-lock-bundle';
        }

        return 'coreshop/core-shop';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeJsPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeCssPaths()
    {
        return [];
    }
}
