# CoreShop Payment Bundle

## Installation
```bash
$ composer require coreshop/payment-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\PaymentBundle\CoreShopPaymentBundle(),
    ]);
}
```

### Updating database schema
Run the following command.

```bash
$ php bin/console doctrine:schema:update --force
```

## Usage

This Bundle integrates Payment Component into Symfony and Doctrine

The Payment Bundle provides you with basic information needed for payment: Payment

The Bundle also introduces an Address Formatter, which formats addresses in country based formats.

## Doctrine Entities
 - Payment

 ## Pimcore UI

 - Payment Provider

How to use?

```javascript
coreshop.global.resource.open('coreshop.payment', 'payment_provider');
```

