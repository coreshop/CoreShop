# CoreShop Notification Trigger Notifications

Triggering Notification events is quite easy, you can use the ```CoreShop\Component\Notification\Processor\RulesProcessorInterface``` implemented by service
```@coreshop.notification_rule.processor```

You also need to add different kinds of parameters based on your Notification Type. In our case, we trigger an Order event.

```php
$this->rulesProcessor->applyRules('order', $event->getProposal(), [
    'fromState' => $event->getOldState(),
    'toState' => $event->getNewState(),
    '_locale' => $order->getOrderLanguage(),
    'recipient' => $customer->getEmail(),
    'firstname' => $customer->getFirstname(),
    'lastname' => $customer->getLastname(),
    'orderNumber' => $order->getOrderNumber()
]);
```

The rest is now handled by CoreShop Notifications.