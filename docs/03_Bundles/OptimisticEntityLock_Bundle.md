# Optimistic Entity Lock Bundle

The CoreShop Optimistic Entity Lock Bundle is a specialized package for the CoreShop e-commerce framework, designed for
handling optimistic entity locking on the Pimcore platform. This bundle is crucial for preventing editing conflicts when
multiple users attempt to modify the same entity simultaneously.

## Installation Process

To install the Optimistic Entity Lock Bundle, use Composer:

```bash
$ composer require coreshop/optimistic-entity-lock-bundle:^4.0
```

### Integrating with the Kernel

Enable the bundle in the kernel by updating the `AppKernel.php` file:

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

## Usage Instructions

### Implementing the Interface

Your Pimcore DataObject Class needs to implement
the `CoreShop\Bundle\OptimisticEntityLockBundle\Model\OptimisticLockedInterface`.

### Adding the Field to Class Definition

Add the field `optimisticLockVersion` to your Pimcore Class Definition. This field is pivotal for the locking mechanism.

### Functionality

Once implemented, every time the DataObject is saved, CoreShop checks and increments the version number. If the version
differs from the last saved state (indicating another user has saved changes), an exception is thrown, preventing
overwrite conflicts.

This bundle ensures data integrity and consistency in scenarios where concurrent editing might occur, safeguarding your
e-commerce data against inadvertent overwrites.
