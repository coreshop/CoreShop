# CoreShop 2

**CoreShop - Pimcore eCommerce**

[![Join the chat at https://gitter.im/coreshop/coreshop](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/coreshop/coreshop?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.com/coreshop/CoreShop.svg?branch=2.0)](https://travis-ci.com/coreshop/CoreShop)
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Pre-Release](https://img.shields.io/packagist/vpre/coreshop/core-shop.svg)](https://www.packagist.org/packages/coreshop/core-shop)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/coreshop/coreshop.svg)](https://www.scrutinizer-ci.com/g/coreshop/CoreShop/)
[![Donate](https://img.shields.io/badge/bitcoin-donate-yellow.svg)](https://www.coreshop.org/bitcoin)

[CoreShop](https://www.coreshop.org) is a eCommerce Platform for [Pimcore](http://www.pimcore.org).

![CoreShop Interface](docs/img/screenshot5.png)

# Requirements
* Pimcore 5.4

# Installation
 - Install with composer ```composer require coreshop/core-shop:^2.0```
 - Run enable Bundle command
    ```php bin/console pimcore:bundle:enable CoreShopCoreBundle```
 - Run Install Command
    `php bin/console coreshop:install`
 - Optional: Install Demo Data `php bin/console coreshop:install:demo`

# Further Information
 - [Website](https://www.coreshop.org)
 - [Documentation](https://www.coreshop.org/docs/latest)
 - [Pimcore Forum](https://talk.pimcore.org)
 - [Help translate CoreShop](https://crowdin.com/project/coreshop)

# Demo
You can see a running demo here [CoreShop Demo](https://demo2.coreshop.org)

**Backend Credentials**

```
Admin: https://demo2.coreshop.org/admin

Username: admin
Password: coreshop
```

## Copyright and license 
Copyright: [Dominik Pfaffenbauer](https://www.pfaffenbauer.at)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![CoreShop Interface](docs/img/screenshot5-2.png)
![CoreShop Interface](docs/img/screenshot5-3.png)
