# CoreShop Notification Trigger Notifications

Triggering Notification events is quite easy, you can use the ```CoreShop\Component\Notification\Processor\RulesProcessorInterface``` implemented by service
```@coreshop.notification_rule.processor```

You also need to add different kinds of parameters based on your Notification Type.
In our case, we trigger an Order event.

```php
$this->rulesProcessor->applyRules('order', $event->getProposal(), [
    'fromState' => $event->getMarking()->getPlaces(),
    'toState' => $event->getTransition()->getTos(),
    '_locale' => $order->getLocaleCode(),
    'recipient' => $customer->getEmail(),
    'firstname' => $customer->getFirstname(),
    'lastname' => $customer->getLastname(),
    'orderNumber' => $order->getOrderNumber(),
    'transition' => $event->getTransition()->getName()
]);
```

The rest is now handled by CoreShop Notifications.