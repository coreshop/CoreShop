# CoreShop Shipping Bundle

## Installation
```
composer require coreshop/shipping-bundle:^2.0
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

        new \CoreShop\Bundle\RuleBundle\CoreShopRuleBundle(),
        new \CoreShop\Bundle\ShippingBundle\CoreShopShippingBundle(),
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