# Money Bundle

The Money Bundle provides an efficient way to handle and format monetary values within CoreShop.

## Installation Process

To install the Money Bundle, use Composer:

```bash
$ composer require coreshop/money-bundle:^4.0
```

### Integrating with the Kernel

To enable the bundle, update the `AppKernel.php` file:

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle(),
    ]);
}
```

## Usage Instructions

The Money Bundle introduces a core extension in Pimcore, allowing you to store currency values as integers. This
approach ensures precision and consistency in financial data handling.

### Twig Extension for Money Formatting

The bundle includes a Twig extension for formatting money values:

```twig
{{ value|coreshop_format_money('€', 'de'); }}
```

This extension allows for easy and flexible formatting of monetary values within your templates, enhancing the display
and readability of prices and financial figures in CoreShop.
