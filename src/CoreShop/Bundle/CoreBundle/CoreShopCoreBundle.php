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

namespace CoreShop\Bundle\CoreBundle;

use CoreShop\Bundle\AddressBundle\CoreShopAddressBundle;
use CoreShop\Bundle\ConfigurationBundle\CoreShopConfigurationBundle;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterIndexProductExtensionPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterPortletsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterReportsPass;
use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle;
use CoreShop\Bundle\ElasticsearchBundle\ElasticsearchBundle;
use CoreShop\Bundle\FixtureBundle\CoreShopFixtureBundle;
use CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle;
use CoreShop\Bundle\IndexBundle\CoreShopIndexBundle;
use CoreShop\Bundle\InventoryBundle\CoreShopInventoryBundle;
use CoreShop\Bundle\MenuBundle\CoreShopMenuBundle;
use CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle;
use CoreShop\Bundle\NotificationBundle\CoreShopNotificationBundle;
use CoreShop\Bundle\OrderBundle\CoreShopOrderBundle;
use CoreShop\Bundle\PayumBundle\CoreShopPayumBundle;
use CoreShop\Bundle\ProductBundle\CoreShopProductBundle;
use CoreShop\Bundle\ProductQuantityPriceRulesBundle\CoreShopProductQuantityPriceRulesBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\SEOBundle\CoreShopSEOBundle;
use CoreShop\Bundle\SequenceBundle\CoreShopSequenceBundle;
use CoreShop\Bundle\ShippingBundle\CoreShopShippingBundle;
use CoreShop\Bundle\StoreBundle\CoreShopStoreBundle;
use CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle;
use CoreShop\Bundle\TrackingBundle\CoreShopTrackingBundle;
use CoreShop\Bundle\UserBundle\CoreShopUserBundle;
use CoreShop\Bundle\VariantBundle\CoreShopVariantBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopCoreBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
    public function getSupportedDrivers(): array
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterIndexProductExtensionPass());
        $container->addCompilerPass(new RegisterReportsPass());
        $container->addCompilerPass(new RegisterPortletsPass());
    }

    public static function registerDependentBundles(BundleCollection $collection): void
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopMenuBundle(), 4000);
        $collection->addBundle(new CoreShopSEOBundle(), 3800);
        $collection->addBundle(new CoreShopFixtureBundle(), 3700);
        $collection->addBundle(new CoreShopMoneyBundle(), 3600);
        $collection->addBundle(new CoreShopConfigurationBundle(), 3300);
        $collection->addBundle(new CoreShopOrderBundle(), 3200);
        $collection->addBundle(new CoreShopCustomerBundle(), 3100);
        $collection->addBundle(new CoreShopUserBundle(), 3050);
        $collection->addBundle(new CoreShopInventoryBundle(), 3000);
        $collection->addBundle(new CoreShopProductBundle(), 2900);
        $collection->addBundle(new CoreShopVariantBundle(), 2950);
        $collection->addBundle(new CoreShopAddressBundle(), 2800);
        $collection->addBundle(new CoreShopCurrencyBundle(), 2700);
        $collection->addBundle(new CoreShopTaxationBundle(), 2600);
        $collection->addBundle(new CoreShopStoreBundle(), 2500);
        $collection->addBundle(new CoreShopIndexBundle(), 2400);
        $collection->addBundle(new CoreShopShippingBundle(), 2300);
        $collection->addBundle(new CoreShopSequenceBundle(), 2100);
        $collection->addBundle(new CoreShopNotificationBundle(), 2000);
        $collection->addBundle(new CoreShopTrackingBundle(), 2000);
        $collection->addBundle(new CoreShopFrontendBundle(), 1800);
        $collection->addBundle(new CoreShopPayumBundle(), 1700);
        $collection->addBundle(new CoreShopProductQuantityPriceRulesBundle(), 1600);
        $collection->addBundle(new ElasticsearchBundle(), 1400);
    }

    protected function getModelNamespace(): string
    {
        return 'CoreShop\Component\Core\Model';
    }

    public function getNiceName(): string
    {
        return 'CoreShop - Core';
    }

    public function getDescription(): string
    {
        return 'CoreShop - Pimcore eCommerce';
    }

    public function getVersion(): string
    {
        return Version::getVersion() . ' (' . $this->getComposerVersion() . ')';
    }

    public function getComposerVersion(): string
    {
        return Versions::getVersion('coreshop/core-shop');
    }

    public function getInstaller(): Installer
    {
        return $this->container->get(Installer::class);
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
