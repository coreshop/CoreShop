# Triggering Notification Events

Triggering notification events in CoreShop is streamlined through the use of
the `CoreShop\Component\Notification\Processor\RulesProcessorInterface`, implemented by the
service `@coreshop.notification_rule.processor`. This processor is key to activating various notification types based on
specific events.

## Example: Triggering an Order Event

Consider an example where you need to trigger a notification for an order event. You'll need to provide relevant
parameters that align with the notification type. Here’s how to trigger such an event for an order:

```php
$this->rulesProcessor->applyRules('order', $event->getProposal(), [
'fromState' => $event->getMarking()->getPlaces(),
'toState' => $event->getTransition()->getTos(),
'_locale' => $order->getLocaleCode(),
'recipient' => $customer->getEmail(),
'firstname' => $customer->getFirstname(),
'lastname' => $customer->getLastname(),
'orderNumber' => $order->getOrderNumber(),
'transition' => $event->getTransition()->getName(),
]);
```

In this code snippet, the `applyRules` method of `RulesProcessorInterface` is called with the order event type and
associated parameters like state transitions, locale, customer details, and the order number.

### CoreShop's Notification Handling

Once the event is triggered with these parameters, CoreShop's notification system processes it according to the defined
rules. This ensures the appropriate notifications are dispatched, maintaining effective communication and operational
efficiency.
