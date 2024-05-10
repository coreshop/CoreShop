# Index Bundle

The Index Bundle integrates the Index Component into Symfony and Doctrine, offering tools for indexing Pimcore models,
managing filters, and creating conditions. It includes ListingServices and FilterServices.

## Installation Process

To install the bundle, use Composer:

```bash
$ composer require coreshop/index-bundle:^4.0
```

### Integrating with the Kernel

Enable the bundle in the kernel by updating the `AppKernel.php` file:

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\IndexBundle\CoreShopIndexBundle(),
    ]);
}
```

### Updating the Database Schema

Update the database schema with the following command:

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage Guidelines

### Creating an Indexable Class

Define a class that implements the `CoreShop\Component\Index\Model\IndexableInterface` interface:

```php
<?php

declare(strict_types=1);

namespace App\Model;

use CoreShop\Component\Index\Model\IndexableInterface;

class MyPimcoreDataObject implements IndexableInterface
{
    // Defines if the Indexable is enabled for the given Index
    public function getIndexableEnabled(IndexInterface $index): bool 
    {
        return true;
    }

    // Defines if the Indexable should be indexed for the given Index
    public function getIndexable(IndexInterface $index): bool 
    {
        return true;
    }

    // Defines the name of the Indexable for the given Index and Language
    public function getIndexableName(IndexInterface $index, string $language): ?string 
    {
        return $this->getName($language);
    }
}
```

Ensure that `App\Model\MyPimcoreDataObject` is set as your parent class for your Pimcore Data-Object.

### Retrieving Listings from an Index

Fetch a listing from an index:

```php
$filter = $this->get('coreshop.repository.filter')->find(1); // Get Filter by ID 1
$filteredList = $this->get('coreshop.factory.filter.list')->createList($filter, $request->request);
$filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);
$filteredList->setCategory($category);
$this->get('coreshop.filter.processor')->processConditions($filter, $filteredList, $request->query);
$filteredList->load();
```

This Index Bundle is crucial for managing indexing and filtering of data within CoreShop, ensuring efficient data
handling and retrieval.
