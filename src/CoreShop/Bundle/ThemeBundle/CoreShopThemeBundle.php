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

namespace CoreShop\Bundle\ThemeBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\ThemeBundle\DependencyInjection\Compiler\CompositeThemeResolverPass;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Sylius\Bundle\ThemeBundle\SyliusThemeBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoreShopThemeBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    use PackageVersionTrait;

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new SyliusThemeBundle(), 1100);
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new CompositeThemeResolverPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName(): string
    {
        return 'CoreShop - Theme';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'CoreShop - Theme Bundle';
    }

    /**
     * @return string
     */
    public function getComposerPackageName(): string
    {
        if (InstalledVersions::isInstalled('coreshop/theme-bundle')) {
            return 'coreshop/theme-bundle';
        }

        return 'coreshop/core-shop';
    }
}
