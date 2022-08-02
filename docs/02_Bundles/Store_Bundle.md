# CoreShop Store Bundle

## Installation
```bash
$ composer require coreshop/store-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\StoreBundle\StoreBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage

Nothing much to say here, adds a Store Model you can work with. It also adds Multi-theme Support.

## Doctrine Entities
 - Store

## Pimcore UI

 - Store

How to use?

```javascript
coreshop.global.resource.open('coreshop.store', 'store');
```
