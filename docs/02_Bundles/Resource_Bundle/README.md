# CoreShop Resource Bundle

Resource Bundle is the Heart of CoreShops Model. It handles saving/deleting/updating/creating of CoreShop Models. It handles
Doctrine ORM Mappings and Translations. As well as Routing, Event Dispatching, Serialization and CRUD.

Resource Bundle also takes care about installation of Pimcore Class Definitions, Object Brick Definitions, Field Collection Definitions,
Static Routes and SQL.

You can use Resource Bundle as base for all your Custom Pimcore Entities.

## Installation
```bash
$ composer require coreshop/resource-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \JMS\SerializerBundle\JMSSerializerBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle()
    ]);
}
```