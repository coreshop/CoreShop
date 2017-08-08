## Installation
```
composer require coreshop/customer-bundle dev-master
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

        new \CoreShop\Bundle\CustomerBundle\CoreShopCustomerBundle(),
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

### Install Pimcore Entities

```
php bin/console coreshop:resources:install
```

Learn more about overriding Pimcore Classes [here](../03_Development/12_Override_CoreShop_Classes.md))


## Usage

This Bundle integrates Customer Component into Symfony and Doctrine

The Customer Bundle provides you with basic information needed for a Customer: Customer and CustomerGroup

The Bundle also introduces an Customer Context, which helps you determine the current Customer.

## Pimcore Entities
 - Customer (CoreShopCustomer)
 - CustomerGroup (CoreShopCustomerGroup)
