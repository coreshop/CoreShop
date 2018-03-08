# CoreShop State Machine - Callbacks

It's really simple to create a custom state machine callback.
In this example we want to register a simple listener which gets triggered after a customer successfully placed a order:

```yml
core_shop_resource:
    state_machine:
        callbacks:
            coreshop_order:
                after:
                    do_something_special:
                        on: ['confirm']
                        do: ['@AppBundle\EventListener\SpecialListener', 'doSomething']
                        args: ['object']
                        priority: -10 # fire action early!
```

| Name | Description |
|:-----|:------------|
| `on` | transition name |
| `do` | service and method to dispatch |
| `args` | `object` or `event`. Object type depends on state machine type. |
| `priority` | set priority. default is `0` |

And your Service:

```php
<?php

namespace AppBundle\EventListener;

use CoreShop\Component\Core\Model\CustomerInterface;
use CoreShop\Component\Core\Model\OrderInterface;

final class SpecialListener
{
    /**
     * @param OrderInterface $order
     */
    public function doSomething(OrderInterface $order)
    {
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        /** @var string $locale */
        $locale = $order->getOrderLanguage();

        // your very special code.
    }
}
```
