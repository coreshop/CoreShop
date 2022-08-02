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

namespace CoreShop\Bundle\ResourceBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\DoctrineTargetEntitiesResolverPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\PimcoreCachePass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterInstallersPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreRepositoriesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\StackClassesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\StackRepositoryPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\ValidatorAutoMappingFixPass;
use JMS\SerializerBundle\JMSSerializerBundle;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopResourceBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    public const DRIVER_DOCTRINE_ORM = 'doctrine/orm';

    public const DRIVER_PIMCORE = 'pimcore';

    public const PIMCORE_MODEL_TYPE_OBJECT = 'object';

    public const PIMCORE_MODEL_TYPE_FIELD_COLLECTION = 'fieldcollection';

    public const PIMCORE_MODEL_TYPE_BRICK = 'brick';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterResourcesPass());
        $container->addCompilerPass(new RegisterPimcoreResourcesPass());
        $container->addCompilerPass(new DoctrineTargetEntitiesResolverPass());
        $container->addCompilerPass(new RegisterInstallersPass());
        $container->addCompilerPass(new StackClassesPass());
        $container->addCompilerPass(new StackRepositoryPass());
        $container->addCompilerPass(new RegisterPimcoreRepositoriesPass());
        $container->addCompilerPass(new ValidatorAutoMappingFixPass());
        $container->addCompilerPass(new PimcoreCachePass());
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        $collection->addBundle(new JMSSerializerBundle(), 3900);
        $collection->addBundle(new \CoreShop\Bundle\PimcoreBundle\CoreShopPimcoreBundle(), 3850);
        $collection->addBundle(new \CoreShop\Bundle\OptimisticEntityLockBundle\CoreShopOptimisticEntityLockBundle(), 3800);
        $collection->addBundle(new \CoreShop\Bundle\LocaleBundle\CoreShopLocaleBundle(), 3850);
        $collection->addBundle(new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(), 1200);
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Resource';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Resource Bundle';
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

    public static function getAvailableDrivers(): array
    {
        return [
            self::DRIVER_DOCTRINE_ORM,
        ];
    }
}
