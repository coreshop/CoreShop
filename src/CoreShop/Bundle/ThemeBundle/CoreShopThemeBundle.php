<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ThemeBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler\CompositeThemeResolverPass;
use CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler\RemoveThemeAwareTranslatorPass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoreShopThemeBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new SyliusThemeBundle(), 1100);
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeThemeResolverPass());
        $container->addCompilerPass(new RemoveThemeAwareTranslatorPass());
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Theme';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Theme Bundle';
    }

    public function getVersion(): string
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

        if (class_exists(Version::class)) {
            return Version::getVersion();
        }

        return '';
    }
}
