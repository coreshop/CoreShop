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
*/use Pimcore\Kernel;

class TestAppKernel extends Kernel
{
    /**
     * AppKernel constructor.
     */
    public function __construct($environment, $debug)
    {
        parent::__construct($environment, $debug);
    }

    /**
     * Returns an array of bundles to register.
     *
     * @return \Symfony\Component\Intl\Data\Bundle\Reader\BundleReaderInterface[] An array of bundle instances
     */
    public function registerBundles()
    {
        $bundles = [
            new \AppBundle\AppBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Okvpn\Bundle\MigrationBundle\OkvpnMigrationBundle(),

            new \CoreShop\Bundle\ConfigurationBundle\CoreShopConfigurationBundle(),
            new \CoreShop\Bundle\AdminBundle\CoreShopAdminBundle(),
            new \CoreShop\Bundle\OrderBundle\CoreShopOrderBundle(),
            new \CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle(),
            new \CoreShop\Bundle\RuleBundle\CoreShopRuleBundle(),
            new \CoreShop\Bundle\ProductBundle\CoreShopProductBundle(),
            new \CoreShop\Bundle\AddressBundle\CoreShopAddressBundle(),
            new \CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle(),
            new \CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle(),
            new \CoreShop\Bundle\StoreBundle\CoreShopStoreBundle(),
            new \CoreShop\Bundle\IndexBundle\CoreShopIndexBundle(),
            new \CoreShop\Bundle\ShippingBundle\CoreShopShippingBundle(),
            new \CoreShop\Bundle\PaymentBundle\CoreShopPaymentBundle(),
            new \CoreShop\Bundle\SequenceBundle\CoreShopSequenceBundle(),
            new \CoreShop\Bundle\NotificationBundle\CoreShopNotificationBundle(),

            new \CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle(),
            new \CoreShop\Bundle\PayumBundle\CoreShopPayumBundle(),

            new \CoreShop\Bundle\CoreBundle\CoreShopCoreBundle(),
            new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Payum\Bundle\PayumBundle\PayumBundle(),
        ];

        $bundles = array_merge($bundles, parent::registerBundles());
        $bundles = array_merge($bundles, [
            //new \Sylius\Bundle\ThemeBundle\SyliusThemeBundle(),
            //new \CoreShop\Bundle\ThemeBundle\CoreShopThemeBundle(),
            //They somehow need to be after the Framework Bundle
        ]);

        return $bundles;
    }
}
