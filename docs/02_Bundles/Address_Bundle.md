# CoreShop Address Bundle

Address Bundle provides you with Models for Storing Countries, States, Zones and Addresses. As well as Context Resolvers
to find the visitors Country.

## Installation
```bash
$ composer require coreshop/address-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\AddressBundle\CoreShopAddressBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

### Install Pimcore Entities

```bash
$ php bin/console coreshop:resources:install
```

Learn more about overriding Pimcore Classes [here](../03_Development/01_Extending_Guide/03_Extend_CoreShop_DataObjects.md))

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

## Pimcore UI

 - Country
 - State
 - Zone

How to use?

```javascript
coreshop.global.resource.open('coreshop.address', 'country');
coreshop.global.resource.open('coreshop.address', 'state');
coreshop.global.resource.open('coreshop.address', 'zone');
```
