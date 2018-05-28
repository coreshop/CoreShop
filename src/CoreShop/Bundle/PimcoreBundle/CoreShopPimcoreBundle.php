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
use Pimcore\Placeholder;

final class CoreShopPimcoreBundle extends AbstractPimcoreBundle
{
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

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        Placeholder::addPlaceholderClassPrefix('CoreShop\Component\Pimcore\Placeholder\\');
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        $jsFiles = [];

        if ($this->container->hasParameter('coreshop.all.pimcore.admin.js')) {
            $jsFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('coreshop.all.pimcore.admin.js'), true);
        }

        return $jsFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        $cssFiles = [];

        if ($this->container->hasParameter('coreshop.all.pimcore.admin.css')) {
            $cssFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('coreshop.all.pimcore.admin.css'));
        }

        return $cssFiles;
    }
}
