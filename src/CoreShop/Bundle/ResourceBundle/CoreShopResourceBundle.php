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

namespace CoreShop\Bundle\ResourceBundle;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\DoctrineTargetEntitiesResolverPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterInstallersPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreRepositoriesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\StackClassesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\StackRepositoryPass;
use JMS\SerializerBundle\JMSSerializerBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Pimcore\HttpKernel\Bundle\DependentBundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopResourceBundle extends AbstractPimcoreBundle implements DependentBundleInterface
{
    use PackageVersionTrait;

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
    }

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        $collection->addBundle(new JMSSerializerBundle(), 3900);
        $collection->addBundle(new \CoreShop\Bundle\PimcoreBundle\CoreShopPimcoreBundle(), 3850);
        $collection->addBundle(new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(), 1400);
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
    public function getComposerPackageName()
    {
        if (isset(Versions::VERSIONS['coreshop/resource-bundle'])) {
            return 'coreshop/resource-bundle';
        }

        return 'coreshop/core-shop';
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
