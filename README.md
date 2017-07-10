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

## Copyright and license 
Copyright: [Dominik Pfaffenbauer](https://www.pfaffenbauer.at)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![CoreShop Interface](docs/img/screenshot2.png)
![CoreShop Interface](docs/img/screenshot3.png)
