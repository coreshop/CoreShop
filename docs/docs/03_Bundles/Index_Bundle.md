#  Index Bundle

## Installation
```bash
$ composer require coreshop/index-bundle:^4.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

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

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage

This Bundle integrates Index Component into Symfony and Doctrine

The Index Bundle provides you with basic information needed for a Indexing Pimcore Models: Index, Filters and Conditions

It also provides you with ListingServices and FilterServices

### Creating an Indexable

Define a class that implements the CoreShop\Component\Index\Model\IndexableInterface interface.

```php
<?php

declare(strict_types=1);

namespace App\Model;

use CoreShop\Component\Index\Model\IndexableInterface;

class MyPimcoreDataObject implements IndexableInterface
{
    // defines if the Indexable is enabled for the given Index
    public function getIndexableEnabled(IndexInterface $index): bool 
    {
        return true;
    }

    // defines if the Indexable should be indexed for the given Index
    public function getIndexable(IndexInterface $index): bool 
    {
        return true;
    }

    // defines the name of the Indexable for the given Index and Language
    public function getIndexableName(IndexInterface $index, string $language): ?string 
    {
        return $this->getName($language);
    }
}
```

Now you also have to set the App\Model\MyPimcoreDataObject as your parent class for your Pimcore Data-Object.

```php

### Get Listing from Index

How to get a Listing from an Index?

```php
$filter = $this->get('coreshop.repository.filter')->find(1); //Get Filter by ID 1
$filteredList = $this->get('coreshop.factory.filter.list')->createList($filter, $request->request);
$filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);
$filteredList->setCategory($category);
$this->get('coreshop.filter.processor')->processConditions($filter, $filteredList, $request->query);
$filteredList->load();
```
