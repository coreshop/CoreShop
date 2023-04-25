# CoreShop Optimistic Entity Lock Bundle

The CoreShop Optimistic Entity Lock Bundle is a package for the CoreShop e-commerce framework, based on the Pimcore platform, designed to handle optimistic entity locking. This bundle helps prevent conflicts when multiple users try to edit the same entity concurrently by implementing an optimistic locking strategy.
![Messenger](img/messenger.png)

## Installation
```bash
$ composer require optimistic-entity-lock-bundle
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel.

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\OptimisticEntityLockBundle\CoreShopOptimisticEntityLockBundle(),
    ]);
}
```

## Usage

Your Pimcore DataObject Class needs to implement the Interface `CoreShop\Bundle\OptimisticEntityLockBundle\Model\OptimisticLockedInterface`.

You can therefore add the field `optimisticLockVersion` to your Pimcore Class Definition.

From now on, everytime the DataObject gets saved, CoreShop compares the Versions and increases it before saving. If the version is different, someone else saved the entity before you and you get a exception.