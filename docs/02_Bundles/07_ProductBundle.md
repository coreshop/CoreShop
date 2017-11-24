# CoreShop Product Bundle

    - Price Calculators
    - Pimcore Core Extensions
    - Doctrine Mappings
    - Symfony Forms
    - Product Price Rules
    - Product Specific Price Rules

## Installation
```bash
$ composer require coreshop/product-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

If you're not using CoreShop bundles, you will also need to add CoreShopResourceBundle and its dependencies
to kernel. Don’t worry, everything was automatically installed via Composer.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \JMS\SerializerBundle\JMSSerializerBundle(),

        new \CoreShop\Bundle\RuleBundle\CoreShopRuleBundle(),
        new \CoreShop\Bundle\ProductBundle\CoreShopProductBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),


        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Payum\Bundle\PayumBundle\PayumBundle(),
        new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
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

This Bundle integrates Product Component into Symfony and Doctrine

The Product Bundle provides you with basic information needed for products: Product, Product Price Rule, Specific Price Rule and Price Calculators

## Doctrine Entities
 - ProductPriceRule
 - ProductSpecificPriceRule

## Pimcore Entities
 - Product (CoreShopProduct)
 - Category (CoreShopCategory)

## Pimcore UI

 - Product Grid

How to use?

```javascript
coreshop.global.resource.open('coreshop.product', 'products');
```