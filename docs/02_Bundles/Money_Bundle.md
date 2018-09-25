# CoreShop Money Bundle

## Installation
```bash
$ composer require coreshop/money-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

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

## Usage

Money Bundle adds a new core-extension to pimcore which allows you to store currency values as integer.

You also get a Twig Extension to format money values.

```twig
{{ value|coreshop_format_money('€', 'de'); }}
```

