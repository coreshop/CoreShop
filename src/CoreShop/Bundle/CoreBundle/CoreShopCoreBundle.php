<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreBundle;

use CoreShop\Bundle\AddressBundle\CoreShopAddressBundle;
use CoreShop\Bundle\ConfigurationBundle\CoreShopConfigurationBundle;
use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterPaymentSettingsFormsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterPortletsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterProductHelperPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterReportsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\TranslatableEntityLocalePass;
use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle;
use CoreShop\Bundle\FixtureBundle\CoreShopFixtureBundle;
use CoreShop\Bundle\IndexBundle\CoreShopIndexBundle;
use CoreShop\Bundle\InventoryBundle\CoreShopInventoryBundle;
use CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle;
use CoreShop\Bundle\NotificationBundle\CoreShopNotificationBundle;
use CoreShop\Bundle\OrderBundle\CoreShopOrderBundle;
use CoreShop\Bundle\PaymentBundle\CoreShopPaymentBundle;
use CoreShop\Bundle\PayumBundle\CoreShopPayumBundle;
use CoreShop\Bundle\ProductBundle\CoreShopProductBundle;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\SequenceBundle\CoreShopSequenceBundle;
use CoreShop\Bundle\ShippingBundle\CoreShopShippingBundle;
use CoreShop\Bundle\StoreBundle\CoreShopStoreBundle;
use CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle;
use CoreShop\Bundle\TrackingBundle\CoreShopTrackingBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopCoreBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
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

        $container->addCompilerPass(new TranslatableEntityLocalePass());
        $container->addCompilerPass(new RegisterProductHelperPass());
        $container->addCompilerPass(new RegisterReportsPass());
        $container->addCompilerPass(new RegisterPortletsPass());
        $container->addCompilerPass(new RegisterPaymentSettingsFormsPass());
    }

    /**
     * {@inheritdoc}
     */
    public static function registerDependentBundles(BundleCollection $collection)
    {
        parent::registerDependentBundles($collection);

        $collection->addBundles([
            new CoreShopOrderBundle(),
            new CoreShopCustomerBundle(),
            new CoreShopInventoryBundle(),
            new CoreShopProductBundle(),
            new CoreShopAddressBundle(),
            new CoreShopCurrencyBundle(),
            new CoreShopMoneyBundle(),
            new CoreShopTaxationBundle(),
            new CoreShopStoreBundle(),
            new CoreShopIndexBundle(),
            new CoreShopSequenceBundle(),
            new CoreShopPaymentBundle(),
            new CoreShopPayumBundle(),
            new CoreShopNotificationBundle(),
            new CoreShopFixtureBundle(),
            new CoreShopShippingBundle(),
            new CoreShopConfigurationBundle(),
            new CoreShopTrackingBundle()
        ], 1500);
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Core\Model';
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
    public function getVersion()
    {
        return Version::getVersion() . " (" . $this->getComposerVersion() . ")";
    }

    /**
     * @return string
     */
    public function getComposerVersion()
    {
        $version = Versions::getVersion('coreshop/core-shop');

        return $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
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
