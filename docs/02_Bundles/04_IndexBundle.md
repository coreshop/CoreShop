# CoreShop Index Bundle

## Installation
```
composer require coreshop/index-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

If you're not using CoreShop bundles, you will also need to add CoreShopResourceBundle and its dependencies
to kernel. Don’t worry, everything was automatically installed via Composer.

```php
<?php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new \JMS\SerializerBundle\JMSSerializerBundle(),

        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),
        new \CoreShop\Bundle\IndexBundle\CoreShopIndexBundle(),

        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    );
}
```

### Updating database schema
Run the following command.

```
php bin/console doctrine:schema:update --force
```

## Usage

This Bundle integrates Index Component into Symfony and Doctrine

The Index Bundle provides you with basic information needed for a Indexing Pimcore Models: Index, Filters and Conditions

It also provides you with ListingServices and FilterServices

### Get Listing from Index

How to get a Listing from an Index?

```php
$filter = $this->get('coreshop.repository.filter')->find(1); //Get Filter by ID 1
$filteredList = $this->get('coreshop.factory.filter.list')->createList($filter, $request->request);
$filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);
$filteredList->setCategory($category);
$filteredList->load();
```

## Pimcore UI

 - Index Configuration
 - Fitler Configuration

How to use?

```javascript
coreshop.global.resource.open('coreshop.index', 'index');
coreshop.global.resource.open('coreshop.index', 'filter');
```