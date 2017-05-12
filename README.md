# CoreShop 2 (Development)

**Looking for the current stable (version 1)?
See https://github.com/coreshop/CoreShop/tree/coreshop1**

**I am happy to announce CoreShop 2 - Pimcore eCommerce Framework, the best CoreShop since CoreShop - now totally based on Symfony.**

[![Join the chat at https://gitter.im/dpfaffenbauer/pimcore-coreshop](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/dpfaffenbauer/pimcore-coreshop?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/coreshop/CoreShop.svg?branch=master)](https://travis-ci.org/coreshop/CoreShop)
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Stable Version](https://poser.pugx.org/coreshop/core-shop/v/stable)](https://packagist.org/packages/coreshop/core-shop)

CoreShop is a Bundle for [Pimcore](http://www.pimcore.org). It enhances Pimcore with eCommerce features.

![CoreShop Interface](docs/img/screenshot.png)

# Installation
 - Install with composer ```composer require coreshop/core-shop dev:master```
 - Add Following Bundles to AppKernel.php
    ```php
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

        new \CoreShop\Bundle\FrontendBundle\CoreShopFrontendBundle(),
        new \CoreShop\Bundle\PayumBundle\CoreShopPayumBundle(),

        new \CoreShop\Bundle\CoreBundle\CoreShopCoreBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),
        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Payum\Bundle\PayumBundle\PayumBundle()
    ```
 - Import ```"@CoreShopCoreBundle/Resources/config/app/config.yml"``` in your app/config/config.yml
 - Activate Admin Bundle in Pimcore Extension Manager
 - Run Install from Pimcore Extension Manager or from CLI
    ```php bin/console coreshop:install```

## Copyright and license 
Copyright: [Dominik Pfaffenbauer](https://www.pfaffenbauer.at)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![CoreShop Interface](docs/img/screenshot2.png)
![CoreShop Interface](docs/img/screenshot3.png)
