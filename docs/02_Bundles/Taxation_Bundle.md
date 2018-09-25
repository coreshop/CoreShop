# CoreShop Taxation Bundle

## Installation
```bash
$ composer require coreshop/taxation-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle(),
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


## Doctrine Entities
 - Tax
 - TaxRule
 - TaxRuleGroup

## Pimcore Entities
 - TaxItem Fieldcollection (CoreShopTaxItem)


## Pimcore UI

 - Tax Item
 - Tax Rule Group

How to use?

```javascript
coreshop.global.resource.open('coreshop.taxation', 'tax_item');
coreshop.global.resource.open('coreshop.taxation', 'tax_rule_group');
```
