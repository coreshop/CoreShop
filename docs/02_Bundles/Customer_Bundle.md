# CoreShop Customer Bundle

## Installation

```bash
$ composer require coreshop/customer-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle(),
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

This Bundle integrates Customer Component into Symfony and Doctrine

The Customer Bundle provides you with basic information needed for a Customer: Customer and CustomerGroup

The Bundle also introduces an Customer Context, which helps you determine the current Customer.

## Pimcore Entities
 - Customer (CoreShopCustomer)
 - CustomerGroup (CoreShopCustomerGroup)
