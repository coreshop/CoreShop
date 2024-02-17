# Sequence Bundle

The Sequence Bundle is a vital component within CoreShop that facilitates sequence generation, integrating seamlessly
with Symfony and Doctrine.

## Installation Process

To install the Sequence Bundle, use Composer:

```bash
$ composer require coreshop/sequence-bundle:^4.0
```

### Integrating with the Kernel

Enable the bundle in the kernel by updating the `AppKernel.php` file:

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

### Updating the Database Schema

After installation, update the database schema with the following command:

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage

The Sequence Bundle integrates the Sequence Component into Symfony and Doctrine, providing essential tools for
generating sequences within your application.

### Doctrine Entities

The primary entity used in the Sequence Bundle is `Sequence`.

### Generating a New Sequence

To generate a new sequence, use the sequence generator service:

```php
$container->get('coreshop.sequence.generator')->getNextSequenceForType('my_sequence');
```

This bundle enhances the functionality of CoreShop, enabling efficient and orderly sequence generation for various
operational needs.
