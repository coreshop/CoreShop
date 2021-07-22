<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\DoctrineTargetEntitiesResolverPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterInstallersPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreRepositoriesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\StackClassesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\StackRepositoryPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\ValidatorAutoMappingFixPass;
use JMS\SerializerBundle\JMSSerializerBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopResourceBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    const DRIVER_DOCTRINE_ORM = 'doctrine/orm';
    const DRIVER_PIMCORE = 'pimcore';

    const PIMCORE_MODEL_TYPE_OBJECT = 'object';
    const PIMCORE_MODEL_TYPE_FIELD_COLLECTION = 'fieldcollection';
    const PIMCORE_MODEL_TYPE_BRICK = 'brick';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
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
    }

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new JMSSerializerBundle(), 3900);
        $collection->addBundle(new \CoreShop\Bundle\PimcoreBundle\CoreShopPimcoreBundle(), 3850);
        $collection->addBundle(new \CoreShop\Bundle\OptimisticEntityLockBundle\CoreShopOptimisticEntityLockBundle(), 3800);
        $collection->addBundle(new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(), 1200);
    }

    /**
     * @return string
     */
    public function getNiceName()
    {
        return 'CoreShop - Resource';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'CoreShop - Resource Bundle';
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
     * @return string[]
     */
    public static function getAvailableDrivers()
    {
        return [
            self::DRIVER_DOCTRINE_ORM,
        ];
    }
}
