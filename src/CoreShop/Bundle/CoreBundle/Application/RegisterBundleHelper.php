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

namespace CoreShop\Bundle\CoreBundle\Application;

use Pimcore\HttpKernel\BundleCollection\BundleCollection;

class RegisterBundleHelper
{
    /**
     * @param BundleCollection $collection
     */
    public static function registerBundles(BundleCollection $collection) {
        $collection->addBundles([
            new \AppBundle\AppBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Okvpn\Bundle\MigrationBundle\OkvpnMigrationBundle(),

            new \CoreShop\Bundle\LocaleBundle\CoreShopLocaleBundle(),
            new \CoreShop\Bundle\ConfigurationBundle\CoreShopConfigurationBundle(),
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
            new \CoreShop\Bundle\TrackingBundle\CoreShopTrackingBundle(),

            new \CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle(),
            new \CoreShop\Bundle\PayumBundle\CoreShopPayumBundle(),

            new \CoreShop\Bundle\CoreBundle\CoreShopCoreBundle(),
            new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),
            new \FOS\RestBundle\FOSRestBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Payum\Bundle\PayumBundle\PayumBundle()
        ], 120);
    }
}