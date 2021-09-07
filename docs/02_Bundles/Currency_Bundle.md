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

```bash
$ composer require coreshop/currency-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\CurrencyBundle\CoreShopCurrencyBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
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
