![CoreShop](etc/logo.png)

---

**CoreShop - Pimcore eCommerce**

[![Join the chat at https://gitter.im/coreshop/coreshop](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/coreshop/coreshop?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
![Behat UI Tests](https://github.com/coreshop/CoreShop/workflows/PHP%20Stan/badge.svg)
![Behat UI Tests](https://github.com/coreshop/CoreShop/workflows/Behat%20UI/badge.svg)
![Behat UI Tests](https://github.com/coreshop/CoreShop/workflows/Behat/badge.svg)
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Pre-Release](https://img.shields.io/packagist/vpre/coreshop/core-shop.svg)](https://www.packagist.org/packages/coreshop/core-shop)

[CoreShop](https://www.coreshop.org) is a eCommerce Platform for [Pimcore](http://www.pimcore.org).

![CoreShop Interface](docs/img/screenshot5.png)

# Requirements
* Pimcore ^10

# Installation
 - Allow dev version to be installed:
   ```
   composer config "minimum-stability" "dev"
   composer config "prefer-stable" "true"
   ```
 - Install with composer 
   ```
   composer require coreshop/core-shop:^3.0
   ```
 - Run enable Bundle command
   ```
   php bin/console pimcore:bundle:enable CoreShopCoreBundle
   ```
 - Run Install Command
   ```
   php bin/console coreshop:install
   ```
 - Optional: Install Demo Data 
   ```
   php bin/console coreshop:install:demo
   ```

# Further Information
 - [Website](https://www.coreshop.org)
 - [Documentation](https://docs.coreshop.org/latest)
 - [Pimcore Forum](https://talk.pimcore.org)
 - [Help translate CoreShop](https://crowdin.com/project/coreshop)

# Demo
You can see a running demo here [CoreShop 3.x Demo](https://demox.coreshop.org)

**Backend Credentials**

```
Admin: https://demox.coreshop.org/admin

Username: admin
Password: coreshop
```

## Copyright and license 
Copyright: [CoreShop GmbH](https://www.coreshop.org)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![CoreShop Interface](docs/img/screenshot5-2.png)
![CoreShop Interface](docs/img/screenshot5-3.png)
