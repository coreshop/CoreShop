## Installation
```
composer require coreshop/address-bundle dev-master
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

If you're not using CoreShop bundles, you will also need to add CoreShopResourceBundle and its dependencies
to kernel. Don’t worry, everything was automatically installed via Composer.

```php
<?php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new \JMS\SerializerBundle\JMSSerializerBundle(),
        new \Okvpn\Bundle\MigrationBundle\OkvpnMigrationBundle(),

        new \CoreShop\Bundle\AddressBundle\CoreShopAddressBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),


        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Payum\Bundle\PayumBundle\PayumBundle(),
        new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    );
}
```

### Updating database schema
Run the following command.

```
php bin/console doctrine:schema:update --force
```

### Install Pimcore Entities

```
php bin/console coreshop:resources:install
```


Learn more about overriding Pimcore Classes [here](../03_Development/12_Override_CoreShop_Classes.md))



## Usage

This Bundle integrates Address Component into Symfony and Doctrine

The Address Bundle provides you with basic information needed for addressing: Countries, States, Zones and Address

The Bundle also introduces an Address Formatter, which formats addresses in country based formats.

## Doctrine Entities
 - Country
 - Zone
 - State

## Pimcore Entities
 - Address (CoreShopAddress)
