<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\MenuBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\MenuBundle\DependencyInjection\CompilerPass\MenuBuilderPass;
use Knp\Bundle\MenuBundle\KnpMenuBundle;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopMenuBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new KnpMenuBundle());
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new MenuBuilderPass());
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Menu';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Menu Bundle';
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
