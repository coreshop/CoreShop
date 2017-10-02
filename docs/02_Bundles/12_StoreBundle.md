# CoreShop Store Bundle

## Installation
```
composer require coreshop/store-bundle:^2.0
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

        new \CoreShop\Bundle\StoreBundle\StoreBundle(),
        new \CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle(),

        new \FOS\RestBundle\FOSRestBundle(),
        new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        new \Payum\Bundle\PayumBundle\PayumBundle(),
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

Nothing much to say here, adds a Store Model you can work with. It also adds Multi-theme Support.

## Doctrine Entities
 - Store

## Pimcore UI

 - Store

How to use?

```javascript
coreshop.global.resource.open('coreshop.store', 'store');
```