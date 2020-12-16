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

namespace CoreShop\Bundle\PimcoreBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\ExpressionLanguageServicePass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridActionPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterGridFilterPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagImplementationLoaderPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterPimcoreDocumentTagPass;
use CoreShop\Bundle\PimcoreBundle\DependencyInjection\Compiler\RegisterTypeHintRegistriesPass;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
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
    public function getVersion()
    {
        $bundleName = 'coreshop/pimcore-bundle';

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

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
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
        $container->addCompilerPass(new RegisterTypeHintRegistriesPass());
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
