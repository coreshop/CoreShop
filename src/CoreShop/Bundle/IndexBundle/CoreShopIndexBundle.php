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

namespace CoreShop\Bundle\IndexBundle;

use Composer\InstalledVersions;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterColumnTypePass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterConditionRendererTypesPass;
use CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler\RegisterExtensionsPass;
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
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopIndexBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopMenuBundle(), 4000);
    }

    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

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

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Index\Model';
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Index';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Index Bundle';
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

    public function getInstaller(): ?Installer
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

    public function getAdminIframePath(): ?string
    {
        return null;
    }

    public function getJsPaths(): array
    {
        return [];
    }

    public function getCssPaths(): array
    {
        return [];
    }

    public function getEditmodeJsPaths(): array
    {
        return [];
    }

    public function getEditmodeCssPaths(): array
    {
        return [];
    }
}
