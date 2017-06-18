## Installation
```
composer require coreshop/taxation-bundle dev-master
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

```
php bin/console doctrine:schema:update --force
```

### Install Pimcore Entities

```
php bin/console coreshop:resources:install
```

Learn more about overriding Pimcore Classes [here](../03_Development/12_Override_CoreShop_Classes.md))


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
