# CoreShop Order Bundle

## Installation
```
composer require coreshop/order-bundle:^2.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

If you're not using CoreShop bundles, you will also need to add CoreShopResourceBundle, CoreShopRuleBundle and CoreShopProductBundle and its dependencies
to kernel. Don’t worry, everything was automatically installed via Composer.

```php
<?php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        new \JMS\SerializerBundle\JMSSerializerBundle(),

        new \CoreShop\Bundle\OrderBundle\CoreShopOrderBundle(),
        new \CoreShop\Bundle\RuleBundle\CoreShopRuleBundle(),
        new \CoreShop\Bundle\ProductBundle\CoreShopProductBundle(),
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

Learn more about overriding Pimcore Classes [here](../03_Development/01_Extending_Guide/03_Extend_CoreShop_DataObjects.md)

## Usage

This Bundle integrates Order Component into Symfony and Doctrine

The Order Bundle provides you with basic information needed for ordering: Orders, Invoices, Shipments and Cart Rules

## Doctrine Entities
 - CartPriceRule
 - CartPriceRuleVoucherCode
 - State

## Pimcore Entities
 - Cart (CoreShopCart)
 - CartItem (CoreShopCartItem)
 - Order (CoreShopOrder)
 - OrderItem (CoreShopOrderItem)
 - OrderInvoice (CoreShopOrderInvoice)
 - OrderInvoiceItem (CoreShopOrderInvoiceItem)
 - OrderShipment (CoreShopOrderShipment)
 - OrderShipmentItem (CoreShopOrderShipmentItem)

## Cart Price Rule

### Conditions
Learn more about adding new Conditions [here](../03_Development/01_Extending_Guide/04_Extending_Rule_Conditions.md)

### Actions
Learn more about adding new Actions [here](../03_Development/01_Extending_Guide/04_Extending_Rule_Actions.md)

## Pimcore UI

 - Order Grid

How to use?

```javascript
coreshop.global.resource.open('coreshop.order', 'orders');
```