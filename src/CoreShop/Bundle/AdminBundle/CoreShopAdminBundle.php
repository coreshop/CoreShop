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

namespace CoreShop\Bundle\AdminBundle;

use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\AdminBundle\Installer\PimcoreInstaller;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

final class CoreShopAdminBundle extends AbstractPimcoreBundle
{
    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Core\Model';
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName()
    {
        return 'CoreShop';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'CoreShop - Pimcore eCommerce';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return Version::getVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return new PimcoreInstaller();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminIframePath()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        $jsFiles = [];

        foreach (['core_shop_resource.pimcore.admin.js', 'import_definitions.pimcore.admin.js'] as $parameter) {
            if ($this->container->hasParameter($parameter)) {
                $jsFiles = array_merge($jsFiles, $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter($parameter)));
            }
        }

        return $jsFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        $cssFiles = [];

        foreach (['core_shop_resource.pimcore.admin.css', 'import_definitions.pimcore.admin.css'] as $parameter) {
            if ($this->container->hasParameter($parameter)) {
                $cssFiles = array_merge($cssFiles, $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter($parameter)));
            }
        }

        return $cssFiles;
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
