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

use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\ExpressionLanguageServicePass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridActionPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridFilterPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagImplementationLoaderPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagPass;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Placeholder;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterGridActionPass());
        $container->addCompilerPass(new RegisterGridFilterPass());
        $container->addCompilerPass(new RegisterPimcoreDocumentTagImplementationLoaderPass());
        $container->addCompilerPass(new RegisterPimcoreDocumentTagPass());
        $container->addCompilerPass(new ExpressionLanguageServicePass());
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

    /**
     * {@inheritdoc}
     */
    public function getEditmodeJsPaths()
    {
        $jsFiles = [];

        if ($this->container->hasParameter('coreshop.all.pimcore.admin.editmode_js')) {
            $jsFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('coreshop.all.pimcore.admin.editmode_js'), false);
        }

        return $jsFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeCssPaths()
    {
        $cssFiles = [];

        if ($this->container->hasParameter('coreshop.all.pimcore.admin.editmode_css')) {
            $cssFiles = $this->container->get('coreshop.resource_loader')->loadResources($this->container->getParameter('coreshop.all.pimcore.admin.editmode_css'));
        }

        return $cssFiles;
    }
}
