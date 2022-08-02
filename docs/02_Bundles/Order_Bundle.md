# CoreShop Order Bundle

## Installation
```bash
$ composer require coreshop/order-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\OrderBundle\CoreShopOrderBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

### Install Pimcore Entities

```bash
$ php bin/console coreshop:resources:install
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
