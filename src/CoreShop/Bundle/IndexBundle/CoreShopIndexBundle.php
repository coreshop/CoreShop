<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle;

use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterConditionRendererTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterColumnTypePass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterFilterConditionTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterGetterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterIndexWorkerPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterInterpreterPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterOrderRendererTypesPass;
use CoreShop\Bundle\MenuBundle\CoreShopMenuBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopIndexBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
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
    public function build(ContainerBuilder $container)
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
    public function getNiceName()
    {
        return 'CoreShop - Core';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'CoreShop - Pimcore eCommerce';
    }

    /**
     * {@inheritdoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'coreshop/index-bundle';
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!array_key_exists('CoreShopCoreBundle', $bundles)) {
            return $this->container->get(Installer::class);
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
