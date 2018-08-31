# CoreShop State Machine - Callbacks

It's really simple to create a custom state machine callback.

## After Callbacks
In this example we want to register a simple listener which gets triggered **after** a customer successfully placed a order:

```yml
core_shop_workflow:
    state_machine:
        coreshop_order:
            callbacks:
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
        $locale = $order->getLocaleCode();

        // your very special code.
    }
}
```

## Before Callbacks
In this example we want to register a simple listener which gets triggered **before** a the shipment transaction `ready` gets applied:

```yml
core_shop_workflow:
    state_machine:
        coreshop_shipment:
            callbacks:
                before:
                    check_something:
                        on: ['create']
                        do: ['@AppBundle\EventListener\SpecialListener', 'checkSomething']
                        args: ['object']
                        priority: 0
```

| Name | Description |
|:-----|:------------|
| `on` | transition name |
| `do` | service and method to dispatch |
| `args` | `object` or `event`. Object type depends on state machine type. |
| `priority` | set priority. default is `0` |

As you can see in the class below, the `checkSomething()` method throws an exception.
This prevents the state machine from switching to the `ready` state.
Just remove the exception and the transition gets applied as expected.

```php
<?php

namespace AppBundle\EventListener;

use CoreShop\Component\Core\Model\OrderShipmentInterface;

final class SpecialListener
{
    /**
     * @param OrderShipmentInterface $shipment
     */
    public function checkSomething(OrderShipmentInterface $shipment)
    {
        // check something and throw an exeption
        throw new \Exception('something is wrong...');
    }
}
```
