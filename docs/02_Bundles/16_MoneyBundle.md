# CoreShop Money Bundle

## Installation
```bash
$ composer require coreshop/money-bundle:^2.0
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

        new \CoreShop\Bundle\MoneyBundle\CoreShopMoneyBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),

        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    ]);
}
```

## Usage

Money Bundle adds a new core-extension to pimcore which allows you to store currency values as integer.

You also get a Twig Extension to format money values.

```twig
{{ value|coreshop_format_money('€', 'de'); }}
```

