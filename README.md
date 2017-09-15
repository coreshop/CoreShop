# CoreShop 2 (Development)

**Looking for the current stable (version 1)?
See https://github.com/coreshop/CoreShop/tree/coreshop1**

**I am happy to announce CoreShop 2 - Pimcore eCommerce Framework, the best CoreShop since CoreShop - now totally based on Symfony.**

[![Join the chat at https://gitter.im/coreshop/coreshop](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/coreshop/coreshop?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Travis](https://img.shields.io/travis/coreshop/CoreShop.svg)]()
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Pre-Release](https://img.shields.io/packagist/vpre/coreshop/core-shop.svg)]()
[![Scrutinizer](https://img.shields.io/scrutinizer/g/coreshop/coreshop.svg)]()

CoreShop is a Bundle for [Pimcore](http://www.pimcore.org). It enhances Pimcore with eCommerce features.

![CoreShop Interface](docs/img/screenshot.png)

# Requirements
* Pimcore 5. Only with Build 118 or greater.

# Installation
 - Install with composer ```composer require coreshop/core-shop dev-master```
 - Add Following Call to AppKernel's registerBundlesToCollection function
    ```php
        \CoreShop\Bundle\CoreBundle\Application\RegisterBundleHelper::registerBundles($collection);
    ```
 - Add Following to your app/config/config.yml
    ```yml
        imports:
            - { resource: "@CoreShopCoreBundle/Resources/config/app/config.yml" }
    ```
 - Run Install Command
    ```php bin/console coreshop:install```
 - Activate AdminBundle in Pimcore Extension Manager
 - Optional: Install Demo Data ```php bin/console coreshop:install:demo```

# Demo
You can see a running demo here [CoreShop Demo](https://demo2.coreshop.org)

# Migration from CoreShop 1
If you want to migrate some of your from CoreShop 1, you can use the Import/Export Bundle:
 - [Export from CoreShop1](https://github.com/coreshop/CoreShopExport)
 - [Import into CoreShop2](https://github.com/coreshop/ImportBundle)

## Copyright and license 
Copyright: [Dominik Pfaffenbauer](https://www.pfaffenbauer.at)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![CoreShop Interface](docs/img/screenshot2.png)
![CoreShop Interface](docs/img/screenshot3.png)
