# CoreShop Messenger Bundle

CoreShop Messenger Bundle provides you with a nice UI to see what Messenger Tasks are queued in which queues:

![Messenger](img/messenger.png)

## Installation
```bash
$ composer require coreshop/messenger-bundle:^3.0
```

### Adding required bundles to kernel
You need to enable the bundle inside the kernel.

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