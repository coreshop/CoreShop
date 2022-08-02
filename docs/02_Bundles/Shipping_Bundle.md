# CoreShop Shipping Bundle

## Installation
```bash
$ composer require coreshop/shipping-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\ShippingBundle\CoreShopShippingBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage

This Bundle integrates Shipping Component into Symfony and Doctrine

The Shipping Bundle provides you with basic information needed for shipping: Carriers, Shipping Rules, Rule Processors and Calculators

## Doctrine Entities
 - Carrier
 - Shipping Rule
 - Shipping Rule Group

## Pimcore UI

 - Carrier
 - Shipping Rule

How to use?

```javascript
coreshop.global.resource.open('coreshop.shipping', 'carrier');
coreshop.global.resource.open('coreshop.shipping', 'shipping_rule');
```
