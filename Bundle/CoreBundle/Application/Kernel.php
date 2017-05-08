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

use CoreShop\Bundle\AddressBundle\CoreShopAddressBundle;
use CoreShop\Bundle\CoreBundle\CoreShopCoreBundle;
use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle;
use CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle;
use CoreShop\Bundle\IndexBundle\CoreShopIndexBundle;
use CoreShop\Bundle\OrderBundle\CoreShopOrderBundle;
use CoreShop\Bundle\PaymentBundle\CoreShopPaymentBundle;
use CoreShop\Bundle\PayumBundle\CoreShopPayumBundle;
use CoreShop\Bundle\ProductBundle\CoreShopProductBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use CoreShop\Bundle\SequenceBundle\CoreShopSequenceBundle;
use CoreShop\Bundle\ShippingBundle\CoreShopShippingBundle;
use CoreShop\Bundle\StoreBundle\CoreShopStoreBundle;
use CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle;
use Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle;
use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Okvpn\Bundle\MigrationBundle\OkvpnMigrationBundle;
use Payum\Bundle\PayumBundle\PayumBundle;

class Kernel extends \Pimcore\Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new JMSSerializerBundle(),
            new OkvpnMigrationBundle(),

            new CoreShopOrderBundle(),
            new CoreShopCustomerBundle(),
            new CoreShopRuleBundle(),
            new CoreShopProductBundle(),
            new CoreShopAddressBundle(),
            new CoreShopCurrencyBundle(),
            new CoreShopTaxationBundle(),
            new CoreShopStoreBundle(),
            new CoreShopIndexBundle(),
            new CoreShopShippingBundle(),
            new CoreShopPaymentBundle(),
            new CoreShopSequenceBundle(),

            new CoreShopFrontendBundle(),
            new CoreShopPayumBundle(),

            new CoreShopCoreBundle(),
            new CoreShopResourceBundle(),
            new FOSRestBundle(),
            new DoctrineCacheBundle(),
            new PayumBundle(),
        ];

        return array_merge($bundles, parent::registerBundles());
    }
}
