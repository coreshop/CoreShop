# CoreShop Currency Bundle

Currency Bundle provides you with Models for persisting Currencies and resolving Currency Contexts.

 - Symfony Profiler
 - Pimcore Core Extensions
 - Doctrine Mappings
 - Symfony Forms
 - Money Formatter
 - Twig Extensions
   - Currency Formatting
   - Currency Conversion
   - Currency Code to Symbol

## Installation
```
composer require coreshop/currency-bundle:^2.0
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

        new \CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle(),
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

This Bundle integrates Currency Component into Symfony and Doctrine

The Currency Bundle provides you with basic information needed for Currencies: Currency, Exchange Rates, Conversion and Formatting

## Doctrine Entities
 - Currency

## Pimcore UI

 - Currency
 - Exchange Rate

How to use?

```javascript
coreshop.global.resource.open('coreshop.currency', 'currency');
coreshop.global.resource.open('coreshop.currency', 'exchange_rate');
```