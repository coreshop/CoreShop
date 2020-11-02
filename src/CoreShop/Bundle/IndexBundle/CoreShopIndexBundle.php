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

namespace CoreShop\Bundle\IndexBundle;

use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterConditionRendererTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterColumnTypePass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterPreConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterUserConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterGetterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterIndexWorkerPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterInterpreterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterOrderRendererTypesPass;
use CoreShop\Bundle\MenuBundle\CoreShopMenuBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\ResourceBundle\ResourceBundleInterface;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopIndexBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
    protected $mappingFormat = ResourceBundleInterface::MAPPING_XML;

    use PackageVersionTrait;

    public static function registerDependentBundles(BundleCollection $collection)
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopMenuBundle(), 4000);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterColumnTypePass());
        $container->addCompilerPass(new RegisterIndexWorkerPass());
        $container->addCompilerPass(new RegisterInterpreterPass());
        $container->addCompilerPass(new RegisterGetterPass());
        $container->addCompilerPass(new RegisterFilterConditionTypesPass());
        $container->addCompilerPass(new RegisterExtensionsPass());
        $container->addCompilerPass(new RegisterConditionRendererTypesPass());
        $container->addCompilerPass(new RegisterOrderRendererTypesPass());
        $container->addCompilerPass(new RegisterFilterPreConditionTypesPass());
        $container->addCompilerPass(new RegisterFilterUserConditionTypesPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Index\Model';
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName(): string
    {
        return 'CoreShop - Index';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'CoreShop - Index Bundle';
    }

    /**
     * @return string
     */
    public function getComposerPackageName(): string
    {
        if (isset(Versions::VERSIONS['coreshop/index-bundle'])) {
            return 'coreshop/index-bundle';
        }

        return 'coreshop/core-shop';
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        if ($this->container->has(Installer::class)) {
            /**
             * @var Installer $installer
             */
            $installer = $this->container->get(Installer::class);

            return $installer;
        }

        return null;
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
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!array_key_exists('CoreShopCoreBundle', $bundles)) {
            return [
                '/admin/coreshop/coreshop.index/menu.js',
            ];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        return [];
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
