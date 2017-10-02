# CoreShop Taxation Bundle

## Installation
```bash
$ composer require coreshop/taxation-bundle:^2.0
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

        new \CoreShop\Bundle\TaxationBundle\CoreShopTaxationBundle(),
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
