<?php

namespace CoreShop\Bundle\CoreBundle\Application;

use CoreShop\Bundle\AddressBundle\CoreShopAddressBundle;
use CoreShop\Bundle\CoreBundle\CoreShopCoreBundle;
use CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle;
use CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle;
use CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle;
use CoreShop\Bundle\OrderBundle\CoreShopOrderBundle;
use CoreShop\Bundle\ProductBundle\CoreShopProductBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use CoreShop\Bundle\RuleBundle\CoreShopRuleBundle;
use CoreShop\Bundle\StoreBundle\CoreShopStoreBundle;
use CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle;
use FOS\RestBundle\FOSRestBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Liip\ThemeBundle\LiipThemeBundle;

class Kernel extends \Pimcore\Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new CoreShopOrderBundle(),
            new CoreShopCustomerBundle(),
            new CoreShopRuleBundle(),
            new CoreShopProductBundle(),
            new CoreShopAddressBundle(),
            new CoreShopCurrencyBundle(),
            new CoreShopTaxationBundle(),
            new CoreShopStoreBundle(),

            new CoreShopCoreBundle(),
            new CoreShopFrontendBundle(),
            new CoreShopResourceBundle(),
            new JMSSerializerBundle(),
            new FOSRestBundle(),
            new LiipThemeBundle()
        ];

        return array_merge($bundles, parent::registerBundles());
    }
}
