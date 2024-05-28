# Messenger Bundle

The CoreShop Messenger Bundle provides a user-friendly interface to view queued Messenger tasks across different queues.

![Messenger](img/messenger.png)

## Installation Process

To install the Messenger Bundle, use Composer:

```bash
$ composer require coreshop/messenger-bundle:^4.0
```

### Integrating with the Kernel

To enable the bundle, update the `AppKernel.php` file:

```php
<?php

// app/AppKernel.php

public function registerBundlesToCollection(BundleCollection $collection)
{
    $collection->addBundles([
        new \CoreShop\Bundle\MessengerBundle\CoreShopMessengerBundle(),
    ]);
}
```
