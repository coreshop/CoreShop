<?php

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
use Liip\ThemeBundle\LiipThemeBundle;
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
            new LiipThemeBundle(),
            new DoctrineCacheBundle(),
            //new PayumBundle()
        ];

        return array_merge($bundles, parent::registerBundles());
    }
}
