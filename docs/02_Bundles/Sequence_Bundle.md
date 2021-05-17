# CoreShop Sequence Bundle

## Installation
```bash
$ composer require coreshop/sequence-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\SequenceBundle\CoreShopSequenceBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage

This Bundle integrates Sequence Component into Symfony and Doctrine

The sequence Bundle provides you with basic information needed for sequence generation.

## Doctrine Entities
 - Sequence

## Get a new Sequence

```php

$container->get('coreshop.sequence.generator')->getNextSequenceForType('my_sequence');

```
