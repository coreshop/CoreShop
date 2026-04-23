<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\FrontendBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\CoreShopCoreBundle;
use CoreShop\Bundle\FrontendBundle\DependencyInjection\CompilerPass\FrontendInstallerPass;
use CoreShop\Bundle\FrontendBundle\DependencyInjection\CompilerPass\RegisterFrontendControllerPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\WebpackEncoreBundle\WebpackEncoreBundle;

final class CoreShopFrontendBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new CoreShopCoreBundle(), 100);
        $collection->addBundle(new WebpackEncoreBundle(), 100);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterFrontendControllerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 100);
        $container->addCompilerPass(new FrontendInstallerPass());
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Frontend';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Frontend Bundle';
    }

    public function getVersion(): string
    {
        if (class_exists('\\CoreShop\\Bundle\\CoreBundle\\Application\\Version')) {
            return \CoreShop\Bundle\CoreBundle\Application\Version::getVersion() . ' (' . $this->getComposerVersion() . ')';
        }

        return $this->getComposerVersion();
    }

    public function getComposerVersion(): string
    {
        $bundleName = 'coreshop/frontend-bundle';

        if (class_exists(InstalledVersions::class)) {
            if (InstalledVersions::isInstalled('coreshop/core-shop')) {
                return InstalledVersions::getPrettyVersion('coreshop/core-shop');
            }

            if (InstalledVersions::isInstalled($bundleName)) {
                return InstalledVersions::getPrettyVersion($bundleName);
            }
        }

        return '';
    }
}
