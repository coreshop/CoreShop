[![CoreShop](etc/illustration.png 'CoreSHop')](https://www.coreshop.org)


---

**CoreShop - Pimcore eCommerce**

![Static Tests (Lint, Stan)](https://github.com/coreshop/CoreShop/actions/workflows/static.yml/badge.svg)
[![Behat UI](https://github.com/coreshop/CoreShop/actions/workflows/behat_ui.yml/badge.svg)](https://github.com/coreshop/CoreShop/actions/workflows/behat_ui.yml)
[![Behat](https://github.com/coreshop/CoreShop/actions/workflows/behat.yml/badge.svg)](https://github.com/coreshop/CoreShop/actions/workflows/behat.yml)
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat)](LICENSE.md)
[![Latest Pre-Release](https://img.shields.io/packagist/vpre/coreshop/core-shop.svg)](https://www.packagist.org/packages/coreshop/core-shop)

[CoreShop](https://www.coreshop.org) is a eCommerce Platform for [Pimcore](http://www.pimcore.org).

![CoreShop Interface](etc/screenshot5.png)

# Requirements 
 - Pimcore `^11.1`

# Installation
Read our Documentation to get a Installation Guide [here](https://docs.coreshop.org/4.0.0/CoreShop/Getting_Started/Installation)

# Further Information
 - [Website](https://www.coreshop.org)
 - [Documentation](https://docs.coreshop.org/latest)
 - [Pimcore Github](https://github.com/pimcore/pimcore)

# Demo
You can see a running demo here [CoreShop 4.x Demo](https://demo4.coreshop.org)

**Backend Credentials**

```
Admin: https://demo4.coreshop.org/admin

Username: admin
Password: coreshop
```

## Running Tests Locally
### Psalm
```
vendor/bin/psalm
```

### PHPStan
```
SYMFONY_ENV=test vendor/bin/phpstan analyse -c phpstan.neon src -l 3 --memory-limit=-1
```

### BEHAT
- create database `coreshop4__behat`

#### Install Pimcore and CoreShop in Test Env
```
APP_ENV=test PIMCORE_TEST_DB_DSN=mysql://root:ROOT@coreshop-4/coreshop4___behat PIMCORE_INSTALL_ADMIN_USERNAME=admin PIMCORE_INSTALL_ADMIN_PASSWORD=admin PIMCORE_INSTALL_MYSQL_HOST_SOCKET=coreshop-4 PIMCORE_INSTALL_MYSQL_USERNAME=root PIMCORE_INSTALL_MYSQL_PASSWORD=ROOT PIMCORE_INSTALL_MYSQL_DATABASE=coreshop4___behat PIMCORE_INSTALL_MYSQL_PORT=3306 PIMCORE_KERNEL_CLASS=Kernel vendor/bin/pimcore-install --env=test --skip-database-config -n
APP_ENV=test PIMCORE_CLASS_DIRECTORY=var/tmp/behat/var/classes PIMCORE_TEST_DB_DSN=mysql://root:ROOT@coreshop-4/coreshop4___behat bin/console coreshop:install
```

#### BEHAT Domain
```
CORESHOP_SKIP_DB_SETUP=1 PIMCORE_TEST_DB_DSN=mysql://root:ROOT@coreshop-4/coreshop4___behat vendor/bin/behat -c behat.yml.dist -p default
```

#### BEHAT UI
```
vendor/bin/bdi detect drivers

# OUTSIDE CONTAINER
# Run Symfony Server
APP_ENV=test PIMCORE_TEST_DB_DSN=mysql://root:ROOT@127.0.0.1:3306/coreshop4___behat symfony server:start --port=9080 --dir=public --no-tls

# Run Behat
CORESHOP_SKIP_DB_SETUP=1 PANTHER_EXTERNAL_BASE_URI=http://127.0.0.1:9080/index_test.php PANTHER_NO_HEADLESS=0 PIMCORE_TEST_DB_DSN=mysql://root:ROOT@127.0.0.1:3306/coreshop4___behat php -d memory_limit=-1 vendor/bin/behat -c behat.yml.dist -p ui -vvv 
```

## Copyright and license 
Copyright: [CoreShop GmbH](https://www.coreshop.org)
For licensing details please visit [LICENSE.md](LICENSE.md) 

## Screenshots
![CoreShop Interface](etc/screenshot5-2.png)
![CoreShop Interface](etc/screenshot5-3.png)
